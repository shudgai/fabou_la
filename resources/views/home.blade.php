@extends('layouts.app')

@section('content')
<div class="h-full bg-white overflow-y-auto">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4 pb-8">
        @php $perms = Auth::user()->getPermissions(); @endphp
        <!-- Dashboard Header -->
    <!-- Simple White Background -->
    <div class="fixed inset-0 z-0 bg-white pointer-events-none"></div>

    <div class="max-w-md mx-auto px-6 min-h-[70vh] flex flex-col justify-center relative z-10">
        <!-- Main Entry Button (Premium Style) -->
        <div class="animate-fade-in-down">
            <a href="{{ route('note.index') }}" class="w-full relative group block flex justify-center cursor-pointer">
                <div class="relative w-full active:scale-95 transition-all duration-200 flex flex-col items-center justify-center mx-auto py-12">
                    <div class="text-slate-400 font-bold tracking-[0.5em] mb-4 text-[15px] pl-2 flex items-center justify-center">
                        請進入
                        <svg class="w-4 h-4 ml-1 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </div>
                    <div class="relative z-10 flex items-center justify-center px-4 transition-transform duration-300 hover:scale-105">
                        <svg class="w-64 h-64 drop-shadow-xl" viewBox="0 0 240 240" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <!-- Golden decorative rings -->
                            <circle cx="120" cy="120" r="114" fill="white" stroke="#eab308" stroke-width="4"/>
                            <circle cx="120" cy="120" r="108" fill="white" stroke="#eab308" stroke-width="1.5"/>
                            <circle cx="120" cy="120" r="58" fill="white" stroke="#eab308" stroke-width="1.5"/>
                            
                            <!-- Central Taiji -->
                            <g transform="translate(70, 70)">
                                <circle cx="50" cy="50" r="49" fill="white" stroke="black" stroke-width="2"/>
                                <mask id="taiji-mask-home">
                                    <rect x="50" y="-10" width="60" height="120" fill="white"></rect>
                                    <circle cx="50" cy="25.5" r="24.5" fill="black"></circle>
                                    <circle cx="50" cy="74.5" r="24.5" fill="white"></circle>
                                </mask>
                                <circle cx="50" cy="50" r="49" fill="black" mask="url(#taiji-mask-home)"></circle>
                                <circle cx="50" cy="74.5" r="8" fill="white"/>
                                <circle cx="50" cy="25.5" r="8" fill="black"/>
                            </g>

                            <!-- Circular Text wrapping around -->
                            <defs>
                                <path id="badgeArcMobile" d="M 71.9, 168.1 A 68,68 0 1,1 168.1, 168.1" />
                            </defs>
                            <text font-family="'BiauKai', 'DFKai-SB', 'PMingLiU', 'Noto Serif TC', serif" font-weight="900" font-size="38" fill="#dc2626">
                                <textPath href="#badgeArcMobile" startOffset="50%" text-anchor="middle" letter-spacing="10">
                                    皇恩筆記本
                                </textPath>
                            </text>
                            
                            <!-- Bottom decorative elements: 9 dots arranged up and down (zigzag) and spread out -->
                            <!-- Inner dots (5) -->
                            <circle cx="120" cy="188" r="3.5" fill="#dc2626" />
                            <circle cx="120" cy="188" r="3.5" fill="#dc2626" transform="rotate(30, 120, 120)" />
                            <circle cx="120" cy="188" r="3.5" fill="#dc2626" transform="rotate(-30, 120, 120)" />
                            <circle cx="120" cy="188" r="3.5" fill="#dc2626" transform="rotate(60, 120, 120)" />
                            <circle cx="120" cy="188" r="3.5" fill="#dc2626" transform="rotate(-60, 120, 120)" />
                            
                            <!-- Outer dots (4) -->
                            <circle cx="120" cy="208" r="3.5" fill="#dc2626" transform="rotate(15, 120, 120)" />
                            <circle cx="120" cy="208" r="3.5" fill="#dc2626" transform="rotate(-15, 120, 120)" />
                            <circle cx="120" cy="208" r="3.5" fill="#dc2626" transform="rotate(45, 120, 120)" />
                            <circle cx="120" cy="208" r="3.5" fill="#dc2626" transform="rotate(-45, 120, 120)" />
                        </svg>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>
@endsection
