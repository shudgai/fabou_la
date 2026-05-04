<template>
    <div class="h-[100dvh] bg-white flex flex-col text-slate-900 overflow-hidden">
        <!-- Delete Confirmation / Status Toast -->
        <div v-if="persistentToast" class="fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 z-[9999] pointer-events-auto">
            <div class="bg-white rounded-3xl shadow-[0_20px_60px_rgba(0,0,0,0.3)] flex flex-col border border-slate-100 overflow-hidden" style="padding: 28px; min-width: 360px;">
                <div class="flex items-start justify-between mb-8">
                    <span class="text-[17px] font-black text-slate-900 leading-relaxed tracking-widest">
                        {{ persistentToast.msg }}
                    </span>
                    <button @click="persistentToast = null" class="ml-6 text-slate-400 hover:text-slate-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="3" stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div v-if="persistentToast.type === 'deleteConfirm'" class="flex space-x-4">
                    <button @click="persistentToast = null" class="flex-1 bg-slate-50 text-slate-600 h-[48px] rounded-2xl border border-slate-100 text-[17px] font-black tracking-widest active:scale-95 transition-all">取消</button>
                    <button @click="executeDelete" class="flex-1 bg-red-50 text-red-600 h-[48px] rounded-2xl border border-red-100 text-[17px] font-black tracking-widest active:scale-95 transition-all">確定刪除</button>
                </div>
                <div v-else class="flex justify-end mt-2">
                    <button @click="persistentToast = null" class="bg-indigo-50 text-indigo-600 px-8 py-2.5 rounded-2xl text-[17px] font-black tracking-widest active:scale-95 transition-all">確定</button>
                </div>
            </div>
        </div>

        <div class="bg-white h-full relative w-full shadow-sm flex flex-col font-sans overflow-hidden">
            <datalist id="target-datalist">
                <option v-for="dn in dharmaNames" :key="'dn'+dn.id" :value="dn.name" />
                <option v-for="g in groups" :key="'g'+g.id" :value="g.name" />
            </datalist>

            <datalist id="master-datalist">
                <option value="老祖仙師" />
                <option value="元始仙師" />
                <option value="道祖仙師" />
                <option value="靈寶仙師" />
                <option value="父皇" />
                <option value="太宰仙師" />
                <option value="太子" />
                <option value="閻王仙師" />
                <option v-for="m in masters" :key="m.id" :value="m.name" />
            </datalist>

            <!-- Header -->
            <div class="border-b border-slate-300 flex items-center bg-white sticky top-0 z-[110] w-full md:max-w-xl md:mx-auto shrink-0" style="padding: 8px 10px; min-height: 52px;">
                <button @click="$emit('goHome')" class="text-slate-400 p-2 mr-1 active:scale-90 transition-transform shrink-0">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" /></svg>
                </button>
                <div class="flex-1 min-w-0">
                    <h2 class="text-[28px] font-black text-slate-900 tracking-widest truncate font-outfit uppercase" style="font-size: 28px !important;">其他記錄專區</h2>
                </div>
                <div class="flex items-center space-x-2 mr-2">
                    <button @click="sortDesc = !sortDesc" class="px-3 py-1.5 text-[13px] text-indigo-500 bg-indigo-50 border border-indigo-100 rounded-xl active:scale-95 transition-all font-black">
                        {{ sortDesc ? '新→舊' : '舊→新' }}
                    </button>
                </div>
            </div>

            <!-- Search Area -->
            <div v-if="isSearchVisible" class="px-4 py-3 bg-white border-b border-slate-100 animate-fade-in">
                <div class="relative flex items-center group">
                    <div class="absolute left-4 text-slate-400 group-focus-within:text-indigo-500 transition-colors pointer-events-none">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </div>
                    <input v-model="searchQuery" 
                           placeholder="搜尋日期、內容或仙師..." 
                           class="w-full bg-slate-50 border-none rounded-2xl pl-12 pr-4 py-3 text-[17px] font-bold text-slate-900 placeholder-slate-400 focus:ring-2 focus:ring-indigo-100 focus:bg-white transition-all outline-none">
                    <button v-if="searchQuery" @click="searchQuery = ''" class="absolute right-4 text-slate-300 hover:text-slate-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                </div>
            </div>

            <!-- Record List -->
            <div class="flex-1 overflow-y-auto custom-scrollbar bg-slate-50/20 px-4 py-6 w-full md:max-w-xl md:mx-auto">
                <div class="max-w-4xl mx-auto space-y-4 pb-32">
                    <div v-for="item in filteredRecords" :key="item.id" 
                         class="bg-white rounded-[32px] p-6 shadow-sm border border-slate-100 hover:shadow-md transition-all duration-300 relative group animate-fade-in">
                        
                        <!-- Action Menu Button -->
                        <button @click.stop="toggleActionMenu(item.id)" 
                                class="absolute top-4 right-4 w-10 h-10 flex items-center justify-center text-slate-300 hover:text-indigo-500 active:scale-90 transition-all z-10">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/></svg>
                        </button>

                        <!-- Action Menu Dropdown -->
                        <div v-if="activeActionMenuId === item.id" v-click-outside="() => activeActionMenuId = null"
                             class="absolute top-14 right-4 w-36 bg-white rounded-2xl shadow-2xl border border-slate-100 z-50 p-1.5 animate-scale-in origin-top-right">
                            <button @click="startEdit(item)" class="w-full flex items-center px-4 h-11 rounded-xl hover:bg-slate-50 text-slate-700 font-bold text-[15px] transition-colors">
                                <svg class="w-4 h-4 mr-3 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" stroke-width="2.5"/></svg>
                                編輯
                            </button>
                            <button @click="deleteRecord(item.id)" class="w-full flex items-center px-4 h-11 rounded-xl hover:bg-rose-50 text-rose-500 font-bold text-[15px] transition-colors">
                                <svg class="w-4 h-4 mr-3 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                刪除
                            </button>
                        </div>

                        <!-- Content -->
                        <div class="space-y-1">
                            <!-- Line 1: Date -->
                            <div class="text-[14px] font-black text-slate-400 tracking-wider">
                                {{ formatDate(item.date) || '無日期' }}
                            </div>

                            <!-- Line 2: Master / Target -->
                            <div v-if="item.master || item.target_remarks" class="flex items-center text-[16px] font-black text-indigo-600 space-x-2">
                                <span v-if="item.master">{{ item.master.name }}</span>
                                <span v-if="item.master && item.target_remarks" class="text-slate-300 mx-1">/</span>
                                <span v-if="item.target_remarks" class="text-amber-600">{{ item.target_remarks }}</span>
                            </div>
                            
                            <!-- Content Body -->
                            <div class="pt-3 text-[18px] font-bold text-slate-800 leading-relaxed whitespace-pre-wrap font-outfit">
                                {{ item.content }}
                            </div>

                            <!-- Footer Remarks -->
                            <div v-if="item.remarks" class="pt-2 text-[13px] font-bold text-slate-400">
                                備註：{{ item.remarks }}
                            </div>
                        </div>
                    </div>

                    <!-- Empty State -->
                    <div v-if="filteredRecords.length === 0" class="py-20 md:pt-10 flex flex-col items-center justify-center text-slate-300">
                        <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center mb-6 shadow-sm">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" stroke-width="2" /></svg>
                        </div>
                        <p class="text-[18px] font-black tracking-widest">尚無記錄資料</p>
                        <p class="text-[14px] mt-2 font-bold opacity-60">點擊右下角「+」開始記錄</p>
                    </div>
                </div>
            </div>

            <!-- Bottom Navbar -->
            <mobile-navbar 
                is-absolute
                active-tab="other_teaching" 
                :show-action="true"
                :action-active="showModal"
                :can-search="true"
                :search-active="isSearchVisible"
                @action="openAddModal"
                @search="isSearchVisible = !isSearchVisible"
                @home="$emit('goHome')" 
                @back="$emit('goHome')" 
                :can-back="true" />

            <!-- Add/Edit Modal -->
            <div v-if="showModal" class="fixed inset-0 z-[1000] flex items-end md:items-center justify-center px-0 bg-slate-900/20 backdrop-blur-sm">
                <!-- Desktop Backdrop Click Area -->
                <div class="absolute inset-0" @click="showModal = false"></div>
                
                <div class="relative w-full h-[100vh] md:h-auto md:max-h-[95vh] md:max-w-xl flex flex-col bg-white animate-slide-up overflow-hidden md:rounded-[32px] shadow-2xl">
                    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between shrink-0">
                        <h3 class="text-[20px] font-black text-slate-900">{{ isEditing ? '編輯紀錄' : '新增紀錄' }}</h3>
                        <button @click="showModal = false" class="text-slate-300 hover:text-slate-500 p-2">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="3" stroke-linecap="round"/></svg>
                        </button>
                    </div>
                
                <div class="flex-1 overflow-y-auto p-6 space-y-6 custom-scrollbar pb-32">
                    <!-- Date Input -->
                    <div class="space-y-2">
                        <label class="text-[14px] font-black text-slate-400 uppercase tracking-widest ml-1">錄入日期</label>
                        <input type="date" v-model="form.date" 
                               class="w-full bg-slate-50 border-none rounded-2xl px-5 py-4 text-[17px] font-bold text-slate-900 focus:ring-2 focus:ring-indigo-100 outline-none">
                    </div>

                    <div class="space-y-2 relative">
                        <label class="text-[14px] font-black text-slate-400 uppercase tracking-widest ml-1">對象仙師</label>
                        <div class="relative">
                            <input v-model="masterNameInput" 
                                   list="master-datalist"
                                   placeholder="搜尋或輸入仙師..." 
                                   class="w-full bg-slate-50 border-none rounded-2xl px-5 py-4 text-[17px] font-bold text-slate-900 focus:ring-2 focus:ring-indigo-100 outline-none">
                        </div>
                    </div>

                    <div class="space-y-2 relative">
                        <label class="text-[14px] font-black text-slate-400 uppercase tracking-widest ml-1">針對對象</label>
                        <div class="relative">
                            <input v-model="targetRemarksInput" 
                                   list="target-datalist"
                                   placeholder="搜尋法號、群組或全體..." 
                                   class="w-full bg-slate-50 border-none rounded-2xl px-5 py-4 text-[17px] font-bold text-slate-900 focus:ring-2 focus:ring-indigo-100 outline-none">
                        </div>
                    </div>

                    <!-- Content Textarea -->
                    <div class="space-y-2">
                        <label class="text-[14px] font-black text-slate-400 uppercase tracking-widest ml-1">記錄內容</label>
                        <textarea v-model="form.content" rows="8" placeholder="輸入記錄內容..." 
                                  class="w-full bg-slate-50 border-none rounded-2xl px-5 py-4 text-[17px] font-bold text-slate-900 focus:ring-2 focus:ring-indigo-100 outline-none resize-none custom-scrollbar"></textarea>
                    </div>

                    <!-- Remarks -->
                    <div class="space-y-2">
                        <label class="text-[14px] font-black text-slate-400 uppercase tracking-widest ml-1">結尾備註</label>
                        <input v-model="form.remarks" placeholder="例如：完畢 (選填)" 
                               class="w-full bg-slate-50 border-none rounded-2xl px-5 py-4 text-[17px] font-bold text-slate-900 focus:ring-2 focus:ring-indigo-100 outline-none">
                    </div>
                </div>

                <!-- Footer Actions -->
                <div class="p-6 border-t border-slate-50 bg-white sticky bottom-0 flex space-x-4 shrink-0">
                    <button @click="showModal = false" class="flex-1 py-4 rounded-2xl font-black text-[17px] bg-slate-400 active:bg-slate-500 transition-all shadow-md" style="color: white !important;">取消</button>
                    <button @click="saveRecord" :disabled="saving" class="flex-[2] py-4 rounded-2xl font-black text-[17px] bg-indigo-600 shadow-lg shadow-indigo-100 active:scale-95 disabled:opacity-50 transition-all" style="color: white !important;">
                        {{ saving ? '儲存中...' : (isEditing ? '確認修改' : '確認存檔') }}
                    </button>
                </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed, watch } from 'vue';
