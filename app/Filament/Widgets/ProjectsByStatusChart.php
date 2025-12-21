<?php

namespace App\Filament\Widgets;

use App\Models\Project;
use Filament\Widgets\ChartWidget;

class ProjectsByStatusChart extends ChartWidget
{
    protected ?string $heading = 'Gráfico de Proyectos por Estado';

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Proyectos',
                    'data' => [
                        Project::where('state', 'pending')->count(),
                        Project::where('state', 'evaluating')->count(),
                        Project::where('state', 'finished')->count(),
                    ],
                    'backgroundColor' => [
                        '#f59e0b',
                        '#3b82f6',
                        '#10b981',
                    ],
                ],
            ],
            'labels' => [
                'Pendientes',
                'En evaluación',
                'Finalizados',
            ],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
