<template>
    <div v-if="mode" class="fixed inset-0 z-[2000] flex items-end md:items-center justify-center px-0 imperial-grace-module" style="overscroll-behavior: contain;">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm" @click="$emit('close')"></div>
        
        <!-- Form Container -->
        <div class="relative w-full h-[100dvh] md:h-full bg-white md:rounded-3xl shadow-[0_-10px_40px_rgba(0,0,0,0.1)] overflow-hidden animate-slide-up flex flex-col pb-[7dvh]">
            <!-- Header -->
            <div class="px-4 py-3 flex items-center bg-white border-b border-slate-50 relative">
                <div class="flex-1 flex flex-col justify-center min-w-0 pr-6">
                    <div class="text-[28px] font-black leading-tight font-outfit tracking-widest text-red-600 uppercase" style="color: #dc2626 !important; font-size: 28px !important; font-weight: 900 !important;">
                        重大皇恩
                    </div>
                </div>
                <button @click="$emit('close')" class="text-slate-300 hover:text-slate-600 transition-colors py-[5px] absolute right-4 top-1/2 -translate-y-1/2">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
            </div>

            <div class="px-4 py-2 bg-slate-50 flex space-x-2 border-b border-slate-100">
                <button @click="localMode = 'single'" 
                    :class="localMode === 'single' ? 'bg-indigo-500 text-white shadow-md' : 'bg-slate-50 text-slate-400 border border-slate-100'"
                    class="flex-1 py-2 text-[16px] font-black rounded-2xl transition-all whitespace-nowrap active:scale-95"
                    :style="{ fontSize: '16px !important', color: localMode === 'single' ? 'white !important' : '#94a3b8 !important' }">
                    逐筆登錄
                </button>
                <button @click="localMode = 'batch'" 
                    :class="localMode === 'batch' ? 'bg-indigo-500 text-white shadow-md' : 'bg-slate-50 text-slate-400 border border-slate-100'"
                    class="flex-1 py-2 text-[16px] font-black rounded-2xl transition-all whitespace-nowrap active:scale-95"
                    :style="{ fontSize: '16px !important', color: localMode === 'batch' ? 'white !important' : '#94a3b8 !important' }">
                    多筆載錄
                </button>
            </div>

            <!-- Scrollable Content -->
            <div class="flex-1 overflow-y-auto px-3 pt-4 pb-32 space-y-5 custom-scrollbar overscroll-contain">
                
                <!-- COMMON FIELDS (Date & Master) -->
                <div class="grid grid-cols-2 gap-3 bg-white p-1">
                    <div class="space-y-1.5">
                        <label class="text-[13px] font-black text-slate-400 uppercase tracking-widest block ml-1" style="font-size: 13px !important;">得知日期</label>
                        <div class="relative flex items-center">
                            <input v-model="form.record_date" type="text" placeholder="得知日期" 
                                class="w-full py-2.5 rounded-2xl bg-white pl-4 pr-10 border border-slate-200 shadow-sm focus:ring-2 focus:ring-indigo-100 outline-none text-[17px] font-black text-slate-900" style="font-size: 17px !important;">
                            <button @click="activePicker = { field: 'record_date', title: '修改得知日期' }" class="absolute right-2 text-slate-300 hover:text-indigo-600 transition-colors p-2 z-20 active:scale-90">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2v12a2 2 0 002 2z" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </button>
                        </div>
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-[13px] font-black text-slate-400 uppercase tracking-widest block ml-1" style="font-size: 13px !important;">載錄目標</label>
                        <select v-model="form.master_id" class="w-full h-[46px] rounded-2xl bg-white px-4 font-black text-slate-900 border border-slate-200 shadow-sm focus:ring-2 focus:ring-indigo-100 outline-none text-[17px]" style="font-size: 17px !important;">
                            <option v-for="m in masters" :key="m.id" :value="m.id">{{ m.name === '父皇仙師' ? '父皇' : m.name }}</option>
                        </select>
                    </div>
                </div>

                <!-- SINGLE MODE -->
                <div v-if="localMode === 'single'" class="space-y-5 animate-fade-in">
                    <div class="space-y-1.5">
                        <label class="text-[13px] font-black text-slate-400 uppercase tracking-widest block ml-1" style="font-size: 13px !important;">法寶名稱</label>
                        <textarea v-model="form.name" rows="3" placeholder="請輸入法寶名稱..." class="w-full py-3 rounded-2xl bg-white px-4 font-black text-slate-900 border border-slate-200 focus:ring-2 focus:ring-indigo-100 outline-none placeholder:text-slate-200 text-[18px]" style="font-size: 18px !important;"></textarea>
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-[13px] font-black text-slate-400 uppercase tracking-widest block ml-1" style="font-size: 13px !important;">法寶用意</label>
                        <textarea v-model="form.purpose" rows="2" placeholder="輸入法寶用途..." class="w-full py-3 rounded-2xl bg-white px-4 font-black text-slate-900 border border-slate-200 focus:ring-2 focus:ring-indigo-100 outline-none placeholder:text-slate-200 text-[18px]" style="font-size: 18px !important;"></textarea>
                    </div>

                    <!-- MASTER STATUS/DATE (ONLY FOR SINGLE PERSON) -->
                    <div v-if="personnel.length === 0" class="grid grid-cols-2 gap-3 animate-fade-in">
                        <div class="space-y-1.5">
                            <label class="text-[13px] font-black text-slate-400 uppercase tracking-widest block ml-1" style="font-size: 13px !important;">目前狀態</label>
                            <select v-model="form.status" class="w-full h-[46px] rounded-2xl bg-white px-4 font-black text-slate-900 border border-slate-200 shadow-sm outline-none text-[17px]" style="font-size: 17px !important;">
                                <option value="未求得">未求得</option>
                                <option value="已求得">已求得</option>
                                <option value="已登記">已登記</option>
                            </select>
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[13px] font-black text-slate-400 uppercase tracking-widest block ml-1" style="font-size: 13px !important;">
                                {{ form.status === '已登記' ? '登記日期' : (form.status === '已求得' ? '求得日期' : '日期') }}
                            </label>
                            <div class="relative">
                                <input v-model="form.obtained_date" type="text" placeholder="日期" class="w-full h-[46px] rounded-2xl bg-white px-4 font-black text-slate-900 border border-slate-200 shadow-sm outline-none pr-10 text-[17px]" style="font-size: 17px !important;">
                                <button @click="activePicker = { field: 'obtained_date', title: '修改日期' }" class="absolute right-2 top-1/2 -translate-y-1/2 text-slate-300 p-2 z-20 active:scale-90">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2v12a2 2 0 002 2z" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div v-if="personnel.length === 0" class="flex justify-center py-4 border-t border-slate-50 mt-4">
                        <button @click="addPersonnelRow" class="px-6 py-2 bg-slate-100 text-indigo-600 font-black text-[16px] rounded-2xl flex items-center space-x-2 active:scale-95 transition-all shadow-sm border border-slate-200" style="font-size: 16px !important;">
                            <span>＋ 多人承接模式</span>
                        </button>
                    </div>

                    <!-- PERSONNEL SECTION (ONLY FOR MULTI-PERSON) -->
                    <div v-if="personnel.length > 0" class="space-y-6 pt-4 border-t border-slate-100 animate-fade-in">
                        <div class="flex items-center justify-between ml-1">
                            <div class="flex flex-col">
                                <label class="text-[15px] font-black text-red-600 uppercase tracking-tight" style="font-size: 15px !important;">多人承接名單</label>
                                <span class="text-[11px] text-slate-400 font-bold uppercase tracking-widest" style="font-size: 11px !important;">MULTI-PERSON MODE</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <button @click="personnel = []" class="px-3 py-1.5 bg-slate-50 text-slate-500 rounded-2xl text-[16px] font-black active:scale-95 transition-all border border-slate-100 shadow-sm" style="font-size: 16px !important;">
                                    切換回單人
                                </button>
                                <button @click="addPersonnelRow" class="px-3 py-1.5 bg-indigo-600 text-white rounded-2xl text-[16px] font-black active:scale-95 transition-all shadow-md" style="font-size: 16px !important; color: white !important;">
                                    ＋ 新增人員
                                </button>
                            </div>
                        </div>

                        <div v-for="(p, idx) in personnel" :key="idx" 
                            class="p-4 bg-white rounded-[24px] border border-slate-200 space-y-4 relative group animate-fade-in shadow-sm">
                            <div class="absolute top-3 right-3 flex items-center space-x-2">
                                <div class="flex items-center space-x-1 bg-slate-50 rounded-lg px-1 py-0.5">
                                    <button v-if="personnel.length > 1" @click="movePersonnel(idx, -1)" :disabled="idx === 0" class="p-1 text-slate-300 hover:text-indigo-500 disabled:opacity-10 transition-all active:scale-90">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 15l7-7 7 7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    </button>
                                    <button v-if="personnel.length > 1" @click="movePersonnel(idx, 1)" :disabled="idx === personnel.length - 1" class="p-1 text-slate-300 hover:text-indigo-500 disabled:opacity-10 transition-all active:scale-90">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    </button>
                                </div>
                                <button @click="removePersonnelRow(idx)" class="w-7 h-7 flex items-center justify-center bg-rose-50 text-white rounded-full active:scale-90 transition-all">✕</button>
                            </div>
                            
                            <!-- Fields -->
                            <div class="space-y-4">
                                <div class="grid grid-cols-2 gap-3">
                                    <div class="space-y-1.5">
                                        <label class="text-[12px] font-black text-red-400 ml-1 uppercase tracking-widest" style="font-size: 12px !important;">法號</label>
                                        <input v-model="p.custom_name" type="text" placeholder="法號" list="dharma-names"
                                            @keydown.enter.prevent="handlePersonnelEnter(idx)"
                                            class="personnel-name-input w-full h-[46px] rounded-2xl border border-slate-200 bg-white px-4 text-[18px] font-black text-slate-900 focus:ring-2 focus:ring-indigo-100 outline-none font-outfit" style="font-size: 18px !important;">
                                    </div>
                                    <div class="space-y-1.5">
                                        <label class="text-[12px] font-black text-red-400 ml-1 uppercase tracking-widest" style="font-size: 12px !important;">狀態</label>
                                        <select v-model="p.status" class="w-full h-[46px] rounded-2xl border border-slate-200 bg-white px-4 text-[17px] font-black focus:ring-2 focus:ring-indigo-100 outline-none"
                                            :style="p.status === '未求得' ? 'color: #dc2626 !important; font-size: 17px !important;' : (p.status === '已求得' ? 'color: #2563eb !important; font-size: 17px !important;' : 'color: #059669 !important; font-size: 17px !important;')">
                                            <option value="未求得">未求得</option>
                                            <option value="已求得">已求得</option>
                                            <option value="已登記">已登記</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div class="space-y-1.5" :class="{ 'opacity-50 pointer-events-none': p.status === '未求得' }">
                                        <label class="text-[12px] font-black text-red-400 ml-1 uppercase tracking-widest" style="font-size: 12px !important;">
                                            {{ p.status === '已登記' ? '登記日期' : (p.status === '已求得' ? '求得日期' : '日期') }}
                                        </label>
                                        <div class="relative">
                                            <input v-model="p.obtained_date" type="text" placeholder="日期" 
                                                class="w-full h-[46px] rounded-2xl bg-white px-3 font-black text-slate-900 border border-slate-200 shadow-sm outline-none text-[16px] pr-8" style="font-size: 16px !important;">
                                            <button @click="activePicker = { field: 'obtained_date', idx: idx, title: '修改人員日期' }" class="absolute right-1 top-1/2 -translate-y-1/2 text-slate-300 p-2 z-20 active:scale-90">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2v12a2 2 0 002 2z" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="space-y-1.5">
                                        <label class="text-[12px] font-black text-slate-400 ml-1 uppercase tracking-widest" style="font-size: 12px !important;">備註對象</label>
                                        <div class="relative flex items-center border border-slate-200 rounded-2xl bg-white overflow-visible h-[46px]">
                                            <input v-model="p.relationship" placeholder="備註對象..." 
                                                @focus="activeRelDropdownIdx = idx"
                                                class="w-full bg-transparent border-none px-4 text-[16px] font-black text-slate-900 focus:ring-0 outline-none placeholder:text-slate-200" style="font-size: 16px !important;">
                                            <button @click.stop="activeRelDropdownIdx = (activeRelDropdownIdx === idx ? null : idx)" class="p-2 text-indigo-500 hover:text-indigo-700 transition-all">
                                                <svg class="w-5 h-5" :class="activeRelDropdownIdx === idx ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                            </button>
                                            <div v-if="activeRelDropdownIdx === idx" class="absolute left-0 top-full mt-2 w-full bg-white rounded-2xl shadow-[0_15px_45px_rgba(0,0,0,0.2)] border border-slate-100 z-[610] overflow-hidden p-1 animate-fade-in max-h-[220px] overflow-y-auto custom-scrollbar overscroll-contain">
                                                <div v-for="opt in relationshipOptions" :key="opt"
                                                     @click.stop="p.relationship = opt; activeRelDropdownIdx = null"
                                                     class="px-4 py-2.5 flex items-center rounded-xl hover:bg-indigo-50 font-black text-[15px] text-slate-900 active:bg-indigo-100 transition-all cursor-pointer" style="font-size: 15px !important;">
                                                    {{ opt }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Table is "collapsed" by default when personnel length is 0 -->


                        <div class="col-span-2 space-y-1 py-[5px]">
                            <label class="text-[15px] font-black text-slate-400 uppercase tracking-widest block ml-1">詳細內容 / 備註</label>
                            <textarea v-model="form.remarks" rows="1" placeholder="輸入更多說明內容..." style="font-size: 17px;" class="w-full py-[5px] rounded-xl bg-white p-2 font-bold text-slate-900 border border-slate-400 focus:ring-2 focus:ring-indigo-500/20 outline-none"></textarea>
                        </div>
                    </div>
                </div>

                <!-- BATCH MODE -->
                <div v-if="localMode === 'batch'" class="space-y-4 animate-fade-in">
                    <!-- Batch Type Toggle -->
                    <div class="px-1 py-1 bg-slate-50 rounded-2xl flex space-x-1 mb-4 border border-slate-100">
                        <button @click="batchType = 'single'" 
                            :class="batchType === 'single' ? 'shadow-md' : 'bg-transparent text-slate-400'"
                            class="flex-1 py-2.5 rounded-xl text-[16px] font-black transition-all active:scale-95"
                            :style="{ 
                                fontSize: '16px !important', 
                                backgroundColor: batchType === 'single' ? 'rgb(135, 206, 235) !important' : 'transparent',
                                color: batchType === 'single' ? 'white !important' : '#94a3b8 !important' 
                            }">
                            單人承接
                        </button>
                        <button @click="batchType = 'multi'" 
                            :class="batchType === 'multi' ? 'shadow-md' : 'bg-transparent text-slate-400'"
                            class="flex-1 py-2.5 rounded-xl text-[16px] font-black transition-all active:scale-95"
                            :style="{ 
                                fontSize: '16px !important', 
                                backgroundColor: batchType === 'multi' ? 'rgb(135, 206, 235) !important' : 'transparent',
                                color: batchType === 'multi' ? 'white !important' : '#94a3b8 !important' 
                            }">
                            多人承接
                        </button>
                    </div>

                    <div class="bg-white rounded-[24px] p-5 space-y-4 shadow-sm border border-slate-100 relative">
                        <div class="flex items-center justify-between">
                            <label class="text-[13px] font-black text-slate-400 uppercase tracking-widest ml-1" style="font-size: 13px !important;">貼入法寶名稱明細</label>
                            <div class="flex items-center space-x-3">
                                <button v-if="batchInput" @click="batchInput = ''" class="text-[12px] font-black text-red-500 hover:underline active:scale-90 transition-all" style="font-size: 12px !important;">清除內容</button>
                            </div>
                        </div>
                        <textarea v-model="batchInput" rows="10" class="w-full rounded-2xl border border-slate-200 p-4 font-mono text-[14px] bg-slate-50/30 focus:ring-2 focus:ring-indigo-100 outline-none shadow-inner" 
                            style="font-size: 14px !important;"
                            :placeholder="batchType === 'single' 
                                ? '單人承接請貼上或輸入 (沒有用意可以不用填入)\n法寶名稱\n用意:\n法寶名稱\n用意:\n以此類推' 
                                : '多人承接請貼上或輸入 (沒有用意可以不用填入)\n法寶名稱:\n用意:\n法號\n法號\n法寶名稱:\n用意:\n法號\n法號\n以此類推'">
                        </textarea>
                        <input type="file" ref="fileInput" class="hidden" @change="handleFileUpload" accept=".txt,.xlsx,.xls,.docx">
                    </div>

                    <div v-if="excelRows.length > 0" class="mt-4 border border-slate-100 rounded-2xl overflow-hidden shadow-sm bg-white animate-fade-in">
                        <div class="bg-slate-50 px-4 py-2.5 border-b border-slate-100 flex justify-between items-center">
                            <span class="text-[13px] font-black text-slate-600" style="font-size: 13px !important;">預覽解析結果 ({{ excelRows.length }} 筆)</span>
                            <span class="text-[11px] text-slate-400 font-bold uppercase tracking-widest" style="font-size: 11px !important;">PREVIEW</span>
                        </div>
                        <div class="overflow-x-auto max-h-[400px] overscroll-contain">
                            <table class="w-full text-left border-collapse">
                                <thead class="sticky top-0 bg-slate-50 shadow-sm z-10">
                                    <tr class="text-[11px] text-slate-400 uppercase tracking-widest font-black">
                                        <th class="px-4 py-3 border-b border-slate-100 w-32" style="font-size: 11px !important;">法寶名稱</th>
                                        <th class="px-4 py-3 border-b border-slate-100" style="font-size: 11px !important;">人員詳情</th>
                                        <th class="px-4 py-3 border-b border-slate-100 w-24 text-center" style="font-size: 11px !important;">狀態</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(row, idx) in excelRows" :key="idx" class="border-b border-slate-50 last:border-0">
                                        <td class="px-4 py-3 align-top">
                                            <div class="text-[15px] font-black text-indigo-600 leading-tight mb-1" style="font-size: 15px !important;">{{ row.c0 }}</div>
                                            <div v-if="row.c1 && row.c1 !== '-'" class="text-[11px] text-slate-400 font-bold leading-tight" style="font-size: 11px !important;">{{ row.c1 }}</div>
                                        </td>
                                        <td class="px-4 py-3 align-top">
                                            <div v-for="(p, pIdx) in row._dharma_name_registries" :key="pIdx" class="text-[13px] font-black text-slate-700 mb-1" style="font-size: 13px !important;">
                                                {{ p.custom_name }} <span class="text-[10px] text-slate-400">{{ p.status }}</span>
                                            </div>
                                            <div v-if="row._manualRemarks" class="text-[11px] text-amber-600 font-bold italic" style="font-size: 11px !important;">
                                                {{ row._manualRemarks }}
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 align-top text-center">
                                            <span class="px-2 py-0.5 rounded-lg text-[10px] font-black uppercase tracking-widest border"
                                                :class="row._status === '未求得' ? 'bg-red-50 border-red-100 text-red-500' : (row._status === '已求得' ? 'bg-blue-50 border-blue-100 text-blue-500' : 'bg-emerald-50 border-emerald-100 text-emerald-500')"
                                                style="font-size: 10px !important;">
                                                {{ row._status }}
                                            </span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Action -->
            <div class="absolute bottom-[7vh] left-0 right-0 md:relative md:bottom-0 px-4 pt-[7px] pb-[0px] bg-white backdrop-blur-md border-t border-slate-50 z-[10] flex justify-center">
                <button @click="handleSubmit" :disabled="isSaving || (localMode === 'batch' && excelRows.length === 0)"
                    class="w-full max-w-md bg-indigo-600 h-[48px] rounded-2xl text-white font-black text-[16px] active:scale-95 transition-all disabled:bg-slate-300 shadow-lg shadow-indigo-100"
                    style="font-size: 16px !important; color: white !important;">
                    {{ isSaving ? '處理中...' : (localMode === 'single' ? '確認載錄' : `開始載錄這 ${excelRows.length} 筆資料`) }}
                </button>
            </div>

            <mobile-navbar class="md:hidden" :can-back="false" @home="$emit('cancel')" :show-action="false" :can-search="false" is-absolute />
            
            <datalist id="dharma-names">
                <option v-for="dn in dharmaNames" :key="dn.id" :value="dn.name" />
            </datalist>
        </div>

        <!-- LOCAL DATE PICKER FOR THE ADD FORM -->
        <compact-date-picker 
            v-if="activePicker"
            v-model="activePickerValue"
            :title="activePicker.title"
            @close="activePicker = null"
        />
    </div>
