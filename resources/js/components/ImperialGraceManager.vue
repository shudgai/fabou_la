<template>
    <div class="bg-white h-full flex flex-col relative overflow-clip text-slate-900 imperial-grace-module overscroll-none">
        <!-- Global Dual Header System -->
        <!-- Header 1: Module Level (Shown ONLY when not in a folder/add mode) -->
        <div v-if="!currentFolder && !addMode" 
            class="border-b border-white flex items-center bg-white sticky top-0 z-[110] w-full transition-all duration-300 min-h-[40px] md:min-h-0"
            style="padding: 4px 15px;">
            <div class="flex-1 flex items-center min-w-0 py-1 pl-1 cursor-pointer" @click="resetToRoot">
                <h1 class="text-red-600 leading-tight font-outfit tracking-widest break-words font-black whitespace-nowrap" style="color: #dc2626 !important; font-size: 32px !important; padding-top: 5px !important; font-weight: 900 !important;">
                    重大皇恩專區
                </h1>
            </div>
        </div>

        <!-- Header 2: Action/Folder Level (Shown when in a folder or add mode) -->
        <div v-if="currentFolder || addMode" 
            class="border-b border-white flex items-center bg-white sticky top-0 z-[110] w-full transition-all duration-300 min-h-[40px] md:min-h-0"
            style="padding: 4px 15px; padding-top: env(safe-area-inset-top, 0px);">
            <div v-if="addMode && !currentFolder" class="flex items-center w-full">
                <button @click="addMode = null" class="p-2 -ml-2 text-slate-400 active:scale-90 transition-all mr-1">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" /></svg>
                </button>
                <div class="flex-1 flex flex-col justify-start min-w-0 py-1 pl-1 cursor-pointer">
                    <div class="leading-tight font-outfit tracking-widest break-words" style="color: #dc1428 !important; font-size: 32px !important; font-weight: 900 !important;">
                        新增重大皇恩
                    </div>
                </div>
            </div>
            <div v-else class="flex-1 flex items-center min-w-0 py-1 pl-1 cursor-pointer" @click="resetToRoot">
                <!-- Mobile only title here, Desktop will use the sticky internal header -->
                <h1 class="text-red-600 leading-tight font-outfit tracking-widest break-words font-black md:hidden whitespace-nowrap" style="color: #dc2626 !important; font-size: 32px !important; padding-top: 5px !important; font-weight: 900 !important;">
                    重大皇恩專區
                </h1>
            </div>
            <div v-if="currentFolder" class="flex items-center space-x-2 ml-auto md:hidden pr-2" style="padding-top: 10px;">
                <button v-if="focusedId" @click="handleBack" class="w-7 h-7 flex items-center justify-center rounded-full bg-slate-100 text-slate-500 hover:bg-slate-200 active:scale-90 transition-all shadow-sm border border-white" title="回到清單">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

        </div>

        <!-- Perfectly Centered Premium Confirmation / Status Modal -->
        <teleport to="body">
        <div v-if="persistentToast" class="fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 z-[9999] pointer-events-auto">
            <div class="bg-white rounded-3xl shadow-[0_20px_60px_rgba(0,0,0,0.3)] flex flex-col border border-white overflow-hidden" style="padding: 28px; min-width: 320px; max-width: calc(100vw - 32px);">
                <div class="flex items-start justify-between mb-8">
                    <span class="text-[17px] font-black text-slate-900 leading-relaxed tracking-widest">
                        {{ persistentToast.msg }}
                    </span>
                    <button @click="persistentToast = null" class="ml-6 text-slate-400 hover:text-slate-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="3" stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div v-if="['confirm', 'deleteConfirm', 'mismatchConfirm'].includes(persistentToast.type)" class="flex space-x-4">
                    <template v-if="persistentToast.type === 'deleteConfirm'">
                        <button @click="persistentToast = null" class="flex-1 bg-slate-100 text-slate-600 h-[48px] rounded-2xl border border-white text-[17px] font-black tracking-widest active:scale-95 transition-all">取消</button>
                        <button @click="executeDelete" class="flex-1 bg-red-600 h-[48px] rounded-2xl border border-white text-[17px] font-black tracking-widest active:scale-95 transition-all shadow-md" style="color: white !important;">確定刪除</button>
                    </template>
                    <template v-else-if="persistentToast.type === 'mismatchConfirm'">
                        <button @click="saveSingle('correct')" class="flex-1 bg-amber-500 h-[48px] rounded-2xl border border-white text-[15px] font-black tracking-tighter active:scale-95 transition-all whitespace-nowrap shadow-md" style="color: white !important;">存入{{ currentFolder?.name }}</button>
                        <button @click="saveSingle('shunt')" class="flex-1 bg-indigo-600 h-[48px] rounded-2xl border border-white text-[15px] font-black tracking-tighter active:scale-95 transition-all whitespace-nowrap shadow-md" style="color: white !important;">存入{{ getMasterName(form.master_id) }}</button>
                    </template>
                    <template v-else>
                        <button @click="saveSingle('shunt')" class="flex-1 bg-indigo-600 h-[48px] rounded-2xl border border-white text-[17px] font-black tracking-widest active:scale-95 transition-all shadow-md" style="color: white !important;">確定</button>
                    </template>
                </div>
                <div v-else class="flex justify-end mt-2">
                    <button @click="persistentToast = null" class="bg-indigo-600 px-8 py-2.5 rounded-2xl text-[17px] font-black tracking-widest active:scale-95 transition-all shadow-md" style="color: white !important;">確定</button>
                </div>
            </div>
        </div>
        </teleport>
        <div ref="scrollContainer" class="flex-1 overflow-y-auto custom-scrollbar overscroll-contain" style="padding-bottom: 120px;">
        <!-- Level 0: Main Category Selection -->
        <div v-if="!currentCategory && !currentFolder && !addMode" class="h-full bg-white flex flex-col items-center">
            <div class="flex-1 flex flex-col items-center justify-center md:justify-start md:pt-4 pb-20 w-full max-w-lg mx-auto">
                <button 
                    @click="currentCategory = 'masters'"
                    class="flex flex-col items-center justify-center bg-white active:scale-95 rounded-none p-0 w-[310px] h-[310px] relative transition-all">
                    <div class="relative w-[310px] h-[310px]">
                        <svg class="w-full h-full" viewBox="0 0 64 64" fill="none">
                            <path d="M4 14C4 11.7909 5.79086 10 8 10H24.5L30 16H56C58.2091 16 60 17.7909 60 20V50C60 52.2091 58.2091 54 56 54H8C5.79086 54 4 52.2091 4 50V14Z" fill="#fbbf24" />
                            <path d="M4 22C4 19.7909 5.79086 18 8 18H56C58.2091 18 60 19.7909 60 22V50C60 52.2091 58.2091 54 56 54H8C5.79086 54 4 52.2091 4 50V22Z" fill="#fbbf24" stroke="rgba(255,255,255,0.6)" stroke-width="1"/>
                        </svg>
                        <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none pt-10">
                            <div class="leading-tight text-center !font-black !text-white" style="font-size: 42px !important;">重大皇恩<br>專區</div>
                            <div class="mt-4 flex items-center space-x-2">
                                <span class="text-black font-normal tracking-tight" style="font-size: 17px !important;">共 {{ totalCount }} 筆</span>
                            </div>
                        </div>
                    </div>
                </button>

                <!-- 第二個按鈕：未求得重大皇恩 (直接進入內容) -->
                <button 
                    @click="currentFolder = { id: 'unobtained', name: '未求得重大皇恩' }; currentCategory = 'masters'"
                    class="flex flex-col items-center justify-center bg-white active:scale-95 rounded-none p-0 w-[310px] h-[310px] relative transition-all mt-[-20px]">
                    <div class="relative w-[310px] h-[310px]">
                        <svg class="w-full h-full" viewBox="0 0 64 64" fill="none">
                            <path d="M4 14C4 11.7909 5.79086 10 8 10H24.5L30 16H56C58.2091 16 60 17.7909 60 20V50C60 52.2091 58.2091 54 56 54H8C5.79086 54 4 52.2091 4 50V14Z" fill="#fbbf24" />
                            <path d="M4 22C4 19.7909 5.79086 18 8 18H56C58.2091 18 60 19.7909 60 22V50C60 52.2091 58.2091 54 56 54H8C5.79086 54 4 52.2091 4 50V22Z" fill="#fbbf24" stroke="rgba(255,255,255,0.6)" stroke-width="1"/>
                        </svg>
                        <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none pt-10">
                            <div class="leading-tight text-center !font-black !text-white" style="font-size: 42px !important;">未求得<br>重大皇恩</div>
                            <div class="mt-4 flex items-center space-x-2">
                                <span class="text-black font-normal tracking-tight" style="font-size: 17px !important;">共 {{ unobtainedTotal }} 筆</span>
                            </div>
                        </div>
                    </div>
                </button>
            </div>
        </div>

        <div v-if="currentCategory === 'masters' && !currentFolder && !addMode" class="bg-white max-w-xl mx-auto">
            <div class="pt-[5px] pb-2 flex items-center relative min-h-[10px] cursor-pointer" @click="resetToRoot">
            </div>

            <div class="grid grid-cols-2 gap-[10px] p-2 place-items-center">
                <button v-for="(folder, idx) in mastersFolders" :key="folder.id" 
                    @click="currentFolder = folder"
                    class="flex flex-col items-center justify-center active:scale-95 transition-all p-2 w-[198px] h-[198px] relative group rounded-none">
                    <div class="relative w-[163px] h-[163px]">
                        <svg class="w-full h-full transition-transform group-hover:scale-105" viewBox="0 0 64 64" fill="none">
                            <path d="M4 14C4 11.7909 5.79086 10 8 10H24.5L30 16H56C58.2091 16 60 17.7909 60 20V50C60 52.2091 58.2091 54 56 54H8C5.79086 54 4 52.2091 4 50V14Z" fill="url(#ig-folderGradBase)" style="fill: #fbbf24;" />
                            <path d="M4 22C4 19.7909 5.79086 18 8 18H56C58.2091 18 60 19.7909 60 22V50C60 52.2091 58.2091 54 56 54H8C5.79086 54 4 52.2091 4 50V22Z" fill="url(#ig-folderGradBase)" style="fill: #fbbf24;" stroke="rgba(255,255,255,0.6)" stroke-width="1"/>
                        </svg>
                        
                        <!-- Label & Pill Inside -->
                        <div class="absolute inset-0 flex flex-col items-center justify-center pt-6 px-2 pointer-events-none">
                            <div class="tracking-tight leading-tight text-center whitespace-nowrap mb-2 !font-black"
                                 :class="folder.name === '閻王仙師' ? 'text-slate-900' : 'text-white'"
                                 style="font-size: 24px !important;">
                                 {{ folder.id === 'unobtained' ? '未求得' : (folder.name === '父皇仙師' ? '父皇' : folder.name) }}
                            </div>
                            <div class="mt-1 flex items-center">
                                <span class="text-black font-black" style="font-size: 17px !important;">{{ folderCounts[folder.id] || 0 }} 筆</span>
                            </div>
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
        <div v-else-if="currentFolder && !addMode" class="px-0 bg-white w-full md:max-w-xl md:mx-auto">
            <!-- Desktop Centered Header Section -->
            <div class="hidden md:flex flex-col border-b border-slate-100 bg-white sticky top-0 z-[60]">
                <div class="px-[15px] pt-0">
                    <h1 class="text-red-600 leading-tight font-outfit tracking-widest break-words font-black whitespace-nowrap" style="color: #dc2626 !important; font-size: 32px !important; padding-top: 5px !important; font-weight: 900 !important;">
                        重大皇恩專區
                    </h1>
                </div>
                <div class="flex items-end justify-between w-full px-[15px] pt-0 pb-[4px]">
                    <span :class="currentFolder?.name === '閻王仙師' ? 'text-slate-900' : '!text-[#dc2626]'" class="font-outfit whitespace-nowrap !font-medium" style="font-size: 28px !important;">{{ currentFolder?.name }}</span>
                    <div class="flex items-center space-x-2 shrink-0 ml-4">
                        <button v-if="!reorderMode" @click="toggleSort" class="px-4 py-1.5 bg-indigo-600 border border-indigo-500 rounded-xl active:scale-95 transition-all font-black shadow-sm" style="color: white !important; font-size: 16px !important;">
                            {{ sortDesc ? '新→舊' : '舊→新' }}
                        </button>
                        <button v-if="currentFolder" @click="reorderMode = !reorderMode" 
                                :class="reorderMode ? 'bg-emerald-600 text-white border-2 border-emerald-500 shadow-lg' : 'bg-slate-50 text-[#64748b] border border-transparent'"
                                class="px-3 py-1.5 rounded-xl font-black transition-all active:scale-95 whitespace-nowrap shadow-sm"
                                style="font-size: 16px !important;">
                            {{ reorderMode ? '確認排序' : '修改排序' }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- Header for Level 2 (Mobile Only) -->
            <div class="flex flex-col border-b border-white w-full bg-white md:hidden sticky top-0 z-[60]">
                <div class="px-[15px] pt-0 pb-[8px] bg-slate-50/50 flex items-center justify-between relative">
                    <span :class="currentFolder?.name === '閻王仙師' ? 'text-slate-900' : '!text-[#dc2626]'" class="font-outfit whitespace-nowrap !font-medium" style="font-size: 28px !important;">{{ currentFolder?.name }}</span>
                    <div class="flex items-center space-x-2 shrink-0">
                        <button v-if="!reorderMode" @click="toggleSort" class="px-2 py-0.5 text-indigo-600 font-outfit !font-bold active:scale-95 transition-all" style="font-size: 16px !important;">
                            {{ sortDesc ? '新→舊' : '舊→新' }}
                        </button>
                        <button v-if="!focusedId" @click="reorderMode = !reorderMode" 
                                class="px-2 py-0.5 font-outfit !font-bold transition-all active:scale-95 whitespace-nowrap"
                                :class="reorderMode ? '!text-[#059669]' : '!text-[#64748b]'"
                                style="font-size: 16px !important;">
                            {{ reorderMode ? '確認排序' : '修改排序' }}
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- List Display Area -->
            <div style="padding: 0px 15px 10px 15px;" class="mt-0 relative">
                <div v-if="loading" class="absolute inset-0 z-20 flex items-start justify-center pt-10 bg-white/60 pointer-events-none">
                    <div class="inline-block animate-spin rounded-full h-6 w-6 border-4 border-slate-100 border-t-indigo-600"></div>
                </div>
                <div class="flex flex-col">
                    <div v-if="showSearch" class="pb-3 animate-fade-in">
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-indigo-400 group-focus-within:text-indigo-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </div>
                            <input v-model="searchQuery" type="text" placeholder="搜尋..."
                                class="block w-full pl-11 pr-12 h-[52px] bg-slate-50 border-2 border-transparent focus:border-indigo-100 focus:bg-white rounded-2xl text-[17px] font-black font-outfit text-slate-800 placeholder-slate-300 transition-all outline-none shadow-sm">
                            <button v-if="searchQuery" @click="searchQuery = ''" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-300 hover:text-red-500 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </button>
                        </div>
                    </div>
                    <!-- Data Rows -->
                    <div v-for="(reg, index) in filteredRegistries" :key="reg.id" 
                        v-show="focusedId === null || focusedId === reg.id"
                        @click="toggleExpand(reg.id)"
                        :class="[
                            'w-full block p-2 border-b border-slate-200 last:border-b-0 relative group transition-all cursor-pointer bg-white active:bg-slate-50 select-none',
                            openMenuId === reg.id ? 'z-[50]' : 'z-0'
                        ]">
                        
                        <!-- List Item Display (Header when collapsed) -->
                        <div v-if="expandedId !== reg.id" class="mt-0 flex items-center">
                            <!-- Number column -->
                            <div class="w-10 shrink-0">
                                <input v-if="reorderMode" type="number" :value="index + 1" 
                                    @click.stop @change="handleReorder(reg, $event.target.value)"
                                    class="w-9 h-9 rounded-xl bg-indigo-50 border-none text-center text-indigo-600 font-black focus:ring-2 focus:ring-indigo-500 pointer-events-auto shadow-inner">
                                <span v-else class="text-[16px] text-slate-300 font-black ml-2 font-outfit">{{ String(index + 1).padStart(2, '0') }}</span>
                            </div>

                            <!-- Content column: date on top, name+status on bottom -->
                            <div class="flex flex-col flex-1 min-w-0">
                                <!-- Row 1: Date + Status -->
                                <div class="mb-0.5 flex items-center justify-between">
                                    <div>
                                        <template v-if="['已登記','已求得'].includes(reg.status) && reg.obtained_date">
                                            <span class="text-[11px] font-black text-slate-400 uppercase tracking-widest font-outfit">日期：</span>
                                            <span class="text-[15px] font-bold text-slate-400 font-outfit">{{ formatDate(reg.obtained_date) }}</span>
                                        </template>
                                        <template v-else-if="reg.record_date">
                                            <span class="text-[11px] font-black text-slate-400 uppercase tracking-widest font-outfit">得知：</span>
                                            <span class="text-[15px] text-slate-900 font-outfit !font-normal">{{ formatDate(reg.record_date) }}</span>
                                        </template>
                                    </div>
                                    <span :class="[
                                        'px-3 py-0.5 rounded-full tracking-widest select-none whitespace-nowrap shrink-0 border scale-[0.8] origin-right !font-black !text-[17px]',
                                        reg.status === '已求得' ? 'bg-blue-50 border-blue-200 text-blue-600' : 
                                        reg.status === '已登記' ? 'bg-emerald-50 border-emerald-200 text-emerald-600' : 
                                        'bg-rose-50 border-rose-200 text-rose-600'
                                    ]">
                                        {{ reg.status }}
                                    </span>
                                </div>
                                <!-- Row 1b: Master (Only for Unobtained folder) -->
                                <div v-if="currentFolder.id === 'unobtained' && reg.master_id" 
                                     class="text-[11px] font-black mb-0.5 tracking-wider uppercase font-outfit"
                                     :class="getMasterName(reg.master_id) === '閻王仙師' ? 'text-slate-900' : 'text-red-600'">
                                    {{ getMasterName(reg.master_id) }}
                                </div>
                                <!-- Row 2: Name -->
                                <div class="text-[17px] font-black text-slate-900 leading-tight truncate font-outfit">{{ reg.name }}</div>
                            </div>
                        </div>


                        <!-- Full-page Expanded Overlay -->
                        <teleport to="body">
                        <div v-if="expandedId === reg.id" class="fixed inset-0 z-[500] animate-fade-in">
                            <!-- Backdrop -->
                            <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm" @click="toggleExpand(reg.id)"></div>
                            <!-- Content Panel -->
                            <div class="absolute inset-0 flex items-end md:items-center md:justify-center pointer-events-none">
                            <div class="w-full h-full md:h-auto bg-white flex flex-col md:max-w-xl md:max-h-[90vh] md:rounded-[32px] md:shadow-2xl overflow-hidden animate-slide-up pointer-events-auto">
                                <!-- Header -->
                                <div class="shrink-0 bg-white flex flex-col w-full border-b border-slate-100">
                                    <!-- Global Main Title + Master Name (same row) -->
                                        <div class="flex-1 px-[15px] py-[8px] flex items-end justify-between pr-12">
                                            <div class="flex items-baseline flex-wrap gap-x-2">
                                                <h1 class="uppercase tracking-widest font-outfit !font-black !text-[#dc2626] whitespace-nowrap" style="font-size: 32px !important; padding-top: 5px !important;">重大皇恩登記簿</h1>
                                                <span :class="currentFolder?.name === '閻王仙師' ? '!text-[#0f172a]' : '!text-[#dc2626]'" class="font-outfit whitespace-nowrap !font-medium" style="font-size: 28px !important;">{{ currentFolder?.name }}</span>
                                            </div>
                                            <!-- Three dots menu in expanded view (Moved to title row) -->
                                            <div class="relative z-[20]">
                                                <button @click.stop="toggleMenu(reg.id)" class="w-10 h-10 flex items-center justify-center text-red-500 active:scale-90 transition-all rounded-full hover:bg-red-50">
                                                    <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 20 20"><path d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM18 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                                                </button>
                                                <div v-if="openMenuId === reg.id" @click.stop 
                                                     class="absolute right-0 top-full mt-1 w-auto min-w-[140px] bg-white opacity-100 rounded-xl shadow-2xl border border-slate-100 z-[110] overflow-hidden animate-slide-up">
                                                    <button @click.stop="toggleExpand(reg.id); openMenuId = null" class="w-full p-3 text-left text-[17px] font-black text-slate-900 hover:bg-indigo-50 border-b border-slate-50 whitespace-nowrap">
                                                        {{ expandedId === reg.id ? '收起清單' : '展開清單' }}
                                                    </button>
                                                    <button v-if="inlineEditId !== reg.id" @click.stop="editItem(reg); openMenuId = null" class="w-full p-3 text-left text-[17px] font-black text-slate-900 hover:bg-slate-50 border-b border-slate-50 whitespace-nowrap">修改內容</button>
                                                    <button v-else @click.stop="cancelInlineEdit()" class="w-full p-3 text-left text-[17px] font-black text-red-600 hover:bg-red-50 border-b border-slate-50 whitespace-nowrap">取消修改</button>
                                                    <button @click.stop="copyAsTextFile(reg); openMenuId = null" class="w-full p-3 text-left text-[17px] font-black text-slate-900 hover:bg-slate-50 border-b border-slate-50 whitespace-nowrap">複製貼 LINE</button>
                                                    <button @click.stop="downloadOnly(reg)" class="w-full p-3 text-left text-[17px] font-black text-slate-900 hover:bg-blue-50 border-b border-slate-50 whitespace-nowrap">下載檔案</button>
                                                    <button @click.stop="confirmDelete(reg.id)" class="w-full p-3 text-left text-[17px] font-black text-red-600 hover:bg-red-50">刪除</button>
                                                </div>
                                            </div>
                                        </div>
                                    
                                    <!-- Close Button moved to top right absolute -->
                                    <button @click="toggleExpand(reg.id)" class="absolute right-3 top-3 z-[100] w-10 h-10 bg-slate-100 rounded-full flex items-center justify-center text-slate-500 active:scale-90 transition-all">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    </button>
                                    
                                </div>
                                <!-- Scrollable Content -->
                                <div class="flex-1 overflow-y-auto px-[15px] pt-2 pb-32 custom-scrollbar">
                        <div class="animate-fade-in bg-white space-y-4 relative">

                            <!-- INLINE EDITING FORM -->
                            <div v-if="inlineEditId === reg.id" class="space-y-4 animate-fade-in pb-4">
                                <div class="grid grid-cols-2 gap-3">
                                    <div class="space-y-1">
                                        <label class="app-title tracking-wider block text-slate-400 font-bold">得知日期</label>
                                        <div class="relative">
                                            <input v-model="inlineEditData.record_date" type="text" class="w-full p-2 border rounded-xl text-[16px] font-bold">
                                            <button @click="activePicker = { field: 'record_date', title: '修改得知日期' }" class="absolute right-2 top-1/2 -translate-y-1/2 text-slate-400">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="space-y-1">
                                        <label class="app-title tracking-wider block text-slate-400 font-bold">載錄目標仙師</label>
                                        <select v-model="inlineEditData.master_id" class="w-full p-2 border rounded-xl text-[16px] font-bold">
                                            <option v-for="m in masters" :key="m.id" :value="m.id">{{ m.name }}</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="space-y-1">
                                    <label class="app-title tracking-wider block text-slate-400 font-bold">法寶名稱</label>
                                    <input v-model="inlineEditData.name" type="text" class="w-full p-2 border rounded-xl text-[17px] font-black text-indigo-700">
                                </div>

                                <div class="space-y-1">
                                    <label class="app-title tracking-wider block text-slate-400 font-bold">法寶用意</label>
                                    <textarea v-model="inlineEditData.purpose" rows="2" class="w-full p-2 border rounded-xl text-[16px] font-bold"></textarea>
                                </div>

                                <!-- Inline Personnel Editor (For Multi-person) -->
                                <div v-if="inlineEditData.is_multi || (inlineEditData.dharma_name_registries && inlineEditData.dharma_name_registries.length > 0)" class="space-y-2">
                                    <div class="flex items-center justify-between">
                                        <label class="app-title tracking-wider text-slate-400 font-bold">人員名單</label>
                                        <button @click="addPersonnelInline" class="text-[12px] bg-indigo-600 px-2 py-1 rounded-lg font-bold shadow-sm active:scale-95" style="color: white !important;">＋ 新增人員</button>
                                    </div>
                                    <div class="space-y-2 border-t pt-2">
                                        <div v-for="(p, idx) in inlineEditData.dharma_name_registries" :key="idx" class="bg-slate-50 p-2 rounded-xl border border-slate-100 space-y-2">
                                            <div class="flex items-center space-x-2">
                                                <input v-model="p.custom_name" placeholder="法號" class="flex-1 p-2 border rounded-lg text-[16px] font-bold">
                                                <select v-model="p.status" class="w-24 p-2 border rounded-lg text-[16px] font-bold">
                                                    <option>已登記</option>
                                                    <option>已求得</option>
                                                    <option>未求得</option>
                                                </select>
                                                <button @click="inlineEditData.dharma_name_registries.splice(idx, 1)" class="text-red-400 p-1">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                                </button>
                                            </div>
                                            <div class="grid grid-cols-2 gap-2">
                                                <div class="relative">
                                                    <input v-model="p.obtained_date" placeholder="求得日期" class="w-full p-2 border rounded-lg text-[16px] pr-8">
                                                    <button @click="activePicker = { idx, field: 'obtained_date', title: (p.custom_name || '人員') + '求得日期' }" class="absolute right-1 top-1/2 -translate-y-1/2 text-slate-400 p-1">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2v12a2 2 0 002 2z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                                    </button>
                                                </div>
                                                <div class="relative flex items-center border rounded-lg bg-white overflow-visible">
                                                    <input v-model="p.remarks" placeholder="備註" 
                                                        @focus="activeRelDropdownIdx = idx"
                                                        class="w-full bg-transparent border-none p-2 text-[16px] focus:ring-0 outline-none">
                                                    <button @click.stop="activeRelDropdownIdx = (activeRelDropdownIdx === idx ? null : idx)" class="p-1 text-slate-400 hover:text-indigo-500 transition-all">
                                                        <svg class="w-4 h-4" :class="activeRelDropdownIdx === idx ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                                    </button>
                                                    <div v-if="activeRelDropdownIdx === idx" class="absolute left-0 top-full mt-1 w-full bg-white rounded-xl shadow-[0_10px_30px_rgba(0,0,0,0.15)] border border-slate-100 z-[610] overflow-hidden p-1 animate-fade-in max-h-[200px] overflow-y-auto custom-scrollbar">
                                                        <div v-for="opt in relationshipOptions" :key="opt"
                                                             @click.stop="p.remarks = opt; activeRelDropdownIdx = null"
                                                             class="px-3 py-2 flex items-center rounded-lg hover:bg-indigo-50 font-bold text-[12px] text-slate-900 active:bg-indigo-100 transition-all cursor-pointer">
                                                            {{ opt }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div v-else class="grid grid-cols-2 gap-3">
                                    <div class="space-y-1">
                                        <label class="app-title tracking-wider block text-slate-400 font-bold">目前狀態</label>
                                        <select v-model="inlineEditData.status" class="w-full p-2 border rounded-xl text-[16px] font-bold">
                                            <option>已登記</option>
                                            <option>已求得</option>
                                            <option>未求得</option>
                                        </select>
                                    </div>
                                    <div class="space-y-1">
                                        <label class="app-title tracking-wider block text-slate-400 font-bold">求得日期</label>
                                        <div class="relative">
                                            <input v-model="inlineEditData.obtained_date" type="text" class="w-full p-2 border rounded-xl text-[16px] font-bold">
                                            <button @click="activePicker = { field: 'obtained_date', title: '修改求得日期' }" class="absolute right-2 top-1/2 -translate-y-1/2 text-slate-400">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2v12a2 2 0 002 2z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="space-y-1">
                                    <label class="app-title tracking-wider block text-slate-400 font-bold">備註</label>
                                    <textarea v-model="inlineEditData.remarks" rows="2" class="w-full p-2 border rounded-xl text-[16px]"></textarea>
                                </div>

                                <div class="flex space-x-2 pt-2">
                                    <button @click="saveInlineEdit" :disabled="isSaving" 
                                        class="flex-1 py-3 bg-indigo-600 rounded-xl font-black text-[17px] shadow-lg active:scale-95 transition-all" style="color: white !important;">
                                        {{ isSaving ? '儲存中...' : '確認儲存' }}
                                    </button>
                                    <button @click="cancelInlineEdit" class="px-6 py-3 bg-slate-400 rounded-xl font-bold shadow-sm active:scale-95 transition-all" style="color: white !important;">
                                        取消
                                    </button>
                                </div>
                            </div>

                            <!-- Detail Content Grid (Read Only View) -->
                            <div v-else class="space-y-4">
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="space-y-1">
                                        <label class="app-title tracking-wider block text-slate-500 font-bold">{{ reg.status === '已登記' ? '登記日期' : '得知日期' }}</label>
                                        <div class="text-[15px] font-normal font-outfit !text-[#0d0d0d] !font-normal">{{ formatDate(reg.record_date) }}</div>
                                    </div>
                                    <div class="space-y-1 text-right pr-8">
                                        <label class="app-title tracking-wider block text-slate-500 font-bold">載錄目標仙師</label>
                                        <div class="app-body font-bold text-slate-900">{{ getMasterName(reg.master_id) }}</div>
                                    </div>
                                </div>

                                <!-- IF MULTI-PERSON ASSOCIATION: Show Table Layout (Figure 2) -->
                                <div v-if="reg.is_multi || (reg.dharma_name_registries && reg.dharma_name_registries.length > 0)" class="mt-4 animate-fade-in">
                                    <div class="mb-3">
                                        <label class="app-title tracking-wider text-slate-500 font-bold mr-1">法寶名稱</label>
                                        <span class="font-outfit !font-medium !text-slate-900" style="font-size: 17px !important;">{{ reg.name }}</span>
                                    </div>

                                    <div v-if="reg.purpose && reg.purpose !== '-' && reg.purpose !== '無'" class="mb-4">
                                        <label class="app-title tracking-wider text-slate-500 font-bold mr-1">法寶用意</label>
                                        <div class="mt-1 text-[17px] font-normal text-slate-900 leading-relaxed bg-slate-50 p-2 rounded-lg border border-slate-100">{{ reg.purpose }}</div>
                                    </div>

                                    <div class="overflow-x-auto border border-slate-200 rounded-xl shadow-sm">
                                        <table class="w-full text-[17px] border-collapse bg-white">
                                            <thead class="bg-slate-50/50">
                                                <tr class="text-slate-600 font-black border-b border-slate-200">
                                                    <th class="py-1.5 px-2 text-left border-r border-slate-200 whitespace-nowrap text-[15px] w-[70px]">法號</th>
                                                    <th class="py-1.5 px-2 text-left border-r border-slate-200 text-[15px] w-[80px]">日期</th>
                                                    <th class="py-1.5 px-2 text-left border-r border-slate-200 whitespace-nowrap text-[15px] w-[95px]">狀態</th>
                                                    <th class="py-1.5 px-2 text-left text-[15px]">備註</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="dnr in reg.dharma_name_registries" :key="dnr.id" class="border-b border-slate-100 last:border-0 hover:bg-slate-50 transition-colors">
                                                    <td class="py-1.5 px-2 border-r border-slate-100 font-black text-slate-900 text-[17px]">
                                                        {{ dnr.dharma_name?.name || dnr.custom_name }}{{ dnr.related_personnel && dnr.related_personnel.length > 0 ? '(' + translateRelList(dnr.related_personnel) + ')' : '' }}
                                                    </td>
                                                    <td class="py-1.5 px-2 border-r border-slate-100 font-normal text-slate-500 whitespace-nowrap text-[15px]">{{ formatDate(dnr.obtained_date) }}</td>
                                                    <td class="py-1.5 px-2 border-r border-slate-100 font-black whitespace-nowrap text-[15px]" :class="dnr.status === '未求得' ? '!text-[#dc2626]' : (dnr.status === '已求得' ? '!text-[#2563eb]' : '!text-[#059669]')">{{ dnr.status }}</td>
                                                    <td class="py-1.5 px-2 text-slate-900 font-bold leading-snug text-[15px]">
                                                        {{ translateRelList(dnr.remarks) }}
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    <div v-if="reg.remarks && reg.remarks !== '-' && reg.remarks !== '無'" class="space-y-1 pt-4 border-t border-slate-50 mt-4">
                                        <label class="app-title tracking-wider block text-slate-500 font-bold">詳細內容 / 備註</label>
                                        <div class="app-body font-normal text-slate-900 leading-relaxed whitespace-pre-wrap text-[17px]">{{ reg.remarks }}</div>
                                    </div>
                                </div>

                                <div v-else class="space-y-4 animate-fade-in">
                                    <div class="space-y-1">
                                        <label class="app-title tracking-wider block text-slate-500 font-bold">法寶名稱</label>
                                        <div class="app-body font-black text-[20px] text-slate-900 leading-tight">{{ reg.name }}</div>
                                    </div>

                                    <div class="space-y-1" v-if="reg.purpose && reg.purpose !== '-' && reg.purpose !== '無'">
                                        <label class="app-title tracking-wider block text-slate-500 font-bold">法寶用意</label>
                                        <div class="app-body font-normal text-slate-900 leading-relaxed">{{ reg.purpose }}</div>
                                    </div>

                                    <div class="grid grid-cols-2 gap-4">
                                        <div class="space-y-1">
                                            <label class="app-title tracking-wider block text-slate-500 font-bold">目前狀態</label>
                                            <div class="app-body font-black" :style="reg.status === '未求得' ? 'color: #dc2626 !important;' : (reg.status === '已求得' ? 'color: #2563eb !important;' : 'color: #059669 !important;')">
                                                {{ reg.status }}
                                            </div>
                                        </div>
                                        <div v-if="reg.status !== '已登記'" class="space-y-1 text-right pr-8">
                                            <label class="app-title tracking-wider block text-slate-500 font-bold">求得日期</label>
                                            <div class="app-body font-bold text-slate-900">{{ formatDate(reg.obtained_date) }}</div>
                                        </div>
                                    </div>

                                    <div v-if="reg.remarks && reg.remarks !== '-' && reg.remarks !== '無'" class="space-y-1 pt-2 border-t border-slate-50">
                                        <label class="app-title tracking-wider block text-slate-500 font-bold">詳細內容 / 備註</label>
                                        <div class="app-body font-bold text-slate-600 leading-relaxed whitespace-pre-wrap">{{ reg.remarks }}</div>
                                    </div>
                                </div>
                            </div>
                        </div><!-- /animate-fade-in content -->
                                </div><!-- /scrollable content area -->

                                <!-- Action bar removed, using header 3-dots menu instead -->
                            </div><!-- /white panel -->
                            </div><!-- /pointer-events-none wrapper -->
                        </div><!-- /overlay root -->
                        </teleport>
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- End Scrollable Area -->

    <div v-if="currentFolder && !focusedId && paginationMeta && paginationMeta.last_page > 1" class="fixed z-[100] flex justify-center bg-white border-t border-slate-200 py-0.5" style="bottom: calc(7vh + env(safe-area-inset-bottom)); left: 0; right: 0;">
        <pagination-buttons :meta="paginationMeta" @page-change="handlePageChange" class="!mb-0 !mt-0" />
    </div>

    <mobile-navbar 
        is-absolute
        :can-back="true"
        :show-action="!addMode"
        :action-disabled="!currentFolder"
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

    <add-action-menu 
        :show="showAddMenu" 
        @close="showAddMenu = false"
        :actions="addActions"
    />

    <imperial-grace-add-form 
        v-if="addMode"
        :key="'add-form-' + addSessionKey"
        :mode="addMode"
        :initialData="form"
        :masters="masters"
        :isSaving="isSaving"
        @saveSingle="saveSingle"
        @saveBatch="triggerBatchSave"
        @open-global-picker="activePicker = { ...$event, isAddForm: true }"
        @close="addMode = null"
        @fileUpload="handleFileUpload"
    />

    <compact-date-picker 
        v-if="activePicker"
        v-model="activePickerValue"
        :title="activePicker.title"
        @close="activePicker = null"
    />
    </div> <!-- End Root Module Div -->
</template>

<script setup>
import { ref, reactive, computed, onMounted, onUnmounted, defineEmits, watch } from 'vue';
import { debounce } from '../utils/debounce';
import axios from 'axios';
import MobileNavbar from './MobileNavbar.vue';
import SearchComponent from './SearchComponent.vue';
import AddActionMenu from './AddActionMenu.vue';
import ImperialGraceAddForm from './ImperialGraceAddForm.vue';
import CompactDatePicker from './CompactDatePicker.vue';
import PaginationButtons from './PaginationButtons.vue';
import { writeClipboard, downloadBlob, lockBodyScroll, unlockBodyScroll } from '../utils/iosCompat';


const resetToRoot = () => {
    currentCategory.value = null;
    currentFolder.value = null;
    searchQuery.value = '';
    focusedId.value = null;
    expandedId.value = null;
    openMenuId.value = null;
    reorderMode.value = false;
    addMode.value = null;
    if (scrollContainer.value) {
        scrollContainer.value.scrollTop = 0;
    }
};



const translateRel = (rel) => {
    if (!rel) return '';
    let result = rel.trim();
    if (result === '之父' || result === '父') return '父親';
    if (result === '之母' || result === '母') return '母親';
    if (result === '之嬤' || result === '嬤') return '奶奶';
    if (result === '之夫' || result === '夫') return '先生';
    return result.replace(/^[之的]/, '');
};

const translateRelList = (relList) => {
    if (!relList) return '-';
    if (Array.isArray(relList)) {
        return relList.map(translateRel).filter(x => x).join('、') || '-';
    }
    return translateRel(relList) || '-';
};

const formatDate = (dateStr) => {
    if (!dateStr || dateStr === '-' || dateStr === '未設定') return dateStr || '-';
    const s = String(dateStr).split('T')[0].trim();
    const parts = s.split(/[-/]/);
    if (parts.length === 3) {
        let y = parts[0];
        let m = parts[1].padStart(2, '0');
        let d = parts[2].padStart(2, '0');
        if (!isNaN(parseInt(y)) && !isNaN(parseInt(m)) && !isNaN(parseInt(d))) {
            return `${y}/${m}/${d}`;
        }
    }
    return s.replace(/-/g, '/');
};

const emit = defineEmits(['goHome']);

const currentCategory = ref(null); 
const currentFolder = ref(null);
const addMode = ref(null);
const allRegistries = ref([]);
const userGraces = ref([]);
const dharmaNames = ref([]);
const folderCounts = ref({});
const unobtainedTotal = ref(0);
const masters = ref([]);
const activeRelDropdownIdx = ref(null);
const relationshipOptions = ['母親', '父親', '公公', '婆婆', '爺爺', '奶奶', '外公', '外婆'];
const loading = ref(false);
const isSaving = ref(false);
const persistentToast = ref(null); 
const sortDesc = ref(true);
const toggleSort = () => { sortDesc.value = !sortDesc.value; };
const openMenuId = ref(null);
const paginationMeta = ref(null);
const currentPage = ref(1);

const handlePageChange = (page) => {
    currentPage.value = page;
    loadData(page);
};

const fileInput = ref(null);
const showAddMenu = ref(false); 
const showSearch = ref(false);
const reorderMode = ref(false);
const deleteConfirmId = ref(null); 
const expandedId = ref(null); 
const focusedId = ref(null);
const scrollContainer = ref(null); 
const batchInput = ref('');
const batchMasterId = ref(null);
const addSessionKey = ref(0);
const searchQuery = ref('');
const form = ref({
    id: null, master_id: null, name: '', purpose: '', remarks: '', record_date: '', obtained_date: '', status: '未求得'
});
const inlineEditId = ref(null);
const inlineEditData = ref({});
const activePicker = ref(null); 
const activePickerValue = computed({
    get: () => {
        if (!activePicker.value) return '';
        if (activePicker.value.isAddForm) {
            return form.value[activePicker.value.field];
        }
        if (activePicker.value.idx !== undefined) {
            return inlineEditData.value.dharma_name_registries[activePicker.value.idx][activePicker.value.field];
        }
        return inlineEditData.value[activePicker.value.field];
    },
    set: (val) => {
        if (!activePicker.value) return;
        if (activePicker.value.isAddForm) {
            form.value[activePicker.value.field] = val;
            return;
        }
        if (activePicker.value.idx !== undefined) {
            inlineEditData.value.dharma_name_registries[activePicker.value.idx][activePicker.value.field] = val;
        } else {
            inlineEditData.value[activePicker.value.field] = val;
        }
    }
});

const addActions = computed(() => [
    { 
        label: '逐筆新增', 
        description: '手動輸入每一項重大皇恩詳細資料',
        icon: '<svg class="text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>',
        handler: () => prepareAdd('single') 
    },
    { 
        label: '多筆貼上新增', 
        description: '快速解析 LINE 聊天內容紀錄',
        icon: '<svg class="text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>',
        handler: () => prepareAdd('batch') 
    },
    { 
        label: '複製隨貼 LINE (全部)', 
        description: '將此分類完整清單複製至剪貼簿',
        icon: '<svg class="text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>',
        handler: () => { copyListOnly(); showAddMenu.value = false; }
    }
]);

const displayTitle = computed(() => {
    if (currentFolder.value) return currentFolder.value.name;
    return '重大皇恩專區';
});

const triggerBatchSave = (data) => {
    saveBatch(data);
};

const loadData = async (page = 1) => {
    loading.value = true;
    try {
        const ts = new Date().getTime();
        const params = {
            page,
            sortDesc: sortDesc.value ? 1 : 0,
            t: ts
        };
        if (searchQuery.value) params.search = searchQuery.value;
        if (currentFolder.value) params.master_id = currentFolder.value.id;

        const [res, mres, dres] = await Promise.all([
            axios.get('/imperial-graces', { params }),
            axios.get('/api/masters-list'),
            axios.get('/api/dharma-names-list')
        ]);
        
        allRegistries.value = res.data.registries.data || [];
        paginationMeta.value = {
            current_page: res.data.registries.current_page,
            last_page: res.data.registries.last_page,
            total: res.data.registries.total
        };
        userGraces.value = res.data.userGraces;
        folderCounts.value = res.data.folderCounts || {};
        unobtainedTotal.value = res.data.unobtainedCount || 0;
        masters.value = mres.data;
        dharmaNames.value = dres.data;
    } catch (e) {
        console.error('Load data failed:', e);
        allRegistries.value = [];
    } finally { 
        loading.value = false; 
    }
};

watch(searchQuery, debounce(() => {
    currentPage.value = 1;
    loadData(1);
}, 300));

watch(currentFolder, () => {
    currentPage.value = 1;
    loadData(1);
    if (scrollContainer.value) {
        scrollContainer.value.scrollTop = 0;
    }
});

watch(sortDesc, () => {
    currentPage.value = 1;
    loadData(1);
});

watch(persistentToast, (newVal) => {
    if (!newVal) {
        deleteConfirmId.value = null;
        return;
    }
    if (!['confirm', 'deleteConfirm', 'mismatchConfirm'].includes(newVal.type)) {
        setTimeout(() => {
            if (persistentToast.value === newVal) persistentToast.value = null;
        }, newVal.duration || 3000);
    }
});

const folders = computed(() => {
    const list = [];
    if (Array.isArray(masters.value)) {
        masters.value.forEach(m => {
            list.push({
                id: m.id,
                name: m.name === '父皇仙師' ? '父皇' : m.name
            });
        });
    }
    list.push({ id: 'unobtained', name: '未求得重大皇恩' });
    return list;
});

const totalCount = computed(() => {
    return Object.values(folderCounts.value || {}).reduce((acc, cur) => acc + Number(cur), 0);
});

const mastersFolders = computed(() => {
    const list = [];
    if (Array.isArray(masters.value)) {
        masters.value.forEach(m => {
            if (m.id !== 'unobtained') {
                list.push({
                    ...m,
                    name: m.name === '父皇仙師' ? '父皇' : m.name
                });
            }
        });
    }
    return list;
});

const copyAsTextFile = (reg) => {
    try {
        const text = formatRegistryForFile(reg);
        navigator.clipboard.writeText(text);
        persistentToast.value = { message: '內容已複製到剪貼簿', type: 'success' };
        setTimeout(() => { persistentToast.value = null; }, 3000);
    } catch (err) {
        console.error('Copy failed:', err);
    }
};

const formatRegistryForFile = (reg) => {
    let out = `【重大皇恩】\n`;
    out += `得知日期：${reg.record_date || '-'}\n`;
    out += `法寶：${reg.name}\n`;
    if (reg.count && reg.count !== 1 && reg.count !== '1') out += `數據：${reg.count}\n`;
    if (reg.purpose && reg.purpose !== '-' && reg.purpose !== '無') out += `用意：${reg.purpose}\n`;
    out += `狀態：${reg.status || '未求得'}\n`;
    if (reg.status !== '已登記') out += `求得日期：${reg.obtained_date || '-'}\n`;
    if (reg.remarks && reg.remarks !== '-' && reg.remarks !== '無') out += `\n由來與備註：${reg.remarks}\n`;
    return out.trim();
};

const getMasterName = (id) => {
    const m = masters.value.find(m => String(m.id) === String(id));
    if (m) {
        return m.name === '父皇仙師' ? '父皇' : m.name;
    }
    return '預設';
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
        inlineEditId.value = null;
        if (focusedId.value === id) focusedId.value = null;
    } else {
        expandedId.value = id;
        focusedId.value = id;
        openMenuId.value = null;
    }
};

