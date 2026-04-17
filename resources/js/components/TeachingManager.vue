<template>
    <div class="bg-white min-h-screen flex justify-center text-slate-800">
        <div class="bg-white min-h-screen relative w-full shadow-sm flex flex-col">
            <!-- Header -->
            <div v-if="currentFolder" class="border-b border-gray-100 flex items-center justify-center bg-white/80 backdrop-blur-md sticky top-0 z-10" style="padding: 12px 10px 10px 10px;">
                <h2 class="text-xl font-medium font-outfit tracking-tight text-black">
                    <span>{{ currentFolder ? '父皇仙師開示 - ' + (currentFolder.id === 0 ? '每日開示' : currentFolder.name) : '父皇仙師開示專區' }}</span>
                </h2>
            </div>

            <!-- Level 1: Folders -->
            <div v-if="!currentFolder" class="min-h-screen bg-white">
                <div class="px-6 py-4 text-center">
                    <h1 class="text-2xl font-normal text-slate-800 tracking-tight">父皇仙師開示專區</h1>
                </div>
                <div class="grid grid-cols-2 gap-[10px] p-4 place-items-center">
                    <button v-for="(folder, idx) in folders" :key="folder.id" 
                        @click="currentFolder = folder"
                        class="flex flex-col items-center justify-center bg-transparent transition-all active:scale-95 border border-[rgb(255,215,0)] rounded-xl group p-2 w-[120px] h-[120px] relative">
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
                            <div v-if="getItemCount(folder.id) > 0" class="absolute top-[2px] right-[-5px] bg-red-500 text-white text-[10px] font-black px-2 py-0.5 rounded-full shadow-lg border-2 border-white">
                                {{ getItemCount(folder.id) }}
                            </div>
                        </div>
                        <span class="mt-[-2px] text-[16px] font-normal text-slate-700 leading-tight text-center">
                            <span v-if="folder.id === 0" class="text-red-500">父皇仙師<br>每日開示</span>
                            <span v-else>{{ folder.name }}</span>
                        </span>
                    </button>
                </div>
                <div class="mt-12 flex justify-center pb-32">
                    <button @click="$emit('goHome')" class="text-slate-300 hover:text-slate-500 transition-colors flex items-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" /></svg>
                        <span class="text-xs font-medium tracking-widest uppercase">返回專區列表</span>
                    </button>
                </div>
            </div>

            <!-- Level 2: List & Add View -->
            <template v-else>
                <!-- Add View (Modal-like) -->
                <div v-if="addMode" class="fixed inset-0 z-[200] bg-white flex justify-center">
                    <div class="bg-white w-full h-full relative flex flex-col overflow-y-auto">
                        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-white sticky top-0 z-[210]">
                            <div class="flex items-center flex-1">
                                <h2 class="text-[19px] font-normal text-slate-800 tracking-tight shrink-0">父皇仙師每日開示</h2>
                                <span v-if="editingId" class="text-indigo-400 ml-3 font-normal text-sm bg-indigo-50 px-2 py-0.5 rounded-full">修改中</span>
                                <div class="flex bg-slate-100 p-0.5 rounded-xl ml-auto">
                                    <button @click="entryTab = 'single'" :class="entryTab === 'single' ? 'bg-white shadow-sm text-indigo-600' : 'text-slate-500'" class="px-3 py-1.5 text-xs rounded-lg transition-all">逐筆</button>
                                    <button @click="entryTab = 'batch'" :class="entryTab === 'batch' ? 'bg-white shadow-sm text-indigo-600' : 'text-slate-500'" class="px-3 py-1.5 text-xs rounded-lg transition-all ml-0.5">批量</button>
                                </div>
                            </div>
                            <button @click="handleBack" class="text-slate-400 text-sm font-normal pl-4">離開</button>
                        </div>
                        <div class="p-4 space-y-4 pb-32 flex-1">
                            <template v-if="entryTab === 'single'">
                                <div class="grid grid-cols-2 gap-3">
                                    <div class="bg-blue-50/50 rounded-2xl border border-blue-100/30 py-1 pl-2 pr-1 flex items-center h-12">
                                        <label class="text-[14px] text-[#94a3b8] w-10 text-left shrink-0 font-normal">日期</label>
                                        <input v-model="form.date" type="date" class="flex-1 bg-transparent border-none text-[13px] focus:ring-0 outline-none p-0 ml-1 pr-8 min-w-0 font-normal">
                                    </div>
                                    <div class="bg-blue-50/50 rounded-2xl border border-blue-100/30 py-1 pl-2 pr-1 flex items-center h-12">
                                        <label class="text-[14px] text-[#94a3b8] w-10 text-left shrink-0 font-normal">仙師</label>
                                        <input v-model="masterNameInput" list="master-list" @change="resolveMasterId" placeholder="選擇或輸入..." class="flex-1 bg-transparent border-none text-[16px] focus:ring-0 outline-none p-0 ml-1 min-w-0 font-normal">
                                        <datalist id="master-list">
                                            <option v-for="m in masters" :key="m.id" :value="m.name" />
                                        </datalist>
                                    </div>
                                </div>
                                
                                <!-- Dharma Name Selection Trigger Area -->
                                <button @click="showDharmaPicker = true" 
                                        class="w-full bg-slate-50 rounded-2xl border border-dashed border-slate-200 p-4 text-left transition-all active:scale-[0.98]">
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="text-[13px] text-slate-500 font-bold uppercase tracking-wider">開示對象 (法號)</span>
                                        <span class="text-[12px] text-indigo-600 font-bold bg-indigo-50 px-3 py-1 rounded-full">點擊選擇人員</span>
                                    </div>
                                    <div v-if="form.dharma_name_ids.length > 0" class="flex flex-wrap gap-1.5 min-h-[40px]">
                                        <span v-for="dnId in form.dharma_name_ids.slice(0, 10)" :key="dnId" 
                                              class="bg-white border border-slate-100 shadow-sm text-[12px] px-2.5 py-1 rounded-lg text-slate-600">
                                            {{ getDharmaNameText(dnId) }}
                                        </span>
                                        <span v-if="form.dharma_name_ids.length > 10" class="text-slate-400 text-[12px] flex items-center pl-1">
                                            + {{ form.dharma_name_ids.length - 10 }} ...
                                        </span>
                                    </div>
                                    <div v-else class="text-slate-300 text-[14px] italic py-3">尚未選擇任何對象 (預設為所有人)</div>
                                </button>

                                <div class="bg-blue-50/50 rounded-2xl border border-blue-100/30 py-1 pl-2 pr-1 flex items-center h-12">
                                    <label class="text-[14px] text-[#94a3b8] w-10 text-left shrink-0 font-normal">詳情</label>
                                    <input v-model="form.supplement" type="text" placeholder="親友 / 信眾 / 特定事件" class="flex-1 bg-transparent border-none text-[16px] focus:ring-0 outline-none p-0 ml-1 min-w-0 font-normal">
                                </div>
                                
                                <div class="bg-blue-50/50 rounded-2xl border border-blue-100/30 py-2 pl-2 pr-3 flex items-start">
                                    <label class="text-[14px] text-[#94a3b8] w-10 text-left shrink-0 mt-0.5 font-normal">開示</label>
                                    <textarea v-model="form.content" rows="4" @input="e => { e.target.style.height = 'auto'; e.target.style.height = e.target.scrollHeight + 'px' }" placeholder="輸入具體內容描述..." class="flex-1 bg-transparent border-none text-[16px] focus:ring-0 outline-none p-0 ml-1 resize-none overflow-hidden min-h-[80px] font-normal"></textarea>
                                </div>

                                <div class="mt-6">
                                    <button @click.stop="itemsDetailMode = true" class="w-full bg-indigo-50/50 border border-indigo-100 text-indigo-600 rounded-2xl py-3.5 shadow-sm active:scale-95 transition-all text-[16px] font-normal tracking-wider">
                                        <template v-if="generateSummary(form)">
                                            降寶錄入 ({{ generateSummary(form) }})
                                        </template>
                                        <template v-else>
                                            降寶錄入
                                        </template>
                                    </button>
                                </div>
                            </template>
                            <template v-else>
                                <textarea v-model="batchInput" rows="15" class="w-full bg-slate-50 rounded-xl p-3 border-none text-[14px] outline-none resize-none" placeholder="批量輸入開示內容..."></textarea>
                            </template>

                            <div class="py-12 flex flex-col items-center">
                                <button @click="saveItem" :disabled="saving" class="bg-yellow-400 text-yellow-950 rounded-[28px] px-16 py-3.5 shadow-lg active:scale-95 disabled:opacity-50">
                                    {{ saving ? '正在儲存中...' : '確認存檔' }}
                                </button>
                                <div v-if="saving" class="mt-4 text-[13px] text-slate-400 animate-pulse">正在寫入雲端紀錄...</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Magic Items Detail View -->
                <div v-if="itemsDetailMode" class="fixed inset-0 z-[500] bg-white flex flex-col p-4 overflow-y-auto animate-fade-in">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xl font-normal text-slate-900">降寶內容</h3>
                        <button @click="itemsDetailMode = false" class="bg-indigo-50 text-indigo-600 px-4 py-2 rounded-2xl text-[13px] font-bold">完成</button>
                    </div>
                    <div class="space-y-6">
                        <div>
                            <label class="text-[12px] text-slate-400 block mb-2 px-1 font-bold">法寶名稱</label>
                            <div class="flex items-center space-x-2 mb-4">
                                <input v-model="tempItem.treasure_name" type="text" list="t-list" placeholder="選擇法寶..." class="flex-1 py-3 px-4 bg-white rounded-2xl text-[15px] border border-slate-100 outline-none">
                                <datalist id="t-list"><option v-for="t in treasures" :key="t.id" :value="t.name"/></datalist>
                                <button @click="pushTempItem" class="text-red-500 text-3xl font-bold px-2">+</button>
                            </div>
                            <label class="text-[12px] text-slate-400 block mb-2 px-1">內容物名稱</label>
                            <div class="flex items-center space-x-2">
                                <input v-model="tempItem.name" type="text" placeholder="輸入內容物..." class="flex-1 py-3 px-4 bg-white rounded-2xl text-[15px] border border-slate-100 outline-none">
                                <button @click="addToStaging" class="text-red-500 text-3xl font-bold px-2">+</button>
                            </div>
                        </div>
                        <div class="grid grid-cols-3 gap-2">
                            <div class="col-span-1">
                                <label class="text-[11px] block mb-1 text-slate-400">作法</label>
                                <input v-model="tempItem.method" type="text" class="w-full h-11 bg-white rounded-2xl px-3 text-[14px] border border-slate-100">
                            </div>
                            <div class="col-span-1">
                                <label class="text-[11px] block mb-1 text-slate-400">數量/尺寸</label>
                                <input v-model="tempItem.quantity" type="text" class="w-full h-11 bg-white rounded-2xl px-3 text-[14px] border border-slate-100">
                            </div>
                            <div class="col-span-1">
                                <label class="text-[11px] block mb-1 text-slate-400">天數</label>
                                <input v-model="tempItem.duration" type="text" class="w-full h-11 bg-white rounded-2xl px-3 text-[14px] border border-slate-100">
                            </div>
                        </div>
                        <div v-if="stagingItems.length > 0" class="bg-slate-50 rounded-2xl p-4 space-y-2 border border-dashed border-slate-200">
                            <div v-for="(sItem, sIdx) in stagingItems" :key="sIdx" class="flex items-center justify-between text-sm">
                                <span>{{ sItem.name }} / {{ sItem.method }}</span>
                                <button @click="stagingItems.splice(sIdx, 1)" class="text-red-300">✕</button>
                            </div>
                        </div>
                        <button @click="commitStaging" class="w-full bg-blue-50 text-blue-600 py-3 rounded-2xl font-bold">確認添加至清單</button>
                        
                        <div class="pt-4 border-t border-slate-100">
                            <div class="text-sm font-bold text-slate-400 mb-3">已加入明細 ({{ form.items.length }})</div>
                            <div v-for="(group, gName) in groupedPendingItems" :key="gName" class="mb-4">
                                <div class="font-bold text-slate-900 border-l-4 border-indigo-400 pl-2 mb-2">{{ formatTreasureName(gName) }}</div>
                                <div v-for="(m, midx) in group" :key="midx" class="pl-4 text-sm text-slate-600 flex justify-between items-center py-1">
                                    <span>{{ m.name || '核心' }} - {{ m.method }} {{ m.quantity }}</span>
                                    <button @click="removeMagicItem(m.originalIdx)" class="text-red-200">✕</button>
                                </div>
                            </div>
                        </div>

                        <div class="pt-6">
                            <label class="text-[12px] text-slate-400 block mb-2 px-1 font-bold">結尾備註</label>
                            <textarea v-model="form.items_footer_remarks" rows="2" class="w-full py-3 px-4 bg-slate-50 rounded-2xl text-[14px] border border-slate-100 outline-none resize-none"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Dharma Picker Modal -->
                <div v-if="showDharmaPicker" class="fixed inset-0 z-[400] bg-black/40 flex items-end justify-center sm:items-center animate-fade-in">
                    <div class="bg-white w-full h-[85vh] sm:h-[70vh] sm:max-w-xl rounded-t-[32px] sm:rounded-[32px] flex flex-col shadow-2xl overflow-hidden animate-slide-up">
                        <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between shrink-0">
                            <div>
                                <h3 class="text-[18px] font-bold text-slate-900">選擇對象</h3>
                                <p class="text-[12px] text-slate-400">可搜尋「法號」或「群組名稱」</p>
                            </div>
                            <button @click="showDharmaPicker = false" class="bg-slate-100 text-slate-500 w-8 h-8 rounded-full flex items-center justify-center active:scale-90 transition-transform">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2" /></svg>
                            </button>
                        </div>
                        
                        <div class="px-6 py-4 shrink-0 bg-slate-50/50">
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-300">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="2" /></svg>
                                </span>
                                <input v-model="pickerSearch" type="text" placeholder="輸入法號或群組名稱進行搜尋..." 
                                       class="w-full bg-white border border-slate-200 rounded-2xl py-3.5 pl-11 pr-4 text-[15px] focus:ring-4 focus:ring-indigo-100 transition-all outline-none shadow-sm">
                            </div>
                        </div>

                        <div class="flex-1 overflow-y-auto px-6 py-2 custom-scrollbar">
                            <!-- Group Results (Priority) -->
                            <div v-if="filteredGroups.length > 0" class="mb-6 mt-2">
                                <div class="text-[12px] text-indigo-500 font-bold px-1 mb-3 tracking-widest uppercase flex items-center">
                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" stroke-width="2"/></svg>
                                    群組搜尋結果
                                </div>
                                <div class="space-y-2">
                                    <div v-for="group in filteredGroups" :key="group.id" 
                                         class="border border-slate-100 rounded-2xl overflow-hidden bg-white shadow-sm ring-1 ring-slate-50">
                                        <div @click="toggleGroupAccordion(group.id)" 
                                             class="flex items-center justify-between px-4 py-4 cursor-pointer active:bg-slate-50">
                                            <div class="flex items-center space-x-3">
                                                <div @click.stop="toggleGroupSelection(group)" 
                                                     class="w-6 h-6 rounded-lg border flex items-center justify-center transition-all shadow-sm"
                                                     :class="isGroupFullySelected(group) ? 'bg-indigo-600 border-indigo-600' : 'bg-white border-slate-200'">
                                                    <svg v-if="isGroupFullySelected(group)" class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" /></svg>
                                                </div>
                                                <span class="text-[16px] font-bold" :class="isGroupFullySelected(group) ? 'text-indigo-600' : 'text-slate-800'">{{ group.name }}</span>
                                                <span class="text-[11px] bg-slate-50 text-slate-400 px-2.5 py-1 rounded-full font-bold">{{ group.dharma_names.length }}人</span>
                                            </div>
                                            <svg :class="expandedGroupPicker === group.id ? 'rotate-180' : ''" class="w-4 h-4 text-slate-300 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" /></svg>
                                        </div>
                                        <div v-if="expandedGroupPicker === group.id || pickerSearch" class="bg-indigo-50/20 px-4 py-3 border-t border-indigo-50/50 grid grid-cols-2 gap-2 animate-fade-in">
                                            <button v-for="member in group.dharma_names" :key="member.id" 
                                                    @click="toggleDharmaName(member.id)"
                                                    class="flex items-center p-2 rounded-xl text-[14px] transition-all bg-white shadow-sm"
                                                    :class="form.dharma_name_ids.includes(member.id) ? 'text-indigo-600 font-bold border border-indigo-200' : 'text-slate-600 border border-transparent'">
                                                <div class="w-2.5 h-2.5 rounded-full mr-2 shrink-0 shadow-sm" :class="form.dharma_name_ids.includes(member.id) ? 'bg-indigo-500' : 'bg-slate-200'"></div>
                                                <span class="truncate">{{ member.name }}</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Individual Results -->
                            <div class="mb-4">
                                <div class="text-[12px] text-slate-400 font-bold px-1 mb-3 tracking-widest uppercase flex items-center">
                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" stroke-width="2"/></svg>
                                    {{ pickerSearch ? '法號搜尋結果' : '所有法號 (個別篩選)' }}
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <button v-for="dn in filteredPickerResults" :key="dn.id" 
                                            @click="toggleDharmaName(dn.id)"
                                            :class="form.dharma_name_ids.includes(dn.id) ? 'bg-indigo-50 border-indigo-200 text-indigo-700 shadow-sm' : 'bg-slate-50 border-slate-50 text-slate-600'"
                                            class="flex items-center px-4 py-3.5 rounded-2xl border transition-all text-[15px] font-medium active:scale-[0.98]">
                                        <div class="w-5 h-5 rounded-md border flex items-center justify-center mr-3 shrink-0 transition-all font-bold"
                                             :class="form.dharma_name_ids.includes(dn.id) ? 'bg-indigo-600 border-indigo-600' : 'bg-white border-slate-200 shadow-sm'">
                                            <svg v-if="form.dharma_name_ids.includes(dn.id)" class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" /></svg>
                                        </div>
                                        <span class="truncate">{{ dn.name }}</span>
                                    </button>
                                </div>
                                <div v-if="filteredPickerResults.length === 0 && filteredGroups.length === 0" class="py-12 text-center text-slate-300 italic">查無符合條件的法號或群組</div>
                            </div>
                        </div>

                        <div class="p-6 border-t border-slate-100 flex items-center space-x-3 shrink-0 bg-white shadow-2xl">
                            <button @click="form.dharma_name_ids = []" class="flex-1 py-4 bg-slate-50 text-slate-500 rounded-2xl font-bold active:scale-95 transition-all">全部清除</button>
                            <button @click="showDharmaPicker = false" class="flex-[2] py-4 bg-indigo-600 text-white rounded-2xl font-bold shadow-lg shadow-indigo-100 active:scale-95 transition-all">確認選擇</button>
                        </div>
                    </div>
                </div>

                <!-- Main List View (Accordion by Date) -->
                <div v-show="!addMode" class="pb-32 flex-1 overflow-y-auto" @scroll="handleScroll">
                    <div v-if="loading && visibleDates.length === 0" class="text-center py-12 text-slate-400 text-xs tracking-widest">正在同步資料紀錄...</div>
                    <div v-else class="space-y-px">
                        <div v-for="dateRow in visibleDates" :key="dateRow.date" class="bg-white border-b border-slate-50">
                            <!-- Date Accordion Header -->
                            <div @click="toggleDate(dateRow.date)" class="px-6 py-4 flex items-center justify-between cursor-pointer active:bg-slate-50">
                                <div class="flex items-center space-x-3">
                                    <div class="w-2 h-2 rounded-full" :class="expandedDates.has(dateRow.date) ? 'bg-indigo-500' : 'bg-slate-200'"></div>
                                    <span class="text-[17px] font-medium" :class="expandedDates.has(dateRow.date) ? 'text-indigo-600' : 'text-slate-800'">{{ dateRow.date }}</span>
                                    <span class="bg-slate-100 text-slate-400 text-[10px] px-2 py-0.5 rounded-full font-bold">{{ dateRow.count }} 筆</span>
                                </div>
                                <svg :class="expandedDates.has(dateRow.date) ? 'rotate-180' : ''" class="w-5 h-5 text-slate-300 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </div>

                            <div v-if="expandedDates.has(dateRow.date)" class="bg-slate-50/30">
                                <div v-if="dateLoading[dateRow.date]" class="p-8 text-center text-xs text-slate-300">載入中...</div>
                                <div v-else-if="dateItems[dateRow.date]?.length">
                                    <div v-for="item in (searchQuery ? filteredDateItems(dateRow.date) : dateItems[dateRow.date])" :key="item.id" 
                                         @click="toggleExpand(item.id)" class="px-6 py-5 border-b border-slate-50 last:border-0 cursor-pointer hover:bg-white transition-colors">
                                        <div class="flex justify-between items-start text-[11px] text-slate-400 uppercase mb-2">
                                            <div class="flex flex-wrap gap-1">
                                                <span v-for="dn in item.dharma_names" :key="dn.id" class="bg-indigo-50 text-indigo-400 px-1.5 py-0.5 rounded text-[9px]">{{ dn.name }}</span>
                                                <span v-if="(!item.dharma_names || item.dharma_names.length === 0)" class="bg-slate-50 text-slate-400 px-1.5 py-0.5 rounded text-[9px]">所有人</span>
                                            </div>
                                            <div class="flex space-x-4">
                                                <span class="text-[9px] text-slate-300 mr-2">{{ getMasterName(item.master_id) }}</span>
                                                <button @click.stop="duplicateItem(item)" class="text-blue-400">複製</button>
                                                <button @click.stop="editItem(item)" class="text-indigo-400">編輯</button>
                                                <button @click.stop="deleteItem(item.id)" class="text-red-300">刪除</button>
                                            </div>
                                        </div>
                                        <div class="text-[17px] font-medium text-slate-900 line-clamp-1" v-if="item.content">{{ item.content.split('\n')[0] }}</div>
                                        <div class="text-[15px] text-slate-600 mt-2 whitespace-pre-wrap leading-relaxed" :class="focusedId == item.id ? '' : 'line-clamp-2'">{{ item.content }}</div>
                                        
                                        <div v-if="focusedId == item.id" class="mt-4 pt-4 border-t border-slate-100 animate-fade-in">
                                            <div v-if="(!item.items || item.items.length === 0) && !item.items_footer_remarks && !item.remarks" class="text-xs text-slate-300 italic py-2">無明細內容</div>
                                            <div v-else class="space-y-4">
                                                <div v-if="generateSummary(item)" class="p-3 bg-white border border-slate-100 rounded-xl text-sm text-slate-700 shadow-sm">{{ generateSummary(item) }}</div>
                                                <div v-for="(group, gName) in groupItems(item.items)" :key="gName" class="space-y-1">
                                                    <div class="text-sm font-bold text-slate-900 border-l-2 border-indigo-400 pl-2">{{ formatTreasureName(gName) }}</div>
                                                    <div v-for="(m, midx) in group" :key="midx" class="pl-4 text-sm text-slate-600">
                                                        <span>{{ m.name || '核心' }} - {{ m.method }} {{ m.quantity }} {{ m.duration }}</span>
                                                    </div>
                                                </div>
                                                <div v-if="item.items_footer_remarks" class="p-3 bg-indigo-50/30 rounded-xl text-sm text-slate-500 italic">{{ item.items_footer_remarks }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div v-else class="p-8 text-center text-xs text-slate-300">查無紀錄</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- (Bottom Nav remains same ...) -->
                <div class="fixed bottom-0 left-0 right-0 z-[100] h-[7vh] bg-white border-t border-slate-100 grid grid-cols-5 items-center px-4">
                    <div class="flex justify-center"><button @click="handleBack" class="text-slate-400"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="2" /></svg></button></div>
                    <div class="flex justify-center"><button @click="$emit('goHome')" class="text-slate-400"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M3 12l9-9 9 9M5 10v10h14V10" stroke-width="2" /></svg></button></div>
                    <div class="flex justify-center"><button v-if="currentFolder" @click="addMode = !addMode" :class="addMode ? 'bg-slate-800 rotate-45' : 'bg-indigo-600'" class="w-9 h-9 rounded-xl flex items-center justify-center text-white transition-all shadow-lg active:scale-95"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4" stroke-width="4" /></svg></button></div>
                    <div class="flex justify-center"><button @click="showSearch = !showSearch" :class="showSearch ? 'text-indigo-600' : 'text-slate-400'"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="2" /></svg></button></div>
                    <div class="flex justify-center"><button @click="downloadList" :disabled="!currentFolder" class="text-slate-400 disabled:opacity-20"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4" stroke-width="2" /></svg></button></div>
                </div>
                <search-component v-if="showSearch" v-model="searchQuery" :show="showSearch" @close="showSearch = false" />
            </template>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, defineEmits, watch } from 'vue';
