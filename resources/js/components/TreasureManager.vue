<template>
    <div class="bg-white h-[100dvh] flex flex-col relative overflow-hidden text-slate-900">
        <!-- Header (Only show in Folder-view or Item-view) -->
        <div v-if="currentFolder" class="border-b border-slate-300 flex items-center justify-center bg-white/80 backdrop-blur-md sticky top-0 z-10" style="padding: 12px 10px 10px 10px;">
            <h2 class="text-[21px] font-normal font-outfit tracking-tight text-slate-800">
                <span v-if="focusedId && displayTitle !== currentFolder?.name" class="text-indigo-600 truncate max-w-[200px] block">{{ displayTitle }}</span>
                <span v-else>{{ currentFolder ? '法寶登記 - ' + currentFolder.name : '法寶登記專區' }}</span>
            </h2>
        </div>

        <!-- Perfectly Centered Ultra-Compact Warning Banner -->
        <div v-if="persistentToast" class="fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 z-[9999] animate-toast-in pointer-events-auto">
            <div class="bg-white rounded-lg shadow-[0_4px_20px_rgba(0,0,0,0.2)] flex flex-col border border-slate-200"
                style="padding: 8px 15px; min-width: 140px; max-width: 85vw;">
                <div class="flex items-start justify-between space-x-3">
                    <span class="text-[12px] font-medium leading-normal text-red-600 break-words uppercase tracking-wide">
                        {{ persistentToast.msg }}
                    </span>
                    <button v-if="['confirm'].includes(persistentToast.type)" 
                        @click="persistentToast = null" 
                        class="text-slate-400 hover:text-slate-600 w-5 h-5 flex items-center justify-center font-normal text-sm">✕</button>
                </div>
                <div v-if="persistentToast.type === 'confirm'" class="flex flex-col space-y-1.5 mt-2 pb-1">
                    <button v-for="action in persistentToast.actions" :key="action.label"
                        @click="action.handler"
                        :class="[action.primary ? 'bg-red-50 text-red-600 border-red-100' : 'bg-slate-50 text-slate-600 border-slate-200']"
                        class="w-full p-[5px] rounded border text-[11px] font-medium whitespace-nowrap active:scale-[0.98] transition-all">
                        {{ action.label }}
                    </button>
                    <button @click="persistentToast = null" class="w-full bg-white text-slate-400 px-2 py-1.5 rounded text-[11px] font-normal">取消</button>
                </div>
            </div>
        </div>

        <!-- Main Scrollable Area -->
        <div class="flex-1 overflow-y-auto custom-scrollbar" style="padding-bottom: 40px;">
        <!-- Level 1: Folder Selection -->
        <div v-if="!currentFolder && !addMode" class="min-h-screen bg-white">
            <!-- Large Static Title -->
            <div class="px-6 py-4 text-center">
                <h1 class="text-2xl font-normal text-slate-800 tracking-tight">法寶登記專區</h1>
                <p class="text-[10px] text-slate-400 font-normal uppercase tracking-widest mt-1">完整記載每份法寶與獲得進度</p>
            </div>



            <div class="grid grid-cols-2 md:grid-cols-2 gap-3 p-4">
                <button v-for="(folder, idx) in folders" :key="folder.id" 
                    @click="currentFolder = folder"
                    class="flex flex-col items-center justify-center bg-transparent transition-all active:scale-95 rounded-[28px] border border-[#EF4444] group p-2 aspect-square relative w-[72%] mx-auto"
                   >
                    <div class="relative">
                        <svg class="w-14 h-14 transition-transform group-hover:scale-110 drop-shadow-md" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <defs>
                                <linearGradient id="folderGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                                    <stop offset="0%" style="stop-color:#FFF9C4;stop-opacity:1" />
                                    <stop offset="50%" style="stop-color:#FDE047;stop-opacity:1" />
                                    <stop offset="100%" style="stop-color:#FBC02D;stop-opacity:1" />
                                </linearGradient>
                            </defs>
                            <!-- Folder Body Back -->
                            <path d="M4 14C4 11.7909 5.79086 10 8 10H24.5L30 16H56C58.2091 16 60 17.7909 60 20V50C60 52.2091 58.2091 54 56 54H8C5.79086 54 4 52.2091 4 50V14Z" fill="url(#folderGrad)" opacity="0.8"/>
                            <!-- Folder Front Cover (3D Offset) -->
                            <path d="M4 22C4 19.7909 5.79086 18 8 18H56C58.2091 18 60 19.7909 60 22V50C60 52.2091 58.2091 54 56 54H8C5.79086 54 4 52.2091 4 50V22Z" fill="url(#folderGrad)" stroke="rgba(255,255,255,0.4)" stroke-width="0.5"/>
                            <!-- Shine effect -->
                            <path d="M10 21H54" stroke="white" stroke-opacity="0.3" stroke-linecap="round"/>
                        </svg>
                    </div>
                    <div class="text-center px-1 mt-1">
                        <div class="text-[14px] leading-tight text-slate-700 break-words font-normal">
                            {{ folder.name }}
                        </div>
                    </div>
                </button>
            </div>


            <!-- Minimalist Back to Dashboard -->
            <div class="mt-12 flex justify-center pb-32">
                <button @click="$emit('goHome')" class="text-slate-300 hover:text-slate-500 transition-colors flex items-center space-x-2 active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" /></svg>
                    <span class="text-xs font-medium tracking-widest uppercase">返回專區列表</span>
                </button>
            </div>
        </div>

        <!-- Level 2: Folder Contents -->
        <div v-else class="pb-32 px-[5px] md:px-0">

            <!-- List Display -->
            <div v-if="!addMode" style="padding: 0px 10px 10px 10px;" class="mt-[-23px]">
                <div v-if="!loading && allTreasures.length > 0 && !addMode" class="mb-3 px-1">
                </div>

                <div v-if="loading" class="text-center py-4 text-xs text-slate-400">載入中...</div>
                <div v-else-if="filteredTreasures.length === 0" class="text-center py-12 text-slate-400">
                    {{ searchQuery ? '找不到符合的法寶' : '目前該專區尚無法寶資料。' }}
                </div>
                <div v-else class="flex flex-col">
                    <div v-for="(item, index) in (expandedIds.size > 0 ? filteredTreasures.filter(t => expandedIds.has(t.id)) : filteredTreasures)" :key="item.id" 
                        class="border-b border-solid border-slate-300 py-[4px] last:border-b-0 relative bg-white cursor-default group"
                        :class="{ 'border-t border-slate-300': index === 0 }">
                        <!-- Row 1: Dates & Actions -->
                        <div class="flex items-center justify-between py-1 transition-all duration-300" 
                             :style="editingIds.has(item.id) ? 'transform: translateY(-25px)' : ''">
                            <div v-show="!editingIds.has(item.id)" class="flex flex-col">
                                <div class="text-[10px] uppercase font-medium text-slate-400 mb-0 tracking-tight">法寶名稱</div>
                                <div class="text-[17px] font-medium text-slate-800 leading-tight tracking-wide uppercase">{{ item.name }}</div>
                            </div>
                            
                            <div class="relative ml-auto" :class="[deleteConfirmId === item.id ? 'text-red-500' : 'text-slate-700']">
                                <button @click.stop="toggleMenu(item.id)" class="p-1 -mr-1">
                                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM18 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                                </button>
                                <!-- DROP-DOWN MENU -->
                                <div v-if="openMenuId === item.id" @click.stop
                                    class="absolute right-0 top-full mt-1 w-24 bg-white rounded-xl shadow-2xl border border-slate-100 z-[100] overflow-hidden animate-slide-up">
                                    <button @click="toggleExpand(item.id); openMenuId = null;" class="w-full p-[5px] text-left text-[11px] text-slate-600 hover:bg-slate-50 border-b border-slate-50 whitespace-nowrap">
                                        {{ expandedIds.has(item.id) ? '縮起清單' : '展開清單' }}
                                    </button>
                                    <button @click="openAndEdit(item.id)" class="w-full p-[5px] text-left text-[11px] text-slate-600 hover:bg-slate-50 border-b border-slate-50">修改內容</button>
                                    <button @click.stop="copyOnly(item)" class="w-full p-[5px] text-left text-[11px] text-green-600 hover:bg-green-50 border-b border-slate-50 font-medium whitespace-nowrap">複製貼 LINE</button>
                                    <button @click.stop="downloadOnly(item, 'excel')" class="w-full p-[5px] text-left text-[11px] text-emerald-600 hover:bg-emerald-50 border-b border-slate-50 font-medium whitespace-nowrap">下載 Excel</button>
                                    <button @click.stop="confirmDelete(item.id)" class="w-full p-[5px] text-left text-[11px] text-red-600 hover:bg-red-50">刪除</button>
                                </div>
                            </div>
                        </div>

                        <!-- Collapsible Rows -->
                        <div v-if="expandedIds.has(item.id)" @click.stop 
                            class="animate-fade-in bg-slate-50 rounded p-[5px] transition-all duration-300"
                            :class="[editingIds.has(item.id) ? 'mt-[-40px]' : 'mt-1']">
                            <!-- EDIT MODE -->
                            <div v-if="editingIds.has(item.id)" class="grid grid-cols-1 gap-[5px] mb-3">
                                <div class="flex items-center space-x-2">
                                    <span class="text-[11px] font-medium text-slate-500 shrink-0 w-16">法寶名稱：</span>
                                    <input v-model="item.name" type="text" 
                                        class="flex-1 text-[13px] p-1 border border-slate-200 rounded focus:border-indigo-500 focus:ring-0">
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span class="text-[11px] font-medium text-slate-500 shrink-0 w-16">法寶用意：</span>
                                    <input v-model="item.purpose" type="text" placeholder="用意"
                                        class="flex-1 text-[13px] p-1 border border-slate-200 rounded focus:border-indigo-500 focus:ring-0">
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span class="text-[11px] font-medium text-slate-500 shrink-0 w-16">求寶方式：</span>
                                    <input v-model="item.acquisition_method" type="text" placeholder="求寶方式"
                                        class="flex-1 text-[13px] p-1 border border-slate-200 rounded focus:border-indigo-500 focus:ring-0">
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span class="text-[11px] font-medium text-slate-500 shrink-0 w-16">備註：</span>
                                    <input v-model="item.remarks" type="text" placeholder="備註"
                                        class="flex-1 text-[13px] p-1 border border-slate-200 rounded focus:border-indigo-500 focus:ring-0">
                                </div>
                            </div>

                            <div v-else class="space-y-2 mb-2">
                                <div v-if="item.purpose">
                                    <div class="text-[10px] font-medium text-slate-400 uppercase tracking-tight">法寶用意</div>
                                    <div class="text-[14.5px] text-slate-700 leading-snug">{{ item.purpose }}</div>
                                </div>
                                <div v-if="item.acquisition_method">
                                    <div class="text-[10px] font-medium text-slate-400 uppercase tracking-tight">求寶方式</div>
                                    <div class="text-[14.5px] text-slate-700 leading-snug">{{ item.acquisition_method }}</div>
                                </div>
                                <div v-if="item.remarks">
                                    <div class="text-[10px] font-medium text-slate-400 uppercase tracking-tight">備註</div>
                                    <div class="text-[14.5px] text-slate-600 leading-snug italic">{{ item.remarks }}</div>
                                </div>
                            </div>
                            
                            <!-- Dharma Names Table -->
                            <div class="mt-3 border border-slate-200 rounded-lg overflow-hidden bg-white">
                                <table class="w-full text-[12px] border-collapse">
                                    <thead class="bg-slate-50">
                                        <tr>
                                            <th class="border-b border-r border-slate-200 py-1 px-2 text-left font-medium text-slate-700 w-16">法號</th>
                                            <th class="border-b border-r border-slate-200 py-1 px-2 text-left font-medium text-slate-700 w-20">日期</th>
                                            <th class="border-b border-slate-200 py-1 px-2 text-left font-medium text-slate-700">備註</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="dn in getSortedDharmaNames()" :key="dn.id" class="border-b last:border-b-0 border-slate-100">
                                            <td class="border-r border-slate-100 py-1 px-2 text-slate-800 font-medium">{{ dn.name }}</td>
                                            
                                            <td class="border-r border-slate-100 py-1 px-1">
                                                <div class="flex items-center space-x-0.5">
                                                    <div class="bg-transparent rounded px-1 flex-1 max-w-[80px] min-w-[70px]">
                                                        <input type="text" 
                                                            :value="getDharmaNameRecord(item, dn.id).obtained_date ? getDharmaNameRecord(item, dn.id).obtained_date.replace(/-/g, '/') : ''"
                                                            @input="handleDharmaDateInput(item, dn.id, $event.target.value)"
                                                            placeholder="年/月/日"
                                                            class="w-full p-0 border-none text-[11px] bg-transparent focus:ring-0 text-slate-600">
                                                    </div>
                                                    <button @click.stop="openPickerForDharma(item, dn.id, dn.name)" 
                                                        class="p-0.5 text-slate-400 hover:text-indigo-500 transition-colors shrink-0">
                                                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2v12a2 2 0 002 2z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                                    </button>
                                                </div>
                                            </td>

                                            <!-- Remarks Column -->
                                            <td class="py-1 px-2">
                                                <template v-if="editingIds.has(item.id)">
                                                    <input type="text" 
                                                        :value="getDharmaNameRecord(item, dn.id).remarks"
                                                        @input="updateDharmaNameRecord(item, dn.id, 'remarks', $event.target.value)"
                                                        placeholder="備註"
                                                        class="w-full p-0 border-none text-[11px] bg-transparent focus:ring-0">
                                                </template>
                                                <template v-else>
                                                    <span v-if="getDharmaNameRecord(item, dn.id).remarks" class="text-slate-500 italic">{{ getDharmaNameRecord(item, dn.id).remarks }}</span>
                                                </template>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div v-if="editingIds.has(item.id)" class="sticky bottom-10 flex justify-end pointer-events-none z-[80]">
                                <button @click.stop="saveDharmaDetails(item)" 
                                    class="pointer-events-auto bg-white text-indigo-600 p-[5px] rounded-full text-[13px] font-medium shadow-[0_4px_15px_rgba(0,0,0,0.1)] hover:bg-slate-50 transition-all active:scale-95 border border-indigo-200">
                                    儲存所有修改內容
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        </div> <!-- End Scrollable Area -->

        <div class="fixed bottom-0 left-0 right-0 bg-white/95 backdrop-blur-md border-t border-slate-100 z-50 shadow-[0_-4px_10px_rgba(0,0,0,0.03)]" style="height: 30px;">
            <div class="grid grid-cols-5 h-full items-center px-2">
                <!-- BACK BUTTON -->
                <div class="flex justify-center">
                    <button @click="handleBack" 
                        class="w-7 h-7 rounded-xl flex items-center justify-center transition-all active:scale-90 text-slate-400 hover:bg-slate-50">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" /></svg>
                    </button>
                </div>

                <!-- HOME BUTTON -->
                <div class="flex justify-center">
                    <button @click="$emit('goHome')" class="w-7 h-7 rounded-xl flex items-center justify-center transition-all active:scale-95 text-slate-400 hover:bg-slate-50">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                </div>

                <!-- ADD BUTTON (Center) -->
                <div class="flex justify-center items-center">
                    <button v-if="currentFolder?.id !== 'unobtained'" @click="showAddMenu = !showAddMenu" 
                        :class="[showAddMenu ? 'bg-slate-800 rotate-45 scale-90' : 'bg-indigo-600 text-white shadow-sm active:scale-95']"
                        class="w-7 h-7 rounded-xl flex items-center justify-center transition-all duration-500">
                        <svg class="h-[10px] w-[10px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                </div>

                <!-- SEARCH BUTTON -->
                <div class="flex justify-center">
                    <button @click="showSearch = !showSearch" 
                        :class="showSearch ? 'text-indigo-600 bg-indigo-50' : 'text-slate-400'"
                        class="w-7 h-7 rounded-xl flex items-center justify-center transition-all active:scale-95 hover:bg-slate-50">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" /></svg>
                    </button>
                </div>

                <!-- EXPORT BUTTON -->
                <div class="flex justify-center">
                    <div class="relative">
                        <button @click="showExportMenu = !showExportMenu" class="w-7 h-7 rounded-xl flex items-center justify-center transition-all active:scale-95 text-slate-400 hover:bg-slate-50">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </button>
                        <div v-if="showExportMenu" class="absolute bottom-16 right-0 w-36 bg-white rounded-3xl shadow-[0_15px_50px_rgba(0,0,0,0.15)] border border-slate-100 overflow-hidden animate-slide-up z-[60] p-1.5">
                            <button @click="downloadList('excel'); showExportMenu = false" class="w-full py-3 px-4 text-xs font-medium text-emerald-600 hover:bg-emerald-50 rounded-2xl transition-colors">下載 Excel</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- MODAL COMPONENTS -->
        <search-component 
            v-model="searchQuery" 
            :show="showSearch" 
            @close="showSearch = false" 
        />

        <add-action-menu 
            :show="showAddMenu" 
            @close="showAddMenu = false"
            :actions="addActions"
        />

        <treasure-add-form 
            ref="addForm"
            :mode="addMode" 
            :initialData="form"
            :masters="masters"
            :isSaving="isSaving"
            @saveSingle="saveSingle"
            @saveBatch="triggerBatchSave"
            @cancel="addMode = null"
            @fileUpload="handleFileUpload"
        />

        <CompactDatePicker 
            v-if="activePicker" 
            :modelValue="activePicker.target[activePicker.field]"
            :title="activePicker.title"
            @update:modelValue="activePicker.target[activePicker.field] = $event"
            @close="activePicker = null" 
        />
    </div>
