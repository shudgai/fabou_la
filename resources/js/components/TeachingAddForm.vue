<template>
    <teleport to="body">
    <div v-if="mode" class="fixed inset-0 z-[3500] flex items-end md:items-center justify-center px-0 teaching-module" style="overscroll-behavior: contain;">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm" @click="$emit('close')"></div>

        <!-- Form Container -->
        <div class="relative w-full h-[100dvh] md:h-auto md:max-h-[95dvh] md:max-w-xl bg-white md:rounded-[32px] shadow-[0_-10px_40px_rgba(0,0,0,0.1)] overflow-hidden animate-slide-up flex flex-col">
            
            <!-- Unified Header System (Immersive Wizard Style) -->
            <div class="shrink-0 bg-white flex flex-col w-full border-b border-slate-100 relative pt-2">
                <div class="px-[15px] py-[10px] flex flex-col items-center w-full">
                    <!-- Title Row -->
                    <div class="flex items-center justify-center gap-3 w-full mb-1">
                        <logo-imperial-notebook :height="38" class="md:hidden" />
                        <h1 class="text-red-600 leading-tight font-outfit tracking-widest break-words font-black whitespace-nowrap" style="color: #dc2626 !important; font-size: 30px !important; font-weight: 900 !important;">
                            父皇仙師每日開示
                        </h1>
                    </div>
                    <!-- Subtitle Row -->
                    <div class="text-[20px] font-black text-slate-400 tracking-[0.3em] font-outfit mt-1">
                        {{ mode === 'batch' ? '多筆載錄' : '逐筆載錄' }}
                    </div>
                </div>
                <!-- Repositioned Close Button (Aligned with refined standard) -->
                <button @click="$emit('close')" class="text-slate-300 hover:text-slate-600 transition-colors p-2 absolute right-4 top-1/2 -translate-y-1/2 z-[50]">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
            </div>

            <!-- Progress Indicator (Immersive Style) -->
            <div v-if="mode === 'single'" class="px-8 pt-8 pb-2 bg-white shrink-0">
                <div class="flex items-center justify-between gap-1.5">
                    <div v-for="s in totalSteps" :key="s" 
                         class="h-1.5 flex-1 rounded-full transition-all duration-500"
                         :class="s <= currentStep ? 'bg-indigo-600 shadow-[0_0_8px_rgba(79,70,229,0.4)]' : 'bg-slate-100'">
                    </div>
                </div>
                <div class="flex justify-between mt-4 px-1">
                    <span class="text-[12px] font-black text-slate-300 uppercase tracking-[0.2em]">STEP {{ currentStep }} / {{ totalSteps }}</span>
                    <span class="text-[12px] font-black text-indigo-500 uppercase tracking-[0.2em]">{{ currentStepTitle }}</span>
                </div>
            </div>

            <!-- Scrollable Content -->
            <div ref="scrollContainer" class="flex-1 overflow-y-auto custom-scrollbar overscroll-contain bg-white">
                
                <!-- BATCH MODE UI (Legacy Style kept for now as per requests usually focusing on single wizard) -->
                <div v-if="mode === 'batch'" class="px-6 pt-6 pb-32 space-y-8">
                     <div class="bg-blue-50/30 border-2 border-dashed border-blue-100 rounded-[28px] overflow-hidden min-h-[400px] flex flex-col relative">
                        <div class="p-5 flex-1 flex flex-col">
                            <textarea v-model="batchImportContent" 
                                      @paste="handleBatchPaste"
                                      placeholder="在此貼上資料...&#10;&#10;格式例：&#10;父皇開示給對象：&#10;內容...&#10;賜降：&#10;1.法寶名稱:詳情" 
                                      class="w-full flex-1 bg-transparent border-none text-[17px] text-slate-800 focus:ring-0 outline-none resize-none font-black leading-relaxed placeholder:text-rose-400 placeholder:font-black placeholder:text-[17px]"></textarea>
                        </div>
                        <div class="px-5 pb-4 flex justify-end">
                            <button @click="processBatchParsing" class="bg-[#FFB266] text-white px-6 py-2.5 rounded-2xl text-[14px] font-black active:scale-95 transition-all shadow-sm">智慧解析</button>
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
                            <div v-if="record.content" class="text-slate-800 text-[17px] font-black leading-relaxed whitespace-pre-wrap">{{ record.content }}</div>
                            <div v-if="record.items.length > 0" class="pt-2 space-y-1 border-t border-slate-50 mt-2">
                                <div v-for="(it, iIdx) in record.items" :key="iIdx" class="text-[16px] font-black text-slate-600 flex items-start gap-2">
                                    <span class="text-indigo-400 shrink-0">{{ iIdx + 1 }}.</span>
                                    <span>{{ it.treasure_name }} <span v-if="it.details" class="text-slate-400 font-normal">：{{ it.details }}</span></span>
                                </div>
                            </div>
                            <div v-if="record.items_footer_remarks" class="text-[16px] font-black text-slate-400 pt-2 border-t border-slate-50 border-dashed mt-2 whitespace-pre-wrap italic">
                                {{ record.items_footer_remarks }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SINGLE MODE UI (6-Step Immersive Wizard) -->
                <transition v-else name="step-fade" mode="out-in">
                    <!-- STEP 1: Date -->
                    <div v-if="currentStep === 1" :key="'step-1'" class="space-y-10 animate-fade-in text-center w-full pt-[30px] px-8 pb-32">
                        <h2 class="text-[18px] font-black text-slate-900 tracking-tight leading-relaxed">請確認<span class="text-indigo-600">得知日期</span></h2>
                        <div class="max-w-md mx-auto mt-12 relative group">
                            <input v-model="form.date" type="text" placeholder="YYYY-MM-DD" 
                                class="w-full text-center text-[22px] font-black border-0 border-b-2 border-slate-200 focus:border-indigo-600 bg-transparent py-6 outline-none transition-all placeholder:text-slate-100">
                        </div>
                    </div>

                    <!-- STEP 2: Master -->
                    <div v-else-if="currentStep === 2" :key="'step-2'" class="space-y-10 animate-fade-in text-center w-full pt-[30px] px-8 pb-32">
                        <h2 class="text-[18px] font-black text-slate-900 tracking-tight leading-relaxed">錄入哪位<span class="text-red-600">仙師</span>的開示？</h2>
                        <div class="max-w-md mx-auto mt-12 relative">
                            <input v-model="masterNameInput" @change="resolveMasterId" @focus="activeMasterDropdown = true" placeholder="選擇仙師..." 
                                class="w-full text-center text-[22px] font-black border-0 border-b-2 border-slate-200 focus:border-red-600 bg-transparent py-6 outline-none transition-all placeholder:text-slate-100">
                            <div v-if="activeMasterDropdown" class="absolute left-0 top-full mt-2 w-full bg-white rounded-3xl shadow-[0_20px_50px_rgba(0,0,0,0.2)] border border-slate-100 z-[600] p-2 animate-fade-in max-h-[350px] overflow-y-auto custom-scrollbar">
                                <div v-for="m in ['老祖仙師', '元始仙師', '道祖仙師', '靈寶仙師', '父皇', '太宰仙師', '太子', '閻王仙師']" :key="m"
                                 @click.stop="masterNameInput = m; resolveMasterId(); activeMasterDropdown = false"
                                 class="px-6 h-[50px] flex items-center md:rounded-2xl hover:bg-indigo-50 font-black text-[18px] text-slate-900 active:bg-indigo-100 transition-all cursor-pointer">
                                    {{ m }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- STEP 3: Target/Group -->
                    <div v-else-if="currentStep === 3" :key="'step-3'" class="space-y-12 animate-fade-in text-center w-full pt-[30px] px-8 pb-32">
                        <div class="space-y-4">
                            <h2 class="text-[18px] font-black text-slate-900 tracking-tight leading-relaxed">此次開示的<span class="text-indigo-600">對象</span>與<span class="text-emerald-600">群組</span></h2>
                            
                            <div class="max-w-md mx-auto mt-8 relative">
                                <input type="text" 
                                       @input="handleDharmaSearchInput" 
                                       @focus="activePractitionerDropdown = true"
                                       v-model="dharmaSearchQuery"
                                       list="teaching-dharma-search"
                                       placeholder="搜尋法號或群組..." 
                                        class="w-full bg-transparent border-0 border-b-2 border-slate-200 focus:border-indigo-600 text-center text-[20px] font-black text-slate-900 py-4 outline-none transition-all placeholder:text-slate-100">
                                
                                <div v-if="activePractitionerDropdown" class="absolute left-0 top-full mt-2 w-full bg-white rounded-3xl shadow-[0_20px_50px_rgba(0,0,0,0.2)] border border-slate-100 z-[600] overflow-hidden p-2 animate-fade-in max-h-[350px] overflow-y-auto custom-scrollbar text-left">
                                    <div v-if="filteredDharmaNames.length > 0" class="px-6 py-2 text-[12px] font-black text-slate-300 uppercase tracking-widest bg-slate-50/50 mb-1 rounded-xl">法號</div>
                                    <div v-for="dn in filteredDharmaNames" :key="'dn'+dn.id"
                                         @click.stop="selectDharmaName(dn)"
                                         class="px-6 h-[48px] flex items-center md:rounded-2xl hover:bg-indigo-50 font-black text-[18px] text-slate-900 active:bg-indigo-100 transition-all cursor-pointer">
                                        {{ dn.name }}
                                    </div>
                                    <div v-if="filteredGroups.length > 0" class="px-6 py-2 text-[12px] font-black text-indigo-300 uppercase tracking-widest bg-slate-50/50 mb-1 rounded-xl">群組</div>
                                    <div v-for="g in filteredGroups" :key="'g'+g.id"
                                         @click.stop="selectGroup(g)"
                                         class="px-6 h-[48px] flex items-center md:rounded-2xl hover:bg-indigo-50 font-black text-[18px] text-indigo-600 active:bg-indigo-100 transition-all cursor-pointer">
                                        {{ formatGroupName(g.name) }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <h2 class="text-[16px] font-black text-slate-400 tracking-tight">備註對象 (選填)</h2>
                            <div class="max-w-md mx-auto relative">
                                <input v-model="form.target_remarks" @focus="activeRelDropdown = true" placeholder="例：之母親 / 長輩..." 
                                    class="w-full text-center text-[19px] font-black border-0 border-b-2 border-slate-200 focus:border-emerald-500 bg-transparent py-4 outline-none transition-all placeholder:text-slate-100">
                                <button v-if="form.target_remarks" @click="form.target_remarks = ''" class="absolute right-0 top-1/2 -translate-y-1/2 p-2 text-slate-300 hover:text-red-500 active:scale-90 transition-all">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </button>
                                
                                <div v-if="activeRelDropdown" class="absolute left-0 top-full mt-2 w-full bg-white rounded-3xl shadow-[0_20px_50px_rgba(0,0,0,0.2)] border border-slate-100 z-[610] p-2 animate-fade-in max-h-[300px] overflow-y-auto custom-scrollbar text-left">
                                     <div v-for="opt in relationshipOptions" :key="opt"
                                         @click.stop="form.target_remarks = opt; activeRelDropdown = false"
                                         class="px-6 h-[48px] flex items-center md:rounded-2xl hover:bg-emerald-50 font-black text-[18px] text-slate-900 active:bg-emerald-100 transition-all cursor-pointer">
                                        {{ opt }}
                                    </div>
                                </div>
                            </div>
                        </div>

                            <div v-if="form.dharma_name_ids.length > 0 || dharmaSearchQuery === '在場全體'" class="pt-4 space-y-4">
                                <div class="flex flex-wrap gap-2.5 justify-center">
                                    <template v-if="isGroupSelected">
                                        <div class="bg-indigo-600 text-white px-5 py-3 rounded-2xl flex items-center shadow-lg animate-scale-in">
                                            <span class="font-black text-[17px] !text-white" style="color: white !important;">{{ formatGroupName(selectedGroupLabel) }} ({{ form.dharma_name_ids.length }}人)</span>
                                            <button @click="clearSelection" class="ml-3 active:scale-90 transition-transform"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="stroke: white !important;"><path d="M6 18L18 6M6 6l12 12" stroke-width="3"/></svg></button>
                                        </div>
                                    </template>
                                    <template v-else>
                                        <div v-for="id in form.dharma_name_ids" :key="id" class="bg-slate-100 text-slate-700 px-4 py-2.5 rounded-2xl flex items-center border border-slate-200 shadow-sm animate-scale-in">
                                            <span class="font-black text-[17px]">{{ getDharmaNameText(id) }}</span>
                                            <button @click="removeDharmaId(id)" class="ml-2 text-slate-300 hover:text-red-500"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="3"/></svg></button>
                                        </div>
                                    </template>
                                </div>

                                <!-- Display individual members for groups -->
                                <div v-if="isGroupSelected" class="flex flex-wrap gap-2 justify-center animate-fade-in">
                                    <div v-for="id in form.dharma_name_ids" :key="'mem'+id" class="bg-slate-50/50 text-slate-400 px-3 py-1.5 rounded-xl border border-slate-100">
                                        <span class="font-bold text-[14px]">{{ getDharmaNameText(id) }}</span>
                                    </div>
                                </div>
                            </div>
                    </div>

                    <!-- STEP 4: Content -->
                    <div v-else-if="currentStep === 4" :key="'step-4'" class="space-y-10 animate-fade-in text-center w-full pt-[30px] px-8 pb-32">
                        <h2 class="text-[18px] font-black text-slate-900 tracking-tight leading-relaxed">請輸入<span class="text-indigo-600">開示內容</span></h2>
                        <div class="max-w-md mx-auto mt-12 relative">
                            <textarea v-model="form.content" rows="8" placeholder="請輸入主文內容..." 
                                class="w-full text-center text-[19px] font-black border-0 border-b-2 border-slate-200 focus:border-indigo-600 bg-transparent py-4 outline-none transition-all placeholder:text-slate-100 resize-none leading-relaxed"></textarea>
                        </div>
                    </div>

                    <!-- STEP 5: Magic Items Detail -->
                    <div v-else-if="currentStep === 5" :key="'step-5'" class="animate-fade-in w-full pt-[30px] px-8 pb-32">
                         <div class="space-y-12 max-w-md mx-auto w-full">
                             <div class="space-y-4">
                                 <h2 class="text-[18px] font-black text-slate-900 tracking-tight text-center leading-relaxed mb-8">此次賜降哪些<span class="text-amber-500">法寶</span>？</h2>
                                 
                                 <div v-if="form.items.length > 0" class="space-y-3 mb-8">
                                     <div v-for="(item, idx) in form.items" :key="idx" class="bg-white border border-slate-100 rounded-[28px] p-5 shadow-sm flex items-center justify-between group">
                                         <div class="flex-1 min-w-0 pr-4">
                                             <div class="flex items-center gap-2">
                                                 <span class="w-6 h-6 bg-amber-50 text-amber-600 rounded-full flex items-center justify-center text-[11px] font-black shrink-0">{{ idx + 1 }}</span>
                                                 <span class="text-[18px] font-black text-slate-900 truncate">{{ item.treasure_name }}</span>
                                             </div>
                                             <div class="mt-1 pl-8 text-[14px] text-slate-400 font-bold leading-tight">{{ item.details || '無詳細內容' }}</div>
                                         </div>
                                         <button @click="removeItem(idx)" class="p-2 text-slate-200 hover:text-red-500 active:scale-90 transition-all">
                                             <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                         </button>
                                     </div>
                                 </div>

                                 <button @click="showItemsSelector = true" class="w-full py-6 border-2 border-dashed border-slate-200 rounded-[32px] text-slate-400 hover:border-amber-200 hover:text-amber-500 transition-all flex items-center justify-center gap-2 group active:scale-[0.98]">
                                     <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4" stroke-width="3" stroke-linecap="round"/></svg>
                                     <span class="text-[17px] font-black tracking-[0.2em] uppercase">新增降寶明細</span>
                                 </button>
                             </div>

                             <div class="space-y-6">
                                 <h2 class="text-[16px] font-black text-slate-400 tracking-tight text-center">結尾備註 (選填)</h2>
                                 <div v-if="footerRemarks.length > 0" class="flex flex-wrap gap-2.5 justify-center mb-6">
                                     <div v-for="(r, idx) in footerRemarks" :key="idx" class="bg-slate-50 px-4 py-2.5 rounded-2xl flex items-center border border-slate-100 shadow-sm">
                                         <span class="font-black text-[15px] text-slate-600">{{ r }}</span>
                                         <button @click="removeFooterRemark(idx)" class="ml-2 text-slate-300 hover:text-red-500"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2.5"/></svg></button>
                                     </div>
                                 </div>
                                 <div class="relative border-0 border-b-2 border-slate-200 focus-within:border-indigo-600 transition-all">
                                     <input v-model="newFooterRemark" list="footer-remark-options" @keyup.enter="addFooterRemark" placeholder="在此輸入結尾備註，按 Enter 加入..." 
                                         class="w-full text-center text-[18px] font-black bg-transparent py-4 px-4 outline-none transition-all placeholder:text-slate-100">
                                     <button v-if="newFooterRemark" @click="addFooterRemark" class="absolute right-4 top-1/2 -translate-y-1/2 p-2 text-indigo-600 active:scale-90"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4" stroke-width="4" stroke-linecap="round"/></svg></button>
                                 </div>
                             </div>
                         </div>
                    </div>

                    <!-- STEP 6: Review -->
                    <div v-else-if="currentStep === 6" :key="'step-6'" class="space-y-8 animate-fade-in text-center w-full pt-[30px] px-8 pb-32">
                        <h2 class="text-[18px] font-black text-slate-900 tracking-tight leading-relaxed">載錄內容<span class="text-indigo-600">預覽確認</span></h2>
                        
                        <div class="max-w-md mx-auto border border-slate-100 rounded-[40px] overflow-hidden shadow-xl bg-white text-left mt-8">
                            <div class="p-8 space-y-6">
                                <div class="flex flex-col border-b border-slate-50 pb-4">
                                    <span class="text-[12px] font-black text-slate-300 uppercase tracking-widest mb-1">日期 / 仙師</span>
                                    <span class="text-[19px] font-black text-slate-900">{{ form.date }} · {{ masterNameInput }}</span>
                                </div>

                                <div class="flex flex-col border-b border-slate-50 pb-4">
                                    <span class="text-[12px] font-black text-slate-300 uppercase tracking-widest mb-1">對象</span>
                                    <span class="text-[19px] font-black text-indigo-600">{{ selectedGroupLabel || '個別法號' }} {{ form.target_remarks ? '('+form.target_remarks+')' : '' }}</span>
                                </div>

                                <div v-if="form.content" class="space-y-2 border-b border-slate-50 pb-4">
                                    <span class="text-[12px] font-black text-slate-300 uppercase tracking-widest block mb-1">開示主文</span>
                                    <p class="text-[18px] font-bold text-slate-700 leading-relaxed whitespace-pre-wrap">{{ form.content }}</p>
                                </div>

                                <div v-if="form.items.length > 0" class="space-y-4 border-b border-slate-50 pb-4">
                                    <span class="text-[12px] font-black text-slate-300 uppercase tracking-widest block mb-1">賜降明細 ({{ form.items.length }})</span>
                                    <div class="space-y-3">
                                        <div v-for="(item, idx) in form.items" :key="idx" class="flex items-start gap-4">
                                            <span class="w-6 h-6 bg-amber-50 text-amber-600 rounded-full flex items-center justify-center text-[11px] font-black shrink-0 mt-1">{{ idx + 1 }}</span>
                                            <div class="flex-1 min-w-0">
                                                <div class="text-[18px] font-black text-slate-900">{{ item.treasure_name }}</div>
                                                <div v-if="item.details" class="text-[14px] text-slate-400 font-bold">{{ item.details }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div v-if="footerRemarks.length > 0" class="space-y-2 pt-2">
                                    <span class="text-[12px] font-black text-slate-300 uppercase tracking-widest block mb-1">結尾備註</span>
                                    <div class="space-y-1.5">
                                        <div v-for="(r, idx) in sortedFooterRemarks" :key="idx" class="text-[17px] font-black text-slate-500 flex items-center gap-2.5">
                                            <span class="w-2 h-2 bg-slate-200 rounded-full"></span>
                                            {{ r }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-indigo-50/50 p-6 rounded-[32px] max-w-sm mx-auto border border-indigo-100 mt-8">
                             <div class="text-[15px] font-black text-slate-900">確認無誤後點擊「確認載錄」</div>
                        </div>
                    </div>
                </transition>
            </div>

            <!-- Unified Footer Action (Immersive Style) -->
            <div class="mt-auto shrink-0 bg-white border-t border-slate-50">
                <div class="px-3 pt-4 pb-1 bg-white flex gap-2 justify-center shadow-[0_-10px_30px_rgba(0,0,0,0.02)] relative z-[20]">
                    <button v-if="currentStep > 1" @click="handleBack"
                        class="w-[100px] py-4 bg-slate-100 text-slate-400 rounded-2xl font-black text-[18px] active:scale-95 transition-all">
                        上一步
                    </button>
                    <button v-if="mode === 'single' && currentStep < totalSteps" @click="handleNext"
                        class="flex-1 py-4 bg-indigo-600 !text-white rounded-2xl font-black text-[18px] shadow-lg shadow-indigo-100 active:scale-95 transition-all"
                        style="color: white !important;">
                        <span class="!text-white" style="color: white !important;">下一步</span>
                    </button>
                    <button v-else @click="handleSubmit" :disabled="isSaving || (mode === 'batch' && batchRecords.length === 0)"
                        class="flex-1 py-4 bg-indigo-600 !text-white rounded-2xl font-black text-[18px] shadow-lg shadow-indigo-100 active:scale-95 transition-all disabled:bg-slate-300"
                        style="color: white !important;">
                        <span class="!text-white" style="color: white !important;">
                            {{ isSaving ? '處理中...' : '確認載錄' }}
                        </span>
                    </button>
                </div>

                <!-- Navbar Wrapper Removed for Modal UX -->
                <div class="h-[env(safe-area-inset-bottom)] md:h-0"></div>
            </div>
        </div>

        <!-- ITEMS SELECTOR MODAL (Full Featured) -->
        <div v-if="showItemsSelector" class="fixed inset-0 z-[3000] bg-white flex flex-col animate-slide-up">
             <div class="bg-white px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                 <div class="flex items-center gap-3">
                     <button @click="showItemsSelector = false" class="p-2 bg-slate-50 rounded-full active:scale-90"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="3"/></svg></button>
                     <h1 class="text-[18px] font-black text-slate-900 tracking-tight">錄入法寶</h1>
                 </div>
             </div>
             <div class="flex-1 overflow-y-auto p-8 space-y-10 custom-scrollbar">
                 <div class="space-y-4">
                     <label class="text-[12px] font-black text-slate-300 uppercase tracking-widest">法宝名稱</label>
                     <input v-model="newItemName" list="item-name-list" placeholder="輸入或選擇法宝..." class="w-full text-[20px] font-black border-0 border-b-2 border-slate-200 focus:border-amber-500 py-4 outline-none transition-all placeholder:text-slate-100">
                 </div>

                 <div class="space-y-4">
                     <label class="text-[12px] font-black text-slate-300 uppercase tracking-widest block">細項備註 (選填)</label>
                     <textarea v-model="newItemRemarks" rows="2" placeholder="輸入其他細節..."
                         class="w-full text-center text-[18px] font-black border-0 border-b-2 border-slate-200 focus:border-indigo-600 bg-transparent py-4 outline-none transition-all placeholder:text-slate-100 resize-none leading-relaxed"></textarea>
                 </div>

                 <div v-if="!newItemRemarks.trim()" class="space-y-4">
                     <label class="text-[12px] font-black text-slate-300 uppercase tracking-widest block">天份 (天)</label>
                     <input v-model="newItemDays" type="number" placeholder="0" class="w-full text-center text-[22px] font-black border-0 border-b-2 border-slate-200 focus:border-amber-500 bg-transparent py-4 outline-none transition-all placeholder:text-slate-100">
                 </div>

                 
                 <div v-if="itemCategory === '三光金丹' || itemCategory === '金丹'" class="bg-indigo-50/50 p-8 rounded-[40px] space-y-8 animate-fade-in border border-indigo-100/50">
                      <div class="grid grid-cols-2 gap-4">
                          <div class="space-y-3">
                              <span class="text-[12px] font-black text-indigo-400 uppercase tracking-widest text-center block"> 顆吃</span>
                              <input v-model="newItemPillsEat" type="number" class="w-full bg-transparent border-0 border-b-2 border-slate-200/50 focus:border-indigo-600 h-14 text-center text-[24px] font-black text-slate-900 outline-none transition-all">
                          </div>
                          <div class="space-y-3">
                              <span class="text-[12px] font-black text-indigo-400 uppercase tracking-widest text-center block"> 顆洗</span>
                              <input v-model="newItemPillsWash" type="number" class="w-full bg-transparent border-0 border-b-2 border-slate-200/50 focus:border-indigo-600 h-14 text-center text-[24px] font-black text-slate-900 outline-none transition-all">
                          </div>
                          <div v-if="!newItemRemarks.trim()" class="space-y-3">
                              <span class="text-[12px] font-black text-indigo-400 uppercase tracking-widest text-center block"> 天份</span>
                              <input v-model="newItemDays" type="number" class="w-full bg-transparent border-0 border-b-2 border-slate-200/50 focus:border-indigo-600 h-14 text-center text-[24px] font-black text-slate-900 outline-none transition-all">
                          </div>
                      </div>
                 </div>


             </div>
             <div class="p-8 border-t border-slate-50">
                 <button @click="confirmAddItem" class="w-full py-6 bg-amber-500 text-white rounded-[32px] font-black text-[20px] active:scale-95 shadow-xl shadow-amber-100">加入降寶清單</button>
             </div>
        </div>

        <datalist id="item-name-list">
            <option v-for="name in uniqueTreasureNames" :key="name" :value="name" />
        </datalist>
        <datalist id="footer-remark-options">
            <option value="*允同享皇恩" />
            <option value="完畢" />
        </datalist>
        <datalist id="teaching-dharma-search">
            <option v-for="dn in dharmaNames" :key="'dl-dn'+dn.id" :value="dn.name" />
            <option v-for="g in groups" :key="'dl-g'+g.id" :value="g.name" />
        </datalist>
    </div>
    </teleport>
</template>

<script setup>
import { ref, computed, watch, onMounted, onUnmounted, nextTick } from 'vue';
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
const totalSteps = 6;
const stepTitles = ['日期', '仙師', '對象 / 群組', '開示內容', '降寶詳情', '預覽確認'];
const currentStepTitle = computed(() => stepTitles[currentStep.value - 1]);

const scrollContainer = ref(null);

function scrollToTop() {
    nextTick(() => {
        if (scrollContainer.value) {
            scrollContainer.value.scrollTo({ top: 0, behavior: 'auto' });
        }
    });
}

watch(currentStep, () => {
    scrollToTop();
});

const form = ref({
    date: props.initialData?.date || new Date().toLocaleDateString('sv-SE'),
    master_id: props.initialData?.master_id || null,
    dharma_name_ids: [],
    target_remarks: '',
    content: '',
    items: [],
    items_footer_remarks: ''
});

const masterNameInput = ref('');
const dharmaSearchQuery = ref('');
const activeMasterDropdown = ref(false);
const activePractitionerDropdown = ref(false);
const activeRelDropdown = ref(false);
const showItemsSelector = ref(false);

const footerRemarks = ref([]);
const newFooterRemark = ref('');

// Batch Mode State
const batchImportContent = ref('');
const batchRecords = ref([]);

// Item Builder State
const newItemName = ref('');
const newItemDays = ref('');
const newItemPillsEat = ref('');
const newItemPillsWash = ref('');
const newItemRemarks = ref('');

// --- 2. Computed ---
const filteredDharmaNames = computed(() => {
    if (!dharmaSearchQuery.value) return props.dharmaNames;
    return props.dharmaNames.filter(dn => dn.name.includes(dharmaSearchQuery.value));
});

const filteredGroups = computed(() => {
    if (!dharmaSearchQuery.value) return props.groups;
    return props.groups.filter(g => g.name.includes(dharmaSearchQuery.value));
});

const isGroupSelected = computed(() => !!selectedGroup.value);
const selectedGroup = ref(null);
const selectedGroupLabel = computed(() => selectedGroup.value?.name || dharmaSearchQuery.value);

// Add '長輩' as the first option
const relationshipOptions = ['長輩', '母親', '父親', '公公', '婆婆', '爺爺', '奶奶', '外公', '外婆'];

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

const sortedFooterRemarks = computed(() => {
    return [...footerRemarks.value].sort((a, b) => {
        if (a.startsWith('*') && !b.startsWith('*')) return -1;
        if (!a.startsWith('*') && b.startsWith('*')) return 1;
        return 0;
    });
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
    if (currentStep.value === 2) {
        if (!form.value.master_id) { alert('請選擇仙師'); return; }
    }
    if (currentStep.value === 3) {
        if (form.value.dharma_name_ids.length === 0 && dharmaSearchQuery.value !== '在場全體') { alert('請選擇對象'); return; }
    }
    
    if (currentStep.value < totalSteps) currentStep.value++;
}

function confirmAddItem() {
    if (!newItemName.value.trim()) { alert('請輸入法寶名稱'); return; }
    
    let detailsArr = [];
    const hasRemarks = newItemRemarks.value.trim() !== '';
    if (itemCategory.value === '三光金丹' || itemCategory.value === '金丹') {
        if (newItemPillsEat.value > 0) detailsArr.push(`${newItemPillsEat.value} 顆吃`);
        if (newItemPillsWash.value > 0) detailsArr.push(`${newItemPillsWash.value} 顆洗`);
        if (newItemDays.value > 0 && !hasRemarks) detailsArr.push(`${newItemDays.value} 天份`);
    } else {
         if (newItemDays.value > 0 && !hasRemarks) detailsArr.push(`${newItemDays.value} 天份`);
    }
    if (newItemRemarks.value.trim()) detailsArr.push(newItemRemarks.value.trim());

    form.value.items.push({
        uid: Date.now() + Math.random(),
        treasure_name: newItemName.value.trim(),
        details: detailsArr.join(' / '),
        quantity: 1
    });

    newItemName.value = '';
    newItemDays.value = '';
    newItemPillsEat.value = '';
    newItemPillsWash.value = '';
    newItemRemarks.value = '';
    showItemsSelector.value = false;
}

function removeItem(idx) {
    form.value.items.splice(idx, 1);
}

function handleBatchPaste(e) {
    e.preventDefault();
    const text = e.clipboardData.getData('text');
    if (!text) return;
    const textarea = e.target;
    const start = textarea.selectionStart;
    const end = textarea.selectionEnd;
    const currentVal = batchImportContent.value;
    batchImportContent.value = currentVal.substring(0, start) + text + currentVal.substring(end);
    setTimeout(() => { textarea.selectionStart = textarea.selectionEnd = start + text.length; }, 0);
}

function processBatchParsing() {
    if (!batchImportContent.value.trim()) return;
    const blocks = batchImportContent.value.split(/\n\s*\n/);
    batchRecords.value = blocks.filter(b => b.trim()).map(block => {
        const lines = block.split('\n');
        let record = { master_name: '父皇', dharmaSearchQuery: '對象', content: '', items: [], dharma_name_ids: [], items_footer_remarks: '' };
        let contentLines = [];
        let parsingTreasures = false;
        let remarks = [];

        lines.forEach(line => {
            const l = line.trim();
            if (!l) return;
            if (l.includes('允同享皇恩') || l.includes('完畢')) { remarks.push(l); return; }
            if (l.includes('賜降：')) { parsingTreasures = true; return; }
            const recMatch = l.match(/開示給\s*([^：:\n\r]*)/);
            if (recMatch) { record.dharmaSearchQuery = recMatch[1].trim(); return; }
            const hasTreasureKeywords = l.includes('法寶') || l.includes('天份') || l.includes('金丹') || l.includes('符') || l.includes('疏文');
            const isItem = (parsingTreasures && l.match(/^(\d+\.|[+])/)) || (hasTreasureKeywords && l.length < 60 && !l.startsWith('*'));
            if (isItem) {
                const tMatch = l.match(/^(\d+\.|[+])?\s*(.*?)([:：]\s*(.*))?$/);
                if (tMatch) record.items.push({ uid: Date.now() + Math.random(), treasure_name: tMatch[2].trim(), details: tMatch[4] || '', quantity: 1 });
            } else contentLines.push(l);
        });
        record.content = contentLines.join('\n');
        record.items_footer_remarks = remarks.join('\n');
        return record;
    });
}

function handleSubmit() {
    if (newFooterRemark.value.trim()) addFooterRemark();
    if (props.mode === 'batch') {
        if (batchRecords.value.length === 0 && !batchImportContent.value.trim()) { alert('請先貼上資料或解析紀錄'); return; }
        emit('save', { mode: 'batch', records: batchRecords.value, importContent: batchImportContent.value });
        batchImportContent.value = ''; batchRecords.value = [];
        return;
    }
    emit('save', { ...form.value, items_footer_remarks: sortedFooterRemarks.value.filter(Boolean).join('\n') });
}

onMounted(() => {
    lockBodyScroll();
    if (props.initialData?.master_id) {
        const m = props.masters.find(v => v.id === props.initialData.master_id);
        if (m) masterNameInput.value = m.name;
    }
});
onUnmounted(() => unlockBodyScroll());
</script>

<style scoped>
.animate-slide-up { animation: slideUp 0.25s cubic-bezier(0.16, 1, 0.3, 1); }
.animate-fade-in { animation: fadeIn 0.3s ease-out; }
.animate-scale-in { animation: scaleIn 0.3s cubic-bezier(0.34, 1.56, 0.64, 1); }
@keyframes slideUp { from { transform: translateY(100%); } to { transform: translateY(0); } }
@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
@keyframes scaleIn { from { opacity: 0; transform: scale(0.9); } to { opacity: 1; transform: scale(1); } }
.custom-scrollbar { -webkit-overflow-scrolling: touch; }
.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
.step-fade-enter-active, .step-fade-leave-active { transition: all 0.3s ease-in-out; }
.step-fade-enter-from { opacity: 0; transform: translateX(30px); }
.step-fade-leave-to { opacity: 0; transform: translateX(-30px); }
</style>
