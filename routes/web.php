<?php

use App\Http\Controllers\ProfileController;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Modules\Map\Http\Controllers\MapController;
use Modules\Report\Models\Report;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Sebelum login: Hanya ada halaman Peta (/ dan /map) dan fitur Autentikasi (/login, /register).
|
*/

Route::get('/', [MapController::class, 'index'])->name('home');

Route::get('/dashboard', function () {
    $totalUsers = User::count();
    $totalReports = Schema::hasTable('reports') ? Report::count() : 0;
    $verifiedReports = Schema::hasTable('reports') ? Report::where('verification_status', 'verified')->count() : 0;
    $pendingReports = Schema::hasTable('reports') ? Report::where('verification_status', 'pending')->count() : 0;
    $myReportsCount = Schema::hasTable('reports') ? Report::where('user_id', auth()->id())->count() : 0;

    return view('dashboard', compact('totalUsers', 'totalReports', 'verifiedReports', 'pendingReports', 'myReportsCount'));
})->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
