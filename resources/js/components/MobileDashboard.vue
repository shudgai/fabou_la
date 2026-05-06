<template>
    <div class="h-[100dvh] bg-white flex flex-col overflow-hidden">
        <!-- 標題區域 -->
        <div style="padding: 16px 20px 8px 20px;" class="flex items-center justify-between bg-white border-b border-slate-50 min-h-[60px] shrink-0">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-slate-900 rounded-full flex items-center justify-center shadow-lg shrink-0 overflow-hidden border border-slate-800">
                    <svg class="w-full h-full p-0.5" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="50" cy="50" r="50" fill="white"/>
                        <path d="M50 0C22.3858 0 0 22.3858 0 50C0 77.6142 22.3858 100 50 100C50 75 50 75 50 50C50 25 50 25 50 0Z" fill="white"/>
                        <path d="M50 0C77.6142 0 100 22.3858 100 50C100 77.6142 77.6142 100 50 100V50V0Z" fill="black"/>
                        <path d="M50 100C36.1929 100 25 88.8071 25 75C25 61.1929 36.1929 50 50 50V100Z" fill="black"/>
                        <path d="M50 50C63.8071 50 75 38.8071 75 25C75 11.1929 63.8071 0 50 0V50Z" fill="white"/>
                        <circle cx="50" cy="75" r="8" fill="white"/>
                        <circle cx="50" cy="25" r="8" fill="black"/>
                    </svg>
                </div>
                <span class="text-2xl font-bold font-outfit tracking-tight text-slate-800 whitespace-nowrap">
                    皇恩筆記本
                </span>
            </div>
            
            <div class="flex items-center">
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
                    class="flex items-center justify-between w-full bg-white active:bg-indigo-50 active:scale-[0.98] transition-all duration-200 rounded-2xl border border-slate-100 h-[75px] shrink-0"
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

        <!-- Global Action Confirm / Toast (Critical for iOS) -->
        <div v-if="persistentToast" class="fixed inset-0 z-[9999] flex items-center justify-center p-6 bg-slate-900/40 backdrop-blur-sm animate-fade-in">
            <div class="bg-white w-full max-w-sm rounded-[32px] shadow-2xl overflow-hidden animate-slide-up border border-white/20">
                <div class="p-8 text-center space-y-6">
                    <div class="flex flex-col items-center">
                        <div class="w-16 h-16 bg-indigo-50 text-indigo-500 rounded-2xl flex items-center justify-center mb-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        </div>
                        <h3 class="text-[20px] font-black text-slate-900 leading-tight whitespace-pre-wrap">{{ persistentToast.msg }}</h3>
                    </div>

                    <div class="flex flex-col space-y-3">
                        <button @click="executeLogout" 
                                class="w-full py-4 bg-red-500 text-white rounded-2xl font-black text-[18px] active:scale-95 transition-all shadow-lg shadow-red-200/50" 
                                style="color: white !important;">
                            確認登出
                        </button>
                        <button @click="persistentToast = null" 
                                class="w-full py-4 rounded-2xl font-black text-[18px] active:scale-95 transition-all shadow-lg bg-slate-100 text-slate-500">
                            取消
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';
import MobileNavbar from './MobileNavbar.vue';

const persistentToast = ref(null);

const menuItems = [
    { id: 'grace', label: '重大皇恩專區', icon: '👑', color: 'bg-amber-50' },
    { id: 'teaching', label: '父皇仙師開示專區', icon: '🙏', color: 'bg-indigo-50' },
    { id: 'kaiwen', label: '開文專區', icon: '📝', color: 'bg-purple-50' },
    { id: 'grudge', label: '怨靈記錄專區', icon: '👻', color: 'bg-rose-50' },
    { id: 'military', label: '軍隊記錄專區', icon: '🛡️', color: 'bg-emerald-50' },
    { id: 'treasure', label: '法寶登記專區', icon: '💎', color: 'bg-sky-50' },
    { id: 'other_teaching', label: '其他記錄專區', icon: '📜', color: 'bg-orange-50' },
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
        // Special Rule: Admin Module access
        if (item.id === 'admin') return user.value?.is_admin;

        // Admin sees everything else by default
        if (user.value?.is_admin) return true;

        // Specific context permissions
        if (item.id === 'teaching') return permissions.value.can_see_teaching_folders;
        if (item.id === 'treasure') return permissions.value.can_see_treasures;
        if (item.id === 'military') return permissions.value.can_see_military;
        if (item.id === 'other' || item.id === 'other_teaching') return permissions.value.can_see_other_folders;
        if (item.id === 'trash') return permissions.value.can_see_trash;
        
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
        const [gre, tre, teach, grud, mil, kai, othTeach] = await Promise.allSettled([
            axios.get('/imperial-graces'),
            axios.get('/registries'),
            axios.get('/teachings'),
            axios.get('/grudges'),
            axios.get('/military-records'),
            axios.get('/kaiwen'),
            axios.get('/other-teachings')
        ]);
        
        if (gre.status === 'fulfilled') counts.value['grace'] = gre.value.data.registries?.total || gre.value.data.registries?.length || 0;
        if (tre.status === 'fulfilled') counts.value['treasure'] = tre.value.data?.total || tre.value.data?.length || 0;
        if (teach.status === 'fulfilled') {
            const res = teach.value.data;
            const recordsObj = res.records || res;
            counts.value['teaching'] = recordsObj.total !== undefined ? recordsObj.total : (recordsObj.data ? recordsObj.data.length : recordsObj.length || 0);
        }
        if (grud.status === 'fulfilled') counts.value['grudge'] = grud.value.data?.paginator?.total || grud.value.data?.total || grud.value.data?.length || 0;
        if (mil.status === 'fulfilled') counts.value['military'] = mil.value.data?.records?.total || mil.value.data?.total || mil.value.data?.length || 0;
        if (kai.status === 'fulfilled') {
            const kData = kai.value.data || {};
            counts.value['kaiwen'] = (kData.weeklyPosts?.length || 0) + (kData.selfPosts?.length || 0);
        }
        if (othTeach.status === 'fulfilled') counts.value['other_teaching'] = othTeach.value.data?.total || othTeach.value.data?.length || 0;
        
        stats.value.totalItems = (counts.value['grace'] || 0) + (counts.value['treasure'] || 0);
        if (grud.status === 'fulfilled') {
            const grudgeData = grud.value.data.data || grud.value.data;
            stats.value.todoGrudges = Array.isArray(grudgeData) ? grudgeData.filter(i => i.status === '待處理').length : 0;
        }
    } catch (e) { 
        if (e.response?.status !== 403) console.error(e); 
    }
};

const handleLogout = () => {
    persistentToast.value = { msg: '確定要登出系統嗎？' };
};

const executeLogout = async () => {
    try {
        await axios.post('/logout');
        window.location.href = '/login';
    } catch (e) {
        window.location.href = '/login';
    }
};

onMounted(loadStats);
</script>
