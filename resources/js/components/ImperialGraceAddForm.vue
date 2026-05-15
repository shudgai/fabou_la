<template>
    <teleport to="body">
    <div v-if="mode" class="fixed inset-0 z-[3500] flex items-end md:items-center justify-center">
        <!-- Backdrop -->
        <div class="hidden md:block fixed inset-0 bg-slate-900/40 backdrop-blur-sm" @click="$emit('close')"></div>

        <!-- Form Container -->
        <div class="relative w-full h-full md:h-auto md:max-h-[95dvh] md:max-w-xl bg-white md:rounded-[32px] md:shadow-2xl flex flex-col overflow-hidden animate-slide-up">
        <!-- Header -->
        <div class="px-4 py-3 flex items-center bg-white border-b border-slate-50 shrink-0 relative">
            <div class="flex-1 flex flex-col items-center justify-center min-w-0">
                <div class="leading-tight font-outfit tracking-widest break-words flex items-center justify-center gap-2" style="color: #dc1428 !important; font-size: 30px !important; font-weight: 900 !important;">
                    <logo-imperial-notebook :height="36" class="md:hidden" />
                    重大皇恩專區
                </div>
                <div class="text-[24px] font-black text-slate-400 mt-1 leading-tight text-center">
                    {{ localMode.startsWith('batch') ? '多筆載錄' : '逐筆載錄' }}
                </div>
            </div>
            <button @click="$emit('close')" class="text-slate-300 hover:text-slate-600 transition-colors p-2 absolute right-4 top-1/2 -translate-y-1/2 z-[50]">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
        </div>

        <!-- Progress Bar -->
        <div class="px-[15px] pt-[15px] pb-[10px] shrink-0 border-b border-slate-50">
            <div class="flex items-center justify-between gap-1">
                <div v-for="s in totalSteps" :key="s" 
                        class="h-1 flex-1 rounded-full transition-all duration-500"
                        :class="s <= currentStep ? 'bg-indigo-500' : 'bg-slate-100'">
                </div>
            </div>
            <div class="flex justify-between mt-2">
                <span class="text-[11px] font-black text-slate-300 uppercase tracking-widest">步驟 {{ currentStep }} / {{ totalSteps }}</span>
                <span class="text-[11px] font-black text-indigo-400 uppercase tracking-widest">{{ currentStepTitles[currentStep - 1] }}</span>
            </div>
        </div>

        <!-- Scrollable Content -->
        <div ref="scrollContainer" class="flex-1 overflow-y-auto px-4 pt-4 pb-20 custom-scrollbar overscroll-contain bg-white">
            <transition name="step-fade" mode="out-in">
                <!-- STEP 1: Metadata -->
                <div v-if="currentStep === 1" :key="1" class="flex flex-col items-center justify-start pt-[15px] space-y-8 animate-fade-in text-center">
                    <h2 class="text-[17px] font-black text-slate-900 leading-relaxed tracking-tight">
                        <template v-if="localMode.startsWith('batch')">
                            請選擇<span class="text-red-600">載錄目標仙師</span>
                        </template>
                        <template v-else>
                            請輸入<span class="text-indigo-600">得知日期</span>
                        </template>
                    </h2>
                    <div class="space-y-10 w-full max-w-sm mt-8">
                        <div v-if="localMode.startsWith('single')" class="relative group">
                            <label class="absolute -top-6 left-0 text-[13px] font-black text-slate-300 uppercase tracking-widest">日期</label>
                            <input v-model="form.record_date" type="text" placeholder="日期格式 年-月-日" 
                                class="w-full text-center text-[17px] font-black border-0 border-b-2 border-slate-300 focus:border-indigo-500 bg-transparent py-4 outline-none transition-all placeholder:text-slate-200">
                            <button @click="activePicker = { field: 'record_date', title: '修改得知日期' }" class="absolute right-0 bottom-4 text-slate-300 hover:text-indigo-500 transition-colors">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2v12a2 2 0 002 2z" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </button>
                        </div>
                        <div v-if="localMode.startsWith('batch')" class="relative group mt-12">
                            <label class="absolute -top-6 left-0 text-[13px] font-black text-slate-300 uppercase tracking-widest">載錄目標仙師</label>
                            <button @click="showMasterModal = true"
                                class="w-full text-center text-[18px] font-black border-0 border-b-2 border-slate-300 bg-transparent py-4 outline-none transition-all">
                                {{ displayMasterName || '點選選擇仙師...' }}
                            </button>
                        </div>
                    </div>
                </div>

                <!-- STEP 2: Content Input -->
                <div v-else-if="currentStep === 2" :key="2" class="flex flex-col items-center justify-start pt-[15px] space-y-8 animate-fade-in text-center">
                    <template v-if="localMode.startsWith('single')">
                        <h2 class="text-[17px] font-black text-slate-900 leading-relaxed tracking-tight">請選擇<span class="text-red-600">載錄目標仙師</span></h2>
                        <div class="space-y-10 w-full max-w-sm mt-12">
                            <div class="relative group">
                                <label class="absolute -top-6 left-0 text-[13px] font-black text-slate-300 uppercase tracking-widest">仙師名稱</label>
                                <button @click="showMasterModal = true"
                                    class="w-full text-center text-[18px] font-black border-0 border-b-2 border-slate-300 bg-transparent py-4 outline-none transition-all">
                                    {{ displayMasterName || '點選選擇仙師...' }}
                                </button>
                            </div>
                        </div>
                    </template>
                    <template v-else>
                        <h2 class="text-[17px] font-black text-slate-900 leading-relaxed tracking-tight">請貼入<span class="text-indigo-600">多筆載錄內容</span></h2>
                        <div class="w-full mx-auto mt-12 px-2">
                            <div class="bg-slate-50/50 rounded-3xl p-4 border border-slate-100">
                                <textarea v-model="batchInput" rows="12" placeholder="請將內容貼於此處...&#10;&#10;法寶名稱&#10;用意...&#10;備註..." 
                                    class="w-full bg-transparent border-none focus:ring-0 outline-none text-[17px] font-black text-slate-700 custom-scrollbar leading-relaxed text-left placeholder:text-slate-200"></textarea>
                            </div>
                            <p class="text-[12px] font-bold text-slate-300 mt-6 leading-relaxed">※ 每筆資料間「務必」空一行 (雙換行) <br> 以免系統誤判為同一項</p>
                        </div>
                    </template>
                </div>

                <!-- STEP 3: Preview/Detail -->
                <div v-else-if="currentStep === 3" :key="3" class="space-y-6 animate-fade-in text-center w-full pt-[15px]">
                    <template v-if="localMode.startsWith('single')">
                        <h2 class="text-[17px] font-black text-slate-900 leading-relaxed tracking-tight">請輸入<span class="text-indigo-600">法寶名稱</span></h2>
                        <div class="max-w-sm mx-auto mt-12">
                            <input v-model="form.name" type="text" placeholder=""
                                class="w-full text-center text-[18px] font-black border-0 border-b-2 border-slate-300 focus:border-indigo-500 bg-transparent py-4 outline-none transition-all placeholder:text-slate-200">
                        </div>
                    </template>
                    <template v-else>
                        <h2 class="text-[17px] font-black text-slate-900 leading-relaxed tracking-tight">多筆載錄內容<span class="text-indigo-600">預覽</span></h2>
                        <div class="max-w-xl mx-auto mt-6 bg-white border border-slate-100 rounded-3xl overflow-hidden shadow-sm">
                            <div class="bg-indigo-50/30 px-4 py-2 flex justify-between items-center border-b border-slate-100">
                                <span class="text-[13px] font-black text-indigo-600">得知日期：{{ form.record_date || '-' }}</span>
                                <span class="text-[13px] font-black text-red-600">{{ getMasterName(form.master_id) }}</span>
                            </div>
                            <div class="divide-y divide-slate-50 max-h-64 overflow-y-auto custom-scrollbar">
                                <div v-for="(line, idx) in rawLines" :key="idx" class="px-4 py-2.5 text-[14px] font-bold text-slate-900 flex items-start gap-3 hover:bg-slate-50 text-left">
                                    <span class="text-[11px] font-black text-slate-300 w-6 shrink-0 mt-0.5">{{ idx + 1 }}</span>
                                    <span class="break-all whitespace-pre-wrap">{{ line }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="text-[14px] font-black text-slate-900 mt-6 bg-indigo-50/50 p-4 rounded-3xl max-w-sm mx-auto border border-indigo-100">
                            確認無誤後點擊「開始載錄」
                        </div>
                    </template>
                </div>

                <!-- STEPS 4-7: Immersive Details -->
                <div v-else-if="currentStep === 4 && localMode.startsWith('single')" :key="4" class="space-y-6 animate-fade-in text-center w-full pt-[15px]">
                    <h2 class="text-[17px] font-black text-slate-900 leading-relaxed tracking-tight">此法寶的<span class="text-indigo-600">用意</span>為何？</h2>

                    <div class="max-w-md mx-auto mt-12">
                        <textarea v-model="form.purpose" rows="3" placeholder="請簡述用途..." 
                            class="w-full text-center text-[18px] font-black border-0 border-b-2 border-slate-300 focus:border-indigo-500 bg-transparent py-4 outline-none transition-all placeholder:text-slate-200 resize-none leading-relaxed"></textarea>
                    </div>
                </div>

                <div v-else-if="currentStep === 5 && localMode.startsWith('single')" :key="5" class="space-y-[15px] animate-fade-in w-full pt-[15px]">
                    <div class="text-center space-y-6">
                        <h2 class="text-[17px] font-black text-slate-900 leading-relaxed tracking-tight">目前的<span class="text-indigo-600">狀態</span>與<span class="text-red-600">日期</span>？</h2>
                        <div class="grid grid-cols-1 gap-12 max-w-sm mx-auto mt-12">
                            <div class="relative">
                                <label class="absolute -top-6 left-0 text-[13px] font-black text-slate-300 uppercase tracking-widest">狀態</label>
                                <select v-model="form.status" class="w-full text-center text-[18px] font-black border-0 border-b-2 border-slate-300 focus:border-indigo-500 bg-transparent py-4 outline-none transition-all appearance-none">
                                    <option value="未求得">未求得</option>
                                    <option value="已求得">已求得</option>
                                    <option value="已登記">已登記</option>
                                </select>
                            </div>
                            <div class="relative">
                                <label class="absolute -top-6 left-0 text-[13px] font-black text-slate-300 uppercase tracking-widest">{{ form.status === '已登記' ? '登記日期' : (form.status === '已求得' ? '求得日期' : '日期') }}</label>
                                <input v-model="form.obtained_date" type="text" placeholder="日期格式 年-月-日" 
                                    :disabled="form.status === '未求得'"
                                    class="w-full text-center text-[18px] font-black border-0 border-b-2 border-slate-300 focus:border-red-500 bg-transparent py-4 outline-none transition-all disabled:text-slate-200">
                                <button @click="activePicker = { field: 'obtained_date', title: '修改日期' }" 
                                    :disabled="form.status === '未求得'"
                                    class="absolute right-0 bottom-4 text-slate-300 hover:text-red-500 transition-colors disabled:opacity-30 disabled:cursor-not-allowed">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2v12a2 2 0 002 2z" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-else-if="currentStep === 6 && localMode.startsWith('single')" :key="6" class="space-y-6 animate-fade-in text-center w-full pt-[15px]">
                    <h2 class="text-[17px] font-black text-slate-900 leading-relaxed tracking-tight">最後的<span class="text-indigo-600">補充說明</span></h2>
                    <div class="max-w-md mx-auto mt-12">
                        <textarea v-model="form.remarks" rows="6" placeholder="請輸入補充備註 (選填)..." 
                            class="w-full text-center text-[18px] font-black border-0 border-b-2 border-slate-300 focus:border-indigo-500 bg-transparent py-4 outline-none transition-all placeholder:text-slate-200 resize-none leading-relaxed"></textarea>
                    </div>
                </div>

                <div v-else-if="currentStep === 7 && localMode.startsWith('single')" :key="7" class="space-y-6 animate-fade-in text-center w-full pt-[15px]">
                    <h2 class="text-[17px] font-black text-slate-900 leading-relaxed tracking-tight">載錄內容<span class="text-indigo-600">預覽確認</span></h2>
                    <div class="max-w-md mx-auto border border-slate-100 rounded-3xl overflow-hidden shadow-sm bg-white text-left mt-12">
                        <div class="p-6 space-y-4">
                            <div class="flex items-center gap-2 border-b border-slate-50 pb-2">
                                <span v-if="form.record_date" class="text-[18px] font-black text-slate-900">{{ form.record_date }}</span>
                                <span class="text-[18px] font-black text-slate-900">{{ getMasterName(form.master_id) }}</span>
                            </div>
                            <div class="flex justify-between items-center border-b border-slate-50 pb-2">
                                <span class="text-[17px] font-black text-slate-900">{{ form.name || '-' }}</span>
                            </div>
                            <div v-if="form.purpose" class="pt-1 flex flex-col items-start border-b border-slate-50 pb-2">
                                <span class="text-[13px] font-black text-slate-300 uppercase tracking-widest mb-1">用意</span>
                                <span class="text-[17px] font-black text-slate-700 leading-relaxed text-left">{{ form.purpose }}</span>
                            </div>
                            <div v-if="form.remarks" class="pt-1 flex flex-col items-start pb-2">
                                <span class="text-[13px] font-black text-slate-300 uppercase tracking-widest mb-1">備註</span>
                                <div class="text-[15px] font-bold text-slate-400 leading-relaxed text-left">
                                    {{ form.remarks }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-indigo-50/50 p-4 rounded-[30px] max-w-sm mx-auto border border-indigo-100 mt-8">
                        <div class="text-[14px] font-black text-slate-900">確認無誤後點擊「確認載錄」</div>
                    </div>
                </div>
            </transition>
        </div>

        <!-- Combined Footer & Navbar (Zero Gap) -->
        <div class="mt-auto shrink-0 bg-white border-t border-slate-50">
            <!-- Buttons -->
            <div class="px-3 pt-4 pb-1 bg-white flex gap-2 justify-center shadow-[0_-10px_30px_rgba(0,0,0,0.02)] relative z-[20]">
                <button v-if="currentStep > 1" @click="handleBack"
                    class="w-[90px] py-4 bg-slate-100 text-slate-400 rounded-2xl font-black text-[18px] active:scale-95 transition-all">
                    上一步
                </button>
                <button v-if="currentStep < totalSteps" @click="handleNext"
                    class="flex-1 py-4 bg-indigo-600 !text-white rounded-2xl font-black text-[18px] shadow-lg shadow-indigo-100 active:scale-95 transition-all"
                    style="color: white !important;">
                    <span class="!text-white" style="color: white !important;">下一步</span>
                </button>
                <button v-else @click="handleSubmit" :disabled="isSaving || (localMode === 'batch' && excelRows.length === 0)"
                    class="flex-1 py-4 bg-indigo-600 !text-white rounded-2xl font-black shadow-lg shadow-indigo-100 active:scale-95 transition-all disabled:bg-slate-300"
                    style="color: white !important;">
                    <span :class="[localMode.startsWith('batch') ? 'text-[15px]' : 'text-[18px]']" 
                        class="whitespace-nowrap px-1 !text-white"
                        style="color: white !important;">
                        {{ isSaving ? '處理中...' : (localMode.startsWith('single') ? '確認載錄' : `開始載錄這 ${excelRows.length} 筆資料`) }}
                    </span>
                </button>
            </div>

            <!-- Navbar Wrapper Removed for Modal UX -->
            <div class="h-[env(safe-area-inset-bottom)] md:h-0"></div>
        </div>

        <!-- Date Picker Overlay -->
        <compact-date-picker 
            v-if="activePicker"
            v-model="activePickerValue"
            :title="activePicker.title"
            @close="activePicker = null"
        />

        <div v-if="showMasterModal" class="fixed inset-0 z-[5500] flex items-end md:items-center justify-center">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-[2px]" @click="showMasterModal = false"></div>
            <div class="relative w-full md:max-w-sm bg-white md:rounded-[32px] rounded-t-[32px] shadow-2xl max-h-[70dvh] flex flex-col animate-slide-up">
                <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100 shrink-0">
                    <span class="text-[17px] font-black text-slate-900">選擇仙師</span>
                    <button @click="showMasterModal = false" class="p-2 text-slate-300 hover:text-slate-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                </div>
                <div class="overflow-y-auto custom-scrollbar p-2 flex-1">
                    <div v-for="m in masters" :key="m.id"
                        @click="form.master_id = m.id; showMasterModal = false"
                        class="px-4 h-[48px] flex items-center hover:bg-indigo-50 font-black text-[17px] text-slate-900 active:bg-indigo-100 transition-all cursor-pointer">
                        {{ m.name === '父皇仙師' ? '父皇' : m.name }}
                    </div>
                </div>
            </div>
        </div>


    </div>
    </div>
    </teleport>
</template>

<script setup>
import { ref, watch, computed, onMounted, onUnmounted, nextTick } from 'vue';
import CompactDatePicker from './CompactDatePicker.vue';

const props = defineProps({
    mode: String,
    initialData: Object,
    masters: Array,
    dharmaNames: { type: Array, default: () => [] },
    isSaving: Boolean
});

const emit = defineEmits(['saveSingle', 'saveBatch', 'cancel', 'close']);

// --- 1. State Declarations ---
const localMode = ref(props.mode || 'single');
const currentStep = ref(1);
const form = ref({ ...props.initialData });
const batchInput = ref('');
const activePicker = ref(null);
const scrollContainer = ref(null);
const showMasterModal = ref(false);

const displayMasterName = computed(() => {
    if (!form.value.master_id) return '';
    const m = props.masters.find(m => m.id === form.value.master_id);
    return m ? (m.name === '父皇仙師' ? '父皇' : m.name) : '';
});


onMounted(() => {
    document.body.style.overflow = 'hidden';
});

onUnmounted(() => {
    document.body.style.overflow = '';
});

// --- 2. Computed Properties ---
const stepTitles = [
    '得知日期',
    '載錄目標',
    '法寶名稱',
    '法寶用意',
    '狀態與日期',
    '補充說明',
    '預覽確認'
];

const batchStepTitles = [
    '載錄目標',
    '多筆載錄',
    '預覽確認'
];

const currentStepTitles = computed(() => {
    return localMode.value.startsWith('batch') ? batchStepTitles : stepTitles;
});

const totalSteps = computed(() => currentStepTitles.value.length);

const rawLines = computed(() => {
    return batchInput.value.split('\n').map(l => l.trim()).filter(l => l !== '');
});

const excelRows = computed(() => {
    if (!batchInput.value.trim()) return [];
    
    const rawText = batchInput.value.trim();
    let sections = rawText.split(/\n\s*\n+/).filter(s => s.trim());

    // Fallback: If only one section was found but there are multiple non-empty lines,
    // and no explicit separators like double-newlines were useful, treat each line as a name.
    if (sections.length === 1 && rawText.split('\n').filter(l => l.trim()).length > 1) {
        // Only fallback if the single section doesn't seem to have structured data (labels)
        if (!rawText.match(/(用意|備註|日期|得知日期)[:：]/)) {
            sections = rawText.split('\n').filter(l => l.trim());
        }
    }
    
    return sections.map(section => {
        let lines = section.split('\n').map(l => l.trim()).filter(l => l);
        if (lines.length === 0) return null;
        
        // Data Cleaning: Remove common prefixes if user pasted them
        lines = lines.map(line => {
            return line.replace(/^(用意|備註|得知日期|日期|仙師)[:：\s]*/, '').trim();
        });

        return {
            name: lines[0],
            purpose: lines[1] || '-',
            remarks: lines.slice(2).join(' '),
            master_id: form.value.master_id
        };
    }).filter(row => row && row.name);
});

const activePickerValue = computed({
    get: () => {
        if (!activePicker.value) return '';
        return form.value[activePicker.value.field] || '';
    },
    set: (val) => {
        if (activePicker.value) {
            form.value[activePicker.value.field] = val;
        }
    }
});

// --- 3. Methods ---
function handleNext() {
    if (validateStep()) {
        currentStep.value++;
        scrollToTop();
    }
}

function handleBack() {
    if (currentStep.value > 1) {
        currentStep.value--;
        scrollToTop();
    }
}

function validateStep() {
    if (localMode.value.startsWith('single')) {
        if (currentStep.value === 1) return true; // Date is optional
        if (currentStep.value === 2 && !form.value.master_id) return false;
        if (currentStep.value === 3 && !form.value.name) return false;
    } else {
        if (currentStep.value === 1 && !form.value.master_id) return false;
        if (currentStep.value === 2 && !batchInput.value.trim()) return false;
    }
    return true;
}

function scrollToTop() {
    nextTick(() => {
        if (scrollContainer.value) {
            scrollContainer.value.scrollTo({ top: 0, behavior: 'smooth' });
        }
    });
}

function handleSubmit() {
    if (localMode.value.startsWith('single')) {
        emit('saveSingle', { ...form.value });
    } else {
        emit('saveBatch', excelRows.value);
    }
}

function getMasterName(id) {
    const m = props.masters.find(m => m.id === id);
    return m ? (m.name === '父皇仙師' ? '父皇' : m.name) : '-';
}

watch(() => props.initialData, (newVal) => {
    form.value = { ...newVal };
}, { deep: true });
</script>

<style scoped>
.step-fade-enter-active, .step-fade-leave-active {
    transition: all 0.3s ease;
}
.step-fade-enter-from {
    opacity: 0;
    transform: translateX(10px);
}
.step-fade-leave-to {
    opacity: 0;
    transform: translateX(-10px);
}
.custom-scrollbar { -webkit-overflow-scrolling: touch; }
.custom-scrollbar::-webkit-scrollbar {
    width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #e2e8f0;
    border-radius: 10px;
}
</style>
