<template>
    <div v-if="show" class="fixed inset-0 z-[150] flex flex-col bg-white overflow-hidden animate-fade-in font-sans">
        <!-- Dashboard Header (Only shown during Setup) -->
        <div v-if="!isDrawing && !hasResult" class="h-[60px] border-b border-slate-100 flex items-center justify-center px-4 bg-white sticky top-0 z-10 shrink-0 relative">
            <button @click="$emit('close')" class="absolute left-4 text-slate-400 p-2 active:scale-90 transition-transform">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" /></svg>
            </button>
            <h3 class="text-[22px] font-black text-slate-900 font-outfit uppercase tracking-widest text-center">
                抽籤筒
            </h3>
            <button @click="resetAll" class="absolute right-4 text-slate-400 hover:text-red-500 p-2 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" /></svg>
            </button>
        </div>

        <!-- Main Content -->
        <div class="flex-1 flex overflow-hidden">
            
            <!-- STEP 1: INPUT PHASE -->
            <div v-show="!hasResult && !isDrawing" class="flex w-full h-full overflow-hidden animate-fade-in">
                <!-- Left: Dharma Name Table (50% Split) -->
                <div class="w-1/2 border-r border-slate-100 flex flex-col h-full bg-white pl-1.5">
                    <div class="px-2 py-2 border-b border-slate-50 space-y-1 bg-white relative z-10">
                        <!-- Row 1: Title & Global Action -->
                        <div class="flex items-center justify-between">
                            <h4 class="text-[20px] font-black text-slate-900">法號表</h4>
                            <button @click="selectAll" class="text-[16px] text-indigo-600 font-black px-4 py-1.5 bg-indigo-50 border border-indigo-100 rounded-xl active:scale-95 transition-transform shadow-sm">全選</button>
                        </div>
                        <!-- Row 2: Operation Modes -->
                        <div class="flex items-center justify-between">
                            <span class="text-[14px] font-black text-slate-400 tracking-tighter">{{ isInverseMode ? '選不在場' : '選在場' }}</span>
                            <button @click="isInverseMode = !isInverseMode" 
                                :class="['flex items-center px-4 py-2 rounded-full border-2 transition-all text-[15px] font-black shrink-0 shadow-sm', 
                                         isInverseMode ? 'bg-rose-50 border-rose-200 text-rose-600' : 'bg-emerald-50 border-emerald-200 text-emerald-700']">
                                {{ isInverseMode ? '反向' : '正向' }}
                            </button>
                        </div>
                    </div>
                    <div class="flex-1 overflow-y-auto p-1 custom-scrollbar">
                        <div class="grid grid-cols-3 gap-y-3 gap-x-1">
                            <button v-for="user in users" :key="user.id"
                                @click="toggleSelect(user.name)"
                                class="h-10 flex items-center justify-center transition-all active:scale-95 border-b border-slate-50">
                                <span :class="['text-[20px] font-normal whitespace-nowrap transition-colors duration-200',
                                             isItemSelected(user.name) ? (isInverseMode ? 'text-rose-500 font-black' : 'text-indigo-500 font-black') : 'text-slate-800']">
                                    {{ user.name }}
                                </span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Right: Pool Preview Only (50% Split) -->
                <div class="w-1/2 flex flex-col h-full bg-slate-50/10">
                    <div class="p-3 border-b border-slate-100 bg-white flex items-center justify-between">
                        <h4 class="text-[22px] font-black text-slate-900">抽籤池</h4>
                        <button @click="clearAll" class="text-[14px] text-rose-500 font-bold hover:underline">清空</button>
                    </div>
                    <div class="flex-1 flex flex-col p-3 space-y-3 overflow-hidden translate-y-[-15px]">
                        <!-- Visual Roster (Now Full Height of Pool Area) -->
                        <div class="flex-1 shrink-0 flex flex-col min-h-0">
                            <label class="text-[16px] font-black text-slate-400 uppercase tracking-widest mb-1 text-center">名單預覽 (並排顯示)</label>
                            <div class="flex-1 bg-white border border-slate-100 rounded-2xl p-4 overflow-y-auto custom-scrollbar">
                                <div v-if="selectedNames.length === 0" class="h-full flex flex-col items-center justify-center text-slate-200">
                                    <p class="text-[16px] font-bold uppercase tracking-widest text-center">待選取人員</p>
                                </div>
                                <div class="grid grid-cols-3 gap-x-1 gap-y-0.5 text-center">
                                    <div v-for="name in selectedNames" :key="'pool'+name" class="flex justify-center border-b border-slate-100 pb-0.5">
                                        <span @click="toggleSelect(name)" 
                                            class="text-[20px] font-normal text-indigo-600 cursor-pointer hover:text-red-500 transition-colors whitespace-nowrap">
                                            {{ name }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Settings Area (Bottom) -->
                        <div class="h-auto flex-none flex flex-col justify-end space-y-3 py-3 border-t border-slate-100 px-2 bg-white">
                            <div class="flex items-center justify-between bg-slate-50/50 p-2 rounded-xl border border-slate-100">
                                <label class="text-[17px] font-black text-slate-400 uppercase tracking-widest">抽取人數</label>
                                <select v-model="drawCount" class="w-32 h-10 bg-white border border-slate-200 rounded-lg px-2 text-[18px] font-black text-slate-900 focus:ring-2 focus:ring-indigo-100 outline-none shadow-sm text-center">
                                    <option v-for="n in 10" :key="n" :value="n">{{ n }} 人</option>
                                    <option :value="999">全部</option>
                                </select>
                            </div>
                            <button 
                                @click="performDraw"
                                :disabled="!isValid"
                                :class="['w-[92%] mx-auto py-[15px] rounded-2xl font-black text-[20px] tracking-[4px] transition-all shadow-xl active:scale-95 flex items-center justify-center leading-none whitespace-nowrap',
                                         isValid ? 'bg-indigo-600 text-white hover:bg-indigo-700 shadow-indigo-200' : 'bg-slate-100 text-slate-300 cursor-not-allowed']"
                            >
                                開始抽籤
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- DRAWING ANIMATION PHASE (Visual Tube - Enlarged to 80%) -->
            <div v-show="isDrawing" class="w-full h-full flex flex-col items-center justify-center animate-fade-in bg-white overflow-hidden p-6">
                <div class="relative w-full h-[80%] flex flex-col items-center justify-center select-none">
                    <!-- Shaking Tube Container - Centered Alignment -->
                    <div class="relative w-full max-w-sm flex flex-col items-center animate-shake justify-center">
                        
                        <!-- The Sticks inside (Wood Color) -->
                        <div class="flex justify-center space-x-[-20px] mb-[-60px] z-0">
                            <div v-for="i in 15" :key="'stick'+i" 
                                class="w-8 h-48 bg-[#D3B68A] border-x border-[#A67C52] rounded-t-xl shadow-sm relative animate-stick-bounce overflow-hidden"
                                :style="{ animationDelay: (i * 0.08) + 's', transform: `rotate(${(i - 8) * 3}deg)` }">
                                <!-- Wood Grain Pattern -->
                                <div class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/wood-pattern.png')]"></div>
                                <!-- Clear Dharma Name (Randomly selected from pool) -->
                                <div class="absolute inset-0 flex flex-col items-center justify-center px-1">
                                    <span class="text-[14px] font-black leading-[1.1] text-[#7C5F3E] [writing-mode:vertical-rl] tracking-tighter filter drop-shadow-sm">
                                        {{ displaySticks[i % displaySticks.length] || '法號' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- The Tube (CSS Cylinder - High Quality) -->
                        <div class="relative w-56 h-72 z-10 filter drop-shadow-2xl">
                            <!-- Tube Shadow/Depth -->
                            <div class="absolute inset-0 bg-[#8B5E3C] rounded-3xl shadow-inner border-t-[15px] border-[#654321] overflow-hidden">
                                <div class="absolute inset-0 opacity-20 bg-[url('https://www.transparenttextures.com/patterns/wood-pattern.png')]"></div>
                            </div>
                            <!-- Tube Front Highlight -->
                            <div class="absolute inset-4 top-8 right-8 bottom-4 left-8 bg-white/10 rounded-3xl blur-xl"></div>
                            <!-- Tube Decoration Labels -->
                            <div class="absolute inset-0 flex items-center justify-center">
                                <div class="border-4 border-[#654321]/40 p-4 rounded-2xl flex flex-col items-center bg-[#8B5E3C]/20 backdrop-blur-sm">
                                    <div class="flex flex-col items-center space-y-[5px] text-[#F5F5F5] font-black text-[32px] drop-shadow-md">
                                        <span>抽</span>
                                        <span>籤</span>
                                        <span>筒</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- STEP 2: RESULT PHASE (Centered List) -->
            <div v-show="hasResult && !isDrawing" class="w-full h-full p-4 overflow-y-auto custom-scrollbar animate-fade-in bg-white">
                <div class="max-w-2xl mx-auto space-y-10 py-6 text-center">
                    
                    <div v-if="results.length > 0" class="flex flex-col items-center space-y-12">
                        <!-- Sequential Result Sticks Display (5 Per Row) -->
                        <div class="w-full grid grid-cols-5 gap-3 py-6 px-2 overflow-y-auto custom-scrollbar max-h-[50vh]">
                            <div v-for="(name, index) in results" :key="'res-stick'+index" 
                                class="animate-stick-reveal relative flex flex-col items-center"
                                :style="{ animationDelay: (index * 0.15) + 's' }">
                                
                                <!-- Result Stick Visual (Highly Compacted - 1/2 Smaller) -->
                                <div class="w-full max-w-[35px] bg-[#D3B68A] border border-[#8B5E3C] rounded-md shadow-md flex flex-col items-center relative overflow-hidden transform hover:-translate-y-1 transition-transform h-36">
                                    <!-- Wood Grain -->
                                    <div class="absolute inset-0 opacity-20 bg-[url('https://www.transparenttextures.com/patterns/wood-pattern.png')]"></div>
                                    
                                    <!-- RED Dharma Name (Centered Legibility) -->
                                    <div class="flex-1 flex flex-col items-center justify-center p-1 z-10">
                                        <span class="text-[15px] font-black leading-[1.1] text-red-600 [writing-mode:vertical-rl] tracking-[2px] filter drop-shadow-sm brightness-110">
                                            {{ name }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Summary List Below (Quick Read - Delayed 1.5s) -->
                        <div v-if="showResultList" class="w-full bg-slate-50/50 rounded-3xl p-6 border border-slate-100 animate-fade-in">
                            <h6 class="text-[16px] font-black text-slate-400 capitalize tracking-widest mb-4">名單總覽</h6>
                            <div class="grid grid-cols-2 gap-4">
                                <div v-for="(name, index) in results" :key="'list'+index" class="flex items-center space-x-2 justify-center">
                                    <span class="text-slate-300 font-mono text-[15px]">#{{ index + 1 }}</span>
                                    <span class="text-[18px] font-normal text-slate-900">{{ name }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Action Header (Centered) -->
                        <div class="space-y-6 pt-4">
                            <button @click="copyResults" class="text-indigo-600 font-black text-[16px] flex items-center bg-white px-8 py-3 rounded-full active:scale-95 border-2 border-indigo-50 shadow-md hover:shadow-lg transition-all mx-auto">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-2M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" /></svg>
                                點擊複製名單
                            </button>
                        </div>
                    </div>

                    <div class="pb-24">
                        <button 
                            @click="hasResult = false"
                            class="w-full h-14 bg-slate-100 text-slate-500 rounded-2xl font-black text-[16px] active:scale-95 transition-all"
                        >
                            返回修改名單
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

const props = defineProps({
    show: Boolean
});

const emit = defineEmits(['close']);

const users = ref([]);
const inputRaw = ref('');
const selectedOnList = ref([]); // Who IS present (Normal Mode)
const excludedOnList = ref([]); // Who IS absent (Inverse Mode)
const drawCount = ref(1);
const isDrawing = ref(false);
const hasResult = ref(false);
const showResultList = ref(false);
const isInverseMode = ref(false);
const results = ref([]);

// Computed Pool of strictly "People Present"
const selectedNames = computed(() => {
    // Note: inputRaw is no longer visually present but logic preserved for future-proofing
    const rawList = inputRaw.value.split(/[,\n\s]+/).map(n => n.trim()).filter(n => n.length > 0);
    
    if (!isInverseMode.value) {
        // Normal Mode: Textbox + Whoever is specifically selected
        return [...new Set([...rawList, ...selectedOnList.value])];
    } else {
        // Inverse Mode: Textbox + (All DB users MINUS those marked as absent)
        const allDbNames = users.value.map(u => u.name);
        const presentFromDb = allDbNames.filter(n => !excludedOnList.value.includes(n));
        return [...new Set([...rawList, ...presentFromDb])];
    }
});

const selectAll = () => {
    if (isInverseMode.value) {
        excludedOnList.value = [];
    } else {
        selectedOnList.value = users.value.map(u => u.name);
    }
};

const clearAll = () => {
    inputRaw.value = '';
    if (isInverseMode.value) {
        // In inverse mode, clearing the pool means marking everyone as ABSENT
        excludedOnList.value = users.value.map(u => u.name);
    } else {
        // In normal mode, simply empty the selection
        selectedOnList.value = [];
    }
};

const displaySticks = ref([]);

// Randomized names for the shake animation
const shuffleSticks = () => {
    if (selectedNames.value.length === 0) {
        displaySticks.value = [];
    } else {
        // Shuffle the pool and take up to 20 names for the tube
        displaySticks.value = [...selectedNames.value]
            .sort(() => Math.random() - 0.5);
    }
};

const isItemSelected = (name) => {
    if (!isInverseMode.value) return selectedOnList.value.includes(name);
    return excludedOnList.value.includes(name);
};

const resetAll = () => {
    inputRaw.value = '';
    selectedOnList.value = [];
    excludedOnList.value = [];
    results.value = [];
    hasResult.value = false;
    showResultList.value = false;
};

const toggleSelect = (name) => {
    if (!isInverseMode.value) {
        if (selectedOnList.value.includes(name)) {
            selectedOnList.value = selectedOnList.value.filter(n => n !== name);
        } else {
            selectedOnList.value.push(name);
        }
    } else {
        if (excludedOnList.value.includes(name)) {
            excludedOnList.value = excludedOnList.value.filter(n => n !== name);
        } else {
            excludedOnList.value.push(name);
        }
    }
};

const loadUsers = async () => {
    try {
        const res = await axios.get('/api/dharma-names-list');
        users.value = res.data;
    } catch (e) { console.error(e); }
};

const performDraw = () => {
    if (selectedNames.value.length === 0) return;
    
    // Refresh the animation sticks every single time
    shuffleSticks();
    
    isDrawing.value = true;
    showResultList.value = false;
    
    setTimeout(() => {
        const pool = [...selectedNames.value];
        const count = Math.min(drawCount.value, pool.length);
        const shuffled = pool.sort(() => Math.random() - 0.5);
        results.value = shuffled.slice(0, count);
        isDrawing.value = false;
        hasResult.value = true;

        // Step 2: Delay summary list by 1.5s
        setTimeout(() => {
            showResultList.value = true;
        }, 1500);
    }, 3000);
};

const copyResults = () => {
    const text = results.value.map((n, i) => `${i + 1}. ${n}`).join('\n');
    navigator.clipboard.writeText(text);
    alert('抽籤結果已複製到剪貼簿');
};

const isValid = computed(() => selectedNames.value.length > 0);

onMounted(() => {
    loadUsers();
});
</script>

<style scoped>
@keyframes shake {
    0%, 100% { transform: rotate(-5deg) translateY(0); }
    25% { transform: rotate(5deg) translateY(-10px); }
    50% { transform: rotate(-5deg) translateY(0); }
    75% { transform: rotate(5deg) translateY(-10px); }
}

@keyframes stick-bounce {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-20px); }
}

.animate-shake {
    animation: shake 0.4s ease-in-out infinite;
}

.animate-stick-bounce {
    animation: stick-bounce 0.3s ease-in-out infinite;
}

@keyframes stick-reveal {
    0% { transform: translateY(100px) rotate(10deg); opacity: 0; }
    100% { transform: translateY(0) rotate(0); opacity: 1; }
}

.animate-stick-reveal {
    animation: stick-reveal 0.6s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
    opacity: 0;
}

.custom-scrollbar::-webkit-scrollbar {
    width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #E2E8F0;
    border-radius: 10px;
}
</style>

<style scoped>
.animate-fade-in { animation: fadeIn 0.3s ease-out; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

@keyframes bounceShort {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-5px); }
}
.animate-bounce-short {
    animation: bounceShort 0.8s ease-in-out infinite;
}

.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
</style>
