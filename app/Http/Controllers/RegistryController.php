<?php

namespace App\Http\Controllers;

use App\Models\Registry;
use App\Models\DharmaNameRegistry;
use App\Models\DharmaName;
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
            return response()->json(['error' => '您沒有權限在法寶登記專區新增紀錄'], 403);
        }

        return DB::transaction(function () use ($request) {
            // Check for existing treasure with same name, master, and category
            $registry = Registry::where('name', $request->name)
                ->where('master_id', $request->master_id)
                ->where('category', $request->category)
                ->first();

            if (!$registry) {
                $registry = Registry::create($request->all());
            }

            if ($request->has('dharma_name_registries')) {
                foreach ($request->input('dharma_name_registries') as $dn) {
                    // AUTO-RESOLVE: Link to real DharmaName if names match
                    if (empty($dn['dharma_name_id']) && !empty($dn['custom_name'])) {
                        $matched = DharmaName::where('name', trim($dn['custom_name']))->first();
                        if ($matched) {
                            $dn['dharma_name_id'] = $matched->id;
                            $dn['custom_name'] = null;
                        }
                    }

                    // Check if this person already has a record for this treasure on this date
                    $exists = DharmaNameRegistry::where('registry_id', $registry->id)
                        ->where(function($q) use ($dn) {
                            if (!empty($dn['dharma_name_id'])) {
                                $q->where('dharma_name_id', $dn['dharma_name_id']);
                            } else {
                                $q->where('custom_name', $dn['custom_name'] ?? '');
                            }
                        })
                        ->where('obtained_date', $dn['obtained_date'] ?? null)
                        ->exists();

                    if (!$exists) {
                        DharmaNameRegistry::create([
                            'registry_id' => $registry->id,
                            'dharma_name_id' => $dn['dharma_name_id'] ?? null,
                            'custom_name' => $dn['custom_name'] ?? null,
                            'obtained_date' => $dn['obtained_date'] ?? null,
                            'remarks' => $dn['remarks'] ?? null,
                            'related_personnel' => $dn['related_personnel'] ?? null
                        ]);
                    } else {
                        // If it exists, we might need to MERGE new related personnel
                        $record = DharmaNameRegistry::where('registry_id', $registry->id)
                            ->where(function($q) use ($dn) {
                                if (!empty($dn['dharma_name_id'])) $q->where('dharma_name_id', $dn['dharma_name_id']);
                                else $q->where('custom_name', $dn['custom_name'] ?? '');
                            })
                            ->where('obtained_date', $dn['obtained_date'] ?? null)
                            ->first();
                        
                        if ($record && !empty($dn['related_personnel'])) {
                            $existingRel = $record->related_personnel ?? [];
                            $newRel = $dn['related_personnel'];
                            
                            $merged = $existingRel;
                            foreach ($newRel as $nr) {
                                // Check if this name already exists in the JSON array
                                $found = false;
                                foreach ($existingRel as $er) {
                                    if (($er['name'] ?? '') === ($nr['name'] ?? '')) {
                                        $found = true;
                                        break;
                                    }
                                }
                                if (!$found) $merged[] = $nr;
                            }
                            $record->update(['related_personnel' => $merged]);
                        }
                    }
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
                $incoming = $request->input('dharma_name_registries');
                $keepIds = [];
                
                foreach ($incoming as $dn) {
                    // AUTO-RESOLVE names
                    $dharma_name_id = $dn['dharma_name_id'] ?? null;
                    if (empty($dharma_name_id) && !empty($dn['custom_name'])) {
                        $matched = DharmaName::where('name', trim($dn['custom_name']))->first();
                        if ($matched) {
                            $dharma_name_id = $matched->id;
                            $dn['custom_name'] = null;
                        }
                    }

                    $record = DharmaNameRegistry::updateOrCreate(
                        [
                            'registry_id' => $registry->id,
                            'dharma_name_id' => $dharma_name_id,
                            'custom_name' => $dn['custom_name'] ?? null,
                        ],
                        [
                            'obtained_date' => $dn['obtained_date'] ?? null,
                            'remarks' => $dn['remarks'] ?? null,
                            // Do NOT overwrite related_personnel if not sent, to preserve existing parser data
                        ]
                    );

                    // If they DO send related_personnel, update it
                    if (array_key_exists('related_personnel', $dn)) {
                        $record->update(['related_personnel' => $dn['related_personnel']]);
                    }

                    $keepIds[] = $record->id;
                }

                // Optional: Cleanup missing ones IF the user expects a full sync. 
                // However, for in-place grid editing, we might just want to keep all.
                // For now, let's keep all to be safe unless they explicitly delete.
            }

            return $registry->load('dharmaNameRegistries');
        });
    }

    public function batchStore(Request $request)
    {
        if (!auth()->user()->isChijue() && !auth()->user()->isAdmin()) {
            return response()->json(['error' => '您沒有權限在法寶登記專區新增紀錄'], 403);
        }

        $records = $request->all();
        if (!is_array($records)) return response()->json(['error' => '無效的資料格式'], 400);

        return DB::transaction(function () use ($records) {
            $results = [];
            foreach ($records as $recordData) {
                if (empty($recordData['name']) || empty($recordData['master_id'])) continue;

                $registry = Registry::where('name', $recordData['name'])
                    ->where('master_id', $recordData['master_id'])
                    ->where('category', $recordData['category'] ?? 'major')
                    ->first();

                if (!$registry) {
                    $registry = Registry::create($recordData);
                }

                if (!empty($recordData['dharma_name_registries'])) {
                    foreach ($recordData['dharma_name_registries'] as $dn) {
                        if (empty($dn['custom_name']) && empty($dn['dharma_name_id'])) continue;

                        if (empty($dn['dharma_name_id']) && !empty($dn['custom_name'])) {
                            $matched = DharmaName::where('name', trim($dn['custom_name']))->first();
                            if ($matched) {
                                $dn['dharma_name_id'] = $matched->id;
                                $dn['custom_name'] = null;
                            }
                        }

                        DharmaNameRegistry::create([
                            'registry_id' => $registry->id,
                            'dharma_name_id' => $dn['dharma_name_id'] ?? null,
                            'custom_name' => $dn['custom_name'] ?? null,
                            'obtained_date' => $dn['obtained_date'] ?? null,
                            'remarks' => $dn['remarks'] ?? null,
                            'related_personnel' => $dn['related_personnel'] ?? null
                        ]);
                    }
                }
                $results[] = $registry->id;
            }
            return response()->json(['success' => true, 'count' => count($results)]);
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
