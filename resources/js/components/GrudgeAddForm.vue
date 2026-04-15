<template>
    <div v-if="show" class="fixed inset-0 z-[70] flex items-end md:items-center justify-center px-0">
        <!-- Backdrop (Desktop Only) -->
        <div class="hidden md:block fixed inset-0 bg-slate-900/40 backdrop-blur-sm" @click="$emit('cancel')"></div>
        
        <!-- Form Container -->
        <div class="relative w-full h-full md:h-auto md:max-h-[90vh] md:max-w-2xl bg-white md:rounded-[32px] shadow-[0_-10px_40px_rgba(0,0,0,0.1)] overflow-hidden animate-slide-up flex flex-col">
            <!-- Header -->
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-white sticky top-0 z-10">
                <h3 class="text-xl font-medium text-black">
                    {{ editingId ? '修改紀錄' : '新增紀錄' }}
                </h3>
                <button @click="$emit('cancel')" class="p-2 text-black hover:text-slate-600 active:scale-95">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
            </div>

            <!-- Scrollable Content -->
            <div class="flex-1 overflow-y-auto p-3 space-y-1 custom-scrollbar">
                <div class="grid grid-cols-2 gap-2">
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-black uppercase ml-1">選擇使用者</label>
                        <select v-model="form.user_id" class="w-full h-[20px] rounded-xl border-none bg-slate-50 px-2 text-[13px] leading-none focus:ring-0 outline-none">
                            <option :value="null">請選擇使用者</option>
                            <option v-for="u in users" :key="u.id" :value="u.id">{{ u.name }}</option>
                        </select>
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-black uppercase ml-1">數量</label>
                        <input v-model="form.quantity" type="number" class="w-full h-[20px] rounded-xl border-none bg-slate-50 px-2 text-[13px] leading-none focus:ring-0 outline-none">
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-bold text-black uppercase ml-1">去處</label>
                    <input v-model="form.destination" type="text" placeholder="例如：某某宮廟" class="w-full h-[20px] rounded-xl border-none bg-slate-50 px-2 text-[13px] leading-none focus:ring-0 outline-none">
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-black uppercase ml-1">得知日期</label>
                        <input v-model="form.know_date" type="date" class="w-full h-[20px] rounded-xl border-none bg-slate-50 px-2 text-[13px] leading-none focus:ring-0 outline-none">
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-black uppercase ml-1">處理日期</label>
                        <input v-model="form.process_date" type="date" class="w-full h-[20px] rounded-xl border-none bg-slate-50 px-2 text-[13px] leading-none focus:ring-0 outline-none">
                    </div>
                </div>
            </div>

            <!-- Footer Action -->
            <div class="p-3 border-t border-slate-100 bg-slate-50/50">
                <button 
                    @click="$emit('save', form)" 
                    class="w-full bg-indigo-600 text-white font-normal p-[5px] rounded-2xl shadow-lg shadow-indigo-200 hover:bg-indigo-700 active:scale-[0.98] transition-all"
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
