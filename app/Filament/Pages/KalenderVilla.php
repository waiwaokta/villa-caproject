<?php

namespace App\Filament\Pages;

use App\Models\Holiday;
use App\Models\Maintenance;
use App\Models\Villa;
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

class KalenderVilla extends Page
{
    protected static string| BackedEnum |null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static ?string $navigationLabel = 'Kalender Villa';

    protected static ?string $title = 'Kalender Villa';

    protected static string | UnitEnum | null $navigationGroup = 'Villa';

    protected static ?string $pluralModelLabel = 'Kalender Villa';

    protected static ?int $navigationSort = 3;

    protected string $view = 'filament.pages.kalender-villa';

    public int $bulan;
    public int $tahun;
    public ?array $currentEditArguments = null;

    public ?array $prevVillaIds = null;

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
        $maintenance = Maintenance::with('villa')
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
                'label'         => $item->villa ? $item->villa->name : 'Semua Villa',
                'reason'        => $item->reason,
                'villaID'       => $item->villaID,
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

    public function openEditEventModal(string $type, string $tanggal, ?string $villaID = null): void
    {
        // DITAMBAHKAN — guard server-side, sama seperti openAddEventModal
        if (Carbon::parse($tanggal)->lt(Carbon::today())) {
            Notification::make()
                ->title('Tidak bisa mengubah event di tanggal yang sudah lewat')
                ->danger()
                ->send();
            return;
        }

        [$rangeStart, $rangeEnd, $name] = $this->resolveEventRange($type, $tanggal, $villaID);

        $this->currentEditArguments = [
            'type'         => $type,
            'villaID'      => $villaID,
            'rangeStart'   => $rangeStart,
            'rangeEnd'     => $rangeEnd,
            'originalName' => $name,
        ];

        $this->mountAction('editEvent', $this->currentEditArguments);
    }

    /**
     * Rekonstruksi rentang tanggal asli dari 1 tanggal yang diklik —
     * cek mundur & maju selama nama/villa sama dan tanggal berdekatan.
     */
    private function resolveEventRange(string $type, string $tanggal, ?string $villaID): array
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
        $current = Maintenance::where('date', $tanggal)->where('villaID', $villaID)->first();
        if (!$current) {
            return [$tanggal, $tanggal, ''];
        }
        $reason = $current->reason;

        $start = $clicked->copy();
        while (Maintenance::where('date', $start->copy()->subDay()->toDateString())->where('villaID', $villaID)->exists()) {
            $start->subDay();
        }

