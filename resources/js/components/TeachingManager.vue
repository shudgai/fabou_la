<template>
    <div class="bg-white min-h-screen relative">
        <!-- Header -->
        <div class="border-b border-gray-100 flex items-center justify-center bg-white/80 backdrop-blur-md sticky top-0 z-10" style="padding: 12px 10px 10px 10px;">
            <h2 class="text-xl font-bold font-outfit tracking-tight text-slate-800">
                {{ currentFolder ? '仙師開示 - ' + currentFolder.name : '仙師開示專區' }}
            </h2>
        </div>

        <!-- Level 1: Folder Selection -->
        <div v-if="!currentFolder" class="grid grid-cols-2 gap-3 p-3" style="margin-top: 5px;">
            <button v-for="(folder, idx) in folders" :key="folder.id" 
                @click="currentFolder = folder"
                class="flex flex-col items-center justify-center aspect-square rounded-3xl transition-all active:scale-95 shadow-sm border border-slate-100 group px-4"
                :class="[
                    idx % 4 === 0 ? 'bg-indigo-50/50 hover:bg-indigo-50' : 
                    idx % 4 === 1 ? 'bg-rose-50/50 hover:bg-rose-50' : 
                    idx % 4 === 2 ? 'bg-emerald-50/50 hover:bg-emerald-50' : 'bg-amber-50/50 hover:bg-amber-50'
                ]">
                <div class="w-12 h-12 rounded-2xl bg-white shadow-sm flex items-center justify-center mb-3 group-hover:shadow-md transition-shadow">
                    <svg class="w-6 h-6" :class="[
                        idx % 4 === 0 ? 'text-indigo-500' : 
                        idx % 4 === 1 ? 'text-rose-500' : 
                        idx % 4 === 2 ? 'text-emerald-500' : 'text-amber-500'
                    ]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </div>
                <span class="text-sm font-bold text-slate-700">{{ folder.name }}</span>
            </button>
        </div>

        <!-- Level 2: List -->
        <div v-else class="pb-32">
            <!-- Add Modal (Same style) -->
            <div v-if="addMode" class="mb-6 bg-white border-b border-gray-100 shadow-sm p-3">
                <input v-model="form.title" type="text" placeholder="輸入標題" class="w-full rounded-xl border-none shadow-sm text-sm mb-2" style="padding: 5px 8px;">
                <textarea v-model="form.content" rows="4" placeholder="輸入開示內容..." class="w-full rounded-xl border-none shadow-sm text-sm" style="padding: 5px 8px;"></textarea>
                <div class="flex justify-center mt-2">
                    <button @click="saveItem" class="bg-indigo-600 text-white font-normal rounded-lg px-4 py-1.5 shadow-md">{{ editingId ? '修改完成' : '確認新增' }}</button>
                </div>
            </div>

            <div v-if="loading" class="text-center py-4 text-xs text-slate-400">載入中...</div>
            <div v-else-if="filteredItems.length === 0" class="text-center py-12 text-slate-400">尚無開示紀錄。</div>
            <div v-else class="p-2">
                <div v-for="item in filteredItems" :key="item.id" class="border-b border-dashed border-slate-200 py-3 relative">
                    <div class="flex justify-between items-start">
                        <span class="text-[11px] text-slate-400">{{ formatDate(item.created_at) }}</span>
                        <div class="flex space-x-3">
                            <button @click="editItem(item)" class="text-indigo-400 text-xs">修改</button>
                            <button @click="deleteItem(item.id)" class="text-red-400 text-xs">刪除</button>
                        </div>
                    </div>
                    <div class="text-lg font-bold text-slate-800 mt-1">{{ item.title }}</div>
                    <div class="text-sm text-slate-600 mt-1 line-clamp-3">{{ item.content }}</div>
                </div>
            </div>
        </div>

        <div class="fixed bottom-0 left-0 right-0 bg-white/95 backdrop-blur-md border-t border-slate-100 z-50 shadow-[0_-4px_10px_rgba(0,0,0,0.03)]" style="height: 60px;">
            <div class="grid grid-cols-5 h-full items-center px-2">
                <!-- BACK BUTTON -->
                <div class="flex justify-center">
                    <button @click="handleBack" class="w-10 h-10 rounded-2xl flex items-center justify-center transition-all active:scale-90 text-slate-400 hover:bg-slate-50">
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
                    <div v-if="currentFolder" class="absolute -top-6">
                        <button @click="addMode = !addMode" 
                            :class="[addMode ? 'bg-slate-800 rotate-45 scale-90' : 'bg-indigo-600 text-white shadow-lg shadow-indigo-200 active:scale-95']"
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
                    <button @click="downloadList" :disabled="!currentFolder"
                        class="w-10 h-10 rounded-2xl flex items-center justify-center transition-all active:scale-95 text-slate-400 hover:bg-slate-50 disabled:opacity-30">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                </div>
            </div>
        </div>
        
        <search-component 
            v-if="showSearch"
            v-model="searchQuery" 
            :show="showSearch" 
            @close="showSearch = false" 
        />
    </div>
