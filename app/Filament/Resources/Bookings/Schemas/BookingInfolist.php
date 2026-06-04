<?php

namespace App\Filament\Resources\Bookings\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;

class BookingInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->schema([

                // KIRI — Data Booking
                Section::make('Informasi Booking')
                    ->icon('heroicon-o-calendar')
                    ->columnSpan(2)
                    ->columns(2)
                    ->schema([
                        TextEntry::make('bookingID')
                            ->label('Kode Booking')
                            ->weight(FontWeight::Bold)
                            ->copyable()
                            ->fontFamily('mono'),

                        TextEntry::make('wisma.name')
                            ->label('Wisma'),

                        TextEntry::make('check_in')
                            ->label('Check-in')
                            ->date('d M Y'),

                        TextEntry::make('check_out')
                            ->label('Check-out')
                            ->date('d M Y'),

                        TextEntry::make('total_nights')
                            ->label('Jumlah Malam')
                            ->suffix(' malam'),

                        TextEntry::make('total_price')
                            ->label('Total Harga')
                            ->money('IDR')
                            ->weight(FontWeight::Bold),

                        TextEntry::make('user_type')
                            ->label('Tipe Pengguna')
                            ->badge()
                            ->formatStateUsing(fn($state) => match($state) {
                                'pln'  => 'PLN',
                                'umum' => 'Umum',
                                default => $state,
                            })
                            ->color(fn($state) => match($state) {
                                'pln'  => 'info',
                                'umum' => 'success',
                                default => 'gray',
                            }),

                        TextEntry::make('booking_type')
                            ->label('Tipe Booking')
                            ->badge()
                            ->formatStateUsing(fn($state) => match($state) {
                                'perorangan' => 'Perorangan',
                                'instansi'   => 'Instansi',
                                default      => $state,
                            })
                            ->color('gray'),

                        TextEntry::make('status')
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

                        TextEntry::make('reject_desc')
                            ->label('Alasan Penolakan')
                            ->placeholder('-')
                            ->columnSpanFull()
                            ->visible(fn($record) => $record->status === 'rejected'),
                    ]),

                // KANAN — Data Tamu
                Section::make('Data Tamu')
                    ->icon('heroicon-o-user')
                    ->columnSpan(1)
                    ->schema([
                        TextEntry::make('guest_name')
                            ->label('Nama')
                            ->weight(FontWeight::Bold),

                        TextEntry::make('guest_phone')
                            ->label('No. HP'),

                        TextEntry::make('guest_ktp')
                            ->label('NIK KTP'),

                        TextEntry::make('employee_id')
                            ->label('ID Pegawai PLN')
                            ->placeholder('-')
                            ->visible(fn($record) => $record->user_type === 'pln'),

                        TextEntry::make('inst_name')
                            ->label('Nama Instansi')
                            ->placeholder('-')
                            ->visible(fn($record) => $record->booking_type === 'instansi'),

                        TextEntry::make('inst_npwp')
                            ->label('NPWP Instansi')
                            ->placeholder('-')
                            ->visible(fn($record) => $record->booking_type === 'instansi'),

                        TextEntry::make('created_at')
                            ->label('Tgl Booking')
                            ->dateTime('d M Y, H:i'),
                    ]),

            ]);
    }
}