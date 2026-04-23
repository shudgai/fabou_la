<template>
    <div class="fixed inset-0 z-[200] flex items-center justify-center p-4 bg-black/20" @click="$emit('close')">
        <div class="bg-white rounded-2xl shadow-2xl p-2 w-fit animate-fade-in border border-slate-100" @click.stop>
            <!-- Top Action Buttons -->
            <div class="flex items-center justify-between mb-2 px-2 pt-1 pb-2 border-b border-slate-50">
                <button @click="clear" class="text-slate-400 text-[16px] font-black hover:text-rose-500 transition-colors">清除</button>
                <button @click="setToday" class="text-indigo-600 text-[16px] font-black hover:text-indigo-800">今天</button>
            </div>

            <!-- Month/Year Header -->
            <div class="flex items-center justify-between mb-3 px-1">
                <button @click="changeMonth(-1)" class="p-2 text-slate-400 hover:bg-slate-100 rounded-full transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
                <span class="text-[17px] font-black text-slate-900 font-outfit">{{ currentYear }}年{{ String(currentMonth + 1).padStart(2, '0') }}月</span>
                <button @click="changeMonth(1)" class="p-2 text-slate-400 hover:bg-slate-100 rounded-full transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
            </div>

            <!-- Grid Header -->
            <div class="grid grid-cols-7 gap-1 mb-2">
                <div v-for="day in ['日', '一', '二', '三', '四', '五', '六']" :key="day" 
                    class="text-center text-[15px] font-black text-slate-300 w-11">
                    {{ day }}
                </div>
            </div>

            <!-- Grid Body -->
            <div class="grid grid-cols-7 gap-1">
                <div v-for="(d, idx) in days" :key="idx" 
                    @click="d.isCurrent && selectDay(d.day)"
                    :class="[
                        'text-center text-[16.5px] font-black h-11 flex items-center justify-center cursor-pointer rounded-xl relative w-11 transition-all',
                        !d.isCurrent ? 'text-slate-100' : 'text-slate-600 hover:bg-indigo-50 hover:text-indigo-600',
                        isSelected(d.day, d.isCurrent) ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-200 scale-110 z-10' : '',
                        isToday(d.day, d.isCurrent) && !isSelected(d.day, d.isCurrent) ? 'bg-indigo-50 border-2 border-indigo-100 text-indigo-600' : ''
                    ]">
                    {{ d.day }}
                </div>
            </div>

            <!-- Footer Action Buttons -->
            <div class="flex items-center justify-between mt-3 pt-3 border-t border-slate-50 px-2 pb-1">
                <button @click="clear" class="text-slate-400 text-[16px] font-black hover:text-rose-500 transition-colors">清除</button>
                <button @click="setToday" class="text-indigo-600 text-[16px] font-black hover:text-indigo-800">今天</button>
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
    if (!isCurrent) return false;
    return today.getFullYear() === currentYear.value && today.getMonth() === currentMonth.value && today.getDate() === day;
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
