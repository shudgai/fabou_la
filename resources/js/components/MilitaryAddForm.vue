<template>
    <div v-if="show" class="fixed inset-0 z-[70] flex items-end md:items-center justify-center px-0">
        <!-- Backdrop -->
        <div class="hidden md:block fixed inset-0 bg-slate-900/40 backdrop-blur-sm" @click="$emit('cancel')"></div>
        
        <!-- Form Container -->
        <div class="relative w-full h-full md:h-auto md:max-h-[90vh] md:max-w-2xl bg-white md:rounded-[32px] shadow-[0_-10px_40px_rgba(0,0,0,0.1)] overflow-hidden animate-slide-up flex flex-col">
            <!-- Header -->
            <div class="flex items-center justify-between p-4 border-b border-slate-50">
                <h3 class="text-[19px] font-normal text-slate-900">
                    {{ editingId ? '修改軍隊載錄' : armyType + '-逐筆新增' }}
                </h3>
                <button @click="$emit('cancel')" class="text-slate-400 hover:text-slate-600 p-1 active:scale-95">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
            </div>

            <!-- Scrollable Content -->
            <div class="flex-1 overflow-y-auto px-[10px] pt-[5px] pb-6 space-y-[5px] custom-scrollbar bg-white">
                <div class="space-y-[5px]">
                    <!-- Row 1: 日期 -->
                    <div class="space-y-1">
                        <label class="text-[13px] font-normal text-[#aeb4be] uppercase ml-1">日期</label>
                        <div @click="activeDate = 'know_date'" 
                            class="w-full h-[36px] rounded-lg bg-white border border-slate-200 px-2 flex items-center justify-between cursor-pointer shadow-sm overflow-hidden">
                            <span :class="form.know_date ? 'text-slate-900 font-normal' : 'text-slate-400'" class="text-[16px] leading-tight">
                                {{ form.know_date ? (new Date(form.know_date).getFullYear() + '/' + (new Date(form.know_date).getMonth() + 1) + '/' + new Date(form.know_date).getDate()) : '點選日期' }}
                            </span>
                            <svg class="h-3 w-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </div>
                    </div>

                    <!-- Row 2: 法號 & 備註 -->
                    <div class="grid grid-cols-2 gap-[5px]">
                        <div class="space-y-1">
                            <label class="text-[13px] font-normal text-[#aeb4be] uppercase ml-1">法號</label>
                            <input v-model="form.user_name" type="text" list="user-list-mil" placeholder="輸入法號" class="w-full h-[36px] rounded-lg border border-slate-200 bg-white px-2 focus:ring-0 outline-none shadow-sm text-[16px] leading-tight text-slate-900 font-normal">
                            <datalist id="user-list-mil">
                                <option v-for="u in users" :key="u.id" :value="u.name"></option>
                            </datalist>
                        </div>
                        <div class="space-y-1">
                            <label class="text-[13px] font-normal text-[#aeb4be] uppercase ml-1">備註</label>
                            <input v-model="form.user_remarks" type="text" placeholder="親友/信眾" class="w-full h-[36px] rounded-lg border border-slate-200 bg-white px-2 focus:ring-0 outline-none shadow-sm text-[16px] leading-tight text-slate-900 font-normal">
                        </div>
                    </div>

                    <!-- Conditional Quantity Row -->
                    <!-- Case 1: 虎甲/虎賁 or Default -->
                    <div v-if="armyType === '虎甲軍' || armyType === '虎賁軍'" class="space-y-1">
                        <label class="text-[13px] font-normal text-[#aeb4be] uppercase ml-1">數量</label>
                        <input v-model="form.quantity" type="number" class="w-full h-[36px] rounded-lg border border-slate-200 bg-white px-2 focus:ring-0 outline-none shadow-sm text-[16px] leading-tight text-slate-900 font-normal">
                    </div>

                    <!-- Case 2: 黑曜軍 (閻尊/閻闇) -->
                    <div v-if="armyType === '黑曜軍'" class="space-y-[5px]">
                        <div class="grid grid-cols-2 gap-[5px]">
                            <div class="space-y-1">
                                <label class="text-[13px] font-normal text-[#aeb4be] uppercase ml-1">閻尊數量</label>
                                <input v-model="form.yan_zun" type="number" class="w-full h-[36px] rounded-lg border border-slate-200 bg-white px-2 focus:ring-0 outline-none shadow-sm text-[16px] leading-tight text-slate-900 font-normal">
                            </div>
                            <div class="space-y-1">
                                <label class="text-[13px] font-normal text-[#aeb4be] uppercase ml-1">閻闇數量</label>
                                <input v-model="form.yan_an" type="number" class="w-full h-[36px] rounded-lg border border-slate-200 bg-white px-2 focus:ring-0 outline-none shadow-sm text-[16px] leading-tight text-slate-900 font-normal">
                            </div>
                        </div>
                        <div class="w-full px-4 flex items-center justify-end py-2 border-t border-slate-50 mt-1 space-x-2">
                            <span class="text-[13px] font-normal text-[#aeb4be]">小計</span>
                            <span class="text-[16px] font-normal text-slate-900">{{ (Number(form.yan_zun || 0) + Number(form.yan_an || 0)) }}</span>
                        </div>
                    </div>

                    <!-- Case 3: 耀紫軍 (龍勝/龍戰) -->
                    <div v-if="armyType === '耀紫軍'" class="space-y-[5px]">
                        <div class="grid grid-cols-2 gap-[5px]">
                            <div class="space-y-1">
                                <label class="text-[13px] font-normal text-[#aeb4be] uppercase ml-1">龍勝數量</label>
                                <input v-model="form.long_sheng" type="number" class="w-full h-[36px] rounded-lg border border-slate-200 bg-white px-2 focus:ring-0 outline-none shadow-sm text-[16px] leading-tight text-slate-900 font-normal">
                            </div>
                            <div class="space-y-1">
                                <label class="text-[13px] font-normal text-[#aeb4be] uppercase ml-1">龍戰數量</label>
                                <input v-model="form.long_zhan" type="number" class="w-full h-[36px] rounded-lg border border-slate-200 bg-white px-2 focus:ring-0 outline-none shadow-sm text-[16px] leading-tight text-slate-900 font-normal">
                            </div>
                        </div>
                        <div class="w-full px-4 flex items-center justify-end py-2 border-t border-slate-50 mt-1 space-x-2">
                            <span class="text-[13px] font-normal text-[#aeb4be]">小計</span>
                            <span class="text-[16px] font-normal text-slate-900">{{ (Number(form.long_sheng || 0) + Number(form.long_zhan || 0)) }}</span>
                        </div>
                    </div>

                    <!-- Row 5: 備註 -->
                    <div class="space-y-1 pt-1">
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

            <!-- Custom Full-Screen Overlay Picker (Kept for component structure but logically unused now) -->
            <div v-if="false" class="absolute inset-0 bg-white z-[100] p-8 flex flex-col animate-fade-in md:rounded-[32px]">
                <div class="flex items-center justify-between mb-10">
                    <span class="text-[14px] font-bold text-slate-400">請選取處理狀況</span>
                    <button @click="showResultPicker = false" class="text-slate-400 p-2"><svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></button>
                </div>
                <div class="grid grid-cols-2 gap-x-12 flex-1">
                    <div class="space-y-12">
                        <div v-for="opt in ['未處理', '暫驅離開', '殲滅']" :key="opt"
                            @click="form.destination = opt; showResultPicker = false"
                            class="cursor-pointer font-bold text-[20px] transition-all active:scale-95 py-2"
                            :class="[
                                form.destination === opt ? 'text-indigo-600 underline underline-offset-4' : 'text-slate-900 opacity-60'
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
            :title="activeDate === 'know_date' ? '日期' : '處理日期'"
            @close="activeDate = null"
        />
    </div>
