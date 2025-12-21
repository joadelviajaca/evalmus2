<?php

namespace App\Filament\Widgets;

use App\Models\Rubric;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class RubricsCount extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Rúbricas', Rubric::count())
            ->description('Rúbricas disponibles')
            ->icon('heroicon-o-clipboard-document-check')
            ->color('success'),
        ];
    }
}
