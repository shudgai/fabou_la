<template>
    <div :class="[
        isAbsolute ? 'absolute' : 'fixed',
        !isAbsolute ? 'md:left-64' : ''
    ]" class="bottom-0 left-0 right-0 bg-white border-t border-white z-[3000] shadow-[0_-4px_20px_rgba(0,0,0,0.05)]" style="height: calc(7vh + env(safe-area-inset-bottom)); padding-bottom: env(safe-area-inset-bottom);">
        <div class="grid grid-cols-5 h-full items-center px-2">
            <!-- BACK BUTTON -->
            <div class="flex justify-center">
                <button @click.stop="$emit('back')" 
                    :disabled="!canBack"
                    :class="[canBack ? 'text-indigo-600 active:scale-95' : 'text-slate-300']"
                    class="h-full px-4 rounded-none flex items-center justify-center transition-all">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" /></svg>
                </button>
            </div>

            <div class="flex justify-center">
                <button @click.stop="$emit('home')" 
                    :disabled="!canHome"
                    :class="[activeTab === 'home' ? 'text-indigo-600' : (canHome ? 'text-indigo-600 active:scale-95' : 'text-slate-300')]"
                    class="h-full px-4 rounded-none flex items-center justify-center transition-all relative">
                    <div v-if="activeTab === 'home'" class="absolute top-1 w-2 h-2 bg-indigo-600 rounded-none"></div>
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
            </div>

            <!-- ACTION BUTTON (Center) -->
            <div class="flex justify-center items-center">
                <button @click.stop="$emit('action')" 
                    :disabled="actionDisabled || !showAction"
                    :class="[
                        (!showAction || actionDisabled) ? 'bg-slate-100 text-slate-300 cursor-not-allowed' : 
                        (actionActive ? 'bg-slate-800 rotate-45 scale-90' : 'bg-indigo-600 text-white shadow-lg active:scale-95')
                    ]"
                    class="w-11 h-11 rounded-2xl flex items-center justify-center transition-all duration-300">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
            </div>

            <!-- SEARCH BUTTON -->
            <div class="flex justify-center">
                <button @click.stop="$emit('search')" 
                    :disabled="!canSearch"
                    :class="[searchActive ? 'text-indigo-600 bg-indigo-50' : (canSearch ? 'text-indigo-600 active:scale-95' : 'text-slate-300')]"
                    class="h-full px-4 rounded-none flex items-center justify-center transition-all">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" /></svg>
                </button>
            </div>

            <!-- FONT SIZE BUTTON -->
            <div class="flex justify-center relative">
                <button @click="showFontMenu = !showFontMenu" 
                    class="h-full px-4 rounded-none flex items-center justify-center transition-all text-indigo-600 active:scale-95">
                    <svg class="w-6 h-6 transition-transform duration-500" :class="{'rotate-90': showFontMenu}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
                <div v-if="showFontMenu" class="absolute bottom-full right-0 mb-4 w-16 bg-white rounded-none shadow-[0_10px_40px_rgba(0,0,0,0.2)] border border-slate-100 p-4 z-[110] animate-slide-up flex flex-col items-center space-y-4">
                    <div class="h-44 flex flex-col items-center justify-between py-2 relative">
                        <span class="!text-[13px] font-black text-slate-400">大</span>
                        <div class="relative w-8 h-24 flex items-center justify-center">
                            <span class="absolute left-[-16px] !text-[13px] font-black text-slate-400">中</span>
                             <input type="range" min="0" max="2" step="1" 
                                    :value="sliderValue"
                                    @input="handleSliderInput"
                                    @change="handleSliderFinish"
                                    @touchend="handleSliderFinish"
                                    class="vertical-slider w-8 h-24 bg-transparent cursor-pointer">
                        </div>
                        <span class="!text-[13px] font-black text-slate-400">小</span>
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
    activeTab: { type: String, default: '' },
    isAbsolute: { type: Boolean, default: false }
});

defineEmits(['back', 'home', 'action', 'search']);

// Font Size System
const showFontMenu = ref(false);
const currentFontSize = ref((function() { try { return localStorage.getItem('fabou_font_size') || 'font-medium'; } catch(e) { return 'font-medium'; } })());
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

const handleSliderFinish = () => {
    // Automatically close the menu after selection is finished
    setTimeout(() => { showFontMenu.value = false; }, 400);
};

const setFontSize = (key) => {
    document.body.classList.remove('font-small', 'font-medium', 'font-large');
    document.body.classList.add(key);
    try { localStorage.setItem('fabou_font_size', key); } catch(e) {}
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
    -webkit-appearance: none;
    appearance: none;
    writing-mode: vertical-lr;
    direction: rtl;
    width: 32px;
    height: 100%;
    cursor: pointer;
    touch-action: none; 
    background: transparent;
}

.vertical-slider::-webkit-slider-runnable-track {
    width: 6px;
    height: 100%;
    background: #f1f5f9;
    border-radius: 3px;
}

.vertical-slider::-webkit-slider-thumb {
    -webkit-appearance: none;
    height: 16px;
    width: 16px;
    border-radius: 50%;
    background: #4f46e5;
    cursor: pointer;
    box-shadow: 0 1px 6px rgba(79, 70, 229, 0.4);
    border: 2px solid white;
    position: relative;
    z-index: 20;
    /* Ensure centering on vertical track */
    transform: translateX(0);
}

.vertical-slider::-moz-range-thumb {
    height: 16px;
    width: 16px;
    border-radius: 50%;
    background: #4f46e5;
    cursor: pointer;
    border: 2px solid white;
    box-shadow: 0 1px 6px rgba(79, 70, 229, 0.4);
}
</style>
