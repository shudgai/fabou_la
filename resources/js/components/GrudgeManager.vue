<template>
    <div class="bg-white min-h-screen relative">
        <!-- Header (Only show in Folder-view or Item-view) -->
        <div v-if="currentFolder" class="border-b border-gray-100 flex items-center justify-center bg-white/80 backdrop-blur-md sticky top-0 z-10" style="padding: 12px 10px 10px 10px;">
            <h2 class="text-xl font-medium font-outfit tracking-tight text-slate-800">
                <span>{{ currentFolder ? '怨靈專區 - ' + currentFolder.name : '怨靈專區' }}</span>
            </h2>
        </div>

        <!-- Level 1: Folder Selection (By Status) -->
        <div v-if="!currentFolder" class="min-h-screen bg-white" style="padding-bottom: 40px;">
            <!-- Large Static Title -->
            <div class="p-[5px] text-center">
                <h1 class="text-2xl font-medium text-black tracking-tight">怨靈專區</h1>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-2 gap-2 md:gap-4 p-4">
                <button v-for="(folder, idx) in folders" :key="folder.id" 
                    @click="currentFolder = folder"
                    class="flex flex-row md:flex-col items-center md:justify-between bg-white transition-all active:scale-95 border-0 md:border-[1.5px] md:border-[#E6D5B8] md:aspect-square md:rounded-[28px] md:shadow-sm group p-2 w-full md:w-[70%] md:mx-auto border-b border-gray-50 last:border-b-0 md:border-b-[1.5px]">
                    <div class="flex items-center justify-center">
                        <svg class="w-8 h-8 md:w-14 md:h-14 text-yellow-400 drop-shadow-sm" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M10 4H4c-1.11 0-2 .89-2 2v12c0 1.1.89 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.11-.89-2-2-2h-8l-2-2z" />
                        </svg>
                    </div>
                    <span class="text-[16px] md:text-[15px] font-medium text-black whitespace-nowrap ml-2 md:ml-0 md:mb-1">{{ folder.name }}</span>
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
        <div v-else class="pb-10">

            <div v-if="loading" class="text-center py-4 text-xs text-slate-400">載入中...</div>
            <div v-else-if="filteredItems.length === 0" class="text-center py-12 text-slate-400">暫無相關紀錄。</div>
            <div v-else class="p-[5px]">
                <div v-for="item in (focusedId ? filteredItems.filter(i => i.id === focusedId) : filteredItems)" :key="item.id" 
                    @click="toggleExpand(item.id)"
                    class="border-b border-dashed border-slate-200 py-3 relative active:bg-slate-50 transition-colors cursor-pointer">
                    <div class="flex justify-between items-start">
                        <span class="text-[11px] text-indigo-500 font-medium">{{ item.user?.name || '未知' }}</span>
                        <div class="flex space-x-3">
                            <button @click="editItem(item)" class="text-indigo-400 text-xs">修改</button>
                            <button @click="deleteItem(item.id)" class="text-red-400 text-xs">刪除</button>
                        </div>
                    </div>
                    <div class="flex justify-between mt-1">
                        <div class="text-lg font-normal text-slate-800">
                            {{ item.destination || '未指定去處' }} 
                            <span class="text-sm text-slate-400 ml-2">x {{ item.quantity }}</span>
                        </div>
                    </div>
                    <div class="text-[10px] text-slate-400 mt-1">
                        得知: {{ formatDate(item.know_date) }} | 處理: {{ formatDate(item.process_date) }}
                    </div>
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
                    <button @click="showAddMenu = !showAddMenu" 
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
                    <button @click="downloadList" :disabled="!currentFolder"
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

        <grudge-add-form 
            :show="addMode"
            :initialData="form"
            :editingId="editingId"
            :users="users"
            @save="saveItem"
            @cancel="addMode = false; editingId = null"
        />
    </div>
</template>

<script setup>
import { ref, computed, onMounted, defineEmits } from 'vue';
import axios from 'axios';

const emit = defineEmits(['goHome']);

const currentFolder = ref(null);
const addMode = ref(false);
const showAddMenu = ref(false);
const showSearch = ref(false);
const searchQuery = ref('');
const items = ref([]);
const users = ref([]);
const loading = ref(true);
const editingId = ref(null);
const focusedId = ref(null);

const toggleExpand = (id) => {
    focusedId.value = focusedId.value === id ? null : id;
};

const displayTitle = computed(() => {
    return currentFolder.value?.name || '怨靈專區';
});

const addActions = computed(() => [
    { 
        label: '新增紀錄', 
        description: '新增一筆怨靈處理資料',
        icon: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>',
        handler: () => { addMode.value = true; } 
    }
]);

const form = ref({
    user_id: null,
    status: '代處理',
    destination: '',
    quantity: 1,
    know_date: new Date().toISOString().split('T')[0],
    process_date: ''
});

const folders = ref([
    { id: 'todo', name: '代處理資料', status: '代處理' },
    { id: 'done', name: '已完成資料', status: '已完成' },
]);

const loadData = async () => {
    loading.value = true;
    try {
        const [res, ures] = await Promise.all([
            axios.get('/grudges'),
            axios.get('/api/users-list')
        ]);
        items.value = res.data;
        users.value = ures.data;
    } catch (e) { console.error(e); } finally { loading.value = false; }
};

const saveItem = async (formData) => {
    try {
        if (editingId.value) {
            await axios.put(`/grudges/${editingId.value}`, formData);
        } else {
            await axios.post('/grudges', { ...formData, status: currentFolder.value?.status });
        }
        addMode.value = false;
        editingId.value = null;
        loadData();
    } catch (e) { alert('儲存失敗'); }
};

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

const editItem = (item) => {
    editingId.value = item.id;
    form.value = { ...item };
    addMode.value = true;
};

const deleteItem = async (id) => {
    if (!confirm('確定？')) return;
    await axios.delete(`/grudges/${id}`);
    loadData();
};

const filteredItems = computed(() => {
    if (!currentFolder.value) return [];
    let filtered = items.value.filter(i => i.status === currentFolder.value.status);
    if (searchQuery.value) {
        const q = searchQuery.value.toLowerCase();
        filtered = filtered.filter(i => 
            (i.destination?.toLowerCase().includes(q)) || 
            (i.user?.name?.toLowerCase().includes(q))
        );
    }
    return filtered;
});

const downloadList = () => {
    if (!currentFolder.value) return;
    const contents = `【怨靈紀錄清單 - ${currentFolder.value.name}】\r\n\r\n` + 
        filteredItems.value.map(i => `${i.user?.name || '未知'}: ${i.destination} (x${i.quantity})`).join('\r\n');
    
    const blob = new Blob([contents], { type: 'text/plain' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `怨靈紀錄_${currentFolder.value.name}.txt`;
    a.click();
    window.URL.revokeObjectURL(url);
};

const formatDate = (d) => d ? new Date(d).toLocaleDateString('zh-TW') : '-';

onMounted(loadData);
</script>
