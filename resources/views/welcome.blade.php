@extends('layouts.app')

@section('content')
<div class="h-full flex flex-col justify-center items-center px-6 relative bg-white">
    <!-- Background Decor -->
    <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-indigo-50 rounded-full blur-[100px] opacity-60"></div>
    <div class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] bg-blue-50 rounded-full blur-[100px] opacity-60"></div>

    <div class="max-w-4xl w-full text-center relative z-10">
        <!-- Badge -->
        <div class="inline-flex items-center space-x-2 bg-indigo-50 border border-indigo-100 px-3 py-1 rounded-full mb-8 animate-fade-in shadow-sm">
            <span class="flex h-2 w-2 rounded-full bg-indigo-500 animate-pulse"></span>
            <span class="text-[11px] font-bold text-indigo-600 uppercase tracking-widest leading-none">皇恩筆記本</span>
        </div>

        <!-- Hero Title -->
        <h1 class="font-bold font-outfit text-slate-900 leading-[1.1] mb-6 tracking-tight">
            <span class="text-3xl md:text-4xl block opacity-80 mb-2">以數據記載</span>
            <span class="text-4xl md:text-6xl drop-shadow-sm" style="color: rgb(255, 215, 0);">每一份皇恩</span>
        </h1>

        <!-- Subtext -->
        <p class="text-lg text-slate-500 max-w-2xl mx-auto mb-10 leading-relaxed">
            支援法寶、重大皇恩、怨靈記錄，<br/>
            提供快速複製 LINE 格式與 Excel 匯出功能。
        </p>

        <!-- CTA Buttons -->
        <div class="flex flex-col sm:flex-row items-center justify-center space-y-4 sm:space-y-0 sm:space-x-4">
            @auth
                <a href="{{ url('/home') }}" class="w-full sm:w-auto bg-indigo-600 text-white font-bold px-8 py-4 rounded-2xl shadow-xl shadow-indigo-200 hover:bg-indigo-700 hover:-translate-y-1 transition-all duration-300 flex items-center justify-center">
                    進入管理控制台
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M14 5l7 7m0 0l-7 7m7-7H3" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </a>
            @else
                <a href="{{ route('login') }}" class="w-full sm:w-auto bg-indigo-600 text-white font-bold px-8 py-4 rounded-2xl shadow-xl shadow-indigo-200 hover:bg-indigo-700 hover:-translate-y-1 transition-all duration-300 flex items-center justify-center">
                    立即登錄
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </a>
                <a href="{{ route('register') }}" class="w-full sm:w-auto bg-white text-slate-700 font-bold px-8 py-4 rounded-2xl border border-slate-200 shadow-sm hover:bg-slate-50 transition-all flex items-center justify-center">
                    註冊帳戶
                </a>
            @endauth
        </div>

        <!-- Feature Grid (Small) -->
        <div class="mt-20 grid grid-cols-2 md:grid-cols-4 gap-8">
            <div class="flex flex-col items-center">
                <div class="text-2xl font-bold font-outfit text-indigo-600">快速</div>
                <div class="text-[11px] text-slate-400 font-bold uppercase tracking-widest mt-1">登記速度提升</div>
            </div>
            <div class="flex flex-col items-center">
                <div class="text-2xl font-bold font-outfit text-indigo-600">即時</div>
                <div class="text-[11px] text-slate-400 font-bold uppercase tracking-widest mt-1">智慧搜尋過濾</div>
            </div>
            <div class="flex flex-col items-center">
                <div class="text-2xl font-bold font-outfit text-indigo-600">相容</div>
                <div class="text-[11px] text-slate-400 font-bold uppercase tracking-widest mt-1">EXCEL / LINE 格式</div>
            </div>
            <div class="flex flex-col items-center">
                <div class="text-2xl font-bold font-outfit text-indigo-600">鎖定</div>
                <div class="text-[11px] text-slate-400 font-bold uppercase tracking-widest mt-1">穩定的運作邏輯</div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in {
        animation: fadeIn 0.8s ease-out forwards;
    }
    main {
        height: calc(100vh - 64px);
    }
</style>
@endpush
@endsection
