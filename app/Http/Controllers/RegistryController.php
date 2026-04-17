<?php

namespace App\Http\Controllers;

use App\Models\Registry;
use App\Models\DharmaNameRegistry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RegistryController extends Controller
{
    public function index()
    {
        if (!auth()->user()->isChijue() && !auth()->user()->isAdmin()) {
            return response()->json(['error' => '您沒有權限查看法寶登記專區'], 403);
        }
        return Registry::with('dharmaNameRegistries')->get();
    }

    public function store(Request $request)
    {
        if (!auth()->user()->isChijue() && !auth()->user()->isAdmin()) {
            return response()->json(['error' => '您沒有權限登記法寶'], 403);
        }
        $validated = $request->validate([
            'master_id' => 'required',
            'name' => 'required',
            'record_date' => 'nullable|date',
        ]);

        return DB::transaction(function () use ($request) {
            $registry = Registry::create($request->all());

            if ($request->has('dharma_name_registries')) {
                foreach ($request->input('dharma_name_registries') as $dn) {
                    DharmaNameRegistry::create([
                        'registry_id' => $registry->id,
                        'dharma_name_id' => $dn['dharma_name_id'],
                        'obtained_date' => $dn['obtained_date'] ?? null,
                        'remarks' => $dn['remarks'] ?? null
                    ]);
                }
            }

            return $registry->load('dharmaNameRegistries');
        });
    }

    public function update(Request $request, Registry $registry)
    {
        if (!auth()->user()->isChijue() && !auth()->user()->isAdmin()) {
            return response()->json(['error' => '您沒有權限修改法寶登記專區'], 403);
        }
        return DB::transaction(function () use ($request, $registry) {
            $registry->update($request->all());

            if ($request->has('dharma_name_registries')) {
                // Simplified sync logic
                DharmaNameRegistry::where('registry_id', $registry->id)->delete();
                foreach ($request->input('dharma_name_registries') as $dn) {
                    DharmaNameRegistry::create([
                        'registry_id' => $registry->id,
                        'dharma_name_id' => $dn['dharma_name_id'],
                        'obtained_date' => $dn['obtained_date'] ?? null,
                        'remarks' => $dn['remarks'] ?? null
                    ]);
                }
            }

            return $registry->load('dharmaNameRegistries');
        });
    }

    public function destroy(Registry $registry)
    {
        if (!auth()->user()->isChijue() && !auth()->user()->isAdmin()) {
            return response()->json(['error' => '您沒有權限刪除法寶登記專區'], 403);
        }
        $registry->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
