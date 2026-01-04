<?php

namespace App\Filament\Resources\Projects\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ProjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label(__('Title'))
                    ->required(),
                Textarea::make('summary')
                    ->label(__('Summary'))
                    ->columnSpanFull(),
                Select::make('state')
                    ->label(__('State'))
                    ->options([
                        'pending' => __('Pending'),
                        'evaluating' => __('Evaluating'),
                        'finished' => __('Finished'),
                    ])
                    ->required()
                    ->default('pending')
                    ->disabled(fn (string $context) => $context === 'create'),
                Select::make('evaluators')
                    ->label(__('Evaluators'))
                    ->multiple()
                    ->relationship(
                        name: 'evaluators',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn ($query) =>
                            $query->role('evaluador')
                    )
                    ->preload()
                    ->searchable()
                    ->required(),
                TextInput::make('metadata'),
                Select::make('rubric_id')
                    ->label('Rúbrica asociada')
                    ->relationship('rubric', 'title')
                    ->searchable()
                    ->preload()
                    ->required()
                    
            ]);
    }
}
