<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhlOvertime extends Model
{
    protected $fillable = [
        'phl_payroll_period_id',
        'employee_id',
        'date',
        'hours',
        'amount',
        'note',
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function period()
    {
        return $this->belongsTo(PhlPayrollPeriod::class, 'phl_payroll_period_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
