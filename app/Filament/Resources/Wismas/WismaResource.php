<?php

namespace App\Filament\Resources\Wismas;

use App\Filament\Resources\Wismas\Pages\CreateWisma;
use App\Filament\Resources\Wismas\Pages\EditWisma;
use App\Filament\Resources\Wismas\Pages\ListWismas;
use App\Filament\Resources\Wismas\Pages\ViewWisma;
use App\Filament\Resources\Wismas\Schemas\WismaForm;
use App\Filament\Resources\Wismas\Schemas\WismaInfolist;
use App\Filament\Resources\Wismas\Tables\WismasTable;
use App\Models\Wisma;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class WismaResource extends Resource
{
    protected static ?string $model = Wisma::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Home;

    protected static string | UnitEnum | null $navigationGroup = 'Wisma';

    protected static ?string $navigationLabel = 'Daftar Wisma';

    protected static ?string $pluralModelLabel = 'Daftar Wisma';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return WismaForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return WismaInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WismasTable::configure($table);
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
            'index' => ListWismas::route('/'),
            'create' => CreateWisma::route('/create'),
            'view' => ViewWisma::route('/{record}'),
            'edit' => EditWisma::route('/{record}/edit'),
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
