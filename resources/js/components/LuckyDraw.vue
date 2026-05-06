<template>
    <div v-if="show" class="fixed inset-0 h-[100dvh] md:relative md:h-full md:w-full z-[500] md:z-auto bg-white overflow-hidden animate-fade-in font-sans">
        
        <!-- STEP 1 & 2: PERSONNEL SELECTION -->
        <div v-if="currentStep === 1 || currentStep === 2" class="fixed inset-0 md:absolute md:inset-0 flex flex-col bg-white overflow-hidden z-[150]">
            <div class="animate-fade-in flex flex-col h-full overflow-hidden">
                <!-- Header bar -->
                <div class="border-b border-slate-300 bg-white sticky top-0 z-[110] w-full md:pt-[60px]" style="padding: 8px 2px; min-height: 52px;">
                    <div class="flex flex-col w-full gap-1">
                        <div class="flex items-center justify-between w-full">
                            <!-- Consolidated Title Row -->
                            <div class="flex items-center flex-1 min-w-0">
                                <div class="app-title leading-tight font-outfit tracking-widest shrink-0" style="color: #0f172a !important; font-size: 32px !important;">
                                    其他專區
                                </div>
                                <div class="flex items-center ml-3 min-w-0">
                                    <span class="text-slate-500 truncate" style="font-size: 25px !important; font-weight: 400 !important;">
                                        {{ lotteryMode === true ? '回合抽籤' : '抽順序' }}
                                    </span>
                                </div>
                            </div>

                            <!-- Right Side Functional Controls -->
                            <div class="flex items-center ml-2 shrink-0 space-x-2">
                                <button @click="invertSelection()" 
                                    class="px-[6px] py-1 bg-indigo-50 text-indigo-600 rounded-lg text-[16px] font-black active:scale-95 transition-all shadow-sm border border-indigo-100 whitespace-nowrap"
                                    style="font-size: 16px !important;">
                                    反選
                                </button>
                                <button @click="resetAll" class="w-10 h-10 bg-red-50 text-red-500 rounded-xl flex items-center justify-center active:scale-95 transition-all shrink-0">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Scrollable Content -->
                <div class="flex-1 overflow-y-auto custom-scrollbar pb-64 animate-fade-in w-full">
                    <div class="p-4 space-y-6">
                        <div class="flex items-center flex-wrap gap-2 mb-2">
                            <label class="text-[18px] font-black text-slate-800 shrink-0">滑動游標選取人員</label>
                            <div class="flex items-center bg-slate-50 rounded-lg px-2 py-1 border border-slate-100">
                                <span class="text-[11px] font-bold text-slate-300 whitespace-nowrap shrink-0 mr-1">手動:</span>
                                <input v-model="manualName" @keyup.enter="addManualName" class="w-16 bg-transparent border-none text-[12px] font-bold h-6 outline-none">
                                <button @click="addManualName" class="text-blue-500 font-black text-[18px] ml-1">＋</button>
                            </div>
                            <div class="ml-auto text-slate-800 text-[18px] font-black">
                                {{ pendingNames.length }} 人
                            </div>
                        </div>
                        
                        <!-- Grid -->
                        <div class="grid grid-cols-4 md:grid-cols-5 gap-2" @mouseleave="stopDrag" @touchmove="handleTouchMove">
                            <button v-for="user in displayUsers" :key="user.id"
                                @mousedown="startDrag(user.name)"
                                @mouseenter="onDragEnter(user.name)"
                                @mouseup="stopDrag"
                                @touchstart="handleTouchStart($event, user.name)"
                                @touchend="stopDrag"
                                :data-name="user.name"
                                class="dharma-btn h-14 flex items-center justify-center rounded-xl border-2 font-black text-[17px] transition-all relative"
                                :style="getPendingStyle(user.name)">
                                <span class="pointer-events-none flex items-center">
                                    <span v-if="fixedParticipants.includes(user.name)" class="mr-1 text-white">✔</span>
                                    {{ user.name }}
                                    <span v-if="pendingNames.includes(user.name) && !fixedParticipants.includes(user.name)" class="absolute -top-2 -right-2 w-5 h-5 bg-indigo-500 text-white rounded-full text-[10px] flex items-center justify-center border border-white">
                                        {{ pendingNames.indexOf(user.name) + 1 }}
                                    </span>
                                </span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Bottom Button Area -->
                <div class="px-4 pb-[85px] pt-2 border-t border-slate-50 bg-white/95 backdrop-blur-sm shrink-0">
                    <div class="w-full space-y-2">
                        <button @click="confirmSelection" 
                                :disabled="pendingNames.length === 0" 
                                class="w-full py-4 rounded-2xl font-black text-[17px] shadow-lg active:scale-95 disabled:opacity-50 transition-all bg-blue-600 text-white"
                                style="color: white !important;">
                            確定名單 (共 {{ pendingNames.length }} 人) → 下一步
                        </button>
                        <button @click="emit('close')" class="w-full py-2 text-slate-400 font-bold text-[13px] flex items-center justify-center">
                            返回上一步
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- STEP 3: DRAW CONFIGURATION -->
        <div v-if="currentStep === 3" class="fixed inset-0 md:absolute md:inset-0 flex flex-col bg-white overflow-hidden z-[150]">
            <div class="animate-slide-in flex flex-col h-full overflow-hidden">
                <div class="border-b border-slate-100 p-4 bg-white sticky top-0 z-10 w-full md:pt-[60px]">
                    <div class="flex items-center">
                        <button @click="currentStep = 2" class="p-2 -ml-2 text-slate-400 mr-2 active:scale-90 transition-all">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </button>
                        <h2 class="text-[24px] font-black text-slate-800">{{ lotteryMode ? '回合抽籤 - 本輪挑選' : '步驟 3：抽籤設定' }}</h2>
                    </div>
                </div>
                <div class="flex-1 overflow-y-auto p-4 space-y-8">
                    <div v-if="!lotteryMode" class="flex justify-center">
                        <div class="bg-indigo-50 px-8 py-4 rounded-3xl border border-indigo-100 flex flex-col items-center">
                            <span class="text-indigo-400 text-[13px] font-black uppercase">總參與人數</span>
                            <span class="text-indigo-600 text-[40px] font-black">{{ selectedNames.length }} 人</span>
                        </div>
                    </div>

                    <div v-if="lotteryMode" class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-[17px] font-black text-slate-800">1. 從基礎名單點選本輪人員</span>
                            <span class="text-[17px] font-black text-slate-800">{{ roundParticipants.length }} 人</span>
                        </div>
                        <div class="flex gap-2">
                            <button @click="roundParticipants = [...selectedNames]" class="px-4 py-1.5 bg-indigo-50 text-indigo-600 rounded-lg font-black text-[15px]">全選</button>
                            <button @click="roundParticipants = []" class="px-4 py-1.5 bg-slate-100 text-slate-400 rounded-lg font-black text-[15px]">清空</button>
                        </div>
                        <div class="grid grid-cols-4 md:grid-cols-5 gap-2 pb-32">
                            <button v-for="name in selectedNames" :key="'round'+name" @click="toggleRoundParticipant(name)" 
                                class="h-14 rounded-xl border-2 font-black text-[17px] transition-all"
                                :style="getStep2RoundStyle(name)">
                                {{ name }}
                            </button>
                        </div>
                    </div>
                    <div v-else class="space-y-6">
                        <div v-if="fixedParticipants.length > 0">
                            <label class="text-rose-500 font-black text-[15px] uppercase">固定優先順序</label>
                            <div class="flex flex-wrap gap-2">
                                <div v-for="(name, i) in fixedParticipants" :key="i" class="bg-rose-50 text-rose-700 px-3 py-2 rounded-lg font-black">{{ i+1 }}. {{ name }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="px-4 pb-[60px] pt-4 border-t border-slate-100 space-y-4 bg-white/95 backdrop-blur-sm shrink-0">
                    <div class="flex items-center justify-between">
                        <span class="text-[19px] font-black text-slate-800">抽取人數</span>
                        <div class="flex items-center bg-slate-100 rounded-xl overflow-hidden border border-slate-200">
                            <button @click="drawCount = Math.max(1, drawCount - 1)" class="w-12 h-12 flex items-center justify-center bg-slate-200 text-slate-600 font-black text-[24px]">-</button>
                            <input v-model.number="drawCount" class="w-16 h-12 text-center font-black text-[24px] bg-transparent outline-none">
                            <button @click="drawCount = Math.min(lotteryMode ? roundParticipants.length : selectedNames.length, drawCount + 1)" 
                                    class="w-12 h-12 flex items-center justify-center bg-slate-500 text-white font-black text-[24px]">+</button>
                        </div>
                    </div>
                    <button @click="lotteryMode === true ? performRoundDraw() : performDraw()" 
                            class="w-full py-4 rounded-3xl font-black text-[19px] shadow-lg active:scale-95 transition-all"
                            :class="lotteryMode ? 'bg-emerald-100 text-emerald-600' : 'bg-indigo-600 text-white'"
                            :style="!lotteryMode ? 'color: white !important;' : ''">
                        {{ lotteryMode ? '開始本輪抽籤' : '開始隨機抽籤' }}
                    </button>
                </div>
            </div>
        </div>
        <!-- FULLSCREEN LOTTERY ANIMATION -->
        <div v-if="isDrawing" class="fixed inset-0 z-[2000] flex flex-col items-center justify-center overflow-hidden" style="background: #fefce8;">
            <!-- Ambient glow -->
            <div class="absolute inset-0 pointer-events-none">
                <div style="position:absolute;top:30%;left:50%;transform:translate(-50%,-50%);width:400px;height:400px;background:radial-gradient(circle,rgba(251,191,36,0.15) 0%,transparent 70%);border-radius:50%;"></div>
            </div>

            <!-- Bamboo cup + Flying Sticks Container -->
            <div class="lottery-cup-container" style="transform: translateY(10vh) scale(0.8);">
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
            <div class="absolute top-[3vh] left-0 right-0 flex flex-col items-center space-y-2">
                <p class="text-[32px] font-black tracking-widest text-amber-900">隨機抽籤中</p>
                <div class="flex gap-2">
                    <span class="dot-lg w-3 h-3 rounded-full" style="background:#b45309;"></span>
                    <span class="dot-lg w-3 h-3 rounded-full" style="background:#d97706;"></span>
                    <span class="dot-lg w-3 h-3 rounded-full" style="background:#f59e0b;"></span>
                </div>
            </div>
        </div>
        <!-- STEP 4: RESULTS -->
        <div v-if="currentStep === 4" class="fixed inset-0 md:absolute md:inset-0 flex flex-col bg-white overflow-hidden z-[1000]">
            <div class="animate-fade-in flex flex-col h-full overflow-hidden">
                <div class="p-4 border-b border-slate-100 flex flex-col space-y-2">
                    <div class="flex items-center">
                        <button @click="handleBack" class="p-2 -ml-2 text-slate-400 active:scale-90 transition-all mr-1">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" /></svg>
                        </button>
                        <h2 class="text-[19px] font-black mx-auto">抽籤結果 ({{ results.length }}人)</h2>
                    </div>
                </div>
                <div class="flex-1 overflow-y-auto p-4">
                    <div v-if="results.length === 1" class="flex flex-col items-center py-4">
                        <span class="text-[60px] -mb-2 relative z-10">👑</span>
                        <div class="bg-yellow-400 text-red-600 text-[36px] font-black px-12 py-6 rounded-3xl shadow-xl border-4 border-yellow-500 animate-bounce">
                            1. {{ results[0] }}
                        </div>
                    </div>
                    <div v-else class="columns-2 gap-2 space-y-2">
                        <div v-for="(name, idx) in results" :key="idx" class="break-inside-avoid py-2 px-3 bg-slate-50 rounded-xl border border-slate-100 font-black flex items-center min-w-0 mb-2">
                            <span class="text-indigo-400 text-[13px] mr-1.5 w-5 shrink-0">{{ idx + 1 }}.</span>
                            <span class="text-[17px] truncate">{{ name }}</span>
                        </div>
                    </div>
                </div>

                <!-- Bottom Button Area (Fixed above navigation) -->
                <div class="px-4 pb-[60px] pt-2 border-t border-slate-50 bg-white/95 backdrop-blur-sm shrink-0">
                    <div class="flex gap-2">
                        <button v-if="lotteryMode" @click="handleNextRound" class="flex-1 py-3 bg-emerald-600 text-white rounded-xl font-black text-[15px]" style="color: white !important;">新回合</button>
                        <button @click="results = []; currentStep = 2" class="flex-1 py-3 bg-slate-500 text-white rounded-xl font-black text-[15px]" style="color: white !important;">重選</button>
                        <button @click="copyResults" class="flex-1 py-3 bg-blue-600 text-white rounded-xl font-black text-[15px]" style="color: white !important;">複製</button>
                        <button @click="saveResults" class="flex-1 py-3 bg-indigo-600 text-white rounded-xl font-black text-[15px]" style="color: white !important;">儲存</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- NAVIGATION -->
        <mobile-navbar v-if="!isDrawing" :can-back="true" :can-home="true" @back="handleBack" @home="$emit('close')" />

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
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import axios from 'axios';
import MobileNavbar from './MobileNavbar.vue';

const props = defineProps({
    show: Boolean,
    initialMode: { type: Boolean, default: null },
    folderId: Number
});

const emit = defineEmits(['close', 'saved']);

const users = ref([]);
const persistentToast = ref(null);
const pendingNames = ref([]);
const selectedNames = ref([]);
const fixedParticipants = ref([]);
const roundParticipants = ref([]);
const results = ref([]);
const currentStep = ref(1);
const drawCount = ref(1);
const isDrawing = ref(false);
const hasResult = ref(false);
const lotteryMode = ref(props.initialMode);
const manualName = ref('');
const DRAW_DURATION = 3000;
const currentType = ref('');
const lotteryDisplayNames = ref([]);
const flyingSticks = ref([]);

watch(() => props.show, (val) => {
    if (val) {
        loadUsers();
        resetAll(true);
        lotteryMode.value = props.initialMode;
    }
});

const loadUsers = async () => {
    try {
        const res = await axios.get('/api/dharma-names-list');
        users.value = res.data;
    } catch (e) { console.error(e); }
};

const displayUsers = computed(() => {
    if (!lotteryMode.value && currentStep.value === 2) {
        return users.value.filter(u => !fixedParticipants.value.includes(u.name));
    }
    return users.value;
});

const togglePending = (name) => {
    if (currentStep.value === 1) {
        const idx = fixedParticipants.value.indexOf(name);
        if (idx > -1) {
            fixedParticipants.value.splice(idx, 1);
            pendingNames.value = pendingNames.value.filter(n => n !== name);
        } else {
            fixedParticipants.value.push(name);
            if (!pendingNames.value.includes(name)) pendingNames.value.push(name);
        }
    } else if (currentStep.value === 2) {
        if (fixedParticipants.value.includes(name)) return;
        const idx = pendingNames.value.indexOf(name);
        if (idx > -1) pendingNames.value.splice(idx, 1);
        else pendingNames.value.push(name);
    }
};

const getPendingStyle = (name) => {
    const isSelected = pendingNames.value.includes(name);
    if (isSelected) return { background: '#e0f2fe', color: 'black !important', borderColor: '#e0f2fe', borderWidth: '1px' };
    return { background: 'white', color: 'black', borderColor: '#e2e8f0', borderWidth: '1px' };
};

const confirmSelection = () => {
    if (lotteryMode.value === true) {
        selectedNames.value = [...pendingNames.value];
        drawCount.value = 1;
        currentStep.value = 3;
        return;
    }

    if (currentStep.value === 1) {
        currentStep.value = 2;
    } else if (currentStep.value === 2) {
        selectedNames.value = [...pendingNames.value];
        drawCount.value = selectedNames.value.length;
        currentStep.value = 3;
    }
};

const handleBack = () => {
    if (currentStep.value > 1) currentStep.value--;
    else emit('close');
};

const resetAll = (silent = false) => {
    if (silent) {
        executeReset();
        return;
    }
    persistentToast.value = { msg: '確定要清空目前的所有選擇與記錄嗎？', type: 'clear' };
};

const executeReset = () => {
    pendingNames.value = [];
    fixedParticipants.value = [];
    selectedNames.value = [];
    roundParticipants.value = [];
    currentStep.value = 1;
    results.value = [];
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
    const active = roundParticipants.value.includes(name);
    if (active) return { background: '#2563eb', color: '#ffffff !important', borderColor: '#2563eb', borderWidth: '1px' };
    return { background: 'white', color: 'black', borderColor: '#e2e8f0', borderWidth: '1px' };
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
        const rest = pool.filter(n => !fixed.includes(n)).sort(() => Math.random() - 0.5);
        const full = [...fixed, ...rest];
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

const copyResults = () => {
    const text = results.value.map((n, i) => `${i+1}. ${n}`).join('\n');
    navigator.clipboard.writeText(text).then(() => alert('已複製'));
};

const saveResults = async () => {
    if (!props.folderId) return;
    try {
        await axios.post(`/other-folders/${props.folderId}/records`, {
            title: `抽籤結果 - ${new Date().toLocaleString()}`,
            content: results.value.map((n, i) => `${i+1}. ${n}`).join('\n'),
            record_date: new Date().toISOString().split('T')[0]
        });
        alert('已儲存');
        emit('saved');
    } catch (e) { console.error(e); }
};

const handleReselect = () => {
    resetAll(true);
    currentStep.value = 1;
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
        if (currentStep.value === 1) {
            if (!fixedParticipants.value.includes(name)) fixedParticipants.value.push(name);
            if (!pendingNames.value.includes(name)) pendingNames.value.push(name);
        } else {
            if (!fixedParticipants.value.includes(name) && !pendingNames.value.includes(name)) pendingNames.value.push(name);
        }
    } else if (dragSelectionType.value === 'remove' && isSelected) {
        if (currentStep.value === 1) {
            fixedParticipants.value = fixedParticipants.value.filter(n => n !== name);
            pendingNames.value = pendingNames.value.filter(n => n !== name);
        } else {
            if (!fixedParticipants.value.includes(name)) {
                pendingNames.value = pendingNames.value.filter(n => n !== name);
            }
        }
    }
};

const stopDrag = () => {
    isDragging.value = false;
    lastTouchedName.value = null;
    window.removeEventListener('mouseup', stopDrag);
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

onMounted(loadUsers);
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
