<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterClinicController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Dental Clinic Management SaaS
|--------------------------------------------------------------------------
*/

// Public landing & Guest Authentication
Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);

    Route::get('/register-clinic', [RegisterClinicController::class, 'showRegistrationForm'])->name('register.clinic');
    Route::post('/register-clinic', [RegisterClinicController::class, 'register']);
});

// Authenticated Routes (Multi-Tenant Scoped)
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Role-protected route groups for permissions & testing
    Route::middleware('role:clinic_admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/settings', function () {
            return response()->json(['status' => 'success', 'message' => 'Clinic Admin Settings Section']);
        })->name('settings');
    });

    Route::middleware('role:clinic_admin,dentist')->prefix('clinical')->name('clinical.')->group(function () {
        Route::get('/confidential-notes', function () {
            return response()->json(['status' => 'success', 'message' => 'Dentist & Clinic Admin Clinical Notes']);
        })->name('notes');
    });

    Route::middleware('role:platform_owner')->prefix('platform')->name('platform.')->group(function () {
        Route::get('/all-clinics', function () {
            return response()->json(['status' => 'success', 'message' => 'Platform Super Owner Area']);
        })->name('clinics');
    });
});
