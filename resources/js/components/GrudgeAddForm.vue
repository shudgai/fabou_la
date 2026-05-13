<template>
    <teleport to="body">
    <div v-if="show" class="fixed inset-0 z-[3500] flex items-end md:items-center justify-center px-0">
        <!-- Backdrop (Desktop Only) -->
        <div class="hidden md:block fixed inset-0 bg-slate-900/40 backdrop-blur-sm" @click="$emit('cancel')"></div>

        <!-- Form Container -->
        <div class="relative w-full h-full md:h-auto md:max-h-[90dvh] md:max-w-2xl bg-white md:rounded-[32px] shadow-[0_-10px_40px_rgba(0,0,0,0.1)] overflow-hidden animate-slide-up flex flex-col pb-0">
            <!-- Header -->
            <div class="px-[10px] py-[12px] flex items-center bg-white border-b border-slate-50 relative">
                <div class="flex-1 flex flex-col justify-center min-w-0">
                    <div class="text-[30px] font-bold leading-tight font-outfit uppercase tracking-wider text-slate-900 flex items-center gap-2" style="font-size: 30px !important;">
                        <logo-imperial-notebook :height="36" class="md:hidden" />
                        <span>怨靈載錄專區<br>逐筆載錄</span>
                    </div>
                </div>
                <button @click="$emit('cancel')" class="text-slate-300 hover:text-slate-600 transition-colors p-2 absolute right-4 top-1/2 -translate-y-1/2 z-[50]">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
            </div>

            <!-- Scrollable Content -->
            <div class="flex-1 overflow-y-auto px-[10px] pt-6 pb-[250px] space-y-[15px] custom-scrollbar bg-white">
                <!-- Step Progress -->
                <div class="px-2 pb-4 bg-white shrink-0">
                    <div class="flex items-center gap-1.5">
                        <div v-for="s in 6" :key="s" class="h-1.5 flex-1 rounded-full transition-all duration-500"
                             :class="s <= currentStep ? 'bg-indigo-500 shadow-[0_0_8px_rgba(99,102,241,0.4)]' : 'bg-slate-100'"></div>
                    </div>
                    <div class="flex justify-between mt-2 px-1">
                        <span class="text-[11px] font-black text-slate-300 uppercase tracking-[0.2em]">Step {{ currentStep }} / 6</span>
                        <span class="text-[11px] font-black text-indigo-500 uppercase tracking-[0.2em]">{{ stepTitles[currentStep - 1] }}</span>
                    </div>
                </div>

                <transition name="step-fade" mode="out-in">
                    <!-- STEP 1: 得知日期 -->
                    <div v-if="currentStep === 1" :key="'s1'" class="space-y-[15px] animate-fade-in">
                        <div class="space-y-0.5 mt-[-5px]">
                            <div class="flex items-center justify-between px-1">
                                <label class="app-title ml-1">得知日期</label>
                                <button @click.stop="activeDate = 'know_date'" class="text-slate-400 hover:text-indigo-600 transition-colors p-1 active:scale-90">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </button>
                            </div>
                            <div class="relative flex items-center">
                                <input v-model="form.know_date" type="text" placeholder="年/月/日 或 註記文字" 
                                    class="w-full border-0 border-b-2 border-slate-300 bg-transparent py-[10px] px-2 focus:ring-0 outline-none app-body font-bold text-[15px]">
                            </div>
                        </div>
                    </div>

                    <!-- STEP 2: 法號與對象 -->
                    <div v-else-if="currentStep === 2" :key="'s2'" class="space-y-[15px] animate-fade-in">
                        <!-- 法號 -->
                        <div class="space-y-0.5 mt-[-5px]">
                            <label class="app-title ml-1">法號</label>
                            <div class="relative flex items-center border-0 border-b-2 border-slate-300 bg-transparent overflow-visible min-h-[44px] dharma-dropdown">
                                <input v-model="dharmaSearch" type="text" placeholder="搜尋或選擇法號..." 
                                    @focus="activeDharmaDropdown = true"
                                    class="w-full bg-transparent border-none px-2 text-[15px] focus:ring-0 outline-none app-body font-bold">
                                <button @click.stop="activeDharmaDropdown = !activeDharmaDropdown" class="p-1.5 text-slate-300 hover:text-indigo-600 transition-all">
                                    <svg class="w-5 h-5" :class="activeDharmaDropdown ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </button>
                                <input type="hidden" v-model="form.user_name">
                                <div v-if="activeDharmaDropdown" class="absolute left-0 top-full mt-1 w-full bg-white rounded-xl shadow-[0_10px_30px_rgba(0,0,0,0.15)] border border-slate-100 z-[2100] overflow-hidden p-1 animate-fade-in max-h-[200px] overflow-y-auto custom-scrollbar">
                                    <div v-for="u in filteredDharmaNames" :key="u.id"
                                        @click.stop="selectDharma(u.name)"
                                        class="px-3 py-2.5 text-[16px] text-slate-700 hover:bg-indigo-50 active:bg-indigo-100 md:rounded-lg cursor-pointer transition-colors font-medium">
                                        {{ u.name }}
                                    </div>
                                    <div v-if="filteredDharmaNames.length === 0" class="px-3 py-2.5 text-[16px] text-slate-400 text-center">
                                        無符合的法號
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 備註對象 -->
                        <div class="space-y-0.5">
                            <label class="app-title ml-1">備註對象</label>
                            <div class="relative flex items-center border-0 border-b-2 border-slate-300 bg-transparent overflow-visible min-h-[44px] remarks-dropdown">
                                <input v-model="form.user_remarks" type="text" placeholder="備註對象 (例如：母親)..." 
                                    @focus="activeRemarksDropdown = true"
                                    class="w-full bg-transparent border-none px-2 text-[15px] focus:ring-0 outline-none app-body">
                                <button @click.stop="activeRemarksDropdown = !activeRemarksDropdown" class="p-1.5 text-slate-300 hover:text-indigo-600 transition-all">
                                    <svg class="w-5 h-5" :class="activeRemarksDropdown ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </button>

                                <div v-if="activeRemarksDropdown" class="absolute left-0 top-full mt-1 w-full bg-white rounded-xl shadow-[0_10px_30px_rgba(0,0,0,0.15)] border border-slate-100 z-[2100] overflow-hidden p-1 animate-fade-in max-h-[180px] overflow-y-auto custom-scrollbar">
                                    <div v-for="opt in relationshipOptions" :key="opt"
                                        @click.stop="form.user_remarks = opt; activeRemarksDropdown = false"
                                        class="px-3 py-2.5 text-[16px] text-slate-700 hover:bg-indigo-50 active:bg-indigo-100 md:rounded-lg cursor-pointer transition-colors font-medium">
                                        {{ opt }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- STEP 3: 載錄數量 -->
                    <div v-else-if="currentStep === 3" :key="'s3'" class="space-y-[15px] animate-fade-in">
                        <div class="space-y-0.5 mt-[-5px]">
                            <label class="app-title ml-1">載錄數量</label>
                            <input v-model="form.quantity" type="text" inputmode="numeric" @click.stop class="w-full border-0 border-b-2 border-slate-300 bg-transparent py-[10px] px-2 focus:ring-0 outline-none app-body text-[15px]">
                        </div>
                    </div>

                    <!-- STEP 4: 處理結果 -->
                    <div v-else-if="currentStep === 4" :key="'s4'" class="space-y-[15px] animate-fade-in">
                        <!-- 處理日期 -->
                        <div class="space-y-0.5 mt-[-5px]">
                            <div class="flex items-center justify-between px-1">
                                <label class="app-title ml-1">處理日期</label>
                                <button @click.stop="activeDate = 'process_date'" class="text-slate-400 hover:text-indigo-600 transition-colors p-1 active:scale-90">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </button>
                            </div>
                            <div class="relative flex items-center">
                                <input v-model="form.process_date" type="text" placeholder="年/月/日 或 註記文字" 
                                    class="w-full border-0 border-b-2 border-slate-300 bg-transparent py-[10px] pl-2 pr-2 focus:ring-0 outline-none app-body text-[15px]">
                            </div>
                        </div>

                        <!-- 處理結果 -->
                        <div class="space-y-0.5 relative">
                            <label class="app-title ml-1">處理結果</label>
                            <div class="relative">
                                <input v-model="form.destination" 
                                    type="text" 
                                    placeholder="選擇或輸入結果..." 
                                    @click.stop="showResultPicker = true"
                                    class="w-full border-0 border-b-2 border-slate-300 bg-transparent py-[10px] pl-2 pr-10 focus:ring-0 outline-none app-body font-bold text-[15px]"
                                    :class="[
                                        form.destination === '未處理' ? 'text-slate-400' : 
                                        form.destination === '九天' ? 'text-red-600' : 
                                        form.destination === '耀紫軍' ? 'text-purple-600' : ''
                                    ]">
                                <button @click.stop="showResultPicker = !showResultPicker" class="absolute right-2 top-1/2 -translate-y-1/2 text-slate-300 p-1">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </button>
                            </div>
                        </div>

                        <!-- Sub-options for 黑曜軍 -->
                        <div v-if="form.destination === '黑曜軍'" class="grid grid-cols-2 gap-[5px] animate-fade-in">
                            <div class="space-y-0.5">
                                <label class="app-title ml-1">閻尊</label>
                                <input v-model="form.remarks.yan_zun" type="text" inputmode="numeric" placeholder="0" @click.stop class="w-full border-0 border-b-2 border-slate-300 bg-transparent py-[10px] px-2 focus:ring-0 outline-none app-body text-[15px]">
                            </div>
                            <div class="space-y-0.5">
                                <label class="app-title ml-1">閻闇</label>
                                <input v-model="form.remarks.yan_an" type="text" inputmode="numeric" placeholder="0" @click.stop class="w-full border-0 border-b-2 border-slate-300 bg-transparent py-[10px] px-2 focus:ring-0 outline-none app-body text-[15px]">
                            </div>
                        </div>

                        <!-- Sub-options for 耀紫軍 -->
                        <div v-if="form.destination === '耀紫軍'" class="grid grid-cols-2 gap-[5px] animate-fade-in">
                            <div class="space-y-0.5">
                                <label class="app-title ml-1">龍勝</label>
                                <input v-model="form.remarks.long_sheng" type="text" inputmode="numeric" placeholder="0" @click.stop class="w-full border-0 border-b-2 border-slate-300 bg-transparent py-[10px] px-2 focus:ring-0 outline-none app-body text-[15px]">
                            </div>
                            <div class="space-y-0.5">
                                <label class="app-title ml-1">龍戰</label>
                                <input v-model="form.remarks.long_zhan" type="text" inputmode="numeric" placeholder="0" @click.stop class="w-full border-0 border-b-2 border-slate-300 bg-transparent py-[10px] px-2 focus:ring-0 outline-none app-body text-[15px]">
                            </div>
                        </div>
                    </div>

                    <!-- STEP 5: 備註文字 -->
                    <div v-else-if="currentStep === 5" :key="'s5'" class="space-y-[15px] animate-fade-in">
                        <div class="space-y-1 mt-[-5px]">
                            <label class="app-title ml-1">備註文字</label>
                            <input v-model="form.remarks_text" type="text" placeholder="輸入相關備註..." @click.stop class="w-full border-0 border-b-2 border-slate-300 bg-transparent py-[10px] px-3 focus:ring-0 outline-none app-body text-[17px] font-bold">
                        </div>
                    </div>

                    <!-- STEP 6: 預覽與確認 -->
                    <div v-else-if="currentStep === 6" :key="'s6'" class="space-y-[15px] animate-fade-in">
                        <div class="bg-white p-5 rounded-[32px] border border-slate-100 shadow-xl space-y-3">
                            <div class="text-[13px] font-black text-slate-400 uppercase tracking-[0.3em] border-b border-slate-50 pb-1.5">最終載錄預覽</div>
                            
                            <div class="space-y-1.5">
                                <div class="flex flex-col">
                                    <span class="text-[10px] font-black text-slate-300 uppercase tracking-widest">得知日期</span>
                                    <span class="text-[17px] font-black text-slate-900 leading-none">{{ form.know_date }}</span>
                                </div>

                                <div class="flex flex-col border-t border-slate-50 pt-1">
                                    <span class="text-[10px] font-black text-slate-300 uppercase tracking-widest">載錄對象</span>
                                    <div class="flex items-baseline gap-1.5 leading-none">
                                        <span class="text-[17px] font-black text-indigo-600">{{ form.user_name }}</span>
                                        <span v-if="form.user_remarks" class="text-[17px] font-black text-slate-400">({{ form.user_remarks }})</span>
                                    </div>
                                </div>

                                <div class="flex flex-col border-t border-slate-50 pt-1">
                                    <span class="text-[10px] font-black text-slate-300 uppercase tracking-widest">載錄數量</span>
                                    <span class="text-[17px] font-black text-slate-900 leading-none">{{ form.quantity }}</span>
                                </div>

                                <div class="flex flex-col border-t border-slate-50 pt-1">
                                    <span class="text-[10px] font-black text-slate-300 uppercase tracking-widest">處理結果</span>
                                    <div class="flex items-center gap-2 leading-none">
                                        <span class="text-[17px] font-black" :class="form.destination === '九天' ? 'text-red-600' : 'text-slate-900'">{{ form.destination }}</span>
                                        <span v-if="form.process_date" class="text-slate-400 text-[17px] font-black">({{ form.process_date }})</span>
                                    </div>
                                </div>

                                <div v-if="form.remarks_text" class="flex flex-col border-t border-slate-50 pt-1">
                                    <span class="text-[10px] font-black text-slate-300 uppercase tracking-widest">備註內容</span>
                                    <span class="text-[17px] font-black text-slate-600 leading-tight">{{ form.remarks_text }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </transition>
            </div>

            <!-- Footer Action -->
            <div class="absolute bottom-[7dvh] left-0 right-0 md:relative md:bottom-0 px-6 py-[4px] bg-white border-t border-slate-50 z-[10] flex gap-3 justify-center shrink-0">
                <button v-if="currentStep > 1" @click="currentStep--" class="w-[100px] py-[12px] bg-slate-100 text-slate-400 rounded-2xl font-black text-[17px] active:scale-95 transition-all">上一步</button>
                <button v-if="currentStep < 6" @click="handleNext" class="flex-1 py-[12px] bg-indigo-600 !text-white rounded-2xl font-black text-[17px] shadow-lg shadow-indigo-100 active:scale-95 transition-all" style="color: white !important;">下一步</button>
                <button v-else @click="handleSave" class="flex-1 py-[12px] bg-emerald-600 !text-white rounded-2xl font-black text-[17px] shadow-lg shadow-emerald-100 active:scale-95 transition-all" style="color: white !important;">
                    {{ editingId ? '確認修改' : '確認載錄' }}
                </button>
            </div>

            <!-- Navbar Wrapper Removed for Modal UX -->
            <div class="h-[env(safe-area-inset-bottom)] md:h-0"></div>

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
                            <div v-for="opt in ['未處理']" :key="opt"
                                @click="form.destination = opt; onDestinationChange(); showResultPicker = false"
                                class="h-[48px] flex items-center px-4 rounded-xl border font-black text-[17px] leading-tight transition-all active:scale-95 cursor-pointer"
                                :class="[
                                    form.destination === opt ? 'bg-indigo-600 text-white border-indigo-500 shadow-md' : 'bg-slate-50 text-slate-900 border-slate-100'
                                ]"
                                :style="{ color: form.destination === opt ? 'white !important' : '#0f172a !important' }"
                            >
                                {{ opt }}
                            </div>
                        </div>
                        <!-- Right Column -->
                        <div class="space-y-6">
                            <div v-for="opt in ['九天', '黑曜軍', '虎賁軍', '虎甲軍', '耀紫軍']" :key="opt"
                                @click="form.destination = opt; onDestinationChange(); showResultPicker = false"
                                class="h-[48px] flex items-center px-4 rounded-xl border font-black text-[17px] leading-tight transition-all active:scale-95 cursor-pointer"
                                :class="[
                                    form.destination === opt ? 'bg-indigo-600 text-white border-indigo-500 shadow-md' : 'bg-slate-50 border-slate-100',
                                    (opt === '九天' && form.destination !== opt) ? 'text-rose-600' : 
                                    (opt === '耀紫軍' && form.destination !== opt) ? 'text-purple-600' : 
                                    (form.destination !== opt) ? 'text-slate-900' : ''
                                ]"
                                :style="{ color: form.destination === opt ? 'white !important' : '' }"
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
        <!-- Global Action Confirm / Toast (Critical for iOS) -->
        <div v-if="persistentToast" class="fixed inset-0 z-[9999] flex items-center justify-center p-6 bg-slate-900/40 backdrop-blur-sm animate-fade-in">
            <div class="bg-white w-full max-w-sm rounded-[32px] shadow-2xl overflow-hidden animate-slide-up border border-white/20">
                <div class="p-8 text-center space-y-6">
                    <div class="flex flex-col items-center">
                        <div v-if="persistentToast.type === 'error'" class="w-16 h-16 bg-rose-50 text-rose-500 rounded-2xl flex items-center justify-center mb-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        </div>
                        <div v-else class="w-16 h-16 bg-indigo-50 text-indigo-500 rounded-2xl flex items-center justify-center mb-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <h3 class="text-[20px] font-black text-slate-900 leading-tight whitespace-pre-wrap">{{ persistentToast.msg }}</h3>
                    </div>

                    <div class="flex flex-col space-y-3">
                        <button @click="persistentToast = null" 
                                class="w-full py-4 bg-indigo-600 !text-white rounded-2xl font-black text-[18px] active:scale-95 transition-all shadow-lg"
                                style="color: white !important;">
                            確認
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </teleport>
</template>

