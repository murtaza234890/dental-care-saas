<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterClinicController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PublicClinicController;
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

// Public Tenant Clinic Websites & Booking Portals
Route::prefix('clinic/{slug}')->name('clinic.')->group(function () {
    Route::get('/', [PublicClinicController::class, 'show'])->name('show');
    Route::get('/available-slots', [PublicClinicController::class, 'getAvailableSlots'])->name('slots');
    Route::post('/book', [PublicClinicController::class, 'storeBooking'])->name('book.store');
    Route::get('/confirmation/{appointment}', [PublicClinicController::class, 'confirmation'])->name('confirmation');
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

    // Appointments Management & Schedule
    Route::get('/appointments', [\App\Http\Controllers\AppointmentController::class, 'index'])->name('appointments.index');
    Route::get('/appointments/create', [\App\Http\Controllers\AppointmentController::class, 'create'])->name('appointments.create');
    Route::post('/appointments', [\App\Http\Controllers\AppointmentController::class, 'store'])->name('appointments.store');
    Route::patch('/appointments/{appointment}/status', [\App\Http\Controllers\AppointmentController::class, 'updateStatus'])->name('appointments.status');
    Route::post('/appointments/{appointment}/reschedule', [\App\Http\Controllers\AppointmentController::class, 'reschedule'])->name('appointments.reschedule');

    // Patient Directory & Clinical Profiles
    Route::resource('patients', \App\Http\Controllers\PatientController::class);

    // Clinical Treatment Records & Dental Charting
    Route::resource('treatments', \App\Http\Controllers\TreatmentRecordController::class);

    // Invoicing, Billing Engine & Payment Receipts
    Route::resource('invoices', \App\Http\Controllers\InvoiceController::class);
    Route::post('/invoices/{invoice}/payments', [\App\Http\Controllers\PaymentController::class, 'store'])->name('invoices.payments.store');
    Route::get('/payments/{payment}/receipt', [\App\Http\Controllers\PaymentController::class, 'receipt'])->name('payments.receipt');

    // Patient Follow-Ups & Clinical Recall Queue
    Route::resource('follow-ups', \App\Http\Controllers\FollowUpController::class);
    Route::patch('/follow-ups/{followUp}/status', [\App\Http\Controllers\FollowUpController::class, 'updateStatus'])->name('follow-ups.status');

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
