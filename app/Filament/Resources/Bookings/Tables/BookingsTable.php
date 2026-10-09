<?php

namespace App\Filament\Resources\Bookings\Tables;

use App\Filament\Actions\CancelBookingAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Actions\Action;
use Illuminate\Database\Eloquent\Model;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Filament\Support\Enums\FontWeight;
use Filament\Notifications\Notification;
use App\Services\FonnteService;
use Illuminate\Support\Facades\Log;
use App\Services\MidtransService;


class BookingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // TextColumn::make('bookingID')
                //     ->label('Kode Booking')
                //     ->searchable()
                //     ->copyable()
                //     ->fontFamily('mono')
                //     ->weight(\Filament\Support\Enums\FontWeight::Bold),

                TextColumn::make('villa.name')
                    ->label('Villa')
                    ->weight(FontWeight::Bold)
                    ->searchable()
                    ->sortable(),

                TextColumn::make('guest_name')
                    ->label('Nama Tamu')
                    ->limit(15)
                    ->weight(FontWeight::Bold)
                    ->searchable(),

                TextColumn::make('guest_phone')
                    ->label('No. Telp')
                    ->searchable(),

                TextColumn::make('check_in')
                    ->label('Check-in')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('check_out')
                    ->label('Check-out')
                    ->date('d M Y')
                    ->sortable(),

                // TextColumn::make('total_nights')
                //     ->label('Malam')
                //     ->suffix(' malam')
                //     ->sortable(),

                TextColumn::make('total_price')
                    ->label('Total')
                    ->money('IDR', locale: 'id')
                    ->sortable(),

                TextColumn::make('paid_at')
                    ->label('Dibayar')
                    ->dateTime('d M Y, H:i', 'Asia/Jakarta') // waktu bayar dalam WIB
                    ->placeholder('-')
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->state(fn($record) => $record->status === 'pending' && $record->expires_at && $record->expires_at->isPast() ? 'expired' : $record->status)
                    ->formatStateUsing(fn($state) => match($state) {
                        'pending'   => 'Menunggu Pembayaran', 
                        'approved'  => 'Lunas', 
                        'rejected'  => 'Ditolak',
                        'cancelled' => 'Dibatalkan',
                        'expired'   => 'Kedaluwarsa', 
                        default     => $state,
                    })
                    ->color(fn($state) => match($state) {
                        'pending'   => 'warning',
                        'approved'  => 'success',
                        'rejected'  => 'danger',
                        'cancelled' => 'danger', 
                        'expired'   => 'gray', 
                        default     => 'gray',
                    }),

                TextColumn::make('created_at')
                    ->label('Tanggal Book')
                    ->date('d M Y')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pending'   => 'Menunggu Pembayaran',
                        'approved'  => 'Lunas',
                        'rejected'  => 'Ditolak',
                        'cancelled' => 'Dibatalkan',
                    ]),
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                CancelBookingAction::make(),
                Action::make('syncPayment')
                    ->label('Cek Pembayaran')
                    ->icon('heroicon-o-arrow-path')
                    ->visible(fn ($record) => $record->status === 'pending' && $record->payment_order_id)
                    ->action(fn ($record) => app(MidtransService::class)->sync($record)),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ])
            ])
            ->emptyStateIcon('heroicon-o-home')
            ->emptyStateHeading('Belum ada Booking')
            ->emptyStateDescription('Booking baru akan muncul di sini');
    }
}