<template>
    <div class="bg-white h-[100dvh] flex flex-col relative overflow-hidden">
        <!-- Delete Confirmation / Status Toast -->
        <div v-if="persistentToast" class="fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 z-[9999] pointer-events-auto">
            <div class="bg-white rounded-3xl shadow-[0_20px_60px_rgba(0,0,0,0.3)] flex flex-col border border-slate-100 overflow-hidden p-[28px] min-w-[320px]" style="max-width: calc(100vw - 32px);">
                <div class="flex items-start justify-between mb-8">
                    <span class="text-[17px] font-black text-slate-900 leading-relaxed tracking-widest">
                        {{ persistentToast.msg }}
                    </span>
                    <button @click="persistentToast = null" class="ml-6 text-slate-400 hover:text-slate-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="3" stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div v-if="persistentToast.type === 'deleteConfirm'" class="flex space-x-4">
                    <button @click="persistentToast = null" class="flex-1 bg-slate-100 text-slate-500 h-[52px] rounded-2xl border border-slate-200 text-[18px] font-black tracking-widest active:scale-95 transition-all">取消</button>
                    <button @click="executeDelete" class="flex-1 bg-red-600 !text-white h-[52px] rounded-2xl border border-red-500 text-[18px] font-black tracking-widest active:scale-95 transition-all shadow-lg shadow-red-100">確定刪除</button>
                </div>
                <div v-else class="flex justify-end mt-2">
                    <button @click="persistentToast = null" class="bg-indigo-600 !text-white px-10 py-3.5 rounded-2xl text-[18px] font-black tracking-widest active:scale-95 transition-all shadow-lg shadow-indigo-100">確定</button>
                </div>
            </div>
        </div>

        <!-- Static Header -->
        <div class="border-b border-gray-100 flex items-center bg-white sticky top-0 z-30 px-[15px] w-full pb-2">
            <div class="flex-1 flex flex-col justify-start min-w-0 py-1 pl-1 cursor-pointer" @click="resetToRoot">
                <h1 class="leading-tight font-outfit tracking-widest break-words !font-black !text-[#0f172a] pt-[5px]" style="color: #0f172a !important; font-size: 32px !important; font-weight: 900 !important;">{{ displayTitle }}</h1>
            </div>
            <div class="flex items-center justify-end shrink-0 space-x-1 pr-2 pt-[15px]">
                <button @click.stop="sortDesc = !sortDesc" class="px-2 py-1 text-indigo-600 active:scale-95 transition-all" style="font-size: 16px !important;">
                    {{ sortDesc ? '新→舊' : '舊→新' }}
                </button>
                <button @click="toggleShowTotal" class="text-slate-900 active:scale-95 transition-all" style="font-size: 16px !important;">
                    總數
                </button>
                <button v-if="focusedId" @click="focusedId = null" class="w-8 h-8 flex items-center justify-center bg-slate-100 text-slate-400 rounded-xl active:scale-90 transition-all ml-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" /></svg>
                </button>
            </div>
        </div>
        
        <!-- Search Component -->
        <div v-if="showSearch" class="px-2 py-2 animate-fade-in">
            <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-indigo-400 group-focus-within:text-indigo-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </div>
                <input v-model="searchQuery" type="text" placeholder="搜尋項目..." 
                    class="block w-full pl-11 pr-12 h-[52px] bg-slate-50 border-2 border-transparent focus:border-indigo-100 focus:bg-white rounded-2xl text-[17px] font-black font-outfit text-slate-800 placeholder-slate-300 transition-all outline-none shadow-sm">
                <button v-if="searchQuery" @click="searchQuery = ''" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-300 hover:text-red-500 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
            </div>
        </div>

        <!-- Total Simple Overlay (Updated with Breakdowns) -->
        <div v-if="showTotal" class="fixed inset-x-0 top-[80px] z-[60] px-4 animate-fade-in pointer-events-none">
            <div class="bg-white text-slate-900 px-8 py-5 rounded-3xl shadow-2xl flex flex-col pointer-events-auto border border-slate-100 relative w-auto min-w-[280px] max-w-md mx-auto">
                <div class="flex items-center justify-between" :class="{ 'mb-4': !searchQuery }">
                    <div class="flex flex-col">
                        <span class="app-body text-slate-400 font-bold uppercase tracking-widest !text-[20px]">{{ searchQuery ? `${searchQuery} 之載錄總量` : '怨靈載錄總量' }}</span>
                        <span class="app-body text-slate-900 font-bold !text-[20px]">{{ formatArmyTotal(totalGrudgeQuantity) }}</span>
                    </div>
                    <button @click="showTotal = false" class="p-2 text-slate-300 active:scale-90 transition-all">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                </div>

            </div>
        </div>

        <!-- Scrollable Content -->
        <div v-if="!showTotal" @click="clickToCollapse" class="flex-1 overflow-y-auto custom-scrollbar min-h-full w-full pb-[80px] relative">
            <div v-if="loading" class="absolute inset-0 z-20 flex items-start justify-center pt-10 bg-white/60 pointer-events-none">
                <div class="inline-block animate-spin rounded-full h-6 w-6 border-4 border-slate-100 border-t-indigo-600"></div>
            </div>
            <div v-if="isEmptyState" class="text-center py-20 text-slate-400 font-light">目前尚無怨靈載錄資料。</div>
            <div v-else class="flex flex-col flex-1 px-2 pt-0">
                <!-- Drill-down: Date Groups List (Level 1) -->
                <template v-if="!activeDateGroup && !focusedId">
                    <div v-for="group in dateGroupsData" :key="group.know_date || 'historical'"
                        @click.stop="activeDateGroup = group.know_date ? formatDate(group.know_date) : '歷史累積'" 
                        class="p-[10px] bg-white border-b border-slate-100 flex items-center justify-between cursor-pointer active:bg-slate-50 transition-colors group">
                        <div class="flex items-center">
                            <span class="app-title font-outfit tracking-wider text-[14px] font-normal text-slate-800">{{ group.know_date ? formatDate(group.know_date) : '歷史累積' }}</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <div class="flex items-center">
                                <span class="text-black text-[14px] font-normal drop-shadow-sm">{{ formatArmyTotal(group.total_qty) }}</span>
                            </div>
                            <svg class="w-5 h-5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" /></svg>
                        </div>
                    </div>
                </template>

                <!-- Drill-down: Specific Date Records List (Level 2) -->
                <template v-else>
                    <!-- Date Header Back Button -->
                    <div v-if="activeDateGroup && focusedId === null" 
                        @click.stop="activeDateGroup = null" 
                        class="px-4 py-2.5 bg-slate-50 border-y border-slate-200 flex items-center sticky top-0 z-20 cursor-pointer active:bg-slate-200 transition-colors">
                        <svg class="w-5 h-5 text-slate-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" /></svg>
                        <span class="app-title font-outfit tracking-wider text-[14px] font-normal text-slate-800">{{ activeDateGroup }}</span>
                    </div>

                    <div v-for="item in sortedItems" :key="item.id" 
                        v-show="focusedId === null || focusedId === item.id"
                        @click.stop="toggleExpand(item.id)"
                        :class="[
                            'p-[10px] border-b border-slate-200 last:border-b-0 relative group transition-all cursor-pointer bg-white active:bg-slate-50',
                            { 'z-[50]': openMenuId === item.id, 'z-10': openMenuId !== item.id },
                            { 'border-b-0': focusedId === item.id }
                        ]"
                    >
                        <!-- List Item Detail (Simplified per user request) -->
                        <div class="animate-fade-in py-0 bg-white relative px-1.5">
                            <div class="grid grid-cols-2 gap-y-3 pr-8 md:flex md:flex-wrap md:items-center md:gap-x-5">
                                <!-- Date -->
                                <div class="grudge-field flex flex-row items-center space-x-1.5">
                                    <label class="grudge-label">日期:</label>
                                    <div class="grudge-date-value">{{ formatDate(item.process_date) || formatDate(item.know_date) }}</div>
                                </div>
                                <!-- Dharma Name -->
                                <div class="grudge-field flex flex-row items-center space-x-1.5">
                                    <label class="grudge-label">法號:</label>
                                    <div class="grudge-value-name">{{ item.user_name || '-' }}{{ item.user_remarks ? '(' + translateRel(item.user_remarks) + ')' : '' }}</div>
                                </div>
                                <!-- Quantity -->
                                <div class="grudge-field flex flex-row items-center space-x-1.5 min-w-[130px]">
                                    <label class="grudge-label">數量:</label>
                                    <div class="grudge-value whitespace-nowrap">{{ (Number(item.quantity) || 0).toLocaleString() }}位</div>
                                </div>
                                <!-- Result -->
                                <div class="grudge-field flex flex-row items-center space-x-1.5">
                                    <label class="grudge-label">結果:</label>
                                    <div class="grudge-value" :class="item.destination === '未處理' ? '!text-[#ef4444]' : '!text-[#0f172a]'">
                                        {{ item.destination || '已處理' }}
                                    </div>
                                </div>
                            </div>

                            <!-- Expanded Content (Show Everything when focused) -->
                            <div v-if="focusedId === item.id" class="mt-6 pt-6 border-t border-slate-100 space-y-6 animate-fade-in">

                                <!-- Army Breakdown (if applicable) -->
                                <div v-if="item.destination === '黑曜軍' || item.destination === '耀紫軍'" class="space-y-2">
                                    <label class="app-title uppercase tracking-widest">軍隊細目</label>
                                    <div class="flex items-center space-x-6">
                                        <template v-if="item.destination === '黑曜軍'">
                                            <div class="flex items-center space-x-2">
                                                <span class="w-2 h-2 rounded-full bg-slate-900"></span>
                                                <span class="app-body font-bold text-[17px]">閻尊: {{ parseRemarks(item.remarks).yan_zun || 0 }}</span>
                                            </div>
                                            <div class="flex items-center space-x-2">
                                                <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                                                <span class="app-body font-bold text-[17px]">閻闇: {{ parseRemarks(item.remarks).yan_an || 0 }}</span>
                                            </div>
                                        </template>
                                        <template v-else-if="item.destination === '耀紫軍'">
                                            <div class="flex items-center space-x-2">
                                                <span class="w-2 h-2 rounded-full bg-purple-600"></span>
                                                <span class="app-body font-bold text-[17px]">龍勝: {{ parseRemarks(item.remarks).long_sheng || 0 }}</span>
                                            </div>
                                            <div class="flex items-center space-x-2">
                                                <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                                                <span class="app-body font-bold text-[17px]">龍戰: {{ parseRemarks(item.remarks).long_zhan || 0 }}</span>
                                            </div>
                                        </template>
                                    </div>
                                </div>

                                <!-- Detailed Remarks / Remarks Text -->
                                <div v-if="item.remarks_text" class="space-y-2">
                                    <label class="app-title uppercase tracking-widest">詳細備註</label>
                                    <div class="app-body font-medium leading-relaxed whitespace-pre-wrap text-[18px] bg-slate-50/50 p-3 rounded-xl border border-slate-100/50">
                                        {{ item.remarks_text }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
                

            </div>
        </div>

        <!-- FAB Bottom Navigation -->
        <mobile-navbar 
            :can-back="true"
            :show-action="true"
            :action-active="showAddMenu"
            :search-active="showSearch"
            @back="handleBack"
            @home="$emit('goHome')"
            @action="showAddMenu = !showAddMenu"
            @search="showSearch = !showSearch"
            @more="downloadList"
        />

        <add-action-menu :show="showAddMenu" @close="showAddMenu = false" :actions="addActions" />

        <!-- Floating Pagination above MobileNavbar -->
        <div v-if="!addMode && !showBatchImport && activePaginationMeta && activePaginationMeta.last_page > 1" 
             class="fixed z-[100] flex justify-center bg-white border-t border-slate-200 py-0.5" 
             style="bottom: calc(7vh + env(safe-area-inset-bottom)); left: 0; right: 0;">
            <pagination-buttons 
                :meta="activePaginationMeta" 
                @page-change="handlePageChange" 
                class="!mb-0 !mt-0"
            />
        </div>
        <grudge-batch-import :show="showBatchImport" @cancel="showBatchImport = false" @success="onBatchSuccess" />
        <grudge-add-form :key="editingId || 'new'" :show="addMode" :initialData="form" :editingId="editingId" :users="users" @save="saveItem" @cancel="addMode = false; editingId = null" />
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, defineEmits, watch } from 'vue';
import { debounce } from '../utils/debounce';
import axios from 'axios';
import GrudgeAddForm from './GrudgeAddForm.vue';
import GrudgeBatchImport from './GrudgeBatchImport.vue';
import AddActionMenu from './AddActionMenu.vue';
import MobileNavbar from './MobileNavbar.vue';
import PaginationButtons from './PaginationButtons.vue';
import { writeClipboard, downloadBlob, lockBodyScroll, unlockBodyScroll } from '../utils/iosCompat';


