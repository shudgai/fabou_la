<?php

namespace App\Http\Controllers;

use App\Services\TreasureService;
use Illuminate\Http\Request;

class TreasureController extends Controller
{
    protected $treasureService;

    public function __construct(TreasureService $treasureService)
    {
        $this->treasureService = $treasureService;
    }

    public function index()
    {
        return response()->json($this->treasureService->getAllTreasures());
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'master_id' => 'required|exists:masters,id',
        ]);

        $treasure = $this->treasureService->createTreasure($request->all());
        return response()->json($treasure, 201);
    }

    public function batchStore(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.name' => 'required|string',
            'items.*.master_id' => 'required|exists:masters,id',
        ]);

        $this->treasureService->createBatchTreasures($request->items);
        return response()->json(['message' => 'Success'], 201);
    }

    public function update(Request $request, string $id)
    {
        $treasure = $this->treasureService->updateTreasure((int)$id, $request->all());
        return $treasure ? response()->json($treasure) : response()->json(['message' => 'Not found'], 404);
    }

    public function destroy(string $id)
    {
        $success = $this->treasureService->deleteTreasure((int)$id);
        return $success ? response()->json(['message' => 'Deleted']) : response()->json(['message' => 'Error'], 400);
    }
}