import axios from 'axios';
import MobileNavbar from './MobileNavbar.vue';

const props = defineProps({
    user: Object
});

const emit = defineEmits(['goHome']);

// State
const records = ref([]);
const masters = ref([]);
const dharmaNames = ref([]);
const groups = ref([]);
const searchQuery = ref('');
const isSearchVisible = ref(false);
const sortDesc = ref(true);
const activeActionMenuId = ref(null);
const showModal = ref(false);
const isEditing = ref(false);
const saving = ref(false);
const showMasterDropdown = ref(false);
const showTargetDropdown = ref(false);
const masterNameInput = ref('');
const targetRemarksInput = ref('');
const persistentToast = ref(null);
const deleteConfirmId = ref(null);

const staticMasters = [
    { name: '老祖仙師' }, { name: '元始仙師' }, { name: '道祖仙師' }, { name: '靈寶仙師' },
    { name: '父皇' }, { name: '太宰仙師' }, { name: '太子' }, { name: '閻王仙師' }
];

const form = ref({
    id: null,
    date: new Date().toISOString().split('T')[0],
    master_id: null,
    content: '',
    target_remarks: '',
    remarks: ''
});

// Computed
const filteredRecords = computed(() => {
    let result = [...records.value];
    
    if (searchQuery.value) {
        const q = searchQuery.value.toLowerCase();
        
        // Find dharma names that match a group name
        const matchingGroupIds = groups.value
            .filter(g => g.name.toLowerCase().includes(q))
            .map(g => g.id);
            
        const namesInMatchingGroups = dharmaNames.value
            .filter(dn => dn.groups && dn.groups.some(g => matchingGroupIds.includes(g.id)))
            .map(dn => dn.name.toLowerCase());

        result = result.filter(r => {
            const content = (r.content || '').toLowerCase();
            const date = (r.date || '').toLowerCase();
            const masterName = (r.master?.name || '').toLowerCase();
            const target = (r.target_remarks || '').toLowerCase();
            const remarks = (r.remarks || '').toLowerCase();

            const basicMatch = content.includes(q) || date.includes(q) || masterName.includes(q) || target.includes(q) || remarks.includes(q);
            
            // If the query is a group name, also check if target_remarks is a dharma name in that group
            let groupMatch = false;
            if (namesInMatchingGroups.length > 0 && target) {
                groupMatch = namesInMatchingGroups.includes(target);
            }

            return basicMatch || groupMatch;
        });
    }

    result.sort((a, b) => {
        const dateA = a.date || '';
        const dateB = b.date || '';
        return sortDesc.value ? dateB.localeCompare(dateA) : dateA.localeCompare(dateB);
    });

    return result;
});

