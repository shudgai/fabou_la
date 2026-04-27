<template>
    <div v-if="mode" class="fixed inset-0 z-[2000] flex items-end md:items-center justify-center px-0">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm" @click="$emit('cancel')"></div>
        
        <!-- Form Container -->
        <div class="relative w-full h-full md:h-auto md:max-h-[95vh] md:max-w-md bg-white md:rounded-[32px] shadow-[0_-10px_40px_rgba(0,0,0,0.1)] overflow-hidden animate-slide-up flex flex-col pb-[7vh]">
            
            <!-- Header -->
            <div class="px-[10px] py-[12px] flex items-center bg-white border-b border-slate-50 relative">
                <div class="flex-1 flex flex-col justify-center min-w-0">
                    <div class="text-[25px] font-bold leading-none font-outfit uppercase tracking-wider text-slate-900">怨靈載錄專區</div>
                    <div class="text-[22px] font-bold mt-2 truncate font-outfit text-slate-900">
                        {{ (form.category === 'major' ? '重大皇恩登記簿' : '其他皇恩登記簿') }} - {{ selectedMasterName || '請選擇仙師' }}
                    </div>
                </div>
                <button @click="$emit('cancel')" class="text-slate-300 hover:text-slate-600 transition-colors p-2 absolute right-4 top-1/2 -translate-y-1/2">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
            </div>

            <!-- Tab Selection -->
            <div class="px-6 pt-4 flex space-x-1">
                <button @click="localMode = 'single'" :class="[localMode === 'single' ? 'bg-blue-600 text-white' : 'text-slate-400 hover:bg-slate-50']" class="flex-1 py-[10px] rounded-xl text-[16px] font-bold transition-all whitespace-nowrap">逐筆登錄</button>
                <button @click="localMode = 'batch'" :class="[localMode === 'batch' ? 'bg-blue-600 text-white' : 'text-slate-400 hover:bg-slate-50']" class="flex-1 py-[10px] rounded-xl text-[16px] font-bold transition-all whitespace-nowrap">文字/EXCEL 記載</button>
            </div>

            <!-- Scrollable Content -->
            <div class="flex-1 min-h-0 overflow-y-auto px-[10px] py-[20px] space-y-[20px] custom-scrollbar bg-white">
                
                <!-- Date & Master Selection (Grid Layout as per Screenshot) -->
                <div class="grid gap-4" :class="localMode === 'single' ? 'grid-cols-2' : 'grid-cols-1'">
                    <div v-if="localMode === 'single'" class="space-y-1.5">
                        <label class="text-[17px] font-bold text-slate-800 block ml-1">得知日期</label>
                        <div class="relative flex items-center">
                            <input v-model="form.record_date" type="text" placeholder="年/月/日 或 註記文字" 
                                class="w-full py-[10px] rounded-2xl border border-slate-400 bg-white pl-3 pr-8 focus:ring-2 focus:ring-indigo-100 outline-none text-[15px] font-bold text-slate-900 shadow-sm">
                            <button @click="activePicker = { idx: 'main', field: 'record_date', title: '選擇日期' }" class="absolute right-3 text-slate-400 hover:text-indigo-600 transition-all p-1">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </button>
                        </div>
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-[17px] font-bold text-slate-800 block ml-1">載錄目標仙師</label>
                        <select v-model="form.master_id" class="w-full py-[10px] rounded-2xl border border-slate-400 bg-white px-4 text-[16px] font-bold text-slate-900 focus:ring-2 focus:ring-indigo-100 outline-none">
                            <option v-for="m in masters" :key="m.id" :value="m.id">{{ m.name }}</option>
                        </select>
                    </div>
                </div>

                <!-- Treasure Names (Now below Master in both modes) -->
                <div class="space-y-2">
                    <div class="flex items-center justify-between ml-1">
                        <label class="text-[15px] font-bold text-red-600 uppercase tracking-wider">法寶名稱</label>
                        <button @click="addTreasureNameRow" class="text-[12px] font-bold text-indigo-500 hover:text-indigo-600 active:scale-95 transition-all">
                            ＋ 增行
                        </button>
                    </div>
                    <div v-for="(tn, tnIdx) in treasureNames" :key="tnIdx" class="flex items-center space-x-2 animate-fade-in mb-2">
                        <input v-model="treasureNames[tnIdx]" type="text" placeholder="輸入法寶名稱" 
                            class="flex-1 py-[10px] rounded-2xl border border-slate-400 bg-white px-4 text-[17px] font-bold text-slate-900 focus:ring-2 focus:ring-indigo-100 outline-none">
                        <button v-if="treasureNames.length > 1" @click="removeTreasureNameRow(tnIdx)" class="w-10 h-10 flex items-center justify-center text-slate-300 hover:text-red-500 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </button>
                    </div>
                </div>

                <!-- Single Mode Fields -->
                <div v-if="localMode === 'single'" class="space-y-5 animate-fade-in">
                    <!-- Purpose & Method Grid -->
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="text-[15px] font-bold text-slate-500 block ml-1 uppercase tracking-wider">法寶用意</label>
                            <input v-model="form.purpose" type="text" placeholder="預設用意" class="w-full py-[10px] rounded-2xl border border-slate-400 bg-white px-4 text-[17px] font-bold text-red-600 focus:ring-2 focus:ring-indigo-100 outline-none">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[15px] font-bold text-slate-500 block ml-1 uppercase tracking-wider">求寶方式</label>
                            <input v-model="form.acquisition_method" type="text" placeholder="預設方式" class="w-full py-[10px] rounded-2xl border border-slate-400 bg-white px-4 text-[17px] font-bold text-red-600 focus:ring-2 focus:ring-indigo-100 outline-none">
                        </div>
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-[15px] font-bold text-slate-500 block ml-1 uppercase tracking-wider">備註 (整體)</label>
                        <input v-model="form.remarks" type="text" placeholder="輸入備註 (選填)" class="w-full py-[10px] rounded-2xl border border-slate-400 bg-white px-4 text-[17px] font-bold text-slate-900 focus:ring-2 focus:ring-indigo-100 outline-none">
                    </div>
                </div>

                <!-- Personnel List (Single Mode) -->
                <div v-if="localMode === 'single'" class="space-y-4">
                        <div class="flex items-center justify-between ml-1">
                            <label class="text-[15px] font-bold text-red-600 uppercase tracking-tight">承接皇恩的師兄姐</label>
                            <button @click="addPersonnelRow" class="text-[12px] font-bold text-indigo-500 hover:text-indigo-600 active:scale-95 transition-all">
                                ＋ 新增人員
                            </button>
                        </div>

                        <div v-for="(p, idx) in personnel" :key="idx" class="p-4 bg-slate-50/30 rounded-2xl border border-slate-400 space-y-3 relative group animate-fade-in">
                            <div class="absolute top-2 right-2 flex items-center space-x-1">
                                <!-- Reorder Buttons -->
                                <button v-if="personnel.length > 1" @click="movePersonnel(idx, -1)" :disabled="idx === 0" class="p-1 text-slate-300 hover:text-indigo-500 disabled:opacity-10 transition-all active:scale-90">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 15l7-7 7 7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </button>
                                <button v-if="personnel.length > 1" @click="movePersonnel(idx, 1)" :disabled="idx === personnel.length - 1" class="p-1 text-slate-300 hover:text-indigo-500 disabled:opacity-10 transition-all active:scale-90">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </button>
                                <button @click="removePersonnelRow(idx)" class="ml-1 text-slate-300 hover:text-red-500 transition-colors p-1">✕</button>
                            </div>
                            
                            <div class="grid grid-cols-3 gap-2">
                                <div class="space-y-1">
                                    <label class="text-[11px] text-red-400 ml-1 font-bold">法號</label>
                                    <input v-model="p.custom_name" type="text" placeholder="法號" list="dharma-names"
                                        @keydown.enter.prevent="handlePersonnelEnter(idx)"
                                        @input="e => handlePersonnelNameInput(idx, e)"
                                        class="personnel-name-input w-full py-[10px] rounded-xl border border-slate-400 bg-white px-3 text-[18px] font-bold text-slate-900 focus:ring-2 focus:ring-indigo-100 outline-none font-outfit">
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[11px] text-red-400 ml-1 font-bold">親友</label>
                                    <input v-model="p.relationship" type="text" placeholder="親友"
                                        class="w-full py-[10px] rounded-xl border border-slate-400 bg-white px-3 text-[16px] font-normal text-slate-900 focus:ring-2 focus:ring-indigo-100 outline-none">
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[11px] text-red-400 ml-1 font-bold">日期</label>
                                    <div class="relative w-full py-[10px] rounded-xl border border-slate-400 bg-white flex items-center overflow-hidden group/date">
                                        <input 
                                            :value="p.obtained_date ? p.obtained_date.replace(/-/g, '/') : ''"
                                            @input="e => handlePersonnelDateInput(idx, e)"
                                            placeholder="年/月/日"
                                            class="personnel-date-input w-full h-full bg-transparent px-2 text-[14px] font-bold font-outfit text-slate-900 outline-none uppercase"
                                        >
                                        <button @click="activePicker = { idx, field: 'obtained_date', title: (p.custom_name || '師兄姐') + '取得日期' }" 
                                            class="absolute right-0 top-0 h-full px-2 text-slate-300 hover:text-indigo-500 transition-colors bg-white/80 backdrop-blur-sm">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="space-y-1">
                                <label class="text-[11px] text-slate-400 ml-1 font-bold">個人備註</label>
                                <div @click="$emit('openRemarksEdit', { idx, remarks: p.remarks })"
                                    class="w-full min-py-[10px] rounded-xl border border-slate-400 bg-white px-3 py-[10px] text-[15px] font-normal text-slate-900 cursor-pointer hover:border-indigo-300 transition-colors flex items-center justify-between group/rem">
                                    <span :class="p.remarks ? 'text-slate-900' : 'text-slate-300'" class="truncate flex-1">
                                        {{ p.remarks || '點此輸入詳細備註...' }}
                                    </span>
                                    <svg class="w-4 h-4 text-slate-200 group-hover/rem:text-indigo-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </div>
                            </div>
                        </div>

                        <div v-if="personnel.length === 0" class="text-center py-6 bg-slate-50/30 rounded-2xl border border-dashed border-slate-400 text-slate-300 text-[13px]">
                            尚無人員紀錄
                        </div>
                    </div>

                <!-- Batch Mode Fields (Matching Mockup) -->
                <div v-if="localMode === 'batch'" class="space-y-4 animate-fade-in">
                    <div class="flex items-center justify-between ml-1">
                        <label class="text-[17px] font-bold text-slate-800">貼入清單內容</label>
                        <div class="flex items-center space-x-2">
                            <button @click="$refs.fileInput.click()" class="text-[15px] font-bold text-slate-800 flex items-center">
                                匯入檔案 (Excel/Word)
                                <svg class="w-5 h-5 ml-1 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </button>
                            <input type="file" ref="fileInput" @change="handleFileImport" accept=".xlsx, .xls" class="hidden">
                        </div>
                    </div>
                    <textarea v-model="batchInput" rows="10" 
                        class="w-full rounded-2xl border border-slate-300 text-[16px] font-normal text-slate-900 bg-white focus:ring-2 focus:ring-indigo-100 p-4" 
                        placeholder="支援直接貼上或輸入 Excel 或 LINE 內容... 第一行若是日期將自動作為登記日期！"></textarea>
                    
                    <!-- Rules Reference Section -->
                    <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100/50 space-y-3 mt-4">
                        <h4 class="text-[12px] font-bold text-indigo-600 uppercase tracking-widest flex items-center">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            行政規則說明
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-2 text-[14px]">
                            <div class="flex items-start text-slate-600">
                                <span class="text-indigo-400 font-bold mr-2">1.</span>
                                <span><strong>法號翻譯：</strong>金開頭自動換為靈/龍/元字輩。</span>
                            </div>
                            <div class="flex items-start text-slate-600">
                                <span class="text-indigo-400 font-bold mr-2">2.</span>
                                <span><strong>關係識別：</strong>「元續之母」自動拆解為元續+備註。</span>
                            </div>
                            <div class="flex items-start text-slate-600">
                                <span class="text-indigo-400 font-bold mr-2">3.</span>
                                <span><strong>日期與求寶：</strong>支援獨立年份、短日期與「求寶：」識別。</span>
                            </div>
                            <div class="flex items-start text-slate-600">
                                <span class="text-indigo-400 font-bold mr-2">4.</span>
                                <span><strong>跨日合併：</strong>同紀錄自動合併人員，不重複建立。</span>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Dharma Names Auto-complete List -->
                <datalist id="dharma-names">
                    <option v-for="dn in dharmaNames" :key="dn.id" :value="dn.name" />
                </datalist>
            </div>

            <!-- Footer Action -->
            <div class="p-4 bg-white border-t border-slate-50">
                <button v-if="localMode === 'single'" @click="handleSubmit" :disabled="isSaving" class="w-full bg-blue-600 text-white rounded-2xl font-bold text-[20px] py-[10px]">
                    {{ isSaving ? '儲存中...' : '儲存' }}
                </button>
                <button v-else @click="handleSubmit" :disabled="isSaving" 
                    class="w-full bg-blue-600 text-white rounded-2xl font-bold text-[20px] py-[10px] transition-all disabled:opacity-50">
                    {{ isSaving ? '儲存中...' : '儲存' }}
                </button>
            </div>

            <!-- Global Mobile Navbar -->
            <mobile-navbar 
                :can-back="false"
                @home="$emit('cancel')"
                :show-action="false"
                :can-search="false"
            />
        </div>

        <compact-date-picker 
            v-if="activePicker && activePicker.idx === 'main'"
            v-model="form[activePicker.field]"
            :title="activePicker.title"
            @close="activePicker = null"
        />
        <compact-date-picker 
            v-if="activePicker && activePicker.idx !== 'main'"
            v-model="personnel[activePicker.idx][activePicker.field]"
            :title="activePicker.title"
            @close="activePicker = null"
        />
    </div>
