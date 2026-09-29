<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use App\Models\Villa;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\ChartWidget\Concerns\HasFiltersSchema;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

class PemasukanVillaChart extends ChartWidget
{
    use HasFiltersSchema;

    protected ?string $heading = 'Pemasukan / Villa';
    protected static ?int $sort = 3;
    protected int|string|array $columnSpan = '1';
    protected ?string $maxHeight = '400px';

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
        $tahun = $this->filters['tahun'] ?? now()->year; // DIUBAH — dari $this->filter, sekarang dari $this->filters['tahun']
        $bulan = $this->filters['bulan'] ?? '';

        $villas = Villa::orderBy('name')->pluck('name', 'villaID');

        $query = Booking::where('status', 'approved')
            ->whereYear('check_in', $tahun);

        if ($bulan !== '') {
            $query->whereMonth('check_in', $bulan);
        }

        $pemasukanPerVilla = $query
            ->selectRaw('villaID, SUM(total_price) as total')
            ->groupBy('villaID')
            ->pluck('total', 'villaID');

        $labels = [];
        $data   = [];

        foreach ($villas as $villaID => $name) {
            $labels[] = $name;
            $data[]   = (float) ($pemasukanPerVilla[$villaID] ?? 0);
        }

        return [
            'datasets' => [
                [
                    'label'           => 'Pemasukan (Rp)',
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
                    'ticks' => [
                        'maxTicksLimit' => 5,
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