import axios from 'axios';
import SearchComponent from './SearchComponent.vue';

const emit = defineEmits(['goHome']);
const currentFolder = ref(null);
const addMode = ref(false);
const teachings = ref([]);
const masters = ref([]);
const dharmaNames = ref([]);
const groups = ref([]);
const treasures = ref([]);
const loading = ref(false);
const entryTab = ref('single');
const saving = ref(false);
const editingId = ref(null);
const focusedId = ref(null);
const itemsDetailMode = ref(false);
const tempItem = ref({ treasure_name: '', name: '', method: '', duration: '', quantity: '', sub_name: '' });
const stagingItems = ref([]);

// Modal & Picker Logic
const showDharmaPicker = ref(false);
const pickerSearch = ref('');
const expandedGroupPicker = ref(null);
const toggleGroupAccordion = (id) => { expandedGroupPicker.value = expandedGroupPicker.value === id ? null : id; };

const visibleDates = ref([]);
const datePagination = ref({ current_page: 1, last_page: 1 });
const dateItems = ref({}); 
const dateLoading = ref({});
const expandedDates = ref(new Set());
const batchInput = ref('');
const form = ref({
    supplement: '', target_remarks: '', content: '',
    date: new Date().toISOString().split('T')[0], master_id: null, items: [], 
    remarks: '', items_footer_remarks: '', user_id: 1, dharma_name_ids: []
});

