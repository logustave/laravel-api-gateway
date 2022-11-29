<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('auth')->group(function () {
    Route::post('/signin', [AuthController::class,'signin'])->name('signin.api');
    Route::post('/register', [AuthController::class,'register'])->name('register.api');
    Route::post('/refresh', [AuthController::class,'refresh'])->name('refresh.api');
    Route::post('/revoke', [AuthController::class,'revoke'])->name('revoke.api');
    Route::post('/logout', [AuthController::class,'logout'])->name('logout.api');
});
