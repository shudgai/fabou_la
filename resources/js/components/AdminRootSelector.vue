<template>
    <div class="h-[100dvh] overflow-hidden overscroll-none bg-white flex flex-col items-stretch relative">
        <!-- Global Transition Logo Overlay -->
        <div v-if="isGlobalTransitioning" class="fixed inset-0 z-[99999] bg-white flex flex-col items-center justify-center pointer-events-auto transition-opacity duration-300">
            <div class="relative flex flex-col items-center">
                <logo-imperial-notebook :height="120" spinning />
                <div class="mt-4 text-[17px] font-black text-slate-400 tracking-widest animate-pulse">介面切換中...</div>
            </div>
        </div>

        <!-- GLOBAL SVG DEFINITIONS (Fixed for iOS Safari stability across all sub-views) -->
        <svg style="width:0; height:0; position:absolute;" aria-hidden="true" focusable="false">
            <defs>
                <!-- Teaching Manager Gradients -->
                <linearGradient id="tm-dailyGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" style="stop-color:rgb(255, 230, 0);stop-opacity:1" />
                    <stop offset="50%" style="stop-color:rgb(255, 200, 0);stop-opacity:1" />
                    <stop offset="100%" style="stop-color:rgb(255, 170, 0);stop-opacity:1" />
                </linearGradient>
                <linearGradient id="tm-mastersGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" style="stop-color:rgb(255, 120, 120);stop-opacity:1" />
                    <stop offset="50%" style="stop-color:rgb(255, 50, 50);stop-opacity:1" />
                    <stop offset="100%" style="stop-color:rgb(220, 0, 0);stop-opacity:1" />
                </linearGradient>
                <linearGradient id="tm-folderGradBase" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" style="stop-color:rgb(255, 120, 120);stop-opacity:1" />
                    <stop offset="50%" style="stop-color:rgb(255, 50, 50);stop-opacity:1" />
                    <stop offset="100%" style="stop-color:rgb(220, 0, 0);stop-opacity:1" />
                </linearGradient>

                <!-- Registry Manager Gradients -->
                <linearGradient id="rm-gold-grad-global" x1="0%" y1="0%" x2="100%" y2="0%">
                    <stop offset="9%" stop-color="rgba(255, 242, 143, 1)" />
                    <stop offset="19%" stop-color="rgba(255, 237, 137, 1)" />
                    <stop offset="34%" stop-color="rgba(255, 233, 133, 1)" />
                    <stop offset="49%" stop-color="rgba(254, 220, 117, 1)" />
                    <stop offset="67%" stop-color="rgba(252, 215, 127, 1)" />
                    <stop offset="82%" stop-color="rgba(249, 208, 140, 1)" />
                    <stop offset="98%" stop-color="rgba(252, 176, 69, 1)" />
                </linearGradient>

                <!-- Other Manager Gradients -->
                <linearGradient id="om-redGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" style="stop-color:rgb(255, 120, 120);stop-opacity:1" />
                    <stop offset="50%" style="stop-color:rgb(255, 50, 50);stop-opacity:1" />
                    <stop offset="100%" style="stop-color:rgb(220, 0, 0);stop-opacity:1" />
                </linearGradient>
                <linearGradient id="om-roundGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" style="stop-color:rgb(52, 211, 153);stop-opacity:1" />
                    <stop offset="50%" style="stop-color:rgb(16, 185, 129);stop-opacity:1" />
                    <stop offset="100%" style="stop-color:rgb(5, 150, 105);stop-opacity:1" />
                </linearGradient>

                <!-- Imperial Grace Manager Gradients -->
                <linearGradient id="ig-mastersGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" style="stop-color:rgb(255, 230, 0);stop-opacity:1" />
                    <stop offset="50%" style="stop-color:rgb(255, 200, 0);stop-opacity:1" />
                    <stop offset="100%" style="stop-color:rgb(255, 170, 0);stop-opacity:1" />
                </linearGradient>
                <linearGradient id="ig-unobtainedGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" style="stop-color:rgb(255, 230, 0);stop-opacity:1" />
                    <stop offset="50%" style="stop-color:rgb(255, 200, 0);stop-opacity:1" />
                    <stop offset="100%" style="stop-color:rgb(255, 170, 0);stop-opacity:1" />
                </linearGradient>
                <linearGradient id="ig-folderGradBase" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" style="stop-color:rgb(255, 230, 0);stop-opacity:1" />
                    <stop offset="50%" style="stop-color:rgb(255, 200, 0);stop-opacity:1" />
                    <stop offset="100%" style="stop-color:rgb(255, 170, 0);stop-opacity:1" />
                </linearGradient>
            </defs>
        </svg>
        <!-- MOBILE VIEW: Menu -> Manager (Back-to-Home Flow) -->
        <div class="block md:hidden flex-1 h-full overflow-hidden flex flex-col">
            <mobile-dashboard v-if="currentView === 'menu'" :user="user" @navigate="handleNavigate" :counts="counts"></mobile-dashboard>

            <div v-else class="flex-1 h-full overflow-hidden flex flex-col">
                <KeepAlive>
                    <teaching-manager v-if="currentView === 'teaching'" :user="user" @go-home="handleNavigate('menu')"></teaching-manager>
                    <grudge-manager v-if="currentView === 'grudge'" :user="user" @go-home="handleNavigate('menu')"></grudge-manager>
                    <imperial-grace-manager v-if="currentView === 'grace'" :user="user" @go-home="handleNavigate('menu')"></imperial-grace-manager>
                    <registry-manager v-if="currentView === 'treasure'" :user="user" @go-home="handleNavigate('menu')"></registry-manager>
                    <military-manager v-if="currentView === 'military'" :user="user" @go-home="handleNavigate('menu')"></military-manager>
                    <other-teaching-manager v-if="currentView === 'other_teaching'" :user="user" @go-home="handleNavigate('menu')"></other-teaching-manager>
                    <other-manager v-if="currentView === 'other'" :user="user" @go-home="handleNavigate('menu')"></other-manager>
                    <kaiwen-manager v-if="currentView === 'kaiwen'" :user="user" @go-home="handleNavigate('menu')"></kaiwen-manager>
                    <trash-manager v-if="currentView === 'trash'" :user="user" @go-home="handleNavigate('menu')"></trash-manager>
                </KeepAlive>
            </div>
        </div>

        <!-- DESKTOP VIEW: Sidebar + Manager (Two-Column Layout) -->
        <div class="hidden md:flex flex-row h-full w-full overflow-hidden">
            <!-- Sidebar -->
            <div class="w-[285px] h-full bg-white border-r border-slate-100 flex flex-col px-[10px] overflow-y-auto custom-scrollbar shrink-0 shadow-[4px_0_24px_rgba(0,0,0,0.02)]"
    style="padding-top: 15px; padding-bottom: calc(2rem + env(safe-area-inset-bottom, 0px));">
                <!-- Sidebar Logo Area -->
                <div class="mb-10 px-2 md:hidden">
                    <logo-imperial-notebook :height="52" />
                </div>

                <!-- Navigation List (Matched to Mobile Cards) -->
                <div class="flex flex-col space-y-1 pb-8 mt-[3px]">
                    <button v-for="item in filteredMenuItems" :key="item.id" 
                        @click="handleNavigate(item.id)"
                        :class="[
                            'flex items-center justify-between w-full transition-all duration-300 rounded-none h-[55px] shrink-0 group',
                            currentView === item.id 
                                ? 'bg-transparent' 
                                : 'bg-white hover:bg-slate-50'
                        ]"
                        style="padding: 0 15px;">
                        <div class="flex flex-col items-start text-left">
                            <span :class="[
                                '!text-[21px] font-black tracking-tight leading-tight transition-colors',
                                currentView === item.id ? 'text-blue-600' : 'text-slate-800'
                            ]">{{ item.label }}</span>
                            <span v-if="counts[item.id]" class="text-[14px] font-normal text-slate-400 mt-0.5">{{ counts[item.id] }} 筆</span>
                        </div>
                        <div class="flex items-center">
                            <svg :class="['h-6 w-6 transition-all', currentView === item.id ? 'text-blue-600 translate-x-1' : 'text-slate-200 group-hover:text-indigo-300 group-hover:translate-x-1']" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7" />
                            </svg>
                        </div>
                    </button>
                </div>
            </div>

            <!-- Content Area -->
            <div class="flex-1 h-full overflow-hidden flex flex-col bg-white relative">
                <KeepAlive>
                    <teaching-manager v-if="currentView === 'teaching'" :user="user" @go-home="handleNavigate('menu')" :is-desktop="true"></teaching-manager>
                    <grudge-manager v-if="currentView === 'grudge'" :user="user" @go-home="handleNavigate('menu')" :is-desktop="true"></grudge-manager>
                    <imperial-grace-manager v-if="currentView === 'grace'" :user="user" @go-home="handleNavigate('menu')" :is-desktop="true"></imperial-grace-manager>
                    <registry-manager v-if="currentView === 'treasure'" :user="user" @go-home="handleNavigate('menu')" :is-desktop="true"></registry-manager>
                    <military-manager v-if="currentView === 'military'" :user="user" @go-home="handleNavigate('menu')" :is-desktop="true"></military-manager>
                    <other-teaching-manager v-if="currentView === 'other_teaching'" :user="user" @go-home="handleNavigate('menu')" :is-desktop="true"></other-teaching-manager>
                    <other-manager v-if="currentView === 'other'" :user="user" @go-home="handleNavigate('menu')" :is-desktop="true"></other-manager>
                    <kaiwen-manager v-if="currentView === 'kaiwen'" :user="user" @go-home="handleNavigate('menu')" :is-desktop="true"></kaiwen-manager>
                    <trash-manager v-if="currentView === 'trash'" :user="user" @go-home="handleNavigate('menu')" :is-desktop="true"></trash-manager>
                </KeepAlive>

                <!-- Welcome State (Desktop menu) -->
                <div v-if="currentView === 'menu'" class="flex-1 flex flex-col items-center justify-center p-12 text-center">
                    <div class="w-32 h-32 bg-indigo-50 rounded-full flex items-center justify-center mb-6">
                        <svg class="w-16 h-16 text-indigo-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </div>
                    <h2 class="!text-3xl font-black text-slate-900 mb-2 font-outfit">歡迎使用皇恩筆記本</h2>
                    <p class="text-slate-400 text-[18px] max-w-md">請從左側選擇一個專區開始記錄或查看資料。</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed, defineAsyncComponent } from 'vue';