<script setup>
import { ref, watch, computed, onMounted, onUnmounted } from 'vue';
import CompactDatePicker from './CompactDatePicker.vue';
import MobileNavbar from './MobileNavbar.vue';

const props = defineProps({
    show: Boolean,
    initialData: Object,
    editingId: [Number, String],
    users: Array
});

const emit = defineEmits(['save', 'cancel']);
const currentStep = ref(1);
const stepTitles = ['得知日期', '法號與對象', '載錄數量', '處理結果', '備註文字', '預覽確認'];
const activeDate = ref(null);

const handleNext = () => {
    if (currentStep.value === 1) {
        if (!form.value.know_date) {
            persistentToast.value = { msg: '請輸入得知日期', type: 'error' };
            setTimeout(() => { persistentToast.value = null; }, 2000);
            return;
        }
    }
    if (currentStep.value === 2) {
        if (!form.value.user_name) {
            persistentToast.value = { msg: '請選擇或輸入法號', type: 'error' };
            setTimeout(() => { persistentToast.value = null; }, 2000);
            return;
        }
    }
    if (currentStep.value === 3) {
        if (!form.value.quantity && form.value.destination !== '黑曜軍' && form.value.destination !== '耀紫軍') {
            persistentToast.value = { msg: '請輸入數量', type: 'error' };
            setTimeout(() => { persistentToast.value = null; }, 2000);
            return;
        }
    }
    if (currentStep.value === 4) {
        if (!form.value.destination) {
            persistentToast.value = { msg: '請選擇處理結果', type: 'error' };
            setTimeout(() => { persistentToast.value = null; }, 2000);
            return;
        }
    }
    if (currentStep.value < 6) currentStep.value++;
};
const showResultPicker = ref(false);
const activeRemarksDropdown = ref(false);
const activeDharmaDropdown = ref(false);
const dharmaSearch = ref('');
const persistentToast = ref(null);
const relationshipOptions = ['母親', '父親', '公公', '婆婆', '爺爺', '奶奶', '外公', '外婆'];

