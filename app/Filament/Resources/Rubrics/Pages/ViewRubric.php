<?php

namespace App\Filament\Resources\Rubrics\Pages;

use App\Filament\Resources\Rubrics\RubricResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewRubric extends ViewRecord
{
    protected static string $resource = RubricResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
