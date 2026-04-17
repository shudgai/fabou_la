<?php

namespace App\Http\Controllers;

use App\Models\Treasure;
use Illuminate\Http\Request;

class TreasureController extends Controller
{
    /**
     * Display a listing of common treasures (The Library).
     */
    public function index(Request $request)
    {
        $masterId = $request->query('master_id');
        $type = $request->query('type', 'teaching');
        $query = Treasure::query();
        if ($masterId !== null) {
            $query->where('master_id', $masterId);
        }
        if ($type !== null) {
            $query->where('type', $type);
        }
        return $query->get();
    }

    /**
     * Store a new common treasure in the library.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|unique:treasures',
            'category' => 'nullable|string',
            'type' => 'required|string',
        ]);
        
        return Treasure::create($validated);
    }

    public function destroy(Treasure $treasure)
    {
        $treasure->delete();
        return response()->json(['message' => 'Deleted from library']);
    }
}
