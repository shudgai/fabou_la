<template>
    <div v-if="mode" class="fixed inset-0 z-[2000] flex items-end md:items-center justify-center px-0 imperial-grace-module">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm" @click="$emit('cancel')"></div>
        
        <!-- Form Container -->
        <div class="relative w-full h-[100vh] md:max-w-4xl bg-white shadow-[0_-10px_40px_rgba(0,0,0,0.1)] overflow-hidden animate-slide-up flex flex-col pb-[7vh]">
            <!-- Header -->
            <div class="px-[10px] py-[12px] flex items-center bg-white border-b border-slate-50 relative">
                <div class="flex-1 flex flex-col justify-center min-w-0 py-[5px] pr-6">
                    <div class="text-[25px] font-bold leading-tight font-outfit tracking-widest break-words text-slate-900">
                        重大皇恩專區
                    </div>
                </div>
                <button @click="$emit('cancel')" class="text-slate-300 hover:text-slate-600 transition-colors py-[5px] absolute right-4 top-1/2 -translate-y-1/2">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
            </div>

            <!-- Tabs -->
            <div class="px-4 py-[5px] bg-white flex space-x-1 border-b border-slate-100 mt-[-5px]">
                <button @click="localMode = 'single'" 
                    :class="localMode === 'single' ? 'bg-white shadow-sm text-indigo-600' : 'text-slate-400'"
                    class="flex-1 py-[5px] text-[16px] font-black rounded-xl transition-all whitespace-nowrap">逐筆登錄</button>
                <button @click="localMode = 'batch'" 
                    :class="localMode === 'batch' ? 'bg-white shadow-sm text-indigo-600' : 'text-slate-400'"
                    class="flex-1 py-[5px] text-[16px] font-black rounded-xl transition-all whitespace-nowrap">文字/EXCEL 記載</button>
            </div>

            <!-- Scrollable Content -->
            <div class="flex-1 overflow-y-auto px-[10px] pt-6 pb-32 space-y-4 custom-scrollbar">
                
                <!-- COMMON FIELDS (Date & Master) -->
                <div class="grid grid-cols-2 gap-[2.5px] bg-white p-[5px] mt-[-10px] py-[5px]">
                    <div class="space-y-1">
                        <label class="text-[15px] font-black text-slate-400 uppercase tracking-widest block ml-1">得知日期</label>
                        <div class="relative flex items-center">
                            <input v-model="form.record_date" type="text" placeholder="年/月/日 或 註記文字" 
                                class="w-full py-[5px] rounded-xl bg-white pl-2 pr-7 border border-slate-400 shadow-sm focus:ring-2 focus:ring-indigo-500/20 outline-none text-[15px] font-bold text-slate-900">
                            <button @click="activeDate = 'record_date'" class="absolute right-2 text-slate-400 hover:text-indigo-600 transition-colors p-1">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </button>
                        </div>
                    </div>
                    <div class="space-y-1">
                        <label class="text-[15px] font-black text-slate-400 uppercase tracking-widest block ml-1">載錄目標仙師</label>
                        <select v-model="form.master_id" style="font-size: 17px;" class="w-full py-[5px] rounded-xl bg-white px-3 font-bold text-slate-900 border border-slate-400 shadow-sm focus:ring-2 focus:ring-indigo-500/20 outline-none">
                            <option v-for="m in masters" :key="m.id" :value="m.id">{{ m.name }}</option>
                        </select>
                    </div>
                </div>

                <!-- SINGLE MODE -->
                <div v-if="localMode === 'single'" class="space-y-1 mt-[-8px] animate-fade-in">
                    <div class="space-y-1 py-[5px]">
                        <label class="text-[15px] font-black text-slate-400 uppercase tracking-widest block ml-1">法寶名稱 (支援多筆貼入)</label>
                        <textarea v-model="form.name" rows="3" placeholder="輸入法寶名稱，可用空格或換行分隔多筆..." style="font-size: 17px;" class="w-full py-[5px] rounded-xl bg-white px-3 font-bold text-slate-900 border border-slate-400 focus:ring-2 focus:ring-indigo-500/20 outline-none placeholder:text-slate-300"></textarea>
                    </div>

                    <div class="space-y-1 py-[5px]">
                        <label class="text-[15px] font-black text-slate-400 uppercase tracking-widest block ml-1">法寶用意</label>
                        <input v-model="form.purpose" type="text" placeholder="輸入法寶用途..." style="font-size: 17px;" class="w-full py-[5px] rounded-xl bg-white px-3 font-bold text-slate-900 border border-slate-400 focus:ring-2 focus:ring-indigo-500/20 outline-none placeholder:text-slate-300">
                    </div>

                    <div class="grid grid-cols-2 gap-[2.5px]">
                        <div class="space-y-1 py-[5px]">
                            <label class="text-[15px] font-black text-slate-400 uppercase tracking-widest block ml-1">目前狀態</label>
                            <select v-model="form.status" @change="handleStatusChange" style="font-size: 17px;" class="w-full py-[5px] rounded-xl bg-white px-3 font-bold border border-slate-400 focus:ring-2 focus:ring-indigo-500/20 outline-none placeholder:text-slate-300"
                                :style="form.status === '未求得' ? 'color: #dc2626 !important;' : (form.status === '已求得' ? 'color: #2563eb !important;' : 'color: #059669 !important;')">
                                <option value="未求得">未求得</option>
                                <option value="已求得">已求得</option>
                                <option value="已登記">已登記</option>
                            </select>
                        </div>
                        <div v-if="form.status !== '已登記'" class="space-y-1 py-[5px]">
                            <label class="text-[15px] font-black text-slate-400 uppercase tracking-widest block ml-1">求得日期</label>
                            <div class="relative flex items-center">
                                <input v-model="form.obtained_date" type="text" :placeholder="form.status === '未求得' ? '-' : '年/月/日 或 註記文字'" 
                                    :disabled="form.status === '未求得'"
                                    :class="form.status === '未求得' ? 'opacity-30 cursor-not-allowed' : ''"
                                    class="w-full py-[5px] rounded-xl bg-white pl-2 pr-7 shadow-sm border border-slate-400 focus:ring-2 focus:ring-indigo-500/20 outline-none text-[15px] font-bold text-slate-900">
                                <button @click="form.status !== '未求得' && (activeDate = 'obtained_date')" 
                                    :disabled="form.status === '未求得'"
                                    class="absolute right-2 text-slate-400 hover:text-indigo-600 transition-colors p-1">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </button>
                            </div>
                        </div>
                        <div v-else></div>
                    </div>

                    <div class="space-y-1 py-[5px]">
                        <label class="text-[15px] font-black text-slate-400 uppercase tracking-widest block ml-1">詳細內容 / 備註</label>
                        <textarea v-model="form.remarks" rows="1" placeholder="輸入更多說明內容..." style="font-size: 17px;" class="w-full py-[5px] rounded-xl bg-white p-2 font-bold text-slate-900 border border-slate-400 focus:ring-2 focus:ring-indigo-500/20 outline-none leading-normal placeholder:text-slate-300"></textarea>
                    </div>
                </div>

                <!-- BATCH MODE -->
                <div v-if="localMode === 'batch'" class="space-y-4 animate-fade-in">
                    <div class="bg-white rounded-2xl p-4 space-y-3 shadow-inner relative border border-slate-100">
                        <div class="flex items-center justify-between mb-1">
                            <label class="text-[15px] font-black text-slate-400 uppercase tracking-widest ml-1">貼入清單內容</label>
                            <div class="flex items-center space-x-3">
                                <button v-if="batchInput" @click="batchInput = ''" class="text-[11px] text-red-500 hover:underline">清除內容</button>
                                <button @click="handleDirectPaste" class="text-[11px] text-emerald-600 font-bold hover:bg-emerald-50 px-2 py-[5px] rounded-lg transition-all">
                                    貼上內容
                                </button>
                                <button @click="$refs.fileInput.click()" class="text-[11px] text-indigo-600 flex items-center hover:bg-white px-2 py-[5px] rounded-lg transition-all">
                                    匯入檔案
                                </button>
                            </div>
                        </div>
                        
                        <div class="relative group">
                            <textarea v-model="batchInput" rows="8" 
                                class="w-full rounded-xl shadow-sm text-[14px] bg-white border border-slate-400 focus:ring-2 focus:ring-indigo-500/20 p-4 font-mono leading-relaxed pr-10 placeholder:text-slate-300" 
                                placeholder="支援直接貼上或輸入 Excel 或 LINE 內容... 第一行若是日期將自動作為登記日期！"></textarea>
                            <button v-if="batchInput" @click="batchInput = ''" 
                                class="absolute top-3 right-3 w-6 h-6 flex items-center justify-center rounded-full bg-slate-100 text-slate-400 hover:bg-red-50 hover:text-red-500 transition-all shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </button>
                        </div>
                        <input type="file" ref="fileInput" class="hidden" @change="handleFileUpload" accept=".txt,.xlsx,.xls,.docx">
                    </div>

                    <!-- Batch Preview Table -->
                    <!-- Batch Preview Table -->
                    <div v-if="excelRows.length > 0" class="border border-slate-100 rounded-2xl overflow-hidden shadow-sm bg-white animate-fade-in">
                        <div class="bg-white px-4 py-3 border-b border-slate-100 flex justify-between items-center">
                            <div class="flex flex-col">
                                <span class="text-[15px] font-black text-indigo-600">偵測到 {{ excelRows.length }} 筆資料</span>
                                <span class="text-[11px] text-slate-400 font-medium">點擊下方「開始載錄」即可存入系統</span>
                            </div>
                            <div class="flex items-center space-x-4">
                                <label class="flex items-center space-x-2 cursor-pointer">
                                    <input type="checkbox" v-model="showTotal" class="rounded text-indigo-600 focus:ring-indigo-500">
                                    <span class="text-[13px] font-black text-slate-500 uppercase tracking-tight">統計加總</span>
                                </label>
                                <select v-if="showTotal" v-model="sumKey" class="text-[13px] font-black bg-slate-50 border border-slate-200 rounded-lg px-2 py-1 outline-none focus:ring-1 focus:ring-indigo-500">
                                    <option value="">選擇統計欄</option>
                                    <option v-for="(col, i) in excelCols" :key="i" :value="col.key">第 {{ i + 1 }} 欄 ({{ col.label }})</option>
                                </select>
                            </div>
                        </div>

                        <!-- Preview Chips (Quick Overview) -->
                        <div class="px-4 py-2 bg-slate-50/50 flex flex-wrap gap-1.5 border-b border-slate-100 max-h-[100px] overflow-y-auto no-scrollbar">
                            <span v-for="(row, idx) in excelRows" :key="idx" class="px-2 py-0.5 bg-white border border-slate-200 rounded-lg text-[12px] font-bold text-slate-600">
                                {{ row.c0 }}
                            </span>
                        </div>

                        <div class="max-h-48 overflow-y-auto custom-scrollbar no-scrollbar">
                            <table class="w-full text-[14px] text-left">
                                <thead class="bg-slate-50 text-slate-400 sticky top-0 uppercase tracking-tighter border-b border-slate-100">
                                    <tr>
                                        <th class="p-2 border-b border-slate-100 font-black">仙師</th>
                                        <th v-for="col in excelCols" :key="col.key" class="p-2 border-b border-slate-100 font-black">{{ col.label }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(row, idx) in excelRows" :key="idx" class="border-b border-slate-50 last:border-0 hover:bg-slate-50 transition-colors">
                                        <td class="py-3 px-2 text-indigo-500 font-black text-[11px] whitespace-nowrap">
                                            {{ getMasterName(row._master_id) }}
                                        </td>
                                        <td v-for="col in excelCols" :key="col.key" class="py-3 px-2 text-slate-600 font-bold truncate max-w-[120px]">{{ row[col.key] }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div v-if="showTotal && sumKey" class="bg-white px-4 py-3 border-t border-slate-100 flex justify-between items-center animate-fade-in">
                            <span class="text-[15px] font-black text-slate-400">本次批次總額預估</span>
                            <span class="text-[17px] font-black text-slate-900 tracking-tighter">{{ batchTotalValue.toLocaleString('zh-TW') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Action (Fixed exactly above Navbar) -->
            <div class="fixed bottom-[7vh] left-0 right-0 px-4 py-4 bg-white/95 backdrop-blur-md border-t border-slate-50 z-[10] flex items-center justify-center">
                <button 
                    @click="handleSubmit" 
                    :disabled="isSaving || (localMode === 'batch' && excelRows.length === 0)"
                    style="font-size: 20px !important; color: white !important;"
                    class="w-full bg-indigo-600 py-[10px] rounded-2xl hover:bg-indigo-700 active:scale-[0.98] transition-all disabled:bg-slate-300 font-bold border-none outline-none"
                >
                    <template v-if="isSaving">
                        <span class="flex items-center justify-center">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            處理入庫中...
                        </span>
                    </template>
                    <template v-else>
                        {{ localMode === 'single' ? (form.id ? '儲存修改' : '確認資料載錄') : `開始載錄這 ${excelRows.length} 筆資料` }}
                    </template>
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

        <!-- Custom Date Picker -->
        <compact-date-picker 
            v-if="activeDate"
            v-model="form[activeDate]"
            :title="activeDate === 'record_date' ? '得知日期' : '求得日期'"
            @close="activeDate = null"
        />
    </div>
</template>

<script setup>
import { ref, watch, computed, onMounted } from 'vue';
import CompactDatePicker from './CompactDatePicker.vue';
import MobileNavbar from './MobileNavbar.vue';

const props = defineProps({
    mode: String, // 'single' or 'batch'
    initialData: Object,
    masters: Array,
    isSaving: Boolean
});

const emit = defineEmits(['saveSingle', 'saveBatch', 'cancel', 'fileUpload']);
const activeDate = ref(null);

const localMode = ref(props.mode || 'single');
const form = ref({ ...props.initialData });
const batchInput = ref('');
const excelRows = ref([]);
const excelCols = ref([]);
const showTotal = ref(false);
const sumKey = ref('');

const selectedMasterName = computed(() => {
    const m = props.masters?.find(m => String(m.id) === String(form.value.master_id));
    return m ? m.name : '';
});

const batchTotalValue = computed(() => {
    if (!sumKey.value) return 0;
    return excelRows.value.reduce((sum, row) => {
        const val = parseFloat(String(row[sumKey.value] || '0').replace(/[^\d.-]/g, ''));
        return sum + (isNaN(val) ? 0 : val);
    }, 0);
});

watch(() => props.initialData, (newVal) => {
    form.value = { count: 1, ...newVal };
}, { deep: true });

watch(() => props.mode, (newVal) => {
    if (newVal) {
        localMode.value = newVal;
        if (newVal === 'batch') {
            form.value.record_date = '';
        }
        // 重新開啟介面時，清空上一次輸入的 batch 資料，避免殘留
        batchInput.value = '';
        excelRows.value = [];
        excelCols.value = [];
        showTotal.value = false;
        sumKey.value = '';
        
        // 如果是新增模式（沒有id），重設 form 資料
        if (!props.initialData?.id) {
            form.value = { count: 1, ...props.initialData };
        }
    }
});

watch(localMode, (newVal) => {
    if (newVal === 'batch') {
        form.value.record_date = '';
    }
});

watch(batchInput, (newVal) => {
    if (!newVal || newVal.trim() === '') {
        excelRows.value = [];
        showTotal.value = false;
        sumKey.value = '';
        return;
    }
    const lines = newVal.trim().split(/\r?\n/).map(line => {
        const trimmedLine = line.trim();
        if (!trimmedLine) return [];
        if (trimmedLine.includes('\t')) return trimmedLine.split(/[\t]+/).map(c => c.trim());
        if (trimmedLine.startsWith('用意') || trimmedLine.startsWith('狀態') || trimmedLine.startsWith('法寶')) {
            return [trimmedLine];
        }
        let parts = trimmedLine.split(/[\s]{2,}|[,，]/);
        return (parts.length <= 1 && / \d+$/.test(trimmedLine)) ? trimmedLine.split(/\s+/) : parts.map(c => c.trim());
    }).filter(l => l.length > 0);
    processBatchLines(lines);
});

const processBatchLines = (rawLines) => {
    if (!rawLines || rawLines.length === 0) return;
    
    const parseDateText = (str) => {
        if (!str) return null;
        const normalized = str.normalize('NFKC').trim();
        // Priority 1: 4-digit CE
        const ceMatch = normalized.match(/\b(\d{4})[/\-\s](\d{1,2})[/\-\s](\d{1,2})\b/);
        if (ceMatch) return `${ceMatch[1]}-${ceMatch[2].padStart(2,'0')}-${ceMatch[3].padStart(2,'0')}`;

        // Priority 2: 2-3 digit ROC
        const rocMatch = normalized.match(/\b(\d{2,3})[/\-\s](\d{1,2})[/\-\s](\d{1,2})\b/);
        if (rocMatch) {
            const y = parseInt(rocMatch[1]) + 1911;
            return `${y}-${rocMatch[2].padStart(2,'0')}-${rocMatch[3].padStart(2,'0')}`;
        }
        return null;
    };

    let currentBlockDate = form.value.record_date || '';
    let currentBlockMasterId = form.value.master_id;
    const filteredLines = [];
    let pendingAttrs = { purpose: '', remarks: '', status: '', record_date: null, obtained_date: null };

    for (const row of rawLines) {
        if (!row || row.length === 0) continue;
        const firstCell = String(row[0] || '').normalize('NFKC').trim();
        if (!firstCell || firstCell.includes('---')) continue;

        const fullLine = row.join(' ').normalize('NFKC').trim();
        const parsedDate = parseDateText(fullLine);
        if (parsedDate) {
            currentBlockDate = parsedDate;
            if (fullLine.length <= 20) continue; 
        }
        
        let masterMatch = props.masters.find(m => {
            const mName = m.name;
            const shortName = mName.replace('仙師', '');
            return firstCell === mName || firstCell === shortName || 
                   firstCell.startsWith(mName + '：') || firstCell.startsWith(shortName + '：') ||
                   firstCell.startsWith(mName + ':') || firstCell.startsWith(shortName + ':');
        });
        
        if (masterMatch) {
            currentBlockMasterId = masterMatch.id;
            const isHeaderOnly = row.length > 1 ? !row.slice(1).some(c => c.trim()) : 
                                 firstCell.length <= (masterMatch.name.length + 2);
            if (isHeaderOnly) continue;
        }
        
        const attrKeywords = ['用意', '狀態', '備註', '求寶方式', '由來', '得知日期', '登記日期', '求得日期', '日期'];
        const firstWord = firstCell.split(/[\s：:]/)[0];
        
        if (attrKeywords.includes(firstWord)) {
            const target = (filteredLines.length > 0) ? filteredLines[filteredLines.length - 1] : pendingAttrs;
            const attrVal = firstCell.replace(new RegExp(`^${firstWord}[\\s：:]*`), '').trim();
            
            if (firstWord === '用意') {
                if (target === pendingAttrs) target.purpose = (target.purpose ? target.purpose + '；' : '') + attrVal;
                else target._injectedPurpose = (target._injectedPurpose ? target._injectedPurpose + '；' : '') + attrVal;
            } else if (firstWord === '狀態') {
                if (attrVal.includes('已登記')) {
                    target._injectedStatus = '已登記';
                    target._injectedObtainedDate = target._injectedDate || currentBlockDate;
                    if (target === pendingAttrs) { target.status = '已登記'; target.obtained_date = currentBlockDate; }
                } else if (attrVal.includes('已求得')) {
                    target._injectedStatus = '已求得';
                    target._injectedObtainedDate = target._injectedDate || currentBlockDate;
                    if (target === pendingAttrs) { target.status = '已求得'; target.obtained_date = currentBlockDate; }
                } else {
                    if (target === pendingAttrs) target.status = attrVal;
                    else target._injectedStatus = attrVal;
                }
            } else if (['得知日期', '日期'].includes(firstWord)) {
                if (target === pendingAttrs) target.record_date = parseDateText(attrVal) || attrVal;
                else target._injectedDate = parseDateText(attrVal) || attrVal;
            } else if (['登記日期', '求得日期'].includes(firstWord)) {
                const d = parseDateText(attrVal) || attrVal;
                if (target === pendingAttrs) { target.obtained_date = d; target.status = (firstWord === '求得日期' ? '已求得' : '已登記'); }
                else { target._injectedObtainedDate = d; target._injectedStatus = (firstWord === '求得日期' ? '已求得' : '已登記'); }
            } else if (firstWord === '備註') {
                if (target === pendingAttrs) target.remarks = (target.remarks ? target.remarks + '\n' : '') + attrVal;
                else target._injectedRemarks = (target._injectedRemarks ? target._injectedRemarks + '\n' : '') + attrVal;
            }
            continue;
        }
        
        if (row.some(c => c.trim() !== '')) {
            const newRow = [...row];
            newRow._injectedDate = currentBlockDate;
            newRow._injectedMasterId = currentBlockMasterId;
            
            if (filteredLines.length === 0) {
                if (pendingAttrs.purpose) newRow._injectedPurpose = pendingAttrs.purpose;
                if (pendingAttrs.remarks) newRow._injectedRemarks = pendingAttrs.remarks;
                if (pendingAttrs.record_date) newRow._injectedDate = pendingAttrs.record_date;
                if (pendingAttrs.obtained_date) newRow._injectedObtainedDate = pendingAttrs.obtained_date;
                if (pendingAttrs.status) newRow._injectedStatus = pendingAttrs.status;
                pendingAttrs = { purpose: '', remarks: '', status: '', record_date: null, obtained_date: null };
            }
            
            filteredLines.push(newRow);
        }
    }

    const maxCols = Math.max(...filteredLines.map(r => r.length), 2);
    excelCols.value = Array.from({ length: maxCols }).map((_, i) => ({
        key: `c${i}`,
        label: i === 0 ? '法寶名稱' : (i === 1 ? '用意/數量' : `欄位 ${i + 1}`)
    }));

    excelRows.value = filteredLines.map(row => {
        const rowData = {};
        for (let i = 0; i < maxCols; i++) {
            rowData[`c${i}`] = row[i] || '';
        }
        if (row._injectedPurpose) rowData.c1 = row._injectedPurpose;
        rowData._record_date = row._injectedDate || currentBlockDate;
        rowData._obtained_date = row._injectedObtainedDate || row._injectedDate || currentBlockDate;
        rowData._master_id = row._injectedMasterId;
        rowData._status = row._injectedStatus;
        rowData._manualRemarks = row._injectedRemarks;
        return rowData;
    });

    // Guess Sum Key
    if (excelRows.value.length > 0 && !sumKey.value) {
        for (let i = maxCols - 1; i >= 0; i--) {
            const val = parseFloat(String(excelRows.value[0][`c${i}`]).replace(/[^\d.-]/g, ''));
            if (!isNaN(val)) {
                sumKey.value = `c${i}`;
                showTotal.value = true;
                break;
            }
        }
    }
};

const handleFileUpload = (e) => {
    const file = e.target.files[0];
    if (!file) return;
    
    const ext = file.name.split('.').pop().toLowerCase();
    const reader = new FileReader();

    if (['xlsx', 'xls', 'csv'].includes(ext)) {
        reader.onload = (event) => {
            try {
                const data = new Uint8Array(event.target.result);
                const workbook = window.XLSX.read(data, { type: 'array' });
                const firstSheet = workbook.Sheets[workbook.SheetNames[0]];
                const csv = window.XLSX.utils.sheet_to_csv(firstSheet);
                batchInput.value = csv;
                const lines = csv.split('\n').map(l => l.split(','));
                processBatchLines(lines);
            } catch (err) { alert('Excel 讀取失敗'); }
        };
        reader.readAsArrayBuffer(file);
    } else if (['docx', 'doc'].includes(ext)) {
        reader.onload = (event) => {
            if (ext === 'doc') { alert('不支援舊版 .doc，請另存為 .docx'); return; }
            window.mammoth.extractRawText({ arrayBuffer: event.target.result })
                .then(result => { 
                    batchInput.value = result.value; 
                    const lines = result.value.split('\n').filter(l => l.trim()).map(line => line.trim().split(/\s+/));
                    processBatchLines(lines);
                })
                .catch(err => { alert('Word 讀取失敗'); });
        };
        reader.readAsArrayBuffer(file);
    } else {
        reader.onload = (event) => {
            batchInput.value = event.target.result;
            const lines = event.target.result.split('\n').map(l => [l]);
            processBatchLines(lines);
        };
        reader.readAsText(file);
    }
};

const getMasterName = (id) => {
    const m = props.masters?.find(m => String(m.id) === String(id));
    return m ? m.name : '預設';
};

const handleDirectPaste = async () => {
    try {
        const text = await navigator.clipboard.readText();
        if (text) {
            batchInput.value = text;
        }
    } catch (err) {
        console.error('Failed to read clipboard:', err);
        alert('請手動在輸入框內點擊並使用 Ctrl+V 貼上');
    }
};

const handleSubmit = () => {
    if (localMode.value === 'single') {
        // 優先依換行切分，其次依兩個以上的空格或 Tab 切分，保留單個標點符號作為名稱的一部分
        const names = form.value.name?.split(/[\n\r]+|[\s\t]{2,}/).filter(n => n.trim()) || [];
        if (names.length === 0) {
            alert('請輸入法寶名稱');
            return;
        }

        names.forEach(n => {
            emit('saveSingle', { ...form.value, name: n.trim() });
        });
    } else {
        // Send the processed list to parent
        emit('saveBatch', { 
            input: batchInput.value, 
            masterId: form.value.master_id,
            rows: excelRows.value.map(row => {
                let remarksArr = [];
                if (row._manualRemarks) remarksArr.push(row._manualRemarks);
                
                // Add any other extra columns as remarks
                const extra = Object.keys(row)
                    .filter(k => !['c0', 'c1', '_record_date', '_obtained_date', '_master_id', '_status', '_manualRemarks', sumKey.value].includes(k))
                    .map(k => row[k])
                    .filter(v => v);
                if (extra.length > 0) remarksArr.push(extra.join(' '));

                return {
                    name: row.c0,
                    purpose: row.c1,
                    master_id: row._master_id || form.value.master_id,
                    record_date: row._record_date || form.value.record_date || '',
                    obtained_date: row._obtained_date || form.value.obtained_date || '',
                    status: row._status || form.value.status || '未求得',
                    count: sumKey.value ? parseFloat(String(row[sumKey.value] || '1').replace(/[^\d.-]/g, '')) : 1,
                    remarks: remarksArr.join('\n')
                };
            }),
            total: batchTotalValue.value
        });
    }
};
</script>

<style scoped>
.animate-slide-up { animation: slideUp 0.4s cubic-bezier(0.16, 1, 0.3, 1); }
.animate-fade-in { animation: fadeIn 0.3s ease-out; }
@keyframes slideUp { from { transform: translateY(100%); } to { transform: translateY(0); } }
@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }

.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
.no-scrollbar::-webkit-scrollbar { display: none; }
</style>
