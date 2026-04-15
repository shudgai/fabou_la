<template>
    <div v-if="show" class="fixed inset-0 z-[70] flex items-end justify-center px-0">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm" @click="$emit('cancel')"></div>
        
        <!-- Form Container -->
        <div class="relative w-full max-w-2xl bg-white rounded-t-[32px] shadow-[0_-10px_40px_rgba(0,0,0,0.1)] overflow-hidden animate-slide-up flex flex-col max-h-[85vh]">
            <!-- Header -->
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-white sticky top-0 z-10">
                <h3 class="text-lg font-bold text-slate-800">
                    {{ editingId ? '修改紀錄' : '新增紀錄' }}
                </h3>
                <button @click="$emit('cancel')" class="p-2 text-slate-400 hover:text-slate-600 active:scale-95">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
            </div>

            <!-- Scrollable Content -->
            <div class="flex-1 overflow-y-auto p-6 space-y-5 custom-scrollbar">
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-500 uppercase ml-1">選擇使用者</label>
                        <select v-model="form.user_id" class="w-full rounded-2xl border-slate-100 bg-slate-50 p-3 text-sm focus:ring-2 focus:ring-indigo-500/20">
                            <option :value="null">請選擇使用者</option>
                            <option v-for="u in users" :key="u.id" :value="u.id">{{ u.name }}</option>
                        </select>
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-500 uppercase ml-1">數量</label>
                        <input v-model="form.quantity" type="number" class="w-full rounded-2xl border-slate-100 bg-slate-50 p-3 text-sm focus:ring-2 focus:ring-indigo-500/20">
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-500 uppercase ml-1">去處</label>
                    <input v-model="form.destination" type="text" placeholder="例如：某某宮廟" class="w-full rounded-2xl border-slate-100 bg-slate-50 p-3 text-sm focus:ring-2 focus:ring-indigo-500/20">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-500 uppercase ml-1">得知日期</label>
                        <input v-model="form.know_date" type="date" class="w-full rounded-2xl border-slate-100 bg-slate-50 p-3 text-sm focus:ring-2 focus:ring-indigo-500/20">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-500 uppercase ml-1">處理日期</label>
                        <input v-model="form.process_date" type="date" class="w-full rounded-2xl border-slate-100 bg-slate-50 p-3 text-sm focus:ring-2 focus:ring-indigo-500/20">
                    </div>
                </div>
            </div>

            <!-- Footer Action -->
            <div class="p-6 border-t border-slate-100 bg-slate-50/50">
                <button 
                    @click="$emit('save', form)" 
                    class="w-full bg-indigo-600 text-white font-bold py-4 rounded-2xl shadow-lg shadow-indigo-200 hover:bg-indigo-700 active:scale-[0.98] transition-all"
                >
                    {{ editingId ? '確認修改' : '確認新增' }}
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue';

const props = defineProps({
    show: Boolean,
    initialData: Object,
    editingId: [Number, String],
    users: Array
});

const emit = defineEmits(['save', 'cancel']);

const form = ref({ ...props.initialData });

watch(() => props.initialData, (newVal) => {
    form.value = { ...newVal };
}, { deep: true });
</script>

<style scoped>
.animate-slide-up {
    animation: slideUp 0.5s cubic-bezier(0.16, 1, 0.3, 1);
}
@keyframes slideUp {
    from { transform: translateY(100%); }
    to { transform: translateY(0); }
}
</style>
