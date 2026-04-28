<template>
    <div class="h-full bg-white flex flex-col">
        <!-- Level 1: Folder Grid View -->
        <div v-if="!activeFolderId" class="min-h-screen bg-white">
            <div class="px-6 py-6 flex items-center justify-between border-b border-slate-50 sticky top-0 bg-white z-10">
                <div class="flex items-center">
                    <button @click="$emit('goHome')" class="p-2 text-slate-400 mr-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" /></svg>
                    </button>
                    <h2 class="font-black tracking-tight cursor-pointer" @click="resetToRoot" style="color: #0f172a !important; font-size: 25px !important;">其他專區資料夾</h2>
                </div>
                <div class="w-10 h-10"></div> <!-- Placeholder to maintain title centering -->
            </div>

            <div class="grid grid-cols-1 gap-8 p-6 place-items-center pb-24">
                <button v-for="(folder, idx) in sortedFolders" :key="folder.id" 
                    @click="activeFolderId = folder.id"
                    class="flex flex-col items-center justify-center active:scale-95 transition-all group relative rounded-[40px] p-[5px]"
                    style="background-color: rgb(255, 250, 205);">
                    <div class="relative w-[280px] h-[280px]">
                        <svg class="w-full h-full transition-transform group-hover:scale-105" viewBox="0 0 64 64" fill="none">
                            <defs>
                                <linearGradient :id="'otherGrad' + idx" x1="0%" y1="0%" x2="100%" y2="100%">
                                    <stop offset="0%" style="stop-color:rgb(255, 120, 120);stop-opacity:1" />
                                    <stop offset="50%" style="stop-color:rgb(255, 50, 50);stop-opacity:1" />
                                    <stop offset="100%" style="stop-color:rgb(220, 0, 0);stop-opacity:1" />
                                </linearGradient>
                            </defs>
                            <path d="M4 14C4 11.7909 5.79086 10 8 10H24.5L30 16H56C58.2091 16 60 17.7909 60 20V50C60 52.2091 58.2091 54 56 54H8C5.79086 54 4 52.2091 4 50V14Z" :fill="'url(#otherGrad' + idx + ')'" opacity="0.8"/>
                            <path d="M4 22C4 19.7909 5.79086 18 8 18H56C58.2091 18 60 19.7909 60 22V50C60 52.2091 58.2091 54 56 54H8C5.79086 54 4 52.2091 4 50V22Z" :fill="'url(#otherGrad' + idx + ')'" stroke="rgba(255,255,255,0.4)" stroke-width="0.5"/>
                        </svg>
                        <!-- Label Inside -->
                        <div class="absolute inset-0 flex items-center justify-center pt-5 px-3">
                            <span :class="[
                                'font-black drop-shadow-[0_2px_4px_rgba(0,0,0,0.6)] tracking-tight leading-tight text-center transition-all',
                                folder.name === '閻王仙師' ? 'text-black' : 'text-white'
                            ]" style="font-weight: 900 !important; font-size: 22px !important;">
                                {{ folder.name }}
                            </span>
                        </div>
                    </div>
                </button>

                <!-- Lucky Draw - Direct Sort (RED FOLDER STYLE) -->
                <button @click="luckyDrawInitialMode = false; showLuckyDraw = true"
                    class="flex flex-col items-center justify-center active:scale-95 transition-all group relative rounded-[40px] p-[5px]"
                    style="background-color: rgb(255, 250, 205);">
                    <div class="relative w-[280px] h-[280px]">
                        <svg class="w-full h-full transition-transform group-hover:scale-105" viewBox="0 0 64 64" fill="none">
                            <defs>
                                <linearGradient id="luckyGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                                    <stop offset="0%" style="stop-color:rgb(255, 120, 120);stop-opacity:1" />
                                    <stop offset="50%" style="stop-color:rgb(255, 50, 50);stop-opacity:1" />
                                    <stop offset="100%" style="stop-color:rgb(220, 0, 0);stop-opacity:1" />
                                </linearGradient>
                            </defs>
                            <path d="M4 14C4 11.7909 5.79086 10 8 10H24.5L30 16H56C58.2091 16 60 17.7909 60 20V50C60 52.2091 58.2091 54 56 54H8C5.79086 54 4 52.2091 4 50V14Z" fill="url(#luckyGrad)" opacity="0.8"/>
                            <path d="M4 22C4 19.7909 5.79086 18 8 18H56C58.2091 18 60 19.7909 60 22V50C60 52.2091 58.2091 54 56 54H8C5.79086 54 4 52.2091 4 50V22Z" fill="url(#luckyGrad)" stroke="rgba(255,255,255,0.4)" stroke-width="0.5"/>
                        </svg>
                        <!-- Label Inside -->
                        <div class="absolute inset-0 flex items-center justify-center pt-5 px-3">
                            <span class="text-white font-black drop-shadow-[0_2px_4px_rgba(0,0,0,0.6)] tracking-tight leading-tight text-center transition-all" style="font-weight: 900 !important; font-size: 22px !important;">
                                直接排列
                            </span>
                        </div>
                    </div>
                </button>

                <!-- Lucky Draw - Round Lottery (EMERALD FOLDER STYLE) -->
                <button @click="luckyDrawInitialMode = true; showLuckyDraw = true"
                    class="flex flex-col items-center justify-center active:scale-95 transition-all group relative rounded-[40px] p-[5px]"
                    style="background-color: rgb(255, 250, 205);">
                    <div class="relative w-[280px] h-[280px]">
                        <svg class="w-full h-full transition-transform group-hover:scale-105" viewBox="0 0 64 64" fill="none">
                            <defs>
                                <linearGradient id="roundGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                                    <stop offset="0%" style="stop-color:rgb(52, 211, 153);stop-opacity:1" />
                                    <stop offset="50%" style="stop-color:rgb(16, 185, 129);stop-opacity:1" />
                                    <stop offset="100%" style="stop-color:rgb(5, 150, 105);stop-opacity:1" />
                                </linearGradient>
                            </defs>
                            <path d="M4 14C4 11.7909 5.79086 10 8 10H24.5L30 16H56C58.2091 16 60 17.7909 60 20V50C60 52.2091 58.2091 54 56 54H8C5.79086 54 4 52.2091 4 50V14Z" fill="url(#roundGrad)" opacity="0.8"/>
                            <path d="M4 22C4 19.7909 5.79086 18 8 18H56C58.2091 18 60 19.7909 60 22V50C60 52.2091 58.2091 54 56 54H8C5.79086 54 4 52.2091 4 50V22Z" fill="url(#roundGrad)" stroke="rgba(255,255,255,0.4)" stroke-width="0.5"/>
                        </svg>
                        <!-- Label Inside -->
                        <div class="absolute inset-0 flex items-center justify-center pt-5 px-3">
                            <span class="text-white font-black drop-shadow-[0_2px_4px_rgba(0,0,0,0.6)] tracking-tight leading-tight text-center transition-all" style="font-weight: 900 !important; font-size: 22px !important;">
                                回合抽籤
                            </span>
                        </div>
                    </div>
                </button>
            </div>

            <!-- Dashboard Bottom Navbar -->
            <mobile-navbar 
                :can-back="true"
                :show-action="true"
                :action-disabled="false"
                :can-search="false"
                :can-more="false"
                @back="$emit('goHome')"
                @home="$emit('goHome')"
                @action="prepareAddFolder"
            />
        </div>

        <!-- Level 2: Record List / Content View -->
        <div v-else class="flex-grow flex flex-col bg-slate-50/50 overflow-hidden">
            <!-- Global Dual Header System -->
            <!-- Header 1: Module Level -->
            <div class="border-b border-slate-300 bg-white sticky top-0 z-[110] w-full" style="padding: 8px 10px; min-height: 48px;">
                <div class="flex flex-col w-full gap-1">
                    <!-- First Row: Main Title -->
                    <div class="flex items-center">
                        <button @click="activeFolderId = null" class="p-2 text-slate-400 mr-1 -ml-1">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" /></svg>
                        </button>
                        <div class="app-title font-black leading-tight font-outfit tracking-widest cursor-pointer" @click="resetToRoot" style="color: #0f172a !important; font-size: 25px !important;">
                            其他專區
                        </div>
                    </div>
                    
                    <!-- Second Row: Subtitle + Quick Controls -->
                    <div v-if="activeFolder" class="flex items-center justify-between w-full mt-1">
                        <span class="text-slate-700 font-normal shrink-0 mr-2" style="font-size: 22px !important;">{{ activeFolder.name }}</span>
                        
                        <div class="flex items-center justify-end flex-1">
                            <template v-if="activeFolder.name.includes('隨機分組')">
                                <div class="flex items-center space-x-1 flex-1 max-w-[80px]" style="transform: translateY(-10px);">
                                    <button ontouchstart="" @click="randomGroupRef?.invertSelection(); activeAction = 'invert'" 
                                        :class="[
                                            'flex-1 py-[6px] text-[17px] rounded-lg shadow-sm border transition-colors duration-150',
                                            activeAction === 'invert' ? 'bg-blue-600 border-blue-600 text-white' : 'bg-white border-slate-300 text-black active:bg-slate-400'
                                        ]" :style="{ color: activeAction === 'invert' ? '#ffffff !important' : '' }">反選</button>
                                </div>
                                <button @click="randomGroupRef?.resetAll()" class="p-1 ml-1 text-slate-400 hover:text-red-500 shrink-0">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </button>
                            </template>
                            <template v-else-if="activeFolder.name.includes('開文核定')">
                                <button @click="kaiwenRef?.clearAll()" class="p-1 ml-1 text-slate-400 hover:text-red-500 shrink-0">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </button>
                            </template>
                        </div>
                    </div>
                </div>
            </div>


            <div v-if="activeFolder" class="h-full">
                <!-- Special View: 開文核定表 -->
                <kaiwen-approval v-if="activeFolder.name.includes('開文核定')" ref="kaiwenRef" />
                <!-- Special View: 隨機分組 -->
                <random-group v-else-if="activeFolder.name.includes('隨機分組')" ref="randomGroupRef" />
                
                <!-- Default View: Standard Records -->
                <div v-else class="max-w-4xl mx-auto w-full px-[10px] pt-6 pb-32">
                    <div class="space-y-4">
                        <div v-for="record in activeFolder.other_records" :key="record.id" class="bg-white p-6 rounded-[24px] shadow-sm border border-slate-100 hover:shadow-md transition-shadow relative group">
                            <button @click="deleteRecord(record.id)" class="absolute top-4 right-4 opacity-0 group-hover:opacity-100 text-slate-300 hover:text-red-500 transition-opacity">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </button>
                            
                            <div class="flex items-center text-slate-400 text-xs font-bold uppercase tracking-widest mb-2">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                {{ formatDate(record.record_date) || formatDate(record.created_at) }}
                            </div>

                            <h3 v-if="record.title" class="text-lg font-bold text-slate-800 mb-2">{{ record.title }}</h3>
                            <p class="text-slate-600 whitespace-pre-wrap leading-relaxed">{{ record.content }}</p>
                        </div>

                        <div v-if="!activeFolder.other_records?.length" class="text-center py-20 bg-white rounded-[32px] border border-dashed border-slate-200">
                            <div class="text-slate-300 mb-4 flex justify-center">
                                <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </div>
                            <p class="text-slate-400 font-medium">尚無任何記事内容</p>
                        </div>
                    </div>
                </div>
            </div>
            <div v-else class="flex-grow flex items-center justify-center text-slate-400 font-medium">
                請選擇一個資料夾開始
            </div>

            <!-- Folder Contents Bottom Navbar -->
            <mobile-navbar 
                :can-back="true"
                :show-action="!activeFolder?.name.includes('開文核定') && !activeFolder?.name.includes('隨機分組')"
                :action-disabled="false"
                :can-search="false"
                :can-more="!!activeFolder"
                @back="activeFolderId = null"
                @home="$emit('goHome')"
                @action="prepareAddRecord"
                @more="handleMore"
            />
        </div>



        <!-- Add Folder Modal -->
        <div v-if="showAddFolder" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-[40px] p-8 w-full max-w-md shadow-2xl">
                <h3 class="text-xl font-bold mb-6">新增資料夾</h3>
                <input v-model="newFolderName" @keyup.enter="saveFolder" placeholder="輸入名稱..." class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl mb-6 focus:ring-2 focus:ring-indigo-500/20 transition-all font-medium">
                <div class="flex items-center space-x-3 mb-8">
                    <span class="text-sm font-bold text-slate-400">主題色：</span>
                    <button v-for="c in ['#6366f1', '#f59e0b', '#10b981', '#ef4444', '#64748b']" :key="c" 
                        @click="newFolderColor = c"
                        :class="['w-8 h-8 rounded-full border-4', newFolderColor === c ? 'border-indigo-100' : 'border-transparent']"
                        :style="{ backgroundColor: c }"></button>
                </div>
                <div class="flex space-x-3">
                    <button @click="showAddFolder = false" class="flex-grow py-4 rounded-2xl font-bold text-slate-400 hover:bg-slate-50 transition-colors">取消</button>
                    <button @click="saveFolder" class="flex-grow py-4 rounded-2xl font-bold bg-indigo-600 text-white shadow-lg shadow-indigo-100 hover:bg-indigo-700 transition-all">建立</button>
                </div>
            </div>
        </div>

        <!-- Add Record Modal -->
        <div v-if="showAddRecord" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-[40px] p-8 w-full max-w-xl shadow-2xl">
                <h3 class="text-xl font-bold mb-6">新增記事</h3>
                <input v-model="newRecord.title" placeholder="標題 (選填)" class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl mb-4 focus:ring-2 focus:ring-indigo-500/20 transition-all font-medium">
                <textarea v-model="newRecord.content" rows="6" placeholder="內容..." class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl mb-6 focus:ring-2 focus:ring-indigo-500/20 transition-all font-medium resize-none"></textarea>
                <div class="flex space-x-3">
                    <button @click="showAddRecord = false" class="flex-grow py-4 rounded-2xl font-bold text-slate-400 hover:bg-slate-50 transition-colors">取消</button>
                    <button @click="saveRecord" class="flex-grow py-4 rounded-2xl font-bold bg-indigo-600 text-white shadow-lg shadow-indigo-100 hover:bg-indigo-700 transition-all">儲存記事</button>
                </div>
            </div>
        </div>
        <lucky-draw :show="showLuckyDraw" :initial-mode="luckyDrawInitialMode" @close="showLuckyDraw = false" />
    </div>
