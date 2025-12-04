<?php

namespace App\Filament\Resources\Rubrics\RelationManagers;

use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CriteriaRelationManager extends RelationManager
{
    protected static string $relationship = 'criteria';

    protected static ?string $recordTitleAttribute = 'title';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('Título')
                    ->required()
                    ->maxLength(255),
                
                Textarea::make('description')
                    ->label('Descripción')
                    ->rows(3),

                Grid::make(2)
                ->schema([
                    TextInput::make('weight')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(100)
                        ->required(),
    
                    TextInput::make('order')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(100)
                        ->required(),

                ]),
                Repeater::make('levels')
                    ->relationship()
                    ->label('Niveles de evaluación')
                    ->schema([
                        TextInput::make('label')
                    ->label('Nombre del nivel')
                    ->required()
                    ->maxLength(255),

                TextInput::make('value')
                    ->label('Valor')
                    ->numeric()
                    ->required(),

                Textarea::make('description')
                    ->label('Descripción')
                    ->rows(3),

                TextInput::make('order')
                    ->numeric()
                    ->minValue(0)
                    ->default(0)
                    ])
                    ->columns(4)
                    ->defaultItems(0)
                    ->addActionLabel('Añadir nivel')
                    ->columnSpanFull()

            ]);
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('title'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                TextColumn::make('title')->label('Título')->sortable()->searchable(),
                TextColumn::make('weight')->label('Peso (%)'),
                TextColumn::make('order')->label('Orden'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
                AssociateAction::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                // DissociateAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DissociateBulkAction::make(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
