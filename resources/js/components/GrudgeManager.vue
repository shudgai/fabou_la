<template>
    <div class="bg-white h-[100vh] flex flex-col relative overflow-hidden">
        <!-- Static Header -->
        <div class="border-b border-gray-100 flex items-center bg-white sticky top-0 z-30 px-2 h-[60px]">
            <div class="w-[60px] flex items-center shrink-0">
                <button @click="$emit('goHome')" class="text-slate-400 p-3 active:scale-90 transition-transform">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" /></svg>
                </button>
            </div>
            <div class="flex-1 flex justify-center items-center min-w-0">
                <h1 class="text-[25px] font-black font-outfit tracking-tight truncate" style="color: #0f172a !important;">{{ displayTitle }}</h1>
            </div>
            <div class="flex items-center justify-end shrink-0 space-x-1 pr-2">
                <button @click="sortDesc = !sortDesc" class="text-[11px] text-indigo-500 font-black bg-indigo-50 px-2 py-1 rounded-lg active:scale-95 transition-all opacity-90 tracking-tighter border border-indigo-100">
                    {{ sortDesc ? '新→舊' : '舊→新' }}
                </button>
                <button @click="toggleShowTotal" class="text-[17px] text-slate-900 font-black active:scale-95 transition-all">
                    總數<span v-if="showTotal || searchQuery" class="ml-0.5 font-mono text-[18px]" :class="{'text-indigo-600': searchQuery}">:{{ searchQuery ? filteredTotal : totalGrudgeQuantity }}</span>
                </button>
            </div>
        </div>
        
        <!-- Search Component (Enhanced with persistent X) -->
        <div v-if="showSearch" class="px-[10px] mt-2 animate-fade-in relative flex items-center group">
            <div class="absolute left-[22px] text-slate-400 pointer-events-none">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            <input v-model="searchQuery" type="text" placeholder="搜尋項目..." 
                class="w-full h-[36px] bg-white border border-slate-200 rounded-xl pl-9 pr-10 text-[15px] focus:ring-1 focus:ring-slate-300 outline-none transition-all shadow-sm">
            
            <!-- Persistent X: Clear or Close -->
            <button @click="searchQuery ? searchQuery = '' : showSearch = false" 
                class="absolute right-[18px] w-8 h-8 flex items-center justify-center text-slate-500 active:text-slate-900 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Total Simple Overlay -->
        <div v-if="showTotal" class="fixed inset-x-0 top-[60px] z-[60] px-4 animate-fade-in pointer-events-none">
        <div class="bg-white text-slate-900 px-8 py-5 rounded-3xl shadow-2xl flex items-center justify-between pointer-events-auto border border-slate-100 relative w-auto min-w-[200px] max-w-[280px]">
            <div class="flex flex-col items-center flex-1">
                <span class="app-body text-slate-400" style="font-weight: 400 !important;">怨靈紀錄總數</span>
                <span class="app-body text-slate-900 mt-1" style="font-weight: 400 !important; font-family: 'Montserrat', sans-serif !important;">{{ totalGrudgeQuantity }}</span>
            </div>
            <button @click="showTotal = false" class="absolute right-4 top-1/2 -translate-y-1/2 p-2 text-slate-300 active:scale-90 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
        </div>
        </div>

        <!-- Scrollable Content -->
        <div v-if="!showTotal" class="flex-1 overflow-y-auto custom-scrollbar" style="padding-bottom: 80px;">
            <div v-if="loading" class="text-center py-10 text-xs text-slate-400">載入中...</div>
            <div v-else-if="filteredItems.length === 0" class="text-center py-20 text-slate-400 font-light">目前尚無怨靈載錄資料。</div>
            <div v-else class="flex flex-col flex-1 px-2 pt-0">
                <div class="flex flex-col">
                    <div v-for="(item, index) in sortedItems" :key="item.id" 
                        v-show="focusedId === null || focusedId === item.id"
                        @click="toggleExpand(item.id)"
                        :class="[
                            'py-[16px] px-2 border-b border-slate-900 last:border-b-0 relative group transition-all cursor-pointer bg-white active:bg-slate-50',
                            { 'z-[50]': openMenuId === item.id, 'z-10': openMenuId !== item.id },
                            { 'border-t border-slate-900': index === 0 && !focusedId, 'border-b-0': focusedId === item.id }
                        ]"
                    >
                        <!-- List Item Display (Collapsed) -->
                        <div v-if="focusedId !== item.id" class="mt-0 flex flex-col pointer-events-none">
                            <!-- Row 1: Date only -->
                            <div class="flex items-center mb-0.5">
                                <div class="flex items-baseline space-x-2">
                                    <div class="app-body font-bold text-slate-500" style="font-weight: 700 !important; font-size: 15px !important;">得知日期</div>
                                    <div class="font-outfit !font-black !text-black ml-0.5" style="font-size: 18px !important; font-weight: 900 !important; color: #0d0d0d !important;">{{ item.know_date ? formatDate(item.know_date) : '----/--/--' }}</div>
                                </div>
                            </div>

                            <!-- Row 2: Name & Quantity & Status -->
                            <div class="flex items-center justify-between mt-1">
                                <div class="app-body font-bold truncate flex-1">
                                    {{ item.user_name || '-' }}<span v-if="item.user_remarks" class="app-body ml-0.5">{{ item.user_remarks }}</span>
                                </div>
                                <div class="flex items-center space-x-1 shrink-0 ml-4">
                                    <div class="app-body text-slate-400" style="font-weight: 400 !important;">數量:</div>
                                    <div class="app-body">{{ item.quantity }}</div>
                                </div>
                                <div class="ml-4">
                                    <span v-if="item.destination === '未處理'" class="app-body px-2.5 py-1 rounded-lg bg-slate-50 border border-slate-100 uppercase tracking-tighter" style="font-weight: 400 !important;">
                                        未處理
                                    </span>
                                    <span v-else class="app-body px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-600 border border-emerald-100 uppercase tracking-tighter" style="font-weight: 400 !important;">
                                        已處理
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Menu Button Layer -->
                        <div v-if="focusedId !== item.id" class="absolute right-0 top-0.5 z-20">
                            <button @click.stop="toggleMenu(item.id)" class="p-2 -mr-1 text-slate-400"><svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM18 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg></button>
                            <div v-if="openMenuId === item.id" @click.stop class="absolute right-0 top-full mt-1 w-auto min-w-[130px] bg-white opacity-100 rounded-xl shadow-[0_10px_40px_rgba(0,0,0,0.2)] border border-slate-100 z-[200] overflow-hidden animate-slide-up py-1">
                                <button @click.stop="toggleExpand(item.id); openMenuId = null" class="w-full px-4 py-3 text-left text-[17px] font-black text-slate-900 hover:bg-slate-50 border-b border-slate-50 whitespace-nowrap">展開詳情</button>
                                <button @click.stop="editItem(item)" class="w-full px-4 py-3 text-left text-[17px] font-black text-slate-900 hover:bg-slate-50 border-b border-slate-50 whitespace-nowrap">修改內容</button>
                                <button @click.stop="copyItem(item)" class="w-full px-4 py-3 text-left text-[17px] font-black text-slate-900 hover:bg-slate-50 border-b border-slate-50 whitespace-nowrap">複製 LINE</button>
                                <button @click.stop="downloadItem(item, 'txt')" class="w-full px-4 py-3 text-left text-[17px] font-black text-slate-900 hover:bg-slate-50 border-b border-slate-50 whitespace-nowrap">下載檔案</button>
                                <button @click.stop="deleteItem(item.id)" class="w-full px-4 py-3 text-left text-[17px] font-black text-red-600 hover:bg-red-50 whitespace-nowrap">刪除</button>
                            </div>
                        </div>

                        <!-- Expanded Detail (Pure White Frameless Style) -->
                        <div v-if="focusedId === item.id" class="animate-fade-in py-3 bg-white space-y-4 relative">
                            <div class="absolute right-0 top-0 z-[101]">
                                <button @click.stop="toggleMenu(item.id)" class="p-1 text-slate-400 hover:text-indigo-600 active:scale-95 transition-all"><svg class="h-6 w-6" fill="currentColor" viewBox="0 0 20 20"><path d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM18 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg></button>
                                <div v-if="openMenuId === item.id" @click.stop class="absolute right-0 top-full mt-1 w-auto min-w-[130px] bg-white opacity-100 rounded-xl shadow-[0_10px_40px_rgba(0,0,0,0.2)] border border-slate-100 z-[200] overflow-hidden animate-slide-up py-1">
                                    <button @click.stop="toggleExpand(item.id); openMenuId = null" class="w-full px-4 py-3 text-left text-[17px] font-black text-slate-900 hover:bg-slate-50 border-b border-slate-50 whitespace-nowrap">收合詳情</button>
                                    <button @click.stop="editItem(item)" class="w-full px-4 py-3 text-left text-[17px] font-black text-slate-900 hover:bg-slate-50 border-b border-slate-50 whitespace-nowrap">修改內容</button>
                                    <button @click.stop="downloadItem(item, 'txt')" class="w-full px-4 py-3 text-left text-[17px] font-black text-slate-900 hover:bg-slate-50 border-b border-slate-50 whitespace-nowrap">下載檔案</button>
                                    <button @click.stop="deleteItem(item.id)" class="w-full px-4 py-3 text-left text-[17px] font-black text-red-600 hover:bg-red-50 whitespace-nowrap">刪除</button>
                                </div>
                            </div>

                            <div class="space-y-1">
                                <div class="flex items-center space-x-1 ml-1">
                                    <button @click.stop="toggleExpand(item.id)" class="p-1 -ml-1 text-slate-300 active:scale-90 transition-all">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" /></svg>
                                    </button>
                                    <label class="app-title block">得知日期</label>
                                </div>
                                <div class="w-full px-3 flex items-center app-title">{{ item.know_date ? formatDate(item.know_date) : '-' }}</div>
                            </div>

                            <!-- Detail Row 2: user_name & user_remarks (Merged) -->
                            <div class="space-y-1">
                                <label class="app-title block ml-1">法號 (親友/信眾)</label>
                                <div class="w-full px-3 flex items-center app-body font-bold">
                                    {{ item.user_name || '-' }}
                                    <span v-if="item.user_remarks" class="app-body font-normal ml-1.5">{{ item.user_remarks }}</span>
                                </div>
                            </div>

                            <div class="space-y-1">
                                <label class="app-body block ml-1 text-slate-400" style="font-weight: 400 !important;">數量</label>
                                <div class="w-full px-3 flex items-center app-body">{{ item.quantity }}</div>
                            </div>

                            <div v-if="item.process_date" class="space-y-1">
                                <label class="app-title block ml-1">處理日期</label>
                                <div class="w-full px-3 flex items-center app-body">{{ formatDate(item.process_date) }}</div>
                            </div>

                            <div class="space-y-1">
                                <label class="app-title block ml-1">處理結果</label>
                                <div class="w-full px-3 flex flex-col app-body" :class="item.destination === '未處理' ? 'text-slate-300' : ''">
                                    <div v-for="(line, idx) in formatDestinations(item.destination)" :key="idx" class="flex items-center space-x-2" style="font-weight: 400 !important;">
                                        <span>{{ line }}</span>
                                    </div>
                                </div>
                                <!-- Military Breakdown (Moved Here) -->
                                <div v-if="item.destination === '黑曜軍' || item.destination === '耀紫軍'" class="w-full px-3 mt-1 app-body">
                                    <span v-if="item.destination === '黑曜軍'">
                                        閻尊: {{ parseRemarks(item.remarks).yan_zun || 0 }} 
                                        &nbsp;&nbsp; 
                                        閻闇: {{ parseRemarks(item.remarks).yan_an || 0 }}
                                    </span>
                                    <span v-if="item.destination === '耀紫軍'">
                                        龍勝: {{ parseRemarks(item.remarks).long_sheng || 0 }} 
                                        &nbsp;&nbsp; 
                                        龍戰: {{ parseRemarks(item.remarks).long_zhan || 0 }}
                                    </span>
                                </div>
                            </div>

                            <div v-if="item.remarks_text" class="space-y-1">
                                <label class="app-title block ml-1">詳細內容 / 備註</label>
                                <div class="w-full px-3 py-1 app-body leading-normal">{{ item.remarks_text }}</div>
                            </div>
                        </div>
                    </div>
                </div>
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
        <grudge-batch-import :show="showBatchImport" @cancel="showBatchImport = false" @success="loadData" />
        <grudge-add-form :key="editingId || 'new'" :show="addMode" :initialData="form" :editingId="editingId" :users="users" @save="saveItem" @cancel="addMode = false; editingId = null" />
    </div>
