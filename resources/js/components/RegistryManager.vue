<template>
    <div class="bg-white h-full flex flex-col relative text-slate-900 registry-manager-module overflow-visible">
        <!-- Global Dual Header System -->
        <!-- Header 1: Module Level (Shown ONLY when not in a folder/add mode) -->
        <div v-if="!currentFolder && !addMode" 
            class="border-b border-white flex items-center bg-white sticky top-0 z-[110] w-full transition-all duration-300"
            style="padding: 4px 4px; min-height: 52px;">
            <div class="flex-1 flex items-center min-w-0 py-1 pl-1 cursor-pointer" @click="resetToRoot">
                <div class="app-title !text-[32px] leading-tight font-outfit tracking-widest break-words !font-black !text-[#dc2626] pt-[5px] whitespace-nowrap" style="color: #dc2626 !important; font-size: 32px !important; font-weight: 900 !important;">
                    法寶登記專區
                </div>
            </div>
        </div>

        <!-- Header 2: Action/Folder Level (Shown when in a folder or add mode) -->
        <!-- Header 2: Action/Folder Level (Mobile & Desktop Unified Layout) -->
        <div v-if="currentFolder && !addMode" 
            class="border-b border-slate-100 flex flex-col bg-white sticky top-0 z-[110] w-full transition-all duration-300 md:hidden">
            <!-- Top Row: Main Title (Clickable) + Actions (X button removed) -->
            <div class="flex items-center justify-between py-[5px] px-3 bg-white border-b border-slate-50">
                <div class="app-title !text-[32px] leading-tight !font-black !text-[#dc2626] whitespace-nowrap cursor-pointer" 
                    @click="resetToRoot"
                    style="color: #dc2626 !important; font-size: 32px !important; font-weight: 900 !important;">
                    法寶登記專區
                </div>
                <div class="flex items-center space-x-2">
                    <button v-if="!reorderMode" @click="toggleSort" class="text-slate-600 font-black active:scale-95 px-0.5" style="font-size: 14px !important;">
                        {{ sortDesc ? '新→舊' : '舊→新' }}
                    </button>
                    <button v-if="currentFolder" @click="reorderMode = !reorderMode" 
                            class="font-black transition-all active:scale-95 whitespace-nowrap px-0.5"
                            :class="reorderMode ? 'text-emerald-600' : 'text-slate-400'"
                            style="font-size: 14px !important;">
                        {{ reorderMode ? '確認排序' : '修改排序' }}
                    </button>
                </div>
            </div>

            <!-- Row 2: Category Name + Master Name (Consolidated 2-row Header) -->
            <div class="px-4 bg-white border-b border-slate-50 flex items-baseline gap-x-2 py-[5px] overflow-hidden">
                <span class="font-outfit font-normal !text-[#dc2626] whitespace-nowrap shrink-0" style="font-size: 23px !important; font-weight: 400 !important; line-height: 1.1;">
                    {{ currentCategory === 'major' ? '重大皇恩登記簿' : '其他皇恩登記簿' }}
                </span>
                <span :class="currentFolder.name === '閻王仙師' ? 'text-slate-900' : 'text-red-600'" class="font-outfit font-normal whitespace-nowrap truncate" style="font-size: 23px !important; font-weight: 400 !important; line-height: 1.1;">{{ currentFolder.name }}</span>
            </div>
        </div>

        <!-- Main Scrollable Area -->
        <div ref="scrollContainer" class="flex-1 overflow-y-auto custom-scrollbar !touch-auto" style="padding-bottom: 150px;">
            <!-- Category and Master Selection -->
            <div v-if="!currentFolder && !addMode" class="min-h-screen bg-white flex flex-col items-center">
                <div v-if="currentCategory" class="w-full px-[15px] py-[2px] flex items-center bg-white border-b border-slate-50 relative min-h-[52px]">
                        <div class="flex-1 flex flex-col justify-start min-w-0 py-1 pl-1 cursor-pointer" @click="resetToRoot">
                            <h1 v-if="!currentCategory" class="leading-tight font-outfit tracking-widest break-words !font-black !text-[#dc2626] pt-[5px] !text-[32px] whitespace-nowrap" style="color: #dc2626 !important; font-size: 32px !important; font-weight: 900 !important;">
                                法寶登記專區
                            </h1>
                            <h1 v-else class="leading-tight font-outfit tracking-widest break-words !text-[#dc2626] pt-[5px]" style="color: #dc2626 !important; font-size: 32px !important; font-weight: 400 !important;">
                                {{ currentCategory === 'major' ? '重大皇恩登記簿' : '其他皇恩登記簿' }}
                                <br>
                                <span v-if="currentFolder" class="text-[28px] text-red-600 font-normal whitespace-nowrap overflow-hidden text-ellipsis block w-full" style="font-size: 28px !important; font-weight: 400 !important;">- {{ currentFolder.name }} -</span>
                            </h1>
                        </div>
                </div>

                <!-- Root Categories -->
                <div v-if="!currentCategory" class="flex-1 flex flex-col items-center justify-start pt-20 pb-20 w-full max-w-lg mx-auto">
                    <button @click="currentCategory = 'major'" class="flex flex-col items-center justify-center bg-white active:scale-95 rounded-none p-0 w-[310px] h-[310px] relative transition-all shadow-sm">
                        <div class="relative w-[310px] h-[310px]">
                            <svg class="w-full h-full drop-shadow-sm" viewBox="0 0 64 64" fill="none">
                                <path d="M4 14C4 11.7909 5.79086 10 8 10H24.5L30 16H56C58.2091 16 60 17.7909 60 20V50C60 52.2091 58.2091 54 56 54H8C5.79086 54 4 52.2091 4 50V14Z" fill="#b91c1c" />
                                <path d="M4 22C4 19.7909 5.79086 18 8 18H56C58.2091 18 60 19.7909 60 22V50C60 52.2091 58.2091 54 56 54H8C5.79086 54 4 52.2091 4 50V22Z" fill="#b91c1c" stroke="rgba(255,255,255,0.6)" stroke-width="1" />
                            </svg>
                            <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none pt-10">
                                <div class="leading-tight drop-shadow-[0_1px_2px_rgba(255,255,255,0.8)] text-center !font-black !text-[#b91c1c]" style="font-size: 42px !important;">重大皇恩<br>登記簿</div>
                                <div class="mt-4 flex items-center">
                                    <span class="text-black font-black tracking-tight drop-shadow-sm" style="font-size: 17px !important;">{{ categoryCounts.major || 0 }} 筆</span>
                                </div>
                            </div>
                        </div>
                    </button>
                </div>

                <!-- Masters Grid -->
                <div v-else class="flex flex-col items-center gap-[4px] mx-auto">
                    <button v-for="folder in folders" :key="folder.id" 
                             @click="currentFolder = folder"
                              class="flex flex-col items-center justify-center transition-all active:scale-95 rounded-none group p-2 w-[198px] h-[198px] relative"
                             :class="[]">
                        
