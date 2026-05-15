<?php
namespace App\Http\Controllers;
use App\Models\OtherTeaching;
use Illuminate\Http\Request;

class OtherTeachingController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $masterId = $request->query('master_id');
        $mode = $request->query('mode');
        
        $query = OtherTeaching::query()->with(['master', 'user'])
            ->where('user_id', $user->id);
        
        if ($masterId !== null) {
            $query->where('master_id', $masterId);
        }

        if ($mode === 'dates') {
            $dates = $query->selectRaw('date, count(*) as count')
                ->groupBy('date')
                ->orderBy('date', 'desc')
                ->get();
            return response()->json($dates);
        }

        return response()->json($query->orderBy('date', 'desc')->orderBy('sort_order', 'asc')->get());
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $data['user_id'] = auth()->id();
        $record = OtherTeaching::create($data);
        return response()->json($record, 201);
    }

    public function update(Request $request, $id)
    {
        $user = auth()->user();
        $record = OtherTeaching::findOrFail($id);
        if ($record->user_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $record->update($request->all());
        return response()->json($record);
    }

    public function destroy($id)
    {
        $user = auth()->user();
        $record = OtherTeaching::findOrFail($id);
        if ($record->user_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $record->delete();
        return response()->json(['message' => '已刪除']);
    }

    public function reorder(Request $request)
    {
        $user = auth()->user();
        $orders = $request->input('orders', []);
        foreach ($orders as $order) {
            OtherTeaching::where('id', $order['id'])
                ->where('user_id', $user->id)
                ->update(['sort_order' => $order['sort_order']]);
        }
        return response()->json(['message' => 'Reordered']);
    }
}
