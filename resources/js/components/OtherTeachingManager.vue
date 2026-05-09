<template>
    <div class="h-full bg-white flex flex-col text-slate-900 overflow-clip">
        <!-- Delete Confirmation / Status Toast -->
        <div v-if="persistentToast" class="fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 z-[9999] pointer-events-auto">
            <div class="bg-white rounded-3xl shadow-[0_20px_60px_rgba(0,0,0,0.3)] flex flex-col border border-slate-100 overflow-hidden" style="padding: 28px; min-width: 320px; max-width: calc(100vw - 32px);">
                <div class="flex items-start justify-between mb-8">
                    <span class="text-[17px] font-black text-slate-900 leading-relaxed tracking-widest">
                        {{ persistentToast.msg }}
                    </span>
                    <button @click="persistentToast = null" class="ml-6 text-slate-400 hover:text-slate-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="3" stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div v-if="persistentToast.type === 'deleteConfirm'" class="flex space-x-4">
                    <button @click="executeDelete" class="flex-1 bg-red-600 text-white h-[52px] rounded-2xl border border-red-500 text-[18px] font-black tracking-widest active:scale-95 transition-all shadow-lg shadow-red-100" style="color: white !important;">確定刪除</button>
                    <button @click="persistentToast = null" class="flex-1 bg-slate-100 text-slate-500 h-[52px] rounded-2xl border border-slate-200 text-[18px] font-black tracking-widest active:scale-95 transition-all">取消</button>
                </div>
                <div v-else class="flex justify-end mt-2">
                    <button @click="persistentToast = null" class="bg-indigo-600 text-white px-10 py-3.5 rounded-2xl text-[18px] font-black tracking-widest active:scale-95 transition-all shadow-lg shadow-indigo-100" style="color: white !important;">確定</button>
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
            <div class="border-b border-slate-300 flex items-center bg-white sticky top-0 z-[110] w-full shrink-0" style="padding: 8px 10px; min-height: 52px;">
                <div class="flex-1 min-w-0">
                    <div class="flex-1 flex items-center min-w-0 py-1 pl-1 cursor-pointer" @click="resetToRoot">
                        <h1 class="leading-tight font-outfit tracking-widest break-words !font-black !text-[#0f172a]" style="color: #0f172a !important; font-size: 32px !important; padding-top: 5px; font-weight: 900 !important;">
                            其他記錄專區
                        </h1>
                    </div>
                </div>
                <div class="flex items-center space-x-2 mr-2">
                    <button @click="sortDesc = !sortDesc" class="px-3 py-1.5 text-[16px] text-indigo-600 active:scale-95 transition-all" style="font-size: 16px !important;">
                        {{ sortDesc ? '新→舊' : '舊→新' }}
                    </button>
                    <button v-if="focusedId" @click="focusedId = null" class="w-8 h-8 flex items-center justify-center bg-slate-100 text-slate-400 rounded-xl active:scale-90 transition-all ml-1">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" /></svg>
                    </button>
                </div>
            </div>

            <!-- Search Area -->
            <div v-if="isSearchVisible" class="px-2 py-2 animate-fade-in">
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-indigo-400 group-focus-within:text-indigo-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </div>
                    <input v-model="searchQuery" 
                           placeholder="搜尋日期、內容或仙師..." 
                           class="block w-full pl-11 pr-12 h-[52px] bg-slate-50 border-2 border-transparent focus:border-indigo-100 focus:bg-white rounded-2xl text-[17px] font-black font-outfit text-slate-800 placeholder-slate-300 transition-all outline-none shadow-sm">
                    <button v-if="searchQuery" @click="searchQuery = ''" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-300 hover:text-red-500 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                </div>
            </div>

            <!-- Record List -->
            <div :class="['flex-1 overflow-y-auto custom-scrollbar bg-slate-50/20 w-full', focusedId ? 'px-0 py-0' : 'px-4 py-6']">
                <div :class="['max-w-4xl mx-auto space-y-4 pb-32', focusedId ? 'space-y-0' : 'space-y-4']">
                    <div v-for="item in filteredRecords" :key="item.id" 
                         v-show="focusedId === null || focusedId === item.id"
                         @click="toggleExpand(item.id)"
                         :class="[
                             'bg-white shadow-sm transition-all duration-300 relative group animate-fade-in cursor-pointer active:bg-slate-50',
                              focusedId === item.id ? 'min-h-[80dvh] rounded-none px-[15px] py-4 md:px-[15px]' : 'rounded-[32px] p-6 border border-slate-100 hover:shadow-md'
                         ]">
                        


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
                            <div :class="[
                                'pt-3 text-[17px] font-black text-slate-800 leading-relaxed font-outfit',
                                focusedId === item.id ? 'whitespace-pre-wrap' : 'line-clamp-1'
                            ]">
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
                
                <div class="relative w-full h-[100dvh] md:h-auto md:max-h-[95dvh] md:max-w-xl flex flex-col bg-white animate-slide-up overflow-hidden md:rounded-[32px] shadow-2xl">
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
                <div class="p-6 border-t border-slate-50 bg-white sticky bottom-0 flex space-x-4 shrink-0 pb-8">
                    <button @click="showModal = false" class="flex-1 h-[52px] rounded-2xl font-black text-[18px] bg-slate-100 text-slate-500 active:bg-slate-200 transition-all">取消</button>
                    <button @click="saveRecord" :disabled="saving" class="flex-[2] h-[52px] rounded-2xl font-black text-[18px] bg-blue-600 shadow-lg shadow-blue-100 active:scale-95 disabled:opacity-50 transition-all" style="color: white !important;">
                        {{ saving ? '儲存中...' : (isEditing ? '確認修改' : '確認存檔') }}
                    </button>
                </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed, watch } from 'vue';
