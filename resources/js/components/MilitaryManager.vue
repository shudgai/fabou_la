<template>
    <div class="bg-white h-[100dvh] flex flex-col relative overflow-hidden">
        <!-- Static Header -->
        <div class="border-b border-gray-100 flex items-center bg-white sticky top-0 z-30 px-4 h-[50px]">
            <!-- Left: Sort Button -->
            <div class="w-[70px] flex items-center shrink-0">
                <button v-if="currentFolder" @click="sortDesc = !sortDesc" class="text-[13px] text-indigo-500 font-normal bg-indigo-50 px-2 py-0.5 rounded-lg active:scale-95 transition-all">
                    {{ sortDesc ? '新→舊' : '舊→新' }}
                </button>
            </div>

            <!-- Center: Title -->
            <div class="flex-1 flex justify-center items-center min-w-0">
                <h2 class="text-[20px] font-bold text-slate-900 tracking-tight flex items-center truncate">
                    {{ currentFolder ? currentFolder.name : '軍隊專區' }}
                </h2>
            </div>

            <!-- Right: Interactive Total Toggle (Optimized Size) -->
            <div class="w-[140px] flex items-center justify-end shrink-0">
                <button v-if="currentFolder" 
                    @click="toggleFullTotal"
                    class="text-black font-normal whitespace-nowrap active:opacity-60 transition-all pr-1"
                    style="font-size: 15px;"
                >
                    總量<span v-if="showFullTotal || searchQuery" class="ml-0.5 font-mono text-[16px]" :class="{'text-indigo-600': searchQuery}">:{{ currentFolderTotal }}</span>
                </button>
            </div>
        </div>

        <!-- FOLDER SELECTION VIEW -->
        <div v-if="!currentFolder" class="flex-1 overflow-y-auto bg-slate-50/50 p-4 animate-fade-in">
            <div class="grid grid-cols-2 gap-[10px] p-4 place-items-center">
                <div v-for="folder in folders" :key="folder.id" 
                    @click="currentFolder = folder"
                    class="flex flex-col items-center justify-center bg-transparent transition-all active:scale-95 border border-[rgb(255,215,0)] rounded-xl group p-2 w-[120px] h-[130px] relative cursor-pointer"
                   >
                    <div class="w-14 h-14 rounded-2xl flex items-center justify-center mb-1" :class="folder.color">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                        </svg>
                    </div>
                    <span class="text-[18px] font-bold text-slate-900">{{ folder.name }}</span>
                </div>
            </div>
            
            <!-- Statistics Card -->
            <div class="mt-8 bg-indigo-600 rounded-[32px] p-6 text-white shadow-xl shadow-indigo-100 flex items-center justify-between">
                <div>
                    <h3 class="text-[14px] opacity-80 font-normal mb-1">軍隊總數規模</h3>
                    <p class="text-[28px] font-normal font-outfit leading-none">{{ totalQuantity }}</p>
                </div>
                <div class="w-14 h-14 bg-white/20 rounded-full flex items-center justify-center">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </div>
            </div>
        </div>
        
        <!-- SEARCH & LEDGER VIEW -->
        <template v-else>
            <div v-if="showSearch" class="px-[10px] mt-2 animate-fade-in">
                <input v-model="searchQuery" type="text" placeholder="搜尋項目..." 
                    class="w-full h-[36px] bg-white border border-slate-200 rounded-xl px-4 text-[15px] focus:ring-0 outline-none">
            </div>

            <div class="flex-1 overflow-x-auto overflow-y-auto pl-[10px] bg-white flex flex-col" :class="[focusedId ? 'h-full pb-0' : 'pb-24']">
                <div v-if="loading" class="text-center py-10 text-xs text-slate-400">載入中...</div>
                <div v-else-if="filteredItems.length === 0" class="text-center py-20 text-slate-400 font-light">
                    目前尚無{{ currentFolder.name }}載錄資料。
                </div>
                <div v-else class="flex flex-col flex-1">
                    <!-- Table Header -->
                    <div class="flex flex-col flex-1">
                        <!-- List Body -->
                        <div v-for="(item, index) in sortedItems" :key="item.id" 
                            v-show="focusedId === null || focusedId === item.id"
                            @click="toggleExpand(item.id)"
                            :class="[
                                'py-[16px] px-2 border-b border-slate-900 last:border-b-0 relative group transition-all cursor-pointer z-10',
                                item.groupParity === 1 ? 'bg-slate-50/50' : 'bg-white',
                                { 'border-t border-slate-900': index === 0 && !focusedId, 'border-b-0': focusedId === item.id }
                            ]"
                        >
                            <!-- List Item Display (Collapsed - Grudge Style) -->
                            <div v-if="focusedId !== item.id" class="mt-0 flex flex-col pointer-events-none">
                                <!-- Row 1: Date only -->
                                <div class="flex items-center mb-0.5">
                                    <div class="flex items-baseline space-x-2">
                                        <div class="text-[14px] font-normal text-slate-400 uppercase tracking-wider">日期</div>
                                        <div class="text-[18px] text-slate-900 font-normal ml-0.5">{{ formatDate(item.know_date) }}</div>
                                    </div>
                                </div>

                                <!-- Row 2: Name & Subtotal -->
                                <div class="flex items-center justify-between">
                                    <div class="grid grid-cols-2 flex-1 items-center">
                                        <!-- Dharma Name -->
                                        <div class="text-[18px] font-normal text-slate-900 leading-tight truncate pr-2">
                                            {{ item.user_name || '-' }}
                                            <span v-if="item.user_remarks" class="text-slate-900 ml-1.5 font-normal">{{ item.user_remarks }}</span>
                                        </div>
                                        <!-- Subtotal -->
                                        <div class="text-[18px] text-slate-900 font-normal">
                                            <span class="text-[14px] font-normal text-slate-400 uppercase mr-1">小計:</span> 
                                            <span class="text-[18px] text-slate-900 font-normal ml-0.5">{{ item.quantity || 0 }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Menu Button Layer -->
                            <div v-if="focusedId !== item.id" class="absolute right-0 top-0.5 z-20">
                                <button @click.stop="toggleMenu(item.id)" class="p-2 -mr-1 text-slate-400 active:text-indigo-600 transition-colors"><svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM18 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg></button>
                                <div v-if="openMenuId === item.id" @click.stop class="absolute right-0 top-full mt-1 w-32 bg-white rounded-xl shadow-2xl border border-slate-100 z-[100] overflow-hidden animate-slide-up">
                                    <button @click.stop="toggleExpand(item.id); openMenuId = null" class="w-full p-2.5 text-left text-[14px] text-indigo-600 hover:bg-indigo-50 border-b border-slate-50 font-normal">展開詳情</button>
                                    <button @click.stop="editItem(item)" class="w-full p-2.5 text-left text-[14px] text-slate-600 hover:bg-slate-50 border-b border-slate-50">修改內容</button>
                                    <button @click.stop="deleteItem(item.id)" class="w-full p-2.5 text-left text-[14px] text-red-600 hover:bg-red-50">刪除</button>
                                </div>
                            </div>

                            <!-- Expanded Detail (Minimalist Style - Wide) -->
                            <div v-if="focusedId === item.id" class="animate-fade-in py-3 bg-white space-y-4 relative px-1.5">
                                <!-- Menu Button for Expanded State -->
                                <div class="absolute right-0 top-0 z-[101]">
                                    <button @click.stop="toggleMenu(item.id)" class="p-1 text-slate-400 hover:text-indigo-600 active:scale-95 transition-all"><svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"><path d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM18 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg></button>
                                    <div v-if="openMenuId === item.id" @click.stop class="absolute right-0 top-full mt-1 w-32 bg-white rounded-xl shadow-2xl border border-slate-100 z-[102] overflow-hidden animate-slide-up">
                                        <button @click.stop="toggleExpand(item.id); openMenuId = null" class="w-full p-2.5 text-left text-[14px] text-indigo-600 hover:bg-indigo-50 border-b border-slate-50 font-normal">收合詳情</button>
                                        <button @click.stop="editItem(item)" class="w-full p-2.5 text-left text-[14px] text-slate-600 hover:bg-slate-50 border-b border-slate-50">修改內容</button>
                                        <button @click.stop="deleteItem(item.id)" class="w-full p-2.5 text-left text-[14px] text-red-600 hover:bg-red-50 font-medium">刪除</button>
                                    </div>
                                </div>

                                <!-- Date Row -->
                                <div class="space-y-1">
                                    <div class="flex items-center space-x-1 ml-1">
                                        <button @click.stop="toggleExpand(item.id)" class="p-1 -ml-1 text-slate-300">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" /></svg>
                                        </button>
                                        <label class="text-[14px] font-normal text-slate-400 tracking-wider">日期</label>
                                    </div>
                                    <div class="w-full px-3 flex items-center text-[18px] font-normal text-slate-900">
                                        {{ formatDate(item.know_date) }}
                                    </div>
                                </div>

                                <!-- Name Row (Combined) -->
                                <div class="space-y-1">
                                    <label class="text-[14px] font-normal text-slate-400 tracking-wider ml-1">法號 (親友/信眾)</label>
                                    <div class="w-full px-3 flex items-center text-[18px] font-normal text-slate-900 leading-tight">
                                        {{ item.user_name }}
                                        <span v-if="item.user_remarks" class="text-slate-900 ml-1.5 font-normal">{{ item.user_remarks }}</span>
                                    </div>
                                </div>

                                <!-- Quantities Row (Grid) -->
                                <div v-if="item.army_type === '黑曜軍' || item.army_type === '耀紫軍'" class="grid grid-cols-2 gap-3">
                                    <template v-if="item.army_type === '黑曜軍'">
                                        <div class="space-y-1">
                                            <label class="text-[13px] font-normal text-[#aeb4be] tracking-wider ml-1">閻尊數量</label>
                                            <div class="w-full px-3 flex items-center text-[16px] font-normal text-slate-900 font-mono">{{ item.yan_zun || 0 }}</div>
                                        </div>
                                        <div class="space-y-1">
                                            <label class="text-[13px] font-normal text-[#aeb4be] tracking-wider ml-1">閻闇數量</label>
                                            <div class="w-full px-3 flex items-center text-[16px] font-normal text-slate-900 font-mono">{{ item.yan_an || 0 }}</div>
                                        </div>
                                    </template>
                                    <template v-if="item.army_type === '耀紫軍'">
                                        <div class="space-y-1">
                                            <label class="text-[13px] font-normal text-[#aeb4be] tracking-wider ml-1">龍勝數量</label>
                                            <div class="w-full px-3 flex items-center text-[16px] font-normal text-slate-900 font-mono">{{ item.long_sheng || 0 }}</div>
                                        </div>
                                        <div class="space-y-1">
                                            <label class="text-[13px] font-normal text-[#aeb4be] tracking-wider ml-1">龍戰數量</label>
                                            <div class="w-full px-3 flex items-center text-[16px] font-normal text-slate-900 font-mono">{{ item.long_zhan || 0 }}</div>
                                        </div>
                                    </template>
                                </div>

                                <!-- Subtotal Special Row (Minimalist Style) -->
                                <div class="w-full px-4 flex items-center justify-end py-2 border-t border-slate-50 mt-1 space-x-2">
                                    <span class="text-[14px] font-normal text-slate-400 tracking-wider">小計</span>
                                    <span class="text-[18px] font-normal text-slate-900">{{ item.quantity || 0 }}</span>
                                </div>

                                <!-- Remarks Row -->
                                <div v-if="item.remarks_text" class="space-y-1">
                                    <label class="text-[13px] font-normal text-[#aeb4be] tracking-wider ml-1">備註文字</label>
                                    <div class="w-full px-3 py-1 text-[16px] font-normal text-slate-600 leading-relaxed whitespace-pre-wrap">
                                        {{ item.remarks_text }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        <!-- FAB Bottom Navigation -->
        <mobile-navbar 
            :can-back="true"
            :show-action="!!currentFolder"
            :action-active="showAddMenu"
            :search-active="showSearch"
            :can-more="!!currentFolder"
            @back="handleBack"
            @home="$emit('goHome')"
            @action="showAddMenu = !showAddMenu"
            @search="showSearch = !showSearch"
            @more="exportToExcel"
        />

        <!-- MODALS -->
        <add-action-menu :show="showAddMenu" @close="showAddMenu = false" :actions="addActions" />
        <military-add-form :key="editingId || 'new'" :show="addMode" :initialData="form" :editingId="editingId" :users="users" :armyType="currentFolder ? currentFolder.name : ''" @save="saveItem" @cancel="addMode = false; editingId = null" />
        <military-batch-add :show="batchMode" :armyType="currentFolder ? currentFolder.name : ''" @save="batchMode = false; loadData()" @cancel="batchMode = false" />
    </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import axios from 'axios';
import MilitaryAddForm from './MilitaryAddForm.vue';
import MilitaryBatchAdd from './MilitaryBatchAdd.vue';
import AddActionMenu from './AddActionMenu.vue';
import MobileNavbar from './MobileNavbar.vue';

const emit = defineEmits(['goHome']);

const folders = [
    { id: 'armor', name: '虎甲軍', color: 'bg-indigo-600' },
    { id: 'brave', name: '虎賁軍', color: 'bg-blue-600' },
    { id: 'obsidian', name: '黑曜軍', color: 'bg-slate-800' },
    { id: 'purple', name: '耀紫軍', color: 'bg-purple-600' }
];

const currentFolder = ref(null);
const addMode = ref(false);
const batchMode = ref(false);
const showAddMenu = ref(false);
const showSearch = ref(false);
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

const toggleFullTotal = () => {
    showFullTotal.value = true;
    if (fullTotalTimer) clearTimeout(fullTotalTimer);
    fullTotalTimer = setTimeout(() => {
        showFullTotal.value = false;
    }, 10000);
};

const filteredItems = computed(() => {
    if (!currentFolder.value) return [];
    
    const folderName = currentFolder.value.name;
    // Create a regex to match the folder name, ignoring spaces and non-printable characters
    const cleanFolderName = folderName.replace(/[^\u4e00-\u9fa5]/g, '');
    const regex = new RegExp(cleanFolderName.split('').join('.*'), 'i');

    let filtered = items.value.filter(i => {
        if (!i.army_type) return false;
        const recordType = String(i.army_type).trim();
        const target = currentFolder.value?.name;
        // Super loose match
        return recordType === target || 
               recordType.includes(target) || 
               target.includes(recordType) ||
               regex.test(recordType);
    });
    
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
        // Dynamic Pinning
        if (focusedId.value === a.id) return -1;
        if (focusedId.value === b.id) return 1;

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

const totalQuantity = computed(() => items.value.reduce((sum, i) => sum + (Number(i.quantity) || 0), 0));
const currentFolderTotal = computed(() => filteredItems.value.reduce((sum, i) => sum + (Number(i.quantity) || 0), 0));

const toggleMenu = (id) => { openMenuId.value = openMenuId.value === id ? null : id; };
const toggleStatusMenu = (id) => { openStatusId.value = openStatusId.value === id ? null : id; };
const toggleExpand = (id) => { focusedId.value = focusedId.value === id ? null : id; };

const addActions = computed(() => [
    { 
        label: '逐筆新增', 
        description: `輸入單筆${currentFolder.value?.name}紀錄`,
        icon: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>',
        handler: () => { 
            editingId.value = null;
            form.value = { 
                user_name: '', user_remarks: '', destination: '未處理', quantity: (currentFolder.value?.id === 'armor' || currentFolder.value?.id === 'brave') ? 1 : 0, 
                yan_zun: 0, yan_an: 0, long_sheng: 0, long_zhan: 0,
                know_date: new Date().toISOString().split('T')[0], process_date: '', remarks_text: '',
                army_type: currentFolder.value?.name
            };
            addMode.value = true; 
        } 
    },
    { 
        label: '多筆新增', 
        description: `貼上文字清單批次匯入`,
        icon: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>',
        handler: () => { 
            batchMode.value = true;
        } 
    },
    { 
        label: '匯出 Excel', 
        description: `下載${currentFolder.value?.name}報表檔案`,
        icon: '<svg fill="currentColor" viewBox="0 0 24 24"><path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/></svg>',
        handler: () => { 
            exportToExcel();
        } 
    },
    { 
        label: '複製貼 LINE', 
        description: `將清單轉為文字複製到 LINE`,
        icon: '<svg fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-1-13h2v6h-2zm0 8h2v2h-2z"/></svg>',
        handler: () => { 
            copyToLine();
        } 
    }
]);

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
        const row = {
            '日期': formatDate(item.know_date),
            '法號': item.user_name + (item.user_remarks || ''),
            '總數量': qty
        };
        
        if (item.army_type === '黑曜軍') {
            row['閻尊量'] = item.yan_zun || 0;
            row['閻闇量'] = item.yan_an || 0;
        } else if (item.army_type === '耀紫軍') {
            row['龍勝量'] = item.long_sheng || 0;
            row['龍戰量'] = item.long_zhan || 0;
        }
        
        row['備註'] = item.remarks_text || '';
        return row;
    });

    // Add Total Row at the end
    data.push({
        '日期': '',
        '法號': '總結',
        '總數量': total,
        '備註': ''
    });

    const worksheet = XLSX.utils.json_to_sheet(data);
    const workbook = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(workbook, worksheet, currentFolder.value.name);
    
    const dateStr = new Date().toISOString().split('T')[0].replace(/-/g, '');
    const fileName = `${currentFolder.value.name}_載錄報表_${dateStr}.xlsx`;
    
    XLSX.writeFile(workbook, fileName);
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
        console.log(`Military loaded: ${items.value.length} items`, items.value[0]);
    } catch (e) { 
        console.error('Load data failed:', e);
        items.value = []; 
    } finally { 
        loading.value = false; 
    }
};

const saveItem = async (formData) => {
    try {
        if (editingId.value) await axios.put(`/military-records/${editingId.value}`, formData);
        else await axios.post('/military-records', formData);
        
        // Reset state after success
        addMode.value = false; 
        editingId.value = null;
        searchQuery.value = ''; // Auto-clear search to show new data
        focusedId.value = null;
        
        await loadData();
    } catch (e) { 
        console.error('Save failed:', e);
        alert('儲存失敗，請檢查資料格式'); 
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
    loadData();
};

const formatDate = (d) => {
    if (!d) return '-';
    // Use standard YYYY/MM/DD
    const date = new Date(d);
    const y = date.getFullYear();
    const m = String(date.getMonth() + 1).padStart(2, '0');
    const d_val = String(date.getDate()).padStart(2, '0');
    return `${y}/${m}/${d_val}`;
};

onMounted(loadData);
</script>

<style scoped>
.animate-fade-in { animation: fadeIn 0.3s ease-out; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }
.animate-slide-up { animation: slideUp 0.15s ease-out; }
@keyframes slideUp { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
</style>
