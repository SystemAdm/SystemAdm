<?php

use App\Http\Controllers\Api\EventsController;
use App\Http\Controllers\Api\LocationsController;
use App\Http\Controllers\Api\MembershipsController;
use App\Http\Controllers\Api\UsersController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::post('/logout', function (Request $request) {
    if ($request->user()) {
        // Attempt to delete the current access token
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Successfully logged out']);
    } else {
        // If no user is found, return an error response for debugging
        return response()->json(['message' => 'User not authenticated or token missing'], 401);
    }
})->middleware('auth:sanctum');
Route::get('/get-permissions', function () {
    return response()->json(auth()->check() ? auth()->user()->jsPermissions() : 0);
});
Route::get('events/short', [EventsController::class, 'shorts']);
Route::get('events/images', [EventsController::class, 'getEventImages']);
Route::post('events/{event}/register', [EventsController::class, 'register']);
Route::apiResource('events', EventsController::class)->only(['index', 'show']);
Route::apiResource('locations', LocationsController::class)->only(['index', 'show']);

Route::get('users/me', [UsersController::class, 'me']);
Route::post('users/qr', [UsersController::class, 'qr']);
Route::apiResource('users', UsersController::class)->only(['index', 'show']);

Route::post('users/validate_phone', [UsersController::class, 'validatePhone']);
Route::post('users/validate_email', [UsersController::class, 'validateEmail']);
Route::post('users/findByPhone', [UsersController::class, 'findByPhone']);
Route::apiResource('users', UsersController::class)->only(['store', 'show']);
Route::post('memberships/pay', [MembershipsController::class, 'pay']);
Route::apiResource('memberships', MembershipsController::class);

Route::prefix('admin')->name('admin.')->group(function () {

    Route::post('upload/{type}', [\App\Http\Controllers\Api\Admin\UploadController::class, 'upload']);
    Route::apiResource('events', \App\Http\Controllers\Api\Admin\EventsController::class);
    Route::apiResource('locations', \App\Http\Controllers\Api\Admin\LocationsController::class);
    Route::apiResource('users', \App\Http\Controllers\Api\Admin\UsersController::class);
    Route::apiResource('postals', \App\Http\Controllers\Api\Admin\PostalsController::class);
    Route::apiResource('locations', \App\Http\Controllers\Api\Admin\LocationsController::class);
    Route::apiResource('memberships', \App\Http\Controllers\Api\Admin\MembershipsController::class);
});

