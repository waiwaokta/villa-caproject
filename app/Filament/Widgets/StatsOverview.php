<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getColumns(): int
    {
        return 4;
    }
    protected function getStats(): array
    {
        $totalBooking        = Booking::count();
        $bookingPending      = Booking::where('status', 'pending')->count();
        $bookingApproved     = Booking::where('status', 'approved')->count();
        $bookingRejected     = Booking::where('status', 'rejected')->count();

        return [
            Stat::make('Total Booking', $totalBooking)
                ->description('Tamu')
                ->descriptionIcon('heroicon-o-calendar')
                ->color('primary'),

            Stat::make('Menunggu Persetujuan', $bookingPending)
                ->description('Booking')
                ->descriptionIcon('heroicon-o-clock')
                ->color('warning'),

            Stat::make('Booking Disetujui', $bookingApproved)
                ->description('Booking')
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('success'),

            Stat::make('Booking Ditolak', $bookingRejected)
                ->description('Booking')
                ->descriptionIcon('heroicon-o-x-circle')
                ->color('danger'),
        ];
    }
}