<template>
    <div v-if="mode" class="fixed inset-0 z-[70] flex items-end md:items-center justify-center px-0">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm" @click="$emit('cancel')"></div>
        
        <!-- Form Container -->
        <div class="relative w-full h-full md:h-auto md:max-h-[95vh] md:max-w-md bg-white md:rounded-[32px] shadow-[0_-10px_40px_rgba(0,0,0,0.1)] overflow-hidden animate-slide-up flex flex-col">
            
            <!-- Header -->
            <div class="px-6 py-4 flex items-center justify-between bg-white border-b border-slate-50">
                <div class="flex items-center space-x-2">
                    <button @click="$emit('cancel')" class="text-indigo-500 font-medium flex items-center hover:opacity-70">
                        <span class="mr-1">&lt;</span>返回
                    </button>
                    <h3 class="text-[22px] font-black text-slate-900 tracking-tight">
                        {{ (form.category === 'major' ? '重大皇恩登記簿' : '其他皇恩登記簿') }} - {{ selectedMasterName || '請選擇仙師' }}
                    </h3>
                </div>
            </div>

            <!-- Scrollable Content -->
            <div class="flex-1 overflow-y-auto px-6 py-4 space-y-5 custom-scrollbar bg-white">
                
                <!-- Date & Master -->
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="text-[15px] font-bold text-slate-500 block ml-1 uppercase tracking-wider">日期</label>
                        <div @click="activePicker = { idx: 'main', field: 'record_date', title: '設定主要日期' }" 
                            class="w-full h-[46px] rounded-2xl border border-slate-100 bg-white px-4 flex items-center justify-between cursor-pointer shadow-sm active:scale-[0.98] transition-all">
                            <span :class="form.record_date ? 'text-slate-900' : 'text-slate-300'" class="text-[18px] font-bold font-outfit uppercase">
                                {{ form.record_date ? form.record_date.replace(/-/g, '/') : '年 / 月 / 日' }}
                            </span>
                            <svg class="w-5 h-5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </div>
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-[15px] font-bold text-slate-500 block ml-1 uppercase tracking-wider">仙師</label>
                        <select v-model="form.master_id" class="w-full h-[46px] rounded-2xl border border-slate-100 bg-white px-4 text-[18px] font-bold text-slate-900 shadow-sm focus:ring-2 focus:ring-indigo-100 outline-none">
                            <option v-for="m in masters" :key="m.id" :value="m.id">{{ m.name }}</option>
                        </select>
                    </div>
                </div>

                <!-- Main Fields (Single Mode) -->
                <div v-if="localMode === 'single'" class="space-y-4 animate-fade-in">
                    <div class="space-y-1.5">
                        <label class="text-[15px] font-bold text-slate-500 block ml-1 uppercase tracking-wider">法寶 / 皇恩名稱</label>
                        <input v-model="form.name" type="text" placeholder="輸入法寶名稱" class="w-full h-[46px] rounded-2xl border border-slate-100 bg-white px-4 text-[18px] font-bold text-slate-900 focus:ring-2 focus:ring-indigo-100 outline-none shadow-sm">
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-[15px] font-bold text-slate-500 block ml-1 uppercase tracking-wider">法寶用意</label>
                        <input v-model="form.purpose" type="text" placeholder="輸入法寶用意" class="w-full h-[46px] rounded-2xl border border-slate-100 bg-white px-4 text-[18px] font-bold text-slate-900 focus:ring-2 focus:ring-indigo-100 outline-none shadow-sm">
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-[15px] font-bold text-slate-500 block ml-1 uppercase tracking-wider">求寶方式</label>
                        <input v-model="form.acquisition_method" type="text" placeholder="輸入求寶方式" class="w-full h-[46px] rounded-2xl border border-slate-100 bg-white px-4 text-[18px] font-bold text-slate-900 focus:ring-2 focus:ring-indigo-100 outline-none shadow-sm">
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-[15px] font-bold text-slate-500 block ml-1 uppercase tracking-wider">備註 (整體)</label>
                        <input v-model="form.remarks" type="text" placeholder="輸入備註 (選填)" class="w-full h-[46px] rounded-2xl border border-slate-100 bg-white px-4 text-[18px] font-bold text-slate-900 focus:ring-2 focus:ring-indigo-100 outline-none shadow-sm">
                    </div>

                    <!-- Personnel List -->
                    <div class="pt-4 border-t border-slate-100 space-y-3">
                        <div class="flex items-center justify-between ml-1">
                            <label class="text-[15px] font-black text-indigo-700 uppercase tracking-tight">承接人員</label>
                            <button @click="addPersonnelRow" class="text-[12px] font-bold text-indigo-500 hover:text-indigo-600 active:scale-95 transition-all">
                                ＋ 新增人員
                            </button>
                        </div>

                        <div v-for="(p, idx) in personnel" :key="idx" class="p-4 bg-slate-50/50 rounded-2xl border border-slate-100 space-y-3 relative group animate-fade-in">
                            <button @click="removePersonnelRow(idx)" class="absolute top-2 right-2 text-slate-300 hover:text-red-500 transition-colors">✕</button>
                            
                            <div class="grid grid-cols-2 gap-3">
                                <div class="space-y-1">
                                    <label class="text-[11px] text-slate-400 ml-1 font-black">法號</label>
                                    <input v-model="p.custom_name" type="text" placeholder="法號" list="dharma-names"
                                        class="w-full h-[36px] rounded-xl border border-slate-200 bg-white px-3 text-[18px] font-black text-slate-900 focus:ring-2 focus:ring-indigo-100 outline-none font-outfit">
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[11px] text-slate-400 ml-1">日期</label>
                                    <div @click="activePicker = { idx, field: 'obtained_date', title: p.custom_name || '設定取得日期' }" 
                                        class="w-full h-[36px] rounded-xl border border-slate-200 bg-white px-2 flex items-center justify-between cursor-pointer hover:bg-slate-50 transition-colors">
                                        <span :class="p.obtained_date ? 'text-slate-900' : 'text-slate-300'" class="text-[14px] font-bold font-outfit uppercase">
                                            {{ p.obtained_date ? p.obtained_date.replace(/-/g, '/') : '年/月/日' }}
                                        </span>
                                        <svg class="w-4 h-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    </div>
                                </div>
                            </div>
                            <div class="space-y-1">
                                <input v-model="p.remarks" type="text" placeholder="個人備註 (選填)"
                                    class="w-full h-[36px] rounded-xl border border-slate-200 bg-white px-3 text-[16px] font-normal text-slate-900 focus:ring-2 focus:ring-indigo-100 outline-none">
                            </div>
                        </div>

                        <div v-if="personnel.length === 0" class="text-center py-6 bg-slate-50/30 rounded-2xl border border-dashed border-slate-200 text-slate-300 text-[13px]">
                            尚無人員紀錄
                        </div>
                    </div>
                </div>

                <!-- Batch Mode -->
                <div v-if="localMode === 'batch'" class="space-y-3 animate-fade-in">
                    <div class="flex items-center justify-between ml-1">
                        <label class="text-[14px] font-normal text-slate-400 block">批量內容 (支援一筆一人或一筆多人)</label>
                        <button @click="handleSubmit" :disabled="isSaving" class="bg-indigo-50 text-indigo-600 font-bold text-[11px] py-1 px-3 rounded-xl hover:bg-indigo-100 active:scale-95 transition-all">
                            確認一次新增
                        </button>
                    </div>
                    <textarea v-model="batchInput" rows="10" 
                        class="w-full rounded-2xl border border-slate-100 shadow-sm text-[16px] font-normal text-slate-900 bg-white focus:ring-2 focus:ring-indigo-100 p-4" 
                        placeholder="請貼上內容..."></textarea>
                </div>
                <!-- Dharma Names Auto-complete List -->
                <datalist id="dharma-names">
                    <option v-for="dn in dharmaNames" :key="dn.id" :value="dn.name" />
                </datalist>
            </div>

            <!-- Footer Action -->
            <div v-if="localMode === 'single'" class="px-6 pt-5 pb-[calc(1.25rem+8vh)] bg-white border-t border-slate-50 shadow-[0_-4px_20px_rgba(0,0,0,0.02)]">
                <button @click="handleSubmit" :disabled="isSaving" class="w-full bg-indigo-600 text-white font-black h-[52px] rounded-2xl shadow-md active:scale-95 transition-all disabled:bg-slate-200 disabled:text-slate-400">
                    {{ isSaving ? '儲存中...' : '確認新增' }}
                </button>
            </div>
        </div>

        <compact-date-picker 
            v-if="activePicker && activePicker.idx === 'main'"
            v-model="form[activePicker.field]"
            :title="activePicker.title"
            @close="activePicker = null"
        />
        <compact-date-picker 
            v-if="activePicker && activePicker.idx !== 'main'"
            v-model="personnel[activePicker.idx][activePicker.field]"
            :title="activePicker.title"
            @close="activePicker = null"
        />
    </div>