const folders = ref([
    { id: 1, name: '老祖仙師' }, { id: 2, name: '元始仙師' }, { id: 3, name: '道祖仙師' },
    { id: 4, name: '靈寶仙師' }, { id: 5, name: '父皇' }, { id: 6, name: '太宰仙師' },
    { id: 7, name: '太子' }, { id: 8, name: '閻王仙師' }, { id: 0, name: '父皇仙師每日開示' }
]);

const masterNameInput = ref('');
const resolveMasterId = () => {
    const name = masterNameInput.value;
    const m = masters.value.find(x => x.name === name);
    if (m) { form.value.master_id = m.id; return; }
    const f = folders.value.find(x => x.name === name);
    if (f) { form.value.master_id = f.id; return; }
    form.value.master_id = name;
};

// Dharma Picker Functions
const getDharmaNameText = (id) => {
    const dn = dharmaNames.value.find(x => x.id === id);
    return dn ? dn.name : '';
};

// New Unified Search Logic
const filteredPickerResults = computed(() => {
    if (!pickerSearch.value) return dharmaNames.value;
    const q = pickerSearch.value.toLowerCase();
    return dharmaNames.value.filter(dn => dn.name.toLowerCase().includes(q));
});

const filteredGroups = computed(() => {
    if (!pickerSearch.value) return groups;
    const q = pickerSearch.value.toLowerCase();
    return groups.value.filter(g => g.name.toLowerCase().includes(q));
});

