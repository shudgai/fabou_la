<template>
    <div class="bg-white min-h-screen flex justify-center text-slate-900">
        <div class="bg-white min-h-screen relative w-full shadow-sm flex flex-col font-sans">
            <!-- Global Datalists -->
            <datalist id="item-name-list">
                <option v-for="name in uniqueTreasureNames" :key="name" :value="name">{{ name }}</option>
            </datalist>
            <datalist id="master-list-entry">
                <option value="老祖仙師" />
                <option value="元始仙師" />
                <option value="道祖仙師" />
                <option value="靈寶仙師" />
                <option value="父皇仙師" />
                <option value="太宰仙師" />
                <option value="太子" />
                <option value="閻王仙師" />
            </datalist>
            <datalist id="num-list">
                <option v-for="n in 9" :key="n" :value="n">{{ n }}</option>
            </datalist>
            <datalist id="unit-list">
                <option v-for="u in units" :key="u" :value="u">{{ u }}</option>
            </datalist>

            <datalist id="dharma-search-list">
                <option v-for="dn in dharmaNames" :key="'dn'+dn.id" :value="dn.name">{{ dn.name }}</option>
                <option v-for="g in groups" :key="'g'+g.id" :value="g.name">{{ g.name }}</option>
            </datalist>

            <!-- Header with Back Button (Persistent) -->
            <div v-if="currentFolder" class="border-b border-gray-100 flex items-center bg-white sticky top-0 z-[60] w-full" style="padding: 12px 10px 10px 10px;">
                <button @click="handleBack" class="text-slate-400 p-2 mr-2 active:scale-90 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" /></svg>
                </button>
                <div class="flex-1 flex flex-col items-center min-w-0">
                    <h2 class="text-[20px] font-bold font-outfit tracking-tight text-slate-900 truncate w-full text-center">
                        {{ addMode ? (editingId ? '修改紀錄' : ((currentFolder?.id === 0 || currentFolder?.id === '0') ? '父皇仙師每日開示新增紀錄' : (currentFolder?.name || '仙師') + '開示新增紀錄')) : (currentFolder?.id === 0 || currentFolder?.id === '0' ? '每日開示紀錄' : currentFolder?.name + '開示紀錄') }}
                    </h2>
                </div>
                <div class="p-2 ml-2 w-10"></div> <!-- Placeholder for balance -->
            </div>

            <!-- Level 1: Two Major Categories (Refined Aesthetic) -->
            <div v-if="!currentCategory && !currentFolder" class="min-h-screen bg-slate-50/30">
                <div class="px-6 py-10 text-center">
                    <h1 class="text-[24px] font-black text-slate-900 tracking-tight">父皇仙師開示專區</h1>
                </div>
                
                <div class="px-6 flex flex-col items-center space-y-12">
                    <!-- Category 1: Daily Teaching (Large Folder Style) -->
                    <button v-if="user?.permissions?.can_see_daily_teachings"
                        @click="currentFolder = folders_list.find(f => f.id === 0); currentCategory = 'daily'"
                        class="flex flex-col items-center justify-center p-4 active:scale-95 transition-all group relative">
                        <div class="relative mb-4">
                            <svg class="w-48 h-48 transition-transform group-hover:scale-105 drop-shadow-2xl" viewBox="0 0 64 64" fill="none">
                                <defs>
                                    <linearGradient id="dailyGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                                        <stop offset="0%" style="stop-color:rgb(255, 150, 150);stop-opacity:1" />
                                        <stop offset="50%" style="stop-color:rgb(248, 113, 113);stop-opacity:1" />
                                        <stop offset="100%" style="stop-color:rgb(220, 38, 38);stop-opacity:1" />
                                    </linearGradient>
                                </defs>
                                <path d="M4 14C4 11.7909 5.79086 10 8 10H24.5L30 16H56C58.2091 16 60 17.7909 60 20V50C60 52.2091 58.2091 54 56 54H8C5.79086 54 4 52.2091 4 50V14Z" fill="url(#dailyGrad)" opacity="0.8"/>
                                <path d="M4 22C4 19.7909 5.79086 18 8 18H56C58.2091 18 60 19.7909 60 22V50C60 52.2091 58.2091 54 56 54H8C5.79086 54 4 52.2091 4 50V22Z" fill="url(#dailyGrad)" stroke="rgba(255,255,255,0.4)" stroke-width="0.5"/>
                            </svg>
                            <!-- Badge -->
                            <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                                <svg class="w-12 h-12 text-white/40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </div>
                        </div>
                        <span class="text-[20px] font-black text-slate-900 tracking-tight leading-tight text-center">
                            父皇仙師<br>每日開示
                        </span>
                    </button>

                    <!-- Category 2: Master Teachings (Large Gold Folder Style) -->
                    <button 
                        @click="currentCategory = 'masters'"
                        class="flex flex-col items-center justify-center p-4 active:scale-95 transition-all group relative">
                        <div class="relative mb-4">
                            <svg class="w-48 h-48 transition-transform group-hover:scale-105 drop-shadow-2xl" viewBox="0 0 64 64" fill="none">
                                <defs>
                                    <linearGradient id="mastersGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                                        <stop offset="0%" style="stop-color:rgb(255, 230, 100);stop-opacity:1" />
                                        <stop offset="50%" style="stop-color:rgb(255, 215, 0);stop-opacity:1" />
                                        <stop offset="100%" style="stop-color:rgb(212, 175, 55);stop-opacity:1" />
                                    </linearGradient>
                                </defs>
                                <path d="M4 14C4 11.7909 5.79086 10 8 10H24.5L30 16H56C58.2091 16 60 17.7909 60 20V50C60 52.2091 58.2091 54 56 54H8C5.79086 54 4 52.2091 4 50V14Z" fill="url(#mastersGrad)" opacity="0.8"/>
                                <path d="M4 22C4 19.7909 5.79086 18 8 18H56C58.2091 18 60 19.7909 60 22V50C60 52.2091 58.2091 54 56 54H8C5.79086 54 4 52.2091 4 50V22Z" fill="url(#mastersGrad)" stroke="rgba(255,255,255,0.4)" stroke-width="0.5"/>
                            </svg>
                            <!-- Badge/Icon -->
                            <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                                <svg class="w-12 h-12 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </div>
                        </div>
                        <span class="text-[20px] font-black text-slate-900 tracking-tight leading-tight text-center">
                            父皇仙師<br>開示紀錄
                        </span>
                    </button>
                </div>

                <div class="mt-20 flex justify-center pb-32">
                    <button @click="$emit('goHome')" class="text-slate-300 hover:text-slate-500 transition-colors flex items-center space-x-2 p-4">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" /></svg>
                        <span class="text-[14px] font-black tracking-widest uppercase">返回主選單</span>
                    </button>
                </div>
            </div>

            <!-- Level 1.5: Subfolders for Masters -->
            <div v-else-if="currentCategory === 'masters' && !currentFolder" class="min-h-screen bg-white">
                <div class="px-6 py-4 flex items-center border-b border-slate-50 sticky top-0 bg-white z-10">
                    <button @click="currentCategory = null" class="p-2 text-slate-400 mr-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" /></svg>
                    </button>
                    <h2 class="text-[19px] font-black text-slate-900">父皇仙師開示資料夾</h2>
                </div>
                <div class="grid grid-cols-2 gap-[10px] p-4 place-items-center">
                    <button v-for="(folder, idx) in filteredFolders" :key="folder.id" 
                        @click="currentFolder = folder"
                        class="flex flex-col items-center justify-center bg-transparent transition-all active:scale-95 border border-slate-100 rounded-xl group p-2 w-[120px] h-[135px] relative">
                        <div class="relative mb-3">
                            <svg class="w-20 h-20 transition-transform group-hover:scale-110 drop-shadow-md" viewBox="0 0 64 64" fill="none">
                                <defs>
                                    <linearGradient :id="'fGrad' + idx" x1="0%" y1="0%" x2="100%" y2="100%">
                                        <stop offset="0%" style="stop-color:rgb(255, 230, 100);stop-opacity:1" />
                                        <stop offset="50%" style="stop-color:rgb(255, 215, 0);stop-opacity:1" />
                                        <stop offset="100%" style="stop-color:rgb(212, 175, 55);stop-opacity:1" />
                                    </linearGradient>
                                </defs>
                                <path d="M4 14C4 11.7909 5.79086 10 8 10H24.5L30 16H56C58.2091 16 60 17.7909 60 20V50C60 52.2091 58.2091 54 56 54H8C5.79086 54 4 52.2091 4 50V14Z" :fill="'url(#fGrad' + idx + ')'" opacity="0.8"/>
                                <path d="M4 22C4 19.7909 5.79086 18 8 18H56C58.2091 18 60 19.7909 60 22V50C60 52.2091 58.2091 54 56 54H8C5.79086 54 4 52.2091 4 50V22Z" :fill="'url(#fGrad' + idx + ')'" stroke="rgba(255,255,255,0.4)" stroke-width="0.5"/>
                            </svg>
                        </div>
                        <span class="mt-[-2px] text-[17px] font-bold text-slate-900 leading-tight text-center">
                            {{ folder.name }}
                        </span>
                    </button>
                </div>
            </div>

            <!-- Level 2: List & Add View -->
            <template v-else>
                <!-- Add View -->
                <div v-if="addMode" class="flex-1 flex flex-col bg-white">
                    <div class="bg-white w-full h-full relative flex flex-col">

                        


                        <div class="space-y-4 pb-32 flex-1 p-4">
                            <template v-if="entryTab === 'single'">
                                <div class="grid grid-cols-2 gap-3 pb-1 mt-1">
                                    <div class="space-y-0.5">
                                        <label class="text-[13px] text-slate-400 font-bold px-1 select-none">日期</label>
                                        <div class="border border-slate-100 rounded-2xl bg-slate-50/50 overflow-hidden px-4 flex items-center h-[56px]">
                                            <input v-model="form.date" type="date" class="w-full bg-transparent border-none text-[17px] text-slate-900 focus:ring-0 outline-none font-bold custom-date-input">
                                        </div>
                                    </div>
                                    <div class="space-y-0.5">
                                        <label class="text-[13px] text-slate-400 font-bold px-1 select-none">仙師</label>
                                        <div class="border border-slate-100 rounded-2xl bg-slate-50/50 overflow-hidden px-4 flex items-center h-[56px]">
                                            <input v-model="masterNameInput" 
                                                   list="master-list-entry" 
                                                   @change="resolveMasterId" 
                                                   placeholder="選擇或輸入..." 
                                                   class="w-full bg-transparent border-none text-[18px] text-slate-900 focus:ring-0 outline-none font-bold">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="grid grid-cols-12 gap-3 py-1.5 border-b border-blue-100/10">
                                    <div class="col-span-8 space-y-0.5">
                                        <div class="flex items-center justify-between">
                                            <label class="text-[13px] text-slate-400 font-bold px-1">對象 / 群組</label>
                                            <button @click.prevent="showDharmaPicker = true" class="text-[11px] bg-indigo-50 text-indigo-600 px-2 py-0.5 rounded-full font-bold active:scale-95 transition-all">選擇器</button>
                                        </div>
                                        <div class="border border-slate-100 rounded-2xl bg-slate-50/50 overflow-hidden px-4 flex items-center h-[56px]">
                                            <input type="text" 
                                                   @input="handleDharmaSearchInput" 
                                                   list="dharma-search-list"
                                                   v-model="dharmaSearchQuery"
                                                   placeholder="搜尋法號或群組..." 
                                                   class="w-full bg-transparent border-none text-[18px] text-slate-900 focus:ring-0 outline-none font-black placeholder:text-slate-300">
                                        </div>
                                    </div>
                                    <div class="col-span-4 space-y-0.5">
                                        <label class="text-[13px] text-slate-400 font-bold px-1">親友 / 信眾</label>
                                        <div class="border border-slate-100 rounded-2xl bg-slate-50/50 overflow-hidden h-[56px]">
                                            <textarea v-model="form.supplement" placeholder="備註資訊..." class="w-full bg-transparent border-none text-[17px] text-slate-900 focus:ring-0 outline-none p-3.5 resize-none font-bold leading-tight h-full" rows="1"></textarea>
                                        </div>
                                    </div>

                                    <!-- Full-Width Selected Personnel Tag Display (Utilizes full page width per user request) -->
                                    <div v-if="form.dharma_name_ids.length > 0" class="col-span-12 mt-1 animate-fade-in">
                                        <div class="flex flex-wrap gap-2.5 px-1 py-1">
                                            <div v-for="id in form.dharma_name_ids" :key="'sel'+id" 
                                                 class="bg-white border-2 border-slate-100 text-slate-900 px-3 py-1.5 rounded-2xl text-[15px] font-black flex items-center shadow-sm hover:border-indigo-200 transition-colors">
                                                <span class="mr-2 w-2 h-2 bg-indigo-400 rounded-full"></span>
                                                {{ getDharmaNameText(id) }}
                                                <button @click.prevent="toggleDharmaName(id)" class="ml-2 text-slate-300 hover:text-red-500 transition-colors">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="bg-blue-50/50 border-b border-blue-100/30 py-3 px-4 flex items-start">
                                    <label class="text-[13px] text-slate-400 w-10 text-left shrink-0 mt-2 font-bold uppercase tracking-wider">開示</label>
                                    <div class="flex-1 ml-1 border border-slate-100 rounded-2xl bg-white overflow-hidden min-h-[100px]">
                                        <textarea v-model="form.content" rows="4" @input="e => { e.target.style.height = 'auto'; e.target.style.height = e.target.scrollHeight + 'px' }" placeholder="輸入具體內容描述..." class="w-full bg-transparent border-none text-[18px] text-slate-900 focus:ring-0 outline-none p-4 resize-none overflow-hidden min-h-[90px] font-normal leading-relaxed"></textarea>
                                    </div>
                                </div>
                            </template>
                            <template v-else>
                                <div class="text-[14px] text-slate-400 font-bold uppercase tracking-[0.1em] mb-4">文字區塊錄入</div>
                                <div class="space-y-6">
                                    <div v-for="(record, index) in batchRecords" :key="index" 
                                         class="bg-white border border-slate-100 rounded-[28px] overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                                        
                                        <!-- Header: Identification -->
                                        <div class="bg-slate-50/50 px-6 py-4 flex items-center justify-between border-b border-slate-100">
                                            <div class="flex items-center space-x-3 flex-1">
                                                <span class="w-8 h-8 bg-[#4A3728] text-white rounded-full flex items-center justify-center font-black text-[14px]">{{ index + 1 }}</span>
                                                <div class="flex-1 relative">
                                                    <input type="text" 
                                                           v-model="record.dharmaSearchQuery"
                                                           @input="e => handleBlockDharmaInput(index, e.target.value)"
                                                           list="dharma-search-list"
                                                           placeholder="輸入對象或群組..." 
                                                           class="w-full bg-transparent border-none text-[17px] font-black text-slate-900 focus:ring-0 outline-none placeholder:text-slate-300">
                                                </div>
                                            </div>
                                            <button v-if="batchRecords.length > 1" @click.prevent="removeBatchBlock(index)" class="text-rose-400 hover:text-rose-600 transition-colors p-2">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                            </button>
                                        </div>

                                        <!-- Content Body -->
                                        <div class="p-4 space-y-4">
                                            <!-- Tag Display -->
                                            <div v-if="record.dharma_name_ids.length > 0" class="flex flex-wrap gap-2 px-1">
                                                <div v-for="id in record.dharma_name_ids" :key="'block'+index+id" 
                                                     class="bg-indigo-50 border border-indigo-100 text-indigo-700 px-3 py-1 rounded-xl text-[13px] font-bold flex items-center">
                                                    {{ getDharmaNameText(id) }}
                                                </div>
                                            </div>

                                            <div class="border border-slate-100 rounded-2xl bg-white overflow-hidden min-h-[140px] transition-all focus-within:ring-2 focus-within:ring-indigo-100">
                                                <textarea v-model="record.content" rows="6" 
                                                          placeholder="在此貼上開示內容..." 
                                                          class="w-full bg-transparent border-none text-[18px] text-slate-900 focus:ring-0 outline-none p-4 font-normal leading-relaxed"></textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Add Button -->
                                    <button @click.prevent="addBatchBlock" 
                                            class="w-full py-5 border-2 border-dashed border-slate-200 rounded-[28px] text-slate-400 font-bold hover:bg-slate-50 hover:border-indigo-300 hover:text-indigo-400 transition-all flex items-center justify-center group">
                                        <svg class="w-6 h-6 mr-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        新增開示區塊
                                    </button>
                                </div>
                            </template>

                            <!-- Floating Action Bar (Side-by-Side) -->
                            <div class="fixed bottom-0 left-0 right-0 p-4 pb-6 bg-white/80 backdrop-blur-md border-t border-slate-100 z-[300] flex items-center space-x-3">
                                <button v-if="currentFolder?.id === 0 || entryTab === 'single'"
                                    @click.prevent="itemsDetailMode = true" 
                                    class="flex-1 bg-slate-100 text-slate-600 rounded-2xl py-3 shadow-md border border-slate-200 active:scale-95 transition-all text-[15px] font-bold truncate">
                                    <span v-if="generateSummary(form)">降寶 ({{ generateSummary(form) }})</span>
                                    <span v-else>降寶詳情</span>
                                </button>
                                <button @click="saveItem" :disabled="saving" class="flex-1 bg-[#4A3728] text-white rounded-2xl py-3 active:scale-95 disabled:opacity-50 text-[16px] font-black tracking-widest shadow-lg shadow-slate-200/50">
                                    {{ saving ? '正在儲存...' : '確認存檔' }}
                                </button>
                            </div>
                            <!-- Space for Floating Button -->
                            <div class="h-32"></div>
                        </div>
                    </div>
                </div>

                <!-- Magic Items Detail View -->
                <div v-if="itemsDetailMode" class="fixed inset-0 z-[500] bg-[#f8fafc] flex flex-col overflow-y-auto animate-fade-in">
                    <!-- Modern Header -->
                    <div class="bg-white px-6 pt-4 pb-4 border-b border-slate-100 sticky top-0 z-[510] flex items-center justify-between shadow-sm">
                        <div class="flex items-center">
                            <button @click.prevent="itemsDetailMode = false" class="text-slate-400 mr-3 p-2 active:scale-90 transition-transform">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" /></svg>
                            </button>
                            <h3 class="text-[20px] font-bold text-slate-900 tracking-tight">降寶詳情錄入</h3>
                        </div>
                        <button @click.prevent="itemsDetailMode = false" class="bg-[#4A3728] text-white px-5 py-2 rounded-2xl text-[15px] font-bold shadow-md active:scale-95 transition-all">完成</button>
                    </div>

                    <div class="px-2.5 pt-0 pb-40 space-y-5">
                        <div class="bg-white rounded-[24px] border border-slate-50 p-2.5 shadow-sm">
                                <div class="text-[14px] font-bold text-slate-400 tracking-[0.1em] uppercase flex items-center justify-between px-1 mb-2">
                                    <div class="flex items-center">
                                        <span class="w-5 h-5 bg-[#4A3728] text-white rounded-full flex items-center justify-center text-[11px] font-black mr-2">1</span>
                                        錄入法寶
                                    </div>
                                    <button @click="addNewItemQuickly" class="text-red-500 text-[36px] font-light leading-none active:scale-95 transition-all"> + </button>
                                </div>
                                <div class="grid grid-cols-12 gap-2.5 items-end">
                                    <div class="col-span-9 space-y-1">
                                        <div class="text-[12px] text-slate-400 font-bold px-1 mb-0.5 text-left">法寶名稱</div>
                                        <div class="flex items-center">
                                            <div class="flex-1 border border-slate-100 rounded-xl bg-slate-50/20 overflow-hidden flex items-center transition-all h-[56px]">
                                                <input v-model="newItemName" list="item-name-list" 
                                                       class="w-full bg-transparent border-none px-4 text-[18px] font-bold text-slate-900 focus:ring-0 outline-none text-left" 
                                                       placeholder="法寶名稱...">
                                            </div>
                                        </div>
                                    </div>
                                    <div v-if="magicItemCategory === 'default'" class="col-span-3 space-y-1">
                                        <div class="text-[12px] text-slate-400 font-bold px-1 mb-0.5 text-center">天份</div>
                                        <div class="border border-slate-100 rounded-xl bg-slate-50/20 overflow-hidden flex items-center px-2 transition-all h-[46px]">
                                            <input v-model="newItemDays" list="num-list"
                                                   class="w-full bg-transparent border-none text-[18px] font-bold text-slate-900 focus:ring-0 outline-none text-center" 
                                                   placeholder="0">
                                            <span class="text-slate-400 font-bold ml-1 shrink-0 text-[13px]">天</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Main Item Remarks (Toggleable) - Hidden when adding details -->
                                <div v-if="!showAddDetails && !(['清煞', '專司靈療', '香灰', '執法', '由'].some(k => newItemName.includes(k)))" class="mt-2 text-left">
                                    <button @click="showMainRemarks = !showMainRemarks" class="text-[12px] font-bold text-slate-400 flex items-center px-1">
                                        {{ showMainRemarks ? '隱藏主備註' : '+ 加入主備註' }}
                                    </button>
                                    <div v-if="showMainRemarks" class="mt-2 border border-slate-100 rounded-2xl bg-slate-50/10 px-3 h-[72px] flex items-center animate-fade-in overflow-hidden">
                                        <textarea v-model="newItemMainRemarks" 
                                               class="w-full bg-transparent border-none text-[15px] font-bold text-slate-500 focus:ring-0 text-left resize-none py-3" 
                                               placeholder="主法寶備註..."></textarea>
                                    </div>
                                </div>

                                <!-- SPECIALIZED INPUTS (Immediate Category fields) -->
                                <div v-if="magicItemCategory !== 'default'" class="mt-2.5 space-y-2.5 animate-fade-in">
                                    <div v-if="magicItemCategory === '金丹'" class="grid grid-cols-3 gap-2">
                                        <div class="space-y-1">
                                            <div class="text-[11px] text-slate-400 font-bold px-2">吃</div>
                                            <div class="border border-slate-100 rounded-2xl bg-slate-50/20 flex items-center px-3 h-[46px]">
                                                <input v-model="newItemMainTimes" list="num-list" class="w-full bg-transparent border-none text-[16px] font-bold text-slate-900 focus:ring-0 outline-none text-center" placeholder="0">
                                                <span class="text-slate-400 font-bold ml-1 shrink-0 text-[13px]">粒</span>
                                            </div>
                                        </div>
                                        <div class="space-y-1">
                                            <div class="text-[11px] text-slate-400 font-bold px-2">洗</div>
                                            <div class="border border-slate-100 rounded-2xl bg-slate-50/20 flex items-center px-3 h-[46px]">
                                                <input v-model="newItemMainHours" list="num-list" class="w-full bg-transparent border-none text-[16px] font-bold text-slate-900 focus:ring-0 outline-none text-center" placeholder="0">
                                                <span class="text-slate-400 font-bold ml-1 shrink-0 text-[13px]">粒</span>
                                            </div>
                                        </div>
                                        <div class="space-y-1">
                                            <div class="text-[11px] text-slate-400 font-bold px-2">天份</div>
                                            <div class="border border-slate-100 rounded-2xl bg-slate-50/20 flex items-center px-3 h-[46px]">
                                                <input v-model="newItemMainDays" list="num-list" class="w-full bg-transparent border-none text-[16px] font-bold text-slate-900 focus:ring-0 outline-none text-center" placeholder="0">
                                                <span class="text-slate-400 font-bold ml-1 shrink-0 text-[13px]">天</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- TALISMANS / SHUWUN / TAI-LING -->
                                    <div v-else-if="magicItemCategory === '符令' || magicItemCategory === '太令'" class="space-y-2.5">
                                        <div class="grid grid-cols-2 gap-2">
                                            <div class="space-y-1">
                                                <div class="text-[11px] text-slate-400 font-bold px-2">尺寸</div>
                                                <div class="border border-slate-100 rounded-2xl bg-slate-50/20 flex items-center px-4 h-[46px]">
                                                    <input v-model="newItemSubSize" class="w-full bg-transparent border-none text-[15px] font-bold text-slate-900 focus:ring-0 outline-none text-center" placeholder="尺寸">
                                                </div>
                                            </div>
                                            <div class="space-y-1">
                                                <div class="text-[11px] text-slate-400 font-bold px-2">天數</div>
                                                <div class="border border-slate-100 rounded-2xl bg-slate-50/20 flex items-center px-3 h-[46px]">
                                                    <input v-model="newItemSubDetails" list="num-list" class="w-full bg-transparent border-none text-[16px] font-bold text-slate-900 focus:ring-0 outline-none text-center" placeholder="天份">
                                                    <span class="text-slate-400 font-bold ml-1 shrink-0 text-[13px]">天份</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div v-if="newItemName.includes('疏文')" class="border border-slate-100 rounded-2xl bg-slate-50/20 px-3 h-[46px] flex items-center">
                                            <input v-model="newItemSubPractitioner" list="dharma-search-list" class="w-full bg-transparent border-none text-[16px] font-bold text-slate-900 focus:ring-0 outline-none text-left" placeholder="開立人 (法號)...">
                                        </div>
                                    </div>



                                    <!-- INCENSE COIL (Strict search) -->
                                    <div v-else-if="magicItemCategory === '香環'" class="grid grid-cols-2 gap-2">
                                        <div class="border border-slate-100 rounded-2xl bg-slate-50/20 flex items-center px-4 h-[46px]">
                                            <input v-model="newItemSubDetails" list="num-list" class="w-full bg-transparent border-none text-[17px] font-bold text-slate-900 focus:ring-0 outline-none text-center" placeholder="個數">
                                            <span class="text-slate-400 font-bold ml-1 shrink-0 text-[13px]">個</span>
                                        </div>
                                        <div class="border border-slate-100 rounded-2xl bg-slate-50/20 flex items-center px-4 h-[46px]">
                                            <input v-model="newItemSubSheets" list="num-list" class="w-full bg-transparent border-none text-[17px] font-bold text-slate-900 focus:ring-0 outline-none text-center" placeholder="盒數">
                                            <span class="text-slate-400 font-bold ml-1 shrink-0 text-[13px]">盒</span>
                                        </div>
                                    </div>

                                    <!-- FU-LU INCENSE -->
                                    <div v-else-if="magicItemCategory === '福祿香'" class="grid grid-cols-2 gap-2">
                                        <div class="border border-slate-100 rounded-2xl bg-slate-50/20 flex items-center px-4 h-[46px]">
                                            <input v-model="newItemSubDetails" list="num-list" class="w-full bg-transparent border-none text-[17px] font-bold text-slate-900 focus:ring-0 outline-none text-center" placeholder="0">
                                            <span class="text-slate-400 font-bold ml-1 shrink-0 text-[13px]">根</span>
                                        </div>
                                        <div class="border border-slate-100 rounded-2xl bg-slate-50/20 flex items-center px-4 h-[46px]">
                                            <input v-model="newItemSubSheets" list="num-list" class="w-full bg-transparent border-none text-[17px] font-bold text-slate-900 focus:ring-0 outline-none text-center" placeholder="0">
                                            <span class="text-slate-400 font-bold ml-1 shrink-0 text-[13px]">包</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-4 border-t border-slate-50 pt-3">
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="flex items-center space-x-1">
                                            <button @click="showAddDetails = !showAddDetails" class="text-[12px] font-bold text-slate-400 px-1 flex items-center group">
                                                <svg :class="{'rotate-180': showAddDetails}" class="w-4 h-4 mr-1 text-slate-300 transition-transform group-hover:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="2.5" /></svg>
                                                <span class="opacity-50">內容物/細項</span>
                                            </button>
                                        </div>
                                        <button v-if="showAddDetails" @click="stageContent" class="text-red-500 text-[28px] font-light leading-none active:scale-95 transition-all"> + </button>
                                    </div>
                                    
                                    <!-- Staged Contents List (Plain display as requested) -->
                                    <div v-if="stagedContents.length > 0" class="px-2 mb-3 space-y-1">
                                        <div v-for="(sc, sci) in stagedContents" :key="sci" class="text-[15px] text-slate-700 font-bold flex items-center justify-between group">
                                            <span>• {{ sc.name }}{{ sc.details ? ' : ' + sc.details : '' }}{{ sc.remarks?.trim() ? ' (' + sc.remarks.trim() + ')' : '' }}</span>
                                            <button @click="stagedContents.splice(sci, 1)" class="text-slate-300 hover:text-red-400 opacity-0 group-hover:opacity-100 transition-all">×</button>
                                        </div>
                                    </div>

                                    <div v-if="showAddDetails" class="space-y-3 animate-fade-in mt-1">
                                        <div class="grid grid-cols-12 gap-2.5 items-end">
                                            <div :class="(magicItemSubCategory === 'default') ? 'col-span-9' : 'col-span-12'" class="space-y-0.5">
                                                <div class="text-[11px] text-slate-400 font-bold px-1 text-left">內容物名稱</div>
                                                <div class="flex items-center">
                                                    <div class="flex-1 border border-slate-100 rounded-xl bg-slate-50/20 px-4 h-[46px] flex items-center">
                                                        <input v-model="newItemSubName" list="item-name-list" 
                                                               class="w-full bg-transparent border-none outline-none shadow-none text-[16px] font-bold text-slate-900 focus:ring-0 text-left" 
                                                               placeholder="錄入內容物...">
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div v-if="magicItemSubCategory === 'default'" class="col-span-3 space-y-0.5">
                                                <div class="text-[11px] text-slate-400 font-bold px-1 text-center">天份</div>
                                                <div class="border border-slate-100 rounded-xl bg-slate-50/20 overflow-hidden flex items-center px-1 transition-all h-[46px]">
                                                    <input v-model="newItemDetailsExtraDays" list="num-list"
                                                           class="w-full bg-transparent border-none text-[16px] font-bold text-slate-900 focus:ring-0 outline-none text-center" 
                                                           placeholder="0">
                                                    <span class="text-slate-400 font-bold mr-0.5 shrink-0 text-[12px]">天</span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Specialized Sub-Inputs -->
                                        <div v-if="magicItemSubCategory !== 'default'" class="space-y-2.5">
                                            <div v-if="magicItemSubCategory === '金丹'" class="grid grid-cols-3 gap-2">
                                                <div class="space-y-1">
                                                    <div class="text-[11px] text-slate-400 font-bold px-2">吃</div>
                                                    <div class="border border-slate-100 rounded-2xl bg-slate-50/20 flex items-center px-3 h-[46px]">
                                                        <input v-model="newItemSubTimes" list="num-list" class="w-full bg-transparent border-none text-[16px] font-bold text-slate-900 focus:ring-0 outline-none text-center" placeholder="0">
                                                        <span class="text-slate-400 font-bold ml-1 shrink-0 text-[13px]">粒</span>
                                                    </div>
                                                </div>
                                                <div class="space-y-1">
                                                    <div class="text-[11px] text-slate-400 font-bold px-2">洗</div>
                                                    <div class="border border-slate-100 rounded-2xl bg-slate-50/20 flex items-center px-3 h-[46px]">
                                                        <input v-model="newItemSubHours" list="num-list" class="w-full bg-transparent border-none text-[16px] font-bold text-slate-900 focus:ring-0 outline-none text-center" placeholder="0">
                                                        <span class="text-slate-400 font-bold ml-1 shrink-0 text-[13px]">粒</span>
                                                    </div>
                                                </div>
                                                <div class="space-y-1">
                                                    <div class="text-[11px] text-slate-400 font-bold px-2">天份</div>
                                                    <div class="border border-slate-100 rounded-2xl bg-slate-50/20 flex items-center px-3 h-[46px]">
                                                        <input v-model="newItemDetailsExtraDays" list="num-list" class="w-full bg-transparent border-none text-[16px] font-bold text-slate-900 focus:ring-0 outline-none text-center" placeholder="0">
                                                        <span class="text-slate-400 font-bold ml-1 shrink-0 text-[13px]">天</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div v-else-if="magicItemSubCategory === '符令' || magicItemSubCategory === '太令'" class="space-y-2.5">
                                                <div class="grid grid-cols-2 gap-2">
                                                    <div class="space-y-1">
                                                        <div class="text-[11px] text-slate-400 font-bold px-2">尺寸</div>
                                                        <div class="border border-slate-100 rounded-2xl bg-slate-50/20 flex items-center px-4 h-[46px]">
                                                            <input v-model="newItemSubSize" class="w-full bg-transparent border-none text-[15px] font-bold text-slate-900 focus:ring-0 outline-none text-center" placeholder="尺寸">
                                                        </div>
                                                    </div>
                                                    <div class="space-y-1">
                                                        <div class="text-[11px] text-slate-400 font-bold px-2">天數</div>
                                                        <div class="border border-slate-100 rounded-2xl bg-slate-50/20 flex items-center px-3 h-[46px]">
                                                            <input v-model="newItemDetailsExtraDays" list="num-list" class="w-full bg-transparent border-none text-[16px] font-bold text-slate-900 focus:ring-0 outline-none text-center" placeholder="天份">
                                                            <span class="text-slate-400 font-bold ml-1 shrink-0 text-[13px]">天份</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div v-else-if="magicItemSubCategory === '香環'" class="grid grid-cols-2 gap-2">
                                                <div class="border border-slate-100 rounded-2xl bg-slate-50/20 flex items-center px-4 h-[46px]">
                                                    <input v-model="newItemDetailsExtraDays" list="num-list" class="w-full bg-transparent border-none text-[17px] font-bold text-slate-900 focus:ring-0 outline-none text-center" placeholder="個數">
                                                    <span class="text-slate-400 font-bold ml-1 shrink-0 text-[13px]">個</span>
                                                </div>
                                                <div class="border border-slate-100 rounded-2xl bg-slate-50/20 flex items-center px-4 h-[46px]">
                                                    <input v-model="newItemSubSheets" list="num-list" class="w-full bg-transparent border-none text-[17px] font-bold text-slate-900 focus:ring-0 outline-none text-center" placeholder="盒數">
                                                    <span class="text-slate-400 font-bold ml-1 shrink-0 text-[13px]">盒</span>
                                                </div>
                                            </div>
                                            <div v-else-if="magicItemSubCategory === '福祿香'" class="grid grid-cols-2 gap-2">
                                                <div class="border border-slate-100 rounded-2xl bg-slate-50/20 flex items-center px-4 h-[46px]">
                                                    <input v-model="newItemDetailsExtraDays" list="num-list" class="w-full bg-transparent border-none text-[17px] font-bold text-slate-900 focus:ring-0 outline-none text-center" placeholder="0">
                                                    <span class="text-slate-400 font-bold ml-1 shrink-0 text-[13px]">根</span>
                                                </div>
                                                <div class="border border-slate-100 rounded-2xl bg-slate-50/20 flex items-center px-4 h-[46px]">
                                                    <input v-model="newItemSubSheets" list="num-list" class="w-full bg-transparent border-none text-[17px] font-bold text-slate-900 focus:ring-0 outline-none text-center" placeholder="0">
                                                    <span class="text-slate-400 font-bold ml-1 shrink-0 text-[13px]">包</span>
                                                </div>
                                            </div>
                                        </div>



                                        <div class="flex items-center">
                                            <div class="flex-1 border border-slate-100 rounded-2xl bg-slate-50/20 px-3 h-[64px] flex items-center overflow-hidden">
                                                <textarea v-model="newItemRemarks" 
                                                       class="w-full bg-transparent border-none text-[15px] font-bold text-slate-400 focus:ring-0 text-left resize-none py-3" 
                                                       placeholder="內容物備註..."></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                        </div>

                        <!-- Plain List Section (No Boxes) -->
                        <div v-if="Object.keys(groupedPendingItems).length > 0" class="pt-2 space-y-0.5 border-t border-slate-100">
                            <div class="px-2 py-4 text-[13px] font-black text-slate-400 uppercase tracking-[0.2em] flex items-center">
                                📝 已加入紀錄 ({{ form.items.length }})
                            </div>
                            
                            <div v-for="(group, gName, gIdx) in groupedPendingItems" :key="gName" class="py-2.5 px-4 border-b border-slate-50 last:border-0 hover:bg-slate-50/50 transition-colors cursor-pointer" @click="expandedDetails[gName] = !expandedDetails[gName]">
                                <div class="flex items-center justify-between">
                                    <div class="flex flex-col flex-1 min-w-0">
                                        <span class="text-[17px] font-bold text-slate-900 truncate">
                                            {{ gIdx + 1 }}. {{ stripMasterPrefix(gName) }}
                                            <template v-if="!(group.length === 1 && group[0].name)">
                                                <span v-if="group[0].details" class="text-black font-bold ml-1"> : {{ group[0].details }}</span>
                                            </template>
                                        </span>
                                        <div class="mt-2 pl-4 space-y-1 animate-fade-in">
                                            <div v-for="(m, midx) in group" :key="midx" class="text-[15px] text-slate-500 font-medium">
                                                <template v-if="m.name || m.sub_name?.trim()">
                                                    └ {{ m.name ? m.name : '項目' }}{{ m.details ? ':' + m.details : '' }}{{ m.sub_name?.trim() ? ' (' + m.sub_name.trim() + ')' : '' }}
                                                    <button @click.stop="removeMagicItem(m.originalIdx)" class="ml-2 text-rose-300">✕</button>
                                                </template>
                                                <button v-else-if="group.length > 1" @click.stop="removeMagicItem(m.originalIdx)" class="ml-2 text-rose-300">✕ (空內容)</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <button @click.stop="removeGroup(gName)" class="text-rose-200 hover:text-rose-400 p-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2" /></svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Empty State -->
                        <div v-else class="py-20 flex flex-col items-center justify-center text-slate-300">
                            <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mb-4">
                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" /></svg>
                            </div>
                            <p class="text-[17px] font-bold">目前無降寶明細</p>
                            <p class="text-[14px] mt-1">請於上方「增加法寶項目」開始輸入</p>
                        </div>

                        <!-- Save Component -->
                        <div class="pt-8 border-t border-slate-100 flex flex-col items-center">
                            <button @click.prevent="saveItem" :disabled="saving" class="w-full bg-[#4A3728] text-white rounded-[28px] py-4 active:scale-95 disabled:opacity-50 text-[18px] font-black shadow-xl shadow-slate-200/50">
                                {{ saving ? '正在儲存中...' : '確認保存整筆開示' }}
                            </button>
                            <p class="text-[14px] text-slate-400 mt-4 italic font-medium px-6 text-center leading-relaxed">※ 保存後將同時儲存對象、仙師及所有開示與降寶內容。</p>
                        </div>
                    </div>

                    <!-- Bottom Navbar in Detail Mode for consistency -->
                    <mobile-navbar 
                        :can-back="true"
                        :show-action="false"
                        :action-active="false"
                        :search-active="false"
                        :can-more="false"
                        @back="itemsDetailMode = false"
                        @home="$emit('goHome')"
                        @action="() => {}"
                        @search="() => {}"
                        @more="() => {}"
                    />
                </div>

                <!-- Dharma Picker Modal -->
                <div v-if="showDharmaPicker" class="fixed inset-0 z-[400] bg-black/40 flex items-end justify-center sm:items-center animate-fade-in">
                    <div class="bg-white w-full h-[85vh] sm:h-[70vh] sm:max-w-xl rounded-t-[32px] sm:rounded-[32px] flex flex-col shadow-2xl overflow-hidden animate-slide-up">
                        <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between shrink-0">
                            <div>
                                <h3 class="text-[20px] font-bold text-slate-900">選擇對象</h3>
                                <p class="text-[14px] text-slate-400">可搜尋「法號」或「群組名稱」</p>
                            </div>
                            <button @click.prevent="showDharmaPicker = false" class="bg-slate-100 text-slate-500 w-8 h-8 rounded-full flex items-center justify-center active:scale-90 transition-transform">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2" /></svg>
                            </button>
                        </div>
                        
                        <div class="px-6 py-4 shrink-0 bg-slate-50/50">
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-300">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="2" /></svg>
                                </span>
                                <input v-model="pickerSearch" type="text" placeholder="輸入法號或群組名稱進行搜尋..." 
                                       class="w-full bg-white border border-slate-200 rounded-2xl py-3.5 pl-11 pr-4 text-[17px] text-slate-900 focus:ring-4 focus:ring-indigo-100 transition-all outline-none shadow-sm">
                            </div>
                        </div>

                        <div class="flex-1 overflow-y-auto px-6 py-2 custom-scrollbar">
                            <div class="mb-4 mt-2">
                                <div class="text-[14px] text-slate-400 font-bold px-1 mb-3 tracking-widest uppercase flex items-center">
                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" stroke-width="2"/></svg>
                                    {{ pickerSearch ? '法號搜尋結果' : '所有法號' }}
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <button v-for="dn in filteredPickerResults" :key="dn.id" 
                                            @click.prevent="toggleDharmaName(dn.id)"
                                            :class="form.dharma_name_ids.includes(dn.id) ? 'bg-indigo-50 border-indigo-200 text-indigo-700 shadow-sm' : 'bg-slate-50 border-slate-50 text-slate-600'"
                                            class="flex items-center px-4 py-3.5 rounded-2xl border transition-all text-[17px] font-medium active:scale-[0.98]">
                                        <div class="w-5 h-5 rounded-md border flex items-center justify-center mr-3 shrink-0"
                                             :class="form.dharma_name_ids.includes(dn.id) ? 'bg-indigo-600 border-indigo-600' : 'bg-white border-slate-200 shadow-sm'">
                                            <svg v-if="form.dharma_name_ids.includes(dn.id)" class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" /></svg>
                                        </div>
                                        <span class="truncate text-[17px]">{{ dn.name }}</span>
                                    </button>
                                </div>
                            </div>

                            <div v-if="filteredGroups.length > 0" class="mb-6">
                                <div class="text-[14px] text-indigo-500 font-bold px-1 mb-3 tracking-widest uppercase flex items-center">
                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" stroke-width="2"/></svg>
                                    群組搜尋結果
                                </div>
                                <div class="space-y-2">
                                    <div v-for="group in filteredGroups" :key="group.id" 
                                         class="border border-slate-100 rounded-2xl overflow-hidden bg-white shadow-sm">
                                        <div @click.prevent="toggleGroupAccordion(group.id)" 
                                             class="flex items-center justify-between px-4 py-4 cursor-pointer active:bg-slate-50">
                                            <div class="flex items-center space-x-3">
                                                <div @click.stop.prevent="toggleGroupSelection(group)" 
                                                     class="w-6 h-6 rounded-lg border flex items-center justify-center transition-all shadow-sm"
                                                     :class="isGroupFullySelected(group) ? 'bg-indigo-600 border-indigo-600' : 'bg-white border-slate-200'">
                                                    <svg v-if="isGroupFullySelected(group)" class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" /></svg>
                                                </div>
                                                <span class="text-[20px] font-bold text-slate-800" :class="isGroupFullySelected(group) ? 'text-indigo-600' : 'text-slate-800'">{{ group.name }}</span>
                                                <span class="text-[14px] bg-slate-50 text-slate-400 px-2.5 py-1 rounded-full font-bold">{{ group.dharma_names.length }}人</span>
                                            </div>
                                            <svg :class="expandedGroupPicker === group.id ? 'rotate-180' : ''" class="w-4 h-4 text-slate-300 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" /></svg>
                                        </div>
                                        <div v-if="expandedGroupPicker === group.id || pickerSearch" class="bg-indigo-50/20 px-4 py-3 border-t border-indigo-50/50 grid grid-cols-2 gap-2 animate-fade-in">
                                            <button v-for="member in group.dharma_names" :key="member.id" 
                                                    @click.prevent="toggleDharmaName(member.id)"
                                                    class="flex items-center p-2 rounded-xl text-[17px] transition-all bg-white shadow-sm"
                                                    :class="form.dharma_name_ids.includes(member.id) ? 'text-slate-800 font-bold border border-indigo-200' : 'text-slate-900 border border-transparent'">
                                                <div class="w-2.5 h-2.5 rounded-full mr-2 shrink-0 shadow-sm" :class="form.dharma_name_ids.includes(member.id) ? 'bg-indigo-500' : 'bg-slate-200'"></div>
                                                <span class="truncate">{{ member.name }}</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="p-6 border-t border-slate-100 flex items-center space-x-3 shrink-0 bg-white">
                            <button @click.prevent="form.dharma_name_ids = []" class="flex-1 py-4 bg-slate-200 text-slate-700 rounded-2xl font-bold active:scale-95 transition-all text-[17px]">全部清除</button>
                            <button @click.prevent="showDharmaPicker = false" class="flex-1 py-4 bg-[#4A3728] text-white rounded-2xl font-bold active:scale-95 transition-all text-[17px] shadow-lg shadow-slate-200/50">確認選擇</button>
                        </div>
                    </div>
                </div>

                <!-- Main List View -->
                <div v-show="!addMode" class="pb-32 flex-1 overflow-y-auto bg-white" @click="focusedId = null; activeDropdownId = null" @scroll="handleScroll">
                    <div v-if="loading && visibleItems.length === 0" class="text-center py-12 text-slate-400 text-[20px] font-bold tracking-widest uppercase">載入紀錄中...</div>
                    <div v-else class="space-y-0 mt-0">
                        <template v-for="(item, idx) in (searchQuery ? filteredItems() : visibleItems)" :key="item.id">
                            <div v-if="idx === 0 || item.date !== (idx > 0 ? (searchQuery ? filteredItems()[idx-1].date : visibleItems[idx-1].date) : null)" 
                                 v-show="!focusedDate || focusedDate === item.date"
                                 @click="toggleDateCollapse(item.date)"
                                 class="pl-[14px] pr-2 py-2 flex items-center justify-between border-b border-slate-300 cursor-pointer active:bg-slate-200 transition-colors z-[50]"
                                 :class="[
                                    focusedDate === item.date ? 'bg-slate-200/90 backdrop-blur-md sticky top-0 shadow-sm' : 'bg-slate-100',
                                    (!collapsedDates.has(item.date) && !focusedDate) ? 'sticky top-0' : ''
                                 ]">
                                <div class="flex items-center min-w-0 flex-1">
                                    <svg :class="(collapsedDates.has(item.date) && focusedDate !== item.date) ? 'rotate-[-90deg]' : ''" class="w-4 h-4 text-slate-400 mr-2 transition-transform shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" /></svg>
                                    <span class="text-[17px] font-black text-slate-900 tracking-tight truncate">{{ item.date.replace(/-/g, '/') }}</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span v-if="focusedDate === item.date" @click.stop="focusedDate = null; focusedId = null; collapsedDates.add(item.date)" class="text-[12px] font-bold text-white bg-[#4A3728] px-2 py-0.5 rounded-full uppercase tracking-tighter">返回清單</span>
                                    <span class="text-[12px] font-bold text-slate-400 uppercase tracking-tighter shrink-0 ml-1">{{ (collapsedDates.has(item.date) && focusedDate !== item.date) ? '點擊展開' : '關閉' }}</span>
                                </div>
                            </div>

                            <!-- Unified Record Row (One row per record per user request) -->
                            <div v-show="(!focusedDate || focusedDate === item.date) && (!collapsedDates.has(item.date) || focusedDate === item.date)"
                                 @click.stop="toggleExpand(item.id)" 
                                 class="pl-[14px] pr-1 py-0 border-b border-slate-100 last:border-0 cursor-pointer hover:bg-slate-50/30 transition-all bg-white"
                                 :class="{'mb-0 shadow-sm rounded-lg border border-slate-300 bg-slate-50/50': allExpanded || focusedId == item.id}"
                            >
                                <div class="flex flex-col">
                                    <div class="flex justify-between items-center relative h-4">
                                        <div class="text-[12px] text-slate-400 font-normal uppercase tracking-wider flex items-center h-full"> 
                                        </div>
                                        <div class="relative mr-[-15px]">
                                            <button @click.stop="activeDropdownId = activeDropdownId === item.id ? null : item.id" class="w-8 h-4 flex items-center justify-center text-slate-300">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM18 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                                            </button>
                                            <div v-if="activeDropdownId === item.id" class="absolute right-0 top-full mt-2 w-48 bg-white rounded-3xl shadow-[0_20px_50px_rgba(0,0,0,0.15)] border border-slate-50 z-50 overflow-hidden animate-slide-up p-1.5 focus:outline-none">
                                                <div class="flex flex-col space-y-1">
                                                    <button @click.stop="allExpanded = !allExpanded; focusedId = null; activeDropdownId = null" class="w-full px-4 py-1 text-left flex items-center hover:bg-slate-50 active:bg-slate-100 rounded-2xl transition-all group">
                                                        <span class="mr-3 text-lg group-active:scale-90 transition-transform tracking-tight">{{ (allExpanded || focusedId == item.id) ? '收' : '展' }}</span>
                                                        <span class="text-[14px] font-bold text-slate-900 tracking-tight">{{ (allExpanded || focusedId == item.id) ? '收起全部資料' : '展開全部資料' }}</span>
                                                    </button>
                                                    <button v-if="canCRUD(item)" @click.stop="editItem(item); activeDropdownId = null" class="w-full px-4 py-1 text-left flex items-center hover:bg-slate-50 active:bg-slate-100 rounded-2xl transition-all group">
                                                        <span class="mr-3 text-lg group-active:scale-90 transition-transform">📝</span>
                                                        <span class="text-[14px] font-bold text-slate-900 tracking-tight">修改</span>
                                                    </button>
                                                    <button @click.stop="copyToLine(item); activeDropdownId = null" class="w-full px-4 py-1 text-left flex items-center hover:bg-slate-50 active:bg-slate-100 rounded-2xl transition-all group">
                                                        <span class="mr-3 text-lg group-active:scale-90 transition-transform">🗨️</span>
                                                        <span class="text-[14px] font-bold text-[#25D366] tracking-tight">複製至 LINE</span>
                                                    </button>
                                                    <button @click.stop="downloadTeaching(item); activeDropdownId = null" class="w-full px-4 py-1 text-left flex items-center hover:bg-slate-50 active:bg-slate-100 rounded-2xl transition-all group">
                                                        <span class="mr-3 text-lg group-active:scale-90 transition-transform">⬇️</span>
                                                        <span class="text-[14px] font-bold text-slate-900 tracking-tight">下載存檔</span>
                                                    </button>
                                                    <button v-if="canCRUD(item)" @click.stop="deleteItem(item.id); activeDropdownId = null" class="w-full px-4 py-1 text-left flex items-center hover:bg-slate-50 active:bg-slate-100 rounded-2xl transition-all group">
                                                        <span class="mr-3 text-lg group-active:scale-90 transition-transform">🗑️</span>
                                                        <span class="text-[14px] font-bold text-slate-900 tracking-tight">刪除</span>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                <div class="flex flex-col py-1">
                                    <!-- Header: Always Visible per user request -->
                                    <div class="text-[17px] text-black font-black tracking-tight leading-tight line-clamp-1">
                                        {{ getRecordHeader(item) }}
                                    </div>

                                    <!-- Collapsed Summary View -->
                                    <div v-if="focusedId != item.id && !allExpanded" class="flex flex-col">
                                        <div class="text-[17px] text-black font-black mt-0.5 leading-tight line-clamp-1">
                                            <template v-if="item.content && item.content !== 'null' && item.content.trim()">
                                                {{ item.content.replace(/^(.*仙師|Master.*?|老祖|元始|道祖|靈寶|父皇|太宰|太子|閻王).*?[：:]/, '').trim().split('\n')[0] || '無文字開示' }}
                                            </template>
                                            <template v-else-if="item.items?.length">
                                                賜降：{{ getFirstItemNameOnly(item) }}
                                            </template>
                                            <template v-else>無文字開示</template>
                                        </div>
                                    </div>

                                    <!-- Expanded Content (Teaching then Treasures) -->
                                    <div v-if="focusedId == item.id || allExpanded" class="mt-0 space-y-0.5 pb-0 animate-fade-in">
                                        <!-- 1. Teaching Content First -->
                                        <div v-if="item.content?.trim()" class="space-y-0">
                                            <div v-if="!item.content?.match(/開示|仙師|Master|老祖|元始|道祖|靈寶|父皇|太宰|太子|閻王/)" class="text-[17px] text-black font-black">開示：</div>
                                            <div class="text-[17px] text-black whitespace-pre-wrap leading-tight font-black">
                                                {{ item.content.replace(/^(.*仙師|Master.*?|老祖|元始|道祖|靈寶|父皇|太宰|太子|閻王).*?[：:]/, '').trim() }}
                                                <div v-show="allExpanded || focusedId == item.id" class="mt-4 pt-4 border-t border-slate-100 flex items-center justify-between">
                                            <div class="text-[11px] text-slate-400 font-bold uppercase tracking-widest flex items-center">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" stroke-width="2"/></svg>
                                                登記：{{ item.user?.name || '管理員' }}
                                            </div>
                                            <div class="text-[11px] text-slate-300 font-bold">#{{ item.id }}</div>
                                        </div>
                                    </div>
                                        </div>

                                        <!-- 2. Treasures (Items) Second -->
                                        <div v-if="item.items?.length > 0 && !item.content?.includes('賜降') && !item.content?.includes('法寶')">
                                            <div class="text-[17px] text-slate-900 font-bold">賜降：</div>
                                            <div class="space-y-0">
                                                <template v-for="(group, gName, gIdx) in groupItems(item.items)" :key="gName">
                                                    <div class="text-[17px] font-black text-black">
                                                        {{ gIdx + 1 }}. {{ stripMasterPrefix(gName) }}
                                                        <template v-if="getMainDetails(group)">
                                                            : {{ getMainDetails(group) }}
                                                        </template>
                                                    </div>
                                                    <div v-for="(m, midx) in group" :key="midx">
                                                        <div v-if="m && m.name && (m.name || m.sub_name || m.details)" class="pl-5 text-[17px] text-black font-black">
                                                            {{ stripMasterPrefix(m.name) }}{{ m.details ? ':' + m.details : '' }}{{ m.sub_name ? ' (' + m.sub_name + ')' : '' }}
                                                        </div>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>

                                        <div v-if="item.items_footer_remarks?.trim()" class="text-[17px] text-slate-900 font-bold italic">
                                            備註：{{ item.items_footer_remarks.trim() }}
                                        </div>

                                        <div class="pt-1 text-left" v-if="!item.content?.includes('完畢')">
                                            <span class="text-[17px] text-slate-900 font-bold tracking-widest">完畢！</span>
                                        </div>
                                    </div>
                                </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>



                <mobile-navbar 
                    :can-back="true"
                    :show-action="!!currentFolder"
                    :action-active="addMode"
                    :search-active="showSearch"
                    :can-more="!!currentFolder"
                    @back="handleBack"
                    @home="$emit('goHome')"
                    @action="showAddMenu = true"
                    @search="showSearch = !showSearch"
                    @more="showAddMenu = true"
                />
                <search-component v-if="showSearch" v-model="searchQuery" :show="showSearch" @close="showSearch = false" />
                <div v-if="distributionModal.show" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm animate-fade-in">
                    <div class="bg-white rounded-[32px] w-full max-w-sm overflow-hidden shadow-2xl animate-slide-up border border-white/20">
                        <div class="p-8 text-center space-y-6">
                            <div class="space-y-2">
                                <div class="text-[22px] font-black text-black tracking-tight leading-tight">偵測到不同的仙師開示</div>
                                <p class="text-[15px] text-slate-500 font-bold">內容包含：{{ distributionModal.detectedNames.join('、') }}</p>
                            </div>
                            
                            <div class="flex flex-col space-y-3 pt-2">
                                <!-- Primary Action: Distribute -->
                                <button @click="executeDistributionSave('distribute')" 
                                        class="w-full py-4 bg-black text-white rounded-2xl font-black text-[17px] active:scale-[0.98] transition-all shadow-lg">
                                    {{ distributionModal.detectedNames.length === 1 ? '存入「' + distributionModal.detectedNames[0] + '」資料夾' : '依內容自動分流儲存' }}
                                </button>
                                
                                <!-- Secondary Action: Keep in Current -->
                                <button @click="executeDistributionSave('keep')" 
                                        class="w-full py-4 bg-[#F1F5F9] text-black rounded-2xl font-black text-[17px] active:scale-[0.98] transition-all border border-slate-200">
                                    維持存入「{{ currentFolder?.name || '目前' }}」資料夾
                                </button>
                                
                                <button @click="distributionModal.show = false" 
                                        class="w-full py-3 text-slate-400 font-bold text-[15px] hover:text-slate-600 transition-colors">
                                    取消錄入
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <add-action-menu :show="showAddMenu" @close="showAddMenu = false" :actions="addActions" />
            </template>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, defineEmits, watch } from 'vue';
