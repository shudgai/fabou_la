<?php

namespace App\Http\Controllers;

use App\Models\Treasure;
use Illuminate\Http\Request;

class TreasureController extends Controller
{
    /**
     * Display a listing of common treasures (The Library).
     */
    public function index()
    {
        return Treasure::all();
    }

    /**
     * Store a new common treasure in the library.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|unique:treasures',
            'category' => 'nullable|string',
        ]);
        
        return Treasure::create($validated);
    }

    public function destroy(Treasure $treasure)
    {
        $treasure->delete();
        return response()->json(['message' => 'Deleted from library']);
    }
}
