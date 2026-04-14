<template>
    <div class="bg-white min-h-screen relative">
        <!-- Header -->
        <div class="border-b border-gray-100 flex items-center bg-white sticky top-0 z-10" style="padding: 12px 10px 10px 10px;">
            <button @click="handleBack" class="mr-3 text-indigo-600 font-normal">
                &lt; 返回
            </button>
            <h2 class="text-xl font-normal text-slate-800">
                {{ currentFolder ? '父皇仙師 - ' + currentFolder.name : '父皇仙師開示' }}
            </h2>
        </div>

        <!-- Level 1: Folder Selection -->
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
