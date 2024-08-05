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
});

Route::middleware(['auth', 'registrar'])->group(function () {
    Route::get('/registrar-home', [RegistrarController::class, 'registrar_index']);
});


// GOOGLE LOGIN
Route::get('/auth/google', [GoogleController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [GoogleController::class, 'handleGoogleCallback'])->name('auth.google.callback');
