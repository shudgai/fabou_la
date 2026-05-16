<?php

namespace App\Services;

use App\Models\Teaching;
use Illuminate\Support\Collection;

class TeachingService
{
    public function getAll($masterId = null, $perPage = 15, $search = null, $sortDesc = true)
    {
        $user = auth()->user();
        $query = Teaching::with(['master', 'dharmaNames', 'user']);
        
        if ($masterId === 'daily' || $masterId === 0 || $masterId === '0') {
            $query->where('is_daily', 1);
        } elseif ($masterId) {
            $this->applyMasterGroupFilter($query, $masterId);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('content', 'like', "%{$search}%")
                  ->orWhere('remarks', 'like', "%{$search}%")
                  ->orWhere('target_remarks', 'like', "%{$search}%")
                  ->orWhereHas('dharmaNames', function($sq) use ($search) {
                      $sq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $this->applyVisibilityFilter($query, $user);

        $direction = $sortDesc ? 'desc' : 'asc';
        return $query->orderBy('date', $direction)
            ->orderBy('sort_order', $direction)
            ->orderBy('id', $direction)
            ->paginate($perPage);
    }

    public function getPaginatedDates($masterId = null, $perPage = 15, $sortDesc = true)
    {
        $user = auth()->user();
        $query = Teaching::query();
        
        if ($masterId === 'daily' || $masterId === 0 || $masterId === '0') {
            $query->where('is_daily', 1);
        } elseif ($masterId) {
            $this->applyMasterGroupFilter($query, $masterId);
        }

        $this->applyVisibilityFilter($query, $user);

        
        return $query->selectRaw('date, count(*) as count')
            ->groupBy('date')
            ->orderBy('date', $sortDesc ? 'desc' : 'asc')
            ->paginate($perPage);
    }

    public function getByDate($date, $masterId = null, $sortDesc = true)
    {
        $user = auth()->user();
        $query = Teaching::with(['master', 'dharmaNames', 'user'])
            ->where('date', $date);
            
        if ($masterId === 'daily' || $masterId === 0 || $masterId === '0') {
            $query->where('is_daily', 1);
        } elseif ($masterId) {
            $this->applyMasterGroupFilter($query, $masterId);
        }

        $this->applyVisibilityFilter($query, $user);

        
        $direction = $sortDesc ? 'desc' : 'asc';
        return $query->orderBy('sort_order', $direction)->orderBy('id', $direction)->get();
    }

    public function create(array $data): Teaching
    {
        $user = auth()->user();
        $data = $this->resolveRelations($data);
        
        $nameAliasMap = [
            '金容' => '靈果', '金涓' => '靈慧', '金梅' => '靈妙', '金蘭' => '靈智', '金平' => '靈平',
            '金瑞' => '龍戰', '金耀' => '龍勝', '金旭' => '靈心', '金熹' => '靈情', '金吉' => '靈奇',
            '金祥' => '靈傾', '金恩' => '靈昡', '金鈺' => '元續', '金穎' => '赤峰'
        ];

        // Ensure user_id is set
        $data['user_id'] = $user->id;
        $teaching = Teaching::create($data);

        $dnIds = $data['dharma_name_ids'] ?? [];
        $targetRemarks = $data['target_remarks'] ?? '';
        $remarksList = $this->normalizeRemarks($teaching->remarks);

        // 2. Parentheses Parsing (if name contains parens, e.g. "元續(先生)")
        if ($targetRemarks && preg_match('/^(.*?)\((.*?)\)$/u', $targetRemarks, $m)) {
            if (trim($m[1])) {
                $targetRemarks = trim($m[1]);
                $extra = trim($m[2]);
                if (!in_array($extra, $remarksList)) $remarksList[] = $extra;
            } else {
                $targetRemarks = trim($m[2]);
            }
        }

        // 3. Dharma Name Alias Translation
        if ($targetRemarks && isset($nameAliasMap[$targetRemarks])) {
            $targetRemarks = $nameAliasMap[$targetRemarks];
        }

        // 4. Relationship Translation
        if ($targetRemarks) {
            $relationshipMatch = null;
            if (preg_match('/^(.*?)([之的].+)$/u', $targetRemarks, $matches)) {
                $relationshipMatch = $matches;
            } else {
                $dnNames = \App\Models\DharmaName::pluck('name')->toArray();
                usort($dnNames, fn($a, $b) => mb_strlen($b) - mb_strlen($a));
                foreach ($dnNames as $dnName) {
                    if (str_starts_with($targetRemarks, $dnName) && mb_strlen($targetRemarks) > mb_strlen($dnName)) {
                        $relationshipMatch = [$targetRemarks, $dnName, mb_substr($targetRemarks, mb_strlen($dnName))];
                        break;
                    }
                }
            }

            if ($relationshipMatch) {
                $targetRemarks = $relationshipMatch[1];
                $relRaw = trim($relationshipMatch[2] ?? '');
                $relTranslated = match(true) {
                    $relRaw === '之父' || $relRaw === '父' => '父親',
                    $relRaw === '之母' || $relRaw === '母' => '母親',
                    $relRaw === '之嬤' || $relRaw === '嬤' => '奶奶',
                    $relRaw === '之夫' || $relRaw === '夫' => '先生',
                    default => preg_replace('/^[之的]/u', '', $relRaw),
                };
                $datePrefix = isset($data['date']) ? date('Y/m/d', strtotime($data['date'])) : '';
                $relEntry = $datePrefix ? "{$datePrefix}  " . trim($targetRemarks) . $relTranslated : trim($targetRemarks) . $relTranslated;
                if (!in_array($relEntry, $remarksList)) $remarksList[] = $relEntry;
                $teaching->update(['remarks' => array_values($remarksList)]);
                // Crucially, don't link the actual DharmaName if it's a relationship record for the parent
                $targetRemarks = null; 
            }
        }

        // 5. Sync Dharma Names
        if ($targetRemarks) {
            $matched = \App\Models\DharmaName::where('name', trim($targetRemarks))->first();
            if ($matched) {
                $dnIds[] = $matched->id;
            } else {
                $group = \App\Models\Group::where('user_id', $user->id)
                    ->where('name', trim($targetRemarks))
                    ->with('dharmaNames')
                    ->first();
                if ($group) {
                    $dnIds = array_merge($dnIds, $group->dharmaNames->pluck('id')->toArray());
                }
            }
        }

        if (!empty($dnIds)) {
            $teaching->dharmaNames()->syncWithoutDetaching(array_unique($dnIds));
        }
        
        return $teaching->load('dharmaNames');
    }

    private function normalizeRemarks($raw): array
    {
        if (is_array($raw)) return array_values(array_filter(array_map('trim', $raw), fn($v) => $v !== ''));
        if (is_string($raw) && $raw !== '') return array_values(array_filter(array_map('trim', preg_split('/[\n；;]/u', $raw)), fn($v) => $v !== ''));
        return [];
    }

    public function findById(int $id): ?Teaching
    {
        $user = auth()->user();
        return Teaching::with(['master', 'dharmaNames', 'user'])
            ->where(function($q) use ($user) {
                $this->applyVisibilityFilter($q, $user);
            })
            ->find($id);
    }

    public function update(int $id, array $data): bool
    {
        $teaching = Teaching::find($id);
        if (!$teaching) return false;
        
        $dnIds = $data['dharma_name_ids'] ?? [];
        $data = $this->resolveRelations($data);
        $success = $teaching->update($data);
        
        if ($success) {
            $teaching->dharmaNames()->sync($dnIds);
        }
        
        return $success;
    }

    public function delete(int $id): bool
    {
        $teaching = Teaching::find($id);
        if (!$teaching) return false;
        return $teaching->delete();
    }

    public function reorder(array $orders): bool
    {
        $userId = auth()->id();
        foreach ($orders as $order) {
            if (isset($order['id']) && isset($order['sort_order'])) {
                Teaching::where('id', $order['id'])
                    ->where('user_id', $userId)
                    ->update(['sort_order' => $order['sort_order']]);
            }
        }
        return true;
    }

    protected function resolveRelations(array $data): array
    {
        // Resolve Master ID if it's a string name
        if (isset($data['master_name']) && !empty($data['master_name'])) {
            $master = \App\Models\Master::where('name', $data['master_name'])->first();
            if ($master) {
                $data['master_id'] = $master->id;
            }
        }

        return $data;
    }

    public function canModify(int $id, int $userId): bool
    {
        $teaching = Teaching::find($id);
        if (!$teaching) return false;
        
        return $teaching->user_id === $userId;
    }

    /**
     * Get specialized rules for magic items in Father Emperor's teaching.
     * Centralized backend rule for item types and their display fields.
     */
    public function getSpecializedRules()
    {
        return [
            [
                'match' => '丹',
                'fields' => ['eat' => '吃', 'wash' => '洗', 'days' => '天份'],
                'color' => 'indigo'
            ],
            [
                'match' => '太令',
                'fields' => ['days' => '天數'],
                'color' => 'amber'
            ],
            [
                'match' => '香環',
                'fields' => ['qty' => '個', 'box' => '盒'],
                'color' => 'rose'
            ],
            [
                'match' => '福祿香',
                'fields' => ['qty' => '根', 'box' => '包'],
                'color' => 'slate'
            ],
            [
                'match' => '令',
                'exclude' => '專司靈療',
                'fields' => ['size' => '尺寸', 'days' => '天數'],
                'color' => 'amber'
            ],
            [
                'match' => '符',
                'fields' => ['size' => '尺寸', 'days' => '天數'],
                'color' => 'amber'
            ],
            [
                'match' => '疏文',
                'fields' => ['person' => '開立', 'days' => '天數'],
                'color' => 'slate'
            ],
            [
                'match' => '專司靈療',
                'fields' => ['days' => '天份'],
                'color' => 'emerald'
            ],
            [
                'match' => '香灰',
                'fields' => ['days' => '天份'],
                'color' => 'slate'
            ],
            [
                'match' => '執法',
                'fields' => ['person' => '執法', 'days' => '天份'],
                'color' => 'slate'
            ],
            [
                'match' => '清煞',
                'fields' => ['person' => '執法', 'days' => '天份'],
                'color' => 'slate'
            ],
            [
                'match' => '*', // Default fallback
                'fields' => ['days' => '天份'],
                'color' => 'slate'
            ]
        ];
    }

    public function isSpecializedItem(string $name): bool
    {
        foreach ($this->getSpecializedRules() as $rule) {
            if ($rule['match'] === '*') continue;
            if (mb_strpos($name, $rule['match']) !== false) {
                if (isset($rule['exclude']) && mb_strpos($name, $rule['exclude']) !== false) continue;
                return true;
            }
        }
        return false;
    }

    protected function applyVisibilityFilter($query, $user)
    {
        $query->where(function($q) use ($user) {
            $q->where('user_id', $user->id);
            if ($user->dharma_name_id) {
                $q->orWhereHas('dharmaNames', function($sq) use ($user) {
                    $sq->where('dharma_names.id', $user->dharma_name_id);
                });
            }
        });
    }

    protected function applyMasterGroupFilter($query, $masterId)
    {
        $keywords = [
            1 => ['老', '祖'],
            2 => ['元'],
            3 => ['道'],
            4 => ['靈'],
            6 => ['宰'],
            7 => ['龍'],
        ];

        if (isset($keywords[$masterId])) {
            $k = $keywords[$masterId];
            $query->whereHas('master', function ($q) use ($k, $masterId) {
                $q->where(function($sq) use ($k, $masterId) {
                    $sq->where('masters.id', $masterId);
                    foreach ($k as $word) {
                        $sq->orWhere('masters.name', 'like', '%' . $word . '%');
                    }
                });
            });
        } else {
            $query->where('teachings.master_id', $masterId);
        }
    }
}
