<template>
    <div class="flex h-full bg-white overflow-hidden font-sans">

        <!-- Left Sidebar: Dharma Name Selector -->
        <div class="w-[41.66%] border-r border-slate-100 flex flex-col h-full bg-white pl-[5px]">
            <div class="p-1 border-b border-slate-50">
                <h3 class="text-[15px] font-black text-slate-900">法號表</h3>
            </div>
            <div class="flex-1 overflow-y-auto px-[5px] py-1 custom-scrollbar">
                <div class="grid grid-cols-3 gap-x-[3px] gap-y-[5px]">
                    <button v-for="user in users" :key="user.id"
                        @click="toggleSelect(user.name)"
                        :class="['h-[30px] flex items-center justify-center transition-all rounded active:scale-95',
                                 selectedNames.includes(user.name) ? 'bg-indigo-50' : '']">
                        <span :class="['text-[15px] font-bold px-0.5 truncate',
                                       selectedNames.includes(user.name) ? 'text-indigo-600' : 'text-slate-800']">
                            {{ user.name }}
                        </span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Right Content: Group Settings + Results -->
        <div class="flex-1 overflow-y-auto flex flex-col h-full">

            <!-- Header -->
            <div class="bg-white border-b border-slate-50 p-2 flex items-center justify-between sticky top-0 z-10">
                <h2 class="text-[15px] font-black text-slate-900 pl-[6px]">隨機分組</h2>
                <button @click="resetAll" class="text-slate-300 hover:text-red-500 p-1 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
            </div>

            <div class="p-3 flex flex-col gap-3">

                <!-- Settings Row -->
                <div class="flex items-center gap-2 flex-wrap">
                    <div class="flex items-center gap-1">
                        <span class="text-[13px] font-bold text-slate-500">已選</span>
                        <span class="text-[15px] font-black text-indigo-600">{{ selectedNames.length }}</span>
                        <span class="text-[13px] font-bold text-slate-500">人</span>
                    </div>
                    <div class="flex items-center gap-1 ml-2">
                        <span class="text-[13px] font-bold text-slate-500">每組</span>
                        <div class="flex items-center border border-slate-200 rounded-lg overflow-hidden">
                            <button @click="groupSize = Math.max(2, groupSize - 1)" class="px-2 py-1 text-slate-400 hover:bg-slate-50 text-[15px] font-black">−</button>
                            <span class="px-2 text-[15px] font-black text-slate-800 min-w-[24px] text-center">{{ groupSize }}</span>
                            <button @click="groupSize = Math.min(20, groupSize + 1)" class="px-2 py-1 text-slate-400 hover:bg-slate-50 text-[15px] font-black">＋</button>
                        </div>
                        <span class="text-[13px] font-bold text-slate-500">人</span>
                    </div>
                    <div class="flex items-center gap-1 ml-2">
                        <span class="text-[13px] font-bold text-slate-500">先抽出</span>
                        <div class="flex items-center border border-slate-200 rounded-lg overflow-hidden">
                            <button @click="pickSize = Math.max(1, pickSize - 1)" class="px-2 py-1 text-slate-400 hover:bg-slate-50 text-[15px] font-black">−</button>
                            <span class="px-2 text-[15px] font-black text-slate-800 min-w-[24px] text-center">{{ pickSize }}</span>
                            <button @click="pickSize = Math.min(10, pickSize + 1)" class="px-2 py-1 text-slate-400 hover:bg-slate-50 text-[15px] font-black">＋</button>
                        </div>
                        <span class="text-[13px] font-bold text-slate-500">人</span>
                    </div>
                </div>

                <!-- Seed Names Input -->
                <div>
                    <p class="text-[13px] font-bold text-slate-400 mb-1">種子組（優先抽出，用逗號分隔）</p>
                    <div class="flex flex-wrap gap-1 min-h-[32px] border border-slate-100 rounded-lg p-1 bg-slate-50">
                        <span v-for="name in seedNames" :key="name"
                            class="flex items-center gap-1 bg-amber-100 text-amber-800 text-[13px] font-bold px-2 py-0.5 rounded-full">
                            {{ name }}
                            <button @click="removeSeed(name)" class="text-amber-400 hover:text-amber-700 leading-none">×</button>
                        </span>
                        <input v-model="seedInput" @keydown.enter.prevent="addSeed" @keydown.comma.prevent="addSeed"
                            placeholder="輸入法號 Enter 加入..."
                            class="flex-1 min-w-[80px] bg-transparent text-[13px] font-bold text-slate-700 outline-none placeholder-slate-300 px-1" />
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-2">
                    <button @click="pickMany"
                        :disabled="selectedNames.length < pickSize"
                        :class="['flex-1 h-10 font-black text-[15px] rounded-xl transition-all active:scale-95 whitespace-nowrap overflow-hidden text-ellipsis',
                                 currentType === 'pick' ? 'bg-rose-500 text-white shadow-lg shadow-rose-100 ring-4 ring-rose-500/20' : 'bg-slate-100 text-slate-400 hover:bg-slate-200']">
                        先抽出 {{ pickSize }} 人
                    </button>
                    <button @click="doGrouping"
                        :disabled="selectedNames.length < 2"
                        :class="['flex-1 h-10 font-black text-[15px] rounded-xl transition-all active:scale-95 whitespace-nowrap overflow-hidden text-ellipsis',
                                 currentType === 'group' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-100 ring-4 ring-indigo-500/20' : 'bg-slate-100 text-slate-400 hover:bg-slate-200']">
                        開始分組
                    </button>
                </div>

                <!-- Loading Dots Animation -->
                <div v-if="isDrawing" class="flex justify-center items-center py-6 gap-2">
                    <span class="dot bg-indigo-400"></span>
                    <span class="dot bg-indigo-500"></span>
                    <span class="dot bg-indigo-600"></span>
                </div>

                <!-- Persistent Results Area -->
                <div v-if="(pickedResults.length > 0 || groups.length > 0) && !isDrawing" class="flex flex-col pb-4">
                    
                    <!-- Compact Pick Results -->
                    <div v-if="pickedResults.length > 0" class="mb-2">
                        <div class="flex items-center justify-between mb-1">
                            <p class="text-[13px] font-bold text-slate-400">📋 先抽出名單</p>
                            <button @click="pickedResults = []" class="text-[12px] text-rose-400 hover:text-rose-600 transition-colors">清除</button>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <span v-for="name in pickedResults" :key="name" 
                                class="bg-rose-50 text-rose-600 text-[15px] font-black px-3 py-0.5 rounded-lg border border-rose-100/50 shadow-sm">
                                {{ name }}
                            </span>
                        </div>
                    </div>

                    <!-- Separator if both exist -->
                    <div v-if="pickedResults.length > 0 && groups.length > 0" class="border-t border-slate-100 my-2"></div>

                    <!-- Groups Results -->
                    <div v-if="groups.length > 0">
                        <div class="flex items-center justify-between mb-2">
                            <p class="text-[13px] font-bold text-slate-400">分組結果</p>
                            <button @click="copyResult" class="text-[13px] font-bold text-emerald-600 hover:text-emerald-800 transition-colors">複製貼 LINE</button>
                        </div>
                        <div class="flex flex-col gap-1">
                            <div v-for="(group, idx) in groups" :key="idx" 
                                :class="['py-1 px-3 rounded-lg border transition-colors', 
                                         group.isSeed ? 'bg-amber-50 border-amber-100' : 
                                         (group.members.length === 1 ? 'bg-indigo-50 border-indigo-100' : 'bg-white border-slate-50')]">
                                <p :class="['text-[12px] font-black mb-0.5', 
                                           group.isSeed ? 'text-amber-500' : 
                                           (group.members.length === 1 ? 'text-indigo-400' : 'text-slate-400')]">
                                    {{ group.name }}{{ group.members.length === 1 ? ' (剩餘)' : '' }}
                                </p>
                                <div class="flex flex-wrap gap-x-3 gap-y-1">
                                    <span v-for="member in group.members" :key="member"
                                        :class="['text-[15px] font-bold whitespace-nowrap', 
                                                 group.isSeed ? 'text-amber-800' : 
                                                 (group.members.length === 1 ? 'text-indigo-700' : 'text-slate-900')]">
                                        {{ member }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-else-if="selectedNames.length === 0" class="py-16 text-center">
                    <p class="text-[14px] font-bold text-slate-200 uppercase tracking-widest">請從左側點選人員</p>
                </div>

            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const users = ref([]);
const selectedNames = ref([]);
const groupSize = ref(4);
const seedInput = ref('');
const seedNames = ref([]);
const groups = ref([]);
const pickedResults = ref([]);
const isDrawing = ref(false);
const currentType = ref(''); // 'group' or 'pick'
const pickSize = ref(1);

const excludedNames = ['鳳尊', '金巧', '赤覺', '紫元', '鳳媓', '金忠', '金孝', '金諦', '金彩', '金德', '靈平', '金護', '靈情'];

const loadUsers = async () => {
    try {
        const res = await axios.get('/api/dharma-names-list');
        users.value = res.data.filter(u => !excludedNames.includes(u.name));
    } catch (e) {
        console.error('Failed to load users', e);
    }
};

const toggleSelect = (name) => {
    const idx = selectedNames.value.indexOf(name);
    if (idx === -1) {
        selectedNames.value.push(name);
    } else {
        selectedNames.value.splice(idx, 1);
    }
};

const addSeed = () => {
    const name = seedInput.value.trim().replace(/,|，$/, '');
    if (name && !seedNames.value.includes(name)) {
        seedNames.value.push(name);
    }
    seedInput.value = '';
};

const removeSeed = (name) => {
    seedNames.value = seedNames.value.filter(n => n !== name);
};

/**
 * 隨機分組：Fisher-Yates Shuffle + 種子組
 */
const distributePlayers = (players, size, preSelected = []) => {
    let pool = [...players];
    const specialGroup = [];

    preSelected.forEach(name => {
        const index = pool.indexOf(name);
        if (index !== -1) {
            specialGroup.push(pool.splice(index, 1)[0]);
        }
    });

    // Fisher-Yates Shuffle
    for (let i = pool.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [pool[i], pool[j]] = [pool[j], pool[i]];
    }

    const result = [];

    if (specialGroup.length > 0) {
        result.push({ name: '🌟 種子組', members: specialGroup, isSeed: true });
    }

    let groupNum = 1;
    while (pool.length > 0) {
        result.push({
            name: `第 ${groupNum} 組`,
            members: pool.splice(0, size),
            isSeed: false
        });
        groupNum++;
    }

    return result;
};

const doGrouping = () => {
    if (selectedNames.value.length < 2) return;
    isDrawing.value = true;
    currentType.value = 'group';
    groups.value = [];
    setTimeout(() => {
        groups.value = distributePlayers(selectedNames.value, groupSize.value, seedNames.value);
        isDrawing.value = false;
    }, 700);
};

const pickMany = () => {
    if (selectedNames.value.length < pickSize.value) return;
    isDrawing.value = true;
    currentType.value = 'pick';
    setTimeout(() => {
        for (let i = 0; i < pickSize.value; i++) {
            const idx = Math.floor(Math.random() * selectedNames.value.length);
            const name = selectedNames.value[idx];
            pickedResults.value.push(name);
            selectedNames.value.splice(idx, 1);
        }
        isDrawing.value = false;
    }, 700);
};

const copyResult = () => {
    if (groups.value.length === 0) return;
    let text = `🎲 隨機分組結果\n`;
    groups.value.forEach(g => {
        text += `\n【${g.name}】\n`;
        text += g.members.join('、') + '\n';
    });
    navigator.clipboard.writeText(text).then(() => {
        alert('已複製到剪貼簿，快前往 LINE 貼上吧！');
    });
};

const resetAll = () => {
    if (confirm('確定要清空所有選取與結果嗎？')) {
        selectedNames.value = [];
        seedNames.value = [];
        groups.value = [];
        seedInput.value = '';
        pickedResults.value = [];
        isDrawing.value = false;
        currentType.value = '';
    }
};

onMounted(loadUsers);
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }

.dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    animation: bounce 0.6s infinite alternate;
}
.dot:nth-child(2) { animation-delay: 0.2s; }
.dot:nth-child(3) { animation-delay: 0.4s; }

@keyframes bounce {
    from { transform: translateY(0); opacity: 0.4; }
    to   { transform: translateY(-8px); opacity: 1; }
}
</style>
