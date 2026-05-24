<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhlAttendance extends Model
{
    protected $fillable = [
        'phl_payroll_period_id',
        'employee_id',
        'date',
        'scan_in',
        'scan_out',
        'late_time',
        'early_time',
        'duration',
        'note',
        'team_id',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function period()
    {
        return $this->belongsTo(PhlPayrollPeriod::class, 'phl_payroll_period_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}
