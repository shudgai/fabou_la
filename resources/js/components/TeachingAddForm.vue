<template>
    <teleport to="body">
    <div v-if="mode" class="fixed inset-0 z-[3500] flex items-end md:items-center justify-center px-0 teaching-module" style="overscroll-behavior: contain;">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-slate-900/60" @click="$emit('close')"></div>

        <!-- Form Container -->
        <div class="relative w-full h-[100dvh] md:h-auto md:max-h-[95dvh] md:max-w-xl bg-white md:rounded-[32px] shadow-[0_-10px_40px_rgba(0,0,0,0.1)] animate-slide-up flex flex-col">
            
            <!-- Header: Standardized Branding (Logo + Main Title + Sub Title) -->
            <div class="px-0 flex flex-col bg-white border-b border-slate-50 relative shrink-0">
                <!-- Row 1: Global Title -->
                <div class="px-4 py-2 bg-white flex items-center gap-2 border-b border-transparent">
                    <logo-imperial-notebook :height="30" />
                    <h1 class="font-outfit !font-normal !text-black tracking-widest pt-[2px]" style="font-size: 26px !important; font-weight: 400 !important;">父皇仙師每日開示</h1>
                </div>
                <!-- Row 2: Subtitle (Category + Form Mode) -->
                <div class="px-4 py-1.5 bg-white border-b border-transparent flex items-center justify-between">
                    <div class="flex items-baseline gap-x-2 flex-1 min-w-0">
                        <span class="font-outfit font-normal text-slate-900 whitespace-nowrap" style="font-size: 23px !important; line-height: 1.1; transform: translateY(1.5px);">
                            {{ mode === 'batch' ? '多筆載錄' : '逐筆載錄' }}
                        </span>
                    </div>
                </div>
                <!-- Close Button -->
                <button @click="$emit('close')" class="text-slate-300 hover:text-slate-600 transition-colors p-2 absolute right-4 top-2 z-[50]">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
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
                    <span class="text-[12px] font-normal text-black uppercase tracking-[0.2em]">STEP {{ currentStep }} / {{ totalSteps }}</span>
                    <span class="text-[12px] font-normal text-black uppercase tracking-[0.2em]">{{ currentStepTitle }}</span>
                </div>
            </div>

            <!-- Scrollable Content -->
            <div ref="scrollContainer" class="flex-1 overflow-y-auto custom-scrollbar overscroll-contain bg-white">
                
                <!-- BATCH MODE UI -->
                <div v-if="mode === 'batch'" class="px-6 pt-6 pb-32 space-y-8">
                     <div class="bg-blue-50/30 border-2 border-dashed border-blue-100 rounded-[28px] overflow-hidden min-h-[400px] flex flex-col relative">
                        <!-- Floating Clear Cross Button -->
                        <button v-if="batchImportContent" @click="batchImportContent = ''" 
                            class="absolute top-4 right-4 z-10 w-8 h-8 flex items-center justify-center bg-white shadow-sm rounded-full text-slate-400 hover:text-red-500 hover:bg-white active:scale-90 transition-all border border-slate-100">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M6 18L18 6M6 6l12 12" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                        <div class="p-5 flex-1 flex flex-col">
                            <textarea v-model="batchImportContent" 
                                      @paste="handleBatchPaste"
                                      placeholder="在此貼上資料...&#10;&#10;格式例：&#10;父皇開示給對象：&#10;內容...&#10;賜降：&#10;1.法寶名稱:詳情" 
                                       class="w-full flex-1 bg-transparent border-none text-[17px] text-black focus:ring-0 outline-none font-normal leading-relaxed placeholder:text-black placeholder:font-normal placeholder:text-[17px] min-h-[300px]"></textarea>
                        </div>
                    </div>
                    <!-- Batch Records Preview -->
                     <div v-if="batchRecords.length > 0" class="space-y-4">
                          <div class="text-[14px] text-black font-normal px-2 flex items-center">
                             <span class="w-2 h-2 bg-emerald-500 rounded-full mr-2"></span>
                             {{ batchRecords.length }} 筆紀錄
                         </div>
                         <div v-for="(record, index) in batchRecords" :key="index" class="bg-white border border-slate-200 rounded-[32px] p-6 shadow-lg space-y-4 animate-fade-in text-left">
                             <!-- Header with Toggle and Delete -->
                             <div class="flex items-center justify-between">
                                 <div @click="record.isExpanded = !record.isExpanded" class="flex items-center gap-3 cursor-pointer group flex-1">
                                     <div class="w-10 h-10 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-600 active:scale-90 transition-all shadow-sm">
                                         <svg :class="record.isExpanded ? 'rotate-180' : ''" class="w-5 h-5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                     </div>
                                     <div class="flex flex-col">
                                         <span class="text-[17px] font-black text-slate-900 leading-tight">{{ record.isExpanded ? '收合詳情' : '點選查看對象詳情' }}</span>
                                         <span v-if="!record.isExpanded" class="text-[13px] text-slate-400 mt-0.5">{{ record.master_name }} · {{ record.dharmaSearchQuery }}</span>
                                     </div>
                                 </div>
                                 <button @click="batchRecords.splice(index, 1)" class="text-slate-200 hover:text-red-500 p-2 active:scale-90 transition-all">
                                     <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                 </button>
                             </div>

                             <!-- Editable Details Accordion -->
                             <div v-if="record.isExpanded" class="space-y-6 pt-4 border-t border-slate-50 animate-fade-in">
                                 <div class="grid grid-cols-1 gap-6">
                                     <!-- Date & Master -->
                                     <div class="grid grid-cols-2 gap-4">
                                         <div class="space-y-2">
                                             <label class="text-[11px] font-normal text-slate-400 uppercase tracking-widest ml-1">日期</label>
                                             <input v-model="record.date" type="text" class="w-full bg-slate-50 border-none rounded-xl px-4 py-3 text-[15px] text-black outline-none focus:ring-2 focus:ring-indigo-100">
                                         </div>
                                         <div class="space-y-2">
                                             <label class="text-[11px] font-normal text-slate-400 uppercase tracking-widest ml-1">仙師</label>
                                             <editable-input-chips 
                                                 v-model="record.master_name" 
                                                 variant="boxed"
                                                 placeholder="仙師"
                                                 :options="['老祖仙師', '元始仙師', '道祖仙師', '靈寶仙師', '父皇', '太宰仙師', '太子', '閻王仙師']" />
                                         </div>
                                     </div>
                                     <!-- Recipient -->
                                     <div class="space-y-2">
                                         <label class="text-[11px] font-normal text-slate-400 uppercase tracking-widest ml-1">對象 (法號或群組)</label>
                                         <editable-input-chips 
                                             v-model="record.dharmaSearchQuery" 
                                             variant="boxed"
                                             placeholder="對象"
                                             :options="combinedPractitionerOptions" />
                                     </div>
                                     <!-- Target Remarks -->
                                     <div class="space-y-2">
                                         <label class="text-[11px] font-normal text-slate-400 uppercase tracking-widest ml-1">備註對象 (選填)</label>
                                         <editable-input-chips 
                                             v-model="record.target_remarks" 
                                             variant="boxed"
                                             placeholder="對象"
                                             :options="relationshipOptions" />
                                     </div>
                                 </div>
                                 
                                 <!-- Parsed Items (Treasure) display -->
                                 <div v-if="record.items && record.items.length > 0" class="space-y-3">
                                      <label class="text-[11px] font-normal text-slate-400 uppercase tracking-widest ml-1">賜降法寶明細</label>
                                      <div class="space-y-2">
                                          <div v-for="(it, iidx) in record.items" :key="iidx" class="flex items-center justify-between bg-amber-50/50 p-4 rounded-2xl border border-amber-100 shadow-sm">
                                              <span class="text-[15px] text-black">{{ it.treasure_name }} · {{ it.details }}</span>
                                              <button @click="record.items.splice(iidx, 1)" class="p-1 text-amber-300 hover:text-red-500">
                                                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                              </button>
                                          </div>
                                      </div>
                                 </div>
                             </div>
                             
                             <!-- Content Editor (Always Visible) -->
                             <div class="space-y-2">
                                 <label v-if="record.isExpanded" class="text-[11px] font-normal text-slate-400 uppercase tracking-widest ml-1">開示內容</label>
                                 <textarea v-model="record.content"
                                     class="w-full bg-white border border-slate-100 rounded-[24px] text-[17px] font-normal text-black leading-relaxed outline-none p-4 min-h-[150px] shadow-sm focus:border-indigo-100 focus:ring-4 focus:ring-indigo-50/30 transition-all"
                                     placeholder="輸入內容..."
                                     rows="5"></textarea>
                             </div>
                         </div>
                         <button @click="addBatchRecord" class="w-full py-3 border-2 border-dashed border-indigo-200 rounded-2xl text-black font-normal text-[15px] active:scale-95 transition-all hover:border-indigo-400 hover:text-indigo-600">
                             ＋ 新增一筆
                         </button>
                     </div>
                 </div>

                <!-- SINGLE MODE UI -->
                <transition v-else name="step-fade" mode="out-in">
                    <!-- UNIFIED EDIT MODE (Redesigned Word Style) -->
                    <div v-if="isEditMode" :key="'edit-mode'" class="animate-fade-in w-full pt-[20px] px-6 pb-32 space-y-6 text-left flex flex-col items-center">
                        <!-- Header Bar -->
                        <div class="w-full max-w-4xl flex items-center justify-between px-4 mb-2">
                            <div class="flex flex-col">
                                <span class="text-[20px] font-black text-slate-900 font-outfit tracking-wider">{{ form.date.replace(/-/g, '/') }}</span>
                                <span class="text-[11px] font-black text-slate-400 uppercase tracking-[0.3em] mt-0.5">開示載錄編輯</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <button @click="handleDelete" class="w-10 h-10 rounded-full bg-rose-50 text-rose-500 flex items-center justify-center hover:bg-rose-100 transition-colors active:scale-90">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </button>
                                <button @click="showActionMenu = !showActionMenu" class="w-10 h-10 rounded-full bg-slate-50 text-slate-400 flex items-center justify-center hover:bg-slate-100 transition-colors active:scale-90">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </button>
                            </div>
                        </div>

                        <!-- Main Paper Container (The "Word" style) -->
                        <div class="w-full max-w-4xl bg-white rounded-[40px] shadow-[0_20px_60px_-15px_rgba(0,0,0,0.1)] border border-slate-50 overflow-hidden flex flex-col min-h-[85dvh] md:min-h-[700px]">
                            <!-- Top Info Banner -->
                            <div class="px-10 py-8 bg-slate-50/50 border-b border-slate-100/50 grid grid-cols-2 gap-10">
                                <!-- Recipient (對象) -->
                                <div class="space-y-3">
                                    <div class="app-title !text-indigo-600/70 leading-snug tracking-widest font-black uppercase text-[13px]">對象</div>
                                    <editable-input-chips 
                                        v-model="dharmaSearchQuery" 
                                        variant="boxed"
                                        placeholder="對象"
                                        :options="combinedPractitionerOptions" 
                                        @change="handleDharmaSearchInput({target: {value: dharmaSearchQuery}})" />
                                    
                                    <!-- Secondary Recipient Detail (Optional) -->
                                    <div v-if="!isGroupSelected" class="pt-2 animate-fade-in">
                                        <div class="app-title !text-slate-400 leading-snug tracking-widest font-black uppercase text-[11px] mb-2">備註對象</div>
                                        <editable-input-chips 
                                            v-model="form.target_remarks" 
                                            variant="boxed"
                                            placeholder="對象"
                                            :options="relationshipOptions" />
                                    </div>
                                </div>

                                <!-- Master (仙師) -->
                                <div class="space-y-3">
                                    <div class="app-title !text-indigo-600/70 leading-snug tracking-widest font-black uppercase text-[13px]">仙師</div>
                                    <editable-input-chips 
                                        v-model="masterNameInput" 
                                        variant="boxed"
                                        placeholder="仙師"
                                        :options="['老祖仙師', '元始仙師', '道祖仙師', '靈寶仙師', '父皇', '太宰仙師', '太子', '閻王仙師']" 
                                        @change="resolveMasterId" />
                                    
                                    <!-- Date Display -->
                                    <div class="pt-2">
                                        <div class="app-title !text-slate-400 leading-snug tracking-widest font-black uppercase text-[11px] mb-2">日期</div>
                                        <div class="px-6 py-4 bg-white/60 rounded-[32px] text-[17px] font-black text-slate-800 border border-slate-100/50">
                                            {{ form.date }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Content Editor Area (Premium Paper feel) -->
                            <div class="flex-1 px-10 py-10 relative">
                                <textarea v-model="form.content" 
                                    class="w-full bg-transparent border-none text-[20px] font-normal text-slate-900 leading-[1.8] outline-none resize-none placeholder:text-slate-200 custom-scrollbar"
                                    style="min-height: 400px;"
                                    placeholder="在此輸入開示內容..."></textarea>
                                
                                <!-- Treasures Section (賜降) -->
                                <div v-if="form.items.length > 0 || showItemsSelector" class="mt-12 border-t border-slate-100 pt-10 px-2 pb-10">
                                    <div class="flex items-center justify-between mb-8">
                                        <div class="app-title !text-indigo-600/70 leading-snug tracking-widest font-black uppercase text-[13px]">賜降法寶</div>
                                        <button @click="showItemsSelector = true" class="text-indigo-500 text-[14px] font-black flex items-center gap-1 hover:text-indigo-600 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4" stroke-width="3" stroke-linecap="round"/></svg>
                                            新增法寶
                                        </button>
                                    </div>
                                    
                                    <div class="space-y-4">
                                        <div v-for="(item, idx) in form.items" :key="item.uid" class="flex items-start gap-4 bg-slate-50/40 p-6 rounded-[28px] border border-slate-100 group animate-fade-in">
                                            <span class="text-slate-300 font-outfit text-[18px] mt-1.5">{{ idx + 1 }}.</span>
                                            <div class="flex-1 grid grid-cols-1 sm:grid-cols-2 gap-6">
                                                <div>
                                                    <div class="text-[11px] font-black text-slate-400 uppercase tracking-widest mb-1.5 ml-1">法寶名稱</div>
                                                    <input v-model="item.treasure_name" class="w-full bg-white/80 rounded-[18px] px-4 py-3 border border-slate-100 focus:border-indigo-200 focus:ring-4 focus:ring-indigo-500/5 outline-none text-[17px] font-normal text-slate-900 transition-all" />
                                                </div>
                                                <div>
                                                    <div class="text-[11px] font-black text-slate-400 uppercase tracking-widest mb-1.5 ml-1">細項備註</div>
                                                    <input v-model="item.details" class="w-full bg-white/80 rounded-[18px] px-4 py-3 border border-slate-100 focus:border-indigo-200 focus:ring-4 focus:ring-indigo-500/5 outline-none text-[17px] font-normal text-slate-900 transition-all" />
                                                </div>
                                            </div>
                                            <button @click="removeItem(idx)" class="p-2 text-slate-300 hover:text-rose-500 transition-all active:scale-90">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2.5" stroke-linecap="round"/></svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Footer Remarks Section -->
                                <div class="mt-8 border-t border-slate-100 pt-10 px-2 pb-20">
                                    <div class="app-title !text-indigo-600/70 leading-snug tracking-widest font-black uppercase text-[13px] mb-8">結尾備註</div>
                                    
                                    <div v-if="footerRemarks.length > 0" class="flex flex-wrap gap-3 mb-8">
                                        <div v-for="(r, idx) in sortedFooterRemarks" :key="idx" class="bg-indigo-50/50 px-5 py-3 rounded-[20px] flex items-center border border-indigo-100/50 shadow-sm animate-fade-in group">
                                            <span class="font-normal text-[16px] text-indigo-900">{{ r }}</span>
                                            <button @click="removeFooterRemark(idx)" class="ml-2.5 text-indigo-200 hover:text-rose-500 transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2.5"/></svg>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div class="relative">
                                            <editable-input-chips 
                                                v-model="newFooterRemark" 
                                                variant="boxed"
                                                :options="[]" 
                                                @change="addFooterRemark" 
                                                placeholder="自訂結尾..." />
                                        </div>
                                        <div class="flex gap-3">
                                            <button @click="quickAddFooterRemark('*允同享皇恩')" class="flex-1 py-4 rounded-[28px] border border-slate-100 bg-white text-slate-600 text-[16px] font-black active:scale-95 transition-all shadow-sm">*允同享皇恩</button>
                                            <button @click="quickAddFooterRemark('完畢')" class="flex-1 py-4 rounded-[28px] border border-slate-100 bg-white text-slate-600 text-[16px] font-black active:scale-95 transition-all shadow-sm">完畢</button>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Bottom Page Number Decor -->
                                <div class="absolute bottom-6 left-0 right-0 flex justify-center opacity-10 pointer-events-none">
                                    <logo-imperial-notebook :height="40" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- WIZARD STEPS (FOR NEW ENTRIES) -->
                    <!-- STEP 1: Date -->
                    <div v-else-if="currentStep === 1" :key="'step-1'" class="space-y-10 animate-fade-in text-center w-full pt-[30px] px-8 pb-32">
                        <h2 class="text-[18px] font-normal text-black tracking-tight leading-relaxed">請確認<span class="text-black">得知日期</span></h2>
                        <div class="max-w-md mx-auto mt-12 relative group">
                            <input v-model="form.date" type="text" placeholder="日期格式 年-月-日" 
                                class="w-full text-center text-[22px] font-normal border-0 border-b-2 border-slate-200 focus:border-indigo-600 bg-transparent py-6 outline-none transition-all placeholder:text-slate-100 text-black">
                        </div>
                    </div>

                    <!-- STEP 2: Master -->
                    <div v-else-if="currentStep === 2" :key="'step-2'" class="space-y-10 animate-fade-in text-center w-full pt-[30px] px-8 pb-32">
                        <h2 class="text-[18px] font-normal text-black tracking-tight leading-relaxed">錄入哪位<span class="text-black">仙師</span>的開示？</h2>
                        <div class="mt-12 max-w-md mx-auto">
                            <editable-input-chips 
                                v-model="masterNameInput" 
                                variant="boxed"
                                :options="['老祖仙師', '元始仙師', '道祖仙師', '靈寶仙師', '父皇', '太宰仙師', '太子', '閻王仙師']" 
                                @change="resolveMasterId" 
                                placeholder="仙師" />
                        </div>
                    </div>

                    <!-- STEP 3: Target/Group -->
                    <div v-else-if="currentStep === 3" :key="'step-3'" class="space-y-12 animate-fade-in text-center w-full pt-[30px] px-8 pb-32">
                        <div class="space-y-4">
                            <h2 class="text-[18px] font-black text-slate-900 tracking-tight leading-relaxed">此次開示的<span class="text-indigo-600">對象</span>與<span class="text-emerald-600">群組</span></h2>
                            
                            <div class="mt-8 max-w-md mx-auto">
                                <editable-input-chips 
                                    v-model="dharmaSearchQuery" 
                                    variant="boxed"
                                    :options="combinedPractitionerOptions" 
                                    @change="handleDharmaSearchInput({target: {value: dharmaSearchQuery}})" 
                                    placeholder="對象" />
                            </div>
                        </div>

                        <div class="space-y-4">
                            <h2 class="text-[16px] font-normal text-black tracking-tight">備註對象 (選填)</h2>
                            <div class="max-w-md mx-auto relative mt-4">
                                <div class="mt-4 max-w-md mx-auto">
                                    <editable-input-chips 
                                        v-model="form.target_remarks" 
                                        variant="boxed"
                                        :options="relationshipOptions" 
                                        placeholder="對象" />
                                </div>
                            </div>
                        </div>

                        <div v-if="form.dharma_name_ids.length > 0 || dharmaSearchQuery === '在場全體'" class="pt-4 space-y-4">
                            <div class="flex flex-wrap gap-2.5 justify-center">
                                <template v-if="isGroupSelected">
                                    <div class="bg-indigo-600 text-white px-5 py-3 rounded-2xl flex items-center shadow-lg animate-scale-in">
                                        <span class="font-normal text-[17px] !text-white" style="color: white !important;">{{ formatGroupName(selectedGroupLabel) }} ({{ form.dharma_name_ids.length }}人)</span>
                                        <button @click="clearSelection" class="ml-3 active:scale-90 transition-transform"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="stroke: white !important;"><path d="M6 18L18 6M6 6l12 12" stroke-width="3"/></svg></button>
                                    </div>
                                </template>
                                <template v-else>
                                    <div v-for="id in form.dharma_name_ids" :key="id" class="bg-slate-100 text-slate-700 px-4 py-2.5 rounded-2xl flex items-center border border-slate-200 shadow-sm animate-scale-in">
                                        <span class="font-normal text-[17px]">{{ getDharmaNameText(id) }}</span>
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
                    <div v-else-if="currentStep === 4" :key="'step-4'" class="space-y-6 animate-fade-in text-center w-full pt-[20px] px-8 pb-32">
                        <!-- Accordion Detail Editor -->
                        <div class="max-w-md mx-auto mb-8 bg-white border border-slate-100 rounded-[32px] p-5 shadow-sm space-y-4 text-left">
                             <div @click="recordExpanded = !recordExpanded" class="flex items-center gap-3 cursor-pointer group">
                                 <div class="w-10 h-10 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-600 active:scale-90 transition-all shadow-sm">
                                     <svg :class="recordExpanded ? 'rotate-180' : ''" class="w-5 h-5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                 </div>
                                 <div class="flex flex-col">
                                     <span class="text-[17px] font-black text-slate-900 leading-tight">{{ recordExpanded ? '收合詳情' : '點選查看對象詳情' }}</span>
                                     <span v-if="!recordExpanded" class="text-[13px] text-slate-400 mt-0.5">{{ masterNameInput }} · {{ dharmaSearchQuery }}</span>
                                 </div>
                             </div>
                             
                             <div v-if="recordExpanded" class="space-y-6 pt-4 border-t border-slate-50 animate-fade-in">
                                 <div class="space-y-6">
                                     <!-- Date & Master -->
                                     <div class="grid grid-cols-2 gap-4">
                                         <div class="space-y-2">
                                             <label class="text-[11px] font-normal text-slate-400 uppercase tracking-widest ml-1">日期</label>
                                             <input v-model="form.date" type="text" class="w-full bg-transparent border-0 border-b-2 border-slate-300 px-1 py-2 text-[15.5px] font-normal text-black outline-none focus:border-indigo-500 transition-colors">
                                         </div>
                                         <div class="space-y-2">
                                             <label class="text-[11px] font-normal text-slate-400 uppercase tracking-widest ml-1">仙師</label>
                                             <editable-input-chips 
                                                 v-model="masterNameInput" 
                                                 variant="boxed"
                                                 placeholder="仙師"
                                                 :options="['老祖仙師', '元始仙師', '道祖仙師', '靈寶仙師', '父皇', '太宰仙師', '太子', '閻王仙師']" 
                                                 @change="resolveMasterId" />
                                         </div>
                                     </div>
                                     <!-- Recipient -->
                                     <div class="space-y-2">
                                         <label class="text-[11px] font-normal text-slate-400 uppercase tracking-widest ml-1">對象 (法號或群組)</label>
                                         <editable-input-chips 
                                             v-model="dharmaSearchQuery" 
                                             variant="boxed"
                                             placeholder="對象"
                                             :options="combinedPractitionerOptions" 
                                             @change="handleDharmaSearchInput({target: {value: dharmaSearchQuery}})" />
                                     </div>
                                     <!-- Target Remarks -->
                                     <div class="space-y-2">
                                         <label class="text-[11px] font-normal text-slate-400 uppercase tracking-widest ml-1">備註對象 (選填)</label>
                                         <editable-input-chips 
                                             v-model="form.target_remarks" 
                                             variant="boxed"
                                             placeholder="對象"
                                             :options="relationshipOptions" />
                                     </div>
                                 </div>
                             </div>
                        </div>

                        <h2 class="text-[18px] font-normal text-black tracking-tight leading-relaxed">請輸入<span class="text-black">開示內容</span></h2>
                        <div class="max-w-md mx-auto mt-6">
                            <textarea v-model="form.content" 
                                class="w-full bg-transparent border-0 border-b-2 border-slate-200 text-[18px] font-normal text-black leading-relaxed outline-none px-2 py-4 min-h-[300px] transition-all focus:border-indigo-400"
                                placeholder="在此輸入開示內容..."></textarea>
                        </div>
                    </div>

                    <!-- STEP 5: Magic Items Detail -->
                    <div v-else-if="currentStep === 5" :key="'step-5'" class="animate-fade-in w-full pt-[20px] px-8 pb-32">
                         <!-- Accordion Detail Editor -->
                         <div class="max-w-md mx-auto mb-8 bg-white border border-slate-100 rounded-[32px] p-5 shadow-sm space-y-4 text-left">
                             <div @click="recordExpanded = !recordExpanded" class="flex items-center gap-3 cursor-pointer group">
                                 <div class="w-10 h-10 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-600 active:scale-90 transition-all shadow-sm">
                                     <svg :class="recordExpanded ? 'rotate-180' : ''" class="w-5 h-5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                 </div>
                                 <div class="flex flex-col">
                                     <span class="text-[17px] font-black text-slate-900 leading-tight">{{ recordExpanded ? '收合詳情' : '點選查看對象詳情' }}</span>
                                     <span v-if="!recordExpanded" class="text-[13px] text-slate-400 mt-0.5">{{ masterNameInput }} · {{ dharmaSearchQuery }}</span>
                                 </div>
                             </div>
                             
                             <div v-if="recordExpanded" class="space-y-6 pt-4 border-t border-slate-50 animate-fade-in">
                                 <div class="space-y-6">
                                     <!-- Date & Master -->
                                     <div class="grid grid-cols-2 gap-4">
                                         <div class="space-y-2">
                                             <label class="text-[11px] font-normal text-slate-400 uppercase tracking-widest ml-1">日期</label>
                                             <input v-model="form.date" type="text" class="w-full bg-transparent border-0 border-b-2 border-slate-300 px-1 py-2 text-[15.5px] font-normal text-black outline-none focus:border-indigo-500 transition-colors">
                                         </div>
                                         <div class="space-y-2">
                                             <label class="text-[11px] font-normal text-slate-400 uppercase tracking-widest ml-1">仙師</label>
                                             <editable-input-chips 
                                                 v-model="masterNameInput" 
                                                 variant="boxed"
                                                 placeholder="仙師"
                                                 :options="['老祖仙師', '元始仙師', '道祖仙師', '靈寶仙師', '父皇', '太宰仙師', '太子', '閻王仙師']" 
                                                 @change="resolveMasterId" />
                                         </div>
                                     </div>
                                     <!-- Recipient -->
                                     <div class="space-y-2">
                                         <label class="text-[11px] font-normal text-slate-400 uppercase tracking-widest ml-1">對象 (法號或群組)</label>
                                         <editable-input-chips 
                                             v-model="dharmaSearchQuery" 
                                             variant="boxed"
                                             placeholder="對象"
                                             :options="combinedPractitionerOptions" 
                                             @change="handleDharmaSearchInput({target: {value: dharmaSearchQuery}})" />
                                     </div>
                                     <!-- Target Remarks -->
                                     <div v-if="!isGroupSelected" class="space-y-2">
                                         <label class="text-[11px] font-normal text-slate-400 uppercase tracking-widest ml-1">備註對象 (選填)</label>
                                         <editable-input-chips 
                                             v-model="form.target_remarks" 
                                             variant="boxed"
                                             placeholder="對象"
                                             :options="relationshipOptions" />
                                     </div>
                                 </div>
                             </div>
                        </div>

                         <div class="space-y-12 max-w-md mx-auto w-full">
                             <div class="space-y-4">
                                 <h2 class="text-[18px] font-normal text-black tracking-tight text-center leading-relaxed mb-8">此次賜降哪些<span class="text-black">法寶</span>？</h2>
                                 
                                 <div v-if="form.items.length > 0" class="space-y-3 mb-8">
                                     <div v-for="(item, idx) in form.items" :key="idx" class="bg-white border border-slate-100 rounded-[28px] p-5 shadow-sm flex items-center justify-between group">
                                         <div class="flex-1 min-w-0 pr-4">
                                             <div class="flex items-center gap-2">
                                                 <span class="w-6 h-6 bg-amber-50 text-black rounded-full flex items-center justify-center text-[11px] font-normal shrink-0">{{ idx + 1 }}</span>
                                                 <span class="text-[18px] font-normal text-black truncate">{{ item.treasure_name }}</span>
                                             </div>
                                             <div class="mt-1 pl-8 text-[14px] text-black font-normal leading-tight">{{ item.details || '無詳細內容' }}</div>
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
                         </div>
                    </div>

                    <!-- STEP 6: Footer Remarks -->
                    <div v-else-if="currentStep === 6" :key="'step-6'" class="space-y-6 animate-fade-in text-center w-full pt-[40px] px-8 pb-32">
                        <div class="max-w-md mx-auto border-b-2 border-slate-100 pb-6 mb-8">
                            <h2 class="text-[20px] font-normal text-black tracking-[0.1em] uppercase">結尾備註 (選填)</h2>
                        </div>
                        
                        <div v-if="footerRemarks.length > 0" class="flex flex-col items-center gap-3 mb-10 px-4">
                            <div v-for="(r, idx) in sortedFooterRemarks" :key="idx" class="bg-slate-50/80 px-6 py-3.5 rounded-[24px] flex items-center border border-slate-200 shadow-sm animate-scale-in group">
                                <span class="font-normal text-[18px] text-slate-700">{{ r }}</span>
                                <button @click="removeFooterRemark(idx)" class="ml-4 text-slate-400 hover:text-red-500 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </button>
                            </div>
                        </div>

                        <div class="px-8 space-y-8 mt-12">
                            <!-- Quick Add Buttons (Vertical) -->
                            <div class="flex flex-col gap-4 mt-4">
                                <button @click="quickAddFooterRemark('*允同享皇恩')" class="w-full py-4.5 rounded-[32px] border border-slate-200 bg-slate-50/30 text-slate-700 text-[18px] font-normal active:scale-[0.97] transition-all shadow-sm hover:bg-slate-100 hover:border-slate-300">
                                    *允同享皇恩
                                </button>
                                <button @click="quickAddFooterRemark('完畢')" class="w-full py-4.5 rounded-[32px] border border-slate-200 bg-slate-50/30 text-slate-700 text-[18px] font-normal active:scale-[0.97] transition-all shadow-sm hover:bg-slate-100 hover:border-slate-300">
                                    完畢
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- STEP 7: Review -->
                    <div v-else-if="currentStep === 7" :key="'step-7'" class="space-y-8 animate-fade-in text-center w-full pt-[30px] px-8 pb-32">
                        <h2 class="text-[18px] font-normal text-black tracking-tight leading-relaxed">載錄內容<span class="text-black">預覽確認</span></h2>
                        
                        <div class="max-w-md mx-auto border border-slate-100 rounded-[40px] overflow-hidden shadow-xl bg-white text-left mt-8">
                            <div class="p-8 space-y-6">
                                <div class="flex flex-col border-b border-slate-50 pb-4">
                                    <span class="text-[12px] font-normal text-black uppercase tracking-widest mb-1">日期 / 仙師</span>
                                    <span class="text-[19px] font-normal text-black">{{ form.date }} · {{ masterNameInput }}</span>
                                </div>
                                
                                <div class="flex flex-col border-b border-slate-50 pb-4">
                                    <span class="text-[12px] font-normal text-black uppercase tracking-widest mb-1">對象</span>
                                    <span class="text-[19px] font-normal text-black">{{ dharmaSearchQuery }}</span>
                                    <span v-if="form.target_remarks" class="text-[14px] font-normal text-black mt-1">備註：{{ form.target_remarks }}</span>
                                </div>

                                <div class="flex flex-col border-b border-slate-50 pb-4">
                                    <span class="text-[12px] font-normal text-black uppercase tracking-widest mb-1">開示內容</span>
                                    <p class="text-[17px] font-normal text-black leading-relaxed whitespace-pre-wrap">{{ form.content }}</p>
                                </div>

                                <div v-if="form.items.length > 0" class="space-y-4">
                                    <span class="text-[12px] font-normal text-black uppercase tracking-widest block">賜降法寶</span>
                                    <div class="space-y-3">
                                        <div v-for="(item, idx) in form.items" :key="idx" class="flex items-start gap-3 bg-slate-50/50 p-4 rounded-2xl border border-slate-100">
                                            <span class="text-black font-normal text-[16px]">{{ idx + 1 }}.</span>
                                            <div class="flex flex-col">
                                                <span class="text-[17px] font-normal text-black">{{ item.treasure_name }}</span>
                                                <span v-if="item.details" class="text-[14px] font-normal text-black mt-0.5">{{ item.details }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div v-if="footerRemarks.length > 0" class="space-y-2 pt-2">
                                    <span class="text-[12px] font-normal text-black uppercase tracking-widest block mb-1">結尾備註</span>
                                    <div class="space-y-1.5">
                                        <div v-for="(r, idx) in sortedFooterRemarks" :key="idx" class="text-[17px] font-normal text-black flex items-center gap-2.5">
                                            <span class="w-2 h-2 bg-slate-200 rounded-full"></span>
                                            {{ r }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-indigo-50/50 p-6 rounded-[32px] max-w-sm mx-auto border border-indigo-100 mt-8">
                             <div class="text-[15px] font-normal text-black">確認無誤後點擊「確認載錄」</div>
                        </div>
                    </div>
                </transition>
            </div>

            <!-- Unified Footer Action (Immersive Style) -->
            <div class="mt-auto shrink-0 bg-white border-t border-slate-50">
                <div class="px-3 pt-4 pb-1 bg-white flex gap-2 justify-center shadow-[0_-10px_30px_rgba(0,0,0,0.02)] relative z-[20]">
                    <button v-if="!isEditMode && currentStep > 1" @click="handleBack"
                        class="w-[100px] py-4 bg-slate-100 text-slate-400 rounded-2xl font-normal text-[18px] active:scale-95 transition-all">
                        上一步
                    </button>
                    <button v-if="!isEditMode && mode === 'single' && currentStep < totalSteps" @click="handleNext"
                        class="flex-1 py-4 bg-indigo-600 !text-white rounded-2xl font-normal text-[18px] shadow-lg shadow-indigo-100 active:scale-95 transition-all"
                        style="color: white !important;">
                        <span class="!text-white" style="color: white !important;">下一步</span>
                    </button>
                    <button v-else @click="handleSubmit" :disabled="props.isSaving || (mode === 'batch' && batchRecords.length === 0)"
                        class="flex-1 py-4 bg-indigo-600 !text-white rounded-2xl font-normal text-[18px] shadow-lg shadow-indigo-100 active:scale-95 transition-all disabled:bg-slate-300"
                        style="color: white !important;">
                        <span class="!text-white" style="color: white !important;">
                            {{ props.isSaving ? '處理中...' : '確認載錄' }}
                        </span>
                    </button>
                </div>

                <!-- Navbar Wrapper Removed for Modal UX -->
                <div class="h-[env(safe-area-inset-bottom)] md:h-0"></div>
            </div>
        </div>

        <!-- ITEMS SELECTOR MODAL (Full Featured) -->
        <teleport to="body">
            <div v-if="showItemsSelector" class="fixed inset-0 z-[5000] flex items-end md:items-center justify-center px-0">
                <!-- Backdrop -->
                <div class="fixed inset-0 bg-slate-900/60" @click="showItemsSelector = false"></div>
                
                <!-- Modal Container -->
                <div class="relative w-full h-[100dvh] md:h-auto md:max-h-[90dvh] md:max-w-xl bg-white md:rounded-[32px] shadow-2xl animate-slide-up flex flex-col overflow-hidden">
                    <!-- Header -->
                    <div class="bg-white px-6 py-4 border-b border-slate-100 flex items-center shrink-0">
                        <button @click="showItemsSelector = false" class="p-2 -ml-2 text-slate-400 active:scale-90 transition-all">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </button>
                        <h1 class="text-[20px] font-normal text-black ml-1">錄入法寶</h1>
                    </div>
                    <!-- Scrollable Content -->
                    <div class="flex-1 overflow-y-auto p-8 space-y-10 custom-scrollbar overscroll-contain bg-white">
                        <div class="space-y-4">
                            <label class="text-[12px] font-normal text-slate-400 uppercase tracking-widest block text-center mb-4">法寶名稱</label>
                            <editable-input-chips 
                                v-model="newItemName" 
                                variant="boxed"
                                :options="props.uniqueTreasureNames" 
                                placeholder="法寶名稱" />
                        </div>

                        <div class="space-y-4">
                            <label class="text-[12px] font-normal text-slate-400 uppercase tracking-widest block">細項備註 (選填)</label>
                            <textarea v-model="newItemRemarks" rows="4" placeholder="輸入其他細節..."
                                class="w-full text-[19px] font-normal border-0 border-b-2 border-slate-200 focus:border-amber-500 bg-transparent py-2 outline-none transition-all placeholder:text-slate-100 leading-relaxed min-h-[100px] text-black"></textarea>
                        </div>

                        <div v-if="!newItemRemarks.trim()" class="space-y-4 animate-fade-in">
                            <label class="text-[12px] font-normal text-slate-400 uppercase tracking-widest block">天份數量 (選填)</label>
                            <input v-model.number="newItemDays" type="number" placeholder="天數..." class="w-full text-[20px] font-normal border-0 border-b-2 border-slate-200 focus:border-amber-500 py-4 outline-none transition-all bg-transparent text-black">
                        </div>
                    </div>

                    <!-- Sticky Footer Button -->
                    <div class="px-6 py-4 bg-white border-t border-slate-50 shrink-0">
                        <button @click="confirmAddItem" class="w-full py-5 bg-amber-500 text-white rounded-[24px] font-normal text-[20px] active:scale-95 shadow-lg shadow-amber-100 transition-all" style="color: white !important;">
                            加入降寶清單
                        </button>
                        <!-- Mobile bottom safe area -->
                        <div class="h-[env(safe-area-inset-bottom)] md:h-0"></div>
                    </div>
                </div>
            </div>
        </teleport>







    </div>
    </teleport>
</template>

<script setup>
import { ref, computed, watch, onMounted, onUnmounted, nextTick } from 'vue';
import LogoImperialNotebook from './LogoImperialNotebook.vue';
import MobileNavbar from './MobileNavbar.vue';
import EditableInputChips from './EditableInputChips.vue';
import { lockBodyScroll, unlockBodyScroll } from '../utils/iosCompat';

const props = defineProps({
    mode: String,
    masters: Array,
    dharmaNames: Array,
    groups: Array,
    uniqueTreasureNames: Array,
    initialData: Object,
    isSaving: Boolean
});

const emit = defineEmits(['save', 'close']);

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

// Wizard Step State
const currentStep = ref(1);
const totalSteps = 7;
const stepTitles = ['選擇日期', '選擇仙師', '選擇對象', '輸入內容', '降寶明細', '結尾備註', '預覽確認'];
const currentStepTitle = computed(() => stepTitles[currentStep.value - 1]);

// Form State
const scrollContainer = ref(null);
const recordExpanded = ref(false);
const isEditMode = computed(() => !!props.initialData?.id);

const form = ref({
    date: props.initialData?.date || new Date().toISOString().slice(0, 10),
    master_id: props.initialData?.master_id || null,
    dharma_name_ids: props.initialData?.dharma_name_ids || [],
    target_remarks: props.initialData?.target_remarks || '',
    content: props.initialData?.content || '',
    items: props.initialData?.items || [],
    items_footer_remarks: props.initialData?.items_footer_remarks || ''
});

// Watch for step changes to reset scroll position
watch(currentStep, () => {
    nextTick(() => {
        if (scrollContainer.value) {
            scrollContainer.value.scrollTo({ top: 0, behavior: 'instant' });
        }
    });
});

// Input State
const masterNameInput = ref(props.initialData?.master?.name || props.initialData?.master_name || '');
const dharmaSearchQuery = ref(props.initialData?.dharmaSearchQuery || '');
const showItemsSelector = ref(false);

// If editing, ensure content includes master prefix if missing
onMounted(() => {
    if (isEditMode.value && form.value.content) {
        const mName = masterNameInput.value || '仙師';
        const prefix = `${mName}開示：`;
        if (!form.value.content.trim().startsWith(prefix)) {
            // Only add if not already there to avoid duplicates
            // form.value.content = prefix + '\n' + form.value.content;
        }
    }
    lockBodyScroll();
});

// --- 2. Computed ---
const masterFilteredList = computed(() => {
    const masters = ['老祖仙師', '元始仙師', '道祖仙師', '靈寶仙師', '父皇', '太宰仙師', '太子', '閻王仙師'];
    if (!masterNameInput.value) return masters;
    return masters.filter(m => m.includes(masterNameInput.value));
});

const practitionerFilteredNames = computed(() => {
    if (!dharmaSearchQuery.value) return props.dharmaNames;
    return props.dharmaNames.filter(dn => dn.name.includes(dharmaSearchQuery.value));
});

const practitionerFilteredGroups = computed(() => {
    if (!dharmaSearchQuery.value) return props.groups;
    return props.groups.filter(g => g.name.includes(dharmaSearchQuery.value));
});

const treasureFilteredList = computed(() => {
    if (!newItemName.value) return props.uniqueTreasureNames;
    return props.uniqueTreasureNames.filter(n => n.includes(newItemName.value));
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
    const input = (masterNameInput.value || '').trim();
    const m = props.masters.find(m => m.name === input || m.name === input.replace('仙師', '').trim() || (m.name === '父皇' && (input === '父皇仙師' || input === '父皇')));
    if (m) { form.value.master_id = m.id; return; }
    const hardcoded = { '老祖仙師': 1, '元始仙師': 2, '道祖仙師': 3, '靈寶仙師': 4, '父皇仙師': 5, '父皇': 5, '太宰仙師': 6, '太子': 7, '閻王仙師': 8 };
    if (hardcoded[input]) { form.value.master_id = hardcoded[input]; return; }
    form.value.master_id = null;
}

function selectDharmaName(dn) {
    if (!form.value.dharma_name_ids.includes(dn.id)) {
        form.value.dharma_name_ids.push(dn.id);
    }
    dharmaSearchQuery.value = dn.name;
    selectedGroup.value = null;
    form.value.target_remarks = '';
}

function selectGroup(g) {
    selectedGroup.value = g;
    form.value.dharma_name_ids = (g.dharma_names || g.dharmaNames || []).map(dn => dn.id);
    dharmaSearchQuery.value = g.name;
    form.value.target_remarks = g.name;
}

function handleDharmaSearchInput(e) {
    const val = (e?.target?.value || dharmaSearchQuery.value || '').trim();
    if (!val || val === '對象') {
        form.value.dharma_name_ids = [];
        return;
    }
    
    // Check for "在場全體"
    if (val === '在場全體') {
        form.value.dharma_name_ids = []; // Virtual group
        form.value.target_remarks = '在場全體';
        return;
    }

    const matchedDN = props.dharmaNames.find(dn => dn.name === val);
    if (matchedDN) {
        form.value.dharma_name_ids = [matchedDN.id];
        form.value.target_remarks = ''; // Clear remarks if it's a direct name match
        return;
    }
    const matchedGroup = props.groups.find(g => g.name === val || formatGroupName(g.name) === val);
    if (matchedGroup) {
        form.value.dharma_name_ids = (matchedGroup.dharma_names || matchedGroup.dharmaNames || []).map(dn => dn.id);
        form.value.target_remarks = matchedGroup.name;
        return;
    }
    
    // If no match found, treat as custom recipient
    form.value.dharma_name_ids = [];
    form.value.target_remarks = val;
}

function removeDharmaId(id) {
    form.value.dharma_name_ids = form.value.dharma_name_ids.filter(v => v !== id);
}

function clearSelection() {
    form.value.dharma_name_ids = [];
    dharmaSearchQuery.value = '';
    selectedGroup.value = null;
    form.value.target_remarks = '';
}

function addFooterRemark() {
    if (newFooterRemark.value.trim()) {
        if (!footerRemarks.value.includes(newFooterRemark.value.trim())) {
            footerRemarks.value.push(newFooterRemark.value.trim());
        }
        newFooterRemark.value = '';
    }
}

function quickAddFooterRemark(val) {
    if (!footerRemarks.value.includes(val)) {
        footerRemarks.value.push(val);
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
        resolveMasterId();
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
    
    // Match everything up to "完畢" as a single block
    const rawBlocks = batchImportContent.value.match(/[\s\S]*?完畢[！!]?/g) || [batchImportContent.value];
    
    batchRecords.value = rawBlocks.filter(b => b.trim()).map(block => {
        const fullBlockText = block.trim();
        const lines = fullBlockText.split('\n');
        
        let record = { 
            master_name: '父皇', 
            dharmaSearchQuery: '對象', 
            target_remarks: '',
            date: form.value.date || new Date().toISOString().slice(0, 10),
            rawRecipientLine: '',
            content: fullBlockText, // Keep the FULL original text here
            items: [], 
            dharma_name_ids: [], 
            items_footer_remarks: '',
            isExpanded: false
        };

        lines.forEach(line => {
            const l = line.trim();
            if (!l) return;
            
            // Footer remarks (Still parse for logic, but don't separate for display)
            if (l.includes('允同享皇恩') || l.includes('完畢')) {
                if (!record.items_footer_remarks.includes(l)) {
                    record.items_footer_remarks += (record.items_footer_remarks ? '\n' : '') + l;
                }
            }

            // Master detection
            const masters = ['老祖', '元始', '道祖', '靈寶', '父皇', '太宰', '太子', '閻王'];
            masters.forEach(m => {
                if (l.includes(m) && record.master_name === '父皇') {
                    record.master_name = m;
                }
            });

            // Recipient detection
            const recMatch = l.match(/(?:開示給|給|開示給對象)\s*[:：]?\s*(.*)/);
            if (recMatch && !record.rawRecipientLine) {
                let recipient = recMatch[1].trim();
                recipient = recipient.replace(/[:：]$/, '').trim();
                record.rawRecipientLine = l;
                record.dharmaSearchQuery = recipient || '對象';
            }

            // Item detection (For database only)
            if (l.includes('賜降') && l.length > 3 && !l.endsWith('：') && !l.endsWith(':')) {
                const tMatch = l.match(/^(\d+[\.、]|[+\-•])?\s*(.*?)([:：]\s*(.*))?$/);
                if (tMatch && tMatch[2].trim()) {
                    record.items.push({ 
                        uid: Date.now() + Math.random(), 
                        treasure_name: tMatch[2].trim(), 
                        details: tMatch[4] || '', 
                        quantity: 1 
                    });
                }
            }
        });
        
        return record;
    });
}

function addBatchRecord() {
    batchRecords.value.push({
        master_name: '父皇',
        dharmaSearchQuery: '',
        target_remarks: '',
        date: form.value.date || new Date().toISOString().slice(0, 10),
        rawRecipientLine: '',
        content: '',
        items: [],
        dharma_name_ids: [],
        items_footer_remarks: '',
        isExpanded: true // New records start expanded for input
    });
}

watch(batchImportContent, () => {
    processBatchParsing();
});

const combinedPractitionerOptions = computed(() => {
    const list = [...props.dharmaNames.map(dn => dn.name), ...props.groups.map(g => g.name)];
    if (!list.includes('在場全體')) list.unshift('在場全體');
    return list;
});

function handleSubmit() {
    if (props.isSaving) return;
    
    // Resolve IDs before submitting to ensure modifications are captured
    resolveMasterId();
    handleDharmaSearchInput();

    if (newFooterRemark.value.trim()) addFooterRemark();
    if (props.mode === 'batch') {
        if (batchRecords.value.length === 0 && !batchImportContent.value.trim()) { alert('請先貼上資料或解析紀錄'); return; }
        emit('save', { mode: 'batch', records: batchRecords.value, importContent: batchImportContent.value });
        batchImportContent.value = ''; batchRecords.value = [];
        return;
    }
    emit('save', { ...form.value, dharmaSearchQuery: dharmaSearchQuery.value, items_footer_remarks: sortedFooterRemarks.value.filter(Boolean).join('\n') });
}

onMounted(() => {
    lockBodyScroll();
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