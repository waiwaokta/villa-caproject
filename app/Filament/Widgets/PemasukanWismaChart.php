<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use App\Models\Wisma;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\ChartWidget\Concerns\HasFiltersSchema;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

class PemasukanWismaChart extends ChartWidget
{
    use HasFiltersSchema;

    protected ?string $heading = 'Pemasukan / Wisma';
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

        $wismas = Wisma::orderBy('name')->pluck('name', 'wismaID');

        $query = Booking::where('status', 'approved')
            ->whereYear('check_in', $tahun);

        if ($bulan !== '') {
            $query->whereMonth('check_in', $bulan);
        }

        $pemasukanPerWisma = $query
            ->selectRaw('wismaID, SUM(total_price) as total')
            ->groupBy('wismaID')
            ->pluck('total', 'wismaID');

        $labels = [];
        $data   = [];

        foreach ($wismas as $wismaID => $name) {
            $labels[] = $name;
            $data[]   = (float) ($pemasukanPerWisma[$wismaID] ?? 0);
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