<div class="relative w-[163px] h-[163px]">
                              <svg class="w-full h-full transition-transform group-hover:scale-105" viewBox="0 0 64 64" fill="none">
                                <path d="M4 14C4 11.7909 5.79086 10 8 10H24.5L30 16H56C58.2091 16 60 17.7909 60 20V50C60 52.2091 58.2091 54 56 54H8C5.79086 54 4 52.2091 4 50V14Z" 
                                    fill="#b91c1c" />
                                <path d="M4 22C4 19.7909 5.79086 18 8 18H56C58.2091 18 60 19.7909 60 22V50C60 52.2091 58.2091 54 56 54H8C5.79086 54 4 52.2091 4 50V22Z" 
                                    fill="#b91c1c" 
                                    stroke="rgba(255,255,255,0.6)" stroke-width="1" />
                            </svg>
                            
                            <div class="absolute inset-0 flex flex-col items-center justify-center pt-6 px-1 pointer-events-none">
                         <div class="font-black tracking-tight leading-tight text-center whitespace-nowrap mb-2 !font-black !text-[#fbbf24]"
                             style="font-size: 24px !important;">
                             {{ folder.name }}
                        </div>
                                <div class="mt-1 flex items-center">
                                    <span class="font-normal !text-black" style="font-size: 17px !important;">{{ folderCounts[folder.id] || 0 }} 筆</span>
                                </div>
                            </div>
                        </div>
                    </button>

                </div>

                <div class="mt-12 flex justify-center pb-32">
                    <button @click="$emit('goHome')" class="text-slate-300 hover:text-slate-500 transition-colors flex items-center space-x-2 active:scale-95">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" /></svg>
                        <span class="text-xs font-medium tracking-widest uppercase">返回專區列表</span>
                    </button>
                </div>
            </div>

            <!-- Folder Contents -->
            <div v-else-if="currentFolder && !addMode" 
                :class="[
                    'px-0 bg-white transition-all duration-300 w-full md:max-w-xl md:mx-auto',
                    focusedId ? 'fixed inset-0 z-[100] pt-[110px] overflow-y-auto custom-scrollbar' : ''
                ]"
                style="">
                <!-- Desktop Centered Header Section within Col-7 (Updated per user request for 图2 Layout) -->
                <div class="hidden md:flex flex-col items-center border-b border-slate-100 bg-white sticky top-0 z-[50]">
                    <!-- Top Row: Main Title + All Action Buttons -->
                    <div class="flex items-center justify-between bg-white border-b border-slate-100 w-full py-2 px-4">
                        <h1 class="uppercase tracking-widest font-outfit !font-black !text-[#dc2626]" style="font-size: 32px !important;">法寶登記專區</h1>
                        <div class="flex items-center space-x-3">
                            <!-- Sort Button -->
                            <button v-if="!reorderMode" @click="toggleSort" class="px-1 py-0.5 text-slate-600 hover:text-indigo-600 font-black transition-all active:scale-95" style="font-size: 14px !important;">
                                {{ sortDesc ? '新→舊' : '舊→新' }}
                            </button>
                            <!-- Reorder Button -->
                            <button v-if="currentFolder" @click="reorderMode = !reorderMode" 
                                    :class="reorderMode ? 'text-emerald-600' : 'text-slate-400 hover:text-slate-600'"
                                    class="px-1 py-0.5 font-black transition-all active:scale-95 whitespace-nowrap"
                                    style="font-size: 14px !important;">
                                {{ reorderMode ? '確認排序' : '修改排序' }}
                            </button>
                            <!-- Back/Close Button -->
                            <button @click="resetToRoot" class="w-8 h-8 flex items-center justify-center rounded-full bg-slate-100 text-slate-400 hover:text-red-500 transition-all active:scale-90">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </button>
                        </div>
                    </div>
                    <!-- Bottom Row: Dynamic Sub-title + Folder Name (same line, adjacent) -->
                    <div class="px-4 py-2 bg-white flex items-baseline gap-x-2 w-full">
                        <span class="font-outfit !text-[#dc2626] whitespace-nowrap" style="font-size: 23px !important; font-weight: 400 !important;">
                            {{ currentCategory === 'major' ? '重大皇恩登記簿' : '其他皇恩登記簿' }}
                        </span>
                        <span :class="currentFolder.name === '閻王仙師' ? 'text-slate-900' : 'text-red-600'" class="font-outfit whitespace-nowrap" style="font-size: 23px !important; font-weight: 400 !important;">{{ currentFolder.name }}</span>
                    </div>
                </div>

                <div :class="[focusedId ? 'p-0 pb-[120px] md:p-[10px]' : 'px-2 py-[10px]']" class="mt-0 relative">
                    <!-- Loading overlay (keeps DOM intact so input keeps focus) -->
                    <div v-if="loading" class="absolute inset-0 bg-white/60 z-30 flex items-start justify-center pt-20 pointer-events-none">
                        <div class="inline-block animate-spin rounded-full h-8 w-8 border-4 border-slate-100 border-t-indigo-600"></div>
                    </div>

                    <div v-if="showSearch && !focusedId" class="mb-4 sticky top-0 z-40 bg-white/80 backdrop-blur-md pb-2 px-1 animate-fade-in">
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-indigo-400 group-focus-within:text-indigo-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </div>
                                <input v-model="searchQuery" type="text" placeholder="搜尋項目、用意、法號..." 
                                    class="block w-full pl-11 pr-12 h-[52px] bg-slate-50 border-2 border-transparent focus:border-indigo-100 focus:bg-white rounded-2xl text-[17px] font-black font-outfit text-slate-800 placeholder-slate-300 transition-all outline-none shadow-sm">
                                <button v-if="searchQuery" @click="searchQuery = ''" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-300 hover:text-red-500 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </button>
                            </div>
                        </div>

                        <!-- Pagination (Moved to headers for both mobile and desktop) -->

                        <!-- Empty State -->
                        <div v-if="filteredTreasures.length === 0" class="flex flex-col items-center justify-center py-24 px-6 text-center">
                            <h3 class="text-[17px] font-black text-slate-300 font-outfit uppercase tracking-widest">尚無資料</h3>
                        </div>

                        <div v-for="(item, idx) in filteredTreasures" :key="item.id" 
                         @click="toggleExpand(item.id)"
                             :class="[
                                 'bg-white px-2 py-[10px] border-b border-slate-50 relative transition-all cursor-pointer hover:shadow-md active:bg-slate-50 flex items-start',
                                 focusedId === item.id ? 'min-h-[calc(100dvh-100px)] md:min-h-[60vh] border-transparent shadow-none !mb-0 md:!mb-10 !rounded-none md:!rounded-[48px] -mx-4 md:mx-0 z-[60] md:shadow-2xl md:border md:border-slate-100 md:mt-4' : '',
                                 openMenuId === item.id ? 'z-[50]' : 'z-0'
                             ]">
                            
                            <!-- Sequence Number / Reorder Input -->
                            <div class="mr-4 shrink-0 flex items-center justify-center pt-1 md:pt-2">
                                <div v-if="!reorderMode" class="w-8 h-8 md:w-12 md:h-12 bg-slate-50 md:bg-slate-100/60 rounded-xl flex items-center justify-center text-[14px] md:text-[18px] font-black text-slate-400 md:text-slate-500 transition-all">
                                    {{ idx + 1 }}
                                </div>
                                <input v-else 
                                       type="number" 
                                       :value="idx + 1"
                                       @click.stop
                                       @change="e => handleReorder(item, e.target.value)"
                                       class="w-10 h-9 bg-blue-50 border-2 border-blue-200 rounded-xl text-center text-[15px] font-black text-blue-600 focus:ring-2 focus:ring-blue-400 outline-none">
                            </div>

                            <div class="flex-1 min-w-0 pr-[10px]">
                            <!-- Action Dropdown Trigger -->
                            <div class="absolute top-[24px] right-4 z-30 translate-x-0">
                                <button @click.stop="openMenuId = openMenuId === item.id ? null : item.id" 
                                        class="p-2 active:scale-90 transition-all rounded-full bg-slate-50/50 !text-[#dc1428]">
                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                        <circle cx="5" cy="12" r="2" />
                                        <circle cx="12" cy="12" r="2" />
                                        <circle cx="19" cy="12" r="2" />
                                    </svg>
                                </button>
                                
                                <div v-if="openMenuId === item.id" 
                                     class="absolute right-0 mt-2 w-24 bg-white opacity-100 border border-slate-100 rounded-2xl shadow-2xl z-[110] py-1 ring-1 ring-black ring-opacity-5 animate-fade-in overflow-hidden">
                                    <button @click.stop="toggleExpand(item.id); openMenuId = null" 
                                            class="w-full text-left px-3 py-2.5 text-[14px] font-black text-slate-900 hover:bg-slate-50 flex items-center transition-colors border-b border-slate-50 whitespace-nowrap">
                                        {{ expandedIds.has(item.id) ? '收起' : '展開' }}
                                    </button>
                                    <button @click.stop="openAndEdit(item.id); openMenuId = null" 
                                            class="w-full text-left px-3 py-2.5 text-[14px] font-black text-slate-900 hover:bg-slate-50 flex items-center transition-colors border-b border-slate-50 whitespace-nowrap">
                                        修改
                                    </button>
                                    <button @click.stop="copyAsTextFile(item); openMenuId = null" 
                                            class="w-full text-left px-3 py-2.5 text-[14px] font-black text-slate-900 hover:bg-slate-50 flex items-center transition-colors border-b border-slate-50 whitespace-nowrap">
                                        複製
                                    </button>
                                    <button @click.stop="downloadItemData(item); openMenuId = null" 
                                            class="w-full text-left px-3 py-2.5 text-[14px] font-black text-slate-900 hover:bg-slate-50 flex items-center transition-colors border-b border-slate-50 whitespace-nowrap">
                                        下載
                                    </button>
                                    <button @click.stop="deleteConfirmId = item.id; openMenuId = null" 
                                            class="w-full text-left px-3 py-2.5 text-[14px] font-black text-red-600 hover:bg-red-50 flex items-center transition-colors whitespace-nowrap">
                                        刪除
                                    </button>
                                </div>
                            </div>

                            <!-- Card Header (Toggle Expansion) - Standardized to Imperial Grace Style -->
                            <div :class="[!expandedIds.has(item.id) ? 'flex' : 'hidden md:flex']" class="mt-0 flex-col flex-1 min-w-0 pr-8 py-1">
                                <!-- Row 1: Date -->
                                <div v-if="getEarliestDate(item) && getEarliestDate(item) !== '-'" class="app-title font-bold mb-0.5">
                                    日期：<span class="app-title !font-normal !text-[#0d0d0d]">{{ formatDisplayDate(getEarliestDate(item)) }}</span>
                                </div>

                                <!-- Row 2: Name + (Expansion Indicator) -->
                                <div class="flex items-center justify-between">
                                    <div class="app-body font-bold text-slate-900 leading-tight truncate">{{ item.name }}</div>
                                    <div v-if="!expandedIds.has(item.id)" class="ml-2 text-slate-300 transition-colors md:hidden">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path d="M19 9l-7 7-7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                                

                             


                                    <div v-if="expandedIds.has(item.id)" @click.stop class="border-t border-slate-50 md:mt-2 md:pt-4 md:border-t-slate-100 relative">
                                        <!-- Detailed Record View -->
                                        <div v-if="!editingIds.has(item.id)" class="space-y-4 px-0 mb-4 animate-fade-in">
                                            <!-- Row 1: Date and Master -->
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                <div class="space-y-1 md:hidden">
                                                    <label class="app-title tracking-wider block text-slate-500 font-bold">日期</label>
                                                    <div class="text-[15px] font-normal font-outfit !text-[#0d0d0d] !font-normal">{{ formatDisplayDate(getEarliestDate(item)) }}</div>
                                                </div>
                                                <div class="space-y-1 md:col-span-2 md:text-left">
                                                    <label class="app-title tracking-wider block text-slate-500 font-bold">載錄目標仙師</label>
                                                    <div class="app-body font-bold" :class="getMasterName(item.master_id) === '閻王仙師' ? 'text-slate-900' : 'text-red-600'">{{ getMasterName(item.master_id) }}</div>
                                                </div>
                                            </div>
                                            
                                            <!-- Row 2: Name -->
                                            <div class="space-y-1 md:hidden">
                                                <label class="app-title tracking-wider block text-slate-500 font-bold">法寶名稱</label>
                                                <div class="app-body font-black text-[20px] text-slate-900 leading-tight">{{ item.name }}</div>
                                            </div>

                                            <div v-if="item.purpose && item.purpose !== '-' && item.purpose !== '無'" class="space-y-1">
                                                <label class="app-title tracking-wider block text-slate-500 font-bold">法寶用意</label>
                                                <div class="app-body font-normal text-slate-900 leading-relaxed">{{ item.purpose }}</div>
                                            </div>
                                            <div v-if="item.acquisition_method && item.acquisition_method !== '-' && item.acquisition_method !== '無'" class="space-y-1">
                                                <label class="app-title tracking-wider block text-slate-500 font-bold">求寶方式</label>
                                                <div class="app-body font-normal text-slate-900 leading-relaxed">{{ item.acquisition_method }}</div>
                                            </div>
                                            <div v-if="item.remarks && item.remarks !== '-' && item.remarks !== '無'" class="space-y-1">
                                                <label class="app-title tracking-wider block text-slate-500 font-bold">詳細內容 / 備註</label>
                                                <div @click.stop="openRemarks(item.remarks)" class="app-body font-bold text-slate-600 leading-relaxed whitespace-pre-wrap cursor-pointer hover:text-indigo-600 transition-colors">{{ item.remarks }}</div>
                                            </div>
                                        </div>

                                        <!-- Edit Mode Fields -->
                                        <div v-if="editingIds.has(item.id)" class="p-4 bg-indigo-50/30 rounded-2xl border border-indigo-100/50 space-y-5 shadow-inner mb-8">
                                            <div class="grid grid-cols-1 gap-4">
                                                <div class="space-y-1">
                                                    <label class="text-[11px] text-red-400 uppercase tracking-widest font-black font-outfit">日期</label>
                                                    <div @click.stop="activePicker = { id: item.id, field: 'record_date', title: '修改日期' }" 
                                                        class="w-full bg-white border border-slate-400 rounded-xl px-3 py-[5px] flex items-center justify-between cursor-pointer">
                                                        <span :class="editMap[item.id].record_date ? 'text-black' : 'text-slate-300'" class="text-[17px] font-black font-outfit uppercase">
                                                            {{ formatDisplayDate(editMap[item.id].record_date) || '未設定' }}
                                                        </span>
                                                        <svg class="w-5 h-5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                                    </div>
                                                </div>
                                                <div class="space-y-1">
                                                    <label class="text-[11px] text-red-400 uppercase tracking-widest font-black font-outfit">法寶名稱</label>
                                                    <input type="text" v-model="editMap[item.id].name" class="w-full bg-white border border-slate-400 rounded-xl px-3 py-[5px] text-[17px] font-black outline-none font-outfit">
                                                </div>
                                                <div class="space-y-1">
                                                    <label class="text-[11px] text-red-400 uppercase tracking-widest font-black font-outfit">法寶用意</label>
                                                    <textarea rows="2" v-model="editMap[item.id].purpose" class="w-full bg-white border border-slate-400 rounded-xl px-3 py-[5px] text-[17px] font-black outline-none font-outfit custom-scrollbar"></textarea>
                                                </div>
                                                <div class="space-y-1">
                                                    <label class="text-[11px] text-red-400 uppercase tracking-widest font-black font-outfit">求寶方式</label>
                                                    <textarea rows="2" v-model="editMap[item.id].acquisition_method" class="w-full bg-white border border-slate-400 rounded-xl px-3 py-[5px] text-[17px] font-black outline-none font-outfit custom-scrollbar"></textarea>
                                                </div>
                                                <div class="space-y-1">
                                                    <label class="text-[11px] text-red-400 uppercase tracking-widest font-black font-outfit">備註</label>
                                                    <input type="text" v-model="editMap[item.id].remarks" class="w-full bg-white border border-slate-400 rounded-xl px-3 py-[5px] text-[17px] font-black outline-none font-outfit">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="space-y-3 pt-[10px] border-t border-slate-50 mt-[10px] md:mt-6">
                                            <template v-if="currentCategory === 'major'">
                                                <div class="overflow-x-auto rounded-xl border border-slate-200 shadow-sm mb-20 -mx-4 md:mx-0 bg-white">
                                                    <table class="w-full border-collapse bg-white text-[16px]">
                                                        <thead>
                                                            <tr class="bg-slate-50/80 text-slate-600 font-outfit border-b border-slate-200">
                                                                <th class="px-2 py-1.5 text-left font-black w-[80px] whitespace-nowrap border-r border-slate-100 text-[15px] font-outfit">法號/群組</th>
                                                                <th class="px-2 py-1.5 text-center font-black w-[130px] whitespace-nowrap border-r border-slate-100 text-[15px] font-outfit">日期</th>
                                                                <th class="px-2 py-1.5 text-center font-black text-[15px] font-outfit">備註</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr v-for="recipient in getFilteredDharmaNames" :key="recipient.id" class="hover:bg-slate-50 transition-colors border-b border-slate-50 last:border-0">
                                                                <td class="px-2 py-1.5 font-black text-slate-900 whitespace-nowrap border-r border-slate-50 text-[17px] font-outfit">{{ recipient.name }}</td>
                                                                <td class="p-0 text-black border-r border-slate-50">
                                                                    <div class="flex items-center px-2 py-1.5 justify-center relative">
                                                                        <input v-if="editingIds.has(item.id) && editMap[item.id + '-' + recipient.id]" 
                                                                            v-model="editMap[item.id + '-' + (recipient.id)]['obtained_date']" 
                                                                            type="text"
                                                                            class="w-full bg-white border border-slate-300 rounded-md px-1 py-0.5 text-[14px] font-black font-outfit text-center focus:ring-1 focus:ring-indigo-300 outline-none !text-[#dc1428]">
                                                                        <button v-if="editingIds.has(item.id) && editMap[item.id + '-' + recipient.id]" 
                                                                            @click.stop="activePicker = { id: item.id + '-' + (recipient.id), field: 'obtained_date', title: recipient.name }"
                                                                            class="absolute right-0 text-slate-300 hover:text-indigo-600 p-0.5">
                                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                                                        </button>
                                                                        <span v-else :class="editMap[item.id + '-' + recipient.id]?.obtained_date ? '' : 'opacity-30'" class="text-[15px] font-black font-outfit !text-[#dc1428]" style="font-family: 'PMingLiU', serif;">
                                                                            {{ formatDisplayDate(editMap[item.id + '-' + recipient.id]?.obtained_date) || '-' }}
                                                                        </span>
                                                                    </div>
                                                                </td>
                                                                <td class="p-0 text-black">
                                                                    <div @click.stop="triggerRemarksEdit(item, recipient.id)" 
                                                                        class="w-full py-1.5 px-2 flex items-center justify-center transition-colors">
                                                                        <span v-if="editingIds.has(item.id)" class="text-[15px] font-black text-indigo-400">...</span>
                                                                        <span v-else-if="item.dharma_name_registries?.find(r => (getDharmaNameText(r).replace('升','昇') === (typeof recipient.name === 'string' ? recipient.name.replace('升','昇') : recipient.name)))?.remarks?.length" class="text-[18px] text-amber-500 animate-pulse">●</span>
                                                                        <span v-else class="text-[15px] text-slate-200">-</span>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </template>
                                            <template v-else>
                                                <div class="overflow-x-auto rounded-xl border border-slate-200 shadow-sm mb-20 -mx-4 md:mx-0 bg-white">
                                                    <table class="w-full border-collapse bg-white text-[16px]">
                                                        <thead>
                                                             <tr class="bg-slate-50/80 text-slate-600 font-outfit border-b border-slate-200">
                                                                 <th class="px-2 py-1.5 text-center font-black w-[110px] whitespace-nowrap border-r border-slate-200 text-[15px] font-outfit">日期</th>
                                                                 <th class="px-2 py-1.5 text-left font-black w-[80px] whitespace-nowrap border-r border-slate-200 text-[15px] font-outfit">法號</th>
                                                                 <th class="px-2 py-1.5 text-left font-black w-[90px] whitespace-nowrap border-r border-slate-200 text-[15px] font-outfit">親友</th>
                                                                 <th class="px-2 py-1.5 text-center font-black text-[15px] font-outfit">備註</th>
                                                             </tr>
                                                         </thead>
                                                        <tbody>
                                                            <tr v-for="dnr in getFilteredSortedRegistries(item)" :key="dnr.id" class="hover:bg-slate-50 transition-colors border-b border-slate-200 last:border-0">
                                                                <td class="p-0 text-black border-r border-slate-200">
                                                                    <div class="flex items-center px-2 py-1.5 justify-center relative">
                                                                        <input v-if="editingIds.has(item.id)" 
                                                                            v-model="editMap[item.id + '-' + dnr.dharma_name_id].obtained_date" 
                                                                            type="text"
                                                                            class="w-full bg-white border border-slate-300 rounded-md px-1 py-0.5 text-[14px] font-black font-outfit text-center focus:ring-1 focus:ring-indigo-300 outline-none !text-[#1e293b]">
                                                                        <button v-if="editingIds.has(item.id)" 
                                                                            @click.stop="activePicker = { id: item.id + '-' + dnr.dharma_name_id, field: 'obtained_date', title: dnr.dharma_name?.name || dnr.custom_name }"
                                                                            class="absolute right-0 text-slate-300 hover:text-indigo-600 p-0.5">
                                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                                                        </button>
                                                                        <span v-else class="text-[15px] font-bold font-outfit" style="font-family: 'PMingLiU', serif; color: #1e293b !important;">{{ formatDisplayDate(editMap[item.id + '-' + dnr.dharma_name_id]?.obtained_date) || formatDisplayDate(dnr.obtained_date) || '-' }}</span>
                                                                    </div>
                                                                </td>
                                                                <td @click="openRemarks(dnr)" class="px-2 py-1.5 font-black text-slate-900 whitespace-nowrap border-r border-slate-200 text-[17px] font-outfit cursor-pointer active:bg-slate-100 transition-colors">{{ getDharmaNameText(dnr) }}</td>
                                                                <td class="p-0 text-black border-r border-slate-200">
                                                                    <div class="w-full py-1.5 px-2 flex items-center">
                                                                        <input v-if="editingIds.has(item.id)" 
                                                                            v-model="editMap[item.id + '-' + (dnr.dharma_name_id || dnr.custom_name)].relationship"
                                                                            class="w-full h-[32px] bg-white border border-slate-100 rounded-lg px-2 text-[14px] outline-none focus:ring-1 focus:ring-indigo-100"
                                                                            placeholder="親友">
                                                                        <span v-else class="text-[14px] text-slate-600 font-bold font-outfit">
                                                                            {{ translateRelList(dnr.related_personnel) }}
                                                                        </span>
                                                                    </div>
                                                                </td>
                                                                <td class="p-0 text-black">
                                                                    <div @click.stop="triggerRemarksEdit(item, dnr)" 
                                                                        class="w-full py-1.5 px-2 flex items-center justify-center cursor-pointer active:scale-95 transition-all">
                                                                        <span v-if="editingIds.has(item.id)" class="text-[15px] font-black text-indigo-400 font-outfit">...</span>
                                                                        <span v-else-if="dnr.remarks && (Array.isArray(dnr.remarks) ? dnr.remarks.length > 0 : true)" 
                                                                             :class="(Array.isArray(dnr.remarks) ? dnr.remarks.length > 1 : dnr.remarks.includes('；')) ? 'text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-md text-[13px] font-black font-outfit' : 'text-[18px] text-amber-500 animate-pulse font-outfit'">
                                                                             {{ (Array.isArray(dnr.remarks) ? dnr.remarks.length > 1 : dnr.remarks.includes('；')) ? '多重' : '●' }}
                                                                         </span>
                                                                        <span v-else class="text-[14px] text-slate-300 font-outfit">
                                                                            <svg class="w-4 h-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" /></svg>
                                                                        </span>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            <tr v-if="editingIds.has(item.id)" class="bg-indigo-50/20 border-t-2 border-indigo-100/50">
                                                                <td class="p-0 border-b border-indigo-100">
                                                                    <div @click.stop="activePicker = { id: item.id + '-new', field: 'obtained_date', title: '設定新加入日期' }" class="w-full h-[46px] px-3 flex items-center justify-between cursor-pointer">
                                                                        <span class="text-[14px] font-black font-outfit text-[#1e293b]">{{ editMap[item.id + '-new']?.obtained_date || '設定日期' }}</span>
                                                                    </div>
                                                                </td>
                                                                <td class="p-2 border-b border-indigo-100">
                                                                    <select @change="addPersonRowFromSelect(item.id, $event)" class="w-full h-9 rounded-xl border border-indigo-200 font-outfit">
                                                                        <option value="">＋ 點此加入人員</option>
                                                                        <option v-for="dn in dharmaNames" :key="dn.id" :value="dn.id">{{ dn.name }}</option>
                                                                    </select>
                                                                </td>
                                                                <td class="p-0 border-b border-indigo-100">
                                                                    <div @click="triggerRemarksEdit(item, 'new')" class="w-full h-[46px] px-3 flex items-center text-[14px] text-slate-300 font-black truncate font-outfit">
                                                                        {{ editMap[item.id + '-new']?.remarks || '輸入備註...' }}
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </template>
                                        </div>

                                        <!-- Sticky Save Button Bar (Inside expansion) -->
                                        <div v-if="editingIds.has(item.id)" class="fixed bottom-[3vh] left-0 right-0 p-4 pb-6 bg-white/95 backdrop-blur-md border-t border-slate-100 z-[200] flex justify-center shadow-[0_-10px_30px_rgba(0,0,0,0.05)]">
                                            <button @click.stop="saveItemInPlace(item)" class="w-full max-w-md h-[48px] bg-blue-600 !text-white rounded-2xl font-black text-[16px] shadow-lg shadow-blue-100 active:scale-95 transition-all tracking-widest" style="color: white !important; font-size: 16px !important;">儲存修改</button>
                                        </div>
                                    </div>
                            </div>
                        </div>
                </div>
            </div>
        </div>

        <registry-add-form 
            v-if="addMode"
            ref="addFormRef"
            :mode="addMode" 
            :initialData="form"
            :masters="masters"
            :isSaving="isSaving"
            @saveSingle="saveSingle"
            @saveBatch="triggerBatchSave"
            @openRemarksEdit="handleAddFormRemarksEdit"
            @cancel="addMode = null"
        />

        <!-- Persistent Toasts/Picker -->
        <teleport to="body">
            <div v-if="persistentToast" class="fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 z-[9999] pointer-events-auto">
                <div class="bg-white rounded-3xl shadow-[0_20px_60px_rgba(0,0,0,0.3)] flex flex-col border border-slate-100 overflow-hidden" style="padding: 28px; min-width: 320px; max-width: calc(100vw - 32px);">
                    <div class="flex items-start justify-between mb-8">
                        <span class="text-[17px] font-black text-slate-900 leading-relaxed tracking-widest">
                            {{ persistentToast.msg }}
                        </span>
                        <button @click="persistentToast = null" class="ml-6 text-slate-400 hover:text-slate-600 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="3" stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                    <div v-if="['confirm', 'deleteConfirm', 'mismatchConfirm'].includes(persistentToast.type)" class="flex space-x-4">
                        <button v-for="a in persistentToast.actions" :key="a.label" @click="a.handler" 
                            class="flex-1 h-[52px] rounded-2xl text-[16px] font-black tracking-tighter active:scale-95 transition-all shadow-lg !text-white border border-transparent"
                            :class="persistentToast.type === 'deleteConfirm' ? 'bg-red-600 shadow-red-100' : 'bg-blue-600 shadow-blue-100'"
                            style="color: white !important;">
                            {{ a.label }}
                        </button>
                        <button v-if="persistentToast.type !== 'mismatchConfirm'" @click="persistentToast = null" class="flex-1 bg-slate-100 text-slate-500 h-[52px] rounded-2xl border border-slate-200 text-[18px] font-black tracking-widest active:scale-95 transition-all">取消</button>
                    </div>
                    <div v-else class="flex justify-end mt-2">
                        <button @click="persistentToast = null" class="bg-indigo-600 !text-white px-10 py-3.5 rounded-2xl text-[18px] font-black tracking-widest active:scale-95 transition-all shadow-lg shadow-indigo-100" style="color: white !important;">知道了</button>
                    </div>
                </div>
            </div>
        </teleport>
        
        <!-- Delete Confirmation Overlay -->
        <teleport to="body">
            <div v-if="deleteConfirmId" class="fixed inset-0 z-[250] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-md animate-fade-in">
                <div class="bg-white w-full max-w-sm rounded-[32px] p-6 shadow-2xl animate-pop-in">
                    <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" /></svg>
                    </div>
                    <h3 class="text-[22px] font-black text-center mb-2 font-outfit text-slate-800">確定要刪除嗎？</h3>
                    <p class="text-slate-500 text-center mb-6 font-black font-outfit leading-relaxed">此操作將永久刪除此筆載錄，且無法復原。請確認身分後再執行。</p>
                    <div class="flex flex-col space-y-3">
                        <button @click="executeDelete" class="w-full py-4 bg-red-600 !text-white rounded-2xl font-black text-[18px] active:scale-95 transition-all font-outfit">
                            是的，確認刪除
                        </button>
                        <button @click="deleteConfirmId = null" class="w-full py-4 bg-slate-100 text-slate-600 rounded-2xl font-black text-[18px] active:scale-95 transition-all font-outfit">
                            返回
                        </button>
                    </div>
                </div>
            </div>
        </teleport>

        <!-- Backdrops & Pickers -->
        <teleport to="body">
            <div v-if="openMenuId" @click="openMenuId = null" class="fixed inset-0 z-[25] bg-black/5 backdrop-blur-[1px]"></div>
        </teleport>

        <compact-date-picker 
            v-if="activePicker"
            v-model="editMap[activePicker.id][activePicker.field]"
            :title="activePicker.title"
            :scale="0.8"
            @close="activePicker = null"
        />

        <!-- Remarks Viewer -->
        <remarks-viewer 
            v-model="showRemarksModal"
            :remarks="activeRemarks"
            :editable="currentRemarksKey !== null || currentDnrId !== null || currentAddFormIdx !== null"
            :forceEdit="currentRemarksKey !== null || currentAddFormIdx !== null"
            :dnrId="currentDnrId"
            @save="handleRemarksViewerSave"
        />
        <lucky-draw :show="showLuckyDraw" @close="showLuckyDraw = false" />

        <!-- Bottom Pagination -->
        <div v-if="currentFolder && !focusedId && paginationMeta && paginationMeta.last_page > 1" 
             class="fixed z-[100] flex justify-center bg-white border-t border-slate-200 py-0.5" 
             style="bottom: calc(7vh + env(safe-area-inset-bottom)); left: 0; right: 0;">
            <pagination-buttons :meta="paginationMeta" @page-change="handlePageChange" class="!mb-0 !mt-0" />
        </div>

        <mobile-navbar 
            is-absolute
            :can-back="true"
            :can-home="true"
            :show-action="true"
            :action-disabled="false"
            :action-active="showAddMenu"
            :search-active="showSearch"
            @back="handleBack"
            @home="$emit('goHome')"
            @action="showAddMenu = !showAddMenu"
            @search="showSearch = !showSearch"
        />

        <!-- Add Action Menu (uses z-[2000], above navbar) -->
        <add-action-menu
            :show="showAddMenu"
            @close="showAddMenu = false"
            :actions="addActions"
        />
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, defineEmits, watch } from 'vue';
import { debounce } from '../utils/debounce';
import axios from 'axios';

