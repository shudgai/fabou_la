<template>
    <div class="h-[100dvh] bg-white flex flex-col other-manager-module relative overflow-hidden">
        <!-- Global SVG Definitions (Fix for disappearing gradients on desktop) -->
        <svg style="width:0; height:0; position:absolute;" aria-hidden="true" focusable="false">
            <defs>
                <linearGradient id="om-redGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" style="stop-color:rgb(255, 120, 120);stop-opacity:1" />
                    <stop offset="50%" style="stop-color:rgb(255, 50, 50);stop-opacity:1" />
                    <stop offset="100%" style="stop-color:rgb(220, 0, 0);stop-opacity:1" />
                </linearGradient>
                <linearGradient id="om-roundGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" style="stop-color:rgb(52, 211, 153);stop-opacity:1" />
                    <stop offset="50%" style="stop-color:rgb(16, 185, 129);stop-opacity:1" />
                    <stop offset="100%" style="stop-color:rgb(5, 150, 105);stop-opacity:1" />
                </linearGradient>
            </defs>
        </svg>
        <!-- Level 1: Folder Grid View -->
        <div v-if="!activeFolderId && !showLuckyDraw" class="h-full bg-white flex flex-col relative overflow-hidden">
            <div class="px-6 py-6 flex items-center justify-between border-b border-slate-50 sticky top-0 bg-white z-10 shrink-0 w-full md:max-w-xl md:mx-auto">
                <div class="flex items-center">
                    <button @click="$emit('goHome')" class="p-2 text-slate-400 mr-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" /></svg>
                    </button>
                    <h2 class="font-black tracking-tight cursor-pointer" @click="resetToRoot" style="color: #0f172a !important; font-size: 32px !important;">其他專區資料夾</h2>
                </div>
                <div class="w-10 h-10"></div> <!-- Placeholder to maintain title centering -->
            </div>

            <div class="flex-1 overflow-y-auto custom-scrollbar w-full md:max-w-xl md:mx-auto">
                <div class="grid grid-cols-1 gap-8 p-6 place-items-center pb-24">
                <button v-for="(folder, idx) in sortedFolders" :key="folder.id" 
                    @click="activeFolderId = folder.id"
                    class="flex flex-col items-center justify-center active:scale-95 transition-all group relative rounded-[40px] p-[5px]"
                    style="background-color: rgb(255, 250, 205);">
                    
                    <!-- Folder Delete Button (Top Right) -->
                    <button v-if="folder.name !== '抽籤紀錄' && !folder.name.includes('開文核定') && !folder.name.includes('隨機分組')"
                        @click.stop="deleteFolder(folder.id)"
                        class="absolute top-2 right-2 w-10 h-10 rounded-full bg-white/90 backdrop-blur shadow-sm text-rose-500 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity active:scale-90 z-20 border border-rose-100">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>

                    <div class="relative w-[280px] h-[280px]">
                        <svg class="w-full h-full transition-transform group-hover:scale-105" viewBox="0 0 64 64" fill="none">
                            <path d="M4 14C4 11.7909 5.79086 10 8 10H24.5L30 16H56C58.2091 16 60 17.7909 60 20V50C60 52.2091 58.2091 54 56 54H8C5.79086 54 4 52.2091 4 50V14Z" fill="url(#om-redGrad)" style="fill: #ef4444;" opacity="0.8"/>
                            <path d="M4 22C4 19.7909 5.79086 18 8 18H56C58.2091 18 60 19.7909 60 22V50C60 52.2091 58.2091 54 56 54H8C5.79086 54 4 52.2091 4 50V22Z" fill="url(#om-redGrad)" style="fill: #ef4444;" stroke="rgba(255,255,255,0.4)" stroke-width="0.5"/>
                        </svg>
                        <div class="absolute inset-0 flex flex-col items-center justify-center pt-6 px-3 pointer-events-none">
                            <div :class="[
                                'font-black drop-shadow-[0_2px_4px_rgba(0,0,0,0.6)] tracking-tight leading-tight text-center transition-all',
                                folder.name === '閻王仙師' ? 'text-black' : 'text-white'
                            ]" style="font-weight: 900 !important; font-size: 32px !important;">{{ folder.name }}</div>
                        </div>
                    </div>
                </button>

                <!-- Lucky Draw - Direct Sort (RED FOLDER STYLE) -->
                <button @click="luckyDrawInitialMode = false; showLuckyDraw = true"
                    class="flex flex-col items-center justify-center active:scale-95 transition-all group relative rounded-[40px] p-[5px]"
                    style="background-color: rgb(255, 250, 205);">
                    <div class="relative w-[280px] h-[280px]">
                        <svg class="w-full h-full transition-transform group-hover:scale-105" viewBox="0 0 64 64" fill="none">
                            <path d="M4 14C4 11.7909 5.79086 10 8 10H24.5L30 16H56C58.2091 16 60 17.7909 60 20V50C60 52.2091 58.2091 54 56 54H8C5.79086 54 4 52.2091 4 50V14Z" fill="url(#om-redGrad)" style="fill: #ef4444;" opacity="0.8"/>
                            <path d="M4 22C4 19.7909 5.79086 18 8 18H56C58.2091 18 60 19.7909 60 22V50C60 52.2091 58.2091 54 56 54H8C5.79086 54 4 52.2091 4 50V22Z" fill="url(#om-redGrad)" style="fill: #ef4444;" stroke="rgba(255,255,255,0.4)" stroke-width="0.5"/>
                        </svg>
                        <!-- Label Inside -->
                        <div class="absolute inset-0 flex items-center justify-center pt-5 px-3">
                            <span class="text-white font-black drop-shadow-[0_2px_4px_rgba(0,0,0,0.6)] tracking-tight leading-tight text-center transition-all" style="font-weight: 900 !important; font-size: 32px !important;">
                                抽順序
                            </span>
                        </div>
                    </div>
                </button>

                <!-- Lucky Draw - Round Lottery (EMERALD FOLDER STYLE) -->
                <button @click="luckyDrawInitialMode = true; showLuckyDraw = true"
                    class="flex flex-col items-center justify-center active:scale-95 transition-all group relative rounded-[40px] p-[5px]"
                    style="background-color: rgb(255, 250, 205);">
                    <div class="relative w-[280px] h-[280px]">
                        <svg class="w-full h-full transition-transform group-hover:scale-105" viewBox="0 0 64 64" fill="none">
                            <path d="M4 14C4 11.7909 5.79086 10 8 10H24.5L30 16H56C58.2091 16 60 17.7909 60 20V50C60 52.2091 58.2091 54 56 54H8C5.79086 54 4 52.2091 4 50V14Z" fill="url(#om-roundGrad)" style="fill: #10b981;" opacity="0.8"/>
                            <path d="M4 22C4 19.7909 5.79086 18 8 18H56C58.2091 18 60 19.7909 60 22V50C60 52.2091 58.2091 54 56 54H8C5.79086 54 4 52.2091 4 50V22Z" fill="url(#om-roundGrad)" style="fill: #10b981;" stroke="rgba(255,255,255,0.4)" stroke-width="0.5"/>
                        </svg>
                        <!-- Label Inside -->
                        <div class="absolute inset-0 flex items-center justify-center pt-5 px-3">
                            <span class="text-white font-black drop-shadow-[0_2px_4px_rgba(0,0,0,0.6)] tracking-tight leading-tight text-center transition-all" style="font-weight: 900 !important; font-size: 32px !important;">
                                回合抽籤
                            </span>
                        </div>
                    </div>
                </button> 
                </div> <!-- end grid -->
            </div> <!-- end scrollable flex-1 -->

            <!-- Dashboard Bottom Navbar -->
            <mobile-navbar 
                is-absolute
                :can-back="true"
                :show-action="true"
                :action-disabled="false"
                :can-search="false"
                :can-more="false"
                @back="$emit('goHome')"
                @home="$emit('goHome')"
                @action="prepareAddFolder"
            />
        </div>
        <div v-else class="flex-grow flex flex-col bg-slate-50/50 overflow-hidden w-full md:max-w-xl md:mx-auto relative md:h-[90vh]">
            <!-- Global Dual Header System -->
            <!-- Header 1: Module Level -->
            <div class="border-b border-slate-300 bg-white sticky top-0 z-[110] w-full" style="padding: 8px 2px; min-height: 52px;">
                <div class="flex items-center justify-between w-full">
                    <!-- Consolidated Title Row -->
                    <div class="flex items-center flex-1 min-w-0">
                        <div class="app-title font-black leading-tight font-outfit tracking-widest cursor-pointer shrink-0" @click="resetToRoot" style="color: #0f172a !important; font-size: 32px !important;">
                            其他專區
                        </div>
                        <!-- Subtitle (Active Folder) moved behind Main Title -->
                        <div v-if="activeFolder" class="flex items-center ml-3 min-w-0">
                            <span class="text-slate-500 font-black truncate" style="font-size: 25px !important;">{{ activeFolder.name }}</span>
                        </div>
                    </div>

                    <!-- Right Side Functional Controls -->
                    <div v-if="activeFolder" class="flex items-center ml-2 shrink-0 space-x-2">
                        <!-- Trash Icon (Clear/Reset) moved to the far right -->
                        <template v-if="activeFolder.name.includes('隨機分組')">
                            <!-- Invert Selection Button for Random Group -->
                            <button @click="randomGroupRef?.invertSelection()" 
                                class="px-[6px] py-1 bg-indigo-50 text-indigo-600 rounded-lg text-[16px] font-black active:scale-95 transition-all shadow-sm border border-indigo-100 whitespace-nowrap"
                                style="font-size: 16px !important;">
                                反選
                            </button>
                            <button @click="randomGroupRef?.resetAll()" class="w-10 h-10 bg-red-50 text-red-500 rounded-xl flex items-center justify-center active:scale-95 transition-all shrink-0">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </button>
                        </template>
                        <template v-else-if="activeFolder.name.includes('開文核定')">
                            <button @click="kaiwenRef?.clearAll()" class="w-10 h-10 bg-red-50 text-red-500 rounded-xl flex items-center justify-center active:scale-95 transition-all shrink-0">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </button>
                        </template>
                    </div>
                </div>
            </div>


            <div v-if="activeFolder || showLuckyDraw" class="h-full">
                <!-- Special View: 抽籤工具 (LuckyDraw) -->
                <lucky-draw v-if="showLuckyDraw" ref="luckyDrawRef" 
                    :show="showLuckyDraw" 
                    :initial-mode="luckyDrawInitialMode" 
                    :folder-id="lotteryFolderId"
                    @close="showLuckyDraw = false" 
                    @saved="loadData(); showLuckyDraw = false" />
                
                <!-- Special View: 開文核定表 -->
                <kaiwen-approval v-if="activeFolder && activeFolder.name.includes('開文核定')" ref="kaiwenRef" />
                <!-- Special View: 隨機分組 -->
                <random-group v-else-if="activeFolder && activeFolder.name.includes('隨機分組')" ref="randomGroupRef" />
                
                <!-- Default View: Standard Records -->
                <div v-else-if="activeFolder" class="max-w-4xl mx-auto w-full px-[10px] pt-6 pb-32">
                    <div class="space-y-4">
                        <div v-for="record in activeFolder.other_records" :key="record.id" class="bg-white p-6 rounded-[24px] shadow-sm border border-slate-100 hover:shadow-md transition-shadow relative group">
                            <button @click.stop="deleteRecord(record.id)" class="absolute top-4 right-4 text-slate-300 active:text-red-500 transition-colors p-2 z-20">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </button>
                            
                            <div class="flex items-center text-slate-400 text-xs font-bold uppercase tracking-widest mb-2">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                {{ formatDate(record.record_date) || formatDate(record.created_at) }}
                            </div>

                            <h3 v-if="record.title" class="text-lg font-bold text-slate-800 mb-2">{{ record.title }}</h3>
                            <p class="text-slate-900 whitespace-pre-wrap leading-relaxed font-medium">{{ record.content }}</p>
                        </div>

                        <div v-if="!activeFolder.other_records?.length" class="text-center py-20 bg-white rounded-[32px] border border-dashed border-slate-200">
                            <div class="text-slate-300 mb-4 flex justify-center">
                                <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </div>
                            <p class="text-slate-400 font-medium">尚無任何記事内容</p>
                        </div>
                    </div>
                </div>
            </div>
            <div v-else class="flex-grow flex items-center justify-center text-slate-400 font-medium">
                請選擇一個資料夾開始
            </div>

            <!-- Folder Contents Bottom Navbar -->
            <mobile-navbar 
                :can-back="true"
                :show-action="!activeFolder?.name.includes('開文核定') && !activeFolder?.name.includes('隨機分組')"
                :action-disabled="false"
                :can-search="false"
                :can-more="!!activeFolder"
                @back="resetToRoot"
                @home="$emit('goHome')"
                @action="prepareAddRecord"
                @more="handleMore"
            />
        </div>



        <!-- Add Folder Modal -->
        <div v-if="showAddFolder" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-[40px] p-8 w-full max-w-md shadow-2xl">
                <h3 class="text-xl font-bold mb-6">新增資料夾</h3>
                <input v-model="newFolderName" @keyup.enter="saveFolder" placeholder="輸入名稱..." class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl mb-6 focus:ring-2 focus:ring-indigo-500/20 transition-all font-medium">
                <div class="flex items-center space-x-3 mb-8">
                    <span class="text-sm font-bold text-slate-400">主題色：</span>
                    <button v-for="c in ['#6366f1', '#f59e0b', '#10b981', '#ef4444', '#64748b']" :key="c" 
                        @click="newFolderColor = c"
                        :class="['w-8 h-8 rounded-full border-4', newFolderColor === c ? 'border-indigo-100' : 'border-transparent']"
                        :style="{ backgroundColor: c }"></button>
                </div>
                <div class="flex space-x-3 mt-4">
                    <button @click="showAddFolder = false" class="flex-1 h-[52px] rounded-2xl font-black text-slate-400 bg-slate-50 active:bg-slate-100 transition-all text-[18px]">取消</button>
                    <button @click="saveFolder" class="flex-[2] h-[52px] rounded-2xl font-black bg-blue-600 text-white shadow-lg shadow-blue-100 active:scale-95 transition-all text-[18px]" style="color: white !important;">建立資料夾</button>
                </div>
            </div>
        </div>

        <!-- Add Record Modal -->
        <div v-if="showAddRecord" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-[40px] p-8 w-full max-w-xl shadow-2xl">
                <h3 class="text-xl font-bold mb-6">新增記事</h3>
                <input v-model="newRecord.title" placeholder="標題 (選填)" class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl mb-4 focus:ring-2 focus:ring-indigo-500/20 transition-all font-medium">
                <textarea v-model="newRecord.content" rows="6" placeholder="內容..." class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl mb-6 focus:ring-2 focus:ring-indigo-500/20 transition-all font-medium resize-none"></textarea>
                <div class="flex space-x-3 mt-4">
                    <button @click="showAddRecord = false" class="flex-1 h-[52px] rounded-2xl font-black text-slate-400 bg-slate-50 active:bg-slate-100 transition-all text-[18px]">取消</button>
                    <button @click="saveRecord" class="flex-[2] h-[52px] rounded-2xl font-black bg-blue-600 text-white shadow-lg shadow-blue-100 active:scale-95 transition-all text-[18px]" style="color: white !important;">儲存記事</button>
                </div>
            </div>
        </div>

        <!-- Global Action Confirm / Toast (Critical for iOS deletion) -->
        <div v-if="persistentToast" class="fixed inset-0 z-[9999] flex items-center justify-center p-6 bg-slate-900/40 backdrop-blur-sm animate-fade-in">
            <div class="bg-white w-full max-w-sm rounded-[32px] shadow-2xl overflow-hidden animate-slide-up border border-white/20">
                <div class="p-8 text-center space-y-6">
                    <div class="flex flex-col items-center">
                        <div v-if="persistentToast.type === 'deleteConfirm'" class="w-16 h-16 bg-rose-50 text-rose-500 rounded-2xl flex items-center justify-center mb-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </div>
                        <div v-else-if="persistentToast.type === 'success'" class="w-16 h-16 bg-emerald-50 text-emerald-500 rounded-2xl flex items-center justify-center mb-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="3" stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <div v-else class="w-16 h-16 bg-indigo-50 text-indigo-500 rounded-2xl flex items-center justify-center mb-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <h3 class="text-[20px] font-black text-slate-900 leading-tight whitespace-pre-wrap">{{ persistentToast.msg }}</h3>
                    </div>

                    <div class="flex flex-col space-y-3">
                        <button v-if="persistentToast.type === 'deleteRecord' || persistentToast.type === 'deleteFolder'" 
                                @click="executeToastAction" 
                                class="w-full py-4 bg-rose-500 text-white rounded-2xl font-black text-[18px] active:scale-95 transition-all shadow-lg shadow-rose-200/50" 
                                style="color: white !important;">
                            確認刪除
                        </button>
                        <button @click="persistentToast = null" 
                                :class="persistentToast.type === 'success' || persistentToast.type === 'error' ? 'bg-indigo-600 text-white shadow-indigo-100' : 'bg-slate-100 text-slate-500'"
                                class="w-full py-4 rounded-2xl font-black text-[18px] active:scale-95 transition-all shadow-lg"
                                :style="{ color: (persistentToast.type === 'success' || persistentToast.type === 'error' ? 'white !important' : 'inherit') }">
                            {{ persistentToast.type === 'success' || persistentToast.type === 'error' ? '確認' : '取消' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- LuckyDraw moved inside content area -->
    </div>
</template>

<script setup>
import { ref, onMounted, computed, defineEmits } from 'vue';
import axios from 'axios';
import KaiwenApproval from './KaiwenApproval.vue';
import RandomGroup from './RandomGroup.vue';
import MobileNavbar from './MobileNavbar.vue';
import LuckyDraw from './LuckyDraw.vue';

const getTodayStr = () => {
    const d = new Date();
    return `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`;
};

const emit = defineEmits(['goHome']);

const resetToRoot = () => {
    activeFolderId.value = null;
    showLuckyDraw.value = false;
    activeAction.value = '';
};

const props = defineProps({
    user: Object
});

const folders = ref([]);
const activeFolderId = ref(null);
const kaiwenRef = ref(null);
const showLuckyDraw = ref(false);
const luckyDrawInitialMode = ref(null);
const showAddFolder = ref(false);
const showAddRecord = ref(false);
const randomGroupRef = ref(null);
const activeAction = ref('');

const newFolderName = ref('');
const newFolderColor = ref('#6366f1');
const newRecord = ref({ title: '', content: '', record_date: getTodayStr() });
const persistentToast = ref(null);
const deleteConfirmId = ref(null);

const sortedFolders = computed(() => {
    return [...folders.value]
        .filter(f => f.name !== '__other_records_zone__' && f.name !== '抽籤紀錄')
        .sort((a, b) => {
            const isKaiwenA = a.name.includes('開文核定');
            const isKaiwenB = b.name.includes('開文核定');
            const isRandomA = a.name.includes('隨機分組');
            const isRandomB = b.name.includes('隨機分組');

            if (isKaiwenA && !isKaiwenB) return -1;
            if (!isKaiwenA && isKaiwenB) return 1;
            if (isRandomA && !isRandomB && !isKaiwenB) return -1;
            if (!isRandomA && isRandomB && !isKaiwenA) return 1;
            return 0;
        });
});

const activeFolder = computed(() => folders.value.find(f => f.id === activeFolderId.value));
const lotteryFolderId = computed(() => folders.value.find(f => f.name === '抽籤紀錄')?.id);

const loadData = async () => {
    const res = await axios.get('/other-folders');
    folders.value = res.data;
    
    const kaiwenFolders = folders.value.filter(f => f.name.includes('開文核定'));
    const randomFolders = folders.value.filter(f => f.name.includes('隨機分組'));

    let needReload = false;

    // Remove duplicates if they exist (keep the first one)
    if (kaiwenFolders.length > 1) {
        for (let i = 1; i < kaiwenFolders.length; i++) {
            await axios.delete(`/other-folders/${kaiwenFolders[i].id}`);
        }
        needReload = true;
    }
    if (randomFolders.length > 1) {
        for (let i = 1; i < randomFolders.length; i++) {
            await axios.delete(`/other-folders/${randomFolders[i].id}`);
        }
        needReload = true;
    }

    // Auto-initialize if completely missing
    if (kaiwenFolders.length === 0) {
        await axios.post('/other-folders', { name: '開文核定表', color: '#6366f1' });
        needReload = true;
    }
    if (randomFolders.length === 0) {
        await axios.post('/other-folders', { name: '隨機分組', color: '#10b981' });
        needReload = true;
    }

    if (needReload) {
        return loadData();
    }
};

const saveFolder = async () => {
    if (!newFolderName.value) return;
    await axios.post('/other-folders', { name: newFolderName.value, color: newFolderColor.value });
    newFolderName.value = '';
    showAddFolder.value = false;
    await loadData();
};

const deleteFolder = (id) => {
    deleteConfirmId.value = id;
    persistentToast.value = { msg: '確定要刪除整個資料夾嗎？\n內容也會被一併刪除。', type: 'deleteFolder' };
};

const deleteRecord = (id) => {
    deleteConfirmId.value = id;
    persistentToast.value = { msg: '確定要刪除此記事嗎？', type: 'deleteRecord' };
};

const executeToastAction = async () => {
    if (!persistentToast.value) return;
    const type = persistentToast.value.type;
    const id = deleteConfirmId.value;
    
    try {
        if (type === 'deleteRecord') {
            await axios.delete(`/other-records/${id}`);
            persistentToast.value = { msg: '✓ 已成功刪除記事', type: 'success' };
        } else if (type === 'deleteFolder') {
            await axios.delete(`/other-folders/${id}`);
            if (activeFolderId.value === id) activeFolderId.value = null;
            persistentToast.value = { msg: '✓ 已成功刪除資料夾', type: 'success' };
        }
        
        if (persistentToast.value?.type === 'success') {
            setTimeout(() => { if (persistentToast.value?.type === 'success') persistentToast.value = null; }, 1500);
        }
        await loadData();
    } catch (e) {
        persistentToast.value = { msg: '✖ 操作失敗', type: 'error' };
    }
    deleteConfirmId.value = null;
};

const formatDate = (dateString) => {
    if (!dateString) return '';
    const s = String(dateString).split('T')[0].trim();
    const parts = s.split(/[-/]/);
    if (parts.length === 3) {
        let y = parts[0];
        let m = parts[1].padStart(2, '0');
        let d = parts[2].padStart(2, '0');
        if (!isNaN(parseInt(y)) && !isNaN(parseInt(m)) && !isNaN(parseInt(d))) {
            return `${y}/${m}/${d}`;
        }
    }
    return s.replace(/-/g, '/');
};

const prepareAddFolder = () => {
    showAddFolder.value = true;
};

const prepareAddRecord = () => {
    showAddRecord.value = true;
};

const handleMore = () => {
    persistentToast.value = { msg: '✖ 此資料夾暫不支援匯出功能', type: 'error' };
};

const getFolderSum = (id) => {
    const folder = folders.value.find(f => String(f.id) === String(id));
    return folder?.other_records?.length || 0;
};

onMounted(loadData);
</script>

<style scoped>
.custom-scrollbar { -webkit-overflow-scrolling: touch; overscroll-behavior-y: contain; }
.custom-scrollbar::-webkit-scrollbar { width: 5px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
* { -webkit-tap-highlight-color: transparent; }
</style>
