<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeesFile extends Model
{
    use HasFactory;

    protected $fillable = ['employee_id', 'file_name', 'file_path'];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
