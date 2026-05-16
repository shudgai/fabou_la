<template>
    <teleport to="body">
    <div v-if="mode" class="fixed inset-0 z-[3500] flex items-end md:items-center justify-center px-0">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-slate-900/60" @click="$emit('cancel')"></div>

        <!-- Form Container -->
        <div class="relative w-full h-full md:h-auto md:max-h-[95dvh] md:max-w-xl bg-white md:rounded-[32px] shadow-[0_-10px_40px_rgba(0,0,0,0.1)] overflow-hidden animate-slide-up flex flex-col">

            <!-- Header: Standardized Branding (Logo + Main Title + Sub Title) -->
            <div class="px-0 flex flex-col bg-white border-b border-slate-50 relative shrink-0">
                <!-- Row 1: Global Title -->
                <div class="px-4 py-2 bg-white flex items-center gap-2 border-b border-transparent">
                    <logo-imperial-notebook :height="30" />
                    <h1 class="font-outfit !font-normal !text-black tracking-widest pt-[2px]" style="font-size: 26px !important; font-weight: 400 !important;">法寶登記專區</h1>
                </div>
                <!-- Row 2: Subtitle (Category + Form Mode) -->
                <div class="px-4 py-1.5 bg-white border-b border-transparent flex items-center justify-between">
                    <div class="flex items-baseline gap-x-2 flex-1 min-w-0">
                        <span v-if="categoryText" class="font-outfit font-normal !text-black whitespace-nowrap" style="font-size: 23px !important; font-weight: 400 !important; line-height: 1.1;">
                            {{ categoryText }}
                        </span>
                        <span class="font-outfit font-normal text-slate-900 whitespace-nowrap" style="font-size: 23px !important; line-height: 1.1; transform: translateY(1.5px);">
                            - {{ modeTitleText }}
                        </span>
                    </div>
                </div>
                <!-- Close Button -->
                <button @click="$emit('cancel')" class="text-slate-300 hover:text-slate-600 transition-colors p-2 absolute right-4 top-2 z-[50]">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
            </div>

            <!-- Tab Selection (hidden when editing) -->
            <div v-if="!isEditing" class="px-6 pt-4 flex space-x-1">
                <button @click="localMode = 'single'; currentStep = 1"
                    :class="localMode === 'single' ? 'bg-blue-500 text-white shadow-md' : 'bg-slate-50 text-slate-400 border border-slate-100'"
                    class="flex-1 py-[12px] rounded-2xl text-[16px] font-normal transition-all whitespace-nowrap active:scale-95"
                    :style="{ fontSize: '16px !important', color: localMode === 'single' ? 'white !important' : '#000000 !important' }">
                    逐筆載錄
                </button>
                <button @click="localMode = 'batch'"
                    :class="localMode === 'batch' ? 'bg-blue-500 text-white shadow-md' : 'bg-slate-50 text-slate-400 border border-slate-100'"
                    class="flex-1 py-[12px] rounded-2xl text-[16px] font-normal transition-all whitespace-nowrap active:scale-95"
                    :style="{ fontSize: '16px !important', color: localMode === 'batch' ? 'white !important' : '#000000 !important' }">
                    多筆載錄
                </button>
            </div>

            <!-- Step Progress (Single Mode Only, hidden when editing) -->
            <div v-if="localMode === 'single' && !isEditing" class="px-6 pt-4 pb-1 bg-white shrink-0">
                <div class="flex items-center gap-1.5">
                    <div v-for="s in totalSteps" :key="s" class="h-1.5 flex-1 rounded-full transition-all duration-500"
                         :class="s <= currentStep ? 'bg-blue-500 shadow-[0_0_8px_rgba(59,130,246,0.4)]' : 'bg-slate-100'"></div>
                </div>
                <div class="flex justify-between mt-2 px-1">
                    <span class="text-[11px] font-normal text-black uppercase tracking-[0.2em]">第 {{ currentStep }} 步 / 共 {{ totalSteps }} 步</span>
                    <span class="text-[11px] font-normal text-black uppercase tracking-[0.2em]">{{ currentStepTitle }}</span>
                </div>
            </div>

            <!-- Scrollable Content -->
            <div class="flex-1 min-h-0 overflow-y-auto px-[15px] py-[20px] custom-scrollbar bg-white">

                <!-- SINGLE MODE: Step-by-Step (hidden when editing) -->
                <transition v-if="localMode === 'single' && !isEditing" name="step-fade" mode="out-in">

                    <!-- STEP 1: 日期 -->
                    <div v-if="currentStep === 1" :key="'s1'" class="space-y-10 max-w-md mx-auto pt-4 animate-fade-in">
                        <div class="text-center space-y-2 mb-8">
                            <h2 class="text-[17px] font-normal text-black">請輸入<span class="text-black">日期</span></h2>
                        </div>
                        <div class="space-y-8">
                            <div class="relative group">
                                <div class="flex items-center justify-between mb-2">
                                    <label class="text-[11px] font-normal text-black uppercase tracking-[0.2em]">得知日期</label>
                                    <button @click="activePicker = { idx: 'main', field: 'record_date', title: '選擇日期' }" class="text-slate-300 hover:text-blue-500 p-1 active:scale-90">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2v12a2 2 0 002 2z" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    </button>
                                </div>
                                <textarea v-model="form.record_date" rows="2" placeholder="年/月/日 或 註記文字"
                                    class="w-full text-center text-[17px] font-normal border-0 border-b-2 border-slate-100 focus:border-blue-500 bg-transparent py-4 outline-none transition-all placeholder:text-slate-200 resize-none leading-relaxed text-black"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- STEP 2: 載錄仙師 (Moved from step 3) -->
                    <div v-else-if="currentStep === 2" :key="'s2'" class="space-y-10 max-w-md mx-auto pt-4 animate-fade-in">
                        <div class="text-center space-y-2 mb-8">
                            <h2 class="text-[17px] font-normal text-black">請確認<span class="text-black">載錄仙師</span></h2>
                        </div>
                        <div class="space-y-8">
                            <div class="relative group">
                                <label class="text-[11px] font-normal text-black uppercase tracking-[0.2em] block mb-2">載錄目標仙師</label>
                                <editable-input-chips 
                                    v-model="masterNameInput" 
                                    variant="boxed"
                                    :options="masters.map(m => m.name === '父皇仙師' ? '父皇' : m.name)" 
                                    @change="resolveMasterId" 
                                    placeholder="仙師" />
                            </div>
                        </div>
                    </div>

                    <!-- STEP 3: 法寶名稱 (Moved from step 2) -->
                    <div v-else-if="currentStep === 3" :key="'s3'" class="space-y-8 max-w-md mx-auto pt-4 animate-fade-in">
                        <div class="text-center space-y-2 mb-8">
                            <h2 class="text-[17px] font-normal text-black">請輸入<span class="text-black">法寶名稱</span></h2>
                        </div>
                        <div class="relative group">
                            <label class="text-[11px] font-normal text-black uppercase tracking-[0.2em] block mb-2">法寶名稱</label>
                            <textarea v-model="treasureNamesText" rows="5" placeholder="輸入內容..."
                                class="w-full text-[17px] font-normal border-0 border-b-2 border-slate-100 focus:border-blue-500 bg-transparent py-4 outline-none transition-all placeholder:text-slate-200 resize-none leading-relaxed text-black"></textarea>
                        </div>
                    </div>

                    <!-- STEP 4: 用意 (Moved from step 3) -->
                    <div v-else-if="currentStep === 4" :key="'s4'" class="space-y-8 max-w-md mx-auto pt-4 animate-fade-in">
                        <div class="text-center space-y-2 mb-8">
                            <h2 class="text-[17px] font-normal text-black">請輸入<span class="text-black">用意</span></h2>
                        </div>
                        <div class="relative group">
                            <label class="text-[11px] font-normal text-black uppercase tracking-[0.2em] block mb-2">用意 (選填)</label>
                            <textarea v-model="form.purpose" rows="5" placeholder="輸入用意..."
                                class="w-full text-[17px] font-normal border-0 border-b-2 border-slate-100 focus:border-blue-500 bg-transparent py-4 outline-none transition-all placeholder:text-slate-200 resize-none leading-relaxed text-black"></textarea>
                        </div>
                    </div>

                    <!-- STEP 5: 功效 (Moved from step 4) -->
                    <div v-else-if="currentStep === 5" :key="'s5'" class="space-y-8 max-w-md mx-auto pt-4 animate-fade-in">
                        <div class="text-center space-y-2 mb-8">
                            <h2 class="text-[17px] font-normal text-black">請輸入<span class="text-black">功效</span></h2>
                        </div>
                        <div class="relative group">
                            <label class="text-[11px] font-normal text-black uppercase tracking-[0.2em] block mb-2">功效 (選填)</label>
                            <textarea v-model="form.effect" rows="5" placeholder="輸入功效..."
                                class="w-full text-[17px] font-normal border-0 border-b-2 border-slate-100 focus:border-blue-500 bg-transparent py-4 outline-none transition-all placeholder:text-slate-200 resize-none leading-relaxed text-black"></textarea>
                        </div>
                    </div>

                    <!-- STEP 6: 作法 -->
                    <div v-else-if="currentStep === 6" :key="'s6'" class="space-y-8 max-w-md mx-auto pt-4 animate-fade-in">
                        <div class="text-center space-y-2 mb-8">
                            <h2 class="text-[17px] font-normal text-black">請輸入<span class="text-black">作法</span></h2>
                        </div>
                        <div class="relative group">
                            <label class="text-[11px] font-normal text-black uppercase tracking-[0.2em] block mb-2">作法/求寶方式</label>
                            <textarea v-model="form.acquisition_method" rows="5" placeholder="輸入作法..."
                                class="w-full text-[17px] font-normal border-0 border-b-2 border-slate-100 focus:border-blue-500 bg-transparent py-4 outline-none transition-all placeholder:text-slate-200 resize-none leading-relaxed text-black"></textarea>
                        </div>
                    </div>

                    <!-- STEP 7: 法寶內容 (NEW) -->
                    <div v-else-if="currentStep === 7" :key="'s7'" class="space-y-8 max-w-md mx-auto pt-4 animate-fade-in">
                        <div class="text-center space-y-2 mb-8">
                            <h2 class="text-[17px] font-normal text-black">請輸入<span class="text-black">法寶內容</span></h2>
                        </div>
                        <div class="relative group">
                            <label class="text-[11px] font-normal text-black uppercase tracking-[0.2em] block mb-2">法寶內容 (選填)</label>
                            <textarea v-model="form.content" rows="5" placeholder="輸入內容..."
                                class="w-full text-[17px] font-normal border-0 border-b-2 border-slate-100 focus:border-blue-500 bg-transparent py-4 outline-none transition-all placeholder:text-slate-200 resize-none leading-relaxed text-black"></textarea>
                        </div>
                    </div>

                    <!-- STEP 8: 備註與承接師兄姐 -->
                    <div v-else-if="currentStep === 8" :key="'s8'" class="space-y-6 max-w-md mx-auto pt-4 animate-fade-in">
                        <div class="text-center space-y-2 mb-6">
                            <h2 class="text-[17px] font-normal text-black">請輸入<span class="text-black">備註及人員</span></h2>
                        </div>

                        <div class="relative group mb-8">
                            <label class="text-[11px] font-normal text-black uppercase tracking-[0.2em] block mb-2">備註 (選填)</label>
                            <div @click="openRemarksModal"
                                class="w-full text-center text-[17px] font-normal border-0 border-b-2 border-slate-100 hover:border-blue-500 bg-transparent py-4 outline-none transition-all cursor-pointer">
                                <span :class="form.remarks ? 'text-black' : 'text-slate-300'">{{ form.remarks || '輸入備註...' }}</span>
                            </div>
                        </div>

                        <div class="flex items-center justify-between">
                            <label class="text-[11px] font-normal text-black uppercase tracking-[0.2em]">承接師兄姐</label>
                            <button @click="addPersonnelRow" class="px-4 py-1.5 bg-blue-600 text-white rounded-2xl text-[15px] font-normal active:scale-95 transition-all shadow-md" style="color: white !important;">＋ 新增人員</button>
                        </div>
                        <div v-for="(p, idx) in personnel" :key="idx" class="p-4 bg-slate-50/30 rounded-2xl border border-slate-200 space-y-3 relative animate-fade-in">
                            <div class="absolute top-2 right-2 flex items-center space-x-1">
                                <button v-if="personnel.length > 1" @click="movePersonnel(idx, -1)" :disabled="idx === 0" class="p-1 text-slate-300 hover:text-blue-500 disabled:opacity-10 active:scale-90"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 15l7-7 7 7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg></button>
                                <button v-if="personnel.length > 1" @click="movePersonnel(idx, 1)" :disabled="idx === personnel.length - 1" class="p-1 text-slate-300 hover:text-blue-500 disabled:opacity-10 active:scale-90"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg></button>
                                <button @click="removePersonnelRow(idx)" class="ml-1 text-slate-300 hover:text-red-500 p-1">✕</button>
                            </div>
                            <div class="grid grid-cols-2 gap-x-2 gap-y-3">
                                <div class="space-y-1 personnel-name-dropdown">
                                    <label class="text-[11px] text-red-400 ml-1 font-bold">法號</label>
                                    <editable-input-chips 
                                        v-model="p.custom_name" 
                                        variant="boxed"
                                        :options="props.dharmaNames.map(dn => dn.name)" 
                                        placeholder="對象" />
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[11px] text-red-400 ml-1 font-bold">備註對象</label>
                                    <editable-input-chips 
                                        v-model="p.relationship" 
                                        variant="boxed"
                                        :options="relationshipOptions" 
                                        placeholder="對象" />
                                </div>
                                <div class="space-y-1">
                                    <div class="flex items-center justify-between px-1">
                                        <label class="text-[11px] text-red-400 ml-1 font-bold">日期</label>
                                        <button @click="activePicker = { idx, field: 'obtained_date', title: (p.custom_name || '師兄姐') + '取得日期' }" class="text-slate-300 hover:text-blue-500 active:scale-90 p-0.5">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2v12a2 2 0 002 2z" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        </button>
                                    </div>
                                    <textarea :value="p.obtained_date ? p.obtained_date.replace(/-/g, '/') : ''" @input="e => handlePersonnelDateInput(idx, e)" placeholder="年/月/日" rows="2"
                                        class="personnel-date-input w-full py-[10px] rounded-xl border border-slate-300 bg-white px-3 text-[15px] font-bold text-slate-900 outline-none resize-none leading-tight"></textarea>
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[11px] text-slate-400 ml-1 font-bold">個人備註</label>
                                    <div @click="$emit('openRemarksEdit', { idx, remarks: p.remarks })"
                                        class="w-full h-[46px] rounded-xl border border-slate-300 bg-white px-3 py-[10px] text-[15px] text-slate-900 cursor-pointer hover:border-blue-300 flex items-center justify-between">
                                        <span :class="p.remarks ? 'text-slate-900' : 'text-slate-300'" class="truncate flex-1 text-[13px]">{{ p.remarks || '備註...' }}</span>
                                        <svg class="w-4 h-4 text-slate-200 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-if="personnel.length === 0" class="text-center py-6 bg-slate-50/30 rounded-2xl border border-dashed border-slate-200 text-slate-300 text-[13px]">尚無人員紀錄</div>
                        <button @click="addPersonnelRow" class="w-full py-4 rounded-2xl border-2 border-dashed border-blue-100 bg-blue-50/30 flex items-center justify-center space-x-2 text-blue-500 hover:bg-blue-50 active:scale-[0.98] transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            <span class="text-[16px] font-normal">新增人員</span>
                        </button>
                    </div>

                </transition>

                <!-- EDIT MODE: All fields on one page -->
                <div v-if="isEditing && localMode === 'single'" class="space-y-8 max-w-md mx-auto pb-8 animate-fade-in">
                    <div class="relative group">
                        <label class="text-[11px] font-normal text-black uppercase tracking-[0.2em] block mb-2">得知日期</label>
                        <textarea v-model="form.record_date" rows="2" placeholder="年/月/日 或 註記文字"
                            class="w-full text-center text-[17px] font-normal border-0 border-b-2 border-slate-100 focus:border-blue-500 bg-transparent py-4 outline-none transition-all placeholder:text-slate-200 resize-none leading-relaxed text-black"></textarea>
                    </div>
                    <div class="relative group">
                        <label class="text-[11px] font-normal text-black uppercase tracking-[0.2em] block mb-2">載錄目標仙師</label>
                        <editable-input-chips 
                            v-model="masterNameInput" 
                            variant="boxed"
                            :options="masters.map(m => m.name === '父皇仙師' ? '父皇' : m.name)" 
                            @change="resolveMasterId" 
                            placeholder="仙師" />
                    </div>
                    <div class="relative group">
                        <label class="text-[11px] font-normal text-black uppercase tracking-[0.2em] block mb-2">用意 (選填)</label>
                        <textarea v-model="form.purpose" rows="3" placeholder="輸入用意..."
                            class="w-full text-[17px] font-normal border-0 border-b-2 border-slate-100 focus:border-blue-500 bg-transparent py-4 outline-none transition-all placeholder:text-slate-200 resize-none leading-relaxed text-black"></textarea>
                    </div>
                    <div class="relative group">
                        <label class="text-[11px] font-normal text-black uppercase tracking-[0.2em] block mb-2">功效 (選填)</label>
                        <textarea v-model="form.effect" rows="3" placeholder="輸入功效..."
                            class="w-full text-[17px] font-normal border-0 border-b-2 border-slate-100 focus:border-blue-500 bg-transparent py-4 outline-none transition-all placeholder:text-slate-200 resize-none leading-relaxed text-black"></textarea>
                    </div>
                    <div class="relative group">
                        <label class="text-[11px] font-normal text-black uppercase tracking-[0.2em] block mb-2">作法/求寶方式</label>
                        <textarea v-model="form.acquisition_method" rows="3" placeholder="輸入作法..."
                            class="w-full text-[17px] font-normal border-0 border-b-2 border-slate-100 focus:border-blue-500 bg-transparent py-4 outline-none transition-all placeholder:text-slate-200 resize-none leading-relaxed text-black"></textarea>
                    </div>
                    <div class="relative group">
                        <label class="text-[11px] font-normal text-black uppercase tracking-[0.2em] block mb-2">法寶名稱</label>
                        <textarea v-model="treasureNamesText" rows="3" placeholder="輸入內容..."
                            class="w-full text-[17px] font-normal border-0 border-b-2 border-slate-100 focus:border-blue-500 bg-transparent py-4 outline-none transition-all placeholder:text-slate-200 resize-none leading-relaxed text-black"></textarea>
                    </div>
                    <div class="relative group">
                        <label class="text-[11px] font-normal text-black uppercase tracking-[0.2em] block mb-2">法寶內容 (選填)</label>
                        <textarea v-model="form.content" rows="3" placeholder="輸入內容..."
                            class="w-full text-[17px] font-normal border-0 border-b-2 border-slate-100 focus:border-blue-500 bg-transparent py-4 outline-none transition-all placeholder:text-slate-200 resize-none leading-relaxed text-black"></textarea>
                    </div>
                    <div class="relative group">
                        <label class="text-[11px] font-normal text-black uppercase tracking-[0.2em] block mb-2">備註 (選填)</label>
                        <div @click="openRemarksModal"
                            class="w-full text-center text-[17px] font-normal border-0 border-b-2 border-slate-100 hover:border-blue-500 bg-transparent py-4 outline-none transition-all cursor-pointer">
                            <span :class="form.remarks ? 'text-black' : 'text-slate-300'">{{ form.remarks || '輸入備註...' }}</span>
                        </div>
                    </div>
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <label class="text-[11px] font-normal text-black uppercase tracking-[0.2em]">承接師兄姐</label>
                            <button @click="addPersonnelRow" class="px-4 py-1.5 bg-blue-600 text-white rounded-2xl text-[15px] font-normal active:scale-95 transition-all shadow-md" style="color: white !important;">＋ 新增人員</button>
                        </div>
                        <div v-for="(p, idx) in personnel" :key="idx" class="p-4 bg-slate-50/30 rounded-2xl border border-slate-200 space-y-3 relative mb-3 animate-fade-in">
                            <div class="absolute top-2 right-2 flex items-center space-x-1">
                                <button v-if="personnel.length > 1" @click="movePersonnel(idx, -1)" :disabled="idx === 0" class="p-1 text-slate-300 hover:text-blue-500 disabled:opacity-10 active:scale-90"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 15l7-7 7 7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg></button>
                                <button v-if="personnel.length > 1" @click="movePersonnel(idx, 1)" :disabled="idx === personnel.length - 1" class="p-1 text-slate-300 hover:text-blue-500 disabled:opacity-10 active:scale-90"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg></button>
                                <button @click="removePersonnelRow(idx)" class="ml-1 text-slate-300 hover:text-red-500 p-1">✕</button>
                            </div>
                            <div class="grid grid-cols-2 gap-x-2 gap-y-3">
                                <div class="space-y-1 personnel-name-dropdown">
                                    <label class="text-[11px] text-red-400 ml-1 font-bold">法號</label>
                                    <editable-input-chips 
                                        v-model="p.custom_name" 
                                        variant="boxed"
                                        :options="props.dharmaNames.map(dn => dn.name)" 
                                        placeholder="對象" />
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[11px] text-red-400 ml-1 font-bold">備註對象</label>
                                    <editable-input-chips 
                                        v-model="p.relationship" 
                                        variant="boxed"
                                        :options="relationshipOptions" 
                                        placeholder="對象" />
                                </div>
                                <div class="space-y-1">
                                    <div class="flex items-center justify-between px-1">
                                        <label class="text-[11px] text-red-400 ml-1 font-bold">日期</label>
                                    </div>
                                    <textarea :value="p.obtained_date ? p.obtained_date.replace(/-/g, '/') : ''" @input="e => handlePersonnelDateInput(idx, e)" placeholder="年/月/日" rows="2"
                                        class="personnel-date-input w-full py-[10px] rounded-xl border border-slate-300 bg-white px-3 text-[15px] font-bold text-slate-900 outline-none resize-none leading-tight"></textarea>
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[11px] text-slate-400 ml-1 font-bold">個人備註</label>
                                    <div @click="$emit('openRemarksEdit', { idx, remarks: p.remarks })"
                                        class="w-full h-[46px] rounded-xl border border-slate-300 bg-white px-3 py-[10px] text-[15px] text-slate-900 cursor-pointer hover:border-blue-300 flex items-center justify-between">
                                        <span :class="p.remarks ? 'text-slate-900' : 'text-slate-300'" class="truncate flex-1 text-[13px]">{{ p.remarks || '備註...' }}</span>
                                        <svg class="w-4 h-4 text-slate-200 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-if="personnel.length === 0" class="text-center py-6 bg-slate-50/30 rounded-2xl border border-dashed border-slate-200 text-slate-300 text-[13px]">尚無人員紀錄</div>
                        <button @click="addPersonnelRow" class="w-full py-4 rounded-2xl border-2 border-dashed border-blue-100 bg-blue-50/30 flex items-center justify-center space-x-2 text-blue-500 hover:bg-blue-50 active:scale-[0.98] transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            <span class="text-[16px] font-normal">新增人員</span>
                        </button>
                    </div>
                </div>

                <!-- BATCH MODE -->
                <div v-if="localMode === 'batch'" class="space-y-4 animate-fade-in">
                    <!-- Master Selector -->
                    <div class="px-1">
                        <label class="text-[11px] font-normal text-black uppercase tracking-[0.2em] block mb-2">載錄目標仙師</label>
                        <editable-input-chips 
                            v-model="masterNameInput" 
                            variant="boxed"
                            :options="masters.map(m => m.name === '父皇仙師' ? '父皇' : m.name)" 
                            @change="resolveMasterId" 
                            placeholder="仙師" />
                    </div>
                    <div class="flex items-center justify-between ml-1">
                        <label class="text-[17px] font-bold text-slate-800">貼入內容明細</label>
                        <button v-if="batchInput" @click="batchInput = ''" class="px-3 py-1 bg-red-50 text-red-400 rounded-lg text-[13px] font-bold active:scale-95 flex items-center space-x-1 border border-red-100">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            <span>清空</span>
                        </button>
                    </div>
                    <textarea v-model="batchInput" rows="10" class="w-full border-none text-[15px] font-normal text-slate-900 bg-slate-50/50 focus:ring-2 focus:ring-blue-100 p-4 rounded-2xl" placeholder="多筆載錄格式：
