<?php

namespace App\Http\Controllers;

use App\Models\EmployeeStatus;
use App\Models\Employee;
use App\Http\Requests\EmployeeStatus\StoreEmployeeStatusRequest;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmployeeStatusController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim($request->query('search'));

        $statuses = EmployeeStatus::query()
            ->with('employee')
            ->when($search, function ($query, $search) {
                $query->whereHas('employee', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('emp_no', 'like', "%{$search}%")
                        ->orWhere('no_id', 'like', "%{$search}%");
                })->orWhere('reason', 'like', "%{$search}%");
            })
            ->orderBy('effective_date', 'desc')
            ->get()
            ->map(fn(EmployeeStatus $status) => [
                'id' => $status->id,
                'name' => $status->employee->name ?? '-',
                'emp_no' => $status->employee->emp_no ?? '-',
                'no_id' => $status->employee->no_id ?? '-',
                'nik' => $status->employee->nik ?? '-',
                'role' => $status->employee->employment_type ?? '-',
                'type' => $status->type,
                'date' => \Carbon\Carbon::parse($status->effective_date)->format('d-m-Y'),
                'reason' => $status->reason,
                'team' => $status->employee->team ?? '-',
                'location' => $status->employee->location ?? '-',
            ]);

        $stats = [
            'total' => $statuses->count(),
            'resign' => $statuses->where('type', 'Resign')->count(),
            'sphk' => $statuses->where('type', 'SPHK')->count(),
        ];

        $activeEmployees = Employee::where('status', 'Aktif')
            ->orderBy('name')
            ->select('id', 'name', 'emp_no')
            ->get();

        return view('pages.employees.status', [
            'title' => 'Data Resign & SPHK',
            'statuses' => $statuses,
            'stats' => $stats,
            'activeEmployees' => $activeEmployees,
        ]);
    }

    public function store(StoreEmployeeStatusRequest $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validated();

        \Illuminate\Support\Facades\DB::transaction(function () use ($validated) {
            EmployeeStatus::create($validated);

            Employee::where('id', $validated['employee_id'])->update([
                'status' => $validated['type']
            ]);
        });

        return redirect()->back()->with('success', 'Status pemberhentian karyawan berhasil disimpan.');
    }

    public function destroy($id): \Illuminate\Http\RedirectResponse
    {
        $statusRecord = EmployeeStatus::findOrFail($id);

        \Illuminate\Support\Facades\DB::transaction(function () use ($statusRecord) {
            $statusRecord->employee->update(['status' => 'Aktif']);
            $statusRecord->delete();
        });

        return redirect()->back()->with('success', 'Data pemberhentian berhasil dihapus dan status dikembalikan menjadi Aktif.');
    }

    private function formatAmount($value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        return is_numeric($value) ? (string) ((int) round((float) $value)) : null;
    }
}
