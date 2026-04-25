<template>
    <div v-if="show" class="fixed inset-0 z-[100] flex flex-col bg-white animate-fade-in">
        <!-- Header -->
        <div class="h-[50px] border-b border-slate-100 flex items-center justify-between px-4 shrink-0 bg-white">
            <button @click="$emit('cancel', false)" class="text-slate-500 text-[14px] font-normal">取消</button>
            <h3 class="text-[19px] font-normal text-slate-800">多筆新增：{{ armyType }}</h3>
            <div class="w-10"></div> <!-- Spacer to keep title centered -->
        </div>

        <!-- Options Container -->
        <div class="p-4 bg-slate-50/50 space-y-4 shrink-0">
            <!-- Date Picker, Excel Import & Save -->
            <div class="flex items-center space-x-2">
                <!-- Date Box (Entirely Clickable) -->
                <div @click="showDatePicker = true" class="flex-1 flex items-center bg-white p-3 rounded-2xl border border-slate-100 shadow-sm cursor-pointer active:bg-slate-50 transition-all">
                    <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600 mr-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </div>
                    <div>
                        <p class="text-[13px] text-[#aeb4be] font-normal uppercase tracking-wider">錄入日期</p>
                    </div>
                </div>
                
                <!-- Import Button -->
                <button @click="$refs.fileInput.click()" class="bg-emerald-50 border border-emerald-100 text-emerald-600 px-3 h-14 rounded-2xl flex flex-col items-center justify-center active:scale-95 transition-all">
                    <svg class="w-5 h-5 mb-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/></svg>
                    <span class="text-[11px] font-bold">匯入他檔</span>
                </button>
                <input type="file" ref="fileInput" @change="handleFileImport" accept=".xlsx, .xls" class="hidden">

                <!-- Save Button (Moved from Header) -->
                <button @click="handleBatchSave" :disabled="parsedItems.length === 0 || processing" 
                    :class="[
                        'px-4 h-14 rounded-2xl flex flex-col items-center justify-center active:scale-95 transition-all shadow-lg',
                        parsedItems.length === 0 ? 'bg-indigo-400 cursor-not-allowed' : 'bg-indigo-600 shadow-indigo-100'
                    ]"
                    style="color: white !important; opacity: 1 !important;">
                    <svg class="w-5 h-5 mb-0.5" style="color: white !important;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    <span class="text-[11px] font-bold" style="color: white !important;">{{ processing ? '存取中' : `儲存 (${parsedItems.length})` }}</span>
                </button>
            </div>
        </div>

        <!-- Input Area -->
        <div class="flex-1 flex flex-col p-4">
            <textarea 
                v-model="batchText" 
                placeholder="請貼上日期、法號、數量"
                class="flex-1 w-full bg-slate-50 rounded-[28px] border-none p-6 text-[16px] leading-relaxed focus:ring-2 focus:ring-indigo-100 outline-none resize-none font-medium shadow-inner"
            ></textarea>

            <!-- Preview Counter -->
            <div v-if="parsedItems.length > 0" class="mt-4 p-4 bg-indigo-50/30 rounded-2xl border border-indigo-100/50">
                <p class="text-[13px] text-[#aeb4be] font-normal">預計匯入：{{ parsedItems.length }} 筆資料</p>
                <div class="mt-2 flex flex-wrap gap-2 overflow-y-auto max-h-[60px]">
                    <span v-for="(item, idx) in parsedItems.slice(0, 15)" :key="idx" class="text-[11px] bg-white px-2 py-0.5 rounded-full border border-indigo-100 text-slate-600">
                        [{{ item.know_date?.replace(/-/g, '/') }}] {{ item.user_name }} ({{ item.quantity }})
                    </span>
                    <span v-if="parsedItems.length > 10" class="text-[11px] text-slate-400 self-center">...及其他 {{ parsedItems.length - 10 }} 筆</span>
                </div>
            </div>
        </div>
        <!-- MODALS -->
        <compact-date-picker v-if="showDatePicker" v-model="batchDate" @close="showDatePicker = false" />

        <!-- Global Mobile Navbar -->
        <mobile-navbar 
            @back="$emit('cancel', false)"
            @home="$emit('cancel', false)"
            :show-action="false"
            :can-search="false"
        />
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import axios from 'axios';
import CompactDatePicker from './CompactDatePicker.vue';
import MobileNavbar from './MobileNavbar.vue';

const props = defineProps({
    show: Boolean,
    armyType: String
});

const emit = defineEmits(['save', 'cancel']);

const batchDate = ref(new Date().toISOString().split('T')[0]);
const batchText = ref('');
const processing = ref(false);
const showDatePicker = ref(false);

