<template>
    <div v-if="show" class="fixed inset-0 z-[70] flex items-end md:items-center justify-center px-0">
        <!-- Backdrop (Desktop Only) -->
        <div class="hidden md:block fixed inset-0 bg-slate-900/40 backdrop-blur-sm" @click="$emit('cancel')"></div>
        
        <!-- Form Container -->
        <div class="relative w-full h-full md:h-auto md:max-h-[90dvh] md:max-w-2xl bg-white md:rounded-[32px] shadow-[0_-10px_40px_rgba(0,0,0,0.1)] overflow-hidden animate-slide-up flex flex-col">
            <!-- Header -->
            <div class="px-6 py-4 border-b border-slate-100 flex items-center bg-white sticky top-0 z-10 relative">
                <h5 class="text-[30px] font-black tracking-tight text-slate-900" style="font-size: 30px !important;">
                    怨靈載錄專區<br>多筆載錄
                </h5>
                <button @click="$emit('cancel')" class="p-2 text-black hover:text-slate-600 active:scale-95 absolute right-2 top-1/2 -translate-y-1/2">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto px-4 py-4 space-y-4 custom-scrollbar bg-white">
                <!-- Anchor Date Selection -->
                <div class="flex items-center bg-slate-50 p-4 rounded-2xl border border-slate-100 shadow-sm transition-all mb-2 relative">
                    <div @click="showDatePicker = true" class="flex-1 flex items-center cursor-pointer">
                        <div class="w-10 h-10 rounded-xl bg-indigo-100 flex items-center justify-center text-indigo-600 mr-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-[17px] font-black text-slate-900 tracking-tight">基準日期 (無指定日期時使用)</p>
                            <p class="text-[15px] font-bold text-indigo-600">{{ batchDate ? batchDate.replace(/-/g, '/') : '未設定 (首位顯示)' }}</p>
                        </div>
                    </div>
                    <!-- Clear Date Button -->
                    <button v-if="batchDate" @click="batchDate = null" class="absolute right-4 p-2 text-slate-300 hover:text-red-500 active:scale-90 transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                </div>

                <div class="space-y-1 relative group/textarea">
                    <div class="flex items-center justify-between mb-2 ml-1">
                        <label class="app-title">貼上資料</label>
                        <div class="flex items-center space-x-2">
                            <button @click.stop="triggerFileUpload" class="text-[14px] font-black text-indigo-600 flex items-center space-x-1 active:scale-95 transition-all">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M9 13h6m-6-4h6m-6 8h3" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                <span>載入他檔</span>
                            </button>
                        </div>
                    </div>
                    <div class="relative">
                        <textarea 
                            v-model="batchText" 
                            rows="15"
                            @click.stop
                            class="w-full app-body p-4 bg-slate-50 rounded-2xl border-none focus:ring-2 focus:ring-indigo-500 min-h-[500px] leading-relaxed pr-12"
                            placeholder="多筆新增如下列:&#10;日期(yyyy/mm/dd)&#10;法號總數"
                        ></textarea>
                        <!-- Floating Clear Cross Button -->
                        <button v-if="batchText" @click="batchText = ''" 
                            class="absolute top-3 right-3 w-8 h-8 flex items-center justify-center bg-white/80 backdrop-blur shadow-sm rounded-full text-slate-400 hover:text-red-500 hover:bg-white active:scale-90 transition-all border border-slate-100">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M6 18L18 6M6 6l12 12" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <div class="absolute bottom-[7dvh] left-0 right-0 md:relative md:bottom-0 px-6 py-[2px] bg-white border-t border-slate-100 shadow-[0_-10px_30px_rgba(0,0,0,0.03)] z-[1100] shrink-0">
                <button 
                    @click="handleBatchSave" 
                    :disabled="loading"
                    class="w-full bg-indigo-600 disabled:bg-slate-300 h-[52px] rounded-2xl shadow-xl shadow-indigo-100 active:scale-[0.98] transition-all flex items-center justify-center space-x-2"
                >
                    <span v-if="loading" class="animate-spin h-5 w-5 border-2 border-white/30 border-t-white rounded-full"></span>
                    <span class="text-[20px] tracking-widest font-black" style="color: white !important;">{{ loading ? '正在處理中...' : '確認批量載錄' }}</span>
                </button>
            </div>

            <!-- Global Mobile Navbar -->
            <mobile-navbar 
                @back="$emit('cancel')"
                @home="$emit('cancel')"
                :show-action="false"
                :can-search="false"
                is-absolute
            />
        </div>

        <compact-date-picker 
            v-if="showDatePicker" 
            v-model="batchDate" 
            title="基準日期"
            @close="showDatePicker = false" 
        />

        <!-- Global Action Confirm / Toast (Critical for iOS) -->
        <div v-if="persistentToast" class="fixed inset-0 z-[9999] flex items-center justify-center p-6 bg-slate-900/40 backdrop-blur-sm animate-fade-in">
            <div class="bg-white w-full max-w-md rounded-[32px] shadow-2xl overflow-hidden animate-slide-up border border-white/20">
                <div class="text-center">
                    <div class="p-8 pb-4 flex flex-col items-center w-full">
                        <div v-if="persistentToast.type === 'preview'" class="w-16 h-16 bg-indigo-50 text-indigo-500 rounded-2xl flex items-center justify-center mb-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                        </div>
                        <div v-else-if="persistentToast.type === 'success'" class="w-16 h-16 bg-emerald-50 text-emerald-500 rounded-2xl flex items-center justify-center mb-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="3" stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <div v-else class="w-16 h-16 bg-rose-50 text-rose-500 rounded-2xl flex items-center justify-center mb-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        </div>
                    </div>

                    <h3 class="w-full text-[17px] font-black text-slate-900 leading-tight whitespace-pre-wrap text-left bg-slate-50 p-[10px] border-y border-slate-100 overflow-y-auto max-h-[40dvh] custom-scrollbar">{{ persistentToast.msg }}</h3>
                    
                    <div class="p-8 pt-4 flex flex-col items-center w-full">
                        <p v-if="persistentToast.type === 'preview'" class="mb-4 text-[15px] font-bold text-slate-400 uppercase tracking-widest">確定繼續匯入嗎？</p>
                    </div>

                    <div class="flex flex-col space-y-3">
                        <button v-if="persistentToast.type === 'preview'" 
                                @click="executeBatchSave" 
                                class="w-full py-4 bg-indigo-600 text-white rounded-2xl font-black text-[18px] active:scale-95 transition-all shadow-lg" 
                                style="color: white !important;">
                            確認匯入
                        </button>
                        <button @click="handleToastConfirm" 
                                :class="persistentToast.type === 'success' || persistentToast.type === 'error' ? 'bg-indigo-600 text-white shadow-indigo-100' : 'bg-slate-100 text-slate-500'"
                                class="w-full py-4 rounded-2xl font-black text-[18px] active:scale-95 transition-all shadow-lg"
                                :style="{ color: (persistentToast.type === 'success' || persistentToast.type === 'error' ? 'white !important' : 'inherit') }">
                            {{ persistentToast.type === 'success' || persistentToast.type === 'error' ? '確認' : '取消' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import axios from 'axios';
import CompactDatePicker from './CompactDatePicker.vue';
import MobileNavbar from './MobileNavbar.vue';

const props = defineProps({
    show: Boolean
});

const emit = defineEmits(['save', 'cancel', 'success']);
const batchText = ref('');
const loading = ref(false);
const persistentToast = ref(null);

const handleToastConfirm = () => {
    const type = persistentToast.value?.type;
    persistentToast.value = null;
    if (type === 'success') {
        emit('cancel');
    }
};

const translateRel = (rel) => {
    if (!rel) return '';
    let result = rel.trim();
    if (result === '之父' || result === '父') return '父親';
    if (result === '之母' || result === '母') return '母親';
    if (result === '之嬤' || result === '嬤') return '奶奶';
    if (result === '之夫' || result === '夫') return '先生';
    return result.replace(/^[之的]/, '');
};
const showDatePicker = ref(false);

const getTodayStr = () => {
    // 建立一個台北時間的日期物件
    const now = new Date();
    const taipeiDate = new Intl.DateTimeFormat('zh-TW', {
        timeZone: 'Asia/Taipei',
        year: 'numeric',
        month: '2-digit',
        day: '2-digit'
    }).formatToParts(now);

    const year = taipeiDate.find(p => p.type === 'year').value;
    const month = taipeiDate.find(p => p.type === 'month').value;
    const day = taipeiDate.find(p => p.type === 'day').value;
    
    return `${year}-${month}-${day}`;
};
const batchDate = ref(null);

const triggerFileUpload = () => {
    const input = document.createElement('input');
    input.type = 'file';
    input.accept = '.xlsx, .xls, .doc, .docx';
    input.onchange = (e) => {
        const file = e.target.files[0];
        if (file) {
            persistentToast.value = { msg: `✓ 已讀取檔案「${file.name}」\n(解析功能串接中)`, type: 'success' };
            setTimeout(() => { if (persistentToast.value?.type === 'success') persistentToast.value = null; }, 2000);
        }
    };
    input.click();
};

const nameAliasMap = {
    '金容': '靈果', '金涓': '靈慧', '金梅': '靈妙', '金蘭': '靈智', '金平': '靈平',
        '金瑞': '龍戰', '金耀': '龍勝', '金旭': '靈心', '金熹': '靈情', '金吉': '靈奇',
    '金祥': '靈傾', '金恩': '靈昡', '金鈺': '元續', '金穎': '赤峰',
    '金律': '閻㻇', '金欣': '閻闇', '閰琉': '閻尊', '金剛': '閰帝', '金頓': '閻爵',
    '金虹': '赤覺', '金湘': '紫元', '金雍': '道妙', '金無': '閻澤', '金真': '閻願',
    '金翎': '鳳尊', '金妙': '鳳媓'
};
const translateName = (n) => nameAliasMap[n] || null;

const handleBatchSave = async () => {
    const lines = batchText.value.split('\n').map(l => l.trim()).filter(l => l !== '');
    if (lines.length === 0) {
        persistentToast.value = { msg: '✖ 請先輸入或貼上法號內容。', type: 'error' };
        return;
    }

    const nameMap = {
        '金容': '靈果', '金涓': '靈慧', '金梅': '靈妙', '金蘭': '靈智', '金平': '靈平',
            '金瑞': '龍戰', '金耀': '龍勝', '金旭': '靈心', '金熹': '靈情', '金吉': '靈奇',
        '金祥': '靈傾', '金恩': '靈昡', '金鈺': '元續', '金穎': '赤峰',
        '金律': '閻㻇', '金欣': '閻闇', '閰琉': '閻尊', '金剛': '閰帝', '金頓': '閻爵',
        '金虹': '赤覺', '金湘': '紫元', '金雍': '道妙', '金無': '閻澤', '金真': '閻願',
        '金翎': '鳳尊', '金妙': '鳳媓'
    };
    const translate = (n) => nameMap[n] || n;

    const results = [];
    let currentDateFromText = null;

    const parseDateText = (str) => {
        if (!str) return null;
        // Support YYYY.MM.DD, YYYY/MM/DD, YYYY-MM-DD
        const ceMatch = str.match(/\b(\d{4})[\.\/\-\s](\d{1,2})[\.\/\-\s](\d{1,2})\b/);
        if (ceMatch) return `${ceMatch[1]}-${ceMatch[2].padStart(2,'0')}-${ceMatch[3].padStart(2,'0')}`;
        const rocMatch = str.match(/\b(\d{2,3})[\.\/\-\s](\d{1,2})[\.\/\-\s](\d{1,2})\b/);
        if (rocMatch) {
            const y = parseInt(rocMatch[1]) + 1911;
            return `${y}-${rocMatch[2].padStart(2,'0')}-${rocMatch[3].padStart(2,'0')}`;
        }
        return null;
    };

    lines.forEach(line => {
        const normLine = line.normalize('NFKC').trim();
        if (!normLine) return;

        // 1. Detect Standalone Date Header (Check this BEFORE any cleaning)
        const dateHeader = parseDateText(normLine);
        if (dateHeader && normLine.length < 20) {
            currentDateFromText = dateHeader;
            return;
        }

        // 2. Detect Attribute Keywords
        const attrKeywords = ['備註', '處理日期', '結果', '法號備註'];
        const firstWord = normLine.split(/[\s：:]/)[0];
        if (attrKeywords.includes(firstWord) && results.length > 0) {
            const prev = results[results.length - 1];
            const val = normLine.replace(new RegExp(`^${firstWord}[\\s：:]*`), '').trim();
            if (firstWord === '備註') prev.remarks_text = (prev.remarks_text ? prev.remarks_text + '\n' : '') + val;
            else if (firstWord === '處理日期') prev.process_date = val;
            else if (firstWord === '結果') prev.destination = val;
            else if (firstWord === '法號備註') prev.user_remarks = val;
            return;
        }

        // 3. Name and Content Split
        // Refined cleaning to avoid eating up the entire line if it's just a name
        let cleanLine = normLine.replace(/^([\d\.\、\)\s-]+|[（\(]\d+[）\)]\s*|[一二三四五六七八九十]+[\.\、\s-]+)+/, '').trim();
        if (!cleanLine && normLine.length > 0) {
            // If cleaning ate everything but the original line had content, it might be a name that looks like a number (unlikely) or just a short name
            cleanLine = normLine.trim();
        }
        if (!cleanLine) return;

        // Skip headers
        const skipKeywords = ['法號', '日期', '數量', '結果', '項次', '總結', '總計', '處理日期'];
        if (skipKeywords.some(k => cleanLine.startsWith(k))) return;

        let subject = cleanLine;
        let resultsPart = '';
        const separators = ['—', '–', '-', '―', ':', '：', ' '];
        for (const sep of separators) {
            if (cleanLine.includes(sep)) {
                const idx = cleanLine.indexOf(sep);
                const sub = cleanLine.substring(0, idx).trim();
                // Ensure the subject isn't just a number
                if (sub && !/^\d+$/.test(sub)) {
                    subject = sub;
                    resultsPart = cleanLine.substring(idx + 1).trim();
                    break;
                }
            }
        }

        let rawName = subject;
        let uRemarks = '';

        // Handle parenthesis first (e.g. "靈奇 (靈情之友)")
        const rMatch = subject.match(/[（\(](.*?)[）\)]/);
        if (rMatch) {
            rawName = subject.replace(/[（\(].*?[）\)]/, '').trim();
            uRemarks = rMatch[1].trim();
        } else {
            // Handle "Name 之/的 Relative" pattern (e.g. "元續之母")
            const relMatch = subject.match(/^(.*?)([之的])(.+)$/);
            if (relMatch) {
                rawName = relMatch[1].trim();
                let connector = relMatch[2];
                let relPart = relMatch[3].trim();
                uRemarks = connector + relPart;
            }
        }

        // Clean digits from name only if they are at the end
        rawName = rawName.replace(/\d+$/, '').trim();
        let finalName = translate(rawName);

        // Extract quantity (Priority to "XX位")
        const qtyMatch = cleanLine.match(/(\d+)\s*位/);
        const qty = qtyMatch ? parseInt(qtyMatch[1]) : (cleanLine.match(/(\d+)/) ? parseInt(cleanLine.match(/(\d+)/)[1]) : 1);

        // Determine Processed status
        const isProcessed = !resultsPart.includes('未處理') && !resultsPart.includes('尚未處理');
        
        // --- Detect Army and Breakdown ---
        let dest = isProcessed ? '已處理' : '未處理';
        let breakdown = { yan_zun: 0, yan_an: 0, long_sheng: 0, long_zhan: 0 };
        
        if (normLine.includes('龍勝') || normLine.includes('龍戰')) {
            dest = '耀紫軍';
        } else if (normLine.includes('閻尊') || normLine.includes('閻闇')) {
            dest = '黑曜軍';
        } else if (resultsPart) {
            // Check if resultsPart explicitly says another destination
            const knownDests = ['耀紫軍', '虎賁軍', '虎甲軍', '黑曜軍', '九天'];
            const foundDest = knownDests.find(d => resultsPart.includes(d));
            if (foundDest) dest = foundDest;
        }

        // Parse sub-quantities if army detected
        if (dest === '耀紫軍') {
            const lsMatch = normLine.match(/龍勝\s*(\d+)/);
            if (lsMatch) breakdown.long_sheng = parseInt(lsMatch[1]);
            const lzMatch = normLine.match(/龍戰\s*(\d+)/);
            if (lzMatch) breakdown.long_zhan = parseInt(lzMatch[1]);
        } else if (dest === '黑曜軍') {
            const yzMatch = normLine.match(/閻尊\s*(\d+)/);
            if (yzMatch) breakdown.yan_zun = parseInt(yzMatch[1]);
            const yaMatch = normLine.match(/閻闇\s*(\d+)/);
            if (yaMatch) breakdown.yan_an = parseInt(yaMatch[1]);
        }

        // Extract remarks: Content inside parentheses ( )
        // 注意：若 rMatch 已從括號擷取了關係詞 (uRemarks)，就不再重複存入 remarks_text
        let finalRemarksText = '';
        if (!rMatch) {
            const pMatch = normLine.match(/[（\(](.*)[）\)]/);
            if (pMatch) {
                finalRemarksText = pMatch[1].trim();
            }
        }
        if (!finalRemarksText && resultsPart) {
            // Fallback: Use resultsPart excluding the quantity
            finalRemarksText = resultsPart.replace(/^\d+\s*位/, '').replace(/^[（\(]|[）\)]$/g, '').trim();
        }

        const finalDate = currentDateFromText || batchDate.value;

        results.push({
            user_name: finalName,
            user_remarks: uRemarks,
            know_date: finalDate,
            process_date: (isProcessed || dest !== '未處理') ? finalDate : null,
            destination: dest,
            quantity: qty,
            remarks: breakdown,
            status: (isProcessed || dest !== '未處理') ? '已處理' : '待處理',
            remarks_text: finalRemarksText
        });
    });

    const finalItems = results;

    if (finalItems.length === 0) {
        persistentToast.value = { msg: '✖ 解析失敗，找不到任何有效法號資料。', type: 'error' };
        return;
    }

    // --- Detailed Preview ---
    const first = finalItems[0];
    const preview = `即將匯入 ${finalItems.length} 筆資料。\n\n[ 第一筆資料預覽 ]\n法號：${first.user_name}${first.user_remarks ? '(' + translateRel(first.user_remarks) + ')' : ''}\n得知日期：${first.know_date}\n處理日期：${first.process_date || '無'}\n狀態：${first.status}\n備註：${first.remarks_text}`;
    
    persistentToast.value = { msg: preview, type: 'preview', data: finalItems };
};

const executeBatchSave = async () => {
    if (!persistentToast.value || !persistentToast.value.data) return;
    const finalItems = persistentToast.value.data;
    
    loading.value = true;
    persistentToast.value = null; // Close preview
    try {
        const response = await axios.post('/grudges/batch', { items: finalItems });
        const saved = response.data;
        const pending = saved.filter(i => i.status === '待處理').length;
        
        persistentToast.value = { 
            msg: `匯入成功！\n共新增/更新 ${saved.length} 筆紀錄\n- 已處理：${saved.length - pending} 筆\n- 待處理：${pending} 筆`, 
            type: 'success' 
        };
        
        batchText.value = '';
        emit('success');
        // Close form after a delay or let user see the success toast
        setTimeout(() => {
            if (persistentToast.value?.type === 'success') {
                handleToastConfirm();
            }
        }, 2000);
    } catch (error) {
        console.error('Batch import failed:', error);
        persistentToast.value = { msg: '匯入失敗：' + (error.response?.data?.message || error.message), type: 'error' };
    } finally {
        loading.value = false;
    }
};
</script>

<style scoped>
.animate-slide-up {
    animation: slideUp 0.5s cubic-bezier(0.16, 1, 0.3, 1);
}
@keyframes slideUp {
    from { opacity: 0; transform: translateY(100%); }
    to { opacity: 1; transform: translateY(0); }
}
.custom-scrollbar::-webkit-scrollbar { width: 5px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
</style>
