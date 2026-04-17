<template>
    <div v-if="mode" class="fixed inset-0 z-[70] flex items-end md:items-center justify-center px-0">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm" @click="$emit('cancel')"></div>
        
        <!-- Form Container -->
        <div class="relative w-full h-full md:h-auto md:max-h-[95vh] md:max-w-md bg-white md:rounded-[32px] shadow-[0_-10px_40px_rgba(0,0,0,0.1)] overflow-hidden animate-slide-up flex flex-col">
            
            <!-- Header (From Screenshot) -->
            <div class="px-6 py-4 flex items-center justify-between bg-white border-b border-slate-50">
                <div class="flex items-center space-x-2">
                    <button @click="$emit('cancel')" class="text-indigo-500 font-medium flex items-center hover:opacity-70">
                        <span class="mr-1">&lt;</span>返回
                    </button>
                    <h3 class="text-[19px] font-bold text-black">
                        法寶登記 - {{ selectedMasterName || '請選擇仙師' }}
                    </h3>
                </div>
                <!-- Optional close X if needed, but screenshot shows back button -->
            </div>

            <!-- Scrollable Content -->
            <div class="flex-1 overflow-y-auto px-6 py-4 space-y-5 custom-scrollbar bg-white">
                
                <!-- TOP GRID: Date & Master -->
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="text-[14px] font-bold text-[#9ba7b9] block ml-1">日期</label>
                        <div class="relative">
                            <input v-model="form.record_date" type="date" class="w-full h-[40px] rounded-2xl border border-slate-100 bg-white px-3 text-[17px] font-bold text-black focus:ring-2 focus:ring-indigo-100 outline-none shadow-sm">
                        </div>
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-[14px] font-bold text-[#9ba7b9] block ml-1">仙師</label>
                        <select v-model="form.master_id" class="w-full h-[40px] rounded-2xl border border-slate-100 bg-white px-3 text-[17px] font-bold text-black shadow-sm focus:ring-2 focus:ring-indigo-100 outline-none">
                            <option v-for="m in masters" :key="m.id" :value="m.id">{{ m.name }}</option>
                        </select>
                    </div>
                </div>

                <!-- SINGLE FIELDS (Acting as defaults for Batch or for Single) -->
                <div class="space-y-4">
                    <div v-if="localMode === 'single'" class="space-y-1.5 animate-fade-in">
                        <label class="text-[14px] font-bold text-[#9ba7b9] block ml-1">法寶名稱</label>
                        <input v-model="form.name" type="text" list="treasure-list" placeholder="輸入或選擇法寶名稱" @input="onNameInput" class="w-full h-[40px] rounded-2xl border border-slate-100 bg-white px-3 text-[17px] font-bold text-black focus:ring-2 focus:ring-indigo-100 outline-none shadow-sm">
                        <datalist id="treasure-list">
                            <option v-for="t in treasureLibrary" :key="t.id" :value="t.name">{{ t.category }}</option>
                        </datalist>                    </div>
 
                    <div class="space-y-1.5">
                        <label class="text-[14px] font-bold text-[#9ba7b9] block ml-1">法寶用意</label>
                        <input v-model="form.purpose" type="text" placeholder="輸入法寶用意" class="w-full h-[40px] rounded-2xl border border-slate-100 bg-white px-3 text-[17px] font-bold text-black focus:ring-2 focus:ring-indigo-100 outline-none shadow-sm">
                    </div>
 
                    <div class="space-y-1.5">
                        <label class="text-[14px] font-bold text-[#9ba7b9] block ml-1">求寶方式</label>
                        <input v-model="form.acquisition_method" type="text" placeholder="輸入求寶方式" class="w-full h-[40px] rounded-2xl border border-slate-100 bg-white px-3 text-[17px] font-bold text-black focus:ring-2 focus:ring-indigo-100 outline-none shadow-sm">
                    </div>
 
                    <div class="space-y-1.5">
                        <label class="text-[14px] font-bold text-[#9ba7b9] block ml-1">備註</label>
                        <input v-model="form.remarks" type="text" placeholder="輸入備註 (選填)" class="w-full h-[40px] rounded-2xl border border-slate-100 bg-white px-3 text-[17px] font-bold text-black focus:ring-2 focus:ring-indigo-100 outline-none shadow-sm">
                    </div>
                </div>

                <!-- BATCH SECTION (Only if in batch mode) -->
                <div v-if="localMode === 'batch'" class="space-y-3 animate-fade-in">
                    <div class="flex items-center justify-between ml-1">
                        <label class="text-[14px] font-bold text-[#9ba7b9] block">貼上內容</label>
                        <div class="flex items-center space-x-3">
                            <button @click="$refs.fileInput.click()" class="text-indigo-500 font-bold text-[11px] hover:opacity-70">
                                下載他檔
                            </button>
                            <button 
                                @click="handleSubmit" 
                                :disabled="isSaving"
                                class="bg-indigo-50 text-indigo-600 font-bold text-[11px] py-1 px-3 rounded-xl shadow-sm hover:bg-indigo-100 active:scale-95 transition-all disabled:opacity-50"
                            >
                                {{ isSaving ? '稍候' : '確認一次新增' }}
                            </button>
                        </div>
                    </div>
                    <div class="relative group">
                        <textarea v-model="batchInput" rows="6" 
                            class="w-full rounded-2xl border border-slate-100 shadow-sm text-[17px] font-bold text-black bg-white focus:ring-2 focus:ring-indigo-100 p-4" 
                            placeholder="請加入清單內容..."></textarea>
                        <button v-if="batchInput" @click="batchInput = ''" 
                            class="absolute top-3 right-3 w-6 h-6 flex items-center justify-center rounded-full bg-slate-100 text-slate-400 hover:text-red-500 transition-all shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </button>
                    </div>
                    <input type="file" ref="fileInput" class="hidden" @change="handleFileUpload" accept=".txt,.xlsx,.xls,.doc,.docx">
                </div>
            </div>

            <!-- Footer Action (Restored for Single mode, but Hidden in Batch) -->
            <div v-if="localMode === 'single'" class="p-6 bg-white flex items-center justify-center border-t border-slate-100">
                <button 
                    @click="handleSubmit" 
                    :disabled="isSaving"
                    class="w-full bg-indigo-50 text-indigo-600 font-bold h-[45px] rounded-2xl shadow-sm hover:bg-indigo-100 active:scale-95 transition-all disabled:opacity-50"
                >
                    {{ isSaving ? '請稍候...' : '確認新增' }}
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, watch, computed, onMounted } from 'vue';
import axios from 'axios';

