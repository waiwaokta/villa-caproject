<?php

namespace App\Filament\Resources\Bookings\Pages;

use App\Services\FonnteService;
use App\Filament\Resources\Bookings\BookingResource;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Log;

class ViewBooking extends ViewRecord
{
    protected static string $resource = BookingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('approve')
                ->label('Setujui Booking')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn() => $this->record->status === 'pending')
                ->requiresConfirmation()
                ->modalHeading('Setujui Booking')
                ->modalDescription("Setujui booking {$this->record->bookingID} atas nama {$this->record->guest_name}?")
                ->action(function() {
                    $this->record->update(['status' => 'approved']);

                    // Kirim WA — gagal kirim tidak boleh block approve
                    try {
                        app(FonnteService::class)->sendApproved($this->record);
                    } catch (\Throwable $e) {
                        Log::error('WA approve gagal', [
                            'bookingID' => $this->record->bookingID,
                            'error'     => $e->getMessage(),
                        ]);
                    }

                    Notification::make()
                        ->title('Booking disetujui')
                        ->body('Notifikasi WhatsApp telah dikirim ke tamu.')
                        ->success()
                        ->send();
                    $this->refreshFormData(['status']);
                }),

            Action::make('reject')
                ->label('Tolak Booking')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->visible(fn() => $this->record->status === 'pending')
                ->requiresConfirmation()
                ->modalHeading('Tolak Booking')
                ->form([
                    Textarea::make('reject_desc')
                        ->label('Alasan Penolakan')
                        ->required()
                        ->placeholder('Tulis alasan penolakan...'), 
                ])
                ->action(function(array $data) {
                    $this->record->update([
                        'status'      => 'rejected',
                        'reject_desc' => $data['reject_desc'],
                    ]);

                     // Kirim WA — gagal kirim tidak boleh block reject
                    try {
                        app(FonnteService::class)->sendRejected($this->record);
                    } catch (\Throwable $e) {
                        Log::error('WA reject gagal', [
                            'bookingID' => $this->record->bookingID,
                            'error'     => $e->getMessage(),
                        ]);
                    }

                    Notification::make()
                        ->title('Booking ditolak')
                        ->body('Notifikasi WhatsApp telah dikirim ke tamu.')
                        ->danger()
                        ->send();
                    $this->refreshFormData(['status', 'reject_desc']);
                }),
        ];
    }
}
