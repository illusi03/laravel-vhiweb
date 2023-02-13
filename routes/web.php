<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});


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