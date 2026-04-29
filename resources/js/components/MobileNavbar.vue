<template>
    <div class="fixed bottom-0 left-0 right-0 bg-white/95 backdrop-blur-md border-t border-slate-200 z-[100] shadow-[0_-4px_20px_rgba(0,0,0,0.05)]" style="height: 7vh;">
        <div class="grid grid-cols-5 h-full items-center px-2">
            <!-- BACK BUTTON -->
            <div class="flex justify-center">
                <button @click="$emit('back')" 
                    :disabled="!canBack"
                    :class="[canBack ? 'text-slate-400 active:scale-95' : 'text-slate-300']"
                    class="h-full px-4 rounded-xl flex items-center justify-center transition-all">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" /></svg>
                </button>
            </div>

            <!-- HOME BUTTON -->
            <div class="flex justify-center">
                <button @click="$emit('home')" 
                    :disabled="!canHome"
                    :class="[activeTab === 'home' ? 'text-indigo-600' : (canHome ? 'text-slate-400 active:scale-95' : 'text-slate-300')]"
                    class="h-full px-4 rounded-xl flex items-center justify-center transition-all relative">
                    <div v-if="activeTab === 'home'" class="absolute top-1 w-2 h-2 bg-indigo-600 rounded-full"></div>
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
            </div>

            <!-- ACTION BUTTON (Center) -->
            <div class="flex justify-center items-center">
                <button @click="(!actionDisabled && showAction) && $emit('action')" 
                    :disabled="actionDisabled || !showAction"
                    :class="[
                        (!showAction || actionDisabled) ? 'bg-slate-100 text-slate-300 opacity-60 cursor-not-allowed' : 
                        (actionActive ? 'bg-slate-800 rotate-45 scale-90' : 'bg-red-600 text-white shadow-sm active:scale-95')
                    ]"
                    class="w-9 h-9 rounded-2xl flex items-center justify-center transition-all duration-300">
                    <svg class="h-[16px] w-[16px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
            </div>

            <!-- SEARCH BUTTON -->
            <div class="flex justify-center">
                <button @click="$emit('search')" 
                    :disabled="!canSearch"
                    :class="[searchActive ? 'text-indigo-600 bg-indigo-50' : (canSearch ? 'text-slate-400 active:scale-95' : 'text-slate-300')]"
                    class="h-full px-4 rounded-xl flex items-center justify-center transition-all">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" /></svg>
                </button>
            </div>

            <!-- FONT SIZE DROPDOWN (Rightmost) -->
            <div class="flex justify-center relative">
                <button @click="showFontMenu = !showFontMenu" 
                    class="h-full px-4 rounded-xl flex items-center justify-center transition-all text-indigo-600 bg-indigo-50/50 active:scale-95">
                    <svg class="w-6 h-6 transition-transform duration-500" :class="{'rotate-90': showFontMenu}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
                <div v-if="showFontMenu" class="absolute bottom-full right-0 mb-4 w-16 bg-white rounded-3xl shadow-[0_10px_40px_rgba(0,0,0,0.2)] border border-slate-100 p-4 z-[110] animate-slide-up flex flex-col items-center space-y-4">
                    <div class="h-44 flex flex-col items-center justify-between py-2 relative">
                        <span class="text-[14px] font-black text-slate-400">大</span>
                        <div class="relative w-1.5 h-24 bg-slate-100 rounded-full flex items-center justify-center">
                            <input type="range" min="0" max="2" step="1" 
                                   :value="sliderValue"
                                   @input="handleSliderInput"
                                   class="vertical-slider w-24 h-8 bg-transparent appearance-none cursor-pointer">
                        </div>
                        <span class="text-[14px] font-black text-slate-400">小</span>
                    </div>
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

const sliderValue = computed(() => {
    const map = { 'font-large': 2, 'font-medium': 1, 'font-small': 0 };
    return map[currentFontSize.value] ?? 1;
});

const handleSliderInput = (e) => {
    const val = parseInt(e.target.value);
    const map = { 2: 'font-large', 1: 'font-medium', 0: 'font-small' };
    setFontSize(map[val]);
};

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

/* Vertical Slider Styles */
.vertical-slider {
    transform: rotate(-90deg);
    -webkit-appearance: none;
    appearance: none;
}

.vertical-slider::-webkit-slider-runnable-track {
    width: 100%;
    height: 4px;
    background: transparent;
}

.vertical-slider::-webkit-slider-thumb {
    -webkit-appearance: none;
    height: 24px;
    width: 24px;
    border-radius: 50%;
    background: #4f46e5;
    cursor: pointer;
    margin-top: -10px;
    box-shadow: 0 0 10px rgba(79, 70, 229, 0.3);
    border: 2px solid white;
}

.vertical-slider::-moz-range-thumb {
    height: 24px;
    width: 24px;
    border-radius: 50%;
    background: #4f46e5;
    cursor: pointer;
    border: 2px solid white;
}
</style>
