<template>
    <div class="h-full bg-white flex flex-col overflow-clip">
        <!-- Header -->
        <div class="border-b border-slate-100 bg-white sticky top-0 z-20 flex items-center shrink-0 w-full" style="padding: 12px 12px; min-height: 52px;">
            <button @click="$emit('goHome')" class="p-2 text-slate-400 mr-2 active:scale-90 transition-transform">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
            <h1 class="font-black tracking-widest font-outfit !font-black" style="font-size: 32px !important; color: #0f172a !important; font-weight: 900 !important;">其他記錄專區</h1>
        </div>

        <!-- Document List -->
        <div class="flex-1 overflow-y-auto pb-32 px-4 pt-4 space-y-3 custom-scrollbar w-full">
            <!-- Navigation back when a record is focused -->
            <div v-if="expandedRecordId" @click="expandedRecordId = null" class="flex items-center text-indigo-600 font-black px-2 py-2 cursor-pointer active:scale-95 transition-all mb-1 animate-fade-in">
                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                返回記錄清單
            </div>

            <!-- Empty state -->
            <div v-if="!loading && records.length === 0" class="flex flex-col items-center justify-center py-24 text-slate-300">
                <svg class="w-16 h-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <p class="text-[17px] font-bold">尚無文件記錄</p>
                <p class="text-[14px] mt-1">點下方 + 新增第一筆</p>
            </div>

            <!-- Records -->
            <div v-for="record in (expandedRecordId ? records.filter(r => r.id === expandedRecordId) : records)" :key="record.id"
                @click="expandedRecordId === record.id ? null : toggleExpand(record.id)"
                :class="[
                    'bg-white border rounded-[22px] px-5 transition-all cursor-pointer relative',
                    expandedRecordId === record.id 
                        ? 'border-indigo-200 ring-4 ring-indigo-50 shadow-xl shadow-indigo-100/50 z-20 px-[15px] py-4 md:px-[15px]' 
                        : 'border-slate-100 shadow-sm hover:border-slate-200 z-10 py-4'
                ]">
                
                <div class="flex items-start justify-between">
                    <div class="flex flex-col min-w-0">
                        <div v-if="record.record_date" :class="[
                            'text-[13px] font-black mb-1 transition-colors',
                            expandedRecordId === record.id ? 'text-indigo-600' : 'text-slate-400'
                        ]">
                            {{ formatDate(record.record_date) }}
                        </div>
                        <h3 :class="[
                            'text-[17px] font-black truncate leading-tight transition-colors',
                            expandedRecordId === record.id ? 'text-indigo-900' : 'text-slate-900'
                        ]">{{ record.title || '文件記錄' }}</h3>
                    </div>


                </div>

                <!-- Expandable Content -->
                <div v-if="expandedRecordId === record.id" class="mt-6 pt-6 border-t border-slate-100 animate-fade-in" @click.stop>
                    <p class="text-[16px] text-slate-600 whitespace-pre-wrap leading-relaxed">{{ record.content }}</p>
                </div>
            </div>

            <div v-if="loading" class="py-12 flex justify-center">
                <div class="w-8 h-8 border-4 border-indigo-200 border-t-indigo-500 rounded-full animate-spin"></div>
            </div>
        </div>

        <!-- Bottom Navbar -->
        <mobile-navbar
            is-absolute
            :can-back="true"
            :show-action="true"
            :action-disabled="false"
            :can-search="false"
            :can-more="false"
            @back="$emit('goHome')"
            @home="$emit('goHome')"
            @action="prepareAdd"
        />

        <!-- Add/Edit Document View (Full Page) -->
        <div v-if="showAddModal" class="fixed inset-0 bg-white z-[100] flex flex-col animate-fade-in">
            <!-- Header -->
            <div class="border-b border-slate-100 bg-white sticky top-0 z-20 flex items-center shrink-0" style="padding: 8px 12px; min-height: 52px;">
                <button @click="closeModal" class="p-2 text-slate-400 mr-2 active:scale-90 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
                <h1 style="font-size: 32px !important; font-weight: 900; color: #0f172a !important;">{{ editingRecordId ? '修改文件記錄' : '新增文件記錄' }}</h1>
            </div>
            
            <div class="flex-1 overflow-y-auto p-6 space-y-5">
                <!-- Date Field -->
                <div class="space-y-1.5">
                    <label class="text-[13px] text-slate-400 font-bold px-1">記錄日期</label>
                    <div @click="showDatePicker = true" class="w-full px-4 py-4 bg-slate-50 border border-slate-200 rounded-2xl flex items-center justify-between cursor-pointer active:bg-slate-100 transition-all">
                        <span class="text-[17px] font-bold text-slate-900">{{ newDate ? newDate.replace(/-/g, '/') : '選擇日期' }}</span>
                        <svg class="w-5 h-5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </div>
                    <compact-date-picker v-if="showDatePicker" v-model="newDate" @close="showDatePicker = false" title="選擇記錄日期" />
                </div>

                <div class="space-y-1.5">
                    <label class="text-[13px] text-slate-400 font-bold px-1">標題 (選填)</label>
                    <input v-model="newTitle"
                        placeholder="點此輸入標題..."
                        class="w-full px-4 py-4 bg-slate-50 border border-slate-200 rounded-2xl text-[17px] font-bold text-slate-900 focus:ring-2 focus:ring-indigo-300 outline-none transition-all">
                </div>

                <div class="space-y-1.5 flex-1 flex flex-col">
                    <label class="text-[13px] text-slate-400 font-bold px-1">內容</label>
                    <textarea v-model="newContent"
                        placeholder="輸入文件內容..."
                        class="w-full px-4 py-4 bg-slate-50 border border-slate-200 rounded-2xl text-[16px] text-slate-900 focus:ring-2 focus:ring-indigo-300 outline-none transition-all resize-none leading-relaxed min-h-[300px]"></textarea>
                </div>
            </div>

            <div class="p-6 border-t border-slate-100 bg-white pb-8">
                <div class="flex space-x-3">
                    <button @click="closeModal"
                        class="flex-1 h-[52px] rounded-2xl font-black text-[18px] text-slate-500 bg-slate-100 active:bg-slate-200 transition-all">取消</button>
                    <button @click="saveRecord" :disabled="saving"
                        class="flex-[2] h-[52px] rounded-2xl font-black text-[18px] bg-blue-600 text-white shadow-lg shadow-blue-100 active:scale-95 transition-all disabled:opacity-50" style="color: white !important;">
                        {{ saving ? '儲存中...' : (editingRecordId ? '確認修改' : '確認儲存') }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Toast Notification -->
        <div v-if="toast" :class="toast.type === 'success' ? 'bg-emerald-500' : 'bg-red-500'" class="fixed top-20 left-1/2 -translate-x-1/2 px-6 py-3 rounded-2xl text-white font-bold shadow-xl z-[200] animate-toast">
            {{ toast.msg }}
        </div>

        <!-- Global Action Confirm / Toast (Critical for iOS deletion) -->
        <div v-if="persistentToast" class="fixed inset-0 z-[9999] flex items-center justify-center p-6 bg-slate-900/40 backdrop-blur-sm animate-fade-in">
            <div class="bg-white w-full max-w-sm rounded-[32px] shadow-2xl overflow-hidden animate-slide-up border border-white/20">
                <div class="p-8 text-center space-y-6">
                    <div class="flex flex-col items-center">
                        <div v-if="persistentToast.type === 'deleteConfirm'" class="w-16 h-16 bg-rose-50 text-rose-500 rounded-2xl flex items-center justify-center mb-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </div>
                        <div v-else-if="persistentToast.type === 'success'" class="w-16 h-16 bg-emerald-50 text-emerald-500 rounded-2xl flex items-center justify-center mb-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="3" stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <div v-else class="w-16 h-16 bg-indigo-50 text-indigo-500 rounded-2xl flex items-center justify-center mb-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <h3 class="text-[20px] font-black text-slate-900 leading-tight whitespace-pre-wrap">{{ persistentToast.msg }}</h3>
                    </div>

                    <div class="flex flex-col space-y-3">
                        <button v-if="persistentToast.type === 'deleteConfirm'" 
                                @click="executeToastAction" 
                                class="w-full py-4 bg-rose-500 text-white rounded-2xl font-black text-[18px] active:scale-95 transition-all shadow-lg shadow-rose-200/50" 
                                style="color: white !important;">
                            確認刪除
                        </button>
                        <button @click="persistentToast = null" 
                                :class="persistentToast.type === 'success' || persistentToast.type === 'error' ? 'bg-indigo-600 text-white shadow-indigo-100' : 'bg-slate-100 text-slate-500'"
                                class="w-full py-4 rounded-2xl font-black text-[18px] active:scale-95 transition-all shadow-lg"
                                :style="{ color: (persistentToast.type === 'success' || persistentToast.type === 'error' ? 'white !important' : 'inherit') }">
                            {{ persistentToast.type === 'success' || persistentToast.type === 'error' ? '確認' : '取消' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, reactive, computed, watch, onUnmounted } from 'vue';
import axios from 'axios';
import MobileNavbar from './MobileNavbar.vue';
import { writeClipboard, downloadBlob, lockBodyScroll, unlockBodyScroll } from '../utils/iosCompat';

import CompactDatePicker from './CompactDatePicker.vue';

const FIXED_FOLDER_NAME = '__other_records_zone__';
const SECTION = 'records';

const emit = defineEmits(['goHome']);
defineProps({ user: Object });

const records = ref([]);
const loading = ref(false);
const saving = ref(false);
const showAddModal = ref(false);
const showDatePicker = ref(false);
const newTitle = ref('');
const newContent = ref('');
const newDate = ref('');
const editingRecordId = ref(null);
const activeDropdownId = ref(null);
const expandedRecordId = ref(null);
const toast = ref(null);
const persistentToast = ref(null);
const deleteConfirmId = ref(null);

let folderId = null;

const showToast = (msg, type = 'success') => {
    toast.value = { msg, type };
    setTimeout(() => toast.value = null, 2000);
};

const formatDate = (d) => {
    if (!d) return '';
    return String(d).split('T')[0].replace(/-/g, '/');
};

const toggleExpand = (id) => {
    if (expandedRecordId.value === id) expandedRecordId.value = null;
    else expandedRecordId.value = id;
};

const prepareAdd = () => {
    editingRecordId.value = null;
    newTitle.value = '';
    newContent.value = '';
    newDate.value = new Date().toISOString().split('T')[0];
    showAddModal.value = true;
};

const handleMenuEdit = (record) => {
    editingRecordId.value = record.id;
    newTitle.value = record.title || '';
    newContent.value = record.content || '';
    newDate.value = record.record_date ? record.record_date.split('T')[0] : '';
    showAddModal.value = true;
};

const closeModal = () => {
    showAddModal.value = false;
    newTitle.value = '';
    newContent.value = '';
    newDate.value = '';
    editingRecordId.value = null;
};

const copyAsTextFile = (record) => {
    const text = `${record.record_date ? formatDate(record.record_date) + '\n' : ''}${record.title ? record.title + '\n' : ''}${record.content}`;
    writeClipboard(text).then(success => {
        if (success) showToast('✓ 已複製貼 LINE 格式');
    });
};

const downloadFile = (record) => {
    const text = `${record.record_date ? formatDate(record.record_date) + '\n' : ''}${record.title ? record.title + '\n' : ''}${record.content}`;
    const blob = new Blob([text], { type: 'text/plain' });
    downloadBlob(blob, `${record.title || '記錄'}_${formatDate(record.record_date || new Date())}.txt`);
    showToast('✓ 已下載檔案');
};

// Ensure the fixed folder exists and get its ID
const ensureFolder = async () => {
    const res = await axios.get('/other-folders', { params: { section: SECTION } });
    const folders = res.data;
    let folder = folders.find(f => f.name === FIXED_FOLDER_NAME);
    if (!folder) {
        const created = await axios.post('/other-folders', {
            name: FIXED_FOLDER_NAME,
            section: SECTION
        });
        folder = created.data;
    }
    folderId = folder.id;
    return folder;
};

const loadData = async () => {
    loading.value = true;
    try {
        const folder = await ensureFolder();
        const res = await axios.get('/other-folders', { params: { section: SECTION } });
        const found = res.data.find(f => f.id === folderId);
        records.value = found?.other_records || [];
    } catch (e) { console.error(e); } finally { loading.value = false; }
};

const saveRecord = async () => {
    if (!newContent.value.trim() && !newTitle.value.trim()) return;
    saving.value = true;
    try {
        const payload = {
            title: newTitle.value.trim(),
            content: newContent.value.trim(),
            record_date: newDate.value
        };
        if (editingRecordId.value) {
            await axios.put(`/other-records/${editingRecordId.value}`, payload);
            showToast('✓ 已修改紀錄');
        } else {
            await axios.post(`/other-folders/${folderId}/records`, payload);
            showToast('✓ 已儲存紀錄');
        }
        closeModal();
        await loadData();
    } catch (e) { console.error(e); } finally { saving.value = false; }
};

const deleteRecord = (id) => {
    deleteConfirmId.value = id;
    persistentToast.value = { msg: '確定要刪除此筆記錄嗎？', type: 'deleteConfirm' };
};

const executeToastAction = async () => {
    if (!persistentToast.value || persistentToast.value.type !== 'deleteConfirm') return;
    try {
        await axios.delete(`/other-records/${deleteConfirmId.value}`);
        persistentToast.value = { msg: '✓ 已刪除紀錄', type: 'success' };
        setTimeout(() => { if (persistentToast.value?.type === 'success') persistentToast.value = null; }, 1500);
        await loadData();
    } catch (e) {
        persistentToast.value = { msg: '✖ 刪除失敗', type: 'error' };
    }
    deleteConfirmId.value = null;
};

const isAnyModalOpen = computed(() => {
    return !!showAddModal.value || 
           !!persistentToast.value || 
           !!expandedRecordId.value || 
           !!showDatePicker.value || 
           !!activeDropdownId.value;
});

watch(isAnyModalOpen, (newVal) => {
    if (newVal) lockBodyScroll();
    else unlockBodyScroll();
});

onUnmounted(() => {
    if (isAnyModalOpen.value) unlockBodyScroll();
});

onMounted(() => {
    loadData();
    document.addEventListener('click', () => { activeDropdownId.value = null; });
});
</script>

<style scoped>
@keyframes slide-up { from { transform: translateY(100%); } to { transform: translateY(0); } }
.animate-slide-up { animation: slide-up 0.28s cubic-bezier(0.16, 1, 0.3, 1); }
@keyframes spin { to { transform: rotate(360deg); } }
.animate-spin { animation: spin 0.8s linear infinite; }
@keyframes fade-in { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
.animate-fade-in { animation: fade-in 0.2s ease-out; }
@keyframes toast {
    0% { transform: translate(-50%, 20px); opacity: 0; }
    20% { transform: translate(-50%, 0); opacity: 1; }
    80% { transform: translate(-50%, 0); opacity: 1; }
    100% { transform: translate(-50%, -20px); opacity: 0; }
}
.animate-toast { animation: toast 2s forwards; }
.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
</style>
