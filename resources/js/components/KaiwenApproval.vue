<template>
    <div class="flex flex-col h-full bg-slate-50 overflow-hidden relative">
        
        <!-- Step 1: Selection Page -->
        <div v-if="step === 1" class="flex flex-col h-full overflow-hidden relative">
            <div class="animate-fade-in flex flex-col h-full overflow-hidden">
                <div class="flex-1 overflow-y-auto py-3 space-y-4 custom-scrollbar pb-24" style="padding-left: 10px; padding-right: 10px;">
                    <!-- Selection Grid matched to RandomGroup -->
                    <div class="flex items-center justify-between px-3 py-1.5">
                        <span class="font-black text-[15px]" :style="{ color: selectionFiltered ? '#1d4ed8' : '#94a3b8' }">
                            {{ selectionFiltered ? '已確認排序名單' : '點選待定法號' }}
                        </span>
                        <div class="flex items-center space-x-2">
                            <button v-if="selectionList.length > 0" @click="toggleSelectionFilter" 
                                class="px-4 py-1.5 rounded-lg text-[17px] font-black border transition-colors shadow-sm"
                                :class="selectionFiltered ? 'bg-blue-600 border-blue-600' : 'bg-white border-blue-200'"
                                :style="{ color: selectionFiltered ? 'white !important' : '#2563eb' }"
                            >
                                {{ selectionFiltered ? '返回全名冊' : '排列' }}
                            </button>
                            <span class="text-[14px] font-bold shrink-0" :style="{ color: selectionList.length > 0 ? '#1d4ed8' : '#94a3b8' }">已選 {{ selectionList.length }} 人</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-4 md:grid-cols-5 px-1 w-full mt-[15px]" style="gap: 4px; background: #ffffff;">
                            <button v-for="user in filteredUsers" :key="user.id" 
                                @click="addParticipant(user.name)"
                                class="flex items-center justify-center font-black text-[17px] transition-all active:scale-95 rounded-md border shadow-sm w-full min-h-[45px]"
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
                                <span v-if="isParticipantSelected(user.name)" class="text-[15px] mr-0.5 opacity-70">
                                    {{ getParticipantIndex(user.name) }}.
                                </span>
                                {{ user.name }}
                            </span>
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Confirm Action -->
            <div class="fixed bottom-[7vh] left-0 right-0 px-4 py-2 bg-white/95 backdrop-blur-md border-t border-slate-100 z-[200] flex justify-center">
                <button @click="!selectionFiltered ? toggleSelectionFilter() : goToStep2()" 
                    :disabled="selectionList.length === 0" 
                    class="w-full max-w-lg font-black text-[17px] py-3.5 rounded-2xl transition-all active:scale-[0.98] disabled:opacity-50 disabled:active:scale-100 flex items-center justify-center" 
                    :style="{ 
                        backgroundColor: !selectionFiltered ? '#1d4ed8' : '#16a34a',
                        boxShadow: 'none'
                    }"
                >
                    <span style="color: #ffffff !important;">{{ !selectionFiltered ? '完成人員選取 (進入排列)' : '確定順序 (進入核定表)' }}</span>
                </button>
            </div>
        </div>

        <!-- Step 2: Approval Table Page -->
        <div v-if="step === 2" class="flex flex-col h-full bg-white overflow-hidden relative">
            <div class="animate-fade-in flex flex-col h-full overflow-hidden">
                <div class="bg-white border-b border-slate-100 py-2 px-3 flex items-center justify-between shrink-0 shadow-sm z-10">
                    <div class="flex items-center space-x-2">
                        <button @click="step = 1" class="text-slate-400 hover:text-indigo-600 active:scale-90 transition-transform p-2 -ml-2 rounded-full hover:bg-slate-50">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7"></path></svg>
                        </button>
                        <h3 class="text-[17px] font-black text-slate-900 tracking-tight">核定結果</h3>
                    </div>
                </div>

                <div class="flex-1 overflow-y-auto py-4 flex flex-col custom-scrollbar" style="padding-left: 10px; padding-right: 10px;">
                    
                    <!-- Instruction Text -->
                    <div class="px-2 pb-4 mb-2 flex flex-col space-y-1">
                        <div class="text-[15px] font-black text-slate-700 tracking-wide">✓代表合格 ×代表不合格</div>
                        <div class="text-[15px] font-black text-slate-700 tracking-wide">開文結果請示如下：</div>
                    </div>

                    <!-- Ledger Table -->
                    <div class="w-full pb-32">
                        <table class="w-full text-left border-separate border-spacing-0 table-fixed">
                            <tbody>
                                <tr v-for="(item, idx) in selectionList" :key="idx" class="group transition-all h-[42px]">
                                    <td class="pl-3 pr-1 py-1 w-20 whitespace-nowrap align-middle border-b border-slate-50">
                                        <span class="text-[17px] font-black text-blue-600 leading-none tracking-widest">{{ item.name }}</span>
                                    </td>
                                    
                                    <td v-for="n in 3" :key="'slot'+n" class="text-center w-22 px-1 py-1 align-middle border-b border-slate-50">
                                        <div class="flex items-center border border-slate-300 rounded-lg bg-white overflow-hidden divide-x divide-slate-200 h-[36px] shadow-sm">
                                            <button @click="setStatus(idx, n-1, 'v')" 
                                                :class="[item.slots[n-1] === 'v' ? 'text-indigo-900 bg-indigo-100' : 'text-slate-300 hover:text-indigo-600 hover:bg-slate-50']"
                                                class="w-1/2 h-full flex items-center justify-center transition-colors active:bg-slate-100">
                                                <span class="text-[20px] font-black leading-none">√</span>
                                            </button>
                                            <button @click="setStatus(idx, n-1, 'x')" 
                                                :class="[item.slots[n-1] === 'x' ? 'text-rose-600 bg-rose-100' : 'text-slate-300 hover:text-rose-600 hover:bg-slate-50']"
                                                class="w-1/2 h-full flex items-center justify-center transition-colors active:bg-slate-100">
                                                <span class="text-[20px] font-black leading-none">×</span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="fixed bottom-[7vh] left-0 right-0 px-4 py-2 bg-white/95 backdrop-blur-md border-t border-slate-100 z-[200] flex justify-center">
                <button @click="copyToLine" class="w-full max-w-lg bg-emerald-600 h-14 rounded-2xl font-black transition-all active:scale-[0.98] text-[17px] tracking-widest flex items-center justify-center space-x-2" style="box-shadow: none;">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: white !important;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path></svg>
                    <span style="color: #ffffff !important;">複製貼 LINE</span>
                </button>
            </div>
        </div>


    </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import axios from 'axios';

