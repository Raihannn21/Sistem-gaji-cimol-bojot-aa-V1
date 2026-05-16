<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\PhlPayrollPeriod;

class PhlPayrollController extends Controller
{
    public function index()
    {
        $periods = PhlPayrollPeriod::orderBy('start_date', 'desc')->get();
        return view('pages.payroll.phl.periods', [
            'title' => 'Periode Gaji PHL',
            'periods' => $periods
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'date_range' => 'required|string',
        ]);

        $dates = explode(' to ', $request->date_range);
        
        $startDate = trim($dates[0]);
        $endDate = isset($dates[1]) ? trim($dates[1]) : $startDate;

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
        $period = PhlPayrollPeriod::findOrFail($id);
        
        return view('pages.payroll.phl.period-detail', [
            'title' => 'Detail Periode Gaji PHL',
            'period' => $period
        ]);
    }

    public function destroy($id)
    {
        $period = PhlPayrollPeriod::findOrFail($id);
        $period->delete();

        return redirect()->route('payroll.phl.periods')->with('success', 'Periode gaji berhasil dihapus.');
    }
}
