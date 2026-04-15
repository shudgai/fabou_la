@extends('layouts.app')

@section('content')
<div class="h-full bg-slate-50 overflow-y-auto">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Dashboard Header -->
        <div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4">
            <div>
                <h1 class="text-3xl font-black font-outfit text-slate-900 tracking-tight">筆記本總覽</h1>
                <p class="text-slate-500 text-sm mt-1">歡迎回來，{{ Auth::user()->name }}。目前系統已存儲以下紀錄：</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('note.index') }}" class="bg-indigo-600 text-white px-5 py-2.5 rounded-2xl text-sm font-bold shadow-lg shadow-indigo-100 hover:bg-indigo-700 transition-all flex items-center">
                    進入筆記本主動畫面
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M13 7l5 5m0 0l-5 5m5-5H6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </a>
            </div>
        </div>

        <!-- Stats Overview Grid -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-10">
            <div class="bg-white p-5 rounded-[2rem] border border-slate-100 shadow-sm flex flex-col">
                <span class="text-slate-400 text-xs font-bold uppercase tracking-wider mb-1">法寶總數</span>
                <span class="text-3xl font-black text-indigo-600 font-outfit">{{ number_format($stats['treasures']) }}</span>
            </div>
            <div class="bg-white p-5 rounded-[2rem] border border-slate-100 shadow-sm flex flex-col">
                <span class="text-slate-400 text-xs font-bold uppercase tracking-wider mb-1">重大皇恩</span>
                <span class="text-3xl font-black text-blue-600 font-outfit">{{ number_format($stats['grace']) }}</span>
            </div>
            <div class="bg-white p-5 rounded-[2rem] border border-slate-100 shadow-sm flex flex-col">
                <span class="text-slate-400 text-xs font-bold uppercase tracking-wider mb-1">怨靈紀錄</span>
                <span class="text-3xl font-black text-slate-700 font-outfit">{{ number_format($stats['grudges']) }}</span>
            </div>
            <div class="bg-white p-5 rounded-[2rem] border border-slate-100 shadow-sm flex flex-col">
                <span class="text-slate-400 text-xs font-bold uppercase tracking-wider mb-1">仙師開示</span>
                <span class="text-3xl font-black text-amber-600 font-outfit">{{ number_format($stats['teachings']) }}</span>
            </div>
        </div>

        <!-- Main Navigation Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Treasure Card -->
            <a href="{{ route('note.index') }}#treasure" class="group bg-white p-6 rounded-[2.5rem] shadow-sm border border-slate-200 hover:shadow-2xl hover:shadow-indigo-100 hover:border-indigo-300 transition-all duration-300 flex flex-col items-center text-center">
                <div class="w-16 h-16 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center mb-4 transition-transform group-hover:scale-110">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </div>
                <h3 class="text-xl font-bold font-outfit text-slate-800">法寶登記</h3>
                <p class="text-slate-500 text-[13px] mt-2 leading-relaxed">管理法寶名稱、用意、及各類登記明細。</p>
            </a>

            <!-- Imperial Grace Card -->
            <a href="{{ route('note.index') }}#imperial" class="group bg-white p-6 rounded-[2.5rem] shadow-sm border border-slate-200 hover:shadow-2xl hover:shadow-indigo-100 hover:border-indigo-300 transition-all duration-300 flex flex-col items-center text-center">
                <div class="w-16 h-16 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center mb-4 transition-transform group-hover:scale-110">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-7.714 2.143L11 21l-2.286-6.857L1 12l7.714-2.143L11 3z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </div>
                <h3 class="text-xl font-bold font-outfit text-slate-800">重大皇恩</h3>
                <p class="text-slate-500 text-[13px] mt-2 leading-relaxed">紀錄仙師皇恩，支援智慧搜尋與分流管理。</p>
            </a>

            <!-- Grudge Card -->
            <a href="{{ route('note.index') }}#grudge" class="group bg-white p-6 rounded-[2.5rem] shadow-sm border border-slate-200 hover:shadow-2xl hover:shadow-indigo-100 hover:border-indigo-300 transition-all duration-300 flex flex-col items-center text-center">
                <div class="w-16 h-16 bg-slate-50 text-slate-600 rounded-2xl flex items-center justify-center mb-4 transition-transform group-hover:scale-110">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M13 10V3L4 14h7v7l9-11h-7z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </div>
                <h3 class="text-xl font-bold font-outfit text-slate-800">怨靈紀錄</h3>
                <p class="text-slate-500 text-[13px] mt-2 leading-relaxed">追蹤怨靈清理進度，支援多種過濾條件。</p>
            </a>

            <!-- Military Card -->
            <a href="{{ route('note.index') }}#military" class="group bg-white p-6 rounded-[2.5rem] shadow-sm border border-slate-200 hover:shadow-2xl hover:shadow-indigo-100 hover:border-indigo-300 transition-all duration-300 flex flex-col items-center text-center">
                <div class="w-16 h-16 bg-red-50 text-red-600 rounded-2xl flex items-center justify-center mb-4 transition-transform group-hover:scale-110">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </div>
                <h3 class="text-xl font-bold font-outfit text-slate-800">虎甲軍專區</h3>
                <p class="text-slate-500 text-[13px] mt-2 leading-relaxed">軍事單位紀錄管理，高效處理大量數據。</p>
            </a>
        </div>

    </div>
</div>
@endsection
