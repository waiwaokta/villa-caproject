<?php

namespace App\Filament\Resources\Wismas\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Wallacemartinss\FilamentIconPicker\Infolists\Components\IconPickerEntry;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Schemas\Components\Grid;

class WismaInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->schema([

                Section::make('Informasi Wisma')
                    ->icon('heroicon-o-home')
                    ->columnSpan(2)
                    ->columns(2)
                    ->schema([
                        TextEntry::make('name')
                            ->label('Nama Wisma')
                            ->weight(FontWeight::Bold),
                        TextEntry::make('location')
                            ->label('Lokasi / Daerah'),
                        TextEntry::make('address')
                            ->label('Alamat Lengkap')
                            ->placeholder('-'),
                        TextEntry::make('capacity')
                            ->label('Kapasitas')
                            ->suffix(' orang'),
                        TextEntry::make('is_active')
                            ->label('Status di Web')
                            ->badge()
                            ->formatStateUsing(fn (bool $state): string => $state ? 'Tampil' : 'Tidak Tampil')
                            ->color(fn (bool $state): string => $state ? 'success' : 'danger'),
                        TextEntry::make('desc')
                            ->label('Deskripsi')
                            ->columnSpanFull(),
                    ]),

                Section::make('Foto Wisma')
                    ->icon('heroicon-o-photo')
                    ->columnSpan(1)
                    ->schema([
                        RepeatableEntry::make('wismaPhotos')
                            ->hiddenLabel()
                            ->schema([
                                ImageEntry::make('file_path')
                                    ->hiddenLabel()
                                    ->height(100)
                                    ->columnSpan(2),
                                Grid::make(1)
                                    ->schema([
                                        TextEntry::make('order')
                                            ->label('Urutan')
                                            ->badge()
                                            ->color('primary'),
                                        IconEntry::make('is_primary')
                                            ->label('Utama')
                                            ->boolean(),
                                    ])
                                    ->columnSpan(1),
                            ])
                            ->columns(3)
                            ->columnSpanFull()
                            ->extraAttributes([
                                'style' => 'max-height: 300px; overflow-y: auto; padding-right: 4px;'
                            ]),
                    ]),

                Section::make('Daftar Harga')
                    ->icon('heroicon-o-banknotes')
                    ->columnSpan(2)
                    ->schema([
                        RepeatableEntry::make('prices')
                            ->hiddenLabel()
                            ->schema([
                                TextEntry::make('user_type')
                                    ->label('Status Pengguna')
                                    ->badge()
                                    ->color(fn(string $state): string => match($state) {
                                        'pln'  => 'info',
                                        'umum' => 'success',
                                        default => 'gray',
                                    })
                                    ->formatStateUsing(fn($state) => match($state) {
                                        'pln'  => 'PLN',
                                        'umum' => 'Umum',
                                        default => $state,
                                    }),
                                TextEntry::make('day_type')
                                    ->label('Tipe Hari')
                                    ->badge()
                                    ->color(fn(string $state): string => match($state) {
                                        'weekday' => 'danger',
                                        'weekend' => 'primary',
                                        'holiday' => 'success',
                                        default   => 'gray',
                                    })
                                    ->formatStateUsing(fn($state) => match($state) {
                                        'weekday' => 'Senin – Jumat',
                                        'weekend' => 'Sabtu & Minggu',
                                        'holiday' => 'Libur Panjang',
                                        default   => $state,
                                    }),
                                TextEntry::make('price')
                                    ->label('Harga')
                                    ->money('IDR', locale: 'id'),
                            ])
                            ->columns(3)
                            ->extraAttributes([
                                'style' => 'max-height: 280px; overflow-y: auto; padding-right: 4px;'
                            ]),
                    ]),
                Section::make('Fasilitas')
                    ->icon('heroicon-o-tag')
                    ->columnSpan(1)
                    ->schema([
                        RepeatableEntry::make('facilities')
                            ->hiddenLabel()
                            ->schema([
                                TextEntry::make('name'),
                                IconPickerEntry::make('icon')
                                    ->label('Icon')
                                    ->showIconName(false),
                            ])
                            ->columns(2)
                            ->extraAttributes([
                                'style' => 'max-height: 280px; overflow-y: auto; padding-right: 4px;'
                            ]),
                    ]),

            ]);
    }
}