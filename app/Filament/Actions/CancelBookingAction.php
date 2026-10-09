<?php

namespace App\Filament\Actions;

use App\Services\MidtransService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;

class CancelBookingAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'cancelBooking'; // nama action reusable
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Batalkan')
            ->icon(Heroicon::OutlinedXCircle)
            ->color('danger')
            ->requiresConfirmation()
            ->modalHeading('Batalkan Booking')
            ->modalDescription(fn ($record) => "Batalkan booking {$record->bookingID} atas nama {$record->guest_name}? Refund (jika sudah dibayar) diproses manual lewat CS.")
            ->visible(fn ($record) => in_array($record->status, ['pending', 'approved'])) // hanya pending atau approved
            ->action(function ($record) {
                if ($record->status === 'pending' && $record->payment_order_id) {
                    try {
                        app(MidtransService::class)->cancelQris($record); // best effort, QR dibatalkan di Midtrans
                    } catch (\Throwable $e) {
                        report($e); // kegagalan Midtrans tidak boleh menghalangi pembatalan
                    }
                }

                $record->update(['status' => 'cancelled']); // tanggal terbuka lagi karena locking() hanya menghitung pending aktif dan approved

                Notification::make()
                    ->title('Booking dibatalkan')
                    ->success()
                    ->send();
            });
    }
}