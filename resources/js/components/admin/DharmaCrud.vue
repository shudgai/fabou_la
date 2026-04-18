<template>
    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="p-6 border-b border-slate-50 flex justify-between items-center bg-slate-50/30">
            <div>
                <h2 class="text-2xl font-bold text-slate-800">法號管理</h2>
                <p class="text-slate-500 text-sm">維護人員法號、排序及別名設定</p>
            </div>
            <button @click="openCreate" class="bg-indigo-600 text-white px-6 py-2 rounded-xl font-bold hover:bg-indigo-700 transition-all active:scale-95 shadow-lg shadow-indigo-100 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 6v6m0 0v6m0-6h6m-6 0H6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                新增法號
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="text-slate-400 text-[13px] uppercase tracking-widest border-b border-slate-50">
                        <th class="p-6 font-bold">排序</th>
                        <th class="p-6 font-bold">法號</th>
                        <th class="p-6 font-bold">別名/備註</th>
                        <th class="p-6 font-bold">群組數</th>
                        <th class="p-6 font-bold text-right">操作</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <tr v-for="item in items" :key="item.id" class="hover:bg-slate-50/50 transition-colors group">
                        <td class="p-6 font-mono text-slate-400 text-sm">{{ item.order }}</td>
                        <td class="p-6">
                            <div class="text-[17px] font-bold text-slate-800">{{ item.name }}</div>
                        </td>
                        <td class="p-6 text-slate-500">{{ item.alias || '-' }}</td>
                        <td class="p-6">
                            <span class="bg-indigo-50 text-indigo-600 px-2.5 py-1 rounded-full text-xs font-bold border border-indigo-100">
                                {{ item.groups?.length || 0 }} 個群組
                            </span>
                        </td>
                        <td class="p-6 text-right">
                            <div class="flex justify-end space-x-2">
                                <button @click="openEdit(item)" class="p-2 text-slate-400 hover:text-indigo-600 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </button>
                                <button @click="confirmDelete(item)" class="p-2 text-slate-400 hover:text-rose-600 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Form Modal -->
        <div v-if="showModal" class="fixed inset-0 z-[1000] flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="showModal = false"></div>
            <div class="relative bg-white rounded-[32px] shadow-2xl w-full max-w-md max-h-[85vh] overflow-y-auto custom-scrollbar animate-fade-in">
                <div class="p-8">
                    <h3 class="text-2xl font-bold text-slate-900 mb-6 sticky top-0 bg-white z-10 py-2">{{ editMode ? '編輯法號' : '新增法號' }}</h3>
                    <div class="space-y-5">
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-400 uppercase tracking-widest ml-1">法號內容</label>
                            <input v-model="form.name" type="text" class="w-full bg-slate-50 border-none rounded-2xl px-5 py-4 text-slate-800 placeholder:text-slate-300 focus:ring-2 focus:ring-indigo-100 transition-all font-bold" placeholder="例如：鳳尊">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-400 uppercase tracking-widest ml-1">別名/備註</label>
                            <input v-model="form.alias" type="text" class="w-full bg-slate-50 border-none rounded-2xl px-5 py-4 text-slate-800 placeholder:text-slate-300 focus:ring-2 focus:ring-indigo-100 transition-all" placeholder="選填">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-400 uppercase tracking-widest ml-1">顯示排序 (越小越前面)</label>
                            <input v-model="form.order" type="number" class="w-full bg-slate-50 border-none rounded-2xl px-5 py-4 text-slate-800 focus:ring-2 focus:ring-indigo-100 transition-all">
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
const loading = ref(false);
const showModal = ref(false);
const editMode = ref(false);
const form = ref({ id: null, name: '', alias: '', order: 100 });

const load = async () => {
    const res = await axios.get('/api/dharma-names-list');
    items.value = res.data;
};

const openCreate = () => {
    editMode.value = false;
    form.value = { id: null, name: '', alias: '', order: items.value.length ? Math.max(...items.value.map(i => i.order)) + 10 : 100 };
    showModal.value = true;
};

const openEdit = (item) => {
    editMode.value = true;
    form.value = { ...item };
    showModal.value = true;
};

const save = async () => {
    loading.value = true;
    try {
        if (editMode.value) {
            await axios.patch(`/dharma-names/${form.value.id}`, form.value);
        } else {
            await axios.post('/dharma-names', form.value);
        }
        showModal.value = false;
        load();
    } catch (e) {
        alert('儲存失敗：' + (e.response?.data?.message || '未知錯誤'));
    } finally {
        loading.value = false;
    }
};

const confirmDelete = async (item) => {
    if (confirm(`確定要刪除「${item.name}」嗎？此動作無法復原。`)) {
        await axios.delete(`/dharma-names/${item.id}`);
        load();
    }
};

onMounted(load);
</script>
