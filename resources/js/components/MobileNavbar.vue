<template>
    <div class="fixed bottom-0 left-0 right-0 bg-white/95 backdrop-blur-md border-t border-slate-100 z-[100] shadow-[0_-4px_10px_rgba(0,0,0,0.03)]" style="height: 7vh;">
        <div class="grid grid-cols-5 h-full items-center px-2">
            <!-- BACK BUTTON -->
            <div class="flex justify-center">
                <button @click="$emit('back')" 
                    :disabled="!canBack"
                    :class="[canBack ? 'text-slate-400 active:scale-95' : 'text-slate-100']"
                    class="w-8 h-8 rounded-xl flex items-center justify-center transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" /></svg>
                </button>
            </div>

            <!-- HOME BUTTON -->
            <div class="flex justify-center">
                <button @click="$emit('home')" 
                    :disabled="!canHome"
                    :class="[activeTab === 'home' ? 'text-indigo-600' : (canHome ? 'text-slate-400 active:scale-95' : 'text-slate-100')]"
                    class="w-8 h-8 rounded-xl flex items-center justify-center transition-all relative">
                    <div v-if="activeTab === 'home'" class="absolute -top-1 w-1 h-1 bg-indigo-600 rounded-full"></div>
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
            </div>

            <!-- ACTION BUTTON (Center) -->
            <div class="flex justify-center items-center">
                <button v-if="showAction" @click="$emit('action')" 
                    :class="[actionActive ? 'bg-slate-800 rotate-45 scale-90' : 'bg-indigo-600 text-white shadow-sm active:scale-95']"
                    class="w-9 h-9 rounded-xl flex items-center justify-center transition-all duration-300">
                    <svg class="h-[14px] w-[14px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
                <div v-else class="w-9 h-9"></div>
            </div>

            <!-- SEARCH BUTTON -->
            <div class="flex justify-center">
                <button @click="$emit('search')" 
                    :disabled="!canSearch"
                    :class="[searchActive ? 'text-indigo-600 bg-indigo-50' : (canSearch ? 'text-slate-400 active:scale-95' : 'text-slate-100')]"
                    class="w-8 h-8 rounded-xl flex items-center justify-center transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" /></svg>
                </button>
            </div>

            <!-- MORE/EXPORT BUTTON -->
            <div class="flex justify-center">
                <button @click="$emit('more')" 
                    :disabled="!canMore"
                    :class="[moreActive ? 'text-indigo-600 bg-indigo-50' : (canMore ? 'text-slate-400 active:scale-95' : 'text-slate-100')]"
                    class="w-8 h-8 rounded-xl flex items-center justify-center transition-all">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
defineProps({
    canBack: { type: Boolean, default: true },
    canHome: { type: Boolean, default: true },
    showAction: { type: Boolean, default: true },
    actionActive: { type: Boolean, default: false },
    canSearch: { type: Boolean, default: true },
    searchActive: { type: Boolean, default: false },
    canMore: { type: Boolean, default: true },
    moreActive: { type: Boolean, default: false },
    activeTab: { type: String, default: '' }
});

defineEmits(['back', 'home', 'action', 'search', 'more']);
</script>
