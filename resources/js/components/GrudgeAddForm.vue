<template>
    <div v-if="show" class="fixed inset-0 z-[70] flex items-end md:items-center justify-center px-0">
        <!-- Backdrop (Desktop Only) -->
        <div class="hidden md:block fixed inset-0 bg-slate-900/40 backdrop-blur-sm" @click="$emit('cancel')"></div>
        
        <!-- Form Container -->
        <div class="relative w-full h-full md:h-auto md:max-h-[90vh] md:max-w-2xl bg-white md:rounded-[32px] shadow-[0_-10px_40px_rgba(0,0,0,0.1)] overflow-hidden animate-slide-up flex flex-col">
            <!-- Header -->
            <div class="px-[10px] py-[5px] flex items-center bg-white border-b border-slate-50 relative">
                <button @click="$emit('cancel')" class="text-slate-400 p-2 -ml-2 mr-0.5 active:scale-90 transition-transform">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" /></svg>
                </button>
                <div class="flex-1 flex flex-col justify-center min-w-0">
                    <div class="text-[20px] font-black leading-none font-outfit uppercase tracking-wider" style="color: rgb(220, 20, 40);">怨靈載錄專區</div>
                    <div class="text-[13px] font-black mt-0 truncate font-outfit" style="color: rgb(220, 20, 40);">
                        {{ editingId ? '修改載錄' : '怨靈載錄登記簿' }}
                    </div>
                </div>
                <button @click="$emit('cancel')" class="text-slate-300 hover:text-slate-600 transition-colors p-2 absolute right-4 top-1/2 -translate-y-1/2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
            </div>

            <!-- Scrollable Content -->
            <div class="flex-1 overflow-y-auto px-[10px] pt-[5px] pb-4 space-y-[15px] custom-scrollbar bg-white">
                <!-- New Organized Layout -->
                <div class="space-y-[15px]">
                    <!-- Row 1: 得知日期 -->
                    <div class="space-y-0.5 mt-[-5px]">
                        <label class="app-title ml-1">得知日期</label>
                        <div @click.stop="activeDate = 'know_date'" 
                            class="w-full py-[5px] rounded-lg bg-white border border-slate-400 px-2 flex items-center justify-start space-x-2 cursor-pointer shadow-sm overflow-hidden">
                            <span :class="form.know_date ? 'text-slate-900' : 'text-slate-400'" class="app-body">
                                {{ (form.know_date ? (form.know_date.split('T')[0]) : '選擇日期').replace(/-/g, '/') }}
                            </span>
                            <svg class="h-3 w-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </div>
                    </div>

                    <!-- Row 2: 法號 & 相關人備註 -->
                    <div class="grid grid-cols-2 gap-[15px]">
                        <div class="space-y-0.5">
                            <label class="app-title ml-1">法號</label>
                            <input v-model="form.user_name" type="text" list="user-list" placeholder="輸入法號" @click.stop class="w-full py-[5px] rounded-lg border border-slate-400 bg-white px-2 focus:ring-0 outline-none shadow-sm app-body font-bold">
                            <datalist id="user-list">
                                <option v-for="u in users" :key="u.id" :value="u.name"></option>
                            </datalist>
                        </div>
                        <div class="space-y-0.5">
                            <label class="app-title ml-1">備註</label>
                            <input v-model="form.user_remarks" type="text" placeholder="親友/信眾" @click.stop class="w-full py-[5px] rounded-lg border border-slate-400 bg-white px-2 focus:ring-0 outline-none shadow-sm app-body">
                        </div>
                    </div>

                    <!-- Row 3: 數量 -->
                    <div class="space-y-0.5">
                        <label class="app-title ml-1">數量</label>
                        <input v-model="form.quantity" type="number" @click.stop class="w-full py-[5px] rounded-lg border border-slate-400 bg-white px-2 focus:ring-0 outline-none shadow-sm app-body">
                    </div>

                    <!-- Row 4: 處理日期 & 處理結果 (Conditional Grid) -->
                    <div :class="form.destination === '未處理' ? 'space-y-0.5' : 'grid grid-cols-2 gap-[15px]'">
                        <div v-if="form.destination !== '未處理'" class="space-y-0.5">
                            <label class="app-title ml-1">處理日期</label>
                            <div @click.stop="activeDate = 'process_date'" 
                                class="w-full py-[5px] rounded-lg bg-white border border-slate-400 px-2 flex items-center justify-start space-x-2 cursor-pointer shadow-sm overflow-hidden">
                                <span :class="form.process_date ? 'text-slate-900' : 'text-slate-400'" class="app-body">
                                    {{ (form.process_date ? (form.process_date.split('T')[0]) : '選擇日期').replace(/-/g, '/') }}
                                </span>
                                <svg class="h-3 w-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </div>
                        </div>
                        <div class="space-y-0.5 relative">
                            <label class="app-title ml-1">處理結果</label>
                            
                            <!-- Trigger Button -->
                            <div @click.stop="showResultPicker = !showResultPicker" 
                                class="w-full py-[5px] rounded-lg border border-slate-400 bg-white px-2 flex items-center justify-between cursor-pointer shadow-sm active:bg-slate-50 transition-all overflow-hidden">
                                <span class="app-body" :class="[
                                    form.destination === '未處理' ? 'text-slate-400' : 
                                    form.destination === '九天' ? 'text-red-600' : 
                                    form.destination === '耀紫軍' ? 'text-purple-600' : ''
                                ]">
                                    {{ form.destination }}
                                </span>
                                <svg class="h-3 w-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </div>
                        </div>
                    </div>

                    <!-- Row 4.1: Sub-options for 黑曜軍 -->
                    <div v-if="form.destination === '黑曜軍'" class="grid grid-cols-2 gap-[5px] animate-fade-in">
                        <div class="space-y-0.5">
                            <label class="app-title ml-1">閻尊</label>
                            <input v-model.number="form.remarks.yan_zun" type="number" placeholder="0" @click.stop class="w-full py-[5px] rounded-lg border border-slate-400 bg-white px-2 focus:ring-0 outline-none shadow-sm app-body">
                        </div>
                        <div class="space-y-0.5">
                            <label class="app-title ml-1">閻闇</label>
                            <input v-model.number="form.remarks.yan_an" type="number" placeholder="0" @click.stop class="w-full py-[5px] rounded-lg border border-slate-400 bg-white px-2 focus:ring-0 outline-none shadow-sm app-body">
                        </div>
                    </div>

                    <!-- Row 4.2: Sub-options for 耀紫軍 -->
                    <div v-if="form.destination === '耀紫軍'" class="grid grid-cols-2 gap-[5px] animate-fade-in">
                        <div class="space-y-0.5">
                            <label class="app-title ml-1">龍勝</label>
                            <input v-model.number="form.remarks.long_sheng" type="number" placeholder="0" @click.stop class="w-full py-[5px] rounded-lg border border-slate-400 bg-white px-2 focus:ring-0 outline-none shadow-sm app-body">
                        </div>
                        <div class="space-y-0.5">
                            <label class="app-title ml-1">龍戰</label>
                            <input v-model.number="form.remarks.long_zhan" type="number" placeholder="0" @click.stop class="w-full py-[5px] rounded-lg border border-slate-400 bg-white px-2 focus:ring-0 outline-none shadow-sm app-body">
                        </div>
                    </div>

                    <!-- Row 5: 備註文字 -->
                    <div class="space-y-1">
                        <label class="app-title ml-1">備註文字</label>
                        <input v-model="form.remarks_text" type="text" placeholder="輸入相關備註..." @click.stop class="w-full py-[5px] rounded-lg border border-slate-400 bg-slate-50/50 px-3 focus:ring-0 outline-none shadow-sm app-body">
                    </div>

                </div>
            </div>

            <!-- Footer Action -->
            <div class="px-4 pb-24 bg-white">
                <button 
                    @click="handleSave" 
                    style="color: white !important"
                    class="w-full bg-indigo-600 font-black py-[5px] text-[16px] rounded-xl shadow-lg shadow-indigo-200 hover:bg-indigo-700 active:scale-[0.98] transition-all flex items-center justify-center"
                >
                    {{ editingId ? '確認修改' : '確認載錄' }}
                </button>
            </div>

            <!-- Global Mobile Navbar -->
            <mobile-navbar 
                @back="$emit('cancel')"
                @home="$emit('cancel')"
                :show-action="false"
                :can-search="false"
            />

            <!-- Custom Modal Picker -->
            <div v-if="showResultPicker" class="fixed inset-0 z-[110] flex items-center justify-center px-4">
                <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="showResultPicker = false"></div>
                <div class="relative w-full max-w-sm bg-white rounded-[32px] shadow-2xl p-8 animate-slide-up">
                    <div class="flex items-center justify-between mb-6">
                        <span class="text-[14px] font-black text-slate-400 uppercase tracking-widest">請選取處理結果</span>
                        <button @click="showResultPicker = false" class="text-slate-400 p-1 active:scale-90 transition-all">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </button>
                    </div>
                    <div class="grid grid-cols-2 gap-x-8">
                        <!-- Left Column -->
                        <div class="space-y-6">
                            <div v-for="opt in ['未處理', '暫驅離', '殲滅']" :key="opt"
                                @click="form.destination = opt; onDestinationChange(); showResultPicker = false"
                                class="cursor-pointer font-normal text-[17px] leading-tight transition-all active:scale-95"
                                :class="[
                                    form.destination === opt ? 'text-indigo-600' : 'text-slate-900'
                                ]"
                            >
                                {{ opt }}
                            </div>
                        </div>
                        <!-- Right Column -->
                        <div class="space-y-6">
                            <div v-for="opt in ['九天', '黑曜軍', '虎賁軍', '虎甲軍', '耀紫軍']" :key="opt"
                                @click="form.destination = opt; onDestinationChange(); showResultPicker = false"
                                class="cursor-pointer font-normal text-[17px] leading-tight transition-all active:scale-95"
                                :class="[
                                    form.destination === opt ? 'text-indigo-600' : 'text-slate-900',
                                    opt === '九天' && form.destination !== opt ? 'text-red-600' : 
                                    opt === '耀紫軍' && form.destination !== opt ? 'text-purple-600' : ''
                                ]"
                            >
                                {{ opt }}
                            </div>
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
import MobileNavbar from './MobileNavbar.vue';

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
    remarks: (props.initialData?.remarks && !Array.isArray(props.initialData.remarks)) ? props.initialData.remarks : {}
});

