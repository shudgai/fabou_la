<template>
    <div class="fixed inset-0 z-[5000] flex flex-col bg-white animate-slide-up">
        <!-- Header -->
        <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between bg-white sticky top-0 z-20">
            <div class="flex items-center">
                <button @click="$emit('cancel')" class="p-2 -ml-2 text-slate-400 active:scale-90 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" /></svg>
                </button>
                <div class="flex items-center gap-2 ml-2">
                    <logo-imperial-notebook :height="36" />
                    <h2 class="text-[24px] font-black text-slate-900 tracking-tight">多筆匯入開文</h2>
                </div>
            </div>
            <button @click="handleSave" :disabled="isSaving || parsedPosts.length === 0" 
                class="bg-indigo-600 text-white px-6 py-2 rounded-2xl font-black text-[15px] active:scale-95 transition-all shadow-lg shadow-indigo-100 disabled:opacity-30" style="color: white !important;">
                {{ isSaving ? '儲存中...' : `儲存 (${parsedPosts.length})` }}
            </button>
        </div>
        <!-- Content -->
        <div class="flex-1 overflow-y-auto p-4 custom-scrollbar bg-slate-50">
            <div class="max-w-xl mx-auto space-y-6">
                <!-- Info Alert -->
                <div class="bg-indigo-50/50 p-4 rounded-2xl border border-indigo-100 flex items-start space-x-3">
                    <div class="w-10 h-10 bg-white rounded-2xl shadow-sm flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="2"/></svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-[15px] font-bold text-slate-700 leading-relaxed">請貼上開文內容，每篇文章請以「---」或「空行」分隔。系統將自動識別日期。</p>
                    </div>
                </div>
                <!-- Input Area -->
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden focus-within:border-indigo-300 transition-all">
                    <textarea 
                        v-model="batchInput" 
                        placeholder="在此貼上多筆內容..."
                        class="w-full min-h-[400px] p-6 text-[17px] font-medium leading-relaxed outline-none resize-none border-none"
                    ></textarea>
                </div>
                <!-- Preview Section -->
                <div v-if="parsedPosts.length > 0" class="space-y-4">
                    <div class="flex items-center justify-between px-2">
                        <span class="text-[17px] font-black text-slate-900 uppercase tracking-widest">內容預覽 ({{ parsedPosts.length }})</span>
                    </div>
                    <div v-for="(post, idx) in parsedPosts" :key="idx" class="bg-white rounded-2xl border border-slate-100 p-5 shadow-sm space-y-3 animate-fade-in relative group">
                        <div class="absolute top-4 right-4 bg-slate-50 px-2.5 py-1 rounded-2xl text-[12px] font-bold text-slate-400">
                            #{{ idx + 1 }}
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="text-[14px] font-black text-indigo-500 font-outfit">{{ post.date || '無日期' }}</span>
                        </div>
                        <div class="h-[1px] bg-slate-50"></div>
                        <p class="text-[15px] text-slate-700 leading-relaxed line-clamp-3 whitespace-pre-wrap">{{ post.original_content }}</p>
                        <div class="flex items-center justify-end">
                            <button @click="removePost(idx)" class="text-[13px] font-bold text-red-400 hover:text-red-600 transition-colors">移除此筆</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Bottom Spacer -->
            <div class="h-24"></div>
        </div>
        <!-- Mobile Navbar -->
        <MobileNavbar 
            :can-back="true"
            :can-home="false"
            :show-action="false"
            :can-search="false"
            @back="$emit('cancel')"
        />
    </div>
</template>
<script setup>
import { ref, computed, watch } from 'vue';
import axios from 'axios';
import MobileNavbar from './MobileNavbar.vue';
const props = defineProps({
    isSaving: Boolean,
    tab: String // 'weekly' or 'self'
});
const emit = defineEmits(['save', 'cancel']);
const batchInput = ref('');
const parsedPosts = ref([]);
const getTodayStr = () => {
    const d = new Date();
    return `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`;
};
watch(batchInput, (newVal) => {
    if (!newVal.trim()) {
        parsedPosts.value = [];
        return;
    }
    // Split by separator or multiple newlines
    const blocks = newVal.split(/---|\n{2,}/).filter(b => b.trim().length > 10);
    const results = [];
    blocks.forEach(block => {
        const cleanBlock = block.trim();
        const lines = cleanBlock.split('\n');
        let date = getTodayStr();
        // Simple date detection in the first few lines
        for (let i = 0; i < Math.min(3, lines.length); i++) {
            const line = lines[i].trim();
            const dateMatch = line.match(/(\d{4})[/\-\s](\d{1,2})[/\-\s](\d{1,2})/);
            if (dateMatch) {
                date = `${dateMatch[1]}-${dateMatch[2].padStart(2, '0')}-${dateMatch[3].padStart(2, '0')}`;
                break;
            }
        }
        results.push({
            date: date,
            original_content: cleanBlock,
            message_type: props.tab === 'self' ? '非玄訊' : undefined,
            status: null
        });
    });
    parsedPosts.value = results;
});
const removePost = (idx) => {
    parsedPosts.value.splice(idx, 1);
};
const handleSave = () => {
    emit('save', parsedPosts.value);
};
</script>
<style scoped>
.animate-slide-up { animation: slideUp 0.4s cubic-bezier(0.16, 1, 0.3, 1); }
@keyframes slideUp { from { transform: translateY(100%); } to { transform: translateY(0); } }
.animate-fade-in { animation: fadeIn 0.3s ease-out; }
@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
.custom-scrollbar::-webkit-scrollbar { width: 6px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
.line-clamp-3 { display: -webkit-box; -webkit-line-clamp: 3; line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }
</style>