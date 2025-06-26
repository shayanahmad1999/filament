<?php

namespace App\Filament\App\Widgets;

use App\Models\Department;
use App\Models\Employee;
use App\Models\Team;
use Filament\Facades\Filament;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsAppOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        return [
            Stat::make('Users', Team::find(Filament::getTenant())->first()->members->count())
                ->description('All users')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('success'),
            Stat::make('Departments', Department::query()->whereBelongsTo(Filament::getTenant())->count())
                ->description('All departments')
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('danger'),
            Stat::make('Employess', Employee::query()->whereBelongsTo(Filament::getTenant())->count())
                ->description('All employees')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
        ];
    }
}