const filteredDharmaNames = computed(() => {
    if (!dharmaSearch.value) return props.users || [];
    const search = dharmaSearch.value.toLowerCase();
    return (props.users || []).filter(u => u.name.toLowerCase().includes(search));
});

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

const selectDharma = (name) => {
    form.value.user_name = name;
    dharmaSearch.value = name;
    activeDharmaDropdown.value = false;
};

// Watch form.user_name to sync with dharmaSearch
watch(() => form.value.user_name, (newVal) => {
    if (newVal && !dharmaSearch.value) {
        dharmaSearch.value = newVal;
    }
}, { immediate: true });

// Close dropdowns when clicking outside
const handleClickOutside = (e) => {
    if (activeDharmaDropdown.value && !e.target.closest('.dharma-dropdown')) {
        activeDharmaDropdown.value = false;
    }
    if (activeRemarksDropdown.value && !e.target.closest('.remarks-dropdown')) {
        activeRemarksDropdown.value = false;
    }
};

onMounted(() => {
    document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
});

// Helpers for BigInt operations on potentially large numeric strings
const isValidBigInt = (val) => {
    if (val === null || val === undefined || val === '') return false;
    try {
        BigInt(String(val).replace(/,/g, ''));
        return true;
    } catch (e) {
        return false;
    }
};

