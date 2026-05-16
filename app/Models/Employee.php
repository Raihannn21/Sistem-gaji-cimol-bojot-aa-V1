<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = [
        'emp_no',
        'no_id',
        'nik',
        'name',
        'email',
        'phone',
        'team',
        'location',
        'employment_type',
        'status',
        'salary_daily',
        'salary_monthly',
        'risk_daily_amount',
        'bpjs_health',
        'bpjs_tk',
        'pph21',
        'bank_name',
        'bank_account',
    ];

    protected $casts = [
        'salary_daily' => 'decimal:2',
        'salary_monthly' => 'decimal:2',
        'risk_daily_amount' => 'decimal:2',
        'bpjs_health' => 'decimal:2',
        'bpjs_tk' => 'decimal:2',
        'pph21' => 'decimal:2',
    ];
}