import axios from 'axios';
import SearchComponent from './SearchComponent.vue';
import MobileNavbar from './MobileNavbar.vue';
import AddActionMenu from './AddActionMenu.vue';

// Reactive State
const props = defineProps(['user']);
const emit = defineEmits(['goHome']);
const currentFolder = ref(null);
const currentCategory = ref(null);
const addMode = ref(false);

const formatValue = (val, unit) => {
    if (val === '1' || val === 1) return '一次性';
    return val ? `${val}${unit}` : '';
};
const stripMasterPrefix = (name) => {
    if (!name) return '';
    const parts = name.split(':');
    const res = parts.length > 1 ? parts[1] : parts[0];
    return res.trim();
};

const showAddMenu = ref(false);
const teachings = ref([]); 
const visibleItems = ref([]);
const dharmaNames = ref([]);
const groups = ref([]);
const treasures = ref([]);
const masters = ref([]);
const loading = ref(false);
const entryTab = ref('single');
const saving = ref(false);
const editingId = ref(null);
const focusedId = ref(null);
const focusedDate = ref(null);
const activeDropdownId = ref(null);
const allExpanded = ref(false);
const itemsDetailMode = ref(false);
const expandedDetails = ref({});
const newItemName = ref('');
const newItemDays = ref('');
const newItemSubName = ref('');
const newItemSubDetails = ref('');
const newItemRemarks = ref('');
const stagedContents = ref([]);
const showMainRemarks = ref(false);
const showAddDetails = ref(false);