</template>

<script setup>
import { ref, watch, computed, onMounted } from 'vue';
import axios from 'axios';

const props = defineProps({
    mode: String,
    initialData: Object,
    masters: Array,
    isSaving: Boolean
});

const emit = defineEmits(['saveSingle', 'saveBatch', 'cancel']);

import CompactDatePicker from './CompactDatePicker.vue';

const localMode = ref(props.mode || 'single');
const form = ref({ ...props.initialData });
const batchInput = ref('');
const personnel = ref([]);
const dharmaNames = ref([]);
const activePicker = ref(null); // { idx: number | 'main', field: string, title: string }

const fetchDharmaNames = async () => {
    try {
        const res = await axios.get('/api/dharma-names-list');
        dharmaNames.value = res.data;
    } catch (e) {
        console.error('Failed to load dharma names', e);
    }
};

const addPersonnelRow = () => {
    personnel.value.push({
        custom_name: '',
        obtained_date: '',
        remarks: ''
    });
};

onMounted(() => {
    fetchDharmaNames();
    if (personnel.value.length === 0) addPersonnelRow();
});

const removePersonnelRow = (idx) => {
    personnel.value.splice(idx, 1);
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
    // Clean up personnel: remove empty rows
    const cleanedPersonnel = personnel.value.filter(p => p.custom_name && p.custom_name.trim() !== '');

    if (localMode.value === 'single') {
        const payload = { ...form.value, dharma_name_registries: cleanedPersonnel };
        emit('saveSingle', payload);
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
</script>

<style scoped>
.animate-slide-up { animation: slideUp 0.4s cubic-bezier(0.16, 1, 0.3, 1); }
.animate-fade-in { animation: fadeIn 0.3s ease-out; }
@keyframes slideUp { from { transform: translateY(100%); } to { transform: translateY(0); } }
@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
</style>