const tryBigIntSum = (v1, v2) => {
    try {
        const b1 = isValidBigInt(v1) ? BigInt(String(v1).replace(/,/g, '')) : 0n;
        const b2 = isValidBigInt(v2) ? BigInt(String(v2).replace(/,/g, '')) : 0n;
        return b1 + b2;
    } catch (e) {
        return 0n;
    }
};

// Auto-sum logic for "二人份" (split quantities) using BigInt to prevent overflow
watch(() => form.value.remarks, (newRemarks) => {
    if (form.value.destination === '黑曜軍') {
        form.value.quantity = tryBigIntSum(newRemarks.yan_zun, newRemarks.yan_an).toString();
    } else if (form.value.destination === '耀紫軍') {
        form.value.quantity = tryBigIntSum(newRemarks.long_sheng, newRemarks.long_zhan).toString();
    }
}, { deep: true });

const onDestinationChange = () => {
    if (form.value.destination === '未處理') {
        form.value.process_date = '';
    }
    if (form.value.destination === '黑曜軍') {
        form.value.remarks.yan_zun = form.value.remarks.yan_zun || "0";
        form.value.remarks.yan_an = form.value.remarks.yan_an || "0";
    } else {
        delete form.value.remarks.yan_zun;
        delete form.value.remarks.yan_an;
    }

    if (form.value.destination === '耀紫軍') {
        form.value.remarks.long_sheng = form.value.remarks.long_sheng || "0";
        form.value.remarks.long_zhan = form.value.remarks.long_zhan || "0";
    } else {
        delete form.value.remarks.long_sheng;
        delete form.value.remarks.long_zhan;
    }
};

