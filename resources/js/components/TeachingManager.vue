<template>
    <div class="bg-white min-h-screen relative">
        <!-- Header (Only show in Folder-view or Item-view) -->
        <div v-if="currentFolder" class="border-b border-gray-100 flex items-center justify-center bg-white/80 backdrop-blur-md sticky top-0 z-10" style="padding: 12px 10px 10px 10px;">
            <h2 class="text-xl font-medium font-outfit tracking-tight text-black">
                <span>{{ currentFolder ? '仙師開示 - ' + currentFolder.name : '仙師開示專區' }}</span>
            </h2>
        </div>

        <!-- Level 1: Folder Selection -->
        <div v-if="!currentFolder" class="min-h-screen bg-white">
            <!-- Large Static Title -->
            <div class="px-6 py-4 text-center">
                <h1 class="text-2xl font-normal text-slate-800 tracking-tight">仙師開示專區</h1>
                <p class="text-[10px] text-slate-400 font-normal uppercase tracking-widest mt-1">高效管理每一份神聖教誨</p>
            </div>

            <!-- Global Stats Banner -->
            <div class="mx-4 mb-6 p-5 bg-indigo-900 rounded-[32px] text-white shadow-xl flex items-center justify-between relative overflow-hidden">
                <div class="relative z-10">
                    <span class="text-[10px] font-bold text-indigo-300 uppercase tracking-widest block">總計開示篇數</span>
                    <span class="text-3xl font-black tracking-tighter">{{ teachings.length.toLocaleString('zh-TW') }}</span>
                </div>
                <!-- Glassmorphism decorative circles -->
                <div class="absolute -right-4 -top-4 w-20 h-20 bg-white/10 rounded-full blur-2xl"></div>
                <div class="flex flex-col items-end relative z-10">
                    <span class="text-[10px] font-bold text-indigo-300 uppercase tracking-widest block">本月新增</span>
                    <span class="text-lg font-bold">{{ monthlyTeachingsCount }} 篇</span>
                </div>
            </div>

            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 p-4">
                <button v-for="(folder, idx) in folders" :key="folder.id" 
                    @click="currentFolder = folder"
                    class="flex flex-col items-center justify-center bg-white transition-all active:scale-95 border border-slate-100 rounded-[28px] shadow-sm hover:shadow-md group p-5 aspect-square relative"
                   >
                    <div class="relative mb-3">
                        <svg class="w-12 h-12 text-yellow-500 drop-shadow-sm transition-transform group-hover:scale-110" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M10 4H4c-1.11 0-2 .89-2 2v12c0 1.1.89 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.11-.89-2-2-2h-8l-2-2z" />
                        </svg>
                        <!-- Folder Badge -->
                        <div v-if="getItemCount(folder.id) > 0" class="absolute -top-1 -right-1 bg-indigo-600 text-white text-[10px] font-black px-2 py-0.5 rounded-full shadow-lg border-2 border-white">
                            {{ getItemCount(folder.id) }}
                        </div>
                    </div>
                    <span class="text-[15px] font-bold text-slate-700 whitespace-nowrap">
                        {{ folder.name }}
                    </span>
                </button>
            </div>


            <!-- Minimalist Back to Dashboard -->
            <div class="mt-12 flex justify-center pb-32">
                <button @click="$emit('goHome')" class="text-slate-300 hover:text-slate-500 transition-colors flex items-center space-x-2 active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" /></svg>
                    <span class="text-xs font-medium tracking-widest uppercase">返回專區列表</span>
                </button>
            </div>
        </div>

        <!-- Level 2: List -->
        <div v-else class="pb-32">
            <!-- Add Modal (Same style) -->
            <!-- Add Overlay (Full Screen Mobile) -->
            <div v-if="addMode" class="fixed inset-0 z-[100] bg-white md:relative md:inset-auto md:z-0 md:mb-6 md:bg-white md:border-b md:border-gray-100 md:shadow-sm p-0 md:p-3 overflow-y-auto">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-white sticky top-0 md:hidden">
                    <h3 class="text-lg font-medium text-black">新增開示</h3>
                    <button @click="addMode = false" class="p-2 text-slate-400">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                </div>
                <div class="p-3 space-y-1 md:p-0 md:space-y-1">
                    <input v-model="form.title" type="text" placeholder="輸入標題" class="w-full h-[20px] rounded-xl border-none bg-slate-50 px-2 text-[13px] leading-none focus:ring-0 outline-none md:rounded-xl md:border-none md:shadow-sm">
                    <textarea v-model="form.content" rows="12" placeholder="輸入開示內容..." class="w-full rounded-xl border-none bg-slate-50 px-2 text-sm focus:ring-0 outline-none md:rounded-xl md:border-none md:shadow-sm"></textarea>
                    <div class="flex justify-center mt-3 md:mt-1">
                        <button @click="saveItem" class="w-full md:w-auto bg-indigo-600 text-white font-normal rounded-xl md:rounded-lg p-[5px] shadow-lg md:shadow-md">{{ editingId ? '修改完成' : '確認新增' }}</button>
                    </div>
                </div>
            </div>

            <div v-if="loading" class="text-center py-4 text-xs text-slate-400">載入中...</div>
            <div v-else class="p-[5px]">
                <!-- Statistics Banner -->
                <div v-if="filteredItems.length > 0" class="px-3 py-4 mb-4 bg-gradient-to-br from-indigo-50 to-white rounded-[24px] border border-indigo-100/50 shadow-sm flex items-center justify-between">
                    <div class="flex flex-col">
                        <span class="text-[10px] font-bold text-indigo-400 uppercase tracking-widest">當前區域開示總計</span>
                        <span class="text-2xl font-black text-indigo-900 tracking-tighter">{{ filteredItems.length }} 篇</span>
                    </div>
                    <div class="h-10 w-10 bg-indigo-100/50 rounded-2xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.082.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5S19.832 5.477 21 6.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                </div>

                <div v-if="filteredItems.length === 0" class="text-center py-12 text-slate-400">尚無開示紀錄。</div>
                <div v-for="item in (focusedId ? filteredItems.filter(i => i.id === focusedId) : filteredItems) " :key="item.id" 
                    @click="toggleExpand(item.id)"
                    class="border-b border-dashed border-slate-200 py-3 relative active:bg-slate-50 transition-colors cursor-pointer">
                    <div class="flex justify-between items-start">
                        <span class="text-[11px] text-slate-400">{{ formatDate(item.created_at) }}</span>
                        <div class="flex space-x-3">
                            <button @click="editItem(item)" class="text-indigo-400 text-xs">修改</button>
                            <button @click="deleteItem(item.id)" class="text-red-400 text-xs">刪除</button>
                        </div>
                    </div>
                    <div class="text-lg font-medium text-black mt-1">{{ item.title }}</div>
                    <div class="text-sm text-slate-600 mt-1" :class="focusedId === item.id ? '' : 'line-clamp-3'">{{ item.content }}</div>
                    <div v-if="focusedId === item.id" class="mt-4 pt-4 border-t border-slate-100 flex justify-end space-x-4">
                        <button @click.stop="editItem(item)" class="text-indigo-600 text-xs font-medium px-3 py-1 bg-indigo-50 rounded-lg">修改內容</button>
                        <button @click.stop="deleteItem(item.id)" class="text-red-500 text-xs font-medium px-3 py-1 bg-red-50 rounded-lg">刪除紀錄</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="fixed bottom-0 left-0 right-0 bg-white/95 backdrop-blur-md border-t border-slate-100 z-50 shadow-[0_-4px_10px_rgba(0,0,0,0.03)]" style="height: 30px;">
            <div class="grid grid-cols-5 h-full items-center px-2">
                <!-- BACK BUTTON -->
                <div class="flex justify-center">
                    <button @click="handleBack" class="w-7 h-7 rounded-xl flex items-center justify-center transition-all active:scale-90 text-slate-400 hover:bg-slate-50">
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
                    <button v-if="currentFolder" @click="addMode = !addMode" 
                        :class="[addMode ? 'bg-slate-800 rotate-45 scale-90' : 'bg-indigo-600 text-white shadow-sm active:scale-95']"
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
                    <button @click="downloadList" :disabled="!currentFolder"
                        class="w-8 h-8 rounded-xl flex items-center justify-center transition-all active:scale-95 text-slate-400 hover:bg-slate-50 disabled:opacity-30">
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
const focusedId = ref(null);

const toggleExpand = (id) => {
    focusedId.value = focusedId.value === id ? null : id;
};

const displayTitle = computed(() => {
    return currentFolder.value?.name || '仙師開示專區';
});

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
    } else if (focusedId.value) {
        focusedId.value = null;
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
    let filtered = teachings.value.filter(t => t.master_id === currentFolder.value.id);
    if (searchQuery.value) {
        const q = searchQuery.value.toLowerCase();
        filtered = filtered.filter(i => 
            i.title.toLowerCase().includes(q) || 
            i.content.toLowerCase().includes(q)
        );
    }
    return filtered;
});

const monthlyTeachingsCount = computed(() => {
    const now = new Date();
    return teachings.value.filter(t => {
        const d = new Date(t.created_at);
        return d.getMonth() === now.getMonth() && d.getFullYear() === now.getFullYear();
    }).length;
});

const getItemCount = (id) => {
    return teachings.value.filter(t => t.master_id === id).length;
};

const formatDate = (dateStr) => new Date(dateStr).toLocaleDateString('zh-TW');

onMounted(fetchTeachings);
</script>