</template>

<script setup>
import { ref, computed, onMounted, defineEmits } from 'vue';
import axios from 'axios';
import CompactDatePicker from './CompactDatePicker.vue';

const emit = defineEmits(['goHome']);

const currentFolder = ref(null);
const addMode = ref(null);
const allTreasures = ref([]);
const masters = ref([]);
const dharmaNames = ref([]);
const loading = ref(false);
const persistentToast = ref(null);
import { watch } from 'vue';
watch(persistentToast, (newVal) => {
    if (newVal && newVal.type !== 'confirm') {
        setTimeout(() => { persistentToast.value = null; }, 1000);
    }
    if (!newVal) {
        deleteConfirmId.value = null;
    }
});

const showToast = (msg, type = 'success') => {
    persistentToast.value = { msg, type };
};

const openMenuId = ref(null);
const expandedIds = ref(new Set());
const editingIds = ref(new Set());
const focusedId = ref(null);
const activePicker = ref(null); // { target, field }
const showAddMenu = ref(false);
const showSearch = ref(false);
const showExportMenu = ref(false);
const searchQuery = ref('');
const batchInput = ref('');
const batchMasterId = ref(null);
const batchDate = ref(new Date().toISOString().split('T')[0]);
const isSaving = ref(false);
const deleteConfirmId = ref(null);
const addForm = ref(null);

