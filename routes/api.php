<?php

use App\Http\Controllers\Api\EventsController;
use App\Http\Controllers\Api\LocationsController;
use App\Http\Controllers\Api\MembershipsController;
use App\Http\Controllers\Api\PhonesController;
use App\Http\Controllers\Api\UsersController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::post('/logout', [UsersController::class, 'logout']);
ROute::post('/login',[UsersController::class, 'login']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return response()->json([
        'roles' => $request->user()->roles->map(function ($role) {
            return [
                'id' => $role->id,
                'name' => $role->name,
            ];
        }),
        'permissions' => $request->user()->getAllPermissions()->map(function ($permission) {
            return [
                'id' => $permission->id,
                'name' => $permission->name,
            ];
        }),
    ]);
});


Route::post('/send-code', [\App\Http\Controllers\SocialAuthController::class, 'generateVerificationCode']);
Route::post('/verify-code', [\App\Http\Controllers\SocialAuthController::class, 'verifyCode']);;

// Public routes
Route::get('/get-permissions', fn() => response()->json(Auth::check() ? Auth::user()->jsPermissions() : 0));
Route::get('/rules/{location}',[\App\Http\Controllers\Api\RulesController::class, 'location']);
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
    Route::post('reset_password', [UsersController::class, 'resetPassword']);
    Route::get('/{id}/qr', [UsersController::class, 'qr']);
    Route::post('/login', [UsersController::class, 'login']);
    Route::post('/check', [UsersController::class, 'check']);
    Route::post('/validate_input', [UsersController::class, 'validateInput']);
//    Route::post('/validate_phone', [UsersController::class, 'validatePhone']);
//    Route::post('/validate_email', [UsersController::class, 'validateEmail']);
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
Route::prefix('admin')->name('admin.')->group(function () {
    Route::apiResource('rules', \App\Http\Controllers\Api\Admin\RulesController::class);
    Route::post('upload/{type}', [\App\Http\Controllers\Api\Admin\UploadController::class, 'upload']);
    Route::post('events/{event}/recover', [\App\Http\Controllers\Api\Admin\EventsController::class, 'recover']);
    Route::post('events/{event}/cancel', [\App\Http\Controllers\Api\Admin\EventsController::class, 'cancel']);
    Route::post('events/{event}/uncancel', [\App\Http\Controllers\Api\Admin\EventsController::class, 'unCancel']);
    Route::post('events/{event}/permanent', [\App\Http\Controllers\Api\Admin\EventsController::class, 'permanent']);
    Route::post('events/{event}/attend',[App\Http\Controllers\Api\Admin\EventsController::class, 'attend']);
    Route::post('events/{event}/unattend',[App\Http\Controllers\Api\Admin\EventsController::class, 'unattend']);
    Route::post('events/{event}/inside',[App\Http\Controllers\Api\Admin\EventsController::class, 'inside']);
    Route::post('events/{event}/leave',[App\Http\Controllers\Api\Admin\EventsController::class, 'leave']);
    Route::post('events/{event}/trash',[App\Http\Controllers\Api\Admin\EventsController::class, 'trash']);
    Route::post('events/{event}/start_over',[App\Http\Controllers\Api\Admin\EventsController::class, 'startOver']);
    Route::post('events/{event}/signup_begin_now',[App\Http\Controllers\Api\Admin\EventsController::class, 'signupBeginNow']);
    Route::post('events/{event}/signup_end_now',[App\Http\Controllers\Api\Admin\EventsController::class, 'signupEndNow']);
    Route::post('events/{event}/event_begin_now',[App\Http\Controllers\Api\Admin\EventsController::class, 'eventBeginNow']);
    Route::post('events/{event}/event_end_now',[App\Http\Controllers\Api\Admin\EventsController::class, 'eventEndNow']);
    Route::apiResource('events', \App\Http\Controllers\Api\Admin\EventsController::class);
    Route::apiResource('locations', \App\Http\Controllers\Api\Admin\LocationsController::class);
    Route::apiResource('users', \App\Http\Controllers\Api\Admin\UsersController::class);
    Route::apiResource('postals', \App\Http\Controllers\Api\Admin\PostalsController::class);
    Route::apiResource('memberships', \App\Http\Controllers\Api\Admin\MembershipsController::class);
});

