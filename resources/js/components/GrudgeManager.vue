<template>
    <div class="bg-white min-h-screen relative">
        <!-- Header -->
        <div class="border-b border-gray-100 flex items-center justify-center bg-white/80 backdrop-blur-md sticky top-0 z-10" style="padding: 12px 10px 10px 10px;">
            <h2 class="text-xl font-bold font-outfit tracking-tight text-slate-800">
                {{ currentFolder ? currentFolder.name : '怨靈專區' }}
            </h2>
        </div>

        <!-- Level 1: Folder Selection (By Status) -->
        <div v-if="!currentFolder" class="grid grid-cols-2 gap-3 p-3" style="margin-top: 5px;">
            <button v-for="(folder, idx) in folders" :key="folder.id" 
                @click="currentFolder = folder"
                class="flex flex-col items-center justify-center aspect-square rounded-3xl transition-all active:scale-95 shadow-sm border border-slate-100 group"
                :class="[
                    idx % 2 === 0 ? 'bg-indigo-50/50 hover:bg-indigo-50' : 'bg-emerald-50/50 hover:bg-emerald-50'
                ]"
                style="padding: 15px;">
                <div class="w-12 h-12 rounded-2xl bg-white shadow-sm flex items-center justify-center mb-3 group-hover:shadow-md transition-shadow">
                    <svg class="w-6 h-6" :class="[
                        idx % 2 === 0 ? 'text-indigo-500' : 'text-emerald-500'
                    ]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path v-if="idx % 2 === 0" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        <path v-else d="M5 13l4 4L19 7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </div>
                <span class="text-sm font-bold text-slate-700">{{ folder.name }}</span>
            </button>
        </div>

        <!-- Level 2: List -->
        <div v-else class="pb-32">

            <div v-if="loading" class="text-center py-4 text-xs text-slate-400">載入中...</div>
            <div v-else-if="filteredItems.length === 0" class="text-center py-12 text-slate-400">暫無相關紀錄。</div>
            <div v-else class="p-2">
                <div v-for="item in filteredItems" :key="item.id" class="border-b border-dashed border-slate-200 py-3 relative">
                    <div class="flex justify-between items-start">
                        <span class="text-[11px] text-indigo-500 font-bold">{{ item.user?.name || '未知' }}</span>
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
                </div>
            </div>
        </div>
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
                    <div v-if="currentFolder" class="absolute -top-6">
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
                    <button @click="downloadList" :disabled="!currentFolder"
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

        <grudge-add-form 
            :show="addMode"
            :initialData="form"
            :editingId="editingId"
            :users="users"
            @save="saveItem"
            @cancel="addMode = false; editingId = null"
        />
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
