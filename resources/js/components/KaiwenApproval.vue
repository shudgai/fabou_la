<template>
    <teleport to="body">
    <div class="fixed inset-0 z-[3500] flex items-end md:items-center justify-center">
        <!-- Backdrop -->
        <div class="hidden md:block fixed inset-0 bg-slate-900/40 backdrop-blur-sm" @click="selectionFiltered = false; $emit('close')"></div>

        <!-- Form Container -->
        <div class="relative w-full h-full md:h-auto md:max-h-[95dvh] md:max-w-4xl bg-white md:rounded-[32px] md:shadow-2xl flex flex-col overflow-hidden animate-slide-up">
            <!-- Global Close Button -->
            <button @click="$emit('close')" class="absolute right-4 top-4 z-[500] p-2 text-slate-300 hover:text-slate-600 transition-all active:scale-90 bg-white/80 backdrop-blur-sm rounded-full shadow-sm md:shadow-none">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
            <div class="flex flex-col h-full bg-slate-50 overflow-hidden relative">

        <!-- Step 1: Selection Page -->
        <div v-if="step === 1" class="flex flex-col h-full overflow-hidden relative">
            <div class="animate-fade-in flex flex-col h-full overflow-hidden">
                <div class="flex-1 overflow-y-auto py-3 space-y-4 custom-scrollbar pb-40 w-full md:max-w-xl md:mx-auto" style="padding-left: 10px; padding-right: 10px;">
                    <!-- Selection Grid matched to RandomGroup -->
                    <div class="flex items-center justify-between px-3 py-3 md:pt-[32px]">
                        <div class="flex items-center">
                            <button v-if="selectionFiltered" @click="selectionFiltered = false" class="p-2 -ml-3 text-slate-400 active:scale-90 transition-all mr-1">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" /></svg>
                            </button>
                            <span class="font-black" :style="{ color: selectionFiltered ? '#1d4ed8' : '#94a3b8', fontSize: '16px !important', marginTop: '10px' }">
                                {{ selectionFiltered ? '已確認排序名單' : '點選待定法號' }}
                            </span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="font-bold shrink-0" :style="{ color: selectionList.length > 0 ? '#1d4ed8' : '#94a3b8', fontSize: '16px !important' }">已選 {{ selectionList.length }} 人</span>
                            <button @click="clearAll" class="w-10 h-10 bg-red-50 text-red-500 rounded-xl flex items-center justify-center active:scale-95 transition-all shrink-0 border-none">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </button>
                        </div>
                    </div>

                    <div class="grid grid-cols-4 md:grid-cols-5 px-1 w-full mt-[15px]" style="gap: 4px; background: #ffffff;">
                            <button v-for="user in filteredUsers" :key="user.id" 
                                @click="addParticipant(user.name)"
                                class="flex items-center justify-center font-normal transition-all active:scale-95 rounded-md border shadow-sm w-full min-h-[45px]"
                                style="font-size: 16px !important;"
                            :style="{ 
                                backgroundColor: isParticipantSelected(user.name) ? '#bfdbfe' : '#ffffff',
                                borderColor: isParticipantSelected(user.name) ? '#93c5fd' : '#d1d5db',
                                color: '#000000',
                                borderStyle: 'solid',
                                borderWidth: isParticipantSelected(user.name) ? '2px' : '1px',
                                textShadow: 'none'
                            }"
                        >
                            <span class="truncate leading-none">
                                <span v-if="isParticipantSelected(user.name)" class="mr-0.5 opacity-70" style="font-size: 14px !important;">
                                    {{ getParticipantIndex(user.name) }}.
                                </span>
                                {{ user.name }}
                            </span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Confirm Action -->
            <div class="fixed md:absolute left-0 right-0 px-4 py-3 bg-white/95 backdrop-blur-md border-t border-slate-100 z-[200] w-full bottom-[calc(7dvh+env(safe-area-inset-bottom))] md:bottom-0">
                <button @click="!selectionFiltered ? toggleSelectionFilter() : goToStep2()" 
                    :disabled="selectionList.length === 0" 
                    class="w-full font-black py-[10px] rounded-2xl transition-all active:scale-[0.98] disabled:opacity-50 disabled:active:scale-100 flex items-center justify-center shadow-lg" 
                    :style="{ 
                        backgroundColor: !selectionFiltered ? '#1d4ed8' : '#16a34a',
                        boxShadow: '0 4px 20px rgba(0, 0, 0, 0.1)',
                        fontSize: '16px !important'
                    }"
                >
                    <span style="color: #ffffff !important;">{{ !selectionFiltered ? '完成人員選取 (進入排列)' : '確定順序 (進入核定表)' }}</span>
                </button>
            </div>
        </div>

        <!-- Step 2: Approval Table Page -->
        <div v-if="step === 2" class="flex flex-col h-full bg-white overflow-hidden relative">
            <div class="animate-fade-in flex flex-col h-full overflow-hidden">
                <div class="bg-white border-b border-slate-100 py-2 px-3 flex items-center justify-between shrink-0 shadow-sm z-10 w-full md:max-w-xl md:mx-auto md:mt-[60px]">
                    <div class="flex items-center space-x-2">
                        <button @click="step = 1" class="text-slate-400 hover:text-indigo-600 active:scale-90 transition-transform p-2 -ml-2 rounded-full hover:bg-slate-50">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7"></path></svg>
                        </button>
                        <h3 class="text-slate-900 tracking-tight font-black" style="font-size: 16px !important;">核定結果</h3>
                    </div>
                </div>

                <div class="flex-1 overflow-y-auto py-4 flex flex-col custom-scrollbar w-full md:max-w-xl md:mx-auto" style="padding-left: 10px; padding-right: 10px;">

                    <!-- Instruction Text -->
                    <div class="px-2 pb-4 mb-2 flex flex-col space-y-1">
                        <div class="text-slate-700 tracking-wide font-black" style="font-size: 16px !important;">✓代表合格 ×代表不合格</div>
                        <div class="text-slate-700 tracking-wide font-black" style="font-size: 16px !important;">開文結果請示如下：</div>
                    </div>

                    <!-- Ledger Table -->
                    <div class="w-full pb-32">
                        <table class="w-full text-left border-separate border-spacing-0">
                            <tbody>
                                <tr v-for="(item, idx) in selectionList" :key="idx" class="group transition-all h-[52px]">
                                    <td class="pl-3 pr-1 py-1 w-20 whitespace-nowrap align-middle border-b border-slate-50">
                                        <span class="text-blue-600 leading-none tracking-widest font-black" style="font-size: 16px !important;">{{ item.name }}</span>
                                    </td>

                                    <td class="relative py-2 border-b border-slate-50 min-h-[60px]">
                                        <!-- Fixed Icons at Top-Right of THIS row -->
                                        <div class="absolute top-1 right-0 flex items-center space-x-2 z-20 bg-white/80 backdrop-blur-sm px-1 rounded-bl-lg">
                                            <button v-if="(item.slotCount || 3) < 5" 
                                                @click="item.slotCount = (item.slotCount || 3) + 1" 
                                                class="text-indigo-400 hover:text-indigo-600 active:scale-90 transition-all">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path></svg>
                                            </button>
                                            <button @click="removeSlot(item)" 
                                                class="text-slate-300 hover:text-rose-400 active:scale-90 transition-all">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg>
                                            </button>
                                        </div>

                                        <!-- Scrolling Verification Area -->
                                        <div class="overflow-x-auto custom-scrollbar pt-4">
                                            <div class="flex items-center space-x-1.5 min-w-max pr-12 px-0.5 pb-1">
                                                <div v-for="n in (item.slotCount || 3)" :key="'slot'+n" 
                                                     :class="[item.slots[n-1] ? 'w-[44px]' : 'w-[78px]']"
                                                     class="flex items-center border border-slate-300 rounded-lg bg-white overflow-hidden divide-x divide-slate-200 h-[32px] shrink-0 transition-all duration-300">
                                                    <!-- Only show V if selected or nothing selected -->
                                                    <button v-if="!item.slots[n-1] || item.slots[n-1] === 'v'"
                                                        @click="setStatus(idx, n-1, 'v')" 
                                                        :class="[item.slots[n-1] === 'v' ? 'bg-emerald-500 text-white w-full' : 'text-slate-300 active:bg-slate-50 w-1/2']"
                                                        class="h-full flex items-center justify-center transition-all duration-200">
                                                        <span class="text-[17px] leading-none">✓</span>
                                                    </button>
                                                    <!-- Only show X if selected or nothing selected -->
                                                    <button v-if="!item.slots[n-1] || item.slots[n-1] === 'x'"
                                                        @click="setStatus(idx, n-1, 'x')" 
                                                        :class="[item.slots[n-1] === 'x' ? 'bg-rose-500 text-white w-full' : 'text-slate-300 active:bg-slate-50 w-1/2']"
                                                        class="h-full flex items-center justify-center transition-all duration-200">
                                                        <span class="text-[17px] leading-none">×</span>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Bottom Action Button aligned with desktop container -->
            <div class="fixed md:absolute left-0 right-0 px-4 py-3 bg-white/95 backdrop-blur-md border-t border-slate-100 z-[200] w-full bottom-[calc(7dvh+env(safe-area-inset-bottom))] md:bottom-0">
                <button @click="copyToLine" class="w-full bg-emerald-600 py-[10px] rounded-2xl font-black transition-all active:scale-[0.98] tracking-widest flex items-center justify-center space-x-2 shadow-lg" style="box-shadow: 0 4px 20px rgba(16, 185, 129, 0.2); font-size: 16px !important;">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: white !important;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path></svg>
                    <span style="color: #ffffff !important;">複製給 LINE</span>
                </button>
            </div>
        </div>

        </div>

        <!-- Global Action Confirm / Toast (Critical for iOS) -->
        <div v-if="persistentToast" class="fixed inset-0 z-[9999] flex items-center justify-center p-6 bg-slate-900/40 backdrop-blur-sm animate-fade-in">
            <div class="bg-white w-full max-w-sm rounded-[32px] shadow-2xl overflow-hidden animate-slide-up border border-white/20">
                <div class="p-8 text-center space-y-6">
                    <div class="flex flex-col items-center">
                        <div v-if="persistentToast.type === 'clear'" class="w-16 h-16 bg-rose-50 text-rose-500 rounded-2xl flex items-center justify-center mb-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </div>
                        <div v-else-if="persistentToast.type === 'success'" class="w-16 h-16 bg-emerald-50 text-emerald-500 rounded-2xl flex items-center justify-center mb-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="3" stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <div v-else class="w-16 h-16 bg-indigo-50 text-indigo-500 rounded-2xl flex items-center justify-center mb-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <h3 class="text-[20px] font-black text-slate-900 leading-tight whitespace-pre-wrap">{{ persistentToast.msg }}</h3>
                    </div>

                    <div class="flex flex-col space-y-3">
                        <button v-if="persistentToast.type === 'clear'" 
                                @click="executeClearAll" 
                                class="w-full py-4 bg-rose-500 text-white rounded-2xl font-black text-[18px] active:scale-95 transition-all shadow-lg shadow-rose-200/50" 
                                style="color: white !important;">
                            確認清空
                        </button>
                        <button @click="persistentToast = null" 
                                :class="persistentToast.type === 'success' || persistentToast.type === 'error' ? 'bg-indigo-600 text-white shadow-indigo-100' : 'bg-slate-100 text-slate-500'"
                                class="w-full py-4 rounded-2xl font-black text-[18px] active:scale-95 transition-all shadow-lg"
                                :style="{ color: (persistentToast.type === 'success' || persistentToast.type === 'error' ? 'white !important' : 'inherit') }">
                            {{ persistentToast.type === 'success' || persistentToast.type === 'error' ? '確認' : '取消' }}
                        </button>
                    </div>
                </div>
            </div>
    </div>
    </div>
    </div>
    </teleport>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import axios from 'axios';

