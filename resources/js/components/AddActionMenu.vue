<template>
    <div v-if="show" class="fixed inset-0 z-[55] flex items-end justify-center px-4 pb-20">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-slate-900/20 backdrop-blur-[2px]" @click="$emit('close')"></div>
        
        <!-- Menu -->
        <div class="relative w-full max-w-sm bg-white rounded-[32px] shadow-[0_20px_60px_rgba(0,0,0,0.2)] border border-slate-100 overflow-hidden animate-menu-up p-2">
            <div class="flex flex-col" style="gap: 5px;">
                <button v-for="action in actions" 
                    :key="action.label"
                    @click="handleAction(action)"
                    class="w-full px-6 hover:bg-slate-50 active:bg-slate-100 rounded-2xl flex items-center transition-all group"
                    style="padding-top: 3px; padding-bottom: 3px;"
                >
                    <div :class="action.colorClass || 'bg-indigo-100 text-indigo-600'" class="w-8 h-8 rounded-xl flex items-center justify-center mr-3 group-active:scale-90 transition-transform">
                        <div v-if="action.icon" v-html="action.icon" class="w-4 h-4 flex items-center justify-center"></div>
                    </div>
                    <div class="text-left font-bold" style="font-size: 15px;">
                        <p class="text-slate-800 leading-none">{{ action.label }}</p>
                        <p v-if="action.description" class="text-slate-400 font-normal leading-tight mt-1" style="font-size: 13px;">{{ action.description }}</p>
                    </div>
                </button>
            </div>
            
            <button @click="$emit('close')" class="w-full mt-1 py-2 font-bold text-slate-400 hover:text-slate-600 transition-colors uppercase tracking-widest text-center" style="font-size: 13px;">
                取消
            </button>
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
