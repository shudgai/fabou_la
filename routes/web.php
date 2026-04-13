<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Archival System Routes
Route::middleware(['auth'])->group(function () {
    Route::resource('masters', App\Http\Controllers\MasterController::class);
    Route::resource('imperial-graces', App\Http\Controllers\ImperialGraceController::class);
    Route::resource('teachings', App\Http\Controllers\TeachingController::class);
    Route::resource('other-folders', App\Http\Controllers\OtherFolderController::class);
    Route::resource('other-records', App\Http\Controllers\OtherRecordController::class);
    Route::resource('dharma-names', App\Http\Controllers\DharmaNameController::class);
    Route::resource('roles', App\Http\Controllers\RoleController::class);
});

