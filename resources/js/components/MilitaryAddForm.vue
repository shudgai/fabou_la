<template>
    <div v-if="show" class="fixed inset-0 z-[2000] flex items-end md:items-center justify-center px-0">
        <!-- Backdrop -->
        <div class="hidden md:block fixed inset-0 bg-slate-900/40 backdrop-blur-sm" @click="$emit('cancel')"></div>
        
        <!-- Form Container -->
        <div class="relative w-full h-full md:h-full bg-white md:rounded-3xl shadow-[0_-10px_40px_rgba(0,0,0,0.1)] overflow-hidden animate-slide-up flex flex-col pb-[7dvh]">
            <!-- Header -->
            <div class="px-[10px] py-[12px] flex items-center bg-white border-b border-slate-50 relative">
                <div class="flex-1 flex flex-col justify-center min-w-0">
                    <div class="font-bold leading-none font-outfit uppercase tracking-wider text-slate-900" style="font-size: 25px !important;">軍隊載錄專區</div>
                    <div class="font-bold mt-2 truncate font-outfit text-slate-900" style="font-size: 24px !important;">
                        {{ editingId ? '修改軍隊載錄' : (isCumulative ? '之前累積數量匯入' : armyType + '-逐筆新增') }}
                    </div>
                </div>
                <button @click="$emit('cancel')" class="text-slate-300 hover:text-slate-600 transition-colors p-2 absolute right-4 top-1/2 -translate-y-1/2">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
            </div>

            <!-- Scrollable Content -->
            <div class="flex-1 overflow-y-auto px-[10px] pt-6 pb-6 space-y-[5px] custom-scrollbar bg-white">
                <div class="space-y-[5px]">
                    <!-- Row 1: 日期 -->
                    <div class="space-y-1">
                        <div class="flex items-center justify-between px-1">
                            <label class="app-title ml-1">日期</label>
                            <button @click="activeDate = 'know_date'" class="text-slate-400 hover:text-indigo-600 transition-colors p-1 active:scale-90">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2v12a2 2 0 002 2z" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </button>
                        </div>
                        <div class="relative flex items-center">
                            <input v-model="form.know_date" type="text" placeholder="年/月/日 或 註記文字" 
                                class="w-full py-[10px] rounded-2xl border border-slate-400 bg-white pl-3 pr-3 focus:ring-0 outline-none shadow-sm app-title leading-tight text-slate-900 font-bold">
                        </div>
                    </div>
                    <div class="border-b-2 border-slate-300 my-4 mx-1"></div>
                    <!-- Row 2: 法號 & 備註 -->
                    <div v-if="!isCumulative" class="grid grid-cols-2 gap-[5px]">
                        <div class="space-y-1">
                            <label class="app-title ml-1">法號</label>
                            <div class="relative flex items-center border-0 border-b-2 border-slate-300 bg-transparent overflow-visible min-h-[44px] dharma-dropdown">
                                <input v-model="dharmaSearch" type="text" placeholder="搜尋或選擇法號..." 
                                    @focus="activeDharmaDropdown = true"
                                    class="w-full bg-transparent border-none px-2 text-[15px] focus:ring-0 outline-none app-body leading-tight text-slate-900 font-bold">
                                <button @click.stop="activeDharmaDropdown = !activeDharmaDropdown" class="p-1.5 text-slate-300 hover:text-indigo-600 transition-all">
                                    <svg class="w-5 h-5" :class="activeDharmaDropdown ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </button>
                                <input type="hidden" v-model="form.user_name">
                                <div v-if="activeDharmaDropdown" class="absolute left-0 top-full mt-1 w-full bg-white rounded-xl shadow-[0_10px_30px_rgba(0,0,0,0.15)] border border-slate-100 z-[2100] overflow-hidden p-1 animate-fade-in max-h-[200px] overflow-y-auto custom-scrollbar">
                                    <div v-for="u in filteredDharmaNames" :key="u.id"
                                        @click.stop="selectDharma(u.name)"
                                        class="px-3 py-2.5 text-[17px] text-slate-700 hover:bg-indigo-50 active:bg-indigo-100 rounded-2xl cursor-pointer transition-colors font-medium">
                                        {{ u.name }}
                                    </div>
                                    <div v-if="filteredDharmaNames.length === 0" class="px-3 py-2.5 text-[17px] text-slate-400 text-center">
                                        無符合的法號
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="space-y-1">
                            <label class="app-title ml-1">備註對象</label>
                            <div class="relative flex items-center border-0 border-b-2 border-slate-300 bg-transparent overflow-visible min-h-[44px] remarks-dropdown">
                                <input v-model="form.user_remarks" type="text" placeholder="備註對象 (例如：母親)..." 
                                    @focus="activeRemarksDropdown = true"
                                    class="w-full bg-transparent border-none px-2 text-[15px] focus:ring-0 outline-none app-body leading-tight text-slate-900 font-bold">
                                <button @click.stop="activeRemarksDropdown = !activeRemarksDropdown" class="p-1.5 text-slate-300 hover:text-indigo-600 transition-all">
                                    <svg class="w-5 h-5" :class="activeRemarksDropdown ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </button>
                                
                                <div v-if="activeRemarksDropdown" class="absolute left-0 top-full mt-1 w-full bg-white rounded-xl shadow-[0_10px_30px_rgba(0,0,0,0.15)] border border-slate-100 z-[2100] overflow-hidden p-1 animate-fade-in max-h-[180px] overflow-y-auto custom-scrollbar">
                                    <div v-for="opt in relationshipOptions" :key="opt"
                                        @click.stop="form.user_remarks = opt; activeRemarksDropdown = false"
                                        class="px-3 py-2.5 text-[15px] text-slate-700 hover:bg-indigo-50 active:bg-indigo-100 rounded-2xl cursor-pointer transition-colors font-medium">
                                        {{ opt }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="border-b-2 border-slate-300 my-4 mx-1"></div>
                    <!-- Conditional Quantity Row -->
                    <!-- Case 1: 虎甲/虎賁 or Default -->
                    <div v-if="armyType === '虎甲軍' || armyType === '虎賁軍'" class="space-y-1">
                        <div class="flex items-center justify-between ml-1">
                            <label class="app-title">數量</label>
                            <span v-if="quantityBig >= 1000000n" class="app-title text-indigo-500 bg-indigo-50 px-2 rounded-2xl">{{ formatArmyTotal(form.quantity) }}</span>
                        </div>
                        <input v-model="form.quantity" type="text" inputmode="numeric" class="w-full border-0 border-b-2 border-slate-300 bg-transparent py-[10px] px-2 focus:ring-0 outline-none app-body leading-tight text-slate-900">
                    </div>

                    <!-- Case 2: 黑曜軍 (閻尊/閻闇) -->
                    <div v-if="armyType === '黑曜軍'" class="space-y-[5px]">
                        <div class="grid grid-cols-2 gap-[5px]">
                            <div class="space-y-1">
                                <label class="app-title ml-1">閻尊數量</label>
                                <input v-model="form.yan_zun" type="text" inputmode="numeric" class="w-full border-0 border-b-2 border-slate-300 bg-transparent py-[10px] px-2 focus:ring-0 outline-none app-body leading-tight text-slate-900">
                            </div>
                            <div class="space-y-1">
                                <label class="app-title ml-1">閻闇數量</label>
                                <input v-model="form.yan_an" type="text" inputmode="numeric" class="w-full border-0 border-b-2 border-slate-300 bg-transparent py-[10px] px-2 focus:ring-0 outline-none app-body leading-tight text-slate-900">
                            </div>
                        </div>
                        <div class="w-full px-4 flex items-center justify-end py-[10px] border-t border-slate-50 mt-1 space-x-2">
                            <span class="app-title">小計</span>
                            <span class="app-title text-indigo-600 mr-2" v-if="obsidianSubtotalBig >= 1000000n">({{ formatArmyTotal(obsidianSubtotalBig) }})</span>
                            <span class="app-body text-slate-900">{{ formatWithCommas(obsidianSubtotalBig) }}</span>
                        </div>
                    </div>



                    <!-- Case 5: 耀紫軍 (龍勝/龍戰) -->
                    <div v-if="armyType === '耀紫軍'" class="space-y-[5px]">
                        <div class="grid grid-cols-2 gap-[5px]">
                            <div class="space-y-1">
                                <label class="app-title ml-1">龍勝數量</label>
                                <input v-model="form.long_sheng" type="text" inputmode="numeric" class="w-full border-0 border-b-2 border-slate-300 bg-transparent py-[10px] px-2 focus:ring-0 outline-none app-body leading-tight text-slate-900">
                            </div>
                            <div class="space-y-1">
                                <label class="app-title ml-1">龍戰數量</label>
                                <input v-model="form.long_zhan" type="text" inputmode="numeric" class="w-full border-0 border-b-2 border-slate-300 bg-transparent py-[10px] px-2 focus:ring-0 outline-none app-body leading-tight text-slate-900">
                            </div>
                        </div>
                        <div class="w-full px-4 flex items-center justify-end py-[10px] border-t border-slate-50 mt-1 space-x-2">
                            <span class="app-title">小計</span>
                            <span class="app-title text-indigo-600 mr-2" v-if="purpleSubtotalBig >= 1000000n">({{ formatArmyTotal(purpleSubtotalBig) }})</span>
                            <span class="app-body text-slate-900">{{ formatWithCommas(purpleSubtotalBig) }}</span>
                        </div>
                    </div>



                    <!-- Row 5: 備註 -->
                    <div class="space-y-1 pt-1">
                        <label class="app-title ml-1">備註文字</label>
                        <input v-model="form.remarks_text" type="text" placeholder="輸入相關備註..." class="w-full border-0 border-b-2 border-slate-300 bg-transparent py-[10px] px-3 focus:ring-0 outline-none app-body text-slate-900">
                    </div>
                </div>
            </div>

            <!-- Footer Action -->
            <div class="absolute bottom-[7dvh] left-0 right-0 md:relative md:bottom-0 px-6 py-[2px] bg-white border-t border-slate-50 z-[10] shadow-[0_-10px_30px_rgba(0,0,0,0.02)] flex justify-center">
                <button 
                    @click="handleSave" 
                    :disabled="isSaving"
                    class="w-full max-w-md bg-indigo-600 text-white font-black h-[48px] text-[16px] rounded-2xl shadow-lg shadow-indigo-100 active:scale-[0.98] transition-all flex items-center justify-center tracking-widest disabled:opacity-50 disabled:cursor-not-allowed"
                    style="color: white !important; font-size: 16px !important;"
                >
                    <template v-if="isSaving">
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        處理中...
                    </template>
                    <template v-else>
                        {{ editingId ? '確認修改' : '確認載錄' }}
                    </template>
                </button>
            </div>

            <!-- Global Mobile Navbar -->
            <mobile-navbar 
                class="md:hidden"
                :can-back="false"
                @home="$emit('cancel')"
                :show-action="false"
                :can-search="false"
                is-absolute
            />


        </div>
        <!-- Custom Date Picker -->
        <compact-date-picker 
            v-if="activeDate"
            v-model="form[activeDate]"
            :title="'日期'"
            @close="activeDate = null"
        />
    </div>
</template>

<script setup>
import { ref, watch, computed, onMounted, onUnmounted } from 'vue';
import CompactDatePicker from './CompactDatePicker.vue';
import MobileNavbar from './MobileNavbar.vue';

const props = defineProps({
    show: Boolean,
    initialData: Object,
    editingId: [Number, String],
    users: Array,
    armyType: String,
    isCumulative: Boolean,
    isSaving: Boolean
});

const emit = defineEmits(['save', 'cancel']);
const activeDate = ref(null);
const activeRemarksDropdown = ref(false);
const activeDharmaDropdown = ref(false);
const dharmaSearch = ref('');
const relationshipOptions = ['母親', '父親', '公公', '婆婆', '爺爺', '奶奶', '外公', '外婆'];

const filteredDharmaNames = computed(() => {
    if (!dharmaSearch.value) return props.users || [];
    const search = dharmaSearch.value.toLowerCase();
    return (props.users || []).filter(u => u.name.toLowerCase().includes(search));
});

const form = ref({ 
    ...props.initialData,
    army_type: props.armyType || props.initialData.army_type,
    yan_zun: String(props.initialData.yan_zun || "0"),
    yan_an: String(props.initialData.yan_an || "0"),
    long_sheng: String(props.initialData.long_sheng || "0"),
    long_zhan: String(props.initialData.long_zhan || "0"),
    quantity: String(props.initialData.quantity || "0")
});

const obsidianSubtotalBig = computed(() => tryBigIntSum(form.value.yan_zun, form.value.yan_an));
const purpleSubtotalBig = computed(() => tryBigIntSum(form.value.long_sheng, form.value.long_zhan));
const quantityBig = computed(() => {
    try {
        return isValidBigInt(form.value.quantity) ? BigInt(String(form.value.quantity).replace(/,/g, '')) : 0n;
    } catch (e) {
        return 0n;
    }
});

watch(() => props.initialData, (newVal) => {
    form.value = { ...newVal, army_type: props.armyType || newVal.army_type };
}, { deep: true });

const selectDharma = (name) => {
    form.value.user_name = name;
    dharmaSearch.value = name;
    activeDharmaDropdown.value = false;
};

// Watch form.user_name to sync with dharmaSearch
watch(() => form.value.user_name, (newVal) => {
    if (newVal && !dharmaSearch.value) {
        dharmaSearch.value = newVal;
    }
}, { immediate: true });

// Close dropdowns when clicking outside
const handleClickOutside = (e) => {
    if (activeDharmaDropdown.value && !e.target.closest('.dharma-dropdown')) {
        activeDharmaDropdown.value = false;
    }
    if (activeRemarksDropdown.value && !e.target.closest('.remarks-dropdown')) {
        activeRemarksDropdown.value = false;
    }
};

onMounted(() => {
    document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
});


const isValidBigInt = (val) => {
    if (val === null || val === undefined || val === '') return false;
    try {
        BigInt(String(val).replace(/,/g, ''));
        return true;
    } catch (e) {
        return false;
    }
};

const tryBigIntSum = (v1, v2) => {
    try {
        const b1 = isValidBigInt(v1) ? BigInt(String(v1).replace(/,/g, '')) : 0n;
        const b2 = isValidBigInt(v2) ? BigInt(String(v2).replace(/,/g, '')) : 0n;
        return b1 + b2;
    } catch (e) {
        return 0n;
    }
};

const formatWithCommas = (val) => {
    const s = String(val).replace(/,/g, '');
    if (!/^\d+$/.test(s)) return s;
    return s.replace(/\B(?=(\d{3})+(?!\d))/g, ",");
};

const formatArmyTotal = (num) => {
    if (!isValidBigInt(num)) return '0';
    const b = BigInt(String(num).replace(/,/g, ''));
    if (b < 1000000n) return formatWithCommas(b);
    
    const troops = b / 1000000n;
    const remaining = b % 1000000n;
    if (remaining === 0n) return `${troops}隊`;
    
    const wan = remaining / 10000n;
    const rest = remaining % 10000n;
    let res = `${troops}隊`;
    if (wan > 0n) res += `${wan}萬`;
    if (rest > 0n) res += `${rest}位`;
    else if (wan === 0n) res += `0位`;
    return res;
};

// Auto-detect and parse Name/Relationship pattern removed to prioritize exact input preservation
/*
watch(() => form.value.user_name, (newVal) => {
    if (!newVal) return;
    const relSplitMatch = newVal.match(/^(.*?)[之的](.+)$/);
    if (relSplitMatch) {
        const namePart = relSplitMatch[1].trim();
        let relPart = relSplitMatch[2].trim();
        if (relPart === '母') relPart = '母親';
        if (relPart === '父') relPart = '父親';
        form.value.user_name = namePart;
        form.value.user_remarks = relPart;
    }
});
*/

// Auto-correct terminology for relatives removed to preserve original terms as requested
/*
watch(() => form.value.user_remarks, (newVal) => {
    if (!newVal) return;
    let rel = newVal.trim();
    if (rel === '之母' || rel === '母') form.value.user_remarks = '母親';
    else if (rel === '之父' || rel === '父') form.value.user_remarks = '父親';
    else if (rel.startsWith('之') || rel.startsWith('的')) {
        form.value.user_remarks = rel.substring(1);
    }
});
*/

const handleSave = () => {
    if (!props.isCumulative && !form.value.user_name) {
        alert('請選擇或輸入「法號」，不可留空。');
        return;
    }

    const clean = (val) => String(val || 0).replace(/,/g, '');

    // Auto-calculate quantity for split armies using strings to preserve precision
    if (props.armyType === '黑曜軍') {
        form.value.quantity = tryBigIntSum(form.value.yan_zun, form.value.yan_an).toString();
    } else if (props.armyType === '耀紫軍') {
        form.value.quantity = tryBigIntSum(form.value.long_sheng, form.value.long_zhan).toString();
    } else if (props.armyType === '虎甲軍') {
        form.value.yan_jue = clean(form.value.quantity);
        form.value.yan_ze = "0";
    } else if (props.armyType === '虎賁軍') {
        form.value.yan_di = clean(form.value.quantity);
        form.value.yan_yuan = "0";
    }

    // Clean all quantity fields before sending
    const fields = ['quantity', 'yan_zun', 'yan_an', 'long_sheng', 'long_zhan', 'yan_jue', 'yan_ze', 'yan_di', 'yan_yuan'];
    fields.forEach(f => {
        if (form.value[f] !== undefined) {
            form.value[f] = clean(form.value[f]);
        }
    });

    console.log('Saving Military Record:', form.value);
    emit('save', form.value);
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