const searchQuery = ref('');
const users = ref([]);
const selectionList = ref([]);
const step = ref(1);
const selectionFiltered = ref(false);
const persistentToast = ref(null);

// Storage Keys removed for clean state on load

const excludedNames = ['鳳尊', '金巧', '赤覺', '紫元', '鳳媓', '金忠', '金孝', '金諦', '金彩', '金德', '靈平', '金護', '靈情', '靈奇', '靈傾'];

const loadUsers = async () => {
    try {
        const res = await axios.get('/api/dharma-names-list');
        users.value = res.data;

        // Regular Selection: Start with an empty list
        selectionList.value = [];
        selectionFiltered.value = false;
    } catch (e) {
        console.error('Failed to load users', e);
    }
};

const filteredUsers = computed(() => {
    let list = users.value.filter(u => u && !excludedNames.includes(u.name));
    if (selectionFiltered.value) {
        list = list.filter(u => isParticipantSelected(u.name))
                   .sort((a, b) => getParticipantIndex(a.name) - getParticipantIndex(b.name));
    }
    if (!searchQuery.value) return list;
    const q = searchQuery.value.toLowerCase();
    return list.filter(u => u.name.toLowerCase().includes(q));
});

const getParticipantIndex = (name) => {
    if (!name) return 0;
    const trimmedName = name.trim();
    return selectionList.value.findIndex(item => item.name.trim() === trimmedName) + 1;
};