const cancelInlineEdit = () => {
    inlineEditId.value = null;
    expandedId.value = null;
};

const saveInlineEdit = async () => {
    if (isSaving.value) return;
    isSaving.value = true;
    try {
        await axios.post(`/imperial-graces/registry/${inlineEditId.value}`, { ...inlineEditData.value, _method: 'PATCH' });
        persistentToast.value = { msg: '✓ 已更新載錄', type: 'success' };
        inlineEditId.value = null;
        expandedId.value = null;
        await loadData(currentPage.value);
    } catch (e) {
        console.error('Inline save failed', e);
        persistentToast.value = { msg: '✖ 更新失敗', type: 'error' };
    } finally {
        isSaving.value = false;
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
        if (currentCategory.value === 'unobtained') {
            currentCategory.value = null;
        }
    } else if (currentCategory.value) {
        currentCategory.value = null;
    } else {
        emit('goHome');
    }
};

const handleStatusChange = () => { if (form.value.status === '未求得') form.value.obtained_date = ''; };

const editItem = (reg) => { 
    inlineEditId.value = reg.id;
    inlineEditData.value = JSON.parse(JSON.stringify(reg));
    if (!inlineEditData.value.dharma_name_registries) inlineEditData.value.dharma_name_registries = [];
    inlineEditData.value.dharma_name_registries = inlineEditData.value.dharma_name_registries.map(d => ({
        ...d,
        custom_name: d.dharma_name?.name || d.custom_name,
        remarks: Array.isArray(d.remarks) ? d.remarks.join(' ') : (d.remarks || '')
    }));
    openMenuId.value = null; 
};

