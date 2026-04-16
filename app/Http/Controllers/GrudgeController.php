<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\GrudgeService;
use Illuminate\Http\Request;

class GrudgeController extends Controller
{
    protected $grudgeService;

    public function __construct(GrudgeService $grudgeService)
    {
        $this->grudgeService = $grudgeService;
    }

    public function index()
    {
        $grudges = $this->grudgeService->getAll();
        return response()->json($grudges);
    }

    public function store(Request $request)
    {
        $grudge = $this->grudgeService->create($request->all());
        return response()->json($grudge, 201);
    }

    public function batchStore(Request $request)
    {
        $items = $request->input('items', []);
        $results = $this->grudgeService->batchCreate($items);
        return response()->json($results, 201);
    }

    public function update(Request $request, string $id)
    {
        $success = $this->grudgeService->update((int)$id, $request->all());
        return $success ? response()->json(['message' => 'Updated']) : response()->json(['message' => 'Error'], 400);
    }

    public function destroy(string $id)
    {
        $success = $this->grudgeService->delete((int)$id);
        return $success ? response()->json(['message' => 'Deleted']) : response()->json(['message' => 'Error'], 400);
    }
}
