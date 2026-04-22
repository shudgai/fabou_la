<?php

namespace App\Services;

use App\Models\Teaching;
use Illuminate\Support\Collection;

class TeachingService
{
    public function getAll($masterId = null, $perPage = 15)
    {
        $query = Teaching::with(['master', 'dharmaNames', 'user']);
        
        if ($masterId === 'daily' || $masterId === 0 || $masterId === '0') {
            // Show all records
        } elseif ($masterId) {
            $this->applyMasterGroupFilter($query, $masterId);
        }
        
        return $query->latest('date')->orderBy('sort_order', 'asc')->oldest('id')->paginate($perPage);
    }

    public function getPaginatedDates($masterId = null, $perPage = 15)
    {
        $query = Teaching::query();
        
        if ($masterId === 'daily' || $masterId === 0 || $masterId === '0') {
            // Show all dates for the daily/master dashboard
        } elseif ($masterId) {
            $this->applyMasterGroupFilter($query, $masterId);
        }
        
        return $query->leftJoin('teaching_dharma_name', 'teachings.id', '=', 'teaching_dharma_name.teaching_id')
            ->selectRaw('teachings.date, count(*) as count')
            ->groupBy('teachings.date')
            ->orderBy('teachings.date', 'desc')
            ->paginate($perPage);
    }

    public function getByDate($date, $masterId = null)
    {
        $query = Teaching::with(['master', 'dharmaNames', 'user'])
            ->where('date', $date);
            
        if ($masterId === 'daily' || $masterId === 0 || $masterId === '0') {
            // No extra master filter for daily
        } elseif ($masterId) {
            $this->applyMasterGroupFilter($query, $masterId);
        }
        
        return $query->orderBy('sort_order', 'asc')->oldest()->get();
    }

    public function create(array $data): Teaching
    {
        $dnIds = $data['dharma_name_ids'] ?? [];
        $data = $this->resolveRelations($data);
        
        // Force set user_id to current authenticated user
        $data['user_id'] = auth()->id();
        
        $teaching = Teaching::create($data);
        
        if (!empty($dnIds)) {
            $teaching->dharmaNames()->sync($dnIds);
        }
        
        return $teaching->load('dharmaNames');
    }

    public function findById(int $id): ?Teaching
    {
        return Teaching::with(['master', 'dharmaNames', 'user'])->find($id);
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
        foreach ($orders as $order) {
            if (isset($order['id']) && isset($order['sort_order'])) {
                Teaching::where('id', $order['id'])->update(['sort_order' => $order['sort_order']]);
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
        
        // Admins can modify everything (optional but standard)
        // If the user wants STRICT only-owner, I'll stick to that.
        // Let's check user isAdmin() if exists. 
        $user = \App\Models\User::find($userId);
        if ($user && $user->isAdmin()) return true;

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

    /**
     * Helper to check if an item name matches any specialized rule.
     */
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
