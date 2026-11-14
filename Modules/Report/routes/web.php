<?php

use Illuminate\Support\Facades\Route;
use Modules\Report\Http\Controllers\ReportController;

Route::middleware(['auth'])->prefix('reports')->name('reports.')->group(function () {
    // Web page
    Route::get('/', [ReportController::class, 'index'])->name('index');

    // AJAX API endpoints
    Route::get('/data', [ReportController::class, 'getData'])->name('data');
    Route::post('/', [ReportController::class, 'store'])->name('store');
    Route::get('/{id}', [ReportController::class, 'show'])->name('show');
    Route::post('/{id}', [ReportController::class, 'update'])->name('update'); // Using POST for multipart/form-data support with file upload
    Route::delete('/{id}', [ReportController::class, 'destroy'])->name('destroy');

    // Admin Verification endpoint (AJAX)
    Route::patch('/{id}/verify', [ReportController::class, 'verifyStatus'])
        ->middleware('role:Admin')
        ->name('verify');
});
