<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

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

    /**
     * Get the effective salary based on employment type.
     */
    protected function salary(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->employment_type === 'PHL' 
                ? $this->salary_daily 
                : $this->salary_monthly,
        );
    }

    public function getCompletenessPercentageAttribute(): int
    {
        $fields = [
            'nik', 'email', 'phone', 'team', 'location', 
            'bank_name', 'bank_account'
        ];
        
        if ($this->employment_type === 'PHL') {
            $fields[] = 'salary_daily';
            $fields[] = 'risk_daily_amount';
        } else {
            $fields[] = 'salary_monthly';
            $fields[] = 'bpjs_health';
            $fields[] = 'bpjs_tk';
            $fields[] = 'pph21';
        }

        $totalFields = count($fields);
        $filledFields = 0;

        foreach ($fields as $field) {
            if (!empty($this->$field)) {
                $filledFields++;
            }
        }

        return ($totalFields > 0) ? (int) (($filledFields / $totalFields) * 100) : 100;
    }

    public function getCompletenessColorAttribute(): string
    {
        $percentage = $this->getCompletenessPercentageAttribute();
        if ($percentage === 100) return 'green';
        if ($percentage >= 70) return 'blue';
        if ($percentage >= 40) return 'yellow';
        return 'red';
    }

    public function statuses()
    {
        return $this->hasMany(EmployeeStatus::class);
    }

    public function phlOvertimes()
    {
        return $this->hasMany(PhlOvertime::class);
    }

    public function phlRiskAllowances()
    {
        return $this->hasMany(PhlRiskAllowance::class);
    }
}
