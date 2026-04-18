<template>
    <div class="bg-white h-[100dvh] flex flex-col relative overflow-hidden text-slate-900">
        <!-- Header -->
        <div v-if="currentFolder || currentCategory" class="border-b border-slate-300 flex items-center justify-center bg-white/80 backdrop-blur-md sticky top-0 z-10" style="padding: 12px 10px 10px 10px;">
            <h2 class="text-[20px] font-bold font-outfit tracking-tight text-slate-900 flex items-center">
                <span v-if="focusedId && displayTitle !== currentFolder?.name" class="text-indigo-600 truncate max-w-[200px] block">{{ displayTitle }}</span>
                <div class="flex flex-col items-center">
                    <span class="text-[14px] text-slate-400 font-normal leading-none mb-1">{{ currentFolder ? (currentCategory === 'major' ? '重大皇恩登記簿' : '其他皇恩登記簿') : '' }}</span>
                    <span>{{ focusedId ? (allTreasures.find(t => t.id === focusedId)?.name || '法寶登記') : (currentFolder ? (currentCategory === 'major' ? '重大皇恩登記' : '其他皇恩登記') + ' - ' + currentFolder.name : '法寶登記專區') }}</span>
                </div>
                <button v-if="currentFolder" @click="toggleSort" class="ml-2 px-1 text-[10px] text-indigo-500 font-normal bg-indigo-50 border border-indigo-100 rounded active:scale-95 transition-all opacity-80 tracking-tighter self-end mb-1">
                    ({{ sortDesc ? '新→舊' : '舊→新' }})
                </button>
            </h2>
        </div>

        <!-- Main Scrollable Area -->
        <div class="flex-1 overflow-y-auto custom-scrollbar" style="padding-bottom: 40px;">
            
            <!-- Category and Master Selection -->
            <div v-if="!currentFolder && !addMode" class="min-h-screen bg-white flex flex-col items-center">
                <!-- Titles and Category Buttons handled previously... -->
                <div class="w-full px-6 pt-8 pb-4 text-center">
                    <h1 class="text-[22px] font-bold text-slate-900 tracking-tight">
                        {{ currentCategory ? (currentCategory === 'major' ? '重大皇恩登記簿' : '其他皇恩登記簿') : '法寶登記專區' }}
                    </h1>
                </div>

                <!-- Root Categories -->
                <div v-if="!currentCategory" class="flex flex-col items-center space-y-6 mt-6 pb-20 w-full">
                    <button @click="currentCategory = 'major'" class="flex flex-col items-center justify-center bg-transparent active:scale-95 rounded-2xl border-2 border-[rgb(255,215,0)] p-4 w-[180px] h-[200px] relative transition-all shadow-sm">
                        <div class="mb-4">
                            <svg class="w-28 h-28 drop-shadow-md" viewBox="0 0 64 64" fill="none">
                                <defs>
                                    <linearGradient id="goldGradL1" x1="0%" y1="0%" x2="100%" y2="100%">
                                        <stop offset="0%" style="stop-color:rgb(255, 235, 120);stop-opacity:1" />
                                        <stop offset="50%" style="stop-color:rgb(255, 215, 0);stop-opacity:1" />
                                        <stop offset="100%" style="stop-color:rgb(218, 165, 32);stop-opacity:1" />
                                    </linearGradient>
                                </defs>
                                <path d="M4 14C4 11.7909 5.79086 10 8 10H24.5L30 16H56C58.2091 16 60 17.7909 60 20V50C60 52.2091 58.2091 54 56 54H8C5.79086 54 4 52.2091 4 50V14Z" fill="url(#goldGradL1)" opacity="0.8"/>
                                <path d="M4 22C4 19.7909 5.79086 18 8 18H56C58.2091 18 60 19.7909 60 22V50C60 52.2091 58.2091 54 56 54H8C5.79086 54 4 52.2091 4 50V22Z" fill="url(#goldGradL1)" stroke="rgba(255,255,255,0.4)" stroke-width="0.5"/>
                            </svg>
                        </div>
                        <div class="text-[19px] font-bold text-black leading-tight">重大皇恩登記簿</div>
                    </button>

                    <button @click="currentCategory = 'other'" class="flex flex-col items-center justify-center bg-transparent active:scale-95 rounded-2xl border-2 border-[rgb(255,215,0)] p-4 w-[180px] h-[200px] relative transition-all shadow-sm">
                        <div class="mb-4">
                            <svg class="w-28 h-28 drop-shadow-md" viewBox="0 0 64 64" fill="none">
                                <defs>
                                    <linearGradient id="goldGradL2" x1="0%" y1="0%" x2="100%" y2="100%">
                                        <stop offset="0%" style="stop-color:rgb(255, 235, 120);stop-opacity:1" />
                                        <stop offset="50%" style="stop-color:rgb(255, 215, 0);stop-opacity:1" />
                                        <stop offset="100%" style="stop-color:rgb(218, 165, 32);stop-opacity:1" />
                                    </linearGradient>
                                </defs>
                                <path d="M4 14C4 11.7909 5.79086 10 8 10H24.5L30 16H56C58.2091 16 60 17.7909 60 20V50C60 52.2091 58.2091 54 56 54H8C5.79086 54 4 52.2091 4 50V14Z" fill="url(#goldGradL2)" opacity="0.8"/>
                                <path d="M4 22C4 19.7909 5.79086 18 8 18H56C58.2091 18 60 19.7909 60 22V50C60 52.2091 58.2091 54 56 54H8C5.79086 54 4 52.2091 4 50V22Z" fill="url(#goldGradL2)" stroke="rgba(255,255,255,0.4)" stroke-width="0.5"/>
                            </svg>
                        </div>
                        <div class="text-[19px] font-bold text-black leading-tight">其他皇恩登記簿</div>
                    </button>
                </div>

                <!-- Masters Grid -->
                <div v-else class="grid grid-cols-2 gap-[10px] p-4 place-items-center">
                    <button v-for="folder in folders" :key="folder.id" 
                        @click="currentFolder = folder"
                        class="flex flex-col items-center justify-center transition-all active:scale-95 rounded-xl border border-[rgb(255,215,0)] group p-2 w-[120px] h-[135px] relative">
                        
                        <div class="relative mb-2">
                             <svg class="w-20 h-20 transition-transform group-hover:scale-110 drop-shadow-md" viewBox="0 0 64 64" fill="none">
                                <defs>
                                    <linearGradient id="folderGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                                        <stop offset="0%" style="stop-color:rgb(255, 235, 120);stop-opacity:1" />
                                        <stop offset="50%" style="stop-color:rgb(255, 215, 0);stop-opacity:1" />
                                        <stop offset="100%" style="stop-color:rgb(218, 165, 32);stop-opacity:1" />
                                    </linearGradient>
                                </defs>
                                <path d="M4 14C4 11.7909 5.79086 10 8 10H24.5L30 16H56C58.2091 16 60 17.7909 60 20V50C60 52.2091 58.2091 54 56 54H8C5.79086 54 4 52.2091 4 50V14Z" fill="url(#folderGrad)" opacity="0.8"/>
                                <path d="M4 22C4 19.7909 5.79086 18 8 18H56C58.2091 18 60 19.7909 60 22V50C60 52.2091 58.2091 54 56 54H8C5.79086 54 4 52.2091 4 50V22Z" fill="url(#folderGrad)" stroke="rgba(255,255,255,0.4)" stroke-width="0.5"/>
                            </svg>
                        </div>
                        <div class="text-center px-1">
                            <div class="text-[17px] font-bold leading-tight text-black">{{ folder.name }}</div>
                        </div>
                    </button>
                </div>

                <div class="mt-12 flex justify-center pb-32">
                    <button @click="$emit('goHome')" class="text-slate-300 hover:text-slate-500 transition-colors flex items-center space-x-2 active:scale-95">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" /></svg>
                        <span class="text-xs font-medium tracking-widest uppercase">返回專區列表</span>
                    </button>
                </div>
            </div>

            <!-- Folder Contents -->
            <div v-else class="px-0 bg-white">
                <div style="padding: 0px 8px 10px 8px;" class="mt-0">
                    <div v-if="loading" class="text-center py-4 text-xs text-slate-400">載入中...</div>
                        <!-- Unified Treasure List for Both Categories -->
                        <div v-for="item in filteredTreasures" :key="item.id" 
                            @click="toggleExpand(item.id)"
                            class="py-[10px] px-2 border-b border-slate-100 last:border-b-0 relative group transition-all cursor-pointer bg-white active:bg-slate-50">
                            
                            <!-- Collapsed View -->
                            <div v-if="!expandedIds.has(item.id)" class="flex flex-col pointer-events-none">
                                <div class="flex items-center justify-between mb-1">
                                    <div class="text-[13px] text-slate-400 font-medium">{{ item.record_date?.replace(/-/g, '/') || '-' }}</div>
                                    <div class="flex items-center space-x-2">
                                        <span class="text-[12px] px-2 py-0.5 rounded-full border"
                                              :class="item.status === '已登記' ? 'bg-emerald-50 text-emerald-600 border-emerald-100' : 'bg-blue-50 text-blue-600 border-blue-100'">
                                            {{ item.status }}
                                        </span>
                                    </div>
                                </div>
                                <div class="text-[18px] font-bold text-black leading-tight">{{ item.name }}</div>
                                
                                <!-- Personnel Tags -->
                                <div v-if="item.dharma_name_registries && item.dharma_name_registries.length" class="mt-2 flex flex-wrap gap-1">
                                    <span v-for="dnr in item.dharma_name_registries" :key="dnr.id" class="text-[13px] text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-md border border-indigo-100">
                                        {{ dnr.dharma_name ? dnr.dharma_name.name : dnr.custom_name }}
                                    </span>
                                </div>
                            </div>

                            <!-- Expanded View -->
                            <div v-if="expandedIds.has(item.id)" class="animate-fade-in py-2 space-y-3">
                                <div class="flex justify-between items-start">
                                    <div class="space-y-1">
                                        <label class="text-[12px] text-slate-400 uppercase tracking-widest font-bold">法寶名稱</label>
                                        <div class="text-[20px] font-bold text-black">{{ item.name }}</div>
                                    </div>
                                    <button @click.stop="openAndEdit(item.id)" class="px-3 py-1 bg-indigo-50 text-indigo-600 rounded-full text-[13px] font-bold border border-indigo-100 active:scale-95 transition-all">修改資料</button>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div class="space-y-1">
                                        <label class="text-[12px] text-slate-400 font-bold">登錄日期</label>
                                        <div class="text-[15px] text-slate-800">{{ item.record_date?.replace(/-/g, '/') }}</div>
                                    </div>
                                    <div v-if="item.acquisition_method" class="space-y-1">
                                        <label class="text-[12px] text-slate-400 font-bold">取得方式</label>
                                        <div class="text-[15px] text-slate-800">{{ item.acquisition_method }}</div>
                                    </div>
                                </div>

                                <div v-if="item.purpose" class="space-y-1">
                                    <label class="text-[12px] text-slate-400 font-bold">用意/開示</label>
                                    <div class="text-[15px] text-slate-700 leading-relaxed bg-slate-50 p-3 rounded-xl border border-slate-100 italic">「{{ item.purpose }}」</div>
                                </div>

                                <div class="space-y-2">
                                    <label class="text-[12px] text-slate-400 font-bold">承接皇恩人員清單</label>
                                    <div class="space-y-2">
                                        <div v-for="dnr in item.dharma_name_registries" :key="dnr.id" class="p-3 border border-slate-100 rounded-xl bg-white shadow-sm">
                                            <div class="flex justify-between items-center mb-1">
                                                <span class="font-bold text-[16px] text-black">{{ dnr.dharma_name ? dnr.dharma_name.name : dnr.custom_name }}</span>
                                                <span class="text-[13px] text-slate-400">{{ dnr.obtained_date?.replace(/-/g, '/') }}</span>
                                            </div>
                                            <div v-if="dnr.remarks" class="text-[14px] text-slate-500 mt-1 pl-2 border-l-2 border-slate-200">{{ dnr.remarks }}</div>
                                        </div>
                                    </div>
                                </div>
                                
                                <button @click.stop="confirmDelete(item.id)" class="w-full py-3 mt-4 text-[14px] text-red-500 font-bold border border-red-50 bg-red-50/20 rounded-xl active:bg-red-50 transition-colors">刪除此法寶紀錄</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        <!-- Navigation Components -->
        <mobile-navbar 
            :can-back="true"
            @back="handleBack"
            @home="$emit('goHome')"
            @action="showAddMenu = !showAddMenu"
            @search="showSearch = !showSearch"
            @more="showExportMenu = !showExportMenu"
        />

        <registry-add-form 
            v-if="addMode"
            :mode="addMode" 
            :initialData="form"
            :masters="masters"
            :isSaving="isSaving"
            @saveSingle="saveSingle"
            @saveBatch="triggerBatchSave"
            @cancel="addMode = null"
        />
        
        <!-- Persistent Toasts/Picker -->
        <div v-if="persistentToast" class="fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 z-[9999]">
             <div class="bg-white p-4 rounded-xl shadow-2xl border border-slate-200 text-center">
                 <p class="text-sm font-bold text-slate-800">{{ persistentToast.msg }}</p>
                 <div v-if="persistentToast.type === 'confirm'" class="mt-4 flex space-x-2">
                     <button v-for="a in persistentToast.actions" @click="a.handler" class="flex-1 p-2 rounded-lg bg-red-50 text-red-600 font-bold text-xs">{{ a.label }}</button>
                     <button @click="persistentToast = null" class="flex-1 p-2 rounded-lg bg-slate-50 text-slate-400 font-bold text-xs">取消</button>
                 </div>
             </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, defineEmits, watch } from 'vue';
