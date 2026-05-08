<template>
    <div class="px-2 py-2 bg-white/95 animate-slide-down" v-if="show">
        <div class="relative group">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-indigo-400 group-focus-within:text-indigo-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </div>
            <input
                ref="searchInput"
                v-model="query"
                type="text"
                :placeholder="placeholder"
                class="block w-full pl-11 pr-12 h-[52px] bg-slate-50 border-2 border-transparent focus:border-indigo-100 focus:bg-white rounded-2xl text-[17px] font-black font-outfit text-slate-800 placeholder-slate-300 transition-all outline-none shadow-sm"
                @input="$emit('update:modelValue', query)"
            >
            <button v-if="query" @click="clearSearch" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-300 hover:text-red-500 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
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

const emit = defineEmits(['update:modelValue']);

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
</script>

<style scoped>
.animate-slide-down {
    animation: slideDown 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}
@keyframes slideDown {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>
