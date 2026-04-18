<?php

namespace App\Http\Controllers;

use App\Models\Group;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    public function index()
    {
        return Group::withCount('dharmaNames')->orderBy('name')->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:groups,name',
        ]);

        return Group::create($validated);
    }

    public function show(Group $group)
    {
        return $group->load('dharmaNames');
    }

    public function update(Request $request, Group $group)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:groups,name,' . $group->id,
        ]);

        $group->update($validated);
        
        if ($request->has('dharma_name_ids')) {
            $group->dharmaNames()->sync($request->dharma_name_ids);
        }

        return $group;
    }

    public function destroy(Group $group)
    {
        $group->dharmaNames()->detach();
        $group->delete();
        return response()->json(['success' => true]);
    }
}