const toggleDharmaName = (id) => {
    const idx = form.value.dharma_name_ids.indexOf(id);
    if (idx > -1) form.value.dharma_name_ids.splice(idx, 1);
    else form.value.dharma_name_ids.push(id);
};

const toggleGroupSelection = (group) => {
    const memberIds = (group.dharma_names || []).map(dn => dn.id);
    const allSelected = memberIds.every(id => form.value.dharma_name_ids.includes(id));
    if (allSelected) {
        form.value.dharma_name_ids = form.value.dharma_name_ids.filter(id => !memberIds.includes(id));
    } else {
        memberIds.forEach(id => {
            if (!form.value.dharma_name_ids.includes(id)) form.value.dharma_name_ids.push(id);
        });
    }
};

const isGroupFullySelected = (group) => {
    const memberIds = (group.dharma_names || []).map(dn => dn.id);
    if (memberIds.length === 0) return false;
    return memberIds.every(id => form.value.dharma_name_ids.includes(id));
};

// Data Fetching
const syncRecords = async () => {
    try {
        const [dnRes, groupRes, masterRes, treasureRes] = await Promise.allSettled([
            axios.get('/api/dharma-names-list'), axios.get('/api/groups-list'),
            axios.get('/api/masters-list'), axios.get('/api/treasures-list')
        ]);
        if (dnRes.status === 'fulfilled') dharmaNames.value = dnRes.value.data;
        if (groupRes.status === 'fulfilled') groups.value = groupRes.value.data || [];
        if (masterRes.status === 'fulfilled') masters.value = masterRes.value.data || [];
        if (treasureRes.status === 'fulfilled') treasures.value = treasureRes.value.data || [];
        if (currentFolder.value) await fetchDates(1);
    } catch (e) { console.error(e); }
};

