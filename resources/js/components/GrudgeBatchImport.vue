<template>
    <div v-if="show" class="fixed inset-0 z-[70] flex items-end md:items-center justify-center px-0">
        <!-- Backdrop (Desktop Only) -->
        <div class="hidden md:block fixed inset-0 bg-slate-900/40 backdrop-blur-sm" @click="$emit('cancel')"></div>
        
        <!-- Form Container -->
        <div class="relative w-full h-full md:h-auto md:max-h-[90vh] md:max-w-2xl bg-white md:rounded-[32px] shadow-[0_-10px_40px_rgba(0,0,0,0.1)] overflow-hidden animate-slide-up flex flex-col">
            <!-- Header -->
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-white sticky top-0 z-10">
                <h3 class="text-[23px] font-black tracking-tight text-slate-900">
                    怨靈多筆載錄
                </h3>
                <button @click="$emit('cancel')" class="p-2 text-black hover:text-slate-600 active:scale-95">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
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

                <div class="space-y-1 relative">
                    <div class="flex items-center justify-between mb-2 ml-1">
                        <label class="app-title">貼上資料</label>
                        <div class="flex items-center space-x-2">
                            <button v-if="batchText" @click="batchText = ''" class="text-[14px] font-bold text-red-500 bg-red-50 px-3 py-1.5 rounded-xl border border-red-100 active:scale-95 transition-all">
                                清空
                            </button>
                            <button @click.stop="triggerFileUpload" class="text-[16px] font-black text-indigo-600 bg-indigo-50 px-3 py-1.5 rounded-xl border border-indigo-100 flex items-center space-x-1 active:scale-95 transition-all">
                                <svg class="w-[14px] h-[14px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/><path d="M9 13h6m-6-4h6m-6 8h3" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                <span>載入他檔</span>
                            </button>
                        </div>
                    </div>
                    <textarea 
                        v-model="batchText" 
                        rows="15"
                        @click.stop
                        class="w-full app-body p-4 bg-slate-50 rounded-2xl border-none focus:ring-2 focus:ring-indigo-500 min-h-[450px] leading-relaxed"
                    ></textarea>
                </div>
            </div>

            <!-- Footer Action -->
            <div class="px-4 pb-24 bg-white border-t border-slate-100">
                <button 
                    @click="handleBatchSave" 
                    :disabled="loading"
                    class="w-full bg-indigo-600 disabled:bg-slate-300 py-[12px] rounded-2xl shadow-xl shadow-indigo-100 hover:bg-indigo-700 active:scale-[0.98] transition-all flex items-center justify-center space-x-2"
                >
                    <span v-if="loading" class="animate-spin h-5 w-5 border-2 border-white/30 border-t-white rounded-full"></span>
                    <span class="text-[18px] tracking-widest font-black" style="color: white !important;">{{ loading ? '正在處理中...' : '確認批量載錄' }}</span>
                </button>
            </div>

            <!-- Global Mobile Navbar -->
            <mobile-navbar 
                @back="$emit('cancel')"
                @home="$emit('cancel')"
                :show-action="false"
                :can-search="false"
            />
        </div>

        <!-- Custom Date Picker -->
        <compact-date-picker 
            v-if="showDatePicker" 
            v-model="batchDate" 
            title="基準日期"
            @close="showDatePicker = false" 
        />
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
const batchDate = ref(getTodayStr());

const triggerFileUpload = () => {
    const input = document.createElement('input');
    input.type = 'file';
    input.accept = '.xlsx, .xls, .doc, .docx';
    input.onchange = (e) => {
        const file = e.target.files[0];
        if (file) {
            alert(`已讀取檔案「${file.name}」，解析功能串接中。`);
        }
    };
    input.click();
};

const nameAliasMap = {
    '金容': '靈果', '金涓': '靈慧', '金梅': '靈妙', '金蘭': '靈智', '金平': '靈平',
    '金瑞': '龍戰', '金耀': '龍勝', '金旭': '靈心', '金熙': '靈情', '金吉': '靈奇',
    '金祥': '靈傾', '金恩': '靈昡', '金鈺': '元續', '金穎': '赤峰',
    '金律': '閻㻇', '金欣': '閻闇', '閰琉': '閻尊', '金剛': '閰帝', '金頓': '閻爵',
    '金虹': '赤覺', '金湘': '紫元', '金雍': '道妙', '金無': '閻澤', '金真': '閻願',
    '金翎': '鳳尊', '金妙': '鳳媓'
};
const translateName = (n) => nameAliasMap[n] || null;