import axios from 'axios';
import MobileNavbar from './MobileNavbar.vue';
import RegistryAddForm from './RegistryAddForm.vue';

const emit = defineEmits(['goHome']);

// States
const currentCategory = ref(null); 
const currentFolder = ref(null);
const addMode = ref(null);
const allTreasures = ref([]);
const masters = ref([]);
const dharmaNames = ref([]);
const loading = ref(false);
const persistentToast = ref(null);
const isSaving = ref(false);
const sortDesc = ref(true);
const openMenuId = ref(null);
const expandedIds = ref(new Set());
const editingIds = ref(new Set());
const focusedId = ref(null);
const showAddMenu = ref(false);
const showSearch = ref(false);
const showExportMenu = ref(false);
const deleteConfirmId = ref(null);

const form = ref({
    master_id: null,
    category: 'major',
    name: '',
    purpose: '',
    acquisition_method: '',
    remarks: '',
    record_date: new Date().toISOString().split('T')[0],
    obtained_date: '',
    dharma_name_registries: []
});

const folders = ref([
    { id: 1, name: '老祖仙師' },
    { id: 2, name: '元始仙師' },
    { id: 3, name: '道祖仙師' },
    { id: 4, name: '靈寶仙師' },
    { id: 5, name: '父皇' },
    { id: 6, name: '太宰仙師' },
    { id: 7, name: '太子' },
    { id: 8, name: '閻王仙師' }
]);