const fetchDates = async (page = 1) => {
    if (loading.value && page !== 1) return;
    loading.value = true;
    try {
        const res = await axios.get('/teachings', { params: { master_id: currentFolder.value?.id, mode: 'dates', page: page } });
        if (page === 1) { visibleDates.value = res.data.data; expandedDates.value.clear(); }
        else {
            const existing = new Set(visibleDates.value.map(d => d.date));
            res.data.data.forEach(d => { if (!existing.has(d.date)) visibleDates.value.push(d); });
        }
        datePagination.value = { current_page: res.data.current_page, last_page: res.data.last_page };
    } catch (e) { console.error(e); } finally { loading.value = false; }
};

const toggleDate = async (date) => {
    if (expandedDates.value.has(date)) expandedDates.value.delete(date);
    else {
        expandedDates.value.add(date);
        if (!dateItems.value[date]) {
            dateLoading.value[date] = true;
            try {
                const res = await axios.get('/teachings', { params: { master_id: currentFolder.value?.id, date: date } });
                dateItems.value[date] = res.data;
            } catch (e) { console.error(e); } finally { dateLoading.value[date] = false; }
        }
    }
};

const handleScroll = (e) => {
    const { scrollTop, scrollHeight, clientHeight } = e.target;
    if (scrollTop + clientHeight >= scrollHeight - 100) {
        if (!loading.value && datePagination.value.current_page < datePagination.value.last_page) {
            fetchDates(datePagination.value.current_page + 1);
        }
    }
};

