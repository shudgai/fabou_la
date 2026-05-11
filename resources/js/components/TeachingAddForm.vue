<template>
    <div v-if="mode" class="fixed inset-0 z-[2000] flex items-end md:items-center justify-center px-0 teaching-module" style="overscroll-behavior: contain;">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm" @click="$emit('close')"></div>

        <!-- Form Container -->
        <div class="relative w-full h-[100dvh] md:h-full bg-[#f8fafc] md:rounded-3xl shadow-[0_-10px_40px_rgba(0,0,0,0.1)] overflow-hidden animate-slide-up flex flex-col pb-[7dvh]">
            <!-- Header -->
            <div class="px-4 py-3 flex items-center bg-white border-b border-slate-50 relative shrink-0">
                <div class="flex-1 flex flex-col justify-center min-w-0 pr-6">
                    <div class="text-[26px] font-black leading-tight font-outfit tracking-widest text-red-600 uppercase !font-black flex items-center gap-2" style="color: #dc2626 !important;">
                        <logo-imperial-notebook :height="36" />
                        {{ mode === 'batch' ? '智慧載錄' : '逐筆載錄' }}
                    </div>
                </div>
                <button @click="$emit('close')" class="text-slate-300 hover:text-slate-600 transition-colors py-[5px] absolute right-4 top-1/2 -translate-y-1/2">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
            </div>

            <!-- Progress Indicator (Only for Single Mode) -->
            <div v-if="mode === 'single'" class="px-6 pt-6 pb-2 bg-white shrink-0">
                <div class="flex items-center justify-between gap-1.5">
                    <div v-for="s in totalSteps" :key="s" 
                         class="h-1.5 flex-1 rounded-full transition-all duration-500"
                         :class="s <= currentStep ? 'bg-indigo-600 shadow-[0_0_8px_rgba(79,70,229,0.4)]' : 'bg-slate-100'">
                    </div>
                </div>
                <div class="flex justify-between mt-3 px-1">
                    <span class="text-[11px] font-black text-slate-300 uppercase tracking-[0.2em]">Step {{ currentStep }} / {{ totalSteps }}</span>
                    <span class="text-[11px] font-black text-indigo-500 uppercase tracking-[0.2em]">{{ currentStepTitle }}</span>
                </div>
            </div>

            <!-- Scrollable Content -->
            <div ref="scrollContainer" class="flex-1 overflow-y-auto px-4 pt-6 pb-32 space-y-8 custom-scrollbar overscroll-contain bg-white">
                
                <!-- BATCH MODE UI (Legacy Port) -->
                <div v-if="mode === 'batch'" class="space-y-6 animate-fade-in max-w-xl mx-auto w-full pt-4">
                     <div class="bg-blue-50/30 border-2 border-dashed border-blue-100 rounded-[28px] overflow-hidden min-h-[400px] flex flex-col relative">
                        <div class="p-5 flex-1 flex flex-col">
                            <textarea v-model="batchImportContent" 
                                      @paste="handleBatchPaste"
                                      placeholder="在此貼上資料...&#10;&#10;格式例：&#10;父皇開示給對象：&#10;內容...&#10;賜降：&#10;1.法寶名稱:詳情" 
                                      class="w-full flex-1 bg-transparent border-none text-[17px] text-slate-800 focus:ring-0 outline-none resize-none font-black leading-relaxed placeholder:text-rose-400 placeholder:font-black placeholder:text-[17px]"></textarea>
                        </div>
                    </div>
                    <!-- Batch Records Preview -->
                    <div v-if="batchRecords.length > 0" class="space-y-4">
                         <div class="text-[14px] text-slate-400 font-bold px-2 flex items-center">
                            <span class="w-2 h-2 bg-emerald-500 rounded-full mr-2"></span>
                            已解析紀錄 ({{ batchRecords.length }} 筆)
                        </div>
                        <div v-for="(record, index) in batchRecords" :key="index" class="bg-white border border-slate-200 rounded-3xl p-5 shadow-lg space-y-3">
                             <div class="flex items-center justify-between">
                                <span class="text-[12px] font-black text-indigo-600 bg-indigo-50 px-2.5 py-1 rounded-2xl">#{{ index + 1 }} {{ record.master_name }}</span>
                                <button @click="batchRecords.splice(index, 1)" class="text-slate-200 hover:text-red-400 p-1"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2.5" /></svg></button>
                            </div>
                            <div class="font-black text-slate-900 text-[17px]">{{ record.dharmaSearchQuery }}</div>
                            <div class="text-slate-800 text-[17px] font-black leading-relaxed">{{ record.content }}</div>
                        </div>
                    </div>
                </div>

                <!-- SINGLE MODE UI (Step-by-Step) -->
                <transition v-else name="step-fade" mode="out-in">
                    <!-- STEP 1: Basic Info -->
                    <div v-if="currentStep === 1" :key="'step-1'" class="space-y-10 animate-fade-in max-w-md mx-auto w-full pt-4">
                        <div class="text-center space-y-3 mb-12">
                            <h2 class="text-[22px] font-black text-slate-900 tracking-tight leading-tight">請確認<span class="text-indigo-600">日期</span>與<span class="text-red-600">對象</span></h2>
                            <p class="text-slate-400 text-[14px] font-bold uppercase tracking-widest">Basic Information</p>
                        </div>

                        <div class="space-y-12">
                            <div class="grid grid-cols-2 gap-6">
                                <div class="relative group">
                                    <label class="absolute -top-6 left-0 text-[11px] font-black text-slate-300 uppercase tracking-[0.2em]">日期</label>
                                    <input v-model="form.date" type="text" placeholder="YYYY-MM-DD" 
                                        class="w-full text-center text-[17px] font-black border-0 border-b-2 border-slate-100 focus:border-indigo-500 bg-transparent py-4 outline-none transition-all placeholder:text-slate-100">
                                </div>
                                <div class="relative group">
                                    <label class="absolute -top-6 left-0 text-[11px] font-black text-slate-300 uppercase tracking-[0.2em]">仙師</label>
                                    <div class="relative">
                                        <input v-model="masterNameInput" @change="resolveMasterId" @focus="activeMasterDropdown = true" placeholder="選擇仙師..." 
                                            class="w-full text-center text-[17px] font-black border-0 border-b-2 border-slate-100 focus:border-red-500 bg-transparent py-4 outline-none transition-all appearance-none">
                                        <div v-if="activeMasterDropdown" class="absolute left-0 top-full mt-2 w-full bg-white rounded-2xl shadow-[0_20px_50px_rgba(0,0,0,0.2)] border border-slate-100 z-[600] p-1.5 animate-fade-in max-h-[250px] overflow-y-auto custom-scrollbar">
                                            <div v-for="m in ['老祖仙師', '元始仙師', '道祖仙師', '靈寶仙師', '父皇', '太宰仙師', '太子', '閻王仙師']" :key="m"
                                             @click.stop="masterNameInput = m; resolveMasterId(); activeMasterDropdown = false"
                                             class="px-4 h-[44px] flex items-center rounded-xl hover:bg-indigo-50 font-black text-[17px] text-slate-900 active:bg-indigo-100 transition-all cursor-pointer">
                                                {{ m }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="relative group">
                                <div class="flex items-center justify-between absolute -top-8 left-0 right-0">
                                    <label class="text-[11px] font-black text-slate-300 uppercase tracking-[0.2em]">對象 / 群組</label>
                                </div>
                                <div class="relative flex items-center h-[60px] border-0 border-b-2 border-slate-100 focus-within:border-indigo-600 transition-all">
                                    <input type="text" 
                                   @input="handleDharmaSearchInput" 
                                           @focus="activePractitionerDropdown = true"
                                           v-model="dharmaSearchQuery"
                                           list="teaching-dharma-search"
                                           placeholder="搜尋法號或群組..." 
                                            class="w-full bg-transparent border-none text-center text-[17px] font-black text-slate-900 focus:ring-0 outline-none placeholder:text-slate-100">
                                    <div v-if="activePractitionerDropdown" class="absolute left-0 top-full mt-2 w-full bg-white rounded-3xl shadow-[0_20px_50px_rgba(0,0,0,0.2)] border border-slate-100 z-[600] overflow-hidden p-1.5 animate-fade-in max-h-[300px] overflow-y-auto custom-scrollbar">
                                        <div v-if="filteredDharmaNames.length > 0" class="px-5 py-2 text-[10px] font-black text-slate-300 uppercase tracking-[0.2em] bg-slate-50/50 mt-1 mb-1 rounded-xl">法號</div>
                                        <div v-for="dn in filteredDharmaNames" :key="'dn'+dn.id"
                                             @click.stop="selectDharmaName(dn)"
                                             class="px-5 h-[44px] flex items-center rounded-xl hover:bg-indigo-50 font-black text-[17px] text-slate-900 active:bg-indigo-100 transition-all cursor-pointer whitespace-nowrap">
                                            {{ dn.name }}
                                        </div>
                                        <div v-if="filteredGroups.length > 0" class="px-5 py-2 text-[10px] font-black text-indigo-300 uppercase tracking-[0.2em] bg-slate-50/50 mb-1 rounded-xl">群組</div>
                                        <div v-for="g in filteredGroups" :key="'g'+g.id"
                                             @click.stop="selectGroup(g)"
                                             class="px-5 h-[44px] flex items-center rounded-xl hover:bg-indigo-50 font-black text-[17px] text-indigo-600 active:bg-indigo-100 transition-all cursor-pointer whitespace-nowrap">
                                            {{ formatGroupName(g.name) }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="relative group">
                                <label class="absolute -top-6 left-0 text-[11px] font-black text-slate-300 uppercase tracking-[0.2em]">備註對象 (選填)</label>
                                <div class="relative">
                                    <input v-model="form.target_remarks" @focus="activeRelDropdown = true" placeholder="例：之母親..." 
                                        class="w-full text-center text-[17px] font-black border-0 border-b-2 border-slate-100 focus:border-indigo-500 bg-transparent py-4 outline-none transition-all placeholder:text-slate-100">
                                    <div v-if="activeRelDropdown" class="absolute left-0 top-full mt-2 w-full bg-white rounded-2xl shadow-[0_20px_50px_rgba(0,0,0,0.2)] border border-slate-100 z-[610] p-1.5 animate-fade-in max-h-[250px] overflow-y-auto custom-scrollbar">
                                         <div v-for="opt in relationshipOptions" :key="opt"
                                             @click.stop="form.target_remarks = opt; activeRelDropdown = false"
                                             class="px-4 h-[44px] flex items-center rounded-xl hover:bg-indigo-50 font-black text-[17px] text-slate-900 active:bg-indigo-100 transition-all cursor-pointer">
                                            {{ opt }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div v-if="form.dharma_name_ids.length > 0 || dharmaSearchQuery === '在場全體'" class="pt-4 space-y-4">
                                <div class="flex flex-wrap gap-2.5 justify-center">
                                    <template v-if="isGroupSelected">
                                        <div class="bg-indigo-600 text-white px-5 py-3 rounded-2xl flex items-center shadow-md animate-scale-in">
                                            <span class="font-black text-[17px]">群組：{{ formatGroupName(selectedGroupLabel) }} ({{ form.dharma_name_ids.length }}人)</span>
                                            <button @click="clearSelection" class="ml-3 text-white/60 hover:text-white"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="3"/></svg></button>
                                        </div>
                                    </template>
                                    <template v-else>
                                        <div v-for="id in form.dharma_name_ids" :key="id" class="bg-indigo-50 text-indigo-700 px-4 py-2.5 rounded-2xl flex items-center border border-indigo-100 shadow-sm animate-scale-in">
                                            <span class="font-black text-[17px]">{{ getDharmaNameText(id) }}</span>
                                            <button @click="removeDharmaId(id)" class="ml-2 text-indigo-300 hover:text-indigo-600"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="3"/></svg></button>
                                        </div>
                                    </template>
                                </div>
                                <div v-if="isGroupSelected" class="flex flex-wrap gap-2 justify-center">
                                    <div v-for="id in form.dharma_name_ids" :key="'mem'+id" class="bg-slate-50 text-slate-600 px-3 py-1.5 rounded-xl flex items-center border border-slate-200">
                                        <span class="font-black text-[17px]">{{ getDharmaNameText(id) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- STEP 2: Content -->
                    <div v-else-if="currentStep === 2" :key="'step-2'" class="space-y-10 animate-fade-in max-w-md mx-auto w-full pt-4">
                        <div class="text-center space-y-3 mb-10">
                            <h2 class="text-[22px] font-black text-slate-900 tracking-tight leading-tight">請輸入<span class="text-indigo-600">開示內容</span></h2>
                            <p class="text-slate-400 text-[14px] font-bold uppercase tracking-widest">Teaching Content</p>
                        </div>

                        <div class="space-y-10">
                            <div class="relative group">
                                <label class="absolute -top-6 left-0 text-[11px] font-black text-slate-300 uppercase tracking-[0.2em]">開示主文</label>
                                <textarea v-model="form.content" rows="6" placeholder="請輸入開示內容..." 
                                    class="w-full text-center text-[17px] font-black border-0 border-b-2 border-slate-100 focus:border-indigo-600 bg-transparent py-4 outline-none transition-all placeholder:text-slate-100 resize-none leading-relaxed"></textarea>
                            </div>

                            <div class="space-y-4">
                                <label class="text-[11px] font-black text-slate-300 uppercase tracking-[0.2em] block">結尾備註</label>
                                <div class="flex flex-wrap gap-2 mb-4">
                                    <div v-for="(r, idx) in footerRemarks" :key="idx" class="bg-slate-50 px-3 py-2 rounded-xl flex items-center border border-slate-100">
                                        <span class="font-black text-[14px] text-slate-600">{{ r }}</span>
                                        <button @click="removeFooterRemark(idx)" class="ml-2 text-slate-300 hover:text-red-500"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2.5"/></svg></button>
                                    </div>
                                </div>
                                <div class="relative">
                                    <input v-model="newFooterRemark" list="footer-remark-options" @keyup.enter="addFooterRemark" placeholder="例：完畢..." 
                                        class="w-full text-center text-[17px] font-black border-0 border-b-2 border-slate-300 focus:border-indigo-600 bg-transparent py-3 outline-none transition-all placeholder:text-slate-400">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- STEP 3: Magic Items Detail -->
                    <div v-else-if="currentStep === 3" :key="'step-3'" class="animate-fade-in w-full h-full flex flex-col pt-4">
                         <div class="space-y-10 max-w-md mx-auto w-full">
                             <div class="space-y-4">
                                 <div v-if="form.items.length > 0" class="space-y-3">
                                     <span class="text-[11px] font-black text-slate-300 uppercase tracking-[0.2em]">降寶明細</span>
                                     <div v-for="(item, idx) in form.items" :key="idx" class="bg-white border border-slate-100 rounded-2xl p-4 shadow-sm flex items-center justify-between group">
                                         <div class="flex-1 min-w-0 pr-4">
                                             <div class="flex items-center gap-2">
                                                 <span class="w-5 h-5 bg-indigo-50 text-indigo-600 rounded-full flex items-center justify-center text-[10px] font-black shrink-0">{{ idx + 1 }}</span>
                                                 <span class="text-[17px] font-black text-slate-900 truncate">{{ item.treasure_name }}</span>
                                             </div>
                                             <div class="mt-1 pl-7 text-[13px] text-slate-400 font-bold leading-tight">{{ item.details || '無詳細內容' }}</div>
                                         </div>
                                         <button @click="removeItem(idx)" class="p-2 text-slate-200 hover:text-red-500 active:scale-90 transition-all">
                                             <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                         </button>
                                     </div>
                                 </div>

                                 <button @click="showItemsSelector = true" class="w-full py-5 border-2 border-dashed border-slate-200 rounded-2xl text-slate-400 hover:border-indigo-200 hover:text-indigo-400 transition-all flex items-center justify-center gap-2 group active:scale-[0.98]">
                                     <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4" stroke-width="2.5" stroke-linecap="round"/></svg>
                                     <span class="text-[15px] font-black tracking-widest uppercase">新增降寶明細</span>
                                 </button>
                             </div>

                             <div class="space-y-4">
                                 <label class="text-[11px] font-black text-slate-300 uppercase tracking-[0.2em] block">結尾備註</label>
                                 <div class="flex flex-wrap gap-2 mb-4">
                                     <div v-for="(r, idx) in footerRemarks" :key="idx" class="bg-slate-50 px-3 py-2 rounded-xl flex items-center border border-slate-100">
                                         <span class="font-black text-[14px] text-slate-600">{{ r }}</span>
                                         <button @click="removeFooterRemark(idx)" class="ml-2 text-slate-300 hover:text-red-500"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2.5"/></svg></button>
                                     </div>
                                 </div>
                                 <div class="relative">
                                     <input v-model="newFooterRemark" list="footer-remark-options" @keyup.enter="addFooterRemark" placeholder="例：完畢..." 
                                         class="w-full text-center text-[17px] font-black border-0 border-b-2 border-slate-300 focus:border-indigo-600 bg-transparent py-3 outline-none transition-all placeholder:text-slate-400">
                                 </div>
                             </div>
                         </div>
                    </div>

                    <!-- STEP 4: Review -->
                    <div v-else-if="currentStep === 4" :key="'step-4'" class="space-y-8 animate-fade-in max-w-md mx-auto w-full pt-4">
                        <div class="text-center space-y-3 mb-10">
                            <h2 class="text-[22px] font-black text-slate-900 tracking-tight leading-tight">確認並<span class="text-emerald-600">完成存檔</span></h2>
                            <p class="text-slate-400 text-[14px] font-bold uppercase tracking-widest">Final Review</p>
                        </div>

                        <div class="bg-white rounded-[32px] border border-slate-100 shadow-xl overflow-hidden">
                            <div class="p-6 space-y-6">
                                <div class="flex justify-between items-center border-b border-slate-50 pb-4">
                                    <div class="flex flex-col">
                                        <span class="text-[10px] font-black text-slate-300 uppercase tracking-widest">日期與仙師</span>
                                        <span class="text-[17px] font-black text-slate-900">{{ form.date }} · {{ masterNameInput }}</span>
                                    </div>
                                    <div class="text-right flex flex-col items-end">
                                        <span class="text-[10px] font-black text-slate-300 uppercase tracking-widest">對象</span>
                                        <span class="text-[17px] font-black text-indigo-600">{{ selectedGroupLabel || '個別法號' }}</span>
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    <span class="text-[10px] font-black text-slate-300 uppercase tracking-widest block">開示內容</span>
                                    <p class="text-[17px] font-bold text-slate-700 leading-relaxed whitespace-pre-wrap">{{ finalContent || '(無內容)' }}</p>
                                </div>

                                <div v-if="form.items.length > 0" class="space-y-4 pt-4 border-t border-slate-50">
                                    <span class="text-[10px] font-black text-slate-300 uppercase tracking-widest block">降寶明細 ({{ form.items.length }})</span>
                                    <div class="space-y-2.5">
                                        <div v-for="item in form.items" :key="item.uid" class="flex items-start gap-3 bg-slate-50/50 p-3 rounded-2xl border border-slate-100/50">
                                            <span class="text-indigo-500 font-black text-[14px] mt-0.5">✦</span>
                                            <div class="flex-1 min-w-0">
                                                <div class="text-[17px] font-black text-slate-800">{{ item.treasure_name }}</div>
                                                <div v-if="item.details" class="text-[12px] text-slate-400 font-bold">{{ item.details }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </transition>
            </div>

            <!-- Footer Action -->
            <div class="absolute bottom-[7dvh] left-0 right-0 md:relative md:bottom-0 px-6 py-[4px] bg-white border-t border-slate-50 z-[50] flex gap-3 justify-center shrink-0">
                <button v-if="currentStep > 1" @click="handleBack"
                    class="w-[100px] py-4 bg-slate-100 text-slate-400 rounded-2xl font-black text-[18px] active:scale-95 transition-all">
                    上一步
                </button>
                <button v-if="mode === 'single' && currentStep < totalSteps" @click="handleNext"
                    class="flex-1 py-4 bg-indigo-600 !text-white rounded-2xl font-black text-[18px] shadow-lg shadow-indigo-100 active:scale-95 transition-all"
                    style="color: white !important;">
                    下一步
                </button>
                <button v-else @click="handleSubmit" :disabled="isSaving"
                    class="flex-1 py-4 bg-emerald-600 !text-white rounded-2xl font-black text-[18px] shadow-lg shadow-emerald-100 active:scale-95 transition-all disabled:bg-slate-300"
                    style="color: white !important;">
                    {{ isSaving ? '處理中...' : '確認載錄' }}
                </button>
            </div>

            <mobile-navbar class="md:hidden" :can-back="false" @home="$emit('close')" :show-action="false" :can-search="false" is-absolute />
        </div>

        <!-- ITEMS SELECTOR MODAL (Full Featured) -->
        <div v-if="showItemsSelector" class="fixed inset-0 z-[3000] bg-white flex flex-col animate-slide-up">
             <div class="bg-white px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                 <div class="flex items-center gap-3">
                     <button @click="showItemsSelector = false" class="p-2 bg-slate-50 rounded-full active:scale-90"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="3"/></svg></button>
                     <h1 class="text-[22px] font-black text-slate-900 tracking-tight">錄入法寶</h1>
                 </div>
             </div>
             <div class="flex-1 overflow-y-auto p-6 space-y-8 custom-scrollbar">
                 <div class="space-y-2">
                     <label class="text-[11px] font-black text-slate-300 uppercase tracking-widest">法寶名稱</label>
                     <input v-model="newItemName" list="item-name-list" placeholder="輸入或選擇法寶..." class="w-full text-[22px] font-black border-0 border-b-2 border-slate-100 focus:border-indigo-600 py-4 outline-none transition-all placeholder:text-slate-100">
                 </div>
                 
                 <!-- Dynamic Contextual Inputs Based on Item Name -->
                 <div v-if="itemCategory === '三光金丹' || itemCategory === '金丹'" class="bg-indigo-50/50 p-6 rounded-[32px] space-y-6 animate-fade-in border border-indigo-100/50">
                      <div class="grid grid-cols-2 gap-4">
                          <div class="space-y-2">
                              <span class="text-[11px] font-black text-indigo-400 uppercase tracking-widest">天份</span>
                              <input v-model="newItemDays" type="number" class="w-full bg-white rounded-2xl h-12 text-center text-[20px] font-black text-slate-900 focus:ring-2 focus:ring-indigo-200 outline-none border-none">
                          </div>
                          <div class="space-y-2">
                              <span class="text-[11px] font-black text-indigo-400 uppercase tracking-widest">顆數</span>
                              <input v-model="newItemTimes" type="number" class="w-full bg-white rounded-2xl h-12 text-center text-[20px] font-black text-slate-900 focus:ring-2 focus:ring-indigo-200 outline-none border-none">
                          </div>
                      </div>
                 </div>

                 <div v-else-if="itemCategory === '符令' || newItemName.includes('張')" class="bg-amber-50/50 p-6 rounded-[32px] space-y-6 animate-fade-in border border-amber-100/50">
                      <div class="grid grid-cols-2 gap-4">
                          <div class="space-y-2">
                              <span class="text-[11px] font-black text-amber-500 uppercase tracking-widest">天份</span>
                              <input v-model="newItemDays" type="number" class="w-full bg-white rounded-2xl h-12 text-center text-[20px] font-black text-slate-900 focus:ring-2 focus:ring-amber-200 outline-none border-none">
                          </div>
                          <div class="space-y-2">
                              <span class="text-[11px] font-black text-amber-500 uppercase tracking-widest">張數</span>
                              <input v-model="newItemSheets" type="number" class="w-full bg-white rounded-2xl h-12 text-center text-[20px] font-black text-slate-900 focus:ring-2 focus:ring-amber-200 outline-none border-none">
                          </div>
                      </div>
                 </div>

                 <div v-else class="space-y-6 pt-4">
                     <label class="text-[11px] font-black text-slate-300 uppercase tracking-widest block">詳細參數</label>
                     <div class="grid grid-cols-1 gap-4">
                         <div class="bg-slate-50 p-4 rounded-3xl space-y-2 border border-slate-100">
                             <span class="text-[12px] font-black text-slate-400">天份 (天)</span>
                             <input v-model="newItemDays" type="number" class="bg-transparent border-none text-[22px] font-black text-slate-900 w-full focus:ring-0">
                         </div>
                     </div>
                 </div>

                 <div class="space-y-4 pt-4">
                     <label class="text-[11px] font-black text-slate-300 uppercase tracking-widest block">細項備註 (選填)</label>
                     <textarea v-model="newItemRemarks" rows="4" placeholder="輸入其他細節..." class="w-full bg-slate-50 rounded-[32px] p-6 text-[17px] font-black text-slate-900 focus:ring-2 focus:ring-indigo-100 outline-none border-none resize-none"></textarea>
                 </div>
             </div>
             <div class="p-6 border-t border-slate-50">
                 <button @click="confirmAddItem" class="w-full py-5 bg-indigo-600 text-white rounded-[24px] font-black text-[19px] active:scale-95 shadow-lg shadow-indigo-100">加入降寶清單</button>
             </div>
        </div>

        <datalist id="item-name-list">
            <option v-for="name in uniqueTreasureNames" :key="name" :value="name" />
        </datalist>
        <datalist id="footer-remark-options">
            <option value="完畢" />
            <option value="*允同享皇恩" />
        </datalist>
        <datalist id="teaching-dharma-search">
            <option v-for="dn in dharmaNames" :key="'dl-dn'+dn.id" :value="dn.name" />
            <option v-for="g in groups" :key="'dl-g'+g.id" :value="g.name" />
        </datalist>
    </div>
</template>

<script setup>
import { ref, computed, watch, onMounted, onUnmounted } from 'vue';
import LogoImperialNotebook from './LogoImperialNotebook.vue';
import MobileNavbar from './MobileNavbar.vue';
import { lockBodyScroll, unlockBodyScroll } from '../utils/iosCompat';

const props = defineProps({
    mode: String,
    initialData: Object,
    masters: Array,
    dharmaNames: Array,
    groups: Array,
    uniqueTreasureNames: Array,
    isSaving: Boolean
});

const emit = defineEmits(['save', 'close']);

// --- 1. State ---
const currentStep = ref(1);
const totalSteps = 4;
const stepTitles = ['基礎資料', '開示內容', '降寶詳情', '預覽確認'];
const currentStepTitle = computed(() => stepTitles[currentStep.value - 1]);

const form = ref({
    date: props.initialData?.date || new Date().toLocaleDateString('sv-SE'),
    master_id: props.initialData?.master_id || null,
    dharma_name_ids: [],
    target_remarks: '',
    content: '',
    items: []
});

const masterNameInput = ref('');
const dharmaSearchQuery = ref('');
const activeMasterDropdown = ref(false);
const activePractitionerDropdown = ref(false);
const activeRelDropdown = ref(false);
const showDharmaPicker = ref(false);
const showItemsSelector = ref(false);

const footerRemarks = ref([]);
const newFooterRemark = ref('');

// Batch Mode State
const batchImportContent = ref('');
const batchRecords = ref([]);

// Item Builder State
const newItemName = ref('');
const newItemDays = ref(0);
const newItemTimes = ref(0);
const newItemSheets = ref(0);
const newItemRemarks = ref('');

// --- 2. Computed ---
const filteredDharmaNames = computed(() => {
    if (!dharmaSearchQuery.value) return [];
    return props.dharmaNames.filter(dn => dn.name.includes(dharmaSearchQuery.value));
});

const filteredGroups = computed(() => {
    if (!dharmaSearchQuery.value) return [];
    return props.groups.filter(g => g.name.includes(dharmaSearchQuery.value));
});

const isGroupSelected = computed(() => !!selectedGroup.value);
const selectedGroup = ref(null);
const selectedGroupLabel = computed(() => selectedGroup.value?.name || dharmaSearchQuery.value);

const relationshipOptions = ['母親', '父親', '公公', '婆婆', '爺爺', '奶奶', '外公', '外婆'];

const itemCategory = computed(() => {
    if (!newItemName.value) return 'default';
    const n = newItemName.value;
    if (n.includes('三光金丹')) return '三光金丹';
    if (n.includes('丹')) return '金丹';
    if (n.includes('符') || n.includes('令') || n.includes('疏文')) {
        if (!n.includes('令旗') && !n.includes('令牌')) return '符令';
    }
    return 'default';
});

const finalContent = computed(() => {
    return [form.value.content, ...footerRemarks.value].filter(Boolean).join('\n') || '';
});

// --- 3. Methods ---
function resolveMasterId() {
    const m = props.masters.find(m => m.name === masterNameInput.value || (m.name === '父皇' && masterNameInput.value === '父皇仙師'));
    if (m) form.value.master_id = m.id;
}

function selectDharmaName(dn) {
    if (!form.value.dharma_name_ids.includes(dn.id)) {
        form.value.dharma_name_ids.push(dn.id);
    }
    dharmaSearchQuery.value = '';
    activePractitionerDropdown.value = false;
}

function selectGroup(g) {
    selectedGroup.value = g;
    form.value.dharma_name_ids = (g.dharma_names || g.dharmaNames || []).map(dn => dn.id);
    dharmaSearchQuery.value = g.name;
    activePractitionerDropdown.value = false;
}

function handleDharmaSearchInput(e) {
    const val = e.target.value;
    if (!val) {
        clearSelection();
        activePractitionerDropdown.value = true;
        return;
    }
    const matchedDN = props.dharmaNames.find(dn => dn.name === val);
    if (matchedDN) {
        form.value.dharma_name_ids = [matchedDN.id];
        activePractitionerDropdown.value = false;
        return;
    }
    const matchedGroup = props.groups.find(g => g.name === val || formatGroupName(g.name) === val);
    if (matchedGroup) {
        selectGroup(matchedGroup);
        activePractitionerDropdown.value = false;
        return;
    }
    activePractitionerDropdown.value = true;
}

function removeDharmaId(id) {
    form.value.dharma_name_ids = form.value.dharma_name_ids.filter(v => v !== id);
}

function clearSelection() {
    form.value.dharma_name_ids = [];
    dharmaSearchQuery.value = '';
    selectedGroup.value = null;
}

function addFooterRemark() {
    if (newFooterRemark.value.trim()) {
        footerRemarks.value.push(newFooterRemark.value.trim());
        newFooterRemark.value = '';
    }
}

function removeFooterRemark(idx) {
    footerRemarks.value.splice(idx, 1);
}

function getDharmaNameText(id) {
    const dn = props.dharmaNames.find(v => v.id === id);
    return dn ? dn.name : '未知';
}

function formatGroupName(name) {
    return name.replace('宮主', '宮主 / 副宮主');
}

function handleBack() {
    if (currentStep.value > 1) currentStep.value--;
}

function handleNext() {
    if (currentStep.value === 1) {
        if (!form.value.master_id) { alert('請選擇仙師'); return; }
        if (form.value.dharma_name_ids.length === 0 && dharmaSearchQuery.value !== '在場全體') { alert('請選擇對象'); return; }
    }
    if (currentStep.value < totalSteps) currentStep.value++;
}

function confirmAddItem() {
    if (!newItemName.value.trim()) { alert('請輸入法寶名稱'); return; }
    
    let detailsArr = [];
    if (itemCategory.value === '三光金丹' || itemCategory.value === '金丹') {
        if (newItemDays.value > 0) detailsArr.push(`${newItemDays.value}天份`);
        if (newItemTimes.value > 0) detailsArr.push(`${newItemTimes.value}顆`);
    } else if (itemCategory.value === '符令') {
        if (newItemDays.value > 0) detailsArr.push(`${newItemDays.value}天份`);
        if (newItemSheets.value > 0) detailsArr.push(`${newItemSheets.value}張`);
     } else {
         if (newItemDays.value > 0) detailsArr.push(`${newItemDays.value}天份`);
     }
    if (newItemRemarks.value.trim()) detailsArr.push(newItemRemarks.value.trim());

    form.value.items.push({
        uid: Date.now() + Math.random(),
        treasure_name: newItemName.value.trim(),
        details: detailsArr.join(' / '),
        quantity: 1
    });

    // Reset item fields
    newItemName.value = '';
     newItemDays.value = 0;
     newItemTimes.value = 0;
     newItemSheets.value = 0;
     newItemRemarks.value = '';
    showItemsSelector.value = false;
}

function removeItem(idx) {
    form.value.items.splice(idx, 1);
}

function handleBatchPaste(e) {
    const text = e.clipboardData.getData('text');
    if (!text) return;
    batchImportContent.value = text;
    // Heuristic parse (Simplified version of TeachingManager.processBatchText)
    const blocks = text.split(/\n\s*\n/);
    batchRecords.value = blocks.filter(b => b.trim()).map(b => ({
        master_name: '父皇',
        dharmaSearchQuery: '對象',
        content: b.trim()
    }));
}

function handleSubmit() {
    if (props.mode === 'batch') {
        emit('close'); 
        return;
    }

    emit('save', {
        ...form.value,
        content: finalContent.value
    });
}

// --- 4. Lifecycle ---
onMounted(() => {
    lockBodyScroll();
    if (props.initialData?.master_id) {
        const m = props.masters.find(v => v.id === props.initialData.master_id);
        if (m) masterNameInput.value = m.name;
    }
});

onUnmounted(() => {
    unlockBodyScroll();
});
</script>

<style scoped>
.animate-slide-up { animation: slideUp 0.4s cubic-bezier(0.16, 1, 0.3, 1); }
.animate-fade-in { animation: fadeIn 0.3s ease-out; }
.animate-scale-in { animation: scaleIn 0.3s cubic-bezier(0.34, 1.56, 0.64, 1); }

@keyframes slideUp { from { transform: translateY(100%); } to { transform: translateY(0); } }
@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
@keyframes scaleIn { from { opacity: 0; transform: scale(0.9); } to { opacity: 1; transform: scale(1); } }

.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }

.step-fade-enter-active,
.step-fade-leave-active {
    transition: all 0.3s ease-in-out;
}
.step-fade-enter-from {
    opacity: 0;
    transform: translateX(30px);
}
.step-fade-leave-to {
    opacity: 0;
    transform: translateX(-30px);
}
</style>
