<template>
    <div v-if="show" class="fixed inset-0 z-[100] bg-white overflow-hidden animate-fade-in font-sans">
        
        <!-- STEP 1: PERSONNEL SELECTION -->
        <div v-if="currentStep === 1" class="fixed inset-0 flex flex-col bg-white overflow-hidden z-[150]">
            <div class="animate-fade-in flex flex-col h-full overflow-hidden">
                <!-- Header bar -->
                <div class="border-b border-slate-100 bg-white sticky top-0 z-10 shrink-0 px-2 py-2">
                    <div class="flex flex-col w-full gap-1">
                        <!-- First Row: Main Title -->
                        <div class="flex items-center">
                            <button @click="handleBack" class="p-2 -ml-2 text-slate-400 active:scale-90 transition-all mr-1">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" /></svg>
                            </button>
                            <div class="app-title font-black leading-tight font-outfit tracking-widest" style="color: #0f172a !important; font-size: 25px !important;">
                                其他專區
                            </div>
                        </div>
                        
                                                <!-- Second Row: Subtitle -->
                        <div class="flex items-center justify-between w-full mt-1 px-1">
                            <span class="text-slate-700 font-normal shrink-0 mr-2" style="font-size: 22px !important;">抽順序 - {{ lotteryMode ? '回合抽籤' : '直接排列' }}</span>
                            <button @click="resetAll" class="p-2 text-slate-400 hover:text-rose-500 transition-colors bg-slate-50 rounded-xl border border-slate-100 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </button>
                        </div>

                        <!-- Third Row: Main Actions (Horizontal Row) -->
                        <div class="flex items-center space-x-2 mt-1 px-1 overflow-x-auto no-scrollbar pb-1">
                            <button ontouchstart="" @click="invertSelection(); activeAction = 'invert'" 
                                :class="[
                                    'whitespace-nowrap px-4 py-2 text-[15px] rounded-xl shadow-sm border transition-all duration-150 font-black flex items-center justify-center',
                                    activeAction === 'invert' ? 'bg-blue-600 border-blue-600' : 'bg-white border-slate-200 text-black active:bg-slate-100'
                                ]" :style="{ color: activeAction === 'invert' ? '#ffffff !important' : '#000000' }">
                                反選
                            </button>
                            <button v-if="lotteryMode === false && !selectionFiltered" 
                                @click="selectionMode = (selectionMode === 'pool' ? 'fixed' : 'pool')"
                                class="whitespace-nowrap px-4 py-2 text-[15px] font-black rounded-xl transition-all shadow-sm border flex items-center justify-center"
                                :class="selectionMode === 'fixed' ? 'bg-indigo-600 border-indigo-600 text-white' : 'bg-indigo-50 border-indigo-100 text-indigo-600'"
                                :style="{ color: selectionMode === 'fixed' ? '#ffffff !important' : '#4f46e5' }">
                                {{ selectionMode === 'fixed' ? '正在選：優先固定' : '切換：選固定人' }}
                            </button>
                            <button v-if="lotteryMode !== null" @click="lotteryMode = null; resetAll(true)" 
                                class="whitespace-nowrap px-4 py-2 text-[15px] font-black text-blue-700 bg-blue-50 border border-blue-100 rounded-xl shadow-sm flex items-center justify-center">
                                切換模式
                            </button>
                        </div>
                    </div>
                </div>

                
                <!-- MODE SELECTION GATEKEEPER -->
                <div v-if="lotteryMode === null" class="flex-1 flex flex-col justify-center animate-fade-in bg-slate-50/30">
                    <div class="text-[19px] font-black text-slate-800 mb-8 text-center px-4">請選擇排列方式：</div>
                    <div class="grid grid-cols-1 gap-6 max-w-[280px] mx-auto w-full">
                        <button @click="lotteryMode = false" 
                            class="flex flex-col items-center justify-center p-8 rounded-[40px] border-2 transition-all active:scale-95 bg-white border-blue-100 shadow-xl shadow-blue-50/50">
                            <svg class="w-12 h-12 mb-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            <span class="text-[22px] font-black text-blue-700">直接排列</span>
                            <span class="text-[15px] font-bold text-blue-400 opacity-80 mt-1">依點選順序</span>
                        </button>
                        <button @click="lotteryMode = true" 
                            class="flex flex-col items-center justify-center p-8 rounded-[40px] border-2 transition-all active:scale-95 bg-white border-emerald-100 shadow-xl shadow-emerald-50/50">
                            <svg class="w-12 h-12 mb-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.183.319l-3.08 1.914a1 1 0 00.314 1.83l3.593.513a4 4 0 013.11 2.303l.274.549a1 1 0 001.754 0l.274-.549a4 4 0 013.11-2.303l3.593-.513a1 1 0 00.314-1.83l-3.08-1.914z"></path></svg>
                            <span class="text-[22px] font-black text-emerald-700">回合抽籤</span>
                            <span class="text-[15px] font-bold text-emerald-400 opacity-80 mt-1">從名單中隨機</span>
                        </button>
                    </div>
                </div>

                <!-- Main scrollable selection grid -->
                <div v-else class="flex-1 overflow-y-auto no-scrollbar pb-64 animate-fade-in">
                    
                    <!-- Selected Pool (Persistent Display) -->
                    <div v-if="pendingNames.length > 0" class="bg-blue-50/40 border border-blue-100 rounded-2xl p-4 mx-2 mt-4 mb-2">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center space-x-2">
                                <span class="text-[15px] font-black text-blue-800">{{ selectionMode === "fixed" ? "優先固定人員 (照點選順序)" : "已選定人員 (基礎名單)" }}</span>
                                <span class="px-2 py-0.5 bg-blue-600 text-white text-[11px] font-black rounded-full" style="color: white !important;">{{ pendingNames.length }} 人</span>
                            </div>
                            <button @click="resetAll" class="text-blue-400 hover:text-rose-500 transition-colors p-1">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </div>
                        <div class="flex flex-wrap gap-2 max-h-[250px] overflow-y-auto custom-scrollbar pr-1">
                            <div v-for="(name, pidx) in pendingNames" :key="name" class="bg-white border border-blue-100 px-3 py-1.5 rounded-xl text-[15px] font-black text-blue-900 shadow-sm flex items-center group active:scale-95 transition-all">
                                
                                {{ name }}
                                <button @click="togglePending(name)" class="ml-1.5 text-blue-200 group-hover:text-rose-400 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" /></svg>
                                </button>
                            </div>
                        </div>
                        <div class="mt-3 text-[11px] text-blue-400 font-bold italic text-center">* 已依點選順序自動排列</div>
                    </div>

                    

                    <div class="flex items-center justify-between px-3 py-1.5">
                        <div class="flex items-center space-x-2 flex-1 mr-4">
                            <span class="font-black text-[15px] shrink-0 text-slate-400">
                                全名單表 (可繼續增減)
                            </span>
                            <!-- Manual Add Input -->
                            <div class="flex items-center bg-slate-100 rounded-lg px-2 py-1 flex-1 max-w-[150px]">
                                <input v-model="manualName" @keyup.enter="addManualName" type="text" placeholder="手動輸入..." class="bg-transparent border-none outline-none text-[13px] font-bold w-full">
                                <button @click="addManualName" class="text-blue-500 font-black text-xl ml-1">+</button>
                            </div>
                        </div>
                        <span class="text-[15px] font-bold" :style="{ color: pendingNames.length > 0 ? '#1d4ed8' : '#94a3b8' }">共 {{ pendingNames.length }} 人</span>
                    </div>

                    <!-- Grid evenly distributed, stretching boxes -->
                    <div class="grid grid-cols-4 md:grid-cols-5 px-1 w-full mt-[5px]" 
                         style="gap: 4px; background: #ffffff;"
                         @mouseleave="stopDrag"
                         @touchmove.prevent="handleTouchMove">
                            <button
                                v-for="user in displayUsers"
                                :key="user.id || user.name"
                                :data-name="user.name"
                                @mousedown="startDrag(user.name)"
                                @mouseenter="onDragEnter(user.name)"
                                @mouseup="stopDrag"
                                @touchstart="handleTouchStart($event, user.name)"
                                @touchend="stopDrag"
                                @click.prevent="" class="dharma-btn flex items-center justify-center font-black text-[17px] transition-all active:scale-95 rounded-md border shadow-sm w-full min-h-[45px] select-none"
                                :style="getPendingStyle(user.name)"
                            >
                                <span class="truncate leading-none pointer-events-none">{{ user.name }}</span>
                            </button>
                    </div>
                </div>


            <!-- Confirm button -->
            <div class="fixed bottom-[7vh] left-0 right-0 px-4 pb-1 pt-3 bg-white/95 backdrop-blur-sm border-t border-slate-100 z-[200]">
                <div class="space-y-2">
                    <button
                        v-if="!selectionFiltered && pendingNames.length > 0"
                        @click="performQuickDraw"
                        class="w-full py-[10px] rounded-2xl font-black text-[17px] transition-all active:scale-[0.98] shadow-lg border-2 border-indigo-500 bg-white text-indigo-600"
                    >
                        直接全部隨機排序
                    </button>
                    <button
                        @click="confirmSelection"
                        :disabled="pendingNames.length === 0"
                        class="w-full py-[10px] rounded-2xl font-black text-[17px] transition-all active:scale-[0.98] shadow-none"
                        :style="{
                            background: pendingNames.length === 0 ? '#93c5fd' : (selectionFiltered ? '#16a34a' : '#1d4ed8'),
                            color: '#ffffff'
                        }"
                    >
                        <span v-if="!selectionFiltered" style="color: #ffffff !important;">確定 (已選 {{ pendingNames.length }} 人)</span>
                        <span v-else style="color: #ffffff !important;">確定 → 進入抽籤設定</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

        <!-- STEP 2: DRAW CONFIGURATION / ROUND SELECTION -->
        <div v-if="currentStep === 2" class="fixed inset-0 flex flex-col bg-white overflow-hidden z-[150]">
            <div class="animate-slide-in flex flex-col h-full overflow-hidden">
                <div class="bg-white border-b border-slate-100 p-3 flex flex-col sticky top-0 z-10">
                    <div class="flex flex-col w-full gap-1">
                        <div class="flex items-center">
                            <div class="w-1"></div>
                            <div class="app-title font-black leading-tight font-outfit tracking-widest" style="color: #0f172a !important; font-size: 25px !important;">
                                其他專區
                            </div>
                        </div>
                        <!-- Second Row: Subtitle + Back -->
                        <div class="flex items-center justify-between w-full mt-1 px-1">
                            <div class="flex items-center">
                                <button @click="currentStep = 1" class="text-slate-400 p-1 mr-2 -ml-1">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </button>
                                <span class="text-slate-700 font-normal shrink-0 mr-2" style="font-size: 22px !important;">{{ lotteryMode ? '回合抽籤 - 本輪挑選' : '直接排列 - 抽籤設定' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-4 flex-1 overflow-y-auto flex flex-col gap-6 max-w-lg mx-auto w-full no-scrollbar pb-48">
                    
                    <!-- LOTTERY MODE: Round Subset Selection -->
                    <div v-if="lotteryMode" class="space-y-4">
                        <div class="flex items-center justify-between mb-2">
                            <label class="text-[15px] font-black text-slate-400 uppercase tracking-widest">1. 從基礎名單點選本輪人員</label>
                            <span class="text-[17px] font-black text-emerald-600">{{ roundParticipants.length }} 人</span>
                        </div>
                        <div class="grid grid-cols-4 gap-2" @mouseleave="stopDrag" @touchmove.prevent="handleTouchMoveStep2">
                            <button v-for="name in selectedNames" :key="'round'+name"
                                @mousedown="startDragStep2(name)"
                                @mouseenter="onDragEnterStep2(name)"
                                @mouseup="stopDrag"
                                @touchstart="handleTouchStartStep2($event, name)"
                                @touchend="stopDrag"
                                :data-round-name="name"  
                                @click="toggleRoundParticipant(name)"
                                class="h-12 flex items-center justify-center rounded-xl border-2 font-black text-[17px] transition-all active:scale-95"
                                :class="isRoundParticipant(name) ? 'bg-emerald-600 border-emerald-600 text-white shadow-sm' : 'bg-white border-slate-200 text-black shadow-sm'" :style="{ color: isRoundParticipant(name) ? '#ffffff !important' : '#000000 !important' }">
                                {{ name }}
                            </button>
                        </div>

                        <div v-if="roundParticipants.length > 0" class="pt-4 border-t border-slate-100">
                            <label class="text-[15px] font-black text-slate-400 uppercase tracking-widest block mb-3">2. 本輪參加名單</label>
                            <div class="flex flex-wrap gap-2">
                                <div v-for="name in roundParticipants" :key="'tag'+name" class="bg-rose-50 text-rose-700 px-3 py-1.5 rounded-lg font-black text-[17px] border border-rose-100 flex items-center">
                                    {{ name }}
                                    <button @click="toggleRoundParticipant(name)" class="ml-1 text-rose-300">×</button>
                                </div>
                            </div>
                        </div>
                    </div>

                                        <!-- DIRECT MODE: Fixed Front Positions -->
                    <div v-else class="space-y-8">
                        <!-- Section 1: Fixed People -->
                        <div class="space-y-4">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex flex-col">
                                    <label class="text-[15px] font-black text-slate-400 uppercase tracking-widest">1. 選擇優先固定人員</label>
                                    <span class="text-[12px] text-slate-400 font-bold">點選變為紫色，將固定排在最前面</span>
                                </div>
                                <span class="text-[17px] font-black text-indigo-600">{{ fixedParticipants.length }} 人</span>
                            </div>
                                                                                                                <div class="grid grid-cols-4 gap-2">
                                <button v-for="name in selectedNames" :key="'fixed'+name"
                                    @click="toggleFixedParticipant(name)"
                                    class="h-12 flex flex-col items-center justify-center rounded-xl border-2 font-black text-[15px] transition-all active:scale-95 leading-tight shadow-sm"
                                    :class="fixedParticipants.includes(name) ? 'bg-rose-600 border-rose-600 text-white' : 'bg-emerald-500 border-emerald-500 text-white'" 
                                    :style="{ color: '#ffffff !important' }">
                                    <span>{{ name }}</span>
                                    <span v-if="fixedParticipants.includes(name)" class="text-[10px] opacity-90 font-bold uppercase tracking-tighter">
                                        第 {{ fixedParticipants.indexOf(name) + 1 }} 位
                                    </span>
                                </button>
                            </div>
                        </div>

                        <!-- Section 2: Summary of Lists -->
                        <div v-if="fixedParticipants.length > 0 || (selectedNames.length - fixedParticipants.length) > 0" class="pt-6 border-t border-slate-100 space-y-6">
                            <!-- Fixed List -->
                            <div v-if="fixedParticipants.length > 0">
                                <label class="text-[15px] font-black text-rose-500 uppercase tracking-widest block mb-3">● 優先排列順序 (第 1 ~ {{ fixedParticipants.length }} 名)</label>
                                <div class="flex flex-wrap gap-2">
                                    <div v-for="(name, fidx) in fixedParticipants" :key="'ftag'+name" class="bg-rose-50 text-rose-700 px-3 py-1.5 rounded-lg font-black text-[17px] border border-rose-100 flex items-center">
                                        <span class="mr-1 text-[13px] opacity-40">{{ fidx + 1 }}.</span>
                                        {{ name }}
                                        <button @click="toggleFixedParticipant(name)" class="ml-1 text-rose-300">×</button>
                                    </div>
                                </div>
                            </div>

                            <!-- Random Pool List -->
                            <div v-if="(selectedNames.length - fixedParticipants.length) > 0">
                                <label class="text-[15px] font-black text-emerald-500 uppercase tracking-widest block mb-3">● 隨機抽籤名單 (接在固定人員之後)</label>
                                <div class="flex flex-wrap gap-2">
                                    <div v-for="name in selectedNames.filter(n => !fixedParticipants.includes(n))" :key="'rtag'+name" class="bg-emerald-50 text-emerald-700 px-3 py-1.5 rounded-lg font-black text-[17px] border border-emerald-100 flex items-center">
                                        {{ name }}
                                    </div>
                                </div>
                                <div class="mt-2 text-[12px] text-emerald-500 font-bold italic">以上 {{ selectedNames.length - fixedParticipants.length }} 人將進行隨機排序</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Fixed Bottom Action Area -->
            <div class="fixed bottom-[7vh] left-0 right-0 px-4 pb-1 pt-3 bg-white/95 backdrop-blur-md border-t border-slate-100 z-[200]">
                <div class="max-w-lg mx-auto space-y-4">
                    <!-- Config Row for Direct Mode -->
                    <div class="flex items-center justify-between px-1">
                        <label class="text-[15px] font-black text-slate-400 uppercase tracking-wider">抽取人數</label>
                        <div class="flex items-center border border-slate-200 rounded-xl overflow-hidden h-12 bg-slate-50/50 w-48 shadow-sm">
                            <button @click="drawCount = Math.max(1, drawCount - 1)" class="w-14 h-full text-white bg-slate-400 text-[20px] font-black shadow-none border-none active:bg-slate-500">−</button>
                            <input 
                                type="number" 
                                v-model.number="drawCount" 
                                @blur="drawCount = Math.max(1, Math.min(lotteryMode ? roundParticipants.length : selectedNames.length, drawCount || 1))"
                                class="flex-1 text-center text-[20px] font-black text-slate-800 bg-transparent outline-none w-full"
                            >
                            <button @click="drawCount = Math.min(lotteryMode ? roundParticipants.length : selectedNames.length, drawCount + 1)" class="w-14 h-full text-white bg-slate-400 text-[20px] font-black shadow-none border-none active:bg-slate-500">＋</button>
                        </div>
                    </div>
                    
                    <!-- Action Button -->
                    <button 
                        @click="lotteryMode ? performRoundDraw() : performDraw()"
                        :disabled="lotteryMode && roundParticipants.length === 0"
                        class="w-full py-[12px] rounded-3xl font-black text-[17px] shadow-none active:scale-[0.98] transition-all flex justify-center items-center"
                        :class="lotteryMode ? 'bg-emerald-600' : 'bg-indigo-600'"
                    >
                        <span style="color: #ffffff !important;">{{ lotteryMode ? '開始本輪抽籤' : '開始隨機抽籤' }}</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

        <!-- FULLSCREEN LOTTERY ANIMATION -->
        <div v-if="isDrawing" class="fixed inset-0 z-[500] flex flex-col items-center justify-center overflow-hidden" style="background: #fefce8;">
            <div class="absolute inset-0 pointer-events-none">
                <div style="position:absolute;top:30%;left:50%;transform:translate(-50%,-50%);width:400px;height:400px;background:radial-gradient(circle,rgba(251,191,36,0.15) 0%,transparent 70%);border-radius:50%;"></div>
            </div>

            <div class="lottery-cup-container" style="transform: translateY(10vh);">
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
                    <span class="flying-name-text">{{ lotteryDisplayNames[0] || '' }}</span>
                </div>
            </div>

            <div class="absolute top-[3vh] left-0 right-0 flex flex-col items-center space-y-2">
                <p class="text-[32px] font-black tracking-widest text-amber-900">隨機抽籤中</p>
                <div class="flex gap-2">
                    <span class="dot-lg" style="background:#b45309;"></span>
                    <span class="dot-lg" style="background:#d97706;"></span>
                    <span class="dot-lg" style="background:#f59e0b;"></span>
                </div>
            </div>
        </div>

        <!-- STEP 3: RESULTS -->
        <div v-if="currentStep === 3" class="fixed inset-0 flex flex-col bg-white overflow-hidden z-[1000]">
            <div class="animate-fade-in flex flex-col h-full overflow-hidden">
                <div class="bg-white border-b border-slate-100 p-4 flex items-center justify-between sticky top-0 z-10">
                    <button @click="currentStep = 2" class="text-slate-400 p-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                    <h2 class="text-[19px] font-black text-slate-900 flex-1 text-center">抽籤結果 ({{ results.length }}人)</h2>
                    <div class="flex items-center space-x-2">
                        <button @click="performDraw" class="text-[15px] font-black text-white bg-rose-500 px-3 py-[10px] rounded-full shadow-none border-none">重抽</button>
                        <button @click="copyResults" class="text-[15px] font-black text-white bg-emerald-500 px-3 py-[10px] rounded-full shadow-none border-none">複製</button>
                    </div>
                </div>

                <div class="flex-1 overflow-y-auto p-4 no-scrollbar">
                    <div class="max-w-lg mx-auto space-y-4 pb-32 pt-2">
                        <!-- SINGLE RESULT: CROWN & CENTERED -->
                        <div v-if="results.length === 1" class="flex flex-col items-center justify-center space-y-4 animate-scale-in pt-4">
                            <div class="relative">
                                <span class="text-[56px] filter drop-shadow-lg">👑</span>
                                <div class="absolute -bottom-2 left-1/2 -translate-x-1/2 w-24 h-2 bg-indigo-500/10 blur-xl rounded-full"></div>
                            </div>
                            <h3 class="text-[32px] font-black px-8 py-4 rounded-2xl shadow-lg mb-4" style="background-color: #facc15 !important; color: #dc2626 !important; border: 2px solid #eab308 !important;">1. {{ results[0] }}</h3>
                            <div class="flex items-center space-x-2 text-indigo-300">
                                <div class="h-[2px] w-8 bg-indigo-100"></div>
                                <span class="text-[15px] font-black uppercase tracking-widest">唯一幸運兒</span>
                                <div class="h-[2px] w-8 bg-indigo-100"></div>
                            </div>
                        </div>

                        <!-- MULTIPLE RESULTS: CENTERED & INDEXED -->
                        <div v-else class="flex flex-col items-center space-y-2">
                            <div v-for="(name, idx) in results" :key="'res'+idx" 
                                class="w-full py-3 flex flex-col items-center rounded-xl bg-white border border-slate-200 shadow-sm animate-slide-in">
                                
                                <span class="text-[15px] font-black text-indigo-500 mb-1">{{ idx + 1 }}.</span>
                                <span class="text-[19px] font-black text-black">{{ name }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        <!-- GLOBAL BOTTOM NAVIGATION -->
        <mobile-navbar 
            v-if="!isDrawing"
            :can-back="true"
            :can-home="true"
            :show-action="false"
            @back="currentStep === 2 ? currentStep = 1 : $emit('close')"
            @home="$emit('close')"
        />

    </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import axios from 'axios';
import MobileNavbar from './MobileNavbar.vue';

const props = defineProps({
    show: Boolean
});

const emit = defineEmits(['close']);

watch(() => props.show, (val) => {
    if (val) {
        loadUsers();
        currentStep.value = 1;
        selectedNames.value = [];
        hasResult.value = false;
        results.value = [];
    roundParticipants.value = [];
    fixedParticipants.value = [];
    }
});

const users = ref([]);
const pendingNames = ref([]);
const selectedNames = ref([]);
const drawCount = ref(1);
const currentStep = ref(1);
const selectionFiltered = ref(false);
const isDrawing = ref(false);
const hasResult = ref(false);
const results = ref([]);
const activeAction = ref('');
const lotteryMode = ref(null);
const selectionMode = ref("pool"); // "pool" or "fixed"

watch(lotteryMode, () => {
    resetAll(true);
});


const roundParticipants = ref([]);
const fixedParticipants = ref([]);
const toggleRoundParticipant = (name) => {
    const idx = roundParticipants.value.indexOf(name);
    if (idx > -1) roundParticipants.value.splice(idx, 1);
    else roundParticipants.value.push(name);
};
const isRoundParticipant = (name) => roundParticipants.value.includes(name);

const isDragging = ref(false);
const dragSelectionType = ref(null); // 'add' or 'remove'



const toggleFixedParticipant = (name) => {
    const idx = fixedParticipants.value.indexOf(name);
    if (idx > -1) fixedParticipants.value.splice(idx, 1);
    else fixedParticipants.value.push(name);
};

const startDragFixed = (name) => {
    isDragging.value = true;
    const isSelected = fixedParticipants.value.includes(name);
    dragSelectionType.value = isSelected ? 'remove' : 'add';
    toggleFixedParticipant(name);
    window.addEventListener('mouseup', stopDrag);
};

const onDragEnterFixed = (name) => {
    if (!isDragging.value) return;
    const isSelected = fixedParticipants.value.includes(name);
    if (dragSelectionType.value === 'add' && !isSelected) {
        fixedParticipants.value.push(name);
    } else if (dragSelectionType.value === 'remove' && isSelected) {
        const idx = fixedParticipants.value.indexOf(name);
        fixedParticipants.value.splice(idx, 1);
    }
};

const handleTouchStartFixed = (e, name) => {
    lastTouchedName.value = name;
    startDragFixed(name);
};

const handleTouchMoveFixed = (e) => {
    if (!isDragging.value) return;
    const touch = e.touches[0];
    const el = document.elementFromPoint(touch.clientX, touch.clientY);
    if (!el) return;
    const btn = el.closest('button[data-fixed-name]');
    if (btn) {
        const name = btn.getAttribute('data-fixed-name');
        if (name && name !== lastTouchedName.value) {
            lastTouchedName.value = name;
            onDragEnterFixed(name);
        }
    }
};

const startDragStep2 = (name) => {
    isDragging.value = true;
    const isSelected = isRoundParticipant(name);
    dragSelectionType.value = isSelected ? 'remove' : 'add';
    toggleRoundParticipant(name);
    window.addEventListener('mouseup', stopDrag);
};

const onDragEnterStep2 = (name) => {
    if (!isDragging.value) return;
    const isSelected = isRoundParticipant(name);
    if (dragSelectionType.value === 'add' && !isSelected) {
        roundParticipants.value.push(name);
    } else if (dragSelectionType.value === 'remove' && isSelected) {
        const idx = roundParticipants.value.indexOf(name);
        roundParticipants.value.splice(idx, 1);
    }
};

const handleTouchStartStep2 = (e, name) => {
    lastTouchedName.value = name;
    startDragStep2(name);
};

const handleTouchMoveStep2 = (e) => {
    if (!isDragging.value) return;
    const touch = e.touches[0];
    const el = document.elementFromPoint(touch.clientX, touch.clientY);
    if (!el) return;
    const btn = el.closest('button[data-round-name]');
    if (btn) {
        const name = btn.getAttribute('data-round-name');
        if (name && name !== lastTouchedName.value) {
            lastTouchedName.value = name;
            onDragEnterStep2(name);
        }
    }
};

const startDrag = (name) => {
    isDragging.value = true;
    const isSelected = pendingNames.value.includes(name);
    dragSelectionType.value = isSelected ? 'remove' : 'add';
    togglePending(name);
    
    // Add global mouseup listener
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

const lastTouchedName = ref(null);

const handleTouchStart = (e, name) => {
    lastTouchedName.value = name;
    startDrag(name);
};

const handleTouchMove = (e) => {
    if (!isDragging.value) return;
    const touch = e.touches[0];
    const el = document.elementFromPoint(touch.clientX, touch.clientY);
    if (!el) return;
    
    // Find the closest dharma-btn
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

// Persistence removed per user request for fresh state on load

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
    // Always return all users so the table is always visible
    return users.value.filter(u => u && u.name);
});

const getPendingStyle = (name) => {
    const t = name.trim();
    const isSelected = pendingNames.value.includes(t);
    const isFixed = fixedParticipants.value.includes(t);
    if (isFixed) {
        return {
            backgroundColor: '#4f46e5',
            color: '#ffffff !important',
            border: '2px solid #4338ca'
        };
    } else if (isSelected) {
        return {
            backgroundColor: '#1d4ed8', // Dark Blue for Selected
            color: '#ffffff !important',
            border: '2px solid #1e40af'
        };
    } else {
        return {
            backgroundColor: '#ffffff', // White for Unselected
            color: '#000000', // Black text
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
    const isPool = pendingNames.value.includes(name);
    const isFixed = fixedParticipants.value.includes(name);
    
    if (isPool || isFixed) {
        // Already selected (either blue or indigo), so UNSELECT completely (white)
        const pIdx = pendingNames.value.indexOf(name);
        if (pIdx > -1) pendingNames.value.splice(pIdx, 1);
        
        const fIdx = fixedParticipants.value.indexOf(name);
        if (fIdx > -1) fixedParticipants.value.splice(fIdx, 1);
    } else {
        // Not selected, add according to current mode
        if (selectionMode.value === 'fixed') {
            fixedParticipants.value.push(name);
            pendingNames.value.push(name);
        } else {
            pendingNames.value.push(name);
        }
    }
};

const selectAll = () => {
    pendingNames.value = users.value.map(u => u.name);
};

const invertSelection = () => {
    const allNames = users.value.map(u => u.name);
    pendingNames.value = allNames.filter(n => !pendingNames.value.includes(n));
};

const confirmSelection = () => {
    if (pendingNames.value.length === 0) return;
    if (!selectionFiltered.value) {
        selectionFiltered.value = true;
        return;
    }
    
    selectedNames.value = [...pendingNames.value];
    
    // In Direct Mode, default to all selected names
    // In Lottery Mode, default to 1 person
    if (lotteryMode.value === false) {
        drawCount.value = selectedNames.value.length;
    } else {
        drawCount.value = 1;
    }
    
    currentStep.value = 2;
    if (lotteryMode.value) {
        roundParticipants.value = [];
        fixedParticipants.value = [];
    }
};

const handleBack = () => {
    if (hasResult.value) {
        hasResult.value = false;
        currentStep.value = 2;
    if (lotteryMode.value) {
        roundParticipants.value = [];
    fixedParticipants.value = [];
        drawCount.value = 1;
    }
        return;
    }
    if (currentStep.value === 2) {
        currentStep.value = 1;
        return;
    }
    emit('close');
};

const resetAll = (silent = false) => {
    if (!silent && !confirm('確定要清空所有進度嗎？')) return;
    pendingNames.value = [];
    selectedNames.value = [];
    selectionFiltered.value = false;
    currentStep.value = 1;
    hasResult.value = false;
    results.value = [];
    roundParticipants.value = [];
    fixedParticipants.value = [];
};

// Save to session removed for fresh state on load

const loadUsers = async () => {
    try {
        const res = await axios.get('/api/dharma-names-list');
        let rawUsers = res.data;
        
        // Custom sorting: Ensure 靈奇, 靈傾 are after 靈情
        const targetNames = ['靈情', '靈奇', '靈傾'];
        const listWithoutTargets = rawUsers.filter(u => !targetNames.includes(u.name));
        const lingQing = rawUsers.find(u => u.name === '靈情');
        const lingQi = rawUsers.find(u => u.name === '靈奇');
        const lingQin = rawUsers.find(u => u.name === '靈傾');
        
        if (lingQing) {
            const idx = listWithoutTargets.findIndex(u => u.name === '靈情'); // Should not exist
            const insertIdx = listWithoutTargets.findIndex(u => u.name === '靈情'); // Placeholder
            // Find where LingQing should be or was
            const finalIdx = rawUsers.findIndex(u => u.name === '靈情');
            
            // Simplified approach: Re-insert them in place
            let processed = [...rawUsers];
            const qingIdx = processed.findIndex(u => u.name === '靈情');
            if (qingIdx > -1) {
                // Remove Qi and Qin if they exist elsewhere
                processed = processed.filter(u => u.name !== '靈奇' && u.name !== '靈傾');
                const newQingIdx = processed.findIndex(u => u.name === '靈情');
                if (lingQi) processed.splice(newQingIdx + 1, 0, lingQi);
                if (lingQin) processed.splice(newQingIdx + 2, 0, lingQin);
            }
            users.value = processed;
        } else {
            users.value = rawUsers;
        }

        // Start with empty selection as per user request
        pendingNames.value = [];
        selectionFiltered.value = false;
    } catch (e) { console.error(e); }
};




// Animation Logic
const lotteryDisplayNames = ref([]);
const flyingSticks = ref([]);
let lotteryInterval = null;

const buildFlyingSticks = (names) => {
    flyingSticks.value = names.slice(0, 12).map((name, i) => ({
        id: i,
        name: name,
        x: 20 + (i * 5.5) % 60,
        dur: 1.8 + (i * 0.1) % 1.2,
        delay: (i * 0.25) % 2.5,
        rotate: -40 + (i * 17) % 80,
        drift: -60 + (i * 23) % 120,
    }));
};

// Replacement fallback
const performRoundDraw = () => {
    if (roundParticipants.value.length === 0) {
        alert('請先選擇參與抽籤的人員');
        return;
    }
    
    isDrawing.value = true;
    hasResult.value = false;
    buildFlyingSticks([...roundParticipants.value]);

    const pool = [...roundParticipants.value];
    let idx = 0;
    lotteryInterval = setInterval(() => {
        if (pool.length > 0) {
            idx = (idx + 1) % pool.length;
            lotteryDisplayNames.value = [pool[idx]];
        }
    }, 80);

    setTimeout(() => {
        try {
            clearInterval(lotteryInterval);
            const shuffled = [...pool].sort(() => Math.random() - 0.5);
            // Default drawCount to at least 1
            const count = Math.max(1, Math.min(drawCount.value || 1, pool.length));
            results.value = shuffled.slice(0, count);
            
            if (results.value.length === 0 && pool.length > 0) {
                results.value = [pool[0]];
            }
            
            hasResult.value = true;
            currentStep.value = 3;
        } catch (err) {
            console.error("Round Draw Error:", err);
            results.value = pool.slice(0, 1);
            currentStep.value = 3;
        } finally {
            isDrawing.value = false;
        }
    }, 3000);
};
const performQuickDraw = () => {
    if (pendingNames.value.length === 0) return;
    selectedNames.value = [...pendingNames.value];
    fixedParticipants.value = [];
    drawCount.value = selectedNames.value.length;
    performDraw();
};

const performDraw = () => {
    if (selectedNames.value.length === 0) return;
    
    isDrawing.value = true;
    hasResult.value = false;
    buildFlyingSticks([...selectedNames.value]);

    const pool = [...selectedNames.value];
    let idx = 0;
    lotteryInterval = setInterval(() => {
        if (pool.length > 0) {
            idx = (idx + 1) % pool.length;
            lotteryDisplayNames.value = [pool[idx]];
        }
    }, 80);

    setTimeout(() => {
        try {
            clearInterval(lotteryInterval);
            
            const fixed = [...fixedParticipants.value];
            // Filter fixed list to ensure they actually exist in the pool
            const validFixed = fixed.filter(n => pool.includes(n));
            const remaining = pool.filter(n => !validFixed.includes(n));
            const shuffledRemaining = [...remaining].sort(() => Math.random() - 0.5);
            
            const fullList = [...validFixed, ...shuffledRemaining];
            
            // Ensure results has at least something if fullList is not empty
            const finalCount = Math.max(1, Math.min(drawCount.value || fullList.length, fullList.length));
            results.value = fullList.slice(0, finalCount);
            
            if (results.value.length === 0 && fullList.length > 0) {
                results.value = [fullList[0]];
            }
            
            hasResult.value = true;
            currentStep.value = 3;
        } catch (err) {
            console.error("Draw Error:", err);
            // Fallback to avoid blank screen
            results.value = pool.slice(0, 1);
            currentStep.value = 3;
        } finally {
            isDrawing.value = false;
        }
    }, 3000);
};

const copyResults = () => {
    const text = results.value.map((n, i) => `${i + 1}. ${n}`).join('\n');
    navigator.clipboard.writeText(text).then(() => alert('已複製抽籤結果！'));
};

defineExpose({
    resetAll,
    selectAll,
    invertSelection,
    toggleSelectionFilter,
    selectionFiltered
});

onMounted(loadUsers);
</script>

<style scoped>
.no-scrollbar::-webkit-scrollbar { display: none; }
.no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }

.animate-fade-in { animation: fadeIn 0.3s ease-out; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

.animate-slide-in { animation: slideIn 0.4s ease-out; }
@keyframes slideIn { from { opacity: 0; transform: translateX(30px); } to { opacity: 1; transform: translateX(0); } }

.dot-lg { width: 10px; height: 10px; border-radius: 50%; display: inline-block; animation: bounce 0.5s infinite alternate; }
.dot-lg:nth-child(2) { animation-delay: 0.15s; }
.dot-lg:nth-child(3) { animation-delay: 0.3s; }
@keyframes bounce { from { transform: translateY(0); opacity: 0.4; } to { transform: translateY(-8px); opacity: 1; } }

/* PEN HOLDER STYLES */
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
    background: linear-gradient(90deg, #92400e 0%, #b45309 15%, #92400e 30%, #78350f 50%, #92400e 70%, #b45309 85%, #78350f 100%);
    border-radius: 3px 3px 6px 6px;
    box-shadow: inset 3px 0 8px rgba(255,255,255,0.05), 0 10px 32px rgba(0,0,0,0.4);
    position: relative;
    overflow: hidden;
}
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

/* Flying stick */
.lottery-stick {
    position: absolute;
    bottom: 192px;
    width: 16px;
    display: flex;
    flex-direction: column-reverse;
    align-items: center;
    transform-origin: bottom center;
    animation: stickFly var(--dur, 1.4s) cubic-bezier(0.34, 1.56, 0.64, 1) var(--delay, 0s) infinite;
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
}
.stick-tip {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    margin-top: -3px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.4);
}

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
    animation: namePulse 0.1s ease-in-out;
}
@keyframes namePulse { from { opacity: 0.2; transform: scale(0.9); } to { opacity: 1; transform: scale(1); } }
</style>