import axios from 'axios';
import MobileDashboard from './MobileDashboard.vue';
import LogoImperialNotebook from './LogoImperialNotebook.vue';

const TeachingManager = defineAsyncComponent(() => import('./TeachingManager.vue'));
const GrudgeManager = defineAsyncComponent(() => import('./GrudgeManager.vue'));
const MilitaryManager = defineAsyncComponent(() => import('./MilitaryManagerV2.vue'));
const RegistryManager = defineAsyncComponent(() => import('./RegistryManager.vue'));
const ImperialGraceManager = defineAsyncComponent(() => import('./ImperialGraceManager.vue'));
const OtherTeachingManager = defineAsyncComponent(() => import('./OtherTeachingManager.vue'));
const OtherManager = defineAsyncComponent(() => import('./OtherManager.vue'));
const KaiwenManager = defineAsyncComponent(() => import('./KaiwenManager.vue'));
const AdminDashboard = defineAsyncComponent(() => import('./AdminDashboard.vue'));
const TrashManager = defineAsyncComponent(() => import('./TrashManager.vue'));

const user = ref(null);
const currentView = ref('menu');
const counts = ref({});
const isGlobalTransitioning = ref(false);

const menuItems = [
    { id: 'grace', label: '重大皇恩專區', icon: '👑', color: 'bg-amber-50' },
    { id: 'teaching', label: '父皇仙師開示專區', icon: '🙏', color: 'bg-indigo-50' },
    { id: 'kaiwen', label: '開文專區', icon: '📝', color: 'bg-purple-50' },
    { id: 'grudge', label: '怨靈記錄專區', icon: '👻', color: 'bg-rose-50' },
    { id: 'military', label: '軍隊記錄專區', icon: '🛡️', color: 'bg-emerald-50' },
    { id: 'treasure', label: '特殊法寶登記區', icon: '💎', color: 'bg-sky-50' },
    { id: 'other_teaching', label: '其他記錄專區', icon: '📜', color: 'bg-orange-50' },
    { id: 'other', label: '抽籤專區', icon: '🎰', color: 'bg-orange-50' },
    { id: 'trash', label: '回收桶', icon: '🗑️', color: 'bg-slate-100' },
    { id: 'admin', label: '系統資料管理', icon: '⚙️', color: 'bg-slate-800 text-white' },
];

