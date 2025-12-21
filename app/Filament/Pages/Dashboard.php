<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Dashboard as BaseDashboard;


class Dashboard extends BaseDashboard
{
    protected static ?string $navigationLabel = 'Inicio';
    protected static ?string $title = 'Panel principal';
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-home';
    // protected string $view = 'filament.pages.dashboard';

    public function getHeading(): string
    {
        return 'Bienvenido a Evalmus';
    }

    public function getSubheading(): ?string
    {
        return 'Sistema de gestión y evaluación de proyectos mediante rúbricas';
    }

    public function getWidgets(): array
    {
        return [
            \App\Filament\Widgets\WelcomeWidget::class,
            \App\Filament\Widgets\StatsOverview::class,
            \App\Filament\Widgets\ProjectsByStatusChart::class,
            \App\Filament\Widgets\RubricsUsageChart::class,
            \App\Filament\Widgets\QuickActions::class,
        ];
    }
}