日期(Tab)允求:法寶名稱(Tab)法號,法號

例如：
113.10.6	允求:無形現惡鏡	閻帝
113.10.7	求得:森羅戒	金了,元續"></textarea>
                    <div v-if="batchInput.trim()" class="rounded-[24px] overflow-hidden bg-slate-50/50 animate-fade-in">
                        <div class="bg-blue-50/50 px-4 py-3 border-b border-blue-100 flex justify-between items-center">
                            <div class="flex flex-col">
                                <span class="text-[15px] font-normal text-black">預覽清單 {{ rawLines.length > 0 ? '(' + rawLines.length + ' 筆)' : '' }}</span>
                                <span class="text-[11px] text-blue-400 font-medium">貼上內容原始顯示</span>
                            </div>
                        </div>
                        <div class="max-h-48 overflow-y-auto custom-scrollbar divide-y divide-slate-100">
                            <div v-for="(line, idx) in rawLines" :key="idx" class="px-4 py-2.5 text-[14px] font-normal text-black flex items-start gap-3 hover:bg-slate-50">
                                <span class="text-[11px] font-normal text-black w-6 shrink-0 mt-0.5">{{ idx + 1 }}</span>
                                <span class="break-all whitespace-pre-wrap">{{ line }}</span>
                            </div>
                        </div>
                    </div>
                </div>


            </div>

            <!-- Footer Action -->
            <div class="shrink-0 px-[15px] pt-4 pb-1 bg-white border-t border-slate-50 flex gap-2 justify-center">
                <!-- Edit Mode -->
                <template v-if="isEditing">
                    <button @click="$emit('cancel')" class="w-[85px] py-4 bg-slate-100 text-slate-400 rounded-2xl font-normal text-[17px] active:scale-95 transition-all">取消</button>
                    <button @click="handleSubmit" :disabled="isSaving" class="flex-1 py-4 bg-emerald-600 text-white rounded-2xl font-normal text-[17px] shadow-lg shadow-emerald-100 active:scale-95 transition-all disabled:bg-slate-300" style="color: white !important;">{{ isSaving ? '儲存中...' : '確認修改' }}</button>
                </template>
                <!-- Single Mode Wizard Navigation -->
                <template v-else-if="localMode === 'single'">
                    <button v-if="currentStep > 1" @click="handleBack" class="w-[85px] py-4 bg-slate-100 text-slate-400 rounded-2xl font-normal text-[17px] active:scale-95 transition-all">上一步</button>
                    <button v-if="currentStep < totalSteps" @click="handleNext" class="flex-1 py-4 bg-blue-600 text-white rounded-2xl font-normal text-[17px] shadow-lg shadow-blue-100 active:scale-95 transition-all" style="color: white !important;">下一步</button>
                    <button v-else @click="handleSubmit" :disabled="isSaving" class="flex-1 py-4 bg-emerald-600 text-white rounded-2xl font-normal text-[17px] shadow-lg shadow-emerald-100 active:scale-95 transition-all disabled:bg-slate-300" style="color: white !important;">{{ isSaving ? '儲存中...' : '確認載錄' }}</button>
                </template>
                <!-- Batch Mode -->
                <button v-else @click="handleSubmit" :disabled="isSaving || parsedItemsCount === 0"
                    class="w-full max-w-md bg-blue-600 text-white h-[48px] rounded-2xl font-normal text-[16px] shadow-lg shadow-blue-100 active:scale-95 transition-all disabled:opacity-50" style="color: white !important;">
                    {{ isSaving ? '儲存中...' : `確認載錄 (${parsedItemsCount} 筆)` }}
                </button>
            </div>


            <!-- Navbar Wrapper Removed for Modal UX -->
            <div class="h-[env(safe-area-inset-bottom)] md:h-0"></div>
        </div>

        <compact-date-picker 
            v-if="activePicker && activePicker.idx === 'main'"
            v-model="form[activePicker.field]"
            :title="activePicker.title"
            @close="activePicker = null"
        />
        <compact-date-picker 
            v-if="activePicker && typeof activePicker.idx === 'number'"
            v-model="personnel[activePicker.idx][activePicker.field]"
            :title="activePicker.title"
            @close="activePicker = null"
        />
    </div>

        <!-- Remarks Edit Modal -->
        <teleport to="body">
            <div v-if="showRemarksModal" class="fixed inset-0 z-[5500] flex items-end justify-center">
                <div class="fixed inset-0 bg-slate-900/80" @click="showRemarksModal = false"></div>
                <div class="relative w-full max-w-xl bg-white rounded-t-[32px] shadow-2xl overflow-hidden animate-slide-up flex flex-col max-h-[80dvh]">
                    <div class="px-6 py-5 border-b border-slate-50 flex items-center justify-between sticky top-0 bg-white z-10">
                        <span class="text-[20px] font-normal text-black">編輯備註</span>
                        <button @click="showRemarksModal = false" class="p-2 text-slate-300 hover:text-slate-600"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg></button>
                    </div>
                    <div class="flex-1 overflow-y-auto p-4 pb-10">
                        <textarea v-model="remarksBuffer" rows="8" placeholder="輸入備註..."
                            class="w-full text-[17px] font-normal border-0 border-b-2 border-slate-300 focus:border-blue-500 bg-transparent py-4 outline-none transition-all placeholder:text-slate-200 resize-none leading-relaxed text-black"></textarea>
                    </div>
                    <div class="px-6 py-4 border-t border-slate-100 flex gap-3">
                        <button @click="showRemarksModal" class="flex-1 py-4 bg-slate-100 text-slate-400 rounded-2xl font-normal text-[17px] active:scale-95 transition-all">取消</button>
                        <button @click="confirmRemarks" class="flex-[2] py-4 bg-blue-600 text-white rounded-2xl font-normal text-[17px] shadow-lg shadow-blue-100 active:scale-95 transition-all">確認</button>
                    </div>
                </div>
            </div>
        </teleport>
    </teleport>
