<template>
    <div class="bg-slate-100 md:bg-white h-[100dvh] flex flex-col relative overflow-hidden">
        <!-- Delete Confirmation / Status Toast -->
        <div v-if="persistentToast" class="fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 z-[9999] pointer-events-auto">
            <div class="bg-white rounded-3xl shadow-[0_20px_60px_rgba(0,0,0,0.3)] flex flex-col border border-slate-100 overflow-hidden" style="padding: 28px; min-width: 360px;">
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
                    <button @click="executeDelete" class="flex-1 bg-red-600 text-white h-[52px] rounded-2xl border border-red-500 text-[18px] font-black tracking-widest active:scale-95 transition-all shadow-lg shadow-red-100" style="color: white !important;">確定刪除</button>
                </div>
                <div v-else class="flex justify-end mt-2">
                    <button @click="persistentToast = null" class="bg-indigo-600 text-white px-10 py-3.5 rounded-2xl text-[18px] font-black tracking-widest active:scale-95 transition-all shadow-lg shadow-indigo-100" style="color: white !important;">確定</button>
                </div>
            </div>
        </div>



        <!-- Global Dual Header System -->
        <!-- Header 1: Module Level (Hidden when in folder view to increase density) -->
        <div v-if="!currentFolder" class="border-b border-white flex items-center bg-white sticky top-0 z-[110] w-full" style="padding: 4px 10px; min-height: 32px;">
            <div class="flex-1 flex flex-col justify-start min-w-0 py-1 pl-1 cursor-pointer" @click="resetToRoot">
                <h1 class="text-[32px] text-slate-900 leading-tight font-outfit tracking-widest break-words font-black" style="color: #0f172a !important; font-size: 32px !important; padding-top: 5px; font-weight: 900 !important;">
                    軍隊記錄專區
                </h1>
            </div>
            <div class="absolute right-2 bottom-2">
                <button @click="sortDesc = !sortDesc" class="px-2 py-1 text-[13px] text-indigo-600 active:scale-95 transition-all">
                    {{ sortDesc ? '新→舊' : '舊→新' }}
                </button>
            </div>
        </div>

        <!-- Header 2: Folder/Action Level (Sticky at top when folder selected) -->
        <div v-if="currentFolder" class="border-b border-white flex items-center bg-white sticky top-0 z-[110] w-full px-[10px] py-1" style="min-height: 32px;">
            <button @click="resetToRoot" class="text-slate-400 p-2 -ml-2 mr-2 active:scale-90 transition-transform shrink-0">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" /></svg>
            </button>
            <div class="flex items-center flex-1 min-w-0 justify-start">
                <h2 class="text-[32px] text-slate-900 truncate tracking-tight font-outfit font-black" style="font-size: 32px !important; color: #0f172a !important; font-weight: 900 !important;">
                    {{ currentFolder?.id === 'all' ? '全部軍隊' : currentFolder?.name }}
                </h2>
                <button @click.stop="sortDesc = !sortDesc" class="ml-2 px-2 py-1 text-[13px] text-indigo-600 active:scale-95 transition-all">
                    {{ sortDesc ? '新→舊' : '舊→新' }}
                </button>
            </div>
            <div class="ml-2 flex items-center space-x-2">
                <button @click="toggleFullTotal" class="px-3.5 py-1.5 bg-slate-900 text-white rounded-xl text-[14px] font-black transition-all active:scale-95 shadow-md whitespace-nowrap" style="color: white !important;">
                    總數
                </button>
                <button v-if="focusedId" @click="focusedId = null" class="w-8 h-8 flex items-center justify-center bg-slate-100 text-slate-400 rounded-xl active:scale-90 transition-all ml-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" /></svg>
                </button>
            </div>
        </div>

        <!-- SEARCH COMPONENT -->
        <div v-if="showSearch && currentFolder" class="px-[10px] mt-2 animate-fade-in relative flex items-center group w-full">
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
                <div class="px-6 pb-24 grid grid-cols-1 gap-y-12 items-center justify-center mt-[25px] max-w-4xl mx-auto">
                    <button v-for="folder in filteredFolders" :key="folder.id" 
                        @click="currentFolder = folder"
                        class="flex flex-col items-center justify-center p-2 active:scale-95 transition-all group relative">
                        
                        <!-- Unified Shield Badge (Matches Mobile Style) -->
                        <div class="relative w-[339px] h-[339px] md:w-[387px] md:h-[387px] -translate-y-[60px]">
                            <!-- MOBILE VERSION SVG -->
                            <svg class="block md:hidden w-full h-full drop-shadow-2xl transition-transform group-hover:scale-105" viewBox="0 0 24 24" fill="none">
                                <path d="M12 2L4 5V11C4 16.5 7.5 21 12 22.5C16.5 21 20 16.5 20 11V5L12 2Z" 
                                      :fill="folder.name === '耀紫軍' ? '#581c87' : '#000000'" 
                                      stroke="rgba(0,0,0,0.1)" stroke-width="0.5"/>
                            </svg>

                            <!-- DESKTOP VERSION SVG -->
                            <svg class="hidden md:block w-full h-full drop-shadow-2xl transition-transform group-hover:scale-105" viewBox="0 0 24 24" fill="none">
                                <path d="M12 2L4 5V11C4 16.5 7.5 21 12 22.5C16.5 21 20 16.5 20 11V5L12 2Z" 
                                      :fill="folder.name === '耀紫軍' ? '#581c87' : '#000000'" 
                                      stroke="rgba(0,0,0,0.1)" stroke-width="0.5"/>
                            </svg>

                            <div class="absolute inset-0 flex flex-col items-center justify-center px-4">
                                <span class="font-black text-white tracking-[0.2em] leading-tight text-center drop-shadow-[0_2px_6px_rgba(0,0,0,0.8)]" 
                                    style="font-weight: 900 !important; font-size: 40px !important;">{{ folder.name }}</span>
                            </div>
                        </div>
                    </button>
                </div>
            </div>

            <!-- LEDGER VIEW -->
            <div v-if="(currentFolder || addMode || showSearch)" class="flex-1 overflow-y-auto px-[10px] bg-white flex flex-col pb-24 w-full">
                <!-- Full Total Modal (Centered) -->
                <div v-if="showFullTotal && currentFolder" class="fixed inset-0 z-[300] flex items-center justify-center p-6 bg-slate-900/60 backdrop-blur-sm animate-fade-in" @click="showFullTotal = false">
                    <div class="bg-white w-full max-w-sm rounded-[32px] p-8 shadow-2xl animate-pop-in relative" @click.stop>
                        <button @click="showFullTotal = false" class="absolute right-6 top-6 text-slate-300 hover:text-slate-600 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </button>
                        
                        <div class="text-center space-y-6 pt-4">
                            
                            <div class="space-y-2">
                                <h3 class="app-body text-slate-500 font-bold uppercase tracking-widest text-[20px]" style="font-size: 20px !important;">{{ currentFolder.name }} 總計</h3>
                                <div class="app-body font-black text-indigo-600" style="font-size: 20px !important;">
                                    {{ formatArmyTotal(currentFolderTotal) }}
                                </div>
                            </div>

                            <div v-if="['黑曜軍', '耀紫軍'].includes(currentFolder.name)" class="grid grid-cols-2 gap-y-4 gap-x-2 pt-6 border-t border-slate-100">
                                <template v-for="(val, label) in {
                                    '黑曜軍': { '閻尊': breakdownTotals.yan_zun, '閻闇': breakdownTotals.yan_an },
                                    '耀紫軍': { '龍勝': breakdownTotals.long_sheng, '龍戰': breakdownTotals.long_zhan },
                                    '虎甲軍': { '閻決': breakdownTotals.yan_jue, '閻澤': breakdownTotals.yan_ze },
                                    '虎賁軍': { '閻地': breakdownTotals.yan_di, '閻源': breakdownTotals.yan_yuan }
                                }[currentFolder.name]" :key="label">
                                    <div class="flex flex-col items-center">
                                        <span class="app-body text-slate-400 font-bold uppercase tracking-widest" style="font-size: 20px !important;">{{ label }}</span>
                                        <span class="app-body text-slate-900 font-bold" style="font-size: 20px !important;">{{ formatArmyTotal(val) }}</span>
                                    </div>
                                </template>
                            </div>

                            <button @click="showFullTotal = false" class="w-full py-4 bg-slate-900 text-white rounded-2xl font-black text-[18px] active:scale-95 transition-all mt-4" style="color: white !important;">
                                關閉視窗
                            </button>
                        </div>
                    </div>
                </div>

                <!-- List Content -->
                <div v-if="!showFullTotal" class="flex flex-col flex-1">
                    <div v-if="loading" class="text-center py-10 text-xs text-slate-400">載入中...</div>
                    <div v-else-if="isEmptyState" class="text-center py-20 text-slate-400 font-light px-6">
                        目前尚無{{ currentFolder?.id !== 'all' ? currentFolder?.name : '' }}載錄資料。
                        <div v-if="!expandedDate && !focusedId" class="text-[10px] mt-2 opacity-50">
                        </div>
                    </div>
                    <div v-else class="flex flex-col flex-1 px-1">
                        <!-- Level 1: 日期清單 -->
                        <template v-if="!expandedDate && !focusedId && !searchQuery">
                            <div v-for="group in dateGroupsData" :key="group.know_date || 'historical'"
                                @click.stop="toggleDateGroup(group.know_date ? formatDate(group.know_date) : '原始數量')"
                                class="bg-white p-[10px] border-b border-slate-100 flex items-center justify-between cursor-pointer active:bg-slate-50 transition-all group overflow-hidden relative">
                                <div class="flex items-center z-10">
                                    <div class="flex flex-col">
                                        <span class="app-title font-outfit tracking-wider text-[18px] text-slate-800">{{ group.know_date ? formatDate(group.know_date) : '原始數量' }}</span>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <!-- Level 2: 特定日期的記錄 或 搜尋結果 -->
                        <template v-else>
                            <!-- 返回按鈕 (僅在日期展開時顯示) -->
                            <div v-if="expandedDate && focusedId === null && !searchQuery"
                                @click.stop="expandedDate = null"
                                class="px-4 py-2.5 bg-slate-50 border-y border-slate-200 flex items-center sticky top-0 z-20 cursor-pointer active:bg-slate-200 transition-colors mb-4 rounded-xl">
                                <svg class="w-5 h-5 text-slate-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" /></svg>
                                <span class="app-title font-outfit tracking-wider text-slate-800">{{ expandedDate }}</span>
                            </div>

                            <template v-for="group in groupedItems" :key="group.date">
                                <div v-for="item in group.items" :key="item.id" 
                                    v-show="focusedId === null || focusedId === item.id"
                                    @click="toggleExpand(item.id)"
                                    :class="[
                                        'py-[10px] px-[10px] border-b border-slate-200 last:border-b-0 relative group transition-all cursor-pointer z-0',
                                        openMenuId === item.id ? 'z-[50]' : 'z-0',
                                        { 'border-b-0': focusedId === item.id }
                                    ]"
                                >
                                    <div class="animate-fade-in py-2 bg-white space-y-4 relative px-1.5">
                                        <div class="military-field">
                                            <label class="military-label">日期</label>
                                            <div class="military-date-value">{{ formatDate(item.know_date) || '歷史累積' }}</div>
                                        </div>
                                        <div class="grid grid-cols-2 gap-x-4 pr-8 relative">
                                            <div class="military-field min-w-0">
                                                <label class="military-label">法號</label>
                                                <div class="military-value-name">{{ item.user_name }}{{ item.user_remarks ? '(' + translateRel(item.user_remarks) + ')' : '' }}</div>
                                            </div>
                                            <div class="military-field">
                                                <label class="military-label">數量</label>
                                                <div class="military-value">{{ formatArmyTotal(item.quantity) }}</div>
                                            </div>
                                            <div class="absolute right-1 top-0">
                                                <button @click.stop="toggleMenu(item.id)" class="p-1 text-[#dc1428] hover:text-red-700 active:scale-95 transition-all">
                                                    <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"><path d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM18 10a2 2 0 11-4 0 2 2 0 114 0z" /></svg>
                                                </button>
                                                <div v-if="openMenuId === item.id" @click.stop class="absolute right-0 top-full mt-1 w-auto min-w-[140px] bg-white rounded-2xl shadow-2xl border border-slate-100 z-[110] overflow-hidden animate-slide-up py-1">
                                                    <button @click.stop="editItem(item); openMenuId = null" class="w-full px-4 py-3 text-left text-[17px] font-black text-slate-900 hover:bg-slate-50 border-b border-slate-50 whitespace-nowrap">修改內容</button>
                                                    <button @click.stop="copySingleRecord(item); openMenuId = null" class="w-full px-4 py-3 text-left text-[17px] font-black text-slate-900 hover:bg-slate-50 border-b border-slate-50 whitespace-nowrap">複製貼 LINE</button>
                                                    <button @click.stop="downloadSingleRecord(item); openMenuId = null" class="w-full px-4 py-3 text-left text-[17px] font-black text-slate-900 hover:bg-slate-50 border-b border-slate-50 whitespace-nowrap">下載檔案</button>
                                                    <button @click.stop="deleteItem(item.id)" class="w-full px-4 py-3 text-left text-[17px] font-black text-red-600 hover:bg-red-50 whitespace-nowrap">刪除</button>
                                                </div>
                                            </div>
                                        </div>
                                        <div v-if="focusedId === item.id" class="pt-4 border-t border-slate-100 space-y-4 animate-fade-in">
                                            <div v-if="['黑曜軍','耀紫軍'].includes(item.army_type)" class="military-field">
                                                <label class="military-label">軍隊細目</label>
                                                <div class="flex items-center space-x-6 flex-wrap gap-y-2">
                                                    <template v-for="(val, label) in {
                                                        '黑曜軍': { 'yan_zun': item.yan_zun, 'yan_an': item.yan_an },
                                                        '耀紫軍': { 'long_sheng': item.long_sheng, 'long_zhan': item.long_zhan },
                                                        '虎甲軍': { 'yan_jue': item.yan_jue, 'yan_ze': item.yan_ze },
                                                        '虎賁軍': { 'yan_di': item.yan_di, 'yan_yuan': item.yan_yuan }
                                                    }[item.army_type]" :key="label">
                                                        <div class="flex items-center space-x-2" v-if="val > 0">
                                                            <span class="w-2 h-2 rounded-full" :class="getBulletColor(label)"></span>
                                                            <span class="military-value">{{ {yan_zun:'閻尊', yan_an:'閻闇', long_sheng:'龍勝', long_zhan:'龍戰', yan_jue:'閻決', yan_ze:'閻澤', yan_di:'閻地', yan_yuan:'閻源'}[label] }}: {{ formatArmyTotal(val) }}</span>
                                                        </div>
                                                    </template>
                                                </div>
                                            </div>
                                            <div v-if="item.remarks_text" class="military-field">
                                                <label class="military-label">詳細內容 / 備註</label>
                                                <div class="military-value leading-relaxed whitespace-pre-wrap bg-slate-50/50 p-3 rounded-xl border border-slate-100/50">{{ item.remarks_text }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </template>

                <!-- Pagination Buttons -->
                <pagination-buttons v-if="activePaginationMeta" :meta="activePaginationMeta" @page-change="handlePageChange" />
                </div>
            </div>
        </div>
    </div>

    <!-- FAB Bottom Navigation -->
        <mobile-navbar 
            is-absolute
            :can-back="true"
            :show-action="!addMode"
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
import PaginationButtons from './PaginationButtons.vue';
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
const armyCounts = ref({});
const breakdownTotals = ref({ 
    yan_zun: 0, yan_an: 0, 
    long_sheng: 0, long_zhan: 0,
    yan_jue: 0, yan_ze: 0,
    yan_di: 0, yan_yuan: 0
});
const getBulletColor = (key) => {
    const colors = {
        'yan_zun': 'bg-slate-900', 'yan_an': 'bg-slate-400',
        'long_sheng': 'bg-purple-600', 'long_zhan': 'bg-blue-600',
        'yan_jue': 'bg-emerald-600', 'yan_ze': 'bg-teal-600',
        'yan_di': 'bg-amber-700', 'yan_yuan': 'bg-stone-500'
    };
    return colors[key] || 'bg-slate-300';
};

const users = ref([]);
const loading = ref(true);
const editingId = ref(null);
const focusedId = ref(null);
const openStatusId = ref(null);
const showFullTotal = ref(false);
const paginationMeta = ref(null);
const datePaginationMeta = ref(null);
const dateGroupsData = ref([]);
const persistentToast = ref(null);
const deleteConfirmId = ref(null);
const currentPage = ref(1);
const sortDesc = ref(true);
let fullTotalTimer = null;

const activePaginationMeta = computed(() => {
    if (!expandedDate.value && !focusedId.value && !searchQuery.value) {
        return datePaginationMeta.value;
    }
    return paginationMeta.value;
});

const isEmptyState = computed(() => {
    if (!expandedDate.value && !focusedId.value && !searchQuery.value) {
        return (dateGroupsData.value || []).length === 0;
    }
    return (items.value || []).length === 0;
});

const handlePageChange = (page) => {
    currentPage.value = page;
    loadData(page);
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

const toggleFullTotal = () => {
    showFullTotal.value = !showFullTotal.value;
};

const filteredItems = computed(() => {
    // With server-side pagination, items.value already contains the filtered/paged data
    return items.value;
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
    let list = sortedItems.value;

    // Solo Mode: Focus on a specific record (hide other dates and other records)
    if (focusedId.value) {
        const item = list.find(i => i.id === focusedId.value);
        if (item) {
            const dateStr = item.know_date ? formatDate(item.know_date) : '原始數量';
            return [{ date: dateStr, items: [item] }];
        }
    }

    // Isolation Mode: Focus on a specific date (hide other dates)
    if (expandedDate.value) {
        const dateItems = list.filter(item => {
            const dateStr = item.know_date ? formatDate(item.know_date) : '原始數量';
            return dateStr === expandedDate.value;
        });
        if (dateItems.length > 0) {
            return [{ date: expandedDate.value, items: dateItems }];
        }
    }

    const groups = [];
    let currentGroup = null;

    list.forEach(item => {
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
const currentFolderTotal = computed(() => {
    if (!currentFolder.value) return 0;
    return armyCounts.value[currentFolder.value.name] || 0;
});

// breakdownTotals is now a ref populated from server

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

const loadData = async (page = 1) => {
    loading.value = true;
    try {
        const ts = new Date().getTime();
        
        if (!expandedDate.value) {
            // Level 1: fetch date groups
            const params = { page, per_page: 20, t: ts };
            if (searchQuery.value) params.search = searchQuery.value;
            if (currentFolder.value) params.army_type = currentFolder.value.name;

            const requests = [axios.get('/military-records/date-groups', { params })];
            if (users.value.length === 0) {
                requests.push(axios.get(`/api/dharma-names-list?t=${ts}`));
            }
            const [res, dres] = await Promise.all(requests);
            dateGroupsData.value = res.data.data || [];
            datePaginationMeta.value = {
                current_page: res.data.current_page,
                last_page: res.data.last_page,
                total: res.data.total
            };
            items.value = [];
            if (dres) users.value = dres.data;

            // Also refresh army counts
            const statsRes = await axios.get('/military-records', { params: { ...params, page: 1, per_page: 1 } });
            if (page === 1) {
                armyCounts.value = statsRes.data.armyCounts || {};
                breakdownTotals.value = statsRes.data.breakdownTotals || { 
                    yan_zun: 0, yan_an: 0, long_sheng: 0, long_zhan: 0,
                    yan_jue: 0, yan_ze: 0, yan_di: 0, yan_yuan: 0
                };
            }
        } else {
            // Level 2: fetch records for selected date
            const params = { page, per_page: 10, t: ts };
            if (searchQuery.value) params.search = searchQuery.value;
            if (currentFolder.value) params.army_type = currentFolder.value.name;
            if (expandedDate.value === '原始數量') {
                params.know_date = 'null';
            } else {
                params.know_date = expandedDate.value.replace(/\//g, '-');
            }

            const requests = [axios.get('/military-records', { params })];
            if (users.value.length === 0) {
                requests.push(axios.get(`/api/dharma-names-list?t=${ts}`));
            }
            const [res, dres] = await Promise.all(requests);

            const recData = res.data.records;
            items.value = recData.data || [];
            paginationMeta.value = {
                current_page: recData.current_page,
                last_page: recData.last_page,
                total: recData.total
            };
            if (page === 1) {
                armyCounts.value = res.data.armyCounts || {};
                breakdownTotals.value = res.data.breakdownTotals || { 
                    yan_zun: 0, yan_an: 0, long_sheng: 0, long_zhan: 0,
                    yan_jue: 0, yan_ze: 0, yan_di: 0, yan_yuan: 0
                };
            }
            if (dres) users.value = dres.data;
        }
    } catch (e) { 
        console.error('Load data failed:', e);
        items.value = [];
        dateGroupsData.value = [];
    } finally { 
        loading.value = false;
    }
};

watch(searchQuery, () => {
    currentPage.value = 1;
    loadData(1);
});

watch(currentFolder, () => {
    currentPage.value = 1;
    expandedDate.value = null;
    loadData(1);
});

watch(expandedDate, () => {
    currentPage.value = 1;
    loadData(1);
});

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
    else if (expandedDate.value) expandedDate.value = null;
    else if (currentFolder.value) currentFolder.value = null;
    else emit('goHome');
};

const editItem = (item) => {
    editingId.value = item.id;
    form.value = { ...item };
    addMode.value = true;
    openMenuId.value = null;
};

const deleteItem = (id) => {
    deleteConfirmId.value = id;
    persistentToast.value = { msg: '確定要刪除此筆軍隊紀錄嗎？', type: 'deleteConfirm' };
    openMenuId.value = null;
};

const executeDelete = async () => {
    if (!deleteConfirmId.value) return;
    try {
        await axios.delete(`/military-records/${deleteConfirmId.value}`);
        persistentToast.value = { msg: '已成功刪除紀錄', type: 'success' };
        setTimeout(() => { persistentToast.value = null; }, 1500);
        focusedId.value = null;
        loadData(currentPage.value);
    } catch (e) {
        console.error('Delete failed:', e);
        persistentToast.value = { msg: '刪除失敗，請稍後再試', type: 'error' };
    } finally {
        deleteConfirmId.value = null;
    }
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
        'bg-indigo-500': { start: '#1a1a1a', end: '#000000' }, // Tiger Armor -> Black
        'bg-blue-500':   { start: '#1a1a1a', end: '#000000' }, // Tiger Brave -> Black
        'bg-slate-700':  { start: '#1a1a1a', end: '#000000' }, // Obsidian -> Black
        'bg-purple-500': { start: '#581c87', end: '#2e1065' }  // Purple -> DEEP DARK PURPLE
    };
    return map[colorClass] || { start: '#1a1a1a', end: '#000000' };
};

const getDesktopGradientColor = (colorClass) => {
    // Desktop: Only Purple is Purple, others are Black
    if (colorClass === 'bg-purple-500') {
        return { start: '#581c87', end: '#2e1065' };
    }
    // Pure Black for all other folders on Desktop
    return { start: '#1a1a1a', end: '#000000' };
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
:deep(body.font-large) .military-value, :deep(body.font-large) .military-value-name { font-size: 21px !important; }

.animate-fade-in { animation: fadeIn 0.3s ease-out; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }

.custom-scrollbar { -webkit-overflow-scrolling: touch; }
.custom-scrollbar::-webkit-scrollbar { width: 5px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }

.animate-slide-up { animation: slideUp 0.15s ease-out; }
@keyframes slideUp { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
</style>