const loadData = async () => {
    loading.value = true;
    try {
        const [res, mres, dres] = await Promise.all([
            axios.get('/registries'),
            axios.get('/api/masters-list'),
            axios.get('/api/dharma-names-list')
        ]);
        allTreasures.value = res.data;
        masters.value = mres.data;
        dharmaNames.value = dres.data;
    } catch (e) {} finally { loading.value = false; }
};

onMounted(loadData);

const filteredTreasures = computed(() => {
    if (!currentFolder.value) return [];
    let filtered = allTreasures.value.filter(t => 
        t.master_id === currentFolder.value?.id && 
        (t.category === currentCategory.value || (!t.category && currentCategory.value === 'major'))
    );
    filtered.sort((a,b) => sortDesc.value ? (b.record_date||'').localeCompare(a.record_date||'') : (a.record_date||'').localeCompare(b.record_date||''));
    return filtered;
});

const personCentricList = computed(() => {
    if (currentCategory.value !== 'other' || !currentFolder.value) return [];
    const personMap = new Map();
    allTreasures.value
        .filter(t => t.master_id === currentFolder.value?.id && t.category === 'other')
        .forEach(t => {
            (t.dharma_name_registries || []).forEach(dnr => {
                const dn = dharmaNames.value.find(d => d.id === dnr.dharma_name_id);
                const personName = dn ? dn.name : dnr.custom_name;
                if (!personName) return;
                if (!personMap.has(personName)) personMap.set(personName, { name: personName, records: [] });
                personMap.get(personName).records.push({
                    id: t.id + '-' + (dnr.id || personName),
                    source_id: t.id,
                    treasure_name: t.name,
                    obtained_date: dnr.obtained_date,
                    remarks: dnr.remarks,
                    purpose: t.purpose
                });
            });
        });
    const sortedPersons = Array.from(personMap.values()).sort((a,b) => {
        const indexA = dharmaNames.value.findIndex(d => d.name === a.name);
        const indexB = dharmaNames.value.findIndex(d => d.name === b.name);
        if (indexA !== -1 && indexB !== -1) return indexA - indexB;
        return a.name.localeCompare(b.name, 'zh-Hant');
    });
    sortedPersons.forEach(p => p.records.sort((a,b) => (b.obtained_date||'').localeCompare(a.obtained_date||'')));
    return sortedPersons;
});

