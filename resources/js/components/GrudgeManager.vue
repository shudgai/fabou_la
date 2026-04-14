<template>
    <div class="bg-white min-h-screen relative">
        <!-- Header -->
        <div class="border-b border-gray-100 flex items-center bg-white sticky top-0 z-10" style="padding: 12px 10px 10px 10px;">
            <button @click="handleBack" class="mr-3 text-indigo-600 font-normal">
                &lt; 返回
            </button>
            <h2 class="text-xl font-normal text-slate-800">
                {{ currentFolder ? currentFolder.name : '怨靈專區' }}
            </h2>
        </div>

        <!-- Level 1: Folder Selection (By Status) -->
        <div v-if="!currentFolder" class="flex flex-col">
            <button v-for="folder in folders" :key="folder.id" 
                @click="currentFolder = folder"
                class="flex items-center justify-between w-full border-b border-gray-50 active:bg-gray-50 transition-colors"
                style="padding: 10px 15px;">
                <span class="text-lg font-normal text-slate-800">{{ folder.name }}</span>
                <span class="text-slate-300">&gt;</span>
            </button>
        </div>

        <!-- Level 2: List -->
        <div v-else class="pb-32">
            <!-- Add Form (Same style) -->
            <div v-if="addMode" class="mb-6 bg-white border-b border-gray-100 shadow-sm p-3">
                <div class="grid grid-cols-2 gap-2 mb-2">
                    <select v-model="form.user_id" class="w-full rounded-xl border-none shadow-sm text-sm" style="padding: 5px 8px;">
                        <option :value="null">選擇使用者</option>
                        <option v-for="u in users" :key="u.id" :value="u.id">{{ u.name }}</option>
                    </select>
                    <input v-model="form.quantity" type="number" placeholder="數量" class="w-full rounded-xl border-none shadow-sm text-sm" style="padding: 5px 8px;">
                </div>
                <input v-model="form.destination" type="text" placeholder="去處" class="w-full rounded-xl border-none shadow-sm text-sm mb-2" style="padding: 5px 8px;">
                <div class="grid grid-cols-2 gap-2 mb-2">
                    <input v-model="form.know_date" type="date" class="w-full rounded-xl border-none shadow-sm text-sm" style="padding: 5px 8px;">
                    <input v-model="form.process_date" type="date" class="w-full rounded-xl border-none shadow-sm text-sm" style="padding: 5px 8px;">
                </div>
                <div class="flex justify-center mt-2">
                    <button @click="saveItem" class="bg-indigo-600 text-white font-normal rounded-lg px-4 py-1.5 shadow-md">{{ editingId ? '修改完成' : '確認新增' }}</button>
                </div>
            </div>

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

        <!-- STICKY BOTTOM NAV BAR -->
        <div class="fixed bottom-0 left-0 right-0 bg-white border-t border-slate-100 flex justify-around items-center z-50 shadow-sm" style="height: 44px; padding: 0 20px;">
            <button @click="$emit('goHome')" class="text-slate-400 p-2">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
            <div class="relative">
                <button 
                    @click="addMode = true"
                    :disabled="!currentFolder"
                    :class="[!currentFolder ? 'bg-gray-100 text-gray-300 opacity-50' : 'bg-indigo-600 text-white shadow-lg active:scale-95']"
                    class="w-7 h-7 rounded-lg flex items-center justify-center transition-all">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
            </div>
            <div class="w-10"></div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, defineEmits } from 'vue';
import axios from 'axios';

defineEmits(['goHome']);

const currentFolder = ref(null);
const addMode = ref(false);
const items = ref([]);
const users = ref([]);
const loading = ref(true);
const editingId = ref(null);

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

const saveItem = async () => {
    try {
        if (editingId.value) {
            await axios.put(`/grudges/${editingId.value}`, form.value);
        } else {
            await axios.post('/grudges', { ...form.value, status: currentFolder.value?.status });
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
    } else {
        currentFolder.value = null;
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
    return items.value.filter(i => i.status === currentFolder.value.status);
});

const formatDate = (d) => d ? new Date(d).toLocaleDateString('zh-TW') : '-';

onMounted(loadData);
</script>
