<template>
    <div v-if="mode" class="fixed inset-0 z-[2000] flex items-end md:items-center justify-center px-0 imperial-grace-module" style="overscroll-behavior: contain;">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm" @click="$emit('close')"></div>

        <!-- Form Container -->
        <div class="relative w-full h-[100dvh] md:h-full bg-white md:rounded-3xl shadow-[0_-10px_40px_rgba(0,0,0,0.1)] overflow-hidden animate-slide-up flex flex-col pb-[7dvh]">
            <!-- Header -->
            <div class="px-4 py-3 flex items-center bg-white border-b border-slate-50 relative">
                <div class="flex-1 flex flex-col justify-center min-w-0 pr-6">
                    <div class="text-[26px] font-black leading-tight font-outfit tracking-widest text-red-600 uppercase !font-black flex items-center gap-2" style="color: #dc2626 !important;">
                        <logo-imperial-notebook :height="36" />
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
                    :style="{ color: localMode === 'single' ? 'white !important' : '#94a3b8 !important' }">
                    逐筆登錄
                </button>
                <button @click="localMode = 'batch'" 
                    :class="localMode === 'batch' ? 'bg-indigo-500 text-white shadow-md' : 'bg-slate-50 text-slate-400 border border-slate-100'"
                    class="flex-1 py-2 text-[16px] font-black rounded-2xl transition-all whitespace-nowrap active:scale-95"
                    :style="{ color: localMode === 'batch' ? 'white !important' : '#94a3b8 !important' }">
                    多筆載錄
                </button>
            </div>

            <!-- Scrollable Content -->
            <div ref="scrollContainer" class="flex-1 overflow-y-auto px-3 pt-4 pb-32 space-y-5 custom-scrollbar overscroll-contain">

                <!-- IMMERSIVE ENTRY: One-Thing-At-A-Time -->
                <div class="flex-1 flex flex-col min-h-0 relative">
                    <!-- Progress Indicator -->
                    <div class="px-[15px] pt-[15px] pb-[10px]">
                        <div class="flex items-center justify-between gap-1">
                            <div v-for="s in totalSteps" :key="s" 
                                 class="h-1 flex-1 rounded-full transition-all duration-500"
                                 :class="s <= currentStep ? 'bg-indigo-500' : 'bg-slate-100'">
                            </div>
                        </div>
                        <div class="flex justify-between mt-2">
                            <span class="text-[11px] font-black text-slate-300 uppercase tracking-widest">Step {{ currentStep }} of {{ totalSteps }}</span>
                            <span class="text-[11px] font-black text-indigo-400 uppercase tracking-widest">{{ currentStepTitles[currentStep - 1] }}</span>
                        </div>
                    </div>

                    <!-- Step Content Container - Standard Padding -->
                    <div class="flex-1 flex flex-col justify-start pt-[15px] px-[15px] pb-20">
                        <transition name="step-fade" mode="out-in">
                            <!-- Step 1: Date & Master -->
                            <div v-if="currentStep === 1" :key="1" class="flex-1 flex flex-col items-center justify-start pt-[15px] space-y-12 animate-fade-in text-center">
                                <h2 class="text-[17px] font-black text-slate-900 leading-relaxed tracking-tight">請輸入<br><span class="text-indigo-600">日期</span>與<span class="text-red-600">父皇仙師</span></h2>
                                <div class="space-y-10 w-full max-w-sm mt-12">
                                    <div class="relative group">
                                        <label class="absolute -top-6 left-0 text-[13px] font-black text-slate-300 uppercase tracking-widest">得知日期</label>
                                        <input v-model="form.record_date" type="text" placeholder="YYYY-MM-DD" 
                                            class="w-full text-center text-[17px] font-black border-0 border-b-2 border-slate-100 focus:border-indigo-500 bg-transparent py-4 outline-none transition-all placeholder:text-slate-100">
                                        <button @click="activePicker = { field: 'record_date', title: '修改得知日期' }" class="absolute right-0 bottom-4 text-slate-200 hover:text-indigo-500 transition-colors">
                                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2v12a2 2 0 002 2z" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        </button>
                                    </div>
                                    <div class="relative group">
                                        <label class="absolute -top-6 left-0 text-[13px] font-black text-slate-300 uppercase tracking-widest">載錄目標</label>
                                        <select v-model="form.master_id" class="w-full text-center text-[17px] font-black border-0 border-b-2 border-slate-100 focus:border-red-500 bg-transparent py-4 outline-none transition-all appearance-none">
                                            <option v-for="m in masters" :key="m.id" :value="m.id">{{ m.name === '父皇仙師' ? '父皇' : m.name }}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Step 2: Item Name -->
                            <div v-else-if="currentStep === 2" :key="2" class="space-y-6 animate-fade-in text-center">
                                <h2 class="text-[17px] font-black text-slate-900 leading-relaxed tracking-tight">請輸入<span class="text-indigo-600">法寶名稱</span></h2>
                                <div class="max-w-md mx-auto">
                                    <textarea v-model="form.name" rows="2" 
                                        @input="e => { e.target.style.height = 'auto'; e.target.style.height = e.target.scrollHeight + 'px' }" 
                                        placeholder="例如：如意金剛杵..." 
                                        class="w-full text-center text-[17px] font-black border-0 border-b-2 border-slate-100 focus:border-indigo-500 bg-transparent py-4 outline-none transition-all placeholder:text-slate-100 resize-none leading-relaxed"></textarea>
                                </div>
                            </div>

                            <!-- Step 3: Purpose -->
                            <div v-else-if="currentStep === 3" :key="3" class="space-y-6 animate-fade-in text-center">
                                <h2 class="text-[17px] font-black text-slate-900 leading-relaxed tracking-tight">此法寶的<span class="text-indigo-600">用意</span>是？</h2>
                                <div class="max-w-md mx-auto">
                                    <input v-model="form.purpose" placeholder="選填：例如 鎮宅避邪..." 
                                        class="w-full text-center text-[17px] font-black border-0 border-b-2 border-slate-100 focus:border-indigo-500 bg-transparent py-4 outline-none transition-all placeholder:text-slate-100">
                                </div>
                                <div class="flex justify-center pt-4">
                                    <button @click="currentStep++" class="text-slate-300 font-bold hover:text-indigo-500 transition-colors">跳過此步驟</button>
                                </div>
                            </div>

                            <!-- STEP 2 (Single): NAME or (Batch): BATCH TYPE -->
                            <div v-else-if="currentStep === 2" :key="2" class="space-y-6 animate-fade-in text-center w-full">
                                <template v-if="localMode === 'single'">
                                    <h2 class="text-[17px] font-black text-slate-900 leading-relaxed tracking-tight">請輸入<span class="text-indigo-600">法寶名稱</span></h2>
                                    <div class="max-w-sm mx-auto">
                                        <input v-model="form.name" type="text" placeholder="例如：淨化法水..." 
                                            class="w-full text-center text-[18px] font-black border-0 border-b-2 border-slate-100 focus:border-indigo-500 bg-transparent py-4 outline-none transition-all placeholder:text-slate-100">
                                    </div>
                                </template>
                                <template v-else>
                                    <h2 class="text-[17px] font-black text-slate-900 leading-relaxed tracking-tight">請選擇<span class="text-indigo-600">載錄模式</span></h2>
                                    <div class="grid grid-cols-1 gap-4 max-w-sm mx-auto">
                                        <button @click="batchType = 'single'; currentStep++" 
                                                class="group p-4 bg-white border-2 border-slate-100 hover:border-indigo-500 rounded-[32px] transition-all active:scale-95 text-left flex items-center gap-5">
                                            <div class="w-14 h-14 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center shrink-0">
                                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                            </div>
                                            <div>
                                                <div class="text-[18px] font-black text-slate-900">單人承接</div>
                                                <div class="text-[14px] text-slate-400 font-bold">每行一筆法寶</div>
                                            </div>
                                        </button>
                                        <button @click="batchType = 'multi'; currentStep++" 
                                                class="group p-4 bg-white border-2 border-slate-100 hover:border-indigo-500 rounded-[32px] transition-all active:scale-95 text-left flex items-center gap-5">
                                            <div class="w-14 h-14 bg-red-50 text-red-600 rounded-2xl flex items-center justify-center shrink-0">
                                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                            </div>
                                            <div>
                                                <div class="text-[18px] font-black text-slate-900">多人承接</div>
                                                <div class="text-[14px] text-slate-400 font-bold">多筆名單對應一筆法寶</div>
                                            </div>
                                        </button>
                                    </div>
                                </template>
                            </div>

                            <!-- STEP 3 (Single): PURPOSE or (Batch): INPUT -->
                            <div v-else-if="currentStep === 3" :key="3" class="space-y-6 animate-fade-in text-center w-full">
                                <template v-if="localMode === 'single'">
                                    <h2 class="text-[17px] font-black text-slate-900 leading-relaxed tracking-tight">此法寶的<span class="text-indigo-600">用意</span>為何？</h2>
                                    <div class="max-w-md mx-auto">
                                        <textarea v-model="form.purpose" rows="3" placeholder="請簡述用途..." 
                                            class="w-full text-center text-[18px] font-black border-0 border-b-2 border-slate-100 focus:border-indigo-500 bg-transparent py-4 outline-none transition-all placeholder:text-slate-100 resize-none"></textarea>
                                    </div>
                                </template>
                                <template v-else>
                                    <h2 class="text-[17px] font-black text-slate-900 leading-relaxed tracking-tight">請貼入<span class="text-indigo-600">載錄內容</span></h2>
                                    <div class="max-w-md mx-auto space-y-4 text-left">
                                        <div class="flex items-center justify-between px-1">
                                            <span class="text-[12px] font-black text-slate-300 uppercase tracking-widest">每行一筆內容</span>
                                            <button v-if="batchInput" @click="batchInput = ''" class="text-rose-500 text-[12px] font-bold">清除</button>
                                        </div>
                                        <textarea v-model="batchInput" rows="6" placeholder="直接貼上文字內容..." 
                                            class="w-full text-[17px] font-black border-2 border-slate-100 rounded-[32px] p-6 focus:border-indigo-500 bg-slate-50/50 outline-none transition-all placeholder:text-slate-200 resize-none leading-relaxed"></textarea>
                                    </div>
                                </template>
                            </div>

                            <!-- STEP 4 (Single): MODE or (Batch): PREVIEW -->
                            <div v-else-if="currentStep === 4" :key="4" class="space-y-6 animate-fade-in text-center w-full">
                                <template v-if="localMode === 'single'">
                                    <h2 class="text-[17px] font-black text-slate-900 leading-relaxed tracking-tight">由<span class="text-indigo-600">何人</span>承接此法寶？</h2>
                                    <div class="grid grid-cols-1 gap-4 max-w-sm mx-auto">
                                        <button @click="personnel = []; currentStep++" 
                                                class="group p-4 bg-white border-2 border-slate-100 hover:border-indigo-500 rounded-[32px] transition-all active:scale-95 text-left flex items-center gap-5">
                                            <div class="w-14 h-14 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center shrink-0">
                                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                            </div>
                                            <div>
                                                <div class="text-[20px] font-black text-slate-900">單人承接</div>
                                                <div class="text-[14px] text-slate-400 font-bold">預設為目前登錄者</div>
                                            </div>
                                        </button>
                                        <button @click="addPersonnelRow(); currentStep++" 
                                                class="group p-4 bg-white border-2 border-slate-100 hover:border-indigo-500 rounded-[32px] transition-all active:scale-95 text-left flex items-center gap-5">
                                            <div class="w-14 h-14 bg-red-50 text-red-600 rounded-2xl flex items-center justify-center shrink-0">
                                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                            </div>
                                            <div>
                                                <div class="text-[20px] font-black text-slate-900">多人承接</div>
                                                <div class="text-[14px] text-slate-400 font-bold">可輸入多人法號</div>
                                            </div>
                                        </button>
                                    </div>
                                </template>
                                <template v-else>
                                    <h2 class="text-[17px] font-black text-slate-900 leading-relaxed tracking-tight">預覽<span class="text-indigo-600">載錄資料</span></h2>
                                    <div v-if="excelRows.length > 0" class="max-w-md mx-auto border border-slate-100 rounded-3xl overflow-hidden shadow-sm bg-white animate-fade-in text-left">
                                        <div class="bg-slate-50 px-4 py-3 border-b border-slate-100 flex justify-between items-center">
                                            <span class="text-[13px] font-black text-slate-500 uppercase tracking-widest">目前解析 ({{ excelRows.length }} 筆)</span>
                                            <span class="text-[11px] font-bold text-slate-300">PREVIEW</span>
                                        </div>
                                        <div class="overflow-x-auto max-h-[300px] custom-scrollbar">
                                            <table class="w-full text-left">
                                                <thead class="bg-slate-50/50 text-[11px] text-slate-400 font-black uppercase tracking-widest sticky top-0 backdrop-blur-sm">
                                                    <tr>
                                                        <th class="px-4 py-2 border-r border-slate-100">日期</th>
                                                        <th class="px-4 py-2">法寶內容</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="divide-y divide-slate-50">
                                                    <tr v-for="(row, idx) in excelRows" :key="idx" class="hover:bg-indigo-50/30 transition-colors">
                                                        <td class="px-4 py-3 font-mono text-slate-500 text-[13px] border-r border-slate-100 align-top">{{ row.record_date || '-' }}</td>
                                                        <td class="px-4 py-3 align-top">
                                                            <div class="text-[15px] font-black text-slate-900 mb-1">{{ row.name }}</div>
                                                            <div v-if="row.purpose" class="text-[12px] text-slate-400 font-bold">{{ row.purpose }}</div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div v-else class="py-12 text-slate-200 font-black text-[18px] italic">尚未輸入載錄內容</div>
                                </template>
                            </div>

                            <!-- STEP 5 (Single Only): STATUS & DATE or PERSONNEL LIST -->
                            <div v-else-if="currentStep === 5 && localMode === 'single'" :key="5" class="space-y-[15px] animate-fade-in w-full h-full flex flex-col">
                                <!-- Single Mode Status -->
                                <div v-if="personnel.length === 0" class="text-center space-y-6">
                                    <h2 class="text-[17px] font-black text-slate-900 leading-relaxed tracking-tight">目前的<span class="text-indigo-600">狀態</span>與<span class="text-red-600">日期</span>？</h2>
                                    <div class="grid grid-cols-1 gap-8 max-w-sm mx-auto">
                                        <div class="relative">
                                            <label class="absolute -top-6 left-0 text-[13px] font-black text-slate-300 uppercase tracking-widest">狀態</label>
                                            <select v-model="form.status" class="w-full text-center text-[18px] font-black border-0 border-b-2 border-slate-100 focus:border-indigo-500 bg-transparent py-4 outline-none transition-all appearance-none">
                                                <option value="未求得">未求得</option>
                                                <option value="已求得">已求得</option>
                                                <option value="已登記">已登記</option>
                                            </select>
                                        </div>
                                        <div class="relative">
                                            <label class="absolute -top-6 left-0 text-[13px] font-black text-slate-300 uppercase tracking-widest">{{ form.status === '已登記' ? '登記日期' : (form.status === '已求得' ? '求得日期' : '日期') }}</label>
                                            <input v-model="form.obtained_date" type="text" placeholder="YYYY-MM-DD" 
                                                :disabled="form.status === '未求得'"
                                                class="w-full text-center text-[18px] font-black border-0 border-b-2 border-slate-100 focus:border-red-500 bg-transparent py-4 outline-none transition-all disabled:text-slate-200">
                                            <button @click="activePicker = { field: 'obtained_date', title: '修改日期' }" 
                                                :disabled="form.status === '未求得'"
                                                class="absolute right-0 bottom-4 text-slate-200 hover:text-red-500 transition-colors disabled:opacity-30 disabled:cursor-not-allowed">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2v12a2 2 0 002 2z" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <!-- Multi Mode List -->
                                <div v-else class="flex-1 flex flex-col min-h-0">
                                    <div class="text-center mb-6">
                                        <h2 class="text-[17px] font-black text-slate-900 leading-relaxed tracking-tight">請輸入<span class="text-red-600">承接人員</span>名單</h2>
                                        <p class="text-[12px] font-black text-indigo-500 mt-1 tracking-tight animate-pulse">手機按『換行』，電腦按『ENTER』鍵可新增人員</p>
                                    </div>
                                    <div class="flex-1 overflow-y-auto custom-scrollbar px-1 pb-24 space-y-[15px]">
                                        <div v-for="(p, idx) in personnel" :key="idx" 
                                            class="p-4 bg-slate-50/50 rounded-[32px] border border-slate-100 space-y-4 relative group animate-fade-in">
                                            <button @click="removePersonnelRow(idx)" class="absolute -top-2 -right-2 w-8 h-8 bg-white border border-slate-100 text-slate-400 rounded-full flex items-center justify-center shadow-sm active:scale-90 transition-all z-10">✕</button>
                                            
                                            <div class="grid grid-cols-2 gap-4">
                                                <div class="space-y-1">
                                                    <label class="text-[13px] font-black text-slate-300 uppercase tracking-widest ml-1">法號</label>
                                                    <input v-model="p.custom_name" type="text" placeholder="法號" list="dharma-names"
                                                        @keyup.enter="handlePersonnelEnter(idx)"
                                                        class="personnel-name-input w-full text-[17px] font-black border-0 border-b-2 border-slate-200 focus:border-indigo-500 bg-transparent py-2 outline-none">
                                                </div>
                                                <div class="space-y-1">
                                                    <label class="text-[13px] font-black text-slate-300 uppercase tracking-widest ml-1">狀態</label>
                                                    <select v-model="p.status" class="w-full text-[17px] font-black border-0 border-b-2 border-slate-200 focus:border-indigo-500 bg-transparent py-2 outline-none appearance-none"
                                                        :style="p.status === '未求得' ? 'color: #dc2626 !important;' : (p.status === '已求得' ? 'color: #2563eb !important;' : 'color: #059669 !important;')">
                                                        <option value="未求得">未求得</option>
                                                        <option value="已求得">已求得</option>
                                                        <option value="已登記">已登記</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="grid grid-cols-2 gap-4">
                                                <div class="space-y-1">
                                                    <label class="text-[13px] font-black text-slate-300 uppercase tracking-widest ml-1">日期</label>
                                                    <div class="relative">
                                                        <input v-model="p.obtained_date" type="text" 
                                                            :disabled="p.status === '未求得'"
                                                            class="w-full text-[15px] font-black border-0 border-b-2 border-slate-200 bg-transparent py-2 outline-none disabled:text-slate-300">
                                                        <button @click="activePicker = { field: 'obtained_date', idx: idx, title: '修改人員日期' }" 
                                                            :disabled="p.status === '未求得'"
                                                            class="absolute right-0 bottom-2 text-slate-200 disabled:opacity-30 disabled:cursor-not-allowed">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2v12a2 2 0 002 2z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="space-y-1">
                                                    <label class="text-[13px] font-black text-slate-300 uppercase tracking-widest ml-1">備註對象</label>
                                                    <input v-model="p.relationship" placeholder="選填..." class="w-full text-[15px] font-black border-0 border-b-2 border-slate-200 bg-transparent py-2 outline-none">
                                                </div>
                                            </div>
                                        </div>
                                        <button @click="addPersonnelRow" class="w-full py-4 border-2 border-dashed border-slate-100 rounded-[32px] text-slate-300 font-black hover:border-indigo-300 hover:text-indigo-500 transition-all active:scale-95 flex items-center justify-center gap-2 mt-4">
                                            <span>＋ 繼續新增人員</span>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- STEP 6 (Single Only): REMARKS & CONFIRM -->
                            <div v-else-if="currentStep === 6 && localMode === 'single'" :key="6" class="space-y-6 animate-fade-in text-center w-full">
                                <h2 class="text-[17px] font-black text-slate-900 leading-relaxed tracking-tight">最後，有其他<br><span class="text-indigo-600">補充說明</span>嗎？</h2>
                                <div class="max-w-md mx-auto">
                                    <textarea v-model="form.remarks" rows="3" placeholder="例如：法寶現存放於..." 
                                        class="w-full text-center text-[18px] font-black border-0 border-b-2 border-slate-100 focus:border-indigo-500 bg-transparent py-4 outline-none transition-all placeholder:text-slate-100 resize-none leading-relaxed"></textarea>
                                </div>
                                <div class="bg-indigo-50/50 p-6 rounded-[40px] max-w-sm mx-auto space-y-2 border border-indigo-100">
                                    <div class="text-[11px] font-black text-indigo-400 uppercase tracking-widest">READY TO SAVE</div>
                                    <div class="text-[17px] font-black text-slate-900">資料已備妥，請點擊下方確認</div>
                                </div>
                            </div>
                        </transition>
                    </div>
                </div>
            </div>

            <!-- Footer Action - Only shown in Batch Mode -->
            <div v-if="localMode === 'batch'" class="absolute bottom-[7dvh] left-0 right-0 md:relative md:bottom-0 px-6 py-[2px] bg-white border-t border-slate-50 z-[10] flex justify-center">
                <button @click="handleSubmit" :disabled="isSaving || (localMode === 'batch' && excelRows.length === 0)"
                    class="flex-1 py-4 bg-indigo-600 !text-white rounded-2xl font-black text-[18px] shadow-lg shadow-indigo-100 active:scale-95 transition-all disabled:bg-slate-300"
                    style="color: white !important;">
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

