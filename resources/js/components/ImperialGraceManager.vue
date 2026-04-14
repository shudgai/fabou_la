<template>
    <div class="bg-white min-h-screen relative">
        <!-- Header -->
        <div class="border-b border-gray-100 flex items-center bg-white sticky top-0 z-10" style="padding: 12px 10px 10px 10px;">
            <button @click="handleBack" class="mr-3 text-indigo-600 font-normal">
                &lt; 返回
            </button>
            <h2 class="text-xl font-normal text-slate-800" style="padding-left: 10px;">
                {{ currentFolder ? '重大皇恩 - ' + currentFolder.name : '重大皇恩專區' }}
            </h2>
        </div>
        <!-- Perfectly Centered Ultra-Compact Warning Banner -->
        <div v-if="persistentToast" class="fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 z-[9999] animate-toast-in pointer-events-auto">
            <div class="bg-white rounded-lg shadow-[0_4px_20px_rgba(0,0,0,0.2)] flex flex-col border border-slate-200"
                style="padding: 5px; min-width: 120px;">
                <div class="flex items-center justify-between space-x-3">
                    <span class="text-[13px] font-normal leading-tight text-blue-600 whitespace-nowrap">
                        {{ persistentToast.msg }}
                    </span>
                    <button @click="persistentToast = null" class="text-slate-400 hover:text-slate-600 w-5 h-5 flex items-center justify-center font-normal text-sm">✕</button>
                </div>
                <!-- Action Buttons (Only when needed) -->
                <div v-if="['confirm', 'deleteConfirm'].includes(persistentToast.type)" class="flex space-x-2 mt-2 pb-1">
                    <template v-if="persistentToast.type === 'confirm'">
                        <button @click="saveSingle('correct')" class="flex-1 bg-slate-50 hover:bg-slate-100 text-blue-600 px-3 py-1 rounded border border-slate-100 text-[11px] active:scale-95 transition-all">
                            改回原本
                        </button>
                        <button @click="saveSingle('shunt')" class="flex-1 bg-slate-50 hover:bg-slate-100 text-blue-600 px-3 py-1 rounded border border-slate-100 text-[11px] active:scale-95 transition-all">
                            直接分流
                        </button>
                    </template>
                    <template v-if="persistentToast.type === 'deleteConfirm'">
                        <button @click="executeDelete" class="w-full bg-red-50 hover:bg-red-100 text-red-600 px-3 py-1 rounded border border-red-100 text-[11px] active:scale-95 transition-all">
                            確定刪除
                        </button>
                    </template>
                </div>
            </div>
        </div>
        <!-- Level 1: Folder Selection -->
        <div v-if="!currentFolder" class="flex flex-col">
            <button v-for="folder in folders" :key="folder.id" 
                @click="currentFolder = folder"
                class="flex items-center justify-between w-full border-b border-gray-50 active:bg-gray-50 transition-colors"
                style="padding: 10px 15px;">
                <span class="text-lg font-normal text-slate-800">
                    {{ folder.name }}
                </span>
                <span class="text-slate-300">&gt;</span>
            </button>
        </div>

        <!-- Level 2: Folder Contents -->
        <div v-else class="pb-32">
            <!-- ADD SINGLE FORM -->
            <div v-if="addMode === 'single'" class="mb-6 bg-white border-b border-gray-100 shadow-sm animate-fade-in" style="padding: 10px;">
                <div class="grid grid-cols-2 gap-1.5 mb-1.5">
                    <div>
                        <label class="text-xs text-slate-500 font-normal mb-1 block">得知日期</label>
                        <input v-model="form.record_date" type="date" class="w-full rounded-xl border-none shadow-sm text-sm" style="padding: 5px 8px;">
                    </div>
                    <div>
                        <label class="text-xs text-slate-500 font-normal mb-1 block">仙師</label>
                        <select v-model="form.master_id" class="w-full rounded-xl border-none shadow-sm text-sm bg-white" style="padding: 5px 8px;">
                            <option v-for="m in masters" :key="m.id" :value="m.id">{{ m.name }}</option>
                        </select>
                    </div>
                </div>
                <div class="mb-1.5">
                    <label class="text-xs text-slate-500 font-normal mb-1 block">法寶名稱</label>
                    <input v-model="form.name" type="text" placeholder="輸入法寶名稱" class="w-full rounded-xl border-none shadow-sm text-sm" style="padding: 5px 8px;">
                </div>
                <div class="mb-1.5">
                    <label class="text-xs text-slate-500 font-normal mb-1 block">法寶用意</label>
                    <input v-model="form.purpose" type="text" placeholder="輸入法寶用意" class="w-full rounded-xl border-none shadow-sm text-sm" style="padding: 5px 8px;">
                </div>
                <div class="mb-1.5">
                    <label class="text-xs text-slate-500 font-normal mb-1 block">備註</label>
                    <input v-model="form.remarks" type="text" placeholder="輸入備註 (選填)" class="w-full rounded-xl border-none shadow-sm text-sm" style="padding: 5px 8px;">
                </div>
                <div class="grid grid-cols-2 gap-1.5 mb-1">
                    <div>
                        <label class="text-xs text-slate-500 font-normal mb-1 block" :class="{ 'opacity-30': form.status === '未求得' }">求得日期 (選填)</label>
                        <input v-model="form.obtained_date" type="date" 
                            :disabled="form.status === '未求得'"
                            :class="{ 'bg-gray-50 opacity-50': form.status === '未求得' }"
                            class="w-full rounded-xl border-none shadow-sm text-sm" style="padding: 5px 8px;">
                    </div>
                    <div>
                        <label class="text-xs text-slate-500 font-normal mb-1 block">狀態</label>
                        <select v-model="form.status" @change="handleStatusChange" class="w-full rounded-xl border-none shadow-sm text-sm bg-white" style="padding: 5px 8px;">
                            <option value="未求得">未求得</option>
                            <option value="已求得">已求得</option>
                            <option value="已登記">已登記</option>
                        </select>
                    </div>
                </div>

                <div class="flex justify-center mt-2">
                    <button @click="saveSingle" :disabled="isSaving"
                        :class="isSaving ? 'bg-slate-300 cursor-not-allowed' : 'bg-indigo-600 hover:bg-indigo-700'"
                        class="text-white font-bold rounded-lg shadow-md transition-all uppercase" style="padding: 8px 20px; font-size: 14px; min-width: 120px;">
                        {{ isSaving ? '儲存中...' : (form.id ? '修改完成' : '確認新增') }}
                    </button>
                </div>
            </div>

            <!-- BATCH ADD FORM -->
            <div v-if="addMode === 'batch'" class="mb-4 bg-white border-b border-gray-100 shadow-sm" style="padding: 10px;">
                <div class="mb-3">
                    <label class="text-xs text-slate-500 font-normal mb-1 block">選擇仙師 (多筆資料將歸屬此仙師)</label>
                    <select v-model="batchMasterId" class="w-full rounded-xl border-none shadow-sm text-sm bg-gray-50/50" style="padding: 6px 10px;">
                        <option :value="null">請選擇仙師</option>
                        <option v-for="m in masters" :key="m.id" :value="m.id">{{ m.name }}</option>
                    </select>
                </div>
                <label class="text-xs text-slate-500 font-normal mb-1 block">貼上多筆資料 (格式: 日期 | 重大皇恩 | 用意)</label>
                <textarea v-model="batchInput" rows="6" class="w-full rounded-xl border-none shadow-sm text-sm mb-4" style="padding: 5px 8px;" placeholder="例如:
