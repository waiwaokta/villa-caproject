<?php

namespace App\Filament\Resources\Wismas\Pages;

use App\Filament\Resources\Wismas\WismaResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class ViewWisma extends ViewRecord
{
    protected static string $resource = WismaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('toggleActive')
                ->label(fn () => $this->record->is_active ? 'Nonaktif di Web' : 'Aktifkan di Web')
                ->icon(fn () => $this->record->is_active ? 'heroicon-o-eye-slash' : 'heroicon-o-eye')
                ->color(fn () => $this->record->is_active ? 'danger' : 'success')
                ->action(function () {
                    $this->record->update(['is_active' => !$this->record->is_active]);

                    Notification::make()
                        ->title($this->record->is_active ? 'Wisma sekarang tampil di web' : 'Wisma disembunyikan dari web')
                        ->success()
                        ->send();

                    $this->redirect(static::getResource()::getUrl('view', ['record' => $this->record]));
                }),
            EditAction::make(),
        ];
    }
}
