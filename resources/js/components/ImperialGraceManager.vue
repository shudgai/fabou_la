<template>
    <div class="bg-white h-[100dvh] flex flex-col relative overflow-hidden text-slate-900">
        <!-- Header (Only show in Folder-view or Item-view) -->
        <div v-if="currentFolder" class="border-b border-slate-300 bg-white sticky top-0 z-10" style="padding: 10px 10px 8px 10px;">
            <div class="flex items-center justify-start relative py-2 ml-1">
                <h2 class="text-[23px] font-black font-outfit tracking-tight text-left" style="color: black !important;">
                    重大皇恩 - {{ currentFolder.name }}
                </h2>
                <div class="absolute right-0 top-1/2 -translate-y-1/2 mr-1">
                    <button @click="toggleSort" class="px-2 py-1 text-[12px] text-indigo-500 bg-indigo-50 border border-indigo-100 rounded-lg active:scale-95 transition-all font-black">
                        {{ sortDesc ? '新→舊' : '舊→新' }}
                    </button>
                </div>
            </div>
        </div>
        <!-- Perfectly Centered Ultra-Compact Warning Banner -->
        <div v-if="persistentToast" class="fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 z-[9999] animate-toast-in pointer-events-auto">
            <div class="bg-white rounded-lg shadow-[0_4px_20px_rgba(0,0,0,0.2)] flex flex-col border border-slate-200"
                style="padding: 10px 15px; min-width: 140px; max-width: 85vw;">
                <div class="flex items-start justify-between space-x-3">
                    <span class="text-[15px] font-black leading-normal text-red-600 break-words uppercase tracking-wide">
                        {{ persistentToast.msg }}
                    </span>
                    <button v-if="['confirm', 'deleteConfirm', 'mismatchConfirm'].includes(persistentToast.type)" 
                        @click="persistentToast = null" 
                        class="text-slate-400 hover:text-slate-600 w-5 h-5 flex items-center justify-center font-normal text-sm">✕</button>
                </div>
                <!-- Action Buttons (Horizontal Layout) -->
                <div v-if="['confirm', 'deleteConfirm'].includes(persistentToast.type)" class="flex space-x-2 mt-3 pb-1">
                    <template v-if="persistentToast.type === 'confirm'">
                        <button @click="saveSingle('shunt')" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white px-2 py-1.5 rounded shadow-sm text-[11px] font-normal whitespace-nowrap">確定</button>
                    </template>
                    <template v-if="persistentToast.type === 'mismatchConfirm'">
                        <button @click="saveSingle('correct')" class="flex-1 bg-amber-500 hover:bg-amber-600 text-white px-2 py-1.5 rounded shadow-sm text-[11px] font-bold whitespace-nowrap">存入{{ currentFolder?.name }}</button>
                        <button @click="saveSingle('shunt')" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white px-2 py-1.5 rounded shadow-sm text-[11px] font-bold whitespace-nowrap">存入{{ getMasterName(form.master_id) }}</button>
                        <button @click="persistentToast = null" class="flex-1 bg-slate-100 text-slate-600 px-2 py-1.5 rounded text-[11px] font-normal">取消</button>
                    </template>
                    <template v-if="persistentToast.type === 'deleteConfirm'">
                        <button @click="persistentToast = null" class="flex-1 bg-slate-50 hover:bg-slate-100 text-slate-600 px-2 py-1.5 rounded border border-slate-200 text-[11px] font-normal whitespace-nowrap">取消</button>
                        <button @click="executeDelete" class="flex-1 bg-red-50 hover:bg-red-100 text-red-600 px-2 py-1.5 rounded border border-red-100 text-[11px] font-normal whitespace-nowrap">確定刪除</button>
                    </template>
                </div>
            </div>
        </div>
        <div class="flex-1 overflow-y-auto custom-scrollbar" style="padding-bottom: 80px;">
        <!-- Level 0: Main Category Selection -->
        <div v-if="!currentCategory && !currentFolder" class="h-full bg-slate-50/30">
            <div class="px-4 py-8 flex items-center bg-white border-b border-slate-50 relative min-h-[80px]">
                <button @click="$emit('goHome')" class="text-slate-400 p-4 active:scale-90 transition-transform z-10 shrink-0">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" /></svg>
                </button>
                <div class="flex-1 pr-12">
                    <h1 class="text-[30px] font-black text-slate-900 tracking-tight text-center">重大皇恩專區</h1>
                </div>
            </div>
            
            <div class="px-6 pb-24 flex flex-col items-center space-y-3">
                <!-- Category 1: Masters (Large Folder Style) -->
                <button 
                    @click="currentCategory = 'masters'"
                    class="flex flex-col items-center justify-center p-4 active:scale-95 transition-all group relative">
                    <div class="relative w-[220px] h-[220px]">
                        <svg class="w-full h-full transition-transform group-hover:scale-105 drop-shadow-2xl" viewBox="0 0 64 64" fill="none">
                            <defs>
                                <linearGradient id="mastersGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                                    <stop offset="0%" style="stop-color:rgb(255, 230, 0);stop-opacity:1" />
                                    <stop offset="50%" style="stop-color:rgb(255, 200, 0);stop-opacity:1" />
                                    <stop offset="100%" style="stop-color:rgb(255, 170, 0);stop-opacity:1" />
                                </linearGradient>
                            </defs>
                            <path d="M4 14C4 11.7909 5.79086 10 8 10H24.5L30 16H56C58.2091 16 60 17.7909 60 20V50C60 52.2091 58.2091 54 56 54H8C5.79086 54 4 52.2091 4 50V14Z" fill="url(#mastersGrad)" />
                            <path d="M4 22C4 19.7909 5.79086 18 8 18H56C58.2091 18 60 19.7909 60 22V50C60 52.2091 58.2091 54 56 54H8C5.79086 54 4 52.2091 4 50V22Z" fill="url(#mastersGrad)" stroke="rgba(255,255,255,0.6)" stroke-width="1"/>
                        </svg>
                        <!-- Label Inside -->
                        <div class="absolute inset-0 flex items-center justify-center pt-6 px-4">
                            <span class="text-[28px] font-black text-white drop-shadow-[0_2px_4px_rgba(0,0,0,0.5)] tracking-tight leading-tight text-center" style="font-weight: 900 !important;">
                                重大皇恩<br>專區
                            </span>
                        </div>
                    </div>
                </button>

                <!-- Category 2: Unobtained (Large Folder Style) -->
                <button 
                    @click="currentFolder = { id: 'unobtained', name: '未求得重大皇恩' }; currentCategory = 'unobtained'"
                    class="flex flex-col items-center justify-center p-4 active:scale-95 transition-all group relative">
                    <div class="relative w-[220px] h-[220px]">
                        <svg class="w-full h-full transition-transform group-hover:scale-105 drop-shadow-2xl" viewBox="0 0 64 64" fill="none">
                            <defs>
                                <linearGradient id="unobtainedGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                                    <stop offset="0%" style="stop-color:rgb(255, 230, 0);stop-opacity:1" />
                                    <stop offset="50%" style="stop-color:rgb(255, 200, 0);stop-opacity:1" />
                                    <stop offset="100%" style="stop-color:rgb(255, 170, 0);stop-opacity:1" />
                                </linearGradient>
                            </defs>
                            <path d="M4 14C4 11.7909 5.79086 10 8 10H24.5L30 16H56C58.2091 16 60 17.7909 60 20V50C60 52.2091 58.2091 54 56 54H8C5.79086 54 4 52.2091 4 50V14Z" fill="url(#unobtainedGrad)" />
                            <path d="M4 22C4 19.7909 5.79086 18 8 18H56C58.2091 18 60 19.7909 60 22V50C60 52.2091 58.2091 54 56 54H8C5.79086 54 4 52.2091 4 50V22Z" fill="url(#unobtainedGrad)" stroke="rgba(255,255,255,0.6)" stroke-width="1"/>
                        </svg>
                        <!-- Label Inside -->
                        <div class="absolute inset-0 flex items-center justify-center pt-6 px-4">
                            <span class="text-[28px] font-black text-white drop-shadow-[0_2px_4px_rgba(0,0,0,0.5)] tracking-tight leading-tight text-center" style="font-weight: 900 !important;">
                                未求得<br>重大皇恩
                            </span>
                        </div>
                    </div>
                </button>
            </div>

            <div class="mt-5 flex justify-center pb-32">
                <button @click="$emit('goHome')" class="text-slate-300 hover:text-slate-500 transition-colors flex items-center space-x-2 p-4 active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" /></svg>
                    <span class="text-[14px] font-black tracking-widest uppercase">返回主選單</span>
                </button>
            </div>
        </div>

        <!-- Level 1: Folder Selection (Masters Grid) -->
        <div v-if="currentCategory === 'masters' && !currentFolder" class="bg-white group-fade-in">
            <!-- Header Title -->
            <div class="pt-[5px] pb-2 flex items-center relative min-h-[60px]">
                <button @click="currentCategory = null" class="text-slate-400 p-4 active:scale-90 transition-transform z-10">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" /></svg>
                </button>
                <h1 class="absolute inset-x-0 text-[30px] font-black tracking-tight text-center" style="color: black !important;">重大皇恩專區</h1>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-2 gap-[10px] p-4 place-items-center">
                <button v-for="(folder, idx) in mastersFolders" :key="folder.id" 
                    @click="currentFolder = folder"
                    class="flex flex-col items-center justify-center active:scale-95 transition-all group relative">
                    <div class="relative w-[148px] h-[148px]">
                        <svg class="w-full h-full transition-transform group-hover:scale-105 drop-shadow-lg" viewBox="0 0 64 64" fill="none">
                            <defs>
                                <linearGradient id="folderGradReset" x1="0%" y1="0%" x2="100%" y2="100%">
                                    <stop offset="0%" style="stop-color:rgb(255, 230, 0);stop-opacity:1" />
                                    <stop offset="50%" style="stop-color:rgb(255, 200, 0);stop-opacity:1" />
                                    <stop offset="100%" style="stop-color:rgb(255, 170, 0);stop-opacity:1" />
                                </linearGradient>
                            </defs>
                            <path d="M4 14C4 11.7909 5.79086 10 8 10H24.5L30 16H56C58.2091 16 60 17.7909 60 20V50C60 52.2091 58.2091 54 56 54H8C5.79086 54 4 52.2091 4 50V14Z" fill="url(#folderGradReset)" />
                            <path d="M4 22C4 19.7909 5.79086 18 8 18H56C58.2091 18 60 19.7909 60 22V50C60 52.2091 58.2091 54 56 54H8C5.79086 54 4 52.2091 4 50V22Z" fill="url(#folderGradReset)" stroke="rgba(255,255,255,0.6)" stroke-width="1"/>
                        </svg>
                        <!-- Label Inside -->
                        <div class="absolute inset-0 flex items-center justify-center pt-5 px-3">
                            <span :class="[
                                'font-black drop-shadow-[0_2px_4px_rgba(0,0,0,0.6)] tracking-tight leading-tight text-center transition-all text-[24px]',
                                folder.name === '閻王仙師' ? 'text-black' : 'text-white'
                            ]" style="font-weight: 900 !important;">
                                <template v-if="folder.id === 'unobtained'">
                                    未求得<br>重大皇恩
                                </template>
                                <template v-else>
                                    {{ folder.name }}
                                </template>
                            </span>
                        </div>
                    </div>
                </button>
            </div>


            <!-- Minimalist Back to Category -->
            <div class="mt-12 flex justify-center pb-[5px]">
                <button @click="currentCategory = null" class="text-slate-300 hover:text-slate-500 transition-colors flex items-center space-x-2 active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" /></svg>
                    <span class="text-xs tracking-widest uppercase">返回大專區列表</span>
                </button>
            </div>
        </div>

        <!-- Level 2: Folder Contents -->
        <div v-else-if="currentFolder" class="px-0 bg-white min-h-screen">
            <!-- Header for Level 2 -->
            <div class="flex items-center justify-between px-3 py-2 border-b border-slate-50">
                <div class="flex items-center space-x-1">
                    <button @click="handleBack" class="p-2 -ml-2 text-slate-400 active:scale-90 transition-all">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" /></svg>
                    </button>
                    <span class="text-[17px] font-black text-slate-900">{{ displayTitle }}</span>
                </div>
                <button @click="reorderMode = !reorderMode" 
                    :class="reorderMode ? 'bg-white text-emerald-600 border-2 border-emerald-500' : 'bg-slate-100 text-slate-600 border border-transparent'"
                    class="px-4 py-2 rounded-xl text-[14px] font-black transition-all active:scale-95 shadow-sm">
                    {{ reorderMode ? '確認排序' : '修改排序' }}
                </button>
            </div>
            
            <!-- List Display Area -->
            <div style="padding: 0px 8px 10px 8px;" class="mt-0">
                <div v-if="loading" class="text-center py-4 text-xs text-slate-400">載入中...</div>
                <div v-else class="flex flex-col">
                    <!-- Data Rows -->
                    <div v-for="(reg, index) in filteredRegistries" :key="reg.id" 
                        v-show="focusedId === null || focusedId === reg.id"
                        @click="toggleExpand(reg.id)"
                        :class="[
                            'w-full block py-[12px] px-1 border-b border-slate-100 last:border-b-0 relative group transition-all cursor-pointer bg-white active:bg-slate-50 select-none'
                        ]">
                        
                        <!-- List Item Display (Header when collapsed) -->
                        <div v-if="expandedId !== reg.id" class="mt-0 flex items-start">
                            <!-- Number column -->
                            <div class="w-10 shrink-0 pt-0.5">
                                <input v-if="reorderMode" type="number" :value="index + 1" 
                                    @click.stop @change="handleReorder(reg, $event.target.value)"
                                    class="w-8 h-8 rounded-lg bg-indigo-50 border-none text-center text-indigo-600 focus:ring-2 focus:ring-indigo-500 pointer-events-auto">
                                <span v-else class="app-title text-slate-300 ml-2">{{ index + 1 }}</span>
                            </div>

                            <!-- Content column: date on top, name+status on bottom -->
                            <div class="flex flex-col flex-1 min-w-0 pr-8">
                                <!-- Row 1: Date -->
                                <div class="app-title font-bold mb-0.5">
                                    <template v-if="['已登記','已求得'].includes(reg.status) && reg.obtained_date">
                                        登記：<span class="app-title font-bold">{{ reg.obtained_date.replace(/-/g, '/') }}</span>
                                    </template>
                                    <template v-else-if="reg.record_date">
                                        得知：<span class="app-title font-bold">{{ reg.record_date.replace(/-/g, '/') }}</span>
                                    </template>
                                    <template v-else>
                                        <span class="text-slate-300">未知日期</span>
                                    </template>
                                    <span v-if="currentFolder.id === 'unobtained' && reg.master_id" class="ml-2 app-title opacity-50">{{ getMasterName(reg.master_id) }}</span>
                                </div>
                                <!-- Row 2: Name + Status -->
                                <div class="flex items-center justify-between">
                                    <div class="app-body leading-tight truncate">{{ reg.name }}</div>
                                    <span :class="{
                                        'bg-blue-50 text-blue-700 border-blue-200': reg.status === '已求得',
                                        'bg-emerald-50 text-emerald-700 border-emerald-200': reg.status === '已登記',
                                        'bg-pink-100': reg.status === '未求得'
                                    }" 
                                    :style="reg.status === '未求得' ? 'color: #dc2626 !important; border-width: 0px !important;' : ''"
                                    class="app-title font-bold px-2 py-0.5 rounded border select-none whitespace-nowrap shrink-0 ml-2">
                                        {{ reg.status }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Independent Menu Button (Three Dots) -->
                        <div v-if="expandedId !== reg.id" class="absolute right-0 top-1/2 -translate-y-1/2 z-[20] pr-2">
                            <div class="relative" :class="[deleteConfirmId === reg.id ? 'text-red-500' : 'text-slate-400']">
                                <button @click.stop="toggleMenu(reg.id)" class="p-2 -mr-1">
                                    <svg class="h-5 v-5" fill="currentColor" viewBox="0 0 20 20"><path d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM18 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                                </button>
                                <div v-if="openMenuId === reg.id" @click.stop 
                                     class="absolute right-0 top-full mt-1 w-32 bg-white rounded-xl shadow-2xl border border-slate-100 z-[100] overflow-hidden animate-slide-up">
                                    <button @click.stop="toggleExpand(reg.id)" style="color: #4f46e5 !important;" class="w-full p-2.5 text-left app-body hover:bg-indigo-50 border-b border-slate-50">展開清單</button>
                                    <button @click.stop="editItem(reg)" style="color: #3b82f6 !important;" class="w-full p-2.5 text-left app-body hover:bg-slate-50 border-b border-slate-50">修改內容</button>
                                    <button @click.stop="copyOnly(reg)" style="color: #16a34a !important;" class="w-full p-2.5 text-left app-body hover:bg-green-50 border-b border-slate-50 whitespace-nowrap">複製貼 LINE</button>
                                    <button @click.stop="downloadOnly(reg)" style="color: #3b82f6 !important;" class="w-full p-2.5 text-left app-body hover:bg-blue-50 border-b border-slate-50 whitespace-nowrap">單筆檔案下載</button>
                                    <button @click.stop="confirmDelete(reg.id)" style="color: #dc2626 !important;" class="w-full p-2.5 text-left app-body hover:bg-red-50">刪除</button>
                                </div>
                            </div>
                        </div>

                        <!-- Expanded Detail (Pure White Frameless Wide Style) -->
                        <div v-if="expandedId === reg.id" class="animate-fade-in py-3 bg-white space-y-4 relative px-2">
                            <!-- Action Menu in Expanded Mode -->
                            <div class="absolute right-0 top-0 z-[101]">
                                <button @click.stop="toggleMenu(reg.id)" class="p-1 text-slate-400 hover:text-indigo-600 transition-colors">
                                    <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"><path d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM18 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                                </button>
                                <div v-if="openMenuId === reg.id" @click.stop 
                                     class="absolute right-0 top-full mt-1 w-32 bg-white rounded-xl shadow-2xl border border-slate-100 z-[102] overflow-hidden animate-slide-up">
                                    <button @click.stop="toggleExpand(reg.id)" style="color: #4f46e5 !important;" class="w-full p-2.5 text-left app-body hover:bg-indigo-50 border-b border-slate-50">收合清單</button>
                                    <button @click.stop="editItem(reg)" style="color: #3b82f6 !important;" class="w-full p-2.5 text-left app-body hover:bg-slate-50 border-b border-slate-50">修改內容</button>
                                    <button @click.stop="copyOnly(reg)" style="color: #16a34a !important;" class="w-full p-2.5 text-left app-body hover:bg-green-50 border-b border-slate-50 whitespace-nowrap">複製貼 LINE</button>
                                    <button @click.stop="downloadOnly(reg)" style="color: #3b82f6 !important;" class="w-full p-2.5 text-left app-body hover:bg-blue-50 border-b border-slate-50 whitespace-nowrap">單筆檔案下載</button>
                                    <button @click.stop="confirmDelete(reg.id)" style="color: #dc2626 !important;" class="w-full p-2.5 text-left app-body hover:bg-red-50">刪除</button>
                                </div>
                            </div>

                            <!-- Detail Content Grid -->
                            <div class="grid grid-cols-2 gap-3">
                                <div class="space-y-1">
                                    <div class="flex items-center space-x-1 ml-1">
                                        <button @click.stop="toggleExpand(reg.id)" class="p-1 -ml-1 text-slate-400 active:scale-90 transition-all">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" /></svg>
                                        </button>
                                        <label class="app-title tracking-wider block">{{ reg.status === '已登記' ? '登記日期' : '得知日期' }}</label>
                                    </div>
                                    <div class="w-full px-3 flex items-center app-body">
                                        {{ reg.record_date?.replace(/-/g, '/') || '-' }}
                                    </div>
                                </div>
                                <div class="space-y-1">
                                    <label class="app-title tracking-wider block ml-1">載錄目標仙師</label>
                                    <div class="w-full px-3 flex items-center app-body">
                                        {{ getMasterName(reg.master_id) }}
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-1">
                                <label class="app-title tracking-wider block ml-1">法寶名稱</label>
                                <div class="w-full px-3 flex items-center app-body">
                                    {{ reg.name }}
                                </div>
                            </div>

                            <div class="space-y-1">
                                <label class="app-title tracking-wider block ml-1">法寶用意</label>
                                <div class="w-full px-3 py-0.5 flex items-center app-body">
                                    {{ reg.purpose || '-' }}
                                </div>
                            </div>

                             <div class="grid grid-cols-2 gap-3">
                                <div class="space-y-1">
                                    <label class="app-title tracking-wider block ml-1">目前狀態</label>
                                    <div class="w-full px-3 flex items-center app-body"
                                        :style="reg.status === '未求得' ? 'color: #dc2626 !important;' : (reg.status === '已求得' ? 'color: #2563eb !important;' : 'color: #059669 !important;')">
                                        {{ reg.status }}
                                    </div>
                                </div>
                                <div v-if="reg.status !== '已登記'" class="space-y-1">
                                    <label class="app-title tracking-wider block ml-1">求得日期</label>
                                    <div class="w-full px-3 flex items-center app-body">
                                        {{ reg.obtained_date?.replace(/-/g, '/') || '-' }}
                                    </div>
                                </div>
                                <div v-else></div>
                            </div>

                            <div v-if="reg.remarks" class="space-y-1">
                                <label class="app-title tracking-wider block ml-1">詳細內容 / 備註</label>
                                <div class="w-full px-3 py-1 app-body leading-normal whitespace-pre-wrap">
                                    {{ reg.remarks }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div> <!-- End Scrollable Area -->
    
    <mobile-navbar 
        :can-back="true"
        :show-action="!!currentFolder && currentFolder.id !== 'unobtained'"
        :action-active="showAddMenu"
        :search-active="showSearch"
        :can-more="!!currentFolder && currentFolder.id !== 'unobtained'"
        @back="handleBack"
        @home="$emit('goHome')"
        @action="showAddMenu = !showAddMenu"
        @search="showSearch = !showSearch"
        @more="downloadListOnly"
    />


    <!-- MODAL COMPONENTS -->
    <search-component 
        v-model="searchQuery" 
        :show="showSearch" 
        @close="showSearch = false" 
    />

    <add-action-menu 
        :show="showAddMenu" 
        @close="showAddMenu = false"
        :actions="addActions"
    />

    <imperial-grace-add-form 
        :mode="addMode"
        :initialData="form"
        :masters="masters"
        :isSaving="isSaving"
        @saveSingle="saveSingle"
        @saveBatch="triggerBatchSave"
        @cancel="addMode = null"
        @fileUpload="handleFileUpload"
    />
    </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted, defineEmits, watch } from 'vue';
import axios from 'axios';
import MobileNavbar from './MobileNavbar.vue';
import SearchComponent from './SearchComponent.vue';
import AddActionMenu from './AddActionMenu.vue';
import ImperialGraceAddForm from './ImperialGraceAddForm.vue';

const emit = defineEmits(['goHome']);

const currentCategory = ref(null); 
const currentFolder = ref(null);
const addMode = ref(null);
const allRegistries = ref([]);
const masters = ref([]);
const loading = ref(false);
const isSaving = ref(false);
const persistentToast = ref(null); 
const sortDesc = ref(true);
const toggleSort = () => { sortDesc.value = !sortDesc.value; };
const openMenuId = ref(null);
const fileInput = ref(null);
const showAddMenu = ref(false); // 控制底部新增選單
const showSearch = ref(false);
const reorderMode = ref(false);
const deleteConfirmId = ref(null); // 追蹤正在準備刪除的物件
const expandedId = ref(null); // 追蹤目前展開的 ID，單一變數最穩
const focusedId = ref(null); // 追蹤正在「聚焦」的單筆紀錄
const batchInput = ref('');
const batchMasterId = ref(null);
const searchQuery = ref('');
const form = ref({
    id: null, master_id: null, name: '', purpose: '', remarks: '', record_date: '', obtained_date: '', status: '未求得'
});

const addActions = computed(() => [
    { 
        label: '逐筆新增', 
        description: '手動輸入每一項重大皇恩詳細資料',
        icon: '<svg class="text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>',
        handler: () => prepareAdd('single') 
    },
    { 
        label: '多筆一次新增', 
        description: '快速解析 LINE 聊天內容紀錄',
        icon: '<svg class="text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>',
        handler: () => prepareAdd('batch') 
    },
    { 
        label: '複製隨貼 LINE (全部)', 
        description: '將此分類完整清單複製至剪貼簿',
        icon: '<svg class="text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>',
        handler: () => { copyListOnly(); showAddMenu.value = false; }
    },
    { 
        label: '下載匯出 文字檔 (全部)', 
        description: '將此分類匯出為標準純文字檔案',
        icon: '<svg class="text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>',
        handler: () => { exportListTxt(); showAddMenu.value = false; }
    }
]);

const displayTitle = computed(() => {
    const base = '重大皇恩專區';
    if (currentFolder.value) {
        return `${base}-${currentFolder.value.name}`;
    }
    return base;
});

const triggerBatchSave = (data) => {
    saveBatch(data);
};

// 當關閉提示框時，重設紅點狀態；加入自動消失邏輯
watch(persistentToast, (newVal) => {
    if (!newVal) {
        deleteConfirmId.value = null;
        return;
    }
    // 如果不是需要等待使用者決策的類型，則預設 3 秒後自動消失 (支援自訂 duration)
    if (!['confirm', 'deleteConfirm', 'mismatchConfirm'].includes(newVal.type)) {
        setTimeout(() => {
            if (persistentToast.value === newVal) persistentToast.value = null;
        }, newVal.duration || 3000);
    }
});

const folders = computed(() => {
    const list = masters.value.map(m => ({
        id: m.id,
        name: m.name
    }));
    list.push({ id: 'unobtained', name: '未求得重大皇恩' });
    return list;
});

const mastersFolders = computed(() => folders.value.filter(f => f.id !== 'unobtained'));

const loadData = async () => {
    loading.value = true;
    try {
        const [res, mres] = await Promise.all([ axios.get('/imperial-graces'), axios.get('/api/masters-list') ]);
        allRegistries.value = res.data.registries || [];
        masters.value = mres.data || [];
    } catch (e) { console.error(e); }
    finally { loading.value = false; }
};

const formatDate = (d) => d ? new Date(d).toLocaleDateString('zh-TW').replace(/\//g, '/') : '-';

const getMasterName = (id) => {
    const m = masters.value.find(m => m.id === id);
    return m ? m.name : '未知仙師';
};

const toggleMenu = (id) => { 
    if (openMenuId.value === id) {
        openMenuId.value = null;
    } else {
        openMenuId.value = id;
        focusedId.value = id;
        expandedId.value = id;
    }
    deleteConfirmId.value = null;
};
const toggleExpand = (id) => {
    if (expandedId.value === id) {
        expandedId.value = null;
        if (focusedId.value === id) focusedId.value = null;
        openMenuId.value = id;
    } else {
        expandedId.value = id;
        focusedId.value = id;
        openMenuId.value = null;
    }
};

const handleBack = () => {
    if (addMode.value) {
        addMode.value = null;
    } else if (focusedId.value) {
        focusedId.value = null;
        expandedId.value = null;
    } else if (currentFolder.value) {
        currentFolder.value = null;
        reorderMode.value = false;
        searchQuery.value = '';
        expandedId.value = null;
        // If we came directly from unobtained to level 0
        if (currentCategory.value === 'unobtained') {
            currentCategory.value = null;
        }
    } else if (currentCategory.value) {
        currentCategory.value = null;
    } else {
        emit('goHome');
    }
};

watch(currentFolder, () => {
    focusedId.value = null;
    expandedId.value = null;
    openMenuId.value = null;
    searchQuery.value = '';
});

const handleStatusChange = () => { if (form.value.status === '未求得') form.value.obtained_date = ''; };

const editItem = (reg) => { form.value = { ...reg }; addMode.value = 'single'; openMenuId.value = null; };

const confirmDelete = (id) => {
    deleteConfirmId.value = id;
    persistentToast.value = { msg: '確定要刪除這筆資料嗎？', type: 'deleteConfirm' };
    openMenuId.value = null;
};

const executeDelete = async () => {
    if (!deleteConfirmId.value) return;
    try { 
        await axios.delete(`/imperial-graces/registry/${deleteConfirmId.value}`); 
        persistentToast.value = { msg: '✓ 已成功刪除', type: 'success' };
        focusedId.value = null;
        expandedId.value = null;
        loadData(); 
    }
    catch (e) { alert('刪除失敗'); }
    deleteConfirmId.value = null;
};

// 🌟 核心下載：最穩定的同步 Blob 下載 (確保 Chrome 不攔截)
const triggerSimpleDownload = (text, filename) => {
    const blob = new Blob(['\uFEFF' + text], { type: 'text/plain;charset=utf-8' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = filename;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    window.URL.revokeObjectURL(url);
};

// 1. 單筆複製
const copyOnly = async (reg) => {
    const text = `【重大皇恩】\r\n法寶：${reg.name}\r\n數據：${reg.count || 1}\r\n用意：${reg.purpose || '無'}\r\n狀態：${reg.status}\r\n求得日期：${reg.obtained_date || '-'}\r\n由來與備註：${reg.remarks || '無'}`;
    try {
        await navigator.clipboard.writeText(text);
        persistentToast.value = { msg: '已複製資料,可至 LINE 貼上.', type: 'success' };
    } catch (e) {
        persistentToast.value = { msg: '✖ 複製失敗', type: 'error' };
    }
    openMenuId.value = null;
};

// 2. 單筆下載
const downloadOnly = (reg) => {
    const text = `【重大皇恩】\r\n法寶：${reg.name}\r\n數據：${reg.count || 1}\r\n用意：${reg.purpose || '無'}\r\n狀態：${reg.status}\r\n求得日期：${reg.obtained_date || '-'}\r\n由來與備註：${reg.remarks || '無'}`;
    triggerSimpleDownload(text, `重大皇恩_${reg.name}.txt`);
    persistentToast.value = { msg: '已啟動檔案下載.', type: 'success' };
    openMenuId.value = null;
};

const handleReorder = async (reg, newOrderStr) => {
    const newOrder = parseInt(newOrderStr);
    const currentList = [...filteredRegistries.value];
    const oldIndex = currentList.findIndex(item => item.id === reg.id);
    const newIndex = newOrder - 1;
    
    if (isNaN(newOrder) || oldIndex === -1 || newIndex < 0 || newIndex >= currentList.length) {
        persistentToast.value = { msg: '✖ 無效的項次', type: 'error' };
        return;
    }
    
    // Move item in array
    const [movedItem] = currentList.splice(oldIndex, 1);
    currentList.splice(newIndex, 0, movedItem);
    
    // Assign new sort_order to everyone in this filtered list
    const updates = currentList.map((item, idx) => ({
        id: item.id,
        sort_order: idx + 1
    }));
    
    try {
        await axios.post('/imperial-graces/registry/reorder', { orders: updates });
        persistentToast.value = { msg: '✓ 順序更新成功', type: 'success' };
        await loadData();
    } catch (e) {
        persistentToast.value = { msg: '✖ 順序更新失敗', type: 'error' };
    }
};

// 3. 全部複製
const copyListOnly = async () => {
    if (!currentFolder.value) return;
    const contents = `【重大皇恩清單 - ${currentFolder.value.name}】\r\n\r\n` + 
        filteredRegistries.value.map(r => `${r.name}\n  數據：${r.count || 1}\n  用意：${r.purpose || '無'}\n  狀態：${r.status}`).join('\r\n\r\n');
    try {
        await navigator.clipboard.writeText(contents);
        persistentToast.value = { msg: '已複製全部清單,可至 LINE 貼上.', type: 'success' };
    } catch (e) {
        persistentToast.value = { msg: '✖ 複製失敗', type: 'error' };
    }
    openMenuId.value = null;
};

// 4. 全部下載
const downloadListOnly = () => {
    if (!currentFolder.value) return;
    const contents = `【重大皇恩清單 - ${currentFolder.value.name}】\r\n\r\n` + 
        filteredRegistries.value.map(r => `${r.name}\n  數據：${r.count || 1}\n  用意：${r.purpose || '無'}\n  狀態：${r.status}`).join('\r\n\r\n');
    triggerSimpleDownload(contents, `重大皇恩清單_${currentFolder.value.name}.txt`);
    persistentToast.value = { msg: '已啟動清單下載.', type: 'success' };
    openMenuId.value = null;
};

// 5. 文字檔匯出 (全部清單 - 一筆一筆格式)
const exportListTxt = () => {
    if (!currentFolder.value || !filteredRegistries.value.length) return;
    
    const contents = filteredRegistries.value.map(r => {
        return `【重大皇恩】\r\n` +
               `法寶：${r.name}\r\n` +
               `數據：${r.count || 1}\r\n` +
               `用意：${r.purpose || '無'}\r\n` +
               `狀態：${r.status}\r\n` +
               `求得日期：${r.obtained_date?.replace(/-/g, '/') || '-'}\r\n` +
               `由來與備註：${(r.remarks || '無')}`;
    }).join('\r\n\r\n--------------------------------\r\n\r\n');

    try {
        const finalHeader = `【重大皇恩清單 - ${currentFolder.value.name} 完整清單】\r\n\r\n`;
        triggerSimpleDownload(finalHeader + contents, `重大皇恩清單_${currentFolder.value.name}_一筆一筆資料.txt`);
        persistentToast.value = { msg: '✓ 文字檔匯出成功', type: 'success' };
    } catch (e) {
        console.error(e);
        persistentToast.value = { msg: '✖ 匯出失敗', type: 'error' };
    }
};

const prepareAdd = (mode) => {
    const defaultMasterId = currentFolder.value && currentFolder.value.id !== 'unobtained' ? currentFolder.value.id : null;
    form.value = { 
        id: null, 
        master_id: defaultMasterId, 
        name: '', 
        purpose: '', 
        remarks: '', 
        record_date: mode === 'batch' ? '' : new Date().toISOString().split('T')[0], 
        obtained_date: '', 
        status: '未求得' 
    };
    if (mode === 'batch') {
        batchInput.value = '';
        batchMasterId.value = defaultMasterId;
    }
    addMode.value = mode;
    showAddMenu.value = false;
};

const saveSingle = async (resolutionOrData = null) => {
    if (isSaving.value) return;

    let resolution = null;
    if (typeof resolutionOrData === 'string') {
        resolution = resolutionOrData;
    } else if (resolutionOrData && typeof resolutionOrData === 'object' && !resolutionOrData.target) {
        form.value = { ...resolutionOrData };
    }
    
    let formMid = form.value.master_id ? String(form.value.master_id) : '';
    const folderId = currentFolder.value ? String(currentFolder.value.id) : '';

    // 核心判定：是否發生預期外的資料夾分流 (未求得區改為已求得時，改為自動分流不再提示)
    const isActuallyMismatched = folderId !== 'unobtained' && folderId !== '' && formMid !== folderId;

    if (isActuallyMismatched && !resolution) {
        persistentToast.value = { 
            msg: `偵測到法寶將存入【${getMasterName(form.value.master_id)}】，但目前位於【${currentFolder.value.name}】，請問要存入哪一邊？`, 
            type: 'mismatchConfirm' 
        };
        return;
    }

    if (resolution === 'correct') {
        const fid = currentFolder.value?.id;
        form.value.master_id = (fid === 'unobtained') ? form.value.master_id : fid;
    }

    if (!form.value.name) {
        persistentToast.value = { msg: '錯誤：請輸入法寶名稱', type: 'error' };
        return;
    }

    if (!form.value.master_id) {
        persistentToast.value = { msg: '錯誤：請選擇目標仙師', type: 'error' };
        return;
    }

    const targetName = form.value.name.trim().toLowerCase();
    const isDuplicate = allRegistries.value.some(r => r.id !== form.value.id && r.name.trim().toLowerCase() === targetName);

    if (isDuplicate) {
        persistentToast.value = { msg: `✖ 法寶名稱「${form.value.name}」已存在，請勿重複存檔。`, type: 'error' };
        return;
    }
    
    if (['已登記', '已求得'].includes(form.value.status) && !form.value.obtained_date) {
        persistentToast.value = { msg: '錯誤：請輸入求得日期', type: 'error' };
        return;
    }
    
    isSaving.value = true;
    persistentToast.value = null;

    try {
        const finalMid = form.value.master_id;
        const targetMaster = masters.value.find(m => String(m.id) === String(finalMid));
        const targetMasterName = targetMaster ? targetMaster.name : '';

        let res;
        if (form.value.id) {
            res = await axios.post(`/imperial-graces/registry/${form.value.id}`, { ...form.value, _method: 'PATCH' });
            persistentToast.value = { msg: '✓ 修改成功', type: 'success' };
        } else {
            res = await axios.post('/imperial-graces/registry', { ...form.value, master_id: finalMid });
            persistentToast.value = { msg: '✓ 新增成功', type: 'success' };
        }
        
        // 智慧導向：如果選擇分流，或是在「未求得」區改為已求得，依照「ID」匹配資料夾
        const isFromUnobtained = String(currentFolder.value?.id) === 'unobtained' && ['已求得', '已登記'].includes(form.value.status);
        if ((resolution === 'shunt' || isFromUnobtained) && targetMaster) {
            // 先找對應的仙師資料夾 (改用 ID 匹配更精準)
            const matchedFolder = mastersFolders.value.find(f => String(f.id) === String(targetMaster.id));
            if (matchedFolder) {
                currentCategory.value = 'masters';
                currentFolder.value = matchedFolder;
                focusedId.value = res?.data?.id || form.value.id;
                persistentToast.value = { 
                    msg: `✓ 已改成【${form.value.status}】並回存至【${targetMasterName}】資料夾內`, 
                    type: 'success',
                    duration: 1500 
                };
            }
        }
        addMode.value = null; 
        await loadData();
    } catch (e) {
        console.error('儲存失敗:', e);
        const serverMsg = e.response?.data?.message || e.message || '資料驗證失敗';
        persistentToast.value = { msg: `✖ 儲存失敗：${serverMsg}`, type: 'error' };
    } finally {
        isSaving.value = false;
    }
};

const handleFileUpload = (event) => {
    const file = event.target.files[0];
    if (!file) return;
    
    const reader = new FileReader();
    reader.onload = (e) => {
        batchInput.value = e.target.result;
        persistentToast.value = { msg: '✓ 檔案內容已載入', type: 'success' };
    };
    reader.readAsText(file);
    // 重設 input 以便下次選擇同一個檔案也能觸發
    event.target.value = '';
};

const quickToggleStatus = async (reg) => {
    const statuses = ['未求得', '已登記', '已求得'];
    const currentIdx = statuses.indexOf(reg.status);
    const nextIdx = (currentIdx + 1) % statuses.length;
    const nextStatus = statuses[nextIdx];
    await changeStatus(reg, nextStatus);
};

const statusMenuOpenId = ref(null);
const openStatusMenu = (id) => {
    statusMenuOpenId.value = statusMenuOpenId.value === id ? null : id;
};

const changeStatus = async (reg, nextStatus) => {
    statusMenuOpenId.value = null;
    try {
        const payload = { 
            name: reg.name,
            master_id: reg.master_id,
            record_date: reg.record_date,
            obtained_date: reg.obtained_date,
            purpose: reg.purpose,
            remarks: reg.remarks,
            status: nextStatus 
        };
        // Auto-fix date if moving to Obtained or Registered (both require obtained_date in backend)
        if (['已求得', '已登記'].includes(nextStatus) && !payload.obtained_date) {
            payload.obtained_date = new Date().toISOString().split('T')[0];
        }
        const res = await axios.put(`/imperial-graces/registry/${reg.id}`, payload);
        await loadData();

        // 🌟 分流邏輯：如果在「未求得」專區改為其他狀態，自動跳轉回該仙師位子
        if (String(currentFolder.value?.id) === 'unobtained' && nextStatus !== '未求得') {
            const targetMaster = masters.value.find(m => String(m.id) === String(reg.master_id));
            if (targetMaster) {
                // 優先從資料夾清單中找到完整的 folder 物件以確保導航狀態同步
                const matchedFolder = mastersFolders.value.find(f => String(f.id) === String(targetMaster.id));
                if (matchedFolder) {
                    currentCategory.value = 'masters';
                    currentFolder.value = matchedFolder;
                    focusedId.value = reg.id;
                    expandedId.value = reg.id;
                    persistentToast.value = { 
                        msg: `✓ 已改成【${nextStatus}】並回存至【${targetMaster.name}】資料夾內`, 
                        type: 'success',
                        duration: 1500
                    };
                }
            }
        }
    } catch (e) {
        console.error('狀態變更失敗:', e);
        const serverMsg = e.response?.data?.message || '資料驗證失敗';
        persistentToast.value = { msg: `✖ 狀態變更失敗：${serverMsg}`, type: 'error' };
    }
};

const saveBatch = async (payload = null) => {
    // Sync data if coming from the component event
    if (payload && typeof payload === 'object') {
        batchInput.value = payload.input || '';
        batchMasterId.value = payload.masterId || null;
    }

    if (!batchInput.value || !batchMasterId.value || isSaving.value) return;
    
    // Check for duplicates in pre-processed rows
    if (payload && payload.rows && payload.rows.length > 0) {
        const mid = String(batchMasterId.value);
        const existingNames = allRegistries.value.map(r => r.name.trim());
        
        const duplicates = payload.rows
            .map(row => String(row.name || '').trim())
            .filter(name => name && existingNames.includes(name));

        if (duplicates.length > 0) {
            persistentToast.value = { 
                msg: `注意：偵測到 ${duplicates.length} 筆重複名稱（如 ${duplicates[0]}...）`, 
                type: 'error' 
            };
            return;
        }
    }

    isSaving.value = true;
    try {
        const finalMasterId = batchMasterId.value === 'unobtained' ? null : batchMasterId.value;

        // If payload has pre-processed rows, send them directly as 'items'
        const dataToSend = {
            master_id: finalMasterId
        };
        
        if (payload && payload.rows) {
            dataToSend.items = payload.rows;
        } else {
            const lines = batchInput.value.split('\n')
                .map(l => l.trim())
                .filter(l => l && !l.startsWith('【')); 
            if (lines.length === 0) {
                persistentToast.value = { msg: '✖ 內容格式不正確', type: 'error' };
                isSaving.value = false;
                return;
            }
            dataToSend.lines = lines;
        }

        await axios.post('/imperial-graces/registry/batch', dataToSend);
        
        persistentToast.value = { msg: '✓ 多筆新增成功', type: 'success' };
        addMode.value = null;
        loadData();
    } catch (e) { 
        console.error('批次失敗:', e);
        const serverMsg = e.response?.data?.message || e.message || '格式解析錯誤或伺服器連線失敗';
        persistentToast.value = { msg: `✖ 批次新增失敗：${serverMsg}`, type: 'error' };
    } finally {
        isSaving.value = false;
    }
};

onMounted(() => {
    loadData();
    window.addEventListener('click', (e) => { 
        const isOutside = !e.target.closest('.relative');
        if (isOutside) {
            if (openMenuId.value) openMenuId.value = null;
            if (showAddMenu.value) showAddMenu.value = false;
            if (statusMenuOpenId.value) statusMenuOpenId.value = null;
            // expandedId is managed exclusively by the row @click (toggleExpand)
        }
    });
});

const filteredRegistries = computed(() => {
    if (!currentFolder.value) return [];
    
    let filtered = [];
    if (currentFolder.value.id === 'unobtained') {
        filtered = allRegistries.value.filter(r => !r.master_id || r.status === '未求得');
    } else {
        filtered = allRegistries.value.filter(r => r.master_id === currentFolder.value.id);
    }


    // 狀態排序：未求得 → 已求得 → 已登記
    const statusOrder = { '未求得': 1, '已求得': 2, '已登記': 3 };
    
    filtered.sort((a, b) => {
        // Priority 1: Status (always first)
        const orderA = statusOrder[a.status] || 99;
        const orderB = statusOrder[b.status] || 99;
        if (orderA !== orderB) return orderA - orderB;

        // Priority 2: Manual sort_order (within same status)
        if ((a.sort_order || 9999) !== (b.sort_order || 9999)) {
            return (a.sort_order || 9999) - (b.sort_order || 9999);
        }
        
        // Priority 3: Date (within same status & sort_order)
        const dateA = a.record_date || '';
        const dateB = b.record_date || '';
        if (dateA !== dateB) {
            return sortDesc.value ? dateB.localeCompare(dateA) : dateA.localeCompare(dateB);
        }
        
        // Priority 4: ID Fallback
        return sortDesc.value ? ((b.id || 0) - (a.id || 0)) : ((a.id || 0) - (b.id || 0));
    });

    if (searchQuery.value.trim()) {
        const query = searchQuery.value.trim().toLowerCase();
        filtered = filtered.filter(r => {
            const mName = getMasterName(r.master_id).toLowerCase();
            return r.name.toLowerCase().includes(query) ||
                   (r.purpose || '').toLowerCase().includes(query) ||
                   (r.remarks || '').toLowerCase().includes(query) ||
                   mName.includes(query);
        });
        
        // Matches always move to top regardless of global sort
        filtered.sort((a, b) => {
            const am = a.name.toLowerCase().includes(query) ? 1 : 0;
            const bm = b.name.toLowerCase().includes(query) ? 1 : 0;
            return bm - am;
        });
    }

    return filtered;
});

const folderTotalCount = computed(() => {
    return filteredRegistries.value.reduce((sum, r) => sum + (Number(r.count) || 1), 0);
});

const globalTotalCount = computed(() => {
    return allRegistries.value.reduce((sum, r) => sum + (Number(r.count) || 1), 0);
});

const getFolderSum = (id) => {
    let filtered = [];
    if (id === 'unobtained') {
        filtered = allRegistries.value.filter(r => !r.master_id || r.status === '未求得');
    } else {
        filtered = allRegistries.value.filter(r => String(r.master_id) === String(id));
    }
    return filtered.reduce((sum, r) => sum + (Number(r.count) || 1), 0);
};
</script>

<style scoped>
.animate-fade-in { animation: fadeIn 0.3s ease-out; }
.animate-slide-up { animation: slideUp 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
@keyframes fadeIn { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
@keyframes slideUp { from { opacity: 0; transform: translate(-50%, 20px); } to { opacity: 1; transform: translate(-50%, 0); } }

/* Custom Scrollbar for a cleaner mobile look */
.custom-scrollbar::-webkit-scrollbar { width: 6px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
</style>