import axios from 'axios';
import MobileNavbar from './MobileNavbar.vue';
import { writeClipboard, downloadBlob, lockBodyScroll, unlockBodyScroll } from '../utils/iosCompat';

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
const focusedId = ref(null);

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
        focusedId.value = null;
        await loadData();
    } catch (e) {
        console.error('Delete failed:', e);
        persistentToast.value = { msg: '刪除失敗', type: 'error' };
    } finally {
        deleteConfirmId.value = null;
    }
};

const toggleExpand = (id) => {
    focusedId.value = focusedId.value === id ? null : id;
};

const copyItem = async (item) => {
    const text = `【其他記錄】${formatDate(item.date)}\n${item.master?.name || ''} ${item.target_remarks || ''}\n${item.content}${item.remarks ? '\n備註：' + item.remarks : ''}`;
    writeClipboard(text).then(success => {
        if (success) alert('已複製');
        else alert('複製失敗');
    });
};

const downloadItem = (item) => {
    const text = `【其他記錄】\n日期：${formatDate(item.date)}\n仙師：${item.master?.name || '無'}\n對象：${item.target_remarks || '無'}\n內容：\n${item.content}\n備註：${item.remarks || '無'}`;
    const blob = new Blob([text], { type: 'text/plain;charset=utf-8' });
    downloadBlob(blob, `其他記錄_${item.date || '無日期'}_${item.master?.name || ''}.txt`);
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

const isAnyModalOpen = computed(() => {
    return !!showModal.value || 
           !!persistentToast.value || 
           !!deleteConfirmId.value || 
           !!activeActionMenuId.value || 
           !!focusedId.value;
});

watch(isAnyModalOpen, (newVal) => {
    if (newVal) lockBodyScroll();
    else unlockBodyScroll();
});

onUnmounted(() => {
    if (isAnyModalOpen.value) unlockBodyScroll();
});

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

.custom-scrollbar { overscroll-behavior-y: contain; }
.custom-scrollbar::-webkit-scrollbar { width: 5px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
* { -webkit-tap-highlight-color: transparent; }
</style>
