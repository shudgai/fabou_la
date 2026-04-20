<template>
    <div class="flex flex-col md:flex-row h-[100dvh] bg-slate-50 font-sans overflow-hidden">
        <!-- Sidebar / Top Nav Container -->
        <div class="w-full md:w-72 bg-white border-r border-slate-100 flex-shrink-0 flex flex-col shadow-sm z-[50] h-auto md:h-full">
            <!-- Logo area -->
            <div class="p-4 md:p-6 border-b border-slate-50 flex items-center justify-between bg-white">
                <div>
                    <h1 class="text-xl md:text-2xl font-black text-slate-900 tracking-tight">{{ dashboardTitle }}</h1>
                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-1">System Controller</p>
                </div>
                <a href="/note" class="p-2 text-slate-300 hover:text-indigo-600 active:scale-95 transition-all">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </a>
            </div>

            <!-- Menus -->
            <div class="flex-1 flex md:flex-col items-center md:items-stretch overflow-x-auto md:overflow-y-auto no-scrollbar py-1 md:py-6 bg-white">
                <!-- Notebook Section -->
                <div v-if="notebookItems.length > 0" class="hidden md:block px-6 mb-2">
                    <span class="text-[10px] font-black text-slate-300 uppercase tracking-[0.2em]">筆記系統</span>
                </div>
                <button v-for="item in notebookItems" :key="item.id" 
                    @click="currentTab = item.id"
                    :class="[
                        'flex items-center px-4 md:px-6 py-2 md:py-3 transition-all duration-200 group relative whitespace-nowrap md:whitespace-normal',
                        currentTab === item.id ? 'text-indigo-600 bg-indigo-50/30' : 'text-slate-400 hover:text-slate-600 hover:bg-slate-50'
                    ]">
                    <div v-if="currentTab === item.id" class="hidden md:block absolute left-0 top-1 bottom-1 w-1 bg-indigo-600 rounded-r-full"></div>
                    <span class="text-[13px] md:text-[15px] font-bold">{{ item.label }}</span>
                </button>

                <!-- Admin Section -->
                <div v-if="adminItems.length > 0" class="hidden md:block px-6 mt-6 mb-2">
                    <span class="text-[10px] font-black text-slate-300 uppercase tracking-[0.2em]">系統管理</span>
                </div>
                <div v-if="notebookItems.length > 0 && adminItems.length > 0" class="md:h-px md:bg-slate-50 md:mx-4 md:my-2 md:hidden h-4 w-px bg-slate-200 mx-2"></div>
                
                <button v-for="item in adminItems" :key="item.id" 
                    @click="currentTab = item.id"
                    :class="[
                        'flex items-center px-4 md:px-6 py-2 md:py-3 transition-all duration-200 group relative whitespace-nowrap md:whitespace-normal',
                        currentTab === item.id ? 'text-indigo-600 bg-indigo-50/30' : 'text-slate-400 hover:text-slate-600 hover:bg-slate-50'
                    ]">
                    <div v-if="currentTab === item.id" class="hidden md:block absolute left-0 top-1 bottom-1 w-1 bg-indigo-600 rounded-r-full"></div>
                    <span class="text-[13px] md:text-[15px] font-bold">{{ item.label }}</span>
                </button>
            </div>

            <!-- Footer (Desktop Only) -->
            <div class="hidden md:block p-6 border-t border-slate-50 mt-auto">
                <div class="flex items-center space-x-3 text-slate-400">
                    <div class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></div>
                    <span class="text-xs font-bold tracking-tighter">系統運行穩定</span>
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <main class="flex-1 h-full overflow-y-auto bg-slate-50/30 custom-scrollbar relative">
            <div :class="[
                'max-w-7xl mx-auto w-full pb-32',
                isNotebookView ? 'p-0' : 'p-4 md:p-10 lg:p-12'
            ]">
                <transition name="fade-slide" mode="out-in">
                    <div :key="currentTab">
                        <component :is="currentComponent" :user="user" @go-home="currentTab = 'grace'" />
                    </div>
                </transition>
            </div>
        </main>
    </div>
</template>

