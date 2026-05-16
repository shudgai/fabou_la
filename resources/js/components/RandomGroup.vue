<template>
    <teleport to="body">
    <div class="fixed inset-0 z-[3500] flex items-end md:items-center justify-center">
        <!-- Backdrop -->
        <div class="hidden md:block fixed inset-0 bg-slate-900/40 backdrop-blur-sm" @click="$emit('close')"></div>

        <!-- Form Container -->
        <div class="relative w-full h-full md:h-auto md:max-h-[95dvh] md:max-w-4xl bg-white md:rounded-[32px] md:shadow-2xl flex flex-col overflow-hidden animate-slide-up">
            <!-- Global Close Button -->
            <button @click="$emit('close')" class="absolute right-4 top-4 z-[500] p-2 text-slate-300 hover:text-slate-600 transition-all active:scale-90 bg-white/80 backdrop-blur-sm rounded-full shadow-sm md:shadow-none">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
            <div class="flex h-full bg-white overflow-hidden font-sans relative">

        <!-- STEP 1: PERSONNEL SELECTION -->
        <div v-show="currentStep === 1" class="flex flex-col w-full h-full bg-white overflow-hidden relative">

<!-- Main scrollable selection grid -->
             <div class="flex-1 overflow-y-auto custom-scrollbar pb-[500px]">
                <div class="flex items-start justify-between pl-3 pr-14 py-1.5 md:pt-[60px]">
                    <div class="flex flex-col flex-1 min-w-0">
                        <div class="flex items-center gap-2">
                            <button v-if="selectionFiltered" @click="selectionFiltered = false" class="p-2 -ml-3 text-slate-400 active:scale-90 transition-all mr-1 shrink-0">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" /></svg>
                            </button>
                            <span class="shrink-0 leading-tight font-black" style="font-size: 17px !important; color: #0f172a !important;">
                                {{ selectionFiltered ? '已確認名單' : '點選待定法號' }}
                            </span>
                        </div>
                        <div v-if="!selectionFiltered" class="flex items-center gap-1 mt-1">
                            <span class="text-[12px] text-slate-400 whitespace-nowrap">滑動游標選取人員</span>
                            <input v-model="manualName" @keyup.enter="addManualName" type="text" placeholder="" class="bg-transparent border-none outline-none text-[10px] font-bold w-16 shrink-0">
                        </div>
                        <span v-if="!selectionFiltered" class="font-bold text-slate-800 mt-1" style="font-size: 16px !important;">已選 {{ pendingNames.length }} 人</span>
                    </div>
                    <div class="flex items-center space-x-2 shrink-0 mt-1">
                        <button @click="invertSelection" class="text-indigo-600 font-black text-[16px] active:scale-95 transition-all border-none bg-transparent cursor-pointer shrink-0" style="font-size: 16px !important;">反選</button>
                        <button @click="resetAll" class="text-slate-400 hover:text-red-500 flex items-center justify-center active:scale-90 transition-all shrink-0 border-none p-1">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </button>
                    </div>
                </div>

                <!-- Grid evenly distributed, stretching boxes -->
                <div class="grid grid-cols-4 md:grid-cols-5 px-1 w-full mt-[15px]" style="gap: 4px; background: #ffffff;">
                    <button
                        v-for="user in displayUsers"
                        :key="user.id || user.name"
                        @mousedown="startDrag(user.name)"
                        @mouseenter="onDragEnter(user.name)"
                        @touchstart.passive="handleTouchStart($event, user.name)"
                        @touchmove.passive="handleTouchMove($event)"
                        @touchend.passive="stopDrag"
                        class="dharma-btn flex items-center justify-center font-normal transition-all active:scale-95 rounded-md border shadow-sm w-full min-h-[45px]"
                        :style="{ 
                            ...getPendingStyle(user.name),
                            fontSize: '16px !important'
                        }"
                        :data-name="user.name"
                    >
                        <span class="truncate leading-none pointer-events-none relative">
                            {{ user.name }}
                            <span v-if="pendingNames.includes(user.name)" 
                                class="absolute -top-3.5 -right-3 w-4 h-4 rounded-full bg-rose-500 text-white text-[10px] flex items-center justify-center border border-white shadow-sm font-black">
                                {{ pendingNames.indexOf(user.name) + 1 }}
                            </span>
                        </span>
                    </button>
                </div>
            </div>

