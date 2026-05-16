<?php

namespace App\Http\Controllers;

use App\Models\Group;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    public function index()
    {
        return Group::where('user_id', auth()->id())
            ->withCount('dharmaNames')
            ->orderBy('name')
            ->get();
    }

    public function store(Request $request)
    {
        $userId = auth()->id();
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                \Illuminate\Validation\Rule::unique('groups')->where(function ($query) use ($userId) {
                    return $query->where('user_id', $userId);
                })
            ],
        ]);

        $validated['user_id'] = $userId;
        return Group::create($validated);
    }

    public function show(Group $group)
    {
        if ($group->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        return $group->load('dharmaNames');
    }

    public function update(Request $request, Group $group)
    {
        if ($group->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $userId = auth()->id();
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                \Illuminate\Validation\Rule::unique('groups')->where(function ($query) use ($userId) {
                    return $query->where('user_id', $userId);
                })->ignore($group->id)
            ],
        ]);

        $group->update($validated);
        
        if ($request->has('dharma_name_ids')) {
            $group->dharmaNames()->sync($request->dharma_name_ids);
        }

        return $group;
    }

    public function destroy(Group $group)
    {
        if ($group->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $group->dharmaNames()->detach();
        $group->delete();
        return response()->json(['success' => true]);
    }
}
