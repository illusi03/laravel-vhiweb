<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CooperationController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\MainTransactionController;
use App\Http\Controllers\VerificationEmailController;
use App\Models\MainTransaction;

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

Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);
Route::post('forgot_password', [ForgotPasswordController::class, 'forgotPassword']);
Route::post('update_password', [ForgotPasswordController::class, 'updatePassword']);
Route::get('verification_email/resend/{id}', [VerificationEmailController::class, 'resend'])
    ->name('verification.resend');
Route::get('verification_email/verify/{id}/{hash}', [VerificationEmailController::class, 'verify'])
    ->name('verification.verify');

Route::middleware(['auth.httponly', 'verified'])->group(function () {
    // Authentication
    Route::post('logout', [AuthController::class, 'logout']);
    // Users
    Route::get('users/profile', [AuthController::class, 'profile']);
    Route::get('users', [UserController::class, 'index']);
    Route::post('users/update_password', [UserController::class, 'updatePasswordSelf']);
    // Permissions And Roles
    Route::get('permissions', [PermissionController::class, 'index']);
    Route::apiResource('roles', RoleController::class);
    Route::post('roles_default', [RoleController::class, 'default']);
});