// --- 1. State Declarations (Refs) ---
const localMode = ref(props.mode || 'single');
const currentStep = ref(1);
const personnel = ref([]);
const dharmaNames = ref([]);
const isMulti = ref(true);
const form = ref({ ...props.initialData });
const batchType = ref('single');
const batchInput = ref('');
const activePicker = ref(null);
const activeRelDropdownIdx = ref(null);
const scrollContainer = ref(null);
const relationshipOptions = ['母親', '父親', '公公', '婆婆', '爺爺', '奶奶', '外公', '外婆'];

const stepTitles = [
    '日期與仙師',
    '法寶名稱',
    '法寶用意',
    '承接模式',
    '人員明細',
    '備註確認'
];

const batchStepTitles = [
    '日期與仙師',
    '承接模式',
    '輸入內容',
    '預覽確認'
];

const currentStepTitles = computed(() => {
    return localMode.value === 'single' ? stepTitles : batchStepTitles;
});

const totalSteps = computed(() => {
    return currentStepTitles.value.length;
});

// --- 2. Lifecycle & Global Logic ---
watch(() => props.mode, (newVal) => {
    if (newVal) lockBodyScroll();
    else unlockBodyScroll();
}, { immediate: true });

onUnmounted(() => {
    if (props.mode) unlockBodyScroll();
});

