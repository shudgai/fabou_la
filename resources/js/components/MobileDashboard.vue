<template>
    <div class="min-h-screen bg-white">
        <!-- 標題區域: 統一為 24px padding-top -->
        <div style="padding: 16px 15px 8px 15px;" class="flex items-center justify-between bg-white">
            <h1 style="font-size: 22px; font-weight: 400; color: #1a202c; margin: 0; letter-spacing: -0.025em;">皇恩筆記本</h1>
        </div>




        <!-- 條列式專區 -->
        <div class="flex flex-col pb-20">
        <!-- 條列式專區 -->
        <div class="flex flex-col pb-20">
            <button v-for="item in menuItems" :key="item.id" 
                @click="navigate(item.id)"
                class="flex items-center justify-between w-full bg-white active:bg-slate-50 transition-colors relative h-[56px]"
                style="padding: 0 15px;">
                <div class="flex items-center">
                    <span style="font-size: 16px; font-weight: 400; color: #334155; white-space: nowrap;">{{ item.label }}</span>
                </div>
                <div class="flex items-center space-x-2">
                    <svg class="h-4 w-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </div>
            </button>
        </div>
        </div>

        <!-- Dashboard Bottom Navbar -->
        <div class="fixed bottom-0 left-0 right-0 bg-white/95 backdrop-blur-md border-t border-slate-100 z-50 shadow-[0_-4px_10px_rgba(0,0,0,0.03)]" style="height: 60px;">
            <div class="grid grid-cols-5 h-full items-center px-2">
                <div class="flex justify-center flex-col items-center">
                    <div class="w-1 h-1 bg-transparent mb-1"></div>
                    <svg class="w-6 h-6 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" /></svg>
                </div>
                <div class="flex justify-center flex-col items-center">
                    <div class="w-1.5 h-1.5 bg-indigo-600 rounded-full mb-1"></div>
                    <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </div>
                <div class="flex justify-center -mt-6">
                    <div class="w-14 h-14 rounded-3xl bg-slate-100 flex items-center justify-center border-4 border-white shadow-sm">
                        <svg class="h-7 w-7 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </div>
                </div>
                <div class="flex justify-center pt-2">
                    <svg class="w-6 h-6 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" /></svg>
                </div>
                <div class="flex justify-center pt-2 text-slate-200">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const menuItems = [
    { id: 'grace', label: '重大皇恩' },
    { id: 'teaching', label: '父皇仙師開示專區' },
    { id: 'grudge', label: '怨靈專區' },
    { id: 'military', label: '軍隊專區' },
    { id: 'treasure', label: '法寶登記' },
    { id: 'trash', label: '回收桶' },
];

const emit = defineEmits(['navigate']);
const navigate = (id) => {
    emit('navigate', id);
};

const stats = ref({
    totalItems: 0,
    todoGrudges: 0
});

const counts = ref({});

const loadStats = async () => {
    try {
        const [gre, tre, teach, grud] = await Promise.all([
            axios.get('/imperial-graces'),
            axios.get('/registries'),
            axios.get('/teachings'),
            axios.get('/grudges')
        ]);
        
        counts.value['grace'] = gre.data.registries?.length || 0;
        counts.value['treasure'] = tre.data?.length || 0;
        counts.value['teaching'] = teach.data?.length || 0;
        counts.value['grudge'] = grud.data?.length || 0;
        
        stats.value.totalItems = (counts.value['grace'] || 0) + (counts.value['treasure'] || 0);
        stats.value.todoGrudges = grud.data?.filter(i => i.status === '代處理').length || 0;
    } catch (e) { console.error(e); }
};

onMounted(loadStats);
</script>
