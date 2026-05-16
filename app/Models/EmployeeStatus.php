<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeStatus extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'type',
        'effective_date',
        'reason',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