onMounted(() => {
    fetchDharmaNames();
});

// --- 3. Computed Properties ---
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

const excelRows = computed(() => {
    if (!batchInput.value.trim()) return [];

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
            if (currentRec) records.push(currentRec);
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

// --- 4. Watchers ---
watch(localMode, () => {
    currentStep.value = 1;
});

watch(currentStep, () => {
    if (scrollContainer.value) {
        scrollContainer.value.scrollTop = 0;
    }
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

// Combined Status & Date Logic
watch(() => form.value.status, (newStatus) => {
    if (newStatus === '未求得') {
        form.value.obtained_date = '';
    } else if ((newStatus === '已求得' || newStatus === '已登記') && !form.value.obtained_date) {
        form.value.obtained_date = new Date().toISOString().split('T')[0];
    }
});

watch(personnel, (newVal) => {
    newVal.forEach(p => {
        if (p.status === '未求得') {
            p.obtained_date = '';
        } else if ((p.status === '已求得' || p.status === '已登記') && !p.obtained_date) {
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

// --- 5. Methods ---
async function fetchDharmaNames() {
    try {
        const res = await axios.get('/api/dharma-names-list');
        dharmaNames.value = res.data;
    } catch (e) {}
}

function validateStep(step) {
    if (step === 5) {
        if (personnel.value.length === 0) {
            if (form.value.status === '未求得' && form.value.obtained_date) {
                alert('狀態為「未求得」時不可輸入日期');
                form.value.obtained_date = '';
                return false;
            }
            if ((form.value.status === '已求得' || form.value.status === '已登記') && !form.value.obtained_date) {
                alert(`狀態為「${form.value.status}」時必須輸入日期`);
                return false;
            }
        } else {
            for (let i = 0; i < personnel.value.length; i++) {
                const p = personnel.value[i];
                if (!p.custom_name) {
                    alert(`第 ${i+1} 位人員請輸入法號`);
                    return false;
                }
                if (p.status === '未求得' && p.obtained_date) {
                    alert(`人員「${p.custom_name}」狀態為「未求得」時不可輸入日期`);
                    p.obtained_date = '';
                    return false;
                }
                if ((p.status === '已求得' || p.status === '已登記') && !p.obtained_date) {
                    alert(`人員「${p.custom_name}」狀態為「${p.status}」時必須輸入日期`);
                    return false;
                }
            }
        }
    }
    return true;
}

function handleNext() {
    if (validateStep(currentStep.value)) {
        currentStep.value++;
    }
}

function focusPersonnelInput(idx) {
    nextTick(() => {
        const inputs = document.querySelectorAll('.personnel-name-input');
        if (inputs[idx]) {
            inputs[idx].focus();
            setTimeout(() => {
                inputs[idx].scrollIntoView({ behavior: 'smooth', block: 'center' });
            }, 150);
        }
    });
}

function addPersonnelRow() {
    personnel.value.push({ custom_name: '', relationship: '', obtained_date: form.value.record_date || '', status: '已求得', remarks: '' });
    focusPersonnelInput(personnel.value.length - 1);
}

function handlePersonnelEnter(idx) {
    if (idx === personnel.value.length - 1) {
        addPersonnelRow();
    } else {
        focusPersonnelInput(idx + 1);
    }
}

function removePersonnelRow(idx) { personnel.value.splice(idx, 1); }

function movePersonnel(idx, dir) {
    const target = idx + dir;
    if (target < 0 || target >= personnel.value.length) return;
    const item = personnel.value[idx];
    personnel.value.splice(idx, 1);
    personnel.value.splice(target, 0, item);
}

function handleSubmit() {
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
}

function getMasterName(id) {
    const m = props.masters?.find(m => String(m.id) === String(id));
    return m ? (m.name === '父皇仙師' ? '父皇' : m.name) : '預設';
}
</script>

<style scoped>
.animate-slide-up { animation: slideUp 0.4s cubic-bezier(0.16, 1, 0.3, 1); }
.animate-fade-in { animation: fadeIn 0.3s ease-out; }
@keyframes slideUp { from { transform: translateY(100%); } to { transform: translateY(0); } }
@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
.no-scrollbar::-webkit-scrollbar { display: none; }

/* Step Transitions */
.step-fade-enter-active,
.step-fade-leave-active {
    transition: all 0.3s ease-in-out;
}
.step-fade-enter-from {
    opacity: 0;
    transform: translateX(20px);
}
.step-fade-leave-to {
    opacity: 0;
    transform: translateX(-20px);
}
</style>
