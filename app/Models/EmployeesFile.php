<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeesFile extends Model
{
    use HasFactory;

    protected $fillable = ['employee_id', 'file_name', 'file_path', 'team_id'];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    protected static function booted()
    {
        static::creating(function ($file) {
            if (empty($file->team_id) && $file->employee) {
                $file->team_id = $file->employee->team_id;
            }
        });
    }
}