</template>

<script setup>
import { ref, watch, computed, onMounted, nextTick } from 'vue';
import axios from 'axios';
import MobileNavbar from './MobileNavbar.vue';

const props = defineProps({
    mode: String,
    initialData: Object,
    masters: Array,
    category: String,
    isSaving: Boolean
});

const emit = defineEmits(['saveSingle', 'saveBatch', 'cancel', 'openRemarksEdit']);

import CompactDatePicker from './CompactDatePicker.vue';
import EditableInputChips from './EditableInputChips.vue';

const localMode = ref(props.mode || 'single');
const currentStep = ref(1);
const totalSteps = 8;
const stepTitles = ['日期', '仙師', '法寶名稱', '用意', '功效', '作法', '法寶內容', '備註'];
const currentStepTitle = computed(() => stepTitles[currentStep.value - 1] || '預覽確認');
const isEditing = computed(() => !!props.initialData?.id);

const categoryText = computed(() => {
    if (props.category === 'major') return '特殊法寶登記簿';
    if (props.category === 'minor') return '其他皇恩登記簿';
    return '';
});

const modeTitleText = computed(() => {
    if (isEditing.value) return '修改紀錄';
    if (localMode.value === 'batch') return '多筆貼上新增';
    return '逐筆新增紀錄';
});

