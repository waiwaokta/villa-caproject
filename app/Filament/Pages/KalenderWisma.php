<?php

namespace App\Filament\Pages;

use App\Models\Holiday;
use App\Models\Maintenance;
use App\Models\Wisma;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Filament\Actions\Action;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use UnitEnum;
use BackedEnum;

class KalenderWisma extends Page
{
    protected static string| BackedEnum |null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static ?string $navigationLabel = 'Kalender Wisma';

    protected static ?string $title = 'Kalender Wisma';

    protected static string | UnitEnum | null $navigationGroup = 'Wisma';

    protected static ?string $pluralModelLabel = 'Kalender Wisma';

    protected static ?int $navigationSort = 3;

    protected string $view = 'filament.pages.kalender-wisma';

    public int $bulan;
    public int $tahun;
    public ?array $currentEditArguments = null;

    public ?array $prevWismaIds = null;

    public function mount(): void
    {
        $this->bulan = now()->month;
        $this->tahun = now()->year;
    }

    public function getEvents(): array
    {
        $start = Carbon::createFromDate($this->tahun, $this->bulan, 1)->startOfMonth();
        $end   = $start->copy()->endOfMonth();

        $holidays = Holiday::whereBetween('date', [$start->toDateString(), $end->toDateString()])->get();
        $maintenance = Maintenance::with('wisma')
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->get();

        $events = [];

        foreach ($holidays as $holiday) {
            $dateKey = Carbon::parse($holiday->date)->toDateString();
            $events[$dateKey][] = [
                'type'      => 'holiday',
                'label'     => $holiday->name,
                'holidayID' => $holiday->holidayID,
            ];
        }

        foreach ($maintenance as $item) {
            $dateKey = Carbon::parse($item->date)->toDateString();
            $events[$dateKey][] = [
                'type'          => 'maintenance',
                'label'         => $item->wisma ? $item->wisma->name : 'Semua Wisma',
                'reason'        => $item->reason,
                'wismaID'       => $item->wismaID,
                'maintenanceID' => $item->maintenanceID,
            ];
        }

        return $events;
    }

    public function prevMonth(): void
    {
        $date = Carbon::createFromDate($this->tahun, $this->bulan, 1)->subMonth();
        $this->bulan = $date->month;
        $this->tahun = $date->year;
    }

    public function nextMonth(): void
    {
        $date = Carbon::createFromDate($this->tahun, $this->bulan, 1)->addMonth();
        $this->bulan = $date->month;
        $this->tahun = $date->year;
    }

    public function openAddEventModal(string $tanggal): void
    {
        // DITAMBAHKAN — guard server-side, jaga-jaga kalau wire:click di view ke-bypass
        if (Carbon::parse($tanggal)->lt(Carbon::today())) {
            Notification::make()
                ->title('Tidak bisa menambah event di tanggal yang sudah lewat')
                ->danger()
                ->send();
            return;
        }

        $this->mountAction('tambahEvent', ['tanggal' => $tanggal]);
    }

    public function openEditEventModal(string $type, string $tanggal, ?string $wismaID = null): void
    {
        // DITAMBAHKAN — guard server-side, sama seperti openAddEventModal
        if (Carbon::parse($tanggal)->lt(Carbon::today())) {
            Notification::make()
                ->title('Tidak bisa mengubah event di tanggal yang sudah lewat')
                ->danger()
                ->send();
            return;
        }

        [$rangeStart, $rangeEnd, $name] = $this->resolveEventRange($type, $tanggal, $wismaID);

        $this->currentEditArguments = [
            'type'         => $type,
            'wismaID'      => $wismaID,
            'rangeStart'   => $rangeStart,
            'rangeEnd'     => $rangeEnd,
            'originalName' => $name,
        ];

        $this->mountAction('editEvent', $this->currentEditArguments);
    }