const isParticipantSelected = (name) => {
    if (!name) return false;
    const trimmedName = name.trim();
    return selectionList.value.some(item => item.name.trim() === trimmedName);
};

const addParticipant = (name) => {
    if (!name) return;
    const trimmedName = name.trim();

    const index = selectionList.value.findIndex(item => item.name.trim() === trimmedName);
    if (index !== -1) {
        selectionList.value = selectionList.value.filter((_, i) => i !== index);
        return;
    }
    selectionList.value = [...selectionList.value, {
        name: trimmedName,
        slots: [null, null, null, null, null]
    }];
};

const setStatus = (rowIdx, slotIdx, type) => {
    const current = selectionList.value[rowIdx].slots[slotIdx];
    if (current === type) {
        selectionList.value[rowIdx].slots[slotIdx] = null;
    } else {
        selectionList.value[rowIdx].slots[slotIdx] = type;
    }
};

const removeSlot = (item) => {
    const current = item.slotCount || 3;
    if (current > 1) {
        item.slots[current - 1] = null; // Clear the data of the slot being removed
        item.slotCount = current - 1;
    }
};

const removeItem = (idx) => {
    selectionList.value.splice(idx, 1);
};

const clearAll = () => {
    persistentToast.value = { msg: '確定要清空整份名單與表格嗎？', type: 'clear' };
};

