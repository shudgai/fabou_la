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
                <button @click="$refs.fileInput.click()" class="bg-emerald-600 text-white px-4 h-[52px] rounded-2xl flex flex-col items-center justify-center active:scale-95 transition-all shadow-md border border-emerald-500" style="color: white !important;">
                    <svg class="w-5 h-5 mb-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/></svg>
                    <span class="text-[11px] font-black" style="color: white !important;">匯入他檔</span>
                </button>
                <input type="file" ref="fileInput" @change="handleFileImport" accept=".xlsx, .xls" class="hidden">

            </div>
        </div>

        <!-- Input Area -->
        <div class="flex-1 flex flex-col p-4 relative min-h-0">
            <button v-if="batchText" @click="batchText = ''" class="absolute right-8 top-8 z-10 text-[14px] font-bold text-red-500 bg-red-50/80 backdrop-blur-sm px-3 py-1.5 rounded-xl border border-red-100 active:scale-95 transition-all">
                清空全部
            </button>
            <textarea 
                v-model="batchText" 
                :placeholder="placeholderText"
                class="flex-1 w-full bg-slate-50 rounded-[28px] border-none p-6 text-[16px] leading-relaxed focus:ring-2 focus:ring-indigo-100 outline-none resize-none font-medium shadow-inner"
            ></textarea>

            <!-- Preview Counter -->
            <div v-if="parsedItems.length > 0" class="mt-4 p-4 bg-indigo-50/30 rounded-2xl border border-indigo-100/50 shrink-0">
                <p class="text-[13px] text-[#aeb4be] font-normal">預計匯入：{{ parsedItems.length }} 筆資料</p>
                <div class="mt-2 flex flex-wrap gap-2 overflow-y-auto max-h-[100px]">
                    <span v-for="(item, idx) in parsedItems.slice(0, 15)" :key="idx" class="text-[11px] bg-white px-2 py-0.5 rounded-full border border-indigo-100 text-slate-600">
                        [{{ item.know_date?.replace(/-/g, '/') }}] {{ item.user_name }}{{ item.user_remarks ? '(' + translateRel(item.user_remarks) + ')' : '' }} ({{ item.quantity }})
                    </span>
                    <span v-if="parsedItems.length > 10" class="text-[11px] text-slate-400 self-center">...及其他 {{ parsedItems.length - 10 }} 筆</span>
                </div>
            </div>
        </div>
        <!-- MODALS -->
        <compact-date-picker v-if="showDatePicker" v-model="batchDate" @close="showDatePicker = false" />



        <!-- Bottom Save Action Bar (Integrated into Layout) -->
        <div class="bg-white border-t border-slate-100 p-4 pb-[calc(1rem+env(safe-area-inset-bottom,20px))] z-[1100] shadow-[0_-10px_30px_rgba(0,0,0,0.05)] pt-4 shrink-0">
            <button @click="handleBatchSave" :disabled="parsedItems.length === 0 || processing" 
                class="w-full bg-indigo-600 text-white font-black h-[52px] rounded-2xl shadow-xl shadow-indigo-100 active:scale-[0.98] transition-all flex items-center justify-center space-x-2"
                style="color: white !important;"
            >
                <svg v-if="!processing" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                <span class="text-[20px] tracking-widest" style="color: white !important;">{{ processing ? '正在儲存...' : `確認載錄 (${parsedItems.length} 筆)` }}</span>
            </button>
        </div>
        <!-- Global Action Confirm / Toast (Critical for iOS) -->
        <div v-if="persistentToast" class="fixed inset-0 z-[2000] flex items-center justify-center p-6 bg-slate-900/40 backdrop-blur-sm animate-fade-in">
            <div class="bg-white w-full max-w-sm rounded-[32px] shadow-2xl overflow-hidden animate-slide-up border border-white/20">
                <div class="p-8 text-center space-y-6">
                    <div class="flex flex-col items-center">
                        <div v-if="persistentToast.type === 'error'" class="w-16 h-16 bg-rose-50 text-rose-500 rounded-2xl flex items-center justify-center mb-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        </div>
                        <div v-else class="w-16 h-16 bg-emerald-50 text-emerald-500 rounded-2xl flex items-center justify-center mb-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <h3 class="text-[20px] font-black text-slate-900 leading-tight whitespace-pre-wrap">{{ persistentToast.msg }}</h3>
                    </div>

                    <div class="flex flex-col space-y-3">
                        <button @click="persistentToast = null" 
                                class="w-full py-4 bg-indigo-600 text-white rounded-2xl font-black text-[18px] active:scale-95 transition-all shadow-lg"
                                style="color: white !important;">
                            確認
                        </button>
                    </div>
                </div>
            </div>
        </div>
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