const getTodayStr = () => {
    const d = new Date();
    return `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`;
};
import MobileNavbar from './MobileNavbar.vue';
import AddActionMenu from './AddActionMenu.vue';
import RegistryAddForm from './RegistryAddForm.vue';
import CompactDatePicker from './CompactDatePicker.vue';
import LuckyDraw from './LuckyDraw.vue';
import RemarksViewer from './RemarksViewer.vue';
import PaginationButtons from './PaginationButtons.vue';
import { writeClipboard, lockBodyScroll, unlockBodyScroll } from '../utils/iosCompat';

const isDesktop = ref(window.innerWidth >= 768);
const handleResize = () => { isDesktop.value = window.innerWidth >= 768; };


onMounted(() => {
    window.addEventListener('resize', handleResize);
    loadData();
});
onUnmounted(() => {
    window.removeEventListener('resize', handleResize);
});

const resetToRoot = () => {
    currentCategory.value = null;
    currentFolder.value = null;
    addMode.value = null;
    searchQuery.value = '';
    focusedId.value = null;
    openMenuId.value = null;
    expandedIds.value = new Set();
    editingIds.value = new Set();
    reorderMode.value = false;
    if (scrollContainer.value) {
        scrollContainer.value.scrollTop = 0;
    }
};

const emit = defineEmits(['goHome']);

