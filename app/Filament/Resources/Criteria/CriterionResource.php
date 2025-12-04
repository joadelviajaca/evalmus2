<?php

namespace App\Filament\Resources\Criteria;

use App\Filament\Resources\Criteria\Pages\CreateCriterion;
use App\Filament\Resources\Criteria\Pages\EditCriterion;
use App\Filament\Resources\Criteria\Pages\ListCriteria;
use App\Filament\Resources\Criteria\Pages\ViewCriterion;
use App\Filament\Resources\Criteria\Schemas\CriterionForm;
use App\Filament\Resources\Criteria\Schemas\CriterionInfolist;
use App\Filament\Resources\Criteria\Tables\CriteriaTable;
use App\Models\Criterion;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CriterionResource extends Resource
{
    protected static ?string $model = Criterion::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return CriterionForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CriterionInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CriteriaTable::configure($table);
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
            'index' => ListCriteria::route('/'),
            'create' => CreateCriterion::route('/create'),
            'view' => ViewCriterion::route('/{record}'),
            'edit' => EditCriterion::route('/{record}/edit'),
        ];
    }
}
