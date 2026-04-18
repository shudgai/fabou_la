<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        return User::with('dharmaName')->orderBy('id', 'desc')->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'nullable|string',
            'dharma_name_id' => 'nullable|exists:dharma_names,id'
        ]);

        $validated['password'] = Hash::make($request->password);

        return User::create($validated);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8',
            'role' => 'nullable|string',
            'dharma_name_id' => 'nullable|exists:dharma_names,id'
        ]);

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($request->password);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);
        return $user;
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return response()->json(['error' => '您不能刪除自己'], 400);
        }
        $user->delete();
        return response()->json(['success' => true]);
    }
}
