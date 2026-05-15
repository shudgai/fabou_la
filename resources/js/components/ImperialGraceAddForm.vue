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
            <button @click="$emit('close')" class="text-slate-300 hover:text-slate-600 transition-colors p-2 absolute right-4 top-[55%] -translate-y-1/2 z-[50]">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
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
                            <!-- Unified Master Selection: Searchable Chips Input -->
                            <div class="w-full">
                                <editable-input-chips v-model="masterNameInput" :options="masters.map(m => m.name === '父皇仙師' ? '父皇' : m.name)" @change="resolveMasterId" placeholder="選擇仙師..." />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- STEP 2: Content Input -->
                <div v-else-if="currentStep === 2" :key="2" class="flex flex-col items-center justify-start pt-[15px] space-y-8 animate-fade-in text-center">
                    <template v-if="localMode.startsWith('single')">
                        <h2 class="text-[17px] font-black text-slate-900 leading-relaxed tracking-tight">請選擇<span class="text-red-600">載錄目標仙師</span></h2>
                        <div class="space-y-10 w-full max-w-sm mt-12">
                            <!-- Unified Master Selection: Searchable Chips Input -->
                            <div class="w-full">
                                <editable-input-chips v-model="masterNameInput" :options="masters.map(m => m.name === '父皇仙師' ? '父皇' : m.name)" @change="resolveMasterId" placeholder="選擇仙師..." />
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

                <!-- STEP 3: Item Name -->
                <div v-else-if="currentStep === 3" :key="3" class="space-y-6 animate-fade-in text-center w-full pt-[15px]">
                    <template v-if="localMode.startsWith('single')">
                        <h2 class="text-[17px] font-black text-slate-900 leading-relaxed tracking-tight">請輸入<span class="text-indigo-600">法寶名稱</span></h2>
                        <div class="max-w-sm mx-auto mt-12">
                            <textarea v-model="form.name" rows="3" placeholder="請輸入法寶名稱..."
                                class="w-full text-center text-[18px] font-black border-0 border-b-2 border-slate-300 focus:border-indigo-500 bg-transparent py-4 outline-none transition-all placeholder:text-slate-200 resize-none"></textarea>
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

                <!-- STEP 4: Personnel Selection -->
                <div v-else-if="currentStep === 4 && localMode.startsWith('single')" :key="4" class="space-y-6 animate-fade-in text-center w-full pt-[15px] pb-32">
                    <div class="flex items-center justify-between mb-2">
                        <h2 class="text-[17px] font-black text-slate-900 leading-relaxed tracking-tight">承接<span class="text-blue-600">師兄姐</span></h2>
                        <button @click="addPersonnelRow" class="px-4 py-1.5 bg-blue-600 text-white rounded-2xl text-[14px] font-black active:scale-95 transition-all shadow-md" style="color: white !important;">＋ 新增</button>
                    </div>

                    <div class="space-y-4">
                        <div v-for="(p, idx) in personnel" :key="idx" class="p-5 bg-white rounded-[28px] border border-slate-100 shadow-sm space-y-4 relative animate-fade-in text-left">
                            <button @click="removePersonnelRow(idx)" class="absolute top-4 right-4 text-slate-300 hover:text-red-500 p-1">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </button>

                            <div class="grid grid-cols-2 gap-4">
                                <div class="space-y-1">
                                    <label class="text-[11px] font-black text-slate-300 uppercase tracking-widest ml-1">法號</label>
                                    <editable-input-chips v-model="p.custom_name" :options="dharmaNames.map(dn => dn.name)" placeholder="法號..." />
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[11px] font-black text-slate-300 uppercase tracking-widest ml-1">備註對象</label>
                                    <editable-input-chips v-model="p.relationship" :options="relationshipOptions" placeholder="例如：母親..." />
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div class="space-y-1">
                                    <label class="text-[11px] font-black text-slate-300 uppercase tracking-widest ml-1">狀態</label>
                                    <select v-model="p.status" class="w-full h-[46px] border-0 border-b-2 border-slate-100 bg-transparent px-2 font-black text-slate-900 outline-none">
                                        <option value="未領取">未領取</option>
                                        <option value="已領取">已領取</option>
                                        <option value="已登記">已登記</option>
                                    </select>
                                </div>
                                <div class="space-y-1">
                                    <div class="flex items-center justify-between">
                                        <label class="text-[11px] font-black text-slate-300 uppercase tracking-widest ml-1">日期</label>
                                        <button @click="activePicker = { idx, field: 'obtained_date', title: '領取日期' }" class="text-slate-300 hover:text-blue-500 p-0.5">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2v12a2 2 0 002 2z" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        </button>
                                    </div>
                                    <input v-model="p.obtained_date" type="text" placeholder="年/月/日" class="w-full h-[46px] border-0 border-b-2 border-slate-100 bg-transparent px-2 font-black text-slate-900 outline-none">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-else-if="currentStep === 5 && localMode.startsWith('single')" :key="5" class="space-y-6 animate-fade-in text-center w-full pt-[15px]">
                    <h2 class="text-[17px] font-black text-slate-900 leading-relaxed tracking-tight">此法寶的<span class="text-indigo-600">用意</span>為何？</h2>

                    <div class="max-w-md mx-auto mt-12">
                        <textarea v-model="form.purpose" rows="3" placeholder="請簡述用途..." 
                            class="w-full text-center text-[18px] font-black border-0 border-b-2 border-slate-300 focus:border-indigo-500 bg-transparent py-4 outline-none transition-all placeholder:text-slate-200 resize-none leading-relaxed text-red-600"></textarea>
                    </div>
                </div>

                <div v-else-if="currentStep === 6 && localMode.startsWith('single')" :key="6" class="space-y-12 animate-fade-in text-center w-full pt-[15px]">
                    <h2 class="text-[17px] font-black text-slate-900 leading-relaxed tracking-tight">請確認<span class="text-indigo-600">狀態與日期</span></h2>
                    
                    <div class="space-y-8 max-w-sm mx-auto">
                        <div class="space-y-4">
                            <label class="text-[13px] font-black text-slate-300 uppercase tracking-widest block">法寶狀態</label>
                            <div class="grid grid-cols-1 gap-4">
                                <button v-for="s in ['未領取', '已領取', '已登記']" :key="s"
                                    @click="form.status = s"
                                    :class="form.status === s ? 'bg-indigo-600 text-white shadow-lg' : 'bg-slate-50 text-slate-400'"
                                    class="py-4 rounded-2xl font-black text-[18px] transition-all active:scale-95"
                                    :style="{ color: form.status === s ? 'white !important' : '#94a3b8 !important' }">
                                    {{ s }}
                                </button>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div class="flex items-center justify-center gap-2">
                                <label class="text-[13px] font-black text-slate-300 uppercase tracking-widest">{{ form.status === '已登記' ? '登記' : (form.status === '已領取' ? '領取' : '預定') }}日期</label>
                                <button @click="activePicker = { field: 'obtained_date', title: '選擇日期' }" class="text-slate-300 hover:text-indigo-600 transition-colors p-1 active:scale-90">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2v12a2 2 0 002 2z" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </button>
                            </div>
                            <input v-model="form.obtained_date" type="text" placeholder="年/月/日"
                                class="w-full text-center text-[19px] font-black border-0 border-b-2 border-slate-100 focus:border-indigo-500 bg-transparent py-4 outline-none transition-all placeholder:text-slate-100">
                        </div>
                    </div>
                </div>

                <div v-else-if="currentStep === 7 && localMode.startsWith('single')" :key="7" class="space-y-6 animate-fade-in text-center w-full pt-[15px]">
                    <h2 class="text-[17px] font-black text-slate-900 leading-relaxed tracking-tight">還有其他<span class="text-indigo-600">補充說明</span>嗎？</h2>
                    <div class="max-w-sm mx-auto mt-12">
                        <textarea v-model="form.remarks" rows="8" placeholder="輸入補充說明..."
                            class="w-full text-center text-[18px] font-black border-0 border-b-2 border-slate-300 focus:border-indigo-500 bg-transparent py-4 outline-none transition-all placeholder:text-slate-200 resize-none leading-relaxed"></textarea>
                    </div>
                </div>

                <!-- STEP 8: Review -->
                <div v-else-if="currentStep === 8 && localMode.startsWith('single')" :key="8" class="space-y-8 animate-fade-in text-center w-full pt-[15px] pb-32">
                    <h2 class="text-[17px] font-black text-slate-900 leading-relaxed tracking-tight">載錄內容<span class="text-indigo-600">預覽確認</span></h2>
                    
                    <div class="max-w-md mx-auto border border-slate-100 rounded-[40px] overflow-hidden shadow-xl bg-white text-left">
                        <div class="p-8 space-y-6">
                            <div class="flex flex-col border-b border-slate-50 pb-4">
                                <span class="text-[12px] font-black text-slate-300 uppercase tracking-widest mb-1">得知日期 / 仙師</span>
                                <span class="text-[19px] font-black text-slate-900">{{ form.record_date }} · {{ displayMasterName }}</span>
                            </div>
                            
                            <div class="flex flex-col border-b border-slate-50 pb-4">
                                <span class="text-[12px] font-black text-slate-300 uppercase tracking-widest mb-1">法寶名稱</span>
                                <span class="text-[19px] font-black text-indigo-600 leading-tight">{{ form.name }}</span>
                            </div>

                            <div v-if="personnel.length > 0" class="flex flex-col border-b border-slate-50 pb-4">
                                <span class="text-[12px] font-black text-slate-300 uppercase tracking-widest mb-2">承接人員</span>
                                <div class="space-y-2">
                                    <div v-for="(p, idx) in personnel" :key="idx" class="flex items-center justify-between bg-slate-50 px-3 py-2 rounded-xl">
                                        <span class="text-[16px] font-black text-slate-700">{{ p.custom_name }} <span v-if="p.relationship" class="text-slate-400">({{ p.relationship }})</span></span>
                                        <span class="text-[13px] font-bold text-blue-500">{{ p.status }}</span>
                                    </div>
                                </div>
                            </div>

                            <div v-if="form.purpose" class="flex flex-col border-b border-slate-50 pb-4">
                                <span class="text-[12px] font-black text-slate-300 uppercase tracking-widest mb-1">用意</span>
                                <p class="text-[17px] font-black text-red-600 leading-relaxed whitespace-pre-wrap">{{ form.purpose }}</p>
                            </div>

                            <div class="flex flex-col">
                                <span class="text-[12px] font-black text-slate-300 uppercase tracking-widest mb-1">狀態</span>
                                <span class="text-[17px] font-black text-slate-900">{{ form.status }} ({{ form.obtained_date }})</span>
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
const personnel = ref([]);
const masterNameInput = ref('');
const relationshipOptions = ['母親', '父親', '公公', '婆婆', '爺爺', '奶奶', '外公', '外婆'];

