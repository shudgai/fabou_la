<template>
    <teleport to="body">
    <div v-if="show" class="fixed inset-0 z-[3500] flex md:items-center md:justify-center p-0 md:p-6 animate-fade-in">
        <!-- Backdrop (Desktop Only) -->
        <div class="hidden md:block fixed inset-0 bg-slate-900/40 backdrop-blur-sm" @click="$emit('cancel', false)"></div>

        <!-- Form Container -->
        <div class="relative w-full h-full md:h-auto md:max-h-[90dvh] md:max-w-[633px] bg-white md:rounded-[32px] md:shadow-2xl flex flex-col overflow-hidden pb-[0px] md:pb-0">
            <!-- Header (Matched to Single Add Form) -->
            <div class="px-[10px] py-[12px] flex items-center bg-white border-b border-slate-50 relative shrink-0">
                <div class="flex-1 flex items-center gap-2 min-w-0">
                    <logo-imperial-notebook :height="36" />
                    <div class="flex flex-col justify-center min-w-0">
                        <div class="font-bold leading-none font-outfit uppercase tracking-wider text-slate-900" style="font-size: 25px !important;">軍隊載錄專區 - {{ armyType }}</div>
                        <div class="font-bold mt-2 truncate font-outfit text-slate-900" style="font-size: 24px !important;">
                            多筆新增
                        </div>
                    </div>
                </div>
                <button @click="$emit('cancel', false)" class="text-slate-300 hover:text-slate-600 transition-colors p-2 absolute right-4 top-1/2 -translate-y-1/2 z-[50]">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
            </div>

        <!-- Options Container -->
        <div class="p-4 bg-slate-50/50 space-y-4 shrink-0">
            <!-- Date Picker, Excel Import & Save -->
            <div class="flex items-center space-x-2">
                <!-- Date Box (Entirely Clickable) -->
                <div class="flex-1 flex items-center bg-white h-[52px] px-3 rounded-2xl border border-slate-100 shadow-sm transition-all relative">
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

            </div>
        </div>

        <!-- Input Area -->
        <div class="flex-1 flex flex-col p-4 relative min-h-0">
            <div class="absolute left-8 top-8 z-10 pointer-events-none flex items-center">
                <span class="text-[14px] font-black text-indigo-400 uppercase tracking-widest flex items-center bg-white/80 backdrop-blur-sm px-3 py-1 rounded-xl shadow-sm border border-indigo-50">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    輸入模式
                </span>
            </div>
            <button v-if="batchText" @click="batchText = ''" class="absolute right-8 top-8 z-10 p-2 text-slate-400 hover:text-red-500 active:scale-90 transition-all border-none bg-transparent" title="清空全部">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
            <textarea 
                v-model="batchText" 
                :placeholder="placeholderText"
                class="flex-1 w-full bg-slate-50 rounded-[28px] border-none p-6 pt-16 text-[16px] leading-relaxed focus:ring-2 focus:ring-indigo-100 outline-none resize-none font-medium shadow-inner min-h-[500px]"
            ></textarea>

            <!-- Preview Section (Raw Lines) -->
            <div v-if="batchText.trim()" class="mt-4 border border-indigo-100 rounded-[24px] overflow-hidden bg-white animate-fade-in shrink-0">
                <div class="bg-indigo-50/50 px-4 py-3 border-b border-indigo-100 flex justify-between items-center">
                    <div class="flex flex-col">
                        <span class="text-[15px] font-black text-indigo-600">預覽清單 ({{ rawLines.length }} 筆資料)</span>
                        <span class="text-[11px] text-indigo-400 font-bold">貼上內容原始顯示</span>
                    </div>
                </div>
                <div class="max-h-48 overflow-y-auto custom-scrollbar divide-y divide-slate-50">
                    <div v-for="(line, idx) in rawLines" :key="idx" class="px-4 py-2.5 text-[14px] font-bold text-slate-900 flex items-start gap-3 hover:bg-indigo-50/30">
                        <span class="text-[11px] font-black text-slate-300 w-6 shrink-0 mt-0.5">{{ idx + 1 }}</span>
                        <span class="break-all whitespace-pre-wrap">{{ line }}</span>
                    </div>
                </div>
            </div>
        </div>
        <!-- MODALS -->
        <compact-date-picker v-if="showDatePicker" v-model="batchDate" @close="showDatePicker = false" />

        <!-- Bottom Save Action Bar -->
        <div class="absolute bottom-[7dvh] left-0 right-0 md:relative md:bottom-0 px-4 pt-3 pb-0 bg-white border-t border-slate-100 z-[1100] shadow-[0_-10px_30px_rgba(0,0,0,0.05)] shrink-0">
            <button @click="handleBatchSave" :disabled="parsedItems.length === 0 || processing" 
                class="w-full bg-indigo-600 !text-white font-black h-[52px] rounded-2xl shadow-xl shadow-indigo-100 active:scale-[0.98] transition-all flex items-center justify-center space-x-2"
                style="color: white !important;"
            >
                <svg v-if="!processing" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                <span class="text-[20px] tracking-widest" style="color: white !important;">{{ processing ? '正在儲存...' : `確認載錄 (${parsedItems.length} 筆)` }}</span>
            </button>
        </div>

        <!-- Navbar Wrapper Removed for Modal UX -->
        <div class="h-[env(safe-area-inset-bottom)] md:h-0"></div>

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
                                class="w-full py-4 bg-indigo-600 !text-white rounded-2xl font-black text-[18px] active:scale-95 transition-all shadow-lg"
                                style="color: white !important;">
                            確認
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    </teleport>
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

