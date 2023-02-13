<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\PhotoController;
use App\Http\Controllers\VerificationEmailController;

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
    // Photos
    Route::post('photos', [PhotoController::class, 'store']);
    Route::put('photos/{id}', [PhotoController::class, 'update']);
    Route::delete('photos/{id}', [PhotoController::class, 'delete']);
    Route::post('photos/{id}/like', [PhotoController::class, 'like']);
    Route::post('photos/{id}/unlike', [PhotoController::class, 'unlike']);
});

// Public 
// Photos
Route::get('photos', [PhotoController::class, 'index']);
Route::get('photos/{id}', [PhotoController::class, 'show']);



// TODOS: Remove me if done hosting
// FOR HOSTING ONLY
Route::group(['prefix' => 'artisan', 'as' => 'artisan.'], function () {
    Route::get('/migrate/{id}', function ($id) {
        if ($id != 'P@ssw0rd') return response()->json('Key nya salah');
        Artisan::call('migrate:fresh --seed');
        return response()->json('Berhasil menjalankan : migrate:fresh --seed');
    });
    Route::get('/passport/{id}', function ($id) {
        if ($id != 'P@ssw0rd') return response()->json('Key nya salah');
        Artisan::call('passport:install');
        return response()->json('Berhasil menjalankan : passport:install');
    });
    Route::get('/clear/{id}', function ($id) {
        if ($id != 'P@ssw0rd') return response()->json('Key nya salah');
        Artisan::call("clear-compiled");
        Artisan::call("cache:clear");
        Artisan::call("route:clear");
        Artisan::call("view:clear");
        Artisan::call("config:clear");
        return response()->json('Berhasil menjalankan : clear cache all');
    });
    Route::get('/storage/{id}', function ($id) {
        if ($id != 'P@ssw0rd') return response()->json('Key nya salah');
        $target = '/home/n1575448/public_html/laravel-vhiweb.azhari.web.id/storage/app/public';
        $shortcut = '/home/n1575448/public_html/laravel-vhiweb.azhari.web.id/public/storage';
        symlink($target, $shortcut);
        return response()->json('Berhasil menjalankan : symlink');
    });
});