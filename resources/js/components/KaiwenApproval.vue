<template>
    <div class="flex h-full bg-white overflow-hidden relative">
        
        <!-- Left Sidebar: Dharma Name Table (Toggleable) -->
        <div :class="[isCollapsed ? 'w-[60px]' : 'w-[75%]']" class="border-r border-slate-100 flex flex-col h-full bg-white font-sans transition-all duration-300 relative overflow-hidden">
            <div class="p-3 border-b border-slate-50 flex items-center justify-between bg-white shrink-0">
                <h3 v-if="!isCollapsed" class="text-[17px] font-black text-slate-900 flex items-center">
                    選擇法號
                </h3>
                <button @click="isCollapsed = !isCollapsed" class="p-2 -mr-2 text-slate-400 hover:text-indigo-600 transition-all active:scale-90">
                    <svg :class="isCollapsed ? 'rotate-180' : ''" class="w-6 h-6 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" /></svg>
                </button>
            </div>

            <!-- Quick Search / Add Input -->
            <div v-show="!isCollapsed" class="px-3 py-2 border-b border-slate-50 bg-slate-50/10">
                <div class="relative">
                    <input v-model="searchQuery" @keyup.enter="handleQuickAdd" placeholder="搜尋或直接輸入姓名..." 
                        class="w-full pl-9 pr-4 py-2 bg-white border border-slate-200 rounded-xl text-[15px] focus:ring-2 focus:ring-indigo-500/20 transition-all font-bold">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="2.5"/></svg>
                </div>
            </div>

            <!-- Dharma Name Grid (4 columns, tighter, frameless) -->
            <div class="flex-1 overflow-y-auto px-2 py-4 custom-scrollbar bg-white">
                <div :class="[isCollapsed ? 'grid-cols-1' : 'grid-cols-4']" class="grid gap-x-1 gap-y-1">
                    <button v-for="user in filteredUsers" :key="user.id" 
                        @click="addParticipant(user.name)"
                        class="h-[34px] flex items-center justify-center transition-all bg-transparent active:scale-95 hover:text-indigo-600">
                        <span :class="[isCollapsed ? 'text-[0px]' : 'text-[17px]']" class="font-black text-slate-900 transition-all truncate px-0.5 leading-none">
                            {{ user.name }}
                        </span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Right Content: Approval Table & Selection Queue -->
        <div :class="[isCollapsed ? 'flex-1 border-l border-slate-50' : 'w-[25%] opacity-40']" class="overflow-hidden flex flex-col h-full bg-slate-50/10 relative font-sans transition-all duration-300">
            <!-- Header Area -->
            <div class="bg-white border-b border-slate-50 px-2 pt-4 pb-1 flex items-center justify-between sticky top-0 z-10 shrink-0">
                <div class="flex flex-col">
                    <h2 v-if="isCollapsed" class="text-[17px] font-black text-blue-500 tracking-tight">核定結果</h2>
                    <span v-else class="text-[17px] font-black text-blue-500 uppercase tracking-tight">核定名單</span>
                </div>
                <button v-if="isCollapsed" @click="clearAll" class="text-slate-100 hover:text-red-500 p-1 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
            </div>

            <!-- Table Content -->
            <div class="flex-1 overflow-y-auto p-0 flex flex-col">
                <div class="w-full">
                    <!-- Instruction Text -->
                    <div v-if="isCollapsed" class="py-3 text-left space-y-0.5 animate-fade-in pl-2">
                        <p class="text-[15px] font-black text-slate-900 leading-tight">√ 代表合格 × 代表不合格</p>
                        <p class="text-[15px] font-black text-slate-900 leading-tight">開文結果請示如下：</p>
                    </div>

                    <!-- Right-side Selection Queue (Live Record Mode) -->
                    <div v-if="!isCollapsed" class="flex flex-col p-1 animate-fade-in divide-y divide-slate-100">
                        <div v-for="(item, idx) in selectionList" :key="'r'+idx" 
                            class="flex items-center justify-between py-1.5 px-0.5 group transition-colors">
                            <span class="text-[17px] font-black text-blue-500 truncate w-12">{{ item.name }}</span>
                            <!-- High-Contrast Live Slots -->
                            <div class="flex items-center border border-slate-300 rounded-lg bg-white overflow-hidden divide-x divide-slate-200 shrink-0">
                                <div v-for="n in 3" :key="'rslot'+n" class="flex items-center justify-center w-[46px] h-[28px] space-x-0 relative">
                                    <button @click="setStatus(idx, n-1, 'v')" 
                                        :class="[item.slots[n-1] === 'v' ? 'text-indigo-900 bg-indigo-100' : 'text-slate-300 hover:text-indigo-600']" 
                                        class="w-1/2 h-full flex items-center justify-center transition-all">
                                        <span class="text-[18px] font-black leading-none">√</span>
                                    </button>
                                    <button @click="setStatus(idx, n-1, 'x')" 
                                        :class="[item.slots[n-1] === 'x' ? 'text-rose-600 bg-rose-100' : 'text-slate-300 hover:text-rose-600']" 
                                        class="w-1/2 h-full flex items-center justify-center transition-all">
                                        <span class="text-[18px] font-black leading-none">×</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div v-if="selectionList.length === 0" class="w-full text-slate-200 text-[13px] font-bold py-10 text-center uppercase tracking-widest">
                            待選
                        </div>
                    </div>

                    <!-- Ledger Table (Ultra Density, 28px) -->
                    <table v-else class="w-full text-left border-separate border-spacing-0 table-fixed">
                        <tbody>
                            <tr v-for="(item, idx) in selectionList" :key="idx" class="group transition-all h-[28px]">
                                <td class="pl-0.5 pr-0.5 py-0 relative w-16 whitespace-nowrap align-middle">
                                    <span class="text-[17px] font-black text-blue-500 leading-none">{{ item.name }}</span>
                                    <button @click="removeItem(idx)" class="absolute -left-4 top-1/2 -translate-y-1/2 opacity-0 group-hover:opacity-100 p-2 text-rose-50 hover:text-rose-500 transition-opacity">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" fill-rule="evenodd" clip-rule="evenodd"/></svg>
                                    </button>
                                </td>
                                
                                <td v-for="n in 3" :key="'slot'+n" class="text-center w-22 px-1 py-0 h-[28px] align-middle">
                                    <!-- High-Contrast Interactive Grid -->
                                    <div class="flex items-center border border-slate-300 rounded-lg bg-white overflow-hidden divide-x divide-slate-200 h-[24px]">
                                        <button @click="setStatus(idx, n-1, 'v')" 
                                            :class="[item.slots[n-1] === 'v' ? 'text-indigo-900 bg-indigo-100' : 'text-slate-300 hover:text-indigo-600']"
                                            class="w-1/2 h-full flex items-center justify-center transition-all">
                                            <span class="text-[19px] font-black leading-none">√</span>
                                        </button>
                                        <button @click="setStatus(idx, n-1, 'x')" 
                                            :class="[item.slots[n-1] === 'x' ? 'text-rose-600 bg-rose-100' : 'text-slate-300 hover:text-rose-600']"
                                            class="w-1/2 h-full flex items-center justify-center transition-all">
                                            <span class="text-[19px] font-black leading-none">×</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="selectionList.length === 0">
                                <td colspan="4" class="py-32 text-center text-slate-300">
                                    <p class="text-[17px] font-bold text-slate-200 uppercase tracking-widest">請展開選單開始作業</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>

            <!-- Copy Button (Only in collapsed mode) -->
                    <div v-if="isCollapsed" class="mt-8 mb-20 flex justify-center w-full">
                        <button @click="copyToLine" class="w-full max-w-xs bg-emerald-600 text-white h-14 rounded-2xl font-black shadow-xl shadow-emerald-900/10 hover:bg-emerald-700 transition-all active:scale-95 text-[17px] uppercase tracking-widest">
                            複製貼 LINE
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Draft Saved Toast (Floating) -->
        <transition name="fade">
            <div v-if="showSavedToast" class="fixed top-6 left-1/2 -translate-x-1/2 bg-slate-900/90 text-white px-4 py-2 rounded-full text-[13px] font-bold shadow-2xl z-[100] flex items-center space-x-2 border border-white/10 backdrop-blur-md">
                <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                <span>進度已暫存覽器</span>
            </div>
        </transition>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import axios from 'axios';

