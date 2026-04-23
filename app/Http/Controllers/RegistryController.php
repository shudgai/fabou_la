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
        return Registry::with('dharmaNameRegistries')->orderBy('sort_order', 'asc')->get();
    }

    public function store(Request $request)
    {
        if (!auth()->user()->isChijue() && !auth()->user()->isAdmin()) {
            return response()->json(['error' => '您沒有權限在法寶登記專區新增紀錄'], 403);
        }

        $nameAliasMap = [
            '金容' => '靈果', '金涓' => '靈慧', '金梅' => '靈妙', '金蘭' => '靈智', '金平' => '靈平',
            '金瑞' => '龍戰', '金耀' => '龍勝', '金旭' => '靈心', '金熙' => '靈情', '金吉' => '靈奇',
            '金祥' => '靈傾', '金恩' => '靈昡', '金鈺' => '元續', '金穎' => '赤峰'
        ];

        return DB::transaction(function () use ($request, $nameAliasMap) {
            $cleanName = trim($request->name);
            // Check for existing treasure with same name, master, and category (Cross-date merge)
            $registry = Registry::where('name', $cleanName)
                ->where('master_id', $request->master_id)
                ->where('category', $request->category)
                ->first();

            if (!$registry) {
                $payload = $request->all();
                $payload['name'] = $cleanName; // Ensure name is trimmed in DB
                $registry = Registry::create($payload);
            } else {
                // Update metadata if provided
                $registry->update(array_filter([
                    'acquisition_method' => $request->acquisition_method,
                    'purpose' => $request->purpose,
                    'remarks' => $request->remarks,
                ]));
            }

            if ($request->has('dharma_name_registries')) {
                foreach ($request->input('dharma_name_registries') as $dn) {
                    $custom_name = $dn['custom_name'] ?? null;
                    $remarks = $dn['remarks'] ?? null;
                    $obtained_date = $dn['obtained_date'] ?? null;

                    // 1. 法號翻譯規則
                    if ($custom_name && isset($nameAliasMap[$custom_name])) {
                        $custom_name = $nameAliasMap[$custom_name];
                    }

                    // 4. 親友關係轉備註規則
                    if ($custom_name) {
                        if (preg_match('/^(.*?)(之[夫妻子女左右大小父母兄弟姊姐]|的[夫妻子女左右大小父母兄弟姊姐].*)$/u', $custom_name, $matches)) {
                            $custom_name = $matches[1];
                            $datePrefix = $obtained_date ? (date('Y', strtotime($obtained_date)) - 1911) . date('/m/d', strtotime($obtained_date)) : '';
                            $relationshipRemark = $datePrefix . $matches[0];
                            $remarks = $remarks ? ($remarks . '；' . $relationshipRemark) : $relationshipRemark;
                        }
                    }

                    $dharma_name_id = $dn['dharma_name_id'] ?? null;
                    if (empty($dharma_name_id) && !empty($custom_name)) {
                        $matched = DharmaName::where('name', trim($custom_name))->first();
                        if ($matched) {
                            $dharma_name_id = $matched->id;
                            $custom_name = null;
                        }
                    }

                    // 3. 自動去重：避免同法寶在同一天存入同一個法號
                    $exists = DharmaNameRegistry::where('registry_id', $registry->id)
                        ->where('obtained_date', $obtained_date)
                        ->where(function($q) use ($dharma_name_id, $custom_name) {
                            if ($dharma_name_id) $q->where('dharma_name_id', $dharma_name_id);
                            else $q->where('custom_name', $custom_name);
                        })
                        ->exists();

                    if (!$exists) {
                        DharmaNameRegistry::create([
                            'registry_id' => $registry->id,
                            'dharma_name_id' => $dharma_name_id,
                            'custom_name' => $custom_name,
                            'obtained_date' => $obtained_date,
                            'remarks' => $remarks,
                            'related_personnel' => $dn['related_personnel'] ?? null
                        ]);
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

        /**
         * 行政規則記入：
         * 1. 法號翻譯：金開頭的法號需自動轉換為對應的靈/龍/元字輩法號。
         * 2. 同名合併：若同類別、同仙師、同法寶名稱、同日期，則視為同一筆紀錄。
         * 3. 自動去重：同法寶下，同一個法號（或姓名）在同一天不可重複存檔。
         * 4. 關係識別：若為「XX之母/夫/妻」等，則提取XX為主體，關係記入備註。
         * 5. 求寶方式：識別「求寶：」關鍵字並記入為求寶方式（acquisition_method）。
         */
        $nameAliasMap = [
            '金容' => '靈果', '金涓' => '靈慧', '金梅' => '靈妙', '金蘭' => '靈智', '金平' => '靈平',
            '金瑞' => '龍戰', '金耀' => '龍勝', '金旭' => '靈心', '金熙' => '靈情', '金吉' => '靈奇',
            '金祥' => '靈傾', '金恩' => '靈昡', '金鈺' => '元續', '金穎' => '赤峰'
        ];

        return DB::transaction(function () use ($records, $nameAliasMap) {
            $results = [];
            foreach ($records as $recordData) {
                if (empty($recordData['name']) || empty($recordData['master_id'])) continue;

                $cleanName = trim($recordData['name']);
                // 2. 同名合併邏輯：尋找是否已有同分類、同仙師、同名稱的紀錄（跨日期合併）
                $registry = Registry::where('name', $cleanName)
                    ->where('master_id', $recordData['master_id'])
                    ->where('category', $recordData['category'] ?? 'major')
                    ->first();

                if (!$registry) {
                    $recordData['name'] = $cleanName; // Ensure name is trimmed
                    $registry = Registry::create($recordData);
                } else {
                    // 若已存在，則更新可能變動的欄位（如求寶方式、用途）
                    $registry->update(array_filter([
                        'acquisition_method' => $recordData['acquisition_method'] ?? null,
                        'purpose' => $recordData['purpose'] ?? null,
                        'remarks' => $recordData['remarks'] ?? null,
                    ]));
                }

                if (!empty($recordData['dharma_name_registries'])) {
                    foreach ($recordData['dharma_name_registries'] as $dn) {
                        if (empty($dn['custom_name']) && empty($dn['dharma_name_id'])) continue;

                        $custom_name = $dn['custom_name'] ?? null;
                        $remarks = $dn['remarks'] ?? null;
                        $obtained_date = $dn['obtained_date'] ?? null;
                        
                        // 1. 法號翻譯規則
                        if ($custom_name && isset($nameAliasMap[$custom_name])) {
                            $custom_name = $nameAliasMap[$custom_name];
                        }

                        // 4. 親友關係轉備註規則 (例如: 元續之母)
                        if ($custom_name) {
                            if (preg_match('/^(.*?)(之[夫妻子女左右大小父母兄弟姊姐]|的[夫妻子女左右大小父母兄弟姊姐].*)$/u', $custom_name, $matches)) {
                                $custom_name = $matches[1];
                                $datePrefix = $obtained_date ? (date('Y', strtotime($obtained_date)) - 1911) . date('/m/d', strtotime($obtained_date)) : '';
                                $relationshipRemark = $datePrefix . $matches[0];
                                $remarks = $remarks ? ($remarks . '；' . $relationshipRemark) : $relationshipRemark;
                            }
                        }

                        $dharma_name_id = $dn['dharma_name_id'] ?? null;
                        if (empty($dharma_name_id) && !empty($custom_name)) {
                            $matched = DharmaName::where('name', trim($custom_name))->first();
                            if ($matched) {
                                $dharma_name_id = $matched->id;
                                $custom_name = null;
                            }
                        }

                        // 3. 自動去重：避免同法寶在同一天存入同一個法號
                        $exists = DharmaNameRegistry::where('registry_id', $registry->id)
                            ->where('obtained_date', $obtained_date)
                            ->where(function($q) use ($dharma_name_id, $custom_name) {
                                if ($dharma_name_id) $q->where('dharma_name_id', $dharma_name_id);
                                else $q->where('custom_name', $custom_name);
                            })
                            ->exists();

                        if (!$exists) {
                            DharmaNameRegistry::create([
                                'registry_id' => $registry->id,
                                'dharma_name_id' => $dharma_name_id,
                                'custom_name' => $custom_name,
                                'obtained_date' => $obtained_date,
                                'remarks' => $remarks,
                                'related_personnel' => $dn['related_personnel'] ?? null
                            ]);
                        }
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

    public function reorder(Request $request)
    {
        if (!auth()->user()->isChijue() && !auth()->user()->isAdmin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $orders = $request->input('orders', []);
        foreach ($orders as $order) {
            if (isset($order['id']) && isset($order['sort_order'])) {
                Registry::where('id', $order['id'])->update(['sort_order' => $order['sort_order']]);
            }
        }
        return response()->json(['message' => 'Reordered']);
    }
}
