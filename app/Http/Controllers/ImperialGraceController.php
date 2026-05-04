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
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = ImperialGrace::with('dharmaNameRegistries.dharmaName');

        $query->where('user_id', $user->id);

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('purpose', 'like', "%{$search}%")
                    ->orWhereHas('dharmaNameRegistries', function ($sq) use ($search) {
                        $sq->where('custom_name', 'like', "%{$search}%")
                            ->orWhereHas('dharmaName', function ($ssq) use ($search) {
                                $ssq->where('name', 'like', "%{$search}%");
                            });
                    });
            });
        }

        if ($request->has('master_id')) {
            if ($request->master_id === 'unobtained') {
                $query->where(function ($q) {
                    $q->whereNull('master_id')
                        ->orWhere('status', '未求得');
                });
            } else {
                $query->where('master_id', $request->master_id);
            }
        }

        $direction = filter_var($request->input('sortDesc', true), FILTER_VALIDATE_BOOLEAN) ? 'desc' : 'asc';
        $query->orderBy('sort_order', $direction)
              ->orderBy('id', $direction);

        // Single query: get per-master counts AND unobtained count at once
        $allCounts = ImperialGrace::where('user_id', $user->id)
            ->selectRaw('master_id, status, count(*) as total')
            ->groupBy('master_id', 'status')
            ->get();

        $folderCounts = $allCounts->groupBy('master_id')->map(fn($g) => $g->sum('total'));
        $unobtainedCount = $allCounts->filter(fn($r) => is_null($r->master_id) || $r->status === '未求得')->sum('total');

        return response()->json([
            'registries' => $query->paginate($request->input('per_page', 10)),
            'userGraces' => UserImperialGrace::where('user_id', $user->id)->get(),
            'folderCounts' => $folderCounts,
            'unobtainedCount' => $unobtainedCount
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
        if (ImperialGrace::where('user_id', $user->id)->where('name', $request->name)->exists()) {
            return response()->json(['error' => 'duplicate', 'message' => '此法寶名稱「' . $request->name . '」已存在於系統中。'], 422);
        }

        return DB::transaction(function () use ($request, $user) {
            $grace = ImperialGrace::create([
                'user_id' => $user->id,
                'name' => $request->name,
                'master_id' => $request->master_id,
                'purpose' => $request->purpose,
                'record_date' => $request->record_date,
                'obtained_date' => $request->obtained_date,
                'status' => $request->status ?? '未求得',
                'is_multi' => $request->is_multi ?? false,
                'remarks' => $request->remarks,
            ]);

            if ($request->has('dharma_name_registries')) {
                foreach ($request->input('dharma_name_registries') as $dn) {
                    $dharma_name_id = $dn['dharma_name_id'] ?? null;
                    $custom_name = $dn['custom_name'] ?? null;

                    if (empty($dharma_name_id) && !empty($custom_name)) {
                        // Split name and relative if needed (e.g. "元續之母")
                        if (preg_match('/^(.*?)[之的](.+)$/u', $custom_name, $matches)) {
                            $custom_name = trim($matches[1]);
                            $rel = $this->normalizeRelationshipTerm($matches[2]);
                            $dn['remarks'] = ($dn['remarks'] ?? '') . ($dn['remarks'] ? "\n" : "") . $rel;
                        }

                        $matched = \App\Models\DharmaName::where('name', trim($custom_name))->first();
                        if ($matched) {
                            $dharma_name_id = $matched->id;
                            $custom_name = null;
                        }
                    }

                    DharmaNameRegistry::create([
                        'imperial_grace_id' => $grace->id,
                        'dharma_name_id' => $dharma_name_id,
                        'custom_name' => $custom_name,
                        'obtained_date' => $dn['obtained_date'] ?? null,
                        'status' => $dn['status'] ?? '已求得',
                        'remarks' => $this->normalizeRemarks($dn['remarks'] ?? null),
                        'related_personnel' => $dn['related_personnel'] ?? [],
                    ]);
                }
            }

            return response()->json($grace, 201);
        });
    }

    public function updateRegistry(Request $request, $id)
    {
        $user = auth()->user();
        $grace = ImperialGrace::findOrFail($id);
        if ($grace->user_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if ($request->has('name') && $request->name !== $grace->name) {
            if (ImperialGrace::where('user_id', $user->id)->where('name', $request->name)->where('id', '!=', $id)->exists()) {
                return response()->json(['error' => 'duplicate', 'message' => '名稱已存在'], 422);
            }
        }

        return DB::transaction(function () use ($request, $grace) {
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
                        'dharma_name_id' => $dharma_name_id,
                        'custom_name' => $custom_name,
                        'obtained_date' => $dn['obtained_date'] ?? null,
                        'status' => $dn['status'] ?? '已求得',
                        'remarks' => $this->normalizeRemarks($dn['remarks'] ?? null),
                        'related_personnel' => $dn['related_personnel'] ?? [],
                    ]);
                }
            }

            return response()->json($grace);
        });
    }

    public function batchStoreRegistry(Request $request)
    {
        $items = $request->input('items', []);
        $masterId = $request->input('master_id');
        $userId = auth()->id();
        $results = [];

        return DB::transaction(function () use ($items, $masterId, $userId) {
            foreach ($items as $item) {
                $name = $item['name'] ?? '';
                if (!$name || ImperialGrace::where('user_id', $userId)->where('name', $name)->exists())
                    continue;

                $grace = ImperialGrace::create([
                    'user_id' => $userId,
                    'master_id' => $item['master_id'] ?? $masterId,
                    'name' => $name,
                    'purpose' => $item['purpose'] ?? null,
                    'record_date' => $item['record_date'] ?? null,
                    'obtained_date' => $item['obtained_date'] ?? null,
                    'status' => $item['status'] ?? '已登記',
                    'is_multi' => $item['is_multi'] ?? false,
                    'remarks' => $item['remarks'] ?? null,
                ]);

                if (!empty($item['dharma_name_registries'])) {
                    foreach ($item['dharma_name_registries'] as $dn) {
                        $dharma_name_id = $dn['dharma_name_id'] ?? null;
                        $custom_name = $dn['custom_name'] ?? null;

                        if (empty($dharma_name_id) && !empty($custom_name)) {
                            // Split name and relative if needed (e.g. "元續之母")
                            if (preg_match('/^(.*?)[之的](.+)$/u', $custom_name, $matches)) {
                                $custom_name = trim($matches[1]);
                                $rel = $this->normalizeRelationshipTerm($matches[2]);
                                $dn['remarks'] = ($dn['remarks'] ?? '') . ($dn['remarks'] ? "\n" : "") . $rel;
                            }

                            $matched = \App\Models\DharmaName::where('name', trim($custom_name))->first();
                            if ($matched) {
                                $dharma_name_id = $matched->id;
                                $custom_name = null;
                            }
                        }

                        DharmaNameRegistry::create([
                            'imperial_grace_id' => $grace->id,
                            'dharma_name_id' => $dharma_name_id,
                            'custom_name' => $custom_name,
                            'obtained_date' => $dn['obtained_date'] ?? null,
                            'status' => $dn['status'] ?? '已登記',
                            'remarks' => $this->normalizeRemarks($dn['remarks'] ?? null),
                            'related_personnel' => $dn['related_personnel'] ?? [],
                        ]);
                    }
                }
                $results[] = $grace;
            }
            return response()->json($results, 201);
        });
    }

    public function reorder(Request $request)
    {
        $user = auth()->user();
        $orders = $request->input('orders', []);
        foreach ($orders as $item) {
            ImperialGrace::where('id', $item['id'])
                ->where('user_id', $user->id)
                ->update(['sort_order' => $item['sort_order']]);
        }
        return response()->json(['message' => 'Reordered']);
    }

    public function destroyRegistry($id)
    {
        $user = auth()->user();
        $grace = ImperialGrace::findOrFail($id);
        if ($grace->user_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $grace->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
