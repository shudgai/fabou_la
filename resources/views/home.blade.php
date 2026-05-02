@extends('layouts.app')

@section('content')
<div class="h-full bg-slate-50 overflow-y-auto">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4 pb-8">
        @php $perms = Auth::user()->getPermissions(); @endphp
        <!-- Dashboard Header -->
        <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-6 pt-12">
            <div class="text-center md:text-left">
                <h1 class="text-4xl md:text-5xl font-black font-outfit text-slate-900 tracking-tight mb-3">皇恩筆記本</h1>
                <p class="text-slate-400 text-lg md:text-xl font-bold">歡迎回來，{{ Auth::user()->display_name }}</p>
            </div>
            <div class="flex justify-center">
                <a href="{{ route('note.index') }}" class="w-full md:w-auto bg-indigo-600 text-white px-10 py-5 rounded-[24px] text-xl font-black shadow-xl shadow-indigo-100 hover:bg-indigo-700 hover:scale-105 active:scale-95 transition-all flex items-center justify-center group">
                    進入皇恩筆記本
                    <svg class="w-6 h-6 ml-3 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M13 7l5 5m0 0l-5 5m5-5H6" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </a>
            </div>
        </div>



        <!-- Main Navigation Grid: Show for Admins, Hidden for regular users -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-6 @if(!Auth::user()->isAdmin()) hidden @endif">
            <!-- Major Grace Card -->
            @if($perms['can_see_grace'])
            <a href="{{ route('note.index') }}#grace" class="group bg-white rounded-3xl shadow-sm border border-slate-200 hover:shadow-xl hover:border-indigo-300 transition-all duration-300 flex flex-col items-center justify-center text-center shrink-0 mx-auto" style="width: 110px; height: 110px;">

                <h3 class="text-base font-bold font-outfit text-slate-800">重大皇恩專區</h3>
            </a>
            @endif

            <!-- Imperial Teaching Card -->
            @if($perms['can_see_teaching_folders'])
            <a href="{{ route('note.index') }}#teaching" class="group bg-white rounded-3xl shadow-sm border border-slate-200 hover:shadow-xl hover:border-indigo-300 transition-all duration-300 flex flex-col items-center justify-center text-center shrink-0 mx-auto" style="width: 110px; height: 110px;">

                <h3 class="text-base font-bold font-outfit text-slate-800">父皇仙師開示專區</h3>
            </a>
            @endif

            <!-- Kaiwen Card -->
            @if($perms['can_see_kaiwen'])
            <a href="{{ route('note.index') }}#kaiwen" class="group bg-white rounded-3xl shadow-sm border border-slate-200 hover:shadow-xl hover:border-indigo-300 transition-all duration-300 flex flex-col items-center justify-center text-center shrink-0 mx-auto" style="width: 110px; height: 110px;">

                <h3 class="text-base font-bold font-outfit text-slate-800">開文專區</h3>
            </a>
            @endif

            <!-- Grudge Card -->
            @if($perms['can_see_grudge'])
            <a href="{{ route('note.index') }}#grudge" class="group bg-white rounded-3xl shadow-sm border border-slate-200 hover:shadow-xl hover:border-indigo-300 transition-all duration-300 flex flex-col items-center justify-center text-center shrink-0 mx-auto" style="width: 110px; height: 110px;">

                <h3 class="text-base font-bold font-outfit text-slate-800">怨靈紀錄專區</h3>
            </a>
            @endif

            <!-- Military Card -->
            @if($perms['can_see_military'])
            <a href="{{ route('note.index') }}#military" class="group bg-white rounded-3xl shadow-sm border border-slate-200 hover:shadow-xl hover:border-indigo-300 transition-all duration-300 flex flex-col items-center justify-center text-center shrink-0 mx-auto" style="width: 110px; height: 110px;">

                <h3 class="text-base font-bold font-outfit text-slate-800">軍隊紀錄專區</h3>
            </a>
            @endif

            <!-- Treasure Card -->
            @if($perms['can_see_treasures'])
            <a href="{{ route('note.index') }}#treasure" class="group bg-white rounded-3xl shadow-sm border border-slate-200 hover:shadow-xl hover:border-indigo-300 transition-all duration-300 flex flex-col items-center justify-center text-center shrink-0 mx-auto" style="width: 110px; height: 110px;">

                <h3 class="text-base font-bold font-outfit text-slate-800">法寶登記專區</h3>
            </a>
            @endif

            <!-- Other Card -->
            @if($perms['can_see_other_folders'])
            <a href="{{ route('note.index') }}#other" class="group bg-white rounded-3xl shadow-sm border border-slate-200 hover:shadow-xl hover:border-indigo-300 transition-all duration-300 flex flex-col items-center justify-center text-center shrink-0 mx-auto" style="width: 110px; height: 110px;">

                <h3 class="text-base font-bold font-outfit text-slate-800">其他專區</h3>
            </a>
            @endif

            <!-- Trash Card -->
            @if($perms['can_see_trash'])
            <a href="{{ route('note.index') }}#trash" class="group bg-white rounded-3xl shadow-sm border border-slate-200 hover:shadow-xl hover:border-indigo-300 transition-all duration-300 flex flex-col items-center justify-center text-center shrink-0 mx-auto" style="width: 110px; height: 110px;">

                <h3 class="text-base font-bold font-outfit text-slate-800">回收桶</h3>
            </a>
            @endif
        </div>




        </div>

    </div>
</div>
@endsection
