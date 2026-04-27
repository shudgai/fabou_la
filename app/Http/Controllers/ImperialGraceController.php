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
        $user = auth()->user();
        $query = ImperialGrace::query();

        if (!$user->isAdmin()) {
            // Strict isolation: only own records. No linkage via user_imperial_graces.
            $query->where('user_id', $user->id);
        }

        return response()->json([
            'registries' => $query->get(),
            'userGraces' => UserImperialGrace::where('user_id', $user->id)->get()
        ]);
    }

    public function storeRegistry(Request $request)
    {
        $data = $request->all();
        $data['user_id'] = auth()->id();
        $grace = ImperialGrace::create($data);
        return response()->json($grace, 201);
    }

    public function batchStoreRegistry(Request $request)
    {
        $items = $request->input('items', []);
        $masterId = $request->input('master_id');
        $userId = auth()->id();
        $created = [];

        foreach ($items as $item) {
            $created[] = ImperialGrace::create(array_merge([
                'user_id' => $userId,
                'master_id' => $masterId,
                'status' => '已登記',
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
