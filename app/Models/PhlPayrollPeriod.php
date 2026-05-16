<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhlPayrollPeriod extends Model
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
        return $this->hasMany(PhlAttendance::class);
    }
}
