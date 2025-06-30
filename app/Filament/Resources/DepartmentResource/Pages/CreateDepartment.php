<?php

namespace App\Filament\Resources\DepartmentResource\Pages;

use App\Filament\Resources\DepartmentResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateDepartment extends CreateRecord
{
    protected static string $resource = DepartmentResource::class;

    // protected function mutateFormDataBeforeCreate(array $data): array
    // {
    //     $data['team_id'] = 1;
    //     return $data;
    // }
}
