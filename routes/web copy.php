<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GoogleController;
use App\Http\Controllers\admin\AdminController;
use App\Http\Controllers\student\StudentController;
use App\Http\Controllers\registrar\RegistrarController;

Auth::routes();

Route::get('/', function () {
    return view('auth.login');
});

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard', [AdminController::class, 'admin_index']);
    Route::get('/records-data', function () {
        return view('admin.records.records_data');
    });
    Route::get('/records', function () {
        return view('admin.records.year_select');
    });
    Route::get('/section', function () {
        return view('admin.records.section');
    });

    Route::get('/create-registrar-account', function () {
        return view('admin.accounts.create_account');
    });

    Route::get('/reports', function () {
        return view('admin.reports.reports_index');
    });

    Route::get('/add-forms', function () {
        return view('admin.forms.create_forms');
    });

    // REGISTRAR ROUTES
    Route::get('/registrar-accounts', [AdminController::class, 'show_registrar_accounts'])->name('registrar.accounts');
    Route::post('/create-reg-acc', [AdminController::class, 'add_registrar'])->name('create.registrar');
    // Route to show the edit form
    Route::get('/registrar-accounts/{id}/edit', [AdminController::class, 'edit_registrar'])->name('edit.registrar');
    // Route to update the registrar
    Route::put('/registrar-accounts/{id}', [AdminController::class, 'update_registrar'])->name('update.registrar');

    Route::delete('/registrar-accounts/{id}', [AdminController::class, 'delete_registrar'])->name('delete.registrar');
});

Route::middleware(['auth', 'student'])->group(function () {
    Route::get('/student-home', [StudentController::class, 'student_index']);
    Route::get('/student-requests', [StudentController::class, 'student_request_index'])->name('student.student_request');
    Route::post('/student-requests-add', [StudentController::class, 'student_request_add'])->name('student.request');
    Route::get('/student-logs', [StudentController::class, 'show_request_logs'])->name('student.logs');
    Route::get('/student/request-logs', [StudentController::class, 'show_request_logs'])->name('student.request_logs');
    Route::get('/student/check-pending-requests', [StudentController::class, 'checkPendingRequestsStudent'])->name('checkPendingRequestsStudent');
});

Route::middleware(['auth', 'registrar'])->group(function () {
    Route::get('/registrar-home', [RegistrarController::class, 'registrar_index']);
    Route::get('/registrar-year', [RegistrarController::class, 'year_select_index']);
    Route::get('/registrar-section/{year}/{section}', [RegistrarController::class, 'records_data_index'])->name('registrar-section');
    Route::get('/section-select/{year}', [RegistrarController::class, 'section_select_index'])->name('section-select');
    
   
    Route::get('/registrar-logs', [RegistrarController::class, 'show_request_logs'])->name('registrar.logs');
    Route::post('/update-status', [RegistrarController::class, 'updateStatus'])->name('updateStatus');
    Route::get('/fetch-request-logs', [RegistrarController::class, 'fetchRequestLogs'])->name('fetchRequestLogs');
    Route::get('/registrar/request-logs', [RegistrarController::class, 'show_request_logs'])->name('registrar.request_logs');
    Route::get('/registrar/check-pending-requests', [RegistrarController::class, 'checkPendingRequestsRegistrar'])->name('checkPendingRequestsRegistrar');

    // enroll student routes:
    Route::get('/enroll-student', [RegistrarController::class, 'enroll_student'])->name('enroll_student');
    Route::post('/add-student-data', [RegistrarController::class, 'add_student_data'])->name('enroll.student');

});


// GOOGLE LOGIN
Route::get('/auth/google', [GoogleController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [GoogleController::class, 'handleGoogleCallback'])->name('auth.google.callback');
