<?php

use Illuminate\Support\Facades\Route;
use JobMetric\Panelio\Facades\Middleware;
use JobMetric\Setting\Http\Controllers\SettingController;

/*
|--------------------------------------------------------------------------
| Laravel Setting Routes
|--------------------------------------------------------------------------
|
| All Route in Laravel Setting package
|
*/

// setting
Route::prefix('p/{panel}/{section}/setting')->name('setting.')->namespace('JobMetric\Setting\Http\Controllers')->group(function () {
    Route::middleware(Middleware::getMiddlewares())->group(function () {
        Route::resource('{type}', SettingController::class)->except([
            'create',
            'show',
            'edit',
            'update',
            'destroy'
        ]);
    });
});
