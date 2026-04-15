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
        $stats = [
            'treasures' => \App\Models\Treasure::count(),
            'grace' => \App\Models\ImperialGrace::count(),
            'grudges' => \App\Models\Grudge::count(),
            'teachings' => \App\Models\Teaching::count(),
        ];
        
        return view('home', compact('stats'));
    }
}