// Specialized Sub-fields
const newItemSubTimes = ref('');
const newItemSubHours = ref('');
const newItemMainTimes = ref('');
const newItemMainHours = ref('');
const newItemMainDays = ref('');
const newItemSubSize = ref('');
const newItemSubSheets = ref('');
const newItemSubPractitioner = ref('');
const newItemMainRemarks = ref('');
const newItemDetailsExtraDays = ref('');


const canCRUD = (item) => {
    if (!props.user) return false;
    return item.user_id === props.user.id || props.user.is_admin;
};

const magicItemCategory = computed(() => {
    if (!newItemName.value) return 'default';
    if (newItemName.value.includes('丹')) return '金丹';
    if (newItemName.value.includes('太令')) return '太令';
    if (newItemName.value.includes('香環')) return '香環';
    if (newItemName.value.includes('福祿香')) return '福祿香';
    if (newItemName.value.includes('由') || newItemName.value.includes('執法') || newItemName.value.includes('清煞') || newItemName.value.includes('專司靈療') || newItemName.value.includes('香灰')) return 'default';
    if (newItemName.value.includes('符') || newItemName.value.includes('令') || newItemName.value.includes('疏文')) return '符令';
    return 'default';
});

const magicItemSubCategory = computed(() => {
    if (!newItemSubName.value) return 'default';
    if (newItemSubName.value.includes('丹')) return '金丹';
    if (newItemSubName.value.includes('太令')) return '太令';
    if (newItemSubName.value.includes('香環')) return '香環';
    if (newItemSubName.value.includes('福祿香')) return '福祿香';
    if (newItemSubName.value.includes('符') || newItemSubName.value.includes('令') || newItemSubName.value.includes('疏文')) return '符令';
    return 'default';
});

