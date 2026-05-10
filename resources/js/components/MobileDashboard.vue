<template>
    <div class="h-[100dvh] bg-white flex flex-col overflow-hidden">
        <!-- 標題區域 -->
        <div style="padding: 8px 20px 4px 20px;" class="flex items-center justify-between bg-white border-b border-slate-50 min-h-[50px] shrink-0">
            <logo-imperial-notebook :height="40" />

            <div class="flex items-center">
                <button @click="handleLogout" class="p-2 text-red-500 active:scale-90 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
            </div>
        </div>

        <!-- 現代化條列式專區 (Scrollable Area) -->
        <div class="flex-1 overflow-y-auto custom-scrollbar bg-slate-50/20 py-2 px-4">
            <div class="flex flex-col space-y-1 pb-24">
                <button v-for="item in filteredMenuItems" :key="item.id" 
                    @click="navigate(item.id)"
                    class="flex items-center justify-between w-full bg-white active:bg-indigo-50 active:scale-[0.98] transition-all duration-200 rounded-2xl border border-slate-100 h-[64px] shrink-0 overflow-hidden relative group"
                    style="padding: 0 20px;">
                        <div class="flex flex-col items-start text-left">
                            <span class="!text-[22px] font-black text-slate-800 tracking-tight leading-tight">{{ item.label }}</span>
                            <span v-if="counts[item.id]" class="text-[16px] font-normal text-slate-400 mt-0.5">{{ counts[item.id] }} 筆</span>
                        </div>
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-slate-50 rounded-full flex items-center justify-center group-active:bg-indigo-100 transition-colors">
                            <svg class="h-4 w-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7" />
                            </svg>
                        </div>
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
import { ref, onMounted, computed, watch } from 'vue';
import axios from 'axios';
import MobileNavbar from './MobileNavbar.vue';
import LogoImperialNotebook from './LogoImperialNotebook.vue';
import { lockBodyScroll, unlockBodyScroll } from '../utils/iosCompat';

const persistentToast = ref(null);

const menuItems = [
    { id: 'grace', label: '重大皇恩專區', icon: '👑', color: 'bg-amber-50' },
    { id: 'teaching', label: '父皇仙師開示專區', icon: '🙏', color: 'bg-indigo-50' },
    { id: 'kaiwen', label: '開文專區', icon: '📝', color: 'bg-purple-50' },
    { id: 'grudge', label: '怨靈記錄專區', icon: '👻', color: 'bg-rose-50' },
    { id: 'military', label: '軍隊記錄專區', icon: '🛡️', color: 'bg-emerald-50' },
    { id: 'treasure', label: '法寶登記專區', icon: '💎', color: 'bg-sky-50' },
    { id: 'other_teaching', label: '其他記錄專區', icon: '📜', color: 'bg-orange-50' },
    { id: 'other', label: '抽籤專區', icon: '🎰', color: 'bg-orange-50' },
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

const props = defineProps(['user', 'counts']);
const user = computed(() => props.user);
const counts = computed(() => props.counts || {});
const permissions = computed(() => user.value?.permissions || {});

const filteredMenuItems = computed(() => {
    return menuItems.filter(item => {
        if (item.id === 'admin') return user.value?.is_admin;
        if (user.value?.is_admin) return true;
        if (item.id === 'teaching') return permissions.value.can_see_teaching_folders;
        if (item.id === 'treasure') return permissions.value.can_see_treasures;
        if (item.id === 'military') return permissions.value.can_see_military;
        if (item.id === 'other' || item.id === 'other_teaching') return permissions.value.can_see_other_folders;
        if (item.id === 'trash') return permissions.value.can_see_trash;
        return true;
    });
});

onMounted(() => {
    // Counts are now handled by AdminRootSelector and passed via props
});

const handleLogout = () => {
    persistentToast.value = { msg: '確定要登出系統嗎？' };
};

watch(persistentToast, (newVal) => {
    if (newVal) lockBodyScroll();
    else unlockBodyScroll();
});

const executeLogout = async () => {
    try {
        await axios.post('/logout');
        window.location.href = '/login';
    } catch (e) {
        window.location.href = '/login';
    }
};
</script>
