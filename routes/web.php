<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('auth')->group(function () {
    Route::get('/{provider}/redirect', [AuthController::class, 'redirect']);
    Route::get('/{provider}/callback', [AuthController::class, 'callback']);
});
