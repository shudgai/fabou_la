<template>
    <div class="flex flex-col md:flex-row min-h-screen bg-white md:bg-white font-sans">
        <!-- Mobile Top Nav -->
        <div class="md:hidden sticky top-0 z-[100] bg-white border-b border-gray-100 shadow-sm overflow-x-auto custom-scrollbar no-scrollbar py-2">
            <div class="flex items-center space-x-4 px-4 min-w-max">
                <button v-for="item in filteredMenuItems" :key="'mob-' + item.id"
                    @click="currentTab = item.id"
                    class="relative py-1 px-1 transition-all duration-300"
                    :class="[currentTab === item.id ? 'text-indigo-600 font-normal' : 'text-slate-400 font-normal']">
                    <span class="text-[14px] whitespace-nowrap">{{ item.label }}</span>
                    <div v-if="currentTab === item.id" class="absolute -bottom-1 left-0 right-0 h-0.5 bg-indigo-600 rounded-full animate-fade-in"></div>
                </button>
            </div>
        </div>

        <!-- Sidebar (Desktop Only) -->
        <div class="hidden md:flex w-72 bg-white border-r border-gray-100 flex-shrink-0 flex-col shadow-[2px_0_8px_rgba(0,0,0,0.02)]">
            <!-- Header -->
            <div class="p-5">
                <h1 class="text-[26px] font-normal text-[#1a202c]">皇恩筆記本</h1>
            </div>

            <!-- Menu items -->
            <div class="flex-1 overflow-y-auto">
                <button v-for="item in filteredMenuItems" :key="item.id" 
                    @click="currentTab = item.id"
                    :class="[
                        'flex items-center justify-between w-full border-b border-gray-50 transition-all duration-200 group relative',
                        currentTab === item.id ? 'bg-slate-50' : 'hover:bg-gray-50'
                    ]"
                    style="padding: 20px 20px;">
                    
                    <!-- Active Indicator Bar -->
                    <div v-if="currentTab === item.id" class="absolute left-0 top-0 bottom-0 w-1.5 bg-indigo-600 rounded-r-md"></div>

                    <span :class="[
                        'text-[18px] font-normal whitespace-nowrap transition-colors',
                        currentTab === item.id ? 'text-indigo-600' : 'text-[#4a5568]'
                    ]">{{ item.label }}</span>
                    
                    <svg :class="['h-4 w-4 transition-all duration-300', currentTab === item.id ? 'text-indigo-400 translate-x-1' : 'text-gray-300 group-hover:translate-x-1']" 
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            </div>

            <!-- Version or Footer (Optional) -->
            <div class="p-4 border-t border-gray-50 text-[10px] text-gray-300 text-center">
                皇恩筆記本 管理系統
            </div>
        </div>

        <!-- Content Area -->
        <div class="flex-1 overflow-auto bg-white">
            <div class="p-0 md:p-8 max-w-7xl mx-auto h-full">
                <transition name="fade" mode="out-in">
                    <div :key="currentTab">
                        <div v-if="currentTab === 'grace'">
                            <imperial-grace-manager></imperial-grace-manager>
                        </div>
                        <div v-else-if="currentTab === 'teaching'">
                            <teaching-manager></teaching-manager>
                        </div>
                        <div v-else-if="currentTab === 'grudge'">
                            <grudge-manager></grudge-manager>
                        </div>
                        <div v-else-if="currentTab === 'military'">
                            <military-manager></military-manager>
                        </div>
                        <div v-else-if="currentTab === 'treasure'">
                            <registry-manager></registry-manager>
                        </div>
                        <div v-else-if="currentTab === 'trash'" class="flex flex-col items-center justify-center p-20 text-gray-300 bg-gray-50/50 rounded-3xl border-2 border-dashed border-gray-100 min-h-[600px]">
                            <h2 class="text-3xl font-black mb-2">回收桶</h2>
                            <p class="text-lg">系統功能開發中</p>
                        </div>
                    </div>
                </transition>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, watch, onMounted, computed } from 'vue';
import axios from 'axios';
import RegistryManager from './RegistryManager.vue';
import ImperialGraceManager from './ImperialGraceManager.vue';
import TeachingManager from './TeachingManager.vue';
import GrudgeManager from './GrudgeManager.vue';
import MilitaryManager from './MilitaryManager.vue';

const props = defineProps({
    initialTab: {
        type: String,
        default: 'grace'
    }
});

const menuItems = [
    { id: 'grace', label: '重大皇恩專區' },
    { id: 'teaching', label: '父皇仙師開示專區' },
    { id: 'grudge', label: '怨靈專區' },
    { id: 'military', label: '軍隊專區' },
    { id: 'treasure', label: '法寶登記專區' },
    { id: 'trash', label: '回收桶' },
];

const user = ref(null);
const filteredMenuItems = computed(() => {
    return menuItems.filter(item => {
        if (item.id === 'treasure') {
            return user.value?.dharma_name?.name === '赤覺';
        }
        return true;
    });
});

const currentTab = ref(props.initialTab);

watch(() => props.initialTab, (newTab) => {
    currentTab.value = newTab;
});

onMounted(async () => {
    try {
        const res = await axios.get('/api/user-profile');
        user.value = res.data;
    } catch (e) {
        console.error('Failed to load user profile', e);
    }
});
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
.no-scrollbar::-webkit-scrollbar {
  display: none;
}
.no-scrollbar {
  -ms-overflow-style: none;
  scrollbar-width: none;
}
.animate-fade-in {
  animation: fadeIn 0.3s ease-out;
}
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(2px); }
  to { opacity: 1; transform: translateY(0); }
}
</style>
