<?php

namespace App\Filament\Widgets;

use App\Models\Project;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ProjectsCount extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Proyectos', Project::count())
            ->description('Proyectos registrados')
            ->icon('heroicon-o-folder')
            ->color('primary'),
        ];
    }
}
