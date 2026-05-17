<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\PkwtPayrollPeriod;
use Illuminate\Http\Request;

class PkwtPayrollController extends Controller
{
    public function index()
    {
        $periods = PkwtPayrollPeriod::with(['overtimes', 'riskAllowances', 'otherAllowances'])
            ->orderBy('start_date', 'desc')
            ->get();
            
        $pkwtEmployeeCount = Employee::where('employment_type', 'PKWT')
            ->where('status', 'Aktif')
            ->count();

        $currentYear = date('Y');
        $ytdPaid = 0;
        
        foreach ($periods as $period) {
            $totalPokok = Employee::where('employment_type', 'PKWT')->where('status', 'Aktif')->sum('salary_monthly');
            $totalOvertime = $period->overtimes->sum('amount');
            $totalRisk = $period->riskAllowances->sum('amount');
            $totalOthers = $period->otherAllowances->sum('amount');
            
            $totalDeductions = Employee::where('employment_type', 'PKWT')
                ->where('status', 'Aktif')
                ->get()
                ->sum(function ($emp) {
                    return ($emp->bpjs_health ?? 0) + ($emp->bpjs_tk ?? 0) + ($emp->pph21 ?? 0);
                });
                
            $periodTotal = max(0, $totalPokok + $totalOvertime + $totalRisk + $totalOthers - $totalDeductions);
            
            $period->total_expenditure = $periodTotal;
            $period->total_employees = $pkwtEmployeeCount;
            
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

        $startDate = \Carbon\Carbon::createFromFormat('d-m-Y', trim($dates[0]))->format('Y-m-d');
        $endDate = isset($dates[1]) 
            ? \Carbon\Carbon::createFromFormat('d-m-Y', trim($dates[1]))->format('Y-m-d') 
            : $startDate;

        $period = PkwtPayrollPeriod::create([
            'title' => $request->title,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => 'Open',
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('payroll.pkwt.periods.show', $period->id)
            ->with('success', 'Periode gaji PKWT baru berhasil dibuka.');
    }

    public function show($id)
    {
        $period = PkwtPayrollPeriod::with([
            'attendances.employee', 
            'overtimes.employee', 
            'riskAllowances.employee',
            'otherAllowances.employee'
        ])->findOrFail($id);
        
        $employees = Employee::where('employment_type', 'PKWT')
            ->where('status', 'Aktif')
            ->get();

        return view('pages.payroll.pkwt.period-detail', [
            'title' => 'Detail Periode Gaji PKWT',
            'period' => $period,
            'employees' => $employees,
        ]);
    }

    public function destroy($id)
    {
        $period = PkwtPayrollPeriod::findOrFail($id);
        $period->delete();

        return redirect()->route('payroll.pkwt.periods')
            ->with('success', 'Periode gaji PKWT berhasil dihapus.');
    }
}
