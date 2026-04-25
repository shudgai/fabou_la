<template>
    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden pb-24 relative">
        <div class="p-6 border-b border-slate-50 flex justify-between items-center bg-slate-50/30">
            <div>
                <h2 class="text-2xl font-bold text-slate-800">仙師主檔管理</h2>
                <p class="text-slate-500 text-sm">維護系統分類核心之仙師名單</p>
            </div>
            <button @click="openCreate" class="bg-indigo-600 text-white px-6 py-2 rounded-xl font-bold hover:bg-indigo-700 transition-all active:scale-95 shadow-lg shadow-indigo-100 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 6v6m0 0v6m0-6h6m-6 0H6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                新增仙師
            </button>
        </div>

        <div class="p-6 grid grid-cols-2 md:grid-cols-4 gap-6 bg-slate-50/50">
            <div v-for="item in items" :key="item.id" 
                class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm flex flex-col items-center text-center group cursor-pointer relative overflow-hidden">
                <div class="absolute inset-0 bg-indigo-600/0 group-hover:bg-indigo-600/5 transition-colors"></div>
                <div class="w-16 h-16 bg-slate-50 rounded-2xl flex items-center justify-center mb-4 text-slate-400 group-hover:text-indigo-600 transition-colors">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </div>
                <h3 class="text-[19px] font-bold text-slate-800">{{ item.name }}</h3>
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-tighter mt-1">{{ item.category }}</span>
                
                <div class="mt-4 flex space-x-2 opacity-0 group-hover:opacity-100 transition-opacity z-10">
                    <button @click="openEdit(item)" class="text-xs font-bold text-indigo-600 underline">編輯</button>
                    <button @click="confirmDelete(item)" class="text-xs font-bold text-rose-500 underline">刪除</button>
                </div>
            </div>
        </div>

        <!-- Form Modal -->
        <div v-if="showModal" class="fixed inset-0 z-[1000] flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="showModal = false"></div>
            <div class="relative bg-white rounded-[32px] shadow-2xl w-full max-w-md max-h-[85vh] overflow-y-auto custom-scrollbar animate-fade-in">
                <div class="p-8">
                    <h3 class="text-2xl font-bold text-slate-900 mb-6 sticky top-0 bg-white z-10 py-2">{{ editMode ? '編輯仙師' : '新增仙師' }}</h3>
                    <div class="space-y-5">
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-400 uppercase tracking-widest ml-1">仙師標題/名稱</label>
                            <input v-model="form.name" type="text" class="w-full bg-slate-50 border-none rounded-2xl px-5 py-4 text-slate-800 font-bold" placeholder="例如：延平郡王">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-400 uppercase tracking-widest ml-1">屬性分類</label>
                            <select v-model="form.category" class="w-full bg-slate-50 border-none rounded-2xl px-5 py-4 text-slate-800 font-bold focus:ring-2 focus:ring-indigo-100 transition-all">
                                <option value="imperial">重大皇恩 (Imperial)</option>
                                <option value="others">其餘皇恩 (Others)</option>
                                <option value="teaching">仙師開示 (Teaching)</option>
                            </select>
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
const loading = ref(false);
const showModal = ref(false);
const editMode = ref(false);
const form = ref({ id: null, name: '', category: 'imperial' });

const load = async () => {
    const res = await axios.get('/masters');
    items.value = res.data;
};

const openCreate = () => {
    editMode.value = false;
    form.value = { id: null, name: '', category: 'imperial' };
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
            await axios.patch(`/masters/${form.value.id}`, form.value);
        } else {
            await axios.post('/masters', form.value);
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
    if (confirm(`確定要刪除「${item.name}」嗎？`)) {
        await axios.delete(`/masters/${item.id}`);
        load();
    }
};

onMounted(load);
</script>
