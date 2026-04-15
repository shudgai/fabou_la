<template>
    <div class="bg-white h-[100dvh] flex flex-col relative overflow-hidden text-slate-900">
        <!-- Header -->
        <div class="border-b border-gray-100 flex items-center justify-center bg-white/80 backdrop-blur-md sticky top-0 z-10" style="padding: 12px 10px 10px 10px;">
            <h2 class="text-xl font-bold font-outfit tracking-tight text-slate-800">
                <template v-if="currentFolder">
                    <span v-if="currentFolder.id === 'unobtained'" class="text-red-500">未求得重大皇恩</span>
                    <span v-else>{{ currentFolder.name }}</span>
                </template>
                <span v-else>重大皇恩專區</span>
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
        <div class="flex-1 overflow-y-auto custom-scrollbar" style="padding-bottom: 60px;">
        <!-- Level 1: Folder Selection -->
        <div v-if="!currentFolder" class="grid grid-cols-2 gap-3 p-3" style="margin-top: 5px;">
            <button v-for="(folder, idx) in folders" :key="folder.id" 
                @click="currentFolder = folder"
                class="flex flex-col items-center justify-center rounded-3xl transition-all active:scale-95 shadow-sm border border-slate-100 group relative overflow-hidden"
                :class="[
                    folder.id === 'unobtained' ? 'col-span-2 bg-red-50/50 hover:bg-red-50 py-6' : 
                    idx % 4 === 0 ? 'bg-indigo-50/50 hover:bg-indigo-50 aspect-square' : 
                    idx % 4 === 1 ? 'bg-rose-50/50 hover:bg-rose-50 aspect-square' : 
                    idx % 4 === 2 ? 'bg-emerald-50/50 hover:bg-emerald-50 aspect-square' : 'bg-amber-50/50 hover:bg-amber-50 aspect-square'
                ]"
                style="padding: 15px;">
                <div class="rounded-2xl bg-white shadow-sm flex items-center justify-center mb-3 group-hover:shadow-md transition-shadow"
                    :class="folder.id === 'unobtained' ? 'w-14 h-14' : 'w-12 h-12'">
                    <svg class="w-6 h-6" :class="[
                        folder.id === 'unobtained' ? 'text-red-500' :
                        idx % 4 === 0 ? 'text-indigo-500' : 
                        idx % 4 === 1 ? 'text-rose-500' : 
                        idx % 4 === 2 ? 'text-emerald-500' : 'text-amber-500'
                    ]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path v-if="folder.id === 'unobtained'" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        <path v-else d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </div>
                <span class="text-sm font-bold text-slate-700">
                    {{ folder.name }}
                </span>
            </button>
        </div>

        <!-- Level 2: Folder Contents -->
        <div v-else class="px-[5px] md:px-0">

            <!-- List Display -->
            <div style="padding: 3px;">
                <div v-if="!loading && allRegistries.length > 0 && !addMode" class="mb-3 px-1">
                    <div v-if="searchQuery" class="bg-indigo-50 rounded-2xl p-3 flex justify-between items-center animate-fade-in shadow-sm border border-indigo-100/50">
                        <span class="text-xs font-bold text-indigo-600">搜尋結果：{{ searchQuery }}</span>
                        <button @click="searchQuery = ''" class="text-indigo-400 hover:text-indigo-600">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M10 8.586L2.929 1.515 1.515 2.929 8.586 10l-7.071 7.071 1.414 1.414L10 11.414l7.071 7.071 1.414-1.414L11.414 10l7.071-7.071-1.414-1.414L10 8.586z"/></svg>
                        </button>
                    </div>
                </div>

                <div v-if="loading" class="text-center py-4">載入中...</div>
                <div v-else-if="filteredRegistries.length === 0" class="text-center py-12 text-slate-400">
                    目前該仙師尚無登記資料。
                </div>
                <div v-else class="flex flex-col">
                    <div v-for="reg in (focusedId ? filteredRegistries.filter(r => r.id === focusedId) : filteredRegistries)" :key="reg.id" 
                        @click="toggleExpand(reg.id)"
                        class="border-b border-dashed border-slate-200 py-[6px] last:border-b-0 relative group active:bg-slate-50 transition-colors cursor-pointer">
                        <!-- Row 1 -->
                        <div class="flex items-start justify-between mb-0">
                            <div class="flex items-center space-x-2 pt-1 text-[11px] text-slate-400 w-fit">
                                <span v-if="currentFolder.id === 'unobtained' && reg.master_id" class="text-indigo-600">
                                    {{ getMasterName(reg.master_id) }}
                                </span>
                                <span class="px-[2.5px]">得知: {{ formatDate(reg.record_date) }}</span>
                                <span class="bg-slate-50 px-[2.5px] rounded text-slate-800">
                                    求得: {{ reg.obtained_date ? formatDate(reg.obtained_date) : '----' }}
                                </span>
                            </div>
                            <!-- Action Menu Button -->
                            <div class="relative" :class="[deleteConfirmId === reg.id ? 'text-red-500' : 'text-slate-400']">
                                <button @click.stop="toggleMenu(reg.id)" class="p-1 -mr-1">
                                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM18 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                                </button>
                                <!-- MENU DROPDOWN -->
                                <div v-if="openMenuId === reg.id" @click.stop 
                                    class="absolute right-0 top-full mt-1 w-28 bg-white rounded-xl shadow-2xl border border-slate-100 z-[100] overflow-hidden animate-slide-up">
                                    <button @click="toggleExpand(reg.id)" class="w-full px-2 py-2 text-left text-[11px] text-slate-600 hover:bg-slate-50 border-b border-slate-50 whitespace-nowrap">{{ expandedIds.has(reg.id) ? '縮起清單' : '展開清單' }}</button>
                                    <button @click.stop="editItem(reg)" class="w-full px-2 py-2 text-left text-[11px] text-slate-600 hover:bg-slate-50 border-b border-slate-50">修改</button>
                                    <button @click.stop="copyOnly(reg)" class="w-full px-2 py-2 text-left text-[11px] text-green-600 hover:bg-green-50 border-b border-slate-50 font-bold whitespace-nowrap">單筆貼 LINE</button>
                                    <button @click.stop="downloadOnly(reg)" class="w-full px-2 py-2 text-left text-[11px] text-blue-600 hover:bg-blue-50 border-b border-slate-50 font-bold whitespace-nowrap">單筆檔案下載</button>
                                    <button @click.stop="copyListOnly" class="w-full px-2 py-2 text-left text-[11px] text-green-600 hover:bg-green-50 border-b border-slate-50 font-bold whitespace-nowrap">全部貼 LINE</button>
                                    <button @click.stop="downloadListOnly" class="w-full px-2 py-2 text-left text-[11px] text-blue-600 hover:bg-blue-50 border-b border-slate-50 font-bold whitespace-nowrap">全部檔案下載</button>
                                    <button @click.stop="confirmDelete(reg.id)" class="w-full px-2 py-2 text-left text-[11px] text-red-600 hover:bg-red-50">刪除</button>
                                </div>
                            </div>
                        </div>
                <div class="mt-0 flex items-center justify-between">
                    <div class="text-[17px] font-normal text-slate-900 leading-tight">
                        {{ reg.name }}
                    </div>
                    <span :class="{
                        'bg-emerald-50 text-emerald-600 border-emerald-100': reg.status === '已求得',
                        'bg-blue-50 text-blue-600 border-blue-100': reg.status === '已登記',
                        'bg-slate-50 text-slate-400 border-slate-100': reg.status === '未求得'
                    }" class="text-[10px] px-[2.5px] py-0.5 rounded border font-normal">
                        {{ reg.status }}
                    </span>
                </div>
                <!-- Extra Rows -->
                <div v-if="expandedIds.has(reg.id)" class="animate-fade-in text-sm text-slate-900">
                    <div><span class="text-base text-slate-500">用意：</span>{{ reg.purpose || '無' }}</div>
                    <div v-if="reg.remarks"><span class="text-base text-slate-500">備註：</span>{{ reg.remarks }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

    </div> <!-- End Scrollable Area -->
    
        <div class="fixed bottom-0 left-0 right-0 bg-white/95 backdrop-blur-md border-t border-slate-100 z-50 shadow-[0_-4px_10px_rgba(0,0,0,0.03)]" style="height: 60px;">
            <div class="grid grid-cols-5 h-full items-center px-2">
                <!-- BACK BUTTON -->
                <div class="flex justify-center">
                    <button @click="handleBack" 
                        class="w-10 h-10 rounded-2xl flex items-center justify-center transition-all active:scale-90 text-slate-400 hover:bg-slate-50">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" /></svg>
                    </button>
                </div>

                <!-- HOME BUTTON -->
                <div class="flex justify-center">
                    <button @click="$emit('goHome')" class="w-10 h-10 rounded-2xl flex items-center justify-center transition-all active:scale-95 text-slate-400 hover:bg-slate-50">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                </div>

                <!-- ADD BUTTON (Center) -->
                <div class="flex justify-center relative">
                    <div v-if="currentFolder?.id !== 'unobtained'" class="absolute -top-6">
                        <button @click="showAddMenu = !showAddMenu" 
                            :class="[showAddMenu ? 'bg-slate-800 rotate-45 scale-90' : 'bg-indigo-600 text-white shadow-lg shadow-indigo-200 active:scale-95']"
                            class="w-14 h-14 rounded-3xl flex items-center justify-center transition-all duration-500 border-4 border-white">
                            <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </button>
                    </div>
                </div>

                <!-- SEARCH BUTTON -->
                <div class="flex justify-center">
                    <button @click="showSearch = !showSearch" 
                        :class="showSearch ? 'text-indigo-600 bg-indigo-50' : 'text-slate-400'"
                        class="w-10 h-10 rounded-2xl flex items-center justify-center transition-all active:scale-95 hover:bg-slate-50">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" /></svg>
                    </button>
                </div>

                <!-- DOWNLOAD BUTTON -->
                <div class="flex justify-center">
                    <button @click="downloadListOnly" :disabled="!currentFolder"
                        class="w-10 h-10 rounded-2xl flex items-center justify-center transition-all active:scale-95 text-slate-400 hover:bg-slate-50 disabled:opacity-30">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                </div>
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
    const text = `【重大皇恩】\r\n法寶：${reg.name}\r\n用意：${reg.purpose || '無'}\r\n狀態：${reg.status}\r\n求得日期：${reg.obtained_date || '-'}\r\n由來與備註：${reg.remarks || '無'}`;
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
    const text = `【重大皇恩】\r\n法寶：${reg.name}\r\n用意：${reg.purpose || '無'}\r\n狀態：${reg.status}\r\n求得日期：${reg.obtained_date || '-'}\r\n由來與備註：${reg.remarks || '無'}`;
    triggerSimpleDownload(text, `重大皇恩_${reg.name}.txt`);
    persistentToast.value = { msg: '已啟動檔案下載.', type: 'success' };
    openMenuId.value = null;
};