// Suggestions for dropdowns
const filteredMasterSuggestions = computed(() => {
    const q = masterNameInput.value.toLowerCase();
    const all = [...staticMasters, ...masters.value];
    // Remove duplicates by name
    const unique = [];
    const seen = new Set();
    for (const m of all) {
        if (!seen.has(m.name)) {
            seen.add(m.name);
            unique.push(m);
        }
    }
    if (!q) return unique;
    return unique.filter(m => m.name.toLowerCase().includes(q));
});

const filteredTargetSuggestions = computed(() => {
    const q = targetRemarksInput.value.toLowerCase();
    const all = [
        { name: '全體', type: '系統' },
        ...dharmaNames.value.map(dn => ({ name: dn.name, type: '法號' })),
        ...groups.value.map(g => ({ name: g.name, type: '群組' }))
    ];
    if (!q) return all;
    return all.filter(t => t.name.toLowerCase().includes(q));
});

// Methods
const loadData = async () => {
    try {
        const [recordsRes, mastersRes, dharmaRes, groupsRes] = await Promise.all([
            axios.get('/other-teachings'),
            axios.get('/api/masters-list'),
            axios.get('/api/dharma-names-list'),
            axios.get('/api/groups-list')
        ]);
        records.value = recordsRes.data;
        masters.value = mastersRes.data;
        dharmaNames.value = dharmaRes.data;
        groups.value = groupsRes.data.sort((a, b) => a.name.length - b.name.length);
    } catch (e) {
        console.error('Failed to load other teachings data', e);
    }
};

