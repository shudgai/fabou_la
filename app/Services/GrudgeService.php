<?php

namespace App\Services;

use App\Models\Grudge;
use Illuminate\Support\Collection;

class GrudgeService
{
    public function getAll(array $filters = [])
    {
        $user = auth()->user();
        $query = Grudge::with(['user'])
            ->where('user_id', $user->id);

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('user_name', 'like', "%{$search}%")
                  ->orWhere('user_remarks', 'like', "%{$search}%")
                  ->orWhere('remarks_text', 'like', "%{$search}%");
            });
        }

        if (isset($filters['know_date'])) {
            if (empty($filters['know_date']) || $filters['know_date'] === 'null') {
                $query->whereNull('know_date');
            } else {
                $query->where('know_date', $filters['know_date']);
            }
        }

        // Calculate global totals - relying on database SUM which returns string for DECIMAL
        $globalTotalQuantity = $query->sum('quantity') ?: "0";
        
        // Sum JSON fields (Breakdowns) globally
        // Since it's JSON, we can use JSON_EXTRACT or just fetch and sum in PHP
        // For performance and precision with our specific logic (legacy fallbacks), PHP is safer for now
        $allRecords = $query->get(['remarks', 'destination', 'quantity']);
        $globalBreakdowns = $allRecords->reduce(function($acc, $i) {
            $r = is_string($i->remarks) ? json_decode($i->remarks, true) : $i->remarks;
            if (!is_array($r)) $r = [];
            
            $acc['yan_zun'] = bcadd($acc['yan_zun'], (string)($r['yan_zun'] ?? 0));
            $acc['yan_an'] = bcadd($acc['yan_an'], (string)($r['yan_an'] ?? 0));
            $acc['long_sheng'] = bcadd($acc['long_sheng'], (string)($r['long_sheng'] ?? 0));
            $acc['long_zhan'] = bcadd($acc['long_zhan'], (string)($r['long_zhan'] ?? 0));
            $acc['yan_jue'] = bcadd($acc['yan_jue'], (string)($r['yan_jue'] ?? 0));
            $acc['yan_ze'] = bcadd($acc['yan_ze'], (string)($r['yan_ze'] ?? 0));
            $acc['yan_di'] = bcadd($acc['yan_di'], (string)($r['yan_di'] ?? 0));
            $acc['yan_yuan'] = bcadd($acc['yan_yuan'], (string)($r['yan_yuan'] ?? 0));
            
            // Legacy fallbacks
            if ($i->destination === '虎甲軍') $acc['yan_jue'] = bcadd($acc['yan_jue'], (string)$i->quantity);
            if ($i->destination === '虎賁軍') $acc['yan_di'] = bcadd($acc['yan_di'], (string)$i->quantity);
            
            return $acc;
        }, ['yan_zun'=>'0','yan_an'=>'0','long_sheng'=>'0','long_zhan'=>'0','yan_jue'=>'0','yan_ze'=>'0','yan_di'=>'0','yan_yuan'=>'0']);

        $results = $query->latest()->paginate(10);
        
        return [
            'paginator' => $results,
            'global_total_quantity' => $globalTotalQuantity,
            'global_breakdowns' => $globalBreakdowns
        ];
    }

    public function dateGroups(array $filters = [])
    {
        $user = auth()->user();
        $query = Grudge::where('user_id', $user->id);

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('user_name', 'like', "%{$search}%")
                  ->orWhere('user_remarks', 'like', "%{$search}%")
                  ->orWhere('remarks_text', 'like', "%{$search}%");
            });
        }

        // Calculate global totals for the top-level view
        $globalTotalQuantity = $query->sum('quantity') ?: "0";
        
        $allRecords = $query->get(['remarks', 'destination', 'quantity']);
        $globalBreakdowns = $allRecords->reduce(function($acc, $i) {
            $r = is_string($i->remarks) ? json_decode($i->remarks, true) : $i->remarks;
            if (!is_array($r)) $r = [];
            $acc['yan_zun'] = bcadd($acc['yan_zun'], (string)($r['yan_zun'] ?? 0));
            $acc['yan_an'] = bcadd($acc['yan_an'], (string)($r['yan_an'] ?? 0));
            $acc['long_sheng'] = bcadd($acc['long_sheng'], (string)($r['long_sheng'] ?? 0));
            $acc['long_zhan'] = bcadd($acc['long_zhan'], (string)($r['long_zhan'] ?? 0));
            if ($i->destination === '虎甲軍') $acc['yan_jue'] = bcadd($acc['yan_jue'], (string)$i->quantity);
            if ($i->destination === '虎賁軍') $acc['yan_di'] = bcadd($acc['yan_di'], (string)$i->quantity);
            return $acc;
        }, ['yan_zun'=>'0','yan_an'=>'0','long_sheng'=>'0','long_zhan'=>'0','yan_jue'=>'0','yan_ze'=>'0','yan_di'=>'0','yan_yuan'=>'0']);

        $paginator = $query->select('know_date', \Illuminate\Support\Facades\DB::raw('count(*) as count'), \Illuminate\Support\Facades\DB::raw('sum(quantity) as total_qty'))
            ->groupBy('know_date')
            ->orderByRaw('know_date IS NULL ASC, know_date DESC')
            ->paginate($filters['per_page'] ?? 20);

        return [
            'paginator' => $paginator,
            'global_total_quantity' => $globalTotalQuantity,
            'global_breakdowns' => $globalBreakdowns
        ];
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
                
                if (count($parts) >= 1) {
                    // Combine all parts as the remark text
                    $combinedRemarks = implode('、', $parts);
                    $item['remarks_text'] = $combinedRemarks;
                    
                    // Destination is strictly '未處理' if it contains that keyword, else '已處理'
                    $item['destination'] = str_contains($combinedRemarks, '未處理') ? '未處理' : '已處理';
                } else {
                    $item['remarks_text'] = '';
                    $item['destination'] = str_contains($item['user_name'], '未處理') ? '未處理' : '已處理';
                }

                // 3. Status Logic
                if (str_contains($rawRemarks, '未處理') || str_contains($item['user_name'], '未處理')) {
                    $item['status'] = '待處理';
                    $item['process_date'] = null;
                } else {
                    $item['status'] = '已處理';
                    $item['process_date'] = $item['know_date'] ?? null;
                }

                // 4. Count Categorization
                $mergedRemarks = ['yan_zun' => 0, 'yan_an' => 0, 'long_sheng' => 0, 'long_zhan' => 0];
                $searchSource = $rawRemarks;
                
                preg_match_all('/(.*?)[（(](.*?)[）)]/u', $searchSource, $matches, PREG_SET_ORDER);
                foreach ($matches as $m) {
                    $content = $m[2];
                    // 略過日期格式 (如 9/26) 以免干擾數量統計
                    if (preg_match('/\d+[\/\-.]\d+/', $content)) continue;

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
                    $pending = Grudge::where('user_id', $userId)
                        ->where('user_name', $item['user_name'])
                        ->where('status', '待處理')
                        ->latest()->first();
                    if ($pending) {
                        $pending->update($item);
                        $results->push($pending);
                    } else {
                        $results->push(Grudge::create($item));
                    }
                    continue;
                }
                $results->push(Grudge::create($item));
                
            } catch (\Exception $e) {
                \Log::error("Grudge Batch Import Error: " . $e->getMessage(), [
                    'item' => $item,
                    'trace' => $e->getTraceAsString()
                ]);
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
        if (!$grudge || $grudge->user_id !== auth()->id()) return false;
        return $grudge->update($data);
    }

    public function delete(int $id): bool
    {
        $grudge = $this->findById($id);
        if (!$grudge || $grudge->user_id !== auth()->id()) return false;
        return $grudge->delete();
    }
}
