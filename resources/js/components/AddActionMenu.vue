<template>
    <div v-if="show" class="fixed inset-0 z-[55] flex items-end justify-center px-4 pb-20">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-slate-900/20 backdrop-blur-[2px]" @click="$emit('close')"></div>
        
        <!-- Menu -->
        <div class="relative w-full max-w-[340px] bg-white rounded-[32px] shadow-[0_24px_70px_rgba(0,0,0,0.22)] border border-slate-50/50 overflow-hidden animate-menu-up p-2">
            <div class="flex flex-col space-y-0.5">
                <button v-for="action in actions" 
                    :key="action.label"
                    @click="handleAction(action)"
                    class="w-full px-4 py-2.5 hover:bg-slate-50 active:bg-slate-100 rounded-[22px] flex items-center transition-all group relative overflow-hidden"
                >
                    <div :class="action.colorClass || (action.bgColor && action.textColor ? `${action.bgColor} ${action.textColor}` : 'bg-indigo-50 text-indigo-600')" class="w-10 h-10 rounded-[14px] flex items-center justify-center mr-3.5 group-active:scale-90 transition-all shadow-sm shrink-0">
                        <div v-if="action.icon" v-html="action.icon" class="w-5 h-5 flex items-center justify-center stroke-[2.2]"></div>
                    </div>
                    <div class="text-left flex-1 min-w-0">
                        <p class="text-[16px] font-bold text-slate-900 leading-tight mb-0.5 group-hover:text-indigo-600 transition-colors truncate">{{ action.label }}</p>
                        <p v-if="action.description" class="text-[13px] text-slate-400 font-normal leading-tight truncate">{{ action.description }}</p>
                    </div>
                    <div class="ml-2 opacity-0 group-hover:opacity-100 transition-opacity shrink-0">
                        <svg class="w-4 h-4 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </div>
                </button>
            </div>
            
            <div class="mt-1 mb-1 px-2">
                <button @click="$emit('close')" class="w-full py-3 font-bold text-slate-400 hover:text-slate-600 transition-colors uppercase tracking-[0.2em] text-center text-[12px] bg-slate-50/30 rounded-xl active:scale-[0.98]">
                    取消
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
const props = defineProps({
    show: Boolean,
    actions: {
        type: Array,
        required: true
        // action: { label, description, icon, colorClass, handler }
    }
});

const emit = defineEmits(['close']);

const handleAction = (action) => {
    action.handler();
    emit('close');
};
</script>

<style scoped>
.animate-menu-up {
    animation: menuUp 0.4s cubic-bezier(0.16, 1, 0.3, 1);
}
@keyframes menuUp {
    from { opacity: 0; transform: translateY(40px) scale(0.95); }
    to { opacity: 1; transform: translateY(0) scale(1); }
}
</style>
