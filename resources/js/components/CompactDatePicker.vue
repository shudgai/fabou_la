<template>
    <div class="fixed inset-0 z-[300] flex items-end md:items-center justify-center bg-slate-900/20 backdrop-blur-[1px]" @click="$emit('close')">
        <div class="bg-white rounded-t-[32px] md:rounded-[28px] shadow-[0_-10px_40px_rgba(0,0,0,0.1)] p-4 w-full md:w-fit h-auto md:h-auto animate-slide-up md:animate-fade-in border-t md:border border-slate-100 flex flex-col scale-[0.8] origin-bottom md:origin-center" @click.stop>
            <!-- Name Header (Dharma Name) -->
            <div v-if="title" class="px-2 pb-2 pt-1 border-b border-slate-100 mb-3">
                <h2 class="text-[18px] font-black text-red-600 text-center font-outfit uppercase tracking-tight">
                    {{ title }}
                </h2>
            </div>

            <div class="flex items-center space-x-3 mb-4">
                <button @click="clear" class="flex-1 h-[44px] bg-slate-50 text-slate-400 text-[18px] font-black rounded-xl active:scale-95 transition-all">清除</button>
                <button @click="setToday" class="flex-1 h-[44px] bg-indigo-50 text-indigo-600 text-[18px] font-black rounded-xl active:scale-95 transition-all">今天</button>
            </div>

            <!-- Month/Year Header -->
            <div class="flex items-center justify-between mb-4 px-2">
                <button @click="changeMonth(-1)" class="p-1.5 text-slate-400 hover:bg-slate-100 rounded-full transition-all">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
                <span class="text-[18px] font-black text-slate-900 font-outfit">{{ currentYear }}年{{ String(currentMonth + 1).padStart(2, '0') }}月</span>
                <button @click="changeMonth(1)" class="p-1.5 text-slate-400 hover:bg-slate-100 rounded-full transition-all">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
            </div>

            <!-- Calendar Container (Flexible) -->
            <div class="flex-1 flex flex-col justify-center max-w-sm mx-auto w-full">
                <!-- Grid Header -->
                <div class="grid grid-cols-7 gap-1 mb-2">
                    <div v-for="day in ['日', '一', '二', '三', '四', '五', '六']" :key="day" 
                        class="text-center text-[18px] font-black text-slate-300">
                        {{ day }}
                    </div>
                </div>

                <!-- Grid Body -->
                <div class="grid grid-cols-7 gap-1.5">
                    <div v-for="(d, idx) in days" :key="idx" 
                        @click="d.isCurrent && selectDay(d.day)"
                        :class="[
                            'text-center text-[18px] font-black h-[42px] flex items-center justify-center cursor-pointer rounded-xl relative transition-all',
                            !d.isCurrent ? 'text-slate-100' : 'text-slate-700 active:scale-90',
                            isSelected(d.day, d.isCurrent) ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-100 ring-2 ring-indigo-50 z-10' : ''
                        ]"
                        :style="isSelected(d.day, d.isCurrent) ? 'color: white !important;' : ''">
                        {{ d.day }}
                    </div>
                </div>
            </div>

            <!-- Bottom Actions -->
            <div class="flex items-center space-x-3 mt-6 pb-2">
                <button @click="clear" class="flex-1 h-[48px] bg-slate-50 text-slate-400 text-[18px] font-black rounded-xl active:scale-95 transition-all">清除</button>
                <button @click="setToday" class="flex-1 h-[48px] bg-indigo-50 text-indigo-600 text-[18px] font-black rounded-xl active:scale-95 transition-all">今天</button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const props = defineProps(['modelValue', 'title']);
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
    
    // Previous month days
    for (let i = startOffset - 1; i >= 0; i--) {
        res.push({ day: prevLastDay.getDate() - i, isCurrent: false });
    }

    // Current month days
    for (let i = 1; i <= lastDay.getDate(); i++) {
        res.push({ day: i, isCurrent: true });
    }

    // Next month days
    const remaining = 42 - res.length;
    for (let i = 1; i <= remaining; i++) {
        res.push({ day: i, isCurrent: false });
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
