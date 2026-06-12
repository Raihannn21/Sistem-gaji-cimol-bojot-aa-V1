<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PkwtPayrollPeriodTeam extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'pkwt_payroll_period_id',
        'team_id',
        'off_dates',
        'work_days',
    ];

    protected $casts = [
        'off_dates' => 'array',
    ];

    public function period()
    {
        return $this->belongsTo(PkwtPayrollPeriod::class, 'pkwt_payroll_period_id');
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}
