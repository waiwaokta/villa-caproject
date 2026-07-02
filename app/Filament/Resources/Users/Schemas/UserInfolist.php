<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\User;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\IconEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->schema([

                Section::make('Informasi Akun')
                    ->icon('heroicon-o-user')
                    ->columnSpan(1)
                    ->schema([
                        TextEntry::make('name')
                            ->label('Nama')
                            ->weight(FontWeight::Bold),

                        TextEntry::make('email')
                            ->label('Email'),

                        TextEntry::make('phone')
                            ->label('No. HP')
                            ->placeholder('-'),

                        TextEntry::make('role')
                            ->label('Role')
                            ->badge()
                            ->formatStateUsing(fn($state) => match($state) {
                                'admin'    => 'Admin',
                                'customer' => 'Customer',
                                default    => $state,
                            })
                            ->color(fn($state) => match($state) {
                                'admin'    => 'danger',
                                'customer' => 'info',
                                default    => 'gray',
                            }),
                        IconEntry::make('notify_new_book')
                            ->label('Terima Notifikasi Booking Terbaru')
                            ->boolean()
                            ->trueColor('success')
                            ->falseColor('danger')
                    ]),

                Section::make('Informasi Sistem')
                    ->icon('heroicon-o-clock')
                    ->columnSpan(1)
                    ->schema([
                        TextEntry::make('email_verified_at')
                            ->label('Email Diverifikasi')
                            ->dateTime('d M Y, H:i')
                            ->placeholder('Belum diverifikasi'),

                        TextEntry::make('created_at')
                            ->label('Terdaftar')
                            ->dateTime('d M Y, H:i'),

                        TextEntry::make('updated_at')
                            ->label('Terakhir Diupdate')
                            ->dateTime('d M Y, H:i'),

                        TextEntry::make('deleted_at')
                            ->label('Dihapus Pada')
                            ->dateTime('d M Y, H:i')
                            ->placeholder('-')
                            ->visible(fn(User $record): bool => $record->trashed()),
                    ]),

            ]);
    }
}