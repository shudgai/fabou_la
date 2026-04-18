<template>
    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="p-6 border-b border-slate-50 flex justify-between items-center bg-slate-50/30">
            <div>
                <h2 class="text-2xl font-bold text-slate-800">法寶定義管理</h2>
                <p class="text-slate-500 text-sm">維護全系統可選用之法寶/金丹/符咒主檔</p>
            </div>
            <button @click="openCreate" class="bg-indigo-600 text-white px-6 py-2 rounded-xl font-bold hover:bg-indigo-700 transition-all active:scale-95 shadow-lg shadow-indigo-100 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 6v6m0 0v6m0-6h6m-6 0H6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                新增定義
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="text-slate-400 text-[13px] uppercase tracking-widest border-b border-slate-50">
                        <th class="p-6 font-bold">法寶名稱</th>
                        <th class="p-6 font-bold">所屬仙師</th>
                        <th class="p-6 font-bold">分類類型</th>
                        <th class="p-6 font-bold text-right">操作</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <tr v-for="item in items" :key="item.id" class="hover:bg-slate-50/50 transition-colors group">
                        <td class="p-6 font-bold text-slate-800">{{ item.name }}</td>
                        <td class="p-6">
                            <span v-if="item.master" class="text-slate-600">{{ item.master.name }}</span>
                            <span v-else class="text-slate-300 italic text-sm">通用</span>
                        </td>
                        <td class="p-6">
                            <span :class="[
                                'px-2 py-0.5 rounded text-[11px] font-bold tracking-tight',
                                typeMap[item.type]?.class || 'bg-slate-100 text-slate-500'
                            ]">
                                {{ typeMap[item.type]?.label || item.type }}
                            </span>
                        </td>
                        <td class="p-6 text-right space-x-2">
                            <button @click="openEdit(item)" class="p-1 text-slate-300 hover:text-indigo-600"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></button>
                            <button @click="confirmDelete(item)" class="p-1 text-slate-300 hover:text-rose-600"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <!-- Form Modal -->
        <div v-if="showModal" class="fixed inset-0 z-[1000] flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" @click="showModal = false"></div>
            <div class="relative bg-white rounded-[32px] shadow-2xl w-full max-w-lg overflow-hidden animate-fade-in">
                <div class="p-8">
                    <h3 class="text-2xl font-bold text-slate-900 mb-6 font-outfit">{{ editMode ? '編輯法寶定義' : '新增法寶定義' }}</h3>
                    <div class="space-y-4">
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-400 uppercase tracking-widest ml-1">法寶/主檔名稱</label>
                            <input v-model="form.name" type="text" class="w-full bg-slate-50 border-none rounded-2xl px-5 py-3.5 text-slate-800 font-bold" placeholder="例如：洗髓金丹">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-1.5">
                                <label class="text-xs font-bold text-slate-400 uppercase tracking-widest ml-1">所屬仙師</label>
                                <select v-model="form.master_id" class="w-full bg-slate-50 border-none rounded-2xl px-5 py-3.5 text-slate-800 font-bold">
                                    <option :value="null">-- 通用 / 未設定 --</option>
                                    <option v-for="m in masters" :key="m.id" :value="m.id">{{ m.name }}</option>
                                </select>
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-xs font-bold text-slate-400 uppercase tracking-widest ml-1">分類類型</label>
                                <select v-model="form.type" class="w-full bg-slate-50 border-none rounded-2xl px-5 py-3.5 text-slate-800 font-bold">
                                    <option v-for="(v, k) in typeMap" :key="k" :value="k">{{ v.label }}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mt-8 flex space-x-3">
                        <button @click="showModal = false" class="flex-1 py-4 rounded-2xl font-bold text-slate-400 hover:bg-slate-50 transition-colors">取消</button>
                        <button @click="save" :disabled="loading" class="flex-1 py-4 bg-indigo-600 text-white rounded-2xl font-bold hover:bg-indigo-700 transition-all active:scale-95 flex justify-center items-center">
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
const masters = ref([]);
const loading = ref(false);
const showModal = ref(false);
const editMode = ref(false);
const form = ref({ id: null, name: '', master_id: null, type: 'teaching' });

const typeMap = {
    'teaching': { label: '開示法寶', class: 'bg-indigo-50 text-indigo-500 border border-indigo-100' },
    'pill': { label: '金丹藥品', class: 'bg-amber-50 text-amber-600 border border-amber-100' },
    'talisman': { label: '符咒靈符', class: 'bg-emerald-50 text-emerald-600 border border-emerald-100' },
    'incense': { label: '香品法物', class: 'bg-rose-50 text-rose-500 border border-rose-100' },
    'service': { label: '事務服務', class: 'bg-slate-50 text-slate-500 border border-slate-100' },
    'registry': { label: '正式登記(重大)', class: 'bg-blue-50 text-blue-600 border border-blue-100 font-black' }
};

const load = async () => {
    const [res, mres] = await Promise.all([
        axios.get('/treasures'),
        axios.get('/api/masters-list')
    ]);
    items.value = res.data;
    masters.value = mres.data;
};

const openCreate = () => {
    editMode.value = false;
    form.value = { id: null, name: '', master_id: null, type: 'teaching' };
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
            await axios.patch(`/treasures/${form.value.id}`, form.value);
        } else {
            await axios.post('/treasures', form.value);
        }
        showModal.value = false;
        load();
    } catch (e) {
        alert('儲存失敗');
    } finally {
        loading.value = false;
    }
};

const confirmDelete = async (item) => {
    if (confirm(`確定要刪除「${item.name}」主檔嗎？`)) {
        await axios.delete(`/treasures/${item.id}`);
        load();
    }
};

onMounted(load);
</script>
