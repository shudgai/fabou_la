<?php

namespace App\Http\Controllers;

use App\Models\WeeklyPost;
use App\Models\SelfPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class KaiwenManagerController extends Controller
{
    public function index()
    {
        return response()->json([
            'weeklyPosts' => WeeklyPost::orderBy('sort_order', 'asc')->orderBy('created_at', 'desc')->get(),
            'selfPosts' => SelfPost::with('master')->orderBy('sort_order', 'asc')->orderBy('created_at', 'desc')->get(),
        ]);
    }

    public function storeWeekly(Request $request)
    {
        try {
            $post = WeeklyPost::create($request->all());
            return response()->json($post, 201);
        } catch (\Exception $e) {
            Log::error('Store Weekly Error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function updateWeekly(Request $request, $id)
    {
        try {
            $post = WeeklyPost::findOrFail($id);
            $post->update($request->all());
            return response()->json($post);
        } catch (\Exception $e) {
            Log::error('Update Weekly Error: ' . $e->getMessage(), ['id' => $id, 'data_keys' => array_keys($request->all())]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroyWeekly($id)
    {
        WeeklyPost::destroy($id);
        return response()->json(['message' => 'Deleted']);
    }

    public function storeSelf(Request $request)
    {
        try {
            $data = $request->all();
            if (isset($data['master_name']) && !empty($data['master_name'])) {
                $master = \App\Models\Master::where('name', $data['master_name'])->first();
                if ($master) {
                    $data['master_id'] = $master->id;
                }
            }
            $post = SelfPost::create($data);
            return response()->json($post, 201);
        } catch (\Exception $e) {
            Log::error('Store Self Error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function updateSelf(Request $request, $id)
    {
        try {
            $post = SelfPost::findOrFail($id);
            $data = $request->all();
            if (isset($data['master_name']) && !empty($data['master_name'])) {
                $master = \App\Models\Master::where('name', $data['master_name'])->first();
                if ($master) {
                    $data['master_id'] = $master->id;
                }
            }
            $post->update($data);
            return response()->json($post);
        } catch (\Exception $e) {
            Log::error('Update Self Error: ' . $e->getMessage(), ['id' => $id]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroySelf($id)
    {
        SelfPost::destroy($id);
        return response()->json(['message' => 'Deleted']);
    }

    public function reorderWeekly(Request $request)
    {
        foreach ($request->input('orders', []) as $item) {
            WeeklyPost::where('id', $item['id'])->update(['sort_order' => $item['sort_order']]);
        }
        return response()->json(['message' => 'Reordered']);
    }

    public function reorderSelf(Request $request)
    {
        foreach ($request->input('orders', []) as $item) {
            SelfPost::where('id', $item['id'])->update(['sort_order' => $item['sort_order']]);
        }
        return response()->json(['message' => 'Reordered']);
    }
}
