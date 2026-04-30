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
                        <input v-model="form.purpose" type="text" placeholder="輸入法寶用途..." style="font-size: 17px;" class="w-full py-[5px] rounded-xl bg-white px-3 font-bold text-slate-900 border border-slate-400 focus:ring-2 focus:ring-indigo-500/20 outline-none placeholder:text-slate-300">
                    </div>

                    <!-- Personnel Management Section -->
                    <div class="space-y-6 pt-4 border-t border-slate-100">
                        <div class="flex items-center justify-between ml-1">
                            <div class="flex items-center space-x-3">
                                <label class="text-[15px] font-bold text-red-600 uppercase tracking-tight">承接皇恩的師兄姐</label>
                                <label class="flex items-center space-x-1.5 cursor-pointer bg-slate-50 px-2 py-1 rounded-lg border border-slate-200 active:scale-95 transition-all">
                                    <input type="checkbox" v-model="isMulti" class="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                    <span class="text-[13px] font-black text-slate-600">多人承接</span>
                                </label>
                            </div>
                            <button @click="handleAddPersonnelClick" class="px-3 py-1 bg-indigo-600 text-white rounded-lg text-[12px] font-bold active:scale-95 transition-all shadow-sm" style="color: white !important;">
                                ＋ 新增人員
                            </button>
                        </div>

                        <div v-for="(p, idx) in personnel" :key="idx" class="p-4 bg-slate-50/30 rounded-2xl border border-slate-400 space-y-3 relative group animate-fade-in">
                            <div class="absolute top-2 right-2 flex items-center space-x-1">
                                <button v-if="personnel.length > 1" @click="movePersonnel(idx, -1)" :disabled="idx === 0" class="p-1 text-slate-300 hover:text-indigo-500 disabled:opacity-10 transition-all active:scale-90">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 15l7-7 7 7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </button>
                                <button v-if="personnel.length > 1" @click="movePersonnel(idx, 1)" :disabled="idx === personnel.length - 1" class="p-1 text-slate-300 hover:text-indigo-500 disabled:opacity-10 transition-all active:scale-90">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </button>
                                <button @click="removePersonnelRow(idx)" class="ml-1 text-slate-300 hover:text-red-500 transition-colors p-1">✕</button>
                            </div>
                            
                            <!-- Multi-mode Fields -->
                            <div v-if="isMulti" class="space-y-3">
                                <div class="grid grid-cols-2 gap-2">
                                    <div class="space-y-1">
                                        <label class="text-[11px] text-red-400 ml-1 font-bold">法號</label>
                                        <input v-model="p.custom_name" type="text" placeholder="法號" list="dharma-names"
                                            @keydown.enter.prevent="handlePersonnelEnter(idx)"
                                            class="personnel-name-input w-full py-[10px] rounded-xl border border-slate-400 bg-white px-3 text-[18px] font-bold text-slate-900 focus:ring-2 focus:ring-indigo-100 outline-none font-outfit">
                                    </div>
                                    <div class="space-y-1">
                                        <label class="text-[11px] text-red-400 ml-1 font-bold">日期</label>
                                        <div class="relative w-full py-[10px] rounded-xl border border-slate-400 bg-white flex items-center">
                                            <input v-model="p.obtained_date" placeholder="年/月/日" class="w-full h-full bg-transparent px-2 text-[14px] font-bold font-outfit text-slate-900 outline-none uppercase">
                                            <button @click="activeDate = { idx, field: 'obtained_date', title: (p.custom_name || '師兄姐') + '取得日期' }" class="absolute right-2 text-slate-300 hover:text-indigo-500 transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2v12a2 2 0 002 2z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <div class="space-y-1">
                                        <label class="text-[11px] text-red-400 ml-1 font-bold">狀態</label>
                                        <select v-model="p.status" class="w-full py-[10px] rounded-xl border border-slate-400 bg-white px-3 text-[15px] font-bold focus:ring-2 focus:ring-indigo-100 outline-none"
                                            :style="p.status === '未求得' ? 'color: #dc2626 !important;' : (p.status === '已求得' ? 'color: #2563eb !important;' : 'color: #059669 !important;')">
                                            <option value="未求得">未求得</option>
                                            <option value="已求得">已求得</option>
                                            <option value="已登記">已登記</option>
                                        </select>
                                    </div>
                                    <div class="space-y-1">
                                        <label class="text-[11px] text-slate-400 ml-1 font-bold">個人備註</label>
                                        <input v-model="p.remarks" placeholder="輸入個人備註..." class="w-full py-[10px] rounded-xl border border-slate-400 bg-white px-3 text-[15px] font-normal text-slate-900 focus:ring-2 focus:ring-indigo-100 outline-none">
                                    </div>
                                </div>
                            </div>

                            <!-- Single-mode Fields -->
                            <div v-else class="grid grid-cols-3 gap-2">
                                <div class="space-y-1">
                                    <label class="text-[11px] text-red-400 ml-1 font-bold">法號</label>
                                    <input v-model="p.custom_name" type="text" placeholder="法號" list="dharma-names"
                                        @keydown.enter.prevent="handlePersonnelEnter(idx)"
                                        class="personnel-name-input w-full py-[10px] rounded-xl border border-slate-400 bg-white px-3 text-[18px] font-bold text-slate-900 focus:ring-2 focus:ring-indigo-100 outline-none font-outfit">
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[11px] text-red-400 ml-1 font-bold">親友</label>
                                    <input v-model="p.relationship" type="text" placeholder="親友"
                                        class="w-full py-[10px] rounded-xl border border-slate-400 bg-white px-3 text-[16px] font-normal text-slate-900 focus:ring-2 focus:ring-indigo-100 outline-none">
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[11px] text-red-400 ml-1 font-bold">日期</label>
                                    <div class="relative w-full py-[10px] rounded-xl border border-slate-400 bg-white flex items-center">
                                        <input v-model="p.obtained_date" placeholder="年/月/日" class="w-full h-full bg-transparent px-2 text-[14px] font-bold font-outfit text-slate-900 outline-none uppercase">
                                        <button @click="activeDate = { idx, field: 'obtained_date', title: (p.custom_name || '師兄姐') + '取得日期' }" class="absolute right-2 text-slate-300 hover:text-indigo-500 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2v12a2 2 0 002 2z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div v-if="personnel.length === 0" class="text-center py-6 bg-slate-50/30 rounded-2xl border border-dashed border-slate-400 text-slate-300 text-[13px]">
                            尚無人員紀錄
                        </div>
                    </div>

                    <!-- Single Person Only Fields (Status & Remarks) -->
                    <div v-if="!isMulti" class="grid grid-cols-2 gap-[2.5px] animate-fade-in border-t border-slate-100 pt-4">
                        <div class="space-y-1 py-[5px]">
                            <label class="text-[15px] font-black text-slate-400 uppercase tracking-widest block ml-1">目前狀態</label>
                            <select v-model="form.status" @change="handleStatusChange" style="font-size: 17px;" class="w-full py-[5px] rounded-xl bg-white px-3 font-bold border border-slate-400 focus:ring-2 focus:ring-indigo-500/20 outline-none"
                                :style="form.status === '未求得' ? 'color: #dc2626 !important;' : (form.status === '已求得' ? 'color: #2563eb !important;' : 'color: #059669 !important;')">
                                <option value="未求得">未求得</option>
                                <option value="已求得">已求得</option>
                                <option value="已登記">已登記</option>
                            </select>
                        </div>
                        <div v-if="form.status !== '已登記'" class="space-y-1 py-[5px]">
                            <label class="text-[15px] font-black text-slate-400 uppercase tracking-widest block ml-1">求得日期</label>
                            <div class="relative flex items-center">
                                <input v-model="form.obtained_date" type="text" :placeholder="form.status === '未求得' ? '-' : '年/月/日'" 
                                    :disabled="form.status === '未求得'"
                                    :class="form.status === '未求得' ? 'opacity-30' : ''"
                                    class="w-full py-[5px] rounded-xl bg-white pl-2 pr-7 border border-slate-400 text-[15px] font-bold text-slate-900">
                                <button @click="form.status !== '未求得' && (activeDate = 'obtained_date')" class="absolute right-2 text-slate-400 transition-colors p-1">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </button>
                            </div>
                        </div>
                        <div class="col-span-2 space-y-1 py-[5px]">
                            <label class="text-[15px] font-black text-slate-400 uppercase tracking-widest block ml-1">詳細內容 / 備註</label>
                            <textarea v-model="form.remarks" rows="1" placeholder="輸入更多說明內容..." style="font-size: 17px;" class="w-full py-[5px] rounded-xl bg-white p-2 font-bold text-slate-900 border border-slate-400 focus:ring-2 focus:ring-indigo-500/20 outline-none"></textarea>
                        </div>
                    </div>
                </div>

                <!-- BATCH MODE -->
                <div v-if="localMode === 'batch'" class="space-y-4 animate-fade-in">
                    <!-- (Batch input logic remains same) -->
                    <div class="bg-white rounded-2xl p-4 space-y-3 shadow-inner relative border border-slate-100">
                        <div class="flex items-center justify-between mb-1">
                            <label class="text-[15px] font-black text-slate-400 uppercase tracking-widest ml-1">貼入清單內容</label>
                            <div class="flex items-center space-x-3">
                                <button v-if="batchInput" @click="batchInput = ''" class="text-[11px] text-red-500 hover:underline">清除內容</button>
                                <button @click="handleDirectPaste" class="text-[11px] text-emerald-600 font-bold px-2 py-[5px]">貼上內容</button>
                                <button @click="$refs.fileInput.click()" class="text-[11px] text-indigo-600 px-2 py-[5px]">匯入檔案</button>
                            </div>
                        </div>
                        <textarea v-model="batchInput" rows="8" class="w-full rounded-xl border border-slate-400 p-4 font-mono text-[14px]" placeholder="支援直接貼上內容..."></textarea>
                        <input type="file" ref="fileInput" class="hidden" @change="handleFileUpload" accept=".txt,.xlsx,.xls,.docx">
                    </div>

                    <div v-if="excelRows.length > 0" class="border border-slate-100 rounded-2xl overflow-hidden shadow-sm bg-white">
                        <div class="bg-white px-4 py-3 border-b border-slate-100 flex justify-between items-center">
                            <span class="text-[15px] font-black text-indigo-600">偵測到 {{ excelRows.length }} 筆資料</span>
                        </div>
                        <div class="max-h-48 overflow-y-auto no-scrollbar">
                            <table class="w-full text-[14px]">
                                <thead class="bg-slate-50 sticky top-0">
                                    <tr>
                                        <th class="p-2 font-black">仙師</th>
                                        <th v-for="col in excelCols" :key="col.key" class="p-2 font-black">{{ col.label }}</th>
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
            <div class="fixed bottom-[7vh] left-0 right-0 px-4 py-4 bg-white/95 backdrop-blur-md border-t border-slate-50 z-[10]">
                <button @click="handleSubmit" :disabled="isSaving || (localMode === 'batch' && excelRows.length === 0)"
                    class="w-full bg-indigo-600 py-[10px] rounded-2xl text-white font-bold text-[20px] active:scale-95 transition-all disabled:bg-slate-300">
                    {{ isSaving ? '處理中...' : (localMode === 'single' ? '確認資料載錄' : `開始載錄這 ${excelRows.length} 筆資料`) }}
                </button>
            </div>

            <mobile-navbar :can-back="false" @home="$emit('cancel')" :show-action="false" :can-search="false" />
            
            <datalist id="dharma-names">
                <option v-for="dn in dharmaNames" :key="dn.id" :value="dn.name" />
            </datalist>
        </div>

        <compact-date-picker v-if="activeDate && typeof activeDate === 'string'" v-model="form[activeDate]" title="選擇日期" @close="activeDate = null" />
        <compact-date-picker v-if="activeDate && typeof activeDate === 'object'" v-model="personnel[activeDate.idx][activeDate.field]" :title="activeDate.title" @close="activeDate = null" />
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