function handleNext() {
    // 根據用戶要求：只有法寶名稱是必填 (Step 3)
    if (currentStep.value === 3 && !treasureNamesText.value.trim()) { alert('請輸入法寶名稱'); return; }
    if (currentStep.value < totalSteps) currentStep.value++;
}
function handleBack() {
    if (currentStep.value > 1) currentStep.value--;
}

const form = ref({ ...props.initialData });
const treasureNamesText = ref('');
const batchInput = ref('');
const masterNameInput = ref('');
const personnel = ref([]);
const dharmaNames = ref([]);
const activePicker = ref(null); // { idx: number | 'main', field: string, title: string }
const activeRelationshipDropdownIdx = ref(null);
const relationshipOptions = ['母親', '父親', '公公', '婆婆', '爺爺', '奶奶', '外公', '外婆'];
const fileInput = ref(null);
const showRemarksModal = ref(false);
const remarksBuffer = ref('');

const openRemarksModal = () => {
    remarksBuffer.value = form.value.remarks || '';
    showRemarksModal.value = true;
};

const confirmRemarks = () => {
    form.value.remarks = remarksBuffer.value;
    showRemarksModal.value = false;
};

const treasureNames = computed(() => {
    // 優先依換行切分，其次依兩個以上的空格或 Tab 切分，保留單個標點符號（如括號、逗號）作為名稱的一部分
    return treasureNamesText.value.split(/[\n\r]+|[\s\t]{2,}/).filter(n => n.trim());
});

