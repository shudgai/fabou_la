<template>
    <teleport to="body">
    <div v-if="mode" class="fixed inset-0 z-[3500] flex items-end md:items-center justify-center px-0 teaching-module" style="overscroll-behavior: contain;">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-slate-900/60" @click="$emit('close')"></div>

        <!-- Form Container -->
        <div class="relative w-full h-[100dvh] md:h-auto md:max-h-[95dvh] md:max-w-xl bg-white md:rounded-[32px] shadow-[0_-10px_40px_rgba(0,0,0,0.1)] animate-slide-up flex flex-col">
            
            <!-- Unified Header System (Immersive Wizard Style) -->
            <div class="shrink-0 bg-white flex flex-col w-full border-b border-slate-100 relative pt-2">
                <div class="px-[15px] py-[10px] flex flex-col items-center w-full">
                    <!-- Title Row -->
                    <div class="flex items-center justify-center gap-3 w-full">
                        <logo-imperial-notebook :height="44" class="md:hidden" />
                        <h1 class="text-red-600 leading-tight font-outfit tracking-tight break-words font-black whitespace-nowrap" style="color: #dc2626 !important; font-size: 30px !important; font-weight: 900 !important;">
                            父皇仙師每日開示
                        </h1>
                    </div>
                    <!-- Subtitle Row with Close Button -->
                    <div class="flex items-center w-full mt-1">
                        <div class="w-10 shrink-0"></div>
                        <div class="flex-1 text-center text-[20px] font-black text-slate-400 tracking-[0.3em] font-outfit">
                            {{ mode === 'batch' ? '多筆載錄' : '逐筆載錄' }}
                        </div>
                        <button @click="$emit('close')" class="shrink-0 w-10 flex items-center justify-center text-slate-300 hover:text-slate-600 transition-colors p-1 z-[50]">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </button>
                    </div>
                </div>
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
                                      class="w-full flex-1 bg-transparent border-none text-[17px] text-slate-800 focus:ring-0 outline-none resize-none font-black leading-relaxed placeholder:text-rose-400 placeholder:font-black placeholder:text-[17px]"></textarea>
                        </div>
                    </div>
                    <!-- Batch Records Preview -->
                    <div v-if="batchRecords.length > 0" class="space-y-4">
                         <div class="text-[14px] text-slate-400 font-bold px-2 flex items-center">
                            <span class="w-2 h-2 bg-emerald-500 rounded-full mr-2"></span>
                            已解析紀錄 ({{ batchRecords.length }} 筆)
                        </div>
                        <div v-for="(record, index) in batchRecords" :key="index" class="bg-white border border-slate-200 rounded-3xl p-5 shadow-lg space-y-4 animate-fade-in text-left">
                            <div class="flex items-center justify-between">
                                <span class="text-[12px] font-black text-indigo-600 bg-indigo-50 px-2.5 py-1 rounded-2xl">#{{ index + 1 }} {{ record.master_name }}</span>
                                <button @click="batchRecords.splice(index, 1)" class="text-slate-300 hover:text-red-500 p-1">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </button>
                            </div>
                            
                            <div v-if="record.content && record.content.trim()" class="text-slate-800 text-[17px] font-black leading-relaxed whitespace-pre-wrap">
                                {{ record.content }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SINGLE MODE UI -->
                <transition v-else name="step-fade" mode="out-in">
                    <!-- STEP 1: Date -->
                    <div v-if="currentStep === 1" :key="'step-1'" class="space-y-10 animate-fade-in text-center w-full pt-[30px] px-8 pb-32">
                        <h2 class="text-[18px] font-black text-slate-900 tracking-tight leading-relaxed">請確認<span class="text-indigo-600">得知日期</span></h2>
                        <div class="max-w-md mx-auto mt-12 relative group">
                            <input v-model="form.date" type="text" placeholder="日期格式 年-月-日" 
                                class="w-full text-center text-[22px] font-black border-0 border-b-2 border-slate-200 focus:border-indigo-600 bg-transparent py-6 outline-none transition-all placeholder:text-slate-100">
                        </div>
                    </div>

                    <!-- STEP 2: Master -->
                    <div v-else-if="currentStep === 2" :key="'step-2'" class="space-y-10 animate-fade-in text-center w-full pt-[30px] px-8 pb-32">
                        <h2 class="text-[18px] font-black text-slate-900 tracking-tight leading-relaxed">錄入哪位<span class="text-red-600">仙師</span>的開示？</h2>
                        <div class="max-w-md mx-auto mt-12 relative flex items-center">
                            <input v-model="masterNameInput" 
                                ref="masterInputEl"
                                @click="showMasterDropdown = true; openMasterDropdown()"
                                @focus="showMasterDropdown = true; openMasterDropdown()"
                                @input="showMasterDropdown = true; openMasterDropdown()"
                                @change="resolveMasterId"
                                @blur="delayClose(showMasterDropdown, 200)"
                                autocomplete="off"
                                placeholder="選擇仙師..." 
                                class="w-full text-center text-[22px] font-black border-0 border-b-2 border-slate-200 focus:border-red-600 bg-transparent py-6 outline-none transition-all placeholder:text-slate-100">
                            <button @click.prevent="showMasterDropdown = !showMasterDropdown; if (showMasterDropdown) openMasterDropdown()" class="absolute right-2 top-1/2 -translate-y-1/2 p-2 text-slate-400 hover:text-red-600 active:scale-90 transition-all">
                                <svg class="w-5 h-5 transition-transform" :class="showMasterDropdown ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </button>
                        </div>
                    </div>

                    <!-- STEP 3: Target/Group -->
                    <div v-else-if="currentStep === 3" :key="'step-3'" class="space-y-12 animate-fade-in text-center w-full pt-[30px] px-8 pb-32">
                        <div class="space-y-4">
                            <h2 class="text-[18px] font-black text-slate-900 tracking-tight leading-relaxed">此次開示的<span class="text-indigo-600">對象</span>與<span class="text-emerald-600">群組</span></h2>
                            
                            <div class="max-w-md mx-auto mt-8 relative flex items-center">
                                <input ref="dharmaSearchInputEl"
                                       type="text"
                                        @click="showPractitionerDropdown = true; openPractitionerDropdown()"
                                        @focus="showPractitionerDropdown = true; openPractitionerDropdown()"
                                        @input="handleDharmaSearchInput; if (!showPractitionerDropdown) openPractitionerDropdown()" 
                                       v-model="dharmaSearchQuery"
                                       @blur="delayClose(showPractitionerDropdown, 200)"
                                       autocomplete="off"
                                       placeholder="搜尋法號或群組..." 
                                       class="w-full bg-transparent border-0 border-b-2 border-slate-200 focus:border-indigo-600 text-center text-[20px] font-black text-slate-900 py-4 outline-none transition-all placeholder:text-slate-100">
                                <button @click.prevent="showPractitionerDropdown = !showPractitionerDropdown; if (showPractitionerDropdown) openPractitionerDropdown()" class="absolute right-2 top-1/2 -translate-y-1/2 p-2 text-slate-400 hover:text-indigo-600 active:scale-90 transition-all">
                                    <svg class="w-5 h-5 transition-transform" :class="showPractitionerDropdown ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </button>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <h2 class="text-[16px] font-black text-slate-400 tracking-tight">備註對象 (選填)</h2>
                            <div class="max-w-md mx-auto relative">
                                <input v-model="form.target_remarks" 
                                    ref="relInputEl"
                                    @click="activeRelDropdown = true; openRelDropdown()"
                                    @input="activeRelDropdown = true; openRelDropdown()"
                                    @focus="openRelDropdown"
                                    @blur="delayClose(activeRelDropdown, 150)"
                                    autocomplete="off"
                                    placeholder="例：之母親 / 長輩..." 
                                    class="w-full text-center text-[19px] font-black border-0 border-b-2 border-slate-200 focus:border-emerald-500 bg-transparent py-4 outline-none transition-all placeholder:text-slate-100">
                                <button v-if="form.target_remarks" @click="form.target_remarks = ''" class="absolute right-0 top-1/2 -translate-y-1/2 p-2 text-slate-300 hover:text-red-500 active:scale-90 transition-all">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </button>
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
                                     <input v-model="newFooterRemark" @keyup.enter="addFooterRemark" placeholder="在此輸入結尾備註，按 Enter 加入..." 
                                         class="w-full text-center text-[18px] font-black bg-transparent py-4 px-4 outline-none transition-all placeholder:text-slate-100">
                                     <button v-if="newFooterRemark" @click="addFooterRemark" class="absolute right-4 top-1/2 -translate-y-1/2 p-2 text-indigo-600 active:scale-90"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4" stroke-width="4" stroke-linecap="round"/></svg></button>
                                 </div>
                                 <compact-datalist v-model="newFooterRemark" :options="['*允同享皇恩', '完畢']" />
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
                                    <span class="text-[19px] font-black text-slate-900">{{ dharmaSearchQuery }}</span>
                                    <span v-if="form.target_remarks" class="text-[14px] font-black text-emerald-600 mt-1">備註：{{ form.target_remarks }}</span>
                                </div>

                                <div class="flex flex-col border-b border-slate-50 pb-4">
                                    <span class="text-[12px] font-black text-slate-300 uppercase tracking-widest mb-1">開示內容</span>
                                    <p class="text-[17px] font-black text-slate-800 leading-relaxed whitespace-pre-wrap">{{ form.content }}</p>
                                </div>

                                <div v-if="form.items.length > 0" class="space-y-4">
                                    <span class="text-[12px] font-black text-slate-300 uppercase tracking-widest block">賜降法寶</span>
                                    <div class="space-y-3">
                                        <div v-for="(item, idx) in form.items" :key="idx" class="flex items-start gap-3 bg-slate-50/50 p-4 rounded-2xl border border-slate-100">
                                            <span class="text-indigo-500 font-black text-[16px]">{{ idx + 1 }}.</span>
                                            <div class="flex flex-col">
                                                <span class="text-[17px] font-black text-slate-900">{{ item.treasure_name }}</span>
                                                <span v-if="item.details" class="text-[14px] font-bold text-slate-400 mt-0.5">{{ item.details }}</span>
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
                     <button @click="showItemsSelector = false" class="p-2 bg-slate-50 rounded-full active:scale-90"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg></button>
                     <h1 class="text-[18px] font-black text-slate-900 tracking-tight">錄入法寶</h1>
                 </div>
             </div>
             <div class="flex-1 overflow-y-auto p-8 space-y-10 custom-scrollbar">
                     <div class="space-y-4">
                         <label class="text-[12px] font-black text-slate-300 uppercase tracking-widest">法宝名稱</label>
                          <div class="relative">
                                <input v-model="newItemName"
                                    ref="treasureInputEl"
                                    @click="showTreasureDropdown = true; openTreasureDropdown()"
                                    @input="showTreasureDropdown = true; openTreasureDropdown()"
                                    @focus="openTreasureDropdown"
                                    @blur="delayClose(showTreasureDropdown, 200)"
                                    autocomplete="off"
                                    placeholder="輸入或選擇法宝..." 
                                    class="w-full pr-10 text-[20px] font-black border-0 border-b-2 border-slate-200 focus:border-amber-500 py-4 outline-none transition-all placeholder:text-slate-100">
                                <button v-if="newItemName" @click="newItemName = ''" class="absolute right-2 p-2 text-slate-300 hover:text-red-500 transition-all active:scale-90 top-3">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </button>
                          </div>
                     </div>

                 <div class="space-y-4">
                     <label class="text-[12px] font-black text-slate-300 uppercase tracking-widest block">細項備註 (選填)</label>
                     <textarea v-model="newItemRemarks" rows="2" placeholder="輸入其他細節..."
                         class="w-full text-[19px] font-black border-0 border-b-2 border-slate-200 focus:border-amber-500 bg-transparent py-2 outline-none transition-all placeholder:text-slate-100 resize-none"></textarea>
                 </div>

                 <div v-if="!newItemRemarks.trim()" class="space-y-4 animate-fade-in">
                      <label class="text-[12px] font-black text-slate-300 uppercase tracking-widest block">天份數量 (選填)</label>
                      <input v-model.number="newItemDays" type="number" placeholder="天數..." class="w-full text-[20px] font-black border-0 border-b-2 border-slate-200 focus:border-amber-500 py-4 outline-none transition-all bg-transparent">
                 </div>


                 <button @click="confirmAddItem" class="w-full py-6 bg-amber-500 text-white rounded-[32px] font-black text-[20px] active:scale-95 shadow-xl shadow-amber-100">加入降寶清單</button>
             </div>
        </div>



        <teleport to="body">
            <div v-if="activeRelDropdown" :style="relDropdownStyle" class="fixed bg-white rounded-3xl shadow-[0_20px_50px_rgba(0,0,0,0.2)] border border-slate-100 z-[9000] overflow-y-auto custom-scrollbar text-left p-2 animate-fade-in">
                <div v-for="opt in relationshipOptions" :key="opt"
                     @mousedown.prevent @click.stop="form.target_remarks = opt; activeRelDropdown = false"
                     class="px-6 h-[48px] flex items-center md:rounded-2xl hover:bg-emerald-50 font-black text-[18px] text-slate-900 active:bg-emerald-100 transition-all cursor-pointer">
                    {{ opt }}
                </div>
            </div>
            <div v-if="showMasterDropdown && masterFilteredList.length > 0" :style="masterDropdownStyle" class="fixed bg-white rounded-3xl shadow-[0_20px_50px_rgba(0,0,0,0.2)] border border-slate-100 z-[9000] overflow-y-auto max-h-[240px] custom-scrollbar text-left p-2 animate-fade-in">
                <div v-for="m in masterFilteredList" :key="m"
                     @mousedown.prevent @click.stop="masterNameInput = m; resolveMasterId(); showMasterDropdown = false"
                     class="px-6 h-[48px] flex items-center md:rounded-2xl hover:bg-indigo-50 font-black text-[18px] text-slate-900 active:bg-indigo-100 transition-all cursor-pointer">
                    {{ m }}
                </div>
            </div>
            <div v-if="showTreasureDropdown && treasureFilteredList.length > 0" :style="treasureDropdownStyle" class="fixed bg-white rounded-3xl shadow-[0_20px_50px_rgba(0,0,0,0.2)] border border-slate-100 z-[9000] overflow-y-auto max-h-[240px] custom-scrollbar text-left p-2 animate-fade-in">
                <div v-for="name in treasureFilteredList" :key="name"
                     @mousedown.prevent @click.stop="newItemName = name; showTreasureDropdown = false"
                     class="px-6 h-[48px] flex items-center md:rounded-2xl hover:bg-amber-50 font-black text-[18px] text-slate-900 active:bg-amber-100 transition-all cursor-pointer">
                    {{ name }}
                </div>
            </div>
            <div v-if="showPractitionerDropdown && (practitionerFilteredNames.length > 0 || practitionerFilteredGroups.length > 0)" :style="practitionerDropdownStyle" class="fixed bg-white rounded-3xl shadow-[0_20px_50px_rgba(0,0,0,0.2)] border border-slate-100 z-[9000] overflow-y-auto max-h-[280px] custom-scrollbar text-left p-2 animate-fade-in">
                <div v-if="practitionerFilteredNames.length > 0" class="px-4 py-2 text-[12px] font-black text-slate-300 uppercase tracking-widest">法號</div>
                <div v-for="dn in practitionerFilteredNames" :key="'dn'+dn.id"
                     @mousedown.prevent @click.stop="selectDharmaName(dn); showPractitionerDropdown = false"
                     class="px-6 h-[48px] flex items-center md:rounded-2xl hover:bg-indigo-50 font-black text-[18px] text-slate-900 active:bg-indigo-100 transition-all cursor-pointer">
                    {{ dn.name }}
                </div>
                <div v-if="practitionerFilteredGroups.length > 0" class="px-4 py-2 text-[12px] font-black text-indigo-300 uppercase tracking-widest">群組</div>
                <div v-for="g in practitionerFilteredGroups" :key="'g'+g.id"
                     @mousedown.prevent @click.stop="selectGroup(g); showPractitionerDropdown = false"
                     class="px-6 h-[48px] flex items-center md:rounded-2xl hover:bg-indigo-50 font-black text-[18px] text-indigo-600 active:bg-indigo-100 transition-all cursor-pointer">
                    {{ formatGroupName(g.name) }}
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
import CompactDatalist from './CompactDatalist.vue';
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

function delayClose(ref, ms) {
    window.setTimeout(() => { ref.value = false; }, ms);
}

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
const activeRelDropdown = ref(false);
const showItemsSelector = ref(false);
const showMasterDropdown = ref(false);
const showPractitionerDropdown = ref(false);
const showTreasureDropdown = ref(false);
const dharmaSearchInputEl = ref(null);
const masterInputEl = ref(null);
const relInputEl = ref(null);
const treasureInputEl = ref(null);

const relDropdownStyle = ref({});
const masterDropdownStyle = ref({});
const treasureDropdownStyle = ref({});
const practitionerDropdownStyle = ref({});

function openRelDropdown() {
    activeRelDropdown.value = true;
    nextTick(() => {
        if (relInputEl.value) {
            const rect = relInputEl.value.getBoundingClientRect();
            const spaceBelow = window.innerHeight - rect.bottom - 40;
            relDropdownStyle.value = {
                left: rect.left + 'px',
                width: rect.width + 'px',
                top: (rect.bottom + 8) + 'px',
                maxHeight: Math.min(spaceBelow, 300) + 'px',
            };
        }
    });
}

function openMasterDropdown() {
    showMasterDropdown.value = true;
    nextTick(() => {
        if (masterInputEl.value) {
            const rect = masterInputEl.value.getBoundingClientRect();
            const spaceAbove = rect.top - 20;
            masterDropdownStyle.value = {
                left: rect.left + 'px',
                width: rect.width + 'px',
                top: (rect.top - 8) + 'px',
                transform: 'translateY(-100%)',
                maxHeight: Math.min(spaceAbove, 400) + 'px',
            };
        }
    });
}

function openTreasureDropdown() {
    showTreasureDropdown.value = true;
    nextTick(() => {
        if (treasureInputEl.value) {
            const rect = treasureInputEl.value.getBoundingClientRect();
            const spaceBelow = window.innerHeight - rect.bottom - 40;
            treasureDropdownStyle.value = {
                left: rect.left + 'px',
                width: rect.width + 'px',
                top: (rect.bottom + 8) + 'px',
                maxHeight: Math.min(spaceBelow, 240) + 'px',
            };
        }
    });
}

function openPractitionerDropdown() {
    showPractitionerDropdown.value = true;
    nextTick(() => {
        if (dharmaSearchInputEl.value) {
            const rect = dharmaSearchInputEl.value.getBoundingClientRect();
            const spaceAbove = rect.top - 20;
            practitionerDropdownStyle.value = {
                left: rect.left + 'px',
                width: rect.width + 'px',
                top: (rect.top - 8) + 'px',
                transform: 'translateY(-100%)',
                maxHeight: Math.min(spaceAbove, 280) + 'px',
            };
        }
    });
}

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
    const val = e.target.value;
    if (!val) {
        clearSelection();
        return;
    }
    const matchedDN = props.dharmaNames.find(dn => dn.name === val);
    if (matchedDN) {
        form.value.dharma_name_ids = [matchedDN.id];
        return;
    }
    const matchedGroup = props.groups.find(g => g.name === val || formatGroupName(g.name) === val);
    if (matchedGroup) {
        selectGroup(matchedGroup);
        return;
    }
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
            rawRecipientLine: '',
            content: fullBlockText, // Keep the FULL original text here
            items: [], 
            dharma_name_ids: [], 
            items_footer_remarks: '' 
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

watch(batchImportContent, () => {
    processBatchParsing();
});

function handleSubmit() {
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