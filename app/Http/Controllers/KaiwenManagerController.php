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
        $user = auth()->user();
        $weeklyQuery = WeeklyPost::where('user_id', $user->id)->orderBy('sort_order', 'asc')->orderBy('created_at', 'desc');
        $selfQuery = SelfPost::where('user_id', $user->id)->with('master')->orderBy('sort_order', 'asc')->orderBy('created_at', 'desc');

        return response()->json([
            'weeklyPosts' => $weeklyQuery->get(),
            'selfPosts' => $selfQuery->get(),
        ]);
    }

    public function storeWeekly(Request $request)
    {
        try {
            $data = $request->all();
            $data['user_id'] = auth()->id();
            $post = WeeklyPost::create($data);
            return response()->json($post, 201);
        } catch (\Exception $e) {
            Log::error('Store Weekly Error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function updateWeekly(Request $request, $id)
    {
        try {
            $user = auth()->user();
            $post = WeeklyPost::findOrFail($id);
            if ($post->user_id !== $user->id) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            $post->update($request->all());
            return response()->json($post);
        } catch (\Exception $e) {
            Log::error('Update Weekly Error: ' . $e->getMessage(), ['id' => $id, 'data_keys' => array_keys($request->all())]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroyWeekly($id)
    {
        $user = auth()->user();
        $post = WeeklyPost::findOrFail($id);
        if ($post->user_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $post->delete();
        return response()->json(['message' => 'Deleted']);
    }

    public function storeSelf(Request $request)
    {
        try {
            $data = $request->all();
            $data['user_id'] = auth()->id();
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
            $user = auth()->user();
            $post = SelfPost::findOrFail($id);
            if ($post->user_id !== $user->id) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
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
        $user = auth()->user();
        $post = SelfPost::findOrFail($id);
        if ($post->user_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $post->delete();
        return response()->json(['message' => 'Deleted']);
    }

    public function reorderWeekly(Request $request)
    {
        $user = auth()->user();
        foreach ($request->input('orders', []) as $item) {
            $query = WeeklyPost::where('id', $item['id']);
            $query->where('user_id', $user->id);
            $query->update(['sort_order' => $item['sort_order']]);
        }
        return response()->json(['message' => 'Reordered']);
    }

    public function reorderSelf(Request $request)
    {
        $user = auth()->user();
        foreach ($request->input('orders', []) as $item) {
            $query = SelfPost::where('id', $item['id']);
            $query->where('user_id', $user->id);
            $query->update(['sort_order' => $item['sort_order']]);
        }
        return response()->json(['message' => 'Reordered']);
    }
}