</template>

<script setup>
import { ref, watch, computed, onMounted } from 'vue';
import axios from 'axios';
import MobileNavbar from './MobileNavbar.vue';

const props = defineProps({
    mode: String,
    initialData: Object,
    masters: Array,
    isSaving: Boolean
});

const emit = defineEmits(['saveSingle', 'saveBatch', 'cancel', 'openRemarksEdit']);

import CompactDatePicker from './CompactDatePicker.vue';

const localMode = ref(props.mode || 'single');
const form = ref({ ...props.initialData });
const treasureNames = ref(['']);
const batchInput = ref('');
const personnel = ref([]);
const dharmaNames = ref([]);
const activePicker = ref(null); // { idx: number | 'main', field: string, title: string }
const fileInput = ref(null);

const parsedItemsCount = computed(() => {
    if (!batchInput.value.trim()) return 0;
    const lines = batchInput.value.split('\n').filter(l => l.trim());
    let count = 0;
    let currentTreasure = null;
    const cleanedTreasureNames = treasureNames.value.filter(n => n.trim() !== '');

    lines.forEach(line => {
        const kwMatch = line.match(/(.*)[：:](.*)/);
        if (kwMatch) {
            const kw = kwMatch[1].trim();
            if (kw === '求寶' || kw === '求寶方式' || kw === '用意' || kw === '備註') return;
            // It's a new treasure
            count++;
            currentTreasure = kw;
        } else if (cleanedTreasureNames.length > 0) {
            // Recipient for default treasures
            count += cleanedTreasureNames.length;
        } else if (line.length < 80 && !line.includes('、')) {
            // Pure treasure name
            count++;
        }
    });
    return count;
});

