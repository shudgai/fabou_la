<template>
    <div class="flex h-full bg-white overflow-hidden font-sans relative">
        
        <!-- STEP 1: PERSONNEL SELECTION -->
        <div v-show="currentStep === 1" class="flex w-full h-full overflow-hidden">
            <!-- Left Sidebar: Master Dharma Name List -->
            <div class="w-[58.33%] border-r border-slate-100 flex flex-col h-full bg-white pl-1.5">
                <div class="p-2 border-b border-slate-50 flex items-center justify-between">
                    <h3 class="text-[17px] font-black text-slate-900">法號全表</h3>
                    <div class="flex items-center space-x-2">
                        <span class="text-[14px] font-bold text-slate-400">已選 {{ selectedNames.length }}</span>
                        <button @click="resetAll" class="text-slate-300 hover:text-red-500 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </button>
                    </div>
                </div>
                <div class="flex-1 overflow-y-auto px-1 py-1 custom-scrollbar">
                    <div class="grid grid-cols-4 gap-0.5">
                        <button v-for="user in users" :key="user.id"
                            @click="toggleSelect(user.name)"
                            class="h-8 flex items-center justify-center transition-all active:scale-95">
                            <span :class="['text-[17px] font-black whitespace-nowrap',
                                         selectedNames.includes(user.name) ? 'text-slate-300' : 'text-slate-800']">
                                {{ user.name }}
                            </span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Right Area: On-site Personnel Pool -->
            <div class="w-[41.67%] flex flex-col h-full bg-slate-50/10">
                <div class="p-3 border-b border-slate-100 bg-white flex items-center justify-between">
                    <h2 class="text-[17px] font-black text-slate-900 whitespace-nowrap">在場名冊</h2>
                    <button v-if="selectedNames.length > 0" @click="currentStep = 2" 
                        class="text-indigo-600 hover:text-indigo-800 transition-all p-1 active:scale-90 transform -mr-2">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                </div>
                <div class="flex-1 overflow-y-auto p-4 custom-scrollbar">
                    <div v-if="selectedNames.length === 0" class="h-full flex flex-col items-center justify-center text-slate-200">
                        <p class="text-[15px] font-bold uppercase tracking-widest text-slate-200">待選取人員</p>
                    </div>
                    <div class="grid grid-cols-3 gap-y-1 text-center">
                        <div v-for="name in selectedNames" :key="'sel'+name" class="flex justify-center">
                            <span @click="toggleSelect(name)" 
                                class="text-[17px] font-black text-indigo-600 cursor-pointer hover:text-red-500 transition-colors whitespace-nowrap">
                                {{ name }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- STEP 2: GROUPING & RESULTS -->
        <div v-show="currentStep === 2" class="flex flex-col w-full h-full bg-slate-50/10 overflow-hidden animate-slide-in">
            <!-- Navigation Header -->
            <div class="bg-white border-b border-slate-50 p-2 px-3 flex items-center sticky top-0 z-10">
                <button @click="currentStep = 1" class="text-slate-400 hover:text-indigo-600 p-1.5 -ml-1.5 mr-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
                <div class="flex items-center space-x-2">
                    <h2 class="text-[17px] font-black text-slate-900">隨機分組設定</h2>
                    <div class="flex items-center space-x-1 text-slate-400">
                        <span class="text-[13px] font-bold">總計</span>
                        <span class="text-[16px] font-black text-indigo-600">{{ selectedNames.length }}</span>
                    </div>
                </div>
            </div>

            <!-- Main Content Container -->
            <div class="p-4 flex-1 overflow-y-auto custom-scrollbar flex flex-col gap-4 max-w-2xl mx-auto w-full">
                
                <!-- AREA 1: DESIGNATED GUARDIANS (指定關主) -->
                <div class="bg-amber-50/10 border border-amber-200 p-3 rounded-2xl space-y-2">
                    <div class="flex items-center justify-between px-0.5">
                        <label class="text-[14px] font-black uppercase tracking-wider text-amber-600">🌟 指定關主</label>
                        <span class="text-[11px] font-black text-amber-400 uppercase tracking-widest">⚠️ 全場抽選</span>
                    </div>
                    
                    <div class="flex items-start space-x-2">
                        <!-- Left: Search & Tags (Unified) -->
                        <div class="flex-[1.5] space-y-1.5 min-w-0">
                            <div class="relative">
                                <div class="flex items-center bg-white border border-amber-100 rounded-xl px-3 h-10 shadow-sm">
                                    <input type="text" v-model="guardianQuery" placeholder="搜尋或選取關主..." @focus="isGDropdownOpen = true" 
                                        class="w-full bg-transparent outline-none text-[16px] font-black text-amber-700 placeholder:text-amber-200">
                                    <svg class="w-4 h-4 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </div>
                                <div v-show="isGDropdownOpen && filteredGDropdown.length > 0" class="absolute top-[42px] left-0 right-0 bg-white border border-amber-100 rounded-xl shadow-2xl z-[50] max-h-[160px] overflow-y-auto custom-scrollbar">
                                    <button v-for="name in filteredGDropdown" :key="'gopt'+name" @click="addManualGuardian(name)" 
                                        class="w-full text-left px-4 py-2 text-[15px] font-black text-slate-700 hover:bg-amber-50 hover:text-amber-700 border-b border-slate-50 last:border-0">{{ name }}</button>
                                </div>
                                <div v-if="isGDropdownOpen" @click="isGDropdownOpen = false" class="fixed inset-0 z-[40]"></div>
                            </div>
                            <!-- Tags are now smaller and closer to the 'search' area -->
                            <div v-if="guardianResults.length > 0" class="flex flex-wrap gap-1">
                                <div v-for="name in guardianResults" :key="'glist'+name" 
                                    class="flex items-center space-x-1 bg-amber-500 text-white text-[14px] font-black px-2 py-0.5 rounded-lg animate-fade-in shadow-sm">
                                    <span>{{ name }}</span>
                                    <button @click="removeGuardian(name)" class="opacity-60 hover:opacity-100 transition-opacity">×</button>
                                </div>
                            </div>
                        </div>

                        <!-- Right: Reduced Random Draw (Half Heightish) -->
                        <div class="flex-1 flex items-center space-x-1 pt-0.5">
                            <div class="flex items-center border border-amber-200 rounded-lg overflow-hidden h-9 bg-white shadow-sm flex-1">
                                <button @click="pickSize = Math.max(1, pickSize - 1)" class="w-7 h-full font-black text-[18px] text-amber-400">−</button>
                                <span class="flex-1 text-[16px] font-black text-center text-amber-900 leading-none">{{ pickSize }}</span>
                                <button @click="pickSize = Math.min(10, pickSize + 1)" class="w-7 h-full font-black text-[18px] text-amber-400">＋</button>
                            </div>
                            <button @click="pickGuardians" :disabled="selectedNames.length < pickSize" 
                                class="h-9 px-3 font-black text-[13px] rounded-lg bg-amber-500 text-white shadow-md active:scale-95 disabled:opacity-30 whitespace-nowrap">
                                隨機抽
                            </button>
                        </div>
                    </div>
                </div>

                <!-- AREA 2: PERSONNEL ROLES & CALCULATION -->
                <div class="bg-white p-3 rounded-2xl border border-slate-100 space-y-4 shadow-sm">
                    
                    <!-- 2A. SEED SELECTION (ROLE ASSIGNMENT) -->
                    <div class="space-y-2 pb-1">
                        <label class="text-[13px] font-black text-slate-400 uppercase tracking-wider px-1">🌟 指定種子組 (行政優先扣除)</label>
                        <div class="flex flex-wrap gap-1.5 p-2 bg-slate-50 border border-slate-100 rounded-xl min-h-[44px]">
                            <div v-for="name in seedNames" :key="'seed'+name" class="flex items-center space-x-1.5 bg-amber-50 text-amber-900 text-[16px] font-black px-3 py-1 rounded-lg border border-amber-100">
                                <span>{{ name }}</span>
                                <button @click="removeSeed(name)" class="text-amber-300 hover:text-amber-500">×</button>
                            </div>
                            <div class="relative flex-1 min-w-[120px]">
                                <select @change="addSeedFromSelect($event)" class="w-full h-full bg-transparent outline-none text-[16px] font-black text-slate-400 cursor-pointer">
                                    <option value="" disabled selected>查詢在場人員...</option>
                                    <option v-for="name in availableSeeds" :key="'opt'+name" :value="name">{{ name }}</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- 2B. RULES & SUMMARY (CALCULATION) -->
                    <div class="space-y-3 border-t border-slate-50 pt-3">
                        <div class="flex items-center justify-between px-1">
                            <label class="text-[13px] font-black text-indigo-400 uppercase tracking-widest">⚙️ 分組規則</label>
                            <div class="flex items-center space-x-2">
                                <span class="text-[15px] font-black text-slate-700 uppercase tracking-tight">包含關主分組</span>
                                <button @click="includeGuardians = !includeGuardians" :class="['w-9 h-5 rounded-full transition-all relative', includeGuardians ? 'bg-indigo-500' : 'bg-slate-300']">
                                    <div :class="['absolute top-[4px] w-3 h-3 bg-white rounded-full transition-all shadow-sm', includeGuardians ? 'left-[20px]' : 'left-[4px]']"></div>
                                </button>
                            </div>
                        </div>

                        <div :class="['p-3 rounded-xl border transition-all duration-300', includeGuardians ? 'bg-indigo-50 border-indigo-100 shadow-sm' : 'bg-slate-50 border-slate-200']">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-[14px] font-black text-slate-500">📋 分派人數試算 (系統去重已套用)</span>
                                <span :class="['text-[11px] font-black px-2 py-0.5 rounded-full', includeGuardians ? 'bg-indigo-100 text-indigo-600' : 'bg-amber-100 text-amber-600']">{{ includeGuardians ? '混合分派模式' : '排除關主模式' }}</span>
                            </div>
                            <div class="space-y-1 px-0.5">
                                <div class="flex items-center justify-between text-[16px] font-black"><span class="text-slate-400">❶ 在場總計</span><span class="text-slate-800">{{ selectedNames.length }} 人</span></div>
                                
                                <!-- Detailed Deduction Rows -->
                                <div class="flex items-center justify-between text-[14px] font-bold text-slate-300 pl-4 py-0.5 border-l-2 border-slate-100 ml-1">
                                    <span>− 種子組名單</span>
                                    <span>{{ seedNames.length }} 人</span>
                                </div>
                                <div v-if="!includeGuardians" class="flex items-center justify-between text-[14px] font-bold text-slate-300 pl-4 py-0.5 border-l-2 border-slate-100 ml-1">
                                    <span>− 指定關主名單</span>
                                    <span>{{ guardianResults.length }} 人</span>
                                </div>
                                
                                <div class="flex items-center justify-between text-[16px] font-black pt-1 border-t border-dashed border-slate-100 mt-1">
                                    <span class="text-rose-400/80 italic">❷ 總計排除</span>
                                    <span class="text-rose-500">{{ totalExclusionCount }} 人</span>
                                </div>

                                <div class="border-t border-slate-200 mt-2.5 pt-2.5">
                                    <div class="flex items-center justify-between">
                                        <div class="flex flex-col">
                                            <span class="text-slate-900 font-black text-[17px]">❸ 實際隨機分派</span>
                                            <span class="text-[10px] font-bold text-slate-400">❶ 在場總計 − ❷ 總計排除</span>
                                        </div>
                                        <span :class="['text-[26px] font-black leading-none drop-shadow-sm', includeGuardians ? 'text-indigo-600' : 'text-amber-600']">{{ activePoolCount }} 人</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 2C. GROUP SIZE & ACTION -->
                    <div class="space-y-4 pt-1 border-t border-slate-50 mt-1">
                        <div class="space-y-2">
                            <label class="text-[13px] font-black text-slate-400 uppercase tracking-wider px-1">每組固定人數</label>
                            <div class="flex items-center border border-slate-200 rounded-xl overflow-hidden h-14 bg-slate-50/50">
                                <button @click="groupSize = Math.max(2, groupSize - 1)" class="w-14 h-full text-slate-400 hover:bg-white text-[20px] font-black transition-colors">−</button>
                                <div class="flex-1 flex flex-col items-center justify-center"><span class="text-[20px] font-black text-slate-800 leading-none">{{ groupSize }}</span></div>
                                <button @click="groupSize = Math.min(20, groupSize + 1)" class="w-14 h-full text-slate-400 hover:bg-white text-[20px] font-black transition-colors">＋</button>
                            </div>
                        </div>
                        <button @click="doGrouping" :disabled="selectedNames.length < 1" class="w-full h-14 bg-indigo-600 text-white font-black text-[18px] rounded-2xl transition-all active:scale-95 shadow-lg disabled:opacity-30">開始分組演算</button>
                    </div>
                </div>

                <!-- Drawing Animation -->
                <div v-if="isDrawing" class="flex flex-col items-center justify-center py-12 space-y-4">
                    <div class="flex gap-3">
                        <span class="dot bg-indigo-400 w-4 h-4"></span>
                        <span class="dot bg-indigo-500 w-4 h-4"></span>
                        <span class="dot bg-indigo-600 w-4 h-4"></span>
                    </div>
                    <p class="text-[13px] font-black text-indigo-400 uppercase tracking-widest">隨機演算中...</p>
                </div>

                <!-- Results Display -->
                <div v-if="(guardianResults.length > 0 || groups.length > 0) && !isDrawing" class="animate-fade-in space-y-3 pb-20 px-1 -mt-4">
                    <div class="border-t border-slate-100 w-16 mx-auto mb-1"></div>
                    <div v-if="guardianResults.length > 0" class="bg-amber-50 p-1.5 rounded-2xl border border-amber-200/50">
                        <div class="flex items-center justify-between mb-1">
                            <h4 class="text-[13px] font-black text-amber-600 tracking-wider uppercase">🌟 關主名單 ({{ guardianResults.length }} 位)</h4>
                            <button @click="clearGuardians" class="text-[11px] font-black text-amber-400 hover:text-amber-600">重抽</button>
                        </div>
                        <div class="flex flex-wrap gap-x-4 gap-y-0.5">
                            <div v-for="name in guardianResults" :key="'guard'+name" class="text-amber-700 font-black text-[17px]">{{ name }}</div>
                        </div>
                    </div>
                    <div v-if="groups.length > 0" class="space-y-1.5">
                        <div class="flex items-center justify-between">
                            <h4 class="text-[13px] font-black text-slate-400 tracking-wider uppercase">分組名冊</h4>
                            <div class="flex items-center space-x-2">
                                <button @click="redrawAll" class="text-[11px] font-black text-rose-500 hover:text-rose-700 bg-rose-50 px-2 py-0.5 rounded-full border border-rose-100"><span>重抽</span></button>
                                <button @click="copyResult" class="text-[11px] font-black text-emerald-600 hover:text-emerald-800 flex items-center space-x-1 border border-emerald-100 bg-white px-2 py-0.5 rounded-full"><span>複製名單</span></button>
                                <button @click="groups = []" class="text-[11px] font-black text-slate-300 hover:text-red-500 px-1">隱藏</button>
                            </div>
                        </div>
                        <div class="grid gap-1">
                            <div v-for="(group, idx) in groups" :key="idx" :class="['p-1.5 px-3 rounded-2xl border transition-all', group.isSeed ? 'bg-amber-50 border-amber-100 ring-2 ring-amber-50' : 'bg-white border-slate-100 shadow-none']">
                                <div class="flex items-center justify-between mb-0.5"><span :class="['text-[12px] font-black uppercase tracking-tight', group.isSeed ? 'text-amber-500' : 'text-slate-400']">{{ group.name }}</span><span class="text-[11px] font-bold text-slate-300">{{ group.members.length }} 人</span></div>
                                <div class="flex flex-wrap gap-x-4 gap-y-0.5"><span v-for="member in group.members" :key="member" :class="['text-[17px] font-black', group.isSeed ? 'text-amber-900' : 'text-slate-900']">{{ member }}</span></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Success Notification -->
        <transition name="fade">
            <div v-if="showSavedToast" class="fixed top-6 left-1/2 -translate-x-1/2 bg-slate-900/90 text-white px-4 py-2 rounded-full text-[13px] font-bold shadow-2xl z-[100] flex items-center space-x-2 border border-white/10 backdrop-blur-md">
                <svg v-if="toastMsg.includes('抽出')" class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                <svg v-else class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                <span>{{ toastMsg }}</span>
            </div>
        </transition>
    </div>
</template>

<script setup>
import { ref, onMounted, watch, computed } from 'vue';
import axios from 'axios';

const users = ref([]);
const selectedNames = ref([]);
const groupSize = ref(4);
const seedNames = ref([]);
const groups = ref([]);
const guardianResults = ref([]);
const isDrawing = ref(false);
const currentType = ref('');
const pickSize = ref(1);
const includeGuardians = ref(false);
const showSavedToast = ref(false);
const toastMsg = ref('進度已暫存');
const currentStep = ref(1);

const guardianQuery = ref('');
const isGDropdownOpen = ref(false);

const STORAGE_KEY = 'fabou_random_group_draft_v2';

const availableSeeds = computed(() => {
    const filtered = selectedNames.value.filter(n => !seedNames.value.includes(n));
    return filtered.sort((a, b) => {
        const idxA = users.value.findIndex(u => u.name === a);
        const idxB = users.value.findIndex(u => u.name === b);
        return idxA - idxB;
    });
});

const availableGuardians = computed(() => {
    return selectedNames.value.filter(n => !guardianResults.value.includes(n));
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

const saveToLocalStorage = () => {
    const data = {
        selectedNames: selectedNames.value,
        groupSize: groupSize.value,
        seedNames: seedNames.value,
        groups: groups.value,
        guardianResults: guardianResults.value,
        pickSize: pickSize.value,
        includeGuardians: includeGuardians.value,
        currentStep: currentStep.value
    };
    localStorage.setItem(STORAGE_KEY, JSON.stringify(data));
    
    // Default save toast (only if not already showing another msg)
    if (!showSavedToast.value) {
        toastMsg.value = '進度已暫存';
        showSavedToast.value = true;
        setTimeout(() => { showSavedToast.value = false; }, 2000);
    }
};

watch([selectedNames, groupSize, seedNames, groups, guardianResults, pickSize, includeGuardians, currentStep], saveToLocalStorage, { deep: true });

const loadUsers = async () => {
    try {
        const res = await axios.get('/api/dharma-names-list');
        users.value = res.data; 
        const draft = localStorage.getItem(STORAGE_KEY);
        if (draft) {
            try {
                const parsed = JSON.parse(draft);
                selectedNames.value = parsed.selectedNames || [];
                groupSize.value = parsed.groupSize || 4;
                seedNames.value = parsed.seedNames || [];
                groups.value = parsed.groups || [];
                guardianResults.value = parsed.guardianResults || [];
                pickSize.value = parsed.pickSize || 1;
                includeGuardians.value = parsed.includeGuardians || false;
                currentStep.value = parsed.currentStep || 1;
            } catch (err) { console.error('Draft load failed', err); }
        }
    } catch (e) { console.error('Failed to load users', e); }
};

const toggleSelect = (name) => {
    const idx = selectedNames.value.indexOf(name);
    if (idx === -1) {
        selectedNames.value.push(name);
    } else {
        selectedNames.value.splice(idx, 1);
        removeSeed(name);
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

const totalExclusionCount = computed(() => {
    const combined = new Set(seedNames.value);
    if (!includeGuardians.value) {
        guardianResults.value.forEach(g => combined.add(g));
    }
    // Only count people who are actually marked as present
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

    // Strictly shuffle only those NOT in the exclusion set
    let candidates = players.filter(p => !exclusionSet.has(p));
    
    // Shuffle candidates
    for (let i = candidates.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [candidates[i], candidates[j]] = [candidates[j], candidates[i]];
    }

    const result = [];
    let groupNum = 1;

    // 1. Seed Groups get the "第 1 組" designation as requested
    const seedsInPlay = seedNames.value.filter(s => players.includes(s));
    if (seedsInPlay.length > 0) {
        result.push({ name: `第 ${groupNum} 組 (種子組)`, members: seedsInPlay, isSeed: true });
        groupNum++;
    }

    // 2. Remaining candidates follow from group 2 onwards
    while (candidates.length > 0) {
        result.push({ 
            name: `第 ${groupNum} 組`, 
            members: candidates.splice(0, size), 
            isSeed: false 
        });
        groupNum++;
    }

    return result;
};

const doGrouping = () => {
    if (selectedNames.value.length < 1) return;
    
    isDrawing.value = true;
    currentType.value = 'group';
    groups.value = [];
    
    setTimeout(() => {
        // ALWAYS use full selectedNames pool, distributePlayers handles exclusion logic
        groups.value = distributePlayers([...selectedNames.value], groupSize.value);
        isDrawing.value = false;
        saveToLocalStorage();
    }, 800);
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
        saveToLocalStorage();
        
        toastMsg.value = '關主已抽出';
        showSavedToast.value = true;
        setTimeout(() => { showSavedToast.value = false; }, 2000);
    }, 800);
};

const clearGuardians = () => {
    if (!confirm('確定要清空關主名單嗎？')) return;
    guardianResults.value = [];
    saveToLocalStorage();
};

const redrawAll = () => {
    if (!confirm('確定要將所有關主回歸名單並重新抽選嗎？')) return;
    selectedNames.value = [...selectedNames.value, ...guardianResults.value];
    guardianResults.value = [];
    groups.value = [];
    isDrawing.value = false;
    currentType.value = '';
    saveToLocalStorage();
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
    navigator.clipboard.writeText(text).then(() => { alert('已複製名單（含關主）！'); });
};

const resetAll = () => {
    if (!confirm('確定要清空所有選取與結果嗎？這將重置所有進度。')) return;
    selectedNames.value = [];
    seedNames.value = [];
    groups.value = [];
    guardianResults.value = [];
    isDrawing.value = false;
    currentType.value = '';
    currentStep.value = 1;
    includeGuardians.value = false;
    localStorage.removeItem(STORAGE_KEY);
    saveToLocalStorage();
};

onMounted(loadUsers);
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
.dot { border-radius: 50%; animation: bounce 0.6s infinite alternate; }
.dot:nth-child(2) { animation-delay: 0.2s; }
.dot:nth-child(3) { animation-delay: 0.4s; }
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
</style>
