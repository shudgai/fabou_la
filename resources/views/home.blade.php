@extends('layouts.app')

@section('content')
<div class="h-full bg-slate-50 overflow-y-auto">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Dashboard Header -->
        <div class="mb-8 pt-16 flex flex-col md:flex-row md:items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold font-outfit text-slate-900 tracking-tight">皇恩筆記本總覽</h1>
                <p class="text-slate-500 text-sm mt-1">歡迎回來，{{ Auth::user()->name }}。目前系統已存儲以下紀錄：</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('note.index') }}" class="bg-indigo-600 text-white px-5 py-2.5 rounded-2xl text-sm font-bold shadow-lg shadow-indigo-100 hover:bg-indigo-700 transition-all flex items-center">
                    進入皇恩筆記本
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M13 7l5 5m0 0l-5 5m5-5H6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </a>
            </div>
        </div>



        <!-- Main Navigation Grid -->
        <div class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-4 gap-3 md:gap-6 justify-center justify-items-center">
            <a href="{{ route('note.index') }}#grace" class="group bg-white rounded-3xl shadow-sm border border-slate-200 hover:shadow-xl hover:border-indigo-300 transition-all duration-300 flex flex-col items-center justify-center text-center shrink-0 mx-auto" style="width: 110px; height: 110px;">
                <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center mb-2 transition-transform group-hover:scale-110">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-7.714 2.143L11 21l-2.286-6.857L1 12l7.714-2.143L11 3z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </div>
                <h3 class="text-sm font-bold font-outfit text-slate-800">重大皇恩</h3>
            </a>

            <!-- Imperial Teaching Card -->
            <a href="{{ route('note.index') }}#teaching" class="group bg-white rounded-3xl shadow-sm border border-slate-200 hover:shadow-xl hover:border-indigo-300 transition-all duration-300 flex flex-col items-center justify-center text-center shrink-0 mx-auto" style="width: 110px; height: 110px;">
                <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center mb-2 transition-transform group-hover:scale-110">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5S19.832 5.477 21 6.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </div>
                <h3 class="text-sm font-bold font-outfit text-slate-800">父皇仙師開示</h3>
            </a>

            <!-- Grudge Card -->
            <a href="{{ route('note.index') }}#grudge" class="group bg-white rounded-3xl shadow-sm border border-slate-200 hover:shadow-xl hover:border-indigo-300 transition-all duration-300 flex flex-col items-center justify-center text-center shrink-0 mx-auto" style="width: 110px; height: 110px;">
                <div class="w-12 h-12 bg-slate-50 text-slate-600 rounded-xl flex items-center justify-center mb-2 transition-transform group-hover:scale-110">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M13 10V3L4 14h7v7l9-11h-7z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </div>
                <h3 class="text-sm font-bold font-outfit text-slate-800">怨靈紀錄</h3>
            </a>

            <!-- Military Card -->
            <a href="{{ route('note.index') }}#military" class="group bg-white rounded-3xl shadow-sm border border-slate-200 hover:shadow-xl hover:border-indigo-300 transition-all duration-300 flex flex-col items-center justify-center text-center shrink-0 mx-auto" style="width: 110px; height: 110px;">
                <div class="w-12 h-12 bg-red-50 text-red-600 rounded-xl flex items-center justify-center mb-2 transition-transform group-hover:scale-110">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </div>
                <h3 class="text-sm font-bold font-outfit text-slate-800">軍隊記錄</h3>
            </a>

            <!-- Treasure Card -->
            <a href="{{ route('note.index') }}#treasure" class="group bg-white rounded-3xl shadow-sm border border-slate-200 hover:shadow-xl hover:border-indigo-300 transition-all duration-300 flex flex-col items-center justify-center text-center shrink-0 mx-auto" style="width: 110px; height: 110px;">
                <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center mb-2 transition-transform group-hover:scale-110">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </div>
                <h3 class="text-sm font-bold font-outfit text-slate-800">法寶登記</h3>
            </a>


        </div>

    </div>
</div>
@endsection