const props = defineProps({
    mode: String, // 'single' | 'batch' | null
    initialData: Object,
    masters: Array,
    isSaving: Boolean
});

const emit = defineEmits(['saveSingle', 'saveBatch', 'cancel', 'fileUpload']);

const localMode = ref(props.mode || 'single');
const form = ref({ ...props.initialData });
const batchInput = ref('');
const treasureLibrary = ref([]);

const fetchTreasureLibrary = async () => {
    try {
        const params = { master_id: form.value.master_id, type: 'registry' };
        const res = await axios.get('/api/treasures-list', { params });
        treasureLibrary.value = res.data;
    } catch (e) {
        console.error('Failed to load library', e);
    }
};

onMounted(fetchTreasureLibrary);

watch(() => form.value.master_id, () => {
    fetchTreasureLibrary();
});

const onNameInput = () => {
    // If the name matches a library item, auto-fill the purpose if it's empty
    const match = treasureLibrary.value.find(t => t.name === form.value.name);
    if (match && !form.value.purpose) {
        form.value.purpose = match.category;
    }
};

const selectedMasterName = computed(() => {
    const m = props.masters?.find(m => String(m.id) === String(form.value.master_id));
    return m ? m.name : '';
});

watch(() => props.initialData, (newVal) => {
    form.value = { ...newVal };
}, { deep: true });

watch(() => props.mode, (newVal) => {
    if (newVal) localMode.value = newVal;
});

const handleSubmit = () => {
    if (localMode.value === 'single') {
        emit('saveSingle', form.value);
    } else {
        emit('saveBatch', { 
            input: batchInput.value, 
            masterId: form.value.master_id,
            date: form.value.record_date,
            defaults: {
                purpose: form.value.purpose,
                acquisition_method: form.value.acquisition_method,
                remarks: form.value.remarks
            }
        });
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
                batchInput.value = window.XLSX.utils.sheet_to_csv(firstSheet);
            } catch (err) { alert('Excel 讀取失敗'); }
        };
        reader.readAsArrayBuffer(file);
    } else if (['docx', 'doc'].includes(ext)) {
        reader.onload = (event) => {
            if (ext === 'doc') { alert('不支援舊版 .doc，請另存為 .docx'); return; }
            window.mammoth.extractRawText({ arrayBuffer: event.target.result })
                .then(result => { batchInput.value = result.value; })
                .catch(err => { alert('Word 讀取失敗'); });
        };
        reader.readAsArrayBuffer(file);
    } else {
        reader.onload = (event) => {
            batchInput.value = event.target.result;
        };
        reader.readAsText(file);
    }
};
</script>

<style scoped>
.animate-slide-up { animation: slideUp 0.4s cubic-bezier(0.16, 1, 0.3, 1); }
.animate-fade-in { animation: fadeIn 0.3s ease-out; }
@keyframes slideUp { from { transform: translateY(100%); } to { transform: translateY(0); } }
@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }

.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
</style>
