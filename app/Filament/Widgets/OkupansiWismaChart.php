<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use App\Models\Wisma;
use Carbon\Carbon;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;

class OkupansiWismaChart extends ChartWidget
{
    protected ?string $heading = 'Tingkat Okupansi Wisma';
    protected static ?int $sort = 4; // taruh setelah widget lain, sesuaikan urutan
    protected ?string $maxHeight = '400px';
    protected int | string | array $columnSpan = 1;

    // filter dropdown, sama pola dengan chart lain di dashboard
    public ?string $filter = 'this_month';

    protected function getFilters(): ?array
    {
        return [
            'this_month' => 'Bulan Ini',
            'this_year'  => 'Tahun Ini (' . now()->year . ')',
        ];
    }

    protected function getData(): array
    {
        [$start, $end] = $this->resolvePeriod();
        $totalHariPeriode = $start->diffInDays($end) + 1;

        $wismaList = Wisma::where('is_active', true)->get();

        $labels = [];
        $persentase = [];

        foreach ($wismaList as $wisma) {
            // hitung total night terpakai dari booking approved yang overlap dengan periode
            $bookedNights = Booking::where('wismaID', $wisma->wismaID)
                ->where('status', 'approved')
                ->where('check_in', '<=', $end)
                ->where('check_out', '>=', $start)
                ->get()
                ->sum(function ($booking) use ($start, $end) {
                    // clamp rentang booking ke rentang periode, biar booking yang
                    // menjorok keluar periode tidak dihitung berlebih
                    $effectiveStart = $booking->check_in->max($start);
                    $effectiveEnd = $booking->check_out->min($end);
                    return max(0, $effectiveStart->diffInDays($effectiveEnd));
                });

            $labels[] = $wisma->name;
            $persentase[] = $totalHariPeriode > 0
                ? round(($bookedNights / $totalHariPeriode) * 100, 1)
                : 0;
        }

        return [
            'datasets' => [
                [
                    'label'           => 'Okupansi (%)',
                    'data'            => $persentase,
                    'backgroundColor' => array_map(fn ($p) => $this->backgroundColorByRate($p), $persentase),
                    'borderColor'     => array_map(fn ($p) => $this->borderColorByRate($p), $persentase),
                    'borderWidth'     => 1.5,
                    'borderRadius'    => 4,
                ],
            ],
            'labels' => $labels,
        ];
    }

    private function resolvePeriod(): array
    {
        return match ($this->filter) {
            'this_year' => [now()->startOfYear(), now()->endOfYear()],
            default     => [now()->startOfMonth(), now()->endOfMonth()],
        };
    }

    // warna latar (gelap/transparan), tergantung tingkat okupansi
    private function backgroundColorByRate(float $rate): string
    {
        return match (true) {
            $rate >= 70 => '#00a4ad36', // teal transparan — okupansi tinggi, sehat
            $rate >= 50 => '#6b728036', // abu transparan — okupansi sedang
            default     => '#ef444436', // merah transparan — okupansi rendah, perlu perhatian
        };
    }

    // warna border (terang/solid), pasangan dari background di atas
    private function borderColorByRate(float $rate): string
    {
        return match (true) {
            $rate >= 70 => '#00a3ad', // teal solid
            $rate >= 50 => '#6b7280', // abu solid
            default     => '#ef4444', // merah solid
        };
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): RawJs
    {
        return RawJs::make(<<<'JS'
        {
            indexAxis: 'y',
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom',
                    labels: {
                        color: document.documentElement.classList.contains('dark') ? '#9ca3af' : '#374151',
                        generateLabels: function(chart) {
                            const isDark = document.documentElement.classList.contains('dark');
                            const textColor = isDark ? '#9ca3af' : '#374151';
                            return [
                                { text: '< 50% Rendah', fillStyle: '#ef4444', strokeStyle: '#ef4444', fontColor: textColor },
                                { text: '50-69% Sedang', fillStyle: '#6b7280', strokeStyle: '#6b7280', fontColor: textColor },
                                { text: '>= 70% Tinggi', fillStyle: '#00a3ad', strokeStyle: '#00a3ad', fontColor: textColor },
                            ];
                        },
                    },
                },
            },
            scales: {
                x: {
                    beginAtZero: true,
                    max: 100,
                    ticks: {
                        callback: function(value) { return value + '%'; },
                    },
                },
            },
        }
        JS);
    }
}