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
        $lines = $request->input('lines', []);
        $masterId = $request->input('master_id');
        $created = [];

        foreach ($lines as $line) {
            $parts = explode('|', $line);
            if (count($parts) >= 2) {
                $rawDate = trim($parts[0]);
                $name = trim($parts[1]);
                $purpose = isset($parts[2]) ? trim($parts[2]) : '';
                
                // Parse Date robustly
                $parsedDate = $this->parseFlexibleDate($rawDate);

                $created[] = $this->imperialGraceService->createRegistry([
                    'master_id' => $masterId,
                    'name' => $name,
                    'purpose' => $purpose,
                    'record_date' => $parsedDate ?: now(),
                    'status' => '已登記'
                ]);
            }
        }

        return response()->json($created, 201);
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

        // Pattern 3: MMDD (e.g., 0305)
        if (preg_match('/^(\d{2})(\d{2})$/', $str, $m)) {
            return sprintf('%s-%s-%s', $currentYear, $m[1], $m[2]);
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
