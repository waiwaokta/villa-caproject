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

                TextColumn::make('icon')
                    ->label('Class Icon')
                    ->fontFamily('mono')
                    ->size('sm')
                    ->color('gray'),

                TextColumn::make('wismas_count')
                    ->label('Dipakai di')
                    ->counts('wismas')
                    ->suffix(' wisma')
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
            ->emptyStateDescription('Fasilitas Wisma akan muncul di sini setelah ditambahkan.');
        //     ->emptyStateActions([
        //     Action::make('create')
        //         ->label('Buat Wisma Baru')
        //         ->url(fn (): string => FacilityResource::getUrl('create'))
        //         ->icon('heroicon-m-plus')
        //         ->button(),
        // ]);
    }
}
