<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Teaching;
use App\Services\TeachingService;
use Illuminate\Http\Request;

class TeachingController extends Controller
{
    protected $teachingService;

    public function __construct(TeachingService $teachingService)
    {
        $this->teachingService = $teachingService;
    }

    public function index(Request $request)
    {
        $masterId = $request->query('master_id');
        $date = $request->query('date');
        $mode = $request->query('mode'); // 'dates' or 'items'
        $perPage = $request->query('per_page', 15);
        $search = $request->query('search');
        $sortDesc = filter_var($request->query('sortDesc', true), FILTER_VALIDATE_BOOLEAN);
        
        $user = auth()->user();
        $permissions = $user->getPermissions();
        $isRequestingDaily = ($masterId === '0' || $masterId === 0 || (is_numeric($masterId) && (int)$masterId === 0 && $masterId !== null));
        if ($isRequestingDaily && !$permissions['can_see_daily_teachings']) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Single query: get per-master counts AND daily count at once
        $allCounts = Teaching::where('user_id', $user->id)
            ->selectRaw('master_id, count(*) as total, SUM(CASE WHEN is_daily = 1 THEN 1 ELSE 0 END) as daily_total')
            ->groupBy('master_id')
            ->get();

        $folderCounts = $allCounts->pluck('total', 'master_id');
        $folderCounts['daily'] = $allCounts->sum('daily_total');

        if ($mode === 'dates') {
            $data = $this->teachingService->getPaginatedDates($masterId, $perPage, $sortDesc);
            return response()->json([
                'data' => $data,
                'folderCounts' => $folderCounts
            ]);
        }

        if ($date) {
            $records = $this->teachingService->getByDate($date, $masterId, $sortDesc);
            return response()->json([
                'records' => $records,
                'folderCounts' => $folderCounts
            ]);
        }

        $records = $this->teachingService->getAll($masterId, $perPage, $search, $sortDesc);
        return response()->json([
            'records' => $records,
            'folderCounts' => $folderCounts
        ]);
    }

    public function store(Request $request)
    {
        $permissions = auth()->user()->getPermissions();
        $isDaily = $request->input('is_daily') == 1 || $request->input('master_id') == 0 || $request->input('master_id') === '0';
        
        if ($isDaily && empty($permissions['can_see_daily_teachings'])) {
            return response()->json(['error' => '您無權新增每日開示紀錄'], 403);
        }

        $teaching = $this->teachingService->create($request->all());
        return response()->json($teaching, 201);
    }

    public function show(string $id)
    {
        $teaching = $this->teachingService->findById((int)$id);
        return $teaching ? response()->json($teaching) : response()->json(['message' => 'Not found'], 404);
    }

    public function update(Request $request, string $id)
    {
        $id = (int)$id;
        if (!$this->teachingService->canModify($id, auth()->id())) {
            return response()->json(['message' => '您無權修改此紀錄'], 403);
        }
        $success = $this->teachingService->update($id, $request->all());
        return $success ? response()->json(['message' => 'Updated']) : response()->json(['message' => 'Error'], 400);
    }

    public function destroy(string $id)
    {
        $id = (int)$id;
        if (!$this->teachingService->canModify($id, auth()->id())) {
            return response()->json(['message' => '您無權刪除此紀錄'], 403);
        }
        $success = $this->teachingService->delete($id);
        return $success ? response()->json(['message' => 'Deleted']) : response()->json(['message' => 'Error'], 400);
    }

    public function reorder(Request $request)
    {
        try {
            $orders = $request->input('orders', []);
            if (empty($orders)) {
                return response()->json(['message' => '無效的排序數據'], 400);
            }
            
            \Illuminate\Support\Facades\DB::beginTransaction();
            $this->teachingService->reorder($orders);
            \Illuminate\Support\Facades\DB::commit();
            
            return response()->json(['message' => 'Reordered']);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            \Log::error('Teaching Reorder Error: ' . $e->getMessage());
            return response()->json(['message' => '伺服器排序錯誤: ' . $e->getMessage()], 500);
        }
    }

    public function rules()
    {
        return response()->json($this->teachingService->getSpecializedRules());
    }
}