3/5 | 金線 | 轉亮光線
3月5日 | 法輪 | 能量提升"></textarea>
                <div class="flex justify-center mt-2">
                    <button @click="saveBatch" :disabled="!batchMasterId || !batchInput"
                        :class="(!batchMasterId || !batchInput) ? 'bg-slate-200 text-slate-400 cursor-not-allowed' : 'bg-indigo-600 text-white shadow-lg'"
                        class="font-normal rounded-lg transition-all uppercase px-8 py-2 text-sm">
                        確認一次新增
                    </button>
                </div>
            </div>

            <!-- List Display -->
            <div v-if="!addMode" style="padding: 8px;">
                <div v-if="loading" class="text-center py-4">載入中...</div>
                <div v-else-if="filteredRegistries.length === 0" class="text-center py-12 text-slate-400">
                    目前該仙師尚無登記資料。
                </div>
                <div v-else class="flex flex-col">
                    <div v-for="reg in filteredRegistries" :key="reg.id" class="border-b border-dashed border-slate-200 py-[6px] last:border-b-0 relative group">
                        <!-- Row 1 -->
                        <div class="flex items-start justify-between mb-0">
                            <div class="flex items-center space-x-3 pt-1">
                                <span class="text-[11px] text-slate-400">得知: {{ formatDate(reg.record_date) }}</span>
                                <span class="text-[11px] text-slate-800 bg-slate-50 px-1 rounded">
                                    求得: {{ reg.obtained_date ? formatDate(reg.obtained_date) : '----' }}
                                </span>
                            </div>
                    <div :class="[deleteConfirmId === reg.id ? 'text-red-500' : 'text-slate-900']" class="relative">
                        <button @click.stop="toggleMenu(reg.id)" class="p-1">
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM18 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                        </button>
                        <!-- MENU -->
                        <div v-if="openMenuId === reg.id" class="absolute right-0 mt-1 w-44 bg-white rounded-xl shadow-2xl border border-slate-100 z-[100] overflow-hidden animate-slide-up">
                            <button @click.stop="toggleExpand(reg.id)" class="w-full px-4 py-2.5 text-left text-xs text-slate-600 hover:bg-slate-50 border-b border-slate-50">{{ expandedIds.has(reg.id) ? '縮起清單' : '展開清單' }}</button>
                            <button @click.stop="editItem(reg)" class="w-full px-4 py-2.5 text-left text-xs text-slate-600 hover:bg-slate-50 border-b border-slate-50">修改</button>
                            <button @click.stop="copyOnly(reg)" class="w-full px-4 py-2.5 text-left text-xs text-emerald-600 hover:bg-emerald-50 border-b border-slate-50">單筆貼 LINE</button>
                            <button @click.stop="downloadOnly(reg)" class="w-full px-4 py-2.5 text-left text-xs text-blue-600 hover:bg-blue-50 border-b border-slate-50">單筆檔案下載</button>
                            <button @click.stop="copyListOnly" class="w-full px-4 py-2.5 text-left text-xs text-indigo-600 hover:bg-indigo-50 border-b border-slate-50">全部貼 LINE</button>
                            <button @click.stop="downloadListOnly" class="w-full px-4 py-2.5 text-left text-xs text-cyan-600 hover:bg-cyan-50 border-b border-slate-50">全部檔案下載</button>
                            <button @click.stop="confirmDelete(reg.id)" class="w-full px-4 py-2.5 text-left text-xs text-red-600 hover:bg-red-50">刪除</button>
                        </div>
                    </div>
                </div>
                <!-- Row 2 -->
                <div class="text-[17px] text-slate-900 mb-1 mt-[-10px]">{{ reg.name }}</div>
                <!-- Extra Rows -->
                <div v-if="expandedIds.has(reg.id)" class="animate-fade-in text-sm text-slate-600">
                    <div>用意：{{ reg.purpose || '無' }}</div>
                    <div v-if="reg.remarks">備註：{{ reg.remarks }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- STICKY BOTTOM NAV BAR -->
    <div class="fixed bottom-0 left-0 right-0 bg-white border-t border-slate-100 flex justify-around items-center z-50 shadow-sm" style="height: 44px; padding: 0 20px;">
        <button @click="$emit('goHome')" class="text-slate-400 p-2">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </button>
        <div class="relative">
            <!-- Add Menu -->
            <div v-if="showAddMenu" class="absolute bottom-14 left-1/2 -translate-x-1/2 w-36 bg-white rounded-2xl shadow-[0_10px_40px_rgba(0,0,0,0.2)] border border-slate-100 overflow-hidden animate-slide-up z-[60]">
                <button @click="prepareAdd('single'); showAddMenu = false" class="w-full py-3 px-4 text-sm text-slate-700 active:bg-slate-50 border-b border-slate-50 flex items-center justify-center">
                    <svg class="w-4 h-4 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4" stroke-width="2"/></svg>
                    逐筆新增
                </button>
                <button @click="prepareAdd('batch'); showAddMenu = false" class="w-full py-3 px-4 text-sm text-slate-700 active:bg-slate-50 flex items-center justify-center">
                    <svg class="w-4 h-4 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" stroke-width="2"/></svg>
                    多筆新增
                </button>
            </div>
            
            <button @click="showAddMenu = !showAddMenu" 
                :class="showAddMenu ? 'bg-slate-800 rotate-45' : 'bg-indigo-600'"
                class="text-white shadow-lg w-9 h-9 rounded-full flex items-center justify-center active:scale-90 transition-all duration-300">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
        </div>
        <div class="w-10"></div> <!-- Spacer for symmetry -->
    </div>
</div>
</template>

<script setup>
import { ref, computed, onMounted, defineEmits, watch } from 'vue';
import axios from 'axios';

defineEmits(['goHome']);

const currentFolder = ref(null);
const addMode = ref(null);
const allRegistries = ref([]);
const masters = ref([]);
const loading = ref(false);
const isSaving = ref(false);
const persistentToast = ref(null); 
const openMenuId = ref(null);
const showAddMenu = ref(false); // 控制底部新增選單
const deleteConfirmId = ref(null); // 追蹤正在準備刪除的物件
const expandedIds = ref(new Set());
const batchInput = ref('');
const batchMasterId = ref(null);
const form = ref({
    id: null, master_id: null, name: '', purpose: '', remarks: '', record_date: '', obtained_date: '', status: '未求得'
});

// 當關閉提示框時，重設紅點狀態
watch(persistentToast, (newVal) => {
    if (!newVal) deleteConfirmId.value = null;
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

const toggleMenu = (id) => { 
    openMenuId.value = openMenuId.value === id ? null : id; 
    deleteConfirmId.value = null; // 切換選單時重設紅點
};
const toggleExpand = (id) => {
    if (expandedIds.value.has(id)) expandedIds.value.delete(id);
    else expandedIds.value.add(id);
    openMenuId.value = null;
};

const handleBack = () => { if (addMode.value) addMode.value = null; else currentFolder.value = null; };
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
    if (isActuallyMismatched && resolution !== 'correct' && resolution !== 'shunt') {
        const targetM = masters.value.find(m => String(m.id) === formMid);
        const targetName = targetM ? targetM.name : '新仙師';
        const currentName = currentFolder.value ? currentFolder.value.name : '此區';
        persistentToast.value = { 
            msg: `您選了 [${targetName}]，要改回 [${currentName}] 嗎？`, 
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

const saveBatch = async () => {
    if (!batchInput.value || !batchMasterId.value) return;
    try {
        await axios.post('/imperial-graces/registry/batch', { 
            lines: batchInput.value.split('\n').filter(l => l.trim()), 
            master_id: batchMasterId.value 
        });
        addMode.value = null;
        loadData();
        persistentToast.value = { msg: '✓ 多筆新增成功', type: 'success' };
    } catch (e) { 
        alert('批次失敗'); 
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
    if (!currentFolder.value || currentFolder.value.id === 'unobtained') return [];
    return allRegistries.value.filter(r => r.master_id === currentFolder.value.id);
});
</script>

<style scoped>
.animate-fade-in { animation: fadeIn 0.3s ease-out; }
.animate-slide-up { animation: slideUp 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
@keyframes fadeIn { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
@keyframes slideUp { from { opacity: 0; transform: translate(-50%, 20px); } to { opacity: 1; transform: translate(-50%, 0); } }
</style>
<!-- v-refresh-download-fix-2026041422 -->
