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
    Route::put('/{employee}', [EmployeeController::class, 'update'])->name('employees.update');
    Route::delete('/{employee}', [EmployeeController::class, 'destroy'])->name('employees.destroy');
    
    Route::get('/status', function () {
        return view('pages.employees.status', ['title' => 'Resign & SPHK']);
    })->name('employees.status');
    
});

// Payroll PHL
Route::prefix('payroll/phl')->group(function () {
    Route::get('/periods', function () {
        return view('pages.payroll.phl.periods', ['title' => 'Periode Gaji PHL']);
    })->name('payroll.phl.periods');

    Route::get('/periods/{id}', function ($id) {
        return view('pages.payroll.phl.period-detail', ['title' => 'Detail Periode Gaji PHL', 'id' => $id]);
    })->name('payroll.phl.periods.show');
    
    // Consolidated into Period Detail
});

// Payroll PKWT
Route::prefix('payroll/pkwt')->group(function () {
    Route::get('/periods', function () {
        return view('pages.payroll.pkwt.periods', ['title' => 'Periode Gaji PKWT']);
    })->name('payroll.pkwt.periods');

    Route::get('/periods/{id}', function ($id) {
        return view('pages.payroll.pkwt.period-detail', ['title' => 'Detail Periode Gaji PKWT', 'id' => $id]);
    })->name('payroll.pkwt.periods.show');
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






















