<template>
    <div class="bg-white h-[100dvh] flex flex-col relative overflow-hidden text-slate-900">
        <!-- Header (Sub-levels) -->
        <div v-if="currentFolder || currentCategory" class="border-b border-slate-300 flex items-center bg-white sticky top-0 z-10" style="padding: 10px 10px 8px 10px; min-height: 60px;">
            <button @click="handleBack" class="text-slate-400 p-3 mr-1 active:scale-90 transition-transform shrink-0">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" /></svg>
            </button>
            <h2 :class="[
                'font-black font-outfit tracking-tight flex-1 flex items-center min-w-0',
                currentFolder ? 'text-[21px] text-left' : 'text-[25px] text-left'
            ]" :style="{ color: (currentCategory === 'major' && currentFolder) ? 'black' : 'rgb(220, 20, 40)' }">
                <span v-if="focusedId" @click="focusedId = null; expandedIds.clear()" class="mr-3 p-1.5 bg-slate-100 rounded-full text-slate-500 cursor-pointer active:scale-90 transition-all font-black shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M10 19l-7-7m0 0l7-7m-7 7h18" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" /></svg>
                </span>
                <span class="truncate flex-1">
                    {{ (currentCategory === 'major' ? '重大皇恩登記簿' : '其他皇恩登記簿') + (currentFolder ? ' - ' + currentFolder.name : '') }}
                </span>
                <button v-if="currentFolder && !focusedId" @click="toggleSort" class="ml-2 px-2 py-1 text-[11px] text-indigo-500 bg-indigo-50 border border-indigo-100 rounded-lg active:scale-95 transition-all font-black shrink-0">
                    {{ sortDesc ? '新→舊' : '舊→新' }}
                </button>
            </h2>
        </div>

        <!-- Main Scrollable Area -->
        <div class="flex-1 overflow-y-auto custom-scrollbar" style="padding-bottom: 150px;">
            
            <!-- Category and Master Selection -->
            <div v-if="!currentFolder && !addMode" class="min-h-screen bg-white flex flex-col items-center">
                <div class="w-full px-4 py-8 flex items-center bg-white border-b border-slate-50 relative min-h-[80px]">
                    <button @click="$emit('goHome')" class="text-slate-400 p-4 active:scale-90 transition-transform z-10 shrink-0">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" /></svg>
                    </button>
                    <div class="flex-1 pr-12">
                    <h1 class="text-[24px] font-black text-red-600 tracking-tight text-center">
                        {{ currentCategory ? (currentCategory === 'major' ? '重大皇恩登記簿' : '其他皇恩登記簿') : '法寶登記專區' }}
                    </h1>
                    </div>
                </div>

                <!-- Root Categories -->
                <div v-if="!currentCategory" class="flex flex-col items-center space-y-6 mt-6 pb-20 w-full">
                    <button @click="currentCategory = 'major'" class="flex flex-col items-center justify-center bg-transparent active:scale-95 rounded-[24px] border-2 border-[rgb(240,20,40)] p-3 w-[180px] h-[200px] relative transition-all shadow-sm">
                        <div class="mb-2">
                            <svg class="w-[100px] h-[100px] drop-shadow-md" viewBox="0 0 64 64" fill="none">
                                <defs>
                                    <linearGradient id="goldGradL1" x1="0%" y1="0%" x2="100%" y2="100%">
                                        <stop offset="0%" style="stop-color:rgb(255, 230, 0);stop-opacity:1" />
                                        <stop offset="50%" style="stop-color:rgb(255, 200, 0);stop-opacity:1" />
                                        <stop offset="100%" style="stop-color:rgb(255, 170, 0);stop-opacity:1" />
                                    </linearGradient>
                                </defs>
                                <path d="M4 14C4 11.7909 5.79086 10 8 10H24.5L30 16H56C58.2091 16 60 17.7909 60 20V50C60 52.2091 58.2091 54 56 54H8C5.79086 54 4 52.2091 4 50V14Z" fill="url(#goldGradL1)" />
                                <path d="M4 22C4 19.7909 5.79086 18 8 18H56C58.2091 18 60 19.7909 60 22V50C60 52.2091 58.2091 54 56 54H8C5.79086 54 4 52.2091 4 50V22Z" fill="url(#goldGradL1)" stroke="rgba(255,255,255,0.6)" stroke-width="1"/>
                            </svg>
                        </div>
                        <div class="text-[20px] font-black text-red-700 leading-tight drop-shadow-sm text-center">重大皇恩<br>登記簿</div>
                    </button>

                    <button @click="currentCategory = 'other'" class="flex flex-col items-center justify-center bg-transparent active:scale-95 rounded-[24px] border-2 border-[rgb(240,20,40)] p-3 w-[180px] h-[200px] relative transition-all shadow-sm">
                        <div class="mb-2">
                            <svg class="w-[100px] h-[100px] drop-shadow-md" viewBox="0 0 64 64" fill="none">
                                <defs>
                                    <linearGradient id="redGradL2" x1="0%" y1="0%" x2="100%" y2="100%">
                                        <stop offset="0%" style="stop-color:rgb(220, 20, 40);stop-opacity:1" />
                                        <stop offset="50%" style="stop-color:rgb(190, 10, 30);stop-opacity:1" />
                                        <stop offset="100%" style="stop-color:rgb(160, 0, 20);stop-opacity:1" />
                                    </linearGradient>
                                </defs>
                                <path d="M4 14C4 11.7909 5.79086 10 8 10H24.5L30 16H56C58.2091 16 60 17.7909 60 20V50C60 52.2091 58.2091 54 56 54H8C5.79086 54 4 52.2091 4 50V14Z" fill="url(#redGradL2)" />
                                <path d="M4 22C4 19.7909 5.79086 18 8 18H56C58.2091 18 60 19.7909 60 22V50C60 52.2091 58.2091 54 56 54H8C5.79086 54 4 52.2091 4 50V22Z" fill="url(#redGradL2)" stroke="rgba(255,255,255,0.6)" stroke-width="1"/>
                            </svg>
                        </div>
                        <div class="text-[20px] font-black text-yellow-400 leading-tight drop-shadow-sm text-center">其他皇恩<br>登記簿</div>
                    </button>
                </div>

                <!-- Masters Grid -->
                <div v-else class="grid grid-cols-2 gap-[10px] p-4 place-items-center">
                    <button v-for="folder in folders" :key="folder.id" 
                        @click="currentFolder = folder"
                        class="flex flex-col items-center justify-center transition-all active:scale-95 rounded-2xl border border-red-50 group p-2 w-[160px] h-[160px] relative">
                        
                        <div class="relative mb-1">
                             <svg class="w-[90px] h-[90px] transition-transform group-hover:scale-110 drop-shadow-md" viewBox="0 0 64 64" fill="none">
                                <defs>
                                <linearGradient id="folderGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                                    <template v-if="currentCategory === 'major'">
                                        <stop offset="0%" style="stop-color:rgb(255, 230, 0);stop-opacity:1" />
                                        <stop offset="50%" style="stop-color:rgb(255, 200, 0);stop-opacity:1" />
                                        <stop offset="100%" style="stop-color:rgb(255, 170, 0);stop-opacity:1" />
                                    </template>
                                    <template v-else>
                                        <stop offset="0%" style="stop-color:rgb(220, 20, 40);stop-opacity:1" />
                                        <stop offset="50%" style="stop-color:rgb(190, 10, 30);stop-opacity:1" />
                                        <stop offset="100%" style="stop-color:rgb(160, 0, 20);stop-opacity:1" />
                                    </template>
                                </linearGradient>
                                </defs>
                                <path d="M4 14C4 11.7909 5.79086 10 8 10H24.5L30 16H56C58.2091 16 60 17.7909 60 20V50C60 52.2091 58.2091 54 56 54H8C5.79086 54 4 52.2091 4 50V14Z" fill="url(#folderGrad)" />
                                <path d="M4 22C4 19.7909 5.79086 18 8 18H56C58.2091 18 60 19.7909 60 22V50C60 52.2091 58.2091 54 56 54H8C5.79086 54 4 52.2091 4 50V22Z" fill="url(#folderGrad)" stroke="rgba(255,255,255,0.6)" stroke-width="1"/>
                            </svg>
                        </div>
                        <div class="text-center px-1">
                            <div :class="[
                                'font-black tracking-tight leading-tight text-center transition-all text-[21px]',
                                folder.name === '閻王仙師' ? 'text-black' : (currentCategory === 'major' ? 'text-red-700' : 'text-yellow-400')
                            ]" style="font-weight: 900 !important;">{{ folder.name }}</div>
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
            <div v-else class="px-0 bg-white">
                <div style="padding: 0px 8px 10px 8px;" class="mt-0">
                    <div v-if="loading" class="text-center py-10">
                        <div class="inline-block animate-spin rounded-full h-10 w-10 border-4 border-slate-100 border-t-indigo-600 mb-4"></div>
                        <p class="text-xs font-black font-outfit uppercase tracking-widest text-slate-400">載入中...</p>
                    </div>

                    <template v-else>
                        <div v-for="item in filteredTreasures" :key="item.id" 
                             class="bg-white p-4 mb-4 border border-slate-100 rounded-[28px] shadow-sm relative transition-all cursor-pointer hover:shadow-md active:bg-slate-50">
                            
                            <!-- Action Dropdown Trigger -->
                            <div class="absolute top-4 right-4 z-30">
                                <button @click.stop="openMenuId = openMenuId === item.id ? null : item.id" 
                                        class="p-2 text-slate-400 hover:text-indigo-600 active:scale-90 transition-all rounded-full bg-slate-50/50">
                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                        <circle cx="5" cy="12" r="2" />
                                        <circle cx="12" cy="12" r="2" />
                                        <circle cx="19" cy="12" r="2" />
                                    </svg>
                                </button>
                                
                                <div v-if="openMenuId === item.id" 
                                     class="absolute right-0 mt-2 w-48 bg-white border border-slate-100 rounded-2xl shadow-2xl z-40 py-2 ring-1 ring-black ring-opacity-5 animate-fade-in divide-y divide-slate-50 overflow-hidden">
                                    <button @click.stop="toggleExpand(item.id); openMenuId = null" 
                                            class="w-full text-left px-4 py-3 text-[16px] font-black text-slate-700 hover:bg-slate-50 flex items-center transition-colors">
                                        <svg class="w-5 h-5 mr-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path v-if="expandedIds.has(item.id)" d="M19 15l-7-7-7 7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
                                            <path v-else d="M5 9l7 7 7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        {{ expandedIds.has(item.id) ? '收起詳情' : '展開詳情' }}
                                    </button>
                                    <button @click.stop="openAndEdit(item.id); openMenuId = null" 
                                            class="w-full text-left px-4 py-3 text-[16px] font-black text-slate-700 hover:bg-slate-50 flex items-center transition-colors">
                                        <svg class="w-5 h-5 mr-3 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" /></svg>
                                        修改資料
                                    </button>
                                    <button @click.stop="copyToLine(item); openMenuId = null" 
                                            class="w-full text-left px-4 py-3 text-[16px] font-black text-slate-700 hover:bg-slate-50 flex items-center transition-colors">
                                        <svg class="w-5 h-5 mr-3 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" /></svg>
                                        貼 LINE
                                    </button>
                                    <button @click.stop="downloadItemData(item); openMenuId = null" 
                                            class="w-full text-left px-4 py-3 text-[16px] font-black text-slate-700 hover:bg-slate-50 flex items-center transition-colors">
                                        <svg class="w-5 h-5 mr-3 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" /></svg>
                                        下載資料
                                    </button>
                                    <button @click.stop="deleteConfirmId = item.id; openMenuId = null" 
                                            class="w-full text-left px-4 py-3 text-[16px] font-black text-red-600 hover:bg-red-50 flex items-center transition-colors">
                                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" /></svg>
                                        刪除資料
                                    </button>
                                </div>
                            </div>

                            <!-- Card Header (Toggle Expansion) -->
                            <div @click="toggleExpand(item.id)" class="space-y-2">
                                <div class="flex items-center justify-between pr-12">
                                    <div class="text-[14px] text-slate-400 font-black font-outfit">{{ item.record_date?.replace(/-/g, '/') || '-' }}</div>
                                </div>
                                <div class="flex items-center justify-between group/title pr-12">
                                    <div class="flex flex-col">
                                        <div class="text-[18px] font-black text-black leading-tight truncate font-outfit flex items-center">
                                            {{ item.name }}
                                            <button v-if="expandedIds.has(item.id) && (item.purpose || item.acquisition_method || item.remarks)" 
                                                    @click.stop="showItemDetails = !showItemDetails" 
                                                    class="ml-2 p-1 text-slate-300 hover:text-indigo-500 transition-all active:scale-90">
                                                <svg :class="['w-5 h-5 transition-transform duration-300', showItemDetails ? 'rotate-180 text-indigo-500' : '']" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path d="M19 9l-7 7-7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                    <div v-if="!expandedIds.has(item.id)" class="ml-2 text-slate-300 group-hover/title:text-indigo-400 transition-colors">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path d="M19 9l-7 7-7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </div>
                                </div>
                                
                                <div v-if="!expandedIds.has(item.id) && item.dharma_name_registries?.length" class="mt-1 flex flex-wrap gap-1">
                                    <span v-for="dnr in item.dharma_name_registries" :key="dnr.id" class="text-[13px] text-indigo-700 bg-indigo-50 px-1.5 py-0.5 rounded-md border border-indigo-100 font-outfit font-black">
                                        {{ dnr.dharma_name ? dnr.dharma_name.name : dnr.custom_name }}
                                    </span>
                                </div>
                            </div>

                            <!-- Expanded Details -->
                            <div v-if="expandedIds.has(item.id)" class="mt-4 pt-4 border-t border-slate-50 space-y-6 animate-fade-in">
                                
                                <!-- In-place Edit Form -->
                                    <div v-if="editingIds.has(item.id)" class="p-4 bg-indigo-50/30 rounded-2xl border border-indigo-100/50 space-y-5 shadow-inner">
                                        <div class="flex items-center space-x-2 text-indigo-600 pb-1 border-b border-indigo-100">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" /></svg>
                                            <span class="text-[13px] font-black font-outfit uppercase tracking-widest">正在修改法寶資料</span>
                                        </div>
                                        <div class="grid grid-cols-1 gap-4">
                                            <div class="space-y-1">
                                                <label class="text-[11px] text-slate-400 uppercase tracking-widest font-black font-outfit">日期</label>
                                                <div @click="activePicker = { id: item.id, field: 'record_date', title: '修改日期' }" 
                                                    class="w-full bg-white border border-slate-200 rounded-xl px-3 h-[42px] flex items-center justify-between cursor-pointer shadow-sm transition-all">
                                                    <span :class="editMap[item.id].record_date ? 'text-black' : 'text-slate-300'" class="text-[17px] font-black font-outfit uppercase">
                                                        {{ editMap[item.id].record_date || '未設定' }}
                                                    </span>
                                                    <svg class="w-5 h-5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                                </div>
                                            </div>

                                            <div class="space-y-1">
                                                <label class="text-[11px] text-slate-400 uppercase tracking-widest font-black font-outfit">法寶用意</label>
                                                <input type="text" v-model="editMap[item.id].purpose" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-[17px] font-black focus:ring-2 focus:ring-indigo-100 outline-none shadow-sm transition-all font-outfit">
                                            </div>
                                            <div class="space-y-1">
                                                <label class="text-[11px] text-slate-400 uppercase tracking-widest font-black font-outfit">求寶方式</label>
                                                <input type="text" v-model="editMap[item.id].acquisition_method" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-[17px] font-black focus:ring-2 focus:ring-indigo-100 outline-none shadow-sm transition-all font-outfit">
                                            </div>
                                            <div class="space-y-1">
                                                <label class="text-[11px] text-slate-400 uppercase tracking-widest font-black font-outfit">備註</label>
                                                <input type="text" v-model="editMap[item.id].remarks" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-[17px] font-black focus:ring-2 focus:ring-indigo-100 outline-none shadow-sm transition-all font-outfit">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Normal View -->
                                    <div v-else class="space-y-6">
                                        <div v-if="!item.dharma_name_registries?.some(r => r.obtained_date)" class="space-y-1">
                                            <label class="text-[11px] text-slate-400 uppercase tracking-widest font-black font-outfit">日期</label>
                                            <div class="text-[17px] font-black text-slate-800 font-outfit">{{ item.record_date?.replace(/-/g, '/') }}</div>
                                        </div>
                                        
                                        <div v-if="showItemDetails" class="space-y-5 bg-slate-50/50 p-4 rounded-2xl border border-slate-100/50 animate-fade-in">
                                            <div v-if="item.purpose" class="space-y-1">
                                                <label class="text-[11px] text-slate-400 uppercase tracking-widest font-black font-outfit">法寶用意</label>
                                                <div class="text-[17px] font-black text-slate-700 leading-relaxed font-outfit">{{ item.purpose }}</div>
                                            </div>
                                            <div v-if="item.acquisition_method" class="space-y-1">
                                                <label class="text-[11px] text-slate-400 uppercase tracking-widest font-black font-outfit">求寶方式</label>
                                                <div class="text-[17px] font-black text-slate-700 font-outfit">{{ item.acquisition_method }}</div>
                                            </div>
                                            <div v-if="item.remarks" class="space-y-1">
                                                <label class="text-[11px] text-slate-400 uppercase tracking-widest font-black font-outfit">備註</label>
                                                <div class="text-[17px] font-black text-slate-700 font-outfit">{{ item.remarks }}</div>
                                            </div>
                                        </div>
                                    </div>

                                <!-- Personnel Section -->
                                <div class="space-y-3 pt-2">
                                    <label class="text-[13px] text-indigo-700 uppercase tracking-widest font-black font-outfit block border-b border-indigo-50 pb-1">承接人員</label>
                                    
                                    <template v-if="currentCategory === 'major'">
                                        <div class="overflow-x-auto rounded-2xl border border-slate-100 shadow-sm mb-20">
                                            <table class="w-full border-collapse bg-white text-[16px]">
                                                <thead>
                                                    <tr class="bg-indigo-50/50 text-slate-700 font-outfit">
                                                        <th class="border-b border-slate-100 px-3 py-2.5 text-left font-black w-[60px] whitespace-nowrap">法號</th>
                                                        <th class="border-b border-slate-100 px-3 py-2.5 text-left font-black w-[155px] whitespace-nowrap">日期</th>
                                                        <th class="border-b border-slate-100 px-3 py-2.5 text-left font-black">備註</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="dn in dharmaNames" :key="dn.id" class="hover:bg-slate-50 transition-colors">
                                                        <td class="border-b border-slate-50 px-3 py-2 font-black text-black whitespace-nowrap bg-slate-50/20 font-outfit">
                                                            {{ dn.name }}
                                                        </td>
                                                        <td class="border-b border-slate-50 p-0 text-black">
                                                            <div class="flex items-center px-2 py-1 h-[42px]" 
                                                                 :class="editingIds.has(item.id) ? 'cursor-pointer hover:bg-indigo-50/50' : ''"
                                                                 @click="editingIds.has(item.id) ? (activePicker = { id: item.id + '-' + dn.id, field: 'obtained_date', title: dn.name }) : null">
                                                                <span :class="editMap[item.id + '-' + dn.id].obtained_date ? 'text-black' : 'text-slate-200'" class="text-[15px] font-black font-outfit">
                                                                    {{ editMap[item.id + '-' + dn.id].obtained_date || (editingIds.has(item.id) ? '----/--/--' : '-') }}
                                                                </span>
                                                            </div>
                                                        </td>
                                                        <td class="border-b border-slate-50 p-0 text-black">
                                                            <div @click="editingIds.has(item.id) ? (currentRemarksKey = item.id + '-' + dn.id, showRemarksModal = true) : null" 
                                                                 class="w-full h-[42px] px-3 flex items-center transition-colors group/edit-cell"
                                                                 :class="editingIds.has(item.id) ? 'cursor-pointer hover:bg-indigo-50/50' : ''">
                                                                <span :class="editMap[item.id + '-' + dn.id].remarks ? 'text-black' : 'text-slate-300'" class="text-[15px] font-black font-outfit truncate">
                                                                    {{ editMap[item.id + '-' + dn.id].remarks ? (editMap[item.id + '-' + dn.id].remarks.length > 4 ? editMap[item.id + '-' + dn.id].remarks.substring(0, 4) + '...' : editMap[item.id + '-' + dn.id].remarks) : '...' }}
                                                                </span>
                                                                <svg v-if="editingIds.has(item.id)" class="w-3 h-3 ml-auto text-slate-200 group-hover/edit-cell:text-indigo-400 opacity-0 group-hover/edit-cell:opacity-100 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" /></svg>
                                                            </div>
                                                        </td>                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        
                                        <!-- Sticky Save Button Bar -->
                                        <div v-if="editingIds.has(item.id)" class="fixed bottom-0 left-0 right-0 p-4 bg-white/80 backdrop-blur-md border-t border-slate-100 z-50 flex justify-center shadow-[0_-10px_20px_rgba(0,0,0,0.02)] animate-fade-in">
                                            <button @click="saveItemInPlace(item)" class="w-full max-w-md py-4 bg-red-600 text-white rounded-2xl font-black text-[18px] active:scale-95 shadow-xl shadow-red-100 transition-all flex items-center justify-center font-outfit">
                                                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" /></svg>
                                                {{ isSaving ? '儲存中...' : (editingIds.has(item.id) ? '保存所有修改' : '保存表格內容') }}
                                            </button>
                                        </div>
                                    </template>
                                    <template v-else>
                                        <div class="grid grid-cols-1 gap-2">
                                            <div v-for="dnr in item.dharma_name_registries" :key="dnr.id" class="p-4 border border-slate-100 rounded-[22px] bg-white shadow-sm flex items-center justify-between">
                                                <div class="flex flex-col">
                                                    <span class="font-black text-[18px] text-black font-outfit">{{ dnr.dharma_name ? dnr.dharma_name.name : dnr.custom_name }}</span>
                                                    <span v-if="dnr.remarks" class="text-[14px] text-slate-500 mt-0.5 font-outfit">{{ dnr.remarks }}</span>
                                                </div>
                                                <span class="text-[13px] text-slate-400 font-outfit font-black uppercase">{{ dnr.obtained_date?.replace(/-/g, '/') }}</span>
                                            </div>
                                        </div>
                                        <button v-if="currentCategory !== 'major'" @click.stop="confirmDelete(item.id)" class="w-full py-4 mt-6 text-[15px] text-red-500 font-black border border-red-50 bg-red-50/40 rounded-2xl active:bg-red-50 transition-colors uppercase tracking-widest font-outfit">刪除此法寶紀錄</button>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- Navigation Components -->
        <mobile-navbar 
            :can-back="true"
            @back="handleBack"
            @home="$emit('goHome')"
            @action="currentFolder ? (showAddMenu = true) : openAdd()"
            @search="showSearch = !showSearch"
            @more="showExportMenu = !showExportMenu"
        />

        <!-- Add Action Menu Overlay -->
        <div v-if="showAddMenu" class="fixed inset-0 z-[150] flex items-end justify-center p-4 bg-slate-900/40 backdrop-blur-[2px] animate-fade-in" @click="showAddMenu = false">
            <div class="bg-white w-full max-w-[320px] rounded-[24px] overflow-hidden shadow-2xl animate-pop-in mb-20" @click.stop>
                <div class="p-1 space-y-0.5">
                    <button @click="addMode = 'single'; showAddMenu = false" class="w-full p-2.5 flex items-center space-x-3 hover:bg-slate-50 transition-colors rounded-xl group">
                        <div class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0 group-active:scale-90 transition-transform">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </div>
                        <div class="flex flex-col text-left">
                            <span class="text-[15px] font-black font-outfit text-slate-800">逐筆新增</span>
                            <span class="text-[11px] text-slate-400 font-medium font-outfit">手動輸入每一項重大皇恩詳細資...</span>
                        </div>
                    </button>

                    <button @click="addMode = 'batch'; showAddMenu = false" class="w-full p-2.5 flex items-center space-x-3 hover:bg-slate-50 transition-colors rounded-xl group border-t border-slate-50">
                        <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0 group-active:scale-90 transition-transform">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </div>
                        <div class="flex flex-col text-left">
                            <span class="text-[15px] font-black font-outfit text-slate-800">多筆一次新增</span>
                            <span class="text-[11px] text-slate-400 font-medium font-outfit">快速解析 LINE 聊天內容紀錄</span>
                        </div>
                    </button>

                    <button @click="copyAllToLine(); showAddMenu = false" class="w-full p-2.5 flex items-center space-x-3 hover:bg-slate-50 transition-colors rounded-xl group border-t border-slate-50">
                        <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 group-active:scale-90 transition-transform">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </div>
                        <div class="flex flex-col text-left">
                            <span class="text-[15px] font-black font-outfit text-slate-800">複製隨貼 LINE (全部)</span>
                            <span class="text-[11px] text-slate-400 font-medium font-outfit">將此分類完整清單複製至剪貼簿</span>
                        </div>
                    </button>

                    <button @click="downloadAllData(); showAddMenu = false" class="w-full p-2.5 flex items-center space-x-3 hover:bg-slate-50 transition-colors rounded-xl group border-t border-slate-50">
                        <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0 group-active:scale-90 transition-transform">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </div>
                        <div class="flex flex-col text-left">
                            <span class="text-[15px] font-black font-outfit text-slate-800">下載匯出 EXCEL (全部)</span>
                            <span class="text-[11px] text-slate-400 font-medium font-outfit">將此分類匯出為標準試算表檔案</span>
                        </div>
                    </button>
                    
                    <div class="p-2 border-t border-slate-50">
                        <button @click="showAddMenu = false" class="w-full py-2 text-slate-400 font-black text-[14px] font-outfit active:scale-95 transition-all uppercase tracking-widest">取消</button>
                    </div>
                </div>
            </div>
        </div>

        <registry-add-form 
            v-if="addMode"
            :mode="addMode" 
            :initialData="form"
            :masters="masters"
            :isSaving="isSaving"
            @saveSingle="saveSingle"
            @saveBatch="triggerBatchSave"
            @cancel="addMode = null"
        />
        
        <!-- Persistent Toasts/Picker -->
        <div v-if="persistentToast" class="fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 z-[9999] px-6 w-full max-w-sm">
             <div class="bg-white p-6 rounded-[32px] shadow-2xl border border-slate-100 text-center animate-fade-in">
                 <p class="text-[17px] font-black text-slate-800 font-outfit leading-relaxed">{{ persistentToast.msg }}</p>
                 <div v-if="persistentToast.type === 'confirm'" class="mt-6 flex space-x-3">
                     <button v-for="a in persistentToast.actions" @click="a.handler" class="flex-1 py-3.5 rounded-2xl bg-red-50 text-red-600 font-black text-[14px] uppercase tracking-widest font-outfit active:scale-95 transition-all">{{ a.label }}</button>
                     <button @click="persistentToast = null" class="flex-1 py-3.5 rounded-2xl bg-slate-50 text-slate-400 font-black text-[14px] uppercase tracking-widest font-outfit active:scale-95 transition-all">取消</button>
                 </div>
             </div>
        </div>
        
        <!-- Delete Confirmation Overlay -->
        <div v-if="deleteConfirmId" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-md animate-fade-in">
            <div class="bg-white w-full max-w-sm rounded-[32px] p-8 shadow-2xl animate-pop-in">
                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" /></svg>
                </div>
                <h3 class="text-[22px] font-black text-center mb-2 font-outfit">確定要刪除嗎？</h3>
                <p class="text-slate-500 text-center mb-8 font-black font-outfit leading-relaxed">此操作將永久刪除此筆載錄，且無法復原。請確認身分後再執行。</p>
                <div class="flex flex-col space-y-3">
                    <button @click="deleteItem(deleteConfirmId)" class="w-full py-4 bg-red-600 text-white rounded-2xl font-black text-[18px] active:scale-95 transition-all font-outfit">
                        是的，確認刪除
                    </button>
                    <button @click="deleteConfirmId = null" class="w-full py-4 bg-slate-100 text-slate-600 rounded-2xl font-black text-[18px] active:scale-95 transition-all font-outfit">
                        返回
                    </button>
                </div>
            </div>
        </div>

        <!-- Backdrops & Pickers -->
        <div v-if="openMenuId" @click="openMenuId = null" class="fixed inset-0 z-[25] bg-black/5 backdrop-blur-[1px]"></div>
        
        <compact-date-picker 
            v-if="activePicker"
            v-model="editMap[activePicker.id][activePicker.field]"
            :title="activePicker.title"
            @close="activePicker = null"
        />

        <!-- Remarks Modal -->
        <div v-if="showRemarksModal" class="fixed inset-0 z-[200] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-md animate-fade-in">
            <div class="bg-white w-full max-w-lg rounded-[32px] p-6 shadow-2xl animate-pop-in flex flex-col max-h-[80vh]">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-[20px] font-black font-outfit text-slate-800">編輯詳細備註</h3>
                    <button @click="showRemarksModal = false" class="text-slate-400 hover:text-slate-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" /></svg>
                    </button>
                </div>
                <textarea v-model="editMap[currentRemarksKey].remarks" class="flex-1 w-full bg-slate-50 border-none rounded-2xl p-4 text-[18px] font-black font-outfit text-slate-800 focus:ring-2 focus:ring-indigo-100 outline-none leading-relaxed min-h-[300px]" placeholder="在此輸入更多詳細內容..."></textarea>
                <div class="mt-6">
                    <button @click="showRemarksModal = false" class="w-full py-4 bg-indigo-600 text-white rounded-2xl font-black text-[18px] active:scale-95 transition-all font-outfit shadow-xl shadow-indigo-100">
                        完成編輯
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, defineEmits, watch } from 'vue';
import axios from 'axios';
import MobileNavbar from './MobileNavbar.vue';
import RegistryAddForm from './RegistryAddForm.vue';
import CompactDatePicker from './CompactDatePicker.vue';