const executeClearAll = () => {
    selectionList.value = [];
    step.value = 1;
    selectionFiltered.value = false;
    persistentToast.value = { msg: '✓ 已清空', type: 'success' };
    setTimeout(() => { if (persistentToast.value?.type === 'success') persistentToast.value = null; }, 1500);
};

const invertSelection = () => {
    const allNames = filteredUsers.value.map(u => u.name);
    const selectedNames = selectionList.value.map(item => item.name);
    const unselectedNames = allNames.filter(n => !selectedNames.includes(n));

    // Completely replace selectionList with unselected
    selectionList.value = unselectedNames.map(name => ({
        name: name,
        slots: [null, null, null, null, null]
    }));
};
const selectAll = () => {
    selectionList.value = filteredUsers.value.map(user => ({
        name: user.name,
        slots: [null, null, null, null, null]
    }));
};
const toggleSelectionFilter = () => {
    if (selectionList.value.length > 0) {
        selectionFiltered.value = !selectionFiltered.value;
    }
};

defineExpose({
    clearAll,
    selectAll,
    invertSelection,
    toggleSelectionFilter,
    selectionFiltered
});

const goToStep2 = () => {
    if (selectionList.value.length === 0) {
        persistentToast.value = { msg: '請先加入至少一位待定法號。', type: 'error' };
        return;
    }
    step.value = 2;
};

const copyToLine = () => {
    if (selectionList.value.length === 0) {
        alert('目前表格為空，無法複製。');
        return;
    }

    let text = `✓ 代表合格 × 代表不合格\n`;
    text += `開文結果請示如下：\n`;

    selectionList.value.forEach(item => {
        const count = item.slotCount || 3;
        const symbols = item.slots.slice(0, count)
            .filter(s => s === 'v' || s === 'x')
            .map(s => s === 'v' ? '✓' : '×');

        if (symbols.length === 0) return;

        let results = '';
        if (symbols.length > 0) {
            results = symbols[0];
            if (symbols.length > 1) {
                results += ' ; ' + symbols[1];
                for (let i = 2; i < symbols.length; i++) {
                    results += '、' + symbols[i];
                }
            }
        }

        text += `${item.name}：${results}\n`;
    });

    navigator.clipboard.writeText(text).then(() => {
        persistentToast.value = { msg: '✓ 已複製到剪貼簿，快前往 LINE 貼上吧！', type: 'success' };
        setTimeout(() => { if (persistentToast.value?.type === 'success') persistentToast.value = null; }, 2000);
    }).catch(err => {
        console.error('Copy failed', err);
        persistentToast.value = { msg: '✖ 複製失敗，請手動選取文字。', type: 'error' };
    });
};

onMounted(loadUsers);

</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    width: 4px;
    height: 4px;
}
@media (min-width: 768px) {
    .custom-scrollbar::-webkit-scrollbar {
        width: 10px;
    }
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #e2e8f0;
    border-radius: 10px;
}
@media (min-width: 768px) {
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border: 2px solid transparent;
        background-clip: padding-box;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
        border: 2px solid transparent;
        background-clip: padding-box;
    }
}

.animate-fade-in {
    animation: fadeIn 0.3s ease-out forwards;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.animate-slide-up { animation: slideUp 0.25s cubic-bezier(0.16, 1, 0.3, 1); }
@keyframes slideUp { from { transform: translateY(100%); } to { transform: translateY(0); } }

.fade-enter-active, .fade-leave-active {
    transition: opacity 0.3s, transform 0.3s;
}
.fade-enter-from, .fade-leave-to {
    opacity: 0;
    transform: translate(-50%, -20px);
}

/* Kaiwen Specific Font Size Overrides */
:global(body.font-small) .kaiwen-module :where(.app-body, p, td, span, div, input, textarea, .text-\[18px\], .text-\[17px\], .text-\[16px\]) {
    font-size: 16px !important;
}
:global(body.font-medium) .kaiwen-module :where(.app-body, p, td, span, div, input, textarea, .text-\[18px\], .text-\[17px\], .text-\[16px\]) {
    font-size: 18px !important;
}
:global(body.font-large) .kaiwen-module :where(.app-body, p, td, span, div, input, textarea, .text-\[18px\], .text-\[17px\], .text-\[16px\]) {
    font-size: 21px !important;
}
</style>