    /**
     * Rekonstruksi rentang tanggal asli dari 1 tanggal yang diklik —
     * cek mundur & maju selama nama/wisma sama dan tanggal berdekatan.
     */
    private function resolveEventRange(string $type, string $tanggal, ?string $wismaID): array
    {
        $clicked = Carbon::parse($tanggal);

        if ($type === 'holiday') {
            $current = Holiday::where('date', $tanggal)->first();
            if (!$current) {
                return [$tanggal, $tanggal, ''];
            }
            $name = $current->name;

            $start = $clicked->copy();
            while (Holiday::where('date', $start->copy()->subDay()->toDateString())->where('name', $name)->exists()) {
                $start->subDay();
            }

            $end = $clicked->copy();
            while (Holiday::where('date', $end->copy()->addDay()->toDateString())->where('name', $name)->exists()) {
                $end->addDay();
            }

            return [$start->toDateString(), $end->toDateString(), $name];
        }

        // maintenance
        $current = Maintenance::where('date', $tanggal)->where('wismaID', $wismaID)->first();
        if (!$current) {
            return [$tanggal, $tanggal, ''];
        }
        $reason = $current->reason;

        $start = $clicked->copy();
        while (Maintenance::where('date', $start->copy()->subDay()->toDateString())->where('wismaID', $wismaID)->exists()) {
            $start->subDay();
        }

        $end = $clicked->copy();
        while (Maintenance::where('date', $end->copy()->addDay()->toDateString())->where('wismaID', $wismaID)->exists()) {
            $end->addDay();
        }

        return [$start->toDateString(), $end->toDateString(), $reason ?? ''];
    }

    protected function getActions(): array
    {
        return [
            Action::make('tambahEvent')
                ->label('+ Tambah Event')
                ->modalHeading('Tambah Event Kalender')
                ->schema($this->eventFormSchema())
                ->fillForm(fn (array $arguments): array => [
                    'date_start' => $arguments['tanggal'] ?? now()->toDateString(),
                ])
                ->afterFormFilled(fn () => $this->prevWismaIds = []) // DITAMBAHKAN
                ->action(function (array $data) {
                    $this->handleCreateEvent($data);
                }),

            Action::make('editEvent')
                ->label('Edit Event')
                ->modalHeading('Edit Event Kalender')
                ->schema($this->eventFormSchema())
                ->fillForm(function (array $arguments): array {
                        $wismaIds = $arguments['wismaID']
                        ? [$arguments['wismaID']]
                        : array_merge(['__all__'], Wisma::where('is_active', true)->pluck('wismaID')->toArray());

                    $this->prevWismaIds = $wismaIds;
                    return [
                        'jenis'        => $arguments['type'],
                        'name'         => $arguments['type'] === 'holiday' ? $arguments['originalName'] : null,
                        'reason'       => $arguments['type'] === 'maintenance' ? $arguments['originalName'] : null,
                        'wisma_ids' => $arguments['wismaID']
                            ? [$arguments['wismaID']]
                            : array_merge(['__all__'], Wisma::where('is_active', true)->pluck('wismaID')->toArray()),
                        'date_start'   => $arguments['rangeStart'],
                        'date_end'     => $arguments['rangeStart'] === $arguments['rangeEnd'] ? null : $arguments['rangeEnd'],
                    ];
                })
                ->extraModalFooterActions([
                    Action::make('deleteEvent')
                        ->label('Hapus')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalDescription('Seluruh rentang tanggal event ini akan dihapus permanen.')
                        ->action(function () {
                            $this->handleDeleteEvent($this->currentEditArguments);
                        }),
                ])
                ->action(function (array $data, array $arguments) {
                    $this->handleEditEvent($data, $arguments);
                }),
        ];
    }

    private function eventFormSchema(): array
    {
        return [
            Radio::make('jenis')
                ->label('Jenis Event')
                ->options([
                    'holiday'     => 'Libur Nasional',
                    'maintenance' => 'Maintenance Wisma',
                ])
                ->default('holiday')
                ->reactive()
                ->required(),

            TextInput::make('name')
                ->label('Nama Libur')
                ->required()
                ->maxLength(255)
                ->visible(fn (Get $get) => $get('jenis') === 'holiday'),

            CheckboxList::make('wisma_ids')
                ->label('Berlaku Untuk')
                ->options(fn () => collect(['__all__' => 'Semua Wisma'])
                    ->merge(Wisma::where('is_active', true)->pluck('name', 'wismaID')))
                ->reactive()
                ->afterStateUpdated(function ($state, callable $set) {
                    $this->syncWismaCheckboxes($state ?? [], $set);
                })
                ->visible(fn (Get $get) => $get('jenis') === 'maintenance')
                ->required(fn (Get $get) => $get('jenis') === 'maintenance'),

            Textarea::make('reason')
                ->label('Alasan Maintenance')
                ->visible(fn (Get $get) => $get('jenis') === 'maintenance')
                ->maxLength(500),

            DatePicker::make('date_start')
                ->label('Tanggal Mulai')
                ->required()
                ->native(false),

            DatePicker::make('date_end')
                ->label('Tanggal Selesai (opsional)')
                ->native(false)
                ->afterOrEqual('date_start')
                ->helperText('Kosongkan jika hanya 1 hari.'),
        ];
    }

