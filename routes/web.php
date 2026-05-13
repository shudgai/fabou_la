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

Route::get('/debug-user', function() {
    $user = auth()->user();
    if (!$user) return "未登入";
    
    $myRegistries = \App\Models\Registry::where('user_id', $user->id)->count();
    $allRegistries = \App\Models\Registry::count();
    
    return [
        '目前登入使用者ID' => $user->id,
        '目前使用者姓名' => $user->name,
        '目前使用者法號' => $user->dharmaName ? $user->dharmaName->name : '無',
        '資料庫總資料筆數' => $allRegistries,
        '目前使用者應看見筆數' => $myRegistries,
        '是否為管理員' => $user->isAdmin() ? '是' : '否'
    ];
});

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

// Google Authentication Routes
Route::get('auth/google', [App\Http\Controllers\Auth\GoogleController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('auth/google/callback', [App\Http\Controllers\Auth\GoogleController::class, 'handleGoogleCallback']);

// Direct Password Reset (Simplified Flow)
Route::get('password/direct-reset', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'showDirectResetForm'])->name('password.direct');
Route::post('password/direct-reset', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'directReset'])->name('password.direct.update');

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Archival System Routes
Route::middleware(['auth'])->group(function () {
    Route::resource('masters', App\Http\Controllers\MasterController::class);
    Route::post('registries/batch', [App\Http\Controllers\RegistryController::class, 'batchStore']);
    Route::post('registries/reorder', [App\Http\Controllers\RegistryController::class, 'reorder']);
    Route::patch('registries/personnel/{id}/remarks', [App\Http\Controllers\RegistryController::class, 'updatePersonnelRemarks']);
    Route::resource('registries', App\Http\Controllers\RegistryController::class);
    Route::resource('imperial-graces', App\Http\Controllers\ImperialGraceController::class);
    Route::post('imperial-graces/registry', [App\Http\Controllers\ImperialGraceController::class, 'storeRegistry']);
    Route::match(['PUT', 'PATCH'], 'imperial-graces/registry/{id}', [App\Http\Controllers\ImperialGraceController::class, 'updateRegistry']);
    Route::delete('imperial-graces/registry/{id}', [App\Http\Controllers\ImperialGraceController::class, 'destroyRegistry']);
    Route::post('imperial-graces/registry/batch', [App\Http\Controllers\ImperialGraceController::class, 'batchStoreRegistry']);
    Route::post('imperial-graces/registry/reorder', [App\Http\Controllers\ImperialGraceController::class, 'reorder']);
    
    // Kaiwen Manager Routes
    Route::get('kaiwen', [App\Http\Controllers\KaiwenManagerController::class, 'index']);
    Route::post('kaiwen/weekly', [App\Http\Controllers\KaiwenManagerController::class, 'storeWeekly']);
    Route::match(['PUT', 'PATCH'], 'kaiwen/weekly/{id}', [App\Http\Controllers\KaiwenManagerController::class, 'updateWeekly']);
    Route::delete('kaiwen/weekly/{id}', [App\Http\Controllers\KaiwenManagerController::class, 'destroyWeekly']);
    Route::post('kaiwen/weekly/reorder', [App\Http\Controllers\KaiwenManagerController::class, 'reorderWeekly']);
    
    Route::post('kaiwen/self', [App\Http\Controllers\KaiwenManagerController::class, 'storeSelf']);
    Route::match(['PUT', 'PATCH'], 'kaiwen/self/{id}', [App\Http\Controllers\KaiwenManagerController::class, 'updateSelf']);
    Route::delete('kaiwen/self/{id}', [App\Http\Controllers\KaiwenManagerController::class, 'destroySelf']);
    Route::post('kaiwen/self/reorder', [App\Http\Controllers\KaiwenManagerController::class, 'reorderSelf']);
    Route::post('kaiwen/batch', [App\Http\Controllers\KaiwenManagerController::class, 'batchStore']);
    Route::post('treasures/batch', [App\Http\Controllers\TreasureController::class, 'batchStore']);
    Route::resource('treasures', App\Http\Controllers\TreasureController::class);
    Route::post('teachings/reorder', [App\Http\Controllers\TeachingController::class, 'reorder']);
    Route::resource('teachings', App\Http\Controllers\TeachingController::class);
    Route::post('other-teachings/reorder', [App\Http\Controllers\OtherTeachingController::class, 'reorder']);
    Route::resource('other-teachings', App\Http\Controllers\OtherTeachingController::class);
    Route::resource('other-folders', App\Http\Controllers\OtherFolderController::class);

    Route::resource('users', App\Http\Controllers\UserController::class);
    Route::post('other-folders/{id}/records', [App\Http\Controllers\OtherFolderController::class, 'storeRecord']);
    Route::put('other-records/{id}', [App\Http\Controllers\OtherFolderController::class, 'updateRecord']);
    Route::delete('other-records/{id}', [App\Http\Controllers\OtherFolderController::class, 'destroyRecord']);
    Route::resource('dharma-names', App\Http\Controllers\DharmaNameController::class);
    Route::resource('roles', App\Http\Controllers\RoleController::class);
    Route::post('grudges/batch', [App\Http\Controllers\GrudgeController::class, 'batchStore']);
    Route::get('grudges/date-groups', [App\Http\Controllers\GrudgeController::class, 'dateGroups']);
    Route::resource('grudges', App\Http\Controllers\GrudgeController::class);
    Route::get('military-records/date-groups', [App\Http\Controllers\MilitaryRecordController::class, 'dateGroups']);
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
        return \App\Models\Group::with(['dharmaNames:id,name,order', 'dharmaNames.groups:id,name'])->select('id', 'name')->get();
    });
    Route::get('/api/users-list', function () {
        return \App\Models\User::select('id', 'name')->get();
    });
    Route::get('/api/dharma-names-list', function () {
        return \App\Models\DharmaName::with('groups:id,name')->select('id', 'name', 'alias', 'order')->orderBy('order')->get();
    });
    Route::get('/api/user-profile', function () {
        $user = auth()->user()->load('dharmaName');
        $user->is_admin = $user->isAdmin();
        $user->is_chijue = $user->isChijue();
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
    
    // Image Generation (标楷体 folder images)
    Route::get('folder-image/{template}', [App\Http\Controllers\ImageController::class, 'folder'])->name('folder.image');
    Route::get('folder-image/generate-all', [App\Http\Controllers\ImageController::class, 'generateAll'])->name('folder.generate-all');

    // Trash / Recycle Bin System
    Route::get('/api/trash', [App\Http\Controllers\TrashController::class, 'index']);
    Route::post('/api/trash/restore', [App\Http\Controllers\TrashController::class, 'restore']);
    Route::post('/api/trash/force-delete', [App\Http\Controllers\TrashController::class, 'forceDelete']);
    Route::post('/api/trash/cleanup', [App\Http\Controllers\TrashController::class, 'cleanup']);
});