const batchDate = ref(null);
const batchText = ref('');
const processing = ref(false);
const showDatePicker = ref(false);
const persistentToast = ref(null);

const placeholderText = computed(() => {
    if (props.armyType === '黑曜軍') {
        return "黑曜軍 專屬格式範例：\n2026/05/05\n元續 10 (閻尊 5 閻闇 5)\n閻闇 20 (閻尊 10 閻闇 10)";
    }
    return "請貼上文字或 Excel 複製之內容...";
});

const translateRel = (rel) => {
    if (!rel) return '';
    let result = rel.trim();
    if (result === '之父' || result === '父') return '父親';
    if (result === '之母' || result === '母') return '母親';
    if (result === '之嬤' || result === '嬤') return '奶奶';
    if (result === '之夫' || result === '夫') return '先生';
    return result.replace(/^[之的]/, '');
};

const parsedItems = computed(() => {
    if (!batchText.value.trim()) return [];
    
    const lines = batchText.value.split('\n');
    const getTodayLocal = () => {
        const d = new Date();
        return d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0');
    };
    let currentDate = batchDate.value || getTodayLocal(); 
    const results = [];
    
    const nameAliasMap = {
        '金容': '靈果', '金涓': '靈慧', '金梅': '靈妙', '金蘭': '靈智', '金平': '靈平',
        '金瑞': '龍戰', '金耀': '龍勝', '金旭': '靈心', '金熹': '靈情', '金吉': '靈奇',
        '金祥': '靈傾', '金恩': '靈昡', '金鈺': '元續', '金穎': '赤峰',
        '金律': '閻㻇', '金欣': '閻闇', '閰琉': '閻尊', '金剛': '閰帝', '金頓': '閻爵',
        '金虹': '赤覺', '金湘': '紫元', '金雍': '道妙', '金無': '閻澤', '金真': '閻願',
        '金翎': '鳳尊', '金妙': '鳳媓'
    };
    const translateName = (n) => nameAliasMap[n] || n;

    const parseDateText = (str) => {
        if (!str) return null;
        const clean = str.replace(/[↓↓]/g, '').trim();
        
        // Priority 1: 4-digit CE (YYYY/MM/DD)
        const ceMatch = clean.match(/\b(\d{4})[/\-\.\s](\d{1,2})[/\-\.\s](\d{1,2})\b/);
        if (ceMatch) return `${ceMatch[1]}-${ceMatch[2].padStart(2,'0')}-${ceMatch[3].padStart(2,'0')}`;
        
        // Priority 2: 2-3 digit ROC (民國 YYY/MM/DD)
        const rocMatch = clean.match(/\b(\d{2,3})[/\-\.\s](\d{1,2})[/\-\.\s](\d{1,2})\b/);
        if (rocMatch) {
            const y = parseInt(rocMatch[1]) + 1911;
            return `${y}-${rocMatch[2].padStart(2,'0')}-${rocMatch[3].padStart(2,'0')}`;
        }

        // Priority 3: 2-part dates (Y/M or M/D)
        const twoPartMatch = clean.match(/\b(\d{2,4})[/\-\.\s](\d{1,2})\b/);
        if (twoPartMatch) {
            const p1 = parseInt(twoPartMatch[1]);
            const p2 = parseInt(twoPartMatch[2]);
            // Case A: ROC Year / Month (e.g., 110/05) -> First part 13-199
            if (p1 > 12 && p1 < 200) {
                return `${p1 + 1911}-${String(p2).padStart(2,'0')}-01`;
            }
            // Case B: CE Year / Month (e.g., 2021/05) -> First part >= 1900
            if (p1 >= 1900) {
                return `${p1}-${String(p2).padStart(2,'0')}-01`;
            }
            // Case C: Month / Day (e.g., 05/20) -> Use current year context
            const y = new Date().getFullYear();
            return `${y}-${String(p1).padStart(2,'0')}-${String(p2).padStart(2,'0')}`;
        }
        return null;
    };

    lines.forEach(line => {
        let normLine = line.normalize('NFKC').trim();
        if (!normLine) return;

        // 1. Detect Standalone Date Header (Ignoring symbols like ↓ and Weekdays)
        const dateHeader = parseDateText(normLine);
        // Lenient check: if after removing date-like chars, whitespace, and common date suffixes, nothing remains
        const cleanForCheck = normLine.replace(/[\d/.\-↓\s\(\)（）一二三四五六日月]/g, '');
        if (dateHeader && cleanForCheck.length <= 1) { // Allow 1 extra char for safety
            currentDate = dateHeader;
            return;
        }

        // 1.5 Detect Date at start of line (e.g. "110/05/01 靈奇 2262")
        const startMatch = normLine.match(/^(\d{2,4}[/.-]\d{1,2}([/.-]\d{1,2})?)\s+/);
        if (startMatch) {
            const d = parseDateText(startMatch[1]);
            if (d) currentDate = d;
            normLine = normLine.replace(startMatch[1], '').trim();
        }

        // 2. Detect Attribute Keywords
        const attrKeywords = ['備註', '用意'];
        const firstWord = normLine.split(/[\s：:]/)[0];
        if (attrKeywords.includes(firstWord) && results.length > 0) {
            const prev = results[results.length - 1];
            const val = normLine.replace(new RegExp(`^${firstWord}[\\s：:]*`), '').trim();
            if (firstWord === '備註') prev.remarks_text = (prev.remarks_text ? prev.remarks_text + ' ' : '') + val;
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

        // Split name and relationship if pattern exists (e.g. 元續之母)
        let finalName = name;
        let uRemarks = '';
        const relSplitMatch = name.match(/^(.*?)([之的])(.+)$/);
        if (relSplitMatch) {
            finalName = relSplitMatch[1].trim();
            const connector = relSplitMatch[2];
            const relPart = relSplitMatch[3].trim();
            uRemarks = connector + relPart;
        }

        const item = {
            user_name: finalName,
            quantity: qty,
            know_date: currentDate,
            army_type: props.armyType,
            user_remarks: uRemarks,
            remarks_text: ''
        };
        
        // Specialized Parsing for Obsidian Army (黑曜軍)
        if (props.armyType === '黑曜軍') {
            const parenPartMatch = normLine.match(/[\(（](.*?)[\)）]/);
            const parenPart = parenPartMatch ? parenPartMatch[1] : '';
            
            const yanZunMatch = parenPart.match(/[閻阎]尊[^\d]*(\d+)/);
            const yanAnMatch = parenPart.match(/[閻阎閰][闇閽][^\d]*(\d+)/);
            
            if (yanZunMatch || yanAnMatch) {
                const yz = yanZunMatch ? parseInt(yanZunMatch[1]) : 0;
                const ya = yanAnMatch ? parseInt(yanAnMatch[1]) : 0;
                
                // If total quantity was explicitly specified (qty > 1), ensure sum matches
                if (qty > 1) {
                    item.yan_zun = yz;
                    item.yan_an = ya;
                    // If sum is incorrect, adjust to match the total qty
                    if (item.yan_zun + item.yan_an !== qty) {
                        if (yz > 0 && yz <= qty && ya === 0) {
                            item.yan_an = qty - yz;
                        } else if (ya > 0 && ya <= qty && yz === 0) {
                            item.yan_zun = qty - ya;
                        } else if (yz + ya > 0) {
                            // If both present but sum is wrong, respect the parts and update total
                            item.quantity = yz + ya;
                        }
                    }
                } else {
                    // No total specified, use sum of parts
                    item.yan_zun = yz;
                    item.yan_an = ya;
                    item.quantity = yz + ya;
                }
            } else {
                // Default split logic
                item.yan_zun = Math.ceil(qty / 2); 
                item.yan_an = Math.floor(qty / 2);
            }
        }
        else if (props.armyType === '耀紫軍') { 
            item.long_sheng = Math.ceil(qty / 2); 
            item.long_zhan = Math.floor(qty / 2); 
        }
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
    const itemsToSave = [...parsedItems.value];
    if (itemsToSave.length === 0) return;
    
    processing.value = true;
    try {
        const chunks = [];
        for (let i = 0; i < itemsToSave.length; i += 5) {
            chunks.push(itemsToSave.slice(i, i + 5));
        }

        let savedCount = 0;
        for (const chunk of chunks) {
            await Promise.all(chunk.map(async (item) => {
                await axios.post('/military-records', item);
                savedCount++;
            }));
        }

        persistentToast.value = { msg: `✓ 成功新增 ${savedCount} 筆資料！`, type: 'success' };
        emit('save');
        batchText.value = '';
        
        setTimeout(() => { 
            if (persistentToast.value?.type === 'success') {
                persistentToast.value = null;
                emit('cancel', true); 
            }
        }, 1500);
    } catch (e) {
        console.error('Batch save error:', e);
        const errorMsg = e.response?.data?.message || e.response?.data?.error || '儲存失敗，請檢查資料格式或網路連線';
        persistentToast.value = { msg: `✖ ${errorMsg}`, type: 'error' };
    } finally {
        processing.value = false;
    }
};
</script>

<style scoped>
.custom-scrollbar { -webkit-overflow-scrolling: touch; }
.animate-fade-in { animation: fadeIn 0.3s ease-out; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }


</style>
