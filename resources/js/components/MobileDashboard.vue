<template>
    <div class="min-h-screen bg-white">
        <!-- 標題區域 -->
        <div style="padding: 12px 20px 4px 20px;" class="flex items-center justify-between bg-white">
            <h1 style="font-size: 26px; font-weight: 400; color: #0f172a; margin: 0; letter-spacing: -0.025em;">皇恩筆記本</h1>
        </div>

        <!-- 條列式專區 -->
        <div class="flex flex-col pb-20 mt-[30px]">
            <button v-for="item in filteredMenuItems" :key="item.id" 
                @click="navigate(item.id)"
                class="flex items-center justify-between w-full bg-white active:bg-slate-50 transition-colors relative h-[52px] border-b border-slate-50 last:border-b-0"
                style="padding: 0 15px;">
                <div class="flex items-center">
                    <span style="font-size: 20px; font-weight: 400; color: #1e293b; white-space: nowrap;">{{ item.label }}</span>
                </div>
                <div class="flex items-center space-x-2">
                    <svg class="h-5 w-5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                    </svg>
                </div>
            </button>
        </div>

        <!-- Dashboard Bottom Navbar -->
        <mobile-navbar 
            active-tab="home"
            :can-back="false"
            :show-action="false"
            :can-search="false"
            :can-more="false"
            @home="navigate('menu')"
        />
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';
import MobileNavbar from './MobileNavbar.vue';

const menuItems = [
    { id: 'grace', label: '重大皇恩專區' },
    { id: 'teaching', label: '父皇仙師開示專區' },
    { id: 'grudge', label: '怨靈專區' },
    { id: 'military', label: '軍隊專區' },
    { id: 'treasure', label: '法寶登記專區' },
    { id: 'trash', label: '回收桶' },
];

const emit = defineEmits(['navigate']);
const navigate = (id) => {
    emit('navigate', id);
};

const props = defineProps(['user']);
const user = computed(() => props.user);
const filteredMenuItems = computed(() => {
    return menuItems.filter(item => {
        if (item.id === 'treasure') {
            // Allow both Chijue and Admins/Managers
            return user.value?.dharma_name?.name === '赤覺' || user.value?.is_admin || user.value?.role === 'admin';
        }
        return true;
    });
});

const stats = ref({
    totalItems: 0,
    todoGrudges: 0
});

const counts = ref({});

const loadStats = async () => {
    try {
        const [gre, tre, teach, grud] = await Promise.allSettled([
            axios.get('/imperial-graces'),
            axios.get('/registries'),
            axios.get('/teachings'),
            axios.get('/grudges')
        ]);
        
        if (gre.status === 'fulfilled') counts.value['grace'] = gre.value.data.registries?.length || 0;
        if (tre.status === 'fulfilled') counts.value['treasure'] = tre.value.data?.length || 0;
        if (teach.status === 'fulfilled') counts.value['teaching'] = teach.value.data?.length || 0;
        if (grud.status === 'fulfilled') counts.value['grudge'] = grud.value.data?.length || 0;
        
        stats.value.totalItems = (counts.value['grace'] || 0) + (counts.value['treasure'] || 0);
        if (grud.status === 'fulfilled') stats.value.todoGrudges = grud.value.data?.filter(i => i.status === '待處理').length || 0;
    } catch (e) { 
        if (e.response?.status !== 403) console.error(e); 
    }
};

onMounted(loadStats);
</script>