const addPersonnelInline = () => {
    inlineEditData.value.dharma_name_registries.push({
        custom_name: '',
        status: '已登記',
        obtained_date: '',
        remarks: ''
    });
};

const confirmDelete = (id) => {
    deleteConfirmId.value = id;
    persistentToast.value = { msg: '確定要刪除這筆資料嗎？', type: 'deleteConfirm' };
    openMenuId.value = null;
};

const executeDelete = async () => {
    if (!deleteConfirmId.value) return;
    try { 
        await axios.delete(`/imperial-graces/registry/${deleteConfirmId.value}`); 
        persistentToast.value = { msg: '✓ 已成功刪除紀錄', type: 'success' };
        setTimeout(() => { if (persistentToast.value?.type === 'success') persistentToast.value = null; }, 1500);
        focusedId.value = null;
        expandedId.value = null;
        loadData(currentPage.value); 
    }
    catch (e) { 
        console.error(e);
        persistentToast.value = { msg: '✖ 刪除失敗', type: 'error' };
    } finally {
        deleteConfirmId.value = null;
    }
};

const triggerSimpleDownload = (text, filename) => {
    const blob = new Blob(['\uFEFF' + text], { type: 'text/plain;charset=utf-8' });
    downloadBlob(blob, filename);
};

const copyOnly = async (reg) => {
    const text = formatRegistryForFile(reg);
    try {
        await navigator.clipboard.writeText(text);
        persistentToast.value = { msg: '已複製資料,可至 LINE 貼上.', type: 'success' };
    } catch (e) {
        persistentToast.value = { msg: '✖ 複製失敗', type: 'error' };
    }
    openMenuId.value = null;
};

