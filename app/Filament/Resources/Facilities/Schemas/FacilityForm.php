<?php

namespace App\Filament\Resources\Facilities\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;

class FacilityForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
                ->schema([
                    TextInput::make('name')
                        ->label('Nama Fasilitas')
                        ->placeholder('contoh: WiFi, AC, Parkir')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(100)
                        ->columnSpanFull(),

                    TextInput::make('icon')
                        ->label('Class Icon (Tabler Icons)')
                        ->placeholder('contoh: ti ti-wifi')
                        ->helperText('Cari nama icon di tabler-icons.io, format: ti ti-nama-icon')
                        ->default('ti ti-circle-check')
                        ->required()
                        ->maxLength(50)
                        ->columnSpanFull(),
                ]);
    }
}
