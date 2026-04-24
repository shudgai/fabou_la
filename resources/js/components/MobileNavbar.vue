<template>
    <div class="fixed bottom-0 left-0 right-0 bg-white/95 backdrop-blur-md border-t border-slate-200 z-[100] shadow-[0_-4px_20px_rgba(0,0,0,0.05)]" style="height: 8.5vh;">
        <div class="grid grid-cols-5 h-full items-center px-2">
            <!-- BACK BUTTON -->
            <div class="flex justify-center">
                <button @click="$emit('back')" 
                    :disabled="!canBack"
                    :class="[canBack ? 'text-slate-400 active:scale-95' : 'text-slate-100']"
                    class="w-14 h-14 rounded-xl flex items-center justify-center transition-all">
                    <svg class="w-9 h-9" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" /></svg>
                </button>
            </div>

            <!-- HOME BUTTON -->
            <div class="flex justify-center">
                <button @click="$emit('home')" 
                    :disabled="!canHome"
                    :class="[activeTab === 'home' ? 'text-indigo-600' : (canHome ? 'text-slate-400 active:scale-95' : 'text-slate-100')]"
                    class="w-14 h-14 rounded-xl flex items-center justify-center transition-all relative">
                    <div v-if="activeTab === 'home'" class="absolute -top-1.5 w-2 h-2 bg-indigo-600 rounded-full"></div>
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
            </div>

            <!-- ACTION BUTTON (Center) -->
            <div class="flex justify-center items-center">
                <button v-if="showAction" @click="!actionDisabled && $emit('action')" 
                    :disabled="actionDisabled"
                    :class="[
                        actionDisabled ? 'bg-slate-200 text-slate-400 opacity-50 cursor-not-allowed' : 
                        (actionActive ? 'bg-slate-800 rotate-45 scale-90' : 'bg-indigo-600 text-white shadow-sm active:scale-95')
                    ]"
                    class="w-11 h-11 rounded-2xl flex items-center justify-center transition-all duration-300">
                    <svg class="h-[18px] w-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
                <div v-else class="w-9 h-9"></div>
            </div>

            <!-- SEARCH BUTTON -->
            <div class="flex justify-center">
                <button @click="$emit('search')" 
                    :disabled="!canSearch"
                    :class="[searchActive ? 'text-indigo-600 bg-indigo-50' : (canSearch ? 'text-slate-400 active:scale-95' : 'text-slate-100')]"
                    class="w-14 h-14 rounded-xl flex items-center justify-center transition-all">
                    <svg class="w-9 h-9" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" /></svg>
                </button>
            </div>

            <!-- FONT SIZE DROPDOWN (Rightmost) -->
            <div class="flex justify-center relative">
                <button @click="showFontMenu = !showFontMenu" 
                    class="w-14 h-14 rounded-xl flex items-center justify-center transition-all text-indigo-600 bg-indigo-50/50 active:scale-95 font-bold text-[14px]">
                    字{{ fontSizeLabel }}
                </button>
                <div v-if="showFontMenu" class="absolute bottom-full right-0 mb-2 w-20 bg-white rounded-2xl shadow-xl border border-slate-100 overflow-hidden z-[110] animate-slide-up">
                    <button v-for="(label, key) in {'font-small':'小', 'font-medium':'中', 'font-large':'大'}" 
                            :key="key"
                            @click="setFontSize(key); showFontMenu = false"
                            :class="currentFontSize === key ? 'text-indigo-600 bg-indigo-50' : 'text-slate-600'"
                            class="w-full py-3 text-center font-bold text-[15px] hover:bg-slate-50 transition-colors border-b last:border-b-0 border-slate-50">
                        字{{ label }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';

defineProps({
    canBack: { type: Boolean, default: true },
    canHome: { type: Boolean, default: true },
    showAction: { type: Boolean, default: true },
    actionActive: { type: Boolean, default: false },
    actionDisabled: { type: Boolean, default: false },
    canSearch: { type: Boolean, default: true },
    searchActive: { type: Boolean, default: false },
    activeTab: { type: String, default: '' }
});

defineEmits(['back', 'home', 'action', 'search']);

// Font Size System
const showFontMenu = ref(false);
const currentFontSize = ref(localStorage.getItem('fabou_font_size') || 'font-medium');
const fontSizeLabel = computed(() => {
    const opts = { 'font-small': '小', 'font-medium': '中', 'font-large': '大' };
    return opts[currentFontSize.value] || '中';
});

const setFontSize = (key) => {
    document.body.classList.remove('font-small', 'font-medium', 'font-large');
    document.body.classList.add(key);
    localStorage.setItem('fabou_font_size', key);
    currentFontSize.value = key;
};
</script>

<style scoped>
.animate-slide-up {
    animation: slideUp 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}
@keyframes slideUp {
    from { transform: translateY(10px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}
</style>
