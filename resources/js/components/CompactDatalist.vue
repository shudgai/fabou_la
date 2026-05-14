<template>
    <div v-if="filteredOptions.length > 0" class="flex flex-wrap gap-1.5 mt-1.5 overflow-x-auto pb-1 custom-scrollbar no-scrollbar-mobile md:hidden">
        <button 
            v-for="opt in filteredOptions" 
            :key="opt.value"
            type="button"
            @click="$emit('update:modelValue', opt.value)"
            class="whitespace-nowrap px-3 py-1.5 rounded-xl border border-slate-200 bg-white shadow-sm text-[14px] font-black text-slate-600 active:scale-95 transition-all"
        >
            {{ opt.label }}
        </button>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    options: {
        type: [Array, Object],
        default: () => []
    },
    modelValue: [String, Number]
});

const emit = defineEmits(['update:modelValue']);

const normalizedOptions = computed(() => {
    if (Array.isArray(props.options)) {
        return props.options.map(opt => {
            if (opt === null || opt === undefined) return null;
            if (typeof opt === 'object') {
                return { value: opt.value, label: opt.label || opt.value };
            }
            return { value: opt, label: opt };
        }).filter(Boolean);
    }
    return [];
});

const filteredOptions = computed(() => {
    const val = String(props.modelValue || '').toLowerCase().trim();
    if (!val) {
        // If empty, maybe show first few or nothing? 
        // For remarks like "完畢", we want to show them even if empty.
        // For huge lists like dharma names, maybe only show when typing.
        if (normalizedOptions.value.length < 15) return normalizedOptions.value;
        return [];
    }
    return normalizedOptions.value.filter(opt => 
        String(opt.label).toLowerCase().includes(val) || 
        String(opt.value).toLowerCase().includes(val)
    ).slice(0, 15); // Cap at 15 for performance and UI
});
</script>

<style scoped>
.no-scrollbar-mobile::-webkit-scrollbar {
    display: none;
}
.no-scrollbar-mobile {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>
