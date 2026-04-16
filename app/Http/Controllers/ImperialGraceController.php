<?php

namespace App\Http\Controllers;

use App\Models\ImperialGrace;
use App\Models\UserImperialGrace;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ImperialGraceController extends Controller
{
    public function index()
    {
        return response()->json([
            'registries' => ImperialGrace::all(),
            'userGraces' => UserImperialGrace::all()
        ]);
    }

    public function storeRegistry(Request $request)
    {
        // ... Logic for single storing in master list (Specialist)
        $data = $request->all();
        $grace = ImperialGrace::create($data);
        return response()->json($grace, 201);
    }

    public function batchStoreRegistry(Request $request)
    {
        // Simplified batch store
        $items = $request->input('items', []);
        $masterId = $request->input('master_id');
        $created = [];

        foreach ($items as $item) {
            $created[] = ImperialGrace::create(array_merge([
                'master_id' => $masterId,
                'status' => '已登記',
                'record_date' => now(),
            ], $item));
        }

        return response()->json($created, 201);
    }

    public function updateRegistry(Request $request, $id)
    {
        $grace = ImperialGrace::findOrFail($id);
        $grace->update($request->all());
        return response()->json($grace);
    }

    public function destroyRegistry($id)
    {
        ImperialGrace::destroy($id);
        return response()->json(['message' => 'Deleted']);
    }
}
