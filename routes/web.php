<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\Report\MonthlyReportController;
use App\Http\Controllers\Report\EmployeeReportController;
use App\Http\Controllers\Report\SummaryReportController;
use App\Http\Controllers\EmployeeStatusController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\Payroll\Phl as PhlPayroll;
use App\Http\Controllers\Payroll\Pkwt as PkwtPayroll;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Settings\SmtpSettingsController;

// Guest Authentication Routes
Route::middleware(['guest'])->group(function () {
    Route::get('/signin', [AuthController::class, 'showLogin'])->name('signin');
    Route::post('/signin', [AuthController::class, 'login']);
});

// Authenticated Routes (Protected by standard Auth Middleware)
Route::middleware(['auth'])->group(function () {
    // Logout Action
    Route::post('/signout', [AuthController::class, 'logout'])->name('signout');

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Manajemen Karyawan
    Route::prefix('employees')->group(function () {
        Route::get('/', [EmployeeController::class, 'index'])->name('employees.index');
        Route::post('/', [EmployeeController::class, 'store'])->name('employees.store');
        Route::post('/import', [EmployeeController::class, 'import'])->name('employees.import');
        Route::put('/{employee}', [EmployeeController::class, 'update'])->name('employees.update');
        Route::delete('/{employee}', [EmployeeController::class, 'destroy'])->name('employees.destroy');
        
        Route::get('/status', [EmployeeStatusController::class, 'index'])->name('employees.status');
        Route::post('/status', [EmployeeStatusController::class, 'store'])->name('employees.status.store');
        Route::delete('/status/{id}', [EmployeeStatusController::class, 'destroy'])->name('employees.status.destroy');

        Route::get('/teams', [TeamController::class, 'index'])->name('employees.teams');
        Route::post('/teams', [TeamController::class, 'store'])->name('employees.teams.store');
        Route::put('/teams/{team}', [TeamController::class, 'update'])->name('employees.teams.update');
        Route::delete('/teams/{team}', [TeamController::class, 'destroy'])->name('employees.teams.destroy');
    });

    // Payroll PHL
    Route::prefix('payroll/phl')->group(function () {
        Route::get('/periods', [PhlPayroll\PeriodController::class, 'index'])->name('payroll.phl.periods');
        Route::post('/periods', [PhlPayroll\PeriodController::class, 'store'])->name('payroll.phl.periods.store');
        Route::get('/periods/{id}', [PhlPayroll\PeriodController::class, 'show'])->name('payroll.phl.periods.show');
        Route::get('/periods/{id}/export/pdf', [PhlPayroll\ExportController::class, 'exportPdf'])->name('payroll.phl.periods.export.pdf');
        Route::get('/periods/{id}/slips/{employeeId}/pdf', [PhlPayroll\ExportController::class, 'exportIndividualPdf'])->name('payroll.phl.periods.slip.pdf');
        Route::post('/periods/{id}/slips/{employeeId}/send', [PhlPayroll\ExportController::class, 'sendIndividualSlip'])->name('payroll.phl.periods.slip.send');
        Route::post('/periods/{id}/slips/send-all', [PhlPayroll\ExportController::class, 'sendAllSlips'])->name('payroll.phl.periods.slip.send-all');
        Route::get('/periods/{id}/export/excel', [PhlPayroll\ExportController::class, 'exportExcel'])->name('payroll.phl.periods.export.excel');
        Route::get('/periods/{id}/export/bca', [PhlPayroll\ExportController::class, 'exportBca'])->name('payroll.phl.periods.export.bca');
        Route::post('/periods/{id}/import-attendance', [PhlPayroll\AttendanceController::class, 'importAttendance'])->name('payroll.phl.periods.import-attendance');
        Route::put('/periods/{id}/attendance/{attendanceId}', [PhlPayroll\AttendanceController::class, 'updateAttendance'])->name('payroll.phl.periods.update-attendance');
        Route::delete('/periods/{id}/attendance/{attendanceId}', [PhlPayroll\AttendanceController::class, 'destroyAttendance'])->name('payroll.phl.periods.destroy-attendance');
        Route::post('/periods/{id}/generate', [PhlPayroll\PeriodController::class, 'generate'])->name('payroll.phl.periods.generate');
        Route::post('/periods/{id}/unlock', [PhlPayroll\PeriodController::class, 'unlock'])->name('payroll.phl.periods.unlock');
        Route::delete('/periods/{id}', [PhlPayroll\PeriodController::class, 'destroy'])->name('payroll.phl.periods.destroy');
        
        // Lembur (Overtime) PHL
        Route::post('/periods/{id}/overtime', [PhlPayroll\OvertimeController::class, 'storeOvertime'])->name('payroll.phl.periods.store-overtime');
        Route::put('/periods/{id}/overtime/{overtimeId}', [PhlPayroll\OvertimeController::class, 'updateOvertime'])->name('payroll.phl.periods.update-overtime');
        Route::delete('/periods/{id}/overtime/{overtimeId}', [PhlPayroll\OvertimeController::class, 'destroyOvertime'])->name('payroll.phl.periods.destroy-overtime');
        Route::post('/periods/{id}/import-overtime', [PhlPayroll\OvertimeController::class, 'importOvertime'])->name('payroll.phl.periods.import-overtime');

        // Tunjangan Risiko (Risk Allowance) PHL
        Route::post('/periods/{id}/risk', [PhlPayroll\AllowanceController::class, 'storeRisk'])->name('payroll.phl.periods.store-risk');
        Route::put('/periods/{id}/risk/{riskId}', [PhlPayroll\AllowanceController::class, 'updateRisk'])->name('payroll.phl.periods.update-risk');
        Route::delete('/periods/{id}/risk/{riskId}', [PhlPayroll\AllowanceController::class, 'destroyRisk'])->name('payroll.phl.periods.destroy-risk');
        Route::post('/periods/{id}/import-risk', [PhlPayroll\AllowanceController::class, 'importRisk'])->name('payroll.phl.periods.import-risk');
    });

    // Payroll PKWT
    Route::prefix('payroll/pkwt')->group(function () {
        Route::get('/periods', [PkwtPayroll\PeriodController::class, 'index'])->name('payroll.pkwt.periods');
        Route::post('/periods', [PkwtPayroll\PeriodController::class, 'store'])->name('payroll.pkwt.periods.store');
        Route::get('/periods/{id}', [PkwtPayroll\PeriodController::class, 'show'])->name('payroll.pkwt.periods.show');
        Route::get('/periods/{id}/setup', [PkwtPayroll\PeriodController::class, 'setup'])->name('payroll.pkwt.periods.setup');
        Route::post('/periods/{id}/setup', [PkwtPayroll\PeriodController::class, 'saveSetup'])->name('payroll.pkwt.periods.save-setup');
        Route::get('/periods/{id}/export/pdf', [PkwtPayroll\ExportController::class, 'exportPdf'])->name('payroll.pkwt.periods.export.pdf');
        Route::get('/periods/{id}/slips/{employeeId}/pdf', [PkwtPayroll\ExportController::class, 'exportIndividualPdf'])->name('payroll.pkwt.periods.slip.pdf');
        Route::post('/periods/{id}/slips/{employeeId}/send', [PkwtPayroll\ExportController::class, 'sendIndividualSlip'])->name('payroll.pkwt.periods.slip.send');
        Route::post('/periods/{id}/slips/send-all', [PkwtPayroll\ExportController::class, 'sendAllSlips'])->name('payroll.pkwt.periods.slip.send-all');
        Route::get('/periods/{id}/export/excel', [PkwtPayroll\ExportController::class, 'exportExcel'])->name('payroll.pkwt.periods.export.excel');
        Route::get('/periods/{id}/export/bca', [PkwtPayroll\ExportController::class, 'exportBca'])->name('payroll.pkwt.periods.export.bca');
        Route::post('/periods/{id}/generate', [PkwtPayroll\PeriodController::class, 'generate'])->name('payroll.pkwt.periods.generate');
        Route::post('/periods/{id}/unlock', [PkwtPayroll\PeriodController::class, 'unlock'])->name('payroll.pkwt.periods.unlock');
        Route::post('/periods/{id}/import-attendance', [PkwtPayroll\AttendanceController::class, 'importAttendance'])->name('payroll.pkwt.periods.import-attendance');
        Route::put('/periods/{id}/attendance/{attendanceId}', [PkwtPayroll\AttendanceController::class, 'updateAttendance'])->name('payroll.pkwt.periods.update-attendance');
        Route::delete('/periods/{id}/attendance/{attendanceId}', [PkwtPayroll\AttendanceController::class, 'destroyAttendance'])->name('payroll.pkwt.periods.destroy-attendance');
        
        // Overtime (Lembur) PKWT
        Route::post('/periods/{id}/overtime', [PkwtPayroll\OvertimeController::class, 'storeOvertime'])->name('payroll.pkwt.periods.store-overtime');
        Route::put('/periods/{id}/overtime/{overtimeId}', [PkwtPayroll\OvertimeController::class, 'updateOvertime'])->name('payroll.pkwt.periods.update-overtime');
        Route::delete('/periods/{id}/overtime/{overtimeId}', [PkwtPayroll\OvertimeController::class, 'destroyOvertime'])->name('payroll.pkwt.periods.destroy-overtime');
        Route::post('/periods/{id}/import-overtime', [PkwtPayroll\OvertimeController::class, 'importOvertime'])->name('payroll.pkwt.periods.import-overtime');
        
        // Risk Allowance (Risiko) PKWT
        Route::post('/periods/{id}/risk', [PkwtPayroll\AllowanceController::class, 'storeRisk'])->name('payroll.pkwt.periods.store-risk');
        Route::put('/periods/{id}/risk/{riskId}', [PkwtPayroll\AllowanceController::class, 'updateRisk'])->name('payroll.pkwt.periods.update-risk');
        Route::delete('/periods/{id}/risk/{riskId}', [PkwtPayroll\AllowanceController::class, 'destroyRisk'])->name('payroll.pkwt.periods.destroy-risk');
        Route::post('/periods/{id}/import-risk', [PkwtPayroll\AllowanceController::class, 'importRisk'])->name('payroll.pkwt.periods.import-risk');
        
        // Other Allowances (Lain-lain) PKWT
        Route::post('/periods/{id}/other-allowance', [PkwtPayroll\AllowanceController::class, 'storeOtherAllowance'])->name('payroll.pkwt.periods.store-other-allowance');
        Route::delete('/periods/{id}/other-allowance/{allowanceId}', [PkwtPayroll\AllowanceController::class, 'destroyOtherAllowance'])->name('payroll.pkwt.periods.destroy-other-allowance');
        
        Route::delete('/periods/{id}', [PkwtPayroll\PeriodController::class, 'destroy'])->name('payroll.pkwt.periods.destroy');
    });

    // Laporan
    Route::prefix('reports')->group(function () {
        Route::get('/monthly', [MonthlyReportController::class, 'index'])->name('reports.monthly');
        Route::get('/monthly/export-pdf', [MonthlyReportController::class, 'exportPdf'])->name('reports.monthly.export-pdf');
        Route::get('/monthly/export-excel', [MonthlyReportController::class, 'exportExcel'])->name('reports.monthly.export-excel');
        
        Route::get('/employee', [EmployeeReportController::class, 'index'])->name('reports.employee');
        Route::get('/employee/{id}/history', [EmployeeReportController::class, 'history'])->name('reports.employee.history');
        
        Route::get('/summary', [SummaryReportController::class, 'index'])->name('reports.summary');
        Route::get('/summary/export-excel', [SummaryReportController::class, 'exportExcel'])->name('reports.summary.export-excel');
        Route::get('/summary/export-pdf', [SummaryReportController::class, 'exportPdf'])->name('reports.summary.export-pdf');
    });

    // Pengaturan
    Route::prefix('settings')->group(function () {
        Route::get('/roles', [UserController::class, 'index'])->name('settings.roles');
        Route::post('/roles', [UserController::class, 'store'])->name('settings.roles.store');
        Route::put('/roles/{user}', [UserController::class, 'update'])->name('settings.roles.update');
        Route::delete('/roles/{user}', [UserController::class, 'destroy'])->name('settings.roles.destroy');
        
        // SMTP Settings
        Route::get('/smtp', [SmtpSettingsController::class, 'index'])->name('settings.smtp');
        Route::post('/smtp', [SmtpSettingsController::class, 'update'])->name('settings.smtp.update');
        Route::post('/smtp/test', [SmtpSettingsController::class, 'testConnection'])->name('settings.smtp.test');
    });

    // User Profile
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Error 404
    Route::get('/error-404', function () {
        return view('pages.errors.error-404', ['title' => 'Error 404']);
    })->name('error-404');
});