<!-- Confirm button — FIXED above mobile nav -->
             <div class="fixed md:absolute left-0 right-0 px-4 py-3 bg-white/95 backdrop-blur-md border-t border-slate-100 z-[200] w-full md:left-1/2 md:-translate-x-1/2 md:max-w-xl bottom-[calc(7dvh+env(safe-area-inset-bottom))] md:bottom-0">
                 <button
                     @click="confirmSelection"
                     :disabled="pendingNames.length === 0"
                     class="w-full rounded-2xl font-black transition-all active:scale-[0.98] text-white shadow-lg disabled:opacity-50 flex items-center justify-center"
                     :style="{
                         background: pendingNames.length === 0 ? '#94a3b8' : (selectionFiltered ? '#16a34a' : '#1d4ed8'),
                         color: '#ffffff !important',
                         textShadow: '0 1px 3px rgba(0,0,0,0.3)',
                         boxShadow: '0 4px 20px rgba(0,0,0,0.1)',
                         fontSize: '16px !important',
                         paddingTop: '13px',
                         paddingBottom: '13px'
                     }"
                 >
                     <span v-if="!selectionFiltered" style="color: #ffffff !important;">完成人員選取 (進入排列)</span>
                      <span v-else style="color: #ffffff !important;">確定 → 進入分組設定</span>
                   </button>
               </div>
        </div>

        <!-- STEP 2: GUARDIANS (關主設定) -->
        <div v-show="currentStep === 2" class="flex flex-col w-full h-full bg-slate-50/10 overflow-hidden relative">
            <div class="animate-slide-in flex flex-col h-full overflow-hidden">
                <!-- Navigation Header -->
                <div class="bg-white border-b border-slate-50 p-2 px-3 flex items-center sticky top-0 z-10 md:mt-[60px]">
                    <button @click="currentStep = 1" class="p-2 -ml-2 text-slate-400 active:scale-90 transition-all mr-1">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" /></svg>
                    </button>
                    <div class="flex items-center justify-between w-full">
                        <span class="font-black text-[20px] text-slate-800">步驟 2：關主設定</span>
                        <div class="flex items-center space-x-1 text-slate-400">
                            <span class="font-bold" style="font-size: 16px !important;">總計</span>
                            <span class="font-black text-indigo-600" style="font-size: 16px !important;">{{ selectedNames.length }}</span>
                        </div>
                    </div>
                </div>

                <!-- Main Content Container -->
                <div class="p-4 flex-1 overflow-y-auto no-scrollbar flex flex-col gap-4 max-w-2xl mx-auto w-full pb-40">
                    <div class="bg-amber-50/10 border border-amber-200 p-3 rounded-2xl space-y-3">
                        <div class="flex items-center justify-between px-0.5">
                            <label class="font-black uppercase tracking-wider text-amber-600" style="font-size: 16px !important;">🌟 指定關主</label>
                            <span class="font-black text-amber-400 uppercase tracking-widest" style="font-size: 16px !important;">⚠️ 全場抽選</span>
                        </div>

                        <div class="flex flex-col space-y-3">
                            <div class="relative w-full">
                                <div class="flex items-center bg-white border border-amber-100 rounded-xl px-3 h-12 shadow-sm">
                                    <input type="text" v-model="guardianQuery" placeholder="搜尋或選取關主..." @focus="isGDropdownOpen = true" 
                                        class="w-full bg-transparent outline-none text-[16px] font-black text-amber-700 placeholder:text-amber-200">
                                    <svg class="w-5 h-5 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </div>
                                <div v-show="isGDropdownOpen && filteredGDropdown.length > 0 && !isDrawing" class="absolute top-[52px] left-0 right-0 bg-white border border-amber-100 rounded-xl shadow-2xl z-[50] max-h-[200px] overflow-y-auto custom-scrollbar">
                                    <button v-for="name in filteredGDropdown" :key="'gopt'+name" @click="addManualGuardian(name)" 
                                        class="w-full text-left px-4 py-3 text-[16px] font-black text-slate-700 hover:bg-amber-50 hover:text-amber-700 border-b border-slate-50 last:border-0">{{ name }}</button>
                                </div>

                                <div v-if="isDrawing && currentType === 'guardian'" class="absolute inset-0 bg-amber-50/90 rounded-xl flex items-center justify-center space-x-3 z-[60] border border-amber-200 animate-pulse">
                                    <span class="dot bg-amber-400 w-3 h-3"></span>
                                    <span class="dot bg-amber-500 w-3 h-3"></span>
                                    <span class="dot bg-amber-600 w-3 h-3"></span>
                                    <span class="text-[14px] font-black text-amber-600 uppercase tracking-widest">抽選中...</span>
                                </div>

                                <div v-if="isGDropdownOpen" @click="isGDropdownOpen = false" class="fixed inset-0 z-[40]"></div>
                            </div>
                            
                            <div class="flex items-center space-x-2 w-full pt-1">
                                <div class="flex items-center border border-amber-200 rounded-xl overflow-hidden h-12 bg-white shadow-none flex-1">
                                    <button @click="pickSize = Math.max(1, pickSize - 1)" class="w-12 h-full text-white bg-slate-300 font-black shadow-none border-none active:bg-slate-400" style="color: #ffffff !important; text-shadow: 0 1px 2px rgba(0,0,0,0.2); font-size: 24px !important;">−</button>
                                    <input 
                                        type="number" 
                                        v-model.number="pickSize" 
                                        @blur="pickSize = Math.max(1, Math.min(selectedNames.length, pickSize || 1))"
                                        class="flex-1 font-black text-center text-amber-900 leading-none bg-transparent outline-none w-full"
                                        style="font-size: 24px !important;"
                                    >
                                    <button @click="pickSize = Math.min(10, pickSize + 1)" class="w-12 h-full text-white bg-slate-300 font-black shadow-none border-none active:bg-slate-400" style="color: #ffffff !important; text-shadow: 0 1px 2px rgba(0,0,0,0.2); font-size: 24px !important;">＋</button>
                                </div>
                                <button @click="pickGuardians" :disabled="selectedNames.length < pickSize || isDrawing" 
                                    class="h-12 px-5 font-black text-[16px] rounded-xl bg-amber-400 text-white shadow-sm active:scale-95 disabled:opacity-30 whitespace-nowrap transition-all hover:bg-amber-500"
                                    style="color: #ffffff !important; text-shadow: 0 1px 3px rgba(0,0,0,0.3);">
                                    {{ guardianResults.length > 0 ? '加抽人員' : '隨機抽' }}
                                </button>
                            </div>
                        </div>

                        <div v-if="guardianResults.length > 0" class="pt-4 border-t border-amber-100">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-[14px] font-black text-amber-500/80 uppercase tracking-tighter">* 已抽 {{ guardianResults.length }} 位</span>
                                <button @click="guardianResults = []" class="text-[13px] font-black text-red-400 uppercase tracking-wider">清空關主</button>
                            </div>
                            <div class="flex flex-wrap gap-2">
                                <div v-for="name in guardianResults" :key="'glist'+name" 
                                    class="flex items-center space-x-1.5 bg-amber-500 text-white text-[16px] font-black px-3 py-1.5 rounded-xl animate-fade-in shadow-sm">
                                    <span>{{ name }}</span>
                                    <button @click="removeGuardian(name)" class="text-white bg-red-500 rounded-full w-6 h-6 flex items-center justify-center text-[14px] shadow-none">×</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="fixed md:absolute left-0 right-0 px-4 py-3 bg-white/95 backdrop-blur-md border-t border-slate-100 z-[200] w-full md:left-1/2 md:-translate-x-1/2 md:max-w-xl bottom-[calc(7dvh+env(safe-area-inset-bottom))] md:bottom-0">
                    <div class="w-full">
                        <button
                            @click="currentStep = 3"
                            :disabled="isDrawing"
                            class="w-full rounded-2xl font-black transition-all active:scale-[0.98] shadow-sm bg-indigo-600"
                            :style="{
                                color: '#ffffff !important',
                                textShadow: '0 1px 3px rgba(0,0,0,0.3)',
                                fontSize: '18px !important',
                                paddingTop: '13px',
                                paddingBottom: '13px'
                            }"
                        >
                            下一步：種子組設定 →
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- STEP 3: SEEDS (種子組設定) -->
        <div v-show="currentStep === 3" class="flex flex-col w-full h-full bg-slate-50/10 overflow-hidden relative">
            <div class="animate-slide-in flex flex-col h-full overflow-hidden">
                <div class="bg-white border-b border-slate-50 p-2 px-3 flex items-center sticky top-0 z-10 md:mt-[60px]">
                    <button @click="currentStep = 2" class="p-2 -ml-2 text-slate-400 active:scale-90 transition-all mr-1">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" /></svg>
                    </button>
                    <span class="font-black text-[20px] text-slate-800">步驟 3：種子組設定</span>
                </div>

                <div class="p-4 flex-1 overflow-y-auto no-scrollbar flex flex-col gap-4 max-w-2xl mx-auto w-full pb-40">
                    <div class="bg-white p-4 rounded-2xl border border-slate-100 space-y-4 shadow-sm">
                        <div class="space-y-2">
                            <label class="font-bold text-slate-400 uppercase tracking-wider px-1" style="font-size: 16px !important;">🌟 指定種子組 (優先扣除)</label>
                            <div class="flex flex-col gap-3 p-3 bg-slate-50 border border-slate-100 rounded-xl min-h-[60px]">
                                    <!-- Desktop View -->
                                    <div class="hidden md:block">
                                        <input 
                                            type="text" 
                                            v-model="seedInput"
                                            @change="addSeedFromSelect($event)" 
                                            placeholder="查詢在場人員加入種子組..." 
                                            class="w-full h-12 bg-white border border-slate-200 rounded-xl px-3 outline-none text-[16px] font-black text-slate-600 cursor-pointer shadow-sm text-center"
                                        >
                                        <compact-datalist v-model="seedInput" :options="availableSeeds" @update:modelValue="onSeedSelected" />
                                    </div>
                                    <!-- Mobile View -->
                                    <editable-input-chips class="md:hidden" v-model="seedInput" :options="availableSeeds" @change="onSeedSelected" placeholder="查詢在場人員..." />

                                <div v-if="seedNames.length > 0" class="flex flex-wrap gap-2 mt-2">
                                    <div v-for="name in seedNames" :key="'seed'+name" class="flex items-center space-x-1.5 bg-indigo-50 text-indigo-900 text-[16px] font-black px-3 py-1.5 rounded-xl border border-indigo-100">
                                        <span>{{ name }}</span>
                                        <button @click="removeSeed(name)" class="text-indigo-400 hover:text-indigo-600 text-[18px]">×</button>
                                    </div>
                                </div>
                                <div v-else class="text-[14px] font-bold text-slate-400 text-center py-2">
                                    目前無指定種子
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="fixed md:absolute left-0 right-0 px-4 py-3 bg-white/95 backdrop-blur-md border-t border-slate-100 z-[200] w-full md:left-1/2 md:-translate-x-1/2 md:max-w-xl bottom-[calc(7dvh+env(safe-area-inset-bottom))] md:bottom-0">
                    <div class="w-full">
                        <button
                            @click="currentStep = 4"
                            class="w-full rounded-2xl font-black transition-all active:scale-[0.98] shadow-sm bg-indigo-600"
                            :style="{
                                color: '#ffffff !important',
                                textShadow: '0 1px 3px rgba(0,0,0,0.3)',
                                fontSize: '18px !important',
                                paddingTop: '13px',
                                paddingBottom: '13px'
                            }"
                        >
                            下一步：分組規則 →
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- STEP 4: RULES & SIZE (分組規則與人數) -->
        <div v-show="currentStep === 4" class="flex flex-col w-full h-full bg-slate-50/10 overflow-hidden relative">
            <div class="animate-slide-in flex flex-col h-full overflow-hidden">
                <div class="bg-white border-b border-slate-50 p-2 px-3 flex items-center sticky top-0 z-10 md:mt-[60px]">
                    <button @click="currentStep = 3" class="p-2 -ml-2 text-slate-400 active:scale-90 transition-all mr-1">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" /></svg>
                    </button>
                    <span class="font-black text-[20px] text-slate-800">步驟 4：分組規則</span>
                </div>

                <div class="p-4 flex-1 overflow-y-auto no-scrollbar flex flex-col gap-4 max-w-2xl mx-auto w-full pb-40">
                    <div class="bg-white p-4 rounded-2xl border border-slate-100 space-y-4 shadow-sm">
                        <!-- Rules & Calculation -->
                        <div class="space-y-3 pb-4 border-b border-slate-50">
                            <div class="flex items-center justify-between px-1">
                                <label class="text-[15px] font-black text-indigo-500 uppercase tracking-widest">⚙️ 分組規則試算</label>
                                <div class="flex items-center space-x-2 bg-slate-50 px-3 py-1.5 rounded-lg border border-slate-100">
                                    <span class="text-[14px] font-black text-slate-700">包含關主</span>
                                    <button @click="includeGuardians = !includeGuardians" :class="['w-10 h-6 rounded-full transition-all relative', includeGuardians ? 'bg-indigo-500' : 'bg-slate-300']">
                                        <div :class="['absolute top-[4px] w-4 h-4 bg-white rounded-full transition-all shadow-sm', includeGuardians ? 'left-[22px]' : 'left-[4px]']"></div>
                                    </button>
                                </div>
                            </div>

                            <div class="p-3 bg-slate-50 rounded-xl space-y-3 transition-all duration-300 border border-slate-100">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-[14px] font-black text-slate-500">📋 分派人數</span>
                                    <span :class="['text-[11px] font-black px-2 py-0.5 rounded-full', includeGuardians ? 'bg-indigo-100 text-indigo-600' : 'bg-amber-100 text-amber-600']">{{ includeGuardians ? '包含關主模式' : '排除關主模式' }}</span>
                                </div>
                                <div class="space-y-2 px-1">
                                    <div class="flex items-center justify-between font-black" style="font-size: 16px !important;"><span class="text-slate-400">❶ 在場總計</span><span class="text-slate-800">{{ selectedNames.length }} 人</span></div>

                                    <div class="flex items-center justify-between text-[14px] font-bold text-slate-400 pl-4 py-0.5 border-l-2 border-slate-200 ml-1">
                                        <span>− 種子組名單 <span v-if="guardianSeedsCount > 0" class="text-[11px] opacity-60 font-black ml-1">(含 {{ guardianSeedsCount }} 位關主)</span></span>
                                        <span>{{ seedNames.length }} 人</span>
                                    </div>
                                    <div v-if="!includeGuardians && nonSeedGuardiansCount > 0" class="flex items-center justify-between text-[14px] font-bold text-slate-400 pl-4 py-0.5 border-l-2 border-slate-200 ml-1">
                                        <span>− 指定關主名單 <span class="text-[10px] opacity-40 ml-1">(扣除重複)</span></span>
                                        <span>{{ nonSeedGuardiansCount }} 人</span>
                                    </div>

                                    <div class="flex items-center justify-between text-[15px] font-black pt-2 border-t border-dashed border-slate-200 mt-2">
                                        <span class="text-rose-400">❷ 總計排除</span>
                                        <span class="text-rose-500">{{ totalExclusionCount }} 人</span>
                                    </div>

                                    <div class="border-t border-slate-200 mt-3 pt-3">
                                        <div class="flex items-center justify-between">
                                            <div class="flex flex-col">
                                                <span class="text-slate-900 font-black text-[18px]">❸ 實際隨機分派</span>
                                                <span class="text-[11px] font-bold text-slate-400">❶ 總計 − ❷ 排除</span>
                                            </div>
                                            <span :class="['text-[24px] font-black leading-none', includeGuardians ? 'text-indigo-600' : 'text-amber-600']">{{ activePoolCount }} 人</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Group Size -->
                        <div class="space-y-3">
                            <label class="text-[17px] font-black text-slate-700 uppercase tracking-wider px-1">每組固定人數</label>
                            <div class="flex items-center border border-slate-200 rounded-xl overflow-hidden h-16 bg-slate-50/50 shadow-sm">
                                <button @click="groupSize = Math.max(2, groupSize - 1)" class="w-16 h-full text-white bg-slate-300 font-black transition-colors shadow-none border-none active:bg-slate-400" style="color: #ffffff !important; text-shadow: 0 1px 2px rgba(0,0,0,0.2); font-size: 28px !important;">−</button>
                                <div class="flex-1 flex flex-col items-center justify-center">
                                    <input 
                                        type="number" 
                                        v-model.number="groupSize" 
                                        @blur="groupSize = Math.max(2, Math.min(selectedNames.length, groupSize || 2))"
                                        class="font-black text-slate-800 leading-none bg-transparent outline-none text-center w-full"
                                        style="font-size: 30px !important;"
                                    >
                                </div>
                                <button @click="groupSize = Math.min(20, groupSize + 1)" class="w-16 h-full text-white bg-slate-300 font-black transition-colors shadow-none border-none active:bg-slate-400" style="color: #ffffff !important; text-shadow: 0 1px 2px rgba(0,0,0,0.2); font-size: 28px !important;">＋</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="fixed md:absolute left-0 right-0 px-4 py-3 bg-white/95 backdrop-blur-md border-t border-slate-100 z-[200] w-full md:left-1/2 md:-translate-x-1/2 md:max-w-xl bottom-[calc(7dvh+env(safe-area-inset-bottom))] md:bottom-0">
                    <div class="w-full">
                        <button
                            @click="doGrouping"
                            :disabled="selectedNames.length < 1 || isDrawing"
                            class="w-full rounded-2xl font-black transition-all active:scale-[0.98] shadow-sm"
                            :style="{
                                background: selectedNames.length < 1 || isDrawing ? '#94a3b8' : 'rgb(0,255,0)',
                                color: '#ffffff !important',
                                textShadow: '0 1px 3px rgba(0,0,0,0.3)',
                                fontSize: '18px !important',
                                paddingTop: '13px',
                                paddingBottom: '13px'
                            }"
                        >
                            <span style="color: #000000 !important;">開始分組演算</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- FULLSCREEN LOTTERY ANIMATION: 抽籤動畫 -->
        <div v-if="isDrawing && currentType === 'group'" class="fixed inset-0 z-[500] flex flex-col items-center justify-center overflow-hidden" style="background: #fefce8;">

            <!-- Ambient glow -->
            <div class="absolute inset-0 pointer-events-none">
                <div style="position:absolute;top:30%;left:50%;transform:translate(-50%,-50%);width:min(400px,90vw);height:min(400px,90vw);background:radial-gradient(circle,rgba(251,191,36,0.15) 0%,transparent 70%);border-radius:50%;"></div>
            </div>

            <!-- Bamboo cup + Flying Sticks Container -->
            <div class="lottery-cup-container" style="transform: translateY(10dvh) scale(0.8);">
                <!-- Flying sticks (multiple, staggered) -->
                <div class="absolute inset-0 pointer-events-none">
                    <div v-for="stick in flyingSticks" :key="stick.id"
                        class="lottery-stick"
                        :style="{
                            left: stick.x + '%',
                            animationDuration: stick.dur + 's',
                            animationDelay: stick.delay + 's',
                            '--rotate': stick.rotate + 'deg',
                            '--drift': stick.drift + 'px',
                        }"
                    >
                        <!-- Stick body -->
                        <div class="stick-body" style="background: #d97706;">
                            <span class="stick-name">{{ stick.name }}</span>
                        </div>
                        <!-- Stick tip -->
                        <div class="stick-tip" style="background: #dc2626;"></div>
                    </div>
                </div>

                <!-- Cup body -->
                <div class="lottery-cup">
                    <!-- Cup rim -->
                    <div class="cup-rim"></div>
                    <!-- Sticks inside the cup, shaking -->
                    <div class="cup-sticks-wrapper">
                        <div v-for="i in 9" :key="'cs'+i" class="cup-stick" :style="{ '--ci': i, '--ctilt': (i*23%11 - 5) + 'deg', background: '#d97706' }"></div>
                    </div>
                    <!-- Cup body -->
                    <div class="cup-body"></div>
                    <!-- Cup base -->
                    <div class="cup-base"></div>
                </div>
                <!-- Current name flying out -->
                <div class="flying-name-display">
                    <span class="flying-name-text">{{ lotteryDisplayNames[0] || '' }}</span>
                </div>
            </div>

            <!-- Status text -->
            <div class="absolute top-[3dvh] left-0 right-0 flex flex-col items-center space-y-2">
                <p class="text-[30px] font-black tracking-widest text-amber-900">隨機抽籤中</p>
                <div class="flex gap-2">
                    <span class="dot-lg" style="background:#b45309;"></span>
                    <span class="dot-lg" style="background:#d97706;"></span>
                    <span class="dot-lg" style="background:#f59e0b;"></span>
                </div>
            </div>
        </div>

        <!-- STEP 5: RESULTS (分組結果) -->
        <div v-show="currentStep === 5" class="flex flex-col w-full h-full bg-white overflow-hidden">
            <div class="bg-white border-b border-slate-100 p-3 px-4 flex items-center justify-between sticky top-0 z-10 md:mt-[60px]">
                <button @click="currentStep = 4" class="text-slate-400 hover:text-indigo-600 p-1.5 -ml-1.5 mr-2 flex items-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
                <h2 class="flex-1 whitespace-nowrap font-black" style="color: #0f172a !important; font-size: 26px !important;">分組結果</h2>
                <div class="flex items-center space-x-2">
                    <button @click="handleNextRound" class="w-[72px] h-[36px] flex items-center justify-center font-black text-white bg-indigo-200 rounded-full shadow-sm border-none transition-all active:scale-95 whitespace-nowrap" style="color: #ffffff !important; text-shadow: 0 1px 3px rgba(0,0,0,0.3); font-size: 16px !important;">下一輪</button>
                    <button @click="redrawAll" class="w-[72px] h-[36px] flex items-center justify-center font-black text-white bg-rose-200 rounded-full shadow-sm border-none transition-all active:scale-95 whitespace-nowrap" style="color: #ffffff !important; text-shadow: 0 1px 3px rgba(0,0,0,0.3); font-size: 16px !important;">重抽</button>
                    <button @click="copyResult" class="w-[72px] h-[36px] flex items-center justify-center font-black text-white bg-emerald-200 rounded-full shadow-sm border-none transition-all active:scale-95 whitespace-nowrap" style="color: #ffffff !important; text-shadow: 0 1px 3px rgba(0,0,0,0.3); font-size: 16px !important;">複製</button>
                </div>
            </div>
            <div class="flex-1 overflow-y-auto custom-scrollbar px-3 pt-3 pb-24">
                <!-- Guardian results -->
                <div v-if="guardianResults.length > 0" class="bg-amber-50 p-3 rounded-2xl border border-amber-200 mb-3">
                    <h4 class="text-[15px] font-black text-amber-600 mb-2">🌟 關主名單 ({{ guardianResults.length }} 位)</h4>
                    <div class="flex flex-wrap gap-x-4 gap-y-0.5">
                        <div v-for="name in guardianResults" :key="'g3'+name" class="text-amber-700 font-black text-[18px]">{{ name }}</div>
                    </div>
                </div>
                <!-- Group results -->
                <div v-if="groups.length > 0" class="space-y-2">
                    <div v-for="(group, idx) in groups" :key="'g3g'+idx"
                        :class="['p-3 px-4 rounded-2xl border transition-all', group.isSeed ? 'bg-amber-50 border-amber-200' : 'bg-white border-slate-200 shadow-sm']"
                    >
                        <div class="flex items-center justify-between mb-1">
                            <span :class="['text-[14px] font-black', group.isSeed ? 'text-amber-700' : 'text-slate-900']">{{ group.name }}</span>
                            <span :class="['text-[12px] font-bold', group.isSeed ? 'text-amber-500' : 'text-slate-400']">{{ group.members.length }} 人</span>
                        </div>
                        <div class="flex flex-wrap gap-x-4 gap-y-1">
                            <span v-for="member in group.members" :key="member" :class="['font-black', group.isSeed ? 'text-amber-900' : 'text-black']" style="font-size: 16px !important;">{{ member }}</span>
                        </div>
                    </div>
                </div>
                <div v-else class="text-center py-20 text-slate-400 font-bold">尚未分組，請回上一頁按「開始分組演算」</div>
            </div>
        </div>

        <!-- Global Action Confirm / Toast (Critical for iOS) -->
        <div v-if="persistentToast" class="fixed inset-0 z-[9999] flex items-center justify-center p-6 bg-slate-900/40 backdrop-blur-sm animate-fade-in">
            <div class="bg-white w-full max-w-sm rounded-[32px] shadow-2xl overflow-hidden animate-slide-up border border-white/20">
                <div class="p-8 text-center space-y-6">
                    <div class="flex flex-col items-center">
                        <div v-if="persistentToast.type === 'clear' || persistentToast.type === 'redraw'" class="w-16 h-16 bg-rose-50 text-rose-500 rounded-2xl flex items-center justify-center mb-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </div>
                        <div v-else-if="persistentToast.type === 'success'" class="w-16 h-16 bg-emerald-50 text-emerald-500 rounded-2xl flex items-center justify-center mb-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="3" stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <div v-else class="w-16 h-16 bg-indigo-50 text-indigo-500 rounded-2xl flex items-center justify-center mb-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <h3 class="text-[20px] font-black text-slate-900 leading-tight whitespace-pre-wrap">{{ persistentToast.msg }}</h3>
                    </div>

                    <div class="flex flex-col space-y-3">
                        <button v-if="persistentToast.type !== 'success' && persistentToast.type !== 'error'" 
                                @click="executeToastAction" 
                                :class="persistentToast.type === 'clear' || persistentToast.type === 'redraw' ? 'bg-rose-500 shadow-rose-200/50' : 'bg-indigo-600 shadow-indigo-200/50'"
                                class="w-full py-4 text-white rounded-2xl font-black text-[18px] active:scale-95 transition-all shadow-lg" 
                                style="color: white !important;">
                            {{ persistentToast.type === 'clear' ? '確認清空' : '確認歸還' }}
                        </button>
                        <button @click="persistentToast = null" 
                                :class="persistentToast.type === 'success' || persistentToast.type === 'error' ? 'bg-indigo-600 text-white shadow-indigo-100' : 'bg-slate-100 text-slate-500'"
                                class="w-full py-4 rounded-2xl font-black text-[18px] active:scale-95 transition-all shadow-lg"
                                :style="{ color: (persistentToast.type === 'success' || persistentToast.type === 'error' ? 'white !important' : 'inherit') }">
                            {{ persistentToast.type === 'success' || persistentToast.type === 'error' ? '確認' : '取消' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </div>
        <!-- Mobile Navbar explicitly added per user request to ensure all interfaces have it -->
        <mobile-navbar 
            is-absolute
            :can-back="true"
            :can-home="false"
            :show-action="false"
            :can-search="false"
            @back="$emit('close')"
            class="md:hidden"
        />
    </div>
    </teleport>
</template>

<script setup>
import { ref, onMounted, watch, computed } from 'vue';
import axios from 'axios';
import MobileNavbar from './MobileNavbar.vue';
import { lockBodyScroll, unlockBodyScroll } from '../utils/iosCompat';

const users = ref([]);
const selectedNames = ref([]);   // Step 2 uses this
const pendingNames = ref([]);    // Step 1 selection buffer
const groupSize = ref(4);
const seedNames = ref([]);
const groups = ref([]);
const guardianResults = ref([]);
const isDrawing = ref(false);
const currentType = ref('');
const pickSize = ref(1);
const includeGuardians = ref(false);
const persistentToast = ref(null);
const seedInput = ref('');

const onSeedSelected = (name) => {
    if (name && !seedNames.value.includes(name)) {
        seedNames.value.push(name);
    }
    seedInput.value = '';
};

const currentStep = ref(1);

const handleNextRound = () => {
    groups.value = [];
    guardianResults.value = [];
    currentStep.value = 2; // Return to guardians selection
};

const guardianQuery = ref('');
const isGDropdownOpen = ref(false);

const isDragging = ref(false);
const dragSelectionType = ref(null); // 'add' or 'remove'
const lastTouchedName = ref(null);

const startDrag = (name) => {
    isDragging.value = true;
    const isSelected = pendingNames.value.includes(name);
    dragSelectionType.value = isSelected ? 'remove' : 'add';
    togglePending(name);
    window.addEventListener('mouseup', stopDrag);
};

const onDragEnter = (name) => {
    if (!isDragging.value) return;
    const isSelected = pendingNames.value.includes(name);
    if (dragSelectionType.value === 'add' && !isSelected) {
        pendingNames.value.push(name);
    } else if (dragSelectionType.value === 'remove' && isSelected) {
        const idx = pendingNames.value.indexOf(name);
        pendingNames.value.splice(idx, 1);
    }
};

const handleTouchStart = (e, name) => {
    lastTouchedName.value = name;
    startDrag(name);
};

const handleTouchMove = (e) => {
    if (!isDragging.value) return;
    const touch = e.touches[0];
    const el = document.elementFromPoint(touch.clientX, touch.clientY);
    if (!el) return;
    const btn = el.closest('.dharma-btn');
    if (btn) {
        const name = btn.getAttribute('data-name');
        if (name && name !== lastTouchedName.value) {
            lastTouchedName.value = name;
            onDragEnter(name);
        }
    }
};

const stopDrag = () => {
    isDragging.value = false;
    lastTouchedName.value = null;
    window.removeEventListener('mouseup', stopDrag);
};

const selectionFiltered = ref(false);
const manualName = ref('');
const addManualName = () => {
    if (!manualName.value.trim()) return;
    const name = manualName.value.trim();
    if (!users.value.some(u => u.name === name)) {
        users.value.push({ id: Date.now(), name: name });
    }
    if (!pendingNames.value.includes(name)) {
        pendingNames.value.push(name);
    }
    manualName.value = '';
};

const displayUsers = computed(() => {
    if (!users.value || !Array.isArray(users.value)) return [];
    const pending = pendingNames.value || [];
    if (selectionFiltered.value) {
        return users.value.filter(u => u && u.name && pending.includes(u.name.trim()));
    }
    return users.value.filter(u => u && u.name);
});

const selectAll = () => {
    if (!users.value) return;
    pendingNames.value = users.value.map(u => u.name.trim());
};

const getPendingStyle = (name) => {
    const t = name.trim();
    const isSelected = pendingNames.value.includes(t);
    if (isSelected) {
        return {
            backgroundColor: '#2563eb', // Professional Blue for SELECTED
            color: '#ffffff !important',
            borderColor: '#fbbf24',
            borderWidth: '3px',
            boxShadow: '0 0 12px rgba(251, 191, 36, 0.5)',
            zIndex: '5'
        };
    } else {
        return {
            backgroundColor: '#ffffff',
            color: '#000000',
            border: '1px solid #d1d5db',
            textShadow: 'none'
        };
    }
};

const toggleSelectionFilter = () => {
    if (pendingNames.value.length > 0) {
        selectionFiltered.value = !selectionFiltered.value;
    }
};

const togglePending = (name) => {
    if (!name) return;
    const t = name.trim();
    const idx = pendingNames.value.indexOf(t);
    if (idx === -1) {
        pendingNames.value = [...pendingNames.value, t];
    } else {
        pendingNames.value = pendingNames.value.filter(n => n !== t);
    }
};

const confirmSelection = () => {
    if (pendingNames.value.length === 0) return;
    if (!selectionFiltered.value) {
        selectionFiltered.value = true;
        return;
    }
    const ordered = users.value
        .map(u => u.name.trim())
        .filter(n => pendingNames.value.includes(n));
    selectedNames.value = ordered;
    seedNames.value = [];
    groups.value = [];
    guardianResults.value = [];
    currentStep.value = 2; // Move to Guardians
};

const availableSeeds = computed(() => {
    if (!users.value || !selectedNames.value) return [];
    const filtered = selectedNames.value.filter(n => !seedNames.value.includes(n));
    return filtered.sort((a, b) => {
        const idxA = users.value.findIndex(u => u && u.name === a);
        const idxB = users.value.findIndex(u => u && u.name === b);
        return idxA - idxB;
    });
});

const availableGuardians = computed(() => {
    if (!users.value || !selectedNames.value) return [];
    const list = selectedNames.value.filter(n => !guardianResults.value.includes(n));
    return list.sort((a, b) => {
        const idxA = users.value.findIndex(u => u && u.name === a);
        const idxB = users.value.findIndex(u => u && u.name === b);
        return idxA - idxB;
    });
});

const filteredGDropdown = computed(() => {
    if (!guardianQuery.value) return availableGuardians.value;
    return availableGuardians.value.filter(n => 
        n.toLowerCase().includes(guardianQuery.value.toLowerCase())
    );
});

const addManualGuardian = (name) => {
    if (name && !guardianResults.value.includes(name)) {
        guardianResults.value.push(name);
    }
    guardianQuery.value = '';
    isGDropdownOpen.value = false;
};

const addGuardianFromSelect = (event) => {
    const name = event.target.value;
    if (name && !guardianResults.value.includes(name)) {
        guardianResults.value.push(name);
    }
    event.target.value = '';
};

const removeGuardian = (name) => {
    guardianResults.value = guardianResults.value.filter(n => n !== name);
};

const loadUsers = async () => {
    try {
        const res = await axios.get('/api/dharma-names-list');
        let rawUsers = res.data;

        let processed = [...rawUsers].filter(u => u.name !== '紫真');
        const qingIdx = processed.findIndex(u => u.name === '靈情');
        if (qingIdx > -1) {
            const lingQi = rawUsers.find(u => u.name === '靈奇');
            const lingQin = rawUsers.find(u => u.name === '靈傾');
            processed = processed.filter(u => u.name !== '靈奇' && u.name !== '靈傾');
            const newQingIdx = processed.findIndex(u => u.name === '靈情');
            if (lingQi) processed.splice(newQingIdx + 1, 0, lingQi);
            if (lingQin) processed.splice(newQingIdx + 2, 0, lingQin);
        }
        users.value = processed;

        pendingNames.value = [];
        selectedNames.value = [];

        selectionFiltered.value = false;
        currentStep.value = 1;
    } catch (e) { console.error('Failed to load users', e); }
};

const toggleSelect = (name) => {
    if (!name) return;
    const trimmedName = name.trim();
    const idx = selectedNames.value.indexOf(trimmedName);
    if (idx === -1) {
        selectedNames.value = [...selectedNames.value, trimmedName];
    } else {
        selectedNames.value = selectedNames.value.filter(n => n !== trimmedName);
        removeSeed(trimmedName);
        removeGuardian(trimmedName);
        groups.value = []; 
    }
};

const addSeedFromSelect = (event) => {
    const name = event.target.value;
    if (name && !seedNames.value.includes(name)) {
        seedNames.value.push(name);
    }
    event.target.value = '';
};

const removeSeed = (name) => {
    seedNames.value = seedNames.value.filter(n => n !== name);
};

const guardianSeedsCount = computed(() => {
    return guardianResults.value.filter(g => seedNames.value.includes(g)).length;
});

const nonSeedGuardiansCount = computed(() => {
    return guardianResults.value.filter(g => !seedNames.value.includes(g)).length;
});

const totalExclusionCount = computed(() => {
    const combined = new Set(seedNames.value);
    if (!includeGuardians.value) {
        guardianResults.value.forEach(g => combined.add(g));
    }
    let count = 0;
    combined.forEach(name => {
        if (selectedNames.value.includes(name)) count++;
    });
    return count;
});

const activePoolCount = computed(() => {
    return selectedNames.value.length - totalExclusionCount.value;
});

const distributePlayers = (players, size) => {
    const exclusionSet = new Set(seedNames.value);
    if (!includeGuardians.value) {
        guardianResults.value.forEach(g => exclusionSet.add(g));
    }

    let candidates = players.filter(p => !exclusionSet.has(p));

    for (let i = candidates.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [candidates[i], candidates[j]] = [candidates[j], candidates[i]];
    }

    const result = [];
    let groupNum = 1;

    const seedsInPlay = seedNames.value.filter(s => players.includes(s));
    if (seedsInPlay.length > 0) {
        result.push({ name: `第 ${groupNum} 組 (種子組)`, members: seedsInPlay, isSeed: true });
        groupNum++;
    }

    const safeSize = Math.max(1, parseInt(size) || 1);

    while (candidates.length > 0) {
        result.push({ 
            name: `第 ${groupNum} 組`, 
            members: candidates.splice(0, safeSize), 
            isSeed: false 
        });
        groupNum++;
    }

    return result;
};

const cupStickColors = ['#d97706'];

const stickBodyColors = ['#d97706'];
const stickTipColors  = ['#dc2626'];

const flyingSticks = ref([]);

const buildFlyingSticks = (names) => {
    flyingSticks.value = names.slice(0, 12).map((name, i) => ({
        id: i,
        name: name,
        x: 20 + (i * 5.5) % 60,
        dur: 1.8 + (i * 0.1) % 1.2,
        delay: (i * 0.25) % 2.5,
        rotate: -40 + (i * 17) % 80,
        drift: -60 + (i * 23) % 120,
        color: stickBodyColors[i % stickBodyColors.length],
        tipColor: stickTipColors[i % stickTipColors.length],
    }));
};

const lotteryDisplayNames = ref([]);

let lotteryInterval = null;

const doGrouping = () => {
    if (selectedNames.value.length < 1) return;

    isDrawing.value = true;
    currentType.value = 'group';
    groups.value = [];

    buildFlyingSticks([...selectedNames.value]);

    const pool = [...selectedNames.value];
    let idx = 0;
    lotteryDisplayNames.value = [pool[0]];
    lotteryInterval = setInterval(() => {
        idx = (idx + 1) % pool.length;
        lotteryDisplayNames.value = [pool[idx]];
    }, 80);

    setTimeout(() => {
        clearInterval(lotteryInterval);
        lotteryInterval = null;
        groups.value = distributePlayers([...selectedNames.value], groupSize.value);
        isDrawing.value = false;
        lotteryDisplayNames.value = [];
        currentStep.value = 5; // Move to Results
    }, 3000);
};

const pickGuardians = () => {
    // Check available pool (excluding current guardians and seeds)
    const available = selectedNames.value.filter(n => !guardianResults.value.includes(n));
    if (available.length < pickSize.value) return;

    isDrawing.value = true;
    currentType.value = 'guardian';
    setTimeout(() => {
        const tempPool = [...available];
        for (let i = 0; i < pickSize.value; i++) {
            const idx = Math.floor(Math.random() * tempPool.length);
            const name = tempPool.splice(idx, 1)[0];
            guardianResults.value.push(name);
        }
        isDrawing.value = false;
    }, 3000);
};

const clearGuardians = () => {
    persistentToast.value = { msg: '確定要清空關主名單嗎？', type: 'clear' };
};

const redrawAll = () => {
    persistentToast.value = { msg: '確定要將所有關主回歸名單並重新抽選嗎？', type: 'redraw' };
};

const executeToastAction = () => {
    if (!persistentToast.value) return;
    const type = persistentToast.value.type;

    if (type === 'clear') {
        guardianResults.value = [];
        persistentToast.value = { msg: '✓ 已清空關主名單', type: 'success' };
    } else if (type === 'redraw') {
        selectedNames.value = [...selectedNames.value, ...guardianResults.value];
        guardianResults.value = [];
        groups.value = [];
        isDrawing.value = false;
        currentType.value = '';
        currentStep.value = 2;
        persistentToast.value = { msg: '✓ 已將所有人員歸還', type: 'success' };
    }

    setTimeout(() => { if (persistentToast.value?.type === 'success') persistentToast.value = null; }, 1500);
};

const copyResult = () => {
    if (groups.value.length === 0 && guardianResults.value.length === 0) return;
    let text = `🎲 隨機分組結果\n`;
    if (guardianResults.value.length > 0) {
        text += `\n【🌟 關主名單】\n`;
        text += guardianResults.value.join('、') + '\n';
    }
    groups.value.forEach(g => {
        text += `\n【${g.name}】\n`;
        text += g.members.join('、') + '\n';
    });
    navigator.clipboard.writeText(text).then(() => { 
        persistentToast.value = { msg: '✓ 已複製名單（含關主）！', type: 'success' };
        setTimeout(() => { if (persistentToast.value?.type === 'success') persistentToast.value = null; }, 1500);
    });
};

const invertSelection = () => {
    const allNames = users.value.map(u => u.name.trim());
    if (currentStep.value === 1) {
        // Invert pending selection → show inverted as red hint
        pendingNames.value = allNames.filter(n => !pendingNames.value.includes(n));
    } else {
        selectedNames.value = allNames.filter(n => !selectedNames.value.includes(n));
        seedNames.value = seedNames.value.filter(n => selectedNames.value.includes(n));
        guardianResults.value = guardianResults.value.filter(n => selectedNames.value.includes(n));
        groups.value = [];
    }
};

const resetAll = () => {
    pendingNames.value = [];
    selectionFiltered.value = false;
    selectedNames.value = [];
    seedNames.value = [];
    groups.value = [];
    guardianResults.value = [];
    isDrawing.value = false;
    currentType.value = '';
    currentStep.value = 1;
    includeGuardians.value = false;
};

watch(persistentToast, (newVal) => {
    if (newVal) lockBodyScroll();
    else unlockBodyScroll();
});

onMounted(loadUsers);

defineExpose({
    invertSelection,
    resetAll,
    selectAll,
    toggleSelectionFilter,
    selectionFiltered
});
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
.dot { border-radius: 50%; animation: bounce 0.6s infinite alternate; }
.dot:nth-child(2) { animation-delay: 0.2s; }
.dot:nth-child(3) { animation-delay: 0.4s; }
.dot-lg { width: 10px; height: 10px; border-radius: 50%; display: inline-block; animation: bounce 0.5s infinite alternate; }
.dot-lg:nth-child(2) { animation-delay: 0.15s; }
.dot-lg:nth-child(3) { animation-delay: 0.3s; }
@keyframes bounce { 
    from { transform: translateY(0); opacity: 0.4; }
    to   { transform: translateY(-8px); opacity: 1; }
}
.animate-slide-in { animation: slideIn 0.4s ease-out; }
@keyframes slideIn { 
    from { opacity: 0; transform: translateX(30px); }
    to { opacity: 1; transform: translateX(0); }
}
.fade-enter-active, .fade-leave-active { transition: opacity 0.3s, transform 0.3s; }
.fade-enter-from, .fade-leave-to { opacity: 0; transform: translate(-50%, -20px); }

/* ========== LOTTERY STICK ANIMATION ========== */

/* Flying stick */
.lottery-stick {
    position: absolute;
    bottom: 192px; /* Start relative to cup top */
    width: 16px;
    display: flex;
    flex-direction: column-reverse;
    align-items: center;
    transform-origin: bottom center;
    animation: stickFly var(--dur, 1.4s) cubic-bezier(0.34, 1.56, 0.64, 1) var(--delay, 0s) infinite;
    animation-duration: inherit;
    animation-delay: inherit;
}
@keyframes stickFly {
    0%   { transform: translateY(64px) scale(0.6); opacity: 0; }
    5%   { opacity: 1; transform: translateY(32px) scale(0.8); }
    15%  { transform: translateY(-96px) scale(1.1); opacity: 1; }
    60%  { transform: translateY(-60vh) translateX(var(--drift)) rotate(var(--rotate)) scale(1); opacity: 1; }
    100% { transform: translateY(-90vh) translateX(var(--drift)) rotate(var(--rotate)) scale(0.9); opacity: 0; }
}
.stick-body {
    width: 16px;
    height: 96px;
    border-radius: 8px 8px 3px 3px;
    display: flex;
    align-items: center;
    justify-content: center;
    writing-mode: vertical-rl;
    box-shadow: 0 3px 16px rgba(0,0,0,0.5), inset 0 1px 0 rgba(255,255,255,0.2);
}
.stick-name {
    font-size: 11px;
    font-weight: 900;
    color: #1e1b4b;
    letter-spacing: 1px;
    text-shadow: none;
}
.stick-tip {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    margin-top: -3px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.4);
}

/* Bamboo cup */
.lottery-cup-container {
    position: relative;
    width: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
}
.lottery-cup {
    position: relative;
    width: 160px;
    animation: cupShake 0.15s ease-in-out infinite alternate;
    transform-origin: bottom center;
}
@keyframes cupShake {
    from { transform: rotate(-6deg) translateX(-6px); }
    to   { transform: rotate(6deg) translateX(6px); }
}
.cup-rim {
    width: 160px;
    height: 10px;
    background: #92400e;
    border-radius: 3px 3px 0 0;
    box-shadow: 0 3px 6px rgba(0,0,0,0.3);
    border-bottom: 2px solid #78350f;
}
.cup-sticks-wrapper {
    position: absolute;
    top: -96px;
    left: 50%;
    transform: translateX(-50%);
    width: 128px;
    height: 96px;
    display: flex;
    align-items: flex-end;
    justify-content: center;
    gap: 3px;
}
.cup-stick {
    width: 11px;
    height: 112px;
    border-radius: 3px 3px 1px 1px;
    transform: rotate(var(--ctilt));
    transform-origin: bottom center;
    animation: cupStickJiggle 0.15s ease-in-out infinite alternate;
    animation-delay: calc(var(--ci) * 15ms);
}
@keyframes cupStickJiggle {
    from { transform: rotate(calc(var(--ctilt) - 2deg)) translateY(0px); }
    to   { transform: rotate(calc(var(--ctilt) + 2deg)) translateY(-4px); }
}
.cup-body {
    width: 160px;
    height: 192px;
    background: linear-gradient(90deg, 
        #92400e 0%, 
        #b45309 15%, 
        #92400e 30%, 
        #78350f 50%, 
        #92400e 70%, 
        #b45309 85%, 
        #78350f 100%
    );
    border-radius: 3px 3px 6px 6px;
    box-shadow: inset 3px 0 8px rgba(255,255,255,0.05), 0 10px 32px rgba(0,0,0,0.4);
    position: relative;
    overflow: hidden;
}
/* Bamboo vertical lines */
.cup-body::after {
    content: "";
    position: absolute;
    inset: 0;
    background-image: repeating-linear-gradient(90deg, transparent, transparent 14px, rgba(0,0,0,0.15) 16px);
}
.cup-base {
    width: 176px;
    height: 10px;
    background: #451a03;
    border-radius: 2px;
    box-shadow: 0 3px 10px rgba(0,0,0,0.3);
    position: relative;
    z-index: 2;
}

/* Name display above cup */
.flying-name-display {
    margin-top: 24px;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.flying-name-text {
    font-size: 36px;
    font-weight: 900;
    color: #92400e;
    letter-spacing: 6px;
    text-shadow: 0 2px 10px rgba(217,119,6,0.2);
    animation: namePulse 0.1s ease-in-out;
}
@keyframes namePulse {
    from { opacity: 0.2; transform: scale(0.9); }
    to   { opacity: 1;   transform: scale(1); }
}

.custom-scrollbar {
    -webkit-overflow-scrolling: touch;
    overscroll-behavior-y: contain;
}
.custom-scrollbar::-webkit-scrollbar {
    width: 4px;
    height: 4px;
}
@media (min-width: 768px) {
    .custom-scrollbar::-webkit-scrollbar {
        width: 10px;
    }
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #e2e8f0;
    border-radius: 10px;
}
@media (min-width: 768px) {
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border: 2px solid transparent;
        background-clip: padding-box;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
}
* { -webkit-tap-highlight-color: transparent; }
.animate-slide-up { animation: slideUp 0.25s cubic-bezier(0.16, 1, 0.3, 1); }
@keyframes slideUp { from { transform: translateY(100%); } to { transform: translateY(0); } }
</style>