const addActions = computed(() => [
    { 
        label: '逐筆新增', 
        description: '手動輸入每一項法寶詳細資料',
        icon: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>',
        handler: () => prepareAdd('single') 
    },
    { 
        label: '多筆一次新增', 
        description: '支援 Excel、LINE 內容快速解析匯入',
        icon: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>',
        handler: () => prepareAdd('batch') 
    }
]);

const displayTitle = computed(() => {
    if (focusedId.value) {
        const item = allTreasures.value.find(t => t.id === focusedId.value);
        return item ? item.name : (currentFolder.value?.name || '法寶登記');
    }
    return currentFolder.value?.name || '法寶登記';
});

const triggerBatchSave = (data) => {
    batchInput.value = data.input;
    batchMasterId.value = data.masterId;
    batchDate.value = data.date;
    saveBatch();
};

const form = ref({
    master_id: null,
    name: '',
    purpose: '',
    acquisition_method: '',
    remarks: '',
    record_date: new Date().toISOString().split('T')[0],
    obtained_date: ''
});

const folders = ref([
    { id: 1, name: '老祖仙師' },
    { id: 2, name: '元始仙師' },
    { id: 3, name: '道祖仙師' },
    { id: 4, name: '靈寶仙師' },
    { id: 5, name: '父皇' },
    { id: 6, name: '太宰仙師' },
    { id: 7, name: '太子' },
    { id: 8, name: '閻王仙師' }
]);

