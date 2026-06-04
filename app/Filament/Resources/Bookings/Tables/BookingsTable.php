<?php

namespace App\Filament\Resources\Bookings\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class BookingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('bookingID')
                    ->label('Kode Booking')
                    ->searchable()
                    ->copyable()
                    ->fontFamily('mono')
                    ->weight(\Filament\Support\Enums\FontWeight::Bold),

                TextColumn::make('wisma.name')
                    ->label('Wisma')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('guest_name')
                    ->label('Nama Tamu')
                    ->searchable(),

                TextColumn::make('guest_phone')
                    ->label('No. HP')
                    ->searchable(),

                TextColumn::make('check_in')
                    ->label('Check-in')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('check_out')
                    ->label('Check-out')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('total_nights')
                    ->label('Malam')
                    ->suffix(' malam')
                    ->sortable(),

                TextColumn::make('total_price')
                    ->label('Total')
                    ->money('IDR')
                    ->sortable(),

                TextColumn::make('user_type')
                    ->label('Tipe')
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
                    ->label('Tgl Booking')
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
                SelectFilter::make('user_type')
                    ->label('Tipe Pengguna')
                    ->options([
                        'pln'  => 'PLN',
                        'umum' => 'Umum',
                    ]),
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}