const addTreasureNameRow = () => {
    treasureNames.value.push('');
};

const removeTreasureNameRow = (idx) => {
    treasureNames.value.splice(idx, 1);
};

const fetchDharmaNames = async () => {
    try {
        const res = await axios.get('/api/dharma-names-list');
        dharmaNames.value = res.data;
    } catch (e) {
        console.error('Failed to load dharma names', e);
    }
};

const addPersonnelRow = () => {
    personnel.value.push({
        custom_name: '',
        relationship: '',
        obtained_date: '',
        remarks: ''
    });
};

onMounted(() => {
    fetchDharmaNames();
    if (personnel.value.length === 0) addPersonnelRow();
});

const removePersonnelRow = (idx) => {
    personnel.value.splice(idx, 1);
};

const movePersonnel = (idx, direction) => {
    const targetIdx = idx + direction;
    if (targetIdx < 0 || targetIdx >= personnel.value.length) return;
    const item = personnel.value[idx];
    personnel.value.splice(idx, 1);
    personnel.value.splice(targetIdx, 0, item);
};

const selectedMasterName = computed(() => {
    const m = props.masters?.find(m => String(m.id) === String(form.value.master_id));
    return m ? m.name : '';
});

watch(() => props.initialData, (newVal) => {
    form.value = { ...newVal };
    treasureNames.value = newVal.name ? [newVal.name] : [''];
    if (newVal.dharma_name_registries) {
        personnel.value = newVal.dharma_name_registries.map(r => ({
            dharma_name_id: r.dharma_name_id,
            custom_name: r.dharma_name?.name || r.custom_name || '',
            relationship: Array.isArray(r.related_personnel) ? r.related_personnel.join('、') : (r.related_personnel || ''),
            obtained_date: r.obtained_date || '',
            remarks: Array.isArray(r.remarks) ? r.remarks.join('\n') : (r.remarks || '')
        }));
    }
}, { deep: true, immediate: true });

