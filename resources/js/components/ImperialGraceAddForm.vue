<template>
    <div v-if="mode" class="fixed inset-0 z-[2000] flex items-end md:items-center justify-center px-0 imperial-grace-module" style="overscroll-behavior: contain;">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm" @click="$emit('close')"></div>

        <!-- Form Container -->
        <div class="relative w-full h-[100dvh] md:h-full bg-white md:rounded-3xl shadow-[0_-10px_40px_rgba(0,0,0,0.1)] overflow-hidden animate-slide-up flex flex-col pb-[7dvh]">
            <!-- Header -->
            <div class="px-4 py-3 flex items-center bg-white border-b border-slate-50 relative">
                <div class="flex-1 flex flex-col justify-center min-w-0 pr-6">
                    <div class="text-[26px] font-black leading-tight font-outfit tracking-widest text-red-600 uppercase !font-black flex items-center gap-2" style="color: #dc2626 !important;">
                        <logo-imperial-notebook :height="36" />
                        重大皇恩
                    </div>
                </div>
                <button @click="$emit('close')" class="text-slate-300 hover:text-slate-600 transition-colors py-[5px] absolute right-4 top-1/2 -translate-y-1/2">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
            </div>



            <!-- Scrollable Content -->
            <div ref="scrollContainer" class="flex-1 overflow-y-auto px-3 pt-4 pb-32 space-y-5 custom-scrollbar overscroll-contain">

                <!-- IMMERSIVE ENTRY: One-Thing-At-A-Time -->
                <div class="flex-1 flex flex-col min-h-0 relative">
                    <!-- Progress Indicator -->
                    <div class="px-[15px] pt-[15px] pb-[10px]">
                        <div class="flex items-center justify-between gap-1">
                            <div v-for="s in totalSteps" :key="s" 
                                 class="h-1 flex-1 rounded-full transition-all duration-500"
                                 :class="s <= currentStep ? 'bg-indigo-500' : 'bg-slate-100'">
                            </div>
                        </div>
                        <div class="flex justify-between mt-2">
                            <span class="text-[11px] font-black text-slate-300 uppercase tracking-widest">Step {{ currentStep }} of {{ totalSteps }}</span>
                            <span class="text-[11px] font-black text-indigo-400 uppercase tracking-widest">{{ currentStepTitles[currentStep - 1] }}</span>
                        </div>
                    </div>

                    <!-- Step Content Container - Standard Padding -->
                    <div class="flex-1 flex flex-col justify-start pt-[15px] px-[15px] pb-20">
                        <transition name="step-fade" mode="out-in">
                            <!-- Step 1: Date & Master -->
                            <div v-if="currentStep === 1" :key="1" class="flex-1 flex flex-col items-center justify-start pt-[15px] space-y-12 animate-fade-in text-center">
                                <h2 class="text-[17px] font-black text-slate-900 leading-relaxed tracking-tight">請輸入<br><span class="text-indigo-600">日期</span>與<span class="text-red-600">父皇仙師</span></h2>
                                <div class="space-y-10 w-full max-w-sm mt-12">
                                    <div class="relative group">
                                        <label class="absolute -top-6 left-0 text-[13px] font-black text-slate-300 uppercase tracking-widest">得知日期</label>
                                        <input v-model="form.record_date" type="text" placeholder="YYYY-MM-DD" 
                                            class="w-full text-center text-[17px] font-black border-0 border-b-2 border-slate-100 focus:border-indigo-500 bg-transparent py-4 outline-none transition-all placeholder:text-slate-100">
                                        <button @click="activePicker = { field: 'record_date', title: '修改得知日期' }" class="absolute right-0 bottom-4 text-slate-200 hover:text-indigo-500 transition-colors">
                                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2v12a2 2 0 002 2z" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        </button>
                                    </div>
                                    <div class="relative group">
                                        <label class="absolute -top-6 left-0 text-[13px] font-black text-slate-300 uppercase tracking-widest">載錄目標</label>
                                        <select v-model="form.master_id" class="w-full text-center text-[17px] font-black border-0 border-b-2 border-slate-100 focus:border-red-500 bg-transparent py-4 outline-none transition-all appearance-none">
                                            <option v-for="m in masters" :key="m.id" :value="m.id">{{ m.name === '父皇仙師' ? '父皇' : m.name }}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- STEP 2: Name (Single) or Input (Batch) -->
                            <div v-else-if="currentStep === 2" :key="2" class="space-y-6 animate-fade-in text-center w-full">
                                <template v-if="localMode.startsWith('single')">
                                    <h2 class="text-[17px] font-black text-slate-900 leading-relaxed tracking-tight">請輸入<span class="text-indigo-600">法寶名稱</span></h2>
                                    <div class="max-w-sm mx-auto">
                                        <input v-model="form.name" type="text" placeholder="例如：淨化法水..." 
                                            class="w-full text-center text-[18px] font-black border-0 border-b-2 border-slate-100 focus:border-indigo-500 bg-transparent py-4 outline-none transition-all placeholder:text-slate-100">
                                    </div>
                                </template>
                                <template v-else>
                                    <h2 class="text-[17px] font-black text-slate-900 leading-relaxed tracking-tight">請貼入<span class="text-indigo-600">載錄內容</span></h2>
                                    <div class="max-w-md mx-auto space-y-4 text-left">
                                        <div class="flex items-center justify-between px-1">
                                            <span class="text-[12px] font-black text-slate-300 uppercase tracking-widest">多筆法寶請以空白行分隔</span>
                                            <button v-if="batchInput" @click="batchInput = ''" class="text-rose-500 text-[12px] font-bold">清除</button>
                                        </div>
                                        <textarea v-model="batchInput" rows="6" placeholder="直接貼上文字內容..." 
                                            class="w-full text-center text-[18px] font-black border-0 border-b-2 border-slate-100 focus:border-indigo-500 bg-transparent py-4 outline-none transition-all placeholder:text-slate-100 resize-none leading-relaxed"></textarea>
                                    </div>
                                </template>
                            </div>

                            <!-- STEP 3: Purpose (Single) or Preview (Batch) -->
                            <div v-else-if="currentStep === 3" :key="3" class="space-y-6 animate-fade-in text-center w-full">
                                <template v-if="localMode.startsWith('single')">
                                    <h2 class="text-[17px] font-black text-slate-900 leading-relaxed tracking-tight">此法寶的<span class="text-indigo-600">用意</span>為何？</h2>
                                    <p class="text-[13px] font-bold text-slate-400 mt-1">※ 若沒有用意可直接按下一步跳過</p>
                                    <div class="max-w-md mx-auto mt-4">
                                        <textarea v-model="form.purpose" rows="3" placeholder="請簡述用途..." 
                                            class="w-full text-center text-[18px] font-black border-0 border-b-2 border-slate-100 focus:border-indigo-500 bg-transparent py-4 outline-none transition-all placeholder:text-slate-100 resize-none"></textarea>
                                    </div>
                                </template>
                                <template v-else>
                                    <h2 class="text-[17px] font-black text-slate-900 leading-relaxed tracking-tight">預覽<span class="text-indigo-600">載錄資料</span></h2>
                                    <div v-if="excelRows.length > 0" class="max-w-md mx-auto border border-slate-100 rounded-3xl overflow-hidden shadow-sm bg-white animate-fade-in text-left">
                                        <div class="bg-slate-50 px-4 py-3 border-b border-slate-100 flex justify-between items-center">
                                            <span class="text-[13px] font-black text-slate-500 uppercase tracking-widest">目前解析 ({{ excelRows.length }} 筆)</span>
                                            <span class="text-[11px] font-bold text-slate-300">PREVIEW</span>
                                        </div>
                                        <div class="overflow-x-auto max-h-[300px] custom-scrollbar">
                                            <table class="w-full text-left">
                                                <thead class="bg-slate-50/50 text-[11px] text-slate-400 font-black uppercase tracking-widest sticky top-0 backdrop-blur-sm">
                                                    <tr>
                                                        <th class="px-4 py-2 border-r border-slate-100">日期</th>
                                                        <th class="px-4 py-2">法寶內容</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="divide-y divide-slate-50">
                                                    <tr v-for="(row, idx) in excelRows" :key="idx" class="hover:bg-indigo-50/30 transition-colors">
                                                        <td class="px-4 py-3 font-mono text-slate-500 text-[13px] border-r border-slate-100 align-top">{{ row.record_date || '-' }}</td>
                                                        <td class="px-4 py-3 align-top">
                                                            <div class="text-[15px] font-black text-slate-900 mb-1">{{ row.name }}</div>
                                                            <div v-if="row.purpose" class="text-[12px] text-slate-400 font-bold">{{ row.purpose }}</div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div v-else class="py-12 text-slate-200 font-black text-[18px] italic">尚未輸入載錄內容</div>
                                </template>
                            </div>

                            <!-- STEP 4 (Single Only): STATUS & DATE -->
                            <div v-else-if="currentStep === 4 && localMode.startsWith('single')" :key="4" class="space-y-[15px] animate-fade-in w-full h-full flex flex-col">
                                <div class="text-center space-y-6">
                                    <h2 class="text-[17px] font-black text-slate-900 leading-relaxed tracking-tight">目前的<span class="text-indigo-600">狀態</span>與<span class="text-red-600">日期</span>？</h2>
                                    <div class="grid grid-cols-1 gap-8 max-w-sm mx-auto mt-6">
                                        <!-- single_multi: per-person cards -->
                                        <template v-if="localMode === 'single_multi'">
                                            <div class="flex items-center border-0 border-b-2 border-slate-100 focus-within:border-indigo-500 transition-all">
                                                <input v-model="tempPersonnelName" type="text" placeholder="輸入法號" list="dharma-names" @keydown.enter.prevent="addPersonnelName"
                                                    class="w-full text-center text-[18px] font-black bg-transparent py-4 outline-none placeholder:text-slate-100 border-none">
                                                <button type="button" @click="addPersonnelName" class="px-4 py-2 bg-slate-100 text-slate-600 font-bold rounded-lg whitespace-nowrap active:scale-95 transition-all text-[15px] ml-2">
                                                    加入
                                                </button>
                                            </div>

                                            <div v-if="addedPersonnel.length > 0" class="mt-6 space-y-4">
                                                <div v-for="(person, idx) in addedPersonnel" :key="idx" class="border border-slate-100 rounded-2xl p-4 bg-slate-50/50">
                                                    <div class="flex justify-between items-center mb-4">
                                                        <span class="font-black text-[17px] text-slate-800">{{ person.name }}</span>
                                                        <button type="button" @click="removePersonnelName(idx)" class="text-red-400 font-bold text-[13px] active:scale-95 transition-all">刪除</button>
                                                    </div>
                                                    <div class="grid grid-cols-1 gap-5">
                                                        <div class="relative">
                                                            <label class="text-[13px] font-black text-slate-300 uppercase tracking-widest">狀態</label>
                                                            <select v-model="person.status" class="w-full text-center text-[17px] font-black border-0 border-b-2 border-slate-100 focus:border-indigo-500 bg-transparent py-3 outline-none transition-all appearance-none mt-1">
                                                                <option value="未求得">未求得</option>
                                                                <option value="已求得">已求得</option>
                                                                <option value="已登記">已登記</option>
                                                            </select>
                                                        </div>
                                                        <div class="relative">
                                                            <label class="text-[13px] font-black text-slate-300 uppercase tracking-widest">日期</label>
                                                            <div class="flex items-center border-0 border-b-2 border-slate-100 focus-within:border-red-400 transition-all mt-1">
                                                                <input v-model="person.obtained_date" type="text" placeholder="YYYY-MM-DD"
                                                                    :disabled="person.status === '未求得'"
                                                                    class="w-full text-center text-[17px] font-black bg-transparent py-3 outline-none transition-all disabled:text-slate-200 border-none">
                                                                <button @click="activePicker = { field: 'obtained_date', personIdx: idx, title: person.name + '日期' }"
                                                                    :disabled="person.status === '未求得'"
                                                                    type="button"
                                                                    class="text-slate-200 hover:text-red-500 transition-colors disabled:opacity-30">
                                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2v12a2 2 0 002 2z" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div v-else class="py-6 text-slate-200 font-black text-[15px] text-center">尚未加入任何法號</div>
                                        </template>
                                        <div class="relative">
                                            <label class="absolute -top-6 left-0 text-[13px] font-black text-slate-300 uppercase tracking-widest">狀態</label>
                                            <select v-model="form.status" class="w-full text-center text-[18px] font-black border-0 border-b-2 border-slate-100 focus:border-indigo-500 bg-transparent py-4 outline-none transition-all appearance-none">
                                                <option value="未求得">未求得</option>
                                                <option value="已求得">已求得</option>
                                                <option value="已登記">已登記</option>
                                            </select>
                                        </div>
                                        <div class="relative">
                                            <label class="absolute -top-6 left-0 text-[13px] font-black text-slate-300 uppercase tracking-widest">{{ form.status === '已登記' ? '登記日期' : (form.status === '已求得' ? '求得日期' : '日期') }}</label>
                                            <input v-model="form.obtained_date" type="text" placeholder="YYYY-MM-DD" 
                                                :disabled="form.status === '未求得'"
                                                class="w-full text-center text-[18px] font-black border-0 border-b-2 border-slate-100 focus:border-red-500 bg-transparent py-4 outline-none transition-all disabled:text-slate-200">
                                            <button @click="activePicker = { field: 'obtained_date', title: '修改日期' }" 
                                                :disabled="form.status === '未求得'"
                                                class="absolute right-0 bottom-4 text-slate-200 hover:text-red-500 transition-colors disabled:opacity-30 disabled:cursor-not-allowed">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2v12a2 2 0 002 2z" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- STEP 5 (Single Only): REMARKS -->
                            <div v-else-if="currentStep === 5 && localMode.startsWith('single')" :key="5" class="space-y-6 animate-fade-in text-center w-full">
                                <h2 class="text-[17px] font-black text-slate-900 leading-relaxed tracking-tight">有其他<span class="text-indigo-600">補充說明</span>嗎？</h2>
                                <p class="text-[13px] font-bold text-slate-400 mt-1">※ 若無備註可直接按下一步跳過</p>
                                <div class="max-w-md mx-auto mt-4">
                                    <textarea v-model="form.remarks" rows="3" placeholder="例如：法寶現存放於..." 
                                        class="w-full text-center text-[18px] font-black border-0 border-b-2 border-slate-100 focus:border-indigo-500 bg-transparent py-4 outline-none transition-all placeholder:text-slate-100 resize-none leading-relaxed"></textarea>
                                </div>
                            </div>

                            <!-- STEP 6 (Single Only): PREVIEW & CONFIRM -->
                            <div v-else-if="currentStep === 6 && localMode.startsWith('single')" :key="6" class="space-y-6 animate-fade-in text-center w-full">
                                <h2 class="text-[17px] font-black text-slate-900 leading-relaxed tracking-tight">預覽<span class="text-indigo-600">載錄資料</span></h2>
                                <div class="max-w-md mx-auto border border-slate-100 rounded-3xl overflow-hidden shadow-sm bg-white text-left">
                                    <div class="p-4 space-y-3">
                                        <div class="flex justify-between border-b border-slate-50 pb-2">
                                            <span class="text-[13px] font-black text-slate-400">法寶名稱</span>
                                            <span class="text-[17px] font-black text-slate-900">{{ form.name || '-' }}</span>
                                        </div>
                                        <div class="flex justify-between border-b border-slate-50 pb-2">
                                            <span class="text-[13px] font-black text-slate-400">法寶用意</span>
                                            <span class="text-[17px] font-bold text-slate-700">{{ form.purpose || '-' }}</span>
                                        </div>
                                        <div class="flex justify-between border-b border-slate-50 pb-2">
                                            <span class="text-[13px] font-black text-slate-400">狀態 / 日期</span>
                                            <div class="text-right">
                                                <span class="text-[17px] font-black mr-2 text-indigo-500">{{ form.status }}</span>
                                                <span class="text-[17px] font-bold text-slate-700">{{ form.obtained_date || '-' }}</span>
                                            </div>
                                        </div>
                                        <div class="flex flex-col border-b border-slate-50 pb-2">
                                            <span class="text-[13px] font-black text-slate-400 mb-1">備註</span>
                                            <span class="text-[17px] font-bold text-slate-700 whitespace-pre-wrap">{{ form.remarks || '-' }}</span>
                                        </div>
                                        <div v-if="localMode === 'single_multi' && addedPersonnel.length > 0" class="flex flex-col">
                                            <span class="text-[13px] font-black text-slate-400 mb-2">承接法號 ({{ addedPersonnel.length }}位)</span>
                                            <div class="space-y-2">
                                                <div v-for="p in addedPersonnel" :key="p.name" class="flex justify-between items-center border-b border-slate-50 pb-1">
                                                    <span class="font-black text-[15px] text-slate-700">{{ p.name }}</span>
                                                    <div class="text-right">
                                                        <span class="text-[12px] font-black text-indigo-500 mr-1">{{ p.status }}</span>
                                                        <span class="text-[13px] text-slate-500">{{ p.obtained_date || '-' }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="bg-indigo-50/50 p-4 rounded-[30px] max-w-sm mx-auto border border-indigo-100 mt-6">
                                    <div class="text-[15px] font-black text-slate-900">請確認資料無誤後點擊下方按鈕</div>
                                </div>
                            </div>
                        </transition>
                    </div>
                </div>
            </div>

            <!-- Footer Action -->
            <div class="absolute bottom-[7dvh] left-0 right-0 md:relative md:bottom-0 px-6 py-[4px] bg-white border-t border-slate-50 z-[10] flex gap-3 justify-center">
                <button v-if="currentStep > 1" @click="handleBack"
                    class="w-[100px] py-4 bg-slate-100 text-slate-400 rounded-2xl font-black text-[18px] active:scale-95 transition-all">
                    上一步
                </button>
                <button v-if="currentStep < totalSteps" @click="handleNext"
                    class="flex-1 py-4 bg-indigo-600 !text-white rounded-2xl font-black text-[18px] shadow-lg shadow-indigo-100 active:scale-95 transition-all"
                    style="color: white !important;">
                    下一步
                </button>
                <button v-else @click="handleSubmit" :disabled="isSaving || (localMode === 'batch' && excelRows.length === 0)"
                    class="flex-1 py-4 bg-indigo-600 !text-white rounded-2xl font-black text-[18px] shadow-lg shadow-indigo-100 active:scale-95 transition-all disabled:bg-slate-300"
                    style="color: white !important;">
                    {{ isSaving ? '處理中...' : (localMode.startsWith('single') ? '確認載錄' : `開始載錄這 ${excelRows.length} 筆資料`) }}
                </button>
            </div>

            <mobile-navbar class="md:hidden" :can-back="false" @home="$emit('cancel')" :show-action="false" :can-search="false" is-absolute />

            <datalist id="dharma-names">
                <option v-for="dn in dharmaNames" :key="dn.id" :value="dn.name" />
            </datalist>
        </div>

        <!-- LOCAL DATE PICKER FOR THE ADD FORM -->
        <compact-date-picker 
            v-if="activePicker"
            v-model="activePickerValue"
            :title="activePicker.title"
            @close="activePicker = null"
        />
    </div>
</template>

<script setup>
import { ref, watch, computed, onMounted, onUnmounted, nextTick } from 'vue';
import axios from 'axios';
import CompactDatePicker from './CompactDatePicker.vue';
import MobileNavbar from './MobileNavbar.vue';
import { lockBodyScroll, unlockBodyScroll } from '../utils/iosCompat';

const props = defineProps({
    mode: String,
    initialData: Object,
    masters: Array,
    isSaving: Boolean
});

const emit = defineEmits(['saveSingle', 'saveBatch', 'cancel', 'close']);

// --- 1. State Declarations (Refs) ---
const localMode = ref(props.mode || 'single');
const currentStep = ref(1);
const personnel = ref([]);
const dharmaNames = ref([]);
const isMulti = ref(true);
const form = ref({ ...props.initialData });
const batchType = ref('single');
const batchInput = ref('');
const activePicker = ref(null);
const activeRelDropdownIdx = ref(null);
const scrollContainer = ref(null);

const tempPersonnelName = ref('');
const addedPersonnel = ref([]);

function addPersonnelName() {
    if (tempPersonnelName.value.trim()) {
        const name = tempPersonnelName.value.trim();
        if (!addedPersonnel.value.find(p => p.name === name)) {
            addedPersonnel.value.push({
                name,
                status: '已求得',
                obtained_date: new Date().toISOString().split('T')[0]
            });
        }
        tempPersonnelName.value = '';
    }
}

function removePersonnelName(idx) {
    addedPersonnel.value.splice(idx, 1);
}
const relationshipOptions = ['母親', '父親', '公公', '婆婆', '爺爺', '奶奶', '外公', '外婆'];

const stepTitles = [
    '日期與仙師',
    '法寶名稱',
    '法寶用意',
    '狀態與日期',
    '補充備註',
    '預覽確認'
];

const batchStepTitles = [
    '日期與仙師',
    '輸入內容',
    '預覽確認'
];

const currentStepTitles = computed(() => {
    return localMode.value.startsWith('single') ? stepTitles : batchStepTitles;
});

const totalSteps = computed(() => {
    return currentStepTitles.value.length;
});

// --- 2. Lifecycle & Global Logic ---
watch(() => props.mode, (newVal) => {
    if (newVal) lockBodyScroll();
    else unlockBodyScroll();
}, { immediate: true });

onUnmounted(() => {
    if (props.mode) unlockBodyScroll();
});

onMounted(() => {
    fetchDharmaNames();
});

// --- 3. Computed Properties ---
const activePickerValue = computed({
    get: () => {
        if (!activePicker.value) return '';
        if (activePicker.value.personIdx !== undefined) {
            return addedPersonnel.value[activePicker.value.personIdx]?.[activePicker.value.field] ?? '';
        }
        if (activePicker.value.idx !== undefined) {
            return personnel.value[activePicker.value.idx][activePicker.value.field];
        }
        return form.value[activePicker.value.field];
    },
    set: (val) => {
        if (!activePicker.value) return;
        if (activePicker.value.personIdx !== undefined) {
            if (addedPersonnel.value[activePicker.value.personIdx]) {
                addedPersonnel.value[activePicker.value.personIdx][activePicker.value.field] = val;
            }
        } else if (activePicker.value.idx !== undefined) {
            personnel.value[activePicker.value.idx][activePicker.value.field] = val;
        } else {
            form.value[activePicker.value.field] = val;
        }
    }
});

const hasPersonnelPattern = computed(() => {
    if (!batchInput.value.trim()) return false;
    return /(已登記|已求得|未求得)/.test(batchInput.value);
});

const excelRows = computed(() => {
    if (!batchInput.value.trim()) return [];

    const lines = batchInput.value.split('\n').map(l => l.trim());
    const records = [];
    let currentRec = null;
    let currentMasterId = form.value.master_id;
    let currentDateInText = null;

    for (let i = 0; i < lines.length; i++) {
        const line = lines[i];
        if (!line) { 
            if (currentRec) { records.push(currentRec); currentRec = null; }
            continue; 
        }

        if (line.startsWith('【') && line.endsWith('】')) {
            const mName = line.replace(/[【】]/g, '');
            const found = props.masters?.find(m => m.name.includes(mName));
            if (found) currentMasterId = found.id;
            continue;
        }

        const isNewItemTrigger = line.startsWith('法寶名稱:') || line.startsWith('法寶名稱：');
        const isPurpose = line.startsWith('用意') || line.startsWith('用法') || line.startsWith('用途');
        const isRemark = line.startsWith('備註:') || line.startsWith('備註：');
        const statusMatch = line.match(/(已登記|已求得|未求得)/);
        const dateMatch = line.match(/\b\d{4}[\/\-]\d{1,2}[\/\-]\d{1,2}\b/) || line.match(/\b\d{1,2}[\/\-]\d{1,2}\b/);
        
        let cleanedLine = line;
        let lineDate = '';
        let lineStatus = '';

        if (dateMatch) {
            lineDate = dateMatch[0].replace(/\//g, '-');
            cleanedLine = cleanedLine.replace(dateMatch[0], '').trim();
        }
        if (statusMatch) {
            lineStatus = statusMatch[0];
            cleanedLine = cleanedLine.replace(statusMatch[0], '').replace(/狀態[:：]?/, '').trim();
        }

        let remarkFromParens = '';
        if (cleanedLine && cleanedLine.includes('(') && cleanedLine.endsWith(')')) {
            const match = cleanedLine.match(/^(.*?)\((.*?)\)$/);
            if (match) {
                cleanedLine = match[1].trim();
                remarkFromParens = match[2].trim();
            }
        }

        if (isNewItemTrigger) {
            if (currentRec) records.push(currentRec);
            let name = line.replace(/^法寶名稱[:：]\s*/, '').trim();
            currentRec = { name: name, purpose: '-', personnel: [], remarks: [], status: '已登記', master_id: currentMasterId, date: currentDateInText || '' };
        } else if (isPurpose) {
            if (!currentRec) currentRec = { name: '未命名', purpose: '-', personnel: [], remarks: [], status: '已登記', master_id: currentMasterId, date: currentDateInText || '' };
            currentRec.purpose = line.replace(/.*(用意|用法|用途)[:：]?\s*/, '').trim() || '-';
        } else if (isRemark) {
            if (!currentRec) currentRec = { name: '未命名', purpose: '-', personnel: [], remarks: [], status: '已登記', master_id: currentMasterId, date: currentDateInText || '' };
            currentRec.remarks.push(line.replace(/^備註[:：]\s*/, '').trim());
        } else {
            if (localMode.value === 'batch_personal') {
                if (currentRec) records.push(currentRec);
                let initialRemarks = remarkFromParens ? [remarkFromParens] : [];
                currentRec = { name: cleanedLine || line, purpose: '-', personnel: [], remarks: initialRemarks, status: lineStatus || '已登記', master_id: currentMasterId, date: lineDate || currentDateInText || '' };
            } else {
                if (!currentRec) {
                    let initialRemarks = remarkFromParens ? [remarkFromParens] : [];
                    currentRec = { name: cleanedLine || line, purpose: '-', personnel: [], remarks: initialRemarks, status: lineStatus || '已登記', master_id: currentMasterId, date: lineDate || currentDateInText || '' };
                } else if (!currentRec.name || currentRec.name === '未命名') {
                    currentRec.name = cleanedLine || line;
                    if (lineDate) currentRec.date = lineDate;
                    if (lineStatus) currentRec.status = lineStatus;
                    if (remarkFromParens) currentRec.remarks.push(remarkFromParens);
                } else {
                    if (!cleanedLine && lineDate) {
                        if (currentRec.personnel.length > 0) {
                            currentRec.personnel[currentRec.personnel.length - 1].obtained_date = lineDate;
                            if (lineStatus) currentRec.personnel[currentRec.personnel.length - 1].status = lineStatus;
                        } else {
                            currentRec.date = lineDate;
                            currentDateInText = lineDate;
                            if (lineStatus) currentRec.status = lineStatus;
                        }
                    } else if (cleanedLine) {
                        if (cleanedLine === '未求得' || cleanedLine.includes('以上沒有資料')) {
                            if (cleanedLine === '未求得') currentRec.status = '未求得';
                            if (!cleanedLine.includes('以上沒有資料')) currentRec.remarks.push(line);
                        } else {
                            currentRec.personnel.push({ 
                                custom_name: cleanedLine, 
                                status: lineStatus || '已求得', 
                                obtained_date: lineDate, 
                                remarks: remarkFromParens 
                            });
                        }
                    }
                }
            }
        }
    }
    if (currentRec) records.push(currentRec);

    return records.map(r => {
        let finalStatus = r.status;
        if (r.personnel.length > 0) {
            const hasUnobtained = r.personnel.some(p => p.status === '未求得');
            const allRegistered = r.personnel.every(p => p.status === '已登記');
            if (hasUnobtained) finalStatus = '未求得';
            else if (allRegistered) finalStatus = '已登記';
            else finalStatus = '已求得';
        }

        return {
            c0: r.name,
            c1: r.purpose,
            _dharma_name_registries: r.personnel,
            _manualRemarks: r.remarks.join('\n'),
            _is_multi: r.personnel.length > 1,
            _status: finalStatus,
            _record_date: r.date,
            _master_id: r.master_id
        };
    });
});

// --- 4. Watchers ---
watch(localMode, () => {
    currentStep.value = 1;
});

watch(currentStep, () => {
    if (scrollContainer.value) {
        scrollContainer.value.scrollTop = 0;
    }
});

watch(() => props.initialData, (newVal) => {
    form.value = { ...newVal };
    if (newVal.dharma_name_registries && newVal.dharma_name_registries.length > 0) {
        form.value.owner_dharma_name = newVal.dharma_name_registries[0].dharma_name?.name || newVal.dharma_name_registries[0].custom_name || '';
    }
}, { immediate: true });

// Combined Status & Date Logic
watch(() => form.value.status, (newStatus) => {
    if (newStatus === '未求得') {
        form.value.obtained_date = '';
    } else if ((newStatus === '已求得' || newStatus === '已登記') && !form.value.obtained_date) {
        form.value.obtained_date = new Date().toISOString().split('T')[0];
    }
});

watch(personnel, (newVal) => {
    newVal.forEach(p => {
        if (p.status === '未求得') {
            p.obtained_date = '';
        } else if ((p.status === '已求得' || p.status === '已登記') && !p.obtained_date) {
            p.obtained_date = new Date().toISOString().split('T')[0];
        }

        // Relationship Rule: "Name之Relative" split
        if (p.custom_name && p.custom_name.trim()) {
            const relSplitMatch = p.custom_name.match(/^(.*?)([之的])(.+)$/);
            if (relSplitMatch) {
                const namePart = relSplitMatch[1].trim();
                let connector = relSplitMatch[2];
                let relPart = relSplitMatch[3].trim();
                p.custom_name = namePart;
                p.relationship = connector + relPart;
            }
        }
    });
}, { deep: true });

function handleBack() {
    if (currentStep.value > 1) {
        currentStep.value--;
    }
}

async function fetchDharmaNames() {
    try {
        const res = await axios.get('/api/dharma-names-list');
        dharmaNames.value = res.data;
    } catch (e) {}
}

function validateStep(step) {
    if (localMode.value.startsWith('single')) {
        if (step === 2 && !form.value.name?.trim()) {
            alert('請輸入法寶名稱');
            return false;
        }
    } else {
        if (step === 2 && !batchInput.value.trim()) {
            alert('請貼入載錄內容');
            return false;
        }
    }

    if (step === 4 && localMode.value.startsWith('single')) {
        if (form.value.status === '未求得' && form.value.obtained_date) {
            alert('狀態為「未求得」時不可輸入日期');
            form.value.obtained_date = '';
            return false;
        }
        if ((form.value.status === '已求得' || form.value.status === '已登記') && !form.value.obtained_date) {
            alert(`狀態為「${form.value.status}」時必須輸入日期`);
            return false;
        }
    }
    return true;
}

function handleNext() {
    if (validateStep(currentStep.value)) {
        currentStep.value++;
    }
}



function handleSubmit() {
    if (localMode.value.startsWith('single')) {
        if (!form.value.name?.trim()) { alert('請輸入法寶名稱'); return; }

        let registries = [];
        
        const allExplicitNames = [...addedPersonnel.value];
        if (tempPersonnelName.value?.trim() && !allExplicitNames.includes(tempPersonnelName.value.trim())) {
            allExplicitNames.push(tempPersonnelName.value.trim());
        }
        
        allExplicitNames.forEach(p => {
            registries.push({
                custom_name: p.name ?? p,
                status: p.status ?? form.value.status,
                obtained_date: p.obtained_date ?? form.value.obtained_date,
                remarks: '',
                related_personnel: []
            });
        });
        
        let finalRemarks = form.value.remarks?.trim() ? [form.value.remarks.trim()] : [];

        
        emit('saveSingle', { 
            ...form.value,
            status: form.value.status,
            remarks: finalRemarks.join('\n'),
            is_multi: registries.length > 1,
            dharma_name_registries: registries
        });
    } else {
        emit('saveBatch', { 
            input: batchInput.value, 
            masterId: form.value.master_id,
            rows: excelRows.value.map(row => ({
                name: row.c0, 
                purpose: row.c1, 
                master_id: row._master_id || form.value.master_id,
                record_date: row._record_date || '', 
                obtained_date: row._record_date || '',
                status: row._status || '已登記', 
                count: 1, 
                remarks: row._manualRemarks || '',
                is_multi: row._is_multi || false,
                dharma_name_registries: (row._dharma_name_registries || []).map(p => ({
                    ...p,
                    obtained_date: p.obtained_date || row._record_date || ''
                }))
            }))
        });
    }
}

function getMasterName(id) {
    const m = props.masters?.find(m => String(m.id) === String(id));
    return m ? (m.name === '父皇仙師' ? '父皇' : m.name) : '預設';
}
</script>

<style scoped>
.animate-slide-up { animation: slideUp 0.4s cubic-bezier(0.16, 1, 0.3, 1); }
.animate-fade-in { animation: fadeIn 0.3s ease-out; }
@keyframes slideUp { from { transform: translateY(100%); } to { transform: translateY(0); } }
@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
.no-scrollbar::-webkit-scrollbar { display: none; }

/* Step Transitions */
.step-fade-enter-active,
.step-fade-leave-active {
    transition: all 0.3s ease-in-out;
}
.step-fade-enter-from {
    opacity: 0;
    transform: translateX(20px);
}
.step-fade-leave-to {
    opacity: 0;
    transform: translateX(-20px);
}
</style>