const isAdmin = computed(() => !!user.value?.is_admin);
const isChijue = computed(() => !!user.value?.is_chijue);
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
    } catch (e) { console.error(e); }
};

const syncHash = () => {
    if (!user.value) return; 

    const hash = window.location.hash.replace('#', '');
    const viewMap = {
        'treasure': 'treasure',
        'imperial': 'grace',
        'grace': 'grace',
        'grudge': 'grudge',
        'teaching': 'teaching',
        'military': 'military',
        'other': 'other',
        'other_teaching': 'other_teaching',
        'kaiwen': 'kaiwen',
        'trash': 'trash'
    };

    let targetView = viewMap[hash] || 'menu';

    // Security Check: Unauthorized hash access
    if (isAdmin.value) {
        currentView.value = targetView;
        return;
    }

    const perms = user.value.permissions || {};

    if (targetView === 'treasure' && !perms.can_see_treasures && !isChijue.value) {
        targetView = 'menu';
        window.location.hash = '';
    }

    if (targetView === 'teaching' && !perms.can_see_teaching_folders && !perms.can_see_daily_teachings) {
        targetView = 'menu';
        window.location.hash = '';
    }

    if (targetView === 'military' && !perms.can_see_military) {
        targetView = 'menu';
        window.location.hash = '';
    }

    if (targetView === 'other' && !perms.can_see_other_folders) {
        targetView = 'menu';
        window.location.hash = '';
    }

    if (targetView === 'trash' && !perms.can_see_trash) {
        targetView = 'menu';
        window.location.hash = '';
    }

    if (currentView.value !== targetView) {
        if (isAdmin.value) {
            currentView.value = targetView;
        } else {
            isGlobalTransitioning.value = true;
            setTimeout(() => {
                currentView.value = targetView;
                setTimeout(() => {
                    isGlobalTransitioning.value = false;
                }, 300);
            }, 300);
        }
    }
};