watch(() => props.mode, (newVal) => {
    if (newVal) localMode.value = newVal;
});

// Intelligent Date Auto-conversion and Auto-newline for Batch Mode
watch(batchInput, (newVal, oldVal) => {
    if (newVal.length <= oldVal.length) return;
    
    // Pattern: 2-3 digit ROC year followed by date separators
    // Removed $ anchor to handle text following date
    const rocRegex = /(\b\d{2,3})([/.-])(\d{1,2})\2(\d{1,2})(?!\d)/;
    const ceRegex = /(\b\d{4})([/.-])(\d{1,2})\2(\d{1,2})(?!\d)/;
    
    let match = newVal.match(rocRegex);
    if (match) {
        const fullMatch = match[0];
        const y = parseInt(match[1]) + 1911;
        const s = match[2];
        const m = match[3].padStart(2, '0');
        const d = match[4].padStart(2, '0');
        // Only replace if it hasn't been converted yet
        if (fullMatch.startsWith(match[1])) {
            batchInput.value = newVal.replace(fullMatch, `${y}${s}${m}${s}${d}\n`);
        }
    } else {
        const ceMatch = newVal.match(ceRegex);
        if (ceMatch) {
            const fullMatch = ceMatch[0];
            const y = ceMatch[1];
            const s = ceMatch[2];
            const m = ceMatch[3].padStart(2, '0');
            const d = ceMatch[4].padStart(2, '0');
            // If it's CE, just ensure formatting and add newline
            if (ceMatch[3].length === 1 || ceMatch[4].length === 1 || !newVal.includes(fullMatch + '\n')) {
                batchInput.value = newVal.replace(fullMatch, `${y}${s}${m}${s}${d}\n`);
            }
        }
    }
});

