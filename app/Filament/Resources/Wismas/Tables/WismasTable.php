<?php

namespace App\Filament\Resources\Wismas\Tables;

use App\Filament\Resources\Wismas\WismaResource;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class WismasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Wisma')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('location')
                    ->label('Lokasi')
                    ->searchable(),

                TextColumn::make('capacity')
                    ->label('Kapasitas')
                    ->suffix(' orang')
                    ->sortable(),

                TextColumn::make('is_active')
                    ->label('Status di Web')
                    ->badge()
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Tampil' : 'Tidak Tampil')
                    ->color(fn (bool $state): string => $state ? 'success' : 'danger'),

                TextColumn::make('created_at')
                    ->label('Ditambahkan')
                    ->date('d M Y')
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Status Tampil')
                    ->trueLabel('Tampil')
                    ->falseLabel('Tidak Tampil')
                    ->placeholder('Semua'),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateIcon('heroicon-o-home')
            ->emptyStateHeading('Belum ada Wisma')
            ->emptyStateDescription('Wisma baru akan muncul di sini setelah ditambahkan.')
            ->emptyStateActions([
            Action::make('create')
                ->label('Buat Wisma Baru')
                ->url(fn (): string => WismaResource::getUrl('create'))
                ->icon('heroicon-m-plus')
                ->button(),
        ]);
    }
}