const cleanedTreasureNames = computed(() => treasureNames.value);

const batchParsedRows = computed(() => {
        const results = [];
        const lines = batchInput.value.split('\n');
        
        let currentContextYear = new Date().getFullYear();
        let currentDateInText = '';
        let currentMasterId = form.value.master_id;

        // Pending treasure-level attributes
        let pendingTreasure = {
            name: '',
            purpose: '',
            effect: '',
            acquisition_method: '',
            content: '',
            remarks: ''
        };

        const parseDateText = (text, defaultYear) => {
            if (!text) return null;
            const dMatch = text.match(/(\d{1,4})[./\-](\d{1,2})[./\-](\d{1,2})/);
            if (dMatch) {
                let y = parseInt(dMatch[1]);
                if (y < 1000) y += 1911;
                return `${y}-${dMatch[2].padStart(2, '0')}-${dMatch[3].padStart(2, '0')}`;
            }
            const shortMatch = text.match(/(\d{1,2})[./\-](\d{1,2})/);
            if (shortMatch) {
                return `${defaultYear}-${shortMatch[1].padStart(2, '0')}-${shortMatch[2].padStart(2, '0')}`;
            }
            const standaloneYMatch = text.match(/^(?:民國)?\s*(\d{2,4})\s*年?$/);
            if (standaloneYMatch) {
                let y = parseInt(standaloneYMatch[1]);
                if (y < 1000) y += 1911;
                return `${y}-01-01`;
            }
            return null;
        };

        const attrKeywords = ['用意', '功效', '作法', '求寶方式', '求寶', '法寶內容', '備註', '得知日期', '登記日期', '求得日期', '日期', '法寶名稱'];
        const prefixesToStrip = /^(允求|賜降|得知|賜予|賜|求得|法寶名稱)\s*[：:]\s*/;

        lines.forEach(line => {
            let normLine = line.normalize('NFKC').trim();
            if (!normLine) return;

            // 1. Detect Standalone Year Line
            const standaloneYearMatch = normLine.match(/^(?:民國)?\s*(\d{2,4})\s*年?$/);
            if (standaloneYearMatch) {
                let y = parseInt(standaloneYearMatch[1]);
                if (y < 1000) y += 1911;
                currentContextYear = y;
                return;
            }

            // 2. Detect Master Header
            const masterMatch = props.masters?.find(m => (normLine === m.name || normLine === (m.name === '父皇仙師' ? '父皇' : m.name)) && normLine.length < 15);
            if (masterMatch) {
                currentMasterId = masterMatch.id;
                return;
            }

            // 3. Detect Standalone Date Header
            const dateHeader = parseDateText(normLine, currentContextYear);
            const isPureDateStr = normLine.replace(/[\d/.\-年月日時分秒\s]/g, '').length === 0;
            if (dateHeader && (isPureDateStr || normLine.startsWith('日期'))) {
                currentDateInText = dateHeader;
                if (normLine.startsWith('日期')) {
                    const val = normLine.replace(/^日期[：:\s]*/, '').trim();
                    const d = parseDateText(val, currentContextYear);
                    if (d) currentDateInText = d;
                }
                return;
            }

            // 4. Detect Attribute Keywords
            const keywordMatch = normLine.match(/^([^：:\s]+)[：:\s]+(.*)/);
            if (keywordMatch) {
                const key = keywordMatch[1].trim();
                const val = keywordMatch[2].trim();

                if (attrKeywords.includes(key)) {
                    if (key === '用意') pendingTreasure.purpose = val;
                    else if (key === '功效') pendingTreasure.effect = val;
                    else if (key === '作法' || key === '求寶方式' || key === '求寶') pendingTreasure.acquisition_method = val;
                    else if (key === '法寶內容') pendingTreasure.content = val;
                    else if (key === '備註') pendingTreasure.remarks = val;
                    else if (key === '法寶名稱') pendingTreasure.name = val.replace(prefixesToStrip, '');
                    else if (['得知日期', '登記日期', '求得日期', '日期'].includes(key)) {
                        const d = parseDateText(val, currentContextYear) || val;
                        currentDateInText = d;
                    }
                    return;
                }

                // Handle prefix keywords: 允求/賜降/求得 etc on same line as recipients
                const prefixKeywords = ['允求', '賜降', '求得', '賜予', '賜', '得知'];
                if (prefixKeywords.includes(key)) {
                    const recMatch = val.match(/^(\S+)\s+(.+)/);
                    if (recMatch) {
                        pendingTreasure.name = recMatch[1].trim();
                        const recNames = recMatch[2].split(/[，、,\s\t]+/).map(n => n.trim()).filter(n => n);
                        recNames.forEach(n => {
                            results.push({
                                name: pendingTreasure.name,
                                recipient_name: resolveNewDharmaName(n),
                                person_remarks: '',
                                master_id: currentMasterId,
                                date: currentDateInText,
                                purpose: pendingTreasure.purpose,
                                effect: pendingTreasure.effect,
                                acquisition_method: pendingTreasure.acquisition_method,
                                content: pendingTreasure.content,
                                remarks: pendingTreasure.remarks,
                                obtained_date: currentDateInText || form.value.record_date || '',
                                status: '已登記'
                            });
                        });
                    } else {
                        pendingTreasure.name = val.replace(prefixesToStrip, '');
                    }
                    return;
                }
            }

            // 5. Handle tab-separated lines: 日期\t前綴:法寶名稱\t法號
            if (normLine.includes('\t')) {
                const parts = normLine.split('\t').map(p => p.trim()).filter(p => p);
                if (parts.length >= 2) {
                    const dateParsed = parseDateText(parts[0], currentContextYear);
                    if (dateParsed) currentDateInText = dateParsed;
                    let treasureRaw = parts[1];
                    const pfMatch = treasureRaw.match(/^(允求|賜降|求得|賜予|賜|得知|法寶名稱)\s*[：:]\s*/);
                    if (pfMatch) treasureRaw = treasureRaw.replace(pfMatch[0], '');
                    pendingTreasure.name = treasureRaw;
                    if (parts.length >= 3) {
                        const recNames = parts[2].split(/[，、,\s\t]+/).map(n => n.trim()).filter(n => n);
                        recNames.forEach(n => {
                            results.push({
                                name: pendingTreasure.name,
                                recipient_name: resolveNewDharmaName(n),
                                person_remarks: '',
                                master_id: currentMasterId,
                                date: currentDateInText,
                                purpose: pendingTreasure.purpose,
                                effect: pendingTreasure.effect,
                                acquisition_method: pendingTreasure.acquisition_method,
                                content: pendingTreasure.content,
                                remarks: pendingTreasure.remarks,
                                obtained_date: currentDateInText || form.value.record_date || '',
                                status: '已登記'
                            });
                        });
                    }
                    return;
                }
            }

            // 6. Detect if the line contains Dharma Names (Recipient Line)
            const names = normLine.split(/[，、,\s\t]+/).map(n => n.trim()).filter(n => n);
            const knownNames = dharmaNames.value.length > 0
                ? names.filter(n => {
                    const resolved = resolveNewDharmaName(n);
                    return dharmaNames.value.some(dn => dn.name === resolved || dn.alias === n);
                  })
                : names.filter(n => !n.includes('：') && !n.includes(':'));

            if (knownNames.length > 0 && pendingTreasure.name) {
                // Accept line if it has at least one recipient name and a treasure name is set
                knownNames.forEach(n => {
                    results.push({
                        name: pendingTreasure.name,
                        recipient_name: resolveNewDharmaName(n),
                        person_remarks: '',
                        master_id: currentMasterId,
                        date: currentDateInText,
                        purpose: pendingTreasure.purpose,
                        effect: pendingTreasure.effect,
                        acquisition_method: pendingTreasure.acquisition_method,
                        content: pendingTreasure.content,
                        remarks: pendingTreasure.remarks,
                        obtained_date: currentDateInText || form.value.record_date || '',
                        status: '已登記'
                    });
                });
                return;
            }

            // 6. Detect a standalone treasure name (not a keyword line, not a recipient line)
            if (normLine.length < 50 && !normLine.includes('，') && !normLine.includes('、') && !normLine.includes('：') && !normLine.includes(':')) {
                // Clear pending attributes when a new treasure name starts (attribute bleed prevention)
                pendingTreasure = {
                    name: '',
                    purpose: '',
                    effect: '',
                    acquisition_method: '',
                    content: '',
                    remarks: ''
                };
                // If it has a prefix, strip it
                if (normLine.match(prefixesToStrip)) {
                    pendingTreasure.name = normLine.replace(prefixesToStrip, '');
                } else {
                    pendingTreasure.name = normLine;
                }
            }
        });
        return results;
    });

