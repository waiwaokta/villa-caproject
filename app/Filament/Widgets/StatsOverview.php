<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use App\Models\Wisma;
use App\Models\User;
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
        $totalPemasukan      = Booking::where('status', 'approved')->sum('total_price');
        $totalWisma          = Wisma::count();
        $wismaAktif          = Wisma::where('is_active', true)->count();
        $totalUser           = User::where('role', 'customer')->count();
        $rataRataMenginap = Booking::where('status', 'approved')->avg('total_nights');

        return [
            Stat::make('Total Booking', $totalBooking)
                ->description('Semua booking masuk')
                ->descriptionIcon('heroicon-o-calendar')
                ->color('primary'),

            Stat::make('Menunggu Persetujuan', $bookingPending)
                ->description('Booking belum ditinjau')
                ->descriptionIcon('heroicon-o-clock')
                ->color('warning'),

            Stat::make('Booking Disetujui', $bookingApproved)
                ->description('Booking approved')
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('success'),

            Stat::make('Booking Ditolak', $bookingRejected)
                ->description('Booking rejected')
                ->descriptionIcon('heroicon-o-x-circle')
                ->color('danger'),

            Stat::make('Total Pemasukan', 'Rp ' . number_format($totalPemasukan, 0, ',', '.'))
                ->description('Dari booking approved')
                ->descriptionIcon('heroicon-o-banknotes')
                ->color('success'),

            Stat::make('Wisma Aktif', $wismaAktif . ' / ' . $totalWisma)
                ->description('Tampil di web customer')
                ->descriptionIcon('heroicon-o-home')
                ->color('info'),

            Stat::make('Total Customer', $totalUser)
                ->description('User terdaftar')
                ->descriptionIcon('heroicon-o-users')
                ->color('gray'),

            Stat::make('Rata-rata Menginap', round($rataRataMenginap, 1) . ' malam')
                ->description('Dari booking approved')
                ->descriptionIcon('heroicon-o-moon')
                ->color('info'),
        ];
    }
}