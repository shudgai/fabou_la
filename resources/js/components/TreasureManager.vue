<template>
    <div class="bg-white min-h-screen relative">
        <!-- Header -->
        <div class="border-b border-gray-100 flex items-center bg-white sticky top-0 z-10" style="padding: 12px 10px 10px 10px;">
            <button @click="handleBack" class="mr-3 text-indigo-600 font-normal">
                &lt; 返回
            </button>
            <h2 class="text-xl font-normal text-slate-800">
                {{ currentFolder ? '法寶登記 - ' + currentFolder.name : '法寶登記' }}
            </h2>
        </div>


        <!-- Level 1: Folder Selection -->
        <div v-if="!currentFolder" class="flex flex-col">
            <button v-for="folder in folders" :key="folder.id" 
                @click="currentFolder = folder"
                class="flex items-center justify-between w-full border-b border-gray-50 active:bg-gray-50 transition-colors"
                style="padding: 10px 15px;">
                <span class="text-lg font-normal text-slate-800">
                    {{ folder.name }}
                </span>
                <span class="text-slate-300">&gt;</span>
            </button>
        </div>

        <!-- Level 2: Folder Contents -->
        <div v-else class="pb-32">
            <!-- ADD SINGLE FORM (Clean White) -->
            <div v-if="addMode === 'single'" class="mb-6 bg-white border-b border-gray-100 shadow-sm animate-fade-in" style="padding: 10px;">
                <div class="grid grid-cols-2 gap-1.5 mb-1.5">
                    <div>
                        <label class="text-xs text-slate-500 font-normal mb-1 block">日期</label>
                        <input v-model="form.record_date" type="date" class="w-full rounded-xl border-none shadow-sm text-sm" style="padding: 5px 8px;">
                    </div>
                    <div>
                        <label class="text-xs text-slate-500 font-normal mb-1 block">仙師</label>
                        <select v-model="form.master_id" class="w-full rounded-xl border-none shadow-sm text-sm bg-white" style="padding: 5px 8px;">
                            <option v-for="m in masters" :key="m.id" :value="m.id">{{ m.name }}</option>
                        </select>
                    </div>
                </div>
                <div class="mb-1.5">
                    <label class="text-xs text-slate-500 font-normal mb-1 block">法寶名稱</label>
                    <input v-model="form.name" type="text" placeholder="輸入法寶名稱" class="w-full rounded-xl border-none shadow-sm text-sm" style="padding: 5px 8px;">
                </div>
                <div class="mb-1.5">
                    <label class="text-xs text-slate-500 font-normal mb-1 block">法寶用意</label>
                    <input v-model="form.purpose" type="text" placeholder="輸入法寶用意" class="w-full rounded-xl border-none shadow-sm text-sm" style="padding: 5px 8px;">
                </div>
                <div class="mb-1.5">
                    <label class="text-xs text-slate-500 font-normal mb-1 block">求寶方式</label>
                    <input v-model="form.acquisition_method" type="text" placeholder="輸入求寶方式" class="w-full rounded-xl border-none shadow-sm text-sm" style="padding: 5px 8px;">
                </div>
                <div class="mb-1.5">
                    <label class="text-xs text-slate-500 font-normal mb-1 block">備註</label>
                    <input v-model="form.remarks" type="text" placeholder="輸入備註 (選填)" class="w-full rounded-xl border-none shadow-sm text-sm" style="padding: 5px 8px;">
                </div>
                <div class="flex justify-center mt-2">
                    <button @click="saveSingle" class="bg-indigo-600 text-white font-normal rounded-lg shadow-md hover:bg-indigo-700 transition-all uppercase" style="padding: 5px 10px; font-size: 14px;">{{ form.id ? '修改完成' : '確認新增' }}</button>
                </div>
            </div>

            <!-- List Display -->
            <div v-if="!addMode" style="padding: 8px;">
                <div v-if="loading" class="text-center py-4 text-xs text-slate-400">載入中...</div>
                <div v-else-if="filteredTreasures.length === 0" class="text-center py-12 text-slate-400">
                    目前該專區尚無法寶資料。
                </div>
                <div v-else class="flex flex-col">
                    <div v-for="item in filteredTreasures" :key="item.id" class="border-b border-dashed border-slate-200 py-[6px] last:border-b-0 relative">
                        <!-- Row 1: Dates & Actions -->
                        <div class="flex items-start justify-between mb-0">
                            <div class="flex items-center space-x-3 pt-1">
                                <span class="text-[11px] text-slate-400">
                                    <span class="font-normal">日期:</span> {{ formatDate(item.record_date) }}
                                </span>
                            </div>
                            
                            <div class="flex items-center">
                                <!-- Menu Button -->
                                <button @click.stop="toggleMenu(item.id)" class="p-1 text-red-500 hover:text-red-700">
                                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM18 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                </button>

                                <!-- Dropdown Menu -->
                                <div v-if="openMenuId === item.id" 
                                    class="absolute right-0 mt-8 w-40 bg-white rounded-xl shadow-2xl border border-slate-100 z-[100] overflow-hidden animate-slide-up">
                                    <button @click.stop="toggleExpand(item.id)" class="w-full px-4 py-2 text-left text-xs font-normal text-slate-600 hover:bg-slate-50 border-b border-slate-50">
                                        {{ expandedIds.has(item.id) ? '縮起清單' : '展開清單' }}
                                    </button>
                                    <button @click.stop="editItem(item)" class="w-full px-4 py-2 text-left text-xs font-normal text-slate-600 hover:bg-slate-50 border-b border-slate-50">修改</button>
                                    <button @click.stop="deleteItem(item.id)" class="w-full px-4 py-2 text-left text-xs font-normal text-red-600 hover:bg-red-50">刪除</button>
                                </div>
                            </div>
                        </div>

                        <!-- Row 2: Name -->
                        <div class="text-[17px] font-normal text-slate-900 mb-1 leading-tight mt-[-15px]">{{ item.name }}</div>

                        <!-- Collapsible Rows -->
                        <div v-if="expandedIds.has(item.id)" class="animate-fade-in bg-slate-50 rounded p-2 mt-1">
                            <div class="text-sm text-slate-600 mb-1"><span class="text-slate-400">用意：</span>{{ item.purpose || '無' }}</div>
                            <div class="text-sm text-slate-600 mb-1"><span class="text-slate-400">求寶方式：</span>{{ item.acquisition_method || '無' }}</div>
                            <div v-if="item.remarks" class="text-sm text-slate-500"><span class="text-slate-400">備註：</span>{{ item.remarks }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- STICKY BOTTOM NAV BAR -->
        <div class="fixed bottom-0 left-0 right-0 bg-white border-t border-slate-100 flex justify-around items-center z-50 shadow-sm" style="height: 44px; padding: 0 20px;">
            <button @click="$emit('goHome')" class="text-slate-400 p-2 active:scale-95 transition-all">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
            <div class="relative">
                <button 
                    @click="prepareAdd"
                    :disabled="!currentFolder"
                    :class="[!currentFolder ? 'bg-gray-100 text-gray-300 opacity-50' : 'bg-indigo-600 text-white shadow-lg active:scale-95']"
                    class="w-7 h-7 rounded-lg flex items-center justify-center transition-all">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
            </div>
            <button class="text-slate-400 p-2 opacity-20 cursor-not-allowed">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, defineEmits } from 'vue';