const emit = defineEmits(['saveSingle', 'saveBatch', 'cancel']);
const activeDate = ref(null);
const localMode = ref(props.mode || 'single');
const form = ref({ ...props.initialData });
const batchInput = ref('');
const excelRows = ref([]);
const excelCols = ref([]);
const personnel = ref([]);
const dharmaNames = ref([]);
const isMulti = ref(false);

const fetchDharmaNames = async () => {
    try {
        const res = await axios.get('/api/dharma-names-list');
        dharmaNames.value = res.data;
    } catch (e) {}
};

const addPersonnelRow = () => {
    personnel.value.push({ custom_name: '', relationship: '', obtained_date: '', status: '已求得', remarks: '' });
};

const handleAddPersonnelClick = () => {
    if (!isMulti.value) {
        if (confirm('是否開啟多人承接介面？')) { isMulti.value = true; addPersonnelRow(); }
    } else { addPersonnelRow(); }
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
    if (personnel.value.length === 0) addPersonnelRow();
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
        isMulti.value = !!newVal.is_multi;
    }
}, { immediate: true });

const handlePersonnelEnter = (idx) => {
    if (idx === personnel.value.length - 1) addPersonnelRow();
    nextTick(() => {
        const inputs = document.querySelectorAll('.personnel-name-input');
        if (inputs[idx + 1]) inputs[idx + 1].focus();
    });
};