const resolveMasterId = () => {
    const m = props.masters.find(m => m.name === masterNameInput.value || (m.name === '父皇仙師' && masterNameInput.value === '父皇'));
    if (m) form.value.master_id = m.id;
};

watch(masterNameInput, () => {
    resolveMasterId();
});

const addPersonnelRow = () => {
    personnel.value.push({ custom_name: '', relationship: '', obtained_date: form.value.obtained_date || '', status: '未領取', remarks: '' });
};

const removePersonnelRow = (idx) => { personnel.value.splice(idx, 1); };

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
    '承接人員',
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
        const lines = section.split('\n').map(l => l.trim()).filter(l => l);
        if (lines.length === 0) return null;
        
        // REQUIREMENT: No more stripping labels, no more default '-' placeholders.
        // Display strictly what was pasted.
        return {
            name: lines[0],
            purpose: lines[1] || '',
            remarks: lines.slice(2).join('\n'),
            master_id: form.value.master_id
        };
    }).filter(row => row && row.name);
});

const activePickerValue = computed({
    get: () => {
        if (!activePicker.value) return '';
        if (activePicker.value.idx !== undefined) {
            return personnel.value[activePicker.value.idx][activePicker.value.field] || '';
        }
        return form.value[activePicker.value.field] || '';
    },
    set: (val) => {
        if (activePicker.value) {
            if (activePicker.value.idx !== undefined) {
                personnel.value[activePicker.value.idx][activePicker.value.field] = val;
            } else {
                form.value[activePicker.value.field] = val;
            }
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
        if (currentStep.value === 4) return true; // Personnel optional
        if (currentStep.value === 5 && !form.value.purpose) return false;
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
        emit('saveSingle', { 
            ...form.value,
            dharma_name_registries: personnel.value.map(p => ({
                ...p,
                related_personnel: p.relationship ? [p.relationship] : []
            }))
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
