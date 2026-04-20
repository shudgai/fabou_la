<template>
    <div v-if="mode" class="fixed inset-0 z-[70] flex items-end md:items-center justify-center px-0">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm" @click="$emit('cancel')"></div>
        
        <!-- Form Container -->
        <div class="relative w-full h-[100dvh] md:max-w-4xl bg-white shadow-[0_-10px_40px_rgba(0,0,0,0.1)] overflow-hidden animate-slide-up flex flex-col">
            <!-- Header -->
            <div class="p-[10px] border-b border-slate-100 flex items-center justify-between bg-white sticky top-0 z-10">
                <h3 class="text-[24px] font-black text-slate-900 leading-tight">
                    重大皇恩載錄 <span v-if="selectedMasterName" class="text-indigo-600 ml-1">- {{ selectedMasterName }}</span>
                </h3>
                <button @click="$emit('cancel')" class="p-2 text-black hover:text-slate-600 active:scale-95">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
            </div>

            <!-- Tabs -->
            <div class="px-4 py-[2.5px] bg-white flex space-x-1 border-b border-slate-100 mt-[-5px]">
                <button @click="localMode = 'single'" 
                    :class="localMode === 'single' ? 'bg-white shadow-sm text-indigo-600' : 'text-slate-400'"
                    class="flex-1 py-2 text-sm rounded-xl transition-all">逐筆登錄</button>
                <button @click="localMode = 'batch'" 
                    :class="localMode === 'batch' ? 'bg-white shadow-sm text-indigo-600' : 'text-slate-400'"
                    class="flex-1 py-2 text-sm rounded-xl transition-all">文字/EXCEL 記載</button>
            </div>

            <!-- Scrollable Content -->
            <div class="flex-1 overflow-y-auto p-2 space-y-1 custom-scrollbar">
                
                <!-- COMMON FIELDS (Date & Master) -->
                <div class="grid grid-cols-2 gap-[2.5px] bg-white p-[5px] mt-[-10px]">
                    <div class="space-y-1">
                        <label class="text-[15px] font-black text-slate-400 uppercase tracking-widest block ml-1">得知日期</label>
                        <div @click="activeDate = 'record_date'" 
                            class="w-full h-[36px] rounded-xl bg-white px-3 flex items-center justify-between cursor-pointer shadow-sm">
                            <span :class="form.record_date ? 'text-slate-900 font-bold' : 'text-slate-400'" style="font-size: 17px;">
                                {{ form.record_date || '選擇日期' }}
                            </span>
                            <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </div>
                    </div>
                    <div class="space-y-1">
                        <label class="text-[15px] font-black text-slate-400 uppercase tracking-widest block ml-1">載錄目標仙師</label>
                        <select v-model="form.master_id" style="font-size: 17px;" class="w-full h-[36px] rounded-xl bg-white px-3 font-bold text-slate-900 shadow-sm focus:ring-2 focus:ring-indigo-500/20 outline-none">
                            <option v-for="m in masters" :key="m.id" :value="m.id">{{ m.name }}</option>
                        </select>
                    </div>
                </div>

                <!-- SINGLE MODE -->
                <div v-if="localMode === 'single'" class="space-y-1 mt-[-8px] animate-fade-in">
                    <div class="space-y-1">
                        <label class="text-[15px] font-black text-slate-400 uppercase tracking-widest block ml-1">法寶名稱</label>
                        <input v-model="form.name" type="text" placeholder="輸入法寶名稱..." style="font-size: 17px;" class="w-full h-[38px] rounded-xl bg-white px-3 font-bold text-slate-900 focus:ring-2 focus:ring-indigo-500/20 outline-none placeholder:text-slate-300">
                    </div>

                    <div class="space-y-1">
                        <label class="text-[15px] font-black text-slate-400 uppercase tracking-widest block ml-1">法寶用意</label>
                        <input v-model="form.purpose" type="text" placeholder="輸入法寶用途..." style="font-size: 17px;" class="w-full h-[38px] rounded-xl bg-white px-3 font-bold text-slate-900 focus:ring-2 focus:ring-indigo-500/20 outline-none placeholder:text-slate-300">
                    </div>

                    <div class="grid grid-cols-2 gap-[2.5px]">
                        <div class="space-y-1">
                            <label class="text-[15px] font-black text-slate-400 uppercase tracking-widest block ml-1">求得日期</label>
                            <div @click="form.status !== '未求得' && (activeDate = 'obtained_date')" 
                                :class="form.status === '未求得' ? 'opacity-30 cursor-not-allowed' : 'cursor-pointer'"
                                class="w-full h-[38px] rounded-xl bg-white px-3 flex items-center justify-between shadow-sm border-none">
                                <span :class="form.obtained_date ? 'text-slate-900 font-bold' : 'text-slate-400'" style="font-size: 17px;">
                                    {{ form.obtained_date || (form.status === '未求得' ? '-' : '選擇日期') }}
                                </span>
                                <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </div>
                        </div>
                        <div class="space-y-1">
                            <label class="text-[15px] font-black text-slate-400 uppercase tracking-widest block ml-1">目前狀態</label>
                            <select v-model="form.status" @change="handleStatusChange" style="font-size: 17px;" class="w-full h-[38px] rounded-xl bg-white px-3 font-bold focus:ring-2 focus:ring-indigo-500/20 outline-none placeholder:text-slate-300"
                                :class="form.status === '未求得' ? 'text-red-500' : 'text-emerald-600'">
                                <option value="未求得">未求得</option>
                                <option value="已求得">已求得</option>
                                <option value="已登記">已登記</option>
                            </select>
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label class="text-[15px] font-black text-slate-400 uppercase tracking-widest block ml-1">詳細內容 / 備註</label>
                        <textarea v-model="form.remarks" rows="1" placeholder="輸入更多說明內容..." style="font-size: 17px;" class="w-full h-[40px] rounded-xl bg-white p-2 font-bold text-slate-900 focus:ring-2 focus:ring-indigo-500/20 outline-none leading-normal placeholder:text-slate-300"></textarea>
                    </div>
                </div>

                <!-- BATCH MODE -->
                <div v-if="localMode === 'batch'" class="space-y-4 animate-fade-in">
                    <div class="bg-white rounded-2xl p-4 space-y-3 shadow-inner relative border border-slate-100">
                        <div class="flex items-center justify-between mb-1">
                            <label class="text-[15px] font-black text-slate-400 uppercase tracking-widest ml-1">貼入清單內容</label>
                            <div class="flex items-center space-x-3">
                                <button v-if="batchInput" @click="batchInput = ''" class="text-[11px] text-red-500 hover:underline">清除內容</button>
                                <button @click="$refs.fileInput.click()" class="text-[11px] text-indigo-600 flex items-center hover:bg-white px-2 py-1 rounded-lg transition-all">
                                    匯入檔案 (Excel/Word)
                                </button>
                            </div>
                        </div>
                        
                        <div class="relative group">
                            <textarea v-model="batchInput" rows="8" 
                                @paste="handleBatchPaste"
                                class="w-full rounded-xl border-none shadow-sm text-[14px] bg-white focus:ring-2 focus:ring-indigo-500/20 p-4 font-mono leading-relaxed pr-10 placeholder:text-slate-300" 
                                placeholder="支援直接貼上 Excel 或 LINE 內容..."></textarea>
                            <button v-if="batchInput" @click="batchInput = ''" 
                                class="absolute top-3 right-3 w-6 h-6 flex items-center justify-center rounded-full bg-slate-100 text-slate-400 hover:bg-red-50 hover:text-red-500 transition-all shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </button>
                        </div>
                        <input type="file" ref="fileInput" class="hidden" @change="handleFileUpload" accept=".txt,.xlsx,.xls,.docx">
                    </div>

                    <!-- Batch Preview Table -->
                        <div v-if="excelRows.length > 0" class="border border-slate-100 rounded-2xl overflow-hidden shadow-sm bg-white animate-fade-in">
                        <div class="bg-white px-4 py-2 border-b border-slate-100 flex justify-between items-center">
                            <span class="text-[15px] font-black text-indigo-600">偵測到 {{ excelRows.length }} 筆資料</span>
                            <div class="flex items-center space-x-4">
                                <label class="flex items-center space-x-2 cursor-pointer">
                                    <input type="checkbox" v-model="showTotal" class="rounded text-indigo-600 focus:ring-indigo-500">
                                    <span class="text-[15px] font-black text-indigo-600">統計加總</span>
                                </label>
                                <select v-if="showTotal" v-model="sumKey" class="text-[15px] font-black bg-white border-none rounded px-2 py-0.5 outline-none focus:ring-0">
                                    <option value="">選擇統計欄</option>
                                    <option v-for="(col, i) in excelCols" :key="i" :value="col.key">第 {{ i + 1 }} 欄 ({{ col.label }})</option>
                                </select>
                            </div>
                        </div>
                        <div class="max-h-48 overflow-y-auto custom-scrollbar no-scrollbar">
                            <table class="w-full text-[15px] text-left">
                                <thead class="bg-white text-slate-500 sticky top-0 uppercase tracking-tighter border-b border-slate-100">
                                    <tr>
                                        <th v-for="col in excelCols" :key="col.key" class="p-2 border-b border-slate-100">{{ col.label }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(row, idx) in excelRows" :key="idx" class="border-b border-slate-50 last:border-0 hover:bg-white transition-colors">
                                        <td v-for="col in excelCols" :key="col.key" class="p-2 text-slate-600 font-bold truncate max-w-[120px]">{{ row[col.key] }}</td>
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

            <!-- Footer Action -->
            <div class="p-4 pb-10 bg-white border-t border-slate-50">
                <button 
                    @click="handleSubmit" 
                    :disabled="isSaving || (localMode === 'batch' && excelRows.length === 0)"
                    class="w-full bg-indigo-600 text-white h-[44px] rounded-2xl shadow-xl shadow-indigo-100 hover:bg-indigo-700 active:scale-[0.98] transition-all disabled:bg-slate-300 disabled:shadow-none"
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
        if (newVal === 'batch') form.value.record_date = '';
    }
});

