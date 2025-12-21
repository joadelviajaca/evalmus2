<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;


class UsersCount extends StatsOverviewWidget
{
    // public static function canView(): bool
    // {
    //     return auth()->user()->hasRole('super_admin');
    // }
    protected function getStats(): array
    {
        return [
            Stat::make('Usuarios', User::count())
            ->description('Usuarios del sistema')
            ->icon('heroicon-o-users')
            ->color('warning'),
        ];
    }
}
