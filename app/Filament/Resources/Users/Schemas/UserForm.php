<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->schema([

            Section::make('Informasi Akun')
                ->columns(2)
                ->schema([
                    TextInput::make('name')
                        ->label('Nama Lengkap')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('email')
                        ->label('Email')
                        ->email()
                        ->required()
                        ->maxLength(255)
                        ->unique(ignoreRecord: true),

                    TextInput::make('phone')
                        ->label('No. HP')
                        ->tel()
                        ->maxLength(15)
                        ->placeholder('08xxxxxxxxxx'),

                    Select::make('role')
                        ->label('Role')
                        ->options([
                            'admin'    => 'Admin',
                            'customer' => 'Customer',
                        ])
                        ->default('admin')
                        ->selectablePlaceholder(false)
                        ->required(),

                    TextInput::make('password')
                        ->label('Password')
                        ->password()
                        ->required(fn($context) => $context === 'create')
                        ->dehydrateStateUsing(fn($state) => filled($state) ? bcrypt($state) : null)
                        ->dehydrated(fn($state) => filled($state))
                        ->placeholder('Kosongkan jika tidak ingin mengubah password')
                        ->columnSpanFull(),
                    Toggle::make('notify_new_book')
                        ->label('Terima Notifikasi Booking Terbaru')
                        ->visible(fn (callable $get) => $get('role') === 'admin')
                        ->default(false),
                ])
                ->columnSpanFull(),

        ]);
    }
}