import { nextTick } from 'vue';
const handlePersonnelEnter = (idx) => {
    if (idx === personnel.value.length - 1) {
        addPersonnelRow();
    }
    nextTick(() => {
        const inputs = document.querySelectorAll('.personnel-name-input');
        if (inputs[idx + 1]) inputs[idx + 1].focus();
    });
};

const handlePersonnelNameInput = (idx, event) => {
    const val = event.target.value;
    const splitters = /[，、, \s]+/;
    
    // Check for multi-name input (e.g., "A B C" or "A,B,C")
    if (splitters.test(val)) {
        const names = val.split(splitters).filter(n => n.trim());
        if (names.length > 1) {
            // Keep the first name in current row, move others to new rows
            personnel.value[idx].custom_name = names[0];
            
            // Insert new rows after current index
            const newRows = names.slice(1).map(n => ({
                custom_name: n,
                relationship: personnel.value[idx].relationship,
                obtained_date: personnel.value[idx].obtained_date,
                remarks: ''
            }));
            
            personnel.value.splice(idx + 1, 0, ...newRows);
            
            // Move focus to the end of the newly added rows
            nextTick(() => {
                const inputs = document.querySelectorAll('.personnel-name-input');
                const lastIdx = idx + names.length - 1;
                if (inputs[lastIdx]) inputs[lastIdx].focus();
            });
        }
    }
};

