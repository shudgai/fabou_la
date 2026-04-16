<template>
    <div class="fixed inset-x-0 bottom-[54px] z-[60] px-6 pb-2 animate-slide-up" v-if="show">
        <div class="bg-white rounded-full shadow-lg p-0 border border-black overflow-hidden">
            <div class="relative flex items-center">
                <div class="absolute left-3 text-slate-400">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <input 
                    ref="searchInput"
                    v-model="query" 
                    type="text" 
                    :placeholder="placeholder"
                    class="w-full bg-white border-none rounded-full pl-9 pr-9 py-[2.5px] text-[15.5px] leading-tight focus:ring-0 text-slate-700 placeholder:text-slate-300"
                    @input="$emit('update:modelValue', query)"
                >
                <button v-if="query" @click="clearSearch" class="absolute right-3 text-slate-400 hover:text-slate-600">
                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
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
