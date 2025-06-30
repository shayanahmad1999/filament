<?php

namespace App\Filament\Resources\EmployeesFileResource\Pages;

use App\Filament\Resources\EmployeesFileResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEmployeesFile extends EditRecord
{
    protected static string $resource = EmployeesFileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
