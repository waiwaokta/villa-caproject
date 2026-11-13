<?php

namespace App\Filament\Widgets;

use App\Models\Villa;
use App\Models\VillaView;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\ChartWidget\Concerns\HasFiltersSchema;

class KunjunganVillaChart extends ChartWidget
{
    use HasFiltersSchema;
    protected ?string $heading = 'Kunjungan Villa (Klik)';
    protected static ?int $sort = 2;
    protected ?string $maxHeight = '400px';
    protected int | string | array $columnSpan = 1;

    public function filtersSchema(Schema $schema): Schema
    {
        $tahunSekarang = now()->year;
        $tahunList = range($tahunSekarang, $tahunSekarang - 3);

        $tahunOptions = array_combine(
            array_map('strval', $tahunList),
            array_map('strval', $tahunList)
        );

        $bulanOptions = [
            ''   => 'Semua Bulan',
            '1'  => 'Januari',
            '2'  => 'Februari',
            '3'  => 'Maret',
            '4'  => 'April',
            '5'  => 'Mei',
            '6'  => 'Juni',
            '7'  => 'Juli',
            '8'  => 'Agustus',
            '9'  => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember',
        ];

        return $schema->components([
            Select::make('tahun')
                ->label('Tahun')
                ->options($tahunOptions)
                ->selectablePlaceholder(false)
                ->default((string) $tahunSekarang),

            Select::make('bulan')
                ->label('Bulan')
                ->options($bulanOptions)
                ->selectablePlaceholder(false)
                ->default(''),
        ]);
    }

    protected function getData(): array
    {
        $tahun = (int) ($this->filters['tahun'] ?? now()->year);
        $bulan = $this->filters['bulan'] ?? (string) now()->month;

        if ($bulan === '') {
            $awal  = Carbon::create($tahun, 1, 1)->startOfYear();
            $akhir = $awal->copy()->endOfYear();
        } else {
            $awal  = Carbon::create($tahun, (int) $bulan, 1)->startOfMonth();
            $akhir = $awal->copy()->endOfMonth();
        }

        $villas = Villa::orderBy('name')->pluck('name', 'villaID');

        $klikPerVilla = VillaView::whereBetween('viewed_at', [$awal, $akhir])
            ->selectRaw('villaID, COUNT(*) as total')
            ->groupBy('villaID')
            ->pluck('total', 'villaID');

        $labels = [];
        $data   = [];

        foreach ($villas as $villaID => $name) {
            $labels[] = $name;
            $data[]   = (int) ($klikPerVilla[$villaID] ?? 0);
        }

        return [
            'datasets' => [
                [
                    'label'           => 'Jumlah Klik',
                    'data'            => $data,
                    'borderColor'     => '#00a3ad',
                    'backgroundColor' => '#00a4ad36',
                    'borderWidth'     => 1.5,
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
                    'beginAtZero' => true,
                    'ticks' => [
                        'maxTicksLimit' => 5,
                        'precision'     => 0,
                    ],
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}