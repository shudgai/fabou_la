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
                <div class="space-y-1">
                    <div class="flex items-center justify-between mb-2 ml-1">
                        <label class="app-title">貼上資料</label>
                        <button @click.stop="triggerFileUpload" class="text-[16px] font-black text-indigo-600 bg-indigo-50 px-3 py-1.5 rounded-xl border border-indigo-100 flex items-center space-x-1 active:scale-95 transition-all">
                            <svg class="w-[14px] h-[14px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/><path d="M9 13h6m-6-4h6m-6 8h3" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            <span>載入他檔 (Excel/Word)</span>
                        </button>
                    </div>
                    <textarea 
                        v-model="batchText" 
                        placeholder="" 
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
                    style="color: white !important"
                    class="w-full bg-indigo-600 disabled:bg-slate-300 font-black py-[10px] rounded-xl shadow-lg shadow-indigo-100 hover:bg-indigo-700 active:scale-[0.98] transition-all flex items-center justify-center space-x-2"
                >
                    <span v-if="loading" class="animate-spin h-4 w-4 border-2 border-white/30 border-t-white rounded-full"></span>
                    <span class="text-[16px] tracking-widest">{{ loading ? '正在處理中...' : '確認批量載錄' }}</span>
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
    </div>
</template>

<script setup>
import { ref } from 'vue';
import axios from 'axios';
import MobileNavbar from './MobileNavbar.vue';

const props = defineProps({
    show: Boolean
});

const emit = defineEmits(['save', 'cancel', 'success']);
const batchText = ref('');
const loading = ref(false);

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

