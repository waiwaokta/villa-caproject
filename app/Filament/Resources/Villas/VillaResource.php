<?php

namespace App\Filament\Resources\Villas;

use App\Filament\Resources\Villas\Pages\CreateVilla;
use App\Filament\Resources\Villas\Pages\EditVilla;
use App\Filament\Resources\Villas\Pages\ListVillas;
use App\Filament\Resources\Villas\Pages\ViewVilla;
use App\Filament\Resources\Villas\Schemas\VillaForm;
use App\Filament\Resources\Villas\Schemas\VillaInfolist;
use App\Filament\Resources\Villas\Tables\VillasTable;
use App\Models\Villa;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VillaResource extends Resource
{
    protected static ?string $model = Villa::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Home;

    protected static string | UnitEnum | null $navigationGroup = 'Villa';

    protected static ?string $navigationLabel = 'Daftar Villa';

    protected static ?string $pluralModelLabel = 'Daftar Villa';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return VillaForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return VillaInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return VillasTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListVillas::route('/'),
            'create' => CreateVilla::route('/create'),
            'view' => ViewVilla::route('/{record}'),
            'edit' => EditVilla::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}