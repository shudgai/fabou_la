<template>
    <div>
        <!-- MOBILE VIEW -->
        <div class="block md:hidden">
            <mobile-dashboard v-if="currentView === 'menu'" :user="user" @navigate="handleNavigate"></mobile-dashboard>
            
            <div v-else>
                <teaching-manager v-if="currentView === 'teaching'" @go-home="handleNavigate('menu')"></teaching-manager>
                <grudge-manager v-if="currentView === 'grudge'" @go-home="handleNavigate('menu')"></grudge-manager>
                <imperial-grace-manager v-if="currentView === 'grace'" @go-home="handleNavigate('menu')"></imperial-grace-manager>
                <registry-manager v-if="currentView === 'treasure'" @go-home="handleNavigate('menu')"></registry-manager>
                <military-manager v-if="currentView === 'military'" @go-home="handleNavigate('menu')"></military-manager>
                <div v-if="currentView === 'trash'" class="p-8 text-center">回收桶建置中...</div>
            </div>
        </div>

        <!-- DESKTOP VIEW -->
        <div class="hidden md:block">
            <admin-dashboard :initial-tab="currentView === 'menu' ? 'grace' : currentView"></admin-dashboard>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';
import TeachingManager from './TeachingManager.vue';
import GrudgeManager from './GrudgeManager.vue';
import MilitaryManager from './MilitaryManager.vue';
import RegistryManager from './RegistryManager.vue';
import ImperialGraceManager from './ImperialGraceManager.vue';
import AdminDashboard from './AdminDashboard.vue';
import MobileDashboard from './MobileDashboard.vue';

const user = ref(null);
const currentView = ref('menu');

const isChijue = computed(() => user.value?.dharma_name?.name === '赤覺');
const isAdmin = computed(() => user.value?.is_admin || user.value?.role === 'admin');

const syncHash = () => {
    if (!user.value) return; 
    
    const hash = window.location.hash.replace('#', '');
    const viewMap = {
        'treasure': 'treasure',
        'imperial': 'grace',
        'grace': 'grace',
        'grudge': 'grudge',
        'teaching': 'teaching',
        'military': 'military'
    };
    
    let targetView = viewMap[hash] || 'menu';
    
    // Security Check: Unauthorized hash access
    if (targetView === 'treasure' && !isChijue.value && !isAdmin.value) {
        targetView = 'menu';
        window.location.hash = '';
    }
    
    currentView.value = targetView;
};

const handleNavigate = (view) => {
    if (view === 'treasure' && !isChijue.value && !isAdmin.value) {
        return;
    }
    currentView.value = view;
    // Optionally update hash as well
    const reverseMap = { 'treasure': '#treasure', 'grace': '#grace', 'teaching': '#teaching', 'grudge': '#grudge', 'military': '#military', 'menu': '' };
    window.location.hash = reverseMap[view] || '';
};

onMounted(async () => {
    try {
        const res = await axios.get('/api/user-profile');
        user.value = res.data;
        syncHash(); 
    } catch (e) {
        console.error('Failed to load user profile', e);
    }
    window.addEventListener('hashchange', syncHash);
});
</script>
