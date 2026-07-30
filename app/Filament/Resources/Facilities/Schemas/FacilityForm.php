<?php

namespace App\Filament\Resources\Facilities\Schemas;

use Filament\Forms\Components\TextInput;
use Wallacemartinss\FilamentIconPicker\Forms\Components\IconPickerField;
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

                    IconPickerField::make('icon')
                        ->label('Class Icon (Tabler Icons)')
                        ->allowedSets(['tabler-icons'])
                        ->required()
                        ->columnSpanFull(),
                ]);
    }
}
