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
        $permissions = $user->getPermissions();
        $allowedArmies = $user->isAdmin() ? ['黑曜軍', '耀紫軍', '虎甲軍', '虎賁軍'] : $permissions['allowed_armies'];

        $query = MilitaryRecord::where('user_id', $user->id);

        if ($request->has('army_type')) {
            $armyType = $request->army_type;
            if (!in_array($armyType, $allowedArmies)) {
                return response()->json(['error' => 'Unauthorized army type'], 403);
            }
            $query->where('army_type', $armyType);
        } else {
            $query->whereIn('army_type', $allowedArmies);
        }

        $searchQuery = !empty($request->search) ? mb_strtolower($request->search) : null;

        if ($request->has('know_date')) {
            if (empty($request->know_date) || $request->know_date === 'null') {
                $query->whereNull('know_date');
            } else {
                $query->where('know_date', $request->know_date);
            }
        }

        $query->orderBy('know_date', 'desc')->orderBy('id', 'desc');
        
        $allRecords = $query->get();

        if ($searchQuery) {
            $allRecords = $allRecords->filter(function($r) use ($searchQuery) {
                if (str_contains(mb_strtolower((string)$r->user_name), $searchQuery)) return true;
                if (str_contains(mb_strtolower((string)$r->user_remarks), $searchQuery)) return true;
                if (str_contains(mb_strtolower((string)$r->remarks_text), $searchQuery)) return true;
                return false;
            })->values();
        }

        $statsRecords = MilitaryRecord::where('user_id', $user->id)->whereIn('army_type', $allowedArmies)->get();
        if ($searchQuery) {
            $statsRecords = $statsRecords->filter(function($r) use ($searchQuery) {
                if (str_contains(mb_strtolower((string)$r->user_name), $searchQuery)) return true;
                if (str_contains(mb_strtolower((string)$r->user_remarks), $searchQuery)) return true;
                if (str_contains(mb_strtolower((string)$r->remarks_text), $searchQuery)) return true;
                return false;
            })->values();
        }

        $armyStats = collect();
        $statsRecords->groupBy('army_type')->each(function($group, $armyType) use (&$armyStats) {
            $armyStats->push((object)[
                'army_type' => $armyType,
                'total_qty' => $group->sum('quantity'),
                'yan_zun' => $group->sum('yan_zun'),
                'yan_an' => $group->sum('yan_an'),
                'long_sheng' => $group->sum('long_sheng'),
                'long_zhan' => $group->sum('long_zhan'),
                'yan_jue' => $group->sum('yan_jue'),
                'yan_ze' => $group->sum('yan_ze'),
                'yan_di' => $group->sum('yan_di'),
                'yan_yuan' => $group->sum('yan_yuan'),
            ]);
        });

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

        $page = \Illuminate\Pagination\Paginator::resolveCurrentPage() ?: 1;
        $perPage = $request->input('per_page', 10);
        $paginated = new \Illuminate\Pagination\LengthAwarePaginator(
            $allRecords->forPage($page, $perPage)->values(),
            $allRecords->count(),
            $perPage,
            $page,
            ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
        );

        return response()->json([
            'records'         => $paginated,
            'armyCounts'      => $armyCounts,
            'breakdownTotals' => $breakdownTotals
        ]);
    }

    public function dateGroups(Request $request)
    {
        $user = auth()->user();
        $permissions = $user->getPermissions();
        $allowedArmies = $user->isAdmin() ? ['黑曜軍', '耀紫軍', '虎甲軍', '虎賁軍'] : $permissions['allowed_armies'];

        $query = MilitaryRecord::where('user_id', $user->id);

        if ($request->has('army_type')) {
            $armyType = $request->army_type;
            if (!in_array($armyType, $allowedArmies)) {
                return response()->json(['error' => 'Unauthorized army type'], 403);
            }
            $query->where('army_type', $armyType);
        } else {
            $query->whereIn('army_type', $allowedArmies);
        }

        $searchQuery = !empty($request->search) ? mb_strtolower($request->search) : null;
        $allRecords = $query->get();

        if ($searchQuery) {
            $allRecords = $allRecords->filter(function($r) use ($searchQuery) {
                if (str_contains(mb_strtolower((string)$r->user_name), $searchQuery)) return true;
                if (str_contains(mb_strtolower((string)$r->user_remarks), $searchQuery)) return true;
                if (str_contains(mb_strtolower((string)$r->remarks_text), $searchQuery)) return true;
                return false;
            })->values();
        }

        $dateGroups = collect();
        $allRecords->groupBy(function($item) {
            // Group by formatted date string or 'null'
            return $item->know_date ? $item->know_date->format('Y-m-d') : 'null';
        })->each(function($group, $dateStr) use (&$dateGroups) {
            $dateGroups->push([
                'know_date' => $dateStr === 'null' ? null : $dateStr,
                'count' => $group->count(),
                'total_qty' => $group->sum('quantity')
            ]);
        });

        // Sort: know_date IS NULL ASC (nulls at end), know_date DESC
        $dateGroups = $dateGroups->sort(function($a, $b) {
            if ($a['know_date'] === null && $b['know_date'] !== null) return 1;
            if ($a['know_date'] !== null && $b['know_date'] === null) return -1;
            if ($a['know_date'] === $b['know_date']) return 0;
            return $a['know_date'] < $b['know_date'] ? 1 : -1;
        })->values();

        $page = \Illuminate\Pagination\Paginator::resolveCurrentPage() ?: 1;
        $perPage = $request->input('per_page', 20);
        $datesPaginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $dateGroups->forPage($page, $perPage)->values(),
            $dateGroups->count(),
            $perPage,
            $page,
            ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
        );

        $rootStatsQuery = MilitaryRecord::where('user_id', $user->id)->whereIn('army_type', $allowedArmies)->get();
        if ($searchQuery) {
            $rootStatsQuery = $rootStatsQuery->filter(function($r) use ($searchQuery) {
                if (str_contains(mb_strtolower((string)$r->user_name), $searchQuery)) return true;
                if (str_contains(mb_strtolower((string)$r->user_remarks), $searchQuery)) return true;
                if (str_contains(mb_strtolower((string)$r->remarks_text), $searchQuery)) return true;
                return false;
            })->values();
        }

        $armyCounts = collect();
        $rootStatsQuery->groupBy('army_type')->each(function($group, $armyType) use (&$armyCounts) {
            $armyCounts[$armyType] = $group->sum('quantity');
        });

        $result = $datesPaginator->toArray();
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
                'quantity' => 'required|regex:/^\d+$/',
                'know_date' => 'nullable|string',
                'process_date' => 'nullable|string',
                'destination' => 'nullable|string',
                'user_remarks' => 'nullable|string',
                'remarks_text' => 'nullable|string',
                'yan_zun' => 'nullable|regex:/^\d+$/',
                'yan_an' => 'nullable|regex:/^\d+$/',
                'long_sheng' => 'nullable|regex:/^\d+$/',
                'long_zhan' => 'nullable|regex:/^\d+$/',
                'yan_jue' => 'nullable|regex:/^\d+$/',
                'yan_ze' => 'nullable|regex:/^\d+$/',
                'yan_di' => 'nullable|regex:/^\d+$/',
                'yan_yuan' => 'nullable|regex:/^\d+$/',
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
            'quantity' => 'nullable|regex:/^\d+$/',
            'know_date' => 'nullable|date',
            'process_date' => 'nullable|date',
            'destination' => 'nullable|string',
            'user_remarks' => 'nullable|string',
            'remarks_text' => 'nullable|string',
            'yan_zun' => 'nullable|regex:/^\d+$/',
            'yan_an' => 'nullable|regex:/^\d+$/',
            'long_sheng' => 'nullable|regex:/^\d+$/',
            'long_zhan' => 'nullable|regex:/^\d+$/',
            'yan_jue' => 'nullable|regex:/^\d+$/',
            'yan_ze' => 'nullable|regex:/^\d+$/',
            'yan_di' => 'nullable|regex:/^\d+$/',
            'yan_yuan' => 'nullable|regex:/^\d+$/',
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
        return response()->json(['message' => '已刪除']);
    }
}
