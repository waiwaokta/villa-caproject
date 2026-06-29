<?php

namespace App\Filament\Resources\Wismas\Schemas;

use App\Models\Facility;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class WismaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->schema([
            Wizard::make([
                Step::make('Informasi Wisma')
                ->icon(Heroicon::InformationCircle)
                ->description('Lengkapi informasi dasar wisma')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Wisma')
                            ->required()
                            ->maxLength(100),
                        TextInput::make('location')
                            ->label('Lokasi')
                            ->placeholder('Batu, Sarangan, Ijen')
                            ->required()
                            ->maxLength(50)
                            ->dehydrateStateUsing(fn($state) => ucwords(strtolower(trim($state)))),
                        TextInput::make('address')
                            ->label('Alamat Lengkap')
                            ->placeholder('Jl. Raya No. 123, Kota Batu')
                            ->maxLength(255),
                        TextInput::make('capacity')
                            ->label('Kapasitas (orang)')
                            ->numeric()
                            ->minValue(1)
                            ->required(),
                        Toggle::make('is_active')
                            ->label('Tampilkan di web')
                            ->default(true),
                        Textarea::make('desc')
                            ->label('Deskripsi')
                            ->maxLength(2000)
                            ->columnSpanFull(),
                    ]),
                Step::make('Foto Wisma')
                    ->icon(Heroicon::Photo)
                    ->description('Unggah dan atur foto - foto wisma')
                    ->schema([
                        Repeater::make('wismaPhotos')
                            ->hiddenlabel()
                            ->relationship()
                            ->schema([
                                FileUpload::make('file_path')
                                    ->label('Foto')
                                    ->image()
                                    ->directory('wisma-photos')
                                    ->required(),
                                Toggle::make('is_primary')
                                    ->label('Foto utama')
                                    ->inline(false)
                                    ->default(false),
                            ])
                            ->columns(2)
                            ->reorderableWithDragAndDrop()
                            ->orderColumn('order')  
                            ->addActionLabel('Tambah foto')
                            ->defaultItems(0),
                    ]),
                Step::make('Fasilitas')
                    ->icon(Heroicon::Sparkles)
                    ->description('Pilih fasilitas yang tersedia di wisma ini')
                    ->schema([
                        CheckboxList::make('facilities')
                            ->hiddenLabel()
                            ->relationship('facilities', 'name')
                            ->options(fn() => Facility::pluck('name', 'facilityID'))
                            ->columns(3)
                            ->gridDirection('row')
                            ->bulkToggleable(),
                    ]),
                Step::make('Daftar Harga')
                    ->icon(Heroicon::CurrencyDollar)
                    ->description('Atur daftar harga untuk wisma')
                    ->schema([
                        Repeater::make('prices')
                        ->hiddenlabel()
                        ->relationship()
                        ->schema([
                            Select::make('user_type')
                                ->label('Status Pengguna')
                                ->options([
                                    'pln'  => 'Pegawai / Pensiunan PLN',
                                    'umum' => 'Umum',
                                ])
                                ->required(),
                            Select::make('day_type')
                                ->label('Tipe Hari')
                                ->options([
                                    'weekday' => 'Senin – Jumat',
                                    'weekend' => 'Sabtu & Minggu',
                                    'holiday' => 'Libur Panjang',
                                ])
                                ->required(),
                            TextInput::make('price')
                                ->label('Harga (Rp)')
                                ->numeric()
                                ->prefix('Rp')
                                ->required(),
                        ])
                        ->columns(3)
                        ->addActionLabel('Tambah harga')
                        ->defaultItems(0),
                    ]),
            ])
            ->columnSpanFull()
        ]);
    }
}