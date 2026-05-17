<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PkwtOtherAllowance extends Model
{
    protected $fillable = [
        'pkwt_payroll_period_id',
        'employee_id',
        'allowance_type',
        'amount',
        'note',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function period()
    {
        return $this->belongsTo(PkwtPayrollPeriod::class, 'pkwt_payroll_period_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