const downloadOnly = (reg) => {
    const text = formatRegistryForFile(reg);
    triggerSimpleDownload(text, `重大皇恩_${reg.name}.txt`);
    persistentToast.value = { msg: '已啟動檔案下載.', type: 'success' };
    openMenuId.value = null;
};

const handleReorder = async (reg, newOrderStr) => {
    const newOrder = parseInt(newOrderStr);
    const currentList = [...allRegistries.value];
    const oldIndex = currentList.findIndex(item => item.id === reg.id);
    const newIndex = newOrder - 1;
    
    if (isNaN(newOrder) || oldIndex === -1 || newIndex < 0 || newIndex >= currentList.length) {
        persistentToast.value = { msg: '✖ 無效的項次', type: 'error' };
        return;
    }
    
    const [movedItem] = currentList.splice(oldIndex, 1);
    currentList.splice(newIndex, 0, movedItem);
    const updates = currentList.map((item, idx) => ({
        id: item.id,
        sort_order: idx + 1
    }));
    
    try {
        await axios.post('/imperial-graces/registry/reorder', { orders: updates });
        reorderMode.value = false;
        await loadData(currentPage.value);
    } catch (e) {
        persistentToast.value = { msg: '✖ 順序更新失敗', type: 'error' };
    }
};

const copyListOnly = async () => {
    if (!currentFolder.value) return;
    const contents = `【重大皇恩清單 - ${currentFolder.value.name}】\r\n\r\n` + 
        allRegistries.value.map(r => formatRegistryForFile(r).replace('【重大皇恩】\r\n', '')).join('\r\n\r\n');
    try {
        await navigator.clipboard.writeText(contents);
        persistentToast.value = { msg: '已複製全部清單,可至 LINE 貼上.', type: 'success' };
    } catch (e) {
        persistentToast.value = { msg: '✖ 複製失敗', type: 'error' };
    }
    openMenuId.value = null;
};