const handleNavigate = (view) => {
    const perms = user.value?.permissions || {};
    if (view === 'treasure' && !perms.can_see_treasures && !isAdmin.value) return;
    if (view === 'military' && !perms.can_see_military && !isAdmin.value) return;
    if (view === 'other' && !perms.can_see_other_folders && !isAdmin.value) return;
    if (view === 'trash' && !perms.can_see_trash && !isAdmin.value) return;

    if (currentView.value === view) return;

    // Refresh stats when returning home so counts are always current
    if (view === 'menu') {
        loadStats();
    }

    isGlobalTransitioning.value = true;
    // Use next tick to show overlay before expensive work
    requestAnimationFrame(() => {
        currentView.value = view;
        const reverseMap = { 
            'treasure': '#treasure', 'grace': '#grace', 'teaching': '#teaching', 
            'grudge': '#grudge', 'military': '#military', 'other': '#other', 
            'other_teaching': '#other_teaching', 'kaiwen': '#kaiwen', 
            'trash': '#trash', 'menu': '' 
        };
        window.location.hash = reverseMap[view] || '';
        
        requestAnimationFrame(() => {
            isGlobalTransitioning.value = false;
        });
    });
};

onMounted(async () => {
    try {
        const res = await axios.get('/api/user-profile');
        user.value = res.data;
        loadStats();
        syncHash(); 
    } catch (e) {
        console.error('Failed to load user profile', e);
    }
    window.addEventListener('hashchange', syncHash);
});
</script>

<style>
/* -------------------------------------------------------------------------- */
/* 全域大字體系統 (Global Large Font System) - 全部專區套用 21px */
/* -------------------------------------------------------------------------- */
body.font-large :where(.app-body, .app-title, p, td, span:not(.date-text):not(.shield-text), div:not(.date-text):not(.shield-text), h3, label, [class*="text-\["]:not(.text-\[30px\]):not(.text-\[28px\]):not(.text-\[40px\]):not([class*="!text-"])) {
    font-size: 21px !important;
}

body.font-large input,
body.font-large textarea,
body.font-large select {
    font-size: 21px !important;
}

/* 讓原本極小的標籤（如 13px-15px）在大字體模式下也有感提升 */
body.font-large :where(.text-\[13px\], .text-\[14px\], .text-\[15px\]) {
    font-size: 16px !important;
}
</style>
