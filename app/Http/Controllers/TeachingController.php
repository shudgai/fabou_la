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

    public function index()
    {
        $teachings = $this->teachingService->getAll();
        return response()->json($teachings);
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
        $success = $this->teachingService->update((int)$id, $request->all());
        return $success ? response()->json(['message' => 'Updated']) : response()->json(['message' => 'Error'], 400);
    }

    public function destroy(string $id)
    {
        $success = $this->teachingService->delete((int)$id);
        return $success ? response()->json(['message' => 'Deleted']) : response()->json(['message' => 'Error'], 400);
    }
}