// 3. 全部複製
const copyListOnly = async () => {
    if (!currentFolder.value) return;
    const contents = `【重大皇恩清單 - ${currentFolder.value.name}】\r\n\r\n` + 
        filteredRegistries.value.map(r => `${r.name}\n  用意：${r.purpose || '無'}\n  狀態：${r.status}`).join('\r\n\r\n');
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
        filteredRegistries.value.map(r => `${r.name}\n  用意：${r.purpose || '無'}\n  狀態：${r.status}`).join('\r\n\r\n');
    triggerSimpleDownload(contents, `重大皇恩全部清單_${currentFolder.value.name}.txt`);
    persistentToast.value = { msg: '已啟動全部清單下載.', type: 'success' };
    openMenuId.value = null;
};

const prepareAdd = (mode) => {
    form.value = { id: null, master_id: currentFolder.value?.id || null, name: '', purpose: '', remarks: '', record_date: new Date().toISOString().split('T')[0], obtained_date: '', status: '未求得' };
    if (mode === 'batch') {
        batchInput.value = '';
        batchMasterId.value = currentFolder.value?.id || null;
    }
    addMode.value = mode;
};

const saveSingle = async (resolution = null) => {
    if (isSaving.value) return;
    
    // 💥 修正：將 const 改為 let，否則後面重新賦值會導致 JS 崩潰
    let formMid = form.value.master_id ? String(form.value.master_id) : '';
    const folderId = currentFolder.value ? String(currentFolder.value.id) : '';
    const isActuallyMismatched = folderId !== '' && formMid !== folderId;

    // 攔截詢問 (排除點擊事件物件)
    if (isActuallyMismatched && resolution !== 'shunt') {
        persistentToast.value = { 
            msg: `您已求到此重大皇恩，此重大皇恩將分流至該位仙師之資料夾。`, 
            type: 'confirm' 
        };
        return; 
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
        alert('儲存失敗：' + (e.response?.data?.message || '資料格式錯誤')); 
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

const saveBatch = async () => {
    if (!batchInput.value || !batchMasterId.value || isSaving.value) return;
    
    isSaving.value = true;
    try {
        const lines = batchInput.value.split('\n')
            .map(l => l.trim())
            .filter(l => l && !l.startsWith('【')); // 排除標題行就好，其餘交給後端解析
            
        if (lines.length === 0) {
            persistentToast.value = { msg: '✖ 內容格式不正確', type: 'error' };
            isSaving.value = false;
            return;
        }

        const finalMasterId = batchMasterId.value === 'unobtained' ? null : batchMasterId.value;

        await axios.post('/imperial-graces/registry/batch', { 
            lines: lines, 
            master_id: finalMasterId 
        });
        
        persistentToast.value = { msg: '✓ 多筆新增成功', type: 'success' };
        addMode.value = null;
        loadData();
    } catch (e) { 
        console.error('批次失敗:', e);
        persistentToast.value = { msg: '✖ 批次新增失敗', type: 'error' };
    } finally {
        isSaving.value = false;
    }
};

onMounted(() => {
    loadData();
    window.addEventListener('click', (e) => { 
        if (openMenuId.value && !e.target.closest('.relative')) openMenuId.value = null; 
        if (showAddMenu.value && !e.target.closest('.relative')) showAddMenu.value = false;
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

    // Sort: Master order (1-8) then Record Date (DESC)
    const masterOrder = [1, 2, 3, 4, 5, 6, 7, 8];
    filtered.sort((a, b) => {
        // Master order
        const aIdx = a.master_id ? masterOrder.indexOf(Number(a.master_id)) : 999;
        const bIdx = b.master_id ? masterOrder.indexOf(Number(b.master_id)) : 999;
        
        if (aIdx !== bIdx) return aIdx - bIdx;
        
        // Date order (Desc: latest first)
        const aDate = a.record_date || '0000-00-00';
        const bDate = b.record_date || '0000-00-00';
        return bDate.localeCompare(aDate);
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
        
        // Results move to top
        filtered.sort((a, b) => {
            const am = a.name.toLowerCase().includes(query) ? 1 : 0;
            const bm = b.name.toLowerCase().includes(query) ? 1 : 0;
            return bm - am;
        });
    }

    return filtered;
});
</script>

<style scoped>
.animate-fade-in { animation: fadeIn 0.3s ease-out; }
.animate-slide-up { animation: slideUp 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
@keyframes fadeIn { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
@keyframes slideUp { from { opacity: 0; transform: translate(-50%, 20px); } to { opacity: 1; transform: translate(-50%, 0); } }

/* Custom Scrollbar for a cleaner mobile look */
.custom-scrollbar::-webkit-scrollbar {
    width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #e2e8f0;
    border-radius: 10px;
}
</style>
<!-- v-refresh-download-fix-2026041422 -->