const loadData = async () => {
    loading.value = true;
    try {
        const [res, mres, dres] = await Promise.all([
            axios.get('/treasures'),
            axios.get('/api/masters-list'),
            axios.get('/api/dharma-names-list')
        ]);
        allTreasures.value = res.data;
        masters.value = mres.data;
        dharmaNames.value = dres.data;
    } catch (e) {
        console.error(e);
    } finally {
        loading.value = false;
    }
};

onMounted(loadData);

const handleBack = () => {
    if (addMode.value) {
        addMode.value = null;
    } else if (currentFolder.value) {
        currentFolder.value = null;
        focusedId.value = null;
        expandedIds.value.clear();
    } else {
        emit('goHome');
    }
};

watch(currentFolder, () => {
    focusedId.value = null;
    expandedIds.value.clear();
    openMenuId.value = null;
});

const prepareAdd = (mode) => {
    form.value = {
        id: null,
        master_id: currentFolder.value ? currentFolder.value.id : null,
        name: '',
        purpose: '',
        acquisition_method: '',
        remarks: '',
        record_date: new Date().toISOString().split('T')[0],
        obtained_date: ''
    };
    if (mode === 'batch') {
        batchInput.value = '';
        batchMasterId.value = currentFolder.value?.id || null;
        batchDate.value = new Date().toISOString().split('T')[0];
    }
    addMode.value = mode;
};



