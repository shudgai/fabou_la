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
        $query->orderByRaw("FIELD(status, '未求得', '已求得', '已登記') ASC")
              ->orderBy('sort_order', $direction)
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
        $nameAliasMap = [
            '金容' => '靈果', '金涓' => '靈慧', '金梅' => '靈妙', '金蘭' => '靈智', '金平' => '靈平',
            '金瑞' => '龍戰', '金耀' => '龍勝', '金旭' => '靈心', '金熹' => '靈情', '金吉' => '靈奇',
            '金祥' => '靈傾', '金恩' => '靈昡', '金鈺' => '元續', '金穎' => '赤峰'
        ];

        return DB::transaction(function () use ($request, $nameAliasMap, $user) {
            $cleanName = trim($request->name);
            $grace = ImperialGrace::where('user_id', $user->id)
                ->where('name', $cleanName)
                ->first();

            $allowedDuplicates = ['法宗', '真氣', '親收女兒'];
            if ($grace && !in_array($cleanName, $allowedDuplicates)) {
                $masterName = \App\Models\Master::find($grace->master_id)?->name ?? '未求得';
                throw new \Exception("「{$cleanName}」已存在於【{$masterName}】資料夾中，不可重複載錄。");
            }

            if (!$grace || in_array($cleanName, $allowedDuplicates)) {
                $grace = ImperialGrace::create([
                    'user_id' => $user->id,
                    'name' => $cleanName,
                    'master_id' => $request->master_id,
                    'purpose' => $request->purpose,
                    'record_date' => $request->record_date,
                    'obtained_date' => $request->obtained_date,
                    'status' => $request->status ?? '未求得',
                    'is_multi' => $request->is_multi ?? false,
                    'remarks' => $request->remarks,
                ]);
            } else {
                $grace->update(array_filter([
                    'purpose' => $request->purpose,
                    'remarks' => $request->remarks,
                ]));
            }

            if ($request->has('dharma_name_registries')) {
                foreach ($request->input('dharma_name_registries') as $dn) {
                    $dharma_name_id = $dn['dharma_name_id'] ?? null;
                    $custom_name = $dn['custom_name'] ?? null;
                    $obtained_date = $dn['obtained_date'] ?? null;
                    $remarks = $this->normalizeRemarks($dn['remarks'] ?? null);

                    // 1. 處理括號規則 (Parentheses Rule)
                    if ($custom_name) {
                        if (preg_match('/^(.*?)\((.*?)\)$/u', $custom_name, $m)) {
                            if (trim($m[1])) {
                                $custom_name = trim($m[1]);
                                $extra = trim($m[2]);
                                if (!in_array($extra, $remarks)) $remarks[] = $extra;
                            } else {
                                $custom_name = trim($m[2]);
                            }
                        }
                    }

                    // 2. 法號翻譯規則
                    if ($custom_name && isset($nameAliasMap[$custom_name])) {
                        $custom_name = $nameAliasMap[$custom_name];
                    }

                    // 3. 親友關係轉備註規則
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
                            $custom_name = $relationshipMatch[1];
                            $relRaw = trim($relationshipMatch[2] ?? '');
                            $relTranslated = match(true) {
                                $relRaw === '之父' || $relRaw === '父' => '父親',
                                $relRaw === '之母' || $relRaw === '母' => '母親',
                                $relRaw === '之嬤' || $relRaw === '嬤' => '奶奶',
                                $relRaw === '之夫' || $relRaw === '夫' => '先生',
                                default => preg_replace('/^[之的]/u', '', $relRaw),
                            };
                            $datePrefix = $obtained_date ? date('Y/m/d', strtotime($obtained_date)) : '';
                            $nameOnly = trim($custom_name);
                            $relEntry = $datePrefix ? "{$datePrefix}  {$nameOnly}{$relTranslated}" : "{$nameOnly}{$relTranslated}";
                            if (!in_array($relEntry, $remarks)) {
                                $remarks[] = $relEntry;
                            }
                            $obtained_date = null;
                        }
                    }

                    if (empty($dharma_name_id) && !empty($custom_name)) {
                        $matched = DharmaName::where('name', trim($custom_name))->first();
                        if ($matched) {
                            $dharma_name_id = $matched->id;
                            $custom_name = null;
                        }
                    }

                    // 4. 合併或建立人員紀錄
                    $existingDnr = DharmaNameRegistry::where('imperial_grace_id', $grace->id)
                        ->where(function ($q) use ($dharma_name_id, $custom_name) {
                            if ($dharma_name_id) $q->where('dharma_name_id', $dharma_name_id);
                            else $q->where('custom_name', $custom_name);
                        })->first();

                    if (!$existingDnr) {
                        DharmaNameRegistry::create([
                            'imperial_grace_id' => $grace->id,
                            'dharma_name_id' => $dharma_name_id,
                            'custom_name' => $custom_name,
                            'obtained_date' => $obtained_date,
                            'status' => $dn['status'] ?? '已求得',
                            'remarks' => $remarks,
                            'related_personnel' => $dn['related_personnel'] ?? [],
                        ]);
                    } else {
                        $updates = [];
                        if ($obtained_date) $updates['obtained_date'] = $obtained_date;
                        if (!empty($remarks)) {
                            $existingRemarks = $this->normalizeRemarks($existingDnr->remarks);
                            $merged = false;
                            foreach ($remarks as $r) {
                                if ($r !== '' && !in_array($r, $existingRemarks)) {
                                    $existingRemarks[] = $r;
                                    $merged = true;
                                }
                            }
                            if ($merged) $updates['remarks'] = array_values($existingRemarks);
                        }
                        if (!empty($updates)) $existingDnr->update($updates);
                    }
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
            $cleanName = trim($request->name);
            $allowedDuplicates = ['法宗', '真氣', '親收女兒'];
            $exists = ImperialGrace::where('user_id', $user->id)
                ->where('name', $cleanName)
                ->where('id', '!=', $id)
                ->first();
            
            if ($exists && !in_array($cleanName, $allowedDuplicates)) {
                $masterName = \App\Models\Master::find($exists->master_id)?->name ?? '未求得';
                return response()->json(['error' => 'duplicate', 'message' => "「{$cleanName}」已存在於【{$masterName}】資料夾中，不可重複載錄。"], 422);
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
        $user = auth()->user();
        $nameAliasMap = [
            '金容' => '靈果', '金涓' => '靈慧', '金梅' => '靈妙', '金蘭' => '靈智', '金平' => '靈平',
            '金瑞' => '龍戰', '金耀' => '龍勝', '金旭' => '靈心', '金熹' => '靈情', '金吉' => '靈奇',
            '金祥' => '靈傾', '金恩' => '靈昡', '金鈺' => '元續', '金穎' => '赤峰'
        ];

        return DB::transaction(function () use ($items, $masterId, $user, $nameAliasMap) {
            foreach ($items as $item) {
                $cleanName = trim($item['name'] ?? '');
                if (!$cleanName) continue;

                $grace = ImperialGrace::where('user_id', $user->id)
                    ->where('name', $cleanName)
                    ->first();

                $allowedDuplicates = ['法宗', '真氣', '親收女兒'];
                if ($grace && !in_array($cleanName, $allowedDuplicates)) {
                    continue; // Skip duplicates for batch
                }

                if (!$grace || in_array($cleanName, $allowedDuplicates)) {
                    $grace = ImperialGrace::create([
                        'user_id' => $user->id,
                        'master_id' => $item['master_id'] ?? $masterId,
                        'name' => $cleanName,
                        'purpose' => $item['purpose'] ?? null,
                        'record_date' => $item['record_date'] ?? null,
                        'obtained_date' => $item['obtained_date'] ?? null,
                        'status' => $item['status'] ?? '已登記',
                        'is_multi' => $item['is_multi'] ?? false,
                        'remarks' => $item['remarks'] ?? null,
                    ]);
                } else {
                    $grace->update(array_filter([
                        'purpose' => $item['purpose'] ?? null,
                        'remarks' => $item['remarks'] ?? null,
                    ]));
                }

                if (!empty($item['dharma_name_registries'])) {
                    foreach ($item['dharma_name_registries'] as $dn) {
                        $dharma_name_id = $dn['dharma_name_id'] ?? null;
                        $custom_name = $dn['custom_name'] ?? null;
                        $obtained_date = $dn['obtained_date'] ?? null;
                        $remarks = $this->normalizeRemarks($dn['remarks'] ?? null);

                        // 1. 處理括號規則
                        if ($custom_name) {
                            if (preg_match('/^(.*?)\((.*?)\)$/u', $custom_name, $m)) {
                                if (trim($m[1])) {
                                    $custom_name = trim($m[1]);
                                    $extra = trim($m[2]);
                                    if (!in_array($extra, $remarks)) $remarks[] = $extra;
                                } else {
                                    $custom_name = trim($m[2]);
                                }
                            }
                        }

                        // 2. 法號翻譯
                        if ($custom_name && isset($nameAliasMap[$custom_name])) {
                            $custom_name = $nameAliasMap[$custom_name];
                        }

                        // 3. 親友關係
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
                                $custom_name = $relationshipMatch[1];
                                $relRaw = trim($relationshipMatch[2] ?? '');
                                $relTranslated = match(true) {
                                    $relRaw === '之父' || $relRaw === '父' => '父親',
                                    $relRaw === '之母' || $relRaw === '母' => '母親',
                                    $relRaw === '之嬤' || $relRaw === '嬤' => '奶奶',
                                    $relRaw === '之夫' || $relRaw === '夫' => '先生',
                                    default => preg_replace('/^[之的]/u', '', $relRaw),
                                };
                                $datePrefix = $obtained_date ? date('Y/m/d', strtotime($obtained_date)) : '';
                                $nameOnly = trim($custom_name);
                                $relEntry = $datePrefix ? "{$datePrefix}  {$nameOnly}{$relTranslated}" : "{$nameOnly}{$relTranslated}";
                                if (!in_array($relEntry, $remarks)) $remarks[] = $relEntry;
                                $obtained_date = null;
                            }
                        }

                        if (empty($dharma_name_id) && !empty($custom_name)) {
                            $matched = DharmaName::where('name', trim($custom_name))->first();
                            if ($matched) {
                                $dharma_name_id = $matched->id;
                                $custom_name = null;
                            }
                        }

                        $existingDnr = DharmaNameRegistry::where('imperial_grace_id', $grace->id)
                            ->where(function ($q) use ($dharma_name_id, $custom_name) {
                                if ($dharma_name_id) $q->where('dharma_name_id', $dharma_name_id);
                                else $q->where('custom_name', $custom_name);
                            })->first();

                        if (!$existingDnr) {
                            DharmaNameRegistry::create([
                                'imperial_grace_id' => $grace->id,
                                'dharma_name_id' => $dharma_name_id,
                                'custom_name' => $custom_name,
                                'obtained_date' => $obtained_date,
                                'status' => $dn['status'] ?? '已登記',
                                'remarks' => $remarks,
                                'related_personnel' => $dn['related_personnel'] ?? [],
                            ]);
                        } else {
                            $updates = [];
                            if ($obtained_date) $updates['obtained_date'] = $obtained_date;
                            if (!empty($remarks)) {
                                $existingRemarks = $this->normalizeRemarks($existingDnr->remarks);
                                $merged = false;
                                foreach ($remarks as $r) {
                                    if ($r !== '' && !in_array($r, $existingRemarks)) {
                                        $existingRemarks[] = $r;
                                        $merged = true;
                                    }
                                }
                                if ($merged) $updates['remarks'] = array_values($existingRemarks);
                            }
                            if (!empty($updates)) $existingDnr->update($updates);
                        }
                    }
                }
            }
            return response()->json(['success' => true]);
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
        return response()->json(['message' => '已刪除']);
    }
}