const toggleActionMenu = (id) => {
    activeActionMenuId.value = activeActionMenuId.value === id ? null : id;
};

const openAddModal = () => {
    isEditing.value = false;
    form.value = {
        id: null,
        date: new Date().toISOString().split('T')[0],
        master_id: null,
        content: '',
        target_remarks: '',
        remarks: ''
    };
    masterNameInput.value = '';
    targetRemarksInput.value = '';
    showModal.value = true;
};

const startEdit = (item) => {
    isEditing.value = true;
    form.value = { ...item };
    masterNameInput.value = item.master?.name || '';
    targetRemarksInput.value = item.target_remarks || '';
    activeActionMenuId.value = null;
    showModal.value = true;
};

const selectMaster = (m) => {
    form.value.master_id = m.id || null;
    masterNameInput.value = m.name;
};

const selectTarget = (t) => {
    form.value.target_remarks = t.name;
    targetRemarksInput.value = t.name;
};

const saveRecord = async () => {
    form.value.target_remarks = targetRemarksInput.value;
    if (!form.value.content.trim()) {
        alert('請輸入記錄內容');
        return;
    }

    // Try to resolve master if input manually
    if (!form.value.master_id && masterNameInput.value) {
        const match = masters.value.find(m => m.name === masterNameInput.value);
        if (match) form.value.master_id = match.id;
    }

    saving.value = true;
    try {
        if (isEditing.value) {
            await axios.put(`/other-teachings/${form.value.id}`, form.value);
        } else {
            await axios.post('/other-teachings', form.value);
        }
        await loadData();
        showModal.value = false;
    } catch (e) {
        console.error('Failed to save record', e);
        alert('儲存失敗');
    } finally {
        saving.value = false;
    }
};

