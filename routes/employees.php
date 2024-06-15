<?php

use App\Http\Controllers\API\Auth\EmailVerificationController;
use App\Http\Controllers\API\Auth\LoginController;
use App\Http\Controllers\API\Auth\RegisterController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
 */

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//Authentication Cycle For Users Or Clients
Route::middleware(['auth:sanctum', 'type.user'])->group(function () {
    Route::post('user/register', [RegisterController::class, 'CreateUser']);
    Route::post('send-code', [EmailVerificationController::class, 'SendCode']);
    Route::post('check-code', [EmailVerificationController::class, 'CheckCode']);
    Route::post('/user/login', [LoginController::class, 'Login']);
    Route::get('user/logout', [LoginController::class, 'Logout'])->middleware('auth:sanctum');
    Route::get('user/logout/all', [LoginController::class, 'LogoutFromAll'])->middleware('auth:sanctum');
// reset password
// change  password
});