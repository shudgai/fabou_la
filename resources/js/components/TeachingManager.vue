<template>
    <div class="bg-white min-h-screen flex justify-center text-slate-900">
        <div class="bg-white min-h-screen relative w-full shadow-sm flex flex-col font-sans">
            <!-- Header with Back Button -->
            <div v-if="currentFolder" class="border-b border-gray-100 flex items-center bg-white sticky top-0 z-10" style="padding: 12px 10px 10px 10px;">
                <button @click="handleBack" class="text-slate-400 p-2 mr-2 active:scale-90 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" /></svg>
                </button>
                <h2 class="text-[20px] font-medium font-outfit tracking-tight text-slate-800 flex-1 text-center pr-10">
                    <span>{{ currentFolder ? '父皇仙師開示 - ' + (currentFolder.id === 0 ? '每日開示' : currentFolder.name) : '父皇仙師開示專區' }}</span>
                </h2>
            </div>

            <!-- Level 1: Folders -->
            <div v-if="!currentFolder" class="min-h-screen bg-white">
                <div class="px-6 py-4 text-center">
                    <h1 class="text-[20px] font-medium text-slate-800 tracking-tight">父皇仙師開示專區</h1>
                </div>
                <div class="grid grid-cols-2 gap-[10px] p-4 place-items-center">
                    <button v-for="(folder, idx) in folders" :key="folder.id" 
                        @click="currentFolder = folder"
                        class="flex flex-col items-center justify-center bg-transparent transition-all active:scale-95 border border-[rgb(255,215,0)] rounded-xl group p-2 w-[120px] h-[125px] relative">
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
                        <span class="mt-[-2px] text-[17px] font-medium text-slate-900 leading-tight text-center">
                            <span v-if="folder.id === 0" class="text-red-500">父皇仙師<br>每日開示</span>
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
                                <h2 class="text-[20px] font-medium text-slate-800 tracking-tight shrink-0">
                                    {{ editingId ? '修改開示紀錄' : (currentFolder?.id === 0 ? '每日開示錄入' : currentFolder?.name + '錄入') }}
                                </h2>
                                <span v-if="editingId" class="text-indigo-400 ml-3 font-medium text-[14px] bg-indigo-50 px-2 py-0.5 rounded-full">修改中</span>
                                <div class="flex bg-slate-100 p-0.5 rounded-xl ml-auto">
                                    <button @click="entryTab = 'single'" :class="entryTab === 'single' ? 'bg-white shadow-sm text-indigo-600' : 'text-slate-500'" class="px-3 py-1.5 text-[14px] rounded-lg transition-all">逐筆</button>
                                    <button @click="entryTab = 'batch'" :class="entryTab === 'batch' ? 'bg-white shadow-sm text-indigo-600' : 'text-slate-500'" class="px-3 py-1.5 text-[14px] rounded-lg transition-all ml-0.5">批量</button>
                                </div>
                            </div>
                        </div>
                        <div class="p-4 space-y-4 pb-32 flex-1">
                            <template v-if="entryTab === 'single'">
                                <div class="grid grid-cols-2 gap-3">
                                    <div class="bg-blue-50/50 rounded-2xl border border-blue-100/30 py-1 pl-2 pr-3 flex items-center h-12">
                                        <label class="text-[14px] text-[#9ba7b9] w-8 text-left shrink-0 font-medium">日期</label>
                                        <input v-model="form.date" type="date" class="flex-1 bg-transparent border-none text-[14px] text-black focus:ring-0 outline-none p-0 ml-1 min-w-0 font-bold custom-date-input">
                                    </div>
                                    <div class="bg-blue-50/50 rounded-2xl border border-blue-100/30 py-1 pl-2 pr-1 flex items-center h-12">
                                        <label class="text-[14px] text-[#9ba7b9] w-8 text-left shrink-0 font-medium">仙師</label>
                                        <input v-model="masterNameInput" 
                                               list="master-list" 
                                               @change="resolveMasterId" 
                                               placeholder="選擇或輸入..." 
                                               class="flex-1 bg-transparent border-none text-[17px] text-black focus:ring-0 outline-none p-0 ml-1 min-w-0 font-bold">
                                        <datalist id="master-list">
                                            <option value="老祖仙師" />
                                            <option value="元始仙師" />
                                            <option value="道祖仙師" />
                                            <option value="靈寶仙師" />
                                            <option value="父皇仙師" />
                                            <option value="太宰仙師" />
                                            <option value="太子" />
                                            <option value="閻王仙師" />
                                        </datalist>
                                    </div>
                                </div>
                                
                                <div class="bg-blue-50/50 rounded-2xl border border-blue-100/30 p-3.5 space-y-3">
                                    <div class="flex items-center justify-between">
                                        <label class="text-[14px] text-slate-400 font-bold uppercase tracking-wider">開示對象 (法號)</label>
                                        <button @click="showDharmaPicker = true" class="text-[14px] text-indigo-600 font-bold bg-indigo-100/50 px-3 py-1 rounded-full active:scale-95 transition-all">進階選擇器</button>
                                    </div>
                                    <div class="flex items-center">
                                        <input v-model="dharmaSearchQuery" list="dharma-quick-list" @input="handleDharmaSearchInput" :placeholder="form.dharma_name_ids.length > 1 ? `已選擇 ${form.dharma_name_ids.length} 人...` : '輸入法號或群組...'" class="w-full bg-white border border-slate-100 rounded-xl px-4 py-2.5 text-[17px] text-slate-900 outline-none focus:ring-2 focus:ring-indigo-100 transition-all font-medium">
                                        <datalist id="dharma-quick-list">
                                            <option v-for="dn in dharmaNames" :key="'dn'+dn.id" :value="dn.name" />
                                            <option v-for="g in groups" :key="'g'+g.id" :value="g.name" />
                                        </datalist>
                                    </div>
                                    <div v-if="form.dharma_name_ids.length > 1" class="flex flex-wrap gap-1.5 min-h-[40px] pt-1">
                                        <span v-for="dnId in form.dharma_name_ids" :key="dnId" 
                                              class="bg-white border border-slate-100 shadow-sm text-[16px] px-2.5 py-1 rounded-lg text-slate-900 flex items-center">
                                            {{ getDharmaNameText(dnId) }}
                                            <button @click="toggleDharmaName(dnId)" class="ml-1.5 text-slate-300 hover:text-red-400 transition-colors">✕</button>
                                        </span>
                                    </div>
                                    <div v-else-if="form.dharma_name_ids.length === 0" class="text-slate-300 text-[16px] italic py-1">尚未選擇任何對象 (預設為所有人)</div>
                                </div>

                                <div class="bg-blue-50/50 rounded-2xl border border-blue-100/30 py-1 pl-2 pr-1 flex items-center h-12">
                                    <label class="text-[14px] text-slate-400 w-10 text-left shrink-0 font-medium">備註</label>
                                    <input v-model="form.supplement" type="text" placeholder="親友 / 信眾" class="flex-1 bg-transparent border-none text-[17px] text-slate-900 focus:ring-0 outline-none p-0 ml-1 min-w-0 font-medium">
                                </div>
                                
                                <div class="bg-blue-50/50 rounded-2xl border border-blue-100/30 py-2 pl-2 pr-3 flex items-start">
                                    <label class="text-[14px] text-slate-400 w-10 text-left shrink-0 mt-0.5 font-medium">開示</label>
                                    <textarea v-model="form.content" rows="4" @input="e => { e.target.style.height = 'auto'; e.target.style.height = e.target.scrollHeight + 'px' }" placeholder="輸入具體內容描述..." class="flex-1 bg-transparent border-none text-[17px] text-slate-900 focus:ring-0 outline-none p-0 ml-1 resize-none overflow-hidden min-h-[80px] font-medium"></textarea>
                                </div>

                                <div class="mt-6">
                                    <button @click.stop="itemsDetailMode = true" class="w-full bg-indigo-50/50 border border-indigo-100 text-indigo-600 rounded-2xl py-3.5 shadow-sm active:scale-95 transition-all text-[17px] font-medium tracking-wider">
                                        <template v-if="generateSummary(form)">
                                            降寶內容 ({{ generateSummary(form) }})
                                        </template>
                                        <template v-else>
                                            降寶內容
                                        </template>
                                    </button>
                                </div>
                            </template>
                            <template v-else>
                                <textarea v-model="batchInput" rows="15" class="w-full bg-slate-50 rounded-xl p-3 border-none text-[17px] text-slate-900 outline-none resize-none" placeholder="批量輸入開示內容..."></textarea>
                            </template>

                            <div class="py-12 flex flex-col items-center">
                                <button @click="saveItem" :disabled="saving" class="bg-yellow-400 text-yellow-950 rounded-[28px] px-16 py-3.5 shadow-lg active:scale-95 disabled:opacity-50 text-[17px] font-bold">
                                    {{ saving ? '正在儲存中...' : '確認存檔' }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Magic Items Detail View -->
                <div v-if="itemsDetailMode" class="fixed inset-0 z-[500] bg-white flex flex-col p-4 overflow-y-auto animate-fade-in">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center">
                            <button @click="itemsDetailMode = false" class="text-slate-400 mr-3 p-2 active:scale-90 transition-transform">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" /></svg>
                            </button>
                            <h3 class="text-[20px] font-bold text-black">降寶詳情錄入</h3>
                        </div>
                        <button @click="itemsDetailMode = false" class="bg-indigo-600 text-white px-5 py-2.5 rounded-2xl text-[14px] font-bold shadow-lg shadow-indigo-100 active:scale-95 transition-all">完成</button>
                    </div>

                    <div class="space-y-6">
                        <!-- Step 1: Treasure Name -->
                        <div class="space-y-2">
                            <label class="text-[14px] text-[#9ba7b9] block px-1 font-bold">1. 選取法寶名稱</label>
                            <div class="flex items-center space-x-2">
                                <input v-model="tempItem.treasure_name" type="text" list="item-name-list" placeholder="選擇法寶..." class="flex-1 py-3 px-4 bg-white rounded-2xl text-[17px] text-black border border-slate-100 outline-none font-bold shadow-sm">
                                <datalist id="item-name-list"><option v-for="t in treasures" :key="t.id" :value="t.name"/></datalist>
                                <button @click="pushTempItem" class="text-red-500 p-2 active:scale-90 transition-transform">
                                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
                                </button>
                            </div>
                        </div>

                        <!-- Step 2: Multiple Content Items -->
                        <div class="bg-indigo-50/20 p-4 rounded-[28px] border border-indigo-100/30">
                            <label class="text-[14px] text-[#9ba7b9] block mb-3 px-1 font-bold">2. 輸入多項內容物 (點擊紅 + 加入暫存)</label>
                            <div class="space-y-3">
                                <div class="flex items-center space-x-2">
                                    <input v-model="tempItem.name" type="text" list="item-name-list" placeholder="內容物名稱..." class="flex-1 py-3 px-4 bg-white rounded-2xl text-[17px] text-black border border-slate-100 outline-none font-bold shadow-sm">
                                </div>
                                <div class="flex items-center space-x-2">
                                    <input v-model="tempItem.sub_name" type="text" list="item-name-list" placeholder="細項 (可選)..." class="flex-1 py-3 px-4 bg-white rounded-2xl text-[17px] text-black border border-slate-100 outline-none font-bold shadow-sm">
                                    <button @click="addToStaging" class="text-red-500 p-2 active:scale-90 transition-transform">
                                        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Staging Display -->
                            <div v-if="stagingItems.length > 0" class="pt-4 pb-4 space-y-2">
                                 <div class="text-[13px] text-indigo-400 font-bold mb-2 uppercase tracking-widest text-center">--- 待提交暫存清單 ---</div>
                                 <div v-for="(sItem, sIdx) in stagingItems" :key="sIdx" class="flex items-center justify-between bg-white px-4 py-2 rounded-xl shadow-sm border border-slate-50">
                                    <span class="text-[17px] font-bold text-black">{{ sItem.name || '項目' }}{{ sItem.sub_name ? ' - ' + sItem.sub_name : '' }}</span>
                                    <button @click="stagingItems.splice(sIdx, 1)" class="text-red-300 p-1 active:scale-90 transition-transform">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2.5" /></svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Step 3: Shared Details -->
                        <div class="space-y-2">
                            <label class="text-[14px] text-[#9ba7b9] block px-1 font-bold">3. 共用詳情 (作法/數量/時間)</label>
                            <textarea v-model="tempItem.details" rows="3" placeholder="" class="w-full py-3 px-4 bg-slate-50 border border-slate-100 rounded-2xl text-[17px] text-black outline-none focus:ring-2 focus:ring-indigo-100 transition-all resize-none font-bold"></textarea>
                        </div>
                        
                        <button @click="commitStaging" class="w-full bg-blue-600 text-white py-4 rounded-2xl font-bold shadow-lg shadow-blue-100 active:scale-95 transition-all text-[17px] mt-2">
                            確認添加 {{ stagingItems.length > 0 ? (stagingItems.length + ' 項') : '' }} 至今日清單
                        </button>
                        
                        <!-- Committed Items Section (Ledger Style) -->
                        <div class="pt-6 border-t border-slate-100">
                            <div class="text-[14px] font-bold text-[#9ba7b9] mb-4 uppercase tracking-widest flex items-center">
                                <span class="mr-2">📝</span> 已加入明細 ({{ form.items.length }})
                            </div>
                            
                            <div class="space-y-3">
                                <div v-for="(group, gName, gIdx) in groupedPendingItems" :key="gName" class="bg-slate-50/20 rounded-2xl p-4 border border-slate-100/30">
                                    <template v-if="group.length === 1 && !group[0].name && !group[0].sub_name">
                                        <div class="flex justify-between items-start">
                                            <div class="flex-1 text-[17px] font-bold text-black">
                                                {{ (gIdx + 1) }}. {{ formatTreasureName(gName) }}{{ group[0].details ? '：' + group[0].details : '' }}
                                            </div>
                                            <button @click="removeMagicItem(group[0].originalIdx)" class="text-red-400 p-1 active:scale-90 transition-transform">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" /></svg>
                                            </button>
                                        </div>
                                    </template>
                                    <template v-else>
                                        <div class="flex justify-between items-center mb-3 border-b border-slate-100 pb-2">
                                            <span class="text-[17px] font-bold text-indigo-700">{{ (gIdx + 1) }}. {{ formatTreasureName(gName) }}</span>
                                        </div>
                                        <div v-for="(m, midx) in group" :key="midx" class="flex justify-between items-start py-2.5 border-b border-slate-50 last:border-0">
                                            <div class="flex-1 pr-4">
                                                <div class="text-[17px] font-bold text-black">
                                                    {{ m.name ? m.name : '項目' }}{{ m.sub_name ? ' - ' + m.sub_name : '' }}{{ m.details ? '：' + m.details : '' }}
                                                </div>
                                            </div>
                                            <button @click="removeMagicItem(m.originalIdx)" class="text-red-400 p-1 active:scale-90 transition-transform">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" /></svg>
                                            </button>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <!-- Save Button Section -->
                        <div class="py-10 flex flex-col items-center border-t border-slate-100 mt-8">
                            <button @click="saveItem" :disabled="saving" class="bg-yellow-400 text-yellow-950 rounded-[28px] px-16 py-3.5 shadow-xl active:scale-95 disabled:opacity-50 text-[17px] font-bold">
                                {{ saving ? '正在儲存中...' : '確認存檔' }}
                            </button>
                            <p class="text-[14px] text-slate-400 mt-2 italic">※ 點擊後將直接儲存整筆開示紀錄</p>
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
                                            @click="toggleDharmaName(dn.id)"
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
                                        <div @click="toggleGroupAccordion(group.id)" 
                                             class="flex items-center justify-between px-4 py-4 cursor-pointer active:bg-slate-50">
                                            <div class="flex items-center space-x-3">
                                                <div @click.stop="toggleGroupSelection(group)" 
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
                                                    @click="toggleDharmaName(member.id)"
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
                            <button @click="form.dharma_name_ids = []" class="flex-1 py-4 bg-slate-50 text-slate-500 rounded-2xl font-bold active:scale-95 transition-all text-[17px]">全部清除</button>
                            <button @click="showDharmaPicker = false" class="flex-[2] py-4 bg-indigo-600 text-white rounded-2xl font-bold shadow-lg shadow-indigo-100 active:scale-95 transition-all text-[17px]">確認選擇</button>
                        </div>
                    </div>
                </div>

                <!-- Main List View -->
                <div v-show="!addMode" class="pb-32 flex-1 overflow-y-auto bg-white" @click="focusedId = null; activeDropdownId = null" @scroll="handleScroll">
                    <div v-if="loading && visibleDates.length === 0" class="text-center py-12 text-slate-400 text-[20px] font-bold tracking-widest uppercase">載入紀錄中...</div>
                    <div v-else class="space-y-px">
                        <div v-for="dateRow in visibleDates" :key="dateRow.date" class="bg-white border-b border-slate-50">
                            <!-- Date Accordion -->
                            <div @click.stop="toggleDate(dateRow.date)" class="px-6 py-4 flex items-center justify-between cursor-pointer active:bg-slate-50">
                                <div class="flex items-center space-x-3">
                                    <div class="w-2.5 h-2.5 rounded-full" :class="expandedDates.has(dateRow.date) ? 'bg-indigo-500' : 'bg-slate-200'"></div>
                                    <span class="text-[20px] font-bold text-slate-800" :class="expandedDates.has(dateRow.date) ? 'text-indigo-600' : 'text-slate-800'">{{ dateRow.date }}</span>
                                    <span class="bg-slate-100 text-slate-400 text-[14px] px-2.5 py-0.5 rounded-full font-bold">{{ dateRow.count }} 筆</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span class="text-[14px] font-bold text-[#9ba7b9] uppercase tracking-wider">{{ expandedDates.has(dateRow.date) ? '收起清單' : '展開清單' }}</span>
                                    <svg :class="expandedDates.has(dateRow.date) ? 'rotate-180' : ''" class="w-5 h-5 text-slate-300 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="2" /></svg>
                                </div>
                            </div>

                            <div v-if="expandedDates.has(dateRow.date)" class="bg-slate-50/10">
                                <div v-if="dateLoading[dateRow.date]" class="p-8 text-center text-[17px] text-slate-300">載入中...</div>
                                <div v-else-if="dateItems[dateRow.date]?.length">
                                    <div v-for="item in (searchQuery ? filteredDateItems(dateRow.date) : dateItems[dateRow.date])" :key="item.id" 
                                         v-show="!focusedId || focusedId === item.id"
                                         @click.stop="toggleExpand(item.id)" class="px-6 py-2 border-b border-slate-50 last:border-0 cursor-pointer hover:bg-white transition-all">
                                        <div class="flex flex-col">
                                            <!-- Layer 1: Header row (Master + Actions) -->
                                            <div class="flex justify-between items-center mb-0.5 relative shrink-0">
                                                <div class="text-[17px] text-[#9ba7b9] font-bold uppercase tracking-wider h-7 flex items-center">
                                                    {{ item.master?.name || (item.master_name || '仙師') }}
                                                </div>
                                                
                                                <div class="relative">
                                                    <button @click.stop="activeDropdownId = activeDropdownId === item.id ? null : item.id" 
                                                            class="w-8 h-8 flex items-center justify-center text-slate-300 hover:text-indigo-600">
                                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/></svg>
                                                    </button>
                                                    <!-- Dropdown... same as before -->
                                                    <div v-if="activeDropdownId === item.id" 
                                                         class="absolute right-0 top-full mt-1 w-44 bg-white rounded-2xl shadow-2xl border border-slate-100 z-50 overflow-hidden animate-in fade-in zoom-in duration-150">
                                                        <button @click.stop="editItem(item); activeDropdownId = null" class="w-full px-4 py-3 text-left text-[15px] font-bold text-indigo-600 hover:bg-slate-50 flex items-center border-b border-slate-50">📝 修改</button>
                                                        <button @click.stop="duplicateItem(item); activeDropdownId = null" class="w-full px-4 py-3 text-left text-[15px] font-bold text-blue-500 hover:bg-slate-50 flex items-center border-b border-slate-50">📂 複製</button>
                                                        <button @click.stop="copyToLine(item); activeDropdownId = null" class="w-full px-4 py-3 text-left text-[15px] font-bold text-green-600 hover:bg-slate-50 flex items-center border-b border-slate-50">💬 貼LINE</button>
                                                        <button @click.stop="downloadTeaching(item); activeDropdownId = null" class="w-full px-4 py-3 text-left text-[15px] font-bold text-slate-600 hover:bg-slate-50 flex items-center border-b border-slate-50">💾 下載檔案</button>
                                                        <button @click.stop="deleteItem(item.id); activeDropdownId = null" class="w-full px-4 py-3 text-left text-[15px] font-bold text-red-500 hover:bg-slate-50 flex items-center">🗑️ 刪除</button>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Layer 2: Dharma Names & Content -->
                                            <div class="flex items-start space-x-2 overflow-hidden py-0.5">
                                                <div class="flex flex-wrap gap-1 shrink-0 max-w-[40%]">
                                                    <span v-for="dn in item.dharma_names" :key="dn.id" class="text-[17px] text-black font-extrabold leading-tight">{{ dn.name }}</span>
                                                    <span v-if="!item.dharma_names?.length" class="text-[17px] text-[#9ba7b9] font-bold">全員</span>
                                                </div>
                                                <div class="flex-1 text-[17px] text-black font-bold truncate">
                                                    {{ item.content }}
                                                </div>
                                            </div>
                                            
                                            <!-- Magic Item Minimal Summary in collapsed mode -->
                                            <div v-if="focusedId != item.id && item.items?.length" class="text-[14px] text-indigo-400 font-bold truncate opacity-80">
                                                賜降：{{ generateSummary(item) }}
                                            </div>
                                            
                                            <!-- Layer 3: Expanded Details -->
                                            <div v-if="focusedId == item.id" class="mt-2 pt-2 border-t border-slate-100 animate-fade-in space-y-3 pb-2">
                                                <div v-if="item.content?.trim()" class="text-[17px] text-black whitespace-pre-wrap leading-snug font-bold">{{ item.content }}</div>
                                                <div v-if="item.items && item.items.length > 0">
                                                    <div class="text-[17px] text-[#9ba7b9] font-bold mb-1.5">賜降：</div>
                                                    <div class="bg-indigo-50/10 rounded-2xl p-4 border border-indigo-100/10 shadow-sm">
                                                        <div v-for="(group, gName, gIdx) in groupItems(item.items)" :key="gName" class="mb-3 last:mb-0">
                                                            <div class="text-[17px] font-bold text-black flex items-center mb-0.5">
                                                                {{ (gIdx + 1) }}. {{ formatTreasureName(gName) }}
                                                            </div>
                                                            <div v-for="(m, midx) in group" :key="midx" class="pl-5 py-0.5 text-[17px] text-black">
                                                                <span class="font-bold">
                                                                    {{ m.name ? m.name : '項目' }}{{ m.sub_name ? ' - ' + m.sub_name : '' }}{{ m.details ? '：' + m.details : '' }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div v-if="item.items_footer_remarks" class="mt-1.5 p-3 bg-indigo-50/50 rounded-xl text-[17px] text-black font-bold italic border border-indigo-100/50">
                                                    備註: {{ item.items_footer_remarks }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bottom Navigation -->
                <mobile-navbar 
                    :can-back="true"
                    :show-action="!!currentFolder"
                    :action-active="addMode"
                    :search-active="showSearch"
                    :can-more="!!currentFolder"
                    @back="handleBack"
                    @home="$emit('goHome')"
                    @action="showAdd"
                    @search="showSearch = !showSearch"
                    @more="downloadList"
                />
                <search-component v-if="showSearch" v-model="searchQuery" :show="showSearch" @close="showSearch = false" />
            </template>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, defineEmits, watch } from 'vue';
import axios from 'axios';
import SearchComponent from './SearchComponent.vue';
import MobileNavbar from './MobileNavbar.vue';

const emit = defineEmits(['goHome']);
const currentFolder = ref(null);
const addMode = ref(false);
const teachings = ref([]); 
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
const itemsDetailMode = ref(false);
const tempItem = ref({ treasure_name: '', name: '', details: '', sub_name: '' });
const stagingItems = ref([]);

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

const dharmaSearchQuery = ref('');
const handleDharmaSearchInput = (e) => {
    const val = e.target.value;
    if (!val) { form.value.dharma_name_ids = []; return; }
    
    // 1. Match Dharma Name
    const matchedDN = dharmaNames.value.find(dn => dn.name === val);
    if (matchedDN) {
        form.value.dharma_name_ids = [matchedDN.id];
        // Leave name in box
        return;
    }

    // 2. Match Group Name
    const matchedGroup = groups.value.find(g => g.name === val);
    if (matchedGroup) {
        const memberIds = (matchedGroup.dharma_names || []).map(dn => dn.id);
        form.value.dharma_name_ids = memberIds;
        dharmaSearchQuery.value = matchedGroup.name + ' (全組)';
        return;
    }
};

watch(() => form.value.dharma_name_ids, (newIds) => {
    if (newIds.length === 1) {
        const name = getDharmaNameText(newIds[0]);
        if (dharmaSearchQuery.value !== name) dharmaSearchQuery.value = name;
    } else if (newIds.length === 0) {
        dharmaSearchQuery.value = '';
    } else {
        // Multiple selected, usually via modal or group
        // If it's a group, the handler already set the text
        if (!dharmaSearchQuery.value.includes('(全組)')) {
            dharmaSearchQuery.value = ''; // Let placeholder show "Selected X people"
        }
    }
}, { deep: true });

const folders = ref([
    { id: 1, name: '老祖仙師' }, { id: 2, name: '元始仙師' }, { id: 3, name: '道祖仙師' },
    { id: 4, name: '靈寶仙師' }, { id: 5, name: '父皇' }, { id: 6, name: '太宰仙師' },
    { id: 7, name: '太子' }, { id: 8, name: '閻王仙師' }, { id: 0, name: '父皇仙師每日開示' }
]);

const masterNameInput = ref('');
// Pre-fill logic removed per user request

const resolveMasterId = () => {
    const name = masterNameInput.value;
    form.value.master_name = name; // Always sync name
    const m = masters.value.find(x => x.name === name);
    if (m) { form.value.master_id = m.id; return; }
    
    // Fallback for hardcoded names
    const hardcoded = {
        '老祖仙師': 1, '元始仙師': 2, '道祖仙師': 3, '靈寶仙師': 4,
        '父皇仙師': 5, '太宰仙師': 6, '太子': 7, '閻王仙師': 8
    };
    if (hardcoded[name]) {
        form.value.master_id = hardcoded[name];
        return;
    }

    const f = folders.value.find(x => x.name === name);
    if (f) { form.value.master_id = f.id; return; }
    form.value.master_id = null;
};

const getDharmaNameText = (id) => {
    const dn = dharmaNames.value.find(x => x.id === id);
    return dn ? dn.name : '';
};

const filteredPickerResults = computed(() => {
    if (!pickerSearch.value) return dharmaNames.value;
    const q = pickerSearch.value.toLowerCase();
    return dharmaNames.value.filter(dn => (dn.name || '').toLowerCase().includes(q));
});

const filteredGroups = computed(() => {
    if (!pickerSearch.value) return groups.value || [];
    const q = pickerSearch.value.toLowerCase();
    return (groups.value || []).filter(g => (g.name || '').toLowerCase().includes(q));
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
    if (!group.dharma_names || group.dharma_names.length === 0) return false;
    return group.dharma_names.every(dn => form.value.dharma_name_ids.includes(dn.id));
};

const syncRecords = async () => {
    try {
        const [dnRes, groupRes, masterRes, treasureRes] = await Promise.allSettled([
            axios.get('/api/dharma-names-list'), 
            axios.get('/api/groups-list'),
            axios.get('/api/masters-list'), 
            axios.get('/api/treasures-list', { params: currentFolder.value ? { master_id: currentFolder.value.id, type: 'teaching' } : { type: 'teaching' } })
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
    if (val) { 
        visibleDates.value = []; 
        dateItems.value = {}; 
        dateLoading.value = {}; 
        expandedDates.value.clear(); 
        datePagination.value = { current_page: 1, last_page: 1 }; 
        fetchDates(1);
        syncRecords(); 
    }
});

const saveItem = async () => {
    if (entryTab.value === 'batch' && batchInput.value) form.value.content = batchInput.value;
    saving.value = true;
    try {
        const mid = form.value.master_id || currentFolder.value?.id;
        const finalMasterId = (mid == 0) ? null : mid;
        const payload = { ...form.value, master_id: finalMasterId, user_id: 1 };
        if (editingId.value) await axios.put(`/teachings/${editingId.value}`, payload);
        else await axios.post('/teachings', payload);
        alert('存檔成功！');
        addMode.value = false; showDharmaPicker.value = false; itemsDetailMode.value = false; editingId.value = null;
        form.value = { supplement: '', target_remarks: '', content: '', date: new Date().toISOString().split('T')[0], master_id: null, items: [], remarks: '', items_footer_remarks: '', user_id: 1, dharma_name_ids: [] };
        dharmaSearchQuery.value = '';
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
const addToStaging = () => { if (!tempItem.value.name) return alert('請輸入內容物名稱'); stagingItems.value.push({ name: tempItem.value.name, sub_name: tempItem.value.sub_name }); tempItem.value.name = ''; tempItem.value.sub_name = ''; };
const commitStaging = () => {
    if (stagingItems.value.length === 0) {
        if (!tempItem.value.treasure_name && !tempItem.value.name) return alert('請選擇或填寫內容');
        form.value.items.push({ ...tempItem.value });
    } else {
        // Add all staged items with CURRENT details
        stagingItems.value.forEach(s => {
            form.value.items.push({ 
                treasure_name: tempItem.value.treasure_name,
                name: s.name,
                sub_name: s.sub_name,
                details: tempItem.value.details
            });
        });
        stagingItems.value = [];
    }
    tempItem.value = { treasure_name: '', name: '', details: '', sub_name: '' };
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
const downloadList = () => { alert('匯出功能準備中...'); };

const copyToLine = (item) => {
    const treasureText = item.items && item.items.length > 0 
        ? '\n\n賜降：\n' + item.items.map(i => (i.treasure_name || '法寶') + (i.name ? ' - ' + i.name : '') + (i.details ? ' / ' + i.details : '')).join('\n')
        : '';
    const text = `${item.master?.name || '仙師'} 開示 (${item.date})\n\n${item.content}${treasureText}`;
    
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(text).then(() => {
            alert('內容已複製，可直接至 LINE 貼上');
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

const downloadTeaching = (item) => {
    const treasureText = item.items && item.items.length > 0 
        ? '\n\n賜降：\n' + item.items.map(i => (i.treasure_name || '法寶') + (i.name ? ' - ' + i.name : '') + (i.details ? ' / ' + i.details : '')).join('\n')
        : '';
    const text = `${item.master?.name || '仙師'} 開示\n日期：${item.date}\n\n${item.content}${treasureText}`;
    const blob = new Blob([text], { type: 'text/plain' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `開示紀錄_${item.date}.txt`;
    a.click();
    window.URL.revokeObjectURL(url);
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
            masterNameInput.value = ''; // Keep blank per user request
        } else {
            form.value.master_id = currentFolder.value.id;
            form.value.master_name = currentFolder.value.name;
            masterNameInput.value = ''; // Keep blank per user request
        }
    }
    addMode.value = true;
};

const handleBack = () => { if (addMode.value) { addMode.value = false; editingId.value = null; } else if (currentFolder.value) { currentFolder.value = null; } else { emit('goHome'); } };
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

@keyframes zoom-in { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
.animate-zoom-in { animation: zoom-in 0.2s cubic-bezier(0.16, 1, 0.3, 1); }

.line-clamp-1 { display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden; line-clamp: 1; }
.line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; line-clamp: 2; }

.custom-date-input::-webkit-calendar-picker-indicator {
    margin-left: auto;
    cursor: pointer;
}
</style>
