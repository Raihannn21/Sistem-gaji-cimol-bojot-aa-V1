<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;

// Dashboard
Route::get('/', function () {
    return view('pages.dashboard.payroll', ['title' => 'Payroll Analytics']);
})->name('dashboard');

// Manajemen Karyawan
Route::prefix('employees')->group(function () {
    Route::get('/', [EmployeeController::class, 'index'])->name('employees.index');

    Route::post('/', [EmployeeController::class, 'store'])->name('employees.store');
    Route::post('/import', [EmployeeController::class, 'import'])->name('employees.import');
    Route::put('/{employee}', [EmployeeController::class, 'update'])->name('employees.update');
    Route::delete('/{employee}', [EmployeeController::class, 'destroy'])->name('employees.destroy');
    
    Route::get('/status', [App\Http\Controllers\EmployeeStatusController::class, 'index'])->name('employees.status');
    Route::post('/status', [App\Http\Controllers\EmployeeStatusController::class, 'store'])->name('employees.status.store');
    Route::delete('/status/{id}', [App\Http\Controllers\EmployeeStatusController::class, 'destroy'])->name('employees.status.destroy');
    
});

// Payroll PHL
Route::prefix('payroll/phl')->group(function () {
    Route::get('/periods', [App\Http\Controllers\PhlPayrollController::class, 'index'])->name('payroll.phl.periods');
    Route::post('/periods', [App\Http\Controllers\PhlPayrollController::class, 'store'])->name('payroll.phl.periods.store');
    Route::get('/periods/{id}', [App\Http\Controllers\PhlPayrollController::class, 'show'])->name('payroll.phl.periods.show');
    Route::get('/periods/{id}/export/pdf', [App\Http\Controllers\PhlPayrollController::class, 'exportPdf'])->name('payroll.phl.periods.export.pdf');
    Route::get('/periods/{id}/slips/{employeeId}/pdf', [App\Http\Controllers\PhlPayrollController::class, 'exportIndividualPdf'])->name('payroll.phl.periods.slip.pdf');
    Route::get('/periods/{id}/export/excel', [App\Http\Controllers\PhlPayrollController::class, 'exportExcel'])->name('payroll.phl.periods.export.excel');
    Route::get('/periods/{id}/export/bca', [App\Http\Controllers\PhlPayrollController::class, 'exportBca'])->name('payroll.phl.periods.export.bca');
    Route::post('/periods/{id}/import-attendance', [App\Http\Controllers\PhlPayrollController::class, 'importAttendance'])->name('payroll.phl.periods.import-attendance');
    Route::put('/periods/{id}/attendance/{attendanceId}', [App\Http\Controllers\PhlPayrollController::class, 'updateAttendance'])->name('payroll.phl.periods.update-attendance');
    Route::delete('/periods/{id}/attendance/{attendanceId}', [App\Http\Controllers\PhlPayrollController::class, 'destroyAttendance'])->name('payroll.phl.periods.destroy-attendance');
    Route::post('/periods/{id}/generate', [App\Http\Controllers\PhlPayrollController::class, 'generate'])->name('payroll.phl.periods.generate');
    Route::delete('/periods/{id}', [App\Http\Controllers\PhlPayrollController::class, 'destroy'])->name('payroll.phl.periods.destroy');
    
    // Lembur (Overtime) PHL
    Route::post('/periods/{id}/overtime', [App\Http\Controllers\PhlPayrollController::class, 'storeOvertime'])->name('payroll.phl.periods.store-overtime');
    Route::put('/periods/{id}/overtime/{overtimeId}', [App\Http\Controllers\PhlPayrollController::class, 'updateOvertime'])->name('payroll.phl.periods.update-overtime');
    Route::delete('/periods/{id}/overtime/{overtimeId}', [App\Http\Controllers\PhlPayrollController::class, 'destroyOvertime'])->name('payroll.phl.periods.destroy-overtime');

    // Tunjangan Risiko (Risk Allowance) PHL
    Route::post('/periods/{id}/risk', [App\Http\Controllers\PhlPayrollController::class, 'storeRisk'])->name('payroll.phl.periods.store-risk');
    Route::put('/periods/{id}/risk/{riskId}', [App\Http\Controllers\PhlPayrollController::class, 'updateRisk'])->name('payroll.phl.periods.update-risk');
    Route::delete('/periods/{id}/risk/{riskId}', [App\Http\Controllers\PhlPayrollController::class, 'destroyRisk'])->name('payroll.phl.periods.destroy-risk');
});

