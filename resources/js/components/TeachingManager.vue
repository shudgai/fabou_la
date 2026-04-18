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
                <option v-for="g in groups" :key="'g'+g.id" :value="g.name">{{ g.name }} (群組)</option>
            </datalist>

            <!-- Header with Back Button -->
            <div v-if="currentFolder" class="border-b border-gray-100 flex items-center bg-white sticky top-0 z-10" style="padding: 12px 10px 10px 10px;">
                <button @click="handleBack" class="text-slate-400 p-2 mr-2 active:scale-90 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" /></svg>
                </button>
                <h2 class="text-[20px] font-bold font-outfit tracking-tight text-slate-900 flex-1 text-center truncate px-2">
                    <span>{{ currentFolder ? (currentFolder.id === 0 ? '每日開示' : currentFolder.name) : '父皇仙師開示' }}</span>
                </h2>
            </div>

            <!-- Level 1: Folders -->
            <div v-if="!currentFolder" class="min-h-screen bg-white">
                <div class="px-6 py-4 text-center">
                    <h1 class="text-[20px] font-bold text-slate-900 tracking-tight">父皇仙師開示專區</h1>
                </div>
                <div class="grid grid-cols-2 gap-[10px] p-4 place-items-center">
                    <button v-for="(folder, idx) in folders" :key="folder.id" 
                        @click="currentFolder = folder"
                        class="flex flex-col items-center justify-center bg-transparent transition-all active:scale-95 border border-[rgb(255,215,0)] rounded-xl group p-2 w-[120px] h-[135px] relative">
                        <div class="relative mb-3">
                            <svg class="w-20 h-20 transition-transform group-hover:scale-110 drop-shadow-md" viewBox="0 0 64 64" fill="none">
                                <defs>
                                    <linearGradient :id="'fGrad' + idx" x1="0%" y1="0%" x2="100%" y2="100%">
                                        <stop offset="0%" style="stop-color:rgb(255, 150, 150);stop-opacity:1" />
                                        <stop offset="50%" style="stop-color:rgb(248, 113, 113);stop-opacity:1" />
                                        <stop offset="100%" style="stop-color:rgb(220, 38, 38);stop-opacity:1" />
                                    </linearGradient>
                                </defs>
                                <path d="M4 14C4 11.7909 5.79086 10 8 10H24.5L30 16H56C58.2091 16 60 17.7909 60 20V50C60 52.2091 58.2091 54 56 54H8C5.79086 54 4 52.2091 4 50V14Z" :fill="'url(#fGrad' + idx + ')'" opacity="0.8"/>
                                <path d="M4 22C4 19.7909 5.79086 18 8 18H56C58.2091 18 60 19.7909 60 22V50C60 52.2091 58.2091 54 56 54H8C5.79086 54 4 52.2091 4 50V22Z" :fill="'url(#fGrad' + idx + ')'" stroke="rgba(255,255,255,0.4)" stroke-width="0.5"/>
                            </svg>
                            <div v-if="getItemCount(folder.id) > 0" class="absolute top-[2px] right-[-5px] bg-red-500 text-white text-[14px] font-black px-2 py-0.5 rounded-full shadow-lg border-2 border-white">
                                {{ getItemCount(folder.id) }}
                            </div>
                        </div>
                        <span class="mt-[-2px] text-[18px] font-bold text-slate-900 leading-tight text-center">
                            <span v-if="folder.id === 0">父皇仙師<br>每日開示</span>
                            <span v-else>{{ folder.name }}</span>
                        </span>
                    </button>
                </div>
                <div class="mt-12 flex justify-center pb-32">
                    <button @click="$emit('goHome')" class="text-slate-300 hover:text-slate-500 transition-colors flex items-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" /></svg>
                        <span class="text-[14px] font-medium tracking-widest uppercase">返回專區列表</span>
                    </button>
                </div>
            </div>

            <!-- Level 2: List & Add View -->
            <template v-else>
                <!-- Add View -->
                <div v-if="addMode" class="fixed inset-0 z-[200] bg-white flex justify-center">
                    <div class="bg-white w-full h-full relative flex flex-col overflow-y-auto">
                        <div class="px-6 py-4 border-b border-slate-100 flex items-center bg-white sticky top-0 z-[210]">
                            <button @click="handleBack" class="text-slate-400 mr-4">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" /></svg>
                            </button>
                            <div class="flex items-center flex-1">
                                <h2 class="text-[20px] font-bold text-slate-900 tracking-tight shrink-0">
                                    {{ editingId ? '修改開示紀錄' : (currentFolder?.id === 0 ? '每日開示錄入' : currentFolder?.name + '錄入') }}
                                </h2>
                                <span v-if="editingId" class="text-indigo-400 ml-3 font-medium text-[14px] bg-indigo-50 px-2 py-0.5 rounded-full">修改中</span>
                                <div class="flex bg-slate-100 p-0.5 rounded-xl ml-auto">
                                    <button @click="entryTab = 'single'" :class="entryTab === 'single' ? 'bg-white shadow-sm text-indigo-600' : 'text-slate-500'" class="px-3 py-1.5 text-[14px] rounded-lg transition-all">逐筆</button>
                                    <button @click="entryTab = 'batch'" :class="entryTab === 'batch' ? 'bg-white shadow-sm text-indigo-600' : 'text-slate-500'" class="px-3 py-1.5 text-[14px] rounded-lg transition-all ml-0.5">批量</button>
                                </div>
                            </div>
                        </div>
                        <div class="space-y-4 pb-32 flex-1">
                            <template v-if="entryTab === 'single'">
                                <div class="grid grid-cols-2 gap-3 px-4 pb-1 mt-1">
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
                                
                                <div class="grid grid-cols-12 gap-3 px-4 py-1.5 border-b border-blue-100/10">
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
                                                   class="w-full bg-transparent border-none text-[18px] text-slate-900 focus:ring-0 outline-none font-bold placeholder:text-slate-300">
                                        </div>
                                    </div>
                                    <div class="col-span-4 space-y-0.5">
                                        <label class="text-[13px] text-slate-400 font-bold px-1">備註</label>
                                        <div class="border border-slate-100 rounded-2xl bg-slate-50/50 overflow-hidden h-[56px]">
                                            <textarea v-model="form.supplement" placeholder="摘要..." class="w-full bg-transparent border-none text-[17px] text-slate-900 focus:ring-0 outline-none p-3.5 resize-none font-bold leading-tight h-full" rows="1"></textarea>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="bg-blue-50/50 border-b border-blue-100/30 py-3 px-4 flex items-start">
                                    <label class="text-[13px] text-slate-400 w-10 text-left shrink-0 mt-2 font-bold uppercase tracking-wider">開示</label>
                                    <div class="flex-1 ml-1 border border-slate-100 rounded-2xl bg-white overflow-hidden min-h-[100px]">
                                        <textarea v-model="form.content" rows="4" @input="e => { e.target.style.height = 'auto'; e.target.style.height = e.target.scrollHeight + 'px' }" placeholder="輸入具體內容描述..." class="w-full bg-transparent border-none text-[18px] text-slate-900 focus:ring-0 outline-none p-4 resize-none overflow-hidden min-h-[90px] font-normal leading-relaxed"></textarea>
                                    </div>
                                </div>

                                <!-- Replaced with floating action bar below -->
                            </template>
                            <template v-else>
                                <textarea v-model="batchInput" rows="15" class="w-full bg-slate-50 rounded-xl p-3 border-none text-[17px] text-slate-900 outline-none resize-none" placeholder="批量輸入開示內容..."></textarea>
                            </template>

                            <!-- Floating Action Bar (Side-by-Side) -->
                            <div class="fixed bottom-0 left-0 right-0 p-4 pb-6 bg-white/80 backdrop-blur-md border-t border-slate-100 z-[300] flex items-center space-x-3">
                                <button @click.prevent="itemsDetailMode = true" class="flex-1 bg-indigo-50 text-indigo-600 rounded-2xl py-3 shadow-md border border-indigo-100 active:scale-95 transition-all text-[15px] font-bold truncate">
                                    <span v-if="generateSummary(form)">降寶 ({{ generateSummary(form) }})</span>
                                    <span v-else>降寶詳情</span>
                                </button>
                                <button @click="saveItem" :disabled="saving" class="flex-1 bg-yellow-400 text-yellow-950 rounded-2xl py-3 shadow-lg active:scale-95 disabled:opacity-50 text-[16px] font-black tracking-widest">
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
                        <button @click.prevent="itemsDetailMode = false" class="bg-indigo-600 text-white px-5 py-2 rounded-2xl text-[15px] font-bold shadow-lg shadow-indigo-100 active:scale-95 transition-all">完成</button>
                    </div>

                    <div class="px-2.5 pt-0 pb-40 space-y-5">
                        <div class="bg-white rounded-[24px] border border-slate-50 p-2.5 shadow-sm">
                                <div class="text-[14px] font-bold text-slate-400 tracking-[0.1em] uppercase flex items-center justify-between px-1 mb-2">
                                    <div class="flex items-center">
                                        <span class="w-5 h-5 bg-indigo-600 text-white rounded-full flex items-center justify-center text-[11px] font-black mr-2">1</span>
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
                                        <div class="border border-slate-100 rounded-xl bg-slate-50/20 overflow-hidden flex items-center px-1 transition-all h-[56px]">
                                            <input v-model="newItemDays" list="num-list"
                                                   class="w-full bg-transparent border-none text-[18px] font-bold text-slate-900 focus:ring-0 outline-none text-center" 
                                                   placeholder="0">
                                        </div>
                                    </div>
                                </div>

                                <!-- Main Item Remarks (Toggleable) - Hidden when adding details -->
                                <div v-if="!showAddDetails" class="mt-2 text-left">
                                    <button @click="showMainRemarks = !showMainRemarks" class="text-[12px] font-bold text-slate-400 flex items-center px-1">
                                        {{ showMainRemarks ? '隱藏主備註' : '+ 加入主備註' }}
                                    </button>
                                    <div v-if="showMainRemarks" class="mt-2 border border-slate-100 rounded-[36px] bg-slate-50/10 px-6 h-[100px] flex items-center animate-fade-in overflow-hidden">
                                        <textarea v-model="newItemMainRemarks" 
                                               class="w-full bg-transparent border-none text-[15px] font-bold text-slate-500 focus:ring-0 text-left resize-none py-3" 
                                               placeholder="主法寶備註..."></textarea>
                                    </div>
                                </div>

                                <!-- SPECIALIZED INPUTS (Immediate Category fields) -->
                                <div v-if="magicItemCategory !== 'default'" class="mt-2.5 space-y-2.5 animate-fade-in">
                                    <div v-if="magicItemCategory === '金丹'" class="grid grid-cols-3 gap-2">
                                        <div class="space-y-1">
                                            <div class="text-[11px] text-slate-400 font-bold px-2">次數</div>
                                            <div class="border border-slate-100 rounded-2xl bg-slate-50/20 flex items-center px-4 h-[56px]">
                                                <input v-model="newItemSubTimes" list="num-list" class="w-full bg-transparent border-none text-[16px] font-bold text-slate-900 focus:ring-0 text-center" placeholder="次">
                                            </div>
                                        </div>
                                        <div class="space-y-1">
                                            <div class="text-[11px] text-slate-400 font-bold px-2">時數</div>
                                            <div class="border border-slate-100 rounded-2xl bg-slate-50/20 flex items-center px-4 h-[56px]">
                                                <input v-model="newItemSubHours" list="num-list" class="w-full bg-transparent border-none text-[16px] font-bold text-slate-900 focus:ring-0 text-center" placeholder="時">
                                            </div>
                                        </div>
                                        <div class="space-y-1">
                                            <div class="text-[11px] text-slate-400 font-bold px-2">天份</div>
                                            <div class="border border-slate-100 rounded-2xl bg-slate-50/20 flex items-center px-4 h-[56px]">
                                                <input v-model="newItemSubDetails" list="num-list" class="w-full bg-transparent border-none text-[16px] font-bold text-slate-900 focus:ring-0 text-center" placeholder="天">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- TALISMANS / SHUWUN / TAI-LING -->
                                    <div v-else-if="magicItemCategory === '符令' || magicItemCategory === '太令'" class="space-y-2.5">
                                        <div class="grid grid-cols-2 gap-2">
                                            <div class="space-y-1">
                                                <div class="text-[11px] text-slate-400 font-bold px-2">尺寸</div>
                                                <div class="border border-slate-100 rounded-2xl bg-slate-50/20 flex items-center px-4 h-[56px]">
                                                    <input v-model="newItemSubSize" class="w-full bg-transparent border-none text-[15px] font-bold text-slate-900 focus:ring-0 text-center" placeholder="尺寸">
                                                </div>
                                            </div>
                                            <div class="space-y-1">
                                                <div class="text-[11px] text-slate-400 font-bold px-2">天份/份數</div>
                                                <div class="border border-slate-100 rounded-2xl bg-slate-50/20 flex items-center px-4 h-[56px]">
                                                    <input v-model="newItemSubDetails" list="num-list" class="w-full bg-transparent border-none text-[16px] font-bold text-slate-900 focus:ring-0 text-center" :placeholder="magicItemCategory === '太令' ? '天份' : '數量/份'">
                                                </div>
                                            </div>
                                        </div>
                                        <div v-if="newItemName.includes('疏文')" class="border border-slate-100 rounded-2xl bg-slate-50/20 px-6 h-[56px] flex items-center">
                                            <input v-model="newItemSubPractitioner" list="dharma-search-list" class="w-full bg-transparent border-none text-[16px] font-bold text-slate-900 focus:ring-0 text-left" placeholder="開立人 (法號)...">
                                        </div>
                                    </div>

                                    <!-- INCENSE -->
                                    <div v-else-if="magicItemCategory === '香環'" class="grid grid-cols-2 gap-2">
                                        <div class="border border-slate-100 rounded-2xl bg-slate-50/20 flex items-center px-4 h-[56px]">
                                            <input v-model="newItemSubDetails" list="num-list" class="w-full bg-transparent border-none text-[17px] font-bold text-slate-900 focus:ring-0 text-center" placeholder="個數">
                                            <span class="text-slate-400 font-bold ml-1 shrink-0">個</span>
                                        </div>
                                        <div class="border border-slate-100 rounded-2xl bg-slate-50/20 flex items-center px-4 h-[56px]">
                                            <input v-model="newItemSubSheets" list="num-list" class="w-full bg-transparent border-none text-[17px] font-bold text-slate-900 focus:ring-0 text-center" placeholder="盒數">
                                            <span class="text-slate-400 font-bold ml-1 shrink-0">盒</span>
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
                                            <div class="col-span-9 space-y-0.5">
                                                <div class="text-[11px] text-slate-400 font-bold px-1 text-left">內容物名稱</div>
                                                <div class="flex items-center">
                                                    <div class="flex-1 border border-slate-100 rounded-xl bg-slate-50/20 px-4 h-[50px] flex items-center">
                                                        <input v-model="newItemSubName" list="item-name-list" 
                                                               class="w-full bg-transparent border-none outline-none shadow-none text-[16px] font-bold text-slate-900 focus:ring-0 text-left" 
                                                               placeholder="錄入內容物...">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-span-3 space-y-0.5">
                                                <div class="text-[11px] text-slate-400 font-bold px-1 text-center">天份</div>
                                                <div class="border border-slate-100 rounded-xl bg-slate-50/20 px-1 h-[50px] flex items-center">
                                                    <input v-model="newItemDetailsExtraDays" list="num-list" 
                                                           class="w-full bg-transparent border-none outline-none shadow-none text-[16px] font-bold text-slate-900 focus:ring-0 text-center" 
                                                           placeholder="0">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex items-center">
                                            <div class="flex-1 border border-slate-100 rounded-2xl bg-slate-50/20 px-6 h-[80px] flex items-center overflow-hidden">
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
                                            {{ gIdx + 1 }}. {{ gName }}
                                            <template v-if="!(group.length === 1 && group[0].name)">
                                                <span v-if="group[0].details" class="text-indigo-400 font-bold ml-1"> : {{ group[0].details }}</span>
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
                            <button @click.prevent="saveItem" :disabled="saving" class="w-full bg-yellow-400 text-yellow-950 rounded-[28px] py-4 shadow-xl active:scale-95 disabled:opacity-50 text-[18px] font-black">
                                {{ saving ? '正在儲存中...' : '確認保存整筆開示' }}
                            </button>
                            <p class="text-[14px] text-slate-400 mt-4 italic font-medium px-6 text-center leading-relaxed">※ 保存後將同時儲存對象、仙師及所有開示與降寶內容。</p>
                        </div>
                    </div>
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
                                                    :class="form.dharma_name_ids.includes(member.id) ? 'text-indigo-600 font-bold border border-indigo-200' : 'text-slate-900 border border-transparent'">
                                                <div class="w-2.5 h-2.5 rounded-full mr-2 shrink-0 shadow-sm" :class="form.dharma_name_ids.includes(member.id) ? 'bg-indigo-500' : 'bg-slate-200'"></div>
                                                <span class="truncate">{{ member.name }}</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="p-6 border-t border-slate-100 flex items-center space-x-3 shrink-0 bg-white">
                            <button @click.prevent="form.dharma_name_ids = []" class="flex-1 py-4 bg-slate-50 text-slate-500 rounded-2xl font-bold active:scale-95 transition-all text-[17px]">全部清除</button>
                            <button @click.prevent="showDharmaPicker = false" class="flex-[2] py-4 bg-indigo-600 text-white rounded-2xl font-bold shadow-lg shadow-indigo-100 active:scale-95 transition-all text-[17px]">確認選擇</button>
                        </div>
                    </div>
                </div>

                <!-- Main List View -->
                <div v-show="!addMode" class="pb-32 flex-1 overflow-y-auto bg-white" @click="focusedId = null; activeDropdownId = null" @scroll="handleScroll">
                    <div v-if="loading && visibleItems.length === 0" class="text-center py-12 text-slate-400 text-[20px] font-bold tracking-widest uppercase">載入紀錄中...</div>
                    <div v-else class="space-y-0 mt-0">
                        <template v-for="(item, idx) in (searchQuery ? filteredItems() : visibleItems)" :key="item.id">
                            <!-- Date Separator -->
                            <div v-if="idx === 0 || item.date !== visibleItems[idx-1].date" 
                                 @click="toggleDateCollapse(item.date)"
                                 class="pl-[14px] pr-1 py-0.5 bg-white sticky top-0 z-20 backdrop-blur-md flex items-center justify-between border-b border-slate-100 cursor-pointer active:bg-slate-50 transition-colors shadow-sm">
                                <div class="flex items-center min-w-0 flex-1">
                                    <svg :class="collapsedDates.has(item.date) ? 'rotate-[-90deg]' : ''" class="w-4 h-4 text-slate-400 mr-2 transition-transform shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" /></svg>
                                    <span class="text-[17px] font-black text-slate-900 tracking-tight truncate">{{ item.date.replace(/-/g, '/') }}</span>
                                </div>
                                <span class="text-[12px] font-bold text-slate-300 uppercase tracking-tighter shrink-0 ml-1">{{ collapsedDates.has(item.date) ? '點擊展開' : '開示記錄' }}</span>
                            </div>

                            <!-- Person Rows -->
                            <div v-for="(displayDn, dnIdx) in (item.dharma_names?.length ? item.dharma_names : [{id: 'all', name: '全體'}])" 
                                 :key="item.id + '_' + displayDn.id"
                                 v-show="(!collapsedDates.has(item.date)) && (!focusedId || focusedId === (item.id + '_' + displayDn.id))"
                                 @click.stop="toggleExpand(item.id + '_' + displayDn.id)" 
                                 class="pl-[14px] pr-1 py-0 border-b border-slate-100 last:border-0 cursor-pointer hover:bg-slate-50/30 transition-all bg-white"
                                 :class="{'mb-0 shadow-sm rounded-lg border border-indigo-100 mx-0 !pl-[14px] !pr-1 bg-indigo-50/10': allExpanded || focusedId === (item.id + '_' + displayDn.id)}"
                            >
                                <div class="flex flex-col">
                                    <div class="flex justify-between items-center relative h-4">
                                        <div class="text-[12px] text-slate-400 font-normal uppercase tracking-wider flex items-center h-full"> 
                                        </div>
                                        <div class="relative mr-[-15px]">
                                            <button @click.stop="activeDropdownId = activeDropdownId === item.id ? null : item.id" class="w-8 h-4 flex items-center justify-center text-slate-300">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM18 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                                            </button>
                                            <div v-if="activeDropdownId === item.id" class="absolute right-0 top-full mt-2 w-36 bg-white rounded-3xl shadow-[0_20px_50px_rgba(0,0,0,0.15)] border border-slate-50 z-50 overflow-hidden animate-slide-up p-1.5 focus:outline-none">
                                                <div class="flex flex-col space-y-1">
                                                    <button @click.stop="allExpanded = !allExpanded; focusedId = null; activeDropdownId = null" class="w-full px-4 py-1 text-left flex items-center hover:bg-slate-50 active:bg-slate-100 rounded-2xl transition-all group">
                                                        <span class="mr-3 text-lg group-active:scale-90 transition-transform tracking-tight">{{ (allExpanded || focusedId === (item.id + '_' + displayDn.id)) ? '收' : '展' }}</span>
                                                        <span class="text-[14px] font-bold text-indigo-600 tracking-tight">{{ (allExpanded || focusedId === (item.id + '_' + displayDn.id)) ? '收起清單' : '展開清單' }}</span>
                                                    </button>
                                                    <div class="h-px bg-slate-50 my-0.5"></div>
                                                    <button @click.stop="editItem(item); activeDropdownId = null" class="w-full px-4 py-1 text-left flex items-center hover:bg-slate-50 active:bg-slate-100 rounded-2xl transition-all group">
                                                        <span class="mr-3 text-lg group-active:scale-90 transition-transform">📝</span>
                                                        <span class="text-[14px] font-bold text-indigo-600 tracking-tight">修改</span>
                                                    </button>
                                                    <button @click.stop="duplicateItem(item); activeDropdownId = null" class="w-full px-4 py-1 text-left flex items-center hover:bg-slate-50 active:bg-slate-100 rounded-2xl transition-all group">
                                                        <span class="mr-3 text-lg group-active:scale-90 transition-transform">📂</span>
                                                        <span class="text-[14px] font-bold text-blue-500 tracking-tight">複製</span>
                                                    </button>
                                                    <button @click.stop="copyToLine(item); activeDropdownId = null" class="w-full px-4 py-1 text-left flex items-center hover:bg-slate-50 active:bg-slate-100 rounded-2xl transition-all group">
                                                        <span class="mr-3 text-lg group-active:scale-90 transition-transform">🗨️</span>
                                                        <span class="text-[14px] font-bold text-green-600 tracking-tight">貼LINE</span>
                                                    </button>
                                                    <button @click.stop="deleteItem(item.id); activeDropdownId = null" class="w-full px-4 py-1 text-left flex items-center hover:bg-slate-50 active:bg-slate-100 rounded-2xl transition-all group">
                                                        <span class="mr-3 text-lg group-active:scale-90 transition-transform">🗑️</span>
                                                        <span class="text-[14px] font-bold text-red-500 tracking-tight">刪除</span>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div v-if="focusedId != (item.id + '_' + displayDn.id) && !allExpanded" class="flex items-start space-x-1 overflow-hidden py-0.5">
                                        <span v-if="!item.content?.includes('開示')" class="text-[17px] text-slate-900 font-bold shrink-0">{{ item.master?.name || (item.master_name || '仙師') }}開示給{{ displayDn.name }}：</span>
                                        <span class="text-[17px] text-slate-900 font-medium flex-1 truncate">{{ item.content }}</span>
                                    </div>

                                    <div v-else-if="!item.content?.includes('開示') && !item.content?.includes('給')" class="flex items-center mb-0.5">
                                        <span class="text-[17px] text-indigo-600 font-black tracking-tight">{{ item.master?.name || (item.master_name || '仙師') }}開示給{{ displayDn.name }}：</span>
                                    </div>

                                    <div v-if="focusedId != (item.id + '_' + displayDn.id) && !allExpanded && item.items?.length" class="text-[17px] text-indigo-400 font-bold ml-0.5 leading-tight">
                                        賜降：{{ getFirstItemNameOnly(item) }}
                                    </div>

                                    <div v-if="focusedId == (item.id + '_' + displayDn.id) || allExpanded" class="mt-0 space-y-0.5 pb-0 animate-fade-in">
                                        <!-- Hide automatic items list if content already has the treasure keywords -->
                                        <div v-if="item.items?.length > 0 && !item.content?.includes('賜降') && !item.content?.includes('法寶')">
                                            <div class="text-[17px] text-slate-900 font-bold">賜降：</div>
                                            <div class="space-y-0">
                                                <template v-for="(group, gName, gIdx) in groupItems(item.items)" :key="gName">
                                                    <div class="text-[17px] font-bold text-slate-900">
                                                        {{ gIdx + 1 }}. {{ gName }}
                                                        <template v-if="group.length === 1 && !group[0].name && group[0].details">
                                                            : {{ group[0].details }}
                                                        </template>
                                                        <template v-else-if="group.length === 1 && group[0].name">
                                                            <template v-if="group[0].details || group[0].sub_name">
                                                                <!-- If only one item with info, it goes below per user request "移到法寶名稱下方" -->
                                                            </template>
                                                        </template>
                                                    </div>
                                                    <div class="mt-0.5 ml-1 space-y-0.5">
                                                        <div v-for="(m, midx) in group" :key="midx">
                                                            <div v-if="m && (m.name || m.sub_name)" class="pl-6 text-[17px] text-slate-900 font-bold">
                                                                └ {{ m.name }}{{ m.details ? ':' + m.details : '' }}{{ m.sub_name ? ' (' + m.sub_name + ')' : '' }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>

                                        <div v-if="item.content?.trim()" class="space-y-0">
                                            <!-- Hide "開示：" label if content already has opening keywords -->
                                            <div v-if="!item.content?.includes('開示')" class="text-[17px] text-slate-900 font-bold">開示：</div>
                                            <div class="text-[17px] text-slate-900 whitespace-pre-wrap leading-tight font-bold">
                                                {{ item.content }}
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
                        </template>
                    </div>
                </div>

                <datalist id="item-name-list">
                    <option v-for="t in uniqueTreasureNames" :key="t" :value="t" />
                </datalist>
                <datalist id="dharma-name-list">
                    <option v-for="dn in uniqueDharmaNames" :key="dn" :value="dn" />
                </datalist>
                <datalist id="num-list">
                    <option v-for="n in 9" :key="n" :value="n" />
                </datalist>

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
const emit = defineEmits(['goHome']);
const currentFolder = ref(null);
const addMode = ref(false);

const formatValue = (val, unit) => {
    if (val === '1' || val === 1) return '一次性';
    return val ? `${val}${unit}` : '';
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
const newItemSubSize = ref('');
const newItemSubSheets = ref('');
const newItemSubPractitioner = ref('');
const newItemMainRemarks = ref('');
const newItemDetailsExtraDays = ref('');


const magicItemCategory = computed(() => {
    if (!newItemName.value) return 'default';
    if (newItemName.value.includes('丹')) return '金丹';
    if (newItemName.value.includes('太令')) return '太令';
    if (newItemName.value.includes('由') || newItemName.value.includes('執法') || newItemName.value.includes('清煞')) return '執法';
    if (newItemName.value.includes('符') || newItemName.value.includes('令') || newItemName.value.includes('疏文')) return '符令';
    if (newItemName.value.includes('香') || newItemName.value.includes('環')) return '香環';
    return 'default';
});

const tempItem = ref({ treasure_name: '', name: '', details: '', sub_name: '' });
const stagingItems = ref([]);
const temp1Qty = ref('');
const temp1Unit = ref('天份');
const temp1Eat = ref('');
const temp1Wash = ref('');
const temp1Days = ref('');
const temp1Size = ref('');
const temp1Box = ref('');
const temp1Person = ref('');
const temp2Qty = ref('');
const temp2Unit = ref('天份');
const temp2Eat = ref('');
const temp2Wash = ref('');
const temp2Days = ref('');
const temp2Size = ref('');
const temp2Box = ref('');
const temp2Person = ref('');

const temp2SubEat = ref('');
const temp2SubWash = ref('');
const temp2SubDays = ref('');
const temp2SubSize = ref('');
const temp2SubBox = ref('');
const temp2SubPerson = ref('');

const units = ref(['天份', '次性', '顆', '張', '個', '盒']);

const uniqueTreasureNames = computed(() => {
    const names = (treasures.value || []).map(t => t.name ? t.name.trim() : '');
    return [...new Set(names.filter(n => n))].sort((a, b) => a.localeCompare(b, 'zh-TW'));
});

const uniqueDharmaNames = computed(() => {
    const names = dharmaNames.value.map(d => d.name.trim());
    return [...new Set(names.filter(n => n))].sort((a, b) => a.toLowerCase().localeCompare(b.toLowerCase()));
});

const uniqueUnits = computed(() => {
    const names = units.value.map(u => u.trim());
    return [...new Set(names.filter(n => n))].sort((a, b) => a.toLowerCase().localeCompare(b.toLowerCase()));
});

const showDharmaPicker = ref(false);
const pickerSearch = ref('');
const expandedGroupPicker = ref(null);
const collapsedDates = ref(new Set());
const initializedDates = ref(new Set());
visibleItems.value.forEach(item => {
    if (!initializedDates.value.has(item.date)) {
        collapsedDates.value.add(item.date);
        initializedDates.value.add(item.date);
    }
});

const showSearch = ref(false);
const searchQuery = ref('');
const itemPagination = ref({ current_page: 1, last_page: 1 });
const batchInput = ref('');

const form = ref({
    supplement: '', target_remarks: '', content: '',
    date: new Date().toISOString().split('T')[0], master_id: null, items: [], 
    remarks: '', items_footer_remarks: '', user_id: 1, dharma_name_ids: []
});

const dharmaSearchQuery = ref('');

const folders = ref([
    { id: 1, name: '老祖仙師' }, { id: 2, name: '元始仙師' }, { id: 3, name: '道祖仙師' },
    { id: 4, name: '靈寶仙師' }, { id: 5, name: '父皇' }, { id: 6, name: '太宰仙師' },
    { id: 7, name: '太子' }, { id: 8, name: '閻王仙師' }, { id: 0, name: '父皇仙師每日開示' }
]);

const masterNameInput = ref('');

// Computed
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

const addActions = computed(() => [
    { 
        label: '逐筆新增', 
        description: '錄入單筆開示及賜降紀錄',
        icon: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>',
        colorClass: 'bg-indigo-50 text-indigo-600',
        handler: () => { showAdd(); entryTab.value = 'single'; showAddMenu.value = false; } 
    },
    { 
        label: '多筆一次新增', 
        description: '批次貼上文字清單匯入',
        icon: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>',
        colorClass: 'bg-blue-50 text-blue-600',
        handler: () => { showAdd(); entryTab.value = 'batch'; showAddMenu.value = false; } 
    },
    { 
        label: '複製 LINE', 
        description: '將紀錄轉為文字複製貼上',
        icon: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>',
        colorClass: 'bg-purple-50 text-purple-600',
        handler: copyAllToLine 
    },
    { 
        label: '匯出 EXCEL', 
        description: '下載完整開示紀錄報表',
        icon: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 17v-4m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>',
        colorClass: 'bg-emerald-50 text-emerald-600',
        handler: exportListExcel 
    },
    ...((currentFolder.value?.id === 0 || currentFolder.value?.id === '0') ? [{ 
        label: allExpanded.value ? '收起清單' : '展開清單', 
        description: allExpanded.value ? '切換為精簡模式' : '展開所有詳細開示',
        icon: allExpanded.value 
            ? '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 15l7-7 7 7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>'
            : '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>',
        colorClass: allExpanded.value ? 'bg-rose-50 text-rose-600' : 'bg-amber-50 text-amber-600',
        handler: () => { allExpanded.value = !allExpanded.value; showAddMenu.value = false; } 
    }] : [])
]);

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
        const res = await axios.get('/teachings', { params: { master_id: currentFolder.value?.id, page: page } });
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
    saving.value = true;
    try {
        const mid = form.value.master_id || currentFolder.value?.id;
        const finalMasterId = (mid == 0) ? null : mid;
        const payload = { ...form.value, master_id: finalMasterId, user_id: 1 };
        if (editingId.value) await axios.put(`/teachings/${editingId.value}`, payload);
        else await axios.post('/teachings', payload);
        alert('存檔成功！');
        addMode.value = false; itemsDetailMode.value = false; editingId.value = null;
        form.value = { supplement: '', target_remarks: '', content: '', date: new Date().toISOString().split('T')[0], master_id: null, items: [], remarks: '', items_footer_remarks: '', user_id: 1, dharma_name_ids: [] };
        dharmaSearchQuery.value = '';
        fetchItems(1);
    } catch (e) { alert('儲存失敗'); } finally { saving.value = false; }
};

const editItem = (item) => { 
    editingId.value = item.id; 
    form.value = { ...item, dharma_name_ids: item.dharma_names.map(dn => dn.id) }; 
    masterNameInput.value = getMasterName(item.master_id); 
    addMode.value = true; 
};

const deleteItem = async (id) => { if (!confirm('確定刪除？')) return; try { await axios.delete(`/teachings/${id}`); fetchItems(1); } catch (e) { alert('刪除失敗'); } };

const duplicateItem = (item) => { 
    form.value = { ...item, id: null, date: new Date().toISOString().split('T')[0], dharma_name_ids: item.dharma_names.map(dn => dn.id) }; 
    editingId.value = null; addMode.value = true; 
};

const removeMagicItem = (idx) => { form.value.items.splice(idx, 1); };

function stageContent() {
    if (!newItemSubName.value) return;
    stagedContents.value.push({
        name: newItemSubName.value,
        details: newItemDetailsExtraDays.value ? formatValue(newItemDetailsExtraDays.value, '天份') : '',
        remarks: newItemRemarks.value
    });
    newItemSubName.value = '';
    newItemDetailsExtraDays.value = '';
    newItemRemarks.value = '';
}

function addNewItemQuickly() {
    if (!newItemName.value) return;

    // Standard items logic
    let finalDetails = '';
    const baseVal = newItemSubDetails.value || newItemDays.value || '';
    if (baseVal === '1' || baseVal === 1) finalDetails = '一次性';
    else if (baseVal) finalDetails = `${baseVal}天份`;

    const mainItem = {
        treasure_name: newItemName.value,
        details: finalDetails,
        main_remarks: newItemMainRemarks.value || '',
    };

    // If we have staged contents, commit them as sub-items
    if (stagedContents.value.length > 0) {
        stagedContents.value.forEach((sc) => {
            const combinedRemarks = [sc.remarks, mainItem.main_remarks].filter(r => r).join(' ');
            form.value.items.push({
                treasure_name: mainItem.treasure_name,
                details: sc.details || mainItem.details,
                name: sc.name,
                sub_name: combinedRemarks
            });
        });
    } else {
        // Direct commit if no staged contents
        const combinedRemarks = [newItemRemarks.value, mainItem.main_remarks].filter(r => r).join(' ');
        const extraDetails = newItemDetailsExtraDays.value ? formatValue(newItemDetailsExtraDays.value, '天份') : '';
        const combinedDetails = [mainItem.details, extraDetails].filter(d => d).join(' ');
        
        form.value.items.push({
            treasure_name: mainItem.treasure_name,
            details: combinedDetails,
            name: newItemSubName.value || '',
            sub_name: combinedRemarks
        });
    }

    // Reset all
    newItemName.value = '';
    newItemDays.value = '';
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
    const name = formatTreasureName(item.items[0].treasure_name || item.items[0].name); 
    return name + (item.items.length > 1 ? ` 等${item.items.length}項` : ''); 
};

const getFirstItemNameOnly = (item) => {
    if (!item?.items || item.items.length === 0) return '';
    return formatTreasureName(item.items[0].treasure_name || item.items[0].name);
};

const formatTreasureName = (name) => name ? name.split(':').pop() : '';

const getMasterName = (id) => {
    if (!id || id == 0) return '父皇仙師';
    const m = masters.value.find(x => x.id == id);
    return m ? m.name : '未指定';
};

const getItemCount = (id) => markings.value?.[id] || 0; // Simplified
const markings = ref({});

const toggleExpand = (id) => { 
    if (focusedId.value === id) {
        focusedId.value = null;
    } else {
        allExpanded.value = false;
        focusedId.value = id;
    }
};

const handleBack = () => { if (addMode.value) { addMode.value = false; editingId.value = null; } else if (currentFolder.value) { currentFolder.value = null; } else { emit('goHome'); } };

const downloadTeaching = (item) => {
    const treasureText = item.items && item.items.length > 0 
        ? '\n\n賜降：\n' + item.items.map(i => (i.treasure_name || '法寶') + (i.name ? ' - ' + i.name : '') + (i.details ? ' / ' + i.details : '')).join('\n')
        : '';
    const text = `${item.master?.name || '仙師'} 開示\n日期：${item.date}\n\n${item.content}${treasureText}\n\n完畢！`;
    const blob = new Blob([text], { type: 'text/plain' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `開示紀錄_${item.date}.txt`;
    a.click();
    window.URL.revokeObjectURL(url);
};

const copyToLine = (item) => {
    const grouped = groupItems(item.items);
    let treasureLines = [];
    Object.keys(grouped).forEach((gName, gIdx) => {
        const group = grouped[gName];
        let line = `${gIdx + 1}. ${gName}`;
        if (group.length === 1) {
            line += `: ${group[0].name || ''} ${group[0].sub_name || ''} ${group[0].details || ''}`.replace(/\s+/g, ' ').trim();
            treasureLines.push(line);
        } else {
            treasureLines.push(line);
            group.forEach(m => {
                if (m.name || m.sub_name) {
                    treasureLines.push(`   內容物:${m.name}${m.details ? ':' + m.details : ''}${m.sub_name ? ' (' + m.sub_name + ')' : ''}`);
                }
            });
        }
    });
    const treasureText = treasureLines.length > 0 ? '\n\n賜降：\n' + treasureLines.join('\n') : '';
    const text = `${item.master?.name || (item.master_name || '仙師')} 開示 (${item.date})\n\n${item.content}${treasureText}\n\n完畢！`;
    
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

const toggleDateCollapse = (date) => {
    if (collapsedDates.value.has(date)) {
        collapsedDates.value.delete(date);
    } else {
        collapsedDates.value.add(date);
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
            const dn = item.dharma_names.map(d => d.name).join(', ') || '全員';
            const grouped = groupItems(item.items);
            let treasLines = [];
            Object.keys(grouped).forEach((gName, gIdx) => {
                const group = grouped[gName];
                let line = `${gIdx + 1}. ${gName}`;
                if (group.length === 1) {
                    line += `: ${group[0].name || ''} ${group[0].sub_name || ''} ${group[0].details || ''}`.replace(/\s+/g, ' ').trim();
                    treasLines.push(line);
                } else {
                    treasLines.push(line);
                    group.forEach(m => {
                        if (m.name || m.sub_name) treasLines.push(`   內容物:${m.name}${m.details ? ':' + m.details : ''}${m.sub_name ? ' (' + m.sub_name + ')' : ''}`);
                    });
                }
            });
            const treasuresStr = treasLines.join('\n');
            return `【${item.date}】${dn}\n${item.content}${treasuresStr ? '\n賜降：\n'+treasuresStr : ''}\n\n完畢！`;
        }).join('\n\n---\n\n');
        
        await navigator.clipboard.writeText(text);
        alert('已複製完整清單至剪貼簿');
    } catch (e) { alert('複製失敗'); } finally { loading.value = false; }
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

const showAdd = () => {
    editingId.value = null;
    form.value = {
        supplement: '', target_remarks: '', content: '',
        date: new Date().toISOString().split('T')[0], master_id: null, items: [], 
        remarks: '', items_footer_remarks: '', user_id: 1, dharma_name_ids: []
    };
    stagingItems.value = [];
    dharmaSearchQuery.value = '';
    masterNameInput.value = '';

    if (currentFolder.value) {
        if (currentFolder.value.id === 0 || currentFolder.value.id === '0') {
            form.value.master_id = 5;
            form.value.master_name = '父皇仙師';
        } else {
            form.value.master_id = currentFolder.value.id;
            form.value.master_name = currentFolder.value.name;
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
