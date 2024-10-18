<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::get('events/short',[\App\Http\Controllers\Api\EventsController::class,'shorts']);
Route::apiResource('events',\App\Http\Controllers\Api\EventsController::class)->only(['index','show']);
Route::prefix('admin')->name('admin.')->group(function(){
    Route::apiResource('events',\App\Http\Controllers\Api\Admin\EventsController::class)->except(['create','edit']);
});

