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

    /**
     * 將任意格式的備註值正規化為純字串陣列
     */
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

            $registry = Registry::where('name', $cleanName)
                ->where('master_id', $request->master_id)
                ->where('category', $request->category)
                ->first();

            if (!$registry) {
                $registry = Registry::create([
                    'name'               => $cleanName,
                    'master_id'          => $request->master_id,
                    'category'           => $request->category ?? 'major',
                    'purpose'            => $request->purpose,
                    'acquisition_method' => $request->acquisition_method,
                    'remarks'            => $request->remarks,
                    'record_date'        => $request->record_date,
                    'obtained_date'      => $request->obtained_date,
                    'status'             => $request->status ?? '已求得',
                    'sort_order'         => $request->sort_order ?? 0,
                ]);
            } else {
                $registry->update(array_filter([
                    'acquisition_method' => $request->acquisition_method,
                    'purpose'            => $request->purpose,
                    'remarks'            => $request->remarks,
                ]));
            }

            if ($request->has('dharma_name_registries')) {
                foreach ($request->input('dharma_name_registries') as $dn) {
                    $custom_name   = $dn['custom_name'] ?? null;
                    $obtained_date = $dn['obtained_date'] ?? null;

                    // 立刻將 remarks 正規化為純陣列
                    $remarks = $this->normalizeRemarks($dn['remarks'] ?? null);

                    // 1. 法號翻譯規則
                    if ($custom_name && isset($nameAliasMap[$custom_name])) {
                        $custom_name = $nameAliasMap[$custom_name];
                    }

                    // 2. 親友關係轉備註規則（例如：元續之母 或 元續三姑）
                    if ($custom_name) {
                        $relationshipMatch = null;
                        if (preg_match('/^(.*?)([之的].+)$/u', $custom_name, $matches)) {
                            $relationshipMatch = $matches;
                        } else {
                            $dnNames = DharmaName::pluck('name')->toArray();
                            usort($dnNames, fn($a, $b) => mb_strlen($b) - mb_strlen($a));
                            foreach ($dnNames as $dnName) {
                                if (str_starts_with($custom_name, $dnName) && mb_strlen($custom_name) > mb_strlen($dnName)) {
                                    $relationshipMatch = [$custom_name, $dnName, mb_substr($custom_name, mb_strlen($dnName))];
                                    break;
                                }
                            }
                        }

                        if ($relationshipMatch) {
                            $custom_name      = $relationshipMatch[1];
                            $datePrefix       = $obtained_date ? (date('Y', strtotime($obtained_date)) - 1911) . date('/m/d', strtotime($obtained_date)) : '';
                            $relationshipRemark = $datePrefix . $relationshipMatch[0];
                            if (!in_array($relationshipRemark, $remarks)) {
                                $remarks[] = $relationshipRemark;
                            }
                        }
                    }

                    // 3. 嘗試解析 dharma_name_id
                    $dharma_name_id = $dn['dharma_name_id'] ?? null;
                    if (empty($dharma_name_id) && !empty($custom_name)) {
                        $matched = DharmaName::where('name', trim($custom_name))->first();
                        if ($matched) {
                            $dharma_name_id = $matched->id;
                            $custom_name    = null;
                        }
                    }

                    // 4. 合併親友欄位到備註（Mirrored Logic）
                    $relatedArr = is_array($dn['related_personnel'] ?? null) ? $dn['related_personnel'] : [];
                    if (!empty($relatedArr)) {
                        $datePrefix = $obtained_date ? (date('Y', strtotime($obtained_date)) - 1911) . date('/m/d', strtotime($obtained_date)) : '';
                        foreach ($relatedArr as $rel) {
                            $relRemark = $datePrefix . ($custom_name ?: '') . $rel;
                            if (!in_array($relRemark, $remarks)) {
                                $remarks[] = $relRemark;
                            }
                        }
                    }

                    // 5. 查找是否已存在相同人員紀錄（移除日期限制，確保跨日期備註能正確合併到同一格）
                    $existingDnr = DharmaNameRegistry::where('registry_id', $registry->id)
                        ->where(function ($q) use ($dharma_name_id, $custom_name) {
                            if ($dharma_name_id)
                                $q->where('dharma_name_id', $dharma_name_id);
                            else
                                $q->where('custom_name', $custom_name);
                        })
                        ->first();

                    if (!$existingDnr) {
                        DharmaNameRegistry::create([
                            'registry_id'      => $registry->id,
                            'dharma_name_id'   => $dharma_name_id,
                            'custom_name'      => $custom_name,
                            'obtained_date'    => $obtained_date,
                            'remarks'          => $remarks,
                            'related_personnel'=> is_array($dn['related_personnel'] ?? null) ? $dn['related_personnel'] : [],
                        ]);
                    } else {
                        // 若已存在，合併新備註（去除重複）
                        if (!empty($remarks)) {
                            $existingRemarks = $this->normalizeRemarks($existingDnr->remarks);
                            $merged = false;
                            foreach ($remarks as $r) {
                                if ($r !== '' && !in_array($r, $existingRemarks)) {
                                    $existingRemarks[] = $r;
                                    $merged = true;
                                }
                            }
                            if ($merged) {
                                $existingDnr->update(['remarks' => array_values($existingRemarks)]);
                            }
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
                foreach ($request->input('dharma_name_registries') as $dn) {
                    $dharma_name_id = $dn['dharma_name_id'] ?? null;
                    if (empty($dharma_name_id) && !empty($dn['custom_name'])) {
                        $matched = DharmaName::where('name', trim($dn['custom_name']))->first();
                        if ($matched) {
                            $dharma_name_id    = $matched->id;
                            $dn['custom_name'] = null;
                        }
                    }

                    $record = DharmaNameRegistry::updateOrCreate(
                        [
                            'registry_id'    => $registry->id,
                            'dharma_name_id' => $dharma_name_id,
                            'custom_name'    => $dn['custom_name'] ?? null,
                        ],
                        ['obtained_date' => $dn['obtained_date'] ?? null]
                    );

                    if (isset($dn['remarks'])) {
                        $record->update(['remarks' => $this->normalizeRemarks($dn['remarks'])]);
                    }

                    if (isset($dn['related_personnel'])) {
                        $record->update(['related_personnel' => (array)$dn['related_personnel']]);
                    }
                }
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
        if (!is_array($records))
            return response()->json(['error' => '無效的資料格式'], 400);

        $nameAliasMap = [
            '金容' => '靈果', '金涓' => '靈慧', '金梅' => '靈妙', '金蘭' => '靈智', '金平' => '靈平',
            '金瑞' => '龍戰', '金耀' => '龍勝', '金旭' => '靈心', '金熙' => '靈情', '金吉' => '靈奇',
            '金祥' => '靈傾', '金恩' => '靈昡', '金鈺' => '元續', '金穎' => '赤峰'
        ];

        return DB::transaction(function () use ($records, $nameAliasMap) {
            $results = [];
            foreach ($records as $recordData) {
                if (empty($recordData['name']) || empty($recordData['master_id']))
                    continue;

                $cleanName = trim($recordData['name']);
                $registry  = Registry::where('name', $cleanName)
                    ->where('master_id', $recordData['master_id'])
                    ->where('category', $recordData['category'] ?? 'major')
                    ->first();

                if (!$registry) {
                    $recordData['name'] = $cleanName;
                    $registry = Registry::create($recordData);
                } else {
                    $registry->update(array_filter([
                        'acquisition_method' => $recordData['acquisition_method'] ?? null,
                        'purpose'            => $recordData['purpose'] ?? null,
                        'remarks'            => $recordData['remarks'] ?? null,
                    ]));
                }

                if (!empty($recordData['dharma_name_registries'])) {
                    foreach ($recordData['dharma_name_registries'] as $dn) {
                        if (empty($dn['custom_name']) && empty($dn['dharma_name_id']))
                            continue;

                        $custom_name   = $dn['custom_name'] ?? null;
                        $obtained_date = $dn['obtained_date'] ?? null;

                        // 立刻將 remarks 正規化為純陣列
                        $remarks = $this->normalizeRemarks($dn['remarks'] ?? null);

                        // 1. 法號翻譯
                        if ($custom_name && isset($nameAliasMap[$custom_name])) {
                            $custom_name = $nameAliasMap[$custom_name];
                        }

                        // 2. 親友關係轉備註
                        if ($custom_name) {
                            $relationshipMatch = null;
                            if (preg_match('/^(.*?)([之的].+)$/u', $custom_name, $matches)) {
                                $relationshipMatch = $matches;
                            } else {
                                $dnNames = DharmaName::pluck('name')->toArray();
                                usort($dnNames, fn($a, $b) => mb_strlen($b) - mb_strlen($a));
                                foreach ($dnNames as $dnName) {
                                    if (str_starts_with($custom_name, $dnName) && mb_strlen($custom_name) > mb_strlen($dnName)) {
                                        $relationshipMatch = [$custom_name, $dnName, mb_substr($custom_name, mb_strlen($dnName))];
                                        break;
                                    }
                                }
                            }

                            if ($relationshipMatch) {
                                $custom_name        = $relationshipMatch[1];
                                $datePrefix         = $obtained_date ? (date('Y', strtotime($obtained_date)) - 1911) . date('/m/d', strtotime($obtained_date)) : '';
                                $relationshipRemark = $datePrefix . $relationshipMatch[0];
                                if (!in_array($relationshipRemark, $remarks)) {
                                    $remarks[] = $relationshipRemark;
                                }
                            }
                        }

                        $dharma_name_id = $dn['dharma_name_id'] ?? null;
                        if (empty($dharma_name_id) && !empty($custom_name)) {
                            $matched = DharmaName::where('name', trim($custom_name))->first();
                            if ($matched) {
                                $dharma_name_id = $matched->id;
                                $custom_name    = null;
                            }
                        }

                        // 4. 合併親友欄位到備註（Mirrored Logic）
                        $relatedArr = is_array($dn['related_personnel'] ?? null) ? $dn['related_personnel'] : [];
                        if (!empty($relatedArr)) {
                            $datePrefix = $obtained_date ? (date('Y', strtotime($obtained_date)) - 1911) . date('/m/d', strtotime($obtained_date)) : '';
                            foreach ($relatedArr as $rel) {
                                $relRemark = $datePrefix . ($custom_name ?: '') . $rel;
                                if (!in_array($relRemark, $remarks)) {
                                    $remarks[] = $relRemark;
                                }
                            }
                        }

                        // 5. 查找是否已存在相同人員紀錄（移除日期限制，確保跨日期備註能正確合併到同一格）
                        $existingDnr = DharmaNameRegistry::where('registry_id', $registry->id)
                            ->where(function ($q) use ($dharma_name_id, $custom_name) {
                                if ($dharma_name_id)
                                    $q->where('dharma_name_id', $dharma_name_id);
                                else
                                    $q->where('custom_name', $custom_name);
                            })
                            ->first();

                        if (!$existingDnr) {
                            DharmaNameRegistry::create([
                                'registry_id'       => $registry->id,
                                'dharma_name_id'    => $dharma_name_id,
                                'custom_name'       => $custom_name,
                                'obtained_date'     => $obtained_date,
                                'remarks'           => $remarks,
                                'related_personnel' => is_array($dn['related_personnel'] ?? null) ? $dn['related_personnel'] : [],
                            ]);
                        } else {
                            if (!empty($remarks)) {
                                $existingRemarks = $this->normalizeRemarks($existingDnr->remarks);
                                $merged = false;
                                foreach ($remarks as $r) {
                                    if ($r !== '' && !in_array($r, $existingRemarks)) {
                                        $existingRemarks[] = $r;
                                        $merged = true;
                                    }
                                }
                                if ($merged) {
                                    $existingDnr->update(['remarks' => array_values($existingRemarks)]);
                                }
                            }
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

    /**
     * 更新人員備註（Inline 彈窗儲存）
     */
    public function updatePersonnelRemarks(Request $request, $id)
    {
        $dnr = DharmaNameRegistry::findOrFail($id);
        $dnr->update(['remarks' => $this->normalizeRemarks($request->remarks)]);
        return response()->json(['success' => true]);
    }
}
