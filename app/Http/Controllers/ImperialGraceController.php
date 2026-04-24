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
        $data = $request->all();
        // 移除重複名稱驗證，允許同名法寶重複載錄
        $grace = ImperialGrace::create($data);
        return response()->json($grace, 201);
    }

    public function batchStoreRegistry(Request $request)
    {
        $items = $request->input('items', []);
        $masterId = $request->input('master_id');
        $created = [];

        foreach ($items as $item) {
            // 移除批次新增時的重複名稱驗證，允許同名法寶重複載錄

            $created[] = ImperialGrace::create(array_merge([
                'master_id' => $masterId,
                'status' => '已登記',
                // Removed record_date => now() to allow nulls
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

    public function reorder(Request $request)
    {
        $orders = $request->input('orders', []);
        foreach ($orders as $item) {
            ImperialGrace::where('id', $item['id'])->update(['sort_order' => $item['sort_order']]);
        }
        return response()->json(['message' => 'Reordered']);
    }

    public function destroyRegistry($id)
    {
        ImperialGrace::destroy($id);
        return response()->json(['message' => 'Deleted']);
    }
}