import axios from 'axios';

defineEmits(['goHome']);

const currentFolder = ref(null);
const addMode = ref(null);
const allTreasures = ref([]);
const masters = ref([]);
const loading = ref(false);
const persistentToast = ref(null);
const isSaving = ref(false);

const openMenuId = ref(null);
const expandedIds = ref(new Set());

const form = ref({
    master_id: null,
    name: '',
    purpose: '',
    acquisition_method: '',
    remarks: '',
    record_date: new Date().toISOString().split('T')[0],
    obtained_date: ''
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

const loadData = async () => {
    loading.value = true;
    try {
        const [res, mres] = await Promise.all([
            axios.get('/treasures'),
            axios.get('/api/masters-list')
        ]);
        allTreasures.value = res.data;
        masters.value = mres.data;
    } catch (e) {
        console.error(e);
    } finally {
        loading.value = false;
    }
};

onMounted(loadData);

const handleBack = () => {
    if (addMode.value) {
        addMode.value = null;
    } else {
        currentFolder.value = null;
    }
};

const prepareAdd = () => {
    saveSuccess.value = false;
    form.value = {
        id: null,
        master_id: currentFolder.value ? currentFolder.value.id : null,
        name: '',
        purpose: '',
        acquisition_method: '',
        remarks: '',
        record_date: new Date().toISOString().split('T')[0],
        obtained_date: ''
    };
    addMode.value = 'single';
};

const toggleMenu = (id) => {
    openMenuId.value = openMenuId.value === id ? null : id;
};

const toggleExpand = (id) => {
    if (expandedIds.value.has(id)) {
        expandedIds.value.delete(id);
    } else {
        expandedIds.value.add(id);
    }
    openMenuId.value = null;
};

const editItem = (item) => {
    form.value = { ...item };
    addMode.value = 'single';
    openMenuId.value = null;
};

const deleteItem = async (id) => {
    if (!window.confirm('確定要刪除這筆法寶登記嗎？')) return;
    try {
        await axios.delete(`/treasures/${id}`);
        showToast('刪除成功');
        loadData();
    } catch (e) { alert('刪除失敗'); }
    openMenuId.value = null;
};

const saveSingle = async () => {
    // 最終強制比對邏輯 (使用 Number 強制轉型)
    if (currentFolder.value) {
        const formMid = Number(form.value.master_id);
        const folderId = Number(currentFolder.value.id);

        if (formMid !== folderId) {
            const targetM = masters.value.find(m => Number(m.id) === formMid);
            const targetName = targetM ? targetM.name : '其他仙師';
            const currentName = currentFolder.value.name;

            alert(`【仙師不符警告】\n您選的是「${targetName}」，但目前標題是「${currentName}」！`);
            if (!window.confirm(`確定要將資料存入「${targetName}」名下嗎？`)) {
                return;
            }
        }
    }

    if (!form.value.name) return alert('請輸入法寶名稱');
    
    // 日期驗證
    if (['已登記', '已求得'].includes(form.value.status)) {
        if (!form.value.obtained_date) {
            return alert(`狀態為「${form.value.status}」時，必須輸入日期！`);
        }
    }

    // Check for duplicate
    const isDuplicate = allTreasures.value.some(t => 
        t.name.trim() === form.value.name.trim() && t.id != form.value.id
    );
    if (isDuplicate) return alert('此法寶名稱已存在於登記冊中');

    try {
        if (form.value.id) {
            await axios.put(`/treasures/${form.value.id}`, form.value);
        } else {
            const data = { 
                ...form.value, 
                master_id: currentFolder.value ? currentFolder.value.id : form.value.master_id 
            };
            await axios.post('/treasures', data);
        }
        addMode.value = null;
        loadData();
    } catch (e) { alert('儲存失敗'); }
};

const filteredTreasures = computed(() => {
    if (!currentFolder.value) return [];
    return allTreasures.value.filter(t => t.master_id === currentFolder.value.id);
});

const downloadList = () => {
    if (!currentFolder.value) return;
    const list = filteredTreasures.value.map(t => 
        `[${formatDate(t.record_date)}] ${t.name} - ${t.purpose || '無'}`
    ).join('\n');
    const contents = `法寶登記清單 - ${currentFolder.value.name}\n\n${list}`;
    const blob = new Blob(["\ufeff" + contents], { type: 'text/plain;charset=utf-8' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `法寶登記_${currentFolder.value.name}.txt`;
    a.click();
    openMenuId.value = null;
};

const formatDate = (d) => d ? new Date(d).toLocaleDateString('zh-TW') : '-';

onMounted(() => {
    window.addEventListener('click', (e) => {
        if (openMenuId.value && !e.target.closest('.relative')) {
            openMenuId.value = null;
        }
    });
});
</script>

<style scoped>
.animate-toast-in { animation: toastScaleIn 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
@keyframes toastScaleIn {
    from { opacity: 0; transform: translate(-50%, -20px) scale(0.9); }
    to { opacity: 1; transform: translate(-50%, 0) scale(1); }
}
.animate-fade-in { animation: fadeIn 0.3s ease-out; }
.animate-slide-up { animation: slideUp 0.15s ease-out; }
@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
@keyframes slideUp { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
</style>