</template>

<script setup>
import { ref, computed, onMounted, defineEmits } from 'vue';
import axios from 'axios';

const emit = defineEmits(['goHome']);

const currentFolder = ref(null);
const addMode = ref(false);
const teachings = ref([]);
const masters = ref([]);
const loading = ref(true);
const editingId = ref(null);

const form = ref({
    title: '',
    content: '',
    master_id: null,
    group_id: null,
    user_id: 1
});

const folders = ref([
    { id: 1, name: '老祖仙師' },
    { id: 2, name: '元始仙師' },
    { id: 3, name: '道祖仙師' },
    { id: 4, name: '靈寶仙師' },
    { id: 5, name: '父皇' },
    { id: 6, name: '太宰仙師' },
    { id: 7, name: '太子' },
    { id: 8, name: '閻王仙師' },
]);

const fetchTeachings = async () => {
    loading.value = true;
    try {
        const res = await axios.get('/teachings');
        teachings.value = res.data;
    } catch (e) { console.error(e); } finally { loading.value = false; }
};

const saveItem = async () => {
    if (!form.value.title || !form.value.content) return alert('請填寫完整內容');
    try {
        if (editingId.value) {
            await axios.put(`/teachings/${editingId.value}`, form.value);
        } else {
            await axios.post('/teachings', { ...form.value, master_id: currentFolder.value?.id });
        }
        addMode.value = false;
        editingId.value = null;
        form.value = { title: '', content: '', master_id: null, group_id: null, user_id: 1 };
        fetchTeachings();
    } catch (e) { alert('儲存失敗'); }
};

const searchQuery = ref('');
const showSearch = ref(false);

const handleBack = () => {
    if (addMode.value) {
        addMode.value = false;
        editingId.value = null;
    } else if (currentFolder.value) {
        currentFolder.value = null;
    } else {
        emit('goHome');
    }
};

const downloadList = () => {
    if (!currentFolder.value) return;
    const contents = `【仙師開示清單 - ${currentFolder.value.name}】\r\n\r\n` + 
        filteredItems.value.map(i => `${formatDate(i.created_at)} - ${i.title}\r\n${i.content}`).join('\r\n\r\n');
    
    const blob = new Blob([contents], { type: 'text/plain' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `仙師開示_${currentFolder.value.name}.txt`;
    a.click();
    window.URL.revokeObjectURL(url);
};

const editItem = (item) => {
    editingId.value = item.id;
    form.value = { ...item };
    addMode.value = true;
};

const deleteItem = async (id) => {
    if (!confirm('確定要刪除嗎？')) return;
    try {
        await axios.delete(`/teachings/${id}`);
        fetchTeachings();
    } catch (e) { alert('刪除失敗'); }
};

const filteredItems = computed(() => {
    if (!currentFolder.value) return [];
    return teachings.value.filter(t => t.master_id === currentFolder.value.id);
});

const formatDate = (dateStr) => new Date(dateStr).toLocaleDateString('zh-TW');

onMounted(fetchTeachings);
</script>
