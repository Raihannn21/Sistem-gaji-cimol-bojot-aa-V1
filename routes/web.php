<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;

// Dashboard
Route::get('/', function () {
    return view('pages.dashboard.ecommerce', ['title' => 'E-commerce Dashboard']);
})->name('dashboard');

Route::get('/dashboard/payroll', function () {
    return view('pages.dashboard.payroll', ['title' => 'Payroll Analytics']);
})->name('dashboard.payroll');

// Manajemen Karyawan
Route::prefix('employees')->group(function () {
    Route::get('/', function () {
        return view('pages.employees.index', ['title' => 'Data Karyawan']);
    })->name('employees.index');
    
    Route::get('/status', function () {
        return view('pages.employees.status', ['title' => 'Resign & SPHK']);
    })->name('employees.status');
    
    Route::get('/import-export', function () {
        return view('pages.employees.import-export', ['title' => 'Import & Export']);
    })->name('employees.import-export');
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
    Route::get('/components', function () {
        return view('pages.payroll.pkwt.components', ['title' => 'Tunjangan & Potongan PKWT']);
    })->name('payroll.pkwt.components');
    
    Route::get('/overtime', function () {
        return view('pages.payroll.pkwt.overtime', ['title' => 'Lembur & Risiko PKWT']);
    })->name('payroll.pkwt.overtime');
    
    Route::get('/generate', function () {
        return view('pages.payroll.pkwt.generate', ['title' => 'Generate Payroll PKWT']);
    })->name('payroll.pkwt.generate');
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

Route::get('/signup', function () {
    return view('pages.auth.signup', ['title' => 'Sign Up']);
})->name('signup');

// Other basic pages
Route::get('/profile', function () {
    return view('pages.profile', ['title' => 'Profile']);
})->name('profile');

Route::get('/error-404', function () {
    return view('pages.errors.error-404', ['title' => 'Error 404']);
})->name('error-404');






















