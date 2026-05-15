<template>
    <div v-if="mode" class="fixed inset-0 z-[2000] flex items-end md:items-center justify-center px-0 imperial-grace-module" style="overscroll-behavior: contain;">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm" @click="$emit('close')"></div>
        
        <!-- Form Container -->
        <div class="relative w-full h-[100dvh] md:h-full bg-white md:rounded-3xl shadow-[0_-10px_40px_rgba(0,0,0,0.1)] overflow-hidden animate-slide-up flex flex-col pb-[7dvh]">
            <!-- Header -->
            <div class="px-4 py-3 flex items-center bg-white border-b border-slate-50 relative">
                <div class="flex-1 flex flex-col justify-center min-w-0 pr-6">
                    <div class="text-[28px] font-black leading-tight font-outfit tracking-widest text-red-600 uppercase !font-black" style="color: #dc2626 !important;">
                        ?之?
                    </div>
                </div>
                <button @click="$emit('close')" class="text-slate-300 hover:text-slate-600 transition-colors py-[5px] absolute right-4 top-1/2 -translate-y-1/2">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
            </div>

            <div class="px-4 py-2 bg-slate-50 flex space-x-2 border-b border-slate-100">
                <button @click="localMode = 'single'" 
                    :class="localMode === 'single' ? 'bg-indigo-500 text-white shadow-md' : 'bg-slate-50 text-slate-400 border border-slate-100'"
                    class="flex-1 py-2 text-[16px] font-black rounded-2xl transition-all whitespace-nowrap active:scale-95"
                    :style="{ color: localMode === 'single' ? 'white !important' : '#94a3b8 !important' }">
                    ???駁?
                </button>
                <button @click="localMode = 'batch'" 
                    :class="localMode === 'batch' ? 'bg-indigo-500 text-white shadow-md' : 'bg-slate-50 text-slate-400 border border-slate-100'"
                    class="flex-1 py-2 text-[16px] font-black rounded-2xl transition-all whitespace-nowrap active:scale-95"
                    :style="{ color: localMode === 'batch' ? 'white !important' : '#94a3b8 !important' }">
                    憭?頛?
                </button>
            </div>

            <!-- Scrollable Content -->
            <div class="flex-1 overflow-y-auto px-3 pt-4 pb-32 space-y-5 custom-scrollbar overscroll-contain">
                
                <!-- COMMON FIELDS (Date & Master) -->
                <div class="grid grid-cols-2 gap-3 bg-white p-1">
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between px-1">
                            <label class="app-title font-black text-slate-400 uppercase tracking-widest block ml-1">敺?交?</label>
                            <button @click="activePicker = { field: 'record_date', title: '靽格敺?交?' }" class="text-slate-300 hover:text-indigo-600 transition-colors p-1 z-20 active:scale-90">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2v12a2 2 0 002 2z" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </button>
                        </div>
                        <div class="relative flex items-center">
                            <input v-model="form.record_date" type="text" placeholder="敺?交?" 
                                class="app-body w-full py-2.5 border-0 border-b-2 border-slate-300 bg-transparent pl-4 pr-4 focus:ring-0 outline-none font-black text-slate-900">
                        </div>
                    </div>
                    <div class="space-y-1.5">
                        <label class="app-title font-black text-slate-400 uppercase tracking-widest block ml-1">頛??格?</label>
                        <select v-model="form.master_id" class="app-body w-full h-[46px] border-0 border-b-2 border-slate-300 bg-transparent px-4 font-black text-slate-900 outline-none">
                            <option v-for="m in masters" :key="m.id" :value="m.id">{{ m.name === '?嗥?隞葦' ? '?嗥?' : m.name }}</option>
                        </select>
                    </div>
                </div>

                <!-- SINGLE MODE -->
                <div v-if="localMode === 'single'" class="space-y-5 animate-fade-in">
                    <div class="space-y-1.5">
                        <label class="app-title font-black text-slate-400 uppercase tracking-widest block ml-1">瘜窄?迂</label>
                        <textarea v-model="form.name" rows="1" @input="e => { e.target.style.height = 'auto'; e.target.style.height = e.target.scrollHeight + 'px' }" placeholder="隢撓?交?撖嗅?蝔?.." class="app-body w-full py-3 border-0 border-b-2 border-slate-300 bg-transparent px-4 font-black text-slate-900 outline-none placeholder:text-slate-200"></textarea>
                    </div>

                    <div class="space-y-1.5">
                        <label class="app-title font-black text-slate-400 uppercase tracking-widest block ml-1">瘜窄?冽?</label>
                        <input v-model="form.purpose" placeholder="頛詨瘜窄?券?.." class="app-body w-full h-[46px] border-0 border-b-2 border-slate-300 bg-transparent px-4 font-black text-slate-900 outline-none placeholder:text-slate-200">
                    </div>

                    <!-- MASTER STATUS/DATE (ONLY FOR SINGLE PERSON) -->
                    <div v-if="personnel.length === 0" class="grid grid-cols-2 gap-3 animate-fade-in">
                        <div class="space-y-1.5">
                            <label class="app-title font-black text-slate-400 uppercase tracking-widest block ml-1">?桀????/label>
                            <select v-model="form.status" class="app-body w-full h-[46px] border-0 border-b-2 border-slate-300 bg-transparent px-4 font-black text-slate-900 outline-none">
                                <option value="?芣?敺?>?芣?敺?/option>
                                <option value="撌脫?敺?>撌脫?敺?/option>
                                <option value="撌脩閮?>撌脩閮?/option>
                            </select>
                        </div>
                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between px-1">
                                <label class="app-title font-black text-slate-400 uppercase tracking-widest block ml-1">
                                    {{ form.status === '撌脩閮? ? '?餉??交?' : (form.status === '撌脫?敺? ? '瘙??交?' : '?交?') }}
                                </label>
                                <button @click="activePicker = { field: 'obtained_date', title: '靽格?交?' }" class="text-slate-300 hover:text-indigo-600 transition-colors p-1 z-20 active:scale-90">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2v12a2 2 0 002 2z" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </button>
                            </div>
                            <div class="relative">
                                <input v-model="form.obtained_date" type="text" placeholder="?交?" class="app-body w-full h-[46px] border-0 border-b-2 border-slate-300 bg-transparent px-4 font-black text-slate-900 outline-none">
                            </div>
                        </div>
                    </div>

                    <div v-if="personnel.length === 0" class="flex justify-center py-4 border-t border-slate-50 mt-4">
                        <button @click="addPersonnelRow" class="px-6 py-2 bg-slate-100 text-indigo-600 font-black text-[16px] rounded-2xl flex items-center space-x-2 active:scale-95 transition-all shadow-sm border border-slate-200">
                            <span>嚗?憭犖?踵璅∪?</span>
                        </button>
                    </div>

                    <!-- PERSONNEL SECTION (ONLY FOR MULTI-PERSON) -->
                    <div v-if="personnel.length > 0" class="space-y-6 pt-4 border-t border-slate-100 animate-fade-in">
                        <div class="flex items-center justify-between ml-1">
                            <div class="flex flex-col">
                                <label class="text-[15px] font-black text-red-600 uppercase tracking-tight">憭犖?踵?</label>
                                <span class="text-[11px] text-slate-400 font-bold uppercase tracking-widest">MULTI-PERSON MODE</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <button @click="personnel = []" class="px-3 py-1.5 bg-slate-50 text-slate-500 rounded-2xl text-[16px] font-black active:scale-95 transition-all border border-slate-100 shadow-sm">
                                    ???鈭?                                </button>
                                <button @click="addPersonnelRow" class="px-3 py-1.5 bg-indigo-600 text-white rounded-2xl text-[16px] font-black active:scale-95 transition-all shadow-md" style="color: white !important;">
                                    嚗??啣?鈭箏
                                </button>
                            </div>
                        </div>

                        <div v-for="(p, idx) in personnel" :key="idx" 
                            class="p-4 bg-white rounded-[24px] border border-slate-200 space-y-4 relative group animate-fade-in shadow-sm">
                            <div class="absolute top-3 right-3 flex items-center space-x-2">
                                <div class="flex items-center space-x-1 bg-slate-50 rounded-lg px-1 py-0.5">
                                    <button v-if="personnel.length > 1" @click="movePersonnel(idx, -1)" :disabled="idx === 0" class="p-1 text-slate-300 hover:text-indigo-500 disabled:opacity-10 transition-all active:scale-90">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 15l7-7 7 7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    </button>
                                    <button v-if="personnel.length > 1" @click="movePersonnel(idx, 1)" :disabled="idx === personnel.length - 1" class="p-1 text-slate-300 hover:text-indigo-500 disabled:opacity-10 transition-all active:scale-90">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    </button>
                                </div>
                                <button @click="removePersonnelRow(idx)" class="w-7 h-7 flex items-center justify-center bg-rose-50 text-white rounded-full active:scale-90 transition-all">??/button>
                            </div>
                            
                            <!-- Fields -->
                            <div class="space-y-4">
                                <div class="grid grid-cols-2 gap-3">
                                    <div class="space-y-1.5">
                                        <label class="app-title font-black text-red-400 ml-1 uppercase tracking-widest">瘜?</label>
                                        <input v-model="p.custom_name" type="text" placeholder="法號" 
                                            @keydown.enter.prevent="handlePersonnelEnter(idx)"
                                            class="personnel-name-input app-body w-full h-[46px] border-0 border-b-2 border-slate-300 bg-transparent px-4 font-black text-slate-900 outline-none font-outfit">
                                        <compact-datalist v-model="p.custom_name" :options="dharmaNames.map(dn => dn.name)" />
                                    </div>
                                    <div class="space-y-1.5">
                                        <label class="app-title font-black text-red-400 ml-1 uppercase tracking-widest">???/label>
                                        <select v-model="p.status" class="app-body w-full h-[46px] border-0 border-b-2 border-slate-300 bg-transparent px-4 font-black outline-none"
                                            :style="p.status === '?芣?敺? ? 'color: #dc2626 !important;' : (p.status === '撌脫?敺? ? 'color: #2563eb !important;' : 'color: #059669 !important;')">
                                            <option value="?芣?敺?>?芣?敺?/option>
                                            <option value="撌脫?敺?>撌脫?敺?/option>
                                            <option value="撌脩閮?>撌脩閮?/option>
                                        </select>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div class="space-y-1.5" :class="{ 'opacity-50 pointer-events-none': p.status === '?芣?敺? }">
                                        <div class="flex items-center justify-between px-1">
                                            <label class="app-title font-black text-red-400 ml-1 uppercase tracking-widest">
                                                {{ p.status === '撌脩閮? ? '?餉??交?' : (p.status === '撌脫?敺? ? '瘙??交?' : '?交?') }}
                                            </label>
                                            <button @click="activePicker = { field: 'obtained_date', idx: idx, title: '靽格鈭箏?交?' }" class="text-slate-300 hover:text-indigo-600 transition-colors p-1 z-20 active:scale-90">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2v12a2 2 0 002 2z" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                            </button>
                                        </div>
                                        <div class="relative">
                                            <input v-model="p.obtained_date" type="text" placeholder="?交?" 
                                                class="app-body w-full h-[46px] border-0 border-b-2 border-slate-300 bg-transparent px-3 font-black text-slate-900 outline-none">
                                        </div>
                                    </div>
                                    <div class="space-y-1.5">
                                        <label class="app-title font-black text-slate-400 ml-1 uppercase tracking-widest">?酉撠情</label>
                                        <div class="relative flex items-center border-0 border-b-2 border-slate-300 bg-transparent overflow-visible h-[46px]">
                                            <input v-model="p.relationship" placeholder="?酉撠情..." 
                                                @focus="activeRelDropdownIdx = idx"
                                                class="app-body w-full bg-transparent border-none px-4 font-black text-slate-900 focus:ring-0 outline-none placeholder:text-slate-200">
                                            <button @click.stop="activeRelDropdownIdx = (activeRelDropdownIdx === idx ? null : idx)" class="p-2 text-indigo-500 hover:text-indigo-700 transition-all">
                                                <svg class="w-5 h-5" :class="activeRelDropdownIdx === idx ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                            </button>
                                            <compact-datalist v-model="p.relationship" :options="relationshipOptions" />

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Table is "collapsed" by default when personnel length is 0 -->


                        <div class="col-span-2 space-y-1 py-[5px]">
                            <label class="app-title font-black text-slate-400 uppercase tracking-widest block ml-1">閰喟敦?批捆 / ?酉</label>
                            <textarea v-model="form.remarks" rows="1" placeholder="頛詨?游?隤芣??批捆..." class="app-body w-full py-[5px] border-0 border-b-2 border-slate-300 bg-transparent p-2 font-bold text-slate-900 outline-none"></textarea>
                        </div>
                    </div>
                </div>

                <!-- BATCH MODE -->
                <div v-if="localMode === 'batch'" class="space-y-4 animate-fade-in">
                    <!-- Batch Type Toggle -->
                    <div class="px-1 py-1 bg-slate-50 rounded-2xl flex space-x-1 mb-4 border border-slate-100">
                        <button @click="batchType = 'single'" 
                            :class="batchType === 'single' ? 'shadow-md' : 'bg-transparent text-slate-400'"
                            class="flex-1 py-2.5 rounded-xl text-[16px] font-black transition-all active:scale-95"
                            :style="{ 
                                fontSize: '16px !important', 
                                backgroundColor: batchType === 'single' ? 'rgb(135, 206, 235) !important' : 'transparent',
                                color: batchType === 'single' ? 'white !important' : '#94a3b8 !important' 
                            }">
                            ?桐犖?踵
                        </button>
                        <button @click="batchType = 'multi'" 
                            :class="batchType === 'multi' ? 'shadow-md' : 'bg-transparent text-slate-400'"
                            class="flex-1 py-2.5 rounded-xl text-[16px] font-black transition-all active:scale-95"
                            :style="{ 
                                fontSize: '16px !important', 
                                backgroundColor: batchType === 'multi' ? 'rgb(135, 206, 235) !important' : 'transparent',
                                color: batchType === 'multi' ? 'white !important' : '#94a3b8 !important' 
                            }">
                            憭犖?踵
                        </button>
                    </div>

                    <div class="bg-white rounded-[24px] p-5 space-y-4 shadow-sm border border-slate-100 relative">
                        <div class="flex items-center justify-between">
                            <label class="text-[13px] font-black text-slate-400 uppercase tracking-widest ml-1">鞎澆瘜窄??迂</label>
                            <div class="flex items-center space-x-3">
                                <button v-if="batchInput" @click="batchInput = ''" class="text-[12px] font-black text-red-500 hover:underline active:scale-90 transition-all">皜征</button>
                            </div>
                        </div>
                        <textarea v-model="batchInput" rows="1" @input="e => { e.target.style.height = 'auto'; e.target.style.height = e.target.scrollHeight + 'px' }" class="w-full h-40 py-4 px-4 bg-slate-50 border-0 border-b-2 border-slate-300 font-black text-slate-900 outline-none text-[17px] custom-scrollbar focus:bg-indigo-50/30 transition-all"
                            :placeholder="batchType === 'single' 
                                ? '?桐犖?踵隢票銝?頛詨 (瘝??冽??臭誑銝憛怠)\n瘜窄?迂\n?冽?:\n瘜窄?迂\n?冽?:\n隞交迨憿' 
                                : '憭犖?踵隢票銝?頛詨 (瘝??冽??臭誑銝憛怠)\n瘜窄?迂:\n?冽?:\n瘜?\n瘜?\n瘜窄?迂:\n?冽?:\n瘜?\n瘜?\n隞交迨憿'">
                        </textarea>
                        <input type="file" ref="fileInput" class="hidden" @change="handleFileUpload" accept=".txt,.xlsx,.xls,.docx">
                    </div>

                    <div v-if="excelRows.length > 0 && batchType === 'single'" class="mt-4 border border-slate-100 rounded-2xl overflow-hidden shadow-sm bg-white animate-fade-in">
                        <div class="bg-slate-50 px-4 py-2.5 border-b border-slate-100 flex justify-between items-center">
                            <span class="text-[13px] font-black text-slate-600">?桀?閫??嚗?({{ excelRows.length }} 蝑?</span>
                            <span class="text-[11px] text-slate-400 font-bold uppercase tracking-widest">PREVIEW</span>
                        </div>
                        <div class="overflow-x-auto max-h-[400px] overscroll-contain">
                            <table class="w-full text-left border-collapse">
                                <thead class="sticky top-0 bg-slate-50 shadow-sm z-10">
                                    <tr class="text-[11px] text-slate-400 uppercase tracking-widest font-black">
                                        <th class="px-4 py-3 border-b border-slate-100 w-32 text-[11px]">瘜窄??/th>
                                        <th class="px-4 py-3 border-b border-slate-100 text-[11px]">鈭箏??/th>
                                        <th class="px-4 py-3 border-b border-slate-100 w-24 text-center text-[11px]">?</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(row, idx) in excelRows" :key="idx" class="border-b border-slate-50 last:border-0">
                                        <td class="px-4 py-3 align-top">
                                            <div class="text-[15px] font-black text-indigo-600 leading-tight mb-1">{{ row.c0 }}</div>
                                            <div v-if="row.c1 && row.c1 !== '-'" class="text-[11px] text-slate-400 font-bold leading-tight">{{ row.c1 }}</div>
                                        </td>
                                        <td class="px-4 py-3 align-top">
                                            <div v-for="(p, pIdx) in row._dharma_name_registries" :key="pIdx" class="text-[13px] font-black text-slate-700 mb-1.5 flex flex-wrap items-center gap-x-1">
                                                <span class="text-indigo-600">+</span>
                                                <span>{{ p.custom_name }}</span>
                                                <span class="text-slate-300 mx-0.5">/</span>
                                                <span class="text-slate-400 font-normal">{{ p.obtained_date || '-' }}</span>
                                                <span class="text-slate-300 mx-0.5">/</span>
                                                <span :class="p.status === '?芣?敺? ? 'text-red-500' : (p.status === '撌脫?敺? ? 'text-blue-500' : 'text-emerald-500')">{{ p.status }}</span>
                                            </div>
                                            <div v-if="row._manualRemarks" class="text-[11px] text-amber-600 font-bold italic mt-2 border-t border-amber-50 pt-1">
                                                ?酉: {{ row._manualRemarks }}
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 align-top text-center">
                                            <span class="px-2 py-0.5 rounded-lg text-[10px] font-black uppercase tracking-widest border active:scale-90 transition-all font-bold"
                                                :class="row._status === '?芣?敺? ? 'bg-red-50 border-red-100 text-red-500' : (row._status === '撌脫?敺? ? 'bg-blue-50 border-blue-100 text-blue-500' : 'bg-emerald-50 border-emerald-100 text-emerald-500')">
                                                {{ row._status }}
                                            </span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Action -->
            <div class="absolute bottom-[7dvh] left-0 right-0 md:relative md:bottom-0 px-6 py-[2px] bg-white border-t border-slate-50 z-[10] flex justify-center">
                <button @click="handleSubmit" :disabled="isSaving || (localMode === 'batch' && excelRows.length === 0)"
                    class="flex-1 py-4 bg-indigo-600 !text-white rounded-2xl font-black text-[18px] shadow-lg shadow-indigo-100 active:scale-95 transition-all disabled:bg-slate-300"
                    style="color: white !important;">
                    {{ isSaving ? '??銝?..' : (localMode === 'single' ? '蝣箄?頛?' : `??頛???${excelRows.length} 蝑??) }}
                </button>
            </div>

            <mobile-navbar class="md:hidden" :can-back="false" @home="$emit('cancel')" :show-action="false" :can-search="false" is-absolute />
            

        </div>

        <!-- LOCAL DATE PICKER FOR THE ADD FORM -->
        <compact-date-picker 
            v-if="activePicker"
            v-model="activePickerValue"
            :title="activePicker.title"
            @close="activePicker = null"
        />
    </div>
</template>

<script setup>
import { ref, watch, computed, onMounted, onUnmounted, nextTick } from 'vue';
import axios from 'axios';
import CompactDatePicker from './CompactDatePicker.vue';
import MobileNavbar from './MobileNavbar.vue';
import CompactDatalist from './CompactDatalist.vue';
import { lockBodyScroll, unlockBodyScroll } from '../utils/iosCompat';

const props = defineProps({
    mode: String,
    initialData: Object,
    masters: Array,
    isSaving: Boolean
});

const emit = defineEmits(['saveSingle', 'saveBatch', 'cancel', 'close']);

// Watch for mode changes to handle body scroll locking
watch(() => props.mode, (newVal) => {
    if (newVal) lockBodyScroll();
    else unlockBodyScroll();
}, { immediate: true });

onUnmounted(() => {
    if (props.mode) unlockBodyScroll();
});

const localMode = ref(props.mode || 'single');
const batchType = ref('single');
const form = ref({ ...props.initialData });
const batchInput = ref('');
const activePicker = ref(null);
const activeRelDropdownIdx = ref(null);
const relationshipOptions = ['瘥扛', '?嗉扛', '?砍', '憍?', '?箇', '憟嗅扒', '憭', '憭?'];

const activePickerValue = computed({
    get: () => {
        if (!activePicker.value) return '';
        if (activePicker.value.idx !== undefined) {
            return personnel.value[activePicker.value.idx][activePicker.value.field];
        }
        return form.value[activePicker.value.field];
    },
    set: (val) => {
        if (!activePicker.value) return;
        if (activePicker.value.idx !== undefined) {
            personnel.value[activePicker.value.idx][activePicker.value.field] = val;
        } else {
            form.value[activePicker.value.field] = val;
        }
    }
});

const hasPersonnelPattern = computed(() => {
    if (!batchInput.value.trim()) return false;
    return /(撌脩閮撌脫?敺?芣?敺?/.test(batchInput.value);
});

const excelCols = ref([
    { key: 'c0', label: '瘜窄?迂' },
    { key: 'c1', label: '瘜窄?冽?' }
]);

const excelRows = computed(() => {
    if (!batchInput.value.trim()) return [];

    // HYBRID SMART PARSER: Automatically handles Single & Multi-person blocks
    const lines = batchInput.value.split('\n').map(l => l.trim());
    const records = [];
    let currentRec = null;
    let currentMasterId = form.value.master_id;
    let currentDateInText = null;

    for (let i = 0; i < lines.length; i++) {
        const line = lines[i];
        if (!line) { 
            if (currentRec) { records.push(currentRec); currentRec = null; }
            continue; 
        }

        if (line.startsWith('??) && line.endsWith('??)) {
            const mName = line.replace(/[?/g, '');
            const found = props.masters?.find(m => m.name.includes(mName));
            if (found) currentMasterId = found.id;
            continue;
        }

        const isNewItemTrigger = line.startsWith('瘜窄?迂:') || line.startsWith('瘜窄?迂嚗?);
        const isStatus = line.match(/(撌脩閮撌脫?敺?芣?敺?/);
        const isDate = line.match(/\b\d{4}[\/\-]\d{1,2}[\/\-]\d{1,2}\b/) || line.match(/\b\d{1,2}[\/\-]\d{1,2}\b/);
        const isPurpose = line.startsWith('?冽?') || line.startsWith('?冽?') || line.startsWith('?券?);
        const looksLikePlainName = !line.includes('嚗?) && !line.includes(':') && !isStatus && !isDate && !isPurpose && !isNewItemTrigger;

        if (isNewItemTrigger) {
            if (currentRec) {
                records.push(currentRec);
            }
            let name = line.replace(/^瘜窄?迂[:嚗\s*/, '').trim();
            currentRec = { name: name, purpose: '-', personnel: [], remarks: [], status: '撌脩閮?, master_id: currentMasterId, date: currentDateInText || '' };
        } else if (looksLikePlainName) {
            if (!currentRec) {
                currentRec = { name: line, purpose: '-', personnel: [], remarks: [], status: '撌脩閮?, master_id: currentMasterId, date: currentDateInText || '' };
            } else {
                if (!currentRec.name) {
                    currentRec.name = line;
                } else {
                    if (batchType.value === 'single') {
                        records.push(currentRec);
                        currentRec = { name: line, purpose: '-', personnel: [], remarks: [], status: '撌脩閮?, master_id: currentMasterId, date: currentDateInText || '' };
                    } else {
                        currentRec.personnel.push({ custom_name: line, status: '撌脩閮?, obtained_date: '', remarks: '' });
                    }
                }
            }
        } else if (currentRec) {
            if (isPurpose) {
                currentRec.purpose = line.replace(/.*(?冽?|?冽?|?券?[:嚗?\s*/, '').trim() || '-';
            } else if (isStatus) {
                const statusStr = isStatus[0];
                const namePart = line.split(statusStr)[0].replace(/??:嚗?/, '').trim();
                
                if (namePart) {
                    const pDate = isDate ? isDate[0].replace(/\//g, '-') : '';
                    currentRec.personnel.push({ custom_name: namePart, status: statusStr, obtained_date: pDate, remarks: '' });
                } else {
                    if (currentRec.personnel.length > 0) {
                        currentRec.personnel[currentRec.personnel.length - 1].status = statusStr;
                        if (isDate) currentRec.personnel[currentRec.personnel.length - 1].obtained_date = isDate[0];
                    } else {
                        currentRec.status = statusStr;
                        if (isDate) currentRec.date = isDate[0];
                    }
                }
            } else if (line.includes('?芣?敺?)) {
                currentRec.status = '?芣?敺?;
                if (!line.includes('隞乩?瘝?鞈?')) currentRec.remarks.push(line);
            } else if (isDate) {
                if (currentRec.personnel.length > 0) {
                     currentRec.personnel[currentRec.personnel.length - 1].obtained_date = isDate[0];
                } else {
                     currentRec.date = isDate[0];
                     currentDateInText = isDate[0];
                }
            } else if (!line.includes('隞乩?瘝?鞈?')) {
                currentRec.remarks.push(line);
            }
        }
    }
    if (currentRec) records.push(currentRec);
    
    return records.map(r => {
        let finalStatus = r.status;
        if (r.personnel.length > 0) {
            const hasUnobtained = r.personnel.some(p => p.status === '?芣?敺?);
            const allRegistered = r.personnel.every(p => p.status === '撌脩閮?);
            if (hasUnobtained) finalStatus = '?芣?敺?;
            else if (allRegistered) finalStatus = '撌脩閮?;
            else finalStatus = '撌脫?敺?;
        }

        return {
            c0: r.name,
            c1: r.purpose,
            _dharma_name_registries: r.personnel,
            _manualRemarks: r.remarks.join('\n'),
            _is_multi: r.personnel.length > 0,
            _status: finalStatus,
            _record_date: r.date,
            _master_id: r.master_id
        };
    });
});

const personnel = ref([]);
const dharmaNames = ref([]);
const isMulti = ref(true);

const fetchDharmaNames = async () => {
    try {
        const res = await axios.get('/api/dharma-names-list');
        dharmaNames.value = res.data;
    } catch (e) {}
};

const addPersonnelRow = () => {
    alert('?桐犖?踵?⊿?雿輻憭犖?踵璅∪?');
    personnel.value.push({ custom_name: '', relationship: '', obtained_date: '', status: '撌脫?敺?, remarks: '' });
};

const handlePersonnelEnter = (idx) => {
    if (idx === personnel.value.length - 1) addPersonnelRow();
    nextTick(() => {
        const inputs = document.querySelectorAll('.personnel-name-input');
        if (inputs[idx + 1]) inputs[idx + 1].focus();
    });
};

const removePersonnelRow = (idx) => { personnel.value.splice(idx, 1); };
const movePersonnel = (idx, dir) => {
    const target = idx + dir;
    if (target < 0 || target >= personnel.value.length) return;
    const item = personnel.value[idx];
    personnel.value.splice(idx, 1);
    personnel.value.splice(target, 0, item);
};

onMounted(() => {
    fetchDharmaNames();
});

watch(() => props.initialData, (newVal) => {
    form.value = { ...newVal };
    if (newVal.dharma_name_registries) {
        personnel.value = newVal.dharma_name_registries.map(r => ({
            custom_name: r.dharma_name?.name || r.custom_name || '',
            relationship: Array.isArray(r.related_personnel) ? r.related_personnel.join('??) : (r.related_personnel || ''),
            obtained_date: r.obtained_date || '',
            status: r.status || '撌脫?敺?,
            remarks: Array.isArray(r.remarks) ? r.remarks.join('\n') : (r.remarks || '')
        }));
    }
}, { immediate: true });

// Auto-fill date when status changes
watch(() => form.value.status, (newStatus) => {
    if ((newStatus === '撌脫?敺? || newStatus === '撌脩閮?) && !form.value.obtained_date) {
        form.value.obtained_date = new Date().toISOString().split('T')[0];
    }
});

watch(personnel, (newVal) => {
    newVal.forEach(p => {
        // Auto-fill date
        if ((p.status === '撌脫?敺? || p.status === '撌脩閮?) && !p.obtained_date) {
            p.obtained_date = new Date().toISOString().split('T')[0];
        }

        // Relationship Rule: "Name銋elative" split
        if (p.custom_name && p.custom_name.trim()) {
            const relSplitMatch = p.custom_name.match(/^(.*?)([銋?])(.+)$/);
            if (relSplitMatch) {
                const namePart = relSplitMatch[1].trim();
                let connector = relSplitMatch[2];
                let relPart = relSplitMatch[3].trim();
                
                p.custom_name = namePart;
                p.relationship = connector + relPart;
            }
        }
    });
}, { deep: true });

const handleSubmit = () => {
    if (localMode.value === 'single') {
        if (!form.value.name?.trim()) { alert('隢撓?交?撖嗅?蝔?); return; }
        const cleaned = personnel.value.filter(p => p.custom_name?.trim());
        const isMulti = cleaned.length > 0;
        
        let finalStatus = form.value.status;
        if (isMulti) {
            const hasUnobtained = cleaned.some(p => p.status === '?芣?敺?);
            const allRegistered = cleaned.every(p => p.status === '撌脩閮?);
            if (hasUnobtained) finalStatus = '?芣?敺?;
            else if (allRegistered) finalStatus = '撌脩閮?;
            else finalStatus = '撌脫?敺?;
        }
        
        emit('saveSingle', { 
            ...form.value,
            status: finalStatus,
            is_multi: isMulti,
            dharma_name_registries: cleaned.map(p => ({
                ...p,
                status: p.status || '撌脫?敺?,
                obtained_date: p.obtained_date || '',
                remarks: p.remarks || '',
                related_personnel: p.relationship ? p.relationship.split(/[?? ]+/).filter(x => x) : []
            }))
        });
    } else {
        emit('saveBatch', { 
            input: batchInput.value, 
            masterId: form.value.master_id,
            rows: excelRows.value.map(row => ({
                name: row.c0, 
                purpose: row.c1, 
                master_id: row._master_id || form.value.master_id,
                record_date: row._record_date || '', 
                obtained_date: row._record_date || '',
                status: row._status || '撌脩閮?, 
                count: 1, 
                remarks: row._manualRemarks || '',
                is_multi: row._is_multi || false,
                dharma_name_registries: (row._dharma_name_registries || []).map(p => ({
                    ...p,
                    obtained_date: p.obtained_date || row._record_date || ''
                }))
            }))
        });
    }
};

const getMasterName = (id) => {
    const m = props.masters?.find(m => String(m.id) === String(id));
    return m ? (m.name === '?嗥?隞葦' ? '?嗥?' : m.name) : '?身';
};

const handleFileUpload = (e) => { /* logic */ };
const handleDirectPaste = async () => { /* logic */ };
</script>

<style scoped>
.animate-slide-up { animation: slideUp 0.4s cubic-bezier(0.16, 1, 0.3, 1); }
.animate-fade-in { animation: fadeIn 0.3s ease-out; }
@keyframes slideUp { from { transform: translateY(100%); } to { transform: translateY(0); } }
@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
.no-scrollbar::-webkit-scrollbar { display: none; }
</style>
