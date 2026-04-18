<template>
    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="p-6 border-b border-slate-50 flex justify-between items-center bg-slate-50/30">
            <div>
                <h2 class="text-2xl font-bold text-slate-800">群組/職務管理</h2>
                <p class="text-slate-500 text-sm">維護系統群組清單與人員歸屬關係</p>
            </div>
            <button @click="openCreate" class="bg-indigo-600 text-white px-6 py-2 rounded-xl font-bold hover:bg-indigo-700 transition-all active:scale-95 shadow-lg shadow-indigo-100 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 6v6m0 0v6m0-6h6m-6 0H6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                新增群組
            </button>
        </div>

        <div class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 bg-slate-50/50">
            <div v-for="item in items" :key="item.id" 
                class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm hover:shadow-md transition-all group relative">
                <div class="flex justify-between items-start mb-3">
                    <div class="bg-indigo-50 text-indigo-600 w-10 h-10 rounded-xl flex items-center justify-center font-bold">
                        {{ item.name.charAt(0) }}
                    </div>
                    <div class="flex space-x-1 opacity-0 group-hover:opacity-100 transition-opacity">
                        <button @click="openEdit(item)" class="p-1.5 text-slate-400 hover:text-indigo-600"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></button>
                        <button @click="confirmDelete(item)" class="p-1.5 text-slate-400 hover:text-rose-600"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></button>
                    </div>
                </div>
                <h3 class="text-[18px] font-bold text-slate-800 mb-1">{{ item.name }}</h3>
                <div class="flex items-center text-slate-400 text-sm">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    {{ item.dharma_names_count || 0 }} 人成員
                </div>
            </div>
        </div>

        <!-- Form Modal -->
        <div v-if="showModal" class="fixed inset-0 z-[1000] flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="showModal = false"></div>
            <div class="relative bg-white rounded-[32px] shadow-2xl w-full max-w-md max-h-[85vh] overflow-y-auto custom-scrollbar animate-fade-in">
                <div class="p-8">
                    <h3 class="text-2xl font-bold text-slate-900 mb-6 sticky top-0 bg-white z-10 py-2">{{ editMode ? '編輯群組' : '新增群組' }}</h3>
                    <div class="space-y-6">
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-400 uppercase tracking-widest ml-1">群組名稱</label>
                            <input v-model="form.name" type="text" class="w-full bg-slate-50 border-none rounded-2xl px-5 py-4 text-slate-800 font-bold" placeholder="例如：世代交替">
                        </div>

                        <!-- People Selection -->
                        <div class="space-y-3 pt-4 border-t border-slate-50">
                            <label class="text-xs font-bold text-indigo-400 uppercase tracking-widest ml-1">群組成員管理</label>
                            <div class="bg-slate-50 rounded-2xl p-4 max-h-[40vh] overflow-y-auto custom-scrollbar grid grid-cols-2 gap-2">
                                <label v-for="dn in dharmaNames" :key="dn.id" 
                                    class="flex items-center space-x-2 p-2 hover:bg-white rounded-lg cursor-pointer transition-colors group">
                                    <input type="checkbox" :value="dn.id" v-model="form.dharma_name_ids" 
                                        class="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-100">
                                    <span class="text-sm font-bold text-slate-600 group-hover:text-indigo-600">{{ dn.name }}</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="mt-10 flex space-x-3 sticky bottom-0 bg-white py-2">
                        <button @click="showModal = false" class="flex-1 py-4 rounded-2xl font-bold text-slate-400 hover:bg-slate-50 transition-colors">取消</button>
                        <button @click="save" :disabled="loading" class="flex-1 py-4 bg-indigo-600 text-white rounded-2xl font-bold hover:bg-indigo-700 transition-all active:scale-95 shadow-lg shadow-indigo-100 flex justify-center items-center">
                            <span v-if="loading" class="w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
                            <span v-else>確認儲存</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const items = ref([]);
const dharmaNames = ref([]);
const loading = ref(false);
const showModal = ref(false);
const editMode = ref(false);
const form = ref({ id: null, name: '', dharma_name_ids: [] });

const load = async () => {
    const [gres, dres] = await Promise.all([
        axios.get('/groups'),
        axios.get('/api/dharma-names-list')
    ]);
    items.value = gres.data;
    dharmaNames.value = dres.data;
};

const openCreate = () => {
    editMode.value = false;
    form.value = { id: null, name: '', dharma_name_ids: [] };
    showModal.value = true;
};

const openEdit = async (item) => {
    editMode.value = true;
    loading.value = true;
    try {
        const res = await axios.get(`/groups/${item.id}`);
        form.value = { 
            id: res.data.id, 
            name: res.data.name, 
            dharma_name_ids: res.data.dharma_names.map(dn => dn.id)
        };
        showModal.value = true;
    } finally {
        loading.value = false;
    }
};

const save = async () => {
    loading.value = true;
    try {
        if (editMode.value) {
            await axios.patch(`/groups/${form.value.id}`, form.value);
        } else {
            await axios.post('/groups', form.value);
        }
        showModal.value = false;
        load();
    } catch (e) {
        alert('儲存失敗：' + (e.response?.data?.message || '名稱重複或錯誤'));
    } finally {
        loading.value = false;
    }
};

const confirmDelete = async (item) => {
    if (confirm(`確定要刪除群組「${item.name}」嗎？此群組的人員關聯將被移除。`)) {
        await axios.delete(`/groups/${item.id}`);
        load();
    }
};

onMounted(load);
</script>
