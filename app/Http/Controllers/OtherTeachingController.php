<?php
namespace App\Http\Controllers;
use App\Models\OtherTeaching;
use Illuminate\Http\Request;

class OtherTeachingController extends Controller
{
    public function index(Request $request)
    {
        $masterId = $request->query('master_id');
        $mode = $request->query('mode');
        
        $query = OtherTeaching::query()->with(['master', 'user']);
        
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
        $record = OtherTeaching::findOrFail($id);
        $record->update($request->all());
        return response()->json($record);
    }

    public function destroy($id)
    {
        $record = OtherTeaching::findOrFail($id);
        $record->delete();
        return response()->json(['message' => 'Deleted']);
    }

    public function reorder(Request $request)
    {
        $orders = $request->input('orders', []);
        foreach ($orders as $order) {
            OtherTeaching::where('id', $order['id'])->update(['sort_order' => $order['sort_order']]);
        }
        return response()->json(['message' => 'Reordered']);
    }
}
