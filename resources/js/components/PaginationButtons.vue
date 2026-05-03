<template>
    <div v-if="meta && meta.last_page > 1" class="flex items-center justify-center space-x-2 py-6 px-4">
        <!-- Previous Page -->
        <button 
            @click="emit('page-change', meta.current_page - 1)" 
            :disabled="meta.current_page === 1"
            class="flex items-center justify-center w-10 h-10 rounded-xl border transition-all active:scale-95 disabled:opacity-30 disabled:active:scale-100"
            :class="meta.current_page === 1 ? 'border-slate-100 text-slate-300' : 'border-slate-200 text-slate-600 bg-white hover:border-slate-300 shadow-sm'"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path d="M15 19l-7-7 7-7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
        </button>

        <!-- Page Indicator -->
        <div class="px-4 py-2 bg-slate-50 rounded-xl border border-slate-100 text-slate-600 font-black text-[14px] flex items-center space-x-1">
            <span class="text-slate-900">{{ meta.current_page }}</span>
            <span class="text-slate-300">/</span>
            <span>{{ meta.last_page }}</span>
        </div>

        <!-- Next Page -->
        <button 
            @click="emit('page-change', meta.current_page + 1)" 
            :disabled="meta.current_page === meta.last_page"
            class="flex items-center justify-center w-10 h-10 rounded-xl border transition-all active:scale-95 disabled:opacity-30 disabled:active:scale-100"
            :class="meta.current_page === meta.last_page ? 'border-slate-100 text-slate-300' : 'border-slate-200 text-slate-600 bg-white hover:border-slate-300 shadow-sm'"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path d="M9 5l7 7-7 7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
        </button>
    </div>
</template>

<script setup>
const props = defineProps({
    meta: {
        type: Object,
        required: true
    }
});

const emit = defineEmits(['page-change']);
</script>
