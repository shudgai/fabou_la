<template>
    <teleport to="body">
    <div v-if="mode" class="fixed inset-0 z-[3500] flex items-end md:items-center justify-center">
        <!-- Backdrop -->
        <div class="hidden md:block fixed inset-0 bg-slate-900/40 backdrop-blur-sm" @click="$emit('close')"></div>

        <!-- Form Container -->
        <div class="relative w-full h-full md:h-auto md:max-h-[95dvh] md:max-w-xl bg-white md:rounded-[32px] md:shadow-2xl flex flex-col overflow-hidden animate-slide-up">
        <!-- Header: Standardized Branding (Logo + Main Title + Sub Title) -->
        <div class="px-0 flex flex-col bg-white border-b border-slate-50 relative shrink-0">
            <!-- Row 1: Global Title (Left) -->
            <div class="px-4 py-2 bg-white flex items-center justify-start gap-2 border-b border-transparent">
                <logo-imperial-notebook :height="36" />
                <h1 class="font-outfit !font-normal tracking-widest pt-[2px]" style="color: #dc2626 !important; font-size: 26px !important; font-weight: 400 !important;">重大皇恩專區</h1>
            </div>
            <!-- Row 2: Subtitle (Left) + Close Button (Right) -->
            <div class="px-4 py-1.5 bg-white border-b border-transparent flex items-center justify-between">
                <div class="flex items-baseline gap-x-2 flex-1 min-w-0">
                    <span class="font-outfit font-normal text-slate-900 whitespace-nowrap" style="font-size: 23px !important; line-height: 1.1; transform: translateY(1.5px);">
                        {{ localMode.startsWith('batch') ? '多筆載錄' : '逐筆載錄' }}
                    </span>
                </div>
                <!-- Close Button moved here -->
                <button @click="$emit('close')" class="text-slate-300 hover:text-slate-600 transition-colors p-2 z-[50]">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
            </div>
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
                <span class="text-[11px] font-normal text-black uppercase tracking-widest">步驟 {{ currentStep }} / {{ totalSteps }}</span>
                <span class="text-[11px] font-normal text-black uppercase tracking-widest">{{ currentStepTitles[currentStep - 1] }}</span>
            </div>
        </div>

        <!-- Scrollable Content -->
        <div ref="scrollContainer" class="flex-1 overflow-y-auto px-4 pt-4 pb-20 custom-scrollbar overscroll-contain bg-white">
            <transition name="step-fade" mode="out-in">
                <!-- STEP 1: Metadata -->
                <div v-if="currentStep === 1" :key="1" class="flex flex-col items-center justify-start pt-[15px] space-y-8 animate-fade-in text-center">
                    <h2 class="text-[17px] font-normal text-black leading-relaxed tracking-tight">
                        <template v-if="localMode.startsWith('batch')">
                            請選擇<span class="text-black">載錄目標仙師</span>
                        </template>
                        <template v-else>
                            請輸入<span class="text-black">得知日期</span>
                        </template>
                    </h2>
                        <div v-if="localMode.startsWith('single')" class="relative group">
                            <label class="absolute -top-6 left-0 text-[13px] font-normal text-black uppercase tracking-widest">日期</label>
                            <textarea v-model="form.record_date" rows="2" placeholder="日期格式 年-月-日" 
                                class="w-full text-center text-[17px] font-normal border-0 border-b-2 border-slate-300 focus:border-indigo-500 bg-transparent py-4 outline-none transition-all placeholder:text-slate-200 resize-none leading-relaxed text-black"></textarea>
                            <button @click="activePicker = { field: 'record_date', title: '修改日期' }" class="absolute right-0 bottom-4 text-slate-300 hover:text-indigo-500 transition-colors">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2v12a2 2 0 002 2z" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </button>
                        </div>
                        <div v-if="localMode.startsWith('batch')" class="relative group mt-12">
                            <label class="absolute -top-6 left-0 text-[13px] font-normal text-black uppercase tracking-widest">載錄目標仙師</label>
                            <div class="w-full">
                                <editable-input-chips v-model="masterNameInput" :options="masters.map(m => m.name === '父皇仙師' ? '父皇' : m.name)" @change="resolveMasterId" placeholder="選擇仙師..." />
                            </div>
                        </div>
                </div>

                <!-- STEP 2: Content Input -->
                <div v-else-if="currentStep === 2" :key="2" class="flex flex-col items-center justify-start pt-[15px] space-y-8 animate-fade-in text-center">
                    <template v-if="localMode.startsWith('single')">
                        <h2 class="text-[17px] font-normal text-black leading-relaxed tracking-tight">請選擇<span class="text-black">載錄目標仙師</span></h2>
                        <div class="space-y-10 w-full max-w-sm mt-12">
                            <!-- Unified Master Selection: Searchable Chips Input -->
                            <div class="w-full">
                                <editable-input-chips v-model="masterNameInput" :options="masters.map(m => m.name === '父皇仙師' ? '父皇' : m.name)" @change="resolveMasterId" placeholder="選擇仙師..." />
                            </div>
                        </div>
                    </template>
                    <template v-else>
                        <h2 class="text-[17px] font-normal text-black leading-relaxed tracking-tight">請貼入<span class="text-black">多筆載錄內容</span></h2>
                        <div class="w-full mx-auto mt-12 px-2">
                            <div class="bg-slate-50/50 rounded-3xl p-4 border border-slate-100">
                                <textarea v-model="batchInput" rows="12" placeholder="請將內容貼於此處...&#10;法寶名稱貼的時候要空一行&#10;&#10;法寶名稱&#10;用意...&#10;備註..." 
                                    class="w-full bg-transparent border-none focus:ring-0 outline-none text-[17px] font-normal text-black custom-scrollbar leading-relaxed text-left placeholder:text-slate-200"></textarea>
                            </div>
                            <p class="text-[12px] font-bold text-slate-300 mt-6 leading-relaxed">※ 每筆資料間「務必」空一行 (雙換行) <br> 以免系統誤判為同一項</p>
                        </div>
                    </template>
                </div>

                <!-- STEP 3: Item Name -->
                <div v-else-if="currentStep === 3" :key="3" class="space-y-6 animate-fade-in text-center w-full pt-[15px]">
                    <template v-if="localMode.startsWith('single')">
                        <h2 class="text-[17px] font-normal text-black leading-relaxed tracking-tight">請輸入<span class="text-black">法寶名稱</span></h2>
                        <div class="max-w-sm mx-auto mt-12">
                            <textarea v-model="form.name" rows="3" placeholder="請輸入法寶名稱..."
                                class="w-full text-center text-[18px] font-normal border-0 border-b-2 border-slate-300 focus:border-indigo-500 bg-transparent py-4 outline-none transition-all placeholder:text-slate-200 resize-none text-black"></textarea>
                        </div>
                    </template>
                    <template v-else>
                        <div class="max-w-xl mx-auto mt-6 bg-white border border-slate-100 rounded-3xl overflow-hidden shadow-sm">
                            <div class="bg-indigo-50/30 px-4 py-2 flex justify-end items-center border-b border-slate-100">
                                <span class="text-[13px] font-normal text-black">{{ getMasterName(form.master_id) }}</span>
                            </div>
                            <div class="divide-y divide-slate-50 max-h-64 overflow-y-auto custom-scrollbar">
                                <div v-for="(line, idx) in rawLines" :key="idx" class="px-4 py-2.5 text-[14px] font-normal text-black flex items-start gap-3 hover:bg-slate-50 text-left">
                                    <span class="text-[11px] font-normal text-black w-6 shrink-0 mt-0.5">{{ idx + 1 }}</span>
                                    <span class="break-all whitespace-pre-wrap">{{ line }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="text-[14px] font-normal text-black mt-6 bg-indigo-50/50 p-4 rounded-3xl max-w-sm mx-auto border border-indigo-100">
                            確認無誤後點擊「開始載錄」
                        </div>
                    </template>
                </div>



                <div v-else-if="currentStep === 4 && localMode.startsWith('single')" :key="4" class="space-y-6 animate-fade-in text-center w-full pt-[15px]">
                    <h2 class="text-[17px] font-normal text-black leading-relaxed tracking-tight">此法寶的<span class="text-black">用意</span>為何？ (選填)</h2>

                    <div class="max-w-md mx-auto mt-12">
                        <textarea v-model="form.purpose" rows="3" placeholder="請簡述用途 (選填)..." 
                            class="w-full text-center text-[18px] font-normal border-0 border-b-2 border-slate-300 focus:border-indigo-500 bg-transparent py-4 outline-none transition-all placeholder:text-slate-200 resize-none leading-relaxed text-black"></textarea>
                    </div>
                </div>

                <div v-else-if="currentStep === 5 && localMode.startsWith('single')" :key="5" class="space-y-12 animate-fade-in text-center w-full pt-[15px]">
                    <h2 class="text-[17px] font-normal text-black leading-relaxed tracking-tight">請確認<span class="text-black">狀態與日期</span></h2>
                    
                    <div class="space-y-8 max-w-sm mx-auto">
                        <div class="space-y-4">
                            <label class="text-[13px] font-normal text-black uppercase tracking-widest block">法寶狀態</label>
                            <div class="grid grid-cols-1 gap-4">
                                <button v-for="s in ['未求得', '已求得', '已登記']" :key="s"
                                    @click="form.status = s"
                                    :class="form.status === s ? 'bg-indigo-600 text-white shadow-lg' : 'bg-slate-50 text-slate-400'"
                                    class="py-4 rounded-2xl font-normal text-[18px] transition-all active:scale-95"
                                    :style="{ color: form.status === s ? 'white !important' : '#000000 !important' }">
                                    {{ s }}
                                </button>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div class="flex items-center justify-center gap-2">
                                <label class="text-[13px] font-normal text-black uppercase tracking-widest">{{ form.status === '已登記' ? '登記' : (form.status === '已求得' ? '求得' : '得知') }}日期</label>
                                <button @click="activePicker = { field: 'obtained_date', title: '選擇日期' }" class="text-slate-300 hover:text-indigo-600 transition-colors p-1 active:scale-90">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2v12a2 2 0 002 2z" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </button>
                            </div>
                            <input v-model="form.obtained_date" type="text" placeholder="年/月/日"
                                class="w-full text-center text-[19px] font-normal border-0 border-b-2 border-slate-100 focus:border-indigo-500 bg-transparent py-4 outline-none transition-all placeholder:text-slate-100 text-black">
                        </div>
                    </div>
                </div>

                <div v-else-if="currentStep === 6 && localMode.startsWith('single')" :key="6" class="space-y-6 animate-fade-in text-center w-full pt-[15px]">
                    <h2 class="text-[17px] font-normal text-black leading-relaxed tracking-tight">還有其他<span class="text-black">補充說明</span>嗎？</h2>
                    <div class="max-w-sm mx-auto mt-12">
                        <textarea v-model="form.remarks" rows="8" placeholder="輸入補充說明..."
                            class="w-full text-center text-[18px] font-normal border-0 border-b-2 border-slate-300 focus:border-indigo-500 bg-transparent py-4 outline-none transition-all placeholder:text-slate-200 resize-none leading-relaxed text-black"></textarea>
                    </div>
                </div>

                <!-- STEP 8: Review -->
                <div v-else-if="currentStep === 7 && localMode.startsWith('single')" :key="7" class="space-y-8 animate-fade-in text-center w-full pt-[15px] pb-32">
                    <h2 class="text-[17px] font-normal text-black leading-relaxed tracking-tight">載錄內容<span class="text-black">預覽確認</span></h2>
                    
                    <div class="max-w-md mx-auto border border-slate-100 rounded-[40px] overflow-hidden shadow-xl bg-white text-left">
                        <div class="p-8 space-y-6">
                            <div class="flex flex-col border-b border-slate-50 pb-4">
                                <span class="text-[12px] font-normal text-black uppercase tracking-widest mb-1">得知日期 / 仙師</span>
                                <span class="text-[19px] font-normal text-black">{{ form.record_date }} · {{ displayMasterName }}</span>
                            </div>
                            
                            <div class="flex flex-col border-b border-slate-50 pb-4">
                                <span class="text-[12px] font-normal text-black uppercase tracking-widest mb-1">法寶名稱</span>
                                <span class="text-[19px] font-normal text-black leading-tight">{{ form.name }}</span>
                            </div>



                            <div v-if="form.purpose" class="flex flex-col border-b border-slate-50 pb-4">
                                <span class="text-[12px] font-normal text-black uppercase tracking-widest mb-1">用意</span>
                                <p class="text-[17px] font-normal text-black leading-relaxed whitespace-pre-wrap">{{ form.purpose }}</p>
                            </div>

                            <div class="flex flex-col">
                                <span class="text-[12px] font-normal text-black uppercase tracking-widest mb-1">狀態</span>
                                <span class="text-[17px] font-normal text-black">{{ form.status }} ({{ form.obtained_date }})</span>
                            </div>
                        </div>
                    </div>
                </div>
            </transition>
        </div>

        <!-- Combined Footer & Navbar (Zero Gap) -->
        <div class="mt-auto shrink-0 bg-white border-t border-slate-50">
            <!-- Buttons -->
            <div class="px-3 pt-4 pb-1 bg-white flex gap-2 justify-center shadow-[0_-10px_30px_rgba(0,0,0,0.02)] relative z-[20]">
                <button v-if="currentStep > 1" @click="handleBack"
                    class="w-[90px] py-4 bg-slate-100 text-slate-400 rounded-2xl font-normal text-[18px] active:scale-95 transition-all">
                    上一步
                </button>
                <button v-if="currentStep < totalSteps" @click="handleNext"
                    class="flex-1 py-4 bg-indigo-600 !text-white rounded-2xl font-normal text-[18px] shadow-lg shadow-indigo-100 active:scale-95 transition-all"
                    style="color: white !important;">
                    <span class="!text-white" style="color: white !important;">下一步</span>
                </button>
                <button v-else @click="handleSubmit" :disabled="isSaving || (localMode === 'batch' && excelRows.length === 0)"
                    class="flex-1 py-4 bg-indigo-600 !text-white rounded-2xl font-normal shadow-lg shadow-indigo-100 active:scale-95 transition-all disabled:bg-slate-300"
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

        <!-- Master selection modal removed in favor of unified EditableInputChips -->


    </div>
    </div>
    </teleport>
</template>

<script setup>
import { ref, watch, computed, onMounted, onUnmounted, nextTick } from 'vue';
import CompactDatePicker from './CompactDatePicker.vue';
import EditableInputChips from './EditableInputChips.vue';

const props = defineProps({
    mode: String,
    initialData: Object,
    masters: Array,
    dharmaNames: { type: Array, default: () => [] },
    isSaving: Boolean
});

const emit = defineEmits(['saveSingle', 'saveBatch', 'cancel', 'close', 'openGlobalPicker', 'fileUpload']);

// --- 1. State Declarations ---
const localMode = ref(props.mode || 'single');
const currentStep = ref(1);
const form = ref({ ...props.initialData });
const batchInput = ref('');
const activePicker = ref(null);
const scrollContainer = ref(null);
const masterNameInput = ref('');

const resolveMasterId = () => {
    const m = props.masters.find(m => m.name === masterNameInput.value || (m.name === '父皇仙師' && masterNameInput.value === '父皇'));
    if (m) form.value.master_id = m.id;
};

watch(masterNameInput, () => {
    resolveMasterId();
});



const displayMasterName = computed(() => {
    if (masterNameInput.value) return masterNameInput.value;
    if (!form.value.master_id) return '';
    const m = props.masters.find(m => m.id === form.value.master_id);
    return m ? (m.name === '父皇仙師' ? '父皇' : m.name) : '';
});


onMounted(() => {
    document.body.style.overflow = 'hidden';
    if (form.value.master_id) {
        const m = props.masters.find(m => m.id === form.value.master_id);
        if (m) masterNameInput.value = (m.name === '父皇仙師' ? '父皇' : m.name);
    }
});

onUnmounted(() => {
    document.body.style.overflow = '';
});

// --- 2. Computed Properties ---
const stepTitles = [
    '得知日期',
    '載錄仙師',
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
    
    const parseDateText = (text) => {
        if (!text) return null;
        let s = text.trim();
        // Match 114.10.6 or 114/10/06 or 114-10-6
        const m = s.match(/^(\d{2,4})[./-](\d{1,2})[./-](\d{1,2})$/);
        if (m) {
            let y = parseInt(m[1]);
            let mon = m[2].padStart(2, '0');
            let d = m[3].padStart(2, '0');
            if (y < 1000) y += 1911;
            return `${y}-${mon}-${d}`;
        }
        return null;
    };

    const rawText = batchInput.value.trim();
    let sections = rawText.split(/\n\s*\n+/).filter(s => s.trim());

    if (sections.length === 1 && rawText.split('\n').filter(l => l.trim()).length > 1) {
        if (!rawText.match(/(用意|備註|日期|得知日期)[:：]/)) {
            sections = rawText.split('\n').filter(l => l.trim());
        }
    }
    
    let currentBlockDate = null;

    return sections.map(section => {
        const lines = section.split('\n').map(l => l.trim()).filter(l => l);
        if (lines.length === 0) return null;
        
        let rowDate = null;
        let rowName = '';
        let rowPurpose = '';
        let rowRemarks = '';

        // Case 1: First line is a date
        const dateAtStart = parseDateText(lines[0]);
        if (dateAtStart) {
            rowDate = dateAtStart;
            if (lines.length > 1) {
                rowName = lines[1];
                rowPurpose = lines[2] || '';
                rowRemarks = lines.slice(3).join('\n');
            } else {
                // Single line: might be "Date Name"
                const spaceIdx = lines[0].indexOf(' ');
                const tabIdx = lines[0].indexOf('\t');
                const splitIdx = spaceIdx !== -1 ? spaceIdx : tabIdx;
                if (splitIdx !== -1) {
                    const potentialDate = parseDateText(lines[0].substring(0, splitIdx));
                    if (potentialDate) {
                        rowDate = potentialDate;
                        rowName = lines[0].substring(splitIdx).trim();
                    } else {
                        rowName = lines[0];
                    }
                } else {
                    rowName = lines[0]; // Just a date line with no name? handled below
                }
            }
        } else {
            // Case 2: No date at start of block
            // Check if first line is "Date Name"
            const spaceIdx = lines[0].indexOf(' ');
            const tabIdx = lines[0].indexOf('\t');
            const splitIdx = spaceIdx !== -1 ? spaceIdx : tabIdx;
            if (splitIdx !== -1) {
                const potentialDate = parseDateText(lines[0].substring(0, splitIdx));
                if (potentialDate) {
                    rowDate = potentialDate;
                    rowName = lines[0].substring(splitIdx).trim();
                } else {
                    rowName = lines[0];
                }
            } else {
                rowName = lines[0];
            }
            rowPurpose = lines[1] || '';
            rowRemarks = lines.slice(2).join('\n');
        }

        if (rowName === parseDateText(rowName) || !rowName) return null;

        return {
            name: rowName,
            purpose: rowPurpose,
            remarks: rowRemarks,
            record_date: rowDate,
            obtained_date: rowDate,
            status: '已登記',
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
        if (currentStep.value === 1) return true; 
        if (currentStep.value === 2 && !form.value.master_id) return false;
        if (currentStep.value === 3 && !form.value.name) return false;
        if (currentStep.value === 4) return true; // Purpose optional
        if (currentStep.value === 5) return true; 
        if (currentStep.value === 6) return true; 
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
    if (props.isSaving) return;
    if (localMode.value.startsWith('single')) {
        emit('saveSingle', { 
            ...form.value
        });
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
    if (form.value.master_id) {
        const m = props.masters.find(m => m.id === form.value.master_id);
        if (m) masterNameInput.value = (m.name === '父皇仙師' ? '父皇' : m.name);
    }
}, { deep: true, immediate: true });
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
