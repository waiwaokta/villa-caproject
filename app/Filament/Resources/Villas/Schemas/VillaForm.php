<?php

namespace App\Filament\Resources\Villas\Schemas;

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
use App\Services\ImageOptimizerService;
use Filament\Support\RawJs;

class VillaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->schema([
            Wizard::make([
                Step::make('Informasi Villa')
                ->icon(Heroicon::InformationCircle)
                ->description('Lengkapi informasi dasar villa')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Villa')
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
                Step::make('Foto Villa')
                    ->icon(Heroicon::Photo)
                    ->description('Unggah dan atur foto - foto villa')
                    ->schema([
                        Repeater::make('villaPhotos')
                            ->hiddenlabel()
                            ->relationship()
                            ->schema([
                                FileUpload::make('file_path')
                                    ->label('Foto')
                                    ->image()
                                    ->directory('villa-photos')
                                    ->required()
                                    ->saveUploadedFileUsing(function ($file) { // DITAMBAHKAN — otomatis resize + compress + convert ke WebP sebelum disimpan
                                        return app(ImageOptimizerService::class)->optimize($file);
                                    })
                                    ->helperText('Foto akan otomatis dioptimalkan. Anda tidak perlu mengubah ukuran atau format sebelum mengunggah.'),
                                Toggle::make('is_primary')
                                    ->label('Foto utama')
                                    ->inline(false)
                                    ->default(false),
                            ])
                            ->columns(2)
                            ->reorderableWithDragAndDrop()
                            ->orderColumn('order')  
                            ->addActionLabel('+ Tambah foto')
                            ->defaultItems(1),
                    ]),
                Step::make('Fasilitas')
                    ->icon(Heroicon::Sparkles)
                    ->description('Pilih fasilitas yang tersedia di villa ini')
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
                    ->description('Atur daftar harga untuk villa')
                    ->schema([
                        Repeater::make('prices')
                        ->hiddenlabel()
                        ->relationship()
                        ->schema([
                            Select::make('day_type')
                                ->label('Tipe Hari')
                                ->selectablePlaceholder(false)
                                ->options([
                                    'weekday' => 'Senin – Jumat',
                                    'weekend' => 'Sabtu & Minggu',
                                    'holiday' => 'Libur Panjang',
                                ])
                                ->default('weekday')
                                ->required(),
                            TextInput::make('price')
                                ->label('Harga (Rp)')
                                ->prefix('Rp')
                                ->mask(RawJs::make('$money($input, \',\', \'.\', 2)'))
                                ->dehydrateStateUsing(function ($state) {
                                    if ($state === null || $state === '') return 0;
                                    $clean = str_replace('.', '', $state);
                                    $clean = str_replace(',', '.', $clean);
                                    return (float) $clean;
                                })
                                ->formatStateUsing(fn ($state) => $state == 0 ? null : number_format($state, 2, ',', '.'))
                                ->required(),
                        ])
                        ->columns(3)
                        ->addActionLabel('+ Tambah harga')
                        ->defaultItems(1),
                    ]),
            ])
            ->skippable()
            ->columnSpanFull()
        ]);
    }
}