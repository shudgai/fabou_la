<template>
    <div class="h-[100dvh] bg-white flex flex-col overflow-hidden">
        <!-- 標題區域 -->
        <div style="padding: 16px 20px 8px 20px;" class="flex items-center justify-between bg-white border-b border-slate-50 min-h-[60px] shrink-0">
            <h1 style="font-size: 24px; font-weight: 900; color: #0f172a; margin: 0; letter-spacing: -0.025em;">皇恩筆記本</h1>
            <!-- Logged-in User Box with Logout -->
            <div class="flex items-center space-x-3">
                <div class="flex flex-col items-end">
                    <span class="text-[11px] font-black text-slate-400 uppercase tracking-wider leading-none mb-0.5">當前登入</span>
                    <span class="text-[17px] font-black text-slate-900 leading-none">{{ user?.name || '未知' }}</span>
                </div>
                <button @click="handleLogout" class="p-2 text-red-500 active:scale-90 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
            </div>
        </div>

        <!-- 現代化條列式專區 (Scrollable Area) -->
        <div class="flex-1 overflow-y-auto custom-scrollbar bg-slate-50/20 py-4 px-4">
            <div class="flex flex-col space-y-2 pb-24">
                <button v-for="item in filteredMenuItems" :key="item.id" 
                    @click="navigate(item.id)"
                    class="flex items-center justify-between w-full bg-white active:bg-indigo-50 active:scale-[0.98] transition-all duration-200 rounded-2xl border border-slate-100 h-[96px] shrink-0"
                    style="padding: 0 20px;">
                    <div class="flex items-center space-x-4">
                        <div class="flex flex-col items-start">
                            <span class="text-[24px] font-black text-slate-800 tracking-tight leading-tight">{{ item.label }}</span>
                            <span v-if="counts[item.id] !== undefined" class="text-[16px] text-slate-400 font-bold tracking-tight mt-0.5">
                                {{ counts[item.id] }} 筆記錄
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <svg class="h-5 w-5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                        </svg>
                    </div>
                </button>
            </div>
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
    { id: 'grace', label: '重大皇恩專區', icon: '👑', color: 'bg-amber-50' },
    { id: 'teaching', label: '父皇仙師開示記錄', icon: '🙏', color: 'bg-indigo-50' },
    { id: 'grudge', label: '怨靈記錄專區', icon: '👻', color: 'bg-rose-50' },
    { id: 'military', label: '軍隊記錄專區', icon: '🛡️', color: 'bg-emerald-50' },
    { id: 'treasure', label: '法寶登記專區', icon: '💎', color: 'bg-sky-50' },
    { id: 'other', label: '其他專區', icon: '📁', color: 'bg-orange-50' },
    { id: 'trash', label: '回收桶', icon: '🗑️', color: 'bg-slate-100' },
    { id: 'admin', label: '系統資料管理', icon: '⚙️', color: 'bg-slate-800 text-white' },
];

const emit = defineEmits(['navigate']);
const navigate = (id) => {
    if (id === 'admin') {
        window.location.href = '/admin';
        return;
    }
    emit('navigate', id);
};

const props = defineProps(['user']);
const user = computed(() => props.user);
const permissions = computed(() => user.value?.permissions || {});

const filteredMenuItems = computed(() => {
    return menuItems.filter(item => {
        // Special Rule: Trash is open to everyone
        if (item.id === 'trash') return true;
        
        // Special Rule: Admin is ONLY for '元續'
        if (item.id === 'admin') return user.value?.name === '元續';

        // Admin sees everything else by default
        if (user.value?.is_admin) return true;

        // Specific context permissions
        if (item.id === 'treasure') return permissions.value.can_see_treasures;
        if (item.id === 'military') return permissions.value.can_see_military;
        if (item.id === 'other') return permissions.value.can_see_other_folders;
        
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
        if (teach.status === 'fulfilled') {
            const res = teach.value.data;
            counts.value['teaching'] = res.data ? (res.total || res.data.length || 0) : (res.length || 0);
        }
        if (grud.status === 'fulfilled') counts.value['grudge'] = grud.value.data?.length || 0;
        
        stats.value.totalItems = (counts.value['grace'] || 0) + (counts.value['treasure'] || 0);
        if (grud.status === 'fulfilled') stats.value.todoGrudges = grud.value.data?.filter(i => i.status === '待處理').length || 0;
    } catch (e) { 
        if (e.response?.status !== 403) console.error(e); 
    }
};

const handleLogout = async () => {
    if (!confirm('確定要登出嗎？')) return;
    try {
        await axios.post('/logout');
        window.location.href = '/login';
    } catch (e) {
        window.location.href = '/login';
    }
};

onMounted(loadStats);
</script>