const emit = defineEmits(['goHome']);

// States
const currentCategory = ref(null); 
const currentFolder = ref(null);
const addMode = ref(null);
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
const showAddMenu = ref(false);
const showSearch = ref(false);
const showExportMenu = ref(false);
const deleteConfirmId = ref(null);
const activePicker = ref(null); // { id, field, title }
const showRemarksModal = ref(false);
const currentRemarksKey = ref(null);
const showItemDetails = ref(false);
const editMap = ref({}); // { 'itemId-dnId': { obtained_date, remarks } }

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
                related_map: {} // Map to store related personnel per person
            };
        } else {
             // Update existing metadata if not currently editing or if new data arrived
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
            if (!editMap.value[key]) {
                editMap.value[key] = {
                    obtained_date: existing?.obtained_date || '',
                    remarks: existing?.remarks || '',
                    related_personnel: existing?.related_personnel || []
                };
            } else {
                // Update existing if not currently editing that specific field? 
                // For now just ensure related_personnel is not lost
                if (existing) editMap.value[key].related_personnel = existing.related_personnel;
            }
        });
    });
}, { immediate: true });

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
                editMap.value[key] = {
                    obtained_date: existing?.obtained_date?.replace(/-/g, '/') || '',
                    remarks: existing?.remarks || ''
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

const folders = ref([
    { id: 1, name: '老祖仙師' },
    { id: 2, name: '元始仙師' },
    { id: 3, name: '道祖仙師' },
    { id: 4, name: '靈寶仙師' },
    { id: 5, name: '父皇' },
    { id: 6, name: '太宰仙師' },
    { id: 7, name: '太子' },
    { id: 8, name: '閻王仙師' }
]);

const loadData = async () => {
    loading.value = true;
    try {
        const [res, mres, dres] = await Promise.all([
            axios.get('/registries'),
            axios.get('/api/masters-list'),
            axios.get('/api/dharma-names-list')
        ]);
        allTreasures.value = res.data;
        masters.value = mres.data;
        dharmaNames.value = dres.data;
    } catch (e) {} finally { loading.value = false; }
};

onMounted(loadData);

const filteredTreasures = computed(() => {
    if (!currentFolder.value) return [];
    
    // Solo Mode: If an item is focused, only show that one
    if (focusedId.value) {
        const item = allTreasures.value.find(t => t.id === focusedId.value);
        return item ? [item] : [];
    }

    let filtered = allTreasures.value.filter(t => 
        t.master_id === currentFolder.value?.id && 
        (t.category === currentCategory.value || (!t.category && currentCategory.value === 'major'))
    );
    filtered.sort((a,b) => sortDesc.value ? (b.record_date||'').localeCompare(a.record_date||'') : (a.record_date||'').localeCompare(b.record_date||''));
    return filtered;
});

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
    if (expandedPersonNames.value.has(name)) expandedPersonNames.value.delete(name);
    else expandedPersonNames.value.add(name);
};