        $end = $clicked->copy();
        while (Maintenance::where('date', $end->copy()->addDay()->toDateString())->where('villaID', $villaID)->exists()) {
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
                ->afterFormFilled(fn () => $this->prevVillaIds = []) // DITAMBAHKAN
                ->action(function (array $data) {
                    $this->handleCreateEvent($data);
                }),

            Action::make('editEvent')
                ->label('Edit Event')
                ->modalHeading('Edit Event Kalender')
                ->schema($this->eventFormSchema())
                ->fillForm(function (array $arguments): array {
                        $villaIds = $arguments['villaID']
                        ? [$arguments['villaID']]
                        : array_merge(['__all__'], Villa::where('is_active', true)->pluck('villaID')->toArray());

                    $this->prevVillaIds = $villaIds;
                    return [
                        'jenis'        => $arguments['type'],
                        'name'         => $arguments['type'] === 'holiday' ? $arguments['originalName'] : null,
                        'reason'       => $arguments['type'] === 'maintenance' ? $arguments['originalName'] : null,
                        'villa_ids' => $arguments['villaID']
                            ? [$arguments['villaID']]
                            : array_merge(['__all__'], Villa::where('is_active', true)->pluck('villaID')->toArray()),
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
                    'maintenance' => 'Maintenance Villa',
                ])
                ->default('holiday')
                ->reactive()
                ->required(),

            TextInput::make('name')
                ->label('Nama Libur')
                ->required()
                ->maxLength(255)
                ->visible(fn (Get $get) => $get('jenis') === 'holiday'),

            CheckboxList::make('villa_ids')
                ->label('Berlaku Untuk')
                ->options(fn () => collect(['__all__' => 'Semua Villa'])
                    ->merge(Villa::where('is_active', true)->pluck('name', 'villaID')))
                ->reactive()
                ->afterStateUpdated(function ($state, callable $set) {
                    $this->syncVillaCheckboxes($state ?? [], $set);
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
                ->native(false)
                ->minDate(now()->toDateString()),

            DatePicker::make('date_end')
                ->label('Tanggal Selesai (opsional)')
                ->native(false)
                ->afterOrEqual('date_start')
                ->minDate(now()->toDateString())
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

        $villaIDs = $this->resolveVillaIDs($data);
        $this->insertMaintenance($period, $villaIDs, $data['reason'] ?? null);
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

            $villaIDs = $this->resolveVillaIDs($data);
            $this->insertMaintenance($newPeriod, $villaIDs, $data['reason'] ?? null);
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
            ->where('villaID', $arguments['villaID'])
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

    private function resolveVillaIDs(array $data): array
    {
        $selected = array_values(array_diff($data['villa_ids'] ?? [], ['__all__'])); // DIUBAH — buang pseudo-option

        if (empty($selected)) {
            throw ValidationException::withMessages([
                'villa_ids' => 'Pilih minimal 1 villa.',
            ]);
        }

        $allVillaIDs = Villa::where('is_active', true)->pluck('villaID')->toArray(); // DITAMBAHKAN
        $selectedSorted = $selected; sort($selectedSorted);
        $allSorted = $allVillaIDs; sort($allSorted);

        if ($selectedSorted === $allSorted && count($allSorted) > 0) { // DITAMBAHKAN
            return [null]; // tetap konsisten: null = berlaku untuk semua villa di DB
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

    private function insertMaintenance($period, array $villaIDs, ?string $reason): void
    {
        DB::transaction(function () use ($period, $villaIDs, $reason) {
            foreach ($villaIDs as $villaID) {
                foreach ($period as $date) {
                    Maintenance::firstOrCreate(
                        ['villaID' => $villaID, 'date' => $date],
                        ['reason' => $reason]
                    );
                }
            }
        });
    }

        /**
     * Sinkronisasi checkbox "Semua Villa" dengan checkbox villa individual.
     * Aturan:
     * - Toggle "Semua Villa" sendiri -> semua individual ikut centang/uncheck.
     * - Semua individual dicentang manual satu-satu -> "Semua Villa" ikut nyala.
     * - Salah satu individual di-uncheck -> "Semua Villa" ikut mati.
     */
    private function syncVillaCheckboxes(array $newSelection, callable $set): void
    {
        $allVillaIDs = Villa::where('is_active', true)->pluck('villaID')->toArray();
        $previousSelection = $this->prevVillaIds ?? [];

        $toggledAllVillaDirectly = $this->wasAllVillaToggledDirectly($previousSelection, $newSelection);

        if ($toggledAllVillaDirectly) {
            $nowChecked = in_array('__all__', $newSelection);
            $result = $nowChecked
                ? $this->checkAllVilla($allVillaIDs)
                : []; // uncheck semua
        } else {
            $result = $this->syncBasedOnIndividualCheckboxes($newSelection, $allVillaIDs);
        }

        $set('villa_ids', $result);
        $this->prevVillaIds = $result;
    }

    /**
     * True kalau yang berubah HANYA status "Semua Villa", sedangkan
     * checkbox-checkbox individual di bawahnya tidak ikut berubah sama sekali.
     */
    private function wasAllVillaToggledDirectly(array $previousSelection, array $newSelection): bool
    {
        $wasAllChecked = in_array('__all__', $previousSelection);
        $isAllCheckedNow = in_array('__all__', $newSelection);

        $previousIndividualIDs = $this->extractIndividualVillaIDs($previousSelection);
        $newIndividualIDs = $this->extractIndividualVillaIDs($newSelection);

        $onlyAllVillaChanged = $wasAllChecked !== $isAllCheckedNow;
        $individualCheckboxesUnchanged = $this->sameVillaIDs($previousIndividualIDs, $newIndividualIDs);

        return $onlyAllVillaChanged && $individualCheckboxesUnchanged;
    }

    /**
     * Cek checkbox individual: kalau semua villa aktif sudah tercentang manual,
     * ikutkan "Semua Villa" ke dalam hasil. Kalau belum lengkap, "Semua Villa" dilepas.
     */
    private function syncBasedOnIndividualCheckboxes(array $newSelection, array $allVillaIDs): array
    {
        $selectedIndividualIDs = $this->extractIndividualVillaIDs($newSelection);
        $allVillaAreChecked = $this->sameVillaIDs($selectedIndividualIDs, $allVillaIDs) && count($allVillaIDs) > 0;

        return $allVillaAreChecked
            ? array_merge(['__all__'], $selectedIndividualIDs)
            : $selectedIndividualIDs;
    }

    private function checkAllVilla(array $allVillaIDs): array
    {
        return array_merge(['__all__'], $allVillaIDs);
    }

    /** Buang opsi semu "__all__", sisakan villaID asli saja. */
    private function extractIndividualVillaIDs(array $selection): array
    {
        return array_values(array_diff($selection, ['__all__']));
    }

    /**
     * Bandingkan 2 kumpulan villaID tanpa peduli urutan.
     * Pakai json_encode (bukan ===) karena perbandingan array PHP
     * gagal match kalau index/urutan beda walau isinya sama.
     */
    private function sameVillaIDs(array $a, array $b): bool
    {
        sort($a);
        sort($b);
        return json_encode($a) === json_encode($b);
    }
    public function getCachedHeaderActions(): array // DIUBAH — dari protected ke public, karena parent class Page mendeklarasikan method ini sebagai public
    {
        return [];
    }
}