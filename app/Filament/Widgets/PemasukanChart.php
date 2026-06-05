<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class PemasukanChart extends ChartWidget
{
    protected ?string $heading = 'Grafik Pemasukan';
    protected static ?int $sort = 2;
    protected int|string|array $columnSpan = '1';

    protected ?string $maxHeight = '400px';

    public ?string $filter = 'bulan';

    protected function getFilters(): ?array
    {
        return [
            'bulan'       => 'Per Bulan (Tahun ini)',
            'trisemester' => 'Per Trisemester',
            'semester'    => 'Per Semester',
            'tahun'       => 'Per Tahun',
        ];
    }

    protected function getData(): array
    {
        $filter = $this->filter;

        if ($filter === 'bulan') {
            $labels = [];
            $data   = [];
            for ($m = 1; $m <= 12; $m++) {
                $labels[] = Carbon::create(null, $m)->translatedFormat('M');
                $data[]   = Booking::where('status', 'approved')
                    ->whereYear('check_in', now()->year)
                    ->whereMonth('check_in', $m)
                    ->sum('total_price');
            }
        } elseif ($filter === 'trisemester') {
            $labels = ['Jan–Apr', 'Mei–Agu', 'Sep–Des'];
            $data   = [
                Booking::where('status', 'approved')->whereYear('check_in', now()->year)->whereMonth('check_in', '>=', 1)->whereMonth('check_in', '<=', 4)->sum('total_price'),
                Booking::where('status', 'approved')->whereYear('check_in', now()->year)->whereMonth('check_in', '>=', 5)->whereMonth('check_in', '<=', 8)->sum('total_price'),
                Booking::where('status', 'approved')->whereYear('check_in', now()->year)->whereMonth('check_in', '>=', 9)->whereMonth('check_in', '<=', 12)->sum('total_price'),
            ];
        } elseif ($filter === 'semester') {
            $labels = ['Semester 1 (Jan–Jun)', 'Semester 2 (Jul–Des)'];
            $data   = [
                Booking::where('status', 'approved')->whereYear('check_in', now()->year)->whereMonth('check_in', '<=', 6)->sum('total_price'),
                Booking::where('status', 'approved')->whereYear('check_in', now()->year)->whereMonth('check_in', '>=', 7)->sum('total_price'),
            ];
        } else {
            $years  = range(now()->year - 3, now()->year);
            $labels = array_map('strval', $years);
            $data   = array_map(fn($y) => Booking::where('status', 'approved')->whereYear('check_in', $y)->sum('total_price'), $years);
        }

        return [
            'datasets' => [
                [
                    'label'           => 'Pemasukan (Rp)',
                    'data'            => $data,
                    'borderColor'     => '#00a3ad',
                    'backgroundColor' => 'rgba(0,163,173,0.1)',
                    'fill'            => true,
                    'tension'         => 0.4,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}