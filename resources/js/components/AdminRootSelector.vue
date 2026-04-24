<template>
    <div>
        <!-- MOBILE VIEW -->
        <div class="block md:hidden">
            <mobile-dashboard v-if="currentView === 'menu'" :user="user" @navigate="handleNavigate"></mobile-dashboard>
            
            <div v-else>
                <teaching-manager v-if="currentView === 'teaching'" :user="user" @go-home="handleNavigate('menu')"></teaching-manager>
                <grudge-manager v-if="currentView === 'grudge'" :user="user" @go-home="handleNavigate('menu')"></grudge-manager>
                <imperial-grace-manager v-if="currentView === 'grace'" :user="user" @go-home="handleNavigate('menu')"></imperial-grace-manager>
                <registry-manager v-if="currentView === 'treasure'" :user="user" @go-home="handleNavigate('menu')"></registry-manager>
                <military-manager v-if="currentView === 'military'" :user="user" @go-home="handleNavigate('menu')"></military-manager>
                <other-manager v-if="currentView === 'other'" :user="user" @go-home="handleNavigate('menu')"></other-manager>
                <kaiwen-manager v-if="currentView === 'kaiwen'" @go-home="handleNavigate('menu')"></kaiwen-manager>
                <trash-manager v-if="currentView === 'trash'" :user="user" @go-home="handleNavigate('menu')"></trash-manager>
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
import OtherManager from './OtherManager.vue';
import KaiwenManager from './KaiwenManager.vue';
import AdminDashboard from './AdminDashboard.vue';
import MobileDashboard from './MobileDashboard.vue';
import TrashManager from './TrashManager.vue';

const user = ref(null);
const currentView = ref('menu');

const isChijue = computed(() => user.value?.dharma_name?.name === '赤覺' || isAdmin.value);
const isAdmin = computed(() => user.value?.is_admin || user.value?.role === 'admin' || user.value?.role === '管理員');

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
        'kaiwen': 'kaiwen'
    };
    
    let targetView = viewMap[hash] || 'menu';
    
    // Security Check: Unauthorized hash access (Relaxed for Admins)
    if (isAdmin.value) {
        currentView.value = targetView;
        return;
    }

    if (targetView === 'treasure' && !isChijue.value) {
        targetView = 'menu';
        window.location.hash = '';
    }

    if (targetView === 'military' && !user.value?.permissions?.can_see_military) {
        targetView = 'menu';
        window.location.hash = '';
    }

    if (targetView === 'other' && !user.value?.permissions?.can_see_other_folders) {
        targetView = 'menu';
        window.location.hash = '';
    }
    
    currentView.value = targetView;
};

const handleNavigate = (view) => {
    if (view === 'treasure' && !isChijue.value && !isAdmin.value) return;
    if (view === 'military' && !user.value?.permissions?.can_see_military) return;
    if (view === 'other' && !user.value?.permissions?.can_see_other_folders) return;

    currentView.value = view;
    // Optionally update hash as well
    const reverseMap = { 'treasure': '#treasure', 'grace': '#grace', 'teaching': '#teaching', 'grudge': '#grudge', 'military': '#military', 'other': '#other', 'kaiwen': '#kaiwen', 'menu': '' };
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
