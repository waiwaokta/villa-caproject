<?php

namespace App\Filament\Resources\Wismas\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Support\Enums\FontWeight;

class WismaInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->schema([
            Tabs::make('Tabs')
                ->tabs([
                    Tab::make('Informasi Wisma')
                        ->schema([
                            TextEntry::make('name')
                                ->label('Nama Wisma')
                                ->weight(FontWeight::Bold),
                            TextEntry::make('location')
                                ->label('Lokasi'),
                            TextEntry::make('capacity')
                                ->label('Kapasitas'),
                            IconEntry::make('is_active')
                                ->label('Status Tampil di Web')
                                ->boolean(),
                            TextEntry::make('desc')
                                ->label('Deskripsi')
                                ->columnSpanFull(),
                        ])
                        ->columns(2),
                    Tab::make('Foto Wisma')
                        ->schema([
                            RepeatableEntry::make('wismaPhotos')
                                ->hiddenlabel()
                                ->schema([
                                    ImageEntry::make('file_path')
                                        ->label('Foto')
                                        ->height(150),
                                    TextEntry::make('order')->label('Urutan'),
                                    IconEntry::make('is_primary')
                                        ->label('Foto Utama')
                                        ->boolean(),
                        ])
                        ->columns(3),
                        ]),
                    Tab::make('Daftar Harga')
                        ->schema([
                            RepeatableEntry::make('prices')
                                ->hiddenlabel()
                                ->schema([
                                    TextEntry::make('user_type')
                                        ->label('Status Pengunjung')
                                        ->badge()
                                        ->formatStateUsing(fn($state) => match($state) {
                                            'pln'  => 'PLN',
                                            'umum' => 'Umum',
                                            default => $state,
                                        })
                                        ->color(fn (string $state): string => match ($state) {
                                            'pln'  => 'info',
                                            'umum' => 'success',
                                            default => 'gray',
                                        }),
                                    TextEntry::make('day_type')
                                        ->label('Tipe Hari')
                                        ->badge()
                                        ->color(fn (string $state): string => match ($state) {
                                            'weekday' => 'danger',
                                            'weekend' => 'warning',
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
                                        ->money('IDR'),
                                ])
                                ->columns(3),
                        ]),
                ])
                ->columnSpanFull(),
        ]);
    }
}