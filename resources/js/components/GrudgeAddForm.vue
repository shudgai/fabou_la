<template>
    <div v-if="show" class="fixed inset-0 z-[70] flex items-end md:items-center justify-center px-0">
        <!-- Backdrop (Desktop Only) -->
        <div class="hidden md:block fixed inset-0 bg-slate-900/40 backdrop-blur-sm" @click="$emit('cancel')"></div>
        
        <!-- Form Container -->
        <div class="relative w-full h-full md:h-auto md:max-h-[90vh] md:max-w-2xl bg-white md:rounded-[32px] shadow-[0_-10px_40px_rgba(0,0,0,0.1)] overflow-hidden animate-slide-up flex flex-col">
            <!-- Header -->
            <div class="flex items-center justify-between p-4 border-b border-slate-50">
                <h3 class="text-[19px] font-normal text-slate-900">
                    {{ editingId ? '修改載錄' : '怨靈載錄' }}
                </h3>
                <button @click="$emit('cancel')" class="text-slate-400 hover:text-slate-600 p-1 active:scale-95">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
            </div>

            <!-- Scrollable Content -->
            <div class="flex-1 overflow-y-auto px-[10px] pt-[5px] pb-4 space-y-[15px] custom-scrollbar bg-white">
                <!-- New Organized Layout -->
                <div class="space-y-[15px]">
                    <!-- Row 1: 得知日期 -->
                    <div class="space-y-0.5 mt-[-5px]">
                        <label class="text-[13px] font-normal text-[#aeb4be] uppercase ml-1">得知日期</label>
                        <div @click="activeDate = 'know_date'" 
                            class="w-full h-[36px] rounded-lg bg-white border border-slate-200 px-2 flex items-center justify-start space-x-2 cursor-pointer shadow-sm overflow-hidden">
                            <span :class="form.know_date ? 'text-slate-800' : 'text-slate-400'" class="text-[16px] leading-tight">
                                {{ (form.know_date || '選擇日期').replace(/-/g, '/') }}
                            </span>
                            <svg class="h-3 w-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </div>
                    </div>

                    <!-- Row 2: 法號 & 相關人備註 -->
                    <div class="grid grid-cols-2 gap-[15px]">
                        <div class="space-y-0.5">
                            <label class="text-[13px] font-normal text-[#aeb4be] uppercase ml-1">法號</label>
                            <input v-model="form.user_name" type="text" list="user-list" placeholder="輸入法號" class="w-full h-[36px] rounded-lg border border-slate-200 bg-white px-2 focus:ring-0 outline-none shadow-sm text-[16px] leading-tight">
                            <datalist id="user-list">
                                <option v-for="u in users" :key="u.id" :value="u.name"></option>
                            </datalist>
                        </div>
                        <div class="space-y-0.5">
                            <label class="text-[13px] font-normal text-[#aeb4be] uppercase ml-1">備註</label>
                            <input v-model="form.user_remarks" type="text" placeholder="親友/信眾" class="w-full h-[36px] rounded-lg border border-slate-200 bg-white px-2 focus:ring-0 outline-none shadow-sm text-[16px] leading-tight">
                        </div>
                    </div>

                    <!-- Row 3: 數量 -->
                    <div class="space-y-0.5">
                        <label class="text-[13px] font-normal text-[#aeb4be] uppercase ml-1">數量</label>
                        <input v-model="form.quantity" type="number" class="w-full h-[36px] rounded-lg border border-slate-200 bg-white px-2 focus:ring-0 outline-none shadow-sm text-[16px] leading-tight">
                    </div>

                    <!-- Row 4: 處理日期 & 處理結果 (Conditional Grid) -->
                    <div :class="form.destination === '未處理' ? 'space-y-0.5' : 'grid grid-cols-2 gap-[15px]'">
                        <div v-if="form.destination !== '未處理'" class="space-y-0.5">
                            <label class="text-[13px] font-normal text-[#aeb4be] uppercase ml-1">處理日期</label>
                            <div @click="activeDate = 'process_date'" 
                                class="w-full h-[36px] rounded-lg bg-white border border-slate-200 px-2 flex items-center justify-start space-x-2 cursor-pointer shadow-sm overflow-hidden">
                                <span :class="form.process_date ? 'text-slate-800' : 'text-slate-400'" class="text-[16px] leading-tight">
                                    {{ (form.process_date || '選擇日期').replace(/-/g, '/') }}
                                </span>
                                <svg class="h-3 w-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </div>
                        </div>
                        <div class="space-y-0.5 relative">
                            <label class="text-[13px] font-normal text-[#aeb4be] uppercase ml-1">處理結果</label>
                            
                            <!-- Trigger Button -->
                            <div @click="showResultPicker = !showResultPicker" 
                                class="w-full h-[36px] rounded-lg border border-slate-200 bg-white px-2 flex items-center justify-between cursor-pointer shadow-sm active:bg-slate-50 transition-all overflow-hidden">
                                <span class="font-normal text-[16px] leading-tight" :class="[
                                    form.destination === '未處理' ? 'text-slate-400' : 
                                    form.destination === '九天' ? 'text-red-600' : 
                                    form.destination === '耀紫軍' ? 'text-purple-600' : 'text-slate-900'
                                ]">
                                    {{ form.destination }}
                                </span>
                                <svg class="h-3 w-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </div>
                        </div>
                    </div>

                    <!-- Row 4.1: Sub-options for 黑曜軍 -->
                    <div v-if="form.destination === '黑曜軍'" class="grid grid-cols-2 gap-[5px] animate-fade-in">
                        <div class="space-y-0.5">
                            <label class="text-[13px] font-normal text-[#aeb4be] uppercase ml-1">閻尊</label>
                            <input v-model="form.remarks.yan_zun" type="number" placeholder="0" class="w-full h-[36px] rounded-lg border border-slate-200 bg-white px-2 focus:ring-0 outline-none shadow-sm text-[16px] leading-tight">
                        </div>
                        <div class="space-y-0.5">
                            <label class="text-[13px] font-normal text-[#aeb4be] uppercase ml-1">閻闇</label>
                            <input v-model="form.remarks.yan_an" type="number" placeholder="0" class="w-full h-[36px] rounded-lg border border-slate-200 bg-white px-2 focus:ring-0 outline-none shadow-sm text-[16px] leading-tight">
                        </div>
                    </div>

                    <!-- Row 4.2: Sub-options for 耀紫軍 -->
                    <div v-if="form.destination === '耀紫軍'" class="grid grid-cols-2 gap-[5px] animate-fade-in">
                        <div class="space-y-0.5">
                            <label class="text-[13px] font-normal text-[#aeb4be] uppercase ml-1">龍勝</label>
                            <input v-model="form.remarks.long_sheng" type="number" placeholder="0" class="w-full h-[36px] rounded-lg border border-slate-200 bg-white px-2 focus:ring-0 outline-none shadow-sm text-[16px] leading-tight">
                        </div>
                        <div class="space-y-0.5">
                            <label class="text-[13px] font-normal text-[#aeb4be] uppercase ml-1">龍戰</label>
                            <input v-model="form.remarks.long_zhan" type="number" placeholder="0" class="w-full h-[36px] rounded-lg border border-slate-200 bg-white px-2 focus:ring-0 outline-none shadow-sm text-[16px] leading-tight">
                        </div>
                    </div>

                    <!-- Row 5: 備註文字 -->
                    <div class="space-y-1">
                        <label class="text-[13px] font-normal text-[#aeb4be] uppercase ml-1">備註文字</label>
                        <input v-model="form.remarks_text" type="text" placeholder="輸入相關備註..." class="w-full h-[36px] rounded-lg border border-slate-100 bg-slate-50/50 px-3 focus:ring-0 outline-none shadow-sm text-[16px] text-slate-600 font-normal">
                    </div>

                </div>
            </div>

            <!-- Footer Action -->
            <div class="p-3 border-t border-slate-100 bg-white">
                <button 
                    @click="handleSave" 
                    class="w-full bg-indigo-600 text-white font-normal p-[8px] text-[18px] rounded-2xl shadow-lg shadow-indigo-200 hover:bg-indigo-700 active:scale-[0.98] transition-all"
                >
                    {{ editingId ? '確認修改' : '確認載錄' }}
                </button>
            </div>

            <!-- Custom Full-Screen Overlay Picker (Now correctly covering the whole modal) -->
            <div v-if="showResultPicker" class="absolute inset-0 bg-white z-[100] p-8 flex flex-col animate-fade-in md:rounded-[32px]">
                <div class="flex items-center justify-between mb-10">
                    <span class="text-[14px] font-bold text-slate-400">請選取處理結果</span>
                    <button @click="showResultPicker = false" class="text-slate-400 p-2"><svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></button>
                </div>
                <div class="grid grid-cols-2 gap-x-12 flex-1">
                    <!-- Left Column -->
                    <div class="space-y-8">
                        <div v-for="opt in ['未處理', '暫驅離', '殲滅']" :key="opt"
                            @click="form.destination = opt; onDestinationChange(); showResultPicker = false"
                            class="cursor-pointer font-normal text-[13px] transition-all active:scale-95 py-1"
                            :class="[
                                form.destination === opt ? 'text-indigo-600 underline underline-offset-4' : 'text-slate-900 opacity-60'
                            ]"
                        >
                            {{ opt }}
                        </div>
                    </div>
                    <!-- Right Column -->
                    <div class="space-y-7">
                        <div v-for="opt in ['九天', '黑曜軍', '虎賁軍', '虎甲軍', '耀紫軍']" :key="opt"
                            @click="form.destination = opt; onDestinationChange(); showResultPicker = false"
                            class="cursor-pointer font-normal text-[13px] transition-all active:scale-95 py-1"
                            :class="[
                                form.destination === opt ? 'text-indigo-600 underline underline-offset-4' : 'text-slate-900 opacity-60',
                                opt === '九天' && form.destination !== opt ? 'text-red-500' : 
                                opt === '耀紫軍' && form.destination !== opt ? 'text-purple-500' : ''
                            ]"
                        >
                            {{ opt }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Custom Date Picker -->
        <compact-date-picker 
            v-if="activeDate"
            v-model="form[activeDate]"
            :title="activeDate === 'know_date' ? '得知日期' : '處理日期'"
            @close="activeDate = null"
        />
    </div>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue';
import CompactDatePicker from './CompactDatePicker.vue';

const props = defineProps({
    show: Boolean,
    initialData: Object,
    editingId: [Number, String],
    users: Array
});

const emit = defineEmits(['save', 'cancel']);
const activeDate = ref(null);
const showResultPicker = ref(false);

const form = ref({ 
    ...props.initialData,
    remarks: props.initialData?.remarks || {}
});

watch(() => props.initialData, (newVal) => {
    form.value = { 
        ...newVal,
        remarks: newVal?.remarks || {}
    };
}, { deep: true });

const onDestinationChange = () => {
    if (form.value.destination === '未處理') {
        form.value.process_date = '';
    }
    // Clear sub-options if destination changes
    if (form.value.destination !== '黑曜軍') {
        delete form.value.remarks.yan_zun;
        delete form.value.remarks.yan_an;
    }
    if (form.value.destination !== '耀紫軍') {
        delete form.value.remarks.long_sheng;
        delete form.value.remarks.long_zhan;
    }
};

const handleSave = () => {
    // Validation Rules
    if (!form.value.user_name) {
        alert('請選擇或輸入「法號」，不可留空。');
        return;
    }

    if (form.value.destination !== '未處理') {
        // Must have a process date for military results
        if (!form.value.process_date) {
            alert('處理結果為軍隊時，必須填寫處理日期，不可直接存檔。');
            return;
        }
    } else {
        // Ensure date is cleared for unprocessed
        form.value.process_date = '';
    }

    emit('save', form.value);
};
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
