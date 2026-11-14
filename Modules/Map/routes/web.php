<?php

use Illuminate\Support\Facades\Route;
use Modules\Map\Http\Controllers\MapController;

/*
|--------------------------------------------------------------------------
| Web Routes - Modul Map
|--------------------------------------------------------------------------
|
| Halaman peta dapat diakses oleh publik (sebelum login) maupun setelah login.
|
*/

Route::get('/map', [MapController::class, 'index'])->name('map.index');
Route::get('/maps', [MapController::class, 'index'])->name('maps.index');
