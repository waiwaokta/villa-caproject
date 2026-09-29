<?php

namespace App\Filament\Resources\Bookings\Tables;

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
                        'pending'  => 'Menunggu',
                        'approved' => 'Disetujui',
                        'rejected' => 'Ditolak',
                    ]),
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                Action::make('approve')
                    ->label('Acc')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn(Model $record) => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->modalHeading('Setujui Booking')
                    ->modalDescription(fn(Model $record) => "Setujui booking {$record->bookingID} atas nama {$record->guest_name}?")
                    ->action(function (Model $record) {
                        $record->update(['status' => 'approved']);

                        try {
                            app(FonnteService::class)->sendApproved($record);
                        } catch (\Throwable $e) {
                            Log::error('WA approve gagal (dari tabel)', [
                                'bookingID' => $record->bookingID,
                                'error'     => $e->getMessage(),
                            ]);
                        }

                        Notification::make()
                            ->title('Booking disetujui')
                            ->body('Notifikasi WhatsApp telah dikirim ke tamu.')
                            ->success()
                            ->send();
                    }),

                Action::make('reject')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn(Model $record) => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->modalHeading('Tolak Booking')
                    ->form([
                        Textarea::make('reject_desc')
                            ->label('Alasan Penolakan')
                            ->required()
                            ->placeholder('Tulis alasan penolakan...'),
                    ])
                    ->action(function (array $data, Model $record) {
                            $record->update([
                                'status'      => 'rejected',
                                'reject_desc' => $data['reject_desc'],
                            ]);

                            try {
                                app(FonnteService::class)->sendRejected($record);
                            } catch (\Throwable $e) {
                                Log::error('WA reject gagal (dari tabel)', [
                                    'bookingID' => $record->bookingID,
                                    'error'     => $e->getMessage(),
                                ]);
                            }

                            Notification::make()
                                ->title('Booking ditolak')
                                ->body('Notifikasi WhatsApp telah dikirim ke tamu.')
                                ->danger()
                                ->send();
                        }),
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