const downloadListOnly = () => {
    if (!currentFolder.value) return;
    const contents = `【重大皇恩清單 - ${currentFolder.value.name}】\r\n\r\n` + 
        allRegistries.value.map(r => formatRegistryForFile(r).replace('【重大皇恩】\r\n', '')).join('\r\n\r\n');
    triggerSimpleDownload(contents, `重大皇恩清單_${currentFolder.value.name}.txt`);
    persistentToast.value = { msg: '已啟動清單下載.', type: 'success' };
    openMenuId.value = null;
};

const exportListTxt = () => {
    if (!currentFolder.value || !allRegistries.value.length) return;
    const contents = allRegistries.value.map(r => formatRegistryForFile(r)).join('\r\n\r\n--------------------------------\r\n\r\n');
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
        record_date: mode === 'batch' ? '' : new Date().toLocaleDateString('sv-SE'), 
        obtained_date: '', 
        status: '未求得' 
    };
    batchInput.value = '';
    batchMasterId.value = defaultMasterId;
    addSessionKey.value++;
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
    const isFromUnobtained = folderId === 'unobtained';
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
        
        if (targetMaster) {
            const matchedFolder = mastersFolders.value.find(f => String(f.id) === String(targetMaster.id));
            if (matchedFolder) {
                currentCategory.value = 'masters';
                currentFolder.value = matchedFolder;
            }
        } else if (!finalMid || String(finalMid) === 'unobtained') {
            currentCategory.value = 'unobtained';
            currentFolder.value = { id: 'unobtained', name: '未求得' };
        }
        
        if (resolution === 'shunt' || isFromUnobtained) {
            persistentToast.value = { 
                msg: `✓ 已改成【${form.value.status}】並回存至【${targetMasterName}】資料夾內`, 
                type: 'success',
                duration: 1500 
            };
        }
        
        addMode.value = null;
        await loadData(currentPage.value);
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
        if (['已求得', '已登記'].includes(nextStatus) && !payload.obtained_date) {
            payload.obtained_date = new Date().toLocaleDateString('sv-SE');
        }
        await axios.put(`/imperial-graces/registry/${reg.id}`, payload);
        await loadData(currentPage.value);
        if (String(currentFolder.value?.id) === 'unobtained' && nextStatus !== '未求得') {
            const targetMaster = masters.value.find(m => String(m.id) === String(reg.master_id));
            if (targetMaster) {
                const matchedFolder = mastersFolders.value.find(f => String(f.id) === String(targetMaster.id));
                if (matchedFolder) {
                    currentCategory.value = 'masters';
                    currentFolder.value = matchedFolder;
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
    if (payload && typeof payload === 'object') {
        batchInput.value = payload.input || '';
        batchMasterId.value = payload.masterId || null;
    }
    if (!batchInput.value || !batchMasterId.value || isSaving.value) return;
    isSaving.value = true;
    try {
        const finalMasterId = batchMasterId.value === 'unobtained' ? null : batchMasterId.value;
        const dataToSend = { master_id: finalMasterId };
        if (payload && payload.rows) {
            dataToSend.items = payload.rows;
        } else {
            const lines = batchInput.value.split('\n').map(l => l.trim()).filter(l => l && !l.startsWith('【')); 
            if (lines.length === 0) {
                persistentToast.value = { msg: '✖ 內容格式不正確', type: 'error' };
                isSaving.value = false;
                return;
            }
            dataToSend.lines = lines;
        }
        await axios.post('/imperial-graces/registry/batch', dataToSend);
        persistentToast.value = { msg: '✓ 多筆新增成功', type: 'success' };
        const targetMaster = masters.value.find(m => String(m.id) === String(finalMasterId));
        if (targetMaster) {
            const matchedFolder = mastersFolders.value.find(f => String(f.id) === String(targetMaster.id));
            if (matchedFolder) {
                currentCategory.value = 'masters';
                currentFolder.value = matchedFolder;
            }
        } else if (!finalMasterId || String(finalMasterId) === 'unobtained') {
            currentCategory.value = 'unobtained';
            currentFolder.value = { id: 'unobtained', name: '未求得' };
        }
        addMode.value = null;
        loadData(1);
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
        filtered = allRegistries.value.filter(r => String(r.master_id) === String(currentFolder.value.id));
    }


    // 狀態排序：未求得 → 已求得 → 已登記
    const statusOrder = { '未求得': 1, '已求得': 2, '已登記': 3 };
    
    filtered.sort((a, b) => {
        // Priority 1: Status (always first)
        const orderA = statusOrder[a.status] || 99;
        const orderB = statusOrder[b.status] || 99;
        if (orderA !== orderB) return orderA - orderB;

        // Priority 2: Master Order (Only for Unobtained Folder)
        if (currentFolder.value.id === 'unobtained') {
            const masterOrderA = masters.value.findIndex(m => String(m.id) === String(a.master_id));
            const masterOrderB = masters.value.findIndex(m => String(m.id) === String(b.master_id));
            const ma = masterOrderA === -1 ? 999 : masterOrderA;
            const mb = masterOrderB === -1 ? 999 : masterOrderB;
            if (ma !== mb) return ma - mb;
        }

        // Priority 3: Manual sort_order (within same status)
        if ((a.sort_order || 9999) !== (b.sort_order || 9999)) {
            return (a.sort_order || 9999) - (b.sort_order || 9999);
        }
        
        // Priority 4: Date (within same status & sort_order)
        const dateA = a.record_date || '';
        const dateB = b.record_date || '';
        if (dateA !== dateB) {
            return sortDesc.value ? dateB.localeCompare(dateA) : dateA.localeCompare(dateB);
        }
        
        // Priority 5: ID Fallback
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
    if (id === 'unobtained') return unobtainedTotal.value;
    return folderCounts.value[id] || 0;
};
const isAnyModalOpen = computed(() => {
    return !!addMode.value || 
           !!focusedId.value || 
           !!persistentToast.value || 
           !!showAddMenu.value || 
           !!showSearch.value ||
           !!activePicker.value;
});

watch(isAnyModalOpen, (newVal) => {
    if (newVal) lockBodyScroll();
    else unlockBodyScroll();
});

onUnmounted(() => {
    if (isAnyModalOpen.value) unlockBodyScroll();
});
</script>

<style scoped>
.animate-fade-in { animation: fadeIn 0.1s ease-out; }
.animate-slide-up { animation: slideUp 0.15s ease-out; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(-5px); } to { opacity: 1; transform: translateY(0); } }
@keyframes slideUp { from { opacity: 0; transform: translate(-50%, 10px); } to { opacity: 1; transform: translate(-50%, 0); } }

/* Custom Scrollbar for a cleaner mobile look */
.custom-scrollbar::-webkit-scrollbar { width: 6px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
</style>
