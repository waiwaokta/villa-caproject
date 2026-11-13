<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use App\Models\Villa;
use Carbon\Carbon;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\ChartWidget\Concerns\HasFiltersSchema;

class PemasukanVillaChart extends ChartWidget
{
    use HasFiltersSchema;

    protected ?string $heading = 'Pemasukan / Villa';
    protected static ?int $sort = 5;
    protected int|string|array $columnSpan = '1';
    protected ?string $maxHeight = '400px';

    public function filtersSchema(Schema $schema): Schema
    {
        $tahunSekarang = now()->year;
        $tahunList     = range($tahunSekarang, $tahunSekarang - 3);

        $tahunOptions = array_combine(
            array_map('strval', $tahunList),
            array_map('strval', $tahunList)
        );

        $bulanOptions = [
            '1' => 'Januari', '2' => 'Februari', '3' => 'Maret', '4' => 'April',
            '5' => 'Mei', '6' => 'Juni', '7' => 'Juli', '8' => 'Agustus',
            '9' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember',
        ];

        return $schema->components([
            Select::make('tahun')
                ->label('Tahun')
                ->options($tahunOptions)
                ->selectablePlaceholder(false)
                ->default((string) $tahunSekarang),

            Select::make('periode')
                ->label('Periode')
                ->options([
                    'semua'    => 'Semua Bulan',
                    'semester' => 'Per Semester',
                    'kuartal'  => 'Per Kuartal',
                    'bulan'    => 'Per Bulan',
                ])
                ->selectablePlaceholder(false)
                ->default('semua')
                ->live(), // supaya pilihan rinci langsung muncul atau hilang

            Select::make('kuartal')
                ->label('Kuartal')
                ->options([
                    '1' => 'Q1 (Jan–Mar)',
                    '2' => 'Q2 (Apr–Jun)',
                    '3' => 'Q3 (Jul–Sep)',
                    '4' => 'Q4 (Okt–Des)',
                ])
                ->selectablePlaceholder(false)
                ->default((string) now()->quarter) // default kuartal berjalan
                ->visible(fn (Get $get): bool => $get('periode') === 'kuartal'),

            Select::make('semester')
                ->label('Semester')
                ->options([
                    '1' => 'Semester 1 (Jan–Jun)',
                    '2' => 'Semester 2 (Jul–Des)',
                ])
                ->selectablePlaceholder(false)
                ->default(now()->month <= 6 ? '1' : '2') // default semester berjalan
                ->visible(fn (Get $get): bool => $get('periode') === 'semester'),

            Select::make('bulan')
                ->label('Bulan')
                ->options($bulanOptions)
                ->selectablePlaceholder(false)
                ->default((string) now()->month) // default bulan berjalan
                ->visible(fn (Get $get): bool => $get('periode') === 'bulan'),
        ]);
    }

    protected function getData(): array
    {
        $tahun   = (int) ($this->filters['tahun'] ?? now()->year);
        $periode = $this->filters['periode'] ?? 'semua';

        $awal = match ($periode) { // batas awal rentang sesuai periode
            'kuartal'  => Carbon::create($tahun, (((int) ($this->filters['kuartal'] ?? now()->quarter)) - 1) * 3 + 1, 1),
            'semester' => Carbon::create($tahun, ((int) ($this->filters['semester'] ?? (now()->month <= 6 ? 1 : 2))) === 1 ? 1 : 7, 1),
            'bulan'    => Carbon::create($tahun, (int) ($this->filters['bulan'] ?? now()->month), 1),
            default    => Carbon::create($tahun, 1, 1),
        };

        $akhir = match ($periode) { // batas akhir rentang
            'kuartal'  => $awal->copy()->addMonths(2)->endOfMonth(),
            'semester' => $awal->copy()->addMonths(5)->endOfMonth(),
            'bulan'    => $awal->copy()->endOfMonth(),
            default    => $awal->copy()->endOfYear(),
        };

        $villas = Villa::orderBy('name')->pluck('name', 'villaID');

        $rekap = Booking::where('status', 'approved')
            ->whereBetween('check_in', [$awal->toDateString(), $akhir->toDateString()])
            ->selectRaw('villaID, SUM(total_price) as total, COUNT(*) as jumlah') // jumlah booking ditambahkan
            ->groupBy('villaID')
            ->get()
            ->keyBy('villaID');

        $labels        = [];
        $data          = [];
        $totalBooking  = [];

        foreach ($villas as $villaID => $name) {
            $labels[]       = $name;
            $data[]         = (float) ($rekap[$villaID]->total ?? 0);
            $totalBooking[] = (int) ($rekap[$villaID]->jumlah ?? 0); // dibaca tooltip lewat dataset.totalBooking
        }

        return [
            'datasets' => [
                [
                    'label'           => 'Pemasukan (Rp)',
                    'data'            => $data,
                    'totalBooking'    => $totalBooking, // data tambahan khusus tooltip
                    'borderColor'     => '#00a3ad',
                    'backgroundColor' => '#00a4ad36',
                    'borderWidth'     => 1.5,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getOptions(): RawJs
    {
        return RawJs::make(<<<'JS'
        {
            scales: {
                y: {
                    ticks: { maxTicksLimit: 5 }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: (ctx) => 'Pemasukan: Rp ' + Number(ctx.parsed.y).toLocaleString('id-ID'),
                        afterLabel: (ctx) => 'Total booking: ' + ((ctx.dataset.totalBooking || [])[ctx.dataIndex] ?? 0) + ' booking'
                    }
                }
            }
        }
        JS);
    }

    protected function getType(): string
    {
        return 'bar';
    }
}