</template>

<script setup>
import { ref, onMounted, computed, defineEmits } from 'vue';
import axios from 'axios';
import KaiwenApproval from './KaiwenApproval.vue';
import RandomGroup from './RandomGroup.vue';
import MobileNavbar from './MobileNavbar.vue';
import LuckyDraw from './LuckyDraw.vue';

const getTodayStr = () => {
    const d = new Date();
    return `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`;
};

const emit = defineEmits(['goHome']);

const resetToRoot = () => {
    activeFolderId.value = null;
    showRandomGroup.value = false;
    showLuckyDraw.value = false;
    searchQuery.value = '';
    focusedId.value = null;
    addMode.value = false;
};

const props = defineProps({
    user: Object
});

const folders = ref([]);
const activeFolderId = ref(null);
const kaiwenRef = ref(null);
const showLuckyDraw = ref(false);
const luckyDrawInitialMode = ref(null);
const showAddFolder = ref(false);
const showAddRecord = ref(false);
const randomGroupRef = ref(null);
const activeAction = ref('');

const newFolderName = ref('');
const newFolderColor = ref('#6366f1');
const newRecord = ref({ title: '', content: '', record_date: getTodayStr() });

const sortedFolders = computed(() => {
    return [...folders.value].sort((a, b) => {
        const isKaiwenA = a.name.includes('開文核定');
        const isKaiwenB = b.name.includes('開文核定');
        const isRandomA = a.name.includes('隨機分組');
        const isRandomB = b.name.includes('隨機分組');

        if (isKaiwenA && !isKaiwenB) return -1;
        if (!isKaiwenA && isKaiwenB) return 1;
        if (isRandomA && !isRandomB && !isKaiwenB) return -1;
        if (!isRandomA && isRandomB && !isKaiwenA) return 1;
        return 0;
    });
});