const deleteRecord = (id) => {
    deleteConfirmId.value = id;
    persistentToast.value = { msg: '確定要刪除此筆記錄嗎？', type: 'deleteConfirm' };
    activeActionMenuId.value = null;
};

const executeDelete = async () => {
    if (!deleteConfirmId.value) return;
    try {
        await axios.delete(`/other-teachings/${deleteConfirmId.value}`);
        persistentToast.value = { msg: '已成功刪除紀錄', type: 'success' };
        setTimeout(() => { persistentToast.value = null; }, 1500);
        await loadData();
    } catch (e) {
        console.error('Delete failed:', e);
        persistentToast.value = { msg: '刪除失敗', type: 'error' };
    } finally {
        deleteConfirmId.value = null;
    }
};

const formatDate = (d) => {
    if (!d) return '';
    return d.replace(/-/g, '/');
};

// Custom directive for clicking outside
const vClickOutside = {
    mounted(el, binding) {
        el.clickOutsideEvent = (event) => {
            if (!(el === event.target || el.contains(event.target))) {
                binding.value();
            }
        };
        document.body.addEventListener('click', el.clickOutsideEvent);
    },
    unmounted(el) {
        document.body.removeEventListener('click', el.clickOutsideEvent);
    }
};

onMounted(loadData);
</script>

<style scoped>
.animate-slide-up {
    animation: slideUp 0.4s cubic-bezier(0.16, 1, 0.3, 1);
}

@keyframes slideUp {
    from { transform: translateY(100%); }
    to { transform: translateY(0); }
}

.animate-fade-in {
    animation: fadeIn 0.5s ease-out;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.animate-scale-in {
    animation: scaleIn 0.2s ease-out;
}

@keyframes scaleIn {
    from { opacity: 0; transform: scale(0.95); }
    to { opacity: 1; transform: scale(1); }
}

.custom-scrollbar::-webkit-scrollbar {
    width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #e2e8f0;
    border-radius: 10px;
}
</style>