const handleBatchSave = async () => {
    const lines = batchText.value.split('\n').map(l => l.trim()).filter(l => l !== '');
    if (lines.length === 0) {
        alert('請先輸入或貼上法號內容。');
        return;
    }

    loading.value = true;
    const finalItems = [];
    const today = new Date().toISOString().split('T')[0];
    let currentProcessDate = today;
    let currentKnowDate = today;

    lines.forEach(line => {
        // 0. Normalize
        let normLine = line.normalize('NFKC').trim();
        if (!normLine) return;

        // NEW: Standalone Year detection (e.g., "113年" or "2024")
        const standaloneYMatch = normLine.match(/^\s*(\d{2,4})\s*[年]?\s*$/);
        if (standaloneYMatch) {
            let y = parseInt(standaloneYMatch[1]);
            if (y < 1000) y += 1911;
            const yDate = `${y}-01-01`;
            currentProcessDate = yDate;
            currentKnowDate = yDate;
            return;
        }

        // 1. Detect date
        const cleanDateStr = normLine.replace(/[年月]/g, '-').replace(/[日]/g, '');
        const dateParts = cleanDateStr.split(/[.\/-]/).map(p => p.trim());

        if (dateParts.length === 3) {
            let year = parseInt(dateParts[0]);
            const month = dateParts[1].padStart(2, '0');
            const day = dateParts[2].padStart(2, '0');
            if (year < 1000) year += 1911;
            const parsedDate = `${year}-${month}-${day}`;
            currentProcessDate = parsedDate;
            currentKnowDate = parsedDate;
            return;
        }
        
        // Detect Short Date (M/D) as a standalone line
        if (dateParts.length === 2 && !isNaN(parseInt(dateParts[0])) && !isNaN(parseInt(dateParts[1]))) {
            const y = currentKnowDate.split('-')[0];
            const m = dateParts[0].padStart(2, '0');
            const d = dateParts[1].padStart(2, '0');
            const parsedDate = `${y}-${m}-${d}`;
            currentProcessDate = parsedDate;
            currentKnowDate = parsedDate;
            return;
        }

        // 2. Clear item numbers
        let cleanLine = normLine.replace(/^(\d+[\.\、\)\s-]+|[（\(]\d+[）\)]\s*|[一二三四五六七八九十]+[\.\、\s-]+)/, '').trim();
        if (!cleanLine) return;

        // Header & Summary Filter: Skip if line contains title/summary keywords
        const skipKeywords = ['法號', '日期', '數量', '備註', '處理', '項次', '結果', '總結', '總計', '總量', '小計', '閻尊', '閻闇', '龍勝', '龍戰'];
        if (skipKeywords.some(key => cleanLine.includes(key))) return;

        // Skip lines that are just remarks in parentheses (continuation lines)
        if (/^[（\(].*[）\)]$/.test(cleanLine)) return;

        // 3. Process Data
        if (cleanLine.includes('—') || cleanLine.includes('–') || cleanLine.includes('-')) {
            const separatorMatch = cleanLine.match(/[—–-]/);
            const separator = separatorMatch[0];
            const [subject, resultsPart] = cleanLine.split(separator).map(s => s.trim());
            
            let name = subject;
            let userRemarks = '';
            const remarksMatch = subject.match(/[（\(](.*?)[）\)]/);
            if (remarksMatch) {
                name = subject.replace(/[（\(].*?[）\)]/, '').trim();
                userRemarks = remarksMatch[1];
            }

            // Split results
            const results = resultsPart.split(/[、,，]\s*(?![^（(]*[）)])/).map(r => r.trim());
            
            // Aggregation Logic: Merging results of the same line into ONE database record
            let totalQuantity = 0;
            let destSummaryArr = [];
            const mergedRemarks = { yan_zun: 0, yan_an: 0, long_sheng: 0, long_zhan: 0 };
            let primaryDest = '未處理';

            results.forEach(res => {
                const numMatch = res.match(/(\d+)/);
                const q = numMatch ? parseInt(numMatch[1]) : 1;
                totalQuantity += q;
                
                let curDest = '未處理';
                const dests = ['虎賁軍', '虎甲軍', '黑曜軍', '耀紫軍', '九天', '暫時驅離', '殲滅'];
                for (const d of dests) {
                    if (res.includes(d)) {
                        curDest = d;
                        primaryDest = d;
                        break;
                    }
                }
                destSummaryArr.push(`${curDest}(${q})`);

                const subMatch = res.match(/[（(](.*?)[）)]/);
                if (subMatch) {
                    const subParts = subMatch[1].split(/[、,，]/);
                    subParts.forEach(sp => {
                        const nMatch = sp.match(/(\d+)/);
                        const n = nMatch ? parseInt(nMatch[1]) : 0;
                        if (sp.includes('大姐') || sp.includes('大姊') || sp.includes('閻尊')) mergedRemarks.yan_zun += n;
                        if (sp.includes('四妹') || sp.includes('闇') || sp.includes('閻闇')) mergedRemarks.yan_an += n;
                        if (sp.includes('勝') || sp.includes('龍勝')) mergedRemarks.long_sheng += n;
                        if (sp.includes('戰') || sp.includes('龍戰')) mergedRemarks.long_zhan += n;
                    });
                } else if (curDest === '黑曜軍' || curDest === '耀紫軍') {
                    if (res.includes('大姐') || res.includes('大姊') || res.includes('閻尊')) mergedRemarks.yan_zun += q;
                    if (res.includes('四妹') || res.includes('闇') || res.includes('閻闇')) mergedRemarks.yan_an += q;
                    if (res.includes('勝') || res.includes('龍勝')) mergedRemarks.long_sheng += q;
                    if (res.includes('戰') || res.includes('龍戰')) mergedRemarks.long_zhan += q;
                }
            });

            const finalDisplayDest = results.length > 1 ? destSummaryArr.join('、') : primaryDest;

            finalItems.push({
                user_name: name,
                user_remarks: userRemarks,
                know_date: currentKnowDate,
                process_date: finalDisplayDest === '未處理' ? null : currentProcessDate,
                destination: finalDisplayDest,
                quantity: totalQuantity,
                remarks: mergedRemarks,
                status: finalDisplayDest === '未處理' ? '待處理' : '已處理',
                remarks_text: ''
            });
        } else {
            finalItems.push({
                user_name: cleanLine,
                user_remarks: '',
                know_date: currentKnowDate,
                destination: '未處理',
                quantity: 1,
                remarks: { yan_zun: 0, yan_an: 0, long_sheng: 0, long_zhan: 0 },
                status: '待處理',
                remarks_text: ''
            });
        }
    });

    try {
        await axios.post('/grudges/batch', { items: finalItems });
        batchText.value = '';
        emit('success');
        emit('cancel');
    } catch (error) {
        console.error('Batch import failed:', error);
        alert('解析或匯入失敗，請檢查資料格式是否符合範例。');
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
