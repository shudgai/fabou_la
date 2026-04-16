<template>
    <div class="bg-white h-[100dvh] flex flex-col relative overflow-hidden">
        <!-- Static Header -->
        <div class="border-b border-gray-100 flex items-center bg-white sticky top-0 z-30 px-4 h-[50px]">
            <!-- Left: Sort Toggle -->
            <div class="w-[70px] flex items-center shrink-0">
                <button @click="sortDesc = !sortDesc" class="text-[11px] text-indigo-500 font-bold bg-indigo-50 px-1.5 py-0.5 rounded-lg active:scale-95 transition-all opacity-90 tracking-tighter">
                    {{ sortDesc ? '新→舊' : '舊→新' }}
                </button>
            </div>

            <!-- Center: Title -->
            <div class="flex-1 flex justify-center items-center min-w-0">
                <h2 class="text-[19px] font-bold font-outfit tracking-tight text-slate-800 truncate">
                    {{ displayTitle }}
                </h2>
            </div>

            <!-- Right: Total Info -->
            <div class="w-[70px] flex items-center justify-end shrink-0">
                <button @click="toggleShowTotal" class="text-[15px] text-slate-900 font-bold active:scale-95 transition-all">
                    {{ showTotal ? '總量: ' + totalGrudgeQuantity : '總量' }}
                </button>
            </div>
        </div>
        
        <!-- Search Component -->
        <div v-if="showSearch" class="px-[10px] mt-2 animate-fade-in">
            <input v-model="searchQuery" type="text" placeholder="搜尋項目..." 
                class="w-full h-[36px] bg-white border border-slate-200 rounded-xl px-4 text-sm focus:ring-0 outline-none">
        </div>

        <!-- Table Layout List -->
        <div class="flex-1 overflow-x-auto overflow-y-auto pl-[5px] bg-white flex flex-col" :class="[focusedId ? 'h-full pb-0' : 'pb-24']">
            <div v-if="loading" class="text-center py-10 text-xs text-slate-400">載入中...</div>
            <div v-else-if="filteredItems.length === 0" class="text-center py-20 text-slate-400 font-light">
                目前尚無怨靈載錄資料。
            </div>
            <div v-else class="flex flex-col flex-1">
                <!-- Table Header -->
                <div class="flex bg-white border-y-2 border-slate-200 text-[16px] text-slate-600 sticky top-0 z-10 mt-[-24px] h-[24px]">
                    <div class="flex-1 text-center shrink-0 flex items-center justify-center relative -left-[20px]">法號</div>
                    <div class="w-[70px] relative -left-[5px] px-1 text-center whitespace-nowrap shrink-0 flex items-center justify-center tracking-tighter text-[16px]">得知日期</div>
                    <div class="w-[80px] relative left-[10px] px-1 text-center whitespace-nowrap shrink-0 flex items-center justify-center text-[16px]">數量</div>
                    <div class="w-[90px] relative left-[19px] px-[5px] text-center flex items-center justify-center tracking-tighter text-[16px]">處理狀況</div>
                    <div class="w-[30px] shrink-0"></div>
                </div>

                <!-- Table Body -->
                <div class="pt-[24px]">
                    <div v-for="(item, index) in (focusedId ? sortedItems.filter(i => i.id === focusedId) : sortedItems)" :key="item.id" 
                        @click="focusedId === item.id ? null : toggleExpand(item.id)"
                        class="flex flex-col border-b border-slate-200 last:border-b-0 bg-white overflow-visible transition-all duration-300"
                        :class="[focusedId ? 'flex-1' : '']"
                    >
                        <div class="flex items-stretch h-[32px] w-full" :class="[focusedId === item.id ? 'bg-indigo-50/30' : '']">
                    
                        <!-- 法號 -->
                        <div class="flex-1 pl-[5px] border-r border-slate-100 flex items-center shrink-0 overflow-hidden">
                            <div class="truncate text-[16px] text-slate-900 leading-none">
                                {{ item.user_name || '-' }}<span v-if="item.user_remarks" class="font-normal text-slate-400 text-[16px]">{{ item.user_remarks }}</span>
                            </div>
                        </div>

                        <!-- 得知日期 -->
                        <div class="w-[70px] relative -left-[5px] px-1 border-r border-slate-100 text-center whitespace-nowrap shrink-0 flex items-center justify-center font-mono text-slate-400 tracking-tighter text-[16px]" style="padding-top: 5px; padding-bottom: 5px;">
                            {{ item.know_date ? formatDate(item.know_date) : 'yyyy/mm/dd' }}
                        </div>

                        <!-- 數量 -->
                        <div class="w-[80px] relative left-[10px] px-1 border-r border-slate-100 text-center text-slate-900 shrink-0 flex items-center justify-center font-mono text-[16px]" style="padding-top: 5px; padding-bottom: 5px;">
                            {{ item.quantity }}
                        </div>

                        <!-- 處理狀況 -->
                        <div class="w-[90px] relative left-[19px] px-[5px] text-center relative group/status">
                            <div @click.stop="openStatusId = openStatusId === item.id ? null : item.id" 
                                class="absolute inset-0 flex items-center justify-center cursor-pointer active:bg-slate-100/50 transition-all z-10 space-x-0.5">
                                <div class="leading-none font-mono tracking-tighter text-[16px]" :class="[item.destination === '未處理' ? 'text-slate-300' : 'text-slate-400']">
                                    {{ item.process_date ? formatDate(item.process_date) : (item.destination === '未處理' ? '未處理' : formatDate(item.know_date)) }}
                                </div>
                                <svg class="w-2.5 h-2.5 opacity-20" fill="currentColor" viewBox="0 0 20 20"><path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" /></svg>
                            </div>

                            <!-- Dropdown -->
                            <div v-if="openStatusId === item.id" 
                                 class="absolute top-full left-1/2 -translate-x-1/2 mt-1 w-[120px] bg-white rounded-lg border border-slate-200 z-[110] p-[5px] space-y-1 animate-slide-up text-left overflow-visible">
                                <button @click.stop="openStatusId = null" class="absolute top-1 right-1 text-slate-300 hover:text-slate-400 p-1"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg></button>
                                <div class="space-y-1 pt-1">
                                    <label class="text-[14px] font-bold text-slate-400 uppercase tracking-wider block">處理結果</label>
                                    <div class="space-y-1">
                                        <div v-for="destPart in (item.destination || '未處理').split(/[、，,]/)" :key="destPart" class="text-[14px] font-bold leading-none border-l-2 pl-1.5 whitespace-nowrap overflow-hidden" :class="[destPart.includes('九天') ? 'text-red-600 border-red-200' : destPart.includes('耀紫軍') ? 'text-purple-600 border-purple-200' : 'text-slate-900 border-slate-200']">
                                            {{ destPart.replace(/\(|\)/g, ' ') }}
                                        </div>
                                    </div>
                                    <div v-if="item.remarks && (item.remarks.yan_zun || item.remarks.yan_an || item.remarks.long_sheng || item.remarks.long_zhan)" class="space-y-0.5 mt-2">
                                        <div v-if="item.remarks.yan_zun || item.remarks.yan_an" class="text-[14px] text-slate-500 font-bold bg-slate-50 px-1.5 py-1 rounded flex justify-between">
                                            <span>閻尊 {{ item.remarks.yan_zun || 0 }}</span><span>閻闇 {{ item.remarks.yan_an || 0 }}</span>
                                        </div>
                                        <div v-if="item.remarks.long_sheng || item.remarks.long_zhan" class="text-[14px] text-purple-600 font-bold bg-purple-50 px-1.5 py-1 rounded flex justify-between">
                                            <span>龍勝 {{ item.remarks.long_sheng || 0 }}</span><span>龍戰 {{ item.remarks.long_zhan || 0 }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                            <!-- Menu (Layer 1 end) -->
                            <div class="w-[30px] shrink-0 flex items-center justify-center z-[30] relative overflow-visible">
                                <button @click.stop="toggleMenu(item.id)" :class="[deleteConfirmId === item.id ? 'text-red-500' : 'text-slate-400']" class="p-1 active:text-indigo-600 rounded-full transition-all">
                                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20"><path d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM18 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                                </button>
                                <div v-if="openMenuId === item.id" @click.stop class="absolute right-0 top-full mt-1 w-[100px] bg-white bg-opacity-100 rounded-lg border border-slate-200 z-[100] overflow-hidden animate-slide-up p-1 flex flex-col" style="gap: 2px;">
                                    <button @click.stop="toggleExpand(item.id); openMenuId = null" class="w-full px-2 text-left text-indigo-600 active:bg-indigo-50 font-bold rounded py-1 text-[14px] whitespace-nowrap">
                                        {{ focusedId === item.id ? '展開清單' : '縮起清單' }}
                                    </button>
                                    <button @click.stop="editItem(item)" class="w-full px-2 text-left text-slate-700 active:bg-slate-50 rounded py-1 text-[14px] whitespace-nowrap">修改內容</button>
                                    <button @click.stop="copyItem(item)" class="w-full px-2 text-left text-green-600 active:bg-green-50 font-bold rounded py-1 text-[14px] whitespace-nowrap">複製貼 LINE</button>
                                    <button @click.stop="downloadItem(item, 'txt')" class="w-full px-2 text-left text-blue-600 active:bg-blue-50 rounded py-1 text-[14px] whitespace-nowrap">檔案下載</button>
                                    <button @click.stop="downloadList('txt'); openMenuId = null" class="w-full px-2 text-left text-emerald-600 active:bg-emerald-50 font-bold rounded py-1 text-[14px] whitespace-nowrap">下載清單</button>
                                    <button @click.stop="deleteItem(item.id)" class="w-full px-2 text-left text-red-600 active:bg-red-50 rounded py-1 text-[14px] whitespace-nowrap">刪除</button>
                                </div>
                            </div>
                        </div>

                        <!-- Layer 2: Focus Detail Area (Fills space) -->
                        <div v-if="focusedId === item.id" 
                            @click.self="focusedId = null"
                            class="flex-1 p-3 pt-4 border-t border-slate-100 space-y-4 bg-white animate-fade-in cursor-default">
                            <div v-if="item.process_date" class="space-y-1 pointer-events-none">
                                <label class="text-[11px] font-bold text-slate-400 uppercase block tracking-wider">處理日期</label>
                                <p class="text-[13px] font-mono text-slate-700 mt-0.5">{{ formatDate(item.process_date) }}</p>
                            </div>
                            <div v-if="item.remarks_text" class="space-y-1">
                                <label class="text-[11px] font-bold text-slate-400 uppercase block tracking-wider">備註</label>
                                <p class="text-[13px] text-slate-500 leading-relaxed">{{ item.remarks_text }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- FAB Bottom Navigation -->
        <div class="fixed bottom-0 left-0 right-0 bg-white border-t border-slate-100 z-50 shadow-[0_-4px_10px_rgba(0,0,0,0.03)]" style="height: 6.5vh;">
            <div class="grid grid-cols-5 h-full items-center px-2">
                <div class="flex justify-center">
                    <button @click="handleBack" class="w-7 h-7 rounded-xl flex items-center justify-center transition-all active:scale-90 text-slate-400 hover:bg-slate-50">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" /></svg>
                    </button>
                </div>
                <div class="flex justify-center">
                    <button @click="$emit('goHome')" class="w-7 h-7 rounded-xl flex items-center justify-center transition-all active:scale-95 text-slate-400 hover:bg-slate-50">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                </div>
                <div class="flex justify-center items-center">
                    <button @click="showAddMenu = !showAddMenu" 
                        :class="[showAddMenu ? 'bg-slate-800 rotate-45 scale-90' : 'bg-indigo-600 text-white shadow-sm active:scale-95']"
                        class="w-7 h-7 rounded-xl flex items-center justify-center transition-all duration-500">
                        <svg class="h-[10px] w-[10px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                </div>
                <div class="flex justify-center">
                    <button @click="showSearch = !showSearch" :class="showSearch ? 'text-indigo-600 bg-indigo-50' : 'text-slate-400'" class="w-7 h-7 rounded-xl flex items-center justify-center transition-all active:scale-95 hover:bg-slate-50">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" /></svg>
                    </button>
                </div>
                <div class="flex justify-center">
                    <button @click="downloadList" class="w-8 h-8 rounded-xl flex items-center justify-center transition-all active:scale-95 text-slate-400 hover:bg-slate-50">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                </div>
            </div>
        </div>

    <!-- MODAL COMPONENTS -->
    <search-component v-model="searchQuery" :show="showSearch" @close="showSearch = false" />
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

const emit = defineEmits(['goHome']);

const folders = ref([{ id: 'all', name: '怨靈清單', status: '全部' }]);
const currentFolder = ref(folders.value[0]);
const addMode = ref(false);
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
        icon: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>',
        handler: () => { 
            editingId.value = null;
            form.value = { user_id: null, user_name: '', user_remarks: '', destination: '未處理', quantity: 1, know_date: new Date().toISOString().split('T')[0], process_date: '', remarks_text: '' };
            addMode.value = true; 
        } 
    },
    { 
        label: '多筆新增', 
        description: '直接貼上法號清單批量載錄',
        icon: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>',
        colorClass: 'bg-emerald-100 text-emerald-600',
        handler: () => { showBatchImport.value = true; } 
    }
]);

const form = ref({ user_name: '', user_remarks: '', destination: '未處理', quantity: 1, know_date: new Date().toISOString().split('T')[0], process_date: '', remarks_text: '' });

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
    form.value = { ...item };
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
const displayTitle = computed(() => currentFolder.value?.name || '怨靈專區');
const formatDate = (d) => {
    if (!d) return '-';
    const date = new Date(d);
    return `${date.getFullYear()}/${date.getMonth() + 1}/${date.getDate()}`;
};

onMounted(() => {
    loadData();
    window.addEventListener('click', (e) => { if (!e.target.closest('.relative')) openMenuId.value = null; });
});
</script>

<style scoped>
.animate-slide-up { animation: slideUp 0.15s ease-out; }
@keyframes slideUp { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
.animate-fade-in { animation: fadeIn 0.3s ease-in; }
@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
</style>
