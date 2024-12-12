<?php

use App\Http\Controllers\Api\EventsController;
use App\Http\Controllers\Api\LocationsController;
use App\Http\Controllers\Api\MembershipsController;
use App\Http\Controllers\Api\PhonesController;
use App\Http\Controllers\Api\UsersController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

Route::get('/authorize/{provider}/redirect',[\App\Http\Controllers\Api\SocialAuthController::class, 'redirectToProvider']);
Route::get('/authorize/{provider}/callback',[\App\Http\Controllers\Api\SocialAuthController::class, 'handleProviderCallback']);

Route::post('/logout', [UsersController::class, 'logout']);
// Auth routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [UsersController::class, 'user']);
    //Route::post('/logout', [UsersController::class, 'logout']);
});

// Public routes
Route::get('/get-permissions', fn () => response()->json(Auth::check() ? Auth::user()->jsPermissions() : 0));

// Events routes
Route::prefix('events')->group(function () {
    Route::get('/', [EventsController::class, 'index']);
    Route::get('/short', [EventsController::class, 'shorts']);
    Route::get('/images', [EventsController::class, 'getEventImages']);
    Route::get('/{event}', [EventsController::class, 'show']);
    Route::post('/{event}/register', [EventsController::class, 'register']);
});

// Locations routes
Route::apiResource('locations', LocationsController::class)->only(['index', 'show']);

// Users routes || not logged in
Route::prefix('users')->group(function () {
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/{id}/qr', [UsersController::class, 'qr']);
    });
    Route::get('/{id}/qr', [UsersController::class, 'qr']);
    Route::post('/login', [UsersController::class, 'login']);
    Route::post('/check', [UsersController::class, 'check']);
    Route::post('/validate_phone', [UsersController::class, 'validatePhone']);
    Route::post('/validate_email', [UsersController::class, 'validateEmail']);
    Route::post('/findByPhone', [UsersController::class, 'findByPhone']);
    Route::get('/{user}', [UsersController::class, 'show']);
    Route::post('/', [UsersController::class, 'store']);
});

Route::post('/phones/{phone}/setPrimary', [PhonesController::class, 'setPrimary']);

// Memberships routes
Route::prefix('memberships')->group(function () {
    Route::post('/pay', [MembershipsController::class, 'pay']);
    Route::apiResource('/', MembershipsController::class);
});

// Admin routes
Route::prefix('admin')->name('admin.')->middleware('auth:sanctum')->group(function () {
    Route::apiResource('rules', \App\Http\Controllers\Api\Admin\RulesController::class);
    Route::post('upload/{type}', [\App\Http\Controllers\Api\Admin\UploadController::class, 'upload']);
    Route::apiResource('events', \App\Http\Controllers\Api\Admin\EventsController::class);
    Route::apiResource('locations', \App\Http\Controllers\Api\Admin\LocationsController::class);
    Route::apiResource('users', \App\Http\Controllers\Api\Admin\UsersController::class);
    Route::apiResource('postals', \App\Http\Controllers\Api\Admin\PostalsController::class);
    Route::apiResource('memberships', \App\Http\Controllers\Api\Admin\MembershipsController::class);
});

