<template>
    <div class="bg-white h-[100dvh] flex flex-col relative overflow-hidden text-slate-900">
        <!-- Header (Sub-levels) -->
        <div v-if="currentFolder || currentCategory" class="border-b border-slate-300 flex items-center bg-white sticky top-0 z-[110]" style="padding: 15px; min-height: 60px;">
            <button @click="handleBack" class="text-slate-400 p-3 mr-1 active:scale-90 transition-transform shrink-0">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" /></svg>
            </button>
            <div class="flex-1 flex flex-col justify-center min-w-0">
                <div class="text-[22px] font-black leading-none font-outfit uppercase tracking-wider" style="color: rgb(220, 20, 40);">法寶登記專區</div>
                <div v-if="currentFolder" class="text-[15px] font-bold truncate mt-1 font-outfit" style="color: rgb(220, 20, 40);">
                    {{ currentCategory === 'major' ? '重大皇恩登記簿' : '其他皇恩登記簿' }}-{{ currentFolder.name }}
                </div>
            </div>

            <div v-if="currentFolder && !focusedId" class="flex flex-col items-end space-y-1 shrink-0 ml-2">
                <button v-if="!reorderMode" @click="toggleSort" class="px-2 py-1 text-[11px] text-slate-400 bg-slate-50 border border-slate-100 rounded-lg active:scale-95 transition-all font-black whitespace-nowrap">
                    {{ sortDesc ? '新→舊' : '舊→新' }}
                </button>
                <button @click="reorderMode = !reorderMode" 
                        :class="reorderMode ? 'bg-white border-green-500 text-green-600 ring-2 ring-green-100' : 'bg-indigo-50 border-indigo-100 text-indigo-500'"
                        class="px-3 py-1.5 text-[13px] border rounded-xl active:scale-95 transition-all font-black whitespace-nowrap shadow-sm">
                    {{ reorderMode ? '確認排序' : '修改排序' }}
                </button>
            </div>
        </div>

        <!-- Main Scrollable Area -->
        <div class="flex-1 overflow-y-auto custom-scrollbar" style="padding-bottom: 150px;">
            

            <!-- Category and Master Selection -->
            <div v-if="!currentFolder && !addMode" class="min-h-screen bg-white flex flex-col items-center">
                <div class="w-full px-4 py-8 flex items-center bg-white border-b border-slate-50 relative min-h-[80px]">
                    <button @click="$emit('goHome')" class="text-slate-400 p-4 active:scale-90 transition-transform z-10 shrink-0">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" /></svg>
                    </button>
                    <div class="flex-1">
                        <h1 class="text-[30px] font-black text-red-600 tracking-tight text-center uppercase tracking-widest leading-tight">
                            {{ currentCategory ? (currentCategory === 'major' ? '重大皇恩登記簿' : '其他皇恩登記簿') : '法寶登記專區' }}
                            <br v-if="currentCategory">
                            <span v-if="currentCategory && currentFolder" class="text-[17px] text-slate-400">- {{ currentFolder.name }} -</span>
                        </h1>
                    </div>
                </div>

                <!-- Root Categories -->
                <div v-if="!currentCategory" class="flex flex-col items-center space-y-6 mt-6 pb-20 w-full">
                    <button @click="currentCategory = 'major'" class="flex flex-col items-center justify-center bg-white active:scale-95 rounded-[24px] border-2 border-yellow-400 p-3 w-[220px] h-[220px] relative transition-all shadow-sm">
                        <div class="mb-2">
                            <svg class="w-[120px] h-[120px] drop-shadow-md" viewBox="0 0 64 64" fill="none">
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
                        <div class="text-[26px] font-black text-red-700 leading-tight drop-shadow-sm text-center">重大皇恩<br>登記簿</div>
                    </button>

                    <button @click="currentCategory = 'other'" class="flex flex-col items-center justify-center bg-white active:scale-95 rounded-[24px] border-2 border-red-600 p-3 w-[220px] h-[220px] relative transition-all shadow-sm">
                        <div class="mb-2">
                            <svg class="w-[120px] h-[120px] drop-shadow-md" viewBox="0 0 64 64" fill="none">
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
                        <div class="text-[26px] font-black text-red-700 leading-tight drop-shadow-sm text-center">其他皇恩<br>登記簿</div>
                    </button>

                </div>

                <!-- Masters Grid -->
                <div v-else class="grid grid-cols-2 gap-[20px] p-2 place-items-center">
                    <button v-for="folder in folders" :key="folder.id" 
                        @click="currentFolder = folder"
                        :class="[
                            'flex flex-col items-center justify-center transition-all active:scale-95 rounded-2xl border-2 group p-2 w-[144px] h-[144px] relative bg-white shadow-sm',
                            currentCategory === 'major' ? 'border-yellow-400' : 'border-red-600'
                        ]">
                        
                        <div class="relative mb-1">
                             <svg class="w-[88px] h-[88px] transition-transform group-hover:scale-110 drop-shadow-md" viewBox="0 0 64 64" fill="none">
                                <defs>
                                <linearGradient id="folderGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                                    <stop v-if="currentCategory === 'major'" offset="0%" style="stop-color:rgb(255, 230, 0);stop-opacity:1" />
                                    <stop v-if="currentCategory === 'major'" offset="50%" style="stop-color:rgb(255, 200, 0);stop-opacity:1" />
                                    <stop v-if="currentCategory === 'major'" offset="100%" style="stop-color:rgb(255, 170, 0);stop-opacity:1" />
                                    <stop v-if="currentCategory !== 'major'" offset="0%" style="stop-color:rgb(220, 20, 40);stop-opacity:1" />
                                    <stop v-if="currentCategory !== 'major'" offset="50%" style="stop-color:rgb(190, 10, 30);stop-opacity:1" />
                                    <stop v-if="currentCategory !== 'major'" offset="100%" style="stop-color:rgb(160, 0, 20);stop-opacity:1" />
                                </linearGradient>
                                </defs>
                                <path d="M4 14C4 11.7909 5.79086 10 8 10H24.5L30 16H56C58.2091 16 60 17.7909 60 20V50C60 52.2091 58.2091 54 56 54H8C5.79086 54 4 52.2091 4 50V14Z" fill="url(#folderGrad)" />
                                <path d="M4 22C4 19.7909 5.79086 18 8 18H56C58.2091 18 60 19.7909 60 22V50C60 52.2091 58.2091 54 56 54H8C5.79086 54 4 52.2091 4 50V22Z" fill="url(#folderGrad)" stroke="rgba(255,255,255,0.6)" stroke-width="1"/>
                            </svg>
                        </div>
                        <div class="text-center px-1">
                            <div :class="[
                                'font-black tracking-tight leading-tight text-center transition-all text-[24px]',
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
            <div v-else :class="['px-0 bg-white transition-all duration-300', focusedId ? 'fixed inset-0 z-[100] pt-[60px] overflow-y-auto' : '']">
                <div :style="focusedId ? 'padding: 0px 0px 120px 0px;' : 'padding: 0px 8px 10px 8px;'" class="mt-0">
                    <div v-if="loading" class="text-center py-10">
                        <div class="inline-block animate-spin rounded-full h-10 w-10 border-4 border-slate-100 border-t-indigo-600 mb-4"></div>
                        <p class="text-xs font-black font-outfit uppercase tracking-widest text-slate-400">載入中...</p>
                    </div>

                    <template v-else>
                        <!-- Search Bar -->
                        <div v-if="showSearch" class="mb-4 sticky top-0 z-40 bg-white/80 backdrop-blur-md pb-2 px-1 animate-fade-in">
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

                        <div v-for="(item, idx) in filteredTreasures" :key="item.id" 
                             :class="[
                                 'bg-white p-4 mb-4 border-b border-slate-50 relative transition-all cursor-pointer hover:shadow-md active:bg-slate-50 flex items-start',
                                 focusedId === item.id ? 'min-h-[calc(100vh-100px)] border-transparent shadow-none !mb-0 !rounded-none -mx-4 z-[60]' : '',
                                 openMenuId === item.id ? 'z-[50]' : 'z-0'
                             ]">
                            
                            <!-- Sequence Number / Reorder Input -->
                            <div class="mr-4 shrink-0 flex items-center justify-center pt-1">
                                <div v-if="!reorderMode" class="w-8 h-8 bg-slate-100 rounded-xl flex items-center justify-center text-[14px] font-black text-slate-500">
                                    {{ idx + 1 }}
                                </div>
                                <input v-else 
                                       type="number" 
                                       :value="idx + 1"
                                       @click.stop
                                       @change="e => handleReorder(item, e.target.value)"
                                       class="w-10 h-9 bg-blue-50 border-2 border-blue-200 rounded-xl text-center text-[15px] font-black text-blue-600 focus:ring-2 focus:ring-blue-400 outline-none">
                            </div>

                            <div class="flex-1 min-w-0 pr-10">
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
                                     class="absolute right-0 mt-2 w-48 bg-white opacity-100 border border-slate-100 rounded-2xl shadow-2xl z-[110] py-2 ring-1 ring-black ring-opacity-5 animate-fade-in divide-y divide-slate-50 overflow-hidden">
                                    <button @click.stop="toggleExpand(item.id); openMenuId = null" 
                                            class="w-full text-left px-4 py-3 text-[16px] font-bold text-slate-700 hover:bg-slate-50 flex items-center transition-colors">
                                        <svg class="w-5 h-5 mr-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path v-if="expandedIds.has(item.id)" d="M19 15l-7-7-7 7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
                                            <path v-else d="M5 9l7 7 7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        {{ expandedIds.has(item.id) ? '收起詳情' : '展開詳情' }}
                                    </button>
                                    <button @click.stop="openAndEdit(item.id); openMenuId = null" 
                                            class="w-full text-left px-4 py-3 text-[16px] font-bold text-slate-700 hover:bg-slate-50 flex items-center transition-colors">
                                        <svg class="w-5 h-5 mr-3 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" /></svg>
                                        修改資料
                                    </button>
                                    <button @click.stop="copyToLine(item); openMenuId = null" 
                                            class="w-full text-left px-4 py-3 text-[16px] font-bold text-slate-700 hover:bg-slate-50 flex items-center transition-colors">
                                        <svg class="w-5 h-5 mr-3 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" /></svg>
                                        貼 LINE
                                    </button>
                                    <button @click.stop="downloadItemData(item); openMenuId = null" 
                                            class="w-full text-left px-4 py-3 text-[16px] font-bold text-slate-700 hover:bg-slate-50 flex items-center transition-colors">
                                        <svg class="w-5 h-5 mr-3 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" /></svg>
                                        下載資料
                                    </button>
                                    <button @click.stop="deleteConfirmId = item.id; openMenuId = null" 
                                            class="w-full text-left px-4 py-3 text-[16px] font-bold text-red-600 hover:bg-red-50 flex items-center transition-colors">
                                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" /></svg>
                                        刪除資料
                                    </button>
                                </div>
                            </div>

                            <!-- Card Header (Toggle Expansion) -->
                            <div v-if="!expandedIds.has(item.id)" @click="toggleExpand(item.id)" class="space-y-2">
                                <div class="flex items-center justify-between pr-12 mt-[10px]">
                                    <div class="text-[14px] text-slate-400 font-bold font-outfit uppercase tracking-widest">{{ item.record_date?.replace(/-/g, '/') || '-' }}</div>
                                </div>

                                <div class="flex items-center justify-between group/title pr-12">
                                    <div class="flex flex-col">
                                        <div class="text-[17px] font-normal text-slate-900 leading-tight truncate font-outfit flex items-center">
                                            {{ item.name }}
                                        </div>
                                    </div>
                                    <div class="ml-2 text-slate-300 group-hover/title:text-indigo-400 transition-colors">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path d="M19 9l-7 7-7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                                

                             


                                    <div v-if="expandedIds.has(item.id)" class="mt-6 pt-6 border-t border-slate-50">
                                        <!-- Detailed Record View -->
                                        <div v-if="!editingIds.has(item.id)" class="space-y-5 px-1 mb-8">
                                            <div class="space-y-1">
                                                <label class="text-[11px] text-slate-400 uppercase tracking-widest font-bold font-outfit">日期</label>
                                                <div class="text-[17px] font-bold text-slate-900 font-outfit">{{ item.record_date?.replace(/-/g, '/') }}</div>
                                            </div>
                                            <div class="space-y-1">
                                                <label class="text-[11px] text-slate-400 uppercase tracking-widest font-bold font-outfit">法寶名稱</label>
                                                <div class="text-[17px] font-bold text-slate-900 font-outfit">{{ item.name }}</div>
                                            </div>
                                            <div v-if="item.purpose" class="space-y-1">
                                                <label class="text-[11px] text-slate-400 uppercase tracking-widest font-bold font-outfit">法寶用意</label>
                                                <div class="text-[17px] font-bold text-slate-900 leading-relaxed font-outfit">{{ item.purpose }}</div>
                                            </div>
                                            <div v-if="item.acquisition_method" class="space-y-1">
                                                <label class="text-[11px] text-slate-400 uppercase tracking-widest font-bold font-outfit">求寶方式</label>
                                                <div class="text-[17px] font-bold text-slate-900 font-outfit">{{ item.acquisition_method }}</div>
                                            </div>
                                            <div v-if="item.remarks" class="space-y-1">
                                                <label class="text-[11px] text-slate-400 uppercase tracking-widest font-bold font-outfit">備註</label>
                                                <div @click.stop="openRemarks(item.remarks)" class="text-[17px] font-bold text-slate-900 font-outfit cursor-pointer hover:text-indigo-600 transition-colors">{{ item.remarks }}</div>
                                            </div>
                                        </div>

                                        <!-- Edit Mode Fields -->
                                        <div v-if="editingIds.has(item.id)" class="p-4 bg-indigo-50/30 rounded-2xl border border-indigo-100/50 space-y-5 shadow-inner mb-8">
                                            <div class="grid grid-cols-1 gap-4">
                                                <div class="space-y-1">
                                                    <label class="text-[11px] text-slate-400 uppercase tracking-widest font-black font-outfit">日期</label>
                                                    <div @click="activePicker = { id: item.id, field: 'record_date', title: '修改日期' }" 
                                                        class="w-full bg-white border border-slate-200 rounded-xl px-3 h-[42px] flex items-center justify-between cursor-pointer">
                                                        <span :class="editMap[item.id].record_date ? 'text-black' : 'text-slate-300'" class="text-[17px] font-black font-outfit uppercase">
                                                            {{ editMap[item.id].record_date || '未設定' }}
                                                        </span>
                                                        <svg class="w-5 h-5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                                    </div>
                                                </div>
                                                <div class="space-y-1">
                                                    <label class="text-[11px] text-slate-400 uppercase tracking-widest font-black font-outfit">法寶名稱</label>
                                                    <input type="text" v-model="editMap[item.id].name" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-[17px] font-black outline-none font-outfit">
                                                </div>
                                                <div class="space-y-1">
                                                    <label class="text-[11px] text-slate-400 uppercase tracking-widest font-black font-outfit">法寶用意</label>
                                                    <input type="text" v-model="editMap[item.id].purpose" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-[17px] font-black outline-none font-outfit">
                                                </div>
                                                <div class="space-y-1">
                                                    <label class="text-[11px] text-slate-400 uppercase tracking-widest font-black font-outfit">求寶方式</label>
                                                    <input type="text" v-model="editMap[item.id].acquisition_method" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-[17px] font-black outline-none font-outfit">
                                                </div>
                                                <div class="space-y-1">
                                                    <label class="text-[11px] text-slate-400 uppercase tracking-widest font-black font-outfit">備註</label>
                                                    <input type="text" v-model="editMap[item.id].remarks" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-[17px] font-black outline-none font-outfit">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="space-y-3 pt-4 border-t border-slate-50 mt-4">
                                            <template v-if="currentCategory === 'major'">
                                                <div class="overflow-x-auto rounded-xl border-y border-slate-100 shadow-sm mb-20 -mx-4 md:-mx-4 bg-white">
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
                                                                <td class="border-b border-slate-50 px-3 py-2 font-black text-black whitespace-nowrap bg-slate-50/20 font-outfit">{{ dn.name }}</td>
                                                                <td class="border-b border-slate-50 p-0 text-black">
                                                                    <div class="flex items-center px-3 py-1 h-[42px] justify-between group/date-cell" 
                                                                        :class="editingIds.has(item.id) ? 'cursor-pointer hover:bg-indigo-50/50' : ''"
                                                                        @click="editingIds.has(item.id) ? (activePicker = { id: item.id + '-' + dn.id, field: 'obtained_date', title: dn.name }) : null">
                                                                        <span :class="editMap[item.id + '-' + dn.id].obtained_date ? 'text-black' : 'text-slate-200'" class="text-[15px] font-black font-outfit">
                                                                            {{ editMap[item.id + '-' + dn.id].obtained_date || (editingIds.has(item.id) ? '----/--/--' : '-') }}
                                                                        </span>
                                                                    </div>
                                                                </td>
                                                                <td class="border-b border-slate-50 p-0 text-black">
                                                                    <div @click="editingIds.has(item.id) ? (currentRemarksKey = item.id + '-' + dn.id, showRemarksModal = true) : (editMap[item.id + '-' + dn.id]?.remarks ? openRemarks(editMap[item.id + '-' + dn.id].remarks) : null)" 
                                                                        class="w-full h-[42px] px-3 flex items-center justify-center transition-colors">
                                                                        <span v-if="editingIds.has(item.id)" class="text-[15px] font-black text-indigo-400">...</span>
                                                                        <span v-else-if="editMap[item.id + '-' + dn.id]?.remarks" class="text-[18px] text-amber-500 animate-pulse">●</span>
                                                                        <span v-else class="text-[15px] text-slate-200">-</span>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </template>
                                            <template v-else>
                                                <div class="overflow-x-auto border-y border-slate-100 shadow-sm mb-20 -mx-4 bg-white">
                                                    <table class="w-full border-collapse bg-white text-[16px]">
                                                        <thead>
                                                            <tr class="bg-amber-50/50 text-slate-900 font-outfit">
                                                                <th class="border-b border-slate-100 px-3 py-2.5 text-left font-black w-[120px] whitespace-nowrap">日期</th>
                                                                <th class="border-b border-slate-100 px-3 py-2.5 text-left font-black w-[80px] whitespace-nowrap">法號</th>
                                                                <th class="border-b border-slate-100 px-3 py-2.5 text-left font-black">備註</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr v-for="dnr in getSortedRegistries(item)" :key="dnr.id" class="hover:bg-slate-50 transition-colors">
                                                                <td class="border-b border-slate-50 p-0 text-black">
                                                                    <div class="flex items-center px-3 py-1 h-[42px] justify-between" 
                                                                        @click="editingIds.has(item.id) ? (activePicker = { id: item.id + '-' + dnr.dharma_name_id, field: 'obtained_date', title: dnr.dharma_name?.name || dnr.custom_name }) : null">
                                                                        <span class="text-[15px] font-bold font-outfit">{{ editMap[item.id + '-' + dnr.dharma_name_id]?.obtained_date || dnr.obtained_date?.replace(/-/g, '/') || '-' }}</span>
                                                                    </div>
                                                                </td>
                                                                <td class="border-b border-slate-50 px-3 py-2 font-black text-black whitespace-nowrap bg-slate-50/20 font-outfit">{{ getDharmaNameText(dnr) }}</td>
                                                                <td class="border-b border-slate-50 p-0 text-black">
                                                                    <div @click="editingIds.has(item.id) ? (currentRemarksKey = item.id + '-' + dnr.dharma_name_id, showRemarksModal = true) : (dnr.remarks ? openRemarks(dnr.remarks) : null)" 
                                                                        class="w-full h-[42px] px-3 flex items-center justify-center">
                                                                        <span v-if="editingIds.has(item.id)" class="text-[15px] font-black text-indigo-400">...</span>
                                                                        <span v-else-if="dnr.remarks" class="text-[18px] text-amber-500 animate-pulse">●</span>
                                                                        <span v-else class="text-[15px] text-slate-200">-</span>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            <tr v-if="editingIds.has(item.id)" class="bg-indigo-50/20 border-t-2 border-indigo-100/50">
                                                                <td class="p-0 border-b border-indigo-100">
                                                                    <div @click="activePicker = { id: item.id + '-new', field: 'obtained_date', title: '設定新加入日期' }" class="w-full h-[46px] px-3 flex items-center justify-between cursor-pointer">
                                                                        <span class="text-[14px] font-black text-indigo-600">{{ editMap[item.id + '-new']?.obtained_date || '設定日期' }}</span>
                                                                    </div>
                                                                </td>
                                                                <td class="p-2 border-b border-indigo-100">
                                                                    <select @change="addPersonRowFromSelect(item.id, $event)" class="w-full h-9 rounded-xl border border-indigo-200">
                                                                        <option value="">＋ 點此加入人員</option>
                                                                        <option v-for="dn in dharmaNames" :key="dn.id" :value="dn.id">{{ dn.name }}</option>
                                                                    </select>
                                                                </td>
                                                                <td class="p-0 border-b border-indigo-100">
                                                                    <div @click="currentRemarksKey = item.id + '-new', showRemarksModal = true" class="w-full h-[46px] px-3 flex items-center text-[14px] text-slate-300 font-bold truncate">
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
                                        <div v-if="editingIds.has(item.id)" class="fixed bottom-0 left-0 right-0 p-4 bg-white/80 backdrop-blur-md border-t border-slate-100 z-[120] flex justify-center shadow-lg">
                                            <button @click="saveItemInPlace(item)" class="w-full max-w-md py-4 bg-indigo-600 text-white rounded-2xl font-black text-[18px]">
                                                保存修改
                                            </button>
                                        </div>
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
            @action="showAddMenu = true"
            @search="showSearch = !showSearch"
            @more="showExportMenu = !showExportMenu"
        />

        <!-- Add Action Menu Overlay -->
        <div v-if="showAddMenu" class="fixed inset-0 z-[150] flex items-end justify-center p-4 bg-slate-900/40 backdrop-blur-[2px] animate-fade-in" @click="showAddMenu = false">
            <div class="bg-white w-full max-w-[320px] rounded-[24px] overflow-hidden shadow-2xl animate-pop-in mb-20" @click.stop>
                <div class="p-1 space-y-0.5">
                    <button @click="openAdd('single'); showAddMenu = false" class="w-full p-2.5 flex items-center space-x-3 hover:bg-slate-50 transition-colors rounded-xl group">
                        <div class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0 group-active:scale-90 transition-transform">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </div>
                        <div class="flex flex-col text-left">
                            <span class="text-[15px] font-black font-outfit text-slate-800">逐筆新增</span>
                            <span class="text-[11px] text-slate-400 font-medium font-outfit">輸入單筆法寶登記紀錄</span>
                        </div>
                    </button>

                    <button @click="openAdd('batch'); showAddMenu = false" class="w-full p-2.5 flex items-center space-x-3 hover:bg-slate-50 transition-colors rounded-xl group border-t border-slate-50">
                        <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0 group-active:scale-90 transition-transform">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </div>
                        <div class="flex flex-col text-left">
                            <span class="text-[15px] font-black font-outfit text-slate-800">多筆新增</span>
                            <span class="text-[11px] text-slate-400 font-medium font-outfit">貼上文字清單批次匯入</span>
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
                            <span class="text-[15px] font-black font-outfit text-slate-800">匯出 Excel</span>
                            <span class="text-[11px] text-slate-400 font-medium font-outfit">下載法寶登記報表檔案</span>
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
             <div class="bg-white p-6 rounded-[32px] shadow-2xl border border-slate-100 text-center animate-fade-in relative">
                 <button @click="persistentToast = null" class="absolute top-4 right-4 text-slate-300 hover:text-slate-500 transition-colors">
                     <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                 </button>
                 <p class="text-[17px] font-black text-slate-800 font-outfit leading-relaxed pt-2">{{ persistentToast.msg }}</p>
                 <div v-if="persistentToast.type === 'confirm'" class="mt-6 flex space-x-3">
                     <button v-for="a in persistentToast.actions" @click="a.handler" class="flex-1 py-3.5 rounded-2xl bg-red-50 text-red-600 font-black text-[14px] uppercase tracking-widest font-outfit active:scale-95 transition-all">{{ a.label }}</button>
                     <button @click="persistentToast = null" class="flex-1 py-3.5 rounded-2xl bg-slate-50 text-slate-400 font-black text-[14px] uppercase tracking-widest font-outfit active:scale-95 transition-all">取消</button>
                 </div>
                 <div v-else class="mt-6">
                     <button @click="persistentToast = null" class="w-full py-3.5 rounded-2xl bg-indigo-50 text-indigo-600 font-black text-[14px] uppercase tracking-widest font-outfit active:scale-95 transition-all">知道了</button>
                 </div>
             </div>
        </div>
        
        <!-- Delete Confirmation Overlay -->
        <div v-if="deleteConfirmId" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-md animate-fade-in">
            <div class="bg-white w-full max-w-sm rounded-[32px] p-8 shadow-2xl animate-pop-in">
                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" /></svg>
                </div>
                <h3 class="text-[22px] font-black text-center mb-2 font-outfit text-slate-800">確定要刪除嗎？</h3>
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
                    <h3 class="text-[20px] font-black font-outfit text-slate-800">詳細備註內容</h3>
                    <button @click="showRemarksModal = false" class="text-slate-400 hover:text-slate-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" /></svg>
                    </button>
                </div>
                <div class="flex-1 overflow-y-auto p-4 rounded-2xl border border-slate-50">
                    <textarea v-if="currentRemarksKey" 
                        v-model="editMap[currentRemarksKey].remarks"
                        class="w-full h-full bg-transparent text-[17px] font-black font-outfit text-slate-800 leading-relaxed outline-none focus:ring-0 border-none resize-none"
                        placeholder="輸入備註內容..."></textarea>
                    <div v-else class="text-[17px] font-black font-outfit text-slate-800 leading-relaxed whitespace-pre-wrap">
                        {{ activeRemarks }}
                    </div>
                </div>
                <div class="mt-6">
                    <button @click="showRemarksModal = false; currentRemarksKey = null" 
                        class="w-full py-4 bg-indigo-600 !text-white rounded-2xl font-black text-[18px] active:scale-95 transition-all font-outfit shadow-xl shadow-indigo-100">
                        確認並返回
                    </button>
                </div>
            </div>
        </div>
        <lucky-draw :show="showLuckyDraw" @close="showLuckyDraw = false" />
    </div>
