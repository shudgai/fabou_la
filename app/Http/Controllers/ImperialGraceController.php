<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\ImperialGraceService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ImperialGraceController extends Controller
{
    protected $imperialGraceService;

    public function __construct(ImperialGraceService $imperialGraceService)
    {
        $this->imperialGraceService = $imperialGraceService;
    }

    public function index()
    {
        return response()->json([
            'registries' => $this->imperialGraceService->getAllRegistries(),
            'userGraces' => $this->imperialGraceService->getAllUserGraces()
        ]);
    }

    public function storeRegistry(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'record_date' => 'required|date',
            'obtained_date' => [
                Rule::requiredIf(fn() => in_array($request->status, ['已求得', '已登記'])),
                'nullable',
                'date',
                'after_or_equal:record_date'
            ],
            'status' => 'required|in:未求得,已求得,已登記',
        ], [
            'obtained_date.required_if' => '已登記或已求得狀態必須輸入求得日期',
            'obtained_date.after_or_equal' => '求得日期不可早於得知日期',
        ]);

        $data = $request->all();
        
        // Use Master Mapping logic if name is provided instead of ID
        if (isset($data['master_name'])) {
            $masterService = app(\App\Services\MasterService::class);
            $data['master_id'] = $masterService->resolveMasterId($data['master_name']);
        }

        $registry = $this->imperialGraceService->createRegistry($data);
        return response()->json($registry, 201);
    }

    public function batchStoreRegistry(Request $request)
    {
        $input = $request->input('lines', []); 
        $items = $request->input('items', []);
        $masterId = $request->input('master_id');
        $created = [];

        if (!empty($items)) {
            foreach ($items as $item) {
                $created[] = $this->saveBatchItem($item, $masterId);
            }
            return response()->json($created, 201);
        }

        // Fallback to line parsing logic...
        if (is_string($input)) {
            $rawLines = explode("\n", $input);
        } else {
            $rawLines = $input;
        }

        $currentMasterId = $masterId;
        $currentData = [];

        foreach ($rawLines as $line) {
            $line = trim($line);
            if (!$line) continue;

            // 1. Check for Master header lines (e.g. 【重大皇恩 - 老祖仙師】)
            if (strpos($line, '【') !== false) {
                // Try to extract master name from within common headers
                $extractedMaster = null;
                if (preg_match('/(?:皇恩|仙師)[-\s：]+(.*?)[】\s]/u', $line, $m)) {
                    $extractedMaster = trim($m[1]);
                } elseif (preg_match('/[【](.*?)[】]/u', $line, $m)) {
                    $extractedMaster = trim($m[1]);
                }

                if ($extractedMaster) {
                    $resolved = app(\App\Services\MasterService::class)->resolveMasterId($extractedMaster);
                    if ($resolved) {
                        if (!empty($currentData['name'])) {
                            $created[] = $this->saveBatchItem($currentData, $currentMasterId);
                            $currentData = [];
                        }
                        $currentMasterId = $resolved;
                        continue;
                    }
                }
            }

            // 2. Check if the entire line is JUST a Master Name (Hierarchical Switch)
            $resolvedForWholeLine = app(\App\Services\MasterService::class)->resolveMasterId($line);
            if ($resolvedForWholeLine) {
                if (!empty($currentData['name'])) {
                    $created[] = $this->saveBatchItem($currentData, $currentMasterId);
                    $currentData = [];
                }
                $currentMasterId = $resolvedForWholeLine;
                continue; 
            }

            // 2. Handle Multi-line structured format (e.g. 法寶：名稱)
            if (preg_match('/^(法寶|用意|狀態|求得日期|由來與備註|仙師)[：:](.*)$/u', $line, $matches)) {
                $key = $matches[1];
                $val = trim($matches[2]);
                
                if ($key === '法寶') {
                    // Start of a new record block
                    if (!empty($currentData['name'])) {
                        $created[] = $this->saveBatchItem($currentData, $currentMasterId);
                    }
                    $currentData = ['name' => $val, 'status' => '已登記'];
                } elseif ($key === '仙師') {
                    $resolved = app(\App\Services\MasterService::class)->resolveMasterId($val);
                    if ($resolved) $currentMasterId = $resolved;
                } elseif ($key === '用意') {
                    $currentData['purpose'] = $val;
                } elseif ($key === '狀態') {
                    $currentData['status'] = $val;
                } elseif ($key === '求得日期') {
                    $currentData['obtained_date'] = $this->parseFlexibleDate($val);
                    if ($currentData['obtained_date']) $currentData['record_date'] = $currentData['obtained_date'];
                } elseif ($key === '由來與備註') {
                    $currentData['remarks'] = $val;
                }
                continue;
            }

            // 3. Handle Single-line formats (Delimited or Space-separated)
            $parts = [];
            if (strpos($line, '|') !== false) {
                $parts = explode('|', $line);
            } elseif (preg_match('/^(\d{3,8})\s+(.+)$/', $line, $matches)) {
                // ... (existing space detection logic)
                $parts = [$matches[1]];
                $rest = trim($matches[2]);
                $restParts = preg_split('/\s+/', $rest, 2);
                $parts[] = $restParts[0];
                if (isset($restParts[1])) $parts[] = $restParts[1];
            }

            if (count($parts) >= 2) {
                if (!empty($currentData['name'])) {
                    $created[] = $this->saveBatchItem($currentData, $currentMasterId);
                    $currentData = [];
                }

                $p0 = trim($parts[0]);
                $p1 = trim($parts[1]);
                $p2 = isset($parts[2]) ? trim($parts[2]) : '';
                $p3 = isset($parts[3]) ? trim($parts[3]) : '';
                
                $parsedDate = $this->parseFlexibleDate($p0);

                if ($parsedDate) {
                    $resolvedMasterIdForLine = app(\App\Services\MasterService::class)->resolveMasterId($p1);
                    if ($resolvedMasterIdForLine && !empty($p2)) {
                        $created[] = $this->saveBatchItem([
                            'name' => $p2, 'purpose' => $p3, 'record_date' => $parsedDate, 'master_name' => $p1
                        ], $currentMasterId);
                    } else {
                        $created[] = $this->saveBatchItem([
                            'name' => $p1, 'purpose' => $p2, 'record_date' => $parsedDate
                        ], $currentMasterId);
                    }
                } else {
                    $resolvedMasterIdForLine = app(\App\Services\MasterService::class)->resolveMasterId($p0);
                    if ($resolvedMasterIdForLine && !empty($p1)) {
                        $created[] = $this->saveBatchItem([
                            'name' => $p1, 'purpose' => $p2, 'master_name' => $p0
                        ], $currentMasterId);
                    } else {
                        $created[] = $this->saveBatchItem([
                            'name' => $p0, 'purpose' => $p1
                        ], $currentMasterId);
                    }
                }
            } else {
                // 4. Fallback: Entire line is a name for the current master
                if (!empty($currentData['name'])) {
                    $created[] = $this->saveBatchItem($currentData, $currentMasterId);
                    $currentData = [];
                }
                $created[] = $this->saveBatchItem(['name' => $line], $currentMasterId);
            }
        }

        // Save last block if exists
        if (!empty($currentData['name'])) {
            $created[] = $this->saveBatchItem($currentData, $currentMasterId);
        }

        return response()->json($created, 201);
    }

    private function saveBatchItem($data, $masterId)
    {
        $finalMasterId = $masterId;
        
        // Try to auto-resolve master_id if a master_name was provided in the block or line
        if (isset($data['master_name'])) {
            $resolved = app(\App\Services\MasterService::class)->resolveMasterId($data['master_name']);
            if ($resolved) $finalMasterId = $resolved;
            unset($data['master_name']);
        }

        // Detect Status in other fields if not explicitly set
        $statusKeywords = ['已求得', '已登記', '未求得'];
        foreach (['name', 'purpose', 'remarks'] as $field) {
            if (isset($data[$field])) {
                foreach ($statusKeywords as $kw) {
                    if (strpos($data[$field], $kw) !== false) {
                        $data['status'] = $kw;
                        // Remove keyword from the field to keep it clean
                        $data[$field] = trim(str_replace(['|', $kw], '', $data[$field]));
                        break 2;
                    }
                }
            }
        }

        return $this->imperialGraceService->createRegistry(array_merge([
            'master_id' => $finalMasterId,
            'status' => '已登記',
            'record_date' => now(),
        ], $data));
    }

    /**
     * Helper to parse various date formats from pastes (3/5, 3月5日, 0305, etc.)
     */
    private function parseFlexibleDate($str)
    {
        if (!$str) return null;
        
        $str = str_replace([' ', '　'], '', $str); // Remove spaces
        $currentYear = date('Y');

        // Pattern 1: MM/DD or M/D or MM-DD
        if (preg_match('/^(\d{1,2})[\/\-](\d{1,2})$/', $str, $m)) {
            return sprintf('%s-%02d-%02d', $currentYear, $m[1], $m[2]);
        }
        
        // Pattern 2: MM月DD日
        if (preg_match('/^(\d{1,2})月(\d{1,2})日?$/', $str, $m)) {
            return sprintf('%s-%02d-%02d', $currentYear, $m[1], $m[2]);
        }

        // Pattern 3: MMDD or MDD (e.g., 0305, 305)
        if (preg_match('/^(\d{1,2})(\d{2})$/', $str, $m)) {
            return sprintf('%s-%02d-%02d', $currentYear, $m[1], $m[2]);
        }

        // Pattern 4: Full YYYYMMDD
        if (preg_match('/^(\d{4})(\d{2})(\d{2})$/', $str, $m)) {
            return sprintf('%s-%s-%s', $m[1], $m[2], $m[3]);
        }

        // Pattern 5: Full YYYY/MM/DD
        if (preg_match('/^(\d{4})[\/\-](\d{1,2})[\/\-](\d{1,2})$/', $str, $m)) {
            return sprintf('%s-%02d-%02d', $m[1], $m[2], $m[3]);
        }

        try {
            return \Carbon\Carbon::parse($str)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    public function storeUserGrace(Request $request)
    {
        $grace = $this->imperialGraceService->createUserGrace($request->all());
        return response()->json($grace, 201);
    }

    public function destroyUserGrace(string $id)
    {
        $success = $this->imperialGraceService->deleteUserGrace((int)$id);
        return $success ? response()->json(['message' => 'Deleted']) : response()->json(['message' => 'Error'], 400);
    }

    public function updateRegistry(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required',
            'record_date' => 'required|date',
            'obtained_date' => [
                Rule::requiredIf(fn() => in_array($request->status, ['已求得', '已登記'])),
                'nullable',
                'date',
                'after_or_equal:record_date'
            ],
            'status' => 'required|in:未求得,已求得,已登記',
        ], [
            'obtained_date.required_if' => '已登記或已求得狀態必須輸入求得日期',
            'obtained_date.after_or_equal' => '求得日期不可早於得知日期',
        ]);

        $registry = $this->imperialGraceService->updateRegistry((int)$id, $request->all());
        return $registry ? response()->json($registry) : response()->json(['message' => 'Not found'], 404);
    }

    public function destroyRegistry(string $id)
    {
        $success = $this->imperialGraceService->deleteRegistry((int)$id);
        return $success ? response()->json(['message' => 'Deleted']) : response()->json(['message' => 'Error'], 400);
    }
}
