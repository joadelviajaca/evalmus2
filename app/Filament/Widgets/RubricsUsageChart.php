<?php

namespace App\Filament\Widgets;

use App\Models\Rubric;
use Filament\Widgets\ChartWidget;

class RubricsUsageChart extends ChartWidget
{
    protected ?string $heading = 'Gráfico de Uso de Rúbricas';

    protected function getData(): array
    {
        
        $rubrics = Rubric::withCount('projects')->get();

        $colors = [
        '#0E8B68', // primary
        '#34D399', // secondary
        '#1F6F8B', // accent
        '#F59E0B', // warning
        '#22C55E', // success
        '#3B82F6', // info
    ];

        return [
            'datasets' => [
                [
                    'label' => 'Proyectos',
                    'data' => $rubrics->pluck('projects_count'),
                    'backgroundColor' => $rubrics->map(
                    fn ($_, $index) => $colors[$index % count($colors)]
                ),
                ],
            ],
            'labels' => $rubrics->pluck('title'),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