const searchQuery = ref('');
const users = ref([]);
const selectionList = ref([]);
const isCollapsed = ref(true); // Default to table-view for better mobile visibility
const showSavedToast = ref(false);

// Storage Keys
const STORAGE_KEY = 'fabou_kaiwen_draft';

const saveToLocalStorage = () => {
    const data = {
        selectionList: selectionList.value,
        isCollapsed: isCollapsed.value
    };
    localStorage.setItem(STORAGE_KEY, JSON.stringify(data));
    
    // Show toast briefly
    showSavedToast.value = true;
    setTimeout(() => { showSavedToast.value = false; }, 2000);
};

// Auto-save on any change
watch(selectionList, saveToLocalStorage, { deep: true });
watch(isCollapsed, saveToLocalStorage);

const loadUsers = async () => {
    try {
        const res = await axios.get('/api/dharma-names-list');
        users.value = res.data;
        
        // Load Draft after users are ready
        const draft = localStorage.getItem(STORAGE_KEY);
        if (draft) {
            try {
                const parsed = JSON.parse(draft);
                selectionList.value = parsed.selectionList || [];
                // Respect saved state if it exists, otherwise use default true
                if (Object.prototype.hasOwnProperty.call(parsed, 'isCollapsed')) {
                    isCollapsed.value = parsed.isCollapsed;
                }
            } catch (err) {
                console.error('Failed to parse draft', err);
            }
        }
    } catch (e) {
        console.error('Failed to load users', e);
    }
};

const excludedNames = ['鳳尊', '金巧', '赤覺', '紫元', '鳳媓', '金忠', '金孝', '金諦', '金彩', '金德', '靈平', '金護', '靈情'];

const filteredUsers = computed(() => {
    let list = users.value.filter(u => !excludedNames.includes(u.name));
    if (!searchQuery.value) return list;
    const q = searchQuery.value.toLowerCase();
    return list.filter(u => u.name.toLowerCase().includes(q));
});

const addParticipant = (name) => {
    if (!name) return;
    if (selectionList.value.some(item => item.name === name)) {
        alert('此法號已在清單中。');
        return;
    }
    selectionList.value.push({
        name: name,
        slots: [null, null, null]
    });
};

const handleQuickAdd = () => {
    if (!searchQuery.value) return;
    addParticipant(searchQuery.value.trim());
    searchQuery.value = '';
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
    if (confirm('確定要清空整份表格嗎？')) {
        selectionList.value = [];
        localStorage.removeItem(STORAGE_KEY);
    }
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
        
        // Full-width semicolon as separator — same width as Chinese char in LINE
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

.fade-enter-active, .fade-leave-active {
    transition: opacity 0.3s, transform 0.3s;
}
.fade-enter-from, .fade-leave-to {
    opacity: 0;
    transform: translate(-50%, -20px);
}
</style>