watch(localMode, (newVal) => {
    if (newVal === 'batch') {
        form.value.record_date = '';
    }
});

const handleStatusChange = () => {
    if (form.value.status === '未求得') form.value.obtained_date = '';
};

const handleBatchPaste = (e) => {
    const pasteData = e.clipboardData.getData('text');
    if (pasteData && pasteData.trim()) {
        const lines = pasteData.trim().split(/\r?\n/).map(line => line.split('\t').map(c => c.trim()));
        processBatchLines(lines);
    }
};

const processBatchLines = (rawLines) => {
    if (!rawLines || rawLines.length === 0) return;
    
    // Auto-detect columns
    const maxCols = Math.max(...rawLines.map(r => r.length));
    excelCols.value = Array.from({ length: maxCols }).map((_, i) => ({
        key: `c${i}`,
        label: i === 0 ? '法門/法號' : (i === 1 ? '用意/數量' : `欄位 ${i + 1}`)
    }));

    excelRows.value = rawLines.map(row => {
        const rowData = {};
        for (let i = 0; i < maxCols; i++) {
            rowData[`c${i}`] = row[i] || '';
        }
        return rowData;
    }).filter(r => Object.values(r).some(v => v));

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

const handleSubmit = () => {
    if (localMode.value === 'single') {
        emit('saveSingle', form.value);
    } else {
        // Send the processed list to parent
        emit('saveBatch', { 
            input: batchInput.value, 
            masterId: form.value.master_id,
            rows: excelRows.value.map(row => ({
                name: row.c0,
                purpose: row.c1,
                count: sumKey.value ? parseFloat(String(row[sumKey.value] || '1').replace(/[^\d.-]/g, '')) : 1,
                remarks: Object.keys(row).filter(k => !['c0', 'c1', sumKey.value].includes(k)).map(k => row[k]).join(' ')
            })),
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