const expandedPersonNames = ref(new Set());
const togglePerson = (name) => {
    if (expandedPersonNames.value.has(name)) expandedPersonNames.value.delete(name);
    else expandedPersonNames.value.add(name);
};

const toggleExpand = (id) => {
    if (expandedIds.value.has(id)) expandedIds.value.delete(id);
    else { expandedIds.value.clear(); expandedIds.value.add(id); }
};

const handleBack = () => {
    if (addMode.value) addMode.value = null;
    else if (currentFolder.value) currentFolder.value = null;
    else if (currentCategory.value) currentCategory.value = null;
    else emit('goHome');
};

const saveSingle = async (data) => {
    isSaving.value = true;
    try {
        if (data.id) await axios.post(`/registries/${data.id}`, { ...data, _method: 'PATCH' });
        else await axios.post('/registries', data);
        addMode.value = null;
        loadData();
    } finally { isSaving.value = false; }
};

const openAndEdit = (id) => {
    const item = allTreasures.value.find(t => t.id === id);
    if (!item) return;
    form.value = { ...item };
    addMode.value = 'single';
    openMenuId.value = null;
};

const confirmDelete = (id) => {
    deleteConfirmId.value = id;
    persistentToast.value = { 
        msg: '確定要刪除嗎？', 
        type: 'confirm',
        actions: [{ label: '確定', handler: executeDelete }]
    };
};

const executeDelete = async () => {
    await axios.delete(`/registries/${deleteConfirmId.value}`);
    persistentToast.value = null;
    loadData();
};

const toggleMenu = (id) => openMenuId.value = openMenuId.value === id ? null : id;
const toggleSort = () => sortDesc.value = !sortDesc.value;
const formatDate = (d) => d ? new Date(d).toLocaleDateString('zh-TW') : '-';
</script>

<style scoped>
.animate-fade-in { animation: fadeIn 0.3s ease-out; }
@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
</style>