const units = ref(['天份', '次性', '顆', '張', '個', '盒']);

const uniqueTreasureNames = computed(() => {
    const names = (treasures.value || []).map(t => {
        const n = t.name ? t.name.trim() : '';
        return stripMasterPrefix(n);
    });
    return [...new Set(names.filter(n => n))].sort((a, b) => a.localeCompare(b, 'zh-TW'));
});

const showDharmaPicker = ref(false);
const pickerSearch = ref('');
const expandedGroupPicker = ref(null);
const collapsedDates = ref(new Set());
const initializedDates = ref(new Set());

const uniqueDharmaNames = computed(() => {
    const names = (dharmaNames.value || []).map(d => d.name.trim());
    return [...new Set(names.filter(n => n))].sort((a, b) => a.toLowerCase().localeCompare(b.toLowerCase()));
});

const uniqueUnits = computed(() => {
    const names = units.value.map(u => u.trim());
    return [...new Set(names.filter(n => n))].sort((a, b) => a.toLowerCase().localeCompare(b.toLowerCase()));
});

visibleItems.value.forEach(item => {
    if (!initializedDates.value.has(item.date)) {
        collapsedDates.value.add(item.date);
        initializedDates.value.add(item.date);
    }
});

const showSearch = ref(false);
const searchQuery = ref('');
const itemPagination = ref({ current_page: 1, last_page: 1 });
const batchRecords = ref([
    { dharma_name_ids: [], content: '', dharmaSearchQuery: '', items: [] }
]);