watch(() => props.initialData, (newVal) => {
    form.value = { 
        ...newVal,
        remarks: (newVal?.remarks && !Array.isArray(newVal.remarks)) ? newVal.remarks : {}
    };
}, { deep: true });

// Auto-sum logic for "二人份" (split quantities)
watch(() => form.value.remarks, (newRemarks) => {
    if (form.value.destination === '黑曜軍') {
        form.value.quantity = (Number(newRemarks.yan_zun) || 0) + (Number(newRemarks.yan_an) || 0);
    } else if (form.value.destination === '耀紫軍') {
        form.value.quantity = (Number(newRemarks.long_sheng) || 0) + (Number(newRemarks.long_zhan) || 0);
    }
}, { deep: true });

const onDestinationChange = () => {
    if (form.value.destination === '未處理') {
        form.value.process_date = '';
    }
    if (form.value.destination === '黑曜軍') {
        form.value.remarks.yan_zun = form.value.remarks.yan_zun || 0;
        form.value.remarks.yan_an = form.value.remarks.yan_an || 0;
    } else {
        delete form.value.remarks.yan_zun;
        delete form.value.remarks.yan_an;
    }
    
    if (form.value.destination === '耀紫軍') {
        form.value.remarks.long_sheng = form.value.remarks.long_sheng || 0;
        form.value.remarks.long_zhan = form.value.remarks.long_zhan || 0;
    } else {
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

    if (form.value.destination !== '未處理' && !form.value.process_date) {
        alert('處理結果為軍隊時，必須填寫處理日期，不可直接存檔。');
        return;
    }

    // Ensure we emit a clean object clone to avoid reference issues
    const finalData = JSON.parse(JSON.stringify(form.value));
    emit('save', finalData);
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
