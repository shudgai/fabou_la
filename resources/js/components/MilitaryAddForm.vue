<template>
    <teleport to="body">
    <div v-if="show" class="fixed inset-0 z-[3500] flex items-end md:items-center md:justify-center p-0 md:p-6 animate-fade-in">
        <div class="hidden md:block fixed inset-0 bg-slate-900/40 backdrop-blur-sm" @click="handleCancel"></div>

        <div class="relative w-full h-full md:h-auto md:max-h-[90dvh] md:max-w-lg bg-white md:rounded-[32px] md:shadow-2xl flex flex-col overflow-hidden">
            <!-- Header -->
            <div class="px-[10px] py-[12px] flex items-center bg-white border-b border-slate-50 relative shrink-0">
                <div class="flex-1 flex items-center gap-2 min-w-0">
                    <logo-imperial-notebook :height="36" class="md:hidden" />
                    <div class="flex flex-col justify-center min-w-0">
                        <div class="font-bold leading-none font-outfit uppercase tracking-wider text-slate-900" style="font-size: 25px !important;">
                            軍隊載錄專區
                        </div>
                        <div class="flex items-center gap-1 mt-2">
                            <span class="font-bold truncate font-outfit text-slate-900" style="font-size: 24px !important;">
                                {{ props.armyType || '虎甲軍' }}
                            </span>
                            <span class="font-bold font-outfit text-slate-400" style="font-size: 24px !important;">｜</span>
                            <span class="font-bold font-outfit text-slate-900" style="font-size: 24px !important;">
                                {{ props.isCumulative ? '原始數量' : (editingId ? '修改內容' : '逐筆新增') }}
                            </span>
                        </div>
                    </div>
                </div>
                <button @click="handleCancel" class="text-slate-300 hover:text-slate-600 transition-colors p-2 absolute right-4 top-1/2 -translate-y-1/2 z-[50]">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto p-4 flex flex-col gap-4 text-[17px]" style="padding-bottom: calc(7dvh + 70px);">
                <!-- Step indicator (Matches GrudgeAddForm style) -->
                <div class="px-2 pb-4 bg-white shrink-0">
                    <div class="flex items-center gap-1.5">
                        <div v-for="s in totalSteps" :key="s" class="h-1.5 flex-1 rounded-full transition-all duration-500"
                             :class="s <= currentStep ? 'bg-indigo-500 shadow-[0_0_8px_rgba(99,102,241,0.4)]' : 'bg-slate-100'"></div>
                    </div>
                    <div class="flex justify-between mt-2 px-1">
                        <span class="text-[11px] font-black text-slate-300 uppercase tracking-[0.2em]">Step {{ currentStep }} / {{ totalSteps }}</span>
                        <span class="text-[11px] font-black text-indigo-500 uppercase tracking-[0.2em]">{{ stepLabels[currentStep - 1] }}</span>
                    </div>
                </div>

                <!-- Step 1: Date -->
                <div v-if="currentStep === 1" class="space-y-1">
                    <div class="flex items-center justify-between">
                        <label class="font-black text-slate-400 uppercase tracking-wider">得知日期</label>
                        <button @click.stop="showDatePicker = true" class="text-slate-300 hover:text-slate-600 transition-colors p-1 active:scale-90">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </button>
                    </div>
                    <input v-model="form.know_date" type="text" placeholder="年/月/日 或 註記文字"
                           class="w-full border-0 border-b-2 border-slate-300 bg-transparent py-[10px] px-2 focus:ring-0 outline-none text-center">
                </div>

                <!-- Step 2: 法號與備註對象 (skip if cumulative) -->
                <div v-if="currentStep === 2 && !props.isCumulative" class="space-y-4">
                    <!-- 法號 -->
                    <div class="space-y-1 relative dharma-dropdown">
                        <label class="font-bold text-slate-400 uppercase tracking-wider">法號</label>
                        <div class="relative flex items-center border-0 border-b-2 border-slate-300 bg-transparent overflow-visible min-h-[36px]">
                            <input v-model="dharmaSearch" type="text" placeholder="搜尋或選擇法號..."
                                   @focus="activeDharmaDropdown = true"
                                   class="w-full bg-transparent border-none px-2 py-1 text-center outline-none">
                            <button @click.stop="activeDharmaDropdown = !activeDharmaDropdown" class="p-1 text-slate-300 hover:text-slate-600 transition-all shrink-0">
                                <svg class="w-4 h-4" :class="activeDharmaDropdown ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </button>
                            <input type="hidden" v-model="form.user_name" />
                            <div v-if="activeDharmaDropdown" class="absolute left-0 top-full mt-1 w-full bg-white shadow-xl z-[2100] overflow-hidden animate-fade-in max-h-48 overflow-y-auto">
                                <div v-for="u in filteredDharmaNames" :key="u.id"
                                     @click.stop="selectDharma(u.name)"
                                     class="px-3 py-2 text-slate-700 cursor-pointer transition-colors">
                                    {{ u.name }}
                                </div>
                                <div v-if="filteredDharmaNames.length === 0" class="px-3 py-2 text-slate-400 text-center">
                                    無符合的法號
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 備註對象 -->
                    <div class="space-y-1 relative remarks-dropdown">
                        <label class="font-bold text-slate-400 uppercase tracking-wider">備註對象</label>
                        <div class="relative flex items-center border-0 border-b-2 border-slate-300 bg-transparent overflow-visible min-h-[36px]">
                            <input v-model="form.user_remarks" type="text" placeholder="備註對象（例如：母親）..."
                                   @focus="activeRemarksDropdown = true"
                                   class="w-full bg-transparent border-none px-2 py-1 text-center outline-none">
                            <button @click.stop="activeRemarksDropdown = !activeRemarksDropdown" class="p-1 text-slate-300 hover:text-slate-600 transition-all shrink-0">
                                <svg class="w-4 h-4" :class="activeRemarksDropdown ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </button>
                            <div v-if="activeRemarksDropdown" class="absolute left-0 top-full mt-1 w-full bg-white shadow-xl z-[2100] overflow-hidden animate-fade-in max-h-48 overflow-y-auto">
                                <div v-for="opt in relationshipOptions" :key="opt"
                                     @click.stop="form.user_remarks = opt; activeRemarksDropdown = false"
                                     class="px-3 py-2 text-slate-700 cursor-pointer transition-colors">
                                    {{ opt }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 3 (cumulative: quantity; non-cumulative: army-specific quantity) -->
                <div v-if="(props.isCumulative && currentStep === 3) || (!props.isCumulative && currentStep === 3)" class="space-y-2">
                    <template v-if="props.armyType === '黑曜軍'">
                        <div class="flex gap-2 items-center">
                            <label class="font-bold text-slate-400 uppercase tracking-wider w-12">閻尊</label>
                            <input type="text" v-model="form.yan_zun" placeholder="輸入數量" class="flex-1 border-0 border-b-2 border-slate-300 bg-transparent px-2 py-1 outline-none focus:border-slate-900 text-center" />
                        </div>
                        <div class="flex gap-2 items-center">
                            <label class="font-bold text-slate-400 uppercase tracking-wider w-12">閻閽</label>
                            <input type="text" v-model="form.yan_an" placeholder="輸入數量" class="flex-1 border-0 border-b-2 border-slate-300 bg-transparent px-2 py-1 outline-none focus:border-slate-900 text-center" />
                        </div>
                        <div class="flex gap-2 items-center mt-2 pt-2 border-t border-slate-50">
                            <label class="font-bold text-slate-400 uppercase tracking-wider w-12">總量</label>
                            <input type="text" v-model="form.quantity" placeholder="總計數量" class="flex-1 border-0 border-b-2 border-slate-300 bg-transparent px-2 py-1 outline-none focus:border-slate-900 text-center font-bold" />
                        </div>
                    </template>
                    <template v-else-if="props.armyType === '耀紫軍'">
                        <div class="flex gap-2 items-center">
                            <label class="font-bold text-slate-400 uppercase tracking-wider w-12">龍勝</label>
                            <input type="text" v-model="form.long_sheng" placeholder="輸入數量" class="flex-1 border-0 border-b-2 border-slate-300 bg-transparent px-2 py-1 outline-none focus:border-slate-900 text-center" />
                        </div>
                        <div class="flex gap-2 items-center">
                            <label class="font-bold text-slate-400 uppercase tracking-wider w-12">龍戰</label>
                            <input type="text" v-model="form.long_zhan" placeholder="輸入數量" class="flex-1 border-0 border-b-2 border-slate-300 bg-transparent px-2 py-1 outline-none focus:border-slate-900 text-center" />
                        </div>
                        <div class="flex gap-2 items-center mt-2 pt-2 border-t border-slate-50">
                            <label class="font-bold text-slate-400 uppercase tracking-wider w-12">總量</label>
                            <input type="text" v-model="form.quantity" placeholder="總計數量" class="flex-1 border-0 border-b-2 border-slate-300 bg-transparent px-2 py-1 outline-none focus:border-slate-900 text-center font-bold" />
                        </div>
                    </template>
                    <template v-else>
                        <label class="font-bold text-slate-400 uppercase tracking-wider">數量</label>
                        <input type="text" v-model="form.quantity" placeholder="輸入數量" class="w-full border-0 border-b-2 border-slate-300 bg-transparent px-2 py-1 outline-none focus:border-slate-900 text-center" />
                    </template>
                </div>

                <!-- Step: Remarks (3 for cumulative, 4 for non-cumulative) -->
                <div v-if="(props.isCumulative && currentStep === (hasQuantity ? 4 : 3)) || (!props.isCumulative && currentStep === 4)" class="space-y-1">
                    <label class="font-bold text-slate-400 uppercase tracking-wider">備註文字</label>
                    <input type="text" v-model="form.remarks_text" placeholder="備註文字" class="w-full border-0 border-b-2 border-slate-300 bg-transparent px-2 py-1 outline-none focus:border-slate-900 text-center" />
                </div>

                <!-- Last step: Preview -->
                <div v-if="currentStep === totalSteps" :key="'preview'" class="bg-white p-6 rounded-[32px] border border-slate-100 shadow-xl space-y-4">
                    <div class="text-[14px] font-black text-slate-400 uppercase tracking-[0.3em] border-b border-slate-50 pb-2 text-center">最終載錄預覽</div>
                    <div class="space-y-3">
                        <div v-if="form.know_date" class="flex flex-col items-center">
                            <span class="text-[14px] font-black text-slate-300 uppercase tracking-widest">得知日期</span>
                            <span class="text-[17px] font-black text-slate-900">{{ form.know_date }}</span>
                        </div>
                        <div v-if="form.user_name" class="flex flex-col items-center border-t border-slate-50 pt-2">
                            <span class="text-[14px] font-black text-slate-300 uppercase tracking-widest">法號</span>
                            <span class="text-[17px] font-black text-indigo-600 leading-none">{{ form.user_name }}<span v-if="form.user_remarks" class="text-slate-400">（{{ form.user_remarks }}）</span></span>
                        </div>
                        <div v-if="form.quantity && form.quantity !== '0'" class="flex flex-col items-center border-t border-slate-50 pt-2">
                            <span class="text-[14px] font-black text-slate-300 uppercase tracking-widest">數量</span>
                            <span class="text-[17px] font-black text-slate-900 leading-none">{{ formatWithCommas(form.quantity) }}</span>
                        </div>
                        <div v-if="form.yan_zun && form.yan_zun !== '0'" class="flex flex-col items-center border-t border-slate-50 pt-2">
                            <span class="text-[14px] font-black text-slate-300 uppercase tracking-widest">閻尊</span>
                            <span class="text-[17px] font-black text-slate-900 leading-none">{{ formatWithCommas(form.yan_zun) }}</span>
                        </div>
                        <div v-if="form.yan_an && form.yan_an !== '0'" class="flex flex-col items-center border-t border-slate-50 pt-2">
                            <span class="text-[14px] font-black text-slate-300 uppercase tracking-widest">閻閽</span>
                            <span class="text-[17px] font-black text-slate-900 leading-none">{{ formatWithCommas(form.yan_an) }}</span>
                        </div>
                        <div v-if="form.long_sheng && form.long_sheng !== '0'" class="flex flex-col items-center border-t border-slate-50 pt-2">
                            <span class="text-[14px] font-black text-slate-300 uppercase tracking-widest">龍勝</span>
                            <span class="text-[17px] font-black text-slate-900 leading-none">{{ formatWithCommas(form.long_sheng) }}</span>
                        </div>
                        <div v-if="form.long_zhan && form.long_zhan !== '0'" class="flex flex-col items-center border-t border-slate-50 pt-2">
                            <span class="text-[14px] font-black text-slate-300 uppercase tracking-widest">龍戰</span>
                            <span class="text-[17px] font-black text-slate-900 leading-none">{{ formatWithCommas(form.long_zhan) }}</span>
                        </div>
                        <div v-if="form.remarks_text" class="flex flex-col items-center border-t border-slate-50 pt-2">
                            <span class="text-[14px] font-black text-slate-300 uppercase tracking-widest">備註內容</span>
                            <span class="text-[17px] font-black text-slate-600 leading-tight text-center">{{ form.remarks_text }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Navigation footer -->
            <div class="absolute bottom-[7dvh] left-0 right-0 md:relative md:bottom-0 px-4 py-3 bg-white border-t border-slate-100 flex gap-3 text-[17px] z-10">
                <button v-if="currentStep > 1" @click="prevStep" class="flex-1 py-3 border border-slate-300 text-slate-600 rounded-xl font-bold active:scale-95 transition-all">
                    上一步
                </button>
                <button v-if="currentStep < totalSteps" @click="nextStep" class="flex-1 py-3 bg-slate-900 text-white rounded-xl font-bold active:scale-95 transition-all">
                    下一步
                </button>
                <button v-else @click="handleSave" :disabled="props.isSaving" class="flex-1 py-3 bg-green-700 text-white rounded-xl font-bold active:scale-95 transition-all">
                    {{ props.isSaving ? '儲存中...' : '提交' }}
                </button>
            </div>
        </div>

        <!-- Navbar Wrapper Removed for Modal UX -->
        <div class="h-[env(safe-area-inset-bottom)] md:h-0"></div>

        <compact-date-picker v-if="showDatePicker" v-model="form.know_date" @close="showDatePicker = false" />
    </div>
    </teleport>
</template>

<script setup>
import { ref, computed, watch, onMounted, onBeforeUnmount } from 'vue';
import CompactDatePicker from './CompactDatePicker.vue';
import MobileNavbar from './MobileNavbar.vue';

const props = defineProps({
    show: Boolean,
    initialData: { type: Object, default: () => ({}) },
    editingId: { type: [Number, String], default: null },
    users: { type: Array, default: () => [] },
    armyType: { type: String, default: '虎甲軍' },
    isCumulative: { type: Boolean, default: false },
    isSaving: { type: Boolean, default: false },
});

const emit = defineEmits(['save', 'cancel']);

const hasQuantity = computed(() => {
    return !['黑曜軍', '耀紫軍'].includes(props.armyType);
});

const stepLabels = computed(() => {
    if (props.isCumulative) {
        const labels = ['日期'];
        labels.push('數量');
        labels.push('備註');
        labels.push('預覽');
        return labels;
    }
    return ['日期', '法號', '數量', '備註', '預覽'];
});

const totalSteps = computed(() => stepLabels.value.length);

const currentStep = ref(1);
const showDatePicker = ref(false);
const activeDharmaDropdown = ref(false);
const activeRemarksDropdown = ref(false);
const dharmaSearch = ref('');

const relationshipOptions = ['母親', '父親', '公公', '婆婆', '爺爺', '奶奶', '外公', '外婆'];

const filteredDharmaNames = computed(() => {
    if (!dharmaSearch.value) return props.users || [];
    const search = dharmaSearch.value.toLowerCase();
    return (props.users || []).filter(u => u.name.toLowerCase().includes(search));
});

const selectDharma = (name) => {
    form.value.user_name = name;
    dharmaSearch.value = name;
    activeDharmaDropdown.value = false;
};

const handleClickOutside = (e) => {
    if (activeDharmaDropdown.value && !e.target.closest('.dharma-dropdown')) {
        activeDharmaDropdown.value = false;
    }
    if (activeRemarksDropdown.value && !e.target.closest('.remarks-dropdown')) {
        activeRemarksDropdown.value = false;
    }
};

onMounted(() => document.addEventListener('click', handleClickOutside));
onBeforeUnmount(() => document.removeEventListener('click', handleClickOutside));

const form = ref({
    user_name: '',
    user_remarks: '',
    know_date: '',
    quantity: '',
    yan_zun: '',
    yan_an: '',
    long_sheng: '',
    long_zhan: '',
    remarks_text: '',
});

watch(() => props.show, (val) => {
    if (val) {
        currentStep.value = 1;
        if (props.editingId && props.initialData) {
            form.value = {
                user_name: props.initialData.user_name || '',
                user_remarks: props.initialData.user_remarks || '',
                know_date: props.initialData.know_date || '',
                quantity: props.initialData.quantity || '',
                yan_zun: props.initialData.yan_zun || '',
                yan_an: props.initialData.yan_an || '',
                long_sheng: props.initialData.long_sheng || '',
                long_zhan: props.initialData.long_zhan || '',
                remarks_text: props.initialData.remarks_text || '',
            };
            dharmaSearch.value = props.initialData.user_name || '';
        } else {
            form.value = {
                user_name: '', user_remarks: '', know_date: '', quantity: '',
                yan_zun: '', yan_an: '', long_sheng: '', long_zhan: '', remarks_text: '',
            };
            dharmaSearch.value = '';
        }
    }
});

// Auto-sum logic matching GrudgeAddForm
watch([() => form.value.yan_zun, () => form.value.yan_an], () => {
    if (props.armyType === '黑曜軍') {
        const zun = isValidBigInt(form.value.yan_zun) ? BigInt(String(form.value.yan_zun).replace(/,/g, '')) : 0n;
        const an = isValidBigInt(form.value.yan_an) ? BigInt(String(form.value.yan_an).replace(/,/g, '')) : 0n;
        const sum = zun + an;
        if (sum > 0n) form.value.quantity = sum.toString();
    }
});

watch([() => form.value.long_sheng, () => form.value.long_zhan], () => {
    if (props.armyType === '耀紫軍') {
        const sheng = isValidBigInt(form.value.long_sheng) ? BigInt(String(form.value.long_sheng).replace(/,/g, '')) : 0n;
        const zhan = isValidBigInt(form.value.long_zhan) ? BigInt(String(form.value.long_zhan).replace(/,/g, '')) : 0n;
        const sum = sheng + zhan;
        if (sum > 0n) form.value.quantity = sum.toString();
    }
});

const isValidBigInt = (val) => {
    if (!val && val !== 0) return false;
    try { BigInt(String(val).replace(/,/g, '')); return true; }
    catch { return false; }
};

const formatWithCommas = (val) => {
    if (!val && val !== 0) return '';
    const s = String(val).replace(/,/g, '');
    if (!/^\d+$/.test(s)) return s;
    return s.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
};

const nextStep = () => {
    if (currentStep.value === 1) {
        if (!form.value.know_date) {
            alert('請選擇日期');
            return;
        }
    }
    if (currentStep.value === 2 && !props.isCumulative) {
        if (!form.value.user_name) {
            alert('請選擇或輸入法號');
            return;
        }
    }
    if (currentStep.value === 3) {
        const qty = isValidBigInt(form.value.quantity) ? BigInt(String(form.value.quantity).replace(/,/g, '')) : 0n;
        if (props.armyType === '黑曜軍') {
            const zun = isValidBigInt(form.value.yan_zun) ? BigInt(String(form.value.yan_zun).replace(/,/g, '')) : 0n;
            const an = isValidBigInt(form.value.yan_an) ? BigInt(String(form.value.yan_an).replace(/,/g, '')) : 0n;
            if (zun === 0n && an === 0n && qty === 0n) { alert('請輸入至少一個數量或總量'); return; }
        } else if (props.armyType === '耀紫軍') {
            const sheng = isValidBigInt(form.value.long_sheng) ? BigInt(String(form.value.long_sheng).replace(/,/g, '')) : 0n;
            const zhan = isValidBigInt(form.value.long_zhan) ? BigInt(String(form.value.long_zhan).replace(/,/g, '')) : 0n;
            if (sheng === 0n && zhan === 0n && qty === 0n) { alert('請輸入至少一個數量或總量'); return; }
        } else {
            if (qty === 0n) { alert('請輸入數量'); return; }
        }
    }
    if (currentStep.value < totalSteps.value) currentStep.value++;
};

const prevStep = () => {
    if (currentStep.value > 1) currentStep.value--;
};

const handleSave = () => {
    const payload = {
        know_date: form.value.know_date || null,
        user_name: form.value.user_name || null,
        user_remarks: form.value.user_remarks || null,
        army_type: props.armyType,
        remarks_text: form.value.remarks_text || null,
    };

    if (props.armyType === '黑曜軍') {
        const zun = isValidBigInt(form.value.yan_zun) ? BigInt(String(form.value.yan_zun).replace(/,/g, '')) : 0n;
        const an = isValidBigInt(form.value.yan_an) ? BigInt(String(form.value.yan_an).replace(/,/g, '')) : 0n;
        payload.yan_zun = zun.toString();
        payload.yan_an = an.toString();
        const sum = zun + an;
        payload.quantity = (sum > 0n) ? sum.toString() : (isValidBigInt(form.value.quantity) ? BigInt(String(form.value.quantity).replace(/,/g, '')).toString() : '0');
    } else if (props.armyType === '耀紫軍') {
        const sheng = isValidBigInt(form.value.long_sheng) ? BigInt(String(form.value.long_sheng).replace(/,/g, '')) : 0n;
        const zhan = isValidBigInt(form.value.long_zhan) ? BigInt(String(form.value.long_zhan).replace(/,/g, '')) : 0n;
        payload.long_sheng = sheng.toString();
        payload.long_zhan = zhan.toString();
        const sum = sheng + zhan;
        payload.quantity = (sum > 0n) ? sum.toString() : (isValidBigInt(form.value.quantity) ? BigInt(String(form.value.quantity).replace(/,/g, '')).toString() : '0');
    } else {
        payload.quantity = isValidBigInt(form.value.quantity) ? BigInt(String(form.value.quantity).replace(/,/g, '')).toString() : '0';
    }

    // For cumulative mode, set defaults
    if (props.isCumulative) {
        payload.destination = '已處理';
    }

    emit('save', payload);
};

const handleCancel = () => {
    emit('cancel');
};
</script>

<style scoped>
.animate-fade-in { animation: fadeIn 0.2s ease-out; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
</style>
