<?php

namespace App\Http\Controllers;

use App\Models\MilitaryRecord;
use Illuminate\Http\Request;

class MilitaryRecordController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $permissions = $user->getPermissions();
        $query = MilitaryRecord::orderBy('know_date', 'desc');
        
        if (!$user->isAdmin()) {
            $allowedArmies = $permissions['allowed_armies'] ?? [];
            $query->where(function($q) use ($user, $allowedArmies) {
                $q->where('user_id', $user->id)
                  ->orWhereIn('army_type', $allowedArmies);
            });
        }
        
        return $query->get();
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $permissions = $user->getPermissions();
        
        $validated = $request->validate([
            'user_name' => 'required|string',
            'army_type' => 'required|string',
            'quantity' => 'required|integer',
            'know_date' => 'required|date',
            'process_date' => 'nullable|date',
            'destination' => 'nullable|string',
            'user_remarks' => 'nullable|string',
            'remarks_text' => 'nullable|string',
            'yan_zun' => 'nullable|integer',
            'yan_an' => 'nullable|integer',
            'long_sheng' => 'nullable|integer',
            'long_zhan' => 'nullable|integer',
            'yan_jue' => 'nullable|integer',
            'yan_ze' => 'nullable|integer',
            'yan_di' => 'nullable|integer',
            'yan_yuan' => 'nullable|integer',
        ]);

        if (!$user->isAdmin() && !in_array($validated['army_type'], $permissions['allowed_armies'])) {
            return response()->json(['error' => 'Unauthorized army type'], 403);
        }

        $validated['user_id'] = $user->id;
        return MilitaryRecord::create($validated);
    }

    public function update(Request $request, $id)
    {
        $record = MilitaryRecord::findOrFail($id);
        $permissions = auth()->user()->getPermissions();

        if (!auth()->user()->isAdmin() && !in_array($record->army_type, $permissions['allowed_armies'])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if ($request->has('army_type') && !auth()->user()->isAdmin() && !in_array($request->army_type, $permissions['allowed_armies'])) {
            return response()->json(['error' => 'Unauthorized army type change'], 403);
        }

        $record->update($request->all());
        return $record;
    }

    public function destroy($id)
    {
        $record = MilitaryRecord::findOrFail($id);
        $permissions = auth()->user()->getPermissions();

        if (!auth()->user()->isAdmin() && !in_array($record->army_type, $permissions['allowed_armies'])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $record->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
