<?php

namespace App\Filament\Resources\Rubrics;

use App\Filament\Resources\Rubrics\Pages\CreateRubric;
use App\Filament\Resources\Rubrics\Pages\EditRubric;
use App\Filament\Resources\Rubrics\Pages\ListRubrics;
use App\Filament\Resources\Rubrics\Pages\ViewRubric;
use App\Filament\Resources\Rubrics\Schemas\RubricForm;
use App\Filament\Resources\Rubrics\Schemas\RubricInfolist;
use App\Filament\Resources\Rubrics\Tables\RubricsTable;
use App\Models\Rubric;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use App\Filament\Resources\Rubrics\RelationManagers\CriteriaRelationManager;
use App\Filament\Resources\Rubrics\RelationManagers\CriterionLevelsRelationManager;
use UnitEnum;

class RubricResource extends Resource
{
    protected static ?string $model = Rubric::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'title';
    protected static string|UnitEnum|null $navigationGroup = 'Gestión académica';
    protected static ?string $navigationLabel = 'Rúbricas';
    protected static ?string $pluralLabel = 'Rúbricas';
    protected static ?string $modelLabel = 'rúbrica';

    protected static ?string $plurarModelLabel = 'rúbricas';

    public static function form(Schema $schema): Schema
    {
        return RubricForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return RubricInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RubricsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            CriteriaRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRubrics::route('/'),
            'create' => CreateRubric::route('/create'),
            'view' => ViewRubric::route('/{record}'),
            'edit' => EditRubric::route('/{record}/edit'),
        ];
    }
}
