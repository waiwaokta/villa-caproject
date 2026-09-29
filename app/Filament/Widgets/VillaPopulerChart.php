<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use App\Models\Villa;
use Filament\Widgets\ChartWidget;

class VillaPopulerChart extends ChartWidget
{
    protected ?string $heading = 'Villa Terpopuler';
    protected static ?int $sort = 3;
    protected int|string|array $columnSpan = '1';

    protected ?string $maxHeight = '400px';

    public ?string $filter = 'bulan';

    protected function getFilters(): ?array
    {
        return [
            'bulan' => 'Bulan Ini',
            'tahun' => 'Tahun Ini',
        ];
    }

    protected function getData(): array
    {
        $villas = Villa::all();
        $labels = [];
        $data   = [];
        $colors = [];

        // $baseColors = [
        //     '#00a3ad', '#f59e0b', '#4DC951', '#EF2929',
        //     '#6366f1', '#ec4899', '#14b8a6', '#f97316',
        // ];

        foreach ($villas as $i => $villa) {
            $labels[] = $villa->name;
            // $colors[] = $baseColors[$i % count($baseColors)];

            $query = Booking::where('status', 'approved')
                ->where('villaID', $villa->villaID);

            if ($this->filter === 'bulan') {
                $query->whereMonth('check_in', now()->month)
                      ->whereYear('check_in', now()->year);
            } else {
                $query->whereYear('check_in', now()->year);
            }

            $data[] = $query->count();
        }

        return [
            'datasets' => [
                [
                    'label'           => 'Jumlah Booking',
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
                        'stepSize' => 1, // paksa kelipatan bulat, tidak ada desimal
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