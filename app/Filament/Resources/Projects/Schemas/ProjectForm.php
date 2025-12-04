<?php

namespace App\Filament\Resources\Projects\Schemas;

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
                    ->required(),
                Textarea::make('summary')
                    ->columnSpanFull(),
                TextInput::make('rubric_id')
                    ->numeric(),
                TextInput::make('state')
                    ->required()
                    ->default('pending'),
                TextInput::make('metadata'),
            ]);
    }
}