const saveBatch = async (forceParam = false, resolvedMasterId = null) => {
    if (isSaving.value) return;
    const force = forceParam === true; // Strictly ignore MouseEvent

    const rawLines = batchInput.value.split('\n').filter(l => l.trim());
    if (rawLines.length === 0) {
        showToast('請輸入清單內容', 'error');
        return;
    }

    // Step 1: Smart Parsing for Vertical Headers (Master, Name, Purpose)
    let tName = form.value.name;
    let tPurpose = form.value.purpose;
    let dataStartIndex = 0;

    // Detect Vertical Header Pattern (Row1: Master Name present anywhere in line 1)
    const line1 = (rawLines[0] || '').trim();
    const extractedMaster = masters.value.find(m => line1.includes(m.name));
    
    if (extractedMaster) {
        // Compare with CURRENT dropdown selection or folder
        const dropdownId = Number(batchMasterId.value);
        const folderId = currentFolder.value ? Number(currentFolder.value.id) : dropdownId;
        const targetId = Number(extractedMaster.id);
        
        // If the detected master from text is different from what's currently active
        if (!force && targetId !== folderId) {
            const targetName = extractedMaster.name;
            const currentName = masters.value.find(m => Number(m.id) === folderId)?.name || '目前專區';
            
            persistentToast.value = { 
                msg: `注意！偵測到名單內仙師為「${targetName}」。`, 
                type: 'confirm',
                actions: [
                    { 
                        label: `分流存入「${targetName}」`, 
                        primary: true,
                        handler: () => { 
                            persistentToast.value = null;
                            saveBatch(true, targetId);
                        } 
                    },
                    { 
                        label: `改成存入「${currentName}」`, 
                        handler: () => { 
                            persistentToast.value = null;
                            saveBatch(true, folderId);
                        } 
                    }
                ]
            };
            return; // STOP EXECUTION
        }
        
        batchMasterId.value = extractedMaster.id;
        tName = (rawLines[1] || '').split(/[,\t]/)[0]?.trim() || tName;
        tPurpose = (rawLines[2] || '').split(/[,\t]/)[0]?.trim() || tPurpose;
        dataStartIndex = 3;
        // Skip empty separator rows if any
        while (dataStartIndex < rawLines.length) {
            const parts = rawLines[dataStartIndex].split(/[,\t]/).map(p => p.trim()).filter(p => p);
            if (parts.length > 0) break;
            dataStartIndex++;
        }
    }

    const year = new Date().getFullYear();
    const records = [];

    rawLines.slice(dataStartIndex).forEach(line => {
        // 排除標題列 (Skip common header labels)
        if (line.includes('法寶名稱') || line.includes('日期') || line.includes('法寶用意')) return;
        
        const parts = line.split(/[,\t]/).map(p => p.trim()).filter(p => p);
        if (parts.length === 0) return;

        let dName = "";
        let dDate = batchDate.value;

        // Pattern: [Date, DharmaName] or [DharmaName, Date]
        const dn = dharmaNames.value.find(dn => line.includes(dn.name));
        if (dn) {
            dName = dn.name;
            const dateMatch = line.match(/(\d{1,2}月\d{1,2}日)|(\d{4}[/-]\d{1,2}[/-]\d{1,2})|(\d{1,2}[/-]\d{1,2})/);
            if (dateMatch) {
                let dStr = dateMatch[0].replace(/月/g, '/').replace(/日/g, '').replace(/\//g, '-');
                if (dStr.split('-').length === 2) {
                    const [m, d] = dStr.split('-');
                    dDate = `${year}-${m.padStart(2, '0')}-${d.padStart(2, '0')}`;
                } else {
                    dDate = dStr;
                }
            }
        }

        if (dName) {
            records.push({
                dharma_name_id: dharmaNames.value.find(dn => dn.name === dName).id,
                obtained_date: dDate,
                remarks: ''
            });
        }
    });

    if (records.length === 0) {
        showToast('無法解析任何有效內容', 'error');
        return;
    }

    // Validation
    if (!force && currentFolder.value) {
        const batchMid = Number(batchMasterId.value);
        const folderId = Number(currentFolder.value.id);
        if (batchMid !== folderId && !isNaN(folderId)) {
            const targetM = masters.value.find(m => Number(m.id) === batchMid);
            persistentToast.value = { msg: `仙師不符！存入「${targetM?.name || '其他'}」嗎？`, type: 'confirm' };
            return;
        }
    }

    // Duplicate Check for batch items
    let duplicateName = null;
    for (const record of records) {
        if (allTreasures.value.some(t => t.name.trim() === tName.trim())) {
            duplicateName = tName;
            break;
        }
    }
    if (duplicateName) {
        showToast(`「${duplicateName}」已存在，不可重複登記。`, 'error');
        return;
    }

    isSaving.value = true;
    try {
        await axios.post('/treasures', {
            master_id: batchMasterId.value,
            record_date: batchDate.value,
            name: tName || "未命名法寶",
            purpose: tPurpose,
            acquisition_method: form.value.acquisition_method,
            remarks: form.value.remarks,
            dharma_name_treasures: records
        });
        showToast(`✓ 成功新增法寶及其 ${records.length} 筆法號記錄`);
        loadData();
        addMode.value = null;
        searchQuery.value = '';
    } catch (e) {
        console.error(e);
        showToast('批次新增失敗', 'error');
    } finally {
        isSaving.value = false;
    }
};

const handleFileUpload = (event) => {
    const file = event.target.files[0];
    if (!file) return;

    const ext = file.name.split('.').pop().toLowerCase();
    const reader = new FileReader();

    if (['xlsx', 'xls', 'csv'].includes(ext)) {
        reader.onload = (e) => {
            try {
                const data = new Uint8Array(e.target.result);
                const workbook = window.XLSX.read(data, { type: 'array' });
                const firstSheet = workbook.Sheets[workbook.SheetNames[0]];
                // Convert to CSV string which our batch parser can already handle
                const csv = window.XLSX.utils.sheet_to_csv(firstSheet);
                batchInput.value = csv;
            } catch (err) {
                showToast('Excel 讀取失敗', 'error');
            }
        };
        reader.readAsArrayBuffer(file);
    } else if (['docx', 'doc'].includes(ext)) {
        reader.onload = (e) => {
            if (ext === 'doc') {
                showToast('不支援舊版 .doc，請另存為 .docx', 'warning');
                return;
            }
            window.mammoth.extractRawText({ arrayBuffer: e.target.result })
                .then(result => { batchInput.value = result.value; })
                .catch(err => { showToast('Word 讀取失敗', 'error'); });
        };
        reader.readAsArrayBuffer(file);
    } else {
        reader.onload = (e) => { batchInput.value = e.target.result; };
        reader.readAsText(file);
    }
};

const toggleMenu = (id) => {
    openMenuId.value = openMenuId.value === id ? null : id;
    deleteConfirmId.value = null;
};

const openAndEdit = (id) => {
    expandedIds.value.add(id);
    editingIds.value.add(id);
    openMenuId.value = null;
};

const toggleExpand = (id) => {
    if (expandedIds.value.has(id)) {
        expandedIds.value.delete(id);
        editingIds.value.delete(id);
        searchQuery.value = ''; // 縮起清單時清除搜尋
        showSearch.value = false; // 縮起清單時收起搜尋框
    } else {
        expandedIds.value.add(id);
    }
    openMenuId.value = null;
};

const editItem = (item) => {
    form.value = { ...item };
    addMode.value = 'single';
    openMenuId.value = null;
};

const confirmDelete = (id) => {
    deleteConfirmId.value = id;
    persistentToast.value = { 
        msg: '確定要刪除這筆法寶登記嗎？', 
        type: 'confirm',
        actions: [
            {
                label: '確定刪除',
                primary: true,
                handler: () => { persistentToast.value = null; executeDelete(); }
            }
        ]
    };
    openMenuId.value = null;
};

const executeDelete = async () => {
    if (!deleteConfirmId.value) return;
    try {
        await axios.delete(`/treasures/${deleteConfirmId.value}`);
        showToast('✓ 已成功刪除');
        loadData();
    } catch (e) { showToast('刪除失敗', 'error'); }
    deleteConfirmId.value = null;
};

const saveSingle = async (forceOrData = false) => {
    if (isSaving.value) return;
    
    let force = false;
    if (forceOrData === true) {
        force = true;
    } else if (forceOrData && typeof forceOrData === 'object' && !forceOrData.target) {
        // Sync local form with data from child component
        form.value = { ...forceOrData };
    }

    // Duplicate Check
    const isDuplicate = allTreasures.value.find(t => 
        t.name.trim() === form.value.name.trim() && 
        Number(t.id) !== Number(form.value.id)
    );
    if (isDuplicate) {
        showToast(`「${form.value.name}」已存在於資料庫中，不可重複登記。`, 'error');
        return;
    }

    // 最終強制比對邏輯 (使用 Number 強制轉型)
    if (!force && currentFolder.value) {
        const formMid = Number(form.value.master_id);
        const folderId = Number(currentFolder.value.id);

        if (formMid !== folderId) {
            const targetM = masters.value.find(m => Number(m.id) === formMid);
            const targetName = targetM ? targetM.name : '其他仙師';
            const currentName = currentFolder.value.name;

            persistentToast.value = {
                msg: `注意！仙師與目前目錄不符。`,
                type: 'confirm',
                actions: [
                    {
                        label: `分流存入「${targetName}」`,
                        primary: true,
                        handler: () => { persistentToast.value = null; saveSingle(true); }
                    },
                    {
                        label: `存入目前「${currentName}」`,
                        handler: () => { 
                            form.value.master_id = currentFolder.value.id; 
                            persistentToast.value = null; 
                            saveSingle(true); 
                        }
                    }
                ]
            };
            return;
        }
    }

    if (!form.value.name) {
        showToast('請輸入法寶名稱', 'error');
        return;
    }
    
    isSaving.value = true;
    try {
        if (form.value.id) {
            await axios.post(`/treasures/${form.value.id}`, { ...form.value, _method: 'PATCH' });
            showToast('✓ 修改成功');
        } else {
            const data = { 
                ...form.value
            };
            // Ensure master_id is set
            if (!data.master_id && currentFolder.value) {
                data.master_id = currentFolder.value.id;
            }
            await axios.post('/treasures', data);
            showToast('✓ 新增成功');
        }
        addMode.value = null;
        searchQuery.value = ''; // 修改完後清除搜尋
        loadData();
    } catch (e) { 
        console.error(e);
        showToast('儲存失敗', 'error'); 
    } finally {
        isSaving.value = false;
    }
};

const getDharmaNameRecord = (treasure, dharmaNameId) => {
    if (!treasure.dharma_name_treasures) return { obtained_date: '', remarks: '' };
    const rec = treasure.dharma_name_treasures.find(r => r.dharma_name_id === dharmaNameId);
    return rec || { obtained_date: '', remarks: '' };
};

const handleDharmaDateInput = (treasure, dharmaNameId, val) => {
    // 8位數自動轉換 yyyymmdd -> yyyy/mm/dd
    if (val.length === 8 && /^\d+$/.test(val)) {
        val = `${val.slice(0, 4)}/${val.slice(4, 6)}/${val.slice(6, 8)}`;
    }
    
    // 移除不合法字元，僅保留數字與斜線
    const cleanVal = val.replace(/[^0-9/]/g, '');
    // 轉換為標準格式儲存 YYYY-MM-DD
    const parts = cleanVal.split('/');
    if (parts.length === 3 && parts[0].length === 4 && parts[1].length <= 2 && parts[2].length <= 2) {
        const standardDate = `${parts[0]}-${parts[1].padStart(2, '0')}-${parts[2].padStart(2, '0')}`;
        updateDharmaNameRecord(treasure, dharmaNameId, 'obtained_date', standardDate);
    } else {
        // 暫時更新 model 以免畫面不同步，但不一定是完整日期
        updateDharmaNameRecord(treasure, dharmaNameId, 'obtained_date', cleanVal.replace(/\//g, '-'));
    }
};

const openPickerForDharma = (treasure, dharmaNameId, dharmaName) => {
    if (!treasure.dharma_name_treasures) treasure.dharma_name_treasures = [];
    let rec = treasure.dharma_name_treasures.find(r => r.dharma_name_id === dharmaNameId);
    if (!rec) {
        rec = { dharma_name_id: dharmaNameId, obtained_date: '', remarks: '' };
        treasure.dharma_name_treasures.push(rec);
    }
    activePicker.value = { target: rec, field: 'obtained_date', title: dharmaName };
};

const updateDharmaNameRecord = (treasure, dharmaNameId, field, value) => {
    if (!treasure.dharma_name_treasures) treasure.dharma_name_treasures = [];
    let rec = treasure.dharma_name_treasures.find(r => r.dharma_name_id === dharmaNameId);
    if (!rec) {
        rec = { dharma_name_id: dharmaNameId, obtained_date: '', remarks: '' };
        treasure.dharma_name_treasures.push(rec);
    }
    rec[field] = value;
};

const saveDharmaDetails = async (treasure) => {
    isSaving.value = true;
    try {
        await axios.post(`/treasures/${treasure.id}`, { 
            ...treasure, 
            _method: 'PATCH' 
        });
        showToast('✓ 所有內容已成功儲存');
        editingIds.value.delete(treasure.id);
        searchQuery.value = ''; // 修改完後清除搜尋
        loadData();
    } catch (e) {
        showToast('儲存失敗', 'error');
    } finally {
        isSaving.value = false;
    }
};

const executeSave = () => {
    if (persistentToast.value?.msg.includes('刪除')) {
        executeDelete();
    } else if (addMode.value === 'batch') {
        persistentToast.value = null;
        saveBatch(true);
    } else {
        persistentToast.value = null;
        saveSingle(true); // 使用 force=true 跳過仙師比對
    }
};

const getSortedDharmaNames = () => {
    if (!searchQuery.value.trim()) return dharmaNames.value;
    const query = searchQuery.value.trim().toLowerCase();
    return [...dharmaNames.value].sort((a, b) => {
        const aMatch = a.name.toLowerCase().includes(query);
        const bMatch = b.name.toLowerCase().includes(query);
        if (aMatch && !bMatch) return -1;
        if (!aMatch && bMatch) return 1;
        return 0;
    });
};

const filteredTreasures = computed(() => {
    if (!currentFolder.value) return [];
    
    const filtered = allTreasures.value.filter(t => t.master_id === currentFolder.value.id);
    
    // Sort by Date Descending
    filtered.sort((a, b) => {
        const aDate = a.record_date || '0000-00-00';
        const bDate = b.record_date || '0000-00-00';
        return bDate.localeCompare(aDate);
    });

    if (searchQuery.value.trim()) {
        const query = searchQuery.value.trim().toLowerCase();
        const isMatch = (item) => {
            if (item.name.toLowerCase().includes(query)) return true;
            if (item.purpose?.toLowerCase().includes(query)) return true;
            if (item.acquisition_method?.toLowerCase().includes(query)) return true;
            if (item.remarks?.toLowerCase().includes(query)) return true;
            if (item.dharma_name_treasures) {
                return item.dharma_name_treasures.some(dt => {
                    const dn = dharmaNames.value.find(d => d.id === dt.dharma_name_id);
                    return dn?.name.toLowerCase().includes(query) || dt.remarks?.toLowerCase().includes(query);
                });
            }
            return false;
        };

        filtered.sort((a, b) => {
            const am = isMatch(a);
            const bm = isMatch(b);
            if (am && !bm) return -1;
            if (!am && bm) return 1;
            return 0;
        });
    }
    
    return filtered;
});

const totalDharmaConnections = computed(() => {
    return allTreasures.value.reduce((sum, t) => sum + (t.dharma_name_treasures?.length || 0), 0);
});

const getItemCount = (id) => {
    return allTreasures.value.filter(t => t.master_id === id).length;
};

const copyOnly = (item) => {
    let text = `[${formatDate(item.record_date)}] ${item.name}\n`;
    if (item.purpose) text += `用意：${item.purpose}\n`;
    if (item.acquisition_method) text += `求寶方式：${item.acquisition_method}\n`;
    if (item.remarks) text += `備註：${item.remarks}\n`;
    
    // Add dharma names if any
    if (item.dharma_name_treasures && item.dharma_name_treasures.length > 0) {
        const sortedRecords = [...item.dharma_name_treasures].sort((a, b) => {
            const indexA = dharmaNames.value.findIndex(dn => dn.id === a.dharma_name_id);
            const indexB = dharmaNames.value.findIndex(dn => dn.id === b.dharma_name_id);
            return indexA - indexB;
        });

        const lines = [];
        sortedRecords.forEach(dt => {
            const dn = dharmaNames.value.find(d => d.id === dt.dharma_name_id);
            if (dn && dt.obtained_date) {
                lines.push(`${dn.name}: ${dt.obtained_date.replace(/-/g, '/')}${dt.remarks ? ' (' + dt.remarks + ')' : ''}`);
            }
        });
        if (lines.length > 0) {
            text += `\n法號表：\n${lines.join('\n')}\n`;
        }
    }

    navigator.clipboard.writeText(text).then(() => {
        showToast('✓ 已複製，可貼至 LINE');
    }).catch(() => {
        showToast('複製失敗', 'error');
    });
    openMenuId.value = null;
};

const downloadOnly = (item, format = 'txt') => {
    const master = masters.value.find(m => m.id === item.master_id);
    const masterName = master ? master.name : (currentFolder.value?.name || '未知仙師');
    
    let text = `[${formatDate(item.record_date)}] ${item.name}\n`;
    if (masterName) text += `仙師：${masterName}\n`;
    if (item.purpose) text += `用意：${item.purpose}\n`;
    if (item.acquisition_method) text += `求寶方式：${item.acquisition_method}\n`;
    if (item.remarks) text += `備註：${item.remarks}\n`;
    
    const dntLines = [];
    let sortedDNT = [];
    if (item.dharma_name_treasures && item.dharma_name_treasures.length > 0) {
        sortedDNT = [...item.dharma_name_treasures].sort((a, b) => {
            const indexA = dharmaNames.value.findIndex(dn => dn.id === a.dharma_name_id);
            const indexB = dharmaNames.value.findIndex(dn => dn.id === b.dharma_name_id);
            return indexA - indexB;
        });

        sortedDNT.forEach(dt => {
            const dn = dharmaNames.value.find(d => d.id === dt.dharma_name_id);
            if (dn && dt.obtained_date) {
                dntLines.push(`${dn.name}: ${dt.obtained_date.replace(/-/g, '/')}${dt.remarks ? ' (' + dt.remarks + ')' : ''}`);
            }
        });
    }

    if (format === 'excel') {
        const rows = [
            ["仙師", masterName],
            ["法寶名稱", item.name],
            ["登記日期", formatDate(item.record_date)],
            ["法寶用意", item.purpose || ""],
            ["求寶方式", item.acquisition_method || ""],
            ["備註", item.remarks || ""],
            [],
            ["法號", "取得日期", "法號備註"]
        ];
        
        if (sortedDNT.length > 0) {
            sortedDNT.forEach(dt => {
                const dn = dharmaNames.value.find(d => d.id === dt.dharma_name_id);
                if (dn && dt.obtained_date) {
                    rows.push([dn.name, dt.obtained_date.replace(/-/g, '/'), dt.remarks || ""]);
                }
            });
        }

        if (!window.XLSX) {
            showToast('Excel 組件尚未就緒，請稍候', 'error');
            return;
        }

        const worksheet = window.XLSX.utils.aoa_to_sheet(rows);
        const workbook = window.XLSX.utils.book_new();
        window.XLSX.utils.book_append_sheet(workbook, worksheet, "求寶名單");
        
        // Use standard XLSX.writeFile to handle binary generation and download automatically
        // This is the most reliable way to avoid format mismatch warnings
        window.XLSX.writeFile(workbook, `求寶名單_${item.name}.xlsx`);
    }
    openMenuId.value = null;
};

const saveAsFile = (blob, filename) => {
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.style.display = 'none';
    a.href = url;
    a.download = filename;
    document.body.appendChild(a);
    a.click();
    setTimeout(() => {
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
    }, 100);
};

const downloadList = (format = 'txt') => {
    if (!currentFolder.value) return;
    const list = filteredTreasures.value;
    const folderName = currentFolder.value.name || '法寶清單';

    if (format === 'excel') {
        const rows = [["日期", "名稱", "法寶用意", "求寶方式", "備註"]];
        list.forEach(item => {
            rows.push([formatDate(item.record_date), item.name, item.purpose || "", item.acquisition_method || "", item.remarks || ""]);
        });
        
        if (!window.XLSX) {
            showToast('Excel 組件尚未就緒，請稍候', 'error');
            return;
        }

        const worksheet = window.XLSX.utils.aoa_to_sheet(rows);
        const workbook = window.XLSX.utils.book_new();
        window.XLSX.utils.book_append_sheet(workbook, worksheet, "法寶清單");
        
        window.XLSX.writeFile(workbook, `${folderName}_清單.xlsx`);
    }
    showExportMenu.value = false;
};

const formatDate = (d) => d ? new Date(d).toLocaleDateString('zh-TW') : '-';

onMounted(() => {
    window.addEventListener('click', (e) => {
        const isOutside = !e.target.closest('.relative');
        if (isOutside) {
            if (openMenuId.value) openMenuId.value = null;
            if (showAddMenu.value) showAddMenu.value = false;
            // Collapse details but STAY in focused mode
            if (expandedIds.value.size > 0) expandedIds.value.clear();
        }
    });
});
</script>

<style scoped>
.animate-toast-in { animation: toastScaleIn 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
@keyframes toastScaleIn {
    from { opacity: 0; transform: translate(-50%, -20px) scale(0.9); }
    to { opacity: 1; transform: translate(-50%, 0) scale(1); }
}
.animate-fade-in { animation: fadeIn 0.3s ease-out; }
.animate-slide-up { animation: slideUp 0.15s ease-out; }
@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
@keyframes slideUp { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

/* Custom Scrollbar for a cleaner mobile look */
.custom-scrollbar::-webkit-scrollbar {
    width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #e2e8f0;
    border-radius: 10px;
}
</style>
