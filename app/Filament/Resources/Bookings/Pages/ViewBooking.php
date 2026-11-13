<?php

namespace App\Filament\Resources\Bookings\Pages;

use App\Filament\Actions\CancelBookingAction;
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
            Action::make('badge_status')
                ->label(fn () => match ($this->record->status) {
                    'pending'   => 'Menunggu Pembayaran',
                    'approved'  => 'Lunas', 
                    'rejected'  => 'Ditolak',
                    'cancelled' => 'Dibatalkan',
                    default     => '-',
                })
                ->icon(fn () => match ($this->record->status) {
                    'pending'   => 'heroicon-o-clock',
                    'approved'  => 'heroicon-o-check-circle',
                    'rejected'  => 'heroicon-o-x-circle',
                    'cancelled' => 'heroicon-o-x-circle',
                    default     => 'heroicon-o-minus-circle',
                })
                ->color(fn () => match ($this->record->status) {
                    'pending'   => 'warning',
                    'approved'  => 'success',
                    'rejected'  => 'danger',
                    'cancelled' => 'danger',
                    default     => 'gray',
                })
                ->disabled()
                ->size('sm')
                ->outlined(false),

            CancelBookingAction::make(),
        ];
    }
}
