<template>
    <div class="fixed inset-0 z-[9999] flex items-center justify-center bg-slate-900/10 backdrop-blur-[1px]" @click="$emit('close')">
        <div class="bg-white rounded-[24px] shadow-[0_10px_30px_rgba(0,0,0,0.1)] p-2 w-[85%] md:w-fit h-auto animate-fade-in border border-slate-100 flex flex-col origin-center" 
             @click.stop>
            <!-- Name Header (Dharma Name) -->
            <div v-if="title" class="px-2 pb-1.5 pt-0.5 border-b border-slate-100 mb-2">
                <h2 class="app-body font-black text-[#0f172a] text-center font-outfit uppercase tracking-tight">
                    {{ title }}
                </h2>
            </div>

            <div class="flex items-center space-x-1.5 mb-1.5">
                <button @click="clear" class="flex-1 h-[32px] bg-slate-50 text-slate-400 app-body font-black rounded-lg active:scale-95 transition-all">清除</button>
                <button @click="setToday" class="flex-1 h-[32px] bg-indigo-50 text-indigo-600 app-body font-black rounded-lg active:scale-95 transition-all">今天</button>
            </div>

            <!-- Month/Year Header -->
            <div class="flex items-center justify-between mb-1 px-1">
                <button @click="changeMonth(-1)" class="p-0.5 text-slate-400 hover:bg-slate-100 rounded-full transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
                <span class="app-body font-black text-[#0f172a] font-outfit">{{ currentYear }}年{{ String(currentMonth + 1).padStart(2, '0') }}月</span>
                <button @click="changeMonth(1)" class="p-0.5 text-slate-400 hover:bg-slate-100 rounded-full transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
            </div>

            <!-- Calendar Container (Flexible) -->
            <div class="flex-1 flex flex-col justify-center max-w-sm mx-auto w-full">
                <!-- Grid Header -->
                <div class="grid grid-cols-7 gap-0.5 mb-1">
                    <div v-for="day in ['日', '一', '二', '三', '四', '五', '六']" :key="day" 
                        class="text-center app-body font-black text-slate-400">
                        {{ day }}
                    </div>
                </div>

                <!-- Grid Body -->
                <div class="grid grid-cols-7 gap-1">
                    <div v-for="(d, idx) in days" :key="idx" 
                        @click="d.isCurrent && selectDay(d.day)"
                        :class="[
                            'text-center app-body font-black h-[34px] flex items-center justify-center cursor-pointer rounded-lg relative transition-all',
                            !d.isCurrent ? 'text-slate-100' : 'text-[#0f172a] active:scale-90',
                            isSelected(d.day, d.isCurrent) ? 'bg-indigo-600 text-white shadow-md shadow-indigo-50 ring-2 ring-indigo-50 z-10' : ''
                        ]"
                        :style="isSelected(d.day, d.isCurrent) ? 'color: white !important;' : ''">
                        {{ d.day }}
                    </div>
                </div>
            </div>

            <!-- Bottom Actions -->
            <div class="flex items-center space-x-1.5 mt-1.5">
                <button @click="clear" class="flex-1 h-[36px] bg-slate-50 text-slate-400 app-body font-black rounded-lg active:scale-95 transition-all">清除</button>
                <button @click="setToday" class="flex-1 h-[36px] bg-indigo-50 text-indigo-600 app-body font-black rounded-lg active:scale-95 transition-all">今天</button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
    modelValue: String,
    title: String,
    scale: {
        type: Number,
        default: 1
    }
});
const emit = defineEmits(['update:modelValue', 'close']);

const today = new Date();
const currentYear = ref(props.modelValue ? new Date(props.modelValue).getFullYear() : today.getFullYear());
const currentMonth = ref(props.modelValue ? new Date(props.modelValue).getMonth() : today.getMonth());

const days = computed(() => {
    const firstDay = new Date(currentYear.value, currentMonth.value, 1);
    const lastDay = new Date(currentYear.value, currentMonth.value + 1, 0);
    const prevLastDay = new Date(currentYear.value, currentMonth.value, 0);

    const res = [];
    const startOffset = firstDay.getDay();
    
    // Previous month days (Hidden)
    for (let i = startOffset - 1; i >= 0; i--) {
        res.push({ day: '', isCurrent: false });
    }

    // Current month days
    for (let i = 1; i <= lastDay.getDate(); i++) {
        res.push({ day: i, isCurrent: true });
    }

    // Next month days (Hidden)
    // Only fill up to the end of the current week row, or keep 42 for stability?
    // User said "one month", let's keep it to the minimum required rows.
    const totalCells = res.length > 35 ? 42 : 35; 
    const remaining = totalCells - res.length;
    for (let i = 1; i <= remaining; i++) {
        res.push({ day: '', isCurrent: false });
    }

    return res;
});

const changeMonth = (delta) => {
    currentMonth.value += delta;
    if (currentMonth.value > 11) {
        currentMonth.value = 0;
        currentYear.value++;
    } else if (currentMonth.value < 0) {
        currentMonth.value = 11;
        currentYear.value--;
    }
};

const selectDay = (day) => {
    const y = currentYear.value;
    const m = String(currentMonth.value + 1).padStart(2, '0');
    const d = String(day).padStart(2, '0');
    emit('update:modelValue', `${y}-${m}-${d}`);
    emit('close');
};

const isSelected = (day, isCurrent) => {
    if (!props.modelValue || !isCurrent) return false;
    const d = new Date(props.modelValue);
    return d.getFullYear() === currentYear.value && d.getMonth() === currentMonth.value && d.getDate() === day;
};

const isToday = (day, isCurrent) => {
    // Hidden as per user request
    return false;
};

const setToday = () => {
    currentYear.value = today.getFullYear();
    currentMonth.value = today.getMonth();
    selectDay(today.getDate());
};

const clear = () => {
    emit('update:modelValue', '');
    emit('close');
};
</script>

<style scoped>
.animate-fade-in { animation: fadeIn 0.2s ease-out; }
@keyframes fadeIn { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
</style>
