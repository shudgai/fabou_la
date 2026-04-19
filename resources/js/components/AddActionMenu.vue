<template>
    <div v-if="show" class="fixed inset-0 z-[55] flex items-end justify-center px-4 pb-20">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-slate-900/20 backdrop-blur-[2px]" @click="$emit('close')"></div>
        
        <!-- Menu -->
        <div class="relative w-full max-w-[240px] bg-white rounded-[24px] shadow-[0_16px_40px_rgba(0,0,0,0.15)] border border-slate-100/50 overflow-hidden animate-menu-up p-1">
            <div class="flex flex-col space-y-0">
                <button v-for="action in actions" 
                    :key="action.label"
                    @click="handleAction(action)"
                    class="w-full px-3 py-1.5 hover:bg-slate-50 active:bg-slate-100 rounded-[18px] flex items-center transition-all group relative overflow-hidden"
                >
                    <div :class="action.colorClass || (action.bgColor && action.textColor ? `${action.bgColor} ${action.textColor}` : 'bg-indigo-50 text-indigo-600')" class="w-8 h-8 rounded-[10px] flex items-center justify-center mr-2.5 group-active:scale-90 transition-all shadow-sm shrink-0">
                        <div v-if="action.icon" v-html="action.icon" class="w-4 h-4 flex items-center justify-center stroke-[2.2]"></div>
                    </div>
                    <div class="text-left flex-1 min-w-0">
                        <p class="text-[14px] font-bold text-slate-900 leading-tight group-hover:text-indigo-600 transition-colors truncate">{{ action.label }}</p>
                        <p v-if="action.description" class="text-[11px] text-slate-400 font-normal leading-tight truncate">{{ action.description }}</p>
                    </div>
                </button>
            </div>
            
            <div class="mt-0.5 mb-1 px-1">
                <button @click="$emit('close')" class="w-full py-2 font-bold text-slate-400 hover:text-slate-600 transition-colors uppercase tracking-[0.2em] text-center text-[11px] active:scale-[0.98]">
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
