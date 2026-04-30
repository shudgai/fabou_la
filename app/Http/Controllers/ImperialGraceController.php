<?php

namespace App\Http\Controllers;

use App\Models\ImperialGrace;
use App\Models\UserImperialGrace;
use App\Models\DharmaNameRegistry;
use App\Models\DharmaName;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ImperialGraceController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $query = ImperialGrace::with('dharmaNameRegistries.dharmaName');

        if (!$user->isAdmin()) {
            $query->where('user_id', $user->id);
        }

        return response()->json([
            'registries' => $query->get(),
            'userGraces' => UserImperialGrace::where('user_id', $user->id)->get()
        ]);
    }

    private function normalizeRemarks($raw): array
    {
        if (is_array($raw)) {
            return array_values(array_filter(array_map('trim', $raw), fn($v) => $v !== ''));
        }
        if (is_string($raw) && $raw !== '') {
            return array_values(array_filter(array_map('trim', preg_split('/[\n；;]/u', $raw)), fn($v) => $v !== ''));
        }
        return [];
    }

    public function storeRegistry(Request $request)
    {
        $user = auth()->user();
        if (ImperialGrace::where('name', $request->name)->exists()) {
            return response()->json(['error' => 'duplicate', 'message' => '此法寶名稱「' . $request->name . '」已存在於系統中。'], 422);
        }

        return DB::transaction(function() use ($request, $user) {
            $grace = ImperialGrace::create([
                'user_id'       => $user->id,
                'name'          => $request->name,
                'master_id'     => $request->master_id,
                'purpose'       => $request->purpose,
                'record_date'   => $request->record_date,
                'obtained_date' => $request->obtained_date,
                'status'        => $request->status ?? '未求得',
                'is_multi'      => $request->is_multi ?? false,
                'remarks'       => $request->remarks,
            ]);

            if ($request->has('dharma_name_registries')) {
                foreach ($request->input('dharma_name_registries') as $dn) {
                    $dharma_name_id = $dn['dharma_name_id'] ?? null;
                    $custom_name = $dn['custom_name'] ?? null;
                    
                    if (empty($dharma_name_id) && !empty($custom_name)) {
                        $matched = \App\Models\DharmaName::where('name', trim($custom_name))->first();
                        if ($matched) {
                            $dharma_name_id = $matched->id;
                            $custom_name = null;
                        }
                    }

                    DharmaNameRegistry::create([
                        'imperial_grace_id' => $grace->id,
                        'dharma_name_id'    => $dharma_name_id,
                        'custom_name'       => $custom_name,
                        'obtained_date'     => $dn['obtained_date'] ?? null,
                        'status'            => $dn['status'] ?? '已求得',
                        'remarks'           => $this->normalizeRemarks($dn['remarks'] ?? null),
                        'related_personnel' => $dn['related_personnel'] ?? [],
                    ]);
                }
            }

            return response()->json($grace, 201);
        });
    }

    public function updateRegistry(Request $request, $id)
    {
        $grace = ImperialGrace::findOrFail($id);
        
        if ($request->has('name') && $request->name !== $grace->name) {
            if (ImperialGrace::where('name', $request->name)->where('id', '!=', $id)->exists()) {
                return response()->json(['error' => 'duplicate', 'message' => '名稱已存在'], 422);
            }
        }

        return DB::transaction(function() use ($request, $grace) {
            $grace->update($request->all());

            if ($request->has('dharma_name_registries')) {
                $grace->dharmaNameRegistries()->delete();
                foreach ($request->input('dharma_name_registries') as $dn) {
                    $dharma_name_id = $dn['dharma_name_id'] ?? null;
                    $custom_name = $dn['custom_name'] ?? null;
                    
                    if (empty($dharma_name_id) && !empty($custom_name)) {
                        $matched = \App\Models\DharmaName::where('name', trim($custom_name))->first();
                        if ($matched) {
                            $dharma_name_id = $matched->id;
                            $custom_name = null;
                        }
                    }

                    DharmaNameRegistry::create([
                        'imperial_grace_id' => $grace->id,
                        'dharma_name_id'    => $dharma_name_id,
                        'custom_name'       => $custom_name,
                        'obtained_date'     => $dn['obtained_date'] ?? null,
                        'status'            => $dn['status'] ?? '已求得',
                        'remarks'           => $this->normalizeRemarks($dn['remarks'] ?? null),
                        'related_personnel' => $dn['related_personnel'] ?? [],
                    ]);
                }
            }

            return response()->json($grace);
        });
    }

    public function batchStoreRegistry(Request $request)
    {
        // Keep simple for batch
        $items = $request->input('items', []);
        $masterId = $request->input('master_id');
        $userId = auth()->id();
        $created = [];

        foreach ($items as $item) {
            $name = $item['name'] ?? '';
            if (!$name || ImperialGrace::where('name', $name)->exists()) continue;
            
            $created[] = ImperialGrace::create(array_merge([
                'user_id' => $userId,
                'master_id' => $item['master_id'] ?? $masterId,
                'status' => '已登記',
            ], $item));
        }
        return response()->json($created, 201);
    }

    public function reorder(Request $request)
    {
        $orders = $request->input('orders', []);
        foreach ($orders as $item) {
            ImperialGrace::where('id', $item['id'])->update(['sort_order' => $item['sort_order']]);
        }
        return response()->json(['message' => 'Reordered']);
    }

    public function destroyRegistry($id)
    {
        ImperialGrace::destroy($id);
        return response()->json(['message' => 'Deleted']);
    }
}