const props = defineProps(['user']);
const emit = defineEmits(['goHome']);

// State Definitions
const currentFolder = ref({ id: 'all', name: '怨靈記錄專區', status: '全部' });
const searchQuery = ref('');
const showSearch = ref(false);
const addMode = ref(false);
const activeDateGroup = ref(null);
const showAddMenu = ref(false);
const openMenuId = ref(null);
const persistentToast = ref(null);
const deleteConfirmId = ref(null);
const items = ref([]);
const users = ref([]);
const loading = ref(true);
const editingId = ref(null);
const focusedId = ref(null);
const sortDesc = ref(true);
const showTotal = ref(false);
const showBatchImport = ref(false);
const markings = ref({});
const saving = ref(false);
const paginationMeta = ref(null);
const currentPage = ref(1);
const dateGroupsData = ref([]);
const datePaginationMeta = ref(null);
const globalTotals = ref({ quantity: 0, breakdowns: { yan_zun: 0, yan_an: 0, long_sheng: 0, long_zhan: 0, yan_jue: 0, yan_ze: 0, yan_di: 0, yan_yuan: 0 } });

const activePaginationMeta = computed(() => {
    return (!activeDateGroup.value && !focusedId.value) ? datePaginationMeta.value : paginationMeta.value;
});

const handlePageChange = (page) => {
    currentPage.value = page;
    loadData(page);
};