</template>

<script setup>
import { ref, watch } from 'vue';
import CompactDatePicker from './CompactDatePicker.vue';

const props = defineProps({
    show: Boolean,
    initialData: Object,
    editingId: [Number, String],
    users: Array,
    armyType: String
});

const emit = defineEmits(['save', 'cancel']);
const activeDate = ref(null);
const showResultPicker = ref(false);

const form = ref({ 
    ...props.initialData,
    army_type: props.armyType || props.initialData.army_type
});

watch(() => props.initialData, (newVal) => {
    form.value = { ...newVal, army_type: props.armyType || newVal.army_type };
}, { deep: true });

const handleSave = () => {
    if (!form.value.user_name) {
        alert('請選擇或輸入「法號」，不可留空。');
        return;
    }

    // Auto-calculate quantity for special armies
    if (props.armyType === '黑曜軍') {
        form.value.quantity = Number(form.value.yan_zun || 0) + Number(form.value.yan_an || 0);
    } else if (props.armyType === '耀紫軍') {
        form.value.quantity = Number(form.value.long_sheng || 0) + Number(form.value.long_zhan || 0);
    }

    if (form.value.destination !== '未處理' && !form.value.process_date) {
        alert('處理狀況非「未處理」時，必須填寫處理日期。');
        return;
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
