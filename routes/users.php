<?php

use App\Http\Controllers\API\Auth\User\LoginController;
use App\Http\Controllers\API\Auth\User\RegisterController;
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
Route::post('user/register', [RegisterController::class, 'CreateUser']);
// Route::post('user/status', [RegisterController::class, 'status']);
Route::post('/user/login', [LoginController::class, 'Login']);
//ResetPassword
Route::post('user/reset/password', [LoginController::class, 'ResetPassword']);
Route::post('user/reset/password/checkcode', [LoginController::class, 'ResetPasswordCheckCode']);
Route::post('user/reset/password/change/password', [LoginController::class, 'ResetPasswordChangePassword']);

Route::middleware(['auth:sanctum', 'type.user'])->group(function () {
    // Route::post('send-code', [EmailVerificationController::class, 'SendCode']);
    Route::post('check-code', [RegisterController::class, 'CheckCode']);
    Route::get('user/logout', [LoginController::class, 'Logout'])->middleware('auth:sanctum');
    Route::get('user/logout/all', [LoginController::class, 'LogoutFromAll'])->middleware('auth:sanctum');
    Route::post('user/change/password', [LoginController::class, 'ChangePassword'])->middleware('auth:sanctum');
    Route::post('user/vote', [RegisterController::class, 'UserVote'])->middleware('auth:sanctum');
    Route::get('user/vote/edit', [RegisterController::class, 'UserVoteEdit'])->middleware('auth:sanctum');
// reset password
// change  password
});
