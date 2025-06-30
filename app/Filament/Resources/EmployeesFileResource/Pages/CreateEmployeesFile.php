<?php

namespace App\Filament\Resources\EmployeesFileResource\Pages;

use App\Filament\Resources\EmployeesFileResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateEmployeesFile extends CreateRecord
{
    protected static string $resource = EmployeesFileResource::class;
}
