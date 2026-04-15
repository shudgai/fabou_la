@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-slate-50 font-sans pb-20">
    <!-- Header Area -->
    <div class="bg-white border-b border-slate-100 px-6 py-12">
        <div class="max-w-7xl mx-auto">
            <h1 class="text-4xl font-black text-slate-900 tracking-tight mb-2">系統管理中心</h1>
            <p class="text-slate-500 text-lg">在此管理法號、權限以及系統核心設定。</p>
        </div>
    </div>

    <!-- Admin Grid -->
    <div class="max-w-7xl mx-auto px-6 -mt-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Dharma Names Management -->
            <a href="{{ route('dharma-names.index') }}" class="group bg-white p-8 rounded-[32px] shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-50 transition-all duration-300 hover:shadow-[0_20px_50px_rgba(79,70,229,0.15)] hover:-translate-y-1">
                <div class="h-16 w-16 bg-indigo-50 rounded-2xl flex items-center justify-center mb-6 text-indigo-600 transition-transform group-hover:scale-110">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M7 7h.01M7 11h.01M7 15h.01M13 7h.01M13 11h.01M13 15h.01M19 7h.01M19 11h.01M19 15h.01M5 5h14a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-2">法號管理</h3>
                <p class="text-slate-500 text-sm leading-relaxed text-left">維護全站法號名稱、排序以及相關聯設定內容。</p>
            </a>

            <!-- Roles & Permissions -->
            <a href="{{ route('roles.index') }}" class="group bg-white p-8 rounded-[32px] shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-50 transition-all duration-300 hover:shadow-[0_20px_50px_rgba(79,70,229,0.15)] hover:-translate-y-1">
                <div class="h-16 w-16 bg-rose-50 rounded-2xl flex items-center justify-center mb-6 text-rose-600 transition-transform group-hover:scale-110">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-2">權限設定</h3>
                <p class="text-slate-500 text-sm leading-relaxed text-left">設定管理員與一般使用者的操作權限，確保系統安全。</p>
            </a>

            <!-- User Management (Coming Soon) -->
            <div class="group bg-slate-100/50 p-8 rounded-[32px] border border-dashed border-slate-200 opacity-60">
                <div class="h-16 w-16 bg-slate-200 rounded-2xl flex items-center justify-center mb-6 text-slate-400">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </div>
                <h3 class="text-xl font-bold text-slate-400 mb-2">帳號管理</h3>
                <p class="text-slate-400 text-sm leading-relaxed text-left">檢視並更新使用者帳號資料，此功能正在開發中。</p>
            </div>
        </div>
    </div>
</div>
@endsection