</template>

<script setup>
import { ref, computed, onMounted, defineEmits, watch } from 'vue';
import axios from 'axios';
import MobileNavbar from './MobileNavbar.vue';
import RegistryAddForm from './RegistryAddForm.vue';
import CompactDatePicker from './CompactDatePicker.vue';
import LuckyDraw from './LuckyDraw.vue';

const emit = defineEmits(['goHome']);

// States
const currentCategory = ref(null); 
const currentFolder = ref(null);
const addMode = ref(null);
const showLuckyDraw = ref(false);
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
const searchQuery = ref('');
const showExportMenu = ref(false);
const deleteConfirmId = ref(null);
const activePicker = ref(null); // { id, field, title }
const showRemarksModal = ref(false);
const activeRemarks = ref('');
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

    // Apply Search Filter
    if (searchQuery.value?.trim()) {
        const q = searchQuery.value.toLowerCase().trim();
        filtered = filtered.filter(t => {
            const matchName = t.name?.toLowerCase().includes(q);
            const matchPurpose = t.purpose?.toLowerCase().includes(q);
            const matchRegistries = (t.dharma_name_registries || []).some(dnr => {
                const dnText = getDharmaNameText(dnr).toLowerCase();
                const remarkMatch = (dnr.remarks || '').toLowerCase().includes(q);
                return dnText.includes(q) || remarkMatch;
            });
            return matchName || matchPurpose || matchRegistries;
        });
    }

    filtered.sort((a,b) => {
        if (a.sort_order !== b.sort_order) return a.sort_order - b.sort_order;
        return sortDesc.value ? (b.record_date||'').localeCompare(a.record_date||'') : (a.record_date||'').localeCompare(b.record_date||'');
    });
    return filtered;
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
    if (expandedPersonNames.value.has(name)) expandedPersonNames.value.delete(name);
    else expandedPersonNames.value.add(name);
};