const parsedItems = computed(() => {
    if (!batchText.value.trim()) return [];
    
    const lines = batchText.value.split('\n');
    let lastDate = batchDate.value; // Fallback to UI date
    
    const results = [];
    
    lines.forEach(line => {
        const trimmed = line.trim();
        if (!trimmed) return;

        // NEW: Standalone Year detection (e.g., "113年" or "2024")
        const standaloneYMatch = trimmed.match(/^\s*(\d{2,4})\s*[年]?\s*$/);
        if (standaloneYMatch) {
            let y = parseInt(standaloneYMatch[1]);
            if (y < 1000) y += 1911;
            lastDate = `${y}-01-01`; // Anchor to start of year
            return;
        }

        // Split by Tab (common in Excel copy), Multiple spaces, or Comma
        let parts = trimmed.split(/[\t]+|[\s]{2,}|[,，]/);
        if (parts.length <= 1) {
            parts = trimmed.split(/\s+/);
        }
        
        let possibleDate = parts[0];
        let dateFound = null;

        // NEW: Flexible Date Detection (Supports any ROC or CE year)
        const cleanDate = possibleDate.replace(/[年月]/g, '-').replace(/[日]/g, '');
        const dateParts = cleanDate.split(/[.\/-]/).map(p => p.trim());

        if (dateParts.length === 3) {
            let y = parseInt(dateParts[0]);
            if (y < 1000) y += 1911;
            dateFound = `${y}-${dateParts[1].padStart(2, '0')}-${dateParts[2].padStart(2, '0')}`;
            parts.shift();
        } else if (dateParts.length === 2) {
            // Short date format (M/D)
            const y = lastDate.split('-')[0];
            dateFound = `${y}-${dateParts[0].padStart(2, '0')}-${dateParts[1].padStart(2, '0')}`;
            parts.shift();
        }

        // If a new date is found, update lastDate
        if (dateFound) {
            lastDate = dateFound;
        }

        // The remaining parts are [DharmaName, Quantity]
        if (parts.length > 0) {
            const name = parts[0].trim();
            if (!name) return;

            // Header & Summary Filter: Skip if name contains title or summary keywords
            const skipKeywords = ['法號', '日期', '數量', '備註', '處理', '項次', '結果', '總結', '總計', '總量', '小計'];
            if (skipKeywords.some(key => name.includes(key))) return;

            let qty = parts.length > 1 ? parseInt(parts[1].replace(/[^0-9]/g, '')) : 1;
            if (isNaN(qty)) qty = 1;

            const item = {
                user_name: name,
                quantity: qty,
                know_date: lastDate,
                army_type: props.armyType,
                destination: '未處理',
                user_remarks: '',
                remarks_text: ''
            };
            
            // Specialized field handling
            if (props.armyType === '黑曜軍') {
                item.yan_zun = Math.ceil(qty / 2);
                item.yan_an = Math.floor(qty / 2);
            } else if (props.armyType === '耀紫軍') {
                item.long_sheng = Math.ceil(qty / 2);
                item.long_zhan = Math.floor(qty / 2);
            } else if (props.armyType === '虎甲軍') {
                item.yan_jue = Math.ceil(qty / 2);
                item.yan_ze = Math.floor(qty / 2);
            } else if (props.armyType === '虎賁軍') {
                item.yan_di = Math.ceil(qty / 2);
                item.yan_yuan = Math.floor(qty / 2);
            }
            results.push(item);
        }
    });
    
    return results;
});

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
        
        // Convert to JSON
        const jsonData = XLSX.utils.sheet_to_json(worksheet, { header: 1 });
        
        let newText = "";
        jsonData.forEach(row => {
            if (row.length === 0) return;
            // Join row fields with space to let our smart parser handle it
            const line = row.filter(cell => cell !== null && cell !== undefined).join(' ');
            if (line.trim()) {
                newText += line + "\n";
            }
        });

        if (newText) {
            batchText.value += (batchText.value ? "\n" : "") + newText;
        }
    };
    reader.readAsArrayBuffer(file);
};

const handleBatchSave = async () => {
    if (parsedItems.value.length === 0) return;
    
    processing.value = true;
    try {
        // We'll perform multiple posts or use a batch endpoint if available.
        // For reliability, we'll iterate through parsed items.
        // Since it's military records, we use the MilitaryRecordController.
        
        const chunks = [];
        for (let i = 0; i < parsedItems.value.length; i += 5) {
            chunks.push(parsedItems.value.slice(i, i + 5));
        }

        for (const chunk of chunks) {
            await Promise.all(chunk.map(item => axios.post('/military-records', item)));
        }

        alert(`成功匯入 ${parsedItems.value.length} 筆資料！`);
        emit('save');
        batchText.value = '';
    } catch (e) {
        alert('部分資料儲存失敗，請檢查網路連線或格式。');
    } finally {
        processing.value = false;
    }
};
</script>

<style scoped>
.animate-fade-in { animation: fadeIn 0.3s ease-out; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
</style>
