<?php

namespace App\Filament\Resources\Wismas\Pages;

use App\Filament\Resources\Wismas\WismaResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewWisma extends ViewRecord
{
    protected static string $resource = WismaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
