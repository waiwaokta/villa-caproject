<?php

namespace App\Filament\Resources\Bookings\Pages;

use App\Filament\Resources\Bookings\BookingResource;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

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
                    Notification::make()
                        ->title('Booking disetujui')
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
                    \Filament\Forms\Components\Textarea::make('reject_desc')
                        ->label('Alasan Penolakan')
                        ->required()
                        ->placeholder('Tulis alasan penolakan...'),
                ])
                ->action(function(array $data) {
                    $this->record->update([
                        'status'      => 'rejected',
                        'reject_desc' => $data['reject_desc'],
                    ]);
                    Notification::make()
                        ->title('Booking ditolak')
                        ->danger()
                        ->send();
                    $this->refreshFormData(['status', 'reject_desc']);
                }),
        ];
    }
}