    private function handleCreateEvent(array $data): void
    {
        $period = $this->buildPeriod($data);

        if ($data['jenis'] === 'holiday') {
            $this->validateHolidayNotExists($period);
            $this->insertHolidays($period, $data['name']);
            Notification::make()->title($period->count() . ' hari libur berhasil ditambahkan')->success()->send();
            return;
        }

        $wismaIDs = $this->resolveWismaIDs($data);
        $this->insertMaintenance($period, $wismaIDs, $data['reason'] ?? null);
        Notification::make()->title('Maintenance berhasil ditambahkan')->success()->send();
    }

    private function handleEditEvent(array $data, array $arguments): void
    {
        $newPeriod = $this->buildPeriod($data);

        DB::transaction(function () use ($arguments, $newPeriod, $data) {
            $this->deleteOriginalRange($arguments);

            if ($data['jenis'] === 'holiday') {
                $this->validateHolidayNotExists($newPeriod);
                $this->insertHolidays($newPeriod, $data['name']);
                return;
            }

            $wismaIDs = $this->resolveWismaIDs($data);
            $this->insertMaintenance($newPeriod, $wismaIDs, $data['reason'] ?? null);
        });

        Notification::make()->title('Event berhasil diperbarui')->success()->send();
    }

    private function handleDeleteEvent(array $arguments): void
    {
        $this->deleteOriginalRange($arguments);
        Notification::make()->title('Event berhasil dihapus')->success()->send();
    }

    private function deleteOriginalRange(array $arguments): void
    {
        $period = collect(CarbonPeriod::create($arguments['rangeStart'], $arguments['rangeEnd']))
            ->map(fn (Carbon $d) => $d->toDateString());

        if ($arguments['type'] === 'holiday') {
            Holiday::whereIn('date', $period)->delete();
            return;
        }

        Maintenance::whereIn('date', $period)
            ->where('wismaID', $arguments['wismaID'])
            ->delete();
    }

    private function buildPeriod(array $data): \Illuminate\Support\Collection
    {
        $startDate = Carbon::parse($data['date_start']);
        $endDate   = isset($data['date_end']) && $data['date_end']
            ? Carbon::parse($data['date_end'])
            : $startDate->copy();

        return collect(CarbonPeriod::create($startDate, $endDate))
            ->map(fn (Carbon $d) => $d->toDateString());
    }

    private function validateHolidayNotExists($period): void
    {
        $existing = Holiday::whereIn('date', $period)->pluck('date')
            ->map(fn ($d) => Carbon::parse($d)->toDateString());

        if ($existing->isNotEmpty()) {
            throw ValidationException::withMessages([
                'date_start' => 'Tanggal ' . $existing->implode(', ') . ' sudah terdaftar sebagai hari libur lain.',
            ]);
        }
    }

    private function resolveWismaIDs(array $data): array
    {
        $selected = array_values(array_diff($data['wisma_ids'] ?? [], ['__all__'])); // DIUBAH — buang pseudo-option

        if (empty($selected)) {
            throw ValidationException::withMessages([
                'wisma_ids' => 'Pilih minimal 1 wisma.',
            ]);
        }

        $allWismaIDs = Wisma::where('is_active', true)->pluck('wismaID')->toArray(); // DITAMBAHKAN
        $selectedSorted = $selected; sort($selectedSorted);
        $allSorted = $allWismaIDs; sort($allSorted);

        if ($selectedSorted === $allSorted && count($allSorted) > 0) { // DITAMBAHKAN
            return [null]; // tetap konsisten: null = berlaku untuk semua wisma di DB
        }

        return $selected;
    }

