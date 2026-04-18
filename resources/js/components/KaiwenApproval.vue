<template>
    <div class="flex h-full bg-white overflow-hidden">
        
        <!-- Left Sidebar: Dharma Name Table (COL-5) -->
        <div class="w-[41.66%] border-r border-slate-100 flex flex-col h-full bg-white font-sans pl-[5px]">
            <div class="p-1 border-b border-slate-50">
                <h3 class="text-[17px] font-black text-slate-900 flex items-center">
                    法號表
                </h3>
            </div>

            <!-- Tighter Grid (4px padding container) -->
            <div class="flex-1 overflow-y-auto px-[5px] py-1 custom-scrollbar">
                <div class="grid grid-cols-4 gap-x-[3px] gap-y-[1px]">
                    <button v-for="user in filteredUsers" :key="user.id" 
                        @click="addParticipant(user.name)"
                        class="h-[24px] flex items-center justify-center transition-all group active:scale-95">
                        <span class="text-[15px] font-bold truncate px-0.5 text-slate-800 group-hover:text-indigo-600">
                            {{ user.name }}
                        </span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Right Content: Approval Table (COL-8.5) -->
        <div class="flex-1 overflow-hidden flex flex-col h-full bg-slate-50/10 relative border-l border-slate-100 font-sans">
            <!-- Header Area (4px padding) -->
            <div class="bg-white border-b border-slate-50 p-1 flex items-center justify-between sticky top-0 z-10 gap-4">
                <div class="flex flex-col pl-[8px]">
                    <h2 class="text-lg font-black text-slate-900 tracking-tight">開文核定表</h2>
                </div>
                <button @click="clearAll" class="text-slate-300 hover:text-red-500 p-1 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
            </div>

            <!-- Table Content (Centered with 6px Right Offset) -->
            <div class="flex-1 overflow-y-auto p-1 flex flex-col items-center pb-24">
                <div class="w-full pl-[6px]">
                    <div class="mt-1 mb-2 text-left space-y-1">
                        <p class="text-[15px] font-black text-black tracking-tight">√ 代表合格 × 代表不合格</p>
                        <p class="text-[15px] font-black text-black tracking-tight">開文結果請示如下：</p>
                    </div>

                    <table class="w-auto text-left border-separate border-spacing-y-[3px] table-fixed">
                        <thead>
                            <tr>
                                <th class="px-0.5 py-0 text-[15px] font-bold text-black border border-slate-200 bg-white w-[45px] rounded-tl"></th>
                                <th class="px-0.5 py-0 border border-slate-200 text-center text-[13px] font-bold text-slate-300 bg-white uppercase w-[60px]"></th>
                                <th class="px-0.5 py-0 border border-slate-200 text-center text-[13px] font-bold text-slate-300 bg-white uppercase w-[60px]"></th>
                                <th class="px-0.5 py-0 border border-slate-200 text-center text-[13px] font-bold text-slate-300 bg-white uppercase rounded-tr w-[60px]"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(item, idx) in selectionList" :key="idx" class="group bg-white transition-shadow h-[18px]">
                                <td class="px-0.5 py-0 relative border border-slate-200 w-[45px]">
                                    <span class="text-[15px] font-bold text-slate-900 leading-none flex items-center h-full">{{ item.name }}</span>
                                    <button @click="removeItem(idx)" class="absolute -left-2 top-1/2 -translate-y-1/2 opacity-0 group-hover:opacity-100 text-red-100 hover:text-red-400 transition-opacity">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" /></svg>
                                    </button>
                                </td>
                                
                                <td v-for="n in 3" :key="'slot'+n" class="text-center group/cell border border-slate-200 w-[60px] py-0 h-[18px]">
                                    <!-- Selection Mode -->
                                    <div v-if="item.slots[n-1] === null" class="flex items-center justify-center space-x-4 h-full w-[60px]">
                                        <button @click="setStatus(idx, n-1, 'v')" class="text-slate-200 hover:text-slate-400 transition-colors">
                                            <span class="text-[13px] font-black leading-none">√</span>
                                        </button>
                                        <button @click="setStatus(idx, n-1, 'x')" class="text-slate-200 hover:text-slate-400 transition-colors">
                                            <span class="text-[13px] font-black leading-none">×</span>
                                        </button>
                                    </div>
                                    <!-- Confirmed Mode -->
                                    <div v-else class="flex items-center justify-center h-full w-[60px] relative group/conf">
                                        <span v-if="item.slots[n-1] === 'v'" class="text-blue-900 text-[13px] font-black leading-none">√</span>
                                        <span v-else class="text-red-600 text-[13px] font-black leading-none">×</span>
                                        
                                        <!-- Persistent Edit Button (Visible at low opacity) -->
                                        <button @click="setStatus(idx, n-1, null)" class="absolute right-0 bottom-0 p-0.5 opacity-30 hover:opacity-100 transition-opacity">
                                            <svg class="w-2.5 h-2.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="selectionList.length === 0">
                                <td colspan="4" class="py-32 text-center text-slate-300">
                                    <p class="text-[14px] font-bold text-slate-200 uppercase tracking-widest">請從左側點選加入</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Sticky Bottom Copy Button Area -->
            <div class="absolute bottom-0 left-0 right-0 p-4 bg-white/80 backdrop-blur-sm border-t border-slate-100 flex justify-center">
                <button @click="copyToLine" class="w-1/2 bg-emerald-600 text-white h-11 rounded-xl font-bold shadow-lg shadow-emerald-100 hover:bg-emerald-700 transition-all active:scale-95 text-[14px]">
                    複製貼 LINE
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';

const searchQuery = ref('');
const users = ref([]);
const selectionList = ref([]);

const loadUsers = async () => {
    try {
        const res = await axios.get('/api/dharma-names-list');
        users.value = res.data;
    } catch (e) {
        console.error('Failed to load users', e);
    }
};

const filteredUsers = computed(() => {
    if (!searchQuery.value) return users.value;
    const q = searchQuery.value.toLowerCase();
    return users.value.filter(u => u.name.toLowerCase().includes(q));
});

const addParticipant = (name) => {
    if (selectionList.value.some(item => item.name === name)) {
        alert('此法號已在清單中。');
        return;
    }
    selectionList.value.push({
        name: name,
        slots: [null, null, null]
    });
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
    }
};

const copyToLine = () => {
    if (selectionList.value.length === 0) {
        alert('目前表格為空，無法複製。');
        return;
    }

    let text = `✓ 代表合格，✕ 代表不合格\n`;
    text += `開文結果請示如下：\n`;
    
    selectionList.value.forEach(item => {
        let slots = item.slots.map(s => {
            if (s === 'v') return '✓';
            if (s === 'x') return '×';
            return '  ';
        });

        // Remove trailing empty placeholders to avoid trailing semicolons
        let lastRealIndex = -1;
        for (let i = slots.length - 1; i >= 0; i--) {
            if (slots[i] !== '  ') {
                lastRealIndex = i;
                break;
            }
        }
        
        const results = (lastRealIndex === -1) 
            ? '  ' 
            : slots.slice(0, lastRealIndex + 1).join(' ;  ');
        
        text += `${item.name} ： ${results}\n`;
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
</style>
