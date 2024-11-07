<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('{any}', function () {
    //Auth::attempt(['id'=>8,'password'=>'Tester']);
    return view('welcome');
})->where('any', '.*');