const addBatchBlock = () => {
    batchRecords.value.push({ dharma_name_ids: [], content: '', dharmaSearchQuery: '', items: [] });
};

const removeBatchBlock = (index) => {
    if (batchRecords.value.length > 1) batchRecords.value.splice(index, 1);
};

const handleBlockDharmaInput = (index, val) => {
    const block = batchRecords.value[index];
    if (!val) { block.dharma_name_ids = []; return; }
    const matchedDN = dharmaNames.value.find(dn => dn.name === val);
    if (matchedDN) { block.dharma_name_ids = [matchedDN.id]; return; }
    const matchedGroup = groups.value.find(g => g.name === val);
    if (matchedGroup) {
        block.dharma_name_ids = (matchedGroup.dharma_names || []).map(dn => dn.id);
        block.dharmaSearchQuery = matchedGroup.name;
    }
};


const form = ref({
    supplement: '', target_remarks: '', content: '',
    date: new Date().toISOString().split('T')[0], master_id: null, items: [], 
    remarks: '', items_footer_remarks: '', user_id: 1, dharma_name_ids: []
});

const dharmaSearchQuery = ref('');

const folders_list = [
    { id: 1, name: '老祖仙師' }, { id: 2, name: '元始仙師' }, { id: 3, name: '道祖仙師' },
    { id: 4, name: '靈寶仙師' }, { id: 5, name: '父皇' }, { id: 6, name: '太宰仙師' },
    { id: 7, name: '太子' }, { id: 8, name: '閻王仙師' }, { id: 0, name: '父皇仙師每日開示' }
];

