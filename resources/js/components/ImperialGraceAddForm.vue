<template>
    <div v-if="mode" class="fixed inset-0 z-[70] flex items-end justify-center px-0">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm" @click="$emit('cancel')"></div>
        
        <!-- Form Container -->
        <div class="relative w-full max-w-2xl bg-white rounded-t-[32px] shadow-[0_-10px_40px_rgba(0,0,0,0.1)] overflow-hidden animate-slide-up flex flex-col max-h-[90vh]">
            <!-- Header -->
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-white sticky top-0 z-10">
                <h3 class="text-lg font-bold text-slate-800">
                    {{ mode === 'single' ? (initialData.id ? '修改重大皇恩' : '新增重大皇恩') : '多筆次新增' }}
                </h3>
                <button @click="$emit('cancel')" class="p-2 text-slate-400 hover:text-slate-600 active:scale-95">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
            </div>

            <!-- Scrollable Content -->
            <div class="flex-1 overflow-y-auto p-6 space-y-4 custom-scrollbar">
                <!-- SINGLE MODE -->
                <div v-if="mode === 'single'" class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-500 uppercase ml-1">得知日期</label>
                            <input v-model="form.record_date" type="date" class="w-full rounded-2xl border-slate-100 bg-slate-50 p-3 text-sm focus:ring-2 focus:ring-indigo-500/20">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-500 uppercase ml-1">所屬仙師</label>
                            <select v-model="form.master_id" class="w-full rounded-2xl border-slate-100 bg-slate-50 p-3 text-sm focus:ring-2 focus:ring-indigo-500/20">
                                <option v-for="m in masters" :key="m.id" :value="m.id">{{ m.name }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-500 uppercase ml-1">法寶名稱</label>
                        <input v-model="form.name" type="text" placeholder="輸入法寶名稱" class="w-full rounded-2xl border-slate-100 bg-slate-50 p-3 text-sm focus:ring-2 focus:ring-indigo-500/20">
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-500 uppercase ml-1">法寶用意</label>
                        <input v-model="form.purpose" type="text" placeholder="輸入法寶用意" class="w-full rounded-2xl border-slate-100 bg-slate-50 p-3 text-sm focus:ring-2 focus:ring-indigo-500/20">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-500 uppercase ml-1" :class="{ 'opacity-30': form.status === '未求得' }">求得日期</label>
                            <input v-model="form.obtained_date" type="date" :disabled="form.status === '未求得'"
                                class="w-full rounded-2xl border-slate-100 p-3 text-sm focus:ring-2 focus:ring-indigo-500/20"
                                :class="form.status === '未求得' ? 'bg-slate-100 opacity-50' : 'bg-slate-50'">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-500 uppercase ml-1">狀態</label>
                            <select v-model="form.status" @change="handleStatusChange" class="w-full rounded-2xl border-slate-100 bg-slate-50 p-3 text-sm focus:ring-2 focus:ring-indigo-500/20 font-bold"
                                :class="form.status === '未求得' ? 'text-slate-400' : 'text-emerald-600'">
                                <option value="未求得">未求得</option>
                                <option value="已求得">已求得</option>
                                <option value="已登記">已登記</option>
                            </select>
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-500 uppercase ml-1">備註</label>
                        <textarea v-model="form.remarks" rows="3" placeholder="其他補充說明..." class="w-full rounded-2xl border-slate-100 bg-slate-50 p-3 text-sm focus:ring-2 focus:ring-indigo-500/20"></textarea>
                    </div>
                </div>

                <!-- BATCH MODE -->
                <div v-if="mode === 'batch'" class="space-y-4">
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-500 uppercase ml-1">歸屬仙師</label>
                        <select v-model="batchMasterId" class="w-full rounded-2xl border-slate-100 bg-slate-50 p-3 text-sm font-bold text-indigo-600">
                            <option :value="null">請先選擇仙師</option>
                            <option v-for="m in masters" :key="m.id" :value="m.id">{{ m.name }}</option>
                        </select>
                    </div>

                    <div class="p-4 bg-indigo-50 rounded-2xl space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-indigo-500 uppercase">貼入清單內容</span>
                            <button @click="$refs.fileInput.click()" class="text-xs text-indigo-600 font-bold flex items-center hover:underline">
                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-8l-4-4m0 0l-4 4m4-4v12" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                匯入檔案
                            </button>
                        </div>
                        <textarea v-model="batchInput" rows="10" 
                            class="w-full rounded-xl border-none shadow-inner text-xs bg-white focus:ring-2 focus:ring-indigo-500/20 p-3" 
                            placeholder="支援複製 LINE 內容快速解析匯入..."></textarea>
                        <input type="file" ref="fileInput" class="hidden" @change="handleFileUpload" accept=".txt">
                    </div>
                </div>
            </div>

            <!-- Footer Action -->
            <div class="p-6 border-t border-slate-100 bg-slate-50/50">
                <button 
                    @click="handleSubmit" 
                    :disabled="isSaving"
                    class="w-full bg-indigo-600 text-white font-bold py-4 rounded-2xl shadow-lg shadow-indigo-200 hover:bg-indigo-700 active:scale-[0.98] transition-all disabled:bg-slate-300 disabled:shadow-none"
                >
                    {{ isSaving ? '儲存中...' : (mode === 'single' ? '確認儲存' : '開始解析並新增') }}
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
    mode: String,
    initialData: Object,
    masters: Array,
    isSaving: Boolean
});

const emit = defineEmits(['saveSingle', 'saveBatch', 'cancel', 'fileUpload']);

const form = ref({ ...props.initialData });
const batchInput = ref('');
const batchMasterId = ref(props.initialData?.master_id || null);

watch(() => props.initialData, (newVal) => {
    form.value = { ...newVal };
    if (props.mode === 'batch') batchMasterId.value = newVal.master_id;
}, { deep: true });

const handleStatusChange = () => {
    if (form.value.status === '未求得') form.value.obtained_date = '';
};

const handleSubmit = () => {
    if (props.mode === 'single') {
        emit('saveSingle', form.value);
    } else {
        emit('saveBatch', { input: batchInput.value, masterId: batchMasterId.value });
    }
};

const handleFileUpload = (e) => {
    emit('fileUpload', e);
};
</script>

<style scoped>
.animate-slide-up {
    animation: slideUp 0.5s cubic-bezier(0.16, 1, 0.3, 1);
}
@keyframes slideUp {
    from { transform: translateY(100%); }
    to { transform: translateY(0); }
}
</style>