const rawLines = computed(() => {
    return batchText.value.split('\n').map(l => l.trim()).filter(l => l !== '');
});

const placeholderText = computed(() => {
    if (props.armyType === '黑曜軍') {
        return "多筆新增如下列:\n日期(年/月/日)\n法號總數";
    }
    if (props.armyType === '耀紫軍') {
        return "多筆新增如下列:\n日期(年/月/日)\n法號總數(龍勝數量龍戰數量)";
    }
    if (props.armyType === '虎賁軍' || props.armyType === '虎甲軍') {
        return "多筆新增如下列:\n日期(年/月/日)\n法號數量";
    }
    return "請貼上文字或試算表複製之內容...";
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
    let currentResolvedDate = batchDate.value; 
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
        const ceMatch = clean.match(/\b(\d{4})[/\-\.\s](\d{1,2})[/\-\.\s](\d{1,2})\b/);
        if (ceMatch) return `${ceMatch[1]}-${ceMatch[2].padStart(2,'0')}-${ceMatch[3].padStart(2,'0')}`;
        const rocMatch = clean.match(/\b(\d{2,3})[/\-\.\s](\d{1,2})[/\-\.\s](\d{1,2})\b/);
        if (rocMatch) {
            const y = parseInt(rocMatch[1]) + 1911;
            return `${y}-${rocMatch[2].padStart(2,'0')}-${rocMatch[3].padStart(2,'0')}`;
        }
        const twoPartMatch = clean.match(/\b(\d{2,4})[/\-\.\s](\d{1,2})\b/);
        if (twoPartMatch) {
            const p1 = parseInt(twoPartMatch[1]);
            const p2 = parseInt(twoPartMatch[2]);
            if (p1 > 12 && p1 < 200) return `${p1 + 1911}-${String(p2).padStart(2,'0')}-01`;
            if (p1 >= 1900) return `${p1}-${String(p2).padStart(2,'0')}-01`;
            const y = new Date().getFullYear();
            return `${y}-${String(p1).padStart(2,'0')}-${String(p2).padStart(2,'0')}`;
        }
        return null;
    };

    lines.forEach(line => {
        let normLine = line.normalize('NFKC').trim();
        if (!normLine) return;

        const dateHeader = parseDateText(normLine);
        const cleanForCheck = normLine.replace(/[\d/.\-↓\s\(\)（）一二三四五六日月]/g, '');
        if (dateHeader && cleanForCheck.length <= 1) {
            currentResolvedDate = dateHeader;
            return;
        }

        const startMatch = normLine.match(/^(\d{2,4}[/.-]\d{1,2}([/.-]\d{1,2})?)\s+/);
        if (startMatch) {
            const d = parseDateText(startMatch[1]);
            if (d) currentResolvedDate = d;
            normLine = normLine.replace(startMatch[1], '').trim();
        }

        const attrKeywords = ['備註', '用意'];
        const firstWord = normLine.split(/[\s：:]/)[0];
        if (attrKeywords.includes(firstWord) && results.length > 0) {
            const prev = results[results.length - 1];
            const val = normLine.replace(new RegExp(`^${firstWord}[\\s：:]*`), '').trim();
            if (firstWord === '備註') prev.remarks_text = (prev.remarks_text ? prev.remarks_text + ' ' : '') + val;
            return;
        }

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

            // Robust Quantity Parsing for Units (隊, 萬, 位) and Commas
            let totalQty = 0n;
            let foundUnit = false;

            // 1. Detect "隊" (1,000,000)
            const troopMatch = rest.match(/(\d+)\s*隊/);
            if (troopMatch) {
                totalQty += BigInt(troopMatch[1]) * 1000000n;
                foundUnit = true;
            }

            // 2. Detect "萬" (10,000)
            const wanMatch = rest.match(/(\d+)\s*萬/);
            if (wanMatch) {
                totalQty += BigInt(wanMatch[1]) * 10000n;
                foundUnit = true;
            }

            // 3. Detect remaining numbers or "位" (1)
            // Remove parts already handled by units
            let remainingRest = rest.replace(/\d+\s*[隊萬]/g, ' ');
            // Clean commas
            remainingRest = remainingRest.replace(/,/g, '');
            const numbers = remainingRest.match(/\d+/g);
            if (numbers) {
                totalQty += numbers.reduce((sum, n) => sum + BigInt(n), 0n);
            }

            qty = (totalQty > 0n) ? totalQty.toString() : "1";
        }

        const skipKeywords = ['法號', '日期', '數量', '結果', '項次', '總計'];
        if (skipKeywords.some(key => name === key)) return;

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
            know_date: currentResolvedDate,
            display_date: currentResolvedDate,
            army_type: props.armyType,
            user_remarks: uRemarks,
            remarks_text: ''
        };

        if (props.armyType === '黑曜軍' || props.armyType === '耀紫軍') {
            const parenPartMatch = normLine.match(/[\(（](.*?)[\)）]/);
            const parenPart = parenPartMatch ? parenPartMatch[1] : '';
            let part1 = 0n, part2 = 0n;
            let hasParts = false;

            if (props.armyType === '黑曜軍') {
                const p1Match = parenPart.match(/[閻阎]尊[^\d]*(\d+)/);
                const p2Match = parenPart.match(/[閻阎閰][闇閽][^\d]*(\d+)/);
                if (p1Match || p2Match) {
                    part1 = p1Match ? BigInt(p1Match[1]) : 0n;
                    part2 = p2Match ? BigInt(p2Match[1]) : 0n;
                    hasParts = true;
                }
            } else {
                const p1Match = parenPart.match(/龍勝[^\d]*(\d+)/);
                const p2Match = parenPart.match(/龍戰[^\d]*(\d+)/);
                if (p1Match || p2Match) {
                    part1 = p1Match ? BigInt(p1Match[1]) : 0n;
                    part2 = p2Match ? BigInt(p2Match[1]) : 0n;
                    hasParts = true;
                }
            }

            const bQty = BigInt(qty);
            if (hasParts) {
                if (bQty > 1n) {
                    const sum = part1 + part2;
                    if (sum !== bQty) {
                        if (part1 > 0n && part1 <= bQty && part2 === 0n) part2 = bQty - part1;
                        else if (part2 > 0n && part2 <= bQty && part1 === 0n) part1 = bQty - part2;
                        else if (sum > 0n) qty = sum.toString();
                    }
                } else {
                    qty = (part1 + part2).toString();
                }
            } else {
                part1 = bQty / 2n + (bQty % 2n); 
                part2 = bQty / 2n;
            }

            if (props.armyType === '黑曜軍') {
                item.yan_zun = part1.toString(); item.yan_an = part2.toString();
            } else {
                item.long_sheng = part1.toString(); item.long_zhan = part2.toString();
            }
            item.quantity = qty;
        }
        else if (props.armyType === '虎甲軍') { item.yan_jue = qty; item.yan_ze = "0"; }
        else if (props.armyType === '虎賁軍') { item.yan_di = qty; item.yan_yuan = "0"; }

        results.push(item);
    });

    return results;
});

const formatWithCommas = (val) => {
    const s = String(val).replace(/,/g, '');
    if (!/^\d+$/.test(s)) return s;
    return s.replace(/\B(?=(\d{3})+(?!\d))/g, ",");
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
        if (newText) batchText.value += (batchText.value ? "\n" : "") + newText;
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
.animate-fade-in { animation: fadeIn 0.3s ease-out; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
</style>
