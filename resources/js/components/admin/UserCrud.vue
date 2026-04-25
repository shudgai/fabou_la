<template>
    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden pb-24 relative">
        <div class="p-6 border-b border-slate-50 flex justify-between items-center bg-slate-50/30">
            <div>
                <h2 class="text-2xl font-bold text-slate-800">帳號管理</h2>
                <p class="text-slate-500 text-sm">管理系統登入帳號、角色權限與人員連結</p>
            </div>
            <button @click="openCreate" class="bg-indigo-600 text-white px-6 py-2 rounded-xl font-bold hover:bg-indigo-700 transition-all active:scale-95 shadow-lg shadow-indigo-100 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 6v6m0 0v6m0-6h6m-6 0H6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                新增帳號
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="text-slate-400 text-[13px] uppercase tracking-widest border-b border-slate-50">
                        <th class="p-6 font-bold">使用者名稱 / Email</th>
                        <th class="p-6 font-bold">對應法號</th>
                        <th class="p-6 font-bold">角色權限</th>
                        <th class="p-6 font-bold text-right">操作</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <tr v-for="item in items" :key="item.id" class="hover:bg-slate-50/50 transition-colors group">
                        <td class="p-6">
                            <div class="text-[16px] font-bold text-slate-800">{{ item.name }}</div>
                            <div class="text-[12px] text-slate-400 font-mono">{{ item.email }}</div>
                        </td>
                        <td class="p-6 border-l border-slate-50">
                            <div v-if="item.dharma_name" class="flex items-center space-x-2">
                                <div class="w-2 h-2 rounded-full bg-emerald-400"></div>
                                <span class="text-[16px] font-bold text-slate-700">{{ item.dharma_name.name }}</span>
                            </div>
                            <span v-else class="text-slate-300 text-sm">未連結法號</span>
                        </td>
                        <td class="p-6">
                            <span :class="[
                                'px-2.5 py-1 rounded-full text-xs font-bold border',
                                item.role === 'admin' ? 'bg-rose-50 text-rose-600 border-rose-100' : 'bg-slate-50 text-slate-500 border-slate-100'
                            ]">
                                {{ item.role || 'user' }}
                            </span>
                        </td>
                        <td class="p-6 text-right space-x-2">
                            <button @click="openEdit(item)" class="p-2 text-slate-400 hover:text-indigo-600 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </button>
                            <button @click="confirmDelete(item)" class="p-2 text-slate-400 hover:text-rose-600 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Form Modal -->
        <div v-if="showModal" class="fixed inset-0 z-[1000] flex items-center justify-center p-4 sm:p-6">
            <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="showModal = false"></div>
            <div class="relative bg-white rounded-[32px] shadow-2xl w-full max-w-md max-h-[85vh] overflow-y-auto custom-scrollbar animate-fade-in border border-slate-100">
                <div class="p-6 md:p-8">
                    <h3 class="text-2xl font-bold text-slate-900 mb-6 sticky top-0 bg-white z-10 py-2">{{ editMode ? '編輯帳號' : '新增帳號' }}</h3>
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-1.5">
                                <label class="text-xs font-bold text-slate-400 uppercase tracking-widest ml-1">顯示名稱</label>
                                <input v-model="form.name" type="text" class="w-full bg-slate-50 border-none rounded-2xl px-5 py-3.5 text-slate-800 font-bold" placeholder="姓名">
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-xs font-bold text-slate-400 uppercase tracking-widest ml-1">權限角色</label>
                                <select v-model="form.role" class="w-full bg-slate-50 border-none rounded-2xl px-5 py-3.5 text-slate-800 font-bold">
                                    <option value="user">一般使用者 (User)</option>
                                    <option value="admin">管理員 (Admin)</option>
                                </select>
                            </div>
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-400 uppercase tracking-widest ml-1">登入 Email</label>
                            <input v-model="form.email" type="email" class="w-full bg-slate-50 border-none rounded-2xl px-5 py-3.5 text-slate-800" placeholder="example@mail.com">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-400 uppercase tracking-widest ml-1">設定密碼 (留空則不修改)</label>
                            <input v-model="form.password" type="password" class="w-full bg-slate-50 border-none rounded-2xl px-5 py-3.5 text-slate-800" placeholder="********">
                        </div>
                        <div class="space-y-1.5 border-t border-slate-50 pt-4">
                            <label class="text-xs font-bold text-indigo-400 uppercase tracking-widest ml-1">連結法號 (對應身分)</label>
                            <select v-model="form.dharma_name_id" class="w-full bg-indigo-50/50 border-none rounded-2xl px-5 py-3.5 text-slate-800 font-bold">
                                <option :value="null">-- 無對應法號 --</option>
                                <option v-for="dn in dharmaNames" :key="dn.id" :value="dn.id">{{ dn.name }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-8 flex space-x-3 sticky bottom-0 bg-white py-2">
                        <button @click="showModal = false" class="flex-1 py-4 rounded-2xl font-bold text-slate-400 hover:bg-slate-50 transition-colors">取消</button>
                        <button @click="save" :disabled="loading" class="flex-1 py-4 bg-indigo-600 text-white rounded-2xl font-bold hover:bg-indigo-700 transition-all active:scale-95 shadow-lg shadow-indigo-100 flex justify-center items-center">
                            <span v-if="loading" class="w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
                            <span v-else>確認儲存</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <mobile-navbar 
            :can-back="true"
            :show-action="true"
            @back="$emit('goHome')"
            @home="$emit('goHome')"
            @action="openCreate"
        />
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import MobileNavbar from '../MobileNavbar.vue';

const items = ref([]);
const dharmaNames = ref([]);
const loading = ref(false);
const showModal = ref(false);
const editMode = ref(false);
const form = ref({ id: null, name: '', email: '', password: '', role: 'user', dharma_name_id: null });

const load = async () => {
    const [res, dres] = await Promise.all([
        axios.get('/users'),
        axios.get('/api/dharma-names-list')
    ]);
    items.value = res.data;
    dharmaNames.value = dres.data;
};

const openCreate = () => {
    editMode.value = false;
    form.value = { id: null, name: '', email: '', password: '', role: 'user', dharma_name_id: null };
    showModal.value = true;
};

const openEdit = (item) => {
    editMode.value = true;
    form.value = { ...item, password: '' };
    showModal.value = true;
};

const save = async () => {
    loading.value = true;
    try {
        if (editMode.value) {
            await axios.patch(`/users/${form.value.id}`, form.value);
        } else {
            await axios.post('/users', form.value);
        }
        showModal.value = false;
        load();
    } catch (e) {
        alert('儲存失敗：' + (e.response?.data?.message || '資料重複或格式錯誤'));
    } finally {
        loading.value = false;
    }
};

const confirmDelete = async (item) => {
    if (confirm(`確定要刪除帳號「${item.name}」嗎？`)) {
        try {
            await axios.delete(`/users/${item.id}`);
            load();
        } catch (e) {
            alert(e.response?.data?.error || '刪除失敗');
        }
    }
};

onMounted(load);
</script>
