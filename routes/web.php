<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GoogleController;
use App\Http\Controllers\admin\AdminController;
use App\Http\Controllers\admin\ReportsController;
use App\Http\Controllers\student\StudentController;
use App\Http\Controllers\registrar\RegistrarController;
use App\Http\Controllers\ML\AnalyticsController;
use App\Http\Controllers\registrar\ImportController;

Auth::routes(['register' => false]);

Route::get('/', function () {
    return view('auth.login');
});

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('dashboard', [AdminController::class, 'admin_index'])->name('admin.index');

    // STUDENT RECORDS
    Route::get('/admin/student-records', [AdminController::class, 'records_data_index'])->name('records.data.admin');
    Route::get('/admin/student-records-data', [AdminController::class, 'getStudentRecords'])->name('admin/records.data.ajax');
    // Route to handle deletion of a student record and associated grades
    Route::delete('/admin/student-records/{id}', [AdminController::class, 'delete_student_record'])->name('admin/student.destroy');

    // REGISTRAR ROUTES
    Route::get('/admin/registrar-accounts', [AdminController::class, 'show_registrar_accounts'])->name('registrar.accounts');
    Route::post('/admin/create-reg-acc', [AdminController::class, 'add_registrar'])->name('create.registrar');
    // Route to show the edit form
    Route::get('/admin/registrar-accounts/{id}/edit', [AdminController::class, 'edit_registrar'])->name('edit.registrar');
    // Route to update the registrar
    Route::put('/admin/registrar-accounts/{id}', [AdminController::class, 'update_registrar'])->name('update.registrar');
    Route::put('/admin/update-account-status/{id}', [AdminController::class, 'updateAccountStatus'])->name('update.acc_status');

    // REPORTS ROUTES
    Route::get('/export-grades-csv', [ReportsController::class, 'exportCsv'])->name('export.csv');
    Route::get('/export-student-csv', [ReportsController::class, 'exportCsvStudentTotal'])->name('export.student.total.csv');
    Route::get('/admin/reports', [ReportsController::class, 'index'])->name('admin.reports.index');
    Route::get('/analyze-grades', [ReportsController::class, 'analyzeGrades'])->name('analyze.grades');
    Route::get('/get-distinct-subjects', [ReportsController::class, 'getDistinctSubjects'])->name('get.distinct.subjects');

    Route::post('/admin/update-student-data', [AdminController::class, 'update_student_record'])->name('update.student.admin');
    Route::post('/admin/store-grades', [AdminController::class, 'storeGradesAdmin'])->name('store.grades.admin');
    Route::get('/admin/view-grades/{id}', [AdminController::class, 'viewGradesAdmin'])->name('view_grades_admin');
    Route::put('/admin/update-grade/{id}', [AdminController::class, 'updateGradesAdmin'])->name('update_grades_admin');
    Route::get('/admin/edit-student/{id}', [AdminController::class, 'edit_student'])->name('edit_student');
    Route::get('/pass-fail-stats', [ReportsController::class, 'passFailStats'])->name('passFailStats');
    // DElete subject grade
    Route::delete('/admin/delete-grade/{id}', [AdminController::class, 'deleteGradesAdmin'])->name('delete_grade_admin');
});
// generate random philippines subjects and their grades of 10 students. i want you to give me a analyzation of the subjects and grades wherein you will determine what subject has the lowest grades and the highest grades to see which subjects needed more focus on teaching
Route::middleware(['auth', 'student'])->group(function () {
    Route::get('/student-home', [StudentController::class, 'student_index']);
    Route::get('/student-requests', [StudentController::class, 'student_request_index'])->name('student.student_request');
    Route::post('/student-requests-add', [StudentController::class, 'student_request_add'])->name('student.request');
    Route::get('/student-logs', [StudentController::class, 'show_request_logs'])->name('student.logs');
    Route::get('/student/request-logs', [StudentController::class, 'show_request_logs'])->name('student.request_logs');
    Route::get('/student/check-pending-requests', [StudentController::class, 'checkPendingRequestsStudent'])->name('checkPendingRequestsStudent');

    // download uploaded scanned document:
    Route::get('/student/download-document/{id}', [StudentController::class, 'downloadDocument'])->name('student.documents.download');

});

Route::middleware(['auth', 'registrar'])->group(function () {
    Route::get('/registrar-home', [RegistrarController::class, 'registrar_index'])->name('student.section.counts');;
    Route::get('/registrar-year', [RegistrarController::class, 'year_select_index']);
    Route::get('/student-records', [RegistrarController::class, 'records_data_index'])->name('records.data');
    // Route::get('/registrar-section/{year}/{section}', [RegistrarController::class, 'records_data_index'])->name('registrar-section');
    Route::get('/section-select/{year}', [RegistrarController::class, 'section_select_index'])->name('section-select');

    Route::get('/registrar-logs', [RegistrarController::class, 'show_request_logs'])->name('registrar.logs');
    Route::post('/update-status', [RegistrarController::class, 'updateStatus'])->name('updateStatus');
    Route::get('/fetch-request-logs', [RegistrarController::class, 'fetchRequestLogs'])->name('fetchRequestLogs');
    Route::get('/registrar/request-logs', [RegistrarController::class, 'show_request_logs'])->name('registrar.request_logs');
    Route::get('/registrar/check-pending-requests', [RegistrarController::class, 'checkPendingRequestsRegistrar'])->name('checkPendingRequestsRegistrar');
    Route::get('/student-records-data', [RegistrarController::class, 'getStudentRecords'])->name('records.data.ajax');

    // enroll student routes:
    Route::get('/enroll-student', [RegistrarController::class, 'enroll_student'])->name('enroll_student');
    Route::post('/add-student-data', [RegistrarController::class, 'add_student_data'])->name('enroll.student');
    Route::post('/update-student-data', [RegistrarController::class, 'update_student_record'])->name('update.student');
    Route::get('/edit-student/{id}', [RegistrarController::class, 'edit_student'])->name('edit_student');
    // store grades
    Route::post('/store-grades', [RegistrarController::class, 'storeGrades'])->name('store.grades');
    Route::get('/view-grades/{id}', [RegistrarController::class, 'viewGrades'])->name('view_grades');
    Route::put('/update-grade/{id}', [RegistrarController::class, 'updateGrade'])->name('update_grades');
    // IMPORT STUDENT RECORDS AND GRADES
    Route::get('/import-form', [ImportController::class, 'import_form']);
    Route::post('import', [ImportController::class, 'import'])->name('import');

    // Upload document
    Route::post('/upload-document', [RegistrarController::class, 'upload'])->name('document.upload');
    Route::get('/documents/download/{id}', [RegistrarController::class, 'download'])->name('documents.download');

    // DElete subject grade
    Route::delete('/delete-grade/{id}', [RegistrarController::class, 'deleteGrade'])->name('delete_grade');

    // Route to handle deletion of a student record and associated grades
    Route::delete('/student-records/{id}', [RegistrarController::class, 'delete_student_record'])->name('student.destroy');
});

// AI
Route::get('/analytics', [AnalyticsController::class, 'index']);


// GOOGLE LOGIN
Route::get('/auth/google', [GoogleController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [GoogleController::class, 'handleGoogleCallback'])->name('auth.google.callback');
