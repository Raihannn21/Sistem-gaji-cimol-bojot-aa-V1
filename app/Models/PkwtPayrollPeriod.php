<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PkwtPayrollPeriod extends Model
{
    protected $fillable = [
        'title',
        'start_date',
        'end_date',
        'status',
        'created_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function attendances()
    {
        return $this->hasMany(PkwtAttendance::class);
    }

    public function overtimes()
    {
        return $this->hasMany(PkwtOvertime::class, 'pkwt_payroll_period_id');
    }

    public function riskAllowances()
    {
        return $this->hasMany(PkwtRiskAllowance::class, 'pkwt_payroll_period_id');
    }

    public function otherAllowances()
    {
        return $this->hasMany(PkwtOtherAllowance::class, 'pkwt_payroll_period_id');
    }

    public function periodTeams()
    {
        return $this->hasMany(PkwtPayrollPeriodTeam::class, 'pkwt_payroll_period_id');
    }

    public function selectedTeams()
    {
        return $this->belongsToMany(Team::class, 'pkwt_payroll_period_teams', 'pkwt_payroll_period_id', 'team_id')
            ->withPivot('off_dates', 'work_days')
            ->withTimestamps();
    }
}