const handleBatchSave = async () => {
    const lines = batchText.value.split('\n').map(l => l.trim()).filter(l => l !== '');
    if (lines.length === 0) {
        alert('請先輸入或貼上法號內容。');
        return;
    }

    const nameMap = {
        '金容': '靈果', '金涓': '靈慧', '金梅': '靈妙', '金蘭': '靈智', '金平': '靈平',
        '金瑞': '龍戰', '金耀': '龍勝', '金旭': '靈心', '金熙': '靈情', '金吉': '靈奇',
        '金祥': '靈傾', '金恩': '靈昡', '金鈺': '元續', '金穎': '赤峰',
        '金律': '閻㻇', '金欣': '閻闇', '閰琉': '閻尊', '金剛': '閰帝', '金頓': '閻爵',
        '金虹': '赤覺', '金湘': '紫元', '金雍': '道妙', '金無': '閻澤', '金真': '閻願',
        '金翎': '鳳尊', '金妙': '鳳媓'
    };
    const translate = (n) => nameMap[n] || n;

    const results = [];
    let currentDate = batchDate.value;

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
            currentDate = dateHeader;
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
        const rMatch = subject.match(/[（\(](.*?)[）\)]/);
        if (rMatch) {
            rawName = subject.replace(/[（\(].*?[）\)]/, '').trim();
            uRemarks = rMatch[1];
        }

        // Clean digits from name only if they are at the end
        rawName = rawName.replace(/\d+$/, '').trim();
        let finalName = translate(rawName);

        // Extract quantity (Priority to "XX位")
        const qtyMatch = cleanLine.match(/(\d+)\s*位/);
        const qty = qtyMatch ? parseInt(qtyMatch[1]) : (cleanLine.match(/(\d+)/) ? parseInt(cleanLine.match(/(\d+)/)[1]) : 1);

        // Determine Processed status (Always '已處理' unless '未處理' is specified)
        const isProcessed = !resultsPart.includes('未處理') && !resultsPart.includes('尚未處理');
        const dest = isProcessed ? '已處理' : '未處理';

        // Extract remarks: Content inside parentheses ( )
        let finalRemarksText = '';
        const pMatch = normLine.match(/[（\(](.*)[）\)]/); // Use normLine to be more inclusive
        if (pMatch) {
            finalRemarksText = pMatch[1].trim();
        } else if (resultsPart) {
            // Fallback: Use resultsPart excluding the quantity
            finalRemarksText = resultsPart.replace(/^\d+\s*位/, '').replace(/^[（\(]|[）\)]$/g, '').trim();
        }

        results.push({
            user_name: finalName,
            user_remarks: uRemarks,
            know_date: currentDate,
            process_date: isProcessed ? currentDate : null,
            destination: dest,
            quantity: qty,
            remarks: { yan_zun: 0, yan_an: 0, long_sheng: 0, long_zhan: 0 },
            status: isProcessed ? '已處理' : '待處理',
            remarks_text: finalRemarksText
        });
    });

    const finalItems = results;

    if (finalItems.length === 0) {
        alert('解析失敗，找不到任何有效法號資料。');
        return;
    }

    // --- Detailed Preview ---
    const first = finalItems[0];
    const preview = `即將匯入 ${finalItems.length} 筆資料。\n\n[ 第一筆資料預覽 ]\n法號：${first.user_name}\n得知日期：${first.know_date}\n處理日期：${first.process_date || '無'}\n狀態：${first.status}\n備註：${first.remarks_text}\n\n是否繼續？`;
    
    if (!confirm(preview)) return;

    loading.value = true;
    try {
        const response = await axios.post('/grudges/batch', { items: finalItems });
        const saved = response.data;
        const pending = saved.filter(i => i.status === '待處理').length;
        alert(`匯入成功！\n共新增/更新 ${saved.length} 筆紀錄\n- 已處理：${saved.length - pending} 筆\n- 待處理：${pending} 筆`);
        batchText.value = '';
        emit('success');
        emit('cancel');
    } catch (error) {
        console.error('Batch import failed:', error);
        alert('匯入失敗：' + (error.response?.data?.message || error.message));
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
.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
</style>