const filteredDateItems = (date) => {
    if (!searchQuery.value) return dateItems.value[date] || [];
    const q = searchQuery.value.toLowerCase();
    return (dateItems.value[date] || []).filter(i => (i.content || '').toLowerCase().includes(q));
};

watch(currentFolder, (val) => {
    if (val) { visibleDates.value = []; dateItems.value = {}; dateLoading.value = {}; expandedDates.value.clear(); datePagination.value = { current_page: 1, last_page: 1 }; fetchDates(1); }
});

const saveItem = async () => {
    if (entryTab.value === 'batch' && batchInput.value) form.value.content = batchInput.value;
    if (!form.value.content) return alert('請填寫完整內容');
    saving.value = true;
    try {
        const mid = form.value.master_id || currentFolder.value?.id;
        const finalMasterId = (mid == 0) ? null : mid;
        const payload = { ...form.value, master_id: finalMasterId, master_name: masterNameInput.value, user_id: 1 };
        if (editingId.value) await axios.put(`/teachings/${editingId.value}`, payload);
        else await axios.post('/teachings', payload);
        alert('存檔成功！');
        addMode.value = false; showDharmaPicker.value = false; itemsDetailMode.value = false; editingId.value = null;
        form.value = { supplement: '', target_remarks: '', content: '', date: new Date().toISOString().split('T')[0], master_id: null, items: [], remarks: '', items_footer_remarks: '', user_id: 1, dharma_name_ids: [] };
        dateItems.value[form.value.date] = null;
        if (expandedDates.value.has(form.value.date)) await toggleDate(form.value.date);
        await syncRecords();
    } catch (e) { alert('儲存失敗'); } finally { saving.value = false; }
};