</template>

<script setup>
import { ref, computed, onMounted, defineEmits } from 'vue';
import axios from 'axios';
import GrudgeAddForm from './GrudgeAddForm.vue';
import GrudgeBatchImport from './GrudgeBatchImport.vue';
import AddActionMenu from './AddActionMenu.vue';
import MobileNavbar from './MobileNavbar.vue';

const emit = defineEmits(['goHome']);
const currentFolder = ref({ id: 'all', name: '怨靈記錄專區', status: '全部' });
const searchQuery = ref('');
const showSearch = ref(false);
const addMode = ref(false);

const parseRemarks = (remarks) => {
    // Force empty arrays (often returned by Laravel for empty JSON) to be objects
    if (!remarks || (Array.isArray(remarks) && remarks.length === 0)) return {};
    if (typeof remarks === 'string') {
        try { return JSON.parse(remarks); } catch (e) { return {}; }
    }
    return remarks;
};

const formatDestinations = (destStr) => {
    if (!destStr || destStr === '未處理') return ['未處理'];
    
    // Split by comma separator
    const parts = destStr.split(/[、,，]/);
    const results = parts.map(p => {
        const match = p.match(/^(.*?)(?:\((\d+)\))?$/);
        if (!match) return { name: p.trim(), qty: null };
        return { name: match[1].trim(), qty: match[2] };
    });

    const order = ['耀紫軍', '虎賁軍', '虎甲軍', '黑曜軍', '九天', '殲滅', '暫驅離', '未處理'];
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

const formatDate = (date) => {
    if (!date) return '';
    return new Date(date).toISOString().split('T')[0].replace(/-/g, '/');
};

const folders = ref([{ id: 'all', name: '怨靈記錄專區', status: '全部' }]);
const showAddMenu = ref(false);
const openMenuId = ref(null);
const items = ref([]);
const users = ref([]);
const loading = ref(true);
const editingId = ref(null);
const focusedId = ref(null);
const deleteConfirmId = ref(null);
const sortDesc = ref(true);
const showTotal = ref(false);
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
const showBatchImport = ref(false);
const openStatusId = ref(null);

const filteredItems = computed(() => {
    let filtered = items.value;
    if (currentFolder.value.status !== '全部') {
        filtered = filtered.filter(i => i.status === currentFolder.value.status);
    }
    if (searchQuery.value) {
        const q = searchQuery.value.toLowerCase();
        filtered = filtered.filter(i => 
            (i.destination?.toLowerCase().includes(q)) || 
            (i.user_name?.toLowerCase().includes(q))
        );
    }
    
    return filtered;
});

const sortedItems = computed(() => {
    let result = [...filteredItems.value];
    
    result.sort((a, b) => {
        // Dynamic Pinning
        if (focusedId.value === a.id) return -1;
        if (focusedId.value === b.id) return 1;

        // "未處理" (Unprocessed) always at the top
        const isUnA = a.destination === '未處理';
        const isUnB = b.destination === '未處理';
        if (isUnA && !isUnB) return -1;
        if (!isUnA && isUnB) return 1;

        const dateA = new Date(a.know_date);
        const dateB = new Date(b.know_date);
        if (dateA - dateB !== 0) {
            return sortDesc.value ? dateB - dateA : dateA - dateB;
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

const toggleMenu = (id) => { 
    openMenuId.value = openMenuId.value === id ? null : id;
    deleteConfirmId.value = null;
};

const toggleExpand = (id) => {
    focusedId.value = focusedId.value === id ? null : id;
};

const addActions = computed(() => [
    { 
        label: '逐筆新增', 
        description: '手動輸入單筆怨靈紀錄',
        icon: '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>',
        bgColor: 'bg-indigo-50',
        textColor: 'text-indigo-600',
        handler: () => { 
            editingId.value = null;
            form.value = { user_name: '', user_remarks: '', destination: '未處理', quantity: 0, know_date: new Date().toISOString().split('T')[0], process_date: '', remarks_text: '' };
            addMode.value = true; 
        } 
    },
    { 
        label: '多筆新增', 
        description: '直接貼上法號清單批量載錄',
        icon: '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>',
        bgColor: 'bg-emerald-50',
        textColor: 'text-emerald-600',
        handler: () => { 
            showBatchImport.value = true;
        } 
    },
    { 
        label: '匯出 EXCEL', 
        description: '下載當前篩選名單報表',
        icon: '<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/></svg>',
        bgColor: 'bg-green-50',
        textColor: 'text-green-600',
        handler: () => { 
            exportToExcel();
        } 
    },
    { 
        label: '複製貼 LINE', 
        description: '將清單轉為文字複製到 LINE',
        icon: '<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-1-13h2v6h-2zm0 8h2v2h-2z"/></svg>',
        bgColor: 'bg-amber-50',
        textColor: 'text-amber-600',
        handler: () => { 
            copyAllToLine();
        } 
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
    navigator.clipboard.writeText(text).then(() => {
        alert('已複製到剪貼簿，快去 LINE 貼上吧！');
    });
};

const exportToExcel = () => {
    if (!filteredItems.value.length) return alert('目前沒有資料可供匯出');
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
    const dateStr = new Date().toISOString().split('T')[0].replace(/-/g, '');
    XLSX.writeFile(workbook, `怨靈報表_${dateStr}.xlsx`);
};

const form = ref({ user_name: '', user_remarks: '', destination: '未處理', quantity: 1, know_date: new Date().toISOString().split('T')[0], process_date: '', remarks_text: '', remarks: {} });

const loadData = async () => {
    loading.value = true;
    try {
        const [res, dres] = await Promise.all([ axios.get('/grudges'), axios.get('/api/dharma-names-list') ]);
        items.value = res.data;
        users.value = dres.data;
    } catch (e) { console.error(e); } finally { loading.value = false; }
};

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
        focusedId.value = null; // Restore position after save
        loadData();
    } catch (e) { alert('儲存失敗: ' + (e.response?.data?.message || e.message)); }
};

const handleBack = () => {
    if (addMode.value) { addMode.value = false; editingId.value = null; }
    else if (focusedId.value) { focusedId.value = null; }
    else { emit('goHome'); }
};

const editItem = (item) => {
    focusedId.value = item.id;
    editingId.value = item.id;
    // Ensure remarks are parsed and deep-cloned for independent editing
    const parsed = parseRemarks(item.remarks);
    form.value = { ...item, remarks: JSON.parse(JSON.stringify(parsed)) };
    addMode.value = true;
    openMenuId.value = null;
};

const deleteItem = async (id) => {
    if (!confirm('確定要刪除這筆資料嗎？')) return;
    await axios.delete(`/grudges/${id}`);
    loadData();
};

const copyItem = async (item) => {
    const text = `【怨靈結果】${item.user_name}: ${item.destination} (${item.quantity})`;
    await navigator.clipboard.writeText(text);
    alert('已複製');
    openMenuId.value = null;
};

const downloadItem = (item, format = 'txt') => {
    let contents = '';
    let fileName = '';
    
    if (format === 'csv') {
        const headers = ['法號', '日期', '數量', '處理結果', '處理日期', '備註'].join(',');
        const row = [
            item.user_name || '未知',
            formatDate(item.know_date),
            item.quantity,
            item.destination || '未處理',
            item.process_date ? formatDate(item.process_date) : '-',
            item.remarks_text || '無'
        ].map(val => `"${val}"`).join(',');
        contents = '\uFEFF' + headers + '\n' + row;
        fileName = `怨靈_${item.user_name || '未名'}.csv`;
    } else {
        contents = `【怨靈處理結果】\r\n法號：${item.user_name || '未知'}\r\n日期：${formatDate(item.know_date)}\r\n數量：${item.quantity}\r\n結果：${item.destination || '未處理'}\r\n處理日期：${item.process_date ? formatDate(item.process_date) : '-'}\r\n備註：${item.remarks_text || '無'}`;
        fileName = `怨靈_${item.user_name || '未名'}.txt`;
    }

    const blob = new Blob([contents], { type: format === 'csv' ? 'text/csv;charset=utf-8' : 'text/plain;charset=utf-8' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = fileName;
    a.click();
    window.URL.revokeObjectURL(url);
    openMenuId.value = null;
};

const downloadList = (format = 'txt') => {
    let contents = '';
    let fileName = '';

    if (format === 'csv') {
        const headers = ['法號', '親友備註', '得知日期', '數量', '處理結果', '大姊(閻尊)', '四妹(閻闇)', '勝(龍勝)', '戰(龍戰)', '備註文字'].join(',');
        const rows = filteredItems.value.map(i => [
            i.user_name || '未知',
            i.user_remarks || '',
            formatDate(i.know_date),
            i.quantity,
            i.destination || '未處理',
            i.remarks?.yan_zun || 0,
            i.remarks?.yan_an || 0,
            i.remarks?.long_sheng || 0,
            i.remarks?.long_zhan || 0,
            i.remarks_text || ''
        ].map(val => `"${val}"`).join(','));
        contents = '\uFEFF' + headers + '\n' + rows.join('\n');
        fileName = '怨靈處理結果清單.csv';
    } else {
        contents = `【怨靈處理結果清單】\r\n\r\n` + 
            filteredItems.value.map(i => `${i.user_name || '未知'}${i.user_remarks?'('+i.user_remarks+')':''}: ${i.destination || '未處理'} (${i.quantity})`).join('\r\n');
        fileName = '怨靈處理結果清單.txt';
    }
    
    const blob = new Blob([contents], { type: format === 'csv' ? 'text/csv;charset=utf-8' : 'text/plain;charset=utf-8' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = fileName;
    a.click();
    window.URL.revokeObjectURL(url);
};

const totalGrudgeQuantity = computed(() => items.value.reduce((sum, i) => sum + (Number(i.quantity) || 0), 0));
const filteredTotal = computed(() => filteredItems.value.reduce((sum, i) => sum + (Number(i.quantity) || 0), 0));

// Military Detailed Totals
const obsidianItems = computed(() => items.value.filter(i => i.destination === '黑曜軍'));
const obsidianTotal = computed(() => obsidianItems.value.reduce((sum, i) => sum + (Number(i.quantity) || 0), 0));
const yanZunTotal = computed(() => obsidianItems.value.reduce((sum, s) => sum + (Number(parseRemarks(s.remarks).yan_zun) || 0), 0));
const yanAnTotal = computed(() => obsidianItems.value.reduce((sum, s) => sum + (Number(parseRemarks(s.remarks).yan_an) || 0), 0));

const purpleItems = computed(() => items.value.filter(i => i.destination === '耀紫軍'));
const purpleTotal = computed(() => purpleItems.value.reduce((sum, i) => sum + (Number(i.quantity) || 0), 0));
const longShengTotal = computed(() => purpleItems.value.reduce((sum, s) => sum + (Number(parseRemarks(s.remarks).long_sheng) || 0), 0));
const longZhanTotal = computed(() => purpleItems.value.reduce((sum, s) => sum + (Number(parseRemarks(s.remarks).long_zhan) || 0), 0));

const displayTitle = computed(() => currentFolder.value?.name || '怨靈記錄專區');

onMounted(() => {
    loadData();
    window.addEventListener('click', (e) => { 
        if (!e.target.closest('.relative')) openMenuId.value = null; 
    });
});
</script>

<style scoped>
.animate-slide-up { animation: slideUp 0.15s ease-out; }
@keyframes slideUp { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
.animate-fade-in { animation: fadeIn 0.3s ease-in; }
@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.05); border-radius: 10px; }
</style>