const parsedItemsCount = computed(() => batchParsedRows.value.length);

const previewItems = computed(() => {
    return batchParsedRows.value.slice(0, 20).map(r => {
        const recipient = r.recipient_name ? ` (${r.recipient_name})` : '';
        return `${r.name}${recipient}`;
    });
});

const rawLines = computed(() => {
    return batchInput.value.split('\n').map(l => l.trim()).filter(l => l !== '');
});

// Methods for row management removed as we use textarea now

const resolveNewDharmaName = (input) => {
    if (!input || !input.trim()) return input;
    const trimmed = input.trim();
    // 1. Direct match
    const directMatch = dharmaNames.value.find(dn => dn.name === trimmed);
    if (directMatch) return directMatch.name;

    // 2. Alias match (Old Name check)
    const aliasMatch = dharmaNames.value.find(dn => dn.alias === trimmed);
    if (aliasMatch) {
        return aliasMatch.name;
    }
    return trimmed;
};

const fetchDharmaNames = async () => {
    try {
        const res = await axios.get('/api/dharma-names-list?all=1');
        dharmaNames.value = res.data;
    } catch (e) {
        console.error('Failed to load dharma names', e);
    }
};

const addPersonnelRow = () => {
    personnel.value.push({
        custom_name: '',
        relationship: '',
        obtained_date: '',
        remarks: ''
    });
};

