<?php

namespace App\Filament\Resources\Wismas\Pages;

use App\Filament\Resources\Wismas\WismaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListWismas extends ListRecords
{
    protected static string $resource = WismaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
