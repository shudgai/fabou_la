<?php

namespace App\Services;

use App\Models\Grudge;
use Illuminate\Support\Collection;

class GrudgeService
{
    public function getAll(): Collection
    {
        $user = auth()->user();
        $query = Grudge::with(['user'])->latest();

        if (!$user->isAdmin()) {
            $permissions = $user->getPermissions();
            $allowedArmies = $permissions['allowed_armies'] ?? [];
            
            $query->where(function($q) use ($user, $allowedArmies) {
                // Own records
                $q->where('user_id', $user->id);
                
                // Linkage via army destination
                if (!empty($allowedArmies)) {
                    $q->orWhereIn('destination', $allowedArmies);
                    // Also check for partial matches in destination summary
                    foreach($allowedArmies as $army) {
                        $q->orWhere('destination', 'like', '%' . $army . '%');
                    }
                }
            });
        }

        return $query->get();
    }

    public function create(array $data): Grudge
    {
        $data['user_id'] = auth()->id();
        return Grudge::create($data);
    }

    public function batchCreate(array $items): Collection
    {
        $results = collect();
        $userId = auth()->id();
        
        foreach ($items as $item) {
            try {
                $item['user_id'] = $userId;
                
                // 1. Terminology Standardize: 大姐/大姊 -> 閻尊, 四妹 -> 閻闇
                $rawRemarks = $item['remarks_text'] ?? '';
                $rawRemarks = str_replace(['大姐', '大姊'], '閻尊', $rawRemarks);
                $rawRemarks = str_replace('四妹', '閻闇', $rawRemarks);
                $item['remarks_text'] = $rawRemarks;

                $item['user_name'] = str_replace(['大姐', '大姊'], '閻尊', $item['user_name'] ?? '');
                $item['user_name'] = str_replace('四妹', '閻闇', $item['user_name']);

                // 2. Result & Destination Logic
                $parts = preg_split('/[、,，]\s*(?![^（(]*[）)])/u', $rawRemarks);
                $parts = array_values(array_filter(array_map('trim', $parts)));
                $dests = ['虎賁軍', '虎甲軍', '黑曜軍', '耀紫軍', '九天', '暫時驅離', '殲滅'];
                
                if (count($parts) === 1) {
                    $isKnown = false;
                    foreach ($dests as $d) {
                        if (str_contains($parts[0], $d)) { $isKnown = true; break; }
                    }
                    if ($isKnown) {
                        $item['destination'] = $parts[0];
                        $item['remarks_text'] = '';
                    } else {
                        $item['destination'] = '已處理';
                        $item['remarks_text'] = $parts[0];
                    }
                } elseif (count($parts) >= 2) {
                    $item['destination'] = '多項處理';
                    // remarks_text stays as the raw content
                } else {
                    $item['destination'] = '已處理';
                }

                // 3. Status Logic
                if (str_contains($rawRemarks, '未處理') || str_contains($item['user_name'], '未處理')) {
                    $item['status'] = '待處理';
                    $item['process_date'] = null;
                } else {
                    $item['status'] = '已處理';
                    $item['process_date'] = $item['know_date'] ?? now()->toDateString();
                }

                // 4. Count Categorization
                $mergedRemarks = ['yan_zun' => 0, 'yan_an' => 0, 'long_sheng' => 0, 'long_zhan' => 0];
                $searchSource = $rawRemarks;
                
                preg_match_all('/(.*?)[（(](.*?)[）)]/u', $searchSource, $matches, PREG_SET_ORDER);
                foreach ($matches as $m) {
                    $content = $m[2];
                    $subParts = preg_split('/[、,，]/u', $content);
                    foreach ($subParts as $sp) {
                        preg_match('/(\d+)/', $sp, $nMatch);
                        // Support "全數" or specific number
                        $n = isset($nMatch[1]) ? (int)$nMatch[1] : (str_contains($sp, '全數') ? ($item['quantity'] ?? 0) : 0);
                        if (str_contains($sp, '閻尊')) $mergedRemarks['yan_zun'] += $n;
                        if (str_contains($sp, '閻闇')) $mergedRemarks['yan_an'] += $n;
                        if (str_contains($sp, '勝') || str_contains($sp, '龍勝')) $mergedRemarks['long_sheng'] += $n;
                        if (str_contains($sp, '戰') || str_contains($sp, '龍戰')) $mergedRemarks['long_zhan'] += $n;
                    }
                }
                
                // Fallback for Black/Purple Army without specific parenthetical breakdown
                $hasDestMatch = false;
                foreach (['黑曜軍', '耀紫軍', '多項處理'] as $check) {
                    if (str_contains($item['destination'] ?? '', $check)) { $hasDestMatch = true; break; }
                }
                if ($hasDestMatch && $mergedRemarks['yan_zun'] === 0 && $mergedRemarks['yan_an'] === 0) {
                    $q = $item['quantity'] ?? 1;
                    if (str_contains($searchSource, '閻尊')) $mergedRemarks['yan_zun'] += $q;
                    if (str_contains($searchSource, '閻闇')) $mergedRemarks['yan_an'] += $q;
                }
                $item['remarks'] = $mergedRemarks;

                // 5. Upsert or Create
                if ($item['status'] !== '待處理') {
                    $pending = Grudge::where('user_name', $item['user_name'])
                        ->where('status', '待處理')
                        ->latest()->first();
                    if ($pending) {
                        $pending->update($item);
                        $results->push($pending);
                        continue;
                    }
                }
                $results->push(Grudge::create($item));
                
            } catch (\Exception $e) {
                \Log::error("Grudge Batch Import Error for item: " . json_encode($item) . " - " . $e->getMessage());
                throw $e;
            }
        }
        return $results;
    }

    public function findById(int $id): ?Grudge
    {
        return Grudge::find($id);
    }

    public function update(int $id, array $data): bool
    {
        $grudge = $this->findById($id);
        if (!$grudge) return false;
        return $grudge->update($data);
    }

    public function delete(int $id): bool
    {
        $grudge = $this->findById($id);
        if (!$grudge) return false;
        return $grudge->delete();
    }
}