const filteredFolders = computed(() => {
    return folders_list.filter(f => {
        if (f.id === 0) return false;
        return true;
    });
});

const masterNameInput = ref('');

const filteredPickerResults = computed(() => {
    if (!pickerSearch.value) return dharmaNames.value;
    const q = pickerSearch.value.toLowerCase();
    return dharmaNames.value.filter(dn => (dn.name || '').toLowerCase().includes(q));
});

const filteredGroups = computed(() => {
    let list = groups.value || [];
    list = list.filter(g => g.name !== '信眾');
    
    if (!pickerSearch.value) return list;
    const q = pickerSearch.value.toLowerCase();
    return (groups.value || []).filter(g => (g.name || '').toLowerCase().includes(q));
});

const groupedPendingItems = computed(() => groupItems(form.value.items));

const addActions = computed(() => {
    const isDaily = currentFolder.value?.id === 0 || currentFolder.value?.id === '0';
    const actions = [];
    
    if (isDaily) {
        actions.push({ 
            label: '逐筆新增', 
            description: '錄入單筆開示及賜降紀錄',
            icon: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>',
            colorClass: 'bg-indigo-50 text-indigo-600',
            handler: () => { showAdd(); entryTab.value = 'single'; showAddMenu.value = false; } 
        });
    }

    if (!isDaily) {
        actions.push({ 
            label: '多筆新增 (Excel 貼上)', 
            description: '批次貼上文字清單匯入',
            icon: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>',
            colorClass: 'bg-blue-50 text-blue-600',
            handler: () => { showAdd(); entryTab.value = 'batch'; showAddMenu.value = false; } 
        });
    }

    actions.push({ 
        label: '複製 LINE', 
        description: '將紀錄轉為文字複製貼上',
        icon: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>',
        colorClass: 'bg-purple-50 text-purple-600',
        handler: copyAllToLine 
    });

    actions.push({ 
        label: '匯出 EXCEL', 
        description: '下載完整開示紀錄報表',
        icon: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 17v-4m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>',
        colorClass: 'bg-emerald-50 text-emerald-600',
        handler: exportListExcel 
    });

    if (isDaily) {
        actions.push({ 
            label: allExpanded.value ? '收起清單' : '展開清單', 
            description: allExpanded.value ? '切換為精簡模式' : '展開所有詳細開示',
            icon: allExpanded.value 
                ? '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 15l7-7 7 7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>'
                : '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>',
            colorClass: allExpanded.value ? 'bg-rose-50 text-rose-600' : 'bg-amber-50 text-amber-600',
            handler: () => { allExpanded.value = !allExpanded.value; showAddMenu.value = false; } 
        });

        actions.push({
            label: '清空今日每日開示',
            description: '刪除今日「每日開示」內的所有紀錄',
            icon: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>',
            colorClass: 'bg-rose-100 text-rose-700',
            handler: () => {
                if(confirm('確定要清空今天「每日開示」的所有紀錄嗎？\n(已分流至其他仙師或已儲存在各專區的內容不會被刪除)')) {
                    clearTodayDaily();
                    showAddMenu.value = false;
                }
            }
        });
    }

    return actions;
});

// Methods
const toggleGroupAccordion = (id) => { expandedGroupPicker.value = expandedGroupPicker.value === id ? null : id; };

const handleDharmaSearchInput = (e) => {
    const val = e.target.value;
    if (!val) { form.value.dharma_name_ids = []; return; }
    const matchedDN = dharmaNames.value.find(dn => dn.name === val);
    if (matchedDN) { form.value.dharma_name_ids = [matchedDN.id]; return; }
    const matchedGroup = groups.value.find(g => g.name === val);
    if (matchedGroup) {
        const memberIds = (matchedGroup.dharma_names || []).map(dn => dn.id);
        form.value.dharma_name_ids = memberIds;
        dharmaSearchQuery.value = matchedGroup.name;
        return;
    }
};

const resolveMasterId = () => {
    const name = masterNameInput.value;
    form.value.master_name = name;
    const m = masters.value.find(x => x.name === name);
    if (m) { form.value.master_id = m.id; return; }
    const hardcoded = { '老祖仙師': 1, '元始仙師': 2, '道祖仙師': 3, '靈寶仙師': 4, '父皇仙師': 5, '太宰仙師': 6, '太子': 7, '閻王仙師': 8 };
    if (hardcoded[name]) { form.value.master_id = hardcoded[name]; return; }
    const f = folders.value.find(x => x.name === name);
    if (f) { form.value.master_id = f.id; return; }
    form.value.master_id = null;
};

const toggleDharmaName = (id) => {
    const idx = form.value.dharma_name_ids.indexOf(id);
    if (idx > -1) form.value.dharma_name_ids.splice(idx, 1);
    else form.value.dharma_name_ids.push(id);
};

const toggleGroupSelection = (group) => {
    const memberIds = (group.dharma_names || []).map(dn => dn.id);
    const allSelected = memberIds.every(id => form.value.dharma_name_ids.includes(id));
    if (allSelected) { form.value.dharma_name_ids = form.value.dharma_name_ids.filter(id => !memberIds.includes(id)); }
    else { memberIds.forEach(id => { if (!form.value.dharma_name_ids.includes(id)) form.value.dharma_name_ids.push(id); }); }
};

const isGroupFullySelected = (group) => {
    if (!group.dharma_names || group.dharma_names.length === 0) return false;
    return group.dharma_names.every(dn => form.value.dharma_name_ids.includes(dn.id));
};

const fetchItems = async (page = 1) => {
    loading.value = true;
    try {
        const params = { 
            master_id: currentFolder.value?.id, 
            page: page 
        };
        const isDaily = currentFolder.value?.id == 0 || currentFolder.value?.id === '0';
        if (isDaily) params.is_daily = 1;
        else params.is_daily = 0;

        const res = await axios.get('/teachings', { params });
        if (page === 1) { visibleItems.value = res.data.data; }
        else {
            const existing = new Set(visibleItems.value.map(i => i.id));
            res.data.data.forEach(i => { if (!existing.has(i.id)) visibleItems.value.push(i); });
        }
        itemPagination.value = { current_page: res.data.current_page, last_page: res.data.last_page };
    } catch (e) { console.error(e); } finally { loading.value = false; }
};

const handleScroll = (e) => {
    const { scrollTop, scrollHeight, clientHeight } = e.target;
    if (scrollTop + clientHeight >= scrollHeight - 100) {
        if (!loading.value && itemPagination.value.current_page < itemPagination.value.last_page) {
            fetchItems(itemPagination.value.current_page + 1);
        }
    }
};

const syncRecords = async () => {
    try {
        const [dnRes, groupRes, masterRes, treasureRes] = await Promise.allSettled([
            axios.get('/api/dharma-names-list'), 
            axios.get('/api/groups-list'),
            axios.get('/api/masters-list'), 
            axios.get('/api/treasures-list', { params: { type: ['teaching', 'content'] } })
        ]);
        if (dnRes.status === 'fulfilled') dharmaNames.value = dnRes.value.data;
        if (groupRes.status === 'fulfilled') groups.value = groupRes.value.data || [];
        if (masterRes.status === 'fulfilled') masters.value = masterRes.value.data || [];
        if (treasureRes.status === 'fulfilled') treasures.value = (treasureRes.value.data || []).filter(t => t.name !== '清煞法寶');
    } catch (e) { console.error(e); }
};

const getDharmaNameText = (id) => {
    const dn = dharmaNames.value.find(x => x.id === id);
    return dn ? dn.name : '';
};

const saveItem = async () => {
    if (saving.value) return;
    
    saving.value = true;
    try {
        const mid = form.value.master_id || currentFolder.value?.id;
        const finalMasterId = (mid == 0 || mid === '0') ? null : mid;
        
        if (entryTab.value === 'batch') {
            if (!batchInput.value.trim()) {
                alert('請輸入錄入內容');
                saving.value = false;
                return;
            }
            
            const masterMap = { '老祖': 1, '元始': 2, '道祖': 3, '靈寶': 4, '父皇': 5, '太宰': 6, '太子': 7, '閻王': 8 };
            const isDailyFolder = currentFolder.value?.id == 0 || currentFolder.value?.id === '0';
            
            // Extract Unique Master Names from content
            const detectedNames = [];
            for (const name of Object.keys(masterMap)) {
                if (batchInput.value.includes(name)) {
                    // One-Way Rule: Don't detect "Daily/Fuhuang" (5) if we are in a Master Folder (1-4, 6-8)
                    if (!isDailyFolder && name === '父皇') continue;
                    detectedNames.push(name);
                }
            }
            
            const currentName = currentFolder.value?.name || '';
            const mismatches = detectedNames.filter(n => !currentName.includes(n));
            
            if (!isDailyFolder && mismatches.length > 0) {
                // Show custom modal instead of generic confirm
                distributionModal.value = {
                    show: true,
                    detectedNames: detectedNames,
                    isDailyFolder: false,
                    finalMasterId: finalMasterId // reference current folder
                };
                saving.value = false;
                return; // Stop and wait for modal choice
            }

            // Normal path: Daily folder or no mismatches
            await executeDistributionSave('distribute');
        } else {
            const isDailyFolder = (currentFolder.value?.id == 0 || currentFolder.value?.id === '0');
            const itemIsDaily = isDailyFolder && (finalMasterId === 5);
            
            // Check for group match
            const matchedGroup = (groups.value || []).find(g => g.name === dharmaSearchQuery.value);
            
            const payload = { 
                ...form.value, 
                master_id: finalMasterId, 
                group_id: matchedGroup ? matchedGroup.id : null,
                user_id: 1,
                is_daily: itemIsDaily ? 1 : 0
            };
            if (editingId.value) await axios.put(`/teachings/${editingId.value}`, payload);
            else await axios.post('/teachings', payload);
        }
        
        alert('存檔成功！');
        addMode.value = false; 
        itemsDetailMode.value = false; 
        editingId.value = null;
        batchInput.value = '';
        form.value = { supplement: '', target_remarks: '', content: '', date: new Date().toISOString().split('T')[0], master_id: null, items: [], remarks: '', items_footer_remarks: '', user_id: 1, dharma_name_ids: [] };
        dharmaSearchQuery.value = '';
        fetchItems(1);
    } catch (e) { 
        console.error(e);
        alert('儲存失敗'); 
    } finally { 
        saving.value = false; 
    }
};

const editItem = (item) => { 
    editingId.value = item.id; 
    form.value = { 
        ...item, 
        dharma_name_ids: item.dharma_names?.map(dn => dn.id) || [] 
    }; 
    masterNameInput.value = item.master?.name || item.master_name || ''; 
    stagedContents.value = []; // Clear previous staging when editing
    addMode.value = true; 
};

