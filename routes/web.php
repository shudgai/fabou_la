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
    Route::resource('registries', App\Http\Controllers\RegistryController::class);
    Route::resource('imperial-graces', App\Http\Controllers\ImperialGraceController::class);
    Route::post('imperial-graces/registry', [App\Http\Controllers\ImperialGraceController::class, 'storeRegistry']);
    Route::match(['PUT', 'PATCH'], 'imperial-graces/registry/{id}', [App\Http\Controllers\ImperialGraceController::class, 'updateRegistry']);
    Route::delete('imperial-graces/registry/{id}', [App\Http\Controllers\ImperialGraceController::class, 'destroyRegistry']);
    Route::post('imperial-graces/registry/batch', [App\Http\Controllers\ImperialGraceController::class, 'batchStoreRegistry']);
    Route::post('treasures/batch', [App\Http\Controllers\TreasureController::class, 'batchStore']);
    Route::resource('treasures', App\Http\Controllers\TreasureController::class);
    Route::resource('teachings', App\Http\Controllers\TeachingController::class);
    Route::resource('other-folders', App\Http\Controllers\OtherFolderController::class);
    Route::resource('other-records', App\Http\Controllers\OtherRecordController::class);
    Route::resource('dharma-names', App\Http\Controllers\DharmaNameController::class);
    Route::resource('roles', App\Http\Controllers\RoleController::class);
    Route::post('grudges/batch', [App\Http\Controllers\GrudgeController::class, 'batchStore']);
    Route::resource('grudges', App\Http\Controllers\GrudgeController::class);
    Route::resource('military-records', App\Http\Controllers\MilitaryRecordController::class);

    // Notebook Archive System (Main Dashboard)
    Route::get('/note', function () {
        return view('note.index');
    })->name('note.index');

    // System Administration (Roles, Dharma Names, etc.)
    Route::get('/admin', function () {
        if (!auth()->user()->isAdmin()) {
            return redirect('/home')->with('error', '您沒有管理員權限');
        }
        return view('admin.management');
    })->name('admin.management');

    // API Helpers for Vue Search/Select
    Route::get('/api/masters-list', function () {
        return \App\Models\Master::select('id', 'name')->get();
    });
    Route::get('/api/groups-list', function () {
        return \App\Models\Group::select('id', 'name')->get();
    });
    Route::get('/api/users-list', function () {
        return \App\Models\User::select('id', 'name')->get();
    });
    Route::get('/api/dharma-names-list', function () {
        return \App\Models\DharmaName::select('id', 'name')->orderBy('order')->get();
    });
    Route::get('/api/treasures-list', function () {
        return \App\Models\Treasure::select('id', 'name')->get();
    });
    // 強制下載 Helper
});