const openAdd = (mode = 'single') => {
    form.value = {
        master_id: currentFolder.value?.id || null,
        category: currentCategory.value || 'major',
        name: '',
        purpose: '',
        acquisition_method: '',
        remarks: '',
        record_date: new Date().toISOString().split('T')[0],
        obtained_date: '',
        status: '已求得',
        dharma_name_registries: []
    };
    addMode.value = mode;
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
        const text = batchData.input || '';
        const blocks = text.split(/\n\s*\n|\r\n\s*\r\n/).filter(b => b.trim());
        const masterNames = masters.value.map(m => m.name);
        const masterMap = {};
        masters.value.forEach(m => masterMap[m.name] = m.id);

        const nameAliasMap = {
            '金容': '靈果', '金涓': '靈慧', '金梅': '靈妙', '金蘭': '靈智', '金平': '靈平',
            '金瑞': '龍戰', '金耀': '龍勝', '金旭': '靈心', '金熙': '靈情', '金吉': '靈奇',
            '金祥': '靈傾', '金恩': '靈昡', '金鈺': '元續', '金穎': '赤峰'
        };
        const translateName = (raw) => nameAliasMap[raw] || raw;

        let rawRecords = [];

        blocks.forEach(block => {
            const lines = block.split('\n').map(l => l.trim()).filter(l => l);
            if (lines.length === 0) return;

            let blockMasterId = batchData.masterId || (currentFolder.value?.id);
            let blockRecipient = '';
            let blockDate = batchData.date || new Date().toISOString().split('T')[0];

            let pendingTreasureName = '';

            // Optimized Pass: Detect metadata and parse items simultaneously to support multiple date sessions
            for (let i = 0; i < lines.length; i++) {
                let line = lines[i];

                // A. Metadata Detection (Master, Recipient, Date)
                masterNames.forEach(mName => { if (line.includes(mName)) blockMasterId = masterMap[mName]; });
                if (line.includes('開示給')) {
                    const rMatch = line.split(/[：:]/)[1];
                    if (rMatch) blockRecipient = rMatch.trim();
                    continue;
                }

                // Date detection (Supporting ROC format)
                const rocFullMatch = line.match(/(\d{2,3})\/(\d{1,2})\/(\d{1,2})/);
                if (rocFullMatch) {
                    const year = parseInt(rocFullMatch[1]) + 1911;
                    blockDate = `${year}-${rocFullMatch[2].padStart(2,'0')}-${rocFullMatch[3].padStart(2,'0')}`;
                    continue; 
                } else {
                    const rocYMatch = line.match(/(\d{2,3})年/);
                    if (rocYMatch) {
                        blockDate = `${parseInt(rocYMatch[1]) + 1911}-${blockDate.split('-')[1] || '01'}-${blockDate.split('-')[2] || '01'}`;
                        continue;
                    }
                    const shortDMatch = line.match(/^(\d{1,2})\/(\d{1,2})$/);
                    if (shortDMatch) {
                        const currentYear = blockDate.split('-')[0];
                        blockDate = `${currentYear}-${shortDMatch[1].padStart(2,'0')}-${shortDMatch[2].padStart(2,'0')}`;
                        continue;
                    }
                }

                if (line.includes('完畢') || masterNames.some(m => line === m || line === m + '仙師')) continue;
                
                let treasureName = '';
                let recipients = [];
                let lineDate = blockDate;
                let lineRemarks = '';

                // Pattern A: "允求/賜降/賜予 森羅戒：靈果"
                const kwMatch = line.match(/(允求|賜降|得知|賜予|賜|法寶名稱|法寶內容)\s*(.*?)[：:](.*)/);
                if (kwMatch) {
                    treasureName = kwMatch[2].trim();
                    const content = kwMatch[3].trim();
                    if (content) {
                        recipients = content.split(/[，、, \s]+/).filter(n => n.trim());
                        pendingTreasureName = ''; // Reset if full line
                    } else {
                        pendingTreasureName = treasureName; // Set pending if no content
                        continue;
                    }
                } 
                // Pattern NEW: "求寶/求寶方式：..." -> Assign to previous record
                else if (line.match(/^(求寶|求寶方式)[：:]/)) {
                    const method = line.split(/[：:]/)[1]?.trim();
                    if (method && rawRecords.length > 0) {
                        rawRecords[rawRecords.length - 1].acquisition_method = method;
                    }
                    continue; 
                }
                // Pattern B: "靈果求得森羅戒"
                else if (line.includes('求得')) {
                    const parts = line.split('求得').map(p => p.trim());
                    if (parts.length >= 2) {
                        recipients = [parts[0]];
                        treasureName = parts[1];
                        pendingTreasureName = '';
                    }
                }
                // Pattern C: "森羅戒 | 靈果 | 113/11/24"
                else if (line.includes('|') || line.includes('│') || line.includes('\t')) {
                    const parts = line.split(/[|│\t]/).map(p => p.trim());
                    treasureName = parts[0];
                    if (parts[1]) recipients = [parts[1]];
                    if (parts[2]) lineDate = parts[2].replace(/\//g,'-');
                    if (parts[3]) lineRemarks = parts[3];
                    pendingTreasureName = '';
                }
                // Pattern D: "靈心、金振、金涓" (Pure recipients line if pending treasure exists)
                else if (pendingTreasureName) {
                    treasureName = pendingTreasureName;
                    recipients = line.split(/[，、, \s]+/).filter(n => n.trim());
                    pendingTreasureName = ''; // Done
                }
                // Pattern E: "森羅戒 靈果" (Space separated)
                else {
                    const segments = line.split(/[\s,，、]+/).filter(s => s.trim());
                    if (segments.length >= 2) {
                        treasureName = segments[0];
                        recipients = [segments[1]];
                    } else {
                        treasureName = line;
                        if (blockRecipient) recipients = [blockRecipient];
                    }
                }

                if (treasureName) {
                    treasureName = treasureName.replace(/[：:。！？]$/, '').trim();
                    const dnr = recipients.map(rawName => {
                        const translated = translateName(rawName);
                        // Handle relationship words: "元續之母" -> "元續"
                        const relMatch = translated.match(/^(.*?)(之[夫妻子女左右大小父母兄弟姊姐]|的[夫妻子女左右大小父母兄弟姊姐].*)$/);
                        if (relMatch) {
                            const dParts = lineDate.split('-');
                            const rocY = parseInt(dParts[0]) - 1911;
                            const dStr = `${rocY}/${dParts[1].padStart(2,'0')}/${dParts[2].padStart(2,'0')}`;
                            return { custom_name: relMatch[1], remarks: `${dStr}${translated}`, obtained_date: lineDate };
                        }
                        return { custom_name: translated, remarks: lineRemarks, obtained_date: lineDate };
                    });

                    rawRecords.push({
                        name: treasureName, master_id: blockMasterId,
                        category: batchData.category || currentCategory.value || 'major',
                        record_date: lineDate, 
                        acquisition_method: '',
                        purpose: '',
                        remarks: '',
                        dharma_name_registries: dnr
                    });
                }
            }
        });

        // --- MERGE STAGE ---
        const mergedMap = new Map();
        rawRecords.forEach(rec => {
            // Group ONLY by Name and Master (ignore date for merging records)
            // Trim name to prevent duplication due to trailing spaces
            const cleanName = rec.name.trim();
            const key = `${cleanName}-${rec.master_id}`;
            if (mergedMap.has(key)) {
                const existing = mergedMap.get(key);
                
                // Update the main record_date to the latest one found
                if (new Date(rec.record_date) > new Date(existing.record_date)) {
                    existing.record_date = rec.record_date;
                }

                // Merge text fields if new ones are found
                if (rec.acquisition_method && !existing.acquisition_method.includes(rec.acquisition_method)) {
                    existing.acquisition_method = existing.acquisition_method ? (existing.acquisition_method + '；' + rec.acquisition_method) : rec.acquisition_method;
                }
                if (rec.purpose && !existing.purpose.includes(rec.purpose)) {
                    existing.purpose = existing.purpose ? (existing.purpose + '；' + rec.purpose) : rec.purpose;
                }
                if (rec.remarks && !existing.remarks.includes(rec.remarks)) {
                    existing.remarks = existing.remarks ? (existing.remarks + '；' + rec.remarks) : rec.remarks;
                }
                
                // Merge registries (still deduplicating personnel)
                rec.dharma_name_registries.forEach(nr => {
                    const alreadyExists = existing.dharma_name_registries.some(er => {
                        return (er.custom_name === nr.custom_name && er.obtained_date === nr.obtained_date);
                    });
                    if (!alreadyExists) existing.dharma_name_registries.push(nr);
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
    if (editingIds.value.has(id)) {
        editingIds.value.delete(id);
    } else {
        editingIds.value.add(id);
        if (!expandedIds.value.has(id)) expandedIds.value.add(id);
    }
    openMenuId.value = null;
};

const openRemarks = (text) => {
    if (!text) return;
    activeRemarks.value = text;
    showRemarksModal.value = true;
};

const openCombinedRemarks = (item) => {
    const combined = item.dharma_name_registries
        .filter(r => r.remarks)
        .map(r => `【${r.dharma_name ? r.dharma_name.name : r.custom_name}】：${r.remarks}`)
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

const reorderMode = ref(false);

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
        
        // Handle all existing and potential registries
        // We use the item.dharma_name_registries as the source of truth for what exists
        // and editMap for the updates.
        
        // 1. Process standard dharma names (from master list)
        dharmaNames.value.forEach(dn => {
            const key = `${item.id}-${dn.id}`;
            const data = editMap.value[key];
            // If we have edit data OR there was already a record for this person
            const wasExisting = item.dharma_name_registries?.some(r => r.dharma_name_id === dn.id);
            
            if (data && (data.obtained_date || data.remarks || wasExisting)) {
                registries.push({
                    dharma_name_id: dn.id,
                    obtained_date: data.obtained_date ? data.obtained_date.replace(/\//g, '-') : (item.dharma_name_registries?.find(r => r.dharma_name_id === dn.id)?.obtained_date || null),
                    remarks: data.remarks || (item.dharma_name_registries?.find(r => r.dharma_name_id === dn.id)?.remarks || ''),
                    related_personnel: data.related_personnel || []
                });
            }
        });
        
        // 2. Process custom names (things added via text but not in system list)
        (item.dharma_name_registries || []).forEach(r => {
            if (!r.dharma_name_id && r.custom_name) {
                registries.push({
                    custom_name: r.custom_name,
                    obtained_date: r.obtained_date ? r.obtained_date.replace(/\//g, '-') : null,
                    remarks: r.remarks || ''
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