const getEarliestDate = (item) => {
    if (!item.dharma_name_registries || item.dharma_name_registries.length === 0) {
        return item.record_date || '-';
    }
    const dates = item.dharma_name_registries
        .map(dn => dn.obtained_date)
        .filter(d => d)
        .sort();
    return dates.length > 0 ? dates[0] : (item.record_date || '-');
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
        return relList.map(translateRel).join('、') || '-';
    }
    return translateRel(relList) || '-';
};

const formatDisplayDate = (dateStr) => {
    if (!dateStr || dateStr === '-' || dateStr === '未設定') return dateStr;
    // Strip time part if present (e.g., T16:00:00.000000Z)
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

const copyAsTextFile = (item) => {
    try {
        const text = formatRegistryForFile(item);
        navigator.clipboard.writeText(text);
        persistentToast.value = { msg: '✓ 內容已複製到剪貼簿', type: 'success' };
        setTimeout(() => { if (persistentToast.value?.type === 'success') persistentToast.value = null; }, 1500);
    } catch (err) {
        persistentToast.value = { msg: '✖ 複製失敗', type: 'error' };
    }
};

const formatRegistryForFile = (item) => {
    let registries = [...(item.dharma_name_registries || [])];
    
    registries.sort((a, b) => {
        const indexA = dharmaNames.value.findIndex(dn => dn.id === a.dharma_name_id);
        const indexB = dharmaNames.value.findIndex(dn => dn.id === b.dharma_name_id);
        if (indexA !== -1 && indexB !== -1) return indexA - indexB;
        return (getDharmaNameText(a)).localeCompare(getDharmaNameText(b), 'zh-Hant');
    });

    let res = `法寶：${item.name}\n用意：${item.purpose || ''}\n日期：${formatDisplayDate(getEarliestDate(item))}\n------------------\n`;
    registries.forEach(r => {
        res += `${getDharmaNameText(r)} | ${formatDisplayDate(r.obtained_date) || '-'} | ${r.remarks || ''}\n`;
    });
    return res;
};

// States
const currentCategory = ref(null); 
const currentFolder = ref(null);
const folderCounts = ref({});
const categoryCounts = ref({});
const addMode = ref(null);
const showLuckyDraw = ref(false);
const currentAddFormIdx = ref(null);
const addFormRef = ref(null);
const allTreasures = ref([]);
const masters = ref([]);
const dharmaNames = ref([]);
const loading = ref(false);
const persistentToast = ref(null);
const isSaving = ref(false);
const sortDesc = ref(true);
const openMenuId = ref(null);
const expandedIds = ref(new Set());
const editingIds = ref(new Set());
const focusedId = ref(null);
const scrollContainer = ref(null);
const showAddMenu = ref(false);
const addActions = computed(() => {
    const actions = [
        {
            label: '逐筆新增',
            description: '輸入單筆登記紀錄',
            icon: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>',
            handler: () => openAdd('single')
        },
        {
            label: '多筆貼上新增',
            description: '貼上文字批次匯入',
            icon: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>',
            colorClass: 'bg-blue-50 text-blue-600',
            handler: () => openAdd('batch')
        }
    ];

    if (currentFolder.value) {
        actions.push({
            label: '複製全部至 LINE',
            description: '複製此資料夾所有記錄',
            icon: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>',
            colorClass: 'bg-emerald-50 text-emerald-600',
            handler: () => copyAllToLine()
        });
        actions.push({
            label: reorderMode.value ? '結束手動排序' : '手動排序',
            description: '調整記錄顯示順序',
            icon: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 6h16M4 12h16M4 18h7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>',
            colorClass: 'bg-amber-50 text-amber-600',
            handler: () => { reorderMode.value = !reorderMode.value; }
        });
    }

    return actions;
});
const showSearch = ref(false);
const searchQuery = ref('');

watch(searchQuery, debounce((newVal) => {
    if (newVal?.trim() && focusedId.value) {
        focusedId.value = null;
        expandedIds.value.clear();
    }
    currentPage.value = 1;
    loadData(1);
    if (scrollContainer.value) {
        scrollContainer.value.scrollTop = 0;
    }
}, 300));
const showExportMenu = ref(false);
const deleteConfirmId = ref(null);
const activePicker = ref(null); // { id, field, title }
const showRemarksModal = ref(false);
const activeRemarks = ref('');
const currentRemarksKey = ref(null);
const currentDnrId = ref(null);
const showItemDetails = ref(false);
const reorderMode = ref(false);
const paginationMeta = ref(null);
const currentPage = ref(1);

const handlePageChange = (page) => {
    currentPage.value = page;
    loadData(page);
};

const editMap = ref({}); // { 'itemId-dnId': { obtained_date, remarks } }

// Sync editMap for existing records to enable inline editing
watch(allTreasures, (newVal) => {
    // Sync editMap with fresh data
    newVal.forEach(item => {
        // Sync treasure metadata
        if (!editMap.value[item.id]) {
            editMap.value[item.id] = {
                name: item.name,
                purpose: item.purpose,
                acquisition_method: item.acquisition_method,
                remarks: item.remarks,
                record_date: item.record_date || '',
                related_map: {}
            };
        } else {
            if (!editingIds.value.has(item.id)) {
                editMap.value[item.id].name = item.name;
                editMap.value[item.id].purpose = item.purpose;
                editMap.value[item.id].acquisition_method = item.acquisition_method;
                editMap.value[item.id].remarks = item.remarks;
                editMap.value[item.id].record_date = item.record_date || '';
            }
        }

        dharmaNames.value.forEach(dn => {
            const key = `${item.id}-${dn.id}`;
            const existing = item.dharma_name_registries?.find(r => r.dharma_name_id === dn.id);
            const r   = existing?.remarks;
            const rel = existing?.related_personnel;
            const newRemarks      = Array.isArray(r)   ? r.join('\n')   : (r   || '');
            const newRelationship = Array.isArray(rel) ? rel.join('、') : (rel || '');

            if (!editMap.value[key]) {
                // 建立新的 key
                editMap.value[key] = {
                    obtained_date: existing?.obtained_date || '',
                    remarks:       newRemarks,
                    relationship:  newRelationship,
                    related_personnel: rel || []
                };
            } else if (!editingIds.value.has(item.id)) {
                // 非編輯模式下強制同步最新資料（修復：避免備註一閃而過）
                editMap.value[key].obtained_date    = existing?.obtained_date || '';
                editMap.value[key].remarks          = newRemarks;
                editMap.value[key].relationship     = newRelationship;
                editMap.value[key].related_personnel = rel || [];
            }
        });

        // 同步 custom_name 人員（非 dharma_name_id 的自訂名稱）
        (item.dharma_name_registries || []).forEach(dnr => {
            if (!dnr.dharma_name_id && dnr.custom_name) {
                const key = `${item.id}-${dnr.custom_name}`;
                const r   = dnr.remarks;
                const rel = dnr.related_personnel;
                const newRemarks      = Array.isArray(r)   ? r.join('\n')   : (r   || '');
                const newRelationship = Array.isArray(rel) ? rel.join('、') : (rel || '');

                if (!editMap.value[key]) {
                    editMap.value[key] = {
                        obtained_date: dnr.obtained_date || '',
                        remarks:       newRemarks,
                        relationship:  newRelationship,
                        related_personnel: rel || []
                    };
                } else if (!editingIds.value.has(item.id)) {
                    editMap.value[key].obtained_date    = dnr.obtained_date || '';
                    editMap.value[key].remarks          = newRemarks;
                    editMap.value[key].relationship     = newRelationship;
                    editMap.value[key].related_personnel = rel || [];
                }
            }
        });

    });
}, { immediate: true, deep: false });


watch(dharmaNames, (newDns) => {
    allTreasures.value.forEach(item => {
        // Ensure metadata exists
        if (!editMap.value[item.id]) {
            editMap.value[item.id] = {
                name: item.name,
                purpose: item.purpose,
                acquisition_method: item.acquisition_method,
                remarks: item.remarks,
                record_date: item.record_date || ''
            };
        }

        newDns.forEach(dn => {
            const key = `${item.id}-${dn.id}`;
            const existing = item.dharma_name_registries?.find(r => r.dharma_name_id === dn.id);
            if (!editMap.value[key]) {
                const r = existing?.remarks;
                editMap.value[key] = {
                    obtained_date: existing?.obtained_date?.replace(/-/g, '/') || '',
                    remarks: Array.isArray(r) ? r.join('\n') : (r || '')
                };
            }
        });
    });
}, { immediate: true });

const form = ref({
    master_id: null,
    category: 'major',
    name: '',
    purpose: '',
    acquisition_method: '',
    remarks: '',
    record_date: '',
    obtained_date: '',
    dharma_name_registries: []
});

const folders = computed(() => {
    if (!Array.isArray(masters.value) || masters.value.length === 0) {
        return [
            { id: 1, name: '老祖仙師' },
            { id: 2, name: '元始仙師' },
            { id: 3, name: '道祖仙師' },
            { id: 8, name: '閻王仙師' }
        ];
    }
    return masters.value.map(m => ({
        ...m,
        name: m.name === '父皇仙師' ? '父皇' : m.name
    }));
});


const loadData = async (page = 1) => {
    loading.value = true;
    try {
        const ts = new Date().getTime();
        const params = {
            page,
            t: ts
        };
        if (searchQuery.value) params.search = searchQuery.value;
        if (currentFolder.value) params.master_id = currentFolder.value.id;
        if (currentCategory.value) params.category = currentCategory.value;
        params.sortDesc = sortDesc.value ? 1 : 0;

        const [res, mres, dres] = await Promise.all([
            axios.get('/registries', { params }),
            axios.get('/api/masters-list'),
            axios.get('/api/dharma-names-list')
        ]);
        allTreasures.value = res.data.data || [];
        paginationMeta.value = {
            current_page: res.data.current_page,
            last_page: res.data.last_page,
            total: res.data.total
        };
        if (res.data.folderCounts && page === 1) {
            folderCounts.value = res.data.folderCounts;
            categoryCounts.value = res.data.categoryCounts || {};
        }
        masters.value = mres.data;
        dharmaNames.value = dres.data;
    } catch (e) {
        console.error('Load data failed:', e);
        allTreasures.value = [];
    } finally { 
        loading.value = false; 
    }
};

watch([sortDesc, currentFolder, currentCategory], () => {
    currentPage.value = 1;
    loadData(1);
    if (scrollContainer.value) {
        scrollContainer.value.scrollTop = 0;
    }
});

const filteredTreasures = computed(() => {
    // If an item is focused, only show that item
    if (focusedId.value) {
        return allTreasures.value.filter(t => t.id === focusedId.value);
    }
    // With server-side pagination, allTreasures.value already contains filtered data
    return allTreasures.value;
});

const dynamicHeaderTitle = computed(() => {
    if (!currentFolder.value) return '法寶登記專區';
    
    const catName = currentCategory.value === 'major' ? '重大皇恩登記簿' : '其他皇恩登記簿';
    return `${catName}-${currentFolder.value.name}`;
});

const getDharmaNameText = (dnr) => {
    if (dnr.dharma_name?.name) return dnr.dharma_name.name;
    if (dnr.dharma_name_id) {
        const dn = dharmaNames.value.find(d => d.id === dnr.dharma_name_id);
        if (dn) return dn.name;
    }
    return dnr.custom_name || '-';
};

const getSortedRegistries = (item) => {
    let registries = [...(item.dharma_name_registries || [])];
    

    return registries.sort((a, b) => {
        const indexA = dharmaNames.value.findIndex(dn => dn.id === a.dharma_name_id);
        const indexB = dharmaNames.value.findIndex(dn => dn.id === b.dharma_name_id);
        if (indexA !== -1 && indexB !== -1) return indexA - indexB;
        return (getDharmaNameText(a)).localeCompare(getDharmaNameText(b), 'zh-Hant');
    });
};

const getFilteredDharmaNames = computed(() => {
    const q = searchQuery.value?.toLowerCase().trim();
    if (!q) return dharmaNames.value;
    return dharmaNames.value.filter(dn => dn.name?.toLowerCase().includes(q));
});


const getFilteredSortedRegistries = (item) => {
    let list = getSortedRegistries(item);
    const q = searchQuery.value?.toLowerCase().trim();
    if (!q) return list;
    return list.filter(dnr => {
        const dnText = getDharmaNameText(dnr).toLowerCase();
        const rawRemarks = dnr.remarks;
        const remarksStr = Array.isArray(rawRemarks) ? rawRemarks.join(' ') : (rawRemarks || '');
        return dnText.includes(q) || remarksStr.toLowerCase().includes(q);
    });
};

const addPersonRowFromSelect = (itemId, event) => {
    const dnId = parseInt(event.target.value);
    if (!dnId) return;

    const item = allTreasures.value.find(t => t.id === itemId);
    if (!item) return;

    // Initialize the new person in editMap using the data from the 'new' row if any
    const newRowKey = `${itemId}-new`;
    const newDate = editMap.value[newRowKey]?.obtained_date || editMap.value[itemId]?.record_date || '';
    const newRemarks = editMap.value[newRowKey]?.remarks || '';

    const key = `${itemId}-${dnId}`;
    if (!editMap.value[key]) {
        editMap.value[key] = {
            obtained_date: newDate,
            remarks: newRemarks
        };
    }

    // Add to local registries if not already there so it shows up in the table immediately
    if (!item.dharma_name_registries) item.dharma_name_registries = [];
    if (!item.dharma_name_registries.find(r => r.dharma_name_id === dnId)) {
        const dn = dharmaNames.value.find(d => d.id === dnId);
        item.dharma_name_registries.push({
            dharma_name_id: dnId,
            dharma_name: dn,
            obtained_date: newDate,
            remarks: newRemarks
        });
    }

    // Reset the 'new' row fields
    editMap.value[newRowKey] = { obtained_date: '', remarks: '' };
    event.target.value = ""; // Reset select
};

const removePersonFromEdit = (itemId, dnId) => {
    const item = allTreasures.value.find(t => t.id === itemId);
    if (!item) return;
    
    // Remove from editMap
    delete editMap.value[`${itemId}-${dnId}`];
    
    // Remove from local list so it disappears visually
    item.dharma_name_registries = item.dharma_name_registries.filter(r => r.dharma_name_id !== dnId);
};

const personCentricList = computed(() => {
    if (currentCategory.value !== 'other' || !currentFolder.value) return [];
    const personMap = new Map();
    allTreasures.value
        .filter(t => t.master_id === currentFolder.value?.id && t.category === 'other')
        .forEach(t => {
            (t.dharma_name_registries || []).forEach(dnr => {
                const dn = dharmaNames.value.find(d => d.id === dnr.dharma_name_id);
                const personName = dn ? dn.name : dnr.custom_name;
                if (!personName) return;
                if (!personMap.has(personName)) personMap.set(personName, { name: personName, records: [] });
                personMap.get(personName).records.push({
                    id: t.id + '-' + (dnr.id || personName),
                    source_id: t.id,
                    treasure_name: t.name,
                    obtained_date: dnr.obtained_date,
                    remarks: dnr.remarks,
                    purpose: t.purpose
                });
            });
        });
    const sortedPersons = Array.from(personMap.values()).sort((a,b) => {
        const indexA = dharmaNames.value.findIndex(d => d.name === a.name);
        const indexB = dharmaNames.value.findIndex(d => d.name === b.name);
        if (indexA !== -1 && indexB !== -1) return indexA - indexB;
        return a.name.localeCompare(b.name, 'zh-Hant');
    });
    sortedPersons.forEach(p => p.records.sort((a,b) => (b.obtained_date||'').localeCompare(a.obtained_date||'')));
    return sortedPersons;
});

const displayTitle = computed(() => {
    if (!focusedId.value) return '';
    return allTreasures.value.find(t => t.id === focusedId.value)?.name || '';
});

const expandedPersonNames = ref(new Set());
const togglePerson = (name) => {
    const next = new Set(expandedPersonNames.value);
    if (next.has(name)) next.delete(name);
    else next.add(name);
    expandedPersonNames.value = next;
};

const openAdd = (mode = 'single') => {
    form.value = {
        master_id: currentFolder.value?.id || null,
        category: currentCategory.value || 'major',
        name: '',
        purpose: '',
        acquisition_method: '',
        remarks: '',
        record_date: mode === 'single' ? getTodayStr() : '',
        obtained_date: '',
        status: '已求得',
        dharma_name_registries: []
    };
    addMode.value = mode;
};

const getMasterName = (id) => {
    const m = masters.value.find(m => String(m.id) === String(id));
    if (m) {
        return m.name === '父皇仙師' ? '父皇' : m.name;
    }
    return '未設定';
};

const toggleExpand = (id) => {
    const nextExpanded = new Set(expandedIds.value);
    if (nextExpanded.has(id)) {
        nextExpanded.delete(id);
        focusedId.value = null;
        showItemDetails.value = false;
    } else {
        nextExpanded.clear();
        nextExpanded.add(id);
        // 點擊後進入焦點模式 (Solo Mode)，隱藏其他資料
        focusedId.value = id;
        showItemDetails.value = false;
    }
    expandedIds.value = nextExpanded;
};

const handleBack = () => {
    if (focusedId.value) {
        focusedId.value = null;
        expandedIds.value.clear();
        return;
    }
    if (addMode.value) addMode.value = null;
    else if (currentFolder.value) currentFolder.value = null;
    else if (currentCategory.value) currentCategory.value = null;
    else emit('goHome');
};

const saveSingle = async (data, shuntAction = null) => {
    if (!data.name?.trim()) {
        persistentToast.value = { msg: '✖ 請輸入法寶名稱', type: 'error' };
        return;
    }
    if (!data.master_id) {
        persistentToast.value = { msg: '✖ 請選擇仙師', type: 'error' };
        return;
    }

    // Master Mismatch Routing (Shunting)
    if (currentFolder.value && String(data.master_id) !== String(currentFolder.value.id) && !shuntAction) {
        persistentToast.value = {
            msg: `偵測到所選仙師為「${getMasterName(data.master_id)}」，與當前資料夾「${currentFolder.value.name}」不同。請問要存入何處？`,
            type: 'mismatchConfirm',
            actions: [
                { label: `存入 ${currentFolder.value.name}`, handler: () => saveSingle(data, 'force') },
                { label: `存入 ${getMasterName(data.master_id)}`, handler: () => saveSingle(data, 'shunt') }
            ]
        };
        return;
    }

    // Apply the chosen action
    if (shuntAction === 'force') {
        data.master_id = currentFolder.value.id;
    }

    // 前處理：將「親友」欄位轉換為備註內容後再送出
    const processedDnrs = (data.dharma_name_registries || []).map(p => {
        const { relationship, ...rest } = p;
        let remarks = rest.remarks || '';
        if (relationship && relationship.trim()) {
            let datePrefix = '';
            if (rest.obtained_date) {
                const dParts = rest.obtained_date.split('-');
                if (dParts.length === 3) {
                    datePrefix = `${dParts[0]}/${dParts[1].padStart(2,'0')}/${dParts[2].padStart(2,'0')}`;
                }
            }
            // 格式：日期  法號關係詞（例如：2026-05-02  靈昡父親）
            const namePart = rest.custom_name ? rest.custom_name.trim() : '';
            const relEntry = datePrefix
                ? `${datePrefix}  ${namePart}${relationship.trim()}`
                : `${namePart}${relationship.trim()}`;
            if (!remarks.includes(relEntry)) {
                remarks = remarks ? remarks + '\n' + relEntry : relEntry;
            }
            // Clear the obtained_date because the relationship person obtained it, not the dharma name holder
            rest.obtained_date = null;
        }
        return {
            ...rest,
            remarks,
            related_personnel: relationship
                ? relationship.split(/[、, ]+/).filter(x => x)
                : (rest.related_personnel || [])
        };
    });

    isSaving.value = true;
    try {
        const payload = { ...data, dharma_name_registries: processedDnrs };
        if (data.id) await axios.post(`/registries/${data.id}`, { ...payload, _method: 'PATCH' });
        else await axios.post('/registries', payload);
        addMode.value = null;
        expandedIds.value.clear();
        focusedId.value = null;
        loadData();
        persistentToast.value = { msg: '✓ 儲存成功', type: 'success' };
        setTimeout(() => { if (persistentToast.value?.type === 'success') persistentToast.value = null; }, 2000);
    } catch (e) {
        console.error(e);
        const errorMsg = e.response?.data?.error || e.response?.data?.message || '儲存失敗，請檢查資料';
        persistentToast.value = { msg: '✖ ' + errorMsg, type: 'error' };
    } finally {
        isSaving.value = false;
    }
};

const triggerBatchSave = async (batchData) => {
    isSaving.value = true;
    try {
        const text = batchData.input || '';
        const blocks = text.split(/\n\s*\n|\r\n\s*\r\n/).filter(b => b.trim());
        const masterNames = masters.value.map(m => m.name);
        const masterMap = {};
        masters.value.forEach(m => masterMap[m.name] = m.id);

        const nameAliasMap = {
            '金容': '靈果', '金涓': '靈慧', '金梅': '靈妙', '金蘭': '靈智', '金平': '靈平',
            '金瑞': '龍戰', '金耀': '龍勝', '金旭': '靈心', '金熹': '靈情', '金吉': '靈奇',
            '金祥': '靈傾', '金恩': '靈昡', '金鈺': '元續', '金穎': '赤峰',
            '金律': '閻㻇', '金欣': '閻闇', '閰琉': '閻尊', '金剛': '閰帝', '金頓': '閻爵',
            '金虹': '赤覺', '金湘': '紫元', '金雍': '道妙', '金無': '閻澤', '金真': '閻願',
            '金翎': '鳳尊', '金妙': '鳳媓'
        };
        const translateName = (raw) => nameAliasMap[raw] || raw;

        let rawRecords = [];
        let blockMasterId = batchData.masterId || (currentFolder.value?.id);
        let blockDate = batchData.date || '';
        let currentContextYear = new Date().getFullYear();

        const parseDateStr = (str, ctxYear = null) => {
            if (!str) return null;
            // Handle explicitly ROC prefixes like "民國113年" or "113年" or "113"
            const rocYearMatch = str.match(/^(?:民國)?\s*(\d{2,3})\s*年?$/);
            if (rocYearMatch) {
                const y = parseInt(rocYearMatch[1]) + 1911;
                return `${y}-01-01`;
            }

            // Priority 1: 4-digit CE
            const ceMatch = str.match(/\b(\d{4})[/\-\s](\d{1,2})[/\-\s](\d{1,2})\b/);
            if (ceMatch) return `${ceMatch[1]}-${ceMatch[2].padStart(2,'0')}-${ceMatch[3].padStart(2,'0')}`;
            // Priority 2: 2-3 digit ROC
            const rocMatch = str.match(/\b(\d{2,3})[/\-\s](\d{1,2})[/\-\s](\d{1,2})\b/);
            if (rocMatch) {
                const y = parseInt(rocMatch[1]) + 1911;
                return `${y}-${rocMatch[2].padStart(2,'0')}-${rocMatch[3].padStart(2,'0')}`;
            }
            // Priority 3: Month/Day only (Uses contextYear or Current Year)
            const mdMatch = str.match(/\b(\d{1,2})[/\-\s](\d{1,2})\b/);
            if (mdMatch) {
                const y = ctxYear || new Date().getFullYear();
                return `${y}-${mdMatch[1].padStart(2,'0')}-${mdMatch[2].padStart(2,'0')}`;
            }
            // Fallback for year only
            const standaloneYMatch = str.match(/^\s*(\d{2,4})\s*[年]?\s*$/);
            if (standaloneYMatch) {
                let y = parseInt(standaloneYMatch[1]);
                if (y < 1000) y += 1911;
                return `${y}-01-01`;
            }
            return null;
        };

        blocks.forEach(block => {
            const lines = block.split('\n').map(l => l.trim()).filter(l => l);
            if (lines.length === 0) return;

            let pendingTreasureName = '';
            let pendingAttrs = { purpose: '', remarks: '', status: '', record_date: null, obtained_date: null, acquisition_method: '' };
            let activeRecord = null;

            for (let i = 0; i < lines.length; i++) {
                let line = lines[i].normalize('NFKC').trim();

                // Inline Year Prefix (e.g. 民國113年 4/21 允求...)
                const inlineYearMatch = line.match(/^(?:民國)?\s*(\d{2,4})\s*年?\s+/);
                if (inlineYearMatch) {
                    let y = parseInt(inlineYearMatch[1]);
                    if (y < 1000) y += 1911;
                    currentContextYear = y;
                    line = line.replace(inlineYearMatch[0], '').trim();
                }

                // 1. Metadata Detection
                masterNames.forEach(mName => { 
                    if (line.includes(mName) && line.length < 15) {
                        blockMasterId = masterMap[mName];
                        activeRecord = null; // Clear context on new master
                    }
                });

                // Detect Standalone Year Line
                const standaloneYearMatch = line.match(/^(?:民國)?\s*(\d{2,4})\s*年?$/);
                if (standaloneYearMatch) {
                    let y = parseInt(standaloneYearMatch[1]);
                    if (y < 1000) y += 1911;
                    currentContextYear = y;
                    continue; // Skip the rest, it's just setting the context year
                }
                
                const lineDateParsed = parseDateStr(line, currentContextYear);
                const isPureDateStr = line.replace(/[\d/.\-年月日時分秒\s]/g, '').length === 0;
                if (lineDateParsed && !line.includes('|') && !line.includes('│') && !line.includes('\t') && isPureDateStr) {
                    blockDate = lineDateParsed;
                    activeRecord = null; // Clear context on new date header
                    if (rawRecords.length === 0 || !activeRecord) pendingAttrs.record_date = lineDateParsed;
                    continue;
                }

                if (line.includes('完畢') || masterNames.some(m => line === m || line === m + '仙師')) {
                    activeRecord = null;
                    continue;
                }
                
                let treasureName = '';
                let recipients = [];
                let lineDate = blockDate;
                let lineRemarks = '';
                let lineObtainedDate = null;

                // 2. Attribute Detection
                const attrKeywords = ['用意', '狀態', '備註', '求寶方式', '求寶', '由來', '得知日期', '登記日期', '求得日期', '日期'];
                const firstWord = line.split(/[\s：:]/)[0];
                if (attrKeywords.includes(firstWord)) {
                    const val = line.replace(new RegExp(`^${firstWord}[\\s：:]*`), '').trim();
                    const target = activeRecord || pendingAttrs;
                    
                    if (firstWord === '用意') target.purpose = (target.purpose ? target.purpose + '；' : '') + val;
                    else if (firstWord === '狀態') {
                        if (val.includes('已登記')) {
                            if (target === pendingAttrs) { target.status = '已登記'; target.obtained_date = target.record_date || blockDate; }
                            else { target.obtained_date = target.record_date || blockDate; }
                        }
                    }
                    else if (['得知日期', '登記日期', '求得日期', '日期'].includes(firstWord)) {
                        const d = parseDateStr(val, currentContextYear) || val;
                        target.record_date = d;
                        if (firstWord === '登記日期' || firstWord === '求得日期') target.obtained_date = d;
                    }
                    else if (firstWord === '求寶方式' || firstWord === '求寶') target.acquisition_method = val;
                    else if (firstWord === '備註') target.remarks = (target.remarks ? target.remarks + '\n' : '') + val;
                    continue;
                }

                // 3. Main Parsing logic
                // Extract date from start of line if present
                const lineStartDateMatch = line.match(/^(\d{1,4}[/.-]\d{1,2}[/.-]\d{1,2}|\d{1,2}[/.-]\d{1,2})\s+/);
                if (lineStartDateMatch) {
                    const parsed = parseDateStr(lineStartDateMatch[1], currentContextYear);
                    if (parsed) lineDate = parsed;
                    else lineDate = lineStartDateMatch[1].replace(/\//g, '-');
                    
                    if (!blockDate || blockDate === getTodayStr()) {
                        blockDate = lineDate;
                    }
                    line = line.replace(lineStartDateMatch[0], '').trim();
                }

                const kwMatch = line.match(/^\s*((允求|賜降|得知|賜予|賜|法寶名稱|法寶內容|求得)\s*)?(.*?)[：:](.*)/);
                if (kwMatch && kwMatch[3] && kwMatch[3].trim() && !attrKeywords.includes(kwMatch[3].trim())) {
                    let rawName = kwMatch[3].trim();
                    // Strip common prefixes from name if they were accidentally caught
                    rawName = rawName.replace(/^(允求|賜降|得知|賜予|賜|求得)\s*/, '');
                    treasureName = rawName;
                    
                    const content = kwMatch[4].trim();
                    if (content) {
                        recipients = content.split(/[，、, \s]+/).filter(n => n.trim());
                        if (content.includes('已登記')) lineObtainedDate = lineDate;
                    } else {
                        pendingTreasureName = treasureName;
                        continue;
                    }
                } 
                else if (line.match(/^(允求|賜降|得知|賜予|賜|求得)\s+/)) {
                    // Handle lines like "允求 森羅戒 金了、閻爵"
                    const parts = line.split(/\s+/);
                    const prefix = parts[0];
                    treasureName = parts[1];
                    recipients = parts.slice(2).join(' ').split(/[，、, \s]+/).filter(n => n.trim());
                    if (line.includes('已登記')) lineObtainedDate = lineDate;
                }
                else if (line.includes('求得')) {
                    const parts = line.split('求得').map(p => p.trim());
                    if (parts.length >= 2) {
                        recipients = [parts[0]];
                        treasureName = parts[1];
                        lineObtainedDate = lineDate;
                    }
                }
                else if (line.includes('|') || line.includes('│') || line.includes('\t')) {
                    const parts = line.split(/[|│\t]/).map(p => p.trim());
                    treasureName = parts[0];
                    if (parts[1]) recipients = [parts[1]];
                    if (parts[2]) {
                        const parsed = parseDateStr(parts[2], currentContextYear);
                        if (parsed) { lineDate = parsed; lineObtainedDate = parsed; }
                        else { lineDate = parts[2].replace(/\//g,'-'); lineObtainedDate = lineDate; }
                    }
                    if (parts[3]) lineRemarks = parts[3];
                }
                else if (pendingTreasureName) {
                    treasureName = pendingTreasureName;
                    recipients = line.split(/[，、, \s]+/).filter(n => n.trim());
                    pendingTreasureName = '';
                }
                else if (line.includes(' ') && !line.includes('：') && !line.includes(':')) {
                    const spaceParts = line.split(/\s+/);
                    if (spaceParts.length >= 2 && spaceParts[1].length <= 10) {
                        treasureName = spaceParts[0];
                        recipients = spaceParts.slice(1).join(' ').split(/[，、, \s]+/).filter(n => n.trim());
                    } else {
                        treasureName = line.trim();
                    }
                }
                else if (line.length > 0 && line.length < 50) {
                    treasureName = line;
                }

                if (treasureName) {
                    treasureName = treasureName.replace(/[：:。！？]$/, '').trim();
                    const dnr = recipients.map(rawName => {
                        let cleanRaw = rawName.trim();
                        // 處理括號規則：若有括號，優先提取內容
                        let parenMatch = cleanRaw.match(/^(.*?)\((.*?)\)$/);
                        let nameForProcessing = cleanRaw;
                        let extraRemarkFromParen = '';

                        if (parenMatch) {
                            // 範例：閻尊(玄閻宮) -> name=閻尊, extra=玄閻宮
                            // 範例：(元續之夫) -> name=元續之夫, extra=""
                            if (parenMatch[1].trim()) {
                                nameForProcessing = parenMatch[1].trim();
                                extraRemarkFromParen = parenMatch[2].trim();
                            } else {
                                nameForProcessing = parenMatch[2].trim();
                            }
                        }

                        let translated = translateName(nameForProcessing).trim();
                        if (!translated) return null;

                        let relMatch = translated.match(/^(.*?)([之的].+)$/);
                        if (!relMatch) {
                            const dnsList = dharmaNames.value || [];
                            const sortedDns = [...dnsList].sort((a, b) => (b.name?.length || 0) - (a.name?.length || 0));
                            const matchedDn = sortedDns.find(dn => dn.name && translated.startsWith(dn.name) && translated.length > dn.name.length);
                            if (matchedDn) relMatch = [translated, matchedDn.name, translated.substring(matchedDn.name.length)];
                        }

                        const finalDate = lineDate || blockDate || getTodayStr();
                        const dParts = finalDate.split('-');
                        const dStr = `${dParts[0]}/${dParts[1].padStart(2,'0')}/${dParts[2].padStart(2,'0')}`;

                        if (relMatch) {
                            const translateRel = (rel) => {
                                if (!rel) return rel;
                                const r = rel.trim();
                                if (r === '之父' || r === '父') return '父親';
                                if (r === '之母' || r === '母') return '母親';
                                if (r === '之嬤' || r === '嬤') return '奶奶';
                                if (r === '之夫' || r === '夫') return '先生';
                                return r.replace(/^[之的]/, '');
                            };
                            
                            const nameOnly = relMatch[1].trim();
                            const relPart = translateRel(relMatch[2]);
                            // 格式：日期  法號關係詞
                            let relEntry = `${dStr}  ${nameOnly}${relPart}`;
                            if (extraRemarkFromParen) relEntry += ` (${extraRemarkFromParen})`;
                            return { custom_name: nameOnly, remarks: relEntry, obtained_date: null };
                        }
                        
                        let finalRemarks = lineRemarks;
                        if (extraRemarkFromParen) {
                            finalRemarks = (finalRemarks ? finalRemarks + '\n' : '') + extraRemarkFromParen;
                        }
                        return { custom_name: translated, remarks: finalRemarks, obtained_date: lineObtainedDate || lineDate || blockDate };
                    }).filter(n => n !== null);

                    const newRec = {
                        name: treasureName, 
                        master_id: blockMasterId,
                        category: batchData.category || currentCategory.value || 'major',
                        record_date: lineDate || blockDate, 
                        obtained_date: lineObtainedDate,
                        acquisition_method: batchData.defaults?.acquisition_method || '',
                        purpose: batchData.defaults?.purpose || '',
                        remarks: batchData.defaults?.remarks || '',
                        dharma_name_registries: dnr
                    };

                    // Apply pending attributes
                    if (pendingAttrs.purpose) newRec.purpose = pendingAttrs.purpose;
                    if (pendingAttrs.remarks) newRec.remarks = pendingAttrs.remarks;
                    if (pendingAttrs.record_date) newRec.record_date = pendingAttrs.record_date;
                    if (pendingAttrs.obtained_date) newRec.obtained_date = pendingAttrs.obtained_date;
                    if (pendingAttrs.acquisition_method) newRec.acquisition_method = pendingAttrs.acquisition_method;
                    
                    // Reset pending
                    pendingAttrs = { purpose: '', remarks: '', status: '', record_date: null, obtained_date: null, acquisition_method: '' };

                    rawRecords.push(newRec);
                    activeRecord = newRec; // Set context
                }
            }
        });

        // --- MERGE STAGE ---
        const mergedMap = new Map();
        rawRecords.forEach(rec => {
            const cleanName = rec.name.trim();
            const key = `${cleanName}-${rec.master_id}`;
            if (mergedMap.has(key)) {
                const existing = mergedMap.get(key);
                if (new Date(rec.record_date) > new Date(existing.record_date)) existing.record_date = rec.record_date;
                if (rec.obtained_date) existing.obtained_date = rec.obtained_date;

                if (rec.acquisition_method && !existing.acquisition_method.includes(rec.acquisition_method)) {
                    existing.acquisition_method = existing.acquisition_method ? (existing.acquisition_method + '；' + rec.acquisition_method) : rec.acquisition_method;
                }
                if (rec.purpose && !existing.purpose.includes(rec.purpose)) {
                    existing.purpose = existing.purpose ? (existing.purpose + '；' + rec.purpose) : rec.purpose;
                }
                if (rec.remarks && !existing.remarks.includes(rec.remarks)) {
                    existing.remarks = existing.remarks ? (existing.remarks + '\n' + rec.remarks) : rec.remarks;
                }
                
                rec.dharma_name_registries.forEach(nr => {
                    const existingDnr = existing.dharma_name_registries.find(er => (er.custom_name === nr.custom_name && er.obtained_date === nr.obtained_date));
                    if (existingDnr) {
                        const currentRemarks = Array.isArray(existingDnr.remarks) ? existingDnr.remarks : (existingDnr.remarks ? [existingDnr.remarks] : []);
                        if (nr.remarks) {
                            const newRemarks = Array.isArray(nr.remarks) ? nr.remarks : [nr.remarks];
                            newRemarks.forEach(r => { if (!currentRemarks.includes(r)) currentRemarks.push(r); });
                            existingDnr.remarks = currentRemarks;
                        }
                    } else {
                        existing.dharma_name_registries.push(nr);
                    }
                });
            } else {
                mergedMap.set(key, rec);
            }
        });

        const records = Array.from(mergedMap.values());
        if (records.length === 0) {
            persistentToast.value = { msg: '✖ 無法解析出任何法寶資料', type: 'error' };
            isSaving.value = false;
            return;
        }

        await axios.post('/registries/batch', records);
        addMode.value = null;
        expandedIds.value.clear();
        focusedId.value = null;
        loadData();
        persistentToast.value = { msg: `✓ 批量新增成功 (${records.length} 筆)`, type: 'success' };
        setTimeout(() => { if (persistentToast.value?.type === 'success') persistentToast.value = null; }, 2000);
    } catch (e) {
        console.error(e);
        const errorMsg = e.response?.data?.error || '批量儲存失敗';
        persistentToast.value = { msg: '✖ ' + errorMsg, type: 'error' };
    } finally {
        isSaving.value = false;
    }
};

const openAndEdit = (id) => {
    const nextEditing = new Set(editingIds.value);
    const nextExpanded = new Set(expandedIds.value);
    
    if (nextEditing.has(id)) {
        nextEditing.delete(id);
    } else {
        nextEditing.add(id);
        if (!nextExpanded.has(id)) {
            nextExpanded.clear();
            nextExpanded.add(id);
        }
    }
    
    editingIds.value = nextEditing;
    expandedIds.value = nextExpanded;
    openMenuId.value = null;
};

const triggerRemarksEdit = (item, personIdentifier) => {
    // personIdentifier can be dnId (Number), dnr object, or 'new'
    if (editingIds.value.has(item.id)) {
        // In Edit Mode, we sync with editMap
        let key = '';
        if (personIdentifier === 'new') {
            key = `${item.id}-new`;
        } else if (typeof personIdentifier === 'object') {
            // It's a dnr object
            key = personIdentifier.dharma_name_id ? `${item.id}-${personIdentifier.dharma_name_id}` : `${item.id}-${personIdentifier.custom_name}`;
        } else {
            // It's a dnId
            key = `${item.id}-${personIdentifier}`;
        }
        
        currentRemarksKey.value = key;
        currentDnrId.value = null; // We save via the main form's save, but let's see
        activeRemarks.value = editMap.value[key]?.remarks || '';
    } else {
        // In View Mode, we use openRemarks logic
        const dnr = typeof personIdentifier === 'object' 
            ? personIdentifier 
            : item.dharma_name_registries?.find(r => r.dharma_name_id === personIdentifier);
        
        if (dnr) {
            openRemarks(dnr);
        } else {
            // Fallback or item-level remarks if needed? 
            // For now, only personnel
        }
    }
    showRemarksModal.value = true;
};

const handleAddFormRemarksEdit = ({ idx, remarks }) => {
    currentAddFormIdx.value = idx;
    activeRemarks.value = remarks;
    currentDnrId.value = null;
    currentRemarksKey.value = null;
    showRemarksModal.value = true;
};

const openRemarks = (dnrOrContent) => {
    if (typeof dnrOrContent === 'string') {
        activeRemarks.value = dnrOrContent;
        currentDnrId.value = null;
    } else {
        const r = dnrOrContent.remarks;
        activeRemarks.value = Array.isArray(r) ? r.join('\n') : (r || '');
        currentDnrId.value = dnrOrContent.id;
    }
    currentRemarksKey.value = null;
    showRemarksModal.value = true;
};

const openCombinedRemarks = (item) => {
    const combined = item.dharma_name_registries
        .filter(r => r.remarks && (Array.isArray(r.remarks) ? r.remarks.length > 0 : r.remarks))
        .map(r => {
            const name = r.dharma_name ? r.dharma_name.name : r.custom_name;
            const text = Array.isArray(r.remarks) ? r.remarks.join('\n  ') : r.remarks;
            return `【${name}】：${text}`;
        })
        .join('\n\n');
    openRemarks(combined);
};

const deleteItem = async (id) => {
    try {
        await axios.delete(`/registries/${id}`);
        persistentToast.value = { msg: '✓ 已成功刪除資料', type: 'success' };
        setTimeout(() => { persistentToast.value = null; }, 2000);
        
        // Reset states
        deleteConfirmId.value = null;
        focusedId.value = null;
        expandedIds.value = new Set();
        openMenuId.value = null;
        
        await loadData();
    } catch (e) {
        console.error(e);
        const errorMsg = e.response?.data?.error || '刪除失敗';
        persistentToast.value = { msg: '✖ ' + errorMsg, type: 'error' };
        setTimeout(() => { persistentToast.value = null; }, 2000);
    }
};

const executeDelete = () => {
    if (deleteConfirmId.value) {
        deleteItem(deleteConfirmId.value);
    }
};

const toggleMenu = (id) => openMenuId.value = openMenuId.value === id ? null : id;
const toggleSort = () => sortDesc.value = !sortDesc.value;



const handleReorder = async (item, newOrder) => {
    const targetOrder = parseInt(newOrder);
    if (isNaN(targetOrder)) return;

    const list = [...filteredTreasures.value];
    const oldIndex = list.findIndex(r => r.id === item.id);
    if (oldIndex === -1) return;

    // Remove from old position
    const [movedItem] = list.splice(oldIndex, 1);
    // Insert at new position (1-based index)
    list.splice(Math.max(0, targetOrder - 1), 0, movedItem);

    // Update sort_order for all in the FULL list
    const orders = list.map((r, idx) => ({
        id: r.id,
        sort_order: idx + 1
    }));

    // Update local state immediately
    list.forEach((r, idx) => {
        const orig = allTreasures.value.find(v => v.id === r.id);
        if (orig) orig.sort_order = idx + 1;
    });

    try {
        await axios.post('/registries/reorder', { orders });
        // Refresh to ensure server-side sorting is consistent
        await loadData();
    } catch (err) {
        console.error('Reorder failed:', err);
    }
};

const moveRegistryItem = async (item, direction) => {
    const list = filteredTreasures.value;
    const index = list.findIndex(t => t.id === item.id);
    const targetIdx = index + direction;
    if (targetIdx < 0 || targetIdx >= list.length) return;
    await handleReorder(item, targetIdx + 1);
};

const formatDate = (d) => {
    if (!d) return '-';
    // Use string splitting instead of new Date() to avoid timezone-related day shifts
    const s = String(d).trim();
    const parts = s.split(/[-/]/);
    if (parts.length === 3) {
        let y = parts[0];
        let m = parts[1].padStart(2, '0');
        let d_val = parts[2].padStart(2, '0');
        if (!isNaN(parseInt(y)) && !isNaN(parseInt(m)) && !isNaN(parseInt(d_val))) {
            return `${y}/${m}/${d_val}`;
        }
    }
    return s.replace(/-/g, '/');
};

const copyToLine = (item) => {
    // Collect records from either the saved state or the active edit state
    const currentRegistries = item.dharma_name_registries || [];
    const dnrText = currentRegistries
        .filter(r => r.obtained_date)
        .map(r => {
            const dn = dharmaNames.value.find(d => d.id === r.dharma_name_id);
            const name = dn?.name || r.custom_name || '未知人員';
            return `● ${name} | ${formatToROC(r.obtained_date)}${r.remarks ? ' | ' + r.remarks : ''}`;
        }).join('\n');
    
    const finalRecordsText = dnrText || '尚無紀錄';
    const text = `【${item.name}】\n用意：${item.purpose || '-'}\n登記狀況：\n${finalRecordsText}`;
    writeClipboard(text).then(success => {
        if (success) {
            persistentToast.value = { msg: '✓ 已複製貼 LINE 格式', type: 'success' };
            setTimeout(() => { if (persistentToast.value?.type === 'success') persistentToast.value = null; }, 1500);
        }
    });
    openMenuId.value = null;
};

const downloadItemData = (item) => {
    const headers = ['法號', '登記日期', '備註'];
    const rows = dharmaNames.value.map(dn => {
        const r = item.dharma_name_registries?.find(reg => reg.dharma_name_id === dn.id);
        return [dn.name, formatToROC(r?.obtained_date) || '', r?.remarks || ''].join(',');
    });
    
    // Add UTF-8 BOM to prevent garbled text (亂碼) in Excel
    const csvContent = "\ufeff" + [headers.join(','), ...rows].join('\n');
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement("a");
    const url = URL.createObjectURL(blob);
    link.setAttribute("href", url);
    link.setAttribute("download", `${item.name}_登記表.txt`);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    openMenuId.value = null;
};

const copyAllToLine = () => {
    const items = filteredTreasures.value;
    if (!items.length) {
        persistentToast.value = { msg: '✖ 尚無資料可複製', type: 'error' };
        return;
    }
    
    const text = items.map(item => {
        const dnrText = item.dharma_name_registries?.filter(r => r.obtained_date).map(r => {
            const dn = dharmaNames.value.find(d => d.id === r.dharma_name_id);
            const name = dn?.name || r.custom_name || '未知人員';
            return `● ${name} | ${formatToROC(r.obtained_date)}${r.remarks ? ' | ' + r.remarks : ''}`;
        }).join('\n') || '尚無紀錄';
        return `【${item.name}】\n用意：${item.purpose || '-'}\n登記狀況：\n${dnrText}`;
    }).join('\n\n------------------\n\n');
    
    navigator.clipboard.writeText(text);
    persistentToast.value = { msg: '✓ 已複製全部資料至 LINE', type: 'success' };
    setTimeout(() => persistentToast.value = null, 2000);
};

const downloadAllData = () => {
    const items = filteredTreasures.value;
    if (!items.length) {
        persistentToast.value = { msg: '✖ 尚無資料可下載', type: 'error' };
        return;
    }
    
    const contents = items.map(item => {
        const dnrText = item.dharma_name_registries?.filter(r => r.obtained_date).map(r => {
            const dn = dharmaNames.value.find(d => d.id === r.dharma_name_id);
            const name = dn?.name || r.custom_name || '未知人員';
            return `● ${name} | ${formatToROC(r.obtained_date)}${r.remarks ? ' | ' + r.remarks : ''}`;
        }).join('\r\n') || '尚無紀錄';
        
        return `【${item.name}】\r\n用意：${item.purpose || '-'}\r\n登記狀況：\r\n${dnrText}`;
    }).join('\r\n\r\n--------------------------------\r\n\r\n');
    
    const finalContent = `【法寶登記專區 - ${currentFolder.value.name} 完整清單】\r\n\r\n${contents}`;
    const blob = new Blob(["\ufeff" + finalContent], { type: 'text/plain;charset=utf-8;' });
    const link = document.createElement("a");
    const url = URL.createObjectURL(blob);
    link.setAttribute("href", url);
    link.setAttribute("download", `法寶登記專區_${currentFolder.value.name}_完整清單.txt`);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
};

const saveItemInPlace = async (item) => {
    isSaving.value = true;
    try {
        const registries = [];
        
        // Handle all existing and potential registries
        // We use the item.dharma_name_registries as the source of truth for what exists
        // and editMap for the updates.
        
        // 1. Process standard dharma names (from master list)
        dharmaNames.value.forEach(dn => {
            const key = `${item.id}-${dn.id}`;
            const data = editMap.value[key];
            // If we have edit data OR there was already a record for this person
            const wasExisting = item.dharma_name_registries?.some(r => r.dharma_name_id === dn.id);
            
            if (data && (data.obtained_date || data.remarks || data.relationship || wasExisting)) {
                registries.push({
                    dharma_name_id: dn.id,
                    obtained_date: (data && data.obtained_date) ? data.obtained_date.replace(/\//g, '-') : null,
                    remarks: (data && data.remarks !== undefined) ? data.remarks : (item.dharma_name_registries?.find(r => r.dharma_name_id === dn.id)?.remarks || ''),
                    related_personnel: (data && data.relationship !== undefined) ? (data.relationship ? data.relationship.split(/[、, ]+/).filter(x => x) : []) : (item.dharma_name_registries?.find(r => r.dharma_name_id === dn.id)?.related_personnel || [])
                });
            }
        });
        
        // 2. Process custom names (things added via text but not in system list)
        (item.dharma_name_registries || []).forEach(r => {
            if (!r.dharma_name_id && r.custom_name) {
                const data = editMap.value[item.id + '-' + r.custom_name] || {};
                registries.push({
                    custom_name: r.custom_name,
                    obtained_date: (data && data.obtained_date) ? data.obtained_date.replace(/\//g, '-') : null,
                    remarks: (data && data.remarks !== undefined) ? data.remarks : (r.remarks || ''),
                    related_personnel: (data && data.relationship !== undefined) ? (data.relationship ? data.relationship.split(/[、, ]+/).filter(x => x) : []) : (r.related_personnel || [])
                });
            }
        });
        
        const metadata = editMap.value[item.id] || {};
        await axios.post(`/registries/${item.id}`, { 
            ...item, 
            ...metadata,
            record_date: metadata.record_date || null,
            dharma_name_registries: registries,
            _method: 'PATCH' 
        });
        
        const nextEditing = new Set(editingIds.value);
        nextEditing.delete(item.id);
        editingIds.value = nextEditing;
        persistentToast.value = { msg: '✓ 儲存成功', type: 'success' };
        setTimeout(() => { if (persistentToast.value?.type === 'success') persistentToast.value = null; }, 2000);
        await loadData();
        openMenuId.value = null;
    } catch (e) {
        console.error(e);
        const errorMsg = e.response?.data?.error || '儲存失敗';
        persistentToast.value = { msg: '✖ ' + errorMsg, type: 'error' };
    } finally {
        isSaving.value = false;
    }
};
const handleRemarksViewerSave = async ({ dnrId, content }) => {
    if (currentAddFormIdx.value !== null) {
        if (addFormRef.value) {
            addFormRef.value.updatePersonnelRemarks(currentAddFormIdx.value, content);
        }
        showRemarksModal.value = false;
        currentAddFormIdx.value = null;
        return;
    }

    if (currentRemarksKey.value) {
        // Updating editMap (Form Edit Mode)
        if (editMap.value[currentRemarksKey.value]) {
            editMap.value[currentRemarksKey.value].remarks = content;
        }
        showRemarksModal.value = false;
        currentRemarksKey.value = null;
        return;
    }

    if (!dnrId) return;
    isSaving.value = true;
    try {
        await axios.patch(`/registries/personnel/${dnrId}/remarks`, {
            remarks: content
        });
        persistentToast.value = { msg: '✓ 備註已儲存', type: 'success' };
        setTimeout(() => { if (persistentToast.value?.type === 'success') persistentToast.value = null; }, 1500);
        await loadData();
    } catch (e) {
        persistentToast.value = { msg: '✖ 儲存失敗，請重試', type: 'error' };
    } finally {
        isSaving.value = false;
        showRemarksModal.value = false; // 確保關閉
        currentDnrId.value = null;     // 重置 ID
    }
};

const saveRemarksInline = async () => {
    if (!currentDnrId.value) return;
    isSaving.value = true;
    try {
        await axios.patch(`/registries/personnel/${currentDnrId.value}/remarks`, {
            remarks: activeRemarks.value
        });
        // 先關閉 Modal，再更新資料，避免 watch 更新干擾顯示中的內容
        showRemarksModal.value = false;
        currentDnrId.value = null;
        persistentToast.value = { msg: '✓ 備註已儲存', type: 'success' };
        setTimeout(() => { if (persistentToast.value?.type === 'success') persistentToast.value = null; }, 1500);
        await loadData();
    } catch (e) {
        persistentToast.value = { msg: '✖ 儲存失敗，請重試', type: 'error' };
    } finally {
        isSaving.value = false;
    }
};

const getFolderSum = (id) => {
    return folderCounts.value[id] || 0;
};
const getCategorySum = (category) => {
    return categoryCounts.value[category] || 0;
};
// C6: Body scroll lock for iOS compatibility
const isAnyModalOpen = computed(() => {
    return !!addMode.value || 
           !!persistentToast.value || 
           !!deleteConfirmId.value || 
           !!showRemarksModal.value || 
           !!showLuckyDraw.value || 
           !!showAddMenu.value ||
           (editingIds.value && editingIds.value.size > 0);
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
@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
.custom-scrollbar { overscroll-behavior-y: contain; }
.custom-scrollbar::-webkit-scrollbar { width: 5px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
* { -webkit-tap-highlight-color: transparent; }
</style>
