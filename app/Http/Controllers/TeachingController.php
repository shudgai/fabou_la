<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
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
        
        $permissions = auth()->user()->getPermissions();
        if ($masterId == 0 && !$permissions['can_see_daily_teachings']) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if ($mode === 'dates') {
            return response()->json($this->teachingService->getPaginatedDates($masterId));
        }

        if ($date) {
            return response()->json($this->teachingService->getByDate($date, $masterId));
        }

        return response()->json($this->teachingService->getAll($masterId));
    }

    public function store(Request $request)
    {
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
        $orders = $request->input('orders', []);
        $this->teachingService->reorder($orders);
        return response()->json(['message' => 'Reordered']);
    }

    public function rules()
    {
        return response()->json($this->teachingService->getSpecializedRules());
    }
}
