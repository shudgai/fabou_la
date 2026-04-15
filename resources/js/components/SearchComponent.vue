<template>
    <div class="fixed inset-x-0 bottom-[54px] z-[60] px-4 pb-4 animate-slide-up" v-if="show">
        <div class="bg-white rounded-3xl shadow-[0_20px_60px_rgba(0,0,0,0.15)] border border-slate-100 p-2">
            <div class="relative">
                <input 
                    ref="searchInput"
                    v-model="query" 
                    type="text" 
                    :placeholder="placeholder"
                    class="w-full bg-slate-50 border-none rounded-2xl pl-10 pr-10 py-3 text-sm focus:ring-2 focus:ring-indigo-500/20 text-slate-700"
                    @input="$emit('update:modelValue', query)"
                >
                <div class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <button v-if="query" @click="clearSearch" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 8.586L2.929 1.515 1.515 2.929 8.586 10l-7.071 7.071 1.414 1.414L10 11.414l7.071 7.071 1.414-1.414L11.414 10l7.071-7.071-1.414-1.414L10 8.586z"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue';

const props = defineProps({
    modelValue: String,
    show: Boolean,
    placeholder: {
        type: String,
        default: '搜尋內容...'
    }
});

const emit = defineEmits(['update:modelValue', 'close']);

const query = ref(props.modelValue);
const searchInput = ref(null);

watch(() => props.modelValue, (newVal) => {
    query.value = newVal;
});

watch(() => props.show, (newVal) => {
    if (newVal) {
        setTimeout(() => {
            searchInput.value?.focus();
        }, 100);
    }
});

const clearSearch = () => {
    query.value = '';
    emit('update:modelValue', '');
};

const close = () => {
    emit('close');
};
</script>

<style scoped>
.animate-slide-up {
    animation: slideUp 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}
@keyframes slideUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>
