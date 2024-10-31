<?php

use App\Http\Controllers\Api\EventsController;
use App\Http\Controllers\Api\LocationsController;
use App\Http\Controllers\Api\UsersController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::get('events/short', [EventsController::class, 'shorts']);
Route::get('events/images', [EventsController::class, 'getEventImages']);
Route::post('events/{event}/register', [EventsController::class, 'register']);
Route::apiResource('events', EventsController::class)->only(['index', 'show']);
Route::apiResource('locations', LocationsController::class)->only(['index', 'show']);

Route::post('users/validate_phone', [UsersController::class, 'validatePhone']);
Route::post('users/validate_email', [UsersController::class, 'validateEmail']);
Route::post('users/findByPhone', [UsersController::class, 'findByPhone']);
Route::apiResource('users', UsersController::class)->only(['store', 'show']);

Route::prefix('admin')->name('admin.')->group(function () {
    Route::post('upload/{type}',[\App\Http\Controllers\Api\Admin\UploadController::class,'upload']);
    Route::apiResource('events', \App\Http\Controllers\Api\Admin\EventsController::class)->except(['create', 'edit']);
});

