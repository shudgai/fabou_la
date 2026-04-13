<?php

namespace App\Http\Controllers;

use App\Services\MasterService;
use Illuminate\Http\Request;

class MasterController extends Controller
{
    protected $masterService;

    public function __construct(MasterService $masterService)
    {
        $this->masterService = $masterService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categorizedMasters = $this->masterService->getAllCategorized();
        return view('masters.index', compact('categorizedMasters'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('masters.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|in:imperial,others,teaching',
        ]);

        $this->masterService->createMaster($validated);

        return redirect()->route('masters.index')->with('success', '仙師建立成功');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $master = $this->masterService->findById($id);
        if (!$master) abort(404);

        return view('masters.show', compact('master'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $master = $this->masterService->findById($id);
        if (!$master) abort(404);

        return view('masters.edit', compact('master'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|in:imperial,others,teaching',
            'status' => 'required|string',
        ]);

        $this->masterService->updateMaster($id, $validated);

        return redirect()->back()->with('success', '更新成功');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->masterService->deleteMaster($id);
        return redirect()->route('masters.index')->with('success', '刪除成功');
    }
}