<script setup>
import { ref, computed, markRaw, onMounted, watch } from 'vue';
import DharmaCrud from './admin/DharmaCrud.vue';
import UserCrud from './admin/UserCrud.vue';
import GroupCrud from './admin/GroupCrud.vue';
import MasterCrud from './admin/MasterCrud.vue';
import TreasureCrud from './admin/TreasureCrud.vue';

// Notebook Managers
import TeachingManager from './TeachingManager.vue';
import GrudgeManager from './GrudgeManager.vue';
import MilitaryManager from './MilitaryManager.vue';
import RegistryManager from './RegistryManager.vue';
import ImperialGraceManager from './ImperialGraceManager.vue';
import OtherManager from './OtherManager.vue';

const props = defineProps({
    initialTab: {
        type: String,
        default: 'dharma'
    }
});

const currentTab = ref(props.initialTab);

const dashboardTitle = computed(() => {
    return isNotebookView.value ? '皇恩筆記' : '系統管理';
});

const user = ref(null);

const notebookItems = computed(() => {
    if (!window.location.pathname.includes('/note')) return [];
    
    const items = [
        { id: 'grace', label: '重大皇恩專區' },
        { id: 'teaching', label: '父皇仙師開示記錄' },
        { id: 'grudge', label: '怨靈記錄專區' },
        { id: 'military', label: '軍隊記錄專區' },
        { id: 'treasure', label: '法寶登記專區' },
        { id: 'other', label: '其他專區' },
    ];

    if (!user.value) return items;

    return items.filter(item => {
        if (item.id === 'treasure') return user.value.permissions?.can_see_treasures;
        if (item.id === 'military') return user.value.permissions?.can_see_military;
        if (item.id === 'other') return user.value.permissions?.can_see_other_folders;
        return true;
    });
});

const adminItems = computed(() => {
    if (!window.location.pathname.includes('/admin')) return [];
    
    const items = [
        { id: 'dharma', label: '法號與人員' },
        { id: 'user', label: '帳號與權限' },
        { id: 'group', label: '群組與職務' },
        { id: 'master', label: '仙師管理' },
        { id: 'treasure_master', label: '法寶主檔' },
    ];

    if (!user.value) return items;
    if (!user.value.is_admin) return [];

    return items;
});

const components = {
    // Notebook
    grace: markRaw(ImperialGraceManager),
    teaching: markRaw(TeachingManager),
    grudge: markRaw(GrudgeManager),
    military: markRaw(MilitaryManager),
    treasure: markRaw(RegistryManager),
    other: markRaw(OtherManager),
    // Admin
    dharma: markRaw(DharmaCrud),
    user: markRaw(UserCrud),
    group: markRaw(GroupCrud),
    master: markRaw(MasterCrud),
    treasure_master: markRaw(TreasureCrud),
};

const isNotebookView = computed(() => {
    return notebookItems.value.some(item => item.id === currentTab.value);
});

const currentComponent = computed(() => components[currentTab.value]);

// Watch for tab changes to update URL hash if applicable
watch(currentTab, (newTab) => {
    // Only update hash if we are on the /note page (checking path)
    if (window.location.pathname.includes('/note')) {
        const hash = notebookItems.value.find(i => i.id === newTab) ? newTab : '';
        if (hash) {
            window.location.hash = hash === 'grace' ? 'imperial' : hash;
        } else {
            window.location.hash = '';
        }
    }
});

onMounted(async () => {
    try {
        const res = await axios.get('/api/user-profile');
        user.value = res.data;
    } catch (e) {
        console.error('Failed to load user profile in AdminDashboard', e);
    }

    if (props.initialTab && components[props.initialTab]) {
        currentTab.value = props.initialTab;
    }
});
</script>

<style scoped>
.fade-slide-enter-active,
.fade-slide-leave-active {
  transition: all 0.25s ease-out;
}
.fade-slide-enter-from {
  opacity: 0;
  transform: translateY(10px);
}
.fade-slide-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}

.no-scrollbar::-webkit-scrollbar {
  display: none;
}
.no-scrollbar {
  -ms-overflow-style: none;
  scrollbar-width: none;
}
.custom-scrollbar::-webkit-scrollbar {
  width: 5px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #e2e8f0;
  border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: #cbd5e1;
}
</style>
