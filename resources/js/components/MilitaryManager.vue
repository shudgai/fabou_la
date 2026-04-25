<template>
    <div class="bg-white h-[100vh] flex flex-col relative overflow-hidden">
        <!-- Global Dual Header System -->
        <!-- Header 1: Module Level -->
        <div v-if="currentFolder" class="border-b border-slate-300 flex items-center bg-white sticky top-0 z-[110] w-full" style="padding: 8px 15px; min-height: 52px;">
            <button @click="handleBack" class="text-slate-400 p-2 -ml-2 mr-0.5 active:scale-90 transition-transform shrink-0">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" /></svg>
            </button>
            <div class="flex-1 flex flex-col justify-center min-w-0 py-1">
                <div class="app-title text-[25px] font-black leading-tight font-outfit tracking-widest break-words text-slate-900">
                    軍隊記錄專區
                </div>
            </div>
            <div class="absolute right-4 top-1/2 -translate-y-1/2">
                <button @click="sortDesc = !sortDesc" class="px-2 py-1 text-[12px] text-indigo-500 bg-indigo-50 border border-indigo-100 rounded-lg active:scale-95 transition-all font-black">
                    {{ sortDesc ? '新→舊' : '舊→新' }}
                </button>
            </div>
        </div>

        <!-- Header 2: Folder/Action Level -->
        <div v-if="currentFolder" class="border-b border-slate-50 flex items-center bg-white z-[105] w-full px-3 py-2">
            <div class="flex items-center flex-1 min-w-0">
                <button @click="handleBack" class="p-2 -ml-2 text-slate-400 active:scale-90 transition-all mr-1">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" /></svg>
                </button>
                <h2 class="text-[22px] font-black text-slate-900 truncate tracking-tight">
                    {{ currentFolder.name }}
                </h2>
            </div>
            <div class="ml-2 flex items-center">
                <button @click="toggleFullTotal" class="px-4 py-2 bg-slate-100 text-slate-600 rounded-xl text-[14px] font-black transition-all active:scale-95 shadow-sm whitespace-nowrap">
                    總數
                </button>
            </div>
        </div>

        <!-- FOLDER SELECTION VIEW -->
        <div v-if="!currentFolder" class="flex-1 overflow-y-auto bg-slate-50/50 p-4 animate-fade-in">
            <div class="grid grid-cols-2 gap-[10px] p-4 place-items-center">
                <div v-for="folder in filteredFolders" :key="folder.id" 
                    @click="currentFolder = folder"
                    class="flex flex-col items-center justify-center active:scale-95 transition-all group relative cursor-pointer">
                    <div class="relative w-[148px] h-[148px]">
                        <svg class="w-full h-full transition-transform group-hover:scale-105 drop-shadow-lg" viewBox="0 0 64 64" fill="none">
                            <defs>
                                <linearGradient :id="'fGrad' + folder.id" x1="0%" y1="0%" x2="100%" y2="100%">
                                    <template v-if="folder.name === '耀紫軍'">
                                        <stop offset="0%" style="stop-color:rgb(168, 85, 247);stop-opacity:1" />
                                        <stop offset="50%" style="stop-color:rgb(147, 51, 234);stop-opacity:1" />
                                        <stop offset="100%" style="stop-color:rgb(126, 34, 206);stop-opacity:1" />
                                    </template>
                                    <template v-else-if="['虎甲軍', '黑曜軍', '虎賁軍'].includes(folder.name)">
                                        <stop offset="0%" style="stop-color:rgb(55, 65, 81);stop-opacity:1" />
                                        <stop offset="50%" style="stop-color:rgb(17, 24, 39);stop-opacity:1" />
                                        <stop offset="100%" style="stop-color:rgb(0, 0, 0);stop-opacity:1" />
                                    </template>
                                    <template v-else>
                                        <stop offset="0%" style="stop-color:rgb(107, 114, 128);stop-opacity:1" />
                                        <stop offset="100%" style="stop-color:rgb(55, 65, 81);stop-opacity:1" />
                                    </template>
                                </linearGradient>
                            </defs>
                            <path d="M4 14C4 11.7909 5.79086 10 8 10H24.5L30 16H56C58.2091 16 60 17.7909 60 20V50C60 52.2091 58.2091 54 56 54H8C5.79086 54 4 52.2091 4 50V14Z" :fill="'url(#fGrad' + folder.id + ')'" opacity="0.8"/>
                            <path d="M4 22C4 19.7909 5.79086 18 8 18H56C58.2091 18 60 19.7909 60 22V50C60 52.2091 58.2091 54 56 54H8C5.79086 54 4 52.2091 4 50V22Z" :fill="'url(#fGrad' + folder.id + ')'" stroke="rgba(255,255,255,0.4)" stroke-width="0.5"/>
                        </svg>
                        <!-- Label Inside -->
                        <div class="absolute inset-0 flex items-center justify-center pt-5 px-3">
                            <span class="font-black drop-shadow-[0_2px_4px_rgba(0,0,0,0.6)] tracking-tight leading-tight text-center transition-all text-[22px] text-white" style="font-weight: 900 !important;">
                                {{ folder.name }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
        
        <!-- SEARCH & LEDGER VIEW -->
        <template v-else>
            <!-- Search Component (Enhanced with persistent X) -->
            <div v-if="showSearch" class="px-[10px] mt-2 animate-fade-in relative flex items-center group">
                <div class="absolute left-[22px] text-slate-400 pointer-events-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input v-model="searchQuery" type="text" placeholder="搜尋項目..." 
                    class="w-full h-[36px] bg-white border border-slate-200 rounded-xl pl-9 pr-10 text-[15px] focus:ring-1 focus:ring-slate-300 outline-none transition-all shadow-sm">
                
                <!-- Persistent X: Clear or Close -->
                <button @click="searchQuery ? searchQuery = '' : showSearch = false" 
                    class="absolute right-[18px] w-8 h-8 flex items-center justify-center text-slate-500 active:text-slate-900 transition-all p-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3.5" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="flex-1 overflow-x-auto overflow-y-auto pl-[10px] bg-white flex flex-col" :class="[focusedId ? 'h-full pb-0' : 'pb-24']">
                <div v-if="loading" class="text-center py-10 text-xs text-slate-400">載入中...</div>
                <div v-else-if="filteredItems.length === 0" class="text-center py-20 text-slate-400 font-light">
                    目前尚無{{ currentFolder.name }}載錄資料。
                </div>
                <div v-else class="flex flex-col flex-1">
                    <!-- Table Header -->
                    <div class="flex flex-col flex-1">
                        <!-- List Body -->
                        <div v-for="(item, index) in sortedItems" :key="item.id" 
                            v-show="focusedId === null || focusedId === item.id"
                            @click="toggleExpand(item.id)"
                            :class="[
                                'py-[16px] pl-2 pr-[10px] border-b border-slate-900 last:border-b-0 relative group transition-all cursor-pointer z-0',
                                openMenuId === item.id ? 'z-[50]' : 'z-0',
                                item.groupParity === 1 ? 'bg-slate-50/50' : 'bg-white',
                                { 'border-t border-slate-900': index === 0 && !focusedId, 'border-b-0': focusedId === item.id }
                            ]"
                        >
                            <!-- List Item Display (Collapsed - Grudge Style) -->
                            <div v-if="focusedId !== item.id" class="mt-0 flex flex-col pointer-events-none">
                                <!-- Row 1: Date only -->
                                <div class="flex items-center mb-0.5">
                                    <div class="flex items-baseline space-x-2">
                                        <div class="app-title !font-black !text-black font-outfit" style="font-size: 18px !important; font-weight: 900 !important; color: #0d0d0d !important;">日期</div>
                                        <div class="app-title ml-0.5 !font-black !text-black font-outfit" style="font-size: 18px !important; font-weight: 900 !important; color: #0d0d0d !important;">{{ formatDate(item.know_date) }}</div>
                                    </div>
                                </div>

                                <!-- Row 2: Name & Subtotal -->
                                <div class="flex items-center justify-between">
                                    <div class="flex-1 flex items-center justify-between">
                                        <!-- Dharma Name -->
                                        <div class="app-body font-bold leading-tight truncate pr-2">
                                            {{ item.user_name || '-' }}
                                            <span v-if="item.user_remarks" class="app-body ml-1.5">({{ item.user_remarks }})</span>
                                        </div>
                                        <!-- Subtotal -->
                                        <div class="app-body flex items-center space-x-2">
                                            <span class="app-title">數量:</span>
                                            <span class="app-body">
                                                <template v-if="['虎甲軍','虎賁軍'].includes(item.army_type)">
                                                    {{ (Number(item.quantity) || 0).toLocaleString() }}
                                                </template>
                                                <template v-else>
                                                    {{ formatArmyTotal(item.quantity) }}
                                                </template>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Menu Button Layer -->
                            <div v-if="focusedId !== item.id" class="absolute right-0 top-0.5 z-20">
                                <button @click.stop="toggleMenu(item.id)" class="p-2 -mr-1 text-slate-400 active:text-indigo-600 transition-colors">
                                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM18 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                                </button>
                                <div v-if="openMenuId === item.id" @click.stop class="absolute right-0 top-full mt-1 w-auto min-w-[140px] bg-white opacity-100 rounded-2xl shadow-2xl border border-slate-100 z-[110] overflow-hidden animate-slide-up py-1">
                                    <button @click.stop="toggleExpand(item.id); openMenuId = null" class="w-full px-4 py-3 text-left text-[17px] font-black text-slate-900 hover:bg-slate-50 border-b border-slate-50 whitespace-nowrap">展開詳情</button>
                                    <button @click.stop="editItem(item); openMenuId = null" class="w-full px-4 py-3 text-left text-[17px] font-black text-slate-900 hover:bg-slate-50 border-b border-slate-50 whitespace-nowrap">修改內容</button>
                                    <button @click.stop="copySingleRecord(item); openMenuId = null" class="w-full px-4 py-3 text-left text-[17px] font-black text-slate-900 hover:bg-slate-50 border-b border-slate-50 whitespace-nowrap">複製 LINE</button>
                                    <button @click.stop="downloadSingleRecord(item); openMenuId = null" class="w-full px-4 py-3 text-left text-[17px] font-black text-slate-900 hover:bg-slate-50 border-b border-slate-50 whitespace-nowrap">下載檔案</button>
                                    <button @click.stop="deleteItem(item.id)" class="w-full px-4 py-3 text-left text-[17px] font-black text-red-600 hover:bg-red-50 whitespace-nowrap">刪除</button>
                                </div>
                            </div>

                            <!-- Expanded Detail (Minimalist Style - Wide) -->
                            <div v-if="focusedId === item.id" class="animate-fade-in py-3 bg-white space-y-4 relative px-1.5">
                                <!-- Menu Button for Expanded State -->
                                <div class="absolute right-0 top-0 z-[101]">
                                    <button @click.stop="toggleMenu(item.id)" class="p-1 text-slate-400 hover:text-indigo-600 active:scale-95 transition-all">
                                        <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"><path d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM18 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                                    </button>
                                    <div v-if="openMenuId === item.id" @click.stop class="absolute right-0 top-full mt-1 w-auto min-w-[140px] bg-white opacity-100 rounded-2xl shadow-2xl border border-slate-100 z-[110] overflow-hidden animate-slide-up py-1">
                                        <button @click.stop="toggleExpand(item.id); openMenuId = null" class="w-full px-4 py-3 text-left text-[17px] font-black text-slate-900 hover:bg-slate-50 border-b border-slate-50 whitespace-nowrap">收合詳情</button>
                                        <button @click.stop="editItem(item); openMenuId = null" class="w-full px-4 py-3 text-left text-[17px] font-black text-slate-900 hover:bg-slate-50 border-b border-slate-50 whitespace-nowrap">修改內容</button>
                                        <button @click.stop="copySingleRecord(item); openMenuId = null" class="w-full px-4 py-3 text-left text-[17px] font-black text-slate-900 hover:bg-slate-50 border-b border-slate-50 whitespace-nowrap">複製 LINE</button>
                                        <button @click.stop="downloadSingleRecord(item); openMenuId = null" class="w-full px-4 py-3 text-left text-[17px] font-black text-slate-900 hover:bg-slate-50 border-b border-slate-50 whitespace-nowrap">下載檔案</button>
                                        <button @click.stop="deleteItem(item.id)" class="w-full px-4 py-3 text-left text-[17px] font-black text-red-600 hover:bg-red-50 whitespace-nowrap">刪除</button>
                                    </div>
                                </div>

                                <!-- Date Row -->
                                <div class="space-y-1">
                                    <div class="flex items-center space-x-1 ml-1">
                                        <button @click.stop="toggleExpand(item.id)" class="p-1 -ml-1 text-slate-300">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" /></svg>
                                        </button>
                                        <label class="app-title">日期</label>
                                    </div>
                                    <div class="w-full px-3 flex items-center app-title">
                                        {{ formatDate(item.know_date) }}
                                    </div>
                                </div>

                                <!-- Name Row (Combined) -->
                                <div class="space-y-1">
                                    <label class="app-title ml-1">法號 (親友/信眾)</label>
                                    <div class="w-full px-3 flex items-center app-body leading-tight">
                                        {{ item.user_name }}
                                        <span v-if="item.user_remarks" class="app-body ml-1.5">({{ item.user_remarks }})</span>
                                    </div>
                                </div>

                                <!-- Quantities Row (Grid) - only for armies with sub-fields -->
                                <div v-if="['黑曜軍','耀紫軍'].includes(item.army_type)" class="grid grid-cols-2 gap-3">
                                     <template v-if="item.army_type === '黑曜軍'">
                                         <div class="space-y-1">
                                             <label class="app-title ml-1">閻尊數量</label>
                                             <div class="w-full px-3 flex items-center app-body">{{ (item.yan_zun || 0).toLocaleString() }}</div>
                                         </div>
                                         <div class="space-y-1">
                                             <label class="app-title ml-1">閻闇數量</label>
                                             <div class="w-full px-3 flex items-center app-body">{{ (item.yan_an || 0).toLocaleString() }}</div>
                                         </div>
                                     </template>
                                     <template v-if="item.army_type === '耀紫軍'">
                                         <div class="space-y-1">
                                             <label class="app-title ml-1">龍勝數量</label>
                                             <div class="w-full px-3 flex items-center app-body">{{ (item.long_sheng || 0).toLocaleString() }}</div>
                                         </div>
                                         <div class="space-y-1">
                                             <label class="app-title ml-1">龍戰數量</label>
                                             <div class="w-full px-3 flex items-center app-body">{{ (item.long_zhan || 0).toLocaleString() }}</div>
                                         </div>
                                     </template>
                                 </div>

                                <!-- Subtotal Special Row -->
                                <div class="w-full px-4 flex items-center justify-end py-2 border-t border-slate-50 mt-1 space-x-2">
                                    <span class="app-title">數量</span>
                                    <span class="app-body">
                                        <template v-if="['虎甲軍','虎賁軍'].includes(item.army_type)">
                                            {{ (Number(item.quantity) || 0).toLocaleString() }}
                                        </template>
                                        <template v-else>
                                            {{ formatArmyTotal(item.quantity) }}
                                        </template>
                                    </span>
                                </div>

                                <!-- Remarks Row -->
                                <div v-if="item.remarks_text" class="space-y-1">
                                    <label class="app-title ml-1">備註文字</label>
                                    <div class="w-full px-3 py-1 app-body leading-relaxed whitespace-pre-wrap">
                                        {{ item.remarks_text }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        <!-- Total Overlay (Enhanced with Breakdown & Close Button) -->
        <div v-if="showFullTotal" class="fixed inset-0 z-[200] flex items-center justify-center px-6 animate-fade-in pointer-events-none">
            <div class="bg-white text-slate-900 px-8 py-6 rounded-[24px] shadow-2xl flex flex-col pointer-events-auto border border-slate-100 space-y-4 relative w-auto min-w-[240px] max-w-[300px]">
                <!-- Close Button -->
                <button @click="showFullTotal = false" class="absolute right-6 top-6 text-slate-300 hover:text-slate-600 active:scale-95 transition-all p-1">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>

                <div class="flex flex-col items-center justify-center space-y-1">
                    <span class="app-body text-slate-400" style="font-weight: 400 !important;">{{ currentFolder?.name }}總數</span>
                    <span class="app-body text-slate-900" style="font-weight: 400 !important;">{{ formatArmyTotal(currentFolderTotal) }}</span>
                </div>
                
                <!-- Breakdown Grid (Specific to current army) -->
                <div v-if="currentFolder?.name === '黑曜軍' || currentFolder?.name === '耀紫軍'" class="pt-4 border-t border-slate-50 grid grid-cols-2 gap-4">
                    <template v-if="currentFolder?.name === '黑曜軍'">
                        <div class="flex flex-col items-center">
                            <span class="app-body text-slate-400" style="font-weight: 400 !important;">閻尊總數</span>
                            <span class="app-body text-slate-900 mt-1" style="font-weight: 400 !important;">{{ formatArmyTotal(breakdownTotals.yan_zun) }}</span>
                        </div>
                        <div class="flex flex-col items-center">
                            <span class="app-body text-slate-400" style="font-weight: 400 !important;">閻闇總數</span>
                            <span class="app-body text-slate-900 mt-1" style="font-weight: 400 !important;">{{ formatArmyTotal(breakdownTotals.yan_an) }}</span>
                        </div>
                    </template>
                    <template v-if="currentFolder?.name === '耀紫軍'">
                        <div class="flex flex-col items-center">
                            <span class="app-body text-slate-400" style="font-weight: 400 !important;">龍勝總數</span>
                            <span class="app-body text-slate-900 mt-1" style="font-weight: 400 !important;">{{ formatArmyTotal(breakdownTotals.long_sheng) }}</span>
                        </div>
                        <div class="flex flex-col items-center">
                            <span class="app-body text-slate-400" style="font-weight: 400 !important;">龍戰總數</span>
                            <span class="app-body text-slate-900 mt-1" style="font-weight: 400 !important;">{{ formatArmyTotal(breakdownTotals.long_zhan) }}</span>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- FAB Bottom Navigation -->
        <mobile-navbar 
            :can-back="true"
            :show-action="true"
            :action-disabled="!currentFolder"
            :action-active="showAddMenu"
            :search-active="showSearch"
            :can-search="!!currentFolder"
            :can-more="!!currentFolder"
            @back="handleBack"
            @home="$emit('goHome')"
            @action="showAddMenu = !showAddMenu"
            @search="showSearch = !showSearch"
            @more="exportToExcel"
        />

        <!-- MODALS -->
        <add-action-menu :show="showAddMenu" @close="showAddMenu = false" :actions="addActions" />
        <military-add-form :key="editingId || 'new'" :show="addMode" :initialData="form" :editingId="editingId" :users="users" :armyType="currentFolder ? currentFolder.name : ''" @save="saveItem" @cancel="addMode = false; editingId = null" />
        <military-batch-add :show="batchMode" :armyType="currentFolder ? currentFolder.name : ''" @save="batchMode = false; loadData()" @cancel="batchMode = false" />
    </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import axios from 'axios';
import MilitaryAddForm from './MilitaryAddForm.vue';
import MilitaryBatchAdd from './MilitaryBatchAdd.vue';
import AddActionMenu from './AddActionMenu.vue';
import MobileNavbar from './MobileNavbar.vue';

const props = defineProps(['user']);
const emit = defineEmits(['goHome']);

const folders_list = [
    { id: 'armor', name: '虎甲軍', color: 'bg-indigo-600' },
    { id: 'brave', name: '虎賁軍', color: 'bg-blue-600' },
    { id: 'obsidian', name: '黑曜軍', color: 'bg-slate-800' },
    { id: 'purple', name: '耀紫軍', color: 'bg-purple-600' }
];

const filteredFolders = computed(() => {
    const allowed = props.user?.permissions?.allowed_armies || [];
    return folders_list.filter(f => allowed.includes(f.name));
});

const currentFolder = ref(null);
const addMode = ref(false);
const batchMode = ref(false);
const showAddMenu = ref(false);
const showSearch = ref(false);
const searchQuery = ref('');
const openMenuId = ref(null);
const items = ref([]);
const users = ref([]);
const loading = ref(true);
const editingId = ref(null);
const focusedId = ref(null);
const deleteConfirmId = ref(null);
const sortDesc = ref(true);
const openStatusId = ref(null);
const showFullTotal = ref(false);
let fullTotalTimer = null;

const formatArmyTotal = (num) => {
    num = Number(num) || 0;
    if (num < 1000000) return num.toLocaleString();
    const troops = Math.floor(num / 1000000);
    const remaining = num % 1000000;
    if (remaining === 0) return `${troops}隊`;
    const wan = Math.floor(remaining / 10000);
    const rest = remaining % 10000;
    let res = `${troops}隊`;
    if (wan > 0) res += `${wan}萬`;
    if (rest > 0) res += `${rest}位`;
    else if (wan === 0) res += `0位`;
    return res;
};

const toggleFullTotal = () => {
    showFullTotal.value = true;
    if (fullTotalTimer) clearTimeout(fullTotalTimer);
    fullTotalTimer = setTimeout(() => {
        showFullTotal.value = false;
    }, 10000);
};

const filteredItems = computed(() => {
    if (!currentFolder.value) return [];
    
    const folderName = currentFolder.value.name;
    // Create a regex to match the folder name, ignoring spaces and non-printable characters
    const cleanFolderName = folderName.replace(/[^\u4e00-\u9fa5]/g, '');
    const regex = new RegExp(cleanFolderName.split('').join('.*'), 'i');

    let filtered = items.value.filter(i => {
        if (!i.army_type) return false;
        const recordType = String(i.army_type).trim();
        const target = currentFolder.value?.name;
        
        return recordType === target || recordType.includes(target);
    });
    
    if (searchQuery.value) {
        const q = searchQuery.value.trim().toLowerCase();
        filtered = filtered.filter(i => 
            i.user_name?.toLowerCase().includes(q) || 
            i.user_remarks?.toLowerCase().includes(q) ||
            i.remarks_text?.toLowerCase().includes(q)
        );
    }
    return filtered;
});

const sortedItems = computed(() => {
    let result = [...filteredItems.value];
    
    result.sort((a, b) => {
        // Dynamic Pinning
        if (focusedId.value === a.id) return -1;
        if (focusedId.value === b.id) return 1;

        const dateA = new Date(a.know_date);
        const dateB = new Date(b.know_date);
        if (dateA - dateB !== 0) {
            return sortDesc.value ? dateB - dateA : dateA - dateB;
        }
        return sortDesc.value ? b.id - a.id : a.id - b.id;
    });

    let groupIndex = 0;
    return result.map((item, idx) => {
        if (idx > 0) {
            const prev = result[idx - 1];
            if (item.user_name !== prev.user_name || item.know_date !== prev.know_date) {
                groupIndex++;
            }
        }
        return { ...item, groupParity: groupIndex % 2 };
    });
});

const totalQuantity = computed(() => items.value.reduce((sum, i) => sum + (Number(i.quantity) || 0), 0));
const currentFolderTotal = computed(() => filteredItems.value.reduce((sum, i) => sum + (Number(i.quantity) || 0), 0));

const breakdownTotals = computed(() => {
    return filteredItems.value.reduce((acc, i) => {
        acc.yan_zun += (Number(i.yan_zun) || 0);
        acc.yan_an += (Number(i.yan_an) || 0);
        acc.long_sheng += (Number(i.long_sheng) || 0);
        acc.long_zhan += (Number(i.long_zhan) || 0);
        return acc;
    }, { yan_zun: 0, yan_an: 0, long_sheng: 0, long_zhan: 0 });
});

const toggleMenu = (id) => { openMenuId.value = openMenuId.value === id ? null : id; };
const toggleStatusMenu = (id) => { openStatusId.value = openStatusId.value === id ? null : id; };
const toggleExpand = (id) => { focusedId.value = focusedId.value === id ? null : id; };

const addActions = computed(() => [
    { 
        label: '逐筆新增', 
        description: `輸入單筆${currentFolder.value?.name}紀錄`,
        icon: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>',
        handler: () => { 
            editingId.value = null;
            form.value = { 
                user_name: '', user_remarks: '', destination: '未處理', quantity: (currentFolder.value?.id === 'armor' || currentFolder.value?.id === 'brave') ? 1 : 0, 
                yan_zun: 0, yan_an: 0, long_sheng: 0, long_zhan: 0,
                know_date: new Date().toISOString().split('T')[0], process_date: '', remarks_text: '',
                army_type: currentFolder.value?.name
            };
            addMode.value = true; 
        } 
    },
    { 
        label: '多筆新增', 
        description: `貼上文字清單批次匯入`,
        icon: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>',
        handler: () => { 
            batchMode.value = true;
        } 
    },
    { 
        label: '匯出 Excel', 
        description: `下載${currentFolder.value?.name}報表檔案`,
        icon: '<svg fill="currentColor" viewBox="0 0 24 24"><path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/></svg>',
        handler: () => { 
            exportToExcel();
        } 
    },
    { 
        label: '複製貼 LINE', 
        description: `將清單轉為文字複製到 LINE`,
        icon: '<svg fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-1-13h2v6h-2zm0 8h2v2h-2z"/></svg>',
        handler: () => { 
            copyToLine();
        } 
    }
]);

const getVisualWidth = (str) => {
    let width = 0;
    for (let i = 0; i < str.length; i++) {
        const charCode = str.charCodeAt(i);
        if (charCode >= 0 && charCode <= 128) width += 1; // Half-width
        else width += 2; // Full-width (CJK)
    }
    return width;
};

// Precise padding for LINE (text use full-width space, numbers use figure space)
const padToWidthPrecise = (str, width, isNumeric = false) => {
    const currentWidth = getVisualWidth(str);
    if (currentWidth >= width) return str;
    
    if (isNumeric) {
        // USE FIGURE SPACE (\u2007) FOR NUMBERS - it has same width as digits
        const padding = '\u2007'.repeat(width - currentWidth);
        return padding + str;
    } else {
        // USE FULL-WIDTH SPACE (\u3000) FOR CJK TEXT - 1 char = 2 units
        let remaining = width - currentWidth;
        let padded = str;
        while (remaining >= 2) {
            padded += '\u3000';
            remaining -= 2;
        }
        while (remaining >= 1) {
            padded += ' ';
            remaining -= 1;
        }
        return padded;
    }
};

const copyToLine = () => {
    if (!sortedItems.value.length) return alert('目前沒有資料可供複製');
    
    let text = `【${currentFolder.value?.name || '軍隊'} 載錄報表】\n\n`;
    
    // Header - Precise alignment using \u3000 and \u2007
    const hDate = padToWidthPrecise('日期', 10);
    const hName = padToWidthPrecise('法號', 18);
    const hQty  = '數量';
    text += `${hDate} | ${hName} | ${hQty}\n`;
    text += `----------------------------------------\n`;
    
    let total = 0;
    sortedItems.value.forEach(item => {
        const dateStr = formatDate(item.know_date);
        const fullName = item.user_name + (item.user_remarks || '');
        const qtyNum = Number(item.quantity) || 0;
        total += qtyNum;
        
        // Body - Precise alignment
        const cDate = padToWidthPrecise(dateStr, 10);
        const cName = padToWidthPrecise(fullName, 18);
        const cQty  = padToWidthPrecise(qtyNum.toString(), 4, true); // Pad numeric part
        
        text += `${cDate} | ${cName} | ${cQty}\n`;
    });
    
    text += `----------------------------------------\n`;
    const footerLabel = padToWidthPrecise('總結：', 30);
    const footerQty   = padToWidthPrecise(total.toString(), 4, true);
    text += `${footerLabel} | ${footerQty}\n`;
    
    navigator.clipboard.writeText(text).then(() => {
        alert('已複製到剪貼簿，快去 LINE 貼上吧！');
    }).catch(err => {
        alert('複製失敗，請手動全選複製。');
    });
};

const exportToExcel = () => {
    if (!filteredItems.value.length) return alert('目前沒有資料可供匯出');
    
    // Load XLSX via script if not present
    if (typeof XLSX === 'undefined') {
        const script = document.createElement('script');
        script.src = "https://cdn.sheetjs.com/xlsx-latest/package/dist/xlsx.full.min.js";
        script.onload = () => performExcelExport();
        document.head.appendChild(script);
    } else {
        performExcelExport();
    }
};

const performExcelExport = () => {
    let total = 0;
    const data = sortedItems.value.map(item => {
        const qty = Number(item.quantity) || 0;
        total += qty;
        
        const row = {};
        row['日期'] = formatDate(item.know_date);
        row['法號'] = item.user_name + (item.user_remarks || '');
        
        if (item.army_type === '黑曜軍') {
            row['閻尊量'] = item.yan_zun || 0;
            row['閻闇量'] = item.yan_an || 0;
        } else if (item.army_type === '耀紫軍') {
            row['龍勝量'] = item.long_sheng || 0;
            row['龍戰量'] = item.long_zhan || 0;
        }
        
        row['總數量'] = qty;
        row['備註'] = item.remarks_text || '';
        return row;
    });

    // Add Total Row at the end
    data.push({
        '日期': '',
        '法號': '總量',
        '總數量': total,
        '備註': ''
    });

    const worksheet = XLSX.utils.json_to_sheet(data);
    const workbook = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(workbook, worksheet, currentFolder.value.name);
    
    const dateStr = new Date().toISOString().split('T')[0].replace(/-/g, '');
    const fileName = `${currentFolder.value.name}_載錄報表_${dateStr}.xlsx`;
    
    XLSX.writeFile(workbook, fileName);
};

const triggerSimpleDownload = (text, filename) => {
    const blob = new Blob(['\uFEFF' + text], { type: 'text/plain;charset=utf-8' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = filename;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    window.URL.revokeObjectURL(url);
};

const formatMilitaryRecordForFile = (item) => {
    let res = `【軍隊記錄 - ${item.army_type}】\r\n`;
    res += `日期：${formatDate(item.know_date)}\r\n`;
    res += `法號：${item.user_name}${item.user_remarks ? '(' + item.user_remarks + ')' : ''}\r\n`;
    
    if (['黑曜軍', '耀紫軍'].includes(item.army_type)) {
        if (item.army_type === '黑曜軍') {
            res += `閻尊：${(item.yan_zun || 0).toLocaleString()}\r\n`;
            res += `閻闇：${(item.yan_an || 0).toLocaleString()}\r\n`;
        } else {
            res += `龍勝：${(item.long_sheng || 0).toLocaleString()}\r\n`;
            res += `龍戰：${(item.long_zhan || 0).toLocaleString()}\r\n`;
        }
    }
    
    res += `總數：${formatArmyTotal(item.quantity)}\r\n`;
    res += `備註：${item.remarks_text || '無'}`;
    return res;
};

const copySingleRecord = async (item) => {
    const text = formatMilitaryRecordForFile(item);
    try {
        await navigator.clipboard.writeText(text);
        alert('已複製單筆記錄');
    } catch (e) {
        alert('複製失敗');
    }
};

const downloadSingleRecord = (item) => {
    const text = formatMilitaryRecordForFile(item);
    triggerSimpleDownload(text, `${item.army_type}_${item.user_name}_${formatDate(item.know_date).replace(/\//g, '')}.txt`);
};

const form = ref({});

const loadData = async () => {
    loading.value = true;
    try {
        const ts = new Date().getTime();
        const [res, dres] = await Promise.all([ 
            axios.get(`/military-records?t=${ts}`), 
            axios.get(`/api/dharma-names-list?t=${ts}`) 
        ]);
        items.value = Array.isArray(res.data) ? res.data : (res.data.data || []);
        users.value = dres.data;
        console.log(`Military loaded: ${items.value.length} items`, items.value[0]);
    } catch (e) { 
        console.error('Load data failed:', e);
        items.value = []; 
    } finally { 
        loading.value = false; 
    }
};

const saveItem = async (formData) => {
    try {
        if (editingId.value) await axios.put(`/military-records/${editingId.value}`, formData);
        else await axios.post('/military-records', formData);
        
        // Reset state after success
        addMode.value = false; 
        editingId.value = null;
        searchQuery.value = ''; // Auto-clear search to show new data
        focusedId.value = null;
        
        await loadData();
    } catch (e) { 
        console.error('Save failed:', e);
        alert('儲存失敗，請檢查資料格式'); 
    }
};

const handleBack = () => {
    if (addMode.value) addMode.value = false;
    else if (batchMode.value) batchMode.value = false;
    else if (focusedId.value) focusedId.value = null;
    else if (currentFolder.value) currentFolder.value = null;
    else emit('goHome');
};

const editItem = (item) => {
    editingId.value = item.id;
    form.value = { ...item };
    addMode.value = true;
    openMenuId.value = null;
};

const deleteItem = async (id) => {
    if (!confirm('刪除？')) return;
    await axios.delete(`/military-records/${id}`);
    loadData();
};

const formatDate = (d) => {
    if (!d) return '-';
    // Use standard YYYY/MM/DD
    const date = new Date(d);
    const y = date.getFullYear();
    const m = String(date.getMonth() + 1).padStart(2, '0');
    const d_val = String(date.getDate()).padStart(2, '0');
    return `${y}/${m}/${d_val}`;
};

onMounted(loadData);
</script>

<style scoped>
.animate-fade-in { animation: fadeIn 0.3s ease-out; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }
.animate-slide-up { animation: slideUp 0.15s ease-out; }
@keyframes slideUp { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
</style>
