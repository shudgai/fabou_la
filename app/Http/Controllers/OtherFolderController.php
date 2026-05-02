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
        $user = auth()->user();
        if (!$user->getPermissions()['can_see_other_folders']) {
            return response()->json([]);
        }

        $query = OtherFolder::where('user_id', $user->id)
            ->with(['otherRecords' => function($sq) use ($user) {
                $sq->where('user_id', $user->id)
                  ->orderBy('record_date', 'desc')
                  ->orderBy('created_at', 'desc');
            }]);

        return $query->get();
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        if (!$user->getPermissions()['can_see_other_folders']) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string',
            'color' => 'nullable|string',
        ]);

        $validated['user_id'] = $user->id;
        return OtherFolder::create($validated);
    }

    public function storeRecord(Request $request, $folderId)
    {
        $user = auth()->user();
        if (!$user->getPermissions()['can_see_other_folders']) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $folder = OtherFolder::findOrFail($folderId);
        if ($folder->user_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'title' => 'nullable|string',
            'content' => 'nullable|string',
            'record_date' => 'nullable|date',
            'extra_data' => 'nullable|array',
        ]);

        $validated['user_id'] = $user->id;
        return $folder->otherRecords()->create($validated);
    }

    public function updateRecord(Request $request, $id)
    {
        if (!auth()->user()->getPermissions()['can_see_other_folders']) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $record = OtherRecord::findOrFail($id);
        if ($record->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $record->update($request->all());
        return $record;
    }

    public function destroyRecord($id)
    {
        if (!auth()->user()->getPermissions()['can_see_other_folders']) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $record = OtherRecord::findOrFail($id);
        if ($record->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $record->delete();
        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        if (!auth()->user()->getPermissions()['can_see_other_folders']) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $folder = OtherFolder::findOrFail($id);
        if ($folder->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $folder->delete();
        return response()->json(['success' => true]);
    }
}
