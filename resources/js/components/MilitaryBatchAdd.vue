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
                <div class="flex-1 flex items-center bg-white p-3 rounded-2xl border border-slate-100 shadow-sm transition-all relative">
                    <div @click="showDatePicker = true" class="flex-1 flex items-center cursor-pointer">
                        <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600 mr-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </div>
                        <div>
                            <p class="text-[13px] text-[#aeb4be] font-normal uppercase tracking-wider">錄入日期</p>
                            <p class="text-[15px] font-bold text-slate-800">{{ batchDate ? batchDate.replace(/-/g, '/') : '未設定 (首位顯示)' }}</p>
                        </div>
                    </div>
                    <!-- Clear Date Button -->
                    <button v-if="batchDate" @click="batchDate = null" class="absolute right-3 p-2 text-slate-300 hover:text-red-500 active:scale-90 transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
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
                    ]">
                    <svg class="w-5 h-5 mb-0.5" style="color: white !important;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    <span class="text-[11px] font-bold" style="color: white !important;">{{ processing ? '存取中' : `儲存 (${parsedItems.length})` }}</span>
                </button>
            </div>
        </div>

        <!-- Input Area -->
        <div class="flex-1 flex flex-col p-4 relative">
            <button v-if="batchText" @click="batchText = ''" class="absolute right-8 top-8 z-10 text-[14px] font-bold text-red-500 bg-red-50/80 backdrop-blur-sm px-3 py-1.5 rounded-xl border border-red-100 active:scale-95 transition-all">
                清空全部
            </button>
            <textarea 
                v-model="batchText" 
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

const getTodayStr = () => {
    // 強制使用台北時區 (Asia/Taipei)
    const options = { timeZone: 'Asia/Taipei', year: 'numeric', month: '2-digit', day: '2-digit' };
    const parts = new Intl.DateTimeFormat('en-US', options).formatToParts(new Date());
    const y = parts.find(p => p.type === 'year').value;
    const m = parts.find(p => p.type === 'month').value;
    const d = parts.find(p => p.type === 'day').value;
    return `${y}-${m}-${d}`;
};

const batchDate = ref(getTodayStr());
const batchText = ref('');
const processing = ref(false);
const showDatePicker = ref(false);

const parsedItems = computed(() => {
    if (!batchText.value.trim()) return [];
    
    const lines = batchText.value.split('\n');
    let currentDate = batchDate.value; 
    const results = [];
    
    const nameAliasMap = {
        '金容': '靈果', '金涓': '靈慧', '金梅': '靈妙', '金蘭': '靈智', '金平': '靈平',
        '金瑞': '龍戰', '金耀': '龍勝', '金旭': '靈心', '金熙': '靈情', '金吉': '靈奇',
        '金祥': '靈傾', '金恩': '靈昡', '金鈺': '元續', '金穎': '赤峰',
        '金律': '閻㻇', '金欣': '閻闇', '閰琉': '閻尊', '金剛': '閰帝', '金頓': '閻爵',
        '金虹': '赤覺', '金湘': '紫元', '金雍': '道妙', '金無': '閻澤', '金真': '閻願',
        '金翎': '鳳尊', '金妙': '鳳媓'
    };
    const translateName = (n) => nameAliasMap[n] || n;

    const parseDateText = (str) => {
        if (!str) return null;
        // Priority 1: 4-digit CE
        const ceMatch = str.match(/\b(\d{4})[/\-\s](\d{1,2})[/\-\s](\d{1,2})\b/);
        if (ceMatch) return `${ceMatch[1]}-${ceMatch[2].padStart(2,'0')}-${ceMatch[3].padStart(2,'0')}`;
        // Priority 2: 2-3 digit ROC
        const rocMatch = str.match(/\b(\d{2,3})[/\-\s](\d{1,2})[/\-\s](\d{1,2})\b/);
        if (rocMatch) {
            const y = parseInt(rocMatch[1]) + 1911;
            return `${y}-${rocMatch[2].padStart(2,'0')}-${rocMatch[3].padStart(2,'0')}`;
        }
        return null;
    };

    lines.forEach(line => {
        const normLine = line.normalize('NFKC').trim();
        if (!normLine) return;

        // 1. Detect Standalone Date Header
        const dateHeader = parseDateText(normLine);
        if (dateHeader && normLine.length < 20) {
            currentDate = dateHeader;
            return;
        }

        // 2. Detect Attribute Keywords
        const attrKeywords = ['備註', '處理日期', '結果', '用意'];
        const firstWord = normLine.split(/[\s：:]/)[0];
        if (attrKeywords.includes(firstWord) && results.length > 0) {
            const prev = results[results.length - 1];
            const val = normLine.replace(new RegExp(`^${firstWord}[\\s：:]*`), '').trim();
            if (firstWord === '備註') prev.remarks_text = (prev.remarks_text ? prev.remarks_text + ' ' : '') + val;
            else if (firstWord === '處理日期') prev.process_date = val;
            else if (firstWord === '結果') prev.destination = val;
            return;
        }

        // 3. Name & Quantity Extraction
        let cleanLine = normLine.replace(/^([\d\.\、\)\s-]+|[（\(]\d+[）\)]\s*|[一二三四五六七八九十]+[\.\、\s-]+)+/, '').trim();
        if (!cleanLine) return;

        let name = '';
        let qty = 0;
        const firstDigitIndex = cleanLine.replace(/[\(\（][^\)\）]*[\)\）]/g, m => ' '.repeat(m.length)).search(/\d/);
        
        if (firstDigitIndex === -1) {
            name = translateName(cleanLine);
            qty = 1;
        } else {
            name = translateName(cleanLine.substring(0, firstDigitIndex).trim());
            let rest = cleanLine.substring(firstDigitIndex).replace(/[\(\（][^\)\）]*[\)\）]/g, ' ');
            const numbers = rest.match(/\d+/g);
            qty = numbers ? numbers.reduce((sum, n) => sum + parseInt(n), 0) : 1;
        }

        // Filter out headers
        const skipKeywords = ['法號', '日期', '數量', '結果', '項次', '總計'];
        if (skipKeywords.some(key => name === key)) return;

        const item = {
            user_name: name,
            quantity: qty,
            know_date: currentDate,
            army_type: props.armyType,
            destination: '未處理',
            user_remarks: '',
            remarks_text: '',
            process_date: ''
        };
        
        // Army-specific defaults
        if (props.armyType === '黑曜軍') { item.yan_zun = Math.ceil(qty / 2); item.yan_an = Math.floor(qty / 2); }
        else if (props.armyType === '耀紫軍') { item.long_sheng = Math.ceil(qty / 2); item.long_zhan = Math.floor(qty / 2); }
        else if (props.armyType === '虎甲軍') { item.yan_jue = qty; item.yan_ze = 0; }
        else if (props.armyType === '虎賁軍') { item.yan_di = qty; item.yan_yuan = 0; }

        results.push(item);
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
