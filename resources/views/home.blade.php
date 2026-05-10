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
            <a href="{{ route('note.index') }}" class="w-full relative group block flex justify-center">
                <div class="relative w-[330px] h-[330px] active:scale-95 transition-all duration-200">
                    <img src="/image/registry_book_yellow_v2.png" 
                         class="w-full h-full object-contain mix-blend-multiply" 
                         alt="Book Icon">
                    <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none pt-12 pb-2">
                        <div class="mb-2">
                            <svg class="w-14 h-14" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="50" cy="50" r="50" fill="white"/>
                                <path d="M50 0C22.3858 0 0 22.3858 0 50C0 77.6142 22.3858 100 50 100C50 75 50 75 50 50C50 25 50 25 50 0Z" fill="white"/>
                                <path d="M50 0C77.6142 0 100 22.3858 100 50C100 77.6142 77.6142 100 50 100V50V0Z" fill="black"/>
                                <path d="M50 100C36.1929 100 25 88.8071 25 75C25 61.1929 36.1929 50 50 50V100Z" fill="black"/>
                                <path d="M50 50C63.8071 50 75 38.8071 75 25C75 11.1929 63.8071 0 50 0V50Z" fill="white"/>
                                <circle cx="50" cy="75" r="8" fill="white"/>
                                <circle cx="50" cy="25" r="8" fill="black"/>
                            </svg>
                        </div>
                        <div class="font-black text-center tracking-tighter leading-none -mt-8 mb-4"
                             style="font-size: 28.8px !important; font-weight: 900 !important; color: rgb(139, 0, 0) !important;">
                             法寶登記專區
                        </div>
                        <div class="text-[#dc2626] font-black text-center tracking-tight leading-tight px-6"
                             style="font-size: 25.5px !important; font-weight: 900 !important;">
                             重大皇恩<br>登記簿
                        </div>
                        <div class="mt-2 flex items-center">
                            <span class="text-black font-normal tracking-tight" style="font-size: 18px !important;">共 {{ \App\Models\Registry::count() }} 筆</span>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>
@endsection
