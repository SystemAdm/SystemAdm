<?php

use App\Http\Controllers\SocialAuthController;
use Illuminate\Support\Facades\Route;

Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'callback']);

Route::get('{any}', function () {
    return view('welcome');
})->where('any', '.*');