onMounted(() => {
    fetchDharmaNames();
    if (personnel.value.length === 0) addPersonnelRow();
    if (form.value.master_id) {
        const m = props.masters?.find(m => m.id === form.value.master_id);
        if (m) masterNameInput.value = (m.name === '父皇仙師' ? '父皇' : m.name);
    }
});

const removePersonnelRow = (idx) => {
    personnel.value.splice(idx, 1);
};

const movePersonnel = (idx, direction) => {
    const targetIdx = idx + direction;
    if (targetIdx < 0 || targetIdx >= personnel.value.length) return;
    const item = personnel.value[idx];
    personnel.value.splice(idx, 1);
    personnel.value.splice(targetIdx, 0, item);
};

const resolveMasterId = () => {
    const m = props.masters?.find(m => m.name === masterNameInput.value || (m.name === '父皇仙師' && masterNameInput.value === '父皇'));
    if (m) form.value.master_id = m.id;
};

watch(masterNameInput, () => { resolveMasterId(); });

watch(() => props.initialData, (newVal) => {
    form.value = { ...newVal };
    treasureNamesText.value = newVal.name || '';
    if (newVal.master_id) {
        const m = props.masters?.find(m => m.id === newVal.master_id);
        if (m) masterNameInput.value = (m.name === '父皇仙師' ? '父皇' : m.name);
    }
    if (newVal.dharma_name_registries) {
        personnel.value = newVal.dharma_name_registries.map(r => ({
            dharma_name_id: r.dharma_name_id,
            custom_name: r.dharma_name?.name || r.custom_name || '',
            relationship: Array.isArray(r.related_personnel) ? r.related_personnel.join('、') : (r.related_personnel || ''),
            obtained_date: r.obtained_date || '',
            remarks: Array.isArray(r.remarks) ? r.remarks.join('\n') : (r.remarks || '')
        }));
    }
}, { deep: true, immediate: true });

watch(() => props.mode, (newVal) => {
    if (newVal) {
        localMode.value = newVal;
        currentStep.value = 1;
    }
});

const handlePersonnelEnter = (idx) => {
    if (idx === personnel.value.length - 1) {
        addPersonnelRow();
    }
    nextTick(() => {
        const inputs = document.querySelectorAll('.personnel-name-input');
        if (inputs[idx + 1]) inputs[idx + 1].focus();
    });
};

watch(personnel, (newVal) => {
    newVal.forEach((p, idx) => {
        if (p.relationship) {
            let rel = p.relationship.trim();
            // Keep the '之' if present, and don't force convert to 母親/父親
            if (!rel.startsWith('之') && !rel.startsWith('的')) {
                // Optional: add '之' if it's a known relative term? 
                // The user said "之字不顯示的規則去掉", implying they want it to show up.
            }
        }
    });
}, { deep: true });

// handleTreasureNameInput removed
const getMasterName = (id) => {
    const m = props.masters?.find(m => String(m.id) === String(id));
    return m ? m.name : '預設';
};

const handlePersonnelNameInput = (idx, event) => {
    let val = event.target.value;

    // Rule: "Name之Relative" split
    const relSplitMatch = val.match(/^(.*?)[之的](.+)$/);
    if (relSplitMatch) {
        const namePart = relSplitMatch[1].trim();
        let relPart = relSplitMatch[2].trim();

        // Keep the '之' or '的' as part of the relationship
        const connector = val.includes('之') ? '之' : '的';

        const resolvedName = resolveNewDharmaName(namePart);
        personnel.value[idx].custom_name = resolvedName;
        personnel.value[idx].relationship = connector + relPart;
        return;
    }

    const splitters = /[，、, \s\t]+/;

    // Check for multi-name input (e.g., "A B C" or "A,B,C")
    if (splitters.test(val)) {
        const names = val.split(splitters).filter(n => n.trim());
        if (names.length > 1) {
            // Apply conversion to first name
            personnel.value[idx].custom_name = resolveNewDharmaName(names[0]);

            // Insert new rows after current index with conversion
            const newRows = names.slice(1).map(n => ({
                custom_name: resolveNewDharmaName(n),
                relationship: personnel.value[idx].relationship,
                obtained_date: personnel.value[idx].obtained_date,
                remarks: ''
            }));

            personnel.value.splice(idx + 1, 0, ...newRows);

            // Move focus to the end of the newly added rows
            nextTick(() => {
                const inputs = document.querySelectorAll('.personnel-name-input');
                const lastIdx = idx + names.length - 1;
                if (inputs[lastIdx]) inputs[lastIdx].focus();
            });
        } else if (names.length === 1) {
            personnel.value[idx].custom_name = resolveNewDharmaName(names[0]);
        }
    } else {
        // Individual name entry without splitters - resolve on typing? 
        // Better to resolve when they leave or paste. But let's try a partial resolve if it matches exactly.
        const resolved = resolveNewDharmaName(val);
        if (resolved !== val) {
            personnel.value[idx].custom_name = resolved;
        }
    }
};

const handlePersonnelDateInput = (idx, event) => {
    let val = event.target.value.trim();
    if (!val) return;

    const rocRegex = /^(\d{2,3})([/.-])(\d{1,2})\2(\d{1,2})$/;
    const ceRegex = /^(\d{4})([/.-])(\d{1,2})\2(\d{1,2})$/;

    let isComplete = false;
    let match = val.match(rocRegex);
    if (match) {
        const y = parseInt(match[1]) + 1911;
        const m = match[3].padStart(2, '0');
        const d = match[4].padStart(2, '0');
        personnel.value[idx].obtained_date = `${y}-${m}-${d}`;
        isComplete = true;
    } else {
        match = val.match(ceRegex);
        if (match) {
            const y = match[1];
            const m = match[3].padStart(2, '0');
            const d = match[4].padStart(2, '0');
            personnel.value[idx].obtained_date = `${y}-${m}-${d}`;
            isComplete = true;
        }
    }

    if (isComplete) {
        // Move focus to next line (next person's name field)
        if (idx === personnel.value.length - 1) {
            addPersonnelRow();
        }
        nextTick(() => {
            const nextRowNameInput = document.querySelectorAll('.personnel-name-input')[idx + 1];
            if (nextRowNameInput) nextRowNameInput.focus();
        });
    }
};

const handleFileImport = (event) => {
    const file = event.target.files[0];
    if (!file) return;

    if (typeof XLSX === 'undefined') {
        const script = document.createElement('script');
        script.src = "https://cdn.sheetjs.com/xlsx-latest/package/dist/xlsx.full.min.js";
        script.onload = () => processExcelFile(file);
        document.head.appendChild(script);
    } else {
        processExcelFile(file);
    }
};