const openAdd = () => {
    form.value = {
        master_id: currentFolder.value?.id || null,
        category: currentCategory.value || 'major',
        name: '',
        purpose: '',
        acquisition_method: '',
        remarks: '',
        record_date: '',
        obtained_date: '',
        status: '已求得',
        dharma_name_registries: []
    };
    addMode.value = 'single';
};

const toggleExpand = (id) => {
    if (expandedIds.value.has(id)) {
        expandedIds.value.delete(id);
        focusedId.value = null; // Clear focus when collapsed
        showItemDetails.value = false; // Reset sub-details
    } else {
        expandedIds.value.clear();
        expandedIds.value.add(id);
        focusedId.value = id;   // Set focus when expanded
        showItemDetails.value = false; // Initially hidden
    }
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

const saveSingle = async (data) => {
    if (!data.name?.trim()) {
        persistentToast.value = { msg: '✖ 請輸入法寶名稱', type: 'error' };
        return;
    }
    if (!data.master_id) {
        persistentToast.value = { msg: '✖ 請選擇仙師', type: 'error' };
        return;
    }

    isSaving.value = true;
    try {
        if (data.id) await axios.post(`/registries/${data.id}`, { ...data, _method: 'PATCH' });
        else await axios.post('/registries', data);
        addMode.value = null;
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
        await axios.post('/registries/batch', batchData);
        addMode.value = null;
        loadData();
        persistentToast.value = { msg: '✓ 批量新增成功', type: 'success' };
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
    if (editingIds.value.has(id)) {
        editingIds.value.delete(id);
    } else {
        editingIds.value.add(id);
        if (!expandedIds.value.has(id)) expandedIds.value.add(id);
    }
    openMenuId.value = null;
};

const deleteItem = async (id) => {
    try {
        await axios.delete(`/registries/${id}`);
        persistentToast.value = { msg: '✓ 已成功刪除資料', type: 'success' };
        setTimeout(() => { persistentToast.value = null; }, 2000);
        
        // Reset states
        deleteConfirmId.value = null;
        focusedId.value = null;
        expandedIds.value.clear();
        openMenuId.value = null;
        
        await loadData();
    } catch (e) {
        console.error(e);
        const errorMsg = e.response?.data?.error || '刪除失敗';
        persistentToast.value = { msg: '✖ ' + errorMsg, type: 'error' };
        setTimeout(() => { persistentToast.value = null; }, 2000);
    }
};

const toggleMenu = (id) => openMenuId.value = openMenuId.value === id ? null : id;
const toggleSort = () => sortDesc.value = !sortDesc.value;
const formatDate = (d) => d ? new Date(d).toLocaleDateString('zh-TW') : '-';

const copyToLine = (item) => {
    // Collect records from either the saved state or the active edit state
    const currentRegistries = item.dharma_name_registries || [];
    const dnrText = currentRegistries
        .filter(r => r.obtained_date)
        .map(r => {
            const dn = dharmaNames.value.find(d => d.id === r.dharma_name_id);
            const name = dn?.name || r.custom_name || '未知人員';
            return `● ${name} | ${r.obtained_date.toString().replace(/-/g, '/')}${r.remarks ? ' | ' + r.remarks : ''}`;
        }).join('\n');
    
    const finalRecordsText = dnrText || '尚無紀錄';
    const text = `【${item.name}】\n用意：${item.purpose || '-'}\n登記狀況：\n${finalRecordsText}`;
    navigator.clipboard.writeText(text);
    persistentToast.value = { msg: '✓ 已複製 LINE 格式', type: 'success' };
    setTimeout(() => persistentToast.value = null, 1500);
    openMenuId.value = null;
};

const downloadItemData = (item) => {
    const headers = ['法號', '登記日期', '備註'];
    const rows = dharmaNames.value.map(dn => {
        const r = item.dharma_name_registries?.find(reg => reg.dharma_name_id === dn.id);
        return [dn.name, r?.obtained_date || '', r?.remarks || ''].join(',');
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
            return `● ${name} | ${r.obtained_date.toString().replace(/-/g, '/')}${r.remarks ? ' | ' + r.remarks : ''}`;
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
            return `● ${name} | ${r.obtained_date.toString().replace(/-/g, '/')}${r.remarks ? ' | ' + r.remarks : ''}`;
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
        dharmaNames.value.forEach(dn => {
            const key = `${item.id}-${dn.id}`;
            const data = editMap.value[key];
            if (data && (data.obtained_date || data.remarks || (data.related_personnel && data.related_personnel.length))) {
                registries.push({
                    dharma_name_id: dn.id,
                    obtained_date: data.obtained_date ? data.obtained_date.replace(/\//g, '-') : null,
                    remarks: data.remarks || '',
                    related_personnel: data.related_personnel || []
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
        
        editingIds.value.delete(item.id);
        
        persistentToast.value = { msg: '✓ 儲存成功', type: 'success' };
        setTimeout(() => { if (persistentToast.value?.type === 'success') persistentToast.value = null; }, 2000);
        await loadData();
        // Keep expanded but close edit mode if needed
        openMenuId.value = null;
    } catch (e) {
        console.error(e);
        const errorMsg = e.response?.data?.error || '儲存失敗';
        persistentToast.value = { msg: '✖ ' + errorMsg, type: 'error' };
    } finally {
        isSaving.value = false;
    }
};
</script>

<style scoped>
.animate-fade-in { animation: fadeIn 0.3s ease-out; }
@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
</style>
