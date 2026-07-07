<?php

namespace App\Filament\Resources\Holidays\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Malzariey\FilamentDaterangepickerFilter\Fields\DateRangePicker;

class HolidayForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->schema([
            TextInput::make('name')
                ->label('Nama Libur')
                ->required()
                ->maxLength(255)
                ->placeholder('Contoh: Hari Raya Idul Fitri'),

            DateRangePicker::make('date_range') // ditambahkan — field non-DB, dipakai untuk input rentang tanggal via kalender visual
                ->label('Tanggal Libur')
                ->required()
                ->format('Y-m-d')
                ->useDualState('date', 'date_end') // ditambahkan — sync ke 2 property terpisah: date (kolom asli DB) & date_end (virtual)
                ->helperText('Pilih 1 tanggal saja untuk libur 1 hari, atau pilih rentang untuk cuti bersama.'),
        ]);
    }
}