const searchQuery = ref('');
const users = ref([]);
const selectionList = ref([]);
const step = ref(1);
const selectionFiltered = ref(false);

// Storage Keys
const STORAGE_KEY = 'fabou_kaiwen_draft_v2';

const saveToLocalStorage = () => {
    const data = {
        selectionList: selectionList.value,
        step: step.value
    };
    localStorage.setItem(STORAGE_KEY, JSON.stringify(data));
    
};

// Auto-save on any change
watch(selectionList, saveToLocalStorage, { deep: true });
watch(step, saveToLocalStorage);

const loadUsers = async () => {
    try {
        const res = await axios.get('/api/dharma-names-list');
        users.value = res.data;
        
        // Load Draft after users are ready
        const draft = localStorage.getItem(STORAGE_KEY);
        if (draft) {
            try {
                const parsed = JSON.parse(draft);
                let list = parsed.selectionList || [];
                // Migration: convert string array to object array if needed
                selectionList.value = list.map(item => typeof item === 'string' ? { name: item, slots: [null, null, null, null, null] } : item);
                if (parsed.step) step.value = parsed.step;
            } catch (err) {
                console.error('Failed to parse draft', err);
            }
        } else {
            const oldDraft = localStorage.getItem('fabou_kaiwen_draft');
            if (oldDraft) {
                try {
                    const parsed = JSON.parse(oldDraft);
                    let list = parsed.selectionList || [];
                    selectionList.value = list.map(item => typeof item === 'string' ? { name: item, slots: [null, null, null, null, null] } : item);
                } catch (err) {}
            }
        }
    } catch (e) {
        console.error('Failed to load users', e);
    }
};

const excludedNames = ['鳳尊', '金巧', '赤覺', '紫元', '鳳媓', '金忠', '金孝', '金諦', '金彩', '金德', '靈平', '金護', '靈情', '靈奇', '靈傾'];

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

const removeItem = (idx) => {
    selectionList.value.splice(idx, 1);
};

const clearAll = () => {
    if (confirm('確定要清空整份名單與表格嗎？')) {
        selectionList.value = [];
        step.value = 1;
        localStorage.removeItem(STORAGE_KEY);
        localStorage.removeItem('fabou_kaiwen_draft');
    }
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
        alert('請先加入至少一位待定法號。');
        return;
    }
    step.value = 2;
};

const copyToLine = () => {
    if (selectionList.value.length === 0) {
        alert('目前表格為空，無法複製。');
        return;
    }

    let text = `✓代表合格×代表不合格\n`;
    text += `開文結果請示如下：\n`;
    
    selectionList.value.forEach(item => {
        // Always output all 3 slots with full-width chars for consistent alignment
        let slots = item.slots.map(s => {
            if (s === 'v') return '✓';
            if (s === 'x') return '× ';
            return '　'; // full-width ideographic space (same width as Chinese char)
        });
        
        // Full-width semicolon as separator
        const results = slots.join('；');
        
        text += `${item.name}：${results}\n`;
    });

    navigator.clipboard.writeText(text).then(() => {
        alert('已複製到剪貼簿，快前往 LINE 貼上吧！');
    }).catch(err => {
        console.error('Copy failed', err);
        alert('複製失敗，請手動選取文字。');
    });
};

onMounted(loadUsers);

</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    width: 4px;
    height: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #e2e8f0;
    border-radius: 10px;
}

.animate-fade-in {
    animation: fadeIn 0.3s ease-out forwards;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.fade-enter-active, .fade-leave-active {
    transition: opacity 0.3s, transform 0.3s;
}
.fade-enter-from, .fade-leave-to {
    opacity: 0;
    transform: translate(-50%, -20px);
}
</style>
