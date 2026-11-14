<?php

use Illuminate\Support\Facades\Route;
use Modules\Map\Http\Controllers\MapController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('maps', MapController::class)->names('map');
});
