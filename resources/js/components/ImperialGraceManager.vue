<template>
    <div class="bg-white h-[100vh] flex flex-col relative overflow-hidden text-slate-900">
        <!-- Header (Only show in Folder-view or Item-view) -->
        <div v-if="currentFolder" class="border-b border-slate-300 flex items-center justify-center bg-white sticky top-0 z-10" style="padding: 12px 10px 10px 10px;">
            <h2 class="text-[21px] font-normal font-outfit tracking-tight text-black flex items-center">
                <span v-if="currentFolder">重大皇恩 - {{ currentFolder.name }}</span>
                <span v-else>重大皇恩</span>
                <button @click="toggleSort" class="ml-2 px-1 text-[10px] text-indigo-500 font-normal bg-indigo-50 border border-indigo-100 rounded active:scale-95 transition-all opacity-80 tracking-tighter self-end mb-1">
                    ({{ sortDesc ? '新→舊' : '舊→新' }})
                </button>
            </h2>
        </div>
        <!-- Perfectly Centered Ultra-Compact Warning Banner -->
        <div v-if="persistentToast" class="fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 z-[9999] animate-toast-in pointer-events-auto">
            <div class="bg-white rounded-lg shadow-[0_4px_20px_rgba(0,0,0,0.2)] flex flex-col border border-slate-200"
                style="padding: 10px 15px; min-width: 140px; max-width: 85vw;">
                <div class="flex items-start justify-between space-x-3">
                    <span class="text-[12px] font-bold leading-normal text-red-600 break-words uppercase tracking-wide">
                        {{ persistentToast.msg }}
                    </span>
                    <button v-if="['confirm', 'deleteConfirm'].includes(persistentToast.type)" 
                        @click="persistentToast = null" 
                        class="text-slate-400 hover:text-slate-600 w-5 h-5 flex items-center justify-center font-normal text-sm">✕</button>
                </div>
                <!-- Action Buttons (Horizontal Layout) -->
                <div v-if="['confirm', 'deleteConfirm'].includes(persistentToast.type)" class="flex space-x-2 mt-3 pb-1">
                    <template v-if="persistentToast.type === 'confirm'">
                        <button @click="saveSingle('shunt')" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white px-2 py-1.5 rounded shadow-sm text-[11px] font-bold whitespace-nowrap">確定</button>
                    </template>
                    <template v-if="persistentToast.type === 'deleteConfirm'">
                        <button @click="persistentToast = null" class="flex-1 bg-slate-50 hover:bg-slate-100 text-slate-600 px-2 py-1.5 rounded border border-slate-200 text-[11px] font-bold whitespace-nowrap">取消</button>
                        <button @click="executeDelete" class="flex-1 bg-red-50 hover:bg-red-100 text-red-600 px-2 py-1.5 rounded border border-red-100 text-[11px] font-bold whitespace-nowrap">確定刪除</button>
                    </template>
                </div>
            </div>
        </div>
        <div class="flex-1 overflow-y-auto custom-scrollbar" style="padding-bottom: 5px;">
        <!-- Level 1: Folder Selection -->
        <div v-if="!currentFolder" class="bg-white">
            <!-- Header Title -->
            <div class="px-6 pt-[20px] pb-2 text-center">
                <h1 class="text-2xl font-normal text-slate-800 tracking-tight">重大皇恩</h1>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-2 gap-[10px] p-4">
                <button v-for="(folder, idx) in folders" :key="folder.id" 
                    @click="currentFolder = folder"
                    class="flex flex-col items-center justify-center bg-transparent transition-all active:scale-95 rounded-[28px] border border-[rgb(255,215,0)] group px-[5px] pt-[10px] pb-0 aspect-square relative w-[72%] mx-auto"
                   >
                    <div class="relative mb-3">
                        <svg class="w-14 h-14 transition-transform group-hover:scale-110 drop-shadow-md" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <defs>
                                <linearGradient id="folderGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                                    <stop offset="0%" style="stop-color:rgb(255, 235, 120);stop-opacity:1" />
                                    <stop offset="50%" style="stop-color:rgb(255, 215, 0);stop-opacity:1" />
                                    <stop offset="100%" style="stop-color:rgb(218, 165, 32);stop-opacity:1" />
                                </linearGradient>
                            </defs>
                            <!-- Folder Body Back -->
                            <path d="M4 14C4 11.7909 5.79086 10 8 10H24.5L30 16H56C58.2091 16 60 17.7909 60 20V50C60 52.2091 58.2091 54 56 54H8C5.79086 54 4 52.2091 4 50V14Z" fill="url(#folderGrad)" opacity="0.8"/>
                            <!-- Folder Front Cover (3D Offset) -->
                            <path d="M4 22C4 19.7909 5.79086 18 8 18H56C58.2091 18 60 19.7909 60 22V50C60 52.2091 58.2091 54 56 54H8C5.79086 54 4 52.2091 4 50V22Z" fill="url(#folderGrad)" stroke="rgba(255,255,255,0.4)" stroke-width="0.5"/>
                            <!-- Shine effect -->
                            <path d="M10 21H54" stroke="white" stroke-opacity="0.3" stroke-linecap="round"/>
                        </svg>
                    </div>
                    <div class="mt-[-10px] text-center px-1">
                        <div class="text-[14px] leading-tight text-slate-700 break-words font-normal">
                            <template v-if="folder.id === 'unobtained'">
                                <div>未求得</div>
                                <div>重大皇恩</div>
                            </template>
                            <template v-else>
                                {{ folder.name }}
                            </template>
                        </div>
                    </div>
                </button>
            </div>


            <!-- Minimalist Back to Dashboard -->
            <div class="mt-12 flex justify-center pb-[5px]">
                <button @click="$emit('goHome')" class="text-slate-300 hover:text-slate-500 transition-colors flex items-center space-x-2 active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" /></svg>
                    <span class="text-xs font-bold tracking-widest uppercase">返回筆記本列表</span>
                </button>
            </div>
        </div>

        <!-- Level 2: Folder Contents -->
        <div v-else class="px-[5px] md:px-0 bg-white">

            <!-- List Display -->
            <div style="padding: 0px 10px 10px 10px;" class="mt-[-15px]">
                <div v-if="!loading && allRegistries.length > 0 && !addMode" class="mb-3 px-1">
                </div>

                <div v-if="loading" class="text-center py-4 text-xs text-slate-400">載入中...</div>
                <div v-else class="flex flex-col">

                    <div v-for="(reg, index) in (focusedId ? filteredRegistries.filter(r => r.id === focusedId) : filteredRegistries)" :key="reg.id" 
                        @click="toggleExpand(reg.id)"
                        :class="[
                            'py-[5px] border-b border-slate-300 last:border-b-0 relative group transition-colors cursor-pointer bg-white',
                            { 'border-t border-slate-300': index === 0 }
                        ]">
                        <div class="mt-0 flex flex-col">
                            <!-- Master Name Row (Only in Unobtained Folder) -->
                            <div v-if="currentFolder.id === 'unobtained' && reg.master_id" class="text-[15px] text-slate-400 leading-none mb-1">
                                {{ getMasterName(reg.master_id) }}
                            </div>

                            <!-- Row 1: Label & Dates & Menu -->
                            <div class="flex items-center justify-between mb-0.5">
                                <div class="flex items-baseline space-x-2">
                                    <div class="text-[11.5px] font-normal text-slate-400 uppercase tracking-wider whitespace-nowrap">法寶名稱</div>
                                    <div class="text-[11.5px] text-slate-400 font-normal">
                                        得知: <span class="text-slate-500">{{ reg.record_date?.replace(/-/g, '/') || '-' }}</span>
                                        <span class="ml-2 font-normal">
                                            求得: <span class="text-slate-500">{{ reg.obtained_date?.replace(/-/g, '/') || '----' }}</span>
                                        </span>
                                    </div>
                                </div>

                                <!-- Action Menu Button (Moved to Row 1) -->
                                <div class="relative" :class="[deleteConfirmId === reg.id ? 'text-red-500' : 'text-slate-400']">
                                    <button @click.stop="toggleMenu(reg.id)" class="p-1 -mr-1">
                                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM18 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                                    </button>
                                    <!-- MENU DROPDOWN -->
                                    <div v-if="openMenuId === reg.id" @click.stop 
                                         class="absolute right-0 top-full mt-1 w-28 bg-white rounded-xl shadow-2xl border border-slate-100 z-[100] overflow-hidden animate-slide-up">
                                        <button @click.stop="openAndEdit(reg)" class="w-full p-2 text-left text-[11px] text-slate-600 hover:bg-slate-50 border-b border-slate-50">修改內容</button>
                                        <button @click.stop="copyOnly(reg)" class="w-full p-2 text-left text-[11px] text-green-600 hover:bg-green-50 border-b border-slate-50 font-medium whitespace-nowrap">複製貼 LINE</button>
                                        <button @click.stop="downloadOnly(reg)" class="w-full p-2 text-left text-[11px] text-blue-600 hover:bg-blue-50 border-b border-slate-50 font-normal whitespace-nowrap">單筆檔案下載</button>
                                        <button @click.stop="confirmDelete(reg.id)" class="w-full p-2 text-left text-[11px] text-red-600 hover:bg-red-50">刪除</button>
                                    </div>
                                </div>
                            </div>

                            <!-- Row 2: Name and Status (Unified Height) -->
                            <div class="flex items-center justify-between">
                                <div class="text-[15.5px] font-normal text-slate-900 leading-tight">
                                    {{ reg.name }}
                                </div>

                                <div class="flex items-center">
                                    <!-- Quick Status Toggle -->
                                    <span @click.stop="currentFolder.id === 'unobtained' ? quickToggleStatus(reg) : null" :class="{
                                        'bg-blue-50 text-blue-600 border-blue-100': reg.status === '已求得',
                                        'bg-emerald-50 text-emerald-600 border-emerald-100': reg.status === '已登記',
                                        'bg-rose-50 text-rose-500 border-rose-100': reg.status === '未求得',
                                        'cursor-pointer': currentFolder.id === 'unobtained'
                                    }" class="text-[12px] px-1 py-0.5 rounded border font-normal select-none whitespace-nowrap active:scale-90 transition-all">
                                        {{ reg.status }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <!-- Extra Rows -->
                        <div v-if="expandedIds.has(reg.id)" class="animate-fade-in text-[14.5px] text-slate-900 mt-2">
                            <div v-if="reg.purpose"><span class="text-slate-500">用意：</span>{{ reg.purpose }}</div>
                            <div v-if="reg.remarks"><span class="text-slate-500">備註：</span>{{ reg.remarks }}</div>
                        </div>
                    </div>
        </div>
    </div>
</div>

    </div> <!-- End Scrollable Area -->
    
        <div class="fixed bottom-0 left-0 right-0 bg-white/95 backdrop-blur-md border-t border-slate-100 z-50 shadow-[0_-4px_10px_rgba(0,0,0,0.03)]" style="height: 30px;">
            <div class="grid grid-cols-5 h-full items-center px-2">
                <!-- BACK BUTTON -->
                <div class="flex justify-center">
                    <button @click="handleBack" 
                        class="w-7 h-7 rounded-xl flex items-center justify-center transition-all active:scale-90 text-slate-400 hover:bg-slate-50">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" /></svg>
                    </button>
                </div>

                <!-- HOME BUTTON -->
                <div class="flex justify-center">
                    <button @click="$emit('goHome')" class="w-7 h-7 rounded-xl flex items-center justify-center transition-all active:scale-95 text-slate-400 hover:bg-slate-50">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                </div>

                <!-- ADD BUTTON (Center) -->
                <div class="flex justify-center items-center">
                    <button v-if="currentFolder && currentFolder.id !== 'unobtained'" @click="showAddMenu = !showAddMenu" 
                        :class="[showAddMenu ? 'bg-slate-800 rotate-45 scale-90' : 'bg-indigo-600 text-white shadow-sm active:scale-95']"
                        class="w-7 h-7 rounded-xl flex items-center justify-center transition-all duration-500">
                        <svg class="h-[10px] w-[10px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                </div>

                <!-- SEARCH BUTTON -->
                <div class="flex justify-center">
                    <button @click="showSearch = !showSearch" 
                        :class="showSearch ? 'text-indigo-600 bg-indigo-50' : 'text-slate-400'"
                        class="w-7 h-7 rounded-xl flex items-center justify-center transition-all active:scale-95 hover:bg-slate-50">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" /></svg>
                    </button>
                </div>

                <!-- DOWNLOAD BUTTON -->
                <div class="flex justify-center">
                    <button @click="downloadListOnly" :disabled="!currentFolder"
                        class="w-8 h-8 rounded-xl flex items-center justify-center transition-all active:scale-95 text-slate-400 hover:bg-slate-50 disabled:opacity-30">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                </div>
            </div>
        </div>


    <!-- MODAL COMPONENTS -->
    <search-component 
        v-model="searchQuery" 
        :show="showSearch" 
        @close="showSearch = false" 
    />

    <add-action-menu 
        :show="showAddMenu" 
        @close="showAddMenu = false"
        :actions="addActions"
    />

    <imperial-grace-add-form 
        :mode="addMode"
        :initialData="form"
        :masters="masters"
        :isSaving="isSaving"
        @saveSingle="saveSingle"
        @saveBatch="triggerBatchSave"
        @cancel="addMode = null"
        @fileUpload="handleFileUpload"
    />
    </div>
</template>

<script setup>
import { ref, computed, onMounted, defineEmits, watch } from 'vue';
import axios from 'axios';

const emit = defineEmits(['goHome']);

const currentFolder = ref(null);
const addMode = ref(null);
const allRegistries = ref([]);
const masters = ref([]);
const loading = ref(false);
const isSaving = ref(false);
const persistentToast = ref(null); 
const sortDesc = ref(true);
const toggleSort = () => { sortDesc.value = !sortDesc.value; };
const openMenuId = ref(null);
const fileInput = ref(null);
const showAddMenu = ref(false); // 控制底部新增選單
const showSearch = ref(false);
const deleteConfirmId = ref(null); // 追蹤正在準備刪除的物件
const expandedIds = ref(new Set());
const focusedId = ref(null); // 追蹤正在「聚焦」的單筆紀錄
const batchInput = ref('');
const batchMasterId = ref(null);
const searchQuery = ref('');
const form = ref({
    id: null, master_id: null, name: '', purpose: '', remarks: '', record_date: '', obtained_date: '', status: '未求得'
});

const addActions = computed(() => [
    { 
        label: '逐筆新增', 
        description: '手動輸入每一項重大皇恩詳細資料',
        icon: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>',
        handler: () => prepareAdd('single') 
    },
    { 
        label: '多筆一次新增', 
        description: '快速解析 LINE 聊天內容紀錄',
        icon: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>',
        handler: () => prepareAdd('batch') 
    }
]);

const displayTitle = computed(() => {
    const base = '重大皇恩專區';
    if (currentFolder.value) {
        return `${base} - ${currentFolder.value.name}`;
    }
    return base;
});

const triggerBatchSave = (data) => {
    batchInput.value = data.input;
    batchMasterId.value = data.masterId;
    saveBatch();
};

// 當關閉提示框時，重設紅點狀態；加入自動消失邏輯
watch(persistentToast, (newVal) => {
    if (!newVal) {
        deleteConfirmId.value = null;
        return;
    }
    // 如果不是確認框（confirm），則 1 秒後自動消失
    if (!['confirm', 'deleteConfirm'].includes(newVal.type)) {
        setTimeout(() => {
            if (persistentToast.value === newVal) persistentToast.value = null;
        }, 1000);
    }
});

const folders = ref([
    { id: 1, name: '老祖仙師' }, { id: 2, name: '元始仙師' }, { id: 3, name: '道祖仙師' },
    { id: 4, name: '靈寶仙師' }, { id: 5, name: '父皇' }, { id: 6, name: '太宰仙師' },
    { id: 7, name: '太子' }, { id: 8, name: '閻王仙師' }, { id: 'unobtained', name: '未求得重大皇恩' }
]);

const loadData = async () => {
    loading.value = true;
    try {
        const [res, mres] = await Promise.all([ axios.get('/imperial-graces'), axios.get('/api/masters-list') ]);
        allRegistries.value = res.data.registries || [];
        masters.value = mres.data || [];
    } catch (e) { console.error(e); }
    finally { loading.value = false; }
};

const formatDate = (d) => d ? new Date(d).toLocaleDateString('zh-TW').replace(/\//g, '/') : '-';

const getMasterName = (id) => {
    const m = masters.value.find(m => m.id === id);
    return m ? m.name : '未知仙師';
};

const toggleMenu = (id) => { 
    if (openMenuId.value === id) {
        openMenuId.value = null;
    } else {
        openMenuId.value = id;
        focusedId.value = id;
        expandedIds.value.clear();
        expandedIds.value.add(id);
    }
    deleteConfirmId.value = null;
};
const toggleExpand = (id) => {
    if (expandedIds.value.has(id)) {
        expandedIds.value.delete(id);
        if (focusedId.value === id) focusedId.value = null;
        searchQuery.value = '';
        showSearch.value = false;
    } else {
        // 進入聚焦模式：展開該筆並隱藏其他
        expandedIds.value.clear(); // 清除其他展開
        expandedIds.value.add(id);
        focusedId.value = id;
    }
    openMenuId.value = null;
};

const handleBack = () => { 
    if (addMode.value) {
        addMode.value = null; 
    } else if (currentFolder.value) {
        currentFolder.value = null; 
        focusedId.value = null;
        expandedIds.value.clear();
        searchQuery.value = '';
    } else {
        emit('goHome');
    }
};

watch(currentFolder, () => {
    focusedId.value = null;
    expandedIds.value.clear();
    openMenuId.value = null;
    searchQuery.value = '';
});

const handleStatusChange = () => { if (form.value.status === '未求得') form.value.obtained_date = ''; };

const editItem = (reg) => { form.value = { ...reg }; addMode.value = 'single'; openMenuId.value = null; };

const confirmDelete = (id) => {
    deleteConfirmId.value = id;
    persistentToast.value = { msg: '確定要刪除這筆資料嗎？', type: 'deleteConfirm' };
    openMenuId.value = null;
};

const executeDelete = async () => {
    if (!deleteConfirmId.value) return;
    try { 
        await axios.delete(`/imperial-graces/registry/${deleteConfirmId.value}`); 
        persistentToast.value = { msg: '✓ 已成功刪除', type: 'success' };
        focusedId.value = null;
        expandedIds.value.clear();
        loadData(); 
    }
    catch (e) { alert('刪除失敗'); }
    deleteConfirmId.value = null;
};

// 🌟 核心下載：最穩定的同步 Blob 下載 (確保 Chrome 不攔截)
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

// 1. 單筆複製
const copyOnly = async (reg) => {
    const text = `【重大皇恩】\r\n法寶：${reg.name}\r\n數據：${reg.count || 1}\r\n用意：${reg.purpose || '無'}\r\n狀態：${reg.status}\r\n求得日期：${reg.obtained_date || '-'}\r\n由來與備註：${reg.remarks || '無'}`;
    try {
        await navigator.clipboard.writeText(text);
        persistentToast.value = { msg: '已複製資料,可至 LINE 貼上.', type: 'success' };
    } catch (e) {
        persistentToast.value = { msg: '✖ 複製失敗', type: 'error' };
    }
    openMenuId.value = null;
};

// 2. 單筆下載
const downloadOnly = (reg) => {
    const text = `【重大皇恩】\r\n法寶：${reg.name}\r\n數據：${reg.count || 1}\r\n用意：${reg.purpose || '無'}\r\n狀態：${reg.status}\r\n求得日期：${reg.obtained_date || '-'}\r\n由來與備註：${reg.remarks || '無'}`;
    triggerSimpleDownload(text, `重大皇恩_${reg.name}.txt`);
    persistentToast.value = { msg: '已啟動檔案下載.', type: 'success' };
    openMenuId.value = null;
};

// 3. 全部複製
const copyListOnly = async () => {
    if (!currentFolder.value) return;
    const contents = `【重大皇恩清單 - ${currentFolder.value.name}】\r\n\r\n` + 
        filteredRegistries.value.map(r => `${r.name}\n  數據：${r.count || 1}\n  用意：${r.purpose || '無'}\n  狀態：${r.status}`).join('\r\n\r\n');
    try {
        await navigator.clipboard.writeText(contents);
        persistentToast.value = { msg: '已複製全部清單,可至 LINE 貼上.', type: 'success' };
    } catch (e) {
        persistentToast.value = { msg: '✖ 複製失敗', type: 'error' };
    }
    openMenuId.value = null;
};

// 4. 全部下載
const downloadListOnly = () => {
    if (!currentFolder.value) return;
    const contents = `【重大皇恩清單 - ${currentFolder.value.name}】\r\n\r\n` + 
        filteredRegistries.value.map(r => `${r.name}\n  數據：${r.count || 1}\n  用意：${r.purpose || '無'}\n  狀態：${r.status}`).join('\r\n\r\n');
    triggerSimpleDownload(contents, `重大皇恩全部清單_${currentFolder.value.name}.txt`);
    persistentToast.value = { msg: '已啟動全部清單下載.', type: 'success' };
    openMenuId.value = null;
};

const prepareAdd = (mode) => {
    const defaultMasterId = currentFolder.value && currentFolder.value.id !== 'unobtained' ? currentFolder.value.id : null;
    form.value = { 
        id: null, 
        master_id: defaultMasterId, 
        name: '', 
        purpose: '', 
        remarks: '', 
        record_date: new Date().toISOString().split('T')[0], 
        obtained_date: '', 
        status: '未求得' 
    };
    if (mode === 'batch') {
        batchInput.value = '';
        batchMasterId.value = defaultMasterId;
    }
    addMode.value = mode;
    showAddMenu.value = false;
};

const saveSingle = async (resolutionOrData = null) => {
    if (isSaving.value) return;

    let resolution = null;
    if (typeof resolutionOrData === 'string') {
        resolution = resolutionOrData;
    } else if (resolutionOrData && typeof resolutionOrData === 'object' && !resolutionOrData.target) {
        // Sync local form with data from child component
        form.value = { ...resolutionOrData };
    }
    let formMid = form.value.master_id ? String(form.value.master_id) : '';
    const folderId = currentFolder.value ? String(currentFolder.value.id) : '';
    const isActuallyMismatched = folderId !== '' && formMid !== folderId;

    // Auto-shunt without prompt
    if (isActuallyMismatched) {
        resolution = 'shunt';
    }

    // 處理「改回原本仙師」的邏輯
    if (resolution === 'correct') {
        const fid = currentFolder.value?.id;
        form.value.master_id = (fid === 'unobtained') ? null : fid;
        formMid = form.value.master_id ? String(form.value.master_id) : '';
    }

    if (!form.value.name) {
        persistentToast.value = { msg: '錯誤：請輸入法寶名稱', type: 'error' };
        return;
    }

    // 重複檢查 (全模組下法寶名稱不可重複)
    const targetName = form.value.name.trim().toLowerCase();
    const isDuplicate = allRegistries.value.some(r => {
        const match = r.id !== form.value.id && r.name.trim().toLowerCase() === targetName;
        return match;
    });

    if (isDuplicate) {
        console.warn(`Duplicate detected: ${targetName}`);
        persistentToast.value = { msg: `✖ 法寶名稱「${form.value.name}」在重大皇恩中已存在，請勿重複存檔。`, type: 'error' };
        // Scroll list to the duplicate item if possible (UX enrichment)
        const dupItem = allRegistries.value.find(r => r.name.trim().toLowerCase() === targetName);
        if (dupItem) {
            focusedId.value = dupItem.id;
            expandedIds.value.clear();
            expandedIds.value.add(dupItem.id);
        }
        return;
    }
    if (['已登記', '已求得'].includes(form.value.status) && !form.value.obtained_date) {
        persistentToast.value = { msg: '錯誤：選取已登記/已求得時，請輸入求得日期', type: 'error' };
        return;
    }
    
    isSaving.value = true;
    persistentToast.value = null; // 點擊按鈕後立即清除警告框

    try {
        // 先抓到目標仙師的名字，為了後續精準導向
        const finalMid = form.value.master_id;
        const targetMaster = masters.value.find(m => String(m.id) === String(finalMid));
        const targetMasterName = targetMaster ? targetMaster.name : '';

        if (form.value.id) {
            await axios.post(`/imperial-graces/registry/${form.value.id}`, { ...form.value, _method: 'PATCH' });
            persistentToast.value = { msg: '✓ 修改成功', type: 'success' };
        } else {
            await axios.post('/imperial-graces/registry', { ...form.value, master_id: finalMid });
            persistentToast.value = { msg: '✓ 新增成功', type: 'success' };
        }
        
        loadData();

        // 智慧導向：如果選擇分流，依照「名字」匹配資料夾，解決 ID 不一致的問題
        if (resolution === 'shunt' && targetMasterName) {
            const matchedFolder = folders.value.find(f => f.name === targetMasterName);
            if (matchedFolder) {
                currentFolder.value = matchedFolder;
                persistentToast.value = { msg: `✓ 已分流轉跳至 ${matchedFolder.name}`, type: 'success' };
            }
        }
        addMode.value = null; 

    } catch (e) { 
        console.error('儲存失誤:', e);
        if (e.response && e.response.status === 422) {
            const errors = e.response.data.errors;
            const messages = Object.values(errors).flat().join('\n');
            alert('驗證失敗：\n' + messages);
        } else {
            alert('儲存失敗：' + (e.response?.data?.message || '資料格式錯誤')); 
        }
    }
    finally { isSaving.value = false; }
};

const handleFileUpload = (event) => {
    const file = event.target.files[0];
    if (!file) return;
    
    const reader = new FileReader();
    reader.onload = (e) => {
        batchInput.value = e.target.result;
        persistentToast.value = { msg: '✓ 檔案內容已載入', type: 'success' };
    };
    reader.readAsText(file);
    // 重設 input 以便下次選擇同一個檔案也能觸發
    event.target.value = '';
};

const quickToggleStatus = async (reg) => {
    const statuses = ['未求得', '已登記', '已求得'];
    const currentIdx = statuses.indexOf(reg.status);
    const nextIdx = (currentIdx + 1) % statuses.length;
    const nextStatus = statuses[nextIdx];
    await changeStatus(reg, nextStatus);
};

const statusMenuOpenId = ref(null);
const openStatusMenu = (id) => {
    statusMenuOpenId.value = statusMenuOpenId.value === id ? null : id;
};

const changeStatus = async (reg, nextStatus) => {
    statusMenuOpenId.value = null;
    try {
        const payload = { 
            name: reg.name,
            master_id: reg.master_id,
            record_date: reg.record_date,
            obtained_date: reg.obtained_date,
            purpose: reg.purpose,
            remarks: reg.remarks,
            status: nextStatus 
        };
        // Auto-fix date if moving to Obtained or Registered (both require obtained_date in backend)
        if (['已求得', '已登記'].includes(nextStatus) && !payload.obtained_date) {
            payload.obtained_date = new Date().toISOString().split('T')[0];
        }
        await axios.put(`/imperial-graces/registry/${reg.id}`, payload);
        loadData();
    } catch (e) {
        console.error('狀態變更失敗:', e);
        const serverMsg = e.response?.data?.message || '資料驗證失敗';
        persistentToast.value = { msg: `✖ 狀態變更失敗：${serverMsg}`, type: 'error' };
    }
};

const saveBatch = async (payload = null) => {
    // Sync data if coming from the component event
    if (payload && typeof payload === 'object') {
        batchInput.value = payload.input || '';
        batchMasterId.value = payload.masterId || null;
    }

    if (!batchInput.value || !batchMasterId.value || isSaving.value) return;
    
    // Check for duplicates in pre-processed rows
    if (payload && payload.rows && payload.rows.length > 0) {
        const mid = String(batchMasterId.value);
        const existingNames = allRegistries.value.map(r => r.name.trim());
        
        const duplicates = payload.rows
            .map(row => String(row.c0 || '').trim())
            .filter(name => name && existingNames.includes(name));

        if (duplicates.length > 0) {
            persistentToast.value = { 
                msg: `注意：偵測到 ${duplicates.length} 筆重複名稱（如 ${duplicates[0]}...）`, 
                type: 'error' 
            };
            return;
        }
    }

    isSaving.value = true;
    try {
        const finalMasterId = batchMasterId.value === 'unobtained' ? null : batchMasterId.value;

        // If payload has pre-processed rows, send them directly as 'items'
        const dataToSend = {
            master_id: finalMasterId
        };
        
        if (payload && payload.rows) {
            dataToSend.items = payload.rows;
        } else {
            const lines = batchInput.value.split('\n')
                .map(l => l.trim())
                .filter(l => l && !l.startsWith('【')); 
            if (lines.length === 0) {
                persistentToast.value = { msg: '✖ 內容格式不正確', type: 'error' };
                isSaving.value = false;
                return;
            }
            dataToSend.lines = lines;
        }

        await axios.post('/imperial-graces/registry/batch', dataToSend);
        
        persistentToast.value = { msg: '✓ 多筆新增成功', type: 'success' };
        addMode.value = null;
        loadData();
    } catch (e) { 
        console.error('批次失敗:', e);
        const serverMsg = e.response?.data?.message || e.message || '格式解析錯誤或伺服器連線失敗';
        persistentToast.value = { msg: `✖ 批次新增失敗：${serverMsg}`, type: 'error' };
    } finally {
        isSaving.value = false;
    }
};

onMounted(() => {
    loadData();
    window.addEventListener('click', (e) => { 
        const isOutside = !e.target.closest('.relative');
        if (isOutside) {
            if (openMenuId.value) openMenuId.value = null;
            if (showAddMenu.value) showAddMenu.value = false;
            if (statusMenuOpenId.value) statusMenuOpenId.value = null;
            // Collapse details but STAY in focused mode
            if (expandedIds.value.size > 0) expandedIds.value.clear();
        }
    });
});

const filteredRegistries = computed(() => {
    if (!currentFolder.value) return [];
    
    let filtered = [];
    if (currentFolder.value.id === 'unobtained') {
        filtered = allRegistries.value.filter(r => !r.master_id || r.status === '未求得');
    } else {
        filtered = allRegistries.value.filter(r => r.master_id === currentFolder.value.id);
    }


    // Sort mapping based on user priority: Unobtained -> Obtained -> Registered
    const statusOrder = { '未求得': 1, '已求得': 2, '已登記': 3 };
    
    filtered.sort((a, b) => {
        // Priority 1: Status (Always kept)
        const orderA = statusOrder[a.status] || 99;
        const orderB = statusOrder[b.status] || 99;
        if (orderA !== orderB) return orderA - orderB;
        
        // Priority 2: Date
        const dateA = a.record_date || '';
        const dateB = b.record_date || '';
        if (dateA !== dateB) {
            return sortDesc.value ? dateB.localeCompare(dateA) : dateA.localeCompare(dateB);
        }
        
        // Priority 3: ID Fallback
        return sortDesc.value ? ((b.id || 0) - (a.id || 0)) : ((a.id || 0) - (b.id || 0));
    });

    if (searchQuery.value.trim()) {
        const query = searchQuery.value.trim().toLowerCase();
        filtered = filtered.filter(r => {
            const mName = getMasterName(r.master_id).toLowerCase();
            return r.name.toLowerCase().includes(query) ||
                   (r.purpose || '').toLowerCase().includes(query) ||
                   (r.remarks || '').toLowerCase().includes(query) ||
                   mName.includes(query);
        });
        
        // Matches always move to top regardless of global sort
        filtered.sort((a, b) => {
            const am = a.name.toLowerCase().includes(query) ? 1 : 0;
            const bm = b.name.toLowerCase().includes(query) ? 1 : 0;
            return bm - am;
        });
    }

    return filtered;
});

const folderTotalCount = computed(() => {
    return filteredRegistries.value.reduce((sum, r) => sum + (Number(r.count) || 1), 0);
});

const globalTotalCount = computed(() => {
    return allRegistries.value.reduce((sum, r) => sum + (Number(r.count) || 1), 0);
});

const getFolderSum = (id) => {
    let filtered = [];
    if (id === 'unobtained') {
        filtered = allRegistries.value.filter(r => !r.master_id || r.status === '未求得');
    } else {
        filtered = allRegistries.value.filter(r => String(r.master_id) === String(id));
    }
    return filtered.reduce((sum, r) => sum + (Number(r.count) || 1), 0);
};

onMounted(() => {
    window.addEventListener('click', (e) => {
        const isOutside = !e.target.closest('.relative');
        if (isOutside) {
            if (openMenuId.value) openMenuId.value = null;
            if (showAddMenu.value) showAddMenu.value = false;
            // Collapse details but STAY in focused mode
            if (expandedIds.value.size > 0) expandedIds.value.clear();
        }
    });
});
</script>

<style scoped>
.animate-fade-in { animation: fadeIn 0.3s ease-out; }
.animate-slide-up { animation: slideUp 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
@keyframes fadeIn { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
@keyframes slideUp { from { opacity: 0; transform: translate(-50%, 20px); } to { opacity: 1; transform: translate(-50%, 0); } }

/* Custom Scrollbar for a cleaner mobile look */
.custom-scrollbar::-webkit-scrollbar { width: 6px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
</style>
