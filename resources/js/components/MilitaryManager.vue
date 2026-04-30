<template>
    <div class="bg-white h-[100vh] flex flex-col relative overflow-hidden">
        <!-- Global Dual Header System -->
        <!-- Header 1: Module Level (Hidden when in folder view to increase density) -->
        <div v-if="!currentFolder" class="border-b border-slate-300 flex items-center bg-white sticky top-0 z-[110] w-full" style="padding: 4px 10px; min-height: 32px;">
            <div class="flex-1 flex flex-col justify-start min-w-0 py-1 pl-1 cursor-pointer" @click="resetToRoot">
                <div class="app-title text-[20px] font-black leading-tight font-outfit tracking-widest break-words text-slate-900">
                    軍隊記錄專區
                </div>
            </div>
            <div class="absolute right-4 top-1/2 -translate-y-1/2">
                <button @click="sortDesc = !sortDesc" class="px-2 py-1 text-[12px] text-indigo-500 bg-indigo-50 border border-indigo-100 rounded-lg active:scale-95 transition-all font-black">
                    {{ sortDesc ? '新→舊' : '舊→新' }}
                </button>
            </div>
        </div>

        <!-- Header 2: Folder/Action Level (Sticky at top when folder selected) -->
        <div v-if="currentFolder" class="border-b border-slate-300 flex items-center bg-white sticky top-0 z-[110] w-full px-[10px] py-1" style="min-height: 32px;">
            <button @click="resetToRoot" class="text-slate-400 p-2 -ml-2 mr-2 active:scale-90 transition-transform shrink-0">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" /></svg>
            </button>
            <div class="flex items-center flex-1 min-w-0 justify-start">
                <h2 class="text-[22px] font-black text-slate-900 truncate tracking-tight font-outfit">
                    {{ currentFolder?.id === 'all' ? '全部軍隊' : currentFolder?.name }}
                </h2>
            </div>
            <div class="ml-2 flex items-center space-x-2">
                <button @click="sortDesc = !sortDesc" class="px-2 py-1 text-[12px] text-indigo-500 bg-indigo-50 border border-indigo-100 rounded-lg active:scale-95 transition-all font-black">
                    {{ sortDesc ? '新→舊' : '舊→新' }}
                </button>
                <button @click="toggleFullTotal" class="px-3 py-1.5 bg-slate-100 text-slate-600 rounded-lg text-[13px] font-black transition-all active:scale-95 shadow-sm whitespace-nowrap">
                    總數
                </button>
            </div>
        </div>

        <!-- SEARCH COMPONENT -->
        <div v-if="showSearch && currentFolder" class="px-[10px] mt-2 animate-fade-in relative flex items-center group">
            <div class="absolute left-[22px] text-slate-400 pointer-events-none">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            <input v-model="searchQuery" type="text" placeholder="搜尋項目..." 
                class="w-full h-[36px] bg-white border border-slate-200 rounded-xl pl-9 pr-10 text-[15px] focus:ring-1 focus:ring-slate-300 outline-none transition-all shadow-sm">
            
            <button @click="searchQuery ? searchQuery = '' : showSearch = false" 
                class="absolute right-[18px] w-8 h-8 flex items-center justify-center text-slate-500 active:text-slate-900 transition-all p-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3.5" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- MAIN CONTENT AREA -->
        <div class="flex-1 overflow-hidden relative flex flex-col">
            <!-- HOME VIEW -->
            <div v-if="!currentFolder && !addMode && !showSearch && !showFullTotal" class="flex-1 overflow-y-auto custom-scrollbar bg-slate-50/30">
                <div class="px-6 pb-24 grid grid-cols-2 gap-4 mt-6 place-items-center">
                    <button v-for="folder in filteredFolders" :key="folder.id" 
                        @click="currentFolder = folder"
                        class="flex flex-col items-center justify-center active:scale-95 transition-all group relative">
                        <div class="relative w-[150px] h-[150px]">
                            <svg class="w-full h-full transition-transform group-hover:scale-105 drop-shadow-lg" viewBox="0 0 64 64" fill="none">
                                <defs>
                                    <linearGradient :id="'fGrad' + folder.id" x1="0%" y1="0%" x2="100%" y2="100%">
                                        <stop offset="0%" :stop-color="getGradientColor(folder.color).start" />
                                        <stop offset="100%" :stop-color="getGradientColor(folder.color).end" />
                                    </linearGradient>
                                </defs>
                                <path d="M4 14C4 11.7909 5.79086 10 8 10H24.5L30 16H56C58.2091 16 60 17.7909 60 20V50C60 52.2091 58.2091 54 56 54H8C5.79086 54 4 52.2091 4 50V14Z" :fill="'url(#fGrad' + folder.id + ')'" />
                                <path d="M4 22C4 19.7909 5.79086 18 8 18H56C58.2091 18 60 19.7909 60 22V50C60 52.2091 58.2091 54 56 54H8C5.79086 54 4 52.2091 4 50V22Z" :fill="'url(#fGrad' + folder.id + ')'" stroke="rgba(255,255,255,0.4)" stroke-width="1"/>
                            </svg>
                            <div class="absolute inset-0 flex items-center justify-center pt-5 px-3">
                                <span class="text-[20px] font-black text-white tracking-tight leading-tight text-center drop-shadow-sm">{{ folder.name }}</span>
                            </div>
                        </div>
                    </button>
                </div>
            </div>

            <!-- LEDGER VIEW -->
            <div v-if="(currentFolder || addMode || showSearch)" class="flex-1 overflow-y-auto px-[10px] bg-white flex flex-col pb-24">
                <!-- Full Total Modal (Centered) -->
                <div v-if="showFullTotal && currentFolder" class="fixed inset-0 z-[300] flex items-center justify-center p-6 bg-slate-900/60 backdrop-blur-sm animate-fade-in" @click="showFullTotal = false">
                    <div class="bg-white w-full max-w-sm rounded-[32px] p-8 shadow-2xl animate-pop-in relative" @click.stop>
                        <button @click="showFullTotal = false" class="absolute right-6 top-6 text-slate-300 hover:text-slate-600 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </button>
                        
                        <div class="text-center space-y-6">
                            <div class="w-16 h-16 bg-indigo-50 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </div>
                            
                            <div class="space-y-2">
                                <h3 class="app-body text-slate-500 font-bold uppercase tracking-widest">{{ currentFolder.name }} 總計</h3>
                                <div class="app-body font-black text-indigo-600" style="font-size: 24px !important;">
                                    {{ formatArmyTotal(currentFolderTotal) }}
                                </div>
                            </div>

                            <div v-if="['黑曜軍', '耀紫軍'].includes(currentFolder.name)" class="grid grid-cols-2 gap-4 pt-6 border-t border-slate-100">
                                <template v-if="currentFolder.name === '黑曜軍'">
                                    <div class="flex flex-col items-center">
                                        <span class="app-body text-slate-400 font-bold uppercase tracking-widest" style="font-size: 13px !important;">閻尊</span>
                                        <span class="app-body text-slate-900 font-bold">{{ formatArmyTotal(breakdownTotals.yan_zun) }}</span>
                                    </div>
                                    <div class="flex flex-col items-center">
                                        <span class="app-body text-slate-400 font-bold uppercase tracking-widest" style="font-size: 13px !important;">閻闇</span>
                                        <span class="app-body text-slate-900 font-bold">{{ formatArmyTotal(breakdownTotals.yan_an) }}</span>
                                    </div>
                                </template>
                                <template v-else-if="currentFolder.name === '耀紫軍'">
                                    <div class="flex flex-col items-center">
                                        <span class="app-body text-slate-400 font-bold uppercase tracking-widest" style="font-size: 13px !important;">龍勝</span>
                                        <span class="app-body text-slate-900 font-bold">{{ formatArmyTotal(breakdownTotals.long_sheng) }}</span>
                                    </div>
                                    <div class="flex flex-col items-center">
                                        <span class="app-body text-slate-400 font-bold uppercase tracking-widest" style="font-size: 13px !important;">龍戰</span>
                                        <span class="app-body text-slate-900 font-bold">{{ formatArmyTotal(breakdownTotals.long_zhan) }}</span>
                                    </div>
                                </template>
                            </div>

                            <button @click="showFullTotal = false" class="w-full py-4 bg-slate-900 text-white rounded-2xl font-black text-[18px] active:scale-95 transition-all mt-4">
                                關閉視窗
                            </button>
                        </div>
                    </div>
                </div>

                <!-- List Content -->
                <div v-if="!showFullTotal" class="flex flex-col flex-1">
                    <div v-if="loading" class="text-center py-10 text-xs text-slate-400">載入中...</div>
                    <div v-else-if="filteredItems.length === 0" class="text-center py-20 text-slate-400 font-light px-6">
                        目前尚無{{ currentFolder?.id !== 'all' ? currentFolder?.name : '' }}載錄資料。
                    </div>
                    <div v-else class="flex flex-col flex-1 px-1">
                        <!-- Cumulative Quantity Prompt Card -->
                        <div v-if="showCumulativePrompt && currentFolder && !showSearch && !addMode && !focusedId" 
                            class="mx-1 mt-2 mb-4 p-4 bg-indigo-50 border border-indigo-100 rounded-2xl relative animate-fade-in">
                            <button @click.stop="dismissCumulativePrompt" class="absolute right-3 top-3 text-indigo-300 hover:text-indigo-600 transition-colors p-1">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </button>
                            <div class="flex items-start space-x-3">
                                <div class="bg-indigo-600 p-2 rounded-xl text-white shrink-0">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </div>
                                <div class="flex-1 pr-6">
                                    <h3 class="text-[17px] font-black text-indigo-900 leading-tight">軍隊數量統計</h3>
                                    <p class="text-[14px] text-indigo-700/70 mt-1 font-medium">系統已準備就緒，點擊導覽列「新增」即可開始登錄軍隊資訊。</p>
                                </div>
                            </div>
                        </div>

                        <template v-for="group in groupedItems" :key="group.date">
                            <!-- Date Header -->
                            <div v-if="focusedId === null" 
                                @click="toggleDateGroup(group.date)"
                                class="px-3 py-1.5 bg-slate-50 border-y border-slate-100 flex items-center justify-between sticky top-0 z-20 cursor-pointer active:bg-slate-100 transition-colors">
                                <div class="flex items-center">
                                    <svg :class="{'rotate-[-90deg]': expandedDate !== group.date}" class="w-4 h-4 text-slate-400 mr-2 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" /></svg>
                                    <span class="app-title font-outfit uppercase tracking-wider">{{ group.date }}</span>
                                </div>
                                <span class="text-[12px] font-bold text-slate-400">共 {{ group.items.length }} 筆</span>
                            </div>

                        <div v-if="expandedDate === group.date" class="bg-white">
                            <div v-for="item in group.items" :key="item.id" 
                                v-show="focusedId === null || focusedId === item.id"
                                @click="toggleExpand(item.id)"
                                :class="[
                                    'py-[15px] px-[12px] border-b border-slate-200 last:border-b-0 relative group transition-all cursor-pointer z-0',
                                    openMenuId === item.id ? 'z-[50]' : 'z-0',
                                    { 'border-b-0': focusedId === item.id }
                                ]"
                            >
                                <div class="animate-fade-in py-2 bg-white space-y-4 relative px-1.5">
                                    <!-- Row 1: Date -->
                                    <div class="military-field">
                                        <label class="military-label">日期</label>
                                        <div class="military-date-value">{{ formatDate(item.know_date) || '歷史累積' }}</div>
                                    </div>
                                    
                                    <!-- Row 2: Name & Quantity -->
                                    <div class="grid grid-cols-2 gap-x-4 pr-8 relative">
                                        <div class="military-field min-w-0">
                                            <label class="military-label">法號</label>
                                            <div class="military-value-name truncate">
                                                {{ item.user_name }}
                                                <span v-if="item.user_remarks" class="block text-slate-400 opacity-70 font-medium" style="font-size: 0.85em;">
                                                    {{ item.user_remarks }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="military-field">
                                            <label class="military-label">數量</label>
                                            <div class="military-value">{{ formatArmyTotal(item.quantity) }}</div>
                                        </div>

                                        <!-- Menu Trigger (Right side of Row 2) -->
                                        <div class="absolute right-[-32px] top-0">
                                            <button @click.stop="toggleMenu(item.id)" class="p-1 text-slate-300 hover:text-indigo-600 active:scale-95 transition-all">
                                                <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"><path d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM18 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                                            </button>
                                            <!-- Dropdown Menu -->
                                            <div v-if="openMenuId === item.id" @click.stop class="absolute right-0 top-full mt-1 w-auto min-w-[140px] bg-white rounded-2xl shadow-2xl border border-slate-100 z-[110] overflow-hidden animate-slide-up py-1">
                                                <button @click.stop="editItem(item); openMenuId = null" class="w-full px-4 py-3 text-left text-[17px] font-black text-slate-900 hover:bg-slate-50 border-b border-slate-50 whitespace-nowrap">修改內容</button>
                                                <button @click.stop="copySingleRecord(item); openMenuId = null" class="w-full px-4 py-3 text-left text-[17px] font-black text-slate-900 hover:bg-slate-50 border-b border-slate-50 whitespace-nowrap">複製貼 LINE</button>
                                                                          <button @click.stop="deleteItem(item.id)" class="w-full px-4 py-3 text-left text-[17px] font-black text-red-600 hover:bg-red-50 whitespace-nowrap">刪除</button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Expanded Content (Show Remarks & Breakdown when focused) -->
                                    <div v-if="focusedId === item.id" class="pt-4 border-t border-slate-100 space-y-4 animate-fade-in">
                                        <!-- Specialized breakdown for split armies -->
                                        <div v-if="['黑曜軍','耀紫軍'].includes(item.army_type)" class="military-field">
                                            <label class="military-label">軍隊細目</label>
                                            <div class="flex items-center space-x-6">
                                                <template v-if="item.army_type === '黑曜軍'">
                                                    <div class="flex items-center space-x-2">
                                                        <span class="w-2 h-2 rounded-full bg-slate-900"></span>
                                                        <span class="military-value">閻尊: {{ formatArmyTotal(item.yan_zun) }}</span>
                                                    </div>
                                                    <div class="flex items-center space-x-2">
                                                        <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                                                        <span class="military-value">閻闇: {{ formatArmyTotal(item.yan_an) }}</span>
                                                    </div>
                                                </template>
                                                <template v-else-if="item.army_type === '耀紫軍'">
                                                    <div class="flex items-center space-x-2">
                                                        <span class="w-2 h-2 rounded-full bg-purple-600"></span>
                                                        <span class="military-value">龍勝: {{ formatArmyTotal(item.long_sheng) }}</span>
                                                    </div>
                                                    <div class="flex items-center space-x-2">
                                                        <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                                                        <span class="military-value">龍戰: {{ formatArmyTotal(item.long_zhan) }}</span>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>

                                        <div v-if="item.remarks_text" class="military-field">
                                            <label class="military-label">詳細內容 / 備註</label>
                                            <div class="military-value leading-relaxed whitespace-pre-wrap bg-slate-50/50 p-3 rounded-xl border border-slate-100/50">
                                                {{ item.remarks_text }}
                                            </div>
                                        </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
                </div>
            </div>
        </div>
    </div>

    <!-- FAB Bottom Navigation -->
        <mobile-navbar 
            :can-back="true"
            :show-action="true"
            :action-disabled="false"
            :action-active="showAddMenu"
            :search-active="showSearch"
            :can-search="true"
            :can-more="true"
            @back="handleBack"
            @home="$emit('goHome')"
            @action="showAddMenu = !showAddMenu"
            @search="showSearch = !showSearch"
            @more="exportToExcel"
        />

        <!-- MODALS -->
        <add-action-menu :show="showAddMenu" @close="showAddMenu = false" :actions="addActions" />
        <military-add-form :key="editingId || 'new'" :show="addMode" :initialData="form" :editingId="editingId" :users="users" :armyType="currentFolder ? currentFolder.name : ''" :isCumulative="isCumulativeMode" @save="saveItem" @cancel="addMode = false; editingId = null" />
        <military-batch-add :show="batchMode" :armyType="currentFolder ? currentFolder.name : ''" @save="batchMode = false; loadData()" @cancel="batchMode = false" />
    </div>
</template>


<script setup>
import { ref, computed, onMounted, watch } from 'vue';
// Refreshed component logic to resolve HMR issues
import axios from 'axios';
import MilitaryAddForm from './MilitaryAddForm.vue';
import MilitaryBatchAdd from './MilitaryBatchAdd.vue';
import AddActionMenu from './AddActionMenu.vue';
import MobileNavbar from './MobileNavbar.vue';
const getTodayStr = () => {
    const d = new Date();
    return `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`;
};
const props = defineProps(['user']);
const emit = defineEmits(['goHome']);

const resetToRoot = () => {
    currentFolder.value = null;
    addMode.value = false;
    batchMode.value = false;
    searchQuery.value = '';
    focusedId.value = null;
    openMenuId.value = null;
};

const folders_list = [
    { id: 'armor', name: '虎甲軍', color: 'bg-indigo-500' },
    { id: 'brave', name: '虎賁軍', color: 'bg-blue-500' },
    { id: 'obsidian', name: '黑曜軍', color: 'bg-slate-700' },
    { id: 'purple', name: '耀紫軍', color: 'bg-purple-500' }
];

const filteredFolders = computed(() => {
    const allowed = props.user?.permissions?.allowed_armies || [];
    const isAdmin = props.user?.is_admin || props.user?.role === 'admin' || props.user?.role === '管理員';
    if (isAdmin) return folders_list;
    return folders_list.filter(f => allowed.includes(f.name));
});

const currentFolder = ref(null);
const addMode = ref(false);
const batchMode = ref(false);
const showAddMenu = ref(false);
const showSearch = ref(false);
const isCumulativeMode = ref(false);
const showCumulativePrompt = ref(!localStorage.getItem('hide_mil_cumulative_prompt'));
const showCumulativeAction = ref(true);
const expandedDate = ref(null);
const toggleDateGroup = (date) => {
    expandedDate.value = expandedDate.value === date ? null : date;
};
const dismissCumulativePrompt = () => {
    showCumulativePrompt.value = false;
    localStorage.setItem('hide_mil_cumulative_prompt', 'true');
};
const hideCumulativeAction = () => {
    showCumulativeAction.value = false;
};
const searchQuery = ref('');
const openMenuId = ref(null);
const items = ref([]);
const users = ref([]);
const loading = ref(true);
const editingId = ref(null);
const focusedId = ref(null);
const deleteConfirmId = ref(null);
const sortDesc = ref(true);
const openStatusId = ref(null);
const showFullTotal = ref(false);
let fullTotalTimer = null;

const formatArmyTotal = (num) => {
    num = Number(num) || 0;
    if (num < 1000000) return `${num.toLocaleString()} 位`;
    const troops = Math.floor(num / 1000000);
    const remaining = num % 1000000;
    if (remaining === 0) return `${troops}隊`;
    const wan = Math.floor(remaining / 10000);
    const rest = remaining % 10000;
    let res = `${troops}隊`;
    if (wan > 0) res += `${wan}萬`;
    if (rest > 0) res += `${rest}位`;
    else if (wan === 0) res += `0位`;
    return res;
};

const toggleFullTotal = () => {
    showFullTotal.value = !showFullTotal.value;
};

const filteredItems = computed(() => {
    const allowed = props.user?.permissions?.allowed_armies || [];
    const isAdmin = props.user?.is_admin || props.user?.role === 'admin' || props.user?.role === '管理員';
    let filtered = items.value.filter(i => isAdmin || allowed.includes(i.army_type) || i.user_id === props.user?.id);

    if (currentFolder.value) {
        const target = currentFolder.value.name;
        filtered = filtered.filter(i => i.army_type === target);
    }
    
    if (searchQuery.value) {
        const q = searchQuery.value.trim().toLowerCase();
        filtered = filtered.filter(i => 
            i.user_name?.toLowerCase().includes(q) || 
            i.user_remarks?.toLowerCase().includes(q) ||
            i.remarks_text?.toLowerCase().includes(q)
        );
    }
    return filtered;
});
const sortedItems = computed(() => {
    let result = [...filteredItems.value];
    
    result.sort((a, b) => {
        // Records without know_date (Historical Accumulation) always go to the bottom
        if (!a.know_date && b.know_date) return 1;
        if (a.know_date && !b.know_date) return -1;
        if (!a.know_date && !b.know_date) return a.id - b.id;

        const dateA = String(a.know_date || '');
        const dateB = String(b.know_date || '');
        if (dateA !== dateB) {
            return sortDesc.value ? dateB.localeCompare(dateA) : dateA.localeCompare(dateB);
        }
        return sortDesc.value ? b.id - a.id : a.id - b.id;
    });

    let groupIndex = 0;
    return result.map((item, idx) => {
        if (idx > 0) {
            const prev = result[idx - 1];
            if (item.user_name !== prev.user_name || item.know_date !== prev.know_date) {
                groupIndex++;
            }
        }
        return { ...item, groupParity: groupIndex % 2 };
    });
});

const groupedItems = computed(() => {
    const sorted = sortedItems.value;
    const groups = [];
    let currentGroup = null;

    sorted.forEach(item => {
        const dateStr = item.know_date ? formatDate(item.know_date) : '原始數量';
        if (!currentGroup || currentGroup.date !== dateStr) {
            currentGroup = {
                date: dateStr,
                items: []
            };
            groups.push(currentGroup);
        }
        currentGroup.items.push(item);
    });

    return groups;
});

const totalQuantity = computed(() => items.value.reduce((sum, i) => sum + (Number(i.quantity) || 0), 0));
const currentFolderTotal = computed(() => filteredItems.value.reduce((sum, i) => sum + (Number(i.quantity) || 0), 0));

const breakdownTotals = computed(() => {
    return filteredItems.value.reduce((acc, i) => {
        acc.yan_zun += (Number(i.yan_zun) || 0);
        acc.yan_an += (Number(i.yan_an) || 0);
        acc.long_sheng += (Number(i.long_sheng) || 0);
        acc.long_zhan += (Number(i.long_zhan) || 0);
        acc.yan_jue += (Number(i.yan_jue) || 0);
        acc.yan_ze += (Number(i.yan_ze) || 0);
        acc.yan_di += (Number(i.yan_di) || 0);
        acc.yan_yuan += (Number(i.yan_yuan) || 0);
        return acc;
    }, { yan_zun: 0, yan_an: 0, long_sheng: 0, long_zhan: 0, yan_jue: 0, yan_ze: 0, yan_di: 0, yan_yuan: 0 });
});

const toggleMenu = (id) => { openMenuId.value = openMenuId.value === id ? null : id; };
const toggleStatusMenu = (id) => { openStatusId.value = openStatusId.value === id ? null : id; };
const toggleExpand = (id) => { focusedId.value = focusedId.value === id ? null : id; };

const addActions = computed(() => {
    const actions = [];
    
    actions.push({
        label: '原始數量',
        description: '錄入歷史累積之軍隊數量',
        icon: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>',
        colorClass: 'bg-amber-50 text-amber-600',
        isDismissible: false,
        handler: () => {
            editingId.value = null;
            form.value = {
                user_name: null, user_remarks: '', destination: '已處理', quantity: 0,
                yan_zun: 0, yan_an: 0, long_sheng: 0, long_zhan: 0,
                know_date: null, process_date: '', remarks_text: '',
                army_type: currentFolder.value?.name
            };
            isCumulativeMode.value = true;
            addMode.value = true;
        }
    });

    actions.push({ 
        label: '逐筆新增', 
        description: `輸入單筆${currentFolder.value?.name || '軍隊'}紀錄`,
        icon: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>',
        handler: () => { 
            editingId.value = null;
            const defaultQty = (currentFolder.value?.name === '虎甲軍' || currentFolder.value?.name === '虎賁軍') ? 1 : ((currentFolder.value?.id === 'armor' || currentFolder.value?.id === 'brave') ? 1 : 0);
            form.value = {
                user_name: '', user_remarks: '', destination: '未處理', quantity: defaultQty,
                yan_zun: 0, yan_an: 0, long_sheng: 0, long_zhan: 0,
                know_date: '', process_date: '', remarks_text: '',
                army_type: currentFolder.value?.name
            };
            isCumulativeMode.value = false;
            addMode.value = true; 
        } 
    });

    actions.push({ 
        label: '多筆新增', 
        description: `貼上文字清單批次匯入`,
        icon: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>',
        handler: () => { 
            batchMode.value = true;
        } 
    });

    actions.push({ 
        label: '匯出 Excel', 
        description: `下載${currentFolder.value?.name || '軍隊'}報表檔案`,
        icon: '<svg fill="currentColor" viewBox="0 0 24 24"><path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/></svg>',
        handler: () => { 
            exportToExcel();
        } 
    });

    actions.push({ 
        label: '複製貼 LINE', 
        description: `將清單轉為文字複製到 LINE`,
        icon: '<svg fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-1-13h2v6h-2zm0 8h2v2h-2z"/></svg>',
        handler: () => { 
            copyToLine();
        } 
    });

    return actions;
});

const getVisualWidth = (str) => {
    let width = 0;
    for (let i = 0; i < str.length; i++) {
        const charCode = str.charCodeAt(i);
        if (charCode >= 0 && charCode <= 128) width += 1; // Half-width
        else width += 2; // Full-width (CJK)
    }
    return width;
};

// Precise padding for LINE (text use full-width space, numbers use figure space)
const padToWidthPrecise = (str, width, isNumeric = false) => {
    const currentWidth = getVisualWidth(str);
    if (currentWidth >= width) return str;
    
    if (isNumeric) {
        // USE FIGURE SPACE (\u2007) FOR NUMBERS - it has same width as digits
        const padding = '\u2007'.repeat(width - currentWidth);
        return padding + str;
    } else {
        // USE FULL-WIDTH SPACE (\u3000) FOR CJK TEXT - 1 char = 2 units
        let remaining = width - currentWidth;
        let padded = str;
        while (remaining >= 2) {
            padded += '\u3000';
            remaining -= 2;
        }
        while (remaining >= 1) {
            padded += ' ';
            remaining -= 1;
        }
        return padded;
    }
};

const copyToLine = () => {
    if (!sortedItems.value.length) return alert('目前沒有資料可供複製');
    
    let text = `【${currentFolder.value?.name || '軍隊'} 載錄報表】\n\n`;
    
    // Header - Precise alignment using \u3000 and \u2007
    const hDate = padToWidthPrecise('日期', 10);
    const hName = padToWidthPrecise('法號', 18);
    const hQty  = '數量';
    text += `${hDate} | ${hName} | ${hQty}\n`;
    text += `----------------------------------------\n`;
    
    let total = 0;
    sortedItems.value.forEach(item => {
        const dateStr = formatDate(item.know_date);
        const fullName = item.user_name + (item.user_remarks || '');
        const qtyNum = Number(item.quantity) || 0;
        total += qtyNum;
        
        // Body - Precise alignment
        const cDate = padToWidthPrecise(dateStr, 10);
        const cName = padToWidthPrecise(fullName, 18);
        const cQty  = padToWidthPrecise(qtyNum.toString(), 4, true); // Pad numeric part
        
        text += `${cDate} | ${cName} | ${cQty}\n`;
    });
    
    text += `----------------------------------------\n`;
    const footerLabel = padToWidthPrecise('總結：', 30);
    const footerQty   = padToWidthPrecise(total.toString(), 4, true);
    text += `${footerLabel} | ${footerQty}\n`;
    
    navigator.clipboard.writeText(text).then(() => {
        alert('已複製到剪貼簿，快去 LINE 貼上吧！');
    }).catch(err => {
        alert('複製失敗，請手動全選複製。');
    });
};

const exportToExcel = () => {
    if (!filteredItems.value.length) return alert('目前沒有資料可供匯出');
    
    // Load XLSX via script if not present
    if (typeof XLSX === 'undefined') {
        const script = document.createElement('script');
        script.src = "https://cdn.sheetjs.com/xlsx-latest/package/dist/xlsx.full.min.js";
        script.onload = () => performExcelExport();
        document.head.appendChild(script);
    } else {
        performExcelExport();
    }
};

const performExcelExport = () => {
    let total = 0;
    const data = sortedItems.value.map(item => {
        const qty = Number(item.quantity) || 0;
        total += qty;
        
        const row = {};
        row['日期'] = formatDate(item.know_date);
        row['法號'] = item.user_name + (item.user_remarks || '');
        
        if (item.army_type === '黑曜軍') {
            row['閻尊量'] = item.yan_zun || 0;
            row['閻闇量'] = item.yan_an || 0;
        } else if (item.army_type === '耀紫軍') {
            row['龍勝量'] = item.long_sheng || 0;
            row['龍戰量'] = item.long_zhan || 0;
        }
        
        row['總數量'] = qty;
        row['備註'] = item.remarks_text || '';
        return row;
    });

    // Add Total Row at the end
    data.push({
        '日期': '',
        '法號': '總量',
        '總數量': total,
        '備註': ''
    });

    const worksheet = XLSX.utils.json_to_sheet(data);
    const workbook = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(workbook, worksheet, currentFolder.value?.name || '全部');
    
    const dateStr = getTodayStr().replace(/-/g, '');
    const fileName = `${currentFolder.value?.name || '軍隊'}_載錄報表_${dateStr}.xlsx`;
    
    XLSX.writeFile(workbook, fileName);
};

const triggerSimpleDownload = (text, filename) => {
    const blob = new Blob(['\uFEFF' + text], { type: 'text/plain;charset=utf-8' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = filename;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    window.URL.revokeObjectURL(url);
};

const formatMilitaryRecordForFile = (item) => {
    let res = `【軍隊記錄 - ${item.army_type}】\r\n`;
    res += `日期：${formatDate(item.know_date)}\r\n`;
    res += `法號：${item.user_name}${item.user_remarks ? '(' + item.user_remarks + ')' : ''}\r\n`;
    
    if (['黑曜軍', '耀紫軍'].includes(item.army_type)) {
        if (item.army_type === '黑曜軍') {
            res += `閻尊：${(item.yan_zun || 0).toLocaleString()}\r\n`;
            res += `閻闇：${(item.yan_an || 0).toLocaleString()}\r\n`;
        } else {
            res += `龍勝：${(item.long_sheng || 0).toLocaleString()}\r\n`;
            res += `龍戰：${(item.long_zhan || 0).toLocaleString()}\r\n`;
        }
    }
    
    res += `總數：${formatArmyTotal(item.quantity)}\r\n`;
    res += `備註：${item.remarks_text || '無'}`;
    return res;
};

const copySingleRecord = async (item) => {
    const text = formatMilitaryRecordForFile(item);
    try {
        await navigator.clipboard.writeText(text);
        alert('已複製單筆記錄');
    } catch (e) {
        alert('複製失敗');
    }
};

const downloadSingleRecord = (item) => {
    const text = formatMilitaryRecordForFile(item);
    triggerSimpleDownload(text, `${item.army_type}_${item.user_name}_${formatDate(item.know_date).replace(/\//g, '')}.txt`);
};

const form = ref({});

const loadData = async () => {
    loading.value = true;
    try {
        const ts = new Date().getTime();
        const [res, dres] = await Promise.all([ 
            axios.get(`/military-records?t=${ts}`), 
            axios.get(`/api/dharma-names-list?t=${ts}`) 
        ]);
        items.value = Array.isArray(res.data) ? res.data : (res.data.data || []);
        users.value = dres.data;
    } catch (e) { 
        console.error('Load data failed:', e);
        items.value = []; 
    } finally { 
        loading.value = false;
        // All groups are expanded by default as collapsedGroups starts empty
    }
};

const saveItem = async (formData) => {
    try {
        if (editingId.value) await axios.put(`/military-records/${editingId.value}`, formData);
        else await axios.post('/military-records', formData);
        
        addMode.value = false; 
        editingId.value = null;
        searchQuery.value = ''; 
        focusedId.value = null;
        
        // Removed auto-hide of cumulative action
        
        await loadData();
    } catch (e) { 
        console.error('Save failed:', e);
        const msg = e.response?.data?.message || e.response?.data?.error || '儲存失敗，請檢查資料格式';
        alert(msg); 
    }
};

const handleBack = () => {
    if (addMode.value) addMode.value = false;
    else if (batchMode.value) batchMode.value = false;
    else if (focusedId.value) focusedId.value = null;
    else if (currentFolder.value) currentFolder.value = null;
    else emit('goHome');
};

const editItem = (item) => {
    editingId.value = item.id;
    form.value = { ...item };
    addMode.value = true;
    openMenuId.value = null;
};

const deleteItem = async (id) => {
    if (!confirm('刪除？')) return;
    await axios.delete(`/military-records/${id}`);
    focusedId.value = null;
    openMenuId.value = null;
    loadData();
};

const formatDate = (dateStr) => {
    if (!dateStr || dateStr === '-' || dateStr === '未設定' || dateStr === '原始數量') return '';
    const s = String(dateStr).split(/[T ]/)[0].trim();
    const parts = s.split(/[-/]/);
    if (parts.length === 3) {
        let y = parts[0];
        let m = parts[1].padStart(2, '0');
        let d = parts[2].padStart(2, '0');
        return `${y}/${m}/${d}`;
    }
    return s.replace(/-/g, '/');
};

const getGradientColor = (colorClass) => {
    const map = {
        'bg-indigo-500': { start: '#475569', end: '#0f172a' }, // Tiger Armor -> Black
        'bg-blue-500':   { start: '#334155', end: '#020617' }, // Tiger Brave -> Black
        'bg-slate-700':  { start: '#64748b', end: '#1e293b' }, // Obsidian -> Dark Slate
        'bg-purple-500': { start: '#d8b4fe', end: '#a855f7' }  // Purple -> Purple
    };
    return map[colorClass] || { start: '#cbd5e1', end: '#64748b' };
};

watch(currentFolder, (newVal) => {
    if (newVal) {
        expandedDate.value = null;
    }
});

onMounted(loadData);
</script>

<style scoped>
.military-field { display: flex; flex-direction: column; gap: 4px; }
.military-label {
    font-family: 'Noto Sans TC', sans-serif;
    font-weight: 900;
    color: #94a3b8;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    white-space: nowrap;
}
.military-value {
    font-family: 'Montserrat', sans-serif;
    font-weight: 500;
    color: #0f172a;
}
.military-value-name {
    font-family: 'Montserrat', sans-serif;
    font-weight: 600;
    color: #0f172a;
}
.military-date-value {
    font-family: 'Montserrat', sans-serif;
    font-weight: 500;
    color: #0f172a;
}

/* Custom Military Scaling Logic */
:deep(body.font-small) .military-label, :deep(body.font-small) .military-date-value { font-size: 13px !important; }
:deep(body.font-small) .military-value, :deep(body.font-small) .military-value-name { font-size: 15px !important; }

:deep(body.font-medium) .military-label, :deep(body.font-medium) .military-date-value { font-size: 15px !important; }
:deep(body.font-medium) .military-value, :deep(body.font-medium) .military-value-name { font-size: 17px !important; }

:deep(body.font-large) .military-label, :deep(body.font-large) .military-date-value { font-size: 15px !important; }
:deep(body.font-large) .military-value, :deep(body.font-large) .military-value-name { font-size: 19px !important; }

.animate-fade-in { animation: fadeIn 0.3s ease-out; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }
.animate-slide-up { animation: slideUp 0.15s ease-out; }
@keyframes slideUp { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
</style>
