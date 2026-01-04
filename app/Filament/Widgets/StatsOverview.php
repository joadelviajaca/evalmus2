<?php

namespace App\Filament\Widgets;

use App\Models\Evaluation;
use App\Models\Project;
use App\Models\Rubric;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        // 1. Obtenemos todas las evaluaciones cerradas con las relaciones necesarias
        $closedEvaluations = Evaluation::where('is_locked', true)
            ->with(['criterionEvaluations.criterion', 'project.rubric.criteria'])
            ->get();

        // 2. Calculamos la media en memoria usando tu método existente
        $averageScore = $closedEvaluations->isEmpty() 
            ? 0 
            : $closedEvaluations->avg(fn ($evaluation) => $evaluation->totalScore());

        return [
            Stat::make('Proyectos', Project::count())
                ->description('Proyectos registrados')
                ->icon('heroicon-o-folder'),

            Stat::make('Rúbricas', Rubric::count())
                ->description('Rúbricas disponibles')
                ->icon('heroicon-o-clipboard-document-check'),

            Stat::make('Nota Media Global', number_format($averageScore, 2))
                ->description('En evaluaciones finalizadas')
                ->icon('heroicon-o-academic-cap')
                ->color($averageScore >= 5 ? 'success' : 'danger'),

            Stat::make('Usuarios', User::count())
                ->description('Usuarios del sistema')
                ->icon('heroicon-o-users')
                // ->visible(fn () => auth()->user()->hasRole('admin')),
        ];
    }
}
