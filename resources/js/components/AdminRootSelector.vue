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
            <admin-dashboard></admin-dashboard>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import TeachingManager from './TeachingManager.vue';
import GrudgeManager from './GrudgeManager.vue';
import ImperialGraceManager from './ImperialGraceManager.vue';
import TreasureManager from './TreasureManager.vue';

const currentView = ref('menu'); // 'menu', 'teaching', 'grudge', 'grace', etc.

const handleNavigate = (view) => {
    currentView.value = view;
};
</script>