const toggleExpand = (id) => { focusedId.value = focusedId.value == id ? null : id; };
const getItemCount = (id) => teachings.value.filter(t => (id == 0) ? t.master_id == null : (t.master_id == id)).length;
const getMasterName = (id) => {
    if (!id || id == 0) return '每日開示';
    const m = masters.value.find(x => x.id == id);
    return m ? m.name : '未指定';
};
const editItem = (item) => { 
    editingId.value = item.id; 
    form.value = { ...item, dharma_name_ids: item.dharma_names.map(dn => dn.id) }; 
    masterNameInput.value = getMasterName(item.master_id); 
    addMode.value = true; 
};
const deleteItem = async (id) => { if (!confirm('確定刪除？')) return; try { await axios.delete(`/teachings/${id}`); await syncRecords(); dateItems.value = {}; } catch (e) { alert('刪除失敗'); } };
const duplicateItem = (item) => { 
    form.value = { ...item, id: null, date: new Date().toISOString().split('T')[0], dharma_name_ids: item.dharma_names.map(dn => dn.id) }; 
    editingId.value = null; addMode.value = true; 
};
const addToStaging = () => { if (!tempItem.value.name) return alert('請輸入名稱'); stagingItems.value.push({ ...tempItem.value }); tempItem.value = { ...tempItem.value, name: '', method: '', quantity: '', duration: '' }; };
const commitStaging = () => {
    if (stagingItems.value.length === 0) { if (!tempItem.value.name && !tempItem.value.treasure_name) return alert('請輸入名稱'); form.value.items.push({ ...tempItem.value, category: '清煞' }); }
    else { if (tempItem.value.name) stagingItems.value.push({ ...tempItem.value }); form.value.items.push(...stagingItems.value.map(i => ({ ...i, category: '清煞' }))); stagingItems.value = []; }
    tempItem.value = { treasure_name: '', name: '', method: '', duration: '', quantity: '', sub_name: '' };
};
const pushTempItem = commitStaging;
const removeMagicItem = (idx) => { form.value.items.splice(idx, 1); };
const groupItems = (items) => {
    if (!items) return {};
    const groups = {};
    items.forEach((item, index) => { const key = item.treasure_name || '一般法寶'; if (!groups[key]) groups[key] = []; groups[key].push({ ...item, originalIdx: index }); });
    return groups;
};
const groupedPendingItems = computed(() => groupItems(form.value.items));
const generateSummary = (item) => { if (!item?.items || item.items.length === 0) return ''; const name = formatTreasureName(item.items[0].treasure_name || item.items[0].name); return name + (item.items.length > 1 ? ` 等${item.items.length}項` : ''); };
const formatTreasureName = (name) => name ? name.split(':').pop() : '';
const searchQuery = ref('');
const showSearch = ref(false);
const handleBack = () => { if (addMode.value) { addMode.value = false; itemsDetailMode.value = false; editingId.value = null; } else if (currentFolder.value) { currentFolder.value = null; } else { emit('goHome'); } };
onMounted(syncRecords);
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar { width: 5px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }

@keyframes slide-up {
    from { transform: translateY(100%); }
    to { transform: translateY(0); }
}
.animate-slide-up { animation: slide-up 0.3s ease-out; }

@keyframes fade-in {
    from { opacity: 0; }
    to { opacity: 1; }
}
.animate-fade-in { animation: fade-in 0.2s ease-out; }
</style>