const deleteItem = async (id) => { if (!confirm('確定刪除？')) return; try { await axios.delete(`/teachings/${id}`); fetchItems(1); } catch (e) { alert('刪除失敗'); } };

const duplicateItem = (item) => { 
    form.value = { ...item, id: null, date: new Date().toISOString().split('T')[0], dharma_name_ids: item.dharma_names.map(dn => dn.id) }; 
    editingId.value = null; addMode.value = true; 
};

const removeMagicItem = (idx) => { form.value.items.splice(idx, 1); };

const buildTreasureDetails = (cat, t, h, d, sz, sh, pr) => {
    let parts = [];
    if (cat === '金丹') {
        if (t) parts.push(`${t}吃`);
        if (h) parts.push(`${h}洗`);
        if (d) parts.push(`${d}天`);
    } else if (cat === '符令' || cat === '太令') {
        if (sz) parts.push(sz);
        if (d) parts.push(`${d}天份`);
        if (pr) parts.push(`由 ${pr} 師兄姐開立`);
    } else if (cat === '香環') {
        if (d) parts.push(`${d}個`);
        if (sh) parts.push(`${sh}盒`);
    } else if (cat === '福祿香') {
        if (d) parts.push(`${d}根`);
        if (sh) parts.push(`${sh}包`);
    } else {
        if (d) parts.push(`${d}天份`);
    }
    return parts.join(' ').trim();
};

function stageContent() {
    if (!newItemSubName.value) return;
    
    const details = buildTreasureDetails(
        magicItemSubCategory.value,
        newItemSubTimes.value,
        newItemSubHours.value,
        newItemDetailsExtraDays.value,
        newItemSubSize.value,
        newItemSubSheets.value,
        newItemSubPractitioner.value
    );

    stagedContents.value.push({
        name: newItemSubName.value,
        details: details,
        remarks: newItemRemarks.value
    });

    // Reset sub-fields
    newItemSubName.value = '';
    newItemDetailsExtraDays.value = '';
    newItemRemarks.value = '';
    newItemSubSize.value = '';
    newItemSubTimes.value = '';
    newItemSubHours.value = '';
    newItemSubSheets.value = '';
    newItemSubPractitioner.value = '';
    newItemSubDetails.value = '';
}

function addNewItemQuickly() {
    if (!newItemName.value) return;

    // Standard items logic
    let finalDetails = '';
    const baseVal = newItemMainDays.value || newItemDays.value || '';
    if (baseVal === '1' || baseVal === 1) finalDetails = '一次性';
    else if (baseVal) finalDetails = `${baseVal}天份`;

    const mainItem = {
        treasure_name: newItemName.value,
        details: finalDetails,
        main_remarks: newItemMainRemarks.value || '',
    };

    // If we have staged contents, commit them as sub-items
    if (stagedContents.value.length > 0) {
        // First, if the main item has details, ensure they are saved as the "header" details (item with empty name)
        if (mainItem.details) {
            form.value.items.push({
                treasure_name: mainItem.treasure_name,
                details: mainItem.details,
                name: '',
                sub_name: mainItem.main_remarks
            });
        }
        stagedContents.value.forEach((sc) => {
            const combinedRemarks = [sc.remarks, mainItem.main_remarks].filter(r => r).join(' ');
            form.value.items.push({
                treasure_name: mainItem.treasure_name,
                details: sc.details,
                name: sc.name,
                sub_name: combinedRemarks
            });
        });
    } else {
        const combinedRemarks = [newItemRemarks.value, mainItem.main_remarks].filter(r => r).join(' ');
        const effectiveCategory = newItemSubName.value ? magicItemSubCategory.value : magicItemCategory.value;

        // Use appropriate source variables based on whether we are adding a detail or the main item
        let directDetails = buildTreasureDetails(
            effectiveCategory,
            newItemSubName.value ? newItemSubTimes.value : newItemMainTimes.value,
            newItemSubName.value ? newItemSubHours.value : newItemMainHours.value,
            newItemSubName.value ? newItemDetailsExtraDays.value : (newItemMainDays.value || newItemDays.value || newItemSubDetails.value),
            newItemSubSize.value,
            newItemSubSheets.value,
            newItemSubPractitioner.value
        );

        // If both main name and sub name are present, and both have details, save them separately
        if (newItemSubName.value && mainItem.details && directDetails && mainItem.details !== directDetails) {
            form.value.items.push({
                treasure_name: mainItem.treasure_name,
                details: mainItem.details,
                name: '',
                sub_name: mainItem.main_remarks
            });
            form.value.items.push({
                treasure_name: mainItem.treasure_name,
                details: directDetails,
                name: newItemSubName.value,
                sub_name: combinedRemarks
            });
        } else {
            form.value.items.push({
                treasure_name: mainItem.treasure_name,
                details: directDetails || mainItem.details,
                name: newItemSubName.value || '',
                sub_name: combinedRemarks
            });
        }
    }

    // Reset everything
    newItemName.value = '';
    newItemDays.value = '';
    newItemMainTimes.value = '';
    newItemMainHours.value = '';
    newItemMainDays.value = '';
    newItemMainRemarks.value = '';
    newItemDetailsExtraDays.value = '';
    newItemSubName.value = '';
    newItemSubDetails.value = '';
    newItemSubTimes.value = '';
    newItemSubHours.value = '';
    newItemSubSize.value = '';
    newItemSubSheets.value = '';
    newItemSubPractitioner.value = '';
    newItemRemarks.value = '';
    stagedContents.value = [];
    showAddDetails.value = false;
}

const addNewCategory = () => {
    form.value.items.push({ treasure_name: '', name: '', sub_name: '', details: '' });
};

const addSubItemToGroup = (gName) => {
    // If gName is empty, we just add to the first empty or generic group
    form.value.items.push({ treasure_name: gName, name: '', sub_name: '', details: '' });
    // Auto-expand to show the new sub-item
    expandedDetails.value[gName] = true;
};

const removeGroup = (gName) => {
    form.value.items = form.value.items.filter(item => item.treasure_name !== gName);
};

const updateGroupName = (oldName, newName) => {
    form.value.items.forEach(item => {
        if (item.treasure_name === oldName) item.treasure_name = newName;
    });
};

const groupItems = (items) => {
    const groups = {};
    if (!items) return groups;
    items.forEach((item, index) => { 
        const key = item.treasure_name || '未命名法寶'; 
        if (!groups[key]) groups[key] = []; 
        groups[key].push({ ...item, originalIdx: index }); 
    });
    return groups;
};

const generateSummary = (item) => { 
    if (!item?.items || item.items.length === 0) return ''; 
    const first = item.items[0];
    const name = formatTreasureName(first.treasure_name || first.name); 
    const details = first.details ? ` (${first.details})` : '';
    return name + details + (item.items.length > 1 ? ` 等${item.items.length}項` : ''); 
};

const getFirstItemNameOnly = (item) => {
    if (!item?.items || item.items.length === 0) return '';
    const first = item.items[0];
    const name = formatTreasureName(first.treasure_name || first.name);
    const details = first.details ? ` (${first.details})` : '';
    const more = item.items.length > 1 ? ` ...等${item.items.length}項` : '';
    return name + details + more;
};

const formatTreasureName = (name) => name ? name.split(':').pop() : '';

const getMasterName = (id) => {
    if (!id || id == 0) return '父皇仙師';
    const m = masters.value.find(x => x.id == id);
    return m ? m.name : '未指定';
};

const getItemCount = (id) => markings.value?.[id] || 0; // Simplified
const markings = ref({});

const getRecipientName = (item) => {
    if (item.group) return item.group.name;
    if (item.dharma_names?.length > 1 && groups.value?.length) {
        const itemIds = [...item.dharma_names].map(d => d.id).sort().join(',');
        const matched = groups.value.find(g => {
            const gIds = (g.dharma_names || []).map(dn => dn.id).sort().join(',');
            return gIds === itemIds;
        });
        if (matched) return matched.name;
    }
    if (item.dharma_names?.length > 1) return item.dharma_names[0].name + '...等' + item.dharma_names.length + '人';
    return item.dharma_names?.[0]?.name || '全體';
};

const getRecordHeader = (item) => {
    const headerPrefix = item.content?.match(/^(.*仙師|Master.*?|老祖|元始|道祖|靈寶|父皇|太宰|太子|閻王).*?[：:]/);
    if (headerPrefix) return headerPrefix[0];
    return `${item.master?.name || (item.master_name || '仙師')}開示給${getRecipientName(item)}：`;
};

const toggleExpand = (id) => { 
    if (focusedId.value == id) {
        focusedId.value = null;
    } else {
        allExpanded.value = false;
        focusedId.value = id;
        
        const item = visibleItems.value.find(i => i.id == id);
        if (item) {
            visibleItems.value.forEach(v => {
                if (v.date !== item.date) collapsedDates.value.add(v.date);
                else collapsedDates.value.delete(v.date);
            });
        }
    }
};

const isDateOfFocused = (date) => {
    if (!focusedId.value) return true;
    const matchedItem = visibleItems.value.find(i => i.id == focusedId.value);
    return matchedItem ? matchedItem.date === date : false;
};

const handleBack = () => { 
    if (addMode.value) { 
        addMode.value = false; 
        editingId.value = null; 
    } else if (currentFolder.value) { 
        const isDaily = currentFolder.value.id === 0 || currentFolder.value.id === '0';
        currentFolder.value = null; 
        if (isDaily) currentCategory.value = null;
    } else if (currentCategory.value) { 
        currentCategory.value = null; 
    } else { 
        emit('goHome'); 
    } 
};

