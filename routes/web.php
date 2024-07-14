<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthC;
Route::get('/', function () {
    return view('index');
});

Route::post('/register',[AuthC::class,'store']);

Route::post('/login',[AuthC::class,'auth']);

Route::post("/otp",[AuthC::class, 'sentOTP']);

Route::post('logout',[AuthC::class,'logout'])->name('logout');

Route::post('/verify',[AuthC::class,'verifyOtp']);

Route::get('/pull',[AuthC::class,'pull']);

Route::get('/{any}', function () {
    return view('index');
})->where('any', '.*');