</template>

<script setup>
import { ref, watch, computed, onMounted, onUnmounted, nextTick } from 'vue';
import axios from 'axios';
import CompactDatePicker from './CompactDatePicker.vue';
import MobileNavbar from './MobileNavbar.vue';
import { lockBodyScroll, unlockBodyScroll } from '../utils/iosCompat';

const props = defineProps({
    mode: String,
    initialData: Object,
    masters: Array,
    isSaving: Boolean
});

const emit = defineEmits(['saveSingle', 'saveBatch', 'cancel', 'close']);

// Watch for mode changes to handle body scroll locking
watch(() => props.mode, (newVal) => {
    if (newVal) lockBodyScroll();
    else unlockBodyScroll();
}, { immediate: true });

onUnmounted(() => {
    if (props.mode) unlockBodyScroll();
});

const localMode = ref(props.mode || 'single');
const batchType = ref('single');
const form = ref({ ...props.initialData });
const batchInput = ref('');
const activePicker = ref(null);
const activeRelDropdownIdx = ref(null);
const relationshipOptions = ['母親', '父親', '公公', '婆婆', '爺爺', '奶奶', '外公', '外婆'];

const activePickerValue = computed({
    get: () => {
        if (!activePicker.value) return '';
        if (activePicker.value.idx !== undefined) {
            return personnel.value[activePicker.value.idx][activePicker.value.field];
        }
        return form.value[activePicker.value.field];
    },
    set: (val) => {
        if (!activePicker.value) return;
        if (activePicker.value.idx !== undefined) {
            personnel.value[activePicker.value.idx][activePicker.value.field] = val;
        } else {
            form.value[activePicker.value.field] = val;
        }
    }
});

