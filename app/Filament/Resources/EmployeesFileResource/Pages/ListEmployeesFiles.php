<?php

namespace App\Filament\Resources\EmployeesFileResource\Pages;

use App\Filament\Resources\EmployeesFileResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEmployeesFiles extends ListRecords
{
    protected static string $resource = EmployeesFileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