// Auto-correct terminology for relatives removed to preserve original terms
/*
watch(() => form.user_remarks, (newVal) => {
    if (!newVal) return;
    let rel = newVal.trim();
    if (rel === '之母' || rel === '母') form.user_remarks = '母親';
    else if (rel === '之父' || rel === '父') form.user_remarks = '父親';
    else if (rel.startsWith('之') || rel.startsWith('的')) {
        form.user_remarks = rel.substring(1);
    }
});
*/

const handleSave = () => {
    // Validation Rules
    if (!form.value.user_name) {
        persistentToast.value = { msg: '✖ 請選擇或輸入「法號」，不可留空。', type: 'error' };
        return;
    }

    const clean = (val) => String(val || 0).replace(/,/g, '');

    // Ensure all quantity fields are cleaned of commas and cast to strings for BigInt support
    form.value.quantity = clean(form.value.quantity);
    if (form.value.remarks) {
        Object.keys(form.value.remarks).forEach(k => {
            if (['yan_zun', 'yan_an', 'long_sheng', 'long_zhan'].includes(k)) {
                form.value.remarks[k] = clean(form.value.remarks[k]);
            }
        });
    }

    // Ensure we emit a clean object clone to avoid reference issues
    const finalData = JSON.parse(JSON.stringify(form.value));
    emit('save', finalData);
};
</script>

<style scoped>
.animate-slide-up {
    animation: slideUp 0.25s cubic-bezier(0.16, 1, 0.3, 1);
}
@keyframes slideUp {
    from { transform: translateY(100%); }
    to { transform: translateY(0); }
}
.step-fade-enter-active, .step-fade-leave-active {
    transition: opacity 0.2s ease, transform 0.2s ease;
}
.step-fade-enter-from, .step-fade-leave-to {
    opacity: 0;
    transform: translateX(10px);
}
</style>
