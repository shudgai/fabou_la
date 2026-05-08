<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = auth()->user();
        $stats = [
            'treasures' => \App\Models\Treasure::count(),
            'grace' => \App\Models\ImperialGrace::where('user_id', $user->id)->count(),
            'grudges' => \App\Models\Grudge::where('user_id', $user->id)->count(),
            'teachings' => \App\Models\Teaching::where('user_id', $user->id)->count(),
        ];
        
        return view('home', compact('stats'));
    }
}
