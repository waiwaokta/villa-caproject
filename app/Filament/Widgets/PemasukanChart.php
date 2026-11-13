<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use Carbon\Carbon;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\ChartWidget\Concerns\HasFiltersSchema;

class PemasukanChart extends ChartWidget
{
    use HasFiltersSchema; // filter berbasis schema, menggantikan getFilters() dan $filter

    protected ?string $heading = 'Grafik Penghasilan';
    protected static ?int $sort = 4;
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
                    'semua'    => 'Semua Bulan', // 12 titik, Januari sampai Desember
                    'semester' => 'Per Semester', // 2 titik, tiap 6 bulan
                    'kuartal'  => 'Per Kuartal', // 4 titik, tiap 3 bulan
                    'bulan'    => 'Per Bulan', // harian dalam satu bulan
                ])
                ->selectablePlaceholder(false)
                ->default('semua')
                ->live(),

            Select::make('bulan')
                ->label('Bulan')
                ->options($bulanOptions)
                ->selectablePlaceholder(false)
                ->default((string) now()->month)
                ->visible(fn (Get $get): bool => $get('periode') === 'bulan'), // hanya tampil untuk Per Bulan
        ]);
    }

    protected function getData(): array
    {
        $tahun   = (int) ($this->filters['tahun'] ?? now()->year);
        $periode = $this->filters['periode'] ?? 'semua';
        $bulan   = (int) ($this->filters['bulan'] ?? now()->month);

        if ($periode === 'bulan') {
            $perHari = Booking::where('status', 'approved')
                ->whereYear('check_in', $tahun)
                ->whereMonth('check_in', $bulan)
                ->selectRaw('DAY(check_in) as hari, SUM(total_price) as total')
                ->groupBy('hari')
                ->pluck('total', 'hari'); // satu query untuk semua hari

            $hari   = range(1, Carbon::create($tahun, $bulan, 1)->daysInMonth); // jumlah hari sesuai bulan
            $labels = array_map('strval', $hari);
            $data   = array_map(fn ($d) => (float) ($perHari[$d] ?? 0), $hari);
        } else {
            $perBulan = Booking::where('status', 'approved')
                ->whereYear('check_in', $tahun)
                ->selectRaw('MONTH(check_in) as bln, SUM(total_price) as total')
                ->groupBy('bln')
                ->pluck('total', 'bln'); // satu query, periode lain dihitung dari hasil ini

            $jumlah = fn (int $dari, int $sampai): float => (float) collect(range($dari, $sampai))
                ->sum(fn ($m) => $perBulan[$m] ?? 0); // jumlahkan bulan dari sampai

            if ($periode === 'kuartal') {
                $labels = ['Q1 (Jan–Mar)', 'Q2 (Apr–Jun)', 'Q3 (Jul–Sep)', 'Q4 (Okt–Des)'];
                $data   = [$jumlah(1, 3), $jumlah(4, 6), $jumlah(7, 9), $jumlah(10, 12)];
            } elseif ($periode === 'semester') {
                $labels = ['Semester 1 (Jan–Jun)', 'Semester 2 (Jul–Des)'];
                $data   = [$jumlah(1, 6), $jumlah(7, 12)];
            } else { // semua bulan
                $labels = [];
                $data   = [];
                for ($m = 1; $m <= 12; $m++) {
                    $labels[] = Carbon::create(null, $m)->translatedFormat('M');
                    $data[]   = (float) ($perBulan[$m] ?? 0);
                }
            }
        }

        return [
            'datasets' => [
                [
                    'label'           => 'Pemasukan (Rp)',
                    'data'            => $data,
                    'borderColor'     => '#00a3ad',
                    'backgroundColor' => '#00a4ad36',
                    'fill'            => true,
                    'tension'         => 0.4,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'beginAtZero' => true, // sumbu mulai dari 0
                    'ticks' => [
                        'maxTicksLimit' => 6,
                    ],
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}