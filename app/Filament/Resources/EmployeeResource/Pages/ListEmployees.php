<?php

namespace App\Filament\Resources\EmployeeResource\Pages;

use App\Filament\Resources\EmployeeResource;
use App\Models\Employee;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListEmployees extends ListRecords
{
    protected static string $resource = EmployeeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'All' => Tab::make()
                ->badge(Employee::query()->count())
                ->badgeColor('success'),
            'This week' =>  Tab::make()
                ->badge(Employee::query()->where('date_hired', '>=', now()->subWeek())->count())
                ->badgeColor('primary')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('date_hired', '>=', now()->subWeek())),
            'This month' => Tab::make()
                ->badge(Employee::query()->where('date_hired', '>=', now()->subMonth())->count())
                ->badgeColor('secondary')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('date_hired', '>=', now()->subMonth())),
            'This Year' => Tab::make()
                ->badge(Employee::query()->where('date_hired', '>=', now()->subYear())->count())
                ->badgeColor('warning')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('date_hired', '>=', now()->subYear())),
        ];
    }
}