const handlePersonnelDateInput = (idx, event) => {
    let val = event.target.value.trim();
    if (!val) return;
    
    const rocRegex = /^(\d{2,3})([/.-])(\d{1,2})\2(\d{1,2})$/;
    const ceRegex = /^(\d{4})([/.-])(\d{1,2})\2(\d{1,2})$/;
    
    let isComplete = false;
    let match = val.match(rocRegex);
    if (match) {
        const y = parseInt(match[1]) + 1911;
        const m = match[3].padStart(2, '0');
        const d = match[4].padStart(2, '0');
        personnel.value[idx].obtained_date = `${y}-${m}-${d}`;
        isComplete = true;
    } else {
        match = val.match(ceRegex);
        if (match) {
            const y = match[1];
            const m = match[3].padStart(2, '0');
            const d = match[4].padStart(2, '0');
            personnel.value[idx].obtained_date = `${y}-${m}-${d}`;
            isComplete = true;
        }
    }
    
    if (isComplete) {
        // Move focus to next line (next person's name field)
        if (idx === personnel.value.length - 1) {
            addPersonnelRow();
        }
        nextTick(() => {
            const nextRowNameInput = document.querySelectorAll('.personnel-name-input')[idx + 1];
            if (nextRowNameInput) nextRowNameInput.focus();
        });
    }
};


const handleFileImport = (event) => {
    const file = event.target.files[0];
    if (!file) return;

    if (typeof XLSX === 'undefined') {
        const script = document.createElement('script');
        script.src = "https://cdn.sheetjs.com/xlsx-latest/package/dist/xlsx.full.min.js";
        script.onload = () => processExcelFile(file);
        document.head.appendChild(script);
    } else {
        processExcelFile(file);
    }
};

const processExcelFile = (file) => {
    const reader = new FileReader();
    reader.onload = (e) => {
        const data = new Uint8Array(e.target.result);
        const workbook = XLSX.read(data, { type: 'array' });
        const firstSheetName = workbook.SheetNames[0];
        const worksheet = workbook.Sheets[firstSheetName];
        const jsonData = XLSX.utils.sheet_to_json(worksheet, { header: 1 });
        
        let newText = "";
        jsonData.forEach(row => {
            if (row.length === 0) return;
            const line = row.filter(cell => cell !== null && cell !== undefined).join(' ');
            if (line.trim()) newText += line + "\n";
        });

        if (newText) batchInput.value += (batchInput.value ? "\n" : "") + newText;
    };
    reader.readAsArrayBuffer(file);
};

const handleSubmit = () => {
    if (!form.value.master_id) {
        alert('請先選擇仙師');
        return;
    }

    // Clean up personnel: remove empty rows
    const cleanedPersonnel = personnel.value.filter(p => p.custom_name && p.custom_name.trim() !== '');
    const cleanedTreasureNames = treasureNames.value.filter(n => n.trim() !== '');

    if (localMode.value === 'single') {
        if (cleanedTreasureNames.length === 0) {
            alert('請至少輸入一個法寶名稱');
            return;
        }
        
        // Handle multiple treasure names by emitting multiple save events
        cleanedTreasureNames.forEach(tn => {
            let finalName = tn.trim();
            let finalMethod = form.value.acquisition_method;
            
            // Smart extraction: "Treasure (求寶：Method)" or "Treasure 求寶:Method"
            const methodMatch = finalName.match(/(.*)\s*(求寶|求寶方式)[：:](.*)/);
            if (methodMatch) {
                finalName = methodMatch[1].replace(/[()（）]/g, '').trim();
                finalMethod = methodMatch[3].replace(/[()（）]/g, '').trim();
            }

            const payload = { 
                ...form.value, 
                name: finalName,
                acquisition_method: finalMethod,
                dharma_name_registries: cleanedPersonnel.map(p => ({
                    ...p,
                    related_personnel: p.relationship ? p.relationship.split(/[、, ]+/).filter(x => x) : []
                }))
            };
            emit('saveSingle', payload);
        });
    } else {
        if (!batchInput.value.trim()) {
            alert('請貼上或輸入資料內容');
            return;
        }
        emit('saveBatch', { 
            input: batchInput.value, 
            masterId: form.value.master_id,
            date: form.value.record_date,
            defaults: {
                names: cleanedTreasureNames, // Support multiple default names
                purpose: form.value.purpose,
                acquisition_method: form.value.acquisition_method,
                remarks: form.value.remarks
            }
        });
    }
};

const updatePersonnelRemarks = (idx, content) => {
    if (personnel.value[idx]) {
        personnel.value[idx].remarks = content;
    }
};

defineExpose({ updatePersonnelRemarks });
</script>

<style scoped>
.animate-slide-up { animation: slideUp 0.4s cubic-bezier(0.16, 1, 0.3, 1); }
.animate-fade-in { animation: fadeIn 0.3s ease-out; }
@keyframes slideUp { from { transform: translateY(100%); } to { transform: translateY(0); } }
@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
</style>
