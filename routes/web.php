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

// Google Authentication Routes
Route::get('auth/google', [App\Http\Controllers\Auth\GoogleController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('auth/google/callback', [App\Http\Controllers\Auth\GoogleController::class, 'handleGoogleCallback']);

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Archival System Routes
Route::middleware(['auth'])->group(function () {
    Route::resource('masters', App\Http\Controllers\MasterController::class);
    Route::post('registries/batch', [App\Http\Controllers\RegistryController::class, 'batchStore']);
Route::resource('registries', App\Http\Controllers\RegistryController::class);
    Route::resource('imperial-graces', App\Http\Controllers\ImperialGraceController::class);
    Route::post('imperial-graces/registry', [App\Http\Controllers\ImperialGraceController::class, 'storeRegistry']);
    Route::match(['PUT', 'PATCH'], 'imperial-graces/registry/{id}', [App\Http\Controllers\ImperialGraceController::class, 'updateRegistry']);
    Route::delete('imperial-graces/registry/{id}', [App\Http\Controllers\ImperialGraceController::class, 'destroyRegistry']);
    Route::post('imperial-graces/registry/batch', [App\Http\Controllers\ImperialGraceController::class, 'batchStoreRegistry']);
    Route::post('imperial-graces/registry/reorder', [App\Http\Controllers\ImperialGraceController::class, 'reorder']);
    Route::post('treasures/batch', [App\Http\Controllers\TreasureController::class, 'batchStore']);
    Route::resource('treasures', App\Http\Controllers\TreasureController::class);
    Route::resource('teachings', App\Http\Controllers\TeachingController::class);
    Route::resource('other-folders', App\Http\Controllers\OtherFolderController::class);
    Route::resource('groups', App\Http\Controllers\GroupController::class);
    Route::resource('users', App\Http\Controllers\UserController::class);
    Route::post('other-folders/{id}/records', [App\Http\Controllers\OtherFolderController::class, 'storeRecord']);
    Route::put('other-records/{id}', [App\Http\Controllers\OtherFolderController::class, 'updateRecord']);
    Route::delete('other-records/{id}', [App\Http\Controllers\OtherFolderController::class, 'destroyRecord']);
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
        return \App\Models\Group::with('dharmaNames:id,name')->select('id', 'name')->get();
    });
    Route::get('/api/users-list', function () {
        return \App\Models\User::select('id', 'name')->get();
    });
    Route::get('/api/dharma-names-list', function () {
        return \App\Models\DharmaName::with('groups:id,name')->select('id', 'name', 'alias')->orderBy('order')->get();
    });
    Route::get('/api/user-profile', function () {
        $user = auth()->user()->load('dharmaName');
        $user->is_admin = $user->isAdmin();
        $user->permissions = $user->getPermissions();
        return $user;
    });
    Route::get('/api/treasures-list', function (\Illuminate\Http\Request $request) {
        $masterId = $request->query('master_id');
        $type = $request->query('type', 'teaching');
        
        if ($type === 'registry' && !auth()->user()->isChijue() && !auth()->user()->isAdmin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $query = \App\Models\Treasure::select('id', 'name');
        if ($masterId !== null) {
            $query->where('master_id', $masterId);
        }
        if ($type !== null) {
            $query->whereIn('type', (array)$type);
        }
        return $query->get();
    });
    Route::get('/api/teaching-rules', [App\Http\Controllers\TeachingController::class, 'rules']);
    
    // Trash / Recycle Bin System
    Route::get('/api/trash', [App\Http\Controllers\TrashController::class, 'index']);
    Route::post('/api/trash/restore', [App\Http\Controllers\TrashController::class, 'restore']);
    Route::post('/api/trash/force-delete', [App\Http\Controllers\TrashController::class, 'forceDelete']);
    Route::post('/api/trash/cleanup', [App\Http\Controllers\TrashController::class, 'cleanup']);
});