const resetToRoot = () => {
    searchQuery.value = '';
    showSearch.value = false;
    addMode.value = false;
    focusedId.value = null;
    openMenuId.value = null;
    activeDateGroup.value = null;
};

const clickToCollapse = () => {
    focusedId.value = null;
    openMenuId.value = null;
    activeDateGroup.value = null;
};

const toggleDateGroup = (date) => {
    if (activeDateGroup.value === date) activeDateGroup.value = null;
    else activeDateGroup.value = date;
};

const parseRemarks = (remarks) => {
    if (!remarks || (Array.isArray(remarks) && remarks.length === 0)) return {};
    if (typeof remarks === 'string') {
        try { return JSON.parse(remarks); } catch (e) { return {}; }
    }
    return remarks;
};

const formatDestinations = (destStr) => {
    if (!destStr || destStr === '未處理') return ['未處理'];
    const parts = destStr.split(/[、,，]/);
    const results = parts.map(p => {
        const match = p.match(/^(.*?)(?:\((\d+)\))?$/);
        if (!match) return { name: p.trim(), qty: null };
        return { name: match[1].trim(), qty: match[2] };
    });
    const order = ['耀紫軍', '虎賁軍', '虎甲軍', '黑曜軍', '九天', '未處理'];
    results.sort((a, b) => {
        const idxA = order.indexOf(a.name);
        const idxB = order.indexOf(b.name);
        if (idxA !== -1 && idxB !== -1) return idxA - idxB;
        if (idxA !== -1) return -1;
        if (idxB !== -1) return 1;
        return 0;
    });
    return results.map(r => r.qty ? `${r.name} ${r.qty}` : r.name);
};

