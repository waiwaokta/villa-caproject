<?php

namespace App\Filament\Resources\Bookings\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class BookingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->schema([

            Section::make('Update Status Booking')
                ->description('Admin hanya dapat mengubah status dan alasan penolakan')
                ->schema([
                    Select::make('status')
                        ->label('Status')
                        ->options([
                            'pending'  => 'Menunggu',
                            'approved' => 'Disetujui',
                            'rejected' => 'Ditolak',
                        ])
                        ->required()
                        ->live(),

                    Textarea::make('reject_desc')
                        ->label('Alasan Penolakan')
                        ->placeholder('Isi alasan penolakan jika status ditolak...')
                        ->visible(fn($get) => $get('status') === 'rejected')
                        ->required(fn($get) => $get('status') === 'rejected')
                        ->columnSpanFull(),
                ]),

        ]);
    }
}