const downloadTeaching = (item) => {
    const dnText = item.group ? item.group.name : (item.dharma_names?.map(d => d.name).join(', ') || '全員');
    const grouped = groupItems(item.items);
    let treasureLines = [];
    Object.keys(grouped).forEach((gName, gIdx) => {
        const group = grouped[gName];
        let line = `${gIdx + 1}. ${gName}`;
        if (group.length === 1 && !group[0].name && !group[0].sub_name) {
            if (group[0].details) line += `: ${group[0].details}`;
            treasureLines.push(line);
        } else if (group.length === 1) {
            line += `: ${group[0].name || ''} ${group[0].sub_name || ''} ${group[0].details || ''}`.replace(/\s+/g, ' ').trim();
            treasureLines.push(line);
        } else {
            treasureLines.push(line);
            group.forEach(m => {
                if (m.name || m.sub_name || m.details) {
                    treasureLines.push(`      ${m.name || ''}${m.details ? ':' + m.details : ''}${m.sub_name ? ' (' + m.sub_name + ')' : ''}`.trimEnd());
                }
            });
        }
    });

    const treasureText = treasureLines.length > 0 ? '\n賜降：\n' + treasureLines.join('\n') : '';
    const safeContent = (item.content && item.content !== 'null') ? '\n' + item.content : '';
    const text = `${item.master?.name || '仙師'}開示給${dnText}：${safeContent}${treasureText}\n\n完畢！`;
    const blob = new Blob([text], { type: 'text/plain' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `開示紀錄_${item.date}.txt`;
    a.click();
    window.URL.revokeObjectURL(url);
};

const copyToLine = (item) => {
    const dnText = item.group ? item.group.name : (item.dharma_names?.map(d => d.name).join(', ') || '全員');
    const grouped = groupItems(item.items);
    let treasureLines = [];
    Object.keys(grouped).forEach((gName, gIdx) => {
        const group = grouped[gName];
        let line = `${gIdx + 1}. ${gName}`;
        if (group.length === 1 && !group[0].name && !group[0].sub_name) {
            if (group[0].details) line += `: ${group[0].details}`;
            treasureLines.push(line);
        } else if (group.length === 1) {
            line += `: ${group[0].name || ''} ${group[0].sub_name || ''} ${group[0].details || ''}`.replace(/\s+/g, ' ').trim();
            treasureLines.push(line);
        } else {
            treasureLines.push(line);
            group.forEach(m => {
                if (m.name || m.sub_name || m.details) {
                    treasureLines.push(`      ${m.name || ''}${m.details ? ':' + m.details : ''}${m.sub_name ? ' (' + m.sub_name + ')' : ''}`.trimEnd());
                }
            });
        }
    });

    const treasureText = treasureLines.length > 0 ? '\n賜降：\n' + treasureLines.join('\n') : '';
    const safeContent = (item.content && item.content !== 'null') ? '\n' + item.content : '';
    const text = `${item.master?.name || (item.master_name || '仙師')}開示給${dnText}：${safeContent}${treasureText}\n\n完畢！`;
    
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(text).then(() => {
            alert('內容已複製');
        }).catch(err => {
            console.error('Copy failed', err);
        });
    } else {
        const textArea = document.createElement("textarea");
        textArea.value = text;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        alert('內容已複製 (相容模式)');
    }
};

const handleBatchFileImport = async (e) => {
    const file = e.target.files[0];
    if (!file) return;

    const reader = new FileReader();
    const ext = file.name.split('.').pop().toLowerCase();

    if (ext === 'txt' || ext === 'csv') {
        reader.onload = (res) => {
            batchInput.value = res.target.result;
        };
        reader.readAsText(file);
    } else if (ext === 'xlsx' || ext === 'xls') {
        reader.onload = (res) => {
            const data = new Uint8Array(res.target.result);
            const workbook = window.XLSX.read(data, { type: 'array' });
            const firstSheet = workbook.Sheets[workbook.SheetNames[0]];
            const rows = window.XLSX.utils.sheet_to_json(firstSheet, { header: 1 });
            batchInput.value = rows.map(r => r.join(' ')).join('\n');
        };
        reader.readAsArrayBuffer(file);
    } else if (ext === 'docx') {
        loading.value = true;
        try {
            // Dynamically load Mammoth.js if not present
            if (!window.mammoth) {
                await new Promise((resolve, reject) => {
                    const script = document.createElement('script');
                    script.src = "https://cdnjs.cloudflare.com/ajax/libs/mammoth/1.4.21/mammoth.browser.min.js";
                    script.onload = resolve;
                    script.onerror = reject;
                    document.head.appendChild(script);
                });
            }
            
            reader.onload = async (res) => {
                const arrayBuffer = res.target.result;
                const result = await window.mammoth.extractRawText({ arrayBuffer: arrayBuffer });
                const docText = result.value || '';
                
                // Only prepend header if the text doesn't already mention a Master (仙師)
                if (docText.includes('仙師')) {
                    batchInput.value = docText;
                } else {
                    const master = form.value.master_name || '仙師';
                    const targetDisplay = dharmaSearchQuery.value || '全體';
                    const header = `${master}開示給${targetDisplay}：\n\n`;
                    batchInput.value = header + docText;
                }
                loading.value = false;
            };
            reader.readAsArrayBuffer(file);
        } catch (err) {
            console.error(err);
            alert('Word 檔案解析失敗，請確認檔案格式正確。');
            loading.value = false;
        }
    } else {
        alert('不支援的檔案格式');
    }
};

const toggleDateCollapse = (date) => {
    if (focusedDate.value === date) {
        // Closing the active focus -> restore all
        focusedDate.value = null;
        focusedId.value = null;
        // Make sure it returns to a collapsed state in the main list
        collapsedDates.value.add(date);
    } else if (focusedDate.value !== null) {
        // Trying to switch focus -> show prompt
        const oldDate = focusedDate.value.replace(/-/g, '/');
        const newDate = date.replace(/-/g, '/');
        if (confirm(`目前正在切閱「${oldDate}」的紀錄，是否關閉並改為查看「${newDate}」的開示內容？`)) {
            focusedDate.value = date;
            focusedId.value = null;
            collapsedDates.value.delete(date); // Ensure it's expanded in focus mode
        }
    } else {
        // Fresh focus: Hide others (focusedDate handles this) and ensure this one is expanded
        focusedDate.value = date;
        focusedId.value = null;
        allExpanded.value = false; // RESET: Ensure we start with summaries only
        collapsedDates.value.delete(date); 
    }
};

const handleDateInit = (date) => {
    // Moved initialization logic out of template to avoid re-renders
    return '';
};

watch(visibleItems, (newItems) => {
    newItems.forEach(item => {
        if (!initializedDates.value.has(item.date)) {
            collapsedDates.value.add(item.date);
            initializedDates.value.add(item.date);
        }
    });
}, { immediate: true });

const copyAllToLine = async () => {
    loading.value = true;
    try {
        const res = await axios.get('/teachings', { params: { master_id: currentFolder.value?.id } });
        const allItems = res.data.data || res.data;
        const text = allItems.map(item => {
            const dnText = item.dharma_names?.map(d => d.name).join(', ') || '全員';
            const grouped = groupItems(item.items);
            let treasLines = [];
            Object.keys(grouped).forEach((gName, gIdx) => {
                const group = grouped[gName];
                let line = `${gIdx + 1}. ${gName}`;
                if (group.length === 1 && !group[0].name && !group[0].sub_name) {
                    if (group[0].details) line += `: ${group[0].details}`;
                    treasLines.push(line);
                } else if (group.length === 1) {
                    line += `: ${group[0].name || ''} ${group[0].sub_name || ''} ${group[0].details || ''}`.replace(/\s+/g, ' ').trim();
                    treasLines.push(line);
                } else {
                    treasLines.push(line);
                    group.forEach(m => {
                        if (m.name || m.sub_name || m.details) {
                            treasLines.push(`      ${m.name || ''}${m.details ? ':' + m.details : ''}${m.sub_name ? ' (' + m.sub_name + ')' : ''}`.trimEnd());
                        }
                    });
                }
            });
            const treasuresStr = treasLines.join('\n');
            const safeContent = (item.content && item.content !== 'null') ? '\n' + item.content : '';
            return `${item.master?.name || '仙師'}開示給${dnText}：${safeContent}${treasuresStr ? '\n賜降：\n'+treasuresStr : ''}\n\n完畢！`;
        }).join('\n\n---\n\n');
        
        await navigator.clipboard.writeText(text);
        alert('已複製完整清單至剪貼簿');
    } catch (e) { alert('複製失敗'); } finally { loading.value = false; }
};

const clearTodayDaily = async () => {
    if (!confirm(`確定清空今日 (${form.value.date}) 的每日開示暫存區？\n此動作不會影響已分流至各仙師專區的原始紀錄。`)) return;
    loading.value = true;
    try {
        const res = await axios.get('/teachings', { params: { master_id: 5, per_page: 200, is_daily: 1 } });
        const items = (res.data.data || res.data).filter(i => i.date === form.value.date && i.is_daily);
        
        for (const item of items) {
            await axios.delete(`/teachings/${item.id}`);
        }
        
        alert('今日每日開示暫存區已清空 (仙師專區紀錄均已受保護)');
        fetchItems(1);
    } catch (e) {
        alert('清空失敗');
    } finally {
        loading.value = false;
    }
};

const exportListExcel = async () => {
    if (!window.XLSX) return alert('Excel 匯出元件未就緒');
    loading.value = true;
    try {
        const res = await axios.get('/teachings', { params: { master_id: currentFolder.value?.id } });
        const dataArr = res.data.data || res.data;
        const data = dataArr.map(item => {
            const grouped = groupItems(item.items);
            let treasStrings = [];
            Object.keys(grouped).forEach((gName, gIdx) => {
                const group = grouped[gName];
                let line = `${gIdx + 1}. ${gName}`;
                if (group.length === 1) {
                    line += `: ${group[0].name || ''} ${group[0].sub_name || ''} ${group[0].details || ''}`.replace(/\s+/g, ' ').trim();
                } else {
                    const subs = group.map(m => `[${m.name}${m.details ? ':' + m.details : ''}${m.sub_name ? ' (' + m.sub_name + ')' : ''}]`).join(' ');
                    line += `: ${subs}`;
                }
                treasStrings.push(line);
            });

            return {
                '日期': item.date,
                '功德主/對象': item.dharma_names.map(d => d.name).join(', ') || '全員',
                '開示內容': item.content,
                '賜降項目': treasStrings.join('; '),
                '備註': item.items_footer_remarks || ''
            };
        });
        
        const ws = window.XLSX.utils.json_to_sheet(data);
        const wb = window.XLSX.utils.book_new();
        window.XLSX.utils.book_append_sheet(wb, ws, "開示紀錄");
        window.XLSX.writeFile(wb, `開示紀錄_${currentFolder.value?.name}_${new Date().toISOString().split('T')[0]}.xlsx`);
    } catch (e) { alert('匯出失敗'); } finally { loading.value = false; }
};

const distributionModal = ref({
    show: false,
    detectedNames: [],
    isDailyFolder: false,
    finalMasterId: null
});

const executeDistributionSave = async (mode) => {
    saving.value = true;
    const masterMap = { '老祖': 1, '元始': 2, '道祖': 3, '靈寶': 4, '父皇': 5, '太宰': 6, '太子': 7, '閻王': 8 };
    const mid = form.value.master_id || currentFolder.value?.id;
    const currentMasterId = (mid == 0 || mid === '0') ? null : mid;

    try {
        // Iterate through each block record
        for (const record of batchRecords.value) {
            const content = record.content?.trim();
            if (!content) continue;

            let blockMasterId = currentMasterId;
            
            // Auto-detect Master/Folder if in distribute mode
            if (mode === 'distribute') {
                for (const [key, id] of Object.entries(masterMap)) {
                    if (content.includes(key)) {
                        const isDailyFolder = currentFolder.value?.id == 0 || currentFolder.value?.id === '0';
                        if (!isDailyFolder && id === 5) continue; 
                        blockMasterId = id;
                        break;
                    }
                }
            }

            const isDailyFolder = (currentFolder.value?.id == 0 || currentFolder.value?.id === '0');
            const itemIsDaily = isDailyFolder && (blockMasterId === currentMasterId);
            
            const payload = {
                date: form.value.date,
                master_id: blockMasterId,
                content: content,
                dharma_name_ids: record.dharma_name_ids.length > 0 ? record.dharma_name_ids : form.value.dharma_name_ids,
                items: [],
                user_id: 1,
                is_daily: itemIsDaily ? 1 : 0
            };
            
            await axios.post('/teachings', payload);
        }
        
        distributionModal.value.show = false;
        addMode.value = false;
        // Reset batch records
        batchRecords.value = [{ dharma_name_ids: [], content: '', dharmaSearchQuery: '', items: [] }];
        fetchItems(1);
        alert('錄入成功');
    } catch (e) {
        alert('存檔過程中發生錯誤');
    } finally {
        saving.value = false;
    }
};

const showAdd = () => {
    editingId.value = null;
    form.value = {
        supplement: '', target_remarks: '', content: '',
        date: new Date().toISOString().split('T')[0], master_id: null, items: [], 
        remarks: '', items_footer_remarks: '', user_id: 1, dharma_name_ids: []
    };

    dharmaSearchQuery.value = '';
    masterNameInput.value = '';

    if (currentFolder.value) {
        const isDaily = currentFolder.value.id === 0 || currentFolder.value.id === '0';
        if (isDaily) {
            form.value.master_id = 5;
            form.value.master_name = '父皇仙師';
            entryTab.value = 'single';
        } else {
            form.value.master_id = currentFolder.value.id;
            form.value.master_name = currentFolder.value.name;
            entryTab.value = 'batch';
        }
    }
    addMode.value = true;
};

watch(() => form.value.dharma_name_ids, (newIds) => {
    if (newIds.length === 1) {
        const name = getDharmaNameText(newIds[0]);
        if (dharmaSearchQuery.value !== name) dharmaSearchQuery.value = name;
    } else if (newIds.length === 0) {
        dharmaSearchQuery.value = '';
    } else {
        const isGroupMatch = (groups.value || []).some(g => g.name === dharmaSearchQuery.value);
        if (!isGroupMatch) {
            dharmaSearchQuery.value = '';
        }
    }
}, { deep: true });

watch(currentFolder, (val) => { if (val) { visibleItems.value = []; fetchItems(1); syncRecords(); } });

onMounted(syncRecords);
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar { width: 5px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
@keyframes slide-up { from { transform: translateY(100%); } to { transform: translateY(0); } }
.animate-slide-up { animation: slide-up 0.3s cubic-bezier(0.16, 1, 0.3, 1); }
@keyframes fade-in { from { opacity: 0; } to { opacity: 1; } }
.animate-fade-in { animation: fade-in 0.2s ease-out; }
.custom-date-input::-webkit-calendar-picker-indicator { margin-left: auto; cursor: pointer; }
</style>