// Payroll PKWT
Route::prefix('payroll/pkwt')->group(function () {
    Route::get('/periods', [App\Http\Controllers\PkwtPayrollController::class, 'index'])->name('payroll.pkwt.periods');
    Route::post('/periods', [App\Http\Controllers\PkwtPayrollController::class, 'store'])->name('payroll.pkwt.periods.store');
    Route::get('/periods/{id}', [App\Http\Controllers\PkwtPayrollController::class, 'show'])->name('payroll.pkwt.periods.show');
    Route::get('/periods/{id}/export/pdf', [App\Http\Controllers\PkwtPayrollController::class, 'exportPdf'])->name('payroll.pkwt.periods.export.pdf');
    Route::get('/periods/{id}/slips/{employeeId}/pdf', [App\Http\Controllers\PkwtPayrollController::class, 'exportIndividualPdf'])->name('payroll.pkwt.periods.slip.pdf');
    Route::get('/periods/{id}/export/excel', [App\Http\Controllers\PkwtPayrollController::class, 'exportExcel'])->name('payroll.pkwt.periods.export.excel');
    Route::get('/periods/{id}/export/bca', [App\Http\Controllers\PkwtPayrollController::class, 'exportBca'])->name('payroll.pkwt.periods.export.bca');
    Route::post('/periods/{id}/generate', [App\Http\Controllers\PkwtPayrollController::class, 'generate'])->name('payroll.pkwt.periods.generate');
    Route::post('/periods/{id}/import-attendance', [App\Http\Controllers\PkwtPayrollController::class, 'importAttendance'])->name('payroll.pkwt.periods.import-attendance');
    Route::put('/periods/{id}/attendance/{attendanceId}', [App\Http\Controllers\PkwtPayrollController::class, 'updateAttendance'])->name('payroll.pkwt.periods.update-attendance');
    Route::delete('/periods/{id}/attendance/{attendanceId}', [App\Http\Controllers\PkwtPayrollController::class, 'destroyAttendance'])->name('payroll.pkwt.periods.destroy-attendance');
    
    // Overtime (Lembur) PKWT
    Route::post('/periods/{id}/overtime', [App\Http\Controllers\PkwtPayrollController::class, 'storeOvertime'])->name('payroll.pkwt.periods.store-overtime');
    Route::put('/periods/{id}/overtime/{overtimeId}', [App\Http\Controllers\PkwtPayrollController::class, 'updateOvertime'])->name('payroll.pkwt.periods.update-overtime');
    Route::delete('/periods/{id}/overtime/{overtimeId}', [App\Http\Controllers\PkwtPayrollController::class, 'destroyOvertime'])->name('payroll.pkwt.periods.destroy-overtime');
    
    // Risk Allowance (Risiko) PKWT
    Route::post('/periods/{id}/risk', [App\Http\Controllers\PkwtPayrollController::class, 'storeRisk'])->name('payroll.pkwt.periods.store-risk');
    Route::put('/periods/{id}/risk/{riskId}', [App\Http\Controllers\PkwtPayrollController::class, 'updateRisk'])->name('payroll.pkwt.periods.update-risk');
    Route::delete('/periods/{id}/risk/{riskId}', [App\Http\Controllers\PkwtPayrollController::class, 'destroyRisk'])->name('payroll.pkwt.periods.destroy-risk');
    
    // Other Allowances (Lain-lain) PKWT
    Route::post('/periods/{id}/other-allowance', [App\Http\Controllers\PkwtPayrollController::class, 'storeOtherAllowance'])->name('payroll.pkwt.periods.store-other-allowance');
    Route::delete('/periods/{id}/other-allowance/{allowanceId}', [App\Http\Controllers\PkwtPayrollController::class, 'destroyOtherAllowance'])->name('payroll.pkwt.periods.destroy-other-allowance');
    
    Route::delete('/periods/{id}', [App\Http\Controllers\PkwtPayrollController::class, 'destroy'])->name('payroll.pkwt.periods.destroy');
});

// Laporan
Route::prefix('reports')->group(function () {
    Route::get('/monthly', function () {
        return view('pages.reports.monthly', ['title' => 'Rekap Bulanan']);
    })->name('reports.monthly');
    
    Route::get('/employee', function () {
        return view('pages.reports.employee', ['title' => 'Laporan Individu']);
    })->name('reports.employee');
    
    Route::get('/summary', function () {
        return view('pages.reports.summary', ['title' => 'Rekap PHL & PKWT']);
    })->name('reports.summary');
});

// Pengaturan
Route::prefix('settings')->group(function () {
    Route::get('/roles', function () {
        return view('pages.settings.roles', ['title' => 'User & Role']);
    })->name('settings.roles');
    
    Route::get('/smtp', function () {
        return view('pages.settings.smtp', ['title' => 'Konfigurasi SMTP']);
    })->name('settings.smtp');
});

// Authentication (Existing)
Route::get('/signin', function () {
    return view('pages.auth.signin', ['title' => 'Sign In']);
})->name('signin');

// Other basic pages
Route::get('/profile', function () {
    return view('pages.profile', ['title' => 'Profile']);
})->name('profile');

Route::get('/error-404', function () {
    return view('pages.errors.error-404', ['title' => 'Error 404']);
})->name('error-404');






















