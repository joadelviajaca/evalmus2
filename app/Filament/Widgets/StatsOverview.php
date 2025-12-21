<?php

namespace App\Filament\Widgets;

use App\Models\Project;
use App\Models\Rubric;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Proyectos', Project::count())
                ->description('Proyectos registrados')
                ->icon('heroicon-o-folder'),

            Stat::make('Rúbricas', Rubric::count())
                ->description('Rúbricas disponibles')
                ->icon('heroicon-o-clipboard-document-check'),

            Stat::make('Usuarios', User::count())
                ->description('Usuarios del sistema')
                ->icon('heroicon-o-users')
                // ->visible(fn () => auth()->user()->hasRole('admin')),
        ];
    }
}
