<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('index');
});
Route::get('/register',[AuthC::class,'register'])->name('register');
Route::post('/register',[AuthC::class,'store']);

Route::get('/login',[AuthC::class,'login'])->name('login');
Route::post('/login',[AuthC::class,'auth']);

Route::post('logout',[AuthC::class,'logout'])->name('logout');
