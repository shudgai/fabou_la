<template>
    <div class="h-full bg-slate-50 flex flex-col pt-6 px-4 md:px-10 pb-20">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-black text-slate-900 tracking-tight">回收桶</h1>
                <p class="text-[11px] text-slate-400 font-bold uppercase tracking-[0.2em] mt-1">Recycle Bin</p>
            </div>
            <div class="flex items-center space-x-3">
                <button @click="cleanup" class="bg-rose-50 text-rose-600 px-4 py-2.5 rounded-2xl font-bold text-sm hover:bg-rose-100 transition-all border border-rose-100">
                    清空並清理備份 (5分內)
                </button>
            </div>
        </div>

        <!-- System Message -->
        <div class="bg-indigo-50 border border-indigo-100/50 rounded-2xl p-4 mb-8 flex items-start space-x-3">
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
        <div v-else-if="items.length > 0" class="space-y-3">
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
                    <button @click="restore(item)" class="flex-grow md:flex-none py-2.5 px-4 rounded-xl font-bold text-sm bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white transition-all">
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
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import MobileNavbar from './MobileNavbar.vue';

const items = ref([]);
const loading = ref(true);

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

const restore = async (item) => {
    if (!confirm(`確定要回復「${getTitle(item)}」嗎？`)) return;
    await axios.post('/api/trash/restore', { id: item.id, type: item.type });
    await loadItems();
};

const forceDelete = async (item) => {
    if (!confirm(`警告：這將永久刪除「${getTitle(item)}」，無法復原。確定嗎？`)) return;
    await axios.post('/api/trash/force-delete', { id: item.id, type: item.type });
    await loadItems();
};

const cleanup = async () => {
    if (!confirm('執行系統清理？這將清空所有已過期 (5公鐘) 的紀錄。')) return;
    await axios.post('/api/trash/cleanup');
    await loadItems();
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

onMounted(loadItems);
</script>