const processExcelFile = (file) => {
    const reader = new FileReader();
    reader.onload = (e) => {
        const data = new Uint8Array(e.target.result);
        const workbook = XLSX.read(data, { type: 'array' });
        const firstSheetName = workbook.SheetNames[0];
        const worksheet = workbook.Sheets[firstSheetName];
        const jsonData = XLSX.utils.sheet_to_json(worksheet, { header: 1 });

        let newText = "";
        jsonData.forEach(row => {
            if (row.length === 0) return;
            const line = row.filter(cell => cell !== null && cell !== undefined).join(' ');
            if (line.trim()) newText += line + "\n";
        });

        if (newText) batchInput.value += (batchInput.value ? "\n" : "") + newText;
    };
    reader.readAsArrayBuffer(file);
};

const validateSingle = () => {
    // 只有法寶名稱是必填
    if (!form.value.name && !treasureNamesText.value.trim()) return '請輸入法寶名稱';

    // Auto-fill main record_date if empty but first personnel has a date
    if (!form.value.record_date && personnel.value.length > 0 && personnel.value[0].obtained_date) {
        form.value.record_date = personnel.value[0].obtained_date;
    }
    // Default to today if still empty
    if (!form.value.record_date) {
        form.value.record_date = new Date().toISOString().split('T')[0];
    }

    return null;
};

const handleSubmit = async () => {
    if (props.isSaving) return;
    if (localMode.value === 'single') {
        const error = validateSingle();
        if (error) {
            alert(error);
            return;
        }

        // Clean up personnel: split multiple names and remove empty rows
        let expandedPersonnel = [];
        personnel.value.forEach(p => {
            if (!p.custom_name || !p.custom_name.trim()) return;
            
            const names = p.custom_name.split(/[，、, \s\t]+/).map(n => n.trim()).filter(n => n);
            names.forEach(rawName => {
                const resolvedName = resolveNewDharmaName(rawName);
                const dnMatch = dharmaNames.value.find(dn => dn.name === resolvedName);
                
                if (dnMatch) {
                    expandedPersonnel.push({
                        ...p,
                        dharma_name_id: dnMatch.id,
                        custom_name: dnMatch.name
                    });
                } else {
                    // Strictly DB only names in the name column. 
                    // Non-DB names are moved to remarks of a generic "Anonymous" or handled as error?
                    // User said: "其他法號的親友或備註,都以modal的方式填入備註框"
                    // If the user entered something not in DB, we should notify or reject.
                    // But here we will attempt to find if it's actually meant for the remarks.
                    alert(`「${rawName}」不屬於系統法號庫，請確認。非系統法號請填入備註欄。`);
                    throw new Error('INVALID_DHARMA_NAME');
                }
            });
        });
        
        // De-duplicate expandedPersonnel based on dharma_name_id
        const uniquePersonnel = [];
        const seenIds = new Set();
        expandedPersonnel.forEach(p => {
            if (!seenIds.has(p.dharma_name_id)) {
                seenIds.add(p.dharma_name_id);
                uniquePersonnel.push(p);
            }
        });
        const cleanedPersonnel = uniquePersonnel;

        // Edit mode: use entire textarea as single name, emit once
        if (isEditing.value) {
            const finalName = treasureNamesText.value.trim();
            const payload = {
                ...form.value,
                name: finalName,
                dharma_name_registries: cleanedPersonnel.map(p => ({
                    ...p,
                    remarks: p.remarks,
                    related_personnel: p.relationship ? p.relationship.split(/[、, ]+/).filter(x => x) : []
                }))
            };
            emit('saveSingle', payload);
        } else if (cleanedTreasureNames.value.length > 0) {
            cleanedTreasureNames.value.forEach(tn => {
                let finalName = tn.trim();
                let finalMethod = form.value.acquisition_method;

                const methodMatch = finalName.match(/(.*)\s*(求寶|求寶方式)[：:](.*)/);
                if (methodMatch) {
                    finalName = methodMatch[1].trim();
                    finalMethod = methodMatch[3].trim();
                }

                const payload = { 
                    ...form.value, 
                    name: finalName,
                    acquisition_method: finalMethod,
                    dharma_name_registries: cleanedPersonnel.map(p => ({
                        ...p,
                        remarks: p.remarks,
                        related_personnel: p.relationship ? p.relationship.split(/[、, ]+/).filter(x => x) : []
                    }))
                };
                emit('saveSingle', payload);
            });
        } else {
            // Fallback for single name in form.name
            const payload = {
                ...form.value,
                dharma_name_registries: cleanedPersonnel.map(p => ({
                    ...p,
                    remarks: p.remarks,
                    related_personnel: p.relationship ? p.relationship.split(/[、, ]+/).filter(x => x) : []
                }))
            };
            emit('saveSingle', payload);
        }
    } else {
        // Batch Save Logic
        if (!form.value.master_id) {
            alert('請選擇仙師');
            return;
        }
        if (batchParsedRows.value.length === 0) {
            alert('請貼上或輸入有效資料內容');
            return;
        }
        emit('saveBatch', { 
            input: batchInput.value, 
            masterId: form.value.master_id,
            rows: batchParsedRows.value.map(row => ({
                name: row.name,
                master_id: row.master_id || form.value.master_id,
                record_date: row.date || form.value.record_date || '',
                purpose: row.purpose || form.value.purpose || '',
                effect: row.effect || form.value.effect || '',
                acquisition_method: row.acquisition_method || '',
                content: row.content || '',
                remarks: row.remarks || '',
                recipient_name: row.recipient_name,
                person_remarks: row.person_remarks || ''
            }))
        });
    }
};

const getDharmaOptions = () => {
    return dharmaNames.value.map(dn => dn.name);
};

const updatePersonnelRemarks = (idx, content) => {
    if (personnel.value[idx]) {
        personnel.value[idx].remarks = content;
    }
};

defineExpose({ updatePersonnelRemarks });
</script>

<style scoped>
.tab-button.active { background-color: #2563eb; color: white; }
.save-button { background-color: #2563eb; color: white; height: 52px; font-size: 18px; }
.add-personnel-button { background-color: #4f46e5; color: white; font-size: 18px; }
.btn { font-size: 18px; }
.animate-slide-up { animation: slideUp 0.25s cubic-bezier(0.16, 1, 0.3, 1); }
.animate-fade-in { animation: fadeIn 0.3s ease-out; }
.animate-pop-in { animation: popIn 0.25s cubic-bezier(0.2, 1, 0.3, 1); }
@keyframes slideUp { from { transform: translateY(100%); } to { transform: translateY(0); } }
@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
@keyframes popIn { from { opacity: 0; transform: scale(0.95) translateY(-10px); } to { opacity: 1; transform: scale(1) translateY(0); } }

.dropdown-enter-active, .dropdown-leave-active { transition: all 0.2s ease; }
.dropdown-enter-from, .dropdown-leave-to { opacity: 0; transform: translateY(-10px) scale(0.95); }

.custom-scrollbar { -webkit-overflow-scrolling: touch; }
.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }

.step-fade-enter-active, .step-fade-leave-active { transition: all 0.28s cubic-bezier(0.4, 0, 0.2, 1); }
.step-fade-enter-from { opacity: 0; transform: translateX(30px); }
.step-fade-leave-to { opacity: 0; transform: translateX(-30px); }
</style>
