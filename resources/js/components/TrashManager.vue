<template>
    <div class="h-full bg-slate-50 md:bg-white flex flex-col overflow-clip w-full">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8 pt-6 px-4 shrink-0">
            <div>
                <div class="flex items-center space-x-2">
                    <div class="flex items-center gap-2">
                        <logo-imperial-notebook :height="36" />
                        <h1 class="font-black text-slate-900 tracking-tight" style="font-size: 26px !important;">回收站專區</h1>
                    </div>
                    <div v-if="items.length > 0" class="px-2 py-0.5 bg-slate-900 text-white rounded-lg text-[14px] font-normal animate-fade-in shadow-sm">
                        {{ items.length }}
                    </div>
                </div>
                <p class="text-[11px] text-slate-400 font-bold uppercase tracking-[0.2em] mt-1">Recycle Bin</p>
            </div>
            <div class="flex items-center space-x-3">
                <button @click="cleanup" class="bg-rose-50 text-white px-4 py-2.5 rounded-2xl font-bold text-sm hover:bg-rose-100 transition-all border border-rose-100">
                    清空並清理備份 (5分內)
                </button>
            </div>
        </div>

        <!-- System Message -->
        <div class="bg-indigo-50 border border-indigo-100/50 rounded-2xl p-4 mb-8 flex items-start space-x-3 mx-4 shrink-0">
            <div class="p-2 bg-indigo-100 rounded-xl text-indigo-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </div>
            <div class="flex-grow">
                <p class="text-[13px] font-bold text-indigo-900">目前為測試模式</p>
                <p class="text-[12px] text-indigo-700/70 mt-0.5">刪除超過 5 分鐘的項目將會被系統永久移除。正式環境將改為 1 天後清理。</p>
            </div>
        </div>

        <div v-if="loading" class="flex-grow flex items-center justify-center">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
        </div>

        <!-- Trash List -->
        <div v-else-if="items.length > 0" class="flex-1 overflow-y-auto custom-scrollbar px-4 space-y-3 pb-32">
            <div v-for="item in items" :key="item.type + item.id" 
                class="bg-white p-5 rounded-[28px] border border-slate-100 shadow-sm hover:shadow-md transition-all flex flex-col md:flex-row md:items-center justify-between group overflow-hidden relative">

                <div class="flex-grow">
                    <div class="flex items-center space-x-2 mb-2">
                        <span class="text-[10px] font-black uppercase tracking-wider px-2 py-0.5 rounded-full bg-slate-100 text-slate-500">
                            {{ item.type_label }}
                        </span>
                        <span class="text-[11px] font-bold text-slate-400 flex items-center">
                             刪除於 {{ formatDate(item.deleted_at) }}
                        </span>
                    </div>

                    <h3 class="text-[17px] font-extrabold text-slate-800 leading-tight">
                        {{ getTitle(item) }}
                    </h3>
                    <p class="text-[13px] text-slate-500 mt-1 line-clamp-2 leading-relaxed">
                        {{ getContent(item) }}
                    </p>
                </div>

                <div class="flex items-center space-x-2 mt-4 md:mt-0 md:pl-6">
                    <button @click="restore(item)" class="flex-grow md:flex-none py-2.5 px-4 rounded-xl font-bold text-sm bg-indigo-50 text-white hover:bg-indigo-600 hover:text-white transition-all">
                        回復
                    </button>
                    <button @click="forceDelete(item)" class="p-2.5 rounded-xl font-bold text-sm text-slate-300 hover:text-rose-600 hover:bg-rose-50 transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                </div>
            </div>
        </div>

        <div v-else class="flex-grow flex flex-col items-center justify-center py-20 px-10 text-center">
            <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center text-slate-300 mb-6">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </div>
            <h2 class="text-xl font-bold text-slate-800">回收桶是空的</h2>
            <p class="text-slate-400 text-sm mt-2 max-w-sm">
                目前沒有任何暫存刪除的紀錄。刪除後的筆記會在此保留一段時間以便回復。
            </p>
        </div>

        <mobile-navbar 
            active-tab="home"
            :can-back="true"
            @back="$emit('go-home')"
        />

        <!-- Global Action Confirm / Toast (Critical for iOS) -->
        <div v-if="persistentToast" class="fixed inset-0 z-[9999] flex items-center justify-center p-6 bg-slate-900/40 backdrop-blur-sm animate-fade-in">
            <div class="bg-white w-full max-w-sm rounded-[32px] shadow-2xl overflow-hidden animate-slide-up border border-white/20">
                <div class="p-8 text-center space-y-6">
                    <div class="flex flex-col items-center">
                        <div v-if="persistentToast.type === 'delete' || persistentToast.type === 'cleanup'" class="w-16 h-16 bg-rose-50 text-rose-500 rounded-2xl flex items-center justify-center mb-4">
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
                        <button v-if="persistentToast.type !== 'success' && persistentToast.type !== 'error'" 
                                @click="executeToastAction" 
                                :class="persistentToast.type === 'delete' || persistentToast.type === 'cleanup' ? 'bg-rose-500 shadow-rose-200/50' : 'bg-indigo-600 shadow-indigo-200/50'"
                                class="w-full py-4 text-white rounded-2xl font-black text-[18px] active:scale-95 transition-all shadow-lg" 
                                style="color: white !important;">
                            {{ persistentToast.type === 'delete' ? '確認刪除' : (persistentToast.type === 'restore' ? '確認回復' : '確認執行') }}
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
import { ref, computed, watch, onMounted, onUnmounted } from 'vue';
import axios from 'axios';
import MobileNavbar from './MobileNavbar.vue';
import { lockBodyScroll, unlockBodyScroll } from '../utils/iosCompat';

