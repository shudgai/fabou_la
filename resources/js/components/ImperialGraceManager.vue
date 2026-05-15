<template>
    <div class="bg-white h-full flex flex-col relative text-slate-900 imperial-grace-module overscroll-none">
        <!-- Transition Logo Overlay -->
        <div v-if="loading" class="fixed inset-0 z-[5000] bg-white flex flex-col items-center justify-center pointer-events-none transition-opacity duration-300">
            <div class="relative flex flex-col items-center">
                <logo-imperial-notebook :height="120" spinning />
                <div class="mt-4 text-[17px] font-black text-slate-400 tracking-widest animate-pulse">資料載入中...</div>
            </div>
        </div>

        <!-- Global Dual Header System -->
        <!-- Header 1: Module Level (Shown ONLY when not in a folder/add mode) -->
        <div v-if="!currentFolder && !addMode" 
            class="border-b border-white flex items-center bg-white sticky top-0 z-[110] w-full transition-all duration-300 min-h-[40px] md:min-h-0"
            style="padding: 4px 15px;">
            <div class="flex-1 flex items-center gap-2 min-w-0 py-1 pl-1 cursor-pointer" @click="resetToRoot">
                        <logo-imperial-notebook :height="36" class="md:hidden" />
                        <h1 class="text-red-600 leading-tight font-outfit tracking-widest break-words font-black whitespace-nowrap" style="color: #dc2626 !important; font-size: 24px !important; padding-top: 5px !important; font-weight: 900 !important;">
                    重大皇恩專區
                </h1>
            </div>
        </div>

        <!-- Header 2: Add Mode Level (Shown only when adding a new record) -->
        <div v-if="addMode" 
            class="border-b border-white flex items-center bg-white sticky top-0 z-[110] w-full transition-all duration-300 min-h-[40px] md:min-h-0"
            style="padding: 4px 15px; padding-top: env(safe-area-inset-top, 0px);">
            <div class="flex items-center w-full">
                <button @click="addMode = null" class="p-2 -ml-2 text-slate-400 active:scale-90 transition-all mr-1">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" /></svg>
                </button>
                <div class="flex-1 flex flex-col justify-start min-w-0 py-1 pl-1 cursor-pointer">
                    <div class="leading-tight font-outfit tracking-widest break-words flex items-center gap-2" style="color: #dc1428 !important; font-size: 24px !important; font-weight: 900 !important;">
                        <logo-imperial-notebook :height="36" class="md:hidden" />
                        {{ form.id ? '修改重大皇恩' : '重大皇恩專區' }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Perfectly Centered Premium Confirmation / Status Modal -->
        <teleport to="body">
        <div v-if="persistentToast" class="fixed inset-0 z-[6000] flex items-center justify-center p-6 bg-slate-900/60 animate-fade-in pointer-events-auto">
            <div class="bg-white w-full max-w-[340px] rounded-[48px] p-10 shadow-[0_30px_100px_rgba(0,0,0,0.3)] animate-slide-up flex flex-col items-center relative border border-white/50">

                <!-- Icon Section -->
                <div v-if="persistentToast.type === 'success'" class="mb-6">
                    <svg class="w-12 h-12 text-slate-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M5 13l4 4L19 7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <div v-else-if="persistentToast.type === 'error'" class="mb-6">
                    <svg class="w-12 h-12 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M6 18L18 6M6 6l12 12" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <div v-else-if="persistentToast.type === 'deleteConfirm'" class="mb-6">
                    <div class="w-16 h-16 bg-rose-50 text-rose-500 rounded-2xl flex items-center justify-center">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </div>
                </div>

                <div class="w-full text-center mb-10">
                    <span class="text-[20px] font-black text-slate-900 leading-relaxed block px-2">
                        {{ persistentToast.msg }}
                    </span>
                </div>

                <div v-if="['confirm', 'deleteConfirm', 'mismatchConfirm'].includes(persistentToast.type)" class="w-full flex flex-col space-y-3">
                    <template v-if="persistentToast.type === 'deleteConfirm'">
                        <button @click="executeDelete" class="w-full bg-red-600 h-[44px] rounded-2xl text-[17px] font-black tracking-widest active:scale-95 transition-all shadow-lg shadow-red-100" style="color: white !important;">確定刪除</button>
                        <button @click="persistentToast = null" class="w-full bg-slate-100 text-slate-500 h-[40px] rounded-2xl text-[15px] font-bold tracking-widest active:scale-95 transition-all">取消</button>
                    </template>
                    <template v-else-if="persistentToast.type === 'mismatchConfirm'">
                        <button @click="saveSingle('correct')" class="w-full bg-amber-500 h-[44px] rounded-2xl text-[16px] font-black active:scale-95 transition-all shadow-lg shadow-amber-100" style="color: white !important;">存入{{ currentFolder?.name }}</button>
                        <button @click="saveSingle('shunt')" class="w-full bg-indigo-600 h-[44px] rounded-2xl text-[16px] font-black active:scale-95 transition-all shadow-lg shadow-indigo-100" style="color: white !important;">存入{{ getMasterName(form.master_id) }}</button>
                        <button @click="persistentToast = null" class="w-full h-[36px] text-slate-400 font-bold text-[14px]">取消</button>
                    </template>
                    <template v-else>
                        <button @click="saveSingle('shunt')" class="w-full bg-indigo-600 h-[44px] rounded-2xl text-[17px] font-black tracking-widest active:scale-95 transition-all shadow-lg shadow-indigo-100" style="color: white !important;">確定</button>
                    </template>
                </div>
                <div v-else class="w-full">
                    <button @click="persistentToast = null" class="w-full bg-[#5c56e0] h-[44px] rounded-[22px] text-[18px] font-black tracking-widest active:scale-95 transition-all shadow-[0_10px_30px_rgba(92,86,224,0.3)]" style="color: white !important;">確認</button>
                </div>
            </div>
        </div>
        </teleport>
        <div ref="scrollContainer" class="flex-1 overflow-auto custom-scrollbar overscroll-contain" style="padding-bottom: 120px;">
        <!-- Level 0: Main Category Selection -->
        <div v-if="!currentCategory && !currentFolder && !addMode" class="flex-1 flex flex-col items-center pt-8 pb-20 w-full space-y-2.5 bg-white">
            <button 
                @click="currentCategory = 'masters'"
                class="flex flex-col items-center justify-center bg-white active:scale-95 transition-all group relative rounded-none w-full max-w-[465px]">
                <div class="relative w-full max-w-[465px] aspect-[245/158] bg-white">
                    <picture><source srcset="/image/imperial_grace_book_v5.webp" type="image/webp"><img src="/image/imperial_grace_book_v5.png" fetchpriority="high" loading="eager" class="w-full h-full object-contain transition-transform group-hover:scale-105 mix-blend-multiply" alt="書籍圖示"></picture>
                    <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none font-biaokai-locked pt-2 text-[#fbbf24] tracking-tight leading-tight text-center" style="transform: translateY(-15px);">
                        <div class="tracking-tight leading-tight text-center font-biaokai-locked text-[38px] text-[#fbbf24]">重大皇恩專區</div>
                        <div class="mt-[18px] flex items-center font-biaokai-locked">
                            <span class="text-white font-normal tracking-tight font-biaokai-locked text-[19px]" style="color: white !important;">共 {{ totalCount }} 筆</span>
                        </div>
                    </div>
                </div>
            </button>

            <!-- 第二個按鈕：未求得重大皇恩 (直接進入內容) -->
            <button 
                @click="currentFolder = { id: 'unobtained', name: '未求得重大皇恩' }; currentCategory = 'masters'"
                class="flex flex-col items-center justify-center bg-white active:scale-95 transition-all group relative rounded-none w-full max-w-[465px]">
                <div class="relative w-full max-w-[465px] aspect-[245/158] bg-white">
                    <picture><source srcset="/image/imperial_grace_book_v5.webp" type="image/webp"><img src="/image/imperial_grace_book_v5.png" fetchpriority="high" loading="eager" class="w-full h-full object-contain transition-transform group-hover:scale-105 mix-blend-multiply" alt="書籍圖示"></picture>
                    <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none font-biaokai-locked" style="transform: translateY(-15px);">
                        <div class="tracking-tight leading-tight text-center font-biaokai-locked text-[30px] text-[#fbbf24]"><span class="text-[24px] opacity-90 font-biaokai-locked">未求得</span><br>重大皇恩專區</div>
                        <div class="mt-[18px] flex items-center font-biaokai-locked -translate-y-2">
                            <span class="text-white font-normal tracking-tight font-biaokai-locked text-[19px]" style="color: white !important;">共 {{ unobtainedTotal }} 筆</span>
                        </div>
                    </div>
                </div>
            </button>
        </div>

        <div v-if="currentCategory === 'masters' && !currentFolder && !addMode" class="w-full">
            <div class="pt-[5px] pb-2 flex items-center relative min-h-[10px] cursor-pointer" @click="resetToRoot">
            </div>

            <div class="grid grid-cols-2 gap-x-0 gap-y-0.5 p-2 place-items-center">
                <button v-for="folder in mastersFolders" :key="folder.id" 
                        @click="currentFolder = folder"
                        class="flex flex-col items-center justify-center active:scale-95 transition-all p-0 w-[175px] h-[138px] flex-shrink-0 relative group rounded-none overflow-visible">
                    <div class="relative w-[175px] h-[138px] flex items-center justify-center overflow-visible">
                        <picture><source srcset="/image/imperial_grace_book_v5.webp" type="image/webp"><img src="/image/imperial_grace_book_v5.png" fetchpriority="high" loading="eager" class="w-[220px] h-[142px] max-w-none object-contain transition-transform group-hover:scale-105 mix-blend-multiply transform-gpu" style="will-change: transform;" alt="書籍圖示"></picture>
                         <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none pt-12 text-[#fbbf24] font-biaokai-locked" style="transform: translateY(-22px);">
                              <div class="text-[14px] mb-[2px] tracking-widest font-biaokai-locked text-gold-locked">重大皇恩專區</div>
                              <div class="text-[20px] font-black font-biaokai-locked text-gold-locked stroke-gold-locked">{{ folder.name === '父皇仙師' ? '父皇' : folder.name }}</div>
                             <div class="mt-1 flex items-center font-biaokai-locked -translate-y-[2px]">
                                 <span class="text-white font-normal font-biaokai-locked text-[11.2px]" style="color: white !important;">{{ folderCounts[folder.id] || 0 }} 筆</span>
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
        <div v-else-if="currentFolder && !addMode" class="px-0 bg-white w-full pb-32">
            <!-- Consolidated Header Section -->
            <div class="flex flex-col border-b border-slate-100 bg-white sticky top-0 z-[110]">
                <!-- Row 1: Global Title (Now visible on all devices to replace separate Header 2) -->
                <div class="flex px-[15px] py-1 items-center gap-2 cursor-pointer" @click="resetToRoot">
                    <logo-imperial-notebook :height="36" class="md:hidden" />
                    <h1 class="text-red-600 leading-tight font-outfit tracking-widest font-black whitespace-nowrap" style="color: #dc2626 !important; font-size: 26px !important; padding-top: 5px !important; font-weight: 900 !important;">
                        重大皇恩專區
                    </h1>
                </div>

                <!-- Row 2: Folder Name + Actions (Sticky on all devices) -->
                <div class="flex items-end justify-between w-full px-[15px] pt-1 pb-[6px] bg-white">
                    <span :class="currentFolder?.name === '閻王仙師' ? 'text-slate-900' : '!text-[#dc2626]'" class="font-outfit whitespace-nowrap truncate !font-medium" style="font-size: 24px !important;">{{ currentFolder?.name }}</span>
                    <div class="flex items-center space-x-2 shrink-0 ml-4">
                        <button v-if="!reorderMode" @click="toggleSort" class="px-2 py-1.5 active:scale-95 transition-all font-black" style="color: #4f46e5 !important; font-size: 16px !important;">
                            {{ sortDesc ? '新→舊' : '舊→新' }}
                        </button>
                        <button v-if="currentFolder" @click="reorderMode = !reorderMode" 
                                :class="reorderMode ? 'text-emerald-600' : 'text-slate-400'"
                                class="px-2 py-1.5 font-black transition-all active:scale-95 whitespace-nowrap"
                                style="font-size: 16px !important;">
                            {{ reorderMode ? '確認排序' : '修改排序' }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- List Display Area -->
            <div style="padding: 0px 15px 10px 15px;" class="mt-0 relative">
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
                                <!-- Row 1.5: Remarks -->

                                <!-- Row 2: Name -->
                                <div class="text-[17px] font-black text-slate-900 leading-tight truncate font-outfit">{{ reg.name }}</div>
                            </div>
                        </div>

                        <!-- Full-page Expanded Overlay -->
                        <teleport to="body">
                        <div v-if="expandedId === reg.id" class="fixed inset-0 z-[500] animate-fade-in">
                            <!-- Backdrop -->
                            <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="toggleExpand(reg.id)"></div>
                            <!-- Content Panel -->
                            <div class="absolute inset-0 flex items-end md:items-center md:justify-center pointer-events-none">
                            <div class="w-full h-full md:h-auto md:max-h-[90dvh] md:max-w-2xl bg-white md:rounded-[40px] md:shadow-2xl flex flex-col animate-slide-up pointer-events-auto overflow-hidden">
                                <!-- Header -->
                                <div class="shrink-0 bg-white flex flex-col w-full border-b border-slate-100 relative">
                                    <div class="px-[15px] py-[10px] flex flex-col items-center w-full">
                                        <div class="flex items-center gap-2">
                                            <logo-imperial-notebook :height="36" class="md:hidden" />
                                            <h1 class="uppercase tracking-widest font-outfit !font-black !text-[#dc2626] whitespace-nowrap" style="font-size: 30px !important;">重大皇恩登記簿</h1>
                                        </div>
                                        <div class="mt-1">
                                            <span :class="currentFolder?.name === '閻王仙師' ? '!text-[#0f172a]' : '!text-[#dc2626]'" class="font-outfit whitespace-nowrap !font-medium" style="font-size: 26px !important;">{{ currentFolder?.name }}</span>
                                        </div>
                                    </div>

                                    <!-- Action Buttons Container (Right Side) -->
                                    <div class="absolute right-4 top-[48px] z-[100] flex flex-col items-center gap-0">
                                        <!-- Close Button (X) - Top -->
                                        <button @click="toggleExpand(reg.id)" class="w-10 h-10 flex items-center justify-center text-slate-400 active:scale-90 transition-all">
                                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        </button>
                                        <!-- Three dots menu - Bottom -->
                                        <div class="relative">
                                            <button @click.stop="toggleMenu(reg.id)" class="w-10 h-10 flex items-center justify-center text-red-500 active:scale-90 transition-all">
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
                                <!-- Scrollable Content -->
                                <div class="flex-1 overflow-y-auto overscroll-contain px-[15px] pt-2 pb-32 custom-scrollbar">
                                    <div class="animate-fade-in bg-white space-y-4 relative">
                                        <!-- Detail Content Grid (Read Only View) -->
                                        <div class="space-y-4 animate-fade-in">
                                            <div class="grid grid-cols-2 gap-4">
                                                <div class="space-y-1">
                                                    <label class="app-title !text-[17px] tracking-wider block text-slate-500 font-bold">{{ reg.status === '已登記' ? '登記日期' : '得知日期' }}</label>
                                                    <div class="text-[15px] font-normal font-outfit !text-[#0d0d0d] !font-normal">{{ formatDate(reg.record_date) }}</div>
                                                </div>
                                                <div class="space-y-1 text-right pr-8">
                                                    <label class="app-title !text-[17px] tracking-wider block text-slate-500 font-bold">載錄目標仙師</label>
                                                    <div class="app-body text-[17px] font-bold text-slate-900">{{ getMasterName(reg.master_id) }}</div>
                                                </div>
                                            </div>

                                            <div class="space-y-1">
                                                <label class="app-title !text-[17px] tracking-wider block text-slate-500 font-bold">法寶名稱</label>
                                                <div class="app-body font-black text-[17px] text-slate-900 leading-tight">{{ reg.name }}</div>
                                            </div>

                                            <div v-if="reg.purpose && reg.purpose !== '-' && reg.purpose !== '無'" class="space-y-1">
                                                <label class="app-title !text-[17px] tracking-wider block text-slate-500 font-bold">法寶用意</label>
                                                <div class="app-body text-[17px] font-normal text-slate-900 leading-relaxed">{{ reg.purpose }}</div>
                                            </div>

                                            <div class="grid grid-cols-2 gap-4">
                                                <div class="space-y-1">
                                                    <label class="app-title !text-[17px] tracking-wider block text-slate-500 font-bold">目前狀態</label>
                                                    <div class="app-body text-[17px] font-black" :style="reg.status === '未求得' ? 'color: #dc2626 !important;' : (reg.status === '已求得' ? 'color: #2563eb !important;' : 'color: #059669 !important;')">
                                                        {{ reg.status }}
                                                    </div>
                                                </div>
                                                <div v-if="reg.status !== '已登記'" class="space-y-1 text-right pr-8">
                                                    <label class="app-title !text-[17px] tracking-wider block text-slate-500 font-bold">求得日期</label>
                                                    <div class="app-body text-[15px] font-bold text-slate-900">{{ formatDate(reg.obtained_date) }}</div>
                                                </div>
                                            </div>

                                            <div v-if="reg.remarks && reg.remarks !== '-' && reg.remarks !== '無'" class="space-y-1 pt-2 border-t border-slate-50">
                                                <label class="app-title !text-[17px] tracking-wider block text-slate-500 font-bold">詳細內容 / 備註</label>
                                                <div class="app-body text-[17px] font-bold text-slate-600 leading-relaxed whitespace-pre-wrap">{{ reg.remarks }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
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

    <div v-if="currentFolder && !focusedId && paginationMeta && paginationMeta.last_page > 1" class="fixed z-[100] flex justify-center bg-white border-t border-slate-200 py-0.5" style="bottom: calc(7dvh + env(safe-area-inset-bottom)); left: 0; right: 0;">
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
    </div>
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
const activePicker = ref(null); 
const activePickerValue = computed({
    get: () => {
        if (!activePicker.value) return '';
        return form.value[activePicker.value.field];
    },
    set: (val) => {
        if (!activePicker.value) return;
        form.value[activePicker.value.field] = val;
    }
});

const addActions = computed(() => [
    { 
        label: '逐筆新增', 
        description: '手動輸入法寶詳細資料',
        icon: '<svg class="text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>',
        handler: () => prepareAdd('single_personal') 
    },
    { 
        label: '多筆載錄', 
        description: '貼上多個法寶名稱，每行自動建立一筆',
        icon: '<svg class="text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 6h16M4 12h16m-7 6h7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>',
        handler: () => prepareAdd('batch_personal') 
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
        persistentToast.value = { msg: '內容已複製到剪貼簿', type: 'success' };
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
    form.value = JSON.parse(JSON.stringify(reg));
    if (!form.value.dharma_name_registries) form.value.dharma_name_registries = [];
    addSessionKey.value++;
    addMode.value = 'single';
    openMenuId.value = null; 
    expandedId.value = null; // Close the overlay since form will open
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
        persistentToast.value = { msg: '已成功刪除紀錄', type: 'success' };
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
        persistentToast.value = { msg: '內容已複製到剪貼簿', type: 'success' };
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
        persistentToast.value = { msg: '無效的項次', type: 'error' };
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
        persistentToast.value = { msg: '順序更新失敗', type: 'error' };
    }
};

const copyListOnly = async () => {
    if (!currentFolder.value) return;
    const contents = `【重大皇恩清單 - ${currentFolder.value.name}】\r\n\r\n` + 
        allRegistries.value.map(r => formatRegistryForFile(r).replace('【重大皇恩】\r\n', '')).join('\r\n\r\n');
    try {
        await navigator.clipboard.writeText(contents);
        persistentToast.value = { msg: '內容已複製到剪貼簿', type: 'success' };
    } catch (e) {
        persistentToast.value = { msg: '複製失敗', type: 'error' };
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
        persistentToast.value = { msg: '文字檔匯出成功', type: 'success' };
    } catch (e) {
        console.error(e);
        persistentToast.value = { msg: '匯出失敗', type: 'error' };
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
        persistentToast.value = { msg: '請輸入法寶名稱', type: 'error' };
        return;
    }

    if (!form.value.master_id) {
        persistentToast.value = { msg: '請選擇目標仙師', type: 'error' };
        return;
    }

    // Date is now optional for all statuses as requested
    /*
    if (['已登記', '已求得'].includes(form.value.status) && !form.value.obtained_date) {
        persistentToast.value = { msg: '請輸入求得日期', type: 'error' };
        return;
    }
    */

    isSaving.value = true;
    persistentToast.value = null;

    try {
        const finalMid = form.value.master_id;
        const targetMaster = masters.value.find(m => String(m.id) === String(finalMid));
        const targetMasterName = targetMaster ? targetMaster.name : '';

        let res;
        if (form.value.id) {
            res = await axios.post(`/imperial-graces/registry/${form.value.id}`, { ...form.value, _method: 'PATCH' });
            persistentToast.value = { msg: '修改成功', type: 'success' };
        } else {
            res = await axios.post('/imperial-graces/registry', { ...form.value, master_id: finalMid });
            persistentToast.value = { msg: '新增成功', type: 'success' };
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
                msg: `已改成【${form.value.status}】並回存至【${targetMasterName}】資料夾內`, 
                type: 'success',
                duration: 1500 
            };
        }

        addMode.value = null;
        await loadData(currentPage.value);
        if (res && res.data && res.data.id) {
            expandedId.value = res.data.id;
        }
    } catch (e) {
        console.error('儲存失敗:', e);
        const serverMsg = e.response?.data?.message || e.message || '資料驗證失敗';
        persistentToast.value = { msg: `儲存失敗：${serverMsg}`, type: 'error' };
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
        persistentToast.value = { msg: '檔案內容已載入', type: 'success' };
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
                        msg: `已改成【${nextStatus}】並回存至【${targetMaster.name}】資料夾內`, 
                        type: 'success',
                        duration: 1500
                    };
                }
            }
        }
    } catch (e) {
        console.error('狀態變更失敗:', e);
        const serverMsg = e.response?.data?.message || '資料驗證失敗';
        persistentToast.value = { msg: `狀態變更失敗：${serverMsg}`, type: 'error' };
    }
};

const triggerBatchSave = async (payload = null) => {
    if (!payload || isSaving.value) return;
    isSaving.value = true;
    try {
        const dataToSend = { 
            master_id: form.value.master_id === 'unobtained' ? null : form.value.master_id 
        };

        if (Array.isArray(payload)) {
            // New Wizard Format: payload is the array of rows directly
            dataToSend.items = payload;
        } else if (payload && payload.rows) {
            // Legacy/Alternative Format
            dataToSend.items = payload.rows;
            if (payload.masterId) dataToSend.master_id = payload.masterId;
        } else {
            // Raw Input Parsing Fallback
            const lines = batchInput.value.split('\n').map(l => l.trim()).filter(l => l && !l.startsWith('【')); 
            if (lines.length === 0) {
                persistentToast.value = { msg: '內容格式不正確', type: 'error' };
                isSaving.value = false;
                return;
            }
            dataToSend.lines = lines;
        }

        const itemCount = dataToSend.items ? dataToSend.items.length : (dataToSend.lines ? dataToSend.lines.length : 0);
        await axios.post('/imperial-graces/registry/batch', dataToSend);
        addMode.value = null; 
        persistentToast.value = { msg: `多筆新增成功（共 ${itemCount} 筆）`, type: 'success' };
        
        const targetMasterId = dataToSend.master_id || (payload && payload.rows && payload.rows.length > 0 ? payload.rows[0].master_id : null);
        const targetMaster = masters.value.find(m => String(m.id) === String(targetMasterId));
        
        if (targetMaster) {
            const matchedFolder = mastersFolders.value.find(f => String(f.id) === String(targetMaster.id));
            if (matchedFolder) {
                currentCategory.value = 'masters';
                currentFolder.value = matchedFolder;
            }
        } else if (!targetMasterId || String(targetMasterId) === 'unobtained') {
            currentCategory.value = 'unobtained';
            currentFolder.value = { id: 'unobtained', name: '未求得' };
        }
        await loadData(1);
    } catch (e) { 
        console.error('批次失敗:', e);
        const serverMsg = e.response?.data?.message || e.message || '格式解析錯誤或伺服器連線失敗';
        persistentToast.value = { msg: `批次新增失敗：${serverMsg}`, type: 'error' };
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
        // Priority 1: Status (always first to group by lifecycle)
        const orderA = statusOrder[a.status] || 99;
        const orderB = statusOrder[b.status] || 99;
        if (orderA !== orderB) return orderA - orderB;

        // Priority 2: Date (New to Old / Old to New)
        const dateA = a.record_date || '';
        const dateB = b.record_date || '';
        if (dateA !== dateB) {
            return sortDesc.value ? dateB.localeCompare(dateA) : dateA.localeCompare(dateB);
        }

        // Priority 3: Master Order (Only for Unobtained Folder, within same status/date)
        if (currentFolder.value.id === 'unobtained') {
            const masterOrderA = masters.value.findIndex(m => String(m.id) === String(a.master_id));
            const masterOrderB = masters.value.findIndex(m => String(m.id) === String(b.master_id));
            const ma = masterOrderA === -1 ? 999 : masterOrderA;
            const mb = masterOrderB === -1 ? 999 : masterOrderB;
            if (ma !== mb) return ma - mb;
        }

        // Priority 4: Manual sort_order (within same status/date)
        if ((a.sort_order || 9999) !== (b.sort_order || 9999)) {
            return (a.sort_order || 9999) - (b.sort_order || 9999);
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

/* Locked Font Utility for Administrative Consistency */
.font-biaokai-locked {
    font-family: 'DFKai-SB', '標楷體', 'BiauKai', 'Kaiti TC', serif !important;
}
.text-gold-locked { color: #fbbf24 !important; }
.stroke-gold-locked { -webkit-text-stroke: 0.5px #fbbf24; }

/* Custom Scrollbar for a cleaner mobile look */
.custom-scrollbar { -webkit-overflow-scrolling: touch; }
.custom-scrollbar::-webkit-scrollbar { width: 6px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
</style>