const activeFolder = computed(() => folders.value.find(f => f.id === activeFolderId.value));

const loadData = async () => {
    const res = await axios.get('/other-folders');
    folders.value = res.data;
    
    // Auto-initialize if empty or missing required folders
    const hasKaiwen = folders.value.some(f => f.name.includes('開文核定'));
    const hasRandom = folders.value.some(f => f.name.includes('隨機分組'));

    if (!hasKaiwen || !hasRandom) {
        if (!hasKaiwen) await axios.post('/other-folders', { name: '開文核定表', color: '#6366f1' });
        if (!hasRandom) await axios.post('/other-folders', { name: '隨機分組', color: '#10b981' });
        return loadData();
    }
};

const saveFolder = async () => {
    if (!newFolderName.value) return;
    await axios.post('/other-folders', { name: newFolderName.value, color: newFolderColor.value });
    newFolderName.value = '';
    showAddFolder.value = false;
    await loadData();
};

const deleteFolder = async (id) => {
    if (!confirm('確定要刪除整個資料夾嗎？內容也會被刪除。')) return;
    await axios.delete(`/other-folders/${id}`);
    if (activeFolderId.value === id) activeFolderId.value = null;
    await loadData();
};

const saveRecord = async () => {
    if (!newRecord.value.content) return;
    await axios.post(`/other-folders/${activeFolderId.value}/records`, newRecord.value);
    newRecord.value = { title: '', content: '', record_date: getTodayStr() };
    showAddRecord.value = false;
    await loadData();
};

const deleteRecord = async (id) => {
    if (!confirm('確定要刪除此記事嗎？')) return;
    await axios.delete(`/other-records/${id}`);
    await loadData();
};

const formatDate = (dateString) => {
    if (!dateString) return '';
    const s = String(dateString).split('T')[0].trim();
    const parts = s.split(/[-/]/);
    if (parts.length === 3) {
        let y = parts[0];
        let m = parts[1].padStart(2, '0');
        let d = parts[2].padStart(2, '0');
        if (!isNaN(parseInt(y)) && !isNaN(parseInt(m)) && !isNaN(parseInt(d))) {
            return `${y}/${m}/${d}`;
        }
    }
    return s.replace(/-/g, '/');
};

const prepareAddFolder = () => {
    showAddFolder.value = true;
};

const prepareAddRecord = () => {
    showAddRecord.value = true;
};

const handleMore = () => {
    // Current placeholder for export or more actions
    alert('此資料夾暫不支援匯出功能');
};

onMounted(loadData);
</script>
