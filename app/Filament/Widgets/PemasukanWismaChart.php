<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use App\Models\Wisma;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class PemasukanWismaChart extends ChartWidget
{
    protected ?string $heading = 'Pemasukan / Wisma';
    protected static ?int $sort = 3;
    protected int|string|array $columnSpan = '1';
    protected ?string $maxHeight = '400px';

    public ?string $filter = null;

    protected function getFilters(): ?array
    {
        $tahunTermuda = Booking::min('check_in');
        $tahunAwal    = $tahunTermuda ? Carbon::parse($tahunTermuda)->year : now()->year;

        $tahunList = range(now()->year, $tahunAwal);

        return array_combine(
            array_map('strval', $tahunList),
            array_map('strval', $tahunList)
        );
    }

    public function getDefaultFilter(): ?string
    {
        return (string) now()->year;
    }

    protected function getData(): array
    {
        $tahun = $this->filter ?? now()->year;

        $wismas = Wisma::orderBy('name')->pluck('name', 'wismaID');

        $pemasukanPerWisma = Booking::where('status', 'approved')
            ->whereYear('check_in', $tahun)
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