const handleStatusChange = () => {
    if (form.value.status === '未求得') form.value.obtained_date = '';
    else if (!form.value.obtained_date) form.value.obtained_date = new Date().toISOString().split('T')[0];
};

const handleSubmit = () => {
    if (localMode.value === 'single') {
        if (!form.value.name?.trim()) { alert('請輸入法寶名稱'); return; }
        const cleaned = personnel.value.filter(p => p.custom_name?.trim());
        emit('saveSingle', { 
            ...form.value,
            is_multi: isMulti.value,
            dharma_name_registries: cleaned.map(p => ({
                ...p,
                status: isMulti.value ? p.status : (form.value.status || '已求得'),
                remarks: isMulti.value ? p.remarks : (form.value.remarks || ''),
                obtained_date: isMulti.value ? p.obtained_date : (form.value.obtained_date || ''),
                related_personnel: p.relationship ? p.relationship.split(/[、, ]+/).filter(x => x) : []
            }))
        });
    } else {
        emit('saveBatch', { 
            input: batchInput.value, 
            masterId: form.value.master_id,
            rows: excelRows.value.map(row => ({
                name: row.c0, purpose: row.c1, master_id: row._master_id || form.value.master_id,
                record_date: row._record_date || '', obtained_date: row._obtained_date || '',
                status: row._status || '未求得', count: 1, remarks: row._manualRemarks || ''
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
