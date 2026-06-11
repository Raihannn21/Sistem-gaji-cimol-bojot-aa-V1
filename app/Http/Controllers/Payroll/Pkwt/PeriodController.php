<?php

namespace App\Http\Controllers\Payroll\Pkwt;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\PkwtPayrollPeriod;
use App\Models\PkwtPayrollPeriodTeam;
use App\Models\Team;
use Carbon\Carbon;

class PeriodController extends Controller
{
    public function index()
    {
        $periods = PkwtPayrollPeriod::with(['attendances.employee', 'attendances.team', 'overtimes', 'riskAllowances', 'otherAllowances', 'periodTeams'])
            ->orderBy('start_date', 'desc')
            ->get();

        $pkwtEmployeeCount = Employee::where('employment_type', 'PKWT')
            ->where('status', 'Aktif')
            ->count();

        $currentYear = date('Y');
        $ytdPaid = 0;

        foreach ($periods as $period) {
            $startDate = Carbon::parse($period->start_date);
            $endDate = Carbon::parse($period->end_date);
            $totalPeriodDays = $startDate->diffInDays($endDate) + 1;

            $periodEmployeeIds = $period->attendances->pluck('employee_id')->unique();
            $employeesInPeriodCount = count($periodEmployeeIds);

            $periodTotal = 0;
            $groupedAttendances = $period->attendances->groupBy('employee_id');

            foreach ($groupedAttendances as $empId => $empAttendances) {
                $employee = $empAttendances->first()->employee;
                if (!$employee)
                    continue;

                $employeeAttendance = $empAttendances->first();
                $resolvedTeamId = ($period->status === 'Locked' && $employeeAttendance && $employeeAttendance->team_id)
                    ? $employeeAttendance->team_id
                    : $employee->team_id;
                
                $periodTeam = $period->periodTeams->where('team_id', $resolvedTeamId)->first();
                $offDates = $periodTeam ? ($periodTeam->off_dates ?? []) : [];

                $daysWorked = $empAttendances->filter(function ($att) use ($offDates) {
                    if ($att->duration <= 0) {
                        return false;
                    }
                    $dateStr = $att->date instanceof Carbon ? $att->date->format('Y-m-d') : $att->date;
                    return !in_array($dateStr, $offDates);
                })->count();

                $workDays = $periodTeam ? $periodTeam->work_days : ($totalPeriodDays ?: 1);
                $totalMonthly = ($employee->salary_monthly ?? 0) + ($employee->attendance_allowance ?? 0);
                $harian = $workDays > 0 ? ($totalMonthly / $workDays) : 0;
                $pokok = $daysWorked * $harian;

                $lembur = $period->overtimes->where('employee_id', $empId)->sum('amount');
                $risiko = $period->riskAllowances->where('employee_id', $empId)->sum('amount');
                $other = $period->otherAllowances->where('employee_id', $empId)->sum('amount');

                $bpjsHealth = $employee->bpjs_health ?? 0;
                $bpjsTk = $employee->bpjs_tk ?? 0;
                $pph21 = $employee->pph21 ?? 0;
                $potongan = $bpjsHealth + $bpjsTk + $pph21;

                $netPay = max(0, $pokok + $lembur + $risiko + $other - $potongan);
                $periodTotal += $netPay;
            }

            $period->total_expenditure = $periodTotal;
            $selectedTeamIds = $period->periodTeams->pluck('team_id')->toArray();
            $periodEmployeesCount = Employee::where('employment_type', 'PKWT')
                ->where(function ($q) use ($period, $selectedTeamIds) {
                    $q->where(function($subQ) use ($selectedTeamIds, $period) {
                        $subQ->where('status', 'Aktif')
                            ->whereIn('team_id', $selectedTeamIds)
                            ->where('created_at', '<=', $period->end_date . ' 23:59:59');
                    })
                    ->orWhereHas('pkwtAttendances', function ($sub) use ($period) {
                        $sub->where('pkwt_payroll_period_id', $period->id);
                    })
                    ->orWhereHas('pkwtOvertimes', function ($sub) use ($period) {
                        $sub->where('pkwt_payroll_period_id', $period->id);
                    })
                    ->orWhereHas('pkwtRiskAllowances', function ($sub) use ($period) {
                        $sub->where('pkwt_payroll_period_id', $period->id);
                    })
                    ->orWhereHas('pkwtOtherAllowances', function ($sub) use ($period) {
                        $sub->where('pkwt_payroll_period_id', $period->id);
                    });
                })
                ->distinct()
                ->count();

            $period->total_employees = $periodEmployeesCount ?: $pkwtEmployeeCount;

            if ($period->status === 'Locked' && $period->start_date->format('Y') == $currentYear) {
                $ytdPaid += $periodTotal;
            }
        }

        return view('pages.payroll.pkwt.periods', [
            'title' => 'Periode Gaji PKWT',
            'periods' => $periods,
            'pkwtEmployeeCount' => $pkwtEmployeeCount,
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

        $period = PkwtPayrollPeriod::create([
            'title' => $request->title,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => 'Open',
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('payroll.pkwt.periods.setup', $period->id)
            ->with('success', 'Periode gaji PKWT baru berhasil dibuka. Silakan tentukan tim dan hari libur.');
    }

    public function setup($id)
    {
        $period = PkwtPayrollPeriod::with('periodTeams')->findOrFail($id);
        if ($period->status === 'Locked') {
            return redirect()->route('payroll.pkwt.periods.show', $id)
                ->with('error', 'Periode ini sudah dikunci dan tidak dapat diatur lagi.');
        }

        $teams = Team::withCount(['employees' => function($query) {
            $query->where('employment_type', 'PKWT')->where('status', 'Aktif');
        }])->orderBy('name', 'asc')->get();
        
        $dates = [];
        $current = Carbon::parse($period->start_date);
        $end = Carbon::parse($period->end_date);
        while ($current->lte($end)) {
            $dates[] = $current->format('Y-m-d');
            $current->addDay();
        }

        return view('pages.payroll.pkwt.setup', [
            'title' => 'Setup Tim & Hari Libur - PKWT',
            'period' => $period,
            'teams' => $teams,
            'dates' => $dates,
        ]);
    }

    public function saveSetup(Request $request, $id)
    {
        $period = PkwtPayrollPeriod::findOrFail($id);
        if ($period->status === 'Locked') {
            return redirect()->route('payroll.pkwt.periods.show', $id)
                ->with('error', 'Periode ini sudah dikunci.');
        }

        $request->validate([
            'teams' => 'required|array',
            'teams.*' => 'exists:teams,id',
            'off_dates' => 'nullable|array',
        ]);

        \DB::transaction(function () use ($period, $request) {
            $period->periodTeams()->delete();

            $totalPeriodDays = Carbon::parse($period->start_date)->diffInDays(Carbon::parse($period->end_date)) + 1;

            foreach ($request->teams as $teamId) {
                $teamOffDates = $request->input("off_dates.{$teamId}", []);
                if (is_string($teamOffDates)) {
                    $teamOffDates = json_decode($teamOffDates, true) ?: [];
                }
                $teamOffDates = array_values(array_filter($teamOffDates));
                
                $workDays = $totalPeriodDays - count($teamOffDates);

                PkwtPayrollPeriodTeam::create([
                    'pkwt_payroll_period_id' => $period->id,
                    'team_id' => $teamId,
                    'off_dates' => $teamOffDates,
                    'work_days' => max(0, $workDays),
                ]);
            }
        });

        return redirect()->route('payroll.pkwt.periods.show', $period->id)
            ->with('success', 'Pengaturan tim dan hari libur berhasil disimpan.');
    }

    public function show($id)
    {
        $period = PkwtPayrollPeriod::with([
            'attendances.employee',
            'attendances.team',
            'overtimes.employee',
            'riskAllowances.employee',
            'otherAllowances.employee',
            'periodTeams.team'
        ])->findOrFail($id);

        $period->setRelation('attendances', $period->attendances->sortBy([
            ['employee.name', 'asc'],
            ['date', 'asc']
        ]));

        $selectedTeamIds = $period->periodTeams->pluck('team_id')->toArray();

        $employees = Employee::where('employment_type', 'PKWT')
            ->where(function ($q) use ($period, $selectedTeamIds) {
                $q->where(function($subQ) use ($selectedTeamIds, $period) {
                    $subQ->where('status', 'Aktif')
                        ->whereIn('team_id', $selectedTeamIds)
                        ->where('created_at', '<=', $period->end_date . ' 23:59:59');
                })
                ->orWhereHas('pkwtAttendances', function ($sub) use ($period) {
                    $sub->where('pkwt_payroll_period_id', $period->id);
                })
                ->orWhereHas('pkwtOvertimes', function ($sub) use ($period) {
                    $sub->where('pkwt_payroll_period_id', $period->id);
                })
                ->orWhereHas('pkwtRiskAllowances', function ($sub) use ($period) {
                    $sub->where('pkwt_payroll_period_id', $period->id);
                })
                ->orWhereHas('pkwtOtherAllowances', function ($sub) use ($period) {
                    $sub->where('pkwt_payroll_period_id', $period->id);
                });
            })
            ->distinct()
            ->get();

        return view('pages.payroll.pkwt.period-detail', [
            'title' => 'Detail Periode Gaji PKWT',
            'period' => $period,
            'employees' => $employees,
        ]);
    }

    public function generate($id)
    {
        try {
            $period = PkwtPayrollPeriod::findOrFail($id);
            if ($period->status === 'Locked') {
                return redirect()->route('payroll.pkwt.periods.show', [$id, 'tab' => 'slips'])->with('error', 'Periode payroll ini sudah dikunci.');
            }
            
            \DB::transaction(function () use ($period) {
                // Freeze team for all attendances in this period
                \DB::statement('
                    UPDATE pkwt_attendances 
                    SET team_id = (SELECT team_id FROM employees WHERE employees.id = pkwt_attendances.employee_id)
                    WHERE pkwt_payroll_period_id = ?
                ', [$period->id]);

                $period->update([
                    'status' => 'Locked'
                ]);
            });

            return redirect()->route('payroll.pkwt.periods.show', [$id, 'tab' => 'slips'])
                ->with('success', 'Payroll PKWT berhasil digenerate dan slip gaji telah diterbitkan!');
        } catch (\Exception $e) {
            return redirect()->route('payroll.pkwt.periods.show', [$id, 'tab' => 'overview'])
                ->with('error', 'Terjadi kesalahan saat memproses payroll PKWT: ' . $e->getMessage());
        }
    }

    public function unlock($id)
    {
        try {
            $period = PkwtPayrollPeriod::findOrFail($id);
            if ($period->status !== 'Locked') {
                return redirect()->route('payroll.pkwt.periods.show', $id)
                    ->with('error', 'Periode payroll ini tidak dalam status terkunci.');
            }

            $period->update([
                'status' => 'Open'
            ]);

            return redirect()->route('payroll.pkwt.periods.show', $id)
                ->with('success', 'Periode payroll PKWT berhasil dibuka kunci. Anda sekarang dapat melakukan perubahan data.');
        } catch (\Exception $e) {
            return redirect()->route('payroll.pkwt.periods.show', $id)
                ->with('error', 'Terjadi kesalahan saat membuka kunci payroll PKWT: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $period = PkwtPayrollPeriod::findOrFail($id);
        $period->delete();

        return redirect()->route('payroll.pkwt.periods')
            ->with('success', 'Periode gaji PKWT berhasil dihapus.');
    }
}
