<?php

use Illuminate\Support\Facades\Route;


/*Route::name('authorize.')->prefix('/authorize')->group(function () {
    Route::get('{provider}/callback', [App\Http\Controllers\Api\SocialAuthController::class,'callback'])->name('callback');
});*/
Route::get('{any}', function () {
    //Auth::attempt(['id'=>8,'password'=>'Tester']);
    return view('welcome');
})->where('any', '.*');
