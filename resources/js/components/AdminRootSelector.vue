<template>
    <div>
        <!-- MOBILE VIEW -->
        <div class="block md:hidden">
            <mobile-dashboard v-if="currentView === 'menu'" @navigate="handleNavigate"></mobile-dashboard>
            
            <div v-else>
                <teaching-manager v-show="currentView === 'teaching'" @go-home="handleNavigate('menu')"></teaching-manager>
                <grudge-manager v-show="currentView === 'grudge'" @go-home="handleNavigate('menu')"></grudge-manager>
                <imperial-grace-manager v-show="currentView === 'grace'" @go-home="handleNavigate('menu')"></imperial-grace-manager>
                <treasure-manager v-show="currentView === 'treasure'" @go-home="handleNavigate('menu')"></treasure-manager>
                <div v-show="currentView === 'military'" class="p-8 text-center">軍隊專區建置中...</div>
                <div v-show="currentView === 'trash'" class="p-8 text-center">回收桶建置中...</div>
            </div>
        </div>

        <!-- DESKTOP VIEW -->
        <div class="hidden md:block">
            <admin-dashboard :initial-tab="currentView === 'menu' ? 'grace' : currentView"></admin-dashboard>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import TeachingManager from './TeachingManager.vue';
import GrudgeManager from './GrudgeManager.vue';
import ImperialGraceManager from './ImperialGraceManager.vue';
import TreasureManager from './TreasureManager.vue';
import AdminDashboard from './AdminDashboard.vue';

const getInitialView = () => {
    const hash = window.location.hash.replace('#', '');
    const viewMap = {
        'treasure': 'treasure',
        'imperial': 'grace',
        'grace': 'grace',
        'grudge': 'grudge',
        'teaching': 'teaching',
        'military': 'military'
    };
    return viewMap[hash] || 'menu';
};

const currentView = ref(getInitialView()); // Initialize immediately

const handleNavigate = (view) => {
    currentView.value = view;
};

const syncHash = () => {
    const hash = window.location.hash.replace('#', '');
    if (!hash && currentView.value !== 'menu') {
        // If hash cleared but we are in a view, don't necessarily go back to menu unless intended
        return;
    }
    
    const viewMap = {
        'treasure': 'treasure',
        'imperial': 'grace',
        'grace': 'grace',
        'grudge': 'grudge',
        'teaching': 'teaching',
        'military': 'military'
    };
    
    if (viewMap[hash]) {
        currentView.value = viewMap[hash];
    } else if (!hash) {
        currentView.value = 'menu';
    }
};

onMounted(() => {
    window.addEventListener('hashchange', syncHash);
});
</script>