const formatDate = (dateStr) => {
    if (!dateStr || dateStr === '-' || dateStr === '未設定' || dateStr === '歷史累積') return '';
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

const getTodayStr = () => {
    const d = new Date();
    return `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`;
};

const translateRel = (rel) => {
    if (!rel) return '';
    let result = rel.trim();
    if (result === '之父' || result === '父') return '父親';
    if (result === '之母' || result === '母') return '母親';
    if (result === '之嬤' || result === '嬤') return '奶奶';
    if (result === '之夫' || result === '夫') return '先生';
    return result.replace(/^[之的]/, '');
};


let totalTimer = null;
const toggleShowTotal = () => {
    showTotal.value = !showTotal.value;
    if (totalTimer) clearTimeout(totalTimer);
    if (showTotal.value) {
        totalTimer = setTimeout(() => {
            showTotal.value = false;
        }, 10000);
    }
};


const filteredItems = computed(() => {
    return items.value;
});

// Empty state check: at Level 1 check date groups, at Level 2 check items
const isEmptyState = computed(() => {
    if (!activeDateGroup.value && !focusedId.value) {
        return dateGroupsData.value.length === 0;
    }
    return filteredItems.value.length === 0;
});

const onBatchSuccess = () => {
    activeDateGroup.value = null;
    focusedId.value = null;
    currentPage.value = 1;
    loadData(1);
};

const sortedItems = computed(() => {
    let result = [...filteredItems.value];
    result.sort((a, b) => {
        // Records without know_date (Historical Accumulation) always go to the bottom
        if (!a.know_date && b.know_date) return 1;
        if (a.know_date && !b.know_date) return -1;
        if (!a.know_date && !b.know_date) return a.id - b.id;

        // 使用字串比較日期，避免 Date 物件轉換時區問題
        const dateA = String(a.know_date || '');
        const dateB = String(b.know_date || '');
        
        if (dateA !== dateB) {
            return sortDesc.value ? dateB.localeCompare(dateA) : dateA.localeCompare(dateB);
        }

        // 同一天內，未處理的排在最前面
        const aUnprocessed = a.destination === '未處理';
        const bUnprocessed = b.destination === '未處理';
        if (aUnprocessed && !bUnprocessed) return -1;
        if (!aUnprocessed && bUnprocessed) return 1;

        return sortDesc.value ? b.id - a.id : a.id - b.id;
    });
    return result;
});

const toggleMenu = (id) => { openMenuId.value = openMenuId.value === id ? null : id; };
const toggleExpand = (id) => { focusedId.value = focusedId.value === id ? null : id; };

const addActions = computed(() => [
    { 
        label: '逐筆新增', 
        description: '手動輸入單筆怨靈紀錄',
        icon: '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>',
        handler: () => { 
            editingId.value = null;
            form.value = { user_name: '', user_remarks: '', destination: '未處理', quantity: 1, know_date: getTodayStr(), process_date: '', remarks_text: '' };
            addMode.value = true; 
        } 
    },
    { 
        label: '多筆貼上新增', 
        description: '直接貼上法號清單批量載錄',
        icon: '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>',
        handler: () => { showBatchImport.value = true; } 
    },
    { 
        label: '複製貼 LINE', 
        description: '將清單轉為文字複製到 LINE',
        icon: '<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-1-13h2v6h-2zm0 8h2v2h-2z"/></svg>',
        handler: () => { copyAllToLine(); } 
    }
]);

const getVisualWidth = (str) => {
    let width = 0;
    for (let i = 0; i < str.length; i++) {
        const charCode = str.charCodeAt(i);
        if (charCode >= 0 && charCode <= 128) width += 1;
        else width += 2;
    }
    return width;
};

const padToWidthPrecise = (str, width, isNumeric = false) => {
    const currentWidth = getVisualWidth(str);
    if (currentWidth >= width) return str;
    if (isNumeric) {
        const padding = '\u2007'.repeat(width - currentWidth);
        return padding + str;
    } else {
        let remaining = width - currentWidth;
        let padded = str;
        while (remaining >= 2) { padded += '\u3000'; remaining -= 2; }
        while (remaining >= 1) { padded += ' '; remaining -= 1; }
        return padded;
    }
};

const copyAllToLine = () => {
    if (!sortedItems.value.length) return alert('目前沒有資料可供複製');
    let text = `【怨靈載錄報表】\n\n`;
    const hDate = padToWidthPrecise('日期', 10);
    const hName = padToWidthPrecise('法號', 18);
    text += `${hDate} | ${hName} | 數量\n`;
    text += `----------------------------------------\n`;
    let total = 0;
    sortedItems.value.forEach(item => {
        const dateStr = formatDate(item.know_date);
        const fullName = item.user_name + (item.user_remarks || '');
        const qtyNum = Number(item.quantity) || 0;
        total += qtyNum;
        const cDate = padToWidthPrecise(dateStr, 10);
        const cName = padToWidthPrecise(fullName, 18);
        const cQty  = padToWidthPrecise(qtyNum.toString(), 4, true);
        text += `${cDate} | ${cName} | ${cQty}\n`;
    });
    text += `----------------------------------------\n`;
    const footerLabel = padToWidthPrecise('總結：', 30);
    const footerQty   = padToWidthPrecise(total.toString(), 4, true);
    text += `${footerLabel} | ${footerQty}\n`;
    writeClipboard(text).then((success) => {
        if (success) {
            persistentToast.value = { msg: '✓ 已複製到剪貼簿，快去 LINE 貼上吧！', type: 'success' };
            setTimeout(() => { if (persistentToast.value?.type === 'success') persistentToast.value = null; }, 3000);
        }
    });
};

const exportToExcel = () => {
    if (!sortedItems.value.length) return alert('目前沒有資料可供匯出');
    
    if (typeof XLSX === 'undefined') {
        persistentToast.value = { msg: '正在載入匯出元件，請稍候再試一次...', type: 'info' };
        setTimeout(() => { persistentToast.value = null; }, 3000);
        return;
    }
    performExcelExport();
};

const performExcelExport = () => {
    let total = 0;
    const data = sortedItems.value.map(item => {
        const qty = Number(item.quantity) || 0;
        total += qty;
        return {
            '得知日期': formatDate(item.know_date),
            '法號': item.user_name,
            '備註': item.user_remarks || '',
            '數量': qty,
            '處理結果': item.destination || '未處理',
            '處理日期': item.process_date ? formatDate(item.process_date) : '-',
            '詳細備註': item.remarks_text || ''
        };
    });
    data.push({ '得知日期': '', '法號': '總結', '備註': '', '數量': total, '處理結果': '', '處理日期': '', '詳細備註': '' });
    const worksheet = XLSX.utils.json_to_sheet(data);
    const workbook = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(workbook, worksheet, '怨靈資料');
    const dateStr = getTodayStr().replace(/-/g, '');
    const wbout = XLSX.write(workbook, { bookType: 'xlsx', type: 'array' });
    const blob = new Blob([wbout], { type: 'application/octet-stream' });
    downloadBlob(blob, `怨靈報表_${dateStr}.xlsx`);
};

const form = ref({ user_name: '', user_remarks: '', destination: '未處理', quantity: 1, know_date: getTodayStr(), process_date: '', remarks_text: '', remarks: {} });

const loadData = async (page = 1) => {
    loading.value = true;
    try {
        const ts = new Date().getTime();
        
        if (!activeDateGroup.value) {
            // Fetch Date Groups
            const params = { page, search: searchQuery.value, t: ts };
            const res = await axios.get('/grudges/date-groups', { params });
            const paginator = res.data.paginator;
            dateGroupsData.value = paginator.data || [];
            datePaginationMeta.value = {
                current_page: paginator.current_page,
                last_page: paginator.last_page,
                total: paginator.total
            };
            items.value = [];
            globalTotals.value = {
                quantity: res.data.global_total_quantity,
                breakdowns: res.data.global_breakdowns
            };
        } else {
            // Fetch Records for specific date
            const params = { page, search: searchQuery.value, t: ts };
            if (activeDateGroup.value === '歷史累積') {
                params.know_date = 'null';
            } else {
                params.know_date = activeDateGroup.value.replace(/\//g, '-');
            }
            const res = await axios.get('/grudges', { params });
            const paginator = res.data.paginator;
            items.value = paginator.data || [];
            paginationMeta.value = {
                current_page: paginator.current_page,
                last_page: paginator.last_page,
                total: paginator.total
            };
            globalTotals.value = {
                quantity: res.data.global_total_quantity,
                breakdowns: res.data.global_breakdowns
            };
        }
    } catch (e) { 
        console.error('Load data failed:', e);
        items.value = [];
        dateGroupsData.value = [];
    } finally { 
        loading.value = false; 
    }
};

const fetchUsers = async () => {
    try {
        const res = await axios.get('/api/dharma-names-list');
        users.value = res.data;
    } catch (e) {
        console.error('Failed to fetch dharma names:', e);
    }
};

watch([searchQuery, activeDateGroup], debounce(() => {
    currentPage.value = 1;
    loadData(1);
}, 300));

const saveItem = async (formData) => {
    try {
        let status = formData.status || (formData.destination === '未處理' ? '待處理' : '已處理');
        if (editingId.value) {
            await axios.put(`/grudges/${editingId.value}`, { ...formData, status });
        } else {
            await axios.post('/grudges', { ...formData, status });
        }
        addMode.value = false;
        editingId.value = null;
        focusedId.value = null;
        loadData();
    } catch (e) { alert('儲存失敗: ' + (e.response?.data?.message || e.message)); }
};

const handleBack = () => {
    if (addMode.value) { addMode.value = false; editingId.value = null; }
    else if (focusedId.value) { focusedId.value = null; }
    else if (activeDateGroup.value) { activeDateGroup.value = null; }
    else { emit('goHome'); }
};

const editItem = (item) => {
    focusedId.value = item.id;
    editingId.value = item.id;
    const parsed = parseRemarks(item.remarks);
    form.value = { ...item, remarks: JSON.parse(JSON.stringify(parsed)) };
    addMode.value = true;
    openMenuId.value = null;
};

const deleteItem = (id) => {
    deleteConfirmId.value = id;
    persistentToast.value = { msg: '確定要刪除這筆資料嗎？', type: 'deleteConfirm' };
    openMenuId.value = null;
};

const executeDelete = async () => {
    if (!deleteConfirmId.value) return;
    try {
        await axios.delete(`/grudges/${deleteConfirmId.value}`);
        persistentToast.value = { msg: '已成功刪除紀錄', type: 'success' };
        setTimeout(() => { persistentToast.value = null; }, 1500);
        focusedId.value = null;
        loadData();
    } catch (e) {
        console.error('Delete failed:', e);
        persistentToast.value = { msg: '刪除失敗', type: 'error' };
    } finally {
        deleteConfirmId.value = null;
    }
};

const copyItem = async (item) => {
    const text = `【怨靈結果】${item.user_name}: ${item.destination} (${item.quantity})`;
    await writeClipboard(text);
    persistentToast.value = { msg: '✓ 已複製', type: 'success' };
    setTimeout(() => { if (persistentToast.value?.type === 'success') persistentToast.value = null; }, 1500);
    openMenuId.value = null;
};

const downloadItem = (item, format = 'txt') => {
    let contents = `【怨靈處理結果】\r\n法號：${item.user_name || '未知'}\r\n日期：${formatDate(item.know_date)}\r\n數量：${item.quantity}\r\n結果：${item.destination || '未處理'}\r\n處理日期：${item.process_date ? formatDate(item.process_date) : '-'}\r\n備註：${item.remarks_text || '無'}`;
    let fileName = `怨靈_${item.user_name || '未名'}.txt`;
    const blob = new Blob([contents], { type: 'text/plain;charset=utf-8' });
    downloadBlob(blob, fileName);
    openMenuId.value = null;
};

const downloadList = () => {
    let contents = `【怨靈處理結果清單】\r\n\r\n` + 
        sortedItems.value.map(i => `${i.user_name || '未知'}${i.user_remarks?'('+i.user_remarks+')':''}: ${i.destination || '未處理'} (${i.quantity})`).join('\r\n');
    let fileName = '怨靈處理結果清單.txt';
    const blob = new Blob([contents], { type: 'text/plain;charset=utf-8' });
    downloadBlob(blob, fileName);
};

const breakdownTotals = computed(() => globalTotals.value.breakdowns);
const totalGrudgeQuantity = computed(() => globalTotals.value.quantity);
const filteredTotal = computed(() => globalTotals.value.quantity);
const displayTitle = computed(() => '怨靈記錄專區');

watch(currentFolder, (newVal) => {
    // Auto-expansion disabled per user request
});

onMounted(() => {
    loadData();
    fetchUsers();

    // Pre-load XLSX for iOS compatibility (avoids gesture timeout)
    if (typeof XLSX === 'undefined') {
        const script = document.createElement('script');
        script.src = "https://cdn.sheetjs.com/xlsx-latest/package/dist/xlsx.full.min.js";
        script.async = true;
        document.head.appendChild(script);
    }
});

const isAnyModalOpen = computed(() => {
    return !!addMode.value || 
           !!persistentToast.value || 
           !!deleteConfirmId.value || 
           !!focusedId.value || 
           !!showAddMenu.value || 
           !!showTotal.value ||
           !!showBatchImport.value;
});

watch(isAnyModalOpen, (newVal) => {
    if (newVal) lockBodyScroll();
    else unlockBodyScroll();
});

onUnmounted(() => {
    if (isAnyModalOpen.value) unlockBodyScroll();
});
</script>

<style scoped>
.grudge-field { display: flex; flex-direction: column; gap: 4px; }
.grudge-label {
    font-family: 'Noto Sans TC', sans-serif;
    font-weight: 900;
    color: #94a3b8; /* Changed to a lighter slate for better hierarchy as label is now on top */
    text-transform: uppercase;
    letter-spacing: 0.05em;
    white-space: nowrap;
}
.grudge-value {
    font-family: 'Montserrat', sans-serif;
    font-weight: 500; /* Medium weight for "細體" */
    color: #0f172a;
}
.grudge-value-name {
    font-family: 'Montserrat', sans-serif;
    font-weight: 600; /* Slightly bolder for the name but still lighter than before */
    color: #0f172a;
}
.grudge-date-value {
    font-family: 'Montserrat', sans-serif;
    font-weight: 500;
    color: #0f172a;
}

/* Specific scaling for the date headers to match label size */
:deep(body.font-small) .app-title.text-\[14px\] { font-size: 13px !important; }
:deep(body.font-medium) .app-title.text-\[14px\] { font-size: 14px !important; }
:deep(body.font-large) .app-title.text-\[14px\] { font-size: 15px !important; }

/* Custom Grudge Scaling Logic */
/* Small: Label 13, Body 15 */
:deep(body.font-small) .grudge-label, :deep(body.font-small) .grudge-date-value { font-size: 13px !important; }
:deep(body.font-small) .grudge-value, :deep(body.font-small) .grudge-value-name { font-size: 15px !important; }

/* Medium: Label 15, Body 17 */
:deep(body.font-medium) .grudge-label, :deep(body.font-medium) .grudge-date-value { font-size: 15px !important; }
:deep(body.font-medium) .grudge-value, :deep(body.font-medium) .grudge-value-name { font-size: 17px !important; }

/* Large: Label 15, Body 19 */
:deep(body.font-large) .grudge-label, :deep(body.font-large) .grudge-date-value { font-size: 15px !important; }
:deep(body.font-large) .grudge-value, :deep(body.font-large) .grudge-value-name { font-size: 21px !important; }

.animate-slide-up { animation: slideUp 0.1s ease-out; }
@keyframes slideUp { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }
.animate-fade-in { animation: fadeIn 0.1s ease-in; }
@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
.custom-scrollbar::-webkit-scrollbar { width: 5px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
</style>
