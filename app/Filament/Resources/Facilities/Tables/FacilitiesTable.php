<?php

namespace App\Filament\Resources\Facilities\Tables;

use App\Filament\Resources\Facilities\FacilityResource;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Columns\IconColumn;
use Wallacemartinss\FilamentIconPicker\Tables\Columns\IconPickerColumn;

class FacilitiesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                IconColumn::make('icon')
                    ->label('')
                    ->icon(fn($record) => null) 
                    ->view('filament.tables.columns.tabler-icon'),

                TextColumn::make('name')
                    ->label('Nama Fasilitas')
                    ->searchable()
                    ->sortable(),

                IconPickerColumn::make('icon')
                    ->label('Icon')
                    ->medium(),

                TextColumn::make('villas_count')
                    ->label('Dipakai di')
                    ->counts('villas')
                    ->suffix(' villa')
                    ->badge()
                    ->color('info'),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
             ->emptyStateIcon('heroicon-o-Sparkles')
            ->emptyStateHeading('Belum ada Fasilitas yang Terdaftar')
            ->emptyStateDescription('Fasilitas Villa akan muncul di sini setelah ditambahkan.');
        //     ->emptyStateActions([
        //     Action::make('create')
        //         ->label('Buat Wisma Baru')
        //         ->url(fn (): string => FacilityResource::getUrl('create'))
        //         ->icon('heroicon-m-plus')
        //         ->button(),
        // ]);
    }
}
