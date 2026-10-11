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

// Public SaaS Marketing Landing Page
Route::get('/', function () {
    return view('landing');
})->name('home');

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
    Route::post('/appointments/{appointment}/send-reminder', [\App\Http\Controllers\AppointmentController::class, 'sendReminder'])->name('appointments.send-reminder');

    // Patient Directory & Clinical Profiles
    Route::resource('patients', \App\Http\Controllers\PatientController::class);
    Route::post('/patients/{patient}/documents', [\App\Http\Controllers\PatientDocumentController::class, 'store'])->name('patients.documents.store');
    Route::get('/patients/{patient}/documents/{document}/view', [\App\Http\Controllers\PatientDocumentController::class, 'view'])->name('patients.documents.view');
    Route::get('/patients/{patient}/documents/{document}/download', [\App\Http\Controllers\PatientDocumentController::class, 'download'])->name('patients.documents.download');
    Route::delete('/patients/{patient}/documents/{document}', [\App\Http\Controllers\PatientDocumentController::class, 'destroy'])->name('patients.documents.destroy');

    // Clinical Treatment Records & Dental Charting
    Route::resource('treatments', \App\Http\Controllers\TreatmentRecordController::class);

    // Invoicing, Billing Engine & Payment Receipts
    Route::resource('invoices', \App\Http\Controllers\InvoiceController::class);
    Route::post('/invoices/{invoice}/payments', [\App\Http\Controllers\PaymentController::class, 'store'])->name('invoices.payments.store');
    Route::get('/payments/{payment}/receipt', [\App\Http\Controllers\PaymentController::class, 'receipt'])->name('payments.receipt');

    // Patient Follow-Ups & Clinical Recall Queue
    Route::resource('follow-ups', \App\Http\Controllers\FollowUpController::class);
    Route::patch('/follow-ups/{followUp}/status', [\App\Http\Controllers\FollowUpController::class, 'updateStatus'])->name('follow-ups.status');

    // Doctor Schedules, Working Shifts & Chair Roster
    Route::resource('doctors', \App\Http\Controllers\DoctorScheduleController::class);

    // Dental Services Catalog, Procedure Categories & Pricing Engine
    Route::resource('services', \App\Http\Controllers\DentalServiceController::class);
    Route::patch('/services/{service}/toggle', [\App\Http\Controllers\DentalServiceController::class, 'toggleStatus'])->name('services.toggle');

    // Financial Analytics, Revenue Reports & Performance Telemetry
    Route::get('/reports', [\App\Http\Controllers\ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/print', [\App\Http\Controllers\ReportController::class, 'printReport'])->name('reports.print');

    // Clinic Administration, Staff Roster & Workspace Settings
    Route::middleware('role:clinic_admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/settings', [\App\Http\Controllers\Admin\ClinicSettingController::class, 'edit'])->name('settings');
        Route::put('/settings', [\App\Http\Controllers\Admin\ClinicSettingController::class, 'update'])->name('settings.update');

        Route::resource('staff', \App\Http\Controllers\Admin\ClinicStaffController::class);
        Route::patch('/staff/{staff}/toggle', [\App\Http\Controllers\Admin\ClinicStaffController::class, 'toggleStatus'])->name('staff.toggle');

        Route::get('/subscription', [\App\Http\Controllers\Admin\SubscriptionController::class, 'index'])->name('subscription');
        Route::post('/subscription/change-plan', [\App\Http\Controllers\Admin\SubscriptionController::class, 'changePlan'])->name('subscription.change-plan');
        Route::post('/subscription/renew', [\App\Http\Controllers\Admin\SubscriptionController::class, 'renew'])->name('subscription.renew');
        Route::post('/subscription/checkout', [\App\Http\Controllers\Admin\SubscriptionController::class, 'checkout'])->name('subscription.checkout');
    });

    Route::middleware('role:clinic_admin,dentist')->prefix('clinical')->name('clinical.')->group(function () {
        Route::get('/confidential-notes', function () {
            return response()->json(['status' => 'success', 'message' => 'Dentist & Clinic Admin Clinical Notes']);
        })->name('notes');
    });

    // Platform Super Owner Area
    Route::middleware('role:platform_owner')->prefix('platform')->name('platform.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Platform\PlatformClinicController::class, 'index'])->name('index');
        Route::get('/clinics', [\App\Http\Controllers\Platform\PlatformClinicController::class, 'index'])->name('clinics.index');
        Route::get('/clinics/{clinic}', [\App\Http\Controllers\Platform\PlatformClinicController::class, 'show'])->name('clinics.show');
        Route::patch('/clinics/{clinic}/status', [\App\Http\Controllers\Platform\PlatformClinicController::class, 'updateStatus'])->name('clinics.status');
        Route::get('/telemetry', [\App\Http\Controllers\Platform\PlatformClinicController::class, 'telemetry'])->name('telemetry');
        Route::get('/subscriptions', [\App\Http\Controllers\Platform\PlatformSubscriptionController::class, 'index'])->name('subscriptions.index');
        Route::patch('/subscriptions/{clinic}', [\App\Http\Controllers\Platform\PlatformSubscriptionController::class, 'updateSubscription'])->name('subscriptions.update');
        Route::post('/subscriptions/{clinic}/record-payment', [\App\Http\Controllers\Platform\PlatformSubscriptionController::class, 'recordPayment'])->name('subscriptions.record-payment');
        Route::get('/all-clinics', function () {
            return redirect()->route('platform.clinics.index');
        })->name('clinics');
    });
});
