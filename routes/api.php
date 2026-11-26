<?php

use App\Infrastructure\Http\Controllers\FavoriteController;
use App\Infrastructure\Http\Controllers\GifController;
use App\Infrastructure\Http\Controllers\LoginController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [LoginController::class, 'login']);

Route::get('/gifs', [GifController::class, 'search'])->middleware('auth:api');
Route::get('/gifs/{id}', [GifController::class, 'show'])->middleware('auth:api');
Route::post('/favorites', [FavoriteController::class, 'store'])->middleware('auth:api');
