@extends('layouts.app')

@section('content')
<div class="h-full bg-slate-50 overflow-y-auto">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4 pb-8">
        @php $perms = Auth::user()->getPermissions(); @endphp
        <!-- Dashboard Header -->
    <!-- Sophisticated Background -->
    <div class="fixed inset-0 z-0 bg-slate-50 overflow-hidden pointer-events-none">
        <div class="absolute -top-[10%] -left-[10%] w-[50%] h-[50%] bg-yellow-100/40 rounded-full blur-[120px] opacity-60"></div>
        <div class="absolute top-[20%] -right-[10%] w-[40%] h-[40%] bg-indigo-100/30 rounded-full blur-[100px] opacity-40"></div>
        <div class="absolute -bottom-[10%] left-[20%] w-[60%] h-[50%] bg-red-50/30 rounded-full blur-[120px] opacity-50"></div>
    </div>

    <div class="max-w-md mx-auto px-6 min-h-[70vh] flex flex-col justify-center relative z-10">
        <!-- Main Entry Button (Premium Style) -->
        <div class="animate-fade-in-down">
            <a href="{{ route('note.index') }}" class="w-full relative group">
                <div class="absolute inset-0 bg-yellow-400 rounded-[36px] blur-md opacity-20 group-hover:opacity-30 transition-opacity"></div>
                <div class="relative bg-gradient-to-br from-yellow-300 via-yellow-400 to-amber-500 text-red-700 py-10 rounded-[32px] text-[32px] font-black shadow-[0_10px_25px_-5px_rgba(251,191,36,0.3)] hover:shadow-[0_15px_30px_-5px_rgba(251,191,36,0.4)] active:scale-95 transition-all flex items-center justify-center border-b-2 border-amber-600/20"
                     style="text-shadow: 0 1px 0 rgba(255,255,255,0.4), 0 2px 2px rgba(0,0,0,0.1), 0 3px 3px rgba(0,0,0,0.1);">
                    進入皇恩筆記本
                </div>
            </a>
        </div>
    </div>
</div>
@endsection
