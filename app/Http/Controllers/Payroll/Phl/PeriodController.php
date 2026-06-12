<?php

namespace App\Http\Controllers\Payroll\Phl;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PhlPayrollPeriod;
use App\Models\Employee;
use Carbon\Carbon;

class PeriodController extends Controller
{
    public function index()
    {
        $periods = PhlPayrollPeriod::with(['attendances.employee', 'attendances.team', 'overtimes', 'riskAllowances'])
            ->orderBy('start_date', 'desc')
            ->get();

        $phlEmployeeCount = Employee::where('employment_type', 'PHL')
            ->where('status', 'Aktif')
            ->count();

        $currentYear = date('Y');
        $ytdPaid = 0;

        foreach ($periods as $period) {
            $totalPokok = $period->attendances->sum(function ($attendance) {
                return $attendance->duration > 0 ? ($attendance->employee->salary_daily ?? 0) : 0;
            });
            $totalOvertime = $period->overtimes->sum('amount');
            $totalRisk = $period->riskAllowances->sum('amount');
            $periodTotal = $totalPokok + $totalOvertime + $totalRisk;

            $period->total_expenditure = $periodTotal;

            $periodEmployeesCount = Employee::where('employment_type', 'PHL')
                ->where(function ($q) use ($period) {
                    if ($period->status === 'Locked') {
                        $q->whereHas('phlAttendances', function ($sub) use ($period) {
                            $sub->where('phl_payroll_period_id', $period->id);
                        })
                        ->orWhereHas('phlOvertimes', function ($sub) use ($period) {
                            $sub->where('phl_payroll_period_id', $period->id);
                        })
                        ->orWhereHas('phlRiskAllowances', function ($sub) use ($period) {
                            $sub->where('phl_payroll_period_id', $period->id);
                        });
                    } else {
                        $q->where('status', 'Aktif')
                        ->orWhereHas('phlAttendances', function ($sub) use ($period) {
                            $sub->where('phl_payroll_period_id', $period->id);
                        })
                        ->orWhereHas('phlOvertimes', function ($sub) use ($period) {
                            $sub->where('phl_payroll_period_id', $period->id);
                        })
                        ->orWhereHas('phlRiskAllowances', function ($sub) use ($period) {
                            $sub->where('phl_payroll_period_id', $period->id);
                        });
                    }
                })
                ->distinct()
                ->count();

            $period->total_employees = $periodEmployeesCount ?: $phlEmployeeCount;

            if ($period->status === 'Locked' && $period->start_date->format('Y') == $currentYear) {
                $ytdPaid += $periodTotal;
            }
        }

        return view('pages.payroll.phl.periods', [
            'title' => 'Periode Gaji PHL',
            'periods' => $periods,
            'phlEmployeeCount' => $phlEmployeeCount,
            'ytdPaid' => $ytdPaid
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'date_range' => 'required|string',
        ]);

        $dates = explode(' to ', $request->date_range);

        $startDate = Carbon::createFromFormat('d-m-Y', trim($dates[0]))->format('Y-m-d');
        $endDate = isset($dates[1])
            ? Carbon::createFromFormat('d-m-Y', trim($dates[1]))->format('Y-m-d')
            : $startDate;

        $period = PhlPayrollPeriod::create([
            'title' => $request->title,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => 'Open',
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('payroll.phl.periods.show', $period->id)
            ->with('success', 'Periode gaji baru berhasil dibuka.');
    }

    public function show($id)
    {
        $period = PhlPayrollPeriod::with(['attendances.employee', 'attendances.team', 'overtimes.employee', 'riskAllowances.employee'])->findOrFail($id);

        $period->setRelation('attendances', $period->attendances->sortBy([
            ['employee.name', 'asc'],
            ['date', 'asc']
        ]));

        $employees = Employee::where('employment_type', 'PHL')
            ->where(function ($q) use ($period) {
                if ($period->status === 'Locked') {
                    $q->whereHas('phlAttendances', function ($sub) use ($period) {
                        $sub->where('phl_payroll_period_id', $period->id);
                    })
                    ->orWhereHas('phlOvertimes', function ($sub) use ($period) {
                        $sub->where('phl_payroll_period_id', $period->id);
                    })
                    ->orWhereHas('phlRiskAllowances', function ($sub) use ($period) {
                        $sub->where('phl_payroll_period_id', $period->id);
                    });
                } else {
                    $q->where('status', 'Aktif')
                    ->orWhereHas('phlAttendances', function ($sub) use ($period) {
                        $sub->where('phl_payroll_period_id', $period->id);
                    })
                    ->orWhereHas('phlOvertimes', function ($sub) use ($period) {
                        $sub->where('phl_payroll_period_id', $period->id);
                    })
                    ->orWhereHas('phlRiskAllowances', function ($sub) use ($period) {
                        $sub->where('phl_payroll_period_id', $period->id);
                    });
                }
            })
            ->distinct()
            ->get();

        return view('pages.payroll.phl.period-detail', [
            'title' => 'Detail Periode Gaji PHL',
            'period' => $period,
            'employees' => $employees
        ]);
    }

    public function generate($id)
    {
        try {
            $period = PhlPayrollPeriod::findOrFail($id);
            if ($period->status === 'Locked') {
                return redirect()->route('payroll.phl.periods.show', [$id, 'tab' => 'slips'])->with('error', 'Periode payroll ini sudah dikunci.');
            }
            
            \DB::transaction(function () use ($period) {
                // Freeze team for all attendances in this period
                \DB::statement('
                    UPDATE phl_attendances 
                    SET team_id = (SELECT team_id FROM employees WHERE employees.id = phl_attendances.employee_id)
                    WHERE phl_payroll_period_id = ?
                ', [$period->id]);

                $period->update([
                    'status' => 'Locked'
                ]);
            });

            return redirect()->route('payroll.phl.periods.show', [$id, 'tab' => 'slips'])
                ->with('success', 'Payroll berhasil digenerate dan slip gaji telah diterbitkan!');
        } catch (\Exception $e) {
            return redirect()->route('payroll.phl.periods.show', [$id, 'tab' => 'overview'])
                ->with('error', 'Terjadi kesalahan saat memproses payroll: ' . $e->getMessage());
        }
    }

    public function unlock($id)
    {
        try {
            $period = PhlPayrollPeriod::findOrFail($id);
            if ($period->status !== 'Locked') {
                return redirect()->route('payroll.phl.periods.show', $id)
                    ->with('error', 'Periode payroll ini tidak dalam status terkunci.');
            }

            $period->update([
                'status' => 'Open'
            ]);

            return redirect()->route('payroll.phl.periods.show', $id)
                ->with('success', 'Periode payroll berhasil dibuka kunci. Anda sekarang dapat melakukan perubahan data.');
        } catch (\Exception $e) {
            return redirect()->route('payroll.phl.periods.show', $id)
                ->with('error', 'Terjadi kesalahan saat membuka kunci payroll: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $period = PhlPayrollPeriod::findOrFail($id);
        $period->delete();

        return redirect()->route('payroll.phl.periods')->with('success', 'Periode gaji berhasil dihapus.');
    }
}
