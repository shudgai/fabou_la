<template>
    <div v-if="mode" class="fixed inset-0 z-[2000] flex items-end md:items-center justify-center px-0 imperial-grace-module">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm" @click="$emit('close')"></div>
        
        <!-- Form Container -->
        <div class="relative w-full h-[100vh] md:max-w-xl bg-white shadow-[0_-10px_40px_rgba(0,0,0,0.1)] overflow-hidden animate-slide-up flex flex-col pb-[7vh]">
            <!-- Header -->
            <div class="px-[10px] py-[12px] flex items-center bg-white border-b border-slate-50 relative">
                <div class="flex-1 flex flex-col justify-center min-w-0 py-[5px] pr-6">
                    <div class="text-[25px] font-bold leading-tight font-outfit tracking-widest break-words text-slate-900">
                        重大皇恩專區
                    </div>
                </div>
                <button @click="$emit('close')" class="text-slate-300 hover:text-slate-600 transition-colors py-[5px] absolute right-4 top-1/2 -translate-y-1/2">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
            </div>

            <div class="px-4 py-2 bg-slate-50 flex space-x-1 border-b border-slate-100">
                <button @click="localMode = 'single'" 
                    :class="localMode === 'single' ? 'bg-indigo-600 text-white shadow-md' : 'bg-white text-slate-400 border border-slate-200'"
                    class="flex-1 py-[8px] text-[16px] font-black rounded-xl transition-all whitespace-nowrap"
                    :style="localMode === 'single' ? 'color: white !important;' : ''">
                    逐筆登錄
                </button>
                <button @click="localMode = 'batch'" 
                    :class="localMode === 'batch' ? 'bg-indigo-600 text-white shadow-md' : 'bg-white text-slate-400 border border-slate-200'"
                    class="flex-1 py-[8px] text-[16px] font-black rounded-xl transition-all whitespace-nowrap"
                    :style="localMode === 'batch' ? 'color: white !important;' : ''">
                    貼入法寶名稱明細
                </button>
            </div>

            <!-- Scrollable Content -->
            <div class="flex-1 overflow-y-auto px-[10px] pt-4 pb-32 space-y-4 custom-scrollbar">
                
                <!-- COMMON FIELDS (Date & Master) -->
                <div class="grid grid-cols-2 gap-[2.5px] bg-white p-[5px] mt-[-10px] py-[5px]">
                    <div class="space-y-1">
                        <label class="text-[15px] font-black text-slate-400 uppercase tracking-widest block ml-1">得知日期</label>
                        <div class="relative flex items-center">
                            <input v-model="form.record_date" type="text" placeholder="年/月/日 或 註記文字" 
                                class="w-full py-[5px] rounded-xl bg-white pl-2 pr-7 border border-slate-400 shadow-sm focus:ring-2 focus:ring-indigo-500/20 outline-none text-[15px] font-bold text-slate-900">
                            <button @click="activePicker = { field: 'record_date', title: '修改得知日期' }" class="absolute right-2 text-slate-400 hover:text-indigo-600 transition-colors p-1 z-20 active:scale-90">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2v12a2 2 0 002 2z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </button>
                        </div>
                    </div>
                    <div class="space-y-1">
                        <label class="text-[15px] font-black text-slate-400 uppercase tracking-widest block ml-1">載錄目標仙師</label>
                        <select v-model="form.master_id" style="font-size: 17px;" class="w-full py-[5px] rounded-xl bg-white px-3 font-bold text-slate-900 border border-slate-400 shadow-sm focus:ring-2 focus:ring-indigo-500/20 outline-none">
                            <option v-for="m in masters" :key="m.id" :value="m.id">{{ m.name === '父皇仙師' ? '父皇' : m.name }}</option>
                        </select>
                    </div>
                </div>

                <!-- SINGLE MODE -->
                <div v-if="localMode === 'single'" class="space-y-1 mt-[-8px] animate-fade-in">
                    <div class="space-y-1 py-[5px]">
                        <label class="text-[15px] font-black text-slate-400 uppercase tracking-widest block ml-1">法寶名稱</label>
                        <textarea v-model="form.name" rows="3" placeholder="請輸入一項法寶名稱..." style="font-size: 17px;" class="w-full py-[5px] rounded-xl bg-white px-3 font-bold text-slate-900 border border-slate-400 focus:ring-2 focus:ring-indigo-500/20 outline-none placeholder:text-slate-300"></textarea>
                    </div>

                    <div class="space-y-1 py-[5px]">
                        <label class="text-[15px] font-black text-slate-400 uppercase tracking-widest block ml-1">法寶用意</label>
                        <textarea v-model="form.purpose" rows="2" placeholder="輸入法寶用途..." style="font-size: 17px;" class="w-full py-[5px] rounded-xl bg-white px-3 font-bold text-slate-900 border border-slate-400 focus:ring-2 focus:ring-indigo-500/20 outline-none placeholder:text-slate-300"></textarea>
                    </div>

                    <!-- MASTER STATUS/DATE (ONLY FOR SINGLE PERSON) -->
                    <div v-if="personnel.length === 0" class="grid grid-cols-2 gap-3 py-[5px] animate-fade-in">
                        <div class="space-y-1">
                            <label class="text-[15px] font-black text-slate-400 uppercase tracking-widest block ml-1">目前狀態</label>
                            <select v-model="form.status" style="font-size: 17px;" class="w-full py-[5px] rounded-xl bg-white px-3 font-bold text-slate-900 border border-slate-400 shadow-sm outline-none">
                                <option value="未求得">未求得</option>
                                <option value="已求得">已求得</option>
                                <option value="已登記">已登記</option>
                            </select>
                        </div>
                        <div class="space-y-1">
                            <label class="text-[15px] font-black text-slate-400 uppercase tracking-widest block ml-1">
                                {{ form.status === '已登記' ? '登記日期' : (form.status === '已求得' ? '求得日期' : '日期') }}
                            </label>
                            <div class="relative">
                                <input v-model="form.obtained_date" type="text" placeholder="YYYY-MM-DD" style="font-size: 17px;" class="w-full py-[5px] rounded-xl bg-white px-3 font-bold text-slate-900 border border-slate-400 shadow-sm outline-none pr-10">
                                <button @click="activePicker = { field: 'obtained_date', title: '修改日期' }" class="absolute right-2 top-1/2 -translate-y-1/2 text-slate-400 p-2 z-20 active:scale-90">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2v12a2 2 0 002 2z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div v-if="personnel.length === 0" class="flex justify-center py-4 border-t border-slate-50 mt-4">
                        <button @click="addPersonnelRow" class="px-6 py-2 bg-slate-100 text-indigo-600 font-bold text-[15px] rounded-xl flex items-center space-x-2 active:scale-95 transition-all shadow-sm border border-slate-200">
                            <span>＋ 多人承接模式</span>
                        </button>
                    </div>

                    <!-- PERSONNEL SECTION (ONLY FOR MULTI-PERSON) -->
                    <div v-if="personnel.length > 0" class="space-y-6 pt-4 border-t border-slate-100 animate-fade-in">
                        <div class="flex items-center justify-between ml-1">
                            <div class="flex flex-col">
                                <label class="text-[15px] font-bold text-red-600 uppercase tracking-tight">多人承接人員名單</label>
                                <span class="text-[11px] text-slate-400 font-bold">目前處於多人承接模式</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <button @click="personnel = []" class="px-3 py-1 bg-slate-100 text-slate-500 rounded-lg text-[12px] font-bold active:scale-95 transition-all shadow-sm">
                                    切換回單人
                                </button>
                                <button @click="addPersonnelRow" class="px-3 py-1 bg-indigo-600 text-white rounded-lg text-[12px] font-bold active:scale-95 transition-all shadow-sm" style="color: white !important;">
                                    ＋ 新增人員
                                </button>
                            </div>
                        </div>

                        <div v-for="(p, idx) in personnel" :key="idx" 
                            class="p-4 bg-slate-50/30 rounded-2xl border border-slate-400 space-y-3 relative group animate-fade-in">
                            <div class="absolute top-2 right-2 flex items-center space-x-1">
                                <button v-if="personnel.length > 1" @click="movePersonnel(idx, -1)" :disabled="idx === 0" class="p-1 text-slate-300 hover:text-indigo-500 disabled:opacity-10 transition-all active:scale-90">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 15l7-7 7 7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </button>
                                <button v-if="personnel.length > 1" @click="movePersonnel(idx, 1)" :disabled="idx === personnel.length - 1" class="p-1 text-slate-300 hover:text-indigo-500 disabled:opacity-10 transition-all active:scale-90">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </button>
                                <button @click="removePersonnelRow(idx)" class="ml-1 text-slate-300 hover:text-red-500 transition-colors p-1">✕</button>
                            </div>
                            
                            <!-- Fields -->
                            <div class="space-y-3">
                                <div class="grid grid-cols-2 gap-2">
                                    <div class="space-y-1">
                                        <label class="text-[11px] text-red-400 ml-1 font-bold">法號</label>
                                        <input v-model="p.custom_name" type="text" placeholder="法號" list="dharma-names"
                                            @keydown.enter.prevent="handlePersonnelEnter(idx)"
                                            class="personnel-name-input w-full py-[10px] rounded-xl border border-slate-400 bg-white px-3 text-[18px] font-bold text-slate-900 focus:ring-2 focus:ring-indigo-100 outline-none font-outfit">
                                    </div>
                                    <div class="space-y-1">
                                        <label class="text-[11px] text-red-400 ml-1 font-bold">狀態</label>
                                        <select v-model="p.status" class="w-full py-[10px] rounded-xl border border-slate-400 bg-white px-3 text-[15px] font-bold focus:ring-2 focus:ring-indigo-100 outline-none"
                                            :style="p.status === '未求得' ? 'color: #dc2626 !important;' : (p.status === '已求得' ? 'color: #2563eb !important;' : 'color: #059669 !important;')">
                                            <option value="未求得">未求得</option>
                                            <option value="已求得">已求得</option>
                                            <option value="已登記">已登記</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <div class="space-y-1" :class="{ 'opacity-50 pointer-events-none': p.status === '未求得' }">
                                        <label class="text-[11px] text-red-400 ml-1 font-bold">
                                            {{ p.status === '已登記' ? '登記日期' : (p.status === '已求得' ? '求得日期' : '日期') }}
                                        </label>
                                        <div class="relative">
                                            <input v-model="p.obtained_date" type="text" placeholder="日期" 
                                                class="w-full py-[10px] rounded-xl bg-white px-2 font-bold text-slate-900 border border-slate-400 shadow-sm outline-none text-[15px] pr-8">
                                            <button @click="activePicker = { field: 'obtained_date', idx: idx, title: '修改人員日期' }" class="absolute right-1 top-1/2 -translate-y-1/2 text-slate-300 p-1 z-20 active:scale-90">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2v12a2 2 0 002 2z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="space-y-1">
                                        <label class="text-[11px] text-slate-400 ml-1 font-bold">個人備註</label>
                                        <input v-model="p.remarks" placeholder="輸入個人備註..." class="w-full py-[10px] rounded-xl border border-slate-400 bg-white px-3 text-[15px] font-normal text-slate-900 focus:ring-2 focus:ring-indigo-100 outline-none">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Table is "collapsed" by default when personnel length is 0 -->


                        <div class="col-span-2 space-y-1 py-[5px]">
                            <label class="text-[15px] font-black text-slate-400 uppercase tracking-widest block ml-1">詳細內容 / 備註</label>
                            <textarea v-model="form.remarks" rows="1" placeholder="輸入更多說明內容..." style="font-size: 17px;" class="w-full py-[5px] rounded-xl bg-white p-2 font-bold text-slate-900 border border-slate-400 focus:ring-2 focus:ring-indigo-500/20 outline-none"></textarea>
                        </div>
                    </div>
                </div>

                <!-- BATCH MODE -->
                <div v-if="localMode === 'batch'" class="space-y-4 animate-fade-in">
                    <div class="bg-white rounded-2xl p-4 space-y-4 shadow-inner relative border border-slate-100">
                        <!-- Batch Mode Selection Removed (Automatic Parsing) -->

                        <div class="flex items-center justify-between">
                            <label class="text-[15px] font-black text-slate-400 uppercase tracking-widest ml-1">貼入法寶名稱明細</label>
                            <div class="flex items-center space-x-3">
                                <button v-if="batchInput" @click="batchInput = ''" class="text-[11px] text-red-500 hover:underline">清除內容</button>
                            </div>
                        </div>
                        <textarea v-model="batchInput" rows="8" class="w-full rounded-xl border border-slate-400 p-4 font-mono text-[14px]" 
                            placeholder="單人承接&#10;請輸入&#10;法寶名稱&#10;法寶用意(填入時要加用意二字)&#10;狀態(未求得/已求得/已登記)&#10;日期(yyyy/mm/dd)&#10;備註&#10;&#10;多人承接&#10;請輸入&#10;法寶名稱&#10;法寶用意(填入時要加用意二字)&#10;法號/狀態(未求得/已求得/已登記)&#10;日期(yyyy/mm/dd)&#10;法號/狀態(未求得/已求得/已登記)&#10;日期(yyyy/mm/dd)&#10;備註&#10;以上沒有資料可不填">
                        </textarea>
                        <input type="file" ref="fileInput" class="hidden" @change="handleFileUpload" accept=".txt,.xlsx,.xls,.docx">
                    </div>

                    <div v-if="excelRows.length > 0" class="mt-4 border rounded-xl overflow-hidden shadow-sm bg-white">
                        <div class="bg-slate-50 px-4 py-2 border-b flex justify-between items-center">
                            <span class="text-[13px] font-bold text-slate-600">預覽解析結果 (共 {{ excelRows.length }} 筆)</span>
                            <span class="text-[11px] text-slate-400">請確認內容正確後再儲存</span>
                        </div>
                        <div class="overflow-x-auto max-h-[400px]">
                            <table class="w-full text-left border-collapse">
                                <thead class="sticky top-0 bg-slate-50 shadow-sm z-10">
                                    <tr class="text-[12px] text-slate-500 uppercase">
                                        <th class="px-4 py-2 border-b font-black w-32">法寶名稱 / 用意</th>
                                        <th class="px-4 py-2 border-b font-black">承接人員詳情</th>
                                        <th class="px-4 py-2 border-b font-black w-24 text-center">狀態</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(row, idx) in excelRows" :key="idx" class="border-b border-slate-50">
                                        <td class="p-2 text-indigo-500 font-black">{{ getMasterName(row._master_id) }}</td>
                                        <td v-for="col in excelCols" :key="col.key" class="p-2 text-slate-600">{{ row[col.key] }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Action -->
            <div class="absolute bottom-[7vh] left-0 right-0 px-4 py-4 bg-white/95 backdrop-blur-md border-t border-slate-50 z-[10]">
                <button @click="handleSubmit" :disabled="isSaving || (localMode === 'batch' && excelRows.length === 0)"
                    class="w-full bg-indigo-600 py-[10px] rounded-2xl text-white font-bold text-[20px] active:scale-95 transition-all disabled:bg-slate-300"
                    style="color: white !important;">
                    {{ isSaving ? '處理中...' : (localMode === 'single' ? '確認資料載錄' : `開始載錄這 ${excelRows.length} 筆資料`) }}
                </button>
            </div>

            <mobile-navbar :can-back="false" @home="$emit('cancel')" :show-action="false" :can-search="false" is-absolute />
            
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
import { ref, watch, computed, onMounted, nextTick } from 'vue';
import axios from 'axios';
import CompactDatePicker from './CompactDatePicker.vue';
import MobileNavbar from './MobileNavbar.vue';

const props = defineProps({
    mode: String,
    initialData: Object,
    masters: Array,
    isSaving: Boolean
});

const emit = defineEmits(['saveSingle', 'saveBatch', 'cancel', 'close']);
const localMode = ref(props.mode || 'single');
const form = ref({ ...props.initialData });
const batchInput = ref('');
const activePicker = ref(null);

const activePickerValue = computed({
    get: () => {
        if (!activePicker.value) return '';
        if (activePicker.value.idx !== undefined) {
            return personnel.value[activePicker.value.idx][activePicker.value.field];
        }
        return form.value[activePicker.value.field];
    },
    set: (val) => {
        if (!activePicker.value) return;
        if (activePicker.value.idx !== undefined) {
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

const excelCols = ref([
    { key: 'c0', label: '法寶名稱' },
    { key: 'c1', label: '法寶用意' }
]);

const excelRows = computed(() => {
    if (!batchInput.value.trim()) return [];

    // HYBRID SMART PARSER: Automatically handles Single & Multi-person blocks
    const lines = batchInput.value.split('\n').map(l => l.trim());
    const records = [];
    let currentRec = null;
    let currentMasterId = form.value.master_id;

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

        const isStatus = line.match(/(已登記|已求得|未求得)/);
        const isDate = line.match(/\d{4}[\/\-]\d{1,2}[\/\-]\d{1,2}/);
        const isPurpose = line.includes('用意') || line.includes('用法') || line.includes('用途');
        const looksLikeName = !line.includes('：') && !line.includes(':') && !isStatus && !isDate && !isPurpose;

        if (looksLikeName) {
            // STRONGER FINALIZE: If we see a new name and current record is "substantial", finalize it
            if (currentRec && (currentRec.purpose !== '-' || currentRec.status !== '未求得' || currentRec.personnel.length > 0 || currentRec.remarks.length > 0)) {
                records.push(currentRec);
                currentRec = null;
            }

            if (!currentRec) {
                currentRec = { name: line, purpose: '-', personnel: [], remarks: [], status: '未求得', master_id: currentMasterId, date: '' };
            } else {
                // If it's a multi-person block, names after the first one are personnel
                // BUT only if the record already has some structure (purpose or status)
                if (currentRec.purpose !== '-' || currentRec.status !== '未求得' || currentRec.personnel.length > 0) {
                    currentRec.personnel.push({ custom_name: line, status: '已登記', obtained_date: '', remarks: '' });
                } else {
                    // Otherwise, just update the name (rare case)
                    currentRec.name = line;
                }
            }
        } else if (currentRec) {
            if (isPurpose) {
                currentRec.purpose = line.replace(/.*(用意|用法|用途)[:：]?/, '').trim() || '-';
            } else if (isStatus) {
                const statusStr = isStatus[0];
                // Try to find a name before the status keyword or colon
                const namePart = line.split(statusStr)[0].replace(/狀態[:：]?/, '').trim();
                
                if (namePart) {
                    const pDate = isDate ? isDate[0].replace(/\//g, '-') : '';
                    currentRec.personnel.push({ custom_name: namePart, status: statusStr, obtained_date: pDate, remarks: '' });
                } else {
                    currentRec.status = statusStr;
                    if (isDate) currentRec.date = isDate[0];
                }
            } else if (isDate) {
                currentRec.date = isDate[0];
            } else if (!line.includes('以上沒有資料')) {
                currentRec.remarks.push(line);
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
            _is_multi: r.personnel.length > 0,
            _status: finalStatus,
            _record_date: r.date,
            _master_id: r.master_id
        };
    });
});

const personnel = ref([]);
const dharmaNames = ref([]);
const isMulti = ref(true);

const fetchDharmaNames = async () => {
    try {
        const res = await axios.get('/api/dharma-names-list');
        dharmaNames.value = res.data;
    } catch (e) {}
};

const addPersonnelRow = () => {
    personnel.value.push({ custom_name: '', relationship: '', obtained_date: '', status: '已求得', remarks: '' });
};

const handlePersonnelEnter = (idx) => {
    if (idx === personnel.value.length - 1) addPersonnelRow();
    nextTick(() => {
        const inputs = document.querySelectorAll('.personnel-name-input');
        if (inputs[idx + 1]) inputs[idx + 1].focus();
    });
};

const removePersonnelRow = (idx) => { personnel.value.splice(idx, 1); };
const movePersonnel = (idx, dir) => {
    const target = idx + dir;
    if (target < 0 || target >= personnel.value.length) return;
    const item = personnel.value[idx];
    personnel.value.splice(idx, 1);
    personnel.value.splice(target, 0, item);
};

onMounted(() => {
    fetchDharmaNames();
});

watch(() => props.initialData, (newVal) => {
    form.value = { ...newVal };
    if (newVal.dharma_name_registries) {
        personnel.value = newVal.dharma_name_registries.map(r => ({
            custom_name: r.dharma_name?.name || r.custom_name || '',
            relationship: Array.isArray(r.related_personnel) ? r.related_personnel.join('、') : (r.related_personnel || ''),
            obtained_date: r.obtained_date || '',
            status: r.status || '已求得',
            remarks: Array.isArray(r.remarks) ? r.remarks.join('\n') : (r.remarks || '')
        }));
    }
}, { immediate: true });

// Auto-fill date when status changes
watch(() => form.value.status, (newStatus) => {
    if ((newStatus === '已求得' || newStatus === '已登記') && !form.value.obtained_date) {
        form.value.obtained_date = new Date().toISOString().split('T')[0];
    }
});

watch(personnel, (newVal) => {
    newVal.forEach(p => {
        // Auto-fill date
        if ((p.status === '已求得' || p.status === '已登記') && !p.obtained_date) {
            p.obtained_date = new Date().toISOString().split('T')[0];
        }

        // Relationship Rule: "Name之Relative" split
        if (p.custom_name && p.custom_name.trim()) {
            const relSplitMatch = p.custom_name.match(/^(.*?)[之的](.+)$/);
            if (relSplitMatch) {
                const namePart = relSplitMatch[1].trim();
                let relPart = relSplitMatch[2].trim();
                if (relPart === '母') relPart = '母親';
                if (relPart === '父') relPart = '父親';
                p.custom_name = namePart;
                p.relationship = relPart;
            }
        }

        // Terminology Normalization
        if (p.relationship) {
            let rel = p.relationship.trim();
            if (rel === '之母' || rel === '母') p.relationship = '母親';
            else if (rel === '之父' || rel === '父') p.relationship = '父親';
            else if (rel.startsWith('之') || rel.startsWith('的')) {
                p.relationship = rel.substring(1);
            }
        }
    });
}, { deep: true });

const handleSubmit = () => {
    if (localMode.value === 'single') {
        if (!form.value.name?.trim()) { alert('請輸入法寶名稱'); return; }
        const cleaned = personnel.value.filter(p => p.custom_name?.trim());
        const isMulti = cleaned.length > 0;
        
        let finalStatus = form.value.status;
        if (isMulti) {
            const hasUnobtained = cleaned.some(p => p.status === '未求得');
            const allRegistered = cleaned.every(p => p.status === '已登記');
            if (hasUnobtained) finalStatus = '未求得';
            else if (allRegistered) finalStatus = '已登記';
            else finalStatus = '已求得';
        }
        
        emit('saveSingle', { 
            ...form.value,
            status: finalStatus,
            is_multi: isMulti,
            dharma_name_registries: cleaned.map(p => ({
                ...p,
                status: p.status || '已求得',
                obtained_date: p.obtained_date || '',
                remarks: p.remarks || '',
                related_personnel: p.relationship ? p.relationship.split(/[、, ]+/).filter(x => x) : []
            }))
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
                status: row._status || '未求得', 
                count: 1, 
                remarks: row._manualRemarks || '',
                is_multi: row._is_multi || false,
                dharma_name_registries: row._dharma_name_registries || []
            }))
        });
    }
};

const getMasterName = (id) => {
    const m = props.masters?.find(m => String(m.id) === String(id));
    return m ? (m.name === '父皇仙師' ? '父皇' : m.name) : '預設';
};

const handleFileUpload = (e) => { /* logic */ };
const handleDirectPaste = async () => { /* logic */ };
</script>

<style scoped>
.animate-slide-up { animation: slideUp 0.4s cubic-bezier(0.16, 1, 0.3, 1); }
.animate-fade-in { animation: fadeIn 0.3s ease-out; }
@keyframes slideUp { from { transform: translateY(100%); } to { transform: translateY(0); } }
@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
.no-scrollbar::-webkit-scrollbar { display: none; }
</style>
