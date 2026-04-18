<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\OtherFolder;
use App\Models\OtherRecord;
use Illuminate\Http\Request;

class OtherFolderController extends Controller
{
    public function index()
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->getPermissions()['can_see_other_folders']) {
            return response()->json([]);
        }

        return OtherFolder::with(['otherRecords' => function($query) {
            $query->orderBy('record_date', 'desc')->orderBy('created_at', 'desc');
        }])->get();
    }

    public function store(Request $request)
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->getPermissions()['can_see_other_folders']) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string',
            'color' => 'nullable|string',
        ]);

        return OtherFolder::create($validated);
    }

    public function storeRecord(Request $request, $folderId)
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->getPermissions()['can_see_other_folders']) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'title' => 'nullable|string',
            'content' => 'nullable|string',
            'record_date' => 'nullable|date',
        ]);

        $folder = OtherFolder::findOrFail($folderId);
        return $folder->otherRecords()->create($validated);
    }

    public function updateRecord(Request $request, $id)
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->getPermissions()['can_see_other_folders']) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $record = OtherRecord::findOrFail($id);
        $record->update($request->all());
        return $record;
    }

    public function destroyRecord($id)
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->getPermissions()['can_see_other_folders']) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        OtherRecord::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->getPermissions()['can_see_other_folders']) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        OtherFolder::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }
}