const hasPersonnelPattern = computed(() => {
    if (!batchInput.value.trim()) return false;
    return /(已登記|已求得|未求得)/.test(batchInput.value);
});

const excelCols = ref([
    { key: 'c0', label: '法寶名稱' },
    { key: 'c1', label: '法寶用意' }
]);

const excelRows = computed(() => {
    if (!batchInput.value.trim()) return [];

    // HYBRID SMART PARSER: Automatically handles Single & Multi-person blocks
    const lines = batchInput.value.split('\n').map(l => l.trim());
    const records = [];
    let currentRec = null;
    let currentMasterId = form.value.master_id;
    let currentDateInText = null;

    for (let i = 0; i < lines.length; i++) {
        const line = lines[i];
        if (!line) { 
            if (currentRec) { records.push(currentRec); currentRec = null; }
            continue; 
        }

        if (line.startsWith('【') && line.endsWith('】')) {
            const mName = line.replace(/[【】]/g, '');
            const found = props.masters?.find(m => m.name.includes(mName));
            if (found) currentMasterId = found.id;
            continue;
        }

        const isNewItemTrigger = line.startsWith('法寶名稱:') || line.startsWith('法寶名稱：');
        const isStatus = line.match(/(已登記|已求得|未求得)/);
        const isDate = line.match(/\b\d{4}[\/\-]\d{1,2}[\/\-]\d{1,2}\b/) || line.match(/\b\d{1,2}[\/\-]\d{1,2}\b/);
        const isPurpose = line.startsWith('用意') || line.startsWith('用法') || line.startsWith('用途');
        const looksLikePlainName = !line.includes('：') && !line.includes(':') && !isStatus && !isDate && !isPurpose && !isNewItemTrigger;

        if (isNewItemTrigger) {
            if (currentRec) {
                records.push(currentRec);
            }
            let name = line.replace(/^法寶名稱[:：]\s*/, '').trim();
            currentRec = { name: name, purpose: '-', personnel: [], remarks: [], status: '已登記', master_id: currentMasterId, date: currentDateInText || '' };
        } else if (looksLikePlainName) {
            if (!currentRec) {
                currentRec = { name: line, purpose: '-', personnel: [], remarks: [], status: '已登記', master_id: currentMasterId, date: currentDateInText || '' };
            } else {
                if (!currentRec.name) {
                    currentRec.name = line;
                } else {
                    if (batchType.value === 'single') {
                        records.push(currentRec);
                        currentRec = { name: line, purpose: '-', personnel: [], remarks: [], status: '已登記', master_id: currentMasterId, date: currentDateInText || '' };
                    } else {
                        currentRec.personnel.push({ custom_name: line, status: '已登記', obtained_date: '', remarks: '' });
                    }
                }
            }
        } else if (currentRec) {
            if (isPurpose) {
                currentRec.purpose = line.replace(/.*(用意|用法|用途)[:：]?\s*/, '').trim() || '-';
            } else if (isStatus) {
                const statusStr = isStatus[0];
                const namePart = line.split(statusStr)[0].replace(/狀態[:：]?/, '').trim();
                
                if (namePart) {
                    const pDate = isDate ? isDate[0].replace(/\//g, '-') : '';
                    currentRec.personnel.push({ custom_name: namePart, status: statusStr, obtained_date: pDate, remarks: '' });
                } else {
                    if (currentRec.personnel.length > 0) {
                        currentRec.personnel[currentRec.personnel.length - 1].status = statusStr;
                        if (isDate) currentRec.personnel[currentRec.personnel.length - 1].obtained_date = isDate[0];
                    } else {
                        currentRec.status = statusStr;
                        if (isDate) currentRec.date = isDate[0];
                    }
                }
            } else if (line.includes('未求得')) {
                currentRec.status = '未求得';
                if (!line.includes('以上沒有資料')) currentRec.remarks.push(line);
            } else if (isDate) {
                if (currentRec.personnel.length > 0) {
                     currentRec.personnel[currentRec.personnel.length - 1].obtained_date = isDate[0];
                } else {
                     currentRec.date = isDate[0];
                     currentDateInText = isDate[0];
                }
            } else if (!line.includes('以上沒有資料')) {
                currentRec.remarks.push(line);
            }
        }
    }
    if (currentRec) records.push(currentRec);
    
    return records.map(r => {
        let finalStatus = r.status;
        if (r.personnel.length > 0) {
            const hasUnobtained = r.personnel.some(p => p.status === '未求得');
            const allRegistered = r.personnel.every(p => p.status === '已登記');
            if (hasUnobtained) finalStatus = '未求得';
            else if (allRegistered) finalStatus = '已登記';
            else finalStatus = '已求得';
        }

        return {
            c0: r.name,
            c1: r.purpose,
            _dharma_name_registries: r.personnel,
            _manualRemarks: r.remarks.join('\n'),
            _is_multi: r.personnel.length > 0,
            _status: finalStatus,
            _record_date: r.date,
            _master_id: r.master_id
        };
    });
});

const personnel = ref([]);
const dharmaNames = ref([]);
const isMulti = ref(true);

const fetchDharmaNames = async () => {
    try {
        const res = await axios.get('/api/dharma-names-list');
        dharmaNames.value = res.data;
    } catch (e) {}
};

const addPersonnelRow = () => {
    alert('單人承接無需使用多人承接模式');
    personnel.value.push({ custom_name: '', relationship: '', obtained_date: '', status: '已求得', remarks: '' });
};

const handlePersonnelEnter = (idx) => {
    if (idx === personnel.value.length - 1) addPersonnelRow();
    nextTick(() => {
        const inputs = document.querySelectorAll('.personnel-name-input');
        if (inputs[idx + 1]) inputs[idx + 1].focus();
    });
};

const removePersonnelRow = (idx) => { personnel.value.splice(idx, 1); };
const movePersonnel = (idx, dir) => {
    const target = idx + dir;
    if (target < 0 || target >= personnel.value.length) return;
    const item = personnel.value[idx];
    personnel.value.splice(idx, 1);
    personnel.value.splice(target, 0, item);
};

onMounted(() => {
    fetchDharmaNames();
});

watch(() => props.initialData, (newVal) => {
    form.value = { ...newVal };
    if (newVal.dharma_name_registries) {
        personnel.value = newVal.dharma_name_registries.map(r => ({
            custom_name: r.dharma_name?.name || r.custom_name || '',
            relationship: Array.isArray(r.related_personnel) ? r.related_personnel.join('、') : (r.related_personnel || ''),
            obtained_date: r.obtained_date || '',
            status: r.status || '已求得',
            remarks: Array.isArray(r.remarks) ? r.remarks.join('\n') : (r.remarks || '')
        }));
    }
}, { immediate: true });

// Auto-fill date when status changes
watch(() => form.value.status, (newStatus) => {
    if ((newStatus === '已求得' || newStatus === '已登記') && !form.value.obtained_date) {
        form.value.obtained_date = new Date().toISOString().split('T')[0];
    }
});

watch(personnel, (newVal) => {
    newVal.forEach(p => {
        // Auto-fill date
        if ((p.status === '已求得' || p.status === '已登記') && !p.obtained_date) {
            p.obtained_date = new Date().toISOString().split('T')[0];
        }

        // Relationship Rule: "Name之Relative" split
        if (p.custom_name && p.custom_name.trim()) {
            const relSplitMatch = p.custom_name.match(/^(.*?)([之的])(.+)$/);
            if (relSplitMatch) {
                const namePart = relSplitMatch[1].trim();
                let connector = relSplitMatch[2];
                let relPart = relSplitMatch[3].trim();
                
                p.custom_name = namePart;
                p.relationship = connector + relPart;
            }
        }
    });
}, { deep: true });

const handleSubmit = () => {
    if (localMode.value === 'single') {
        if (!form.value.name?.trim()) { alert('請輸入法寶名稱'); return; }
        const cleaned = personnel.value.filter(p => p.custom_name?.trim());
        const isMulti = cleaned.length > 0;
        
        let finalStatus = form.value.status;
        if (isMulti) {
            const hasUnobtained = cleaned.some(p => p.status === '未求得');
            const allRegistered = cleaned.every(p => p.status === '已登記');
            if (hasUnobtained) finalStatus = '未求得';
            else if (allRegistered) finalStatus = '已登記';
            else finalStatus = '已求得';
        }
        
        emit('saveSingle', { 
            ...form.value,
            status: finalStatus,
            is_multi: isMulti,
            dharma_name_registries: cleaned.map(p => ({
                ...p,
                status: p.status || '已求得',
                obtained_date: p.obtained_date || '',
                remarks: p.remarks || '',
                related_personnel: p.relationship ? p.relationship.split(/[、, ]+/).filter(x => x) : []
            }))
        });
    } else {
        emit('saveBatch', { 
            input: batchInput.value, 
            masterId: form.value.master_id,
            rows: excelRows.value.map(row => ({
                name: row.c0, 
                purpose: row.c1, 
                master_id: row._master_id || form.value.master_id,
                record_date: row._record_date || '', 
                obtained_date: row._record_date || '',
                status: row._status || '已登記', 
                count: 1, 
                remarks: row._manualRemarks || '',
                is_multi: row._is_multi || false,
                dharma_name_registries: (row._dharma_name_registries || []).map(p => ({
                    ...p,
                    obtained_date: p.obtained_date || row._record_date || ''
                }))
            }))
        });
    }
};

const getMasterName = (id) => {
    const m = props.masters?.find(m => String(m.id) === String(id));
    return m ? (m.name === '父皇仙師' ? '父皇' : m.name) : '預設';
};

const handleFileUpload = (e) => { /* logic */ };
const handleDirectPaste = async () => { /* logic */ };
</script>

<style scoped>
.animate-slide-up { animation: slideUp 0.4s cubic-bezier(0.16, 1, 0.3, 1); }
.animate-fade-in { animation: fadeIn 0.3s ease-out; }
@keyframes slideUp { from { transform: translateY(100%); } to { transform: translateY(0); } }
@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
.no-scrollbar::-webkit-scrollbar { display: none; }
</style>
