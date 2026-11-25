<?php

use App\Infrastructure\Http\Controllers\GifController;
use App\Infrastructure\Http\Controllers\LoginController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [LoginController::class, 'login']);

Route::get('/gifs', [GifController::class, 'search'])->middleware('auth:api');
