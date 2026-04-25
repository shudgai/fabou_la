<template>
    <div v-if="show" class="fixed inset-0 z-[150] flex flex-col bg-white overflow-hidden animate-fade-in font-sans">
        
        <!-- STEP 1: PERSONNEL SELECTION -->
        <div v-show="currentStep === 1" class="flex flex-col w-full h-full bg-white overflow-hidden relative">
            <!-- Header bar -->
            <div class="border-b border-slate-100 bg-white sticky top-0 z-10 shrink-0 px-2 py-2">
                <div class="flex flex-col w-full gap-1">
                    <!-- First Row: Main Title -->
                    <div class="flex items-center">
                        <div class="app-title font-black leading-tight font-outfit tracking-widest" style="color: #0f172a !important; font-size: 25px !important;">
                            其他專區
                        </div>
                    </div>
                    
                    <!-- Second Row: Subtitle + Back -->
                    <div class="flex items-center justify-between w-full mt-1 px-1">
                        <span class="text-slate-700 font-normal shrink-0 mr-2" style="font-size: 22px !important;">抽順序</span>
                        <div class="flex items-center space-x-2">
                            <div class="flex items-center space-x-1 flex-1 max-w-[80px]">
                                <button ontouchstart="" @click="invertSelection(); activeAction = 'invert'" 
                                    :class="[
                                        'flex-1 py-[6px] px-3 text-[17px] rounded-lg shadow-sm border transition-colors duration-150',
                                        activeAction === 'invert' ? 'bg-blue-600 border-blue-600 text-white' : 'bg-white border-slate-300 text-black active:bg-slate-400'
                                    ]" :style="{ color: activeAction === 'invert' ? '#ffffff !important' : '' }">反選</button>
                            </div>
                            <button @click="resetAll" class="p-1 text-slate-400 hover:text-red-500 shrink-0">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main scrollable selection grid -->
            <div class="flex-1 overflow-y-auto no-scrollbar pb-24">
                <div class="flex items-center justify-between px-3 py-1.5">
                    <span class="font-black text-[15px]" :style="{ color: selectionFiltered ? '#1d4ed8' : '#94a3b8' }">
                        {{ selectionFiltered ? '已確認名單 (可再調整)' : '點選在場人員 (藍色為已選)' }}
                    </span>
                    <span class="text-[15px] font-bold" :style="{ color: pendingNames.length > 0 ? '#1d4ed8' : '#94a3b8' }">已選 {{ pendingNames.length }} 人</span>
                </div>

                <!-- Grid evenly distributed, stretching boxes -->
                <div class="grid grid-cols-4 md:grid-cols-5 px-1 w-full mt-[15px]" style="gap: 4px; background: #ffffff;">
                        <button
                            v-for="user in displayUsers"
                            :key="user.id"
                            @click="togglePending(user.name)"
                            class="flex items-center justify-center font-black text-[17px] transition-all active:scale-95 rounded-md border shadow-sm w-full min-h-[45px]"
                            :style="getPendingStyle(user.name)"
                        >
                            <span class="truncate leading-none">{{ user.name }}</span>
                        </button>
                </div>
            </div>

            <!-- Confirm button -->
            <div class="fixed bottom-[7vh] left-0 right-0 px-4 pb-4 pt-3 bg-white/95 backdrop-blur-sm border-t border-slate-100 z-[200]">
                <button
                    @click="confirmSelection"
                    :disabled="pendingNames.length === 0"
                    class="w-full py-[10px] rounded-2xl font-black text-[17px] transition-all active:scale-[0.98] shadow-lg"
                    :style="{
                        background: pendingNames.length === 0 ? '#93c5fd' : (selectionFiltered ? '#16a34a' : '#1d4ed8'),
                        boxShadow: 'none',
                    }"
                >
                    <span v-if="!selectionFiltered" style="color: #ffffff !important;">確定 (已選 {{ pendingNames.length }} 人)</span>
                    <span v-else style="color: #ffffff !important;">確定 → 進入抽籤設定</span>
                </button>
            </div>
        </div>

        <!-- STEP 2: DRAW CONFIGURATION -->
        <div v-show="currentStep === 2" class="flex flex-col w-full h-full bg-slate-50/10 overflow-hidden animate-slide-in">
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
                            <span class="text-slate-700 font-normal shrink-0 mr-2" style="font-size: 22px !important;">抽籤設定</span>
                        </div>
                        <button ontouchstart="" @click="invertSelection(); activeAction = 'invert'" 
                            :class="[
                                'py-[6px] px-3 text-[17px] rounded-lg shadow-sm border transition-colors duration-150',
                                activeAction === 'invert' ? 'bg-blue-600 border-blue-600 text-white' : 'bg-white border-slate-300 text-black active:bg-slate-400'
                            ]" :style="{ color: activeAction === 'invert' ? '#ffffff !important' : '' }">反選</button>
                    </div>
                </div>
            </div>

            <div class="p-4 flex-1 overflow-y-auto flex flex-col gap-4 max-w-lg mx-auto w-full no-scrollbar pb-48">
                <!-- Selected Summary -->
                <div class="space-y-1.5 px-1 pt-1">
                    <div class="flex items-center justify-between mb-2">
                        <label class="text-[15px] font-black text-slate-400 uppercase tracking-widest">📋 當前待抽名單</label>
                        <span class="text-[17px] font-black text-indigo-600">{{ selectedNames.length }} 人</span>
                    </div>
                    <div class="grid grid-cols-5 gap-y-3 pt-1">
                        <span v-for="name in selectedNames" :key="name" class="text-[17px] font-black text-slate-700 text-center">{{ name }}</span>
                    </div>
                </div>
            </div>

            <!-- Fixed Bottom Action Area -->
            <div class="fixed bottom-[7vh] left-0 right-0 px-4 pb-4 pt-3 bg-white/95 backdrop-blur-md border-t border-slate-100 z-[200]">
                <div class="max-w-lg mx-auto space-y-4">
                    <!-- Config Row -->
                    <div class="flex items-center justify-between px-1">
                        <label class="text-[15px] font-black text-slate-400 uppercase tracking-wider">抽取人數</label>
                        <div class="flex items-center border border-slate-200 rounded-xl overflow-hidden h-12 bg-slate-50/50 w-48 shadow-sm">
                            <button @click="drawCount = Math.max(1, drawCount - 1)" class="w-14 h-full text-white bg-slate-400 text-[20px] font-black shadow-none border-none active:bg-slate-500">−</button>
                            <input 
                                type="number" 
                                v-model.number="drawCount" 
                                @blur="drawCount = Math.max(1, Math.min(selectedNames.length, drawCount || 1))"
                                class="flex-1 text-center text-[20px] font-black text-slate-800 bg-transparent outline-none w-full"
                            >
                            <button @click="drawCount = Math.min(selectedNames.length, drawCount + 1)" class="w-14 h-full text-white bg-slate-400 text-[20px] font-black shadow-none border-none active:bg-slate-500">＋</button>
                        </div>
                    </div>
                    
                    <!-- Action Button -->
                    <button 
                        @click="performDraw"
                        class="w-full py-[12px] rounded-3xl bg-indigo-600 font-black text-[17px] shadow-lg active:scale-[0.98] transition-all flex justify-center items-center"
                    >
                        <span style="color: #ffffff !important;">開始抽籤</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- STEP 3: ANIMATION & RESULTS -->
        <!-- FULLSCREEN LOTTERY ANIMATION -->
        <div v-if="isDrawing" class="fixed inset-0 z-[500] flex flex-col items-center justify-center overflow-hidden" style="background: #fefce8;">
            <div class="absolute inset-0 pointer-events-none">
                <div style="position:absolute;top:30%;left:50%;transform:translate(-50%,-50%);width:400px;height:400px;background:radial-gradient(circle,rgba(251,191,36,0.15) 0%,transparent 70%);border-radius:50%;"></div>
            </div>

            <!-- Bamboo cup + Flying Sticks Container -->
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
        <div v-show="currentStep === 3" class="flex flex-col w-full h-full bg-white overflow-hidden">
            <div class="bg-white border-b border-slate-100 p-4 flex items-center justify-between sticky top-0 z-10">
                <button @click="currentStep = 2" class="text-slate-400 p-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
                <h2 class="text-[19px] font-black text-slate-900 flex-1 text-center">抽籤結果</h2>
                <div class="flex items-center space-x-2">
                    <button @click="performDraw" class="text-[15px] font-black text-white bg-rose-500 px-3 py-[10px] rounded-full shadow-none border-none">重抽</button>
                    <button @click="copyResults" class="text-[15px] font-black text-white bg-emerald-500 px-3 py-[10px] rounded-full shadow-none border-none">複製</button>
                </div>
            </div>

            <div class="flex-1 overflow-y-auto p-4 no-scrollbar">
                <div class="max-w-lg mx-auto space-y-8 pb-32 pt-10">
                    <!-- SINGLE RESULT: CROWN & CENTERED -->
                    <div v-if="results.length === 1" class="flex flex-col items-center justify-center space-y-6 animate-scale-in">
                        <div class="relative">
                            <span class="text-[64px] filter drop-shadow-lg">👑</span>
                            <div class="absolute -bottom-2 left-1/2 -translate-x-1/2 w-24 h-2 bg-indigo-500/10 blur-xl rounded-full"></div>
                        </div>
                        <h3 class="text-[32px] font-black text-white bg-[#1d4ed8] px-8 py-4 rounded-2xl shadow-xl mb-4">{{ results[0] }}</h3>
                        <div class="flex items-center space-x-2 text-indigo-300">
                            <div class="h-[2px] w-8 bg-indigo-100"></div>
                            <span class="text-[15px] font-black uppercase tracking-widest">唯一幸運兒</span>
                            <div class="h-[2px] w-8 bg-indigo-100"></div>
                        </div>
                    </div>

                    <!-- MULTIPLE RESULTS: CENTERED & INDEXED -->
                    <div v-else class="flex flex-col items-center space-y-2">
                        <div v-for="(name, idx) in results" :key="'res'+idx" 
                            class="w-full py-3 flex flex-col items-center rounded-xl bg-[#3b82f6] border border-white/20 animate-slide-in">
                            <span class="text-[13px] font-black text-white/70 mb-1">#{{ idx + 1 }}</span>
                            <span class="text-[19px] font-black text-white" style="text-shadow: 1px 1px 2px rgba(0,0,0,0.3)">{{ name }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- GLOBAL BOTTOM NAVIGATION -->
        <mobile-navbar 
            v-if="!isDrawing"
            :can-back="true"
            :show-action="true"
            :action-disabled="true"
            :can-search="false"
            :can-more="false"
            @back="handleBack"
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

const STORAGE_KEY = 'fabou_lucky_draw_session';

const displayUsers = computed(() => {
    if (!users.value || !Array.isArray(users.value)) return [];
    const pending = pendingNames.value || [];
    if (selectionFiltered.value) {
        return users.value.filter(u => u && u.name && pending.includes(u.name.trim()));
    }
    return users.value.filter(u => u && u.name);
});

const getPendingStyle = (name) => {
    const isSelected = pendingNames.value.includes(name);
    if (isSelected) {
        return {
            backgroundColor: '#bfdbfe', // Light Blue for Selected
            color: '#000000',
            border: '2px solid #93c5fd'
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
    const idx = pendingNames.value.indexOf(name);
    if (idx === -1) {
        pendingNames.value.push(name);
    } else {
        pendingNames.value.splice(idx, 1);
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
    selectedNames.value = [...pendingNames.value].sort((a, b) => {
        const idxA = users.value.findIndex(u => u.name === a);
        const idxB = users.value.findIndex(u => u.name === b);
        return idxA - idxB;
    });
    drawCount.value = Math.min(1, selectedNames.value.length);
    currentStep.value = 2;
};

const handleBack = () => {
    if (hasResult.value) {
        hasResult.value = false;
        currentStep.value = 2;
        return;
    }
    if (currentStep.value === 2) {
        currentStep.value = 1;
        return;
    }
    emit('close');
};

const resetAll = () => {
    if (!confirm('確定要清空所有進度嗎？')) return;
    pendingNames.value = [];
    selectedNames.value = [];
    selectionFiltered.value = false;
    currentStep.value = 1;
    hasResult.value = false;
    sessionStorage.removeItem(STORAGE_KEY);
};

const saveToSession = () => {
    const data = {
        pendingNames: pendingNames.value,
        selectedNames: selectedNames.value,
        selectionFiltered: selectionFiltered.value,
        currentStep: currentStep.value,
        drawCount: drawCount.value
    };
    sessionStorage.setItem(STORAGE_KEY, JSON.stringify(data));
};

watch([pendingNames, selectedNames, selectionFiltered, currentStep, drawCount], saveToSession, { deep: true });

const loadUsers = async () => {
    try {
        const res = await axios.get('/api/dharma-names-list');
        users.value = res.data;
        const draft = sessionStorage.getItem(STORAGE_KEY);
        if (draft) {
            const parsed = JSON.parse(draft);
            pendingNames.value = parsed.pendingNames || [];
            selectedNames.value = parsed.selectedNames || [];
            selectionFiltered.value = parsed.selectionFiltered || false;
            currentStep.value = parsed.currentStep || 1;
            drawCount.value = parsed.drawCount || 1;
        }
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

const performDraw = () => {
    if (selectedNames.value.length === 0) return;
    
    isDrawing.value = true;
    hasResult.value = false;
    buildFlyingSticks([...selectedNames.value]);

    const pool = [...selectedNames.value];
    let idx = 0;
    lotteryInterval = setInterval(() => {
        idx = (idx + 1) % pool.length;
        lotteryDisplayNames.value = [pool[idx]];
    }, 80);

    setTimeout(() => {
        clearInterval(lotteryInterval);
        const shuffled = [...pool].sort(() => Math.random() - 0.5);
        results.value = shuffled.slice(0, Math.min(drawCount.value, pool.length));
        isDrawing.value = false;
        hasResult.value = true;
        currentStep.value = 3;
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
