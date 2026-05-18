<template>
    <teleport to="body">
    <div v-if="show" class="fixed inset-0 z-[3500] flex items-end md:items-center justify-center">
        <!-- Backdrop (Critical for modal depth & click outside) -->
        <div @click="$emit('close')" class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm transition-all duration-300"></div>

        <!-- Form Container -->
        <div class="relative w-full h-full md:h-auto md:max-h-[95dvh] md:max-w-4xl bg-white md:rounded-[32px] md:shadow-2xl flex flex-col overflow-hidden animate-slide-up">

        <!-- MODE 1: ROUND DRAW (lotteryMode === true) -->
        <template v-if="lotteryMode === true">
            <!-- STEP 1 & 2: PERSONNEL SELECTION -->
            <div v-if="currentStep === 1 || currentStep === 2" class="flex-1 flex flex-col bg-white overflow-hidden z-[150] min-h-[600px] md:min-h-0">
                <div class="animate-fade-in flex flex-col h-full overflow-hidden relative min-h-0">
                    <!-- Header -->
                    <div class="border-b border-slate-300 bg-white sticky top-0 z-[110] w-full md:pt-[60px]" style="padding: 8px 2px; min-height: 52px;">
                        <div class="flex flex-col w-full gap-1">
                            <div class="flex flex-wrap items-center flex-1 min-w-0 gap-x-2">
                                <button v-if="selectionFiltered || currentStep === 2" @click="handleSelectionBack" class="p-2 -ml-2 text-slate-400 active:scale-90 transition-all shrink-0">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </button>
                                <button v-else @click="handleBack" class="p-2 -ml-2 text-slate-400 active:scale-90 transition-all shrink-0">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </button>
                                <logo-imperial-notebook :height="40" />
                                <div class="app-title leading-tight font-outfit tracking-widest shrink-0" style="color: #0f172a !important; font-size: 30px !important;">抽籤專區</div>
                                <span class="text-slate-500" style="font-size: 23px !important; font-weight: 400 !important;">回合抽籤</span>

                            </div>
                        </div>
                    </div>

                    <!-- Main Scrollable Content -->
                    <div ref="scrollContainer" class="flex-1 overflow-y-auto custom-scrollbar pb-[500px] w-full">
                        <!-- Sub control panel -->
                        <div class="flex items-center justify-between px-4 py-2 border-b border-slate-100 bg-slate-50/50">
                            <div class="flex items-center">
                                <span class="text-[14px] text-red-600 font-black" style="color: #dc2626 !important;">
                                    {{ selectionFiltered ? '2. 已確認在場人員' : '1. 選取人員' }}
                                </span>
                            </div>
                            <div class="flex items-center gap-3">
                                <button v-if="!selectionFiltered" @click="invertSelection" class="text-indigo-600 font-black text-[14px] active:scale-95 transition-all border-none bg-transparent cursor-pointer">反選</button>
                                <button @click="resetAll" class="text-slate-400 hover:text-red-500 active:scale-90 transition-all border-none p-1 flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    重置
                                </button>
                            </div>
                        </div>

                        <!-- Title (Only when selectionFiltered is true) -->
                        <div v-if="selectionFiltered" class="px-4 pt-4 pb-2 flex items-center justify-between animate-fade-in shrink-0">
                            <span class="text-[17px] font-black text-indigo-700">在場人員名單</span>
                            <span class="text-[14px] font-bold text-slate-500">共 {{ pendingNames.length }} 人</span>
                        </div>

                        <!-- Grid -->
                        <div class="grid grid-cols-4 md:grid-cols-5 gap-2 px-4" @mouseleave="stopDrag" @touchmove="handleTouchMove">
                            <button v-for="user in displayUsers" :key="user.id"
                                @mousedown="startDrag(user.name)"
                                @mouseenter="onDragEnter(user.name)"
                                @mouseup="stopDrag"
                                @dragstart.prevent
                                @selectstart.prevent
                                @touchstart="handleTouchStart($event, user.name)"
                                @touchend="stopDrag"
                                :data-name="user.name"
                                class="dharma-btn flex items-center justify-center font-normal transition-all active:scale-95 rounded-md border shadow-sm w-full min-h-[45px]"
                                :style="{ 
                                    ...getPendingStyle(user.name),
                                    fontSize: '16px !important'
                                }">
                                <span class="pointer-events-none flex items-center relative">
                                    <span v-if="fixedParticipants.includes(user.name)" class="mr-1 text-white">✓</span>
                                    {{ user.name }}
                                    <span v-if="!selectionFiltered && pendingNames.includes(user.name) && !fixedParticipants.includes(user.name)" 
                                        class="absolute -top-3.5 -right-3 w-4 h-4 rounded-full bg-rose-500 text-white text-[10px] flex items-center justify-center border border-white shadow-sm font-black">
                                        {{ pendingNames.indexOf(user.name) + 1 }}
                                    </span>
                                </span>
                            </button>
                        </div>
                    </div>

                    <!-- Bottom Button Area -->
                    <div class="fixed md:absolute left-0 right-0 px-4 py-3 bg-white border-t border-slate-100 z-[200] w-full bottom-[calc(7dvh+env(safe-area-inset-bottom))] md:bottom-0">
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
                            <span v-else style="color: #ffffff !important;">確定 → 進入抽籤設定</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- STEP 3: DRAW CONFIGURATION -->
            <div v-if="currentStep === 3" class="flex-1 flex flex-col bg-white overflow-hidden z-[150] min-h-[600px] md:min-h-0">
                <div class="animate-slide-in flex flex-col h-full overflow-hidden min-h-0">
                    <div class="border-b border-slate-300 bg-white sticky top-0 z-[110] w-full md:pt-[60px]" style="padding: 8px 2px; min-height: 52px;">
                        <div class="flex flex-col w-full gap-1">
                            <div class="flex flex-wrap items-center flex-1 min-w-0 gap-x-2">
                                <button @click="handleBack" class="p-2 -ml-2 text-slate-400 active:scale-90 transition-all shrink-0">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </button>
                                <logo-imperial-notebook :height="40" />
                                <div class="app-title leading-tight font-outfit tracking-widest shrink-0" style="color: #0f172a !important; font-size: 30px !important;">抽籤專區</div>
                                <span class="text-slate-500" style="font-size: 23px !important; font-weight: 400 !important;">回合抽籤</span>
                                <span class="text-slate-500" style="font-size: 23px !important; font-weight: 400 !important;"> - 點選本輪人員</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex-1 overflow-y-auto p-4 space-y-8">
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <span class="text-[17px] font-black text-slate-800">1. 從基礎名單點選本輪人員</span>
                                <span class="text-[17px] font-black text-slate-800">{{ roundParticipants.length }} 人</span>
                            </div>
                             <div class="flex gap-2">
                                <button @click="roundParticipants = [...selectedNames]" 
                                    :class="[
                                        'px-4 py-[10px] rounded-lg font-black text-[15px] border transition-all active:scale-95 whitespace-nowrap cursor-pointer',
                                        isAllSelected ? 'bg-indigo-600 border-indigo-600 text-white' : 'bg-white border-slate-300 text-slate-600'
                                    ]"
                                    :style="{ color: isAllSelected ? '#ffffff !important' : '#475569 !important' }">
                                    全選
                                </button>
                                <button @click="roundParticipants = []" 
                                    :class="[
                                        'px-4 py-[10px] rounded-lg font-black text-[15px] border transition-all active:scale-95 whitespace-nowrap cursor-pointer',
                                        isNoneSelected ? 'bg-indigo-600 border-indigo-600 text-white' : 'bg-white border-slate-300 text-slate-600'
                                    ]"
                                    :style="{ color: isNoneSelected ? '#ffffff !important' : '#475569 !important' }">
                                    清空
                                </button>
                            </div>
                            <div class="grid grid-cols-4 md:grid-cols-5 gap-2 pb-32">
                                <button v-for="name in selectedNames" :key="'round'+name" @click="toggleRoundParticipant(name)" 
                                    class="dharma-btn flex items-center justify-center font-normal transition-all active:scale-95 rounded-md border shadow-sm w-full min-h-[45px]"
                                    :style="{
                                        ...getStep2RoundStyle(name),
                                        fontSize: '16px !important'
                                    }">
                                    {{ name }}
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="fixed md:absolute left-0 right-0 px-4 py-3 bg-white border-t border-slate-100 space-y-4 z-[200] w-full bottom-[calc(7dvh+env(safe-area-inset-bottom))] md:bottom-0">
                        <div class="flex items-center justify-between">
                            <span class="text-[19px] font-black text-slate-800">抽取人數</span>
                            <div class="flex items-center bg-slate-100 rounded-xl overflow-hidden border border-slate-200">
                                <button @click="drawCount = Math.max(1, drawCount - 1)" class="w-12 h-[44px] flex items-center justify-center bg-slate-200 text-slate-600 font-black" style="font-size: 24px !important;">-</button>
                                <input v-model.number="drawCount" class="w-16 h-[44px] text-center font-black bg-transparent outline-none" style="font-size: 24px !important;">
                                <button @click="drawCount = Math.min(selectedNames.length, drawCount + 1)" 
                                        class="w-12 h-[44px] flex items-center justify-center bg-slate-500 text-white font-black" style="font-size: 24px !important;">+</button>
                            </div>
                        </div>
                        <button @click="performRoundDraw()" 
                                class="w-full py-[10px] rounded-3xl font-black text-[19px] shadow-lg active:scale-95 transition-all text-white bg-indigo-600" style="background: rgb(16, 185, 129);">
                            開始本輪抽籤
                        </button>
                    </div>
                </div>
            </div>

            <!-- STEP 4: RESULTS -->
            <div v-if="currentStep === 4" class="flex-1 flex flex-col bg-white overflow-hidden z-[1000] min-h-[600px] md:min-h-0">
                <div class="animate-fade-in flex flex-col h-full overflow-hidden min-h-0">
                    <div class="border-b border-slate-300 bg-white sticky top-0 z-[110] w-full md:pt-[60px]" style="padding: 8px 2px; min-height: 52px;">
                        <div class="flex flex-col w-full gap-1">
                            <div class="flex flex-wrap items-center flex-1 min-w-0 gap-x-2">
                                <button @click="handleBack" class="p-2 -ml-2 text-slate-400 active:scale-90 transition-all shrink-0">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </button>
                                <logo-imperial-notebook :height="40" />
                                <div class="app-title leading-tight font-outfit tracking-widest shrink-0" style="color: #0f172a !important; font-size: 30px !important;">抽籤專區</div>
                                <span class="text-slate-500" style="font-size: 23px !important; font-weight: 400 !important;">回合抽籤</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex-1 overflow-y-auto custom-scrollbar p-4 pb-48">
                        <div v-if="results.length === 1" class="flex flex-col items-center py-6">
                            <span class="text-[70px] -mb-2 relative z-10">👑</span>
                            <div class="bg-yellow-400 text-red-600 text-[76px] font-black px-12 py-6 rounded-3xl shadow-xl border-4 border-yellow-500 animate-bounce text-center leading-none" style="font-family: 'DFKai-SB', '標楷體', serif;">
                                {{ results[0] }}
                            </div>
                        </div>
                        <div v-else class="w-full flex justify-center items-center py-6">
                            <div class="flex flex-wrap flex-row justify-center items-center gap-x-8 gap-y-6 px-4 w-full mx-auto">
                                <div v-for="(name, idx) in results" :key="idx" 
                                    class="flex flex-col items-center animate-slide-in"
                                    :style="{ animationDelay: (idx * 0.05) + 's' }"
                                >
                                    <span class="text-[13px] font-black text-slate-400 mb-1.5 bg-slate-50 border border-slate-100 px-1.5 py-0.5 rounded-md min-w-[22px] text-center leading-none">
                                        {{ idx + 1 }}
                                    </span>
                                    <span class="font-black tracking-widest select-none leading-tight font-biaokai-locked"
                                        :style="{ 
                                            writingMode: 'vertical-rl',
                                            fontSize: getDynamicFontSize(results.length),
                                            color: '#dc2626',
                                            textShadow: '0 2px 8px rgba(220, 38, 38, 0.15)',
                                            fontFamily: '\'DFKai-SB\', \'標楷體\', serif'
                                        }"
                                    >
                                        {{ name }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Bottom Button Area (Fixed above navigation) -->
                    <div class="fixed md:absolute left-0 right-0 px-4 py-3 bg-white border-t border-slate-50 z-[200] w-full bottom-[calc(7dvh+env(safe-area-inset-bottom))] md:bottom-0">
                        <div class="flex gap-2">
                            <button @click="handleNextRound" class="flex-1 py-[10px] bg-emerald-600 text-white rounded-xl font-black text-[15px]" style="color: white !important;">新回合</button>
                            <button @click="results = []; currentStep = 3" class="flex-1 py-[10px] bg-slate-500 text-white rounded-xl font-black text-[15px]" style="color: white !important;">重選</button>
                            <button @click="copyResults" class="flex-1 py-[10px] bg-blue-600 text-white rounded-xl font-black text-[15px]" style="color: white !important;">複製</button>
                            <button @click="saveResults" class="flex-1 py-[10px] bg-indigo-600 text-white rounded-xl font-black text-[15px]" style="color: white !important;">儲存</button>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        <!-- MODE 2: DRAW ORDER (lotteryMode === false) -->
        <template v-else>
            <!-- STEP 1: PERSONNEL SELECTION (FIXED) -->
            <div v-if="currentStep === 1" class="flex-1 flex flex-col bg-white overflow-hidden z-[150] min-h-[600px] md:min-h-0">
                <div class="animate-fade-in flex flex-col h-full overflow-hidden relative min-h-0">
                    <!-- Header -->
                    <div class="border-b border-slate-300 bg-white sticky top-0 z-[110] w-full md:pt-[60px]" style="padding: 8px 2px; min-height: 52px;">
                        <div class="flex flex-col w-full gap-1">
                            <div class="flex flex-wrap items-center flex-1 min-w-0 gap-x-2">
                                <button @click="handleBack" class="p-2 -ml-2 text-slate-400 active:scale-90 transition-all shrink-0">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </button>
                                <logo-imperial-notebook :height="40" />
                                <div class="app-title leading-tight font-outfit tracking-widest shrink-0" style="color: #0f172a !important; font-size: 30px !important;">抽籤專區</div>
                                <span class="text-slate-500" style="font-size: 23px !important; font-weight: 400 !important;">抽順序</span>

                            </div>
                        </div>
                    </div>

                    <!-- Main Scrollable Content -->
                    <div ref="scrollContainer" class="flex-1 overflow-y-auto custom-scrollbar pb-[300px] w-full">
                        <!-- Sub control panel -->
                        <div class="flex items-center justify-between px-4 py-2 border-b border-slate-100 bg-slate-50/50">
                            <div class="flex items-center gap-2">
                                <span class="text-[14px] text-red-600 font-black" style="color: #dc2626 !important;">1. 選取固定人員</span>
                                <span class="text-[13px] text-slate-400 font-bold ml-2">滑動選取</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <button @click="resetAll(true)" class="text-slate-400 hover:text-red-500 active:scale-90 transition-all border-none p-1 flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    重置
                                </button>
                            </div>
                        </div>

                        <!-- Grid -->
                        <div class="grid grid-cols-4 md:grid-cols-5 gap-2 px-4 mt-2" @mouseleave="stopDrag" @touchmove="handleTouchMove">
                            <button v-for="user in displayUsers" :key="user.id"
                                @mousedown="startDrag(user.name)"
                                @mouseenter="onDragEnter(user.name)"
                                @mouseup="stopDrag"
                                @dragstart.prevent
                                @selectstart.prevent
                                @touchstart="handleTouchStart($event, user.name)"
                                @touchend="stopDrag"
                                :data-name="user.name"
                                class="dharma-btn flex items-center justify-center font-black transition-all active:scale-95 rounded-md border shadow-sm w-full min-h-[45px]"
                                :style="{ 
                                    ...getPendingStyle(user.name),
                                    fontSize: '17px !important'
                                }">
                                <span class="pointer-events-none flex items-center relative truncate leading-none">
                                    {{ user.name }}
                                </span>
                            </button>
                        </div>
                    </div>

                    <!-- Bottom Button Area -->
                    <div class="fixed md:absolute left-0 right-0 px-4 py-3 bg-white border-t border-slate-100 z-[200] w-full bottom-[calc(7dvh+env(safe-area-inset-bottom))] md:bottom-0">
                        <button
                            @click="confirmSelection"
                            class="w-full rounded-2xl font-black transition-all active:scale-[0.98] text-white bg-indigo-600 hover:bg-indigo-700 shadow-lg flex items-center justify-center cursor-pointer"
                            style="color: #ffffff !important; text-shadow: 0 1px 3px rgba(0,0,0,0.3); font-size: 17px !important; padding-top: 11px; padding-bottom: 11px;"
                        >
                            確定並進入下一步
                        </button>
                    </div>
                </div>
            </div>

            <!-- STEP 2: OTHER PERSONNEL SELECTION (RANDOM POOL) -->
            <div v-else-if="currentStep === 2" class="flex-1 flex flex-col bg-white overflow-hidden z-[150] min-h-[600px] md:min-h-0">
                <div class="animate-fade-in flex flex-col h-full overflow-hidden relative min-h-0">
                    <!-- Header -->
                    <div class="border-b border-slate-300 bg-white sticky top-0 z-[110] w-full md:pt-[60px]" style="padding: 8px 2px; min-height: 52px;">
                        <div class="flex flex-col w-full gap-1">
                            <div class="flex flex-wrap items-center flex-1 min-w-0 gap-x-2">
                                <button @click="currentStep = 1" class="p-2 -ml-2 text-slate-400 active:scale-90 transition-all shrink-0">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </button>
                                <logo-imperial-notebook :height="40" />
                                <div class="app-title leading-tight font-outfit tracking-widest shrink-0" style="color: #0f172a !important; font-size: 30px !important;">抽籤專區</div>
                                <span class="text-slate-500" style="font-size: 23px !important; font-weight: 400 !important;">抽順序</span>

                            </div>
                        </div>
                    </div>

                    <!-- Main Scrollable Content -->
                    <div ref="scrollContainer" class="flex-1 overflow-y-auto custom-scrollbar pb-[300px] w-full">
                        <!-- Sub control panel -->
                        <div class="flex items-center justify-between px-4 py-2 border-b border-slate-100 bg-slate-50/50">
                            <div class="flex items-center gap-2">
                                <span class="text-[14px] text-red-600 font-black" style="color: #dc2626 !important;">2. 選取其他抽籤人員</span>
                                <span class="text-[13px] text-slate-400 font-bold ml-2">滑動選取</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <button @click="pendingNames = []" class="text-slate-400 hover:text-red-500 active:scale-90 transition-all border-none p-1 flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    重置
                                </button>
                            </div>
                        </div>

                        <!-- Grid -->
                        <div class="grid grid-cols-4 md:grid-cols-5 gap-2 px-4 mt-2" @mouseleave="stopDrag" @touchmove="handleTouchMove">
                            <button v-for="user in displayUsers" :key="user.id"
                                @mousedown="startDrag(user.name)"
                                @mouseenter="onDragEnter(user.name)"
                                @mouseup="stopDrag"
                                @dragstart.prevent
                                @selectstart.prevent
                                @touchstart="handleTouchStart($event, user.name)"
                                @touchend="stopDrag"
                                :data-name="user.name"
                                class="dharma-btn flex items-center justify-center font-black transition-all active:scale-95 rounded-md border shadow-sm w-full min-h-[45px]"
                                :style="{ 
                                    ...getPendingStyle(user.name),
                                    fontSize: '17px !important'
                                }">
                                <span class="pointer-events-none flex items-center relative truncate leading-none">
                                    {{ user.name }}
                                </span>
                            </button>
                        </div>
                    </div>

                    <!-- Bottom Button Area -->
                    <div class="fixed md:absolute left-0 right-0 px-4 py-3 bg-white border-t border-slate-100 z-[200] w-full bottom-[calc(7dvh+env(safe-area-inset-bottom))] md:bottom-0">
                        <button
                            @click="confirmSelection"
                            :disabled="fixedParticipants.length + pendingNames.length === 0"
                            class="w-full rounded-2xl font-black transition-all active:scale-[0.98] text-white bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 disabled:bg-slate-300 shadow-lg flex items-center justify-center cursor-pointer"
                            style="color: #ffffff !important; text-shadow: 0 1px 3px rgba(0,0,0,0.3); font-size: 17px !important; padding-top: 11px; padding-bottom: 11px;"
                        >
                            下一步 (確定名單 共 {{ fixedParticipants.length + pendingNames.length }} 人)
                        </button>
                    </div>
                </div>
            </div>

            <!-- STEP 3: CONFIRM LIST -->
            <div v-else-if="currentStep === 3" class="flex-1 flex flex-col bg-white overflow-hidden z-[150] min-h-[600px] md:min-h-0">
                <div class="animate-fade-in flex flex-col h-full overflow-hidden relative min-h-0">
                    <!-- Header -->
                    <div class="border-b border-slate-300 bg-white sticky top-0 z-[110] w-full md:pt-[60px]" style="padding: 8px 2px; min-height: 52px;">
                        <div class="flex flex-col w-full gap-1">
                            <div class="flex flex-wrap items-center flex-1 min-w-0 gap-x-2">
                                <button @click="currentStep = 2" class="p-2 -ml-2 text-slate-400 active:scale-90 transition-all shrink-0">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </button>
                                <logo-imperial-notebook :height="40" />
                                <div class="app-title leading-tight font-outfit tracking-widest shrink-0" style="color: #0f172a !important; font-size: 30px !important;">抽籤專區</div>
                                <span class="text-slate-500" style="font-size: 23px !important; font-weight: 400 !important;">抽順序</span>
                                <span class="text-[14px] text-indigo-600 font-bold ml-2 mt-1">3. 確定名單</span>
                                <button @click="resetAll(true)" class="text-slate-400 hover:text-red-500 flex items-center justify-center active:scale-90 transition-all shrink-0 border-none p-1 ml-auto mr-8">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Main Scrollable Content -->
                    <div class="flex-1 overflow-y-auto custom-scrollbar pb-[300px] w-full px-4 pt-4 space-y-6">
                        <!-- Summary Card -->
                        <div class="bg-indigo-50/60 border border-indigo-100 rounded-3xl p-5 text-center shadow-sm">
                            <div class="text-[15px] font-black text-indigo-900 mb-1">本次排列總人數</div>
                            <div class="text-[44px] font-black text-indigo-600 font-mono leading-none py-1">
                                {{ fixedParticipants.length + pendingNames.length }} <span class="text-[20px] font-black text-indigo-400">人</span>
                            </div>
                            <div class="text-[12px] text-indigo-400 font-black mt-2">
                                (固定人員 {{ fixedParticipants.length }} 人 + 隨機人員 {{ pendingNames.length }} 人)
                            </div>
                        </div>

                        <!-- Fixed Participants list if any -->
                        <div v-if="fixedParticipants.length > 0" class="space-y-2">
                            <div class="flex items-center space-x-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span>
                                <span class="text-[16px] font-black text-slate-800">優先固定人員 (照順序排列)</span>
                            </div>
                            <div class="bg-rose-50/20 border border-rose-100/50 rounded-2xl p-4 grid grid-cols-4 gap-2">
                                <div v-for="(name, pidx) in fixedParticipants" :key="'conf-f-'+name" 
                                    class="bg-white border border-rose-100 py-2.5 px-1 rounded-xl text-[16px] font-black text-rose-900 shadow-sm flex flex-col items-center justify-center text-center leading-none"
                                >
                                    <span class="text-[11px] opacity-40 leading-none mb-0.5">{{ pidx + 1 }}</span>
                                    <span class="truncate w-full leading-none">{{ name }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Random Pool Participants list if any -->
                        <div v-if="pendingNames.length > 0" class="space-y-2">
                            <div class="flex items-center space-x-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                                <span class="text-[16px] font-black text-slate-800">隨機人員名單</span>
                            </div>
                            <div class="bg-emerald-50/20 border border-emerald-100/50 rounded-2xl p-4 grid grid-cols-4 gap-2">
                                <div v-for="name in pendingNames" :key="'conf-r-'+name" 
                                    class="bg-white border border-emerald-100 py-3.5 px-1 rounded-xl text-[16px] font-black text-emerald-900 shadow-sm flex items-center justify-center text-center leading-none truncate w-full"
                                >
                                    {{ name }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Bottom Button Area -->
                    <div class="fixed md:absolute left-0 right-0 px-4 py-3 bg-white border-t border-slate-100 z-[200] w-full bottom-[calc(7dvh+env(safe-area-inset-bottom))] md:bottom-0">
                        <button
                            @click="startMode2Draw"
                            class="w-full rounded-2xl font-black transition-all active:scale-[0.98] text-white bg-indigo-600 hover:bg-indigo-700 shadow-lg flex items-center justify-center cursor-pointer"
                            style="color: #ffffff !important; text-shadow: 0 1px 3px rgba(0,0,0,0.3); font-size: 19px !important; padding-top: 13px; padding-bottom: 13px;"
                        >
                            開始隨機排列
                        </button>
                    </div>
                </div>
            </div>

            <!-- STEP 4: RESULTS -->
            <div v-if="currentStep === 4" class="flex-1 flex flex-col bg-white overflow-hidden z-[1000] min-h-[600px] md:min-h-0">
                <div class="animate-fade-in flex flex-col h-full overflow-hidden min-h-0">
                    <div class="border-b border-slate-300 bg-white sticky top-0 z-[110] w-full md:pt-[60px]" style="padding: 8px 2px; min-height: 52px;">
                        <div class="flex flex-col w-full gap-1">
                            <div class="flex flex-wrap items-center flex-1 min-w-0 gap-x-2">
                                <button @click="handleBack" class="p-2 -ml-2 text-slate-400 active:scale-90 transition-all shrink-0">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </button>
                                <logo-imperial-notebook :height="40" />
                                <div class="app-title leading-tight font-outfit tracking-widest shrink-0" style="color: #0f172a !important; font-size: 30px !important;">抽籤專區</div>
                                <span class="text-slate-500" style="font-size: 23px !important; font-weight: 400 !important;">直接排列</span>
                                <span class="text-slate-900 font-black ml-auto mr-8" style="font-size: 19px !important;">抽籤結果 ({{ results.length }}人)</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex-1 overflow-y-auto custom-scrollbar p-4 pb-48">
                        <div class="max-w-lg mx-auto space-y-4 pt-2">
                            <!-- SINGLE RESULT: CROWN & CENTERED -->
                            <div v-if="results.length === 1" class="flex flex-col items-center justify-center space-y-4 animate-scale-in pt-4">
                                <div class="relative">
                                    <span class="text-[70px] filter drop-shadow-lg">👑</span>
                                    <div class="absolute -bottom-2 left-1/2 -translate-x-1/2 w-24 h-2 bg-indigo-500/10 blur-xl rounded-full"></div>
                                </div>
                                <h3 class="text-[76px] font-black px-12 py-6 rounded-3xl shadow-xl mb-4 text-center leading-none" style="background-color: #facc15 !important; color: #dc2626 !important; border: 4px solid #eab308 !important; font-family: 'DFKai-SB', '標楷體', serif;">{{ results[0] }}</h3>
                                <div class="flex items-center space-x-2 text-indigo-300">
                                    <div class="h-[2px] w-8 bg-indigo-100"></div>
                                    <span class="text-[15px] font-black uppercase tracking-widest">唯一幸運兒</span>
                                    <div class="h-[2px] w-8 bg-indigo-100"></div>
                                </div>
                            </div>

                            <!-- MULTIPLE RESULTS: CENTERED & INDEXED -->
                            <div v-else class="w-full flex justify-center items-center py-6">
                                <div class="flex flex-wrap flex-row justify-center items-center gap-x-8 gap-y-6 px-4 w-full mx-auto">
                                    <div v-for="(name, idx) in results" :key="'res'+idx" 
                                        class="flex flex-col items-center animate-slide-in"
                                        :style="{ animationDelay: (idx * 0.05) + 's' }"
                                    >
                                        <span class="text-[13px] font-black text-slate-400 mb-1.5 bg-slate-50 border border-slate-100 px-1.5 py-0.5 rounded-md min-w-[22px] text-center leading-none">
                                            {{ idx + 1 }}
                                        </span>
                                        <span class="font-black tracking-widest select-none leading-tight font-biaokai-locked"
                                            :style="{ 
                                                writingMode: 'vertical-rl',
                                                fontSize: getDynamicFontSize(results.length),
                                                color: '#dc2626',
                                                textShadow: '0 2px 8px rgba(220, 38, 38, 0.15)',
                                                fontFamily: '\'DFKai-SB\', \'標楷體\', serif'
                                            }"
                                        >
                                            {{ name }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Bottom Button Area -->
                    <div class="fixed md:absolute left-0 right-0 px-4 py-3 bg-white border-t border-slate-50 z-[200] w-full bottom-[calc(7dvh+env(safe-area-inset-bottom))] md:bottom-0">
                        <div class="flex gap-2">
                            <button @click="results = []; currentStep = 2" class="flex-1 py-[10px] bg-slate-500 text-white rounded-xl font-black text-[15px] cursor-pointer" style="color: white !important;">重選</button>
                            <button @click="copyResults" class="flex-1 py-[10px] bg-blue-600 text-white rounded-xl font-black text-[15px] cursor-pointer" style="color: white !important;">複製</button>
                            <button @click="saveResults" class="flex-1 py-[10px] bg-indigo-600 text-white rounded-xl font-black text-[15px] cursor-pointer" style="color: white !important;">儲存</button>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        <!-- FULLSCREEN LOTTERY ANIMATION -->
        <div v-if="isDrawing" class="fixed inset-0 z-[2000] flex flex-col items-center justify-center overflow-hidden" style="background: #fefce8;">
            <!-- Ambient glow -->
            <div class="absolute inset-0 pointer-events-none">
                <div style="position:absolute;top:30%;left:50%;transform:translate(-50%,-50%);width:min(400px,90vw);height:min(400px,90vw);background:radial-gradient(circle,rgba(251,191,36,0.15) 0%,transparent 70%);border-radius:50%;"></div>
            </div>

            <!-- Bamboo cup + Flying Sticks Container -->
            <div class="lottery-cup-container" style="transform: translateY(10dvh) scale(0.8);">
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
                        <div class="stick-body" style="background: #d97706;">
                            <span class="stick-name">{{ stick.name }}</span>
                        </div>
                        <div class="stick-tip" style="background: #dc2626;"></div>
                    </div>
                </div>

                <div class="lottery-cup">
                    <div class="cup-rim"></div>
                    <div class="cup-sticks-wrapper">
                        <div v-for="i in 9" :key="'cs'+i" class="cup-stick" :style="{ '--ci': i, '--ctilt': (i*23%11 - 5) + 'deg', background: '#d97706' }"></div>
                    </div>
                    <div class="cup-body"></div>
                    <div class="cup-base"></div>
                </div>
                <div class="flying-name-display">
                    <span v-if="lotteryDisplayNames.length" class="flying-name-text">{{ lotteryDisplayNames[0] }}</span>
                </div>
            </div>

            <!-- Status text (Added to match RandomGroup) -->
            <div class="absolute top-[3dvh] left-0 right-0 flex flex-col items-center space-y-2">
                <p class="text-[30px] font-black tracking-widest text-amber-900">隨機抽籤中</p>
                <div class="flex gap-2">
                    <span class="dot-lg w-3 h-3 rounded-full" style="background:#b45309;"></span>
                    <span class="dot-lg w-3 h-3 rounded-full" style="background:#d97706;"></span>
                    <span class="dot-lg w-3 h-3 rounded-full" style="background:#f59e0b;"></span>
                </div>
            </div>
        </div>

        <!-- Global Action Confirm / Toast (Critical for iOS) -->
        <div v-if="persistentToast" class="fixed inset-0 z-[9999] flex items-center justify-center p-6 bg-slate-900/40 backdrop-blur-sm animate-fade-in">
            <div class="bg-white w-full max-w-sm rounded-[32px] shadow-2xl overflow-hidden animate-slide-up border border-white/20">
                <div class="p-8 text-center space-y-6">
                    <div class="flex flex-col items-center">
                        <div v-if="persistentToast.type === 'clear'" class="w-16 h-16 bg-rose-50 text-rose-500 rounded-2xl flex items-center justify-center mb-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </div>
                        <div v-else-if="persistentToast.type === 'success'" class="w-16 h-16 bg-emerald-50 text-emerald-500 rounded-2xl flex items-center justify-center mb-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="3" stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <h3 class="text-[20px] font-black text-slate-900 leading-tight whitespace-pre-wrap">{{ persistentToast.msg }}</h3>
                    </div>

                    <div class="flex flex-col space-y-3">
                        <button v-if="persistentToast.type === 'clear'" 
                                @click="executeReset" 
                                class="w-full py-4 bg-rose-500 text-white rounded-2xl font-black text-[18px] active:scale-95 transition-all shadow-lg" 
                                style="color: white !important;">
                            確認清空
                        </button>
                        <button @click="persistentToast = null" 
                                :class="persistentToast.type === 'success' ? 'bg-indigo-600 text-white shadow-indigo-100' : 'bg-slate-100 text-slate-500'"
                                class="w-full py-4 rounded-2xl font-black text-[18px] active:scale-95 transition-all shadow-lg"
                                :style="{ color: (persistentToast.type === 'success' ? 'white !important' : 'inherit') }">
                            {{ persistentToast.type === 'success' ? '確認' : '取消' }}
                        </button>
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
import { ref, computed, onMounted, watch } from 'vue';
import axios from 'axios';
import MobileNavbar from './MobileNavbar.vue';
import { writeClipboard, lockBodyScroll, unlockBodyScroll, safeLocalStorage } from '../utils/iosCompat';

const props = defineProps({
    show: Boolean,
    initialMode: { type: Boolean, default: null },
    folderId: Number
});

const emit = defineEmits(['close', 'saved']);

const getDynamicFontSize = (count) => {
    if (count <= 1) return '76px';
    if (count <= 2) return '68px';
    if (count <= 4) return '54px';
    if (count <= 6) return '44px';
    if (count <= 8) return '38px';
    if (count <= 12) return '34px';
    if (count <= 16) return '28px';
    return '24px';
};

const users = ref([]);
const persistentToast = ref(null);
const pendingNames = ref([]);
const selectedNames = ref([]);
const fixedParticipants = ref([]);
const roundParticipants = ref([]);
const results = ref([]);
const currentStep = ref(1);
const selectionFiltered = ref(false);
const drawCount = ref(1);
const isDrawing = ref(false);
const isDragging = ref(false);
const hasResult = ref(false);
const lotteryMode = ref(props.initialMode);
const manualName = ref('');
const DRAW_DURATION = 3000;
const currentType = ref('');
const lotteryDisplayNames = ref([]);
const flyingSticks = ref([]);
const activeAction = ref('');
const selectionMode = ref('pool');

const DRAFT_KEY = 'lucky_draw_draft';

const loadUsers = async () => {
    try {
        const res = await axios.get('/api/dharma-names-list');
        users.value = res.data.filter(u => u && u.name && u.name !== '紫真');
    } catch (e) { console.error(e); }
};

const isAllSelected = computed(() => {
    return selectedNames.value.length > 0 && roundParticipants.value.length === selectedNames.value.length;
});

const isNoneSelected = computed(() => {
    return roundParticipants.value.length === 0;
});

const displayUsers = computed(() => {
    if (!users.value || !Array.isArray(users.value)) return [];
    if (selectionFiltered.value) {
        if (!lotteryMode.value && currentStep.value === 2) {
            return users.value.filter(u => u && u.name && selectedNames.value.includes(u.name.trim()) && !fixedParticipants.value.includes(u.name.trim()));
        }
        return users.value.filter(u => u && u.name && pendingNames.value.includes(u.name.trim()));
    }
    if (!lotteryMode.value && currentStep.value === 2) {
        return users.value.filter(u => u && u.name && !fixedParticipants.value.includes(u.name));
    }
    return users.value.filter(u => u && u.name);
});

const togglePending = (name) => {
    const t = name.trim();
    if (lotteryMode.value === true) {
        // Round Draw selection
        const idx = pendingNames.value.indexOf(t);
        if (idx > -1) {
            pendingNames.value.splice(idx, 1);
        } else {
            pendingNames.value.push(t);
        }
    } else {
        // Draw Order selection
        if (currentStep.value === 1) {
            const fidx = fixedParticipants.value.indexOf(t);
            if (fidx > -1) {
                fixedParticipants.value.splice(fidx, 1);
            } else {
                fixedParticipants.value.push(t);
            }
        } else if (currentStep.value === 2) {
            const idx = pendingNames.value.indexOf(t);
            if (idx > -1) {
                pendingNames.value.splice(idx, 1);
            } else {
                pendingNames.value.push(t);
            }
        }
    }
};

const getPendingStyle = (name) => {
    const t = name.trim();
    if (lotteryMode.value === true) {
        // Mode 1: Round Draw Style
        const isSelected = pendingNames.value.includes(t);
        if (isSelected) {
            if (selectionFiltered.value) {
                return {
                    backgroundColor: '#ffffff',
                    color: '#000000',
                    border: '1px solid #000000',
                    boxShadow: 'none',
                    zIndex: '5'
                };
            }
            return {
                backgroundColor: '#2563eb', // Blue for SELECTED
                color: '#ffffff',
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
    } else {
        // Mode 2: Draw Order Style
        const isFixed = fixedParticipants.value.includes(t);
        const isSelected = pendingNames.value.includes(t);
        if (isFixed) {
            return {
                backgroundColor: '#e11d48', // deep rose
                color: '#ffffff',
                borderColor: '#f43f5e',
                borderWidth: '2px',
                boxShadow: '0 4px 12px rgba(225, 29, 72, 0.3)',
                zIndex: '5'
            };
        } else if (isSelected) {
            return {
                backgroundColor: '#10b981', // emerald green
                color: '#ffffff',
                borderColor: '#34d399',
                borderWidth: '2px',
                boxShadow: '0 4px 12px rgba(16, 185, 129, 0.3)',
                zIndex: '5'
            };
        } else {
            return {
                backgroundColor: '#ffffff',
                color: '#1e293b',
                borderColor: '#cbd5e1',
                borderWidth: '1px',
                boxShadow: 'none'
            };
        }
    }
};

const confirmSelection = () => {
    if (lotteryMode.value === true) {
        if (selectionFiltered.value) {
            if (selectedNames.value.length === 0) return;
        } else {
            if (pendingNames.value.length === 0) return;
        }

        if (!selectionFiltered.value) {
            selectionFiltered.value = true;
            safeLocalStorage.setItem('shared_dharma_selection', JSON.stringify(pendingNames.value));
            return;
        }
        // Save/update the shared selection list
        safeLocalStorage.setItem('shared_dharma_selection', JSON.stringify(pendingNames.value));
        selectedNames.value = [...pendingNames.value];
        roundParticipants.value = [];
        drawCount.value = 1;
        currentStep.value = 3;
    } else {
        // Mode 2: Draw Order
        if (currentStep.value === 1) {
            currentStep.value = 2;
            pendingNames.value = []; // Clear pending names so they start Step 2 fresh
        } else if (currentStep.value === 2) {
            selectedNames.value = [...fixedParticipants.value, ...pendingNames.value];
            currentStep.value = 3;
        }
    }
};

const startMode2Draw = () => {
    drawCount.value = selectedNames.value.length;
    performDraw();
};

const handleBack = () => {
    if (currentStep.value > 1) currentStep.value--;
    else emit('close');
};

const handleSelectionBack = () => {
    if (selectionFiltered.value) {
        selectionFiltered.value = false;
    } else {
        handleBack();
    }
};

const resetAll = (silent = false) => {
    if (silent === true) {
        executeReset(true);
        return;
    }
    persistentToast.value = { msg: '確定要清空目前的所有選擇與記錄嗎？', type: 'clear' };
};

const executeReset = (silent = false) => {
    const isSilent = silent === true;
    pendingNames.value = [];
    fixedParticipants.value = [];
    selectedNames.value = [];
    roundParticipants.value = [];
    selectionFiltered.value = false;
    currentStep.value = 1;
    results.value = [];
    if (!isSilent) {
        safeLocalStorage.removeItem('shared_dharma_selection');
    }
    if (persistentToast.value) {
        persistentToast.value = { msg: '✓ 已清空', type: 'success' };
        setTimeout(() => { if (persistentToast.value?.type === 'success') persistentToast.value = null; }, 1500);
    }
};

const invertSelection = () => {
    const all = users.value.map(u => u.name);
    pendingNames.value = all.filter(n => !pendingNames.value.includes(n));
};

const toggleRoundParticipant = (name) => {
    const idx = roundParticipants.value.indexOf(name);
    if (idx > -1) roundParticipants.value.splice(idx, 1);
    else roundParticipants.value.push(name);
};

const getStep2RoundStyle = (name) => {
    const t = name.trim();
    const active = roundParticipants.value.includes(t);
    if (active) {
        return {
            backgroundColor: '#2563eb', // Professional Blue for SELECTED
            color: '#ffffff',
            borderColor: '#fbbf24',
            borderWidth: '3px',
            boxShadow: '0 0 12px rgba(251, 191, 36, 0.5)',
            zIndex: '5'
        };
    } else {
        return {
            backgroundColor: '#ffffff',
            color: '#000000',
            border: '1px solid #000000', // Confirmed but unselected for the round has a black border!
            boxShadow: 'none',
            zIndex: '5'
        };
    }
};

const toggleFixedParticipant = (name) => {
    const idx = fixedParticipants.value.indexOf(name);
    if (idx > -1) {
        fixedParticipants.value.splice(idx, 1);
    } else {
        fixedParticipants.value.push(name);
    }
};

let lotteryInterval = null;

const buildFlyingSticks = (names) => {
    // Show up to 20 sticks for a fuller animation
    flyingSticks.value = names.slice(0, 20).map((name, i) => ({
        id: i,
        name: name,
        x: 15 + (i * 7) % 70,
        dur: 1.4 + (i * 0.1) % 1.2,
        delay: (i * 0.15) % 2.5,
        rotate: -90 + (i * 25) % 180,
        drift: -180 + (i * 45) % 360
    }));
};

const performDraw = () => {
    if (selectedNames.value.length === 0) return;

    isDrawing.value = true;
    currentType.value = 'draw';
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

        const fixed = fixedParticipants.value.filter(n => pool.includes(n));
        const rest = pendingNames.value.filter(n => !fixed.includes(n)).sort(() => Math.random() - 0.5);
        const full = [...fixed, ...rest];
        
        if (selectionFiltered.value) {
            drawCount.value = fixed.length + rest.length;
        }
        results.value = full.slice(0, Math.min(drawCount.value, full.length));

        isDrawing.value = false;
        lotteryDisplayNames.value = [];
        currentStep.value = 4;
    }, DRAW_DURATION);
};

const performRoundDraw = () => {
    if (roundParticipants.value.length === 0) return;

    isDrawing.value = true;
    currentType.value = 'round';
    buildFlyingSticks([...roundParticipants.value]);

    const pool = [...roundParticipants.value];
    let idx = 0;
    lotteryDisplayNames.value = [pool[0]];
    lotteryInterval = setInterval(() => {
        idx = (idx + 1) % pool.length;
        lotteryDisplayNames.value = [pool[idx]];
    }, 80);

    setTimeout(() => {
        clearInterval(lotteryInterval);
        lotteryInterval = null;

        const shuffled = [...roundParticipants.value].sort(() => Math.random() - 0.5);
        results.value = shuffled.slice(0, Math.min(drawCount.value, shuffled.length));

        isDrawing.value = false;
        lotteryDisplayNames.value = [];
        currentStep.value = 4;
    }, DRAW_DURATION);
};

const performQuickDraw = () => {
    if (pendingNames.value.length === 0) return;
    selectedNames.value = [...pendingNames.value];
    drawCount.value = selectedNames.value.length;
    performDraw();
};

const copyResults = () => {
    const text = results.value.map((n, i) => `${i+1}. ${n}`).join('\n');
    writeClipboard(text).then(() => alert('已複製'));
};

const saving = ref(false);

const saveResults = async () => {
    if (!props.folderId || saving.value) return;
    saving.value = true;
    try {
        await axios.post(`/other-folders/${props.folderId}/records`, {
            title: `抽籤結果 - ${new Date().toLocaleString()}`,
            content: results.value.map((n, i) => `${i+1}. ${n}`).join('\n'),
            record_date: new Date().toISOString().split('T')[0]
        });
        alert('已儲存');
        emit('saved');
    } catch (e) { 
        console.error(e);
        alert('儲存失敗');
    } finally {
        saving.value = false;
    }
};

const handleNextRound = () => {
    pendingNames.value = pendingNames.value.filter(n => !results.value.includes(n));
    selectedNames.value = [...pendingNames.value];
    roundParticipants.value = [];
    currentStep.value = 3;
};

const addManualName = () => {
    if (!manualName.value.trim()) return;
    const n = manualName.value.trim();
    if (!users.value.some(u => u.name === n)) users.value.push({ id: Date.now(), name: n });
    togglePending(n);
    manualName.value = '';
};

const dragSelectionType = ref(null); // 'add' or 'remove'
const lastTouchedName = ref(null);

const scrollContainer = ref(null);
let scrollInterval = null;

const handleMouseMoveDrag = (e) => {
    if (!isDragging.value || !scrollContainer.value) return;

    const rect = scrollContainer.value.getBoundingClientRect();
    const mouseY = e.clientY;

    const distanceToBottom = rect.bottom - mouseY;
    const distanceToTop = mouseY - rect.top;

    clearInterval(scrollInterval);
    scrollInterval = null;

    if (distanceToBottom < 60 && distanceToBottom > -20) {
        const speed = Math.max(2, (60 - distanceToBottom) / 3);
        scrollInterval = setInterval(() => {
            if (scrollContainer.value) {
                scrollContainer.value.scrollTop += speed;
            }
        }, 16);
    } else if (distanceToTop < 60 && distanceToTop > -20) {
        const speed = Math.max(2, (60 - distanceToTop) / 3);
        scrollInterval = setInterval(() => {
            if (scrollContainer.value) {
                scrollContainer.value.scrollTop -= speed;
            }
        }, 16);
    }
};

const startDrag = (name) => {
    isDragging.value = true;
    let isSelected = false;
    if (!lotteryMode.value && selectionFiltered.value) {
        if (currentStep.value === 1) {
            isSelected = fixedParticipants.value.includes(name);
        } else if (currentStep.value === 2) {
            isSelected = pendingNames.value.includes(name);
        }
    } else {
        isSelected = pendingNames.value.includes(name);
    }
    dragSelectionType.value = isSelected ? 'remove' : 'add';
    togglePending(name);
    window.addEventListener('mouseup', stopDrag);
    window.addEventListener('mousemove', handleMouseMoveDrag);
};

const onDragEnter = (name) => {
    if (!isDragging.value) return;
    const isSelected = pendingNames.value.includes(name);
    
    if (dragSelectionType.value === 'add') {
        if (lotteryMode.value === true) {
            if (!pendingNames.value.includes(name)) pendingNames.value.push(name);
        } else {
            if (selectionFiltered.value) {
                if (currentStep.value === 1) {
                    if (!fixedParticipants.value.includes(name)) fixedParticipants.value.push(name);
                } else if (currentStep.value === 2) {
                    if (!pendingNames.value.includes(name)) pendingNames.value.push(name);
                }
            } else {
                if (selectionMode.value === 'fixed') {
                    if (!fixedParticipants.value.includes(name)) fixedParticipants.value.push(name);
                    if (!pendingNames.value.includes(name)) pendingNames.value.push(name);
                } else {
                    if (!pendingNames.value.includes(name)) pendingNames.value.push(name);
                }
            }
        }
    } else if (dragSelectionType.value === 'remove') {
        if (lotteryMode.value === true) {
            pendingNames.value = pendingNames.value.filter(n => n !== name);
        } else {
            if (selectionFiltered.value) {
                if (currentStep.value === 1) {
                    fixedParticipants.value = fixedParticipants.value.filter(n => n !== name);
                } else if (currentStep.value === 2) {
                    pendingNames.value = pendingNames.value.filter(n => n !== name);
                }
            } else {
                if (selectionMode.value === 'fixed') {
                    fixedParticipants.value = fixedParticipants.value.filter(n => n !== name);
                    pendingNames.value = pendingNames.value.filter(n => n !== name);
                } else {
                    pendingNames.value = pendingNames.value.filter(n => n !== name);
                    fixedParticipants.value = fixedParticipants.value.filter(n => n !== name);
                }
            }
        }
    }
};

const stopDrag = () => {
    isDragging.value = false;
    lastTouchedName.value = null;
    clearInterval(scrollInterval);
    scrollInterval = null;
    window.removeEventListener('mouseup', stopDrag);
    window.removeEventListener('mousemove', handleMouseMoveDrag);
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

watch(persistentToast, (newVal) => {
    if (newVal) lockBodyScroll();
    else unlockBodyScroll();
});

// Draft auto-save
watch(() => ({
    step: currentStep.value,
    pending: pendingNames.value,
    fixed: fixedParticipants.value,
    selected: selectedNames.value,
    round: roundParticipants.value,
    count: drawCount.value,
    manual: manualName.value,
    mode: lotteryMode.value
}), (val) => {
    if (props.show) {
        safeLocalStorage.setItem(DRAFT_KEY, JSON.stringify({
            currentStep: val.step,
            pendingNames: val.pending,
            fixedParticipants: val.fixed,
            selectedNames: val.selected,
            roundParticipants: val.round,
            drawCount: val.count,
            manualName: val.manual,
            lotteryMode: val.mode
        }));

        if (lotteryMode.value === true) {
            if (val.pending && val.pending.length > 0) {
                safeLocalStorage.setItem('shared_dharma_selection', JSON.stringify(val.pending));
            } else {
                safeLocalStorage.removeItem('shared_dharma_selection');
            }
        }
    }
}, { deep: true });

const initDraw = () => {
    if (typeof loadUsers === 'function') {
        loadUsers();
    }

    const draftStr = safeLocalStorage.getItem(DRAFT_KEY);
    if (draftStr) {
        try {
            const draft = JSON.parse(draftStr);
            if (draft.lotteryMode === props.initialMode) {
                const hasDraftData = (draft.pendingNames && draft.pendingNames.length > 0) || 
                                     (draft.fixedParticipants && draft.fixedParticipants.length > 0) || 
                                     (draft.selectedNames && draft.selectedNames.length > 0);
                if (hasDraftData && window.confirm('偵測到您有未儲存的草稿，是否要載入？')) {
                    currentStep.value = draft.currentStep || 1;
                    pendingNames.value = draft.pendingNames || [];
                    fixedParticipants.value = draft.fixedParticipants || [];
                    selectedNames.value = draft.selectedNames || [];
                    roundParticipants.value = draft.roundParticipants || [];
                    drawCount.value = draft.drawCount || 1;
                    manualName.value = draft.manualName || '';
                    lotteryMode.value = props.initialMode;
                    return;
                }
            }
        } catch (e) {}
    }

    resetAll(true);
    lotteryMode.value = props.initialMode;

    // Sync with shared selection (Only for Round Draw / Mode 1)
    if (lotteryMode.value === true) {
        const sharedStr = safeLocalStorage.getItem('shared_dharma_selection');
        if (sharedStr) {
            try {
                const sharedNames = JSON.parse(sharedStr);
                if (Array.isArray(sharedNames) && sharedNames.length > 0) {
                    pendingNames.value = sharedNames;
                    selectedNames.value = [...sharedNames];
                    selectionFiltered.value = true;
                    currentStep.value = 1;
                    roundParticipants.value = [];
                    drawCount.value = 1;
                }
            } catch (e) {}
        }
    }
};

watch(() => props.show, (val) => {
    if (val) {
        initDraw();
    } else {
        safeLocalStorage.removeItem(DRAFT_KEY);
    }
});

onMounted(() => {
    loadUsers();
    if (props.show) {
        initDraw();
    }
});
</script>

<style scoped>
.no-scrollbar::-webkit-scrollbar { display: none; }
.custom-scrollbar {
    -webkit-overflow-scrolling: touch;
    overscroll-behavior-y: contain;
}
.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
.animate-fade-in { animation: fadeIn 0.3s ease-out; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
.animate-slide-in { animation: slideIn 0.4s ease-out; }
@keyframes slideIn { from { opacity: 0; transform: translateX(30px); } to { opacity: 1; transform: translateX(0); } }
* { -webkit-tap-highlight-color: transparent; }

/* ========== LOTTERY STICK ANIMATION ========== */
.lottery-stick {
    position: absolute;
    bottom: 192px;
    width: 24px;
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
.animate-slide-up { animation: slideUp 0.25s cubic-bezier(0.16, 1, 0.3, 1); }
@keyframes slideUp { from { transform: translateY(100%); } to { transform: translateY(0); } }
.stick-body {
    width: 24px;
    height: 110px;
    border-radius: 12px 12px 4px 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    writing-mode: vertical-rl;
    box-shadow: 0 4px 20px rgba(0,0,0,0.5), inset 0 1px 0 rgba(255,255,255,0.2);
}
.stick-name {
    font-size: 16px;
    font-weight: 900;
    color: #1e1b4b;
    letter-spacing: 2px;
}
.stick-tip {
    width: 10px; height: 10px; border-radius: 50%; margin-top: -3px; box-shadow: 0 2px 6px rgba(0,0,0,0.4);
}

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
    width: 160px; height: 10px; background: #92400e; border-radius: 3px 3px 0 0;
    box-shadow: 0 3px 6px rgba(0,0,0,0.3); border-bottom: 2px solid #78350f;
}
.cup-sticks-wrapper {
    position: absolute; top: -96px; left: 50%; transform: translateX(-50%);
    width: 128px; height: 96px; display: flex; align-items: flex-end; justify-content: center; gap: 3px;
}
.cup-stick {
    width: 11px; height: 112px; border-radius: 3px 3px 1px 1px; transform: rotate(var(--ctilt));
    transform-origin: bottom center; animation: cupStickJiggle 0.15s ease-in-out infinite alternate;
    animation-delay: calc(var(--ci) * 15ms);
}
@keyframes cupStickJiggle {
    from { transform: rotate(calc(var(--ctilt) - 2deg)) translateY(0px); }
    to   { transform: rotate(calc(var(--ctilt) + 2deg)) translateY(-4px); }
}
.cup-body {
    width: 160px; height: 192px; border-radius: 3px 3px 6px 6px;
    background: linear-gradient(90deg, #92400e 0%, #b45309 15%, #92400e 30%, #78350f 50%, #92400e 70%, #b45309 85%, #78350f 100%);
    box-shadow: inset 3px 0 8px rgba(255,255,255,0.05), 0 10px 32px rgba(0,0,0,0.4);
    position: relative; overflow: hidden;
}
.cup-body::after {
    content: ""; position: absolute; inset: 0;
    background-image: repeating-linear-gradient(90deg, transparent, transparent 14px, rgba(0,0,0,0.15) 16px);
}
.cup-base {
    width: 176px; height: 10px; background: #451a03; border-radius: 2px;
    box-shadow: 0 3px 10px rgba(0,0,0,0.3); position: relative; z-index: 2;
}

.flying-name-display { margin-top: 24px; height: 50px; display: flex; align-items: center; justify-content: center; }
.flying-name-text {
    font-size: 36px; font-weight: 900; color: #92400e; letter-spacing: 6px;
    text-shadow: 0 4px 20px rgba(217,119,6,0.4); animation: namePulse 0.1s ease-in-out;
}
@keyframes namePulse { from { opacity: 0.2; transform: scale(0.9); } to { opacity: 1; transform: scale(1); } }
</style>