const items = ref([]);
const loading = ref(true);
const persistentToast = ref(null);
const toastContext = ref(null);

const loadItems = async () => {
    loading.value = true;
    try {
        const res = await axios.get('/api/trash');
        items.value = res.data;
    } catch (e) {
        console.error(e);
    } finally {
        loading.value = false;
    }
};

const restore = (item) => {
    toastContext.value = item;
    persistentToast.value = { msg: `確定要回復「${getTitle(item)}」嗎？`, type: 'restore' };
};

const forceDelete = (item) => {
    toastContext.value = item;
    persistentToast.value = { msg: `警告：這將永久刪除「${getTitle(item)}」，無法復原。確定嗎？`, type: 'delete' };
};

const cleanup = () => {
    persistentToast.value = { msg: '執行系統清理？這將清空所有已過期 (5公鐘) 的紀錄。', type: 'cleanup' };
};

const executeToastAction = async () => {
    if (!persistentToast.value) return;
    const type = persistentToast.value.type;
    const item = toastContext.value;

    try {
        if (type === 'restore') {
            await axios.post('/api/trash/restore', { id: item.id, type: item.type });
            persistentToast.value = { msg: '✓ 已成功復原', type: 'success' };
        } else if (type === 'delete') {
            await axios.post('/api/trash/force-delete', { id: item.id, type: item.type });
            persistentToast.value = { msg: '✓ 已永久刪除', type: 'success' };
        } else if (type === 'cleanup') {
            await axios.post('/api/trash/cleanup');
            persistentToast.value = { msg: '✓ 系統清理完成', type: 'success' };
        }

        if (persistentToast.value?.type === 'success') {
            setTimeout(() => { if (persistentToast.value?.type === 'success') persistentToast.value = null; }, 1500);
        }
        await loadItems();
    } catch (e) {
        persistentToast.value = { msg: '✖ 操作失敗', type: 'error' };
    }
    toastContext.value = null;
};

const getTitle = (item) => {
    if (item.type === 'registry' || item.type === 'grace') return item.name || '(無標題)';
    if (item.type === 'teaching') return item.date + ' 開示記錄';
    if (item.type === 'other') return item.title || '(無標題記事)';
    if (item.type === 'grudge' || item.type === 'military') return item.user_name + ' 的紀錄';
    return '未命名紀錄';
};

const getContent = (item) => {
    if (item.type === 'teaching') return item.content;
    if (item.type === 'other') return item.content;
    if (item.type === 'grudge' || item.type === 'military') return item.remarks_text || item.destination;
    return item.purpose || item.remarks || '';
};

const formatDate = (dateString) => {
    if (!dateString) return '';
    const s = String(dateString).split('T')[0].trim();
    const parts = s.split(/[-/]/);
    if (parts.length === 3) {
        let y = parts[0];
        let m = parts[1].padStart(2, '0');
        let d = parts[2].padStart(2, '0');
        if (!isNaN(parseInt(y)) && !isNaN(parseInt(m)) && !isNaN(parseInt(d))) {
            return `${y}/${m}/${d}`;
        }
    }
    return s.replace(/-/g, '/');
};

const isAnyModalOpen = computed(() => {
    return !!persistentToast.value;
});

watch(isAnyModalOpen, (newVal) => {
    if (newVal) lockBodyScroll();
    else unlockBodyScroll();
});

onUnmounted(() => {
    if (isAnyModalOpen.value) unlockBodyScroll();
});

onMounted(loadItems);
</script>
