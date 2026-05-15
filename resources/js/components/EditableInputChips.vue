<template>
    <div class="editable-input-chips-container w-full">
        <!-- Input Wrapper -->
        <div class="relative flex items-center min-h-[46px] transition-all duration-200 border-b-2"
             :class="isFocused ? 'border-indigo-500' : 'border-slate-300'">
            
            <div class="flex flex-wrap items-center gap-2 w-full py-1 px-1">
                <!-- Selected Value as a Chip -->
                <div v-if="modelValue && !isEditing" 
                     @click="startEditing"
                     class="flex items-center bg-indigo-50 border border-indigo-100 rounded-xl px-3 py-1 animate-fade-in cursor-pointer hover:bg-indigo-100 transition-colors mx-auto shrink-0 max-w-full">
                    <span class="text-indigo-600 font-black text-[17px] whitespace-pre-wrap text-center">{{ modelValue }}</span>
                    <button @click.stop="clearValue" class="ml-2 text-indigo-300 hover:text-indigo-600 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Hidden Input for actual typing -->
                <textarea 
                    v-else
                    ref="inputRef"
                    :value="modelValue"
                    @input="handleInput"
                    @focus="isFocused = true"
                    @blur="handleBlur"
                    rows="3"
                    class="flex-1 bg-transparent border-none outline-none text-[17px] font-black text-slate-900 text-center w-full min-w-[120px] placeholder:text-slate-200 resize-none leading-relaxed"
                    :placeholder="placeholder"
                ></textarea>
            </div>
        </div>

        <!-- Suggestions (Chips Scroll Area) -->
        <div v-if="filteredOptions.length > 0" ref="suggestionsRef"
             class="mt-3 flex flex-nowrap gap-2 overflow-x-auto no-scrollbar pb-2 custom-scrollbar scroll-smooth">
            <button 
                v-for="opt in filteredOptions" 
                :key="typeof opt === 'object' ? opt.value : opt"
                type="button"
                @click="selectOption(opt)"
                class="whitespace-nowrap px-5 py-2.5 rounded-2xl border border-slate-200 bg-white shadow-sm text-[16px] font-black text-slate-700 active:scale-95 transition-all shrink-0 hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-600"
            >
                {{ typeof opt === 'object' ? opt.label : opt }}
            </button>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, nextTick, watch } from 'vue';

const props = defineProps({
    modelValue: {
        type: String,
        default: ''
    },
    options: {
        type: Array,
        default: () => []
    },
    placeholder: {
        type: String,
        default: '搜尋或輸入...'
    }
});

const emit = defineEmits(['update:modelValue', 'change']);

const isFocused = ref(false);
const isEditing = ref(false);
const inputRef = ref(null);
const suggestionsRef = ref(null);

// --- 1. Normalized & Filtered Data ---
const normalizedOptions = computed(() => {
    return props.options.map(opt => {
        if (typeof opt === 'object') return opt;
        return { value: opt, label: opt };
    });
});

const filteredOptions = computed(() => {
    const search = String(props.modelValue || '').toLowerCase().trim();
    if (!search) {
        // If empty, show some default suggestions if any
        return normalizedOptions.value.slice(0, 15);
    }
    return normalizedOptions.value.filter(opt => 
        String(opt.label).toLowerCase().includes(search) || 
        String(opt.value).toLowerCase().includes(search)
    ).slice(0, 15);
});

// --- 2. Watchers ---
watch(filteredOptions, () => {
    if (filteredOptions.value.length > 0) {
        nextTick(() => {
            suggestionsRef.value?.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
        });
    }
});

// --- 3. Methods ---
const startEditing = () => {
    isEditing.value = true;
    nextTick(() => {
        inputRef.value?.focus();
    });
};

const finishEditing = () => {
    isEditing.value = false;
};

const handleBlur = () => {
    isFocused.value = false;
    // Don't immediately exit editing if they clicked a suggestion
    setTimeout(() => {
        isEditing.value = false;
    }, 200);
};

const handleInput = (e) => {
    emit('update:modelValue', e.target.value);
};

const clearValue = () => {
    emit('update:modelValue', '');
    startEditing();
};

const selectOption = (opt) => {
    const val = typeof opt === 'object' ? opt.value : opt;
    emit('update:modelValue', val);
    emit('change', val);
    isEditing.value = false;
};
</script>

<style scoped>
.no-scrollbar::-webkit-scrollbar {
    display: none;
}
.no-scrollbar {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
.animate-fade-in {
    animation: fadeIn 0.2s ease-out;
}
@keyframes fadeIn {
    from { opacity: 0; transform: scale(0.95); }
    to { opacity: 1; transform: scale(1); }
}
</style>
