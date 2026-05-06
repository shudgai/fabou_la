<?php

namespace App\Http\Controllers;

use App\Models\MilitaryRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MilitaryRecordController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = MilitaryRecord::where('user_id', $user->id);

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('user_name', 'like', "%{$search}%")
                  ->orWhere('user_remarks', 'like', "%{$search}%")
                  ->orWhere('remarks_text', 'like', "%{$search}%");
            });
        }

        if ($request->has('army_type')) {
            $query->where('army_type', $request->army_type);
        }

        if ($request->has('know_date')) {
            if (empty($request->know_date) || $request->know_date === 'null') {
                $query->whereNull('know_date');
            } else {
                $query->where('know_date', $request->know_date);
            }
        }

        $query->orderBy('know_date', 'desc')->orderBy('id', 'desc');
        
        // Single query: get per-army quantity sums AND column sums at once
        $armyStats = MilitaryRecord::where('user_id', $user->id)
            ->selectRaw('army_type, SUM(quantity) as total_qty, SUM(yan_zun) as yan_zun, SUM(yan_an) as yan_an, SUM(long_sheng) as long_sheng, SUM(long_zhan) as long_zhan, SUM(yan_jue) as yan_jue, SUM(yan_ze) as yan_ze, SUM(yan_di) as yan_di, SUM(yan_yuan) as yan_yuan')
            ->groupBy('army_type')
            ->get();

        $armyCounts = $armyStats->pluck('total_qty', 'army_type');
        
        $currentStats = $request->has('army_type') 
            ? $armyStats->where('army_type', $request->army_type)->first() 
            : null;

        $breakdownTotals = [
            'yan_zun'    => $currentStats ? $currentStats->yan_zun : $armyStats->sum('yan_zun'),
            'yan_an'     => $currentStats ? $currentStats->yan_an : $armyStats->sum('yan_an'),
            'long_sheng' => $currentStats ? $currentStats->long_sheng : $armyStats->sum('long_sheng'),
            'long_zhan'  => $currentStats ? $currentStats->long_zhan : $armyStats->sum('long_zhan'),
            'yan_jue'    => $currentStats ? $currentStats->yan_jue : $armyStats->sum('yan_jue'),
            'yan_ze'     => $currentStats ? $currentStats->yan_ze : $armyStats->sum('yan_ze'),
            'yan_di'     => $currentStats ? $currentStats->yan_di : $armyStats->sum('yan_di'),
            'yan_yuan'   => $currentStats ? $currentStats->yan_yuan : $armyStats->sum('yan_yuan'),
        ];

        return response()->json([
            'records'         => $query->paginate($request->input('per_page', 10)),
            'armyCounts'      => $armyCounts,
            'breakdownTotals' => $breakdownTotals
        ]);
    }

    public function dateGroups(Request $request)
    {
        $user = auth()->user();
        $query = MilitaryRecord::where('user_id', $user->id);

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('user_name', 'like', "%{$search}%")
                  ->orWhere('user_remarks', 'like', "%{$search}%")
                  ->orWhere('remarks_text', 'like', "%{$search}%");
            });
        }

        if ($request->has('army_type')) {
            $query->where('army_type', $request->army_type);
        }

        $dates = $query->select('know_date', DB::raw('count(*) as count'), DB::raw('sum(quantity) as total_qty'))
            ->groupBy('know_date')
            ->orderByRaw('know_date IS NULL ASC, know_date DESC')
            ->paginate($request->input('per_page', 20));

        // Get per-army quantity sums for the root view
        $armyCounts = MilitaryRecord::where('user_id', $user->id)
            ->selectRaw('army_type, SUM(quantity) as total_qty')
            ->groupBy('army_type')
            ->get()
            ->pluck('total_qty', 'army_type');

        $result = $dates->toArray();
        $result['armyCounts'] = $armyCounts;

        return response()->json($result);
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $permissions = $user->getPermissions();
        
        try {
            $validated = $request->validate([
                'user_name' => 'nullable|string',
                'army_type' => 'required|string',
                'quantity' => 'required|numeric',
                'know_date' => 'nullable|date',
                'process_date' => 'nullable|date',
                'destination' => 'nullable|string',
                'user_remarks' => 'nullable|string',
                'remarks_text' => 'nullable|string',
                'yan_zun' => 'nullable|numeric',
                'yan_an' => 'nullable|numeric',
                'long_sheng' => 'nullable|numeric',
                'long_zhan' => 'nullable|numeric',
                'yan_jue' => 'nullable|numeric',
                'yan_ze' => 'nullable|numeric',
                'yan_di' => 'nullable|numeric',
                'yan_yuan' => 'nullable|numeric',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation failed for MilitaryRecord:', [
                'errors' => $e->errors(),
                'input' => $request->all()
            ]);
            throw $e;
        }

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
        if ($record->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if (!in_array($record->army_type, $permissions['allowed_armies'])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if ($request->has('army_type') && !in_array($request->army_type, $permissions['allowed_armies'])) {
            return response()->json(['error' => 'Unauthorized army type change'], 403);
        }

        $validated = $request->validate([
            'user_name' => 'nullable|string',
            'army_type' => 'nullable|string',
            'quantity' => 'nullable|integer',
            'know_date' => 'nullable|date',
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

        $record->update($validated);
        return $record;
    }

    public function destroy($id)
    {
        $record = MilitaryRecord::findOrFail($id);
        $permissions = auth()->user()->getPermissions();

        if ($record->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if (!in_array($record->army_type, $permissions['allowed_armies'])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $record->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