    private function insertHolidays($period, string $name): void
    {
        DB::transaction(function () use ($period, $name) {
            foreach ($period as $date) {
                Holiday::create([
                    'name' => $name,
                    'date' => $date,
                ]);
            }
        });
    }

    private function insertMaintenance($period, array $wismaIDs, ?string $reason): void
    {
        DB::transaction(function () use ($period, $wismaIDs, $reason) {
            foreach ($wismaIDs as $wismaID) {
                foreach ($period as $date) {
                    Maintenance::firstOrCreate(
                        ['wismaID' => $wismaID, 'date' => $date],
                        ['reason' => $reason]
                    );
                }
            }
        });
    }

        /**
     * Sinkronisasi checkbox "Semua Wisma" dengan checkbox wisma individual.
     * Aturan:
     * - Toggle "Semua Wisma" sendiri -> semua individual ikut centang/uncheck.
     * - Semua individual dicentang manual satu-satu -> "Semua Wisma" ikut nyala.
     * - Salah satu individual di-uncheck -> "Semua Wisma" ikut mati.
     */
    private function syncWismaCheckboxes(array $newSelection, callable $set): void
    {
        $allWismaIDs = Wisma::where('is_active', true)->pluck('wismaID')->toArray();
        $previousSelection = $this->prevWismaIds ?? [];

        $toggledAllWismaDirectly = $this->wasAllWismaToggledDirectly($previousSelection, $newSelection);

        if ($toggledAllWismaDirectly) {
            $nowChecked = in_array('__all__', $newSelection);
            $result = $nowChecked
                ? $this->checkAllWisma($allWismaIDs)
                : []; // uncheck semua
        } else {
            $result = $this->syncBasedOnIndividualCheckboxes($newSelection, $allWismaIDs);
        }

        $set('wisma_ids', $result);
        $this->prevWismaIds = $result;
    }

    /**
     * True kalau yang berubah HANYA status "Semua Wisma", sedangkan
     * checkbox-checkbox individual di bawahnya tidak ikut berubah sama sekali.
     */
    private function wasAllWismaToggledDirectly(array $previousSelection, array $newSelection): bool
    {
        $wasAllChecked = in_array('__all__', $previousSelection);
        $isAllCheckedNow = in_array('__all__', $newSelection);

        $previousIndividualIDs = $this->extractIndividualWismaIDs($previousSelection);
        $newIndividualIDs = $this->extractIndividualWismaIDs($newSelection);

        $onlyAllWismaChanged = $wasAllChecked !== $isAllCheckedNow;
        $individualCheckboxesUnchanged = $this->sameWismaIDs($previousIndividualIDs, $newIndividualIDs);

        return $onlyAllWismaChanged && $individualCheckboxesUnchanged;
    }

    /**
     * Cek checkbox individual: kalau semua wisma aktif sudah tercentang manual,
     * ikutkan "Semua Wisma" ke dalam hasil. Kalau belum lengkap, "Semua Wisma" dilepas.
     */
    private function syncBasedOnIndividualCheckboxes(array $newSelection, array $allWismaIDs): array
    {
        $selectedIndividualIDs = $this->extractIndividualWismaIDs($newSelection);
        $allWismaAreChecked = $this->sameWismaIDs($selectedIndividualIDs, $allWismaIDs) && count($allWismaIDs) > 0;

        return $allWismaAreChecked
            ? array_merge(['__all__'], $selectedIndividualIDs)
            : $selectedIndividualIDs;
    }

    private function checkAllWisma(array $allWismaIDs): array
    {
        return array_merge(['__all__'], $allWismaIDs);
    }

    /** Buang opsi semu "__all__", sisakan wismaID asli saja. */
    private function extractIndividualWismaIDs(array $selection): array
    {
        return array_values(array_diff($selection, ['__all__']));
    }

    /**
     * Bandingkan 2 kumpulan wismaID tanpa peduli urutan.
     * Pakai json_encode (bukan ===) karena perbandingan array PHP
     * gagal match kalau index/urutan beda walau isinya sama.
     */
    private function sameWismaIDs(array $a, array $b): bool
    {
        sort($a);
        sort($b);
        return json_encode($a) === json_encode($b);
    }
}