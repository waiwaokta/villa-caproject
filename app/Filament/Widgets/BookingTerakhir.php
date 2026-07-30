<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use App\Filament\Resources\Bookings\BookingResource;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class BookingTerakhir extends BaseWidget
{
    protected static ?string $heading = 'Booking Terbaru';
    protected static ?int $sort = 5;
    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(fn() => Booking::query()
                ->with('wisma')
                ->latest('created_at')
                ->limit(5)
            )
            ->columns([
                TextColumn::make('bookingID')
                    ->label('Kode Booking')
                    ->weight(FontWeight::Bold)
                    ->fontFamily('mono')
                    ->copyable(),

                TextColumn::make('wisma.name')
                    ->label('Wisma'),

                TextColumn::make('guest_name')
                    ->label('Nama Tamu'),

                TextColumn::make('guest_phone')
                    ->label('No. HP'),

                TextColumn::make('check_in')
                    ->label('Check-in')
                    ->date('d M Y'),

                TextColumn::make('check_out')
                    ->label('Check-out')
                    ->date('d M Y'),

                TextColumn::make('total_price')
                    ->label('Total')
                    ->money('IDR'),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn($state) => match($state) {
                        'pending'  => 'Menunggu',
                        'approved' => 'Disetujui',
                        'rejected' => 'Ditolak',
                        default    => $state,
                    })
                    ->color(fn($state) => match($state) {
                        'pending'  => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default    => 'gray',
                    }),
            ])
            ->recordActions([
                ViewAction::make()
                    ->url(fn($record) => BookingResource::getUrl('view', ['record' => $record])),
            ])
            ->paginated(false)
            ->emptyStateHeading('Belum ada booking')
            ->emptyStateDescription('Booking dari customer akan muncul di sini')
            ->emptyStateActions([
                Action::make('laporan')
                    ->label('Lihat Laporan')
                    ->url('/admin/laporan-pemasukan')
                    ->icon('heroicon-m-chart-bar')
                    ->button(),
            ]);
    }
}