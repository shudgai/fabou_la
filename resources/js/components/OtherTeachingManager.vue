<template>
    <div class="bg-white h-[100vh] flex justify-center text-slate-900 overflow-hidden">
        <div class="bg-white h-full relative w-full shadow-sm flex flex-col font-sans overflow-hidden">
            <!-- Global Datalists -->
            <datalist id="instrument-list">
        <option v-for="t in instrumentTreasures" :key="t.id" :value="t.name" />
    </datalist>
    <datalist id="item-name-list">
                <option v-for="name in uniqueTreasureNames" :key="name" :value="name">{{ name }}</option>
            </datalist>
            <datalist id="master-list-entry">
                <option value="老祖仙師" />
                <option value="元始仙師" />
                <option value="道祖仙師" />
                <option value="靈寶仙師" />
                <option value="父皇仙師" />
                <option value="太宰仙師" />
                <option value="太子" />
                <option value="閻王仙師" />
                <option v-for="m in masters" :key="'m'+m.id" :value="m.name" />
            </datalist>
            <datalist id="num-list">
                <option v-for="n in 9" :key="n" :value="n">{{ n }}</option>
            </datalist>
            <datalist id="unit-list">
                <option v-for="u in units" :key="u" :value="u">{{ u }}</option>
            </datalist>
            <datalist id="remark-list">
                <option value="*允同享皇恩" />
                <option value="完畢" />
            </datalist>

            <datalist id="dharma-search-list">
                <option value="玄通宮">玄通宮</option>
                <option value="玄應宮">玄應宮</option>
                <option value="玄心宮">玄心宮</option>
                <option value="玄妙宮">玄妙宮</option>
                <option value="玄昇宮">玄昇宮</option>
                <option value="玄願宮">玄願宮</option>
                <option value="玄法宮">玄法宮</option>
                <option value="玄閻宮">玄閻宮</option>
                <option value="玄窕宮">玄窕宮</option>
                <option value="玄瑤宮">玄瑤宮</option>
                <option value="玄義宮">玄義宮</option>
                <option value="宮主">宮主</option>
                <option value="宮主副宮主">宮主副宮主</option>
                <option v-for="dn in sortedDharmaNames" :key="'dn'+dn.id" :value="dn.name">{{ dn.name }}</option>
                <option v-for="g in palacePrioritizedGroups" :key="'g'+g.id" :value="g.name">{{ g.name }}</option>
            </datalist>
            
            <datalist id="body-part-list">
                <option value="患處" />
                <option value="五大穴" />
                <option value="第一大穴" />
                <option value="第二大穴" />
                <option value="第三大穴" />
                <option value="第四大穴" />
                <option value="第五大穴" />
                <option value="前身三大穴" />
                <option value="後身二大穴" />
            </datalist>

            <!-- Header with Back Button (Persistent) -->
            <!-- Global Dual Header System -->
            <!-- Header 1: Module Level -->
            <div v-if="currentFolder !== null && !addMode" class="border-b border-slate-300 flex items-center bg-white sticky top-0 z-[110] w-full" style="padding: 8px 10px; min-height: 52px;">
                <div class="flex-1 flex flex-col justify-center min-w-0 py-1 cursor-pointer" @click="resetToRoot">
                    <div class="app-title text-[20px] font-black leading-tight font-outfit tracking-widest break-words" style="color: #0f172a !important;">
                        其他開示資料
                    </div>
                </div>
                <div class="flex items-center space-x-2 mr-2">
                    <button @click="toggleSort" class="px-2 py-1 text-[12px] text-indigo-500 bg-indigo-50 border border-indigo-100 rounded-lg active:scale-95 transition-all font-black">
                        {{ sortDesc ? '新→舊' : '舊→新' }}
                    </button>
                    <button @click="reorderMode = !reorderMode" 
                            :class="reorderMode ? 'bg-white border-green-500 text-green-600 ring-2 ring-green-100' : 'bg-slate-100 text-slate-600 border border-transparent'"
                            class="px-3 py-1 rounded-lg text-[12px] font-black transition-all active:scale-95 shadow-sm whitespace-nowrap">
                        {{ reorderMode ? '確認排序' : '修改排序' }}
                    </button>
                </div>
            </div>

            <!-- Header 3: Global Add Level -->
            <div v-if="addMode && currentFolder === null" class="border-b border-slate-300 flex items-center bg-white sticky top-0 z-[110] w-full px-3 py-2" style="min-height: 52px;">
                <button @click="addMode = false" class="text-slate-400 p-2 -ml-2 mr-2 active:scale-90 transition-transform shrink-0">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" /></svg>
                </button>
                <div class="flex-1 min-w-0">
                    <h2 class="text-[20px] font-black text-slate-900 tracking-tight truncate font-outfit">其他開示資料</h2>
                </div>
            </div>

            <!-- Header 2: Action Level (Minimized when in folder view as controls moved to Header 1) -->
            <div v-if="currentFolder !== null && addMode" class="border-b border-slate-50 flex items-center bg-white z-[105] w-full sticky top-0" style="padding: 10px 10px;">
                <div class="flex items-center flex-1 min-w-0">
                    <!-- Sub-title removed per user request -->
                </div>
            </div>

            <!-- Level 1: Two Major Categories (Refined Aesthetic) -->
            <div v-if="currentCategory === null && currentFolder === null && !addMode" class="flex-1 overflow-y-auto custom-scrollbar bg-slate-50/30">
                <div class="px-[10px] py-[10px] flex items-center bg-white border-b border-slate-50 relative min-h-[52px] cursor-pointer" @click="resetToRoot">
                    <div class="flex-1">
                        <h1 class="text-[28px] font-black text-slate-900 tracking-tight text-center whitespace-nowrap">其他開示資料</h1>
                    </div>
                </div>
                
                <div class="px-[10px] pb-24 flex flex-col items-center space-y-[-10px] mt-[-30px]">
                    <!-- Category 1: Daily Teaching (Large Folder Style) -->
                    <button v-if="user?.permissions?.can_see_daily_teachings"
                        @click="currentFolder = folders_list.find(f => f.id === 0); currentCategory = 'daily'"
                        class="flex flex-col items-center justify-center p-0 active:scale-95 transition-all group relative">
                        <div class="relative w-[260px] h-[260px]">
                            <svg class="w-full h-full transition-transform group-hover:scale-105" viewBox="0 0 64 64" fill="none">
                                <defs>
                                    <linearGradient id="dailyGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                                        <stop offset="0%" style="stop-color:rgb(255, 230, 0);stop-opacity:1" />
                                        <stop offset="50%" style="stop-color:rgb(255, 200, 0);stop-opacity:1" />
                                        <stop offset="100%" style="stop-color:rgb(255, 170, 0);stop-opacity:1" />
                                    </linearGradient>
                                </defs>
                                <path d="M4 14C4 11.7909 5.79086 10 8 10H24.5L30 16H56C58.2091 16 60 17.7909 60 20V50C60 52.2091 58.2091 54 56 54H8C5.79086 54 4 52.2091 4 50V14Z" fill="url(#dailyGrad)" />
                                <path d="M4 22C4 19.7909 5.79086 18 8 18H56C58.2091 18 60 19.7909 60 22V50C60 52.2091 58.2091 54 56 54H8C5.79086 54 4 52.2091 4 50V22Z" fill="url(#dailyGrad)" stroke="rgba(255,255,255,0.6)" stroke-width="1"/>
                            </svg>
                            <!-- Label Inside -->
                            <div class="absolute inset-0 flex items-center justify-center pt-8 px-4">
                                <span class="text-[42px] font-black text-white tracking-tight leading-tight text-center" style="font-weight: 900 !important;">
                                    每日開示
                                </span>
                            </div>
                        </div>
                    </button>

                    <!-- Category 2: Master Teachings (Large Gold Folder Style) -->
                    <button 
                        @click="currentCategory = 'masters'"
                        class="flex flex-col items-center justify-center p-0 active:scale-95 transition-all group relative">
                        <div class="relative w-[260px] h-[260px]">
                            <svg class="w-full h-full transition-transform group-hover:scale-105" viewBox="0 0 64 64" fill="none">
                                <defs>
                                    <linearGradient id="mastersGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                                        <stop offset="0%" style="stop-color:rgb(255, 120, 120);stop-opacity:1" />
                                        <stop offset="50%" style="stop-color:rgb(255, 50, 50);stop-opacity:1" />
                                        <stop offset="100%" style="stop-color:rgb(220, 0, 0);stop-opacity:1" />
                                    </linearGradient>
                                </defs>
                                <path d="M4 14C4 11.7909 5.79086 10 8 10H24.5L30 16H56C58.2091 16 60 17.7909 60 20V50C60 52.2091 58.2091 54 56 54H8C5.79086 54 4 52.2091 4 50V14Z" fill="url(#mastersGrad)" />
                                <path d="M4 22C4 19.7909 5.79086 18 8 18H56C58.2091 18 60 19.7909 60 22V50C60 52.2091 58.2091 54 56 54H8C5.79086 54 4 52.2091 4 50V22Z" fill="url(#mastersGrad)" stroke="rgba(255,255,255,0.6)" stroke-width="1"/>
                            </svg>
                            <!-- Label Inside -->
                            <div class="absolute inset-0 flex items-center justify-center pt-8 px-4">
                                <span class="text-[42px] font-black text-white tracking-tight leading-tight text-center" style="font-weight: 900 !important;">
                                    其他開示記錄
                                </span>
                            </div>
                        </div>
                    </button>
                </div>


            </div>

            <!-- Level 1.5: Subfolders for Masters -->
            <div v-else-if="currentCategory === 'masters' && !currentFolder && !addMode" class="flex-1 overflow-y-auto custom-scrollbar bg-white">
                <div class="pt-[5px] pb-2 flex items-center relative min-h-[60px] border-b border-slate-50 sticky top-0 bg-white z-30">
                    <button @click="currentCategory = null" class="p-4 text-slate-400 active:scale-90 transition-transform z-10">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" /></svg>
                    </button>
                    <h2 class="absolute inset-x-0 text-[30px] font-black tracking-tight text-center text-slate-900 pointer-events-none">其他開示資料夾</h2>
                </div>
                <div class="grid grid-cols-2 gap-[10px] p-4 place-items-center">
                    <button v-for="(folder, idx) in filteredFolders" :key="folder.id" 
                        @click="currentFolder = folder"
                        class="flex flex-col items-center justify-center active:scale-95 transition-all group relative">
                        <div class="relative w-[148px] h-[148px]">
                            <svg class="w-full h-full transition-transform group-hover:scale-105" viewBox="0 0 64 64" fill="none">
                                <defs>
                                    <linearGradient :id="'fGrad' + idx" x1="0%" y1="0%" x2="100%" y2="100%">
                                        <stop offset="0%" style="stop-color:rgb(255, 120, 120);stop-opacity:1" />
                                        <stop offset="50%" style="stop-color:rgb(255, 50, 50);stop-opacity:1" />
                                        <stop offset="100%" style="stop-color:rgb(220, 0, 0);stop-opacity:1" />
                                    </linearGradient>
                                </defs>
                                <path d="M4 14C4 11.7909 5.79086 10 8 10H24.5L30 16H56C58.2091 16 60 17.7909 60 20V50C60 52.2091 58.2091 54 56 54H8C5.79086 54 4 52.2091 4 50V14Z" :fill="'url(#fGrad' + idx + ')'" />
                                <path d="M4 22C4 19.7909 5.79086 18 8 18H56C58.2091 18 60 19.7909 60 22V50C60 52.2091 58.2091 54 56 54H8C5.79086 54 4 52.2091 4 50V22Z" :fill="'url(#fGrad' + idx + ')'" stroke="rgba(255,255,255,0.6)" stroke-width="1"/>
                            </svg>
                            <!-- Label Inside -->
                            <div class="absolute inset-0 flex items-center justify-center pt-5 px-3">
                                <span :class="[
                                    'font-black tracking-tight leading-tight text-center transition-all text-[22px] whitespace-nowrap',
                                    folder.name === '閻王仙師' ? 'text-black' : 'text-white'
                                ]" style="font-weight: 900 !important;">
                                    {{ folder.name }}
                                </span>
                            </div>
                        </div>
                    </button>
                </div>
                

            </div>

            <!-- Level 2: List & Add View -->
            <template v-else>
                <!-- Add View -->
                <div v-if="addMode" class="flex-1 overflow-y-auto custom-scrollbar bg-white">
                    <div class="bg-white w-full h-full relative flex flex-col">
                        <div class="space-y-4 pb-32 flex-1 p-4">
                                <div class="grid grid-cols-2 gap-3 pb-1 mt-1">
                                    <div class="space-y-0.5">
                                        <label class="app-title ml-1 opacity-0">日期</label>
                                        <div class="relative flex items-center h-[52px]">
                                            <input v-model="form.date" type="text" placeholder="年/月/日 或 註記文字" 
                                                class="w-full h-full border border-slate-400 rounded-xl bg-slate-50/50 pl-[10px] pr-[32px] shadow-sm focus:ring-0 outline-none app-title text-slate-900 font-bold text-[15px]">
                                            <button @click="activeDate = 'date'" class="absolute right-2 text-slate-400 hover:text-indigo-600 transition-colors p-1">
                                                <svg class="w-4 h-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="space-y-0.5 relative">
                                        <label class="app-title ml-1">仙師</label>
                                        <div class="border border-slate-400 rounded-xl bg-slate-50/50 pl-[10px] pr-[2px] py-[2px] flex items-center h-[52px] shadow-sm relative">
                                            <input v-model="masterNameInput" @change="resolveMasterId" @focus="activeMasterDropdownId = 'mainMaster'" placeholder="選擇或輸入仙師..." class="w-full bg-transparent border-none text-[17px] text-slate-900 focus:ring-0 outline-none font-black placeholder-sky-400">
                                            <button @click.prevent="activeMasterDropdownId = (activeMasterDropdownId === 'mainMaster' ? null : 'mainMaster')" class="p-1 text-slate-900 opacity-60 hover:text-indigo-500 hover:opacity-100 shrink-0">
                                                <svg class="w-4 h-4 transition-transform" :class="activeMasterDropdownId === 'mainMaster' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                            </button>
                                            
                                            <!-- Custom Dropdown Menu -->
                                            <div v-if="activeMasterDropdownId === 'mainMaster'" class="absolute left-0 top-full mt-2 w-full bg-white rounded-2xl shadow-[0_20px_50px_rgba(0,0,0,0.2)] border border-slate-100 z-[600] p-1.5 animate-fade-in max-h-[250px] overflow-y-auto custom-scrollbar">
                                                <div v-for="m in ['老祖仙師', '元始仙師', '道祖仙師', '靈寶仙師', '父皇仙師', '太宰仙師', '太子', '閻王仙師']" :key="m"
                                                     @click.stop="masterNameInput = m; resolveMasterId(); activeMasterDropdownId = null"
                                                     class="px-4 h-[38px] flex items-center rounded-xl hover:bg-indigo-50 font-black text-[17px] text-slate-900 active:bg-indigo-100 transition-all cursor-pointer">
                                                    {{ m }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <template v-if="activeEntryTab === 'single'">
                                
                                <div class="grid grid-cols-12 gap-3 py-1.5 border-b border-blue-100/10">
                                    <div class="col-span-8 space-y-0.5">
                                        <div class="flex items-center justify-between">
                                            <label class="text-[13px] text-slate-400 font-bold px-1">對象 / 群組</label>
                                            <button @click.prevent="showDharmaPicker = true" class="text-[11px] bg-indigo-50 text-indigo-600 px-2 py-0.5 rounded-full font-bold active:scale-95 transition-all">選擇器</button>
                                        </div>
                                        <div class="border border-slate-400 rounded-2xl bg-slate-50/50 pl-[10px] pr-[2px] py-[2px] flex items-center h-[52px] relative">
                                            <input type="text" 
                                                   @input="e => { activePractitionerDropdownId = 'mainPract'; handleDharmaSearchInput(e) }" 
                                                   @focus="activePractitionerDropdownId = 'mainPract'"
                                                   @keyup.enter="handleDharmaSearchInput"
                                                   v-model="dharmaSearchQuery"
                                                   placeholder="搜尋法號或群組..." 
                                                   class="w-full bg-transparent border-none text-[17px] text-slate-900 focus:ring-0 outline-none font-black placeholder-sky-400 placeholder:text-[17px]">
                                            <button @click.stop="activePractitionerDropdownId = (activePractitionerDropdownId === 'mainPract' ? null : 'mainPract')" class="p-1 text-slate-900 opacity-60 hover:text-indigo-500 hover:opacity-100">
                                                <svg class="w-5 h-5" :class="activePractitionerDropdownId === 'mainPract' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                            </button>
                                            <div v-if="activePractitionerDropdownId === 'mainPract'" class="absolute left-0 top-full mt-2 w-full bg-white rounded-3xl shadow-[0_20px_50px_rgba(0,0,0,0.2)] border border-slate-100 z-[600] overflow-hidden p-1.5 animate-fade-in max-h-[300px] overflow-y-auto custom-scrollbar">
                                                <div v-if="mainSearchFilteredDharmaNames.length > 0" class="px-5 py-2 text-[12px] font-bold text-slate-400 uppercase tracking-widest bg-slate-50/50 mt-2 mb-1">法號</div>
                                                <div v-for="dn in mainSearchFilteredDharmaNames" :key="'dn'+dn.id"
                                                     @click.stop="dharmaSearchQuery = dn.name; activePractitionerDropdownId = null; handleDharmaSearchInput({target: {value: dn.name}})"
                                                     class="px-5 h-[38px] flex items-center rounded-2xl hover:bg-indigo-50 font-black text-[17px] text-slate-900 active:bg-indigo-100 transition-all cursor-pointer">
                                                    {{ dn.name }}
                                                </div>
                                                <div v-if="mainSearchFilteredGroups.length > 0" class="px-5 py-2 text-[12px] font-bold text-indigo-500 uppercase tracking-widest bg-slate-50/50 mb-1 rounded-t-2xl">群組</div>
                                                <div v-for="g in mainSearchFilteredGroups" :key="'g'+g.id"
                                                     @click.stop="g.name === '各宮' ? (showPalacePicker = true) : triggerGroupSelection(g)"
                                                     class="px-5 h-[38px] flex items-center rounded-2xl hover:bg-indigo-50 font-black text-[17px] text-indigo-600 active:bg-indigo-100 transition-all cursor-pointer">
                                                    {{ formatGroupName(g.name) }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-span-4 space-y-0.5">
                                        <label class="text-[13px] text-slate-400 font-bold px-1">備註對象</label>
                                        <div class="border border-slate-400 rounded-2xl bg-slate-50/50 overflow-hidden h-[52px]">
                                            <input v-model="form.target_remarks" placeholder="備註對象..." class="w-full h-full bg-transparent border-none text-[17px] text-slate-900 focus:ring-0 outline-none pl-[10px] pr-[2px] py-[2px] font-black placeholder-blue-300 placeholder:text-[17px]">
                                        </div>
                                    </div>

                                    <!-- Full-Width Selected Personnel Tag Display (Utilizes full page width per user request) -->
                                    <!-- Concise Group Display or Individual Tags -->
                                    <div v-if="form.dharma_name_ids.length > 0 && dharmaSearchQuery !== '全體殿生'" class="col-span-12 mt-1 animate-fade-in">
                                        <div class="flex flex-wrap gap-2.5 px-1 py-1">
                                            <!-- Group Mode -->
                                            <template v-if="activeModalGroup || currentMatchedGroup">
                                                <div @click="activeModalGroup = (activeModalGroup || currentMatchedGroup); showGroupMembersModal = true" 
                                                     class="bg-indigo-50 border-2 border-indigo-100 text-indigo-700 px-4 py-[10px] rounded-2xl app-body flex items-center shadow-sm cursor-pointer hover:bg-indigo-100 transition-all">
                                                    <span class="mr-2 w-2 h-2 bg-indigo-600 rounded-full animate-pulse"></span>
                                                    <span class="font-black text-[17px]">群組：{{ formatGroupName(activeModalGroup?.name || currentMatchedGroup?.name) }} ({{ form.dharma_name_ids.length }}人)</span>
                                                    <span class="ml-2 text-[12px] opacity-60 underline font-bold tracking-tight">查看明細</span>
                                                    <button @click.stop="form.dharma_name_ids = []; dharmaSearchQuery = ''" class="ml-4 text-indigo-300 hover:text-red-500 transition-colors">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                                    </button>
                                                </div>
                                            </template>
                                            
                                            <!-- Individual Mode -->
                                            <template v-else>
                                                <div v-for="id in form.dharma_name_ids" :key="'sel'+id" 
                                                     class="bg-white border-2 border-slate-100 text-slate-900 px-3 py-[10px] rounded-2xl app-body flex items-center shadow-sm hover:border-indigo-200 transition-colors">
                                                    <button @click.prevent="toggleDharmaName(id)" class="mr-2 text-slate-300 hover:text-red-500 transition-colors">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                                    </button>
                                                    <span class="mr-2 w-2 h-2 bg-indigo-400 rounded-full shrink-0"></span>
                                                    {{ getDharmaNameText(id) }}
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="bg-blue-50/50 border-b border-blue-100/30 py-3 pl-[10px] pr-4 flex flex-col space-y-1.5">
                                    <label class="text-[13px] text-slate-400 font-bold px-1 uppercase tracking-wider">輸入開示內容</label>
                                    <div class="flex-1 border border-slate-400 rounded-2xl bg-white overflow-hidden shadow-inner">
                                        <textarea v-model="form.content" @paste="e => handleSmartPaste(e, form, null)" rows="1" @input="e => { e.target.style.height = 'auto'; e.target.style.height = e.target.scrollHeight + 'px' }" placeholder="輸入開示內容" class="w-full bg-transparent border-none app-body text-[17px] font-black text-slate-900 focus:ring-0 outline-none pl-[10px] pr-[2px] py-[2px] resize-none overflow-hidden min-h-[52px] h-[52px] leading-relaxed placeholder-sky-400"></textarea>
                                    </div>
                                </div>
                            </template>
                            <template v-else>
                                <div class="flex items-center justify-between mb-2">
                                    <div class="text-[14px] text-slate-400 font-bold uppercase tracking-[0.1em]">整筆智慧錄入 (v2)</div>
                                    <div class="flex space-x-2">
                                        <label class="bg-indigo-50 text-indigo-600 px-3 py-[10px] rounded-xl text-[12px] font-black cursor-pointer active:scale-95 transition-all">
                                            匯入檔案
                                            <input type="file" class="hidden" accept=".txt,.csv,.xlsx,.xls,.docx" @change="handleBatchFileImport">
                                        </label>
                                        <button @click="processBatchText" class="bg-[#FFB266] text-white px-4 py-[10px] rounded-xl text-[12px] font-black active:scale-95 transition-all">
                                            智慧解析
                                        </button>
                                    </div>
                                </div>
                                <div class="bg-blue-50/30 border-2 border-dashed border-blue-100 rounded-[28px] overflow-hidden min-h-[400px] flex flex-col relative">
                                    <button v-if="batchImportContent" @click="batchImportContent = ''" class="absolute top-4 right-4 z-10 w-8 h-8 bg-white/80 backdrop-blur rounded-full flex items-center justify-center text-slate-400 hover:text-red-500 transition-all active:scale-90 shadow-sm border border-slate-100">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" /></svg>
                                    </button>
                                    <div class="p-5 flex-1 flex flex-col">
                                        <textarea v-model="batchImportContent" 
                                                  @paste="handleBatchPaste"
                                                  placeholder="整筆智慧錄入 (V2) (不可手動輸入文字，僅供貼上或匯入檔案)&#10;&#10;在此貼上資料...&#10;格式例：&#10;父皇仙師開示給對象：&#10;內容...&#10;賜降：&#10;1.法寶名稱:詳情" 
                                                  class="w-full flex-1 bg-transparent border-none text-[17px] text-slate-800 focus:ring-0 outline-none resize-none font-black leading-relaxed placeholder:text-rose-400 placeholder:font-black placeholder:text-[17px]"></textarea>
                                        
                                        <!-- Instructions -->
                                        <div class="mt-4 p-4 bg-white/60 rounded-2xl border border-blue-50 text-[13px] text-slate-400 leading-normal">
                                            <p class="font-bold text-slate-500 mb-1">💡 智慧貼上說明：</p>
                                            <ul class="space-y-1 list-disc pl-4">
                                                <li>支援從 Excel 貼上多行紀錄</li>
                                                <li>自動識別「仙師」與「對象」</li>
                                                <li>偵測到「賜降：」會自動解析清單</li>
                                                <li>多筆紀錄請保持基本段落區隔</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <!-- Preview of parsed blocks if any -->
                                <div v-if="batchRecords.length > 0 && batchRecords[0].content" class="mt-8 space-y-4">
                                    <div class="text-[14px] text-slate-400 font-bold px-2 flex items-center">
                                        <span class="w-2 h-2 bg-emerald-500 rounded-full mr-2"></span>
                                        已解析紀錄 ({{ batchRecords.length }} 筆)
                                    </div>
                                    <div v-for="(record, index) in batchRecords" :key="index" class="bg-white border border-slate-100 rounded-3xl p-5 shadow-sm space-y-3">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-2">
                                                <span class="text-[12px] font-black text-indigo-500 bg-indigo-50 px-2.5 py-1 rounded-lg">#{{ index + 1 }} {{ record.master_name }}</span>
                                                <!-- Reorder Buttons -->
                                                <div class="flex items-center space-x-1 ml-2">
                                                    <button @click="moveBatchRecord(index, -1)" :disabled="index === 0" class="p-1 text-slate-300 hover:text-indigo-500 disabled:opacity-20 transition-all active:scale-90">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 15l7-7 7 7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                                    </button>
                                                    <button @click="moveBatchRecord(index, 1)" :disabled="index === batchRecords.length - 1" class="p-1 text-slate-300 hover:text-indigo-500 disabled:opacity-20 transition-all active:scale-90">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                                    </button>
                                                </div>
                                            </div>
                                            <button @click="batchRecords.splice(index, 1)" class="text-slate-200 hover:text-red-400 p-1">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2.5" /></svg>
                                            </button>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <div class="font-black text-slate-900 text-[17px]">{{ record.dharmaSearchQuery || getRecipientName(record) }}</div>
                                            <div v-if="record.date" class="text-[12px] font-bold text-slate-400 flex items-center">
                                                <span class="mr-1">📅</span> {{ record.date }}
                                            </div>
                                        </div>
                                        <div class="text-slate-600 text-[16px] font-bold leading-relaxed whitespace-pre-wrap">{{ record.content }}</div>
                                        <!-- Item Preview inside card -->
                                        <div v-if="record.items && record.items.length > 0" class="mt-2 pt-2 border-t border-slate-50 space-y-1">
                                            <div class="text-[11px] font-black text-slate-300 uppercase tracking-widest mb-1">已識別清單：</div>
                                            <div v-for="it in record.items" :key="it.uid" class="text-[13px] text-indigo-400 font-bold flex items-center">
                                                <span class="mr-1.5 opacity-60">💎</span> {{ it.treasure_name }}{{ it.details ? ' : ' + it.details : '' }}
                                                <button @click="record.items.splice(record.items.indexOf(it), 1)" class="ml-2 text-slate-300 hover:text-red-400">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="3"/></svg>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            <!-- Added Treasures List - Hidden per user request to simplify UI -->
                            <div v-if="false && Object.keys(groupedPendingItems).length > 0" class="mt-4 pt-4 border-t border-slate-100 space-y-0.5">
                                <div class="px-2 py-4 text-[13px] font-black text-slate-400 uppercase tracking-[0.2em] flex items-center">
                                    📝 已加入降寶 ({{ form.items.length }})
                                </div>
                                
                                <div v-for="(group, gName, gIdx) in groupedPendingItems" :key="gName" class="py-2.5 px-4 rounded-[28px] mb-2 border border-slate-100 bg-white hover:bg-slate-50 transition-colors cursor-pointer shadow-sm" @click="expandedDetails[gName] = !expandedDetails[gName]">
                                    <div class="flex items-center justify-between">
                                        <div class="flex flex-col flex-1 min-w-0 px-2">
                                            <span class="text-[17px] font-black text-slate-900 truncate">
                                                {{ gIdx + 1 }}. {{ stripMasterPrefix(gName) }}
                                                <template v-if="group.length === 1 && !group[0].name && !isSpecialInstrument(gName) && !gName.includes('丹') && !gName.includes('符') && !gName.includes('令') && !gName.includes('疏') && !gName.includes('香') && !gName.includes('由')">
                                                    <span v-if="group[0].details" class="text-indigo-600 font-black ml-1"> : {{ group[0].details }}</span>
                                                </template>
                                            </span>
                                            
                                            <!-- Specialized mode main details (Directly visible indented line) -->
                                            <div v-if="!(group.length === 1 && !group[0].name && !isSpecialInstrument(gName) && !gName.includes('丹') && !gName.includes('符') && !gName.includes('令') && !gName.includes('疏') && !gName.includes('香') && !gName.includes('由'))" 
                                                 class="mt-1 pl-6 text-[16px] text-slate-800 font-black flex items-center justify-between">
                                                <span class="flex-1">{{ getMainDetails(group) }}</span>
                                                <button @click.stop="removeMagicItemByGroup(gName)" class="text-red-300 hover:text-red-500 transition-colors p-1 opacity-40 hover:opacity-100">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2.5" /></svg>
                                                </button>
                                            </div>
                                            <div v-if="expandedDetails[gName]" class="mt-3 pl-4 space-y-3 animate-fade-in border-l-2 border-slate-100 ml-1">
                                                <div v-for="(m, midx) in group" :key="midx" class="text-[15px]">
                                                    <template v-if="m.name || m.sub_name?.trim()">
                                                        <div class="flex items-start group">
                                                            <span class="text-slate-300 font-black mr-2 shrink-0">+</span>
                                                            <div class="flex flex-col flex-1 min-w-0">
                                                                <div class="flex items-center">
                                                                    <span class="font-bold text-slate-700 truncate">{{ m.name ? m.name : '項目內容' }}</span>
                                                                    <span v-if="m.details" class="ml-2 text-indigo-500 font-black px-2 py-0.5 bg-indigo-50 rounded-lg text-[13px]">{{ m.details }}</span>
                                                                    <button @click.stop="removeMagicItem(m.uid)" class="ml-2 text-rose-300 opacity-0 group-hover:opacity-100 transition-opacity">✕</button>
                                                                </div>
                                                                <div v-if="m.sub_name?.trim()" class="mt-1 text-[13px] text-slate-400 font-medium leading-tight">
                                                                    {{ m.sub_name.trim() }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </template>
                                                    <button v-else-if="group.length > 1" @click.stop="removeMagicItem(m.uid)" class="ml-2 text-rose-300 text-[13px]">✕ (空內容)</button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex items-center space-x-2 pr-2" 
                                             v-if="group.length === 1 && !group[0].name && !isSpecialInstrument(gName) && !gName.includes('丹') && !gName.includes('符') && !gName.includes('令') && !gName.includes('疏') && !gName.includes('香') && !gName.includes('由')">
                                            <button @click.stop="removeGroup(gName)" class="text-rose-200 hover:text-rose-400 p-2 active:scale-90 transition-transform">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2.5" /></svg>
                                            </button>
                                        </div>
                                        <div class="w-10 shrink-0" v-else></div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Empty State -->
                            <div v-else class="py-20 flex flex-col items-center justify-center text-slate-300">
                                <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mb-4">
                                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" /></svg>
                                </div>
                                <p class="text-[17px] font-bold">目前無降寶明細</p>
                                <p class="text-[14px] mt-1">請點選下方「降寶詳情」按鈕開始輸入</p>
                            </div>

                            <!-- Global Footer Remarks in Add Mode (Multi-entry) -->
                            <div class="px-4 mt-6">
                                <div class="text-[14px] font-black text-slate-400 tracking-[0.1em] uppercase px-1 mb-2">結尾備註</div>
                                <!-- Added entries list -->
                                <div v-if="footerRemarksList.length > 0" class="mb-2 space-y-1">
                                    <div v-for="(r, ri) in footerRemarksList" :key="ri" class="flex items-center bg-indigo-50 rounded-2xl px-3 py-2">
                                        <span class="flex-1 text-[16px] font-bold text-slate-800">{{ r }}</span>
                                        <button @click="footerRemarksList.splice(ri, 1); syncFooterRemarks()" class="text-slate-300 hover:text-red-400 ml-2 text-[18px] leading-none transition-all">×</button>
                                    </div>
                                </div>
                                <!-- Input + Add button -->
                                <div class="flex items-center border border-slate-400 rounded-2xl bg-white overflow-hidden shadow-sm">
                                    <input v-model="newFooterRemark" list="remark-list" placeholder="例如：完畢..." @keyup.enter="addFooterRemark" class="flex-1 bg-transparent border-none text-[17px] font-black text-slate-900 focus:ring-0 outline-none px-4 py-3 placeholder-sky-400">
                                    <button @click="addFooterRemark" class="px-4 py-3 text-indigo-500 font-black text-[22px] leading-none hover:text-indigo-700 transition-all">+</button>
                                </div>
                            </div>

                            <!-- Floating Action Bar (Side-by-Side): Fixed above mobile navbar -->
                            <div class="fixed bottom-[7vh] left-0 right-0 p-[3px] pb-[1px] backdrop-blur-md z-[300] flex items-center space-x-4 px-6 shadow-[0_-10px_30px_rgba(0,0,0,0.05)]">
                                <button v-if="currentFolder?.id === 0 || activeEntryTab === 'single'"
                                    @click.prevent="itemsDetailMode = true" 
                                    class="w-[45%] bg-slate-100 text-slate-600 rounded-2xl py-[10px] shadow-md border border-slate-200 active:scale-95 transition-all text-[16px] font-bold">
                                    <span>降寶內容</span>
                                </button>
                                
                                <button @click="saveItem" :disabled="saving" class="flex-1 bg-[#FFB266] text-white rounded-2xl py-[10px] active:scale-95 disabled:opacity-50 text-[19px] font-black shadow-lg shadow-slate-900/20">
                                    {{ saving ? '錄入中...' : '確認存檔' }}
                                </button>
                            </div>
                            <!-- Space for Floating Button -->
                            <div class="h-96"></div>
                        </div>
                    </div>
                </div>

                <!-- Magic Items Detail View -->
                <div v-if="itemsDetailMode" class="items-detail-container fixed inset-0 z-[500] bg-[#f8fafc] flex flex-col overflow-y-auto animate-fade-in">
                    <div class="bg-white px-6 pt-4 pb-4 border-b border-slate-100 sticky top-0 z-[510] flex items-center justify-between shadow-sm">
                        <div class="flex items-center">
                            <button @click.prevent="handleItemsDetailClose" class="text-slate-400 mr-3 p-2 active:scale-90 transition-transform">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" /></svg>
                            </button>
                            <h3 class="text-[20px] font-bold text-slate-900 tracking-tight">降寶詳情</h3>
                        </div>
                    </div>

                    <div class="px-2.5 pt-0 pb-[20px] space-y-5">
                        <div class="bg-white rounded-[24px] border border-slate-50 p-2.5 shadow-sm">
                                <div class="text-[14px] font-black text-slate-400 tracking-[0.1em] uppercase flex items-center justify-between px-1 mb-2">
                                    <div class="flex items-center whitespace-nowrap overflow-hidden">
                                        <span class="w-5 h-5 bg-[#4A3728] text-white rounded-full flex items-center justify-center text-[11px] font-black mr-2 shrink-0">1</span>
                                        <span class="shrink-0">錄入法寶</span>
                                        <span v-if="activeBatchIndex !== null" class="ml-1 text-[13px] font-black truncate text-slate-400">
                                            (<span class="text-blue-600">{{ getRecipientName(batchRecords[activeBatchIndex]) }}</span>)
                                        </span>
                                        <span v-else-if="form.dharma_name_ids.length > 0 || form.target_remarks" class="ml-1 text-[13px] font-black truncate text-slate-400">
                                            (<span class="text-blue-600">{{ getRecipientName({dharma_name_ids: form.dharma_name_ids, target_remarks: form.target_remarks}) }}</span>)
                                        </span>
                                    </div>
                                    <button v-if="newItemName" @click="addNewItemQuickly" 
                                            :class="isAddingFlash ? 'text-emerald-500 scale-125' : 'text-red-500'"
                                            class="text-[36px] font-light leading-none active:scale-95 transition-all duration-300"> + </button>
                                </div>
                                <div class="grid grid-cols-12 gap-2.5 items-end">
                                    <div :class="(isSpecialInstrument(newItemName) || newItemName.includes('由')) ? 'col-span-10' : 'col-span-9'" class="space-y-1 relative">
                                        <div class="text-[12px] text-slate-400 font-bold px-1 mb-0.5 text-left">法寶名稱</div>
                                        <div class="flex items-center">
                                            <div class="flex-1 border border-slate-400 rounded-xl bg-blue-50/40 flex items-center transition-all py-[10px] relative">
                                                <input v-model="newItemName" ref="treasureInput" @input="activeTreasureDropdownId = 'main'"
                                                       class="treasure-name-input w-full bg-transparent border-none px-4 text-[17px] font-black text-slate-900 focus:ring-0 outline-none text-left placeholder-sky-400 placeholder:text-[17px] placeholder:font-black">
                                                <div class="flex items-center space-x-1 pr-1">
                                                    <button @click.stop="activeTreasureDropdownId = (activeTreasureDropdownId === 'main' ? null : 'main')" class="p-1 text-slate-300 hover:text-indigo-500">
                                                        <svg class="w-5 h-5" :class="activeTreasureDropdownId === 'main' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                                    </button>
                                                    <button v-if="newItemName" @click="newItemName = ''" class="p-1 text-slate-300 hover:text-red-400 active:scale-95 transition-all">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Custom Treasure Dropdown -->
                                        <div v-if="activeTreasureDropdownId === 'main'" class="absolute left-0 top-full mt-2 w-full bg-white rounded-3xl shadow-[0_20px_50px_rgba(0,0,0,0.2)] border border-slate-100 z-[600] overflow-hidden p-1.5 animate-fade-in max-h-[300px] overflow-y-auto custom-scrollbar">
                                            <div v-for="name in uniqueTreasureNames.filter(n => !newItemName || n.toLowerCase().includes(newItemName.toLowerCase()))" :key="name" 
                                                 @click.stop="newItemName = name; activeTreasureDropdownId = null" 
                                                 class="px-5 h-[38px] flex items-center rounded-2xl hover:bg-indigo-50 font-black text-[17px] text-slate-900 active:bg-indigo-100 transition-all cursor-pointer">
                                                {{ name }}
                                            </div>
                                        </div>
                                        <div v-if="newItemName === '令牌' || newItemName === '令牌 '" class="mt-3 flex flex-wrap gap-2 px-1 animate-fade-in">
                                            <button v-for="t in ['太令令牌', '極令令牌', '道令令牌', '元令令牌', '靈令令牌', '玉皇令令牌', '皇令牌', '龍令令牌', '王令令牌']" 
                                                    :key="t"
                                                    @click="newItemName = t"
                                                    class="px-4 py-[10px] bg-blue-50 text-blue-600 rounded-xl text-[17px] font-black border border-blue-100 active:scale-95 transition-all">
                                                {{ t }}
                                            </button>
                                        </div>
                                    </div>
                                    <div v-if="magicItemCategory === 'default' && !isSpecialInstrument(newItemName) && !newItemName.includes('由') && !showAddDetails && !newItemMainRemarks.trim()" class="col-span-3 space-y-1">
                                        <div class="text-[12px] text-slate-400 font-bold px-1 mb-0.5 text-center">天份</div>
                                        <div class="border border-slate-400 rounded-xl bg-blue-50/40 overflow-hidden flex items-center px-2 transition-all py-[10px]">
                                            <input v-model="newItemDays" list="num-list"
                                                   class="w-full bg-transparent border-none text-[17px] font-black text-slate-900 focus:ring-0 outline-none text-center placeholder:text-[17px] placeholder:font-black" 
                                                   placeholder="0">
                                            <span class="text-slate-400 font-bold ml-1 shrink-0 text-[13px]">天</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Main Item Remarks (Toggleable) - Hidden when adding details -->
                                <div v-if="!showAddDetails" class="mt-2 text-left">
                                    <button @click="showMainRemarks = !showMainRemarks" class="text-[12px] font-bold text-slate-400 flex items-center px-1">
                                        {{ showMainRemarks ? '隱藏主備註' : '+ 加入主備註' }}
                                    </button>
                                    <div v-if="showMainRemarks" class="mt-2 border border-slate-400 rounded-2xl bg-blue-50/30 px-3 py-[10px] flex items-center animate-fade-in overflow-hidden">
                                        <input v-model="newItemMainRemarks" list="remark-list"
                                               class="w-full bg-transparent border-none text-[17px] font-black text-slate-900 focus:ring-0 text-left py-3 placeholder-sky-400 placeholder:text-[17px] placeholder:font-black" 
                                               placeholder="加入備註...">
                                    </div>
                                </div>

                                <!-- SPECIALIZED INPUTS (Immediate Category fields) -->
                                <div v-if="magicItemCategory !== 'default'" class="mt-2.5 space-y-2.5 animate-fade-in">
                                    <div v-if="magicItemCategory === '三光金丹'" class="grid grid-cols-4 gap-2">
                                        <div class="space-y-1">
                                            <div class="text-[11px] text-slate-400 font-bold px-2">日</div>
                                            <div class="border border-slate-100 rounded-2xl bg-slate-50/20 flex items-center px-3 py-[10px]">
                                                <input v-model="newItemSun" list="num-list" class="w-full bg-transparent border-none text-[16px] font-bold text-slate-900 focus:ring-0 outline-none text-center" placeholder="0">
                                                <span class="text-slate-400 font-bold ml-0.5 shrink-0 text-[11px]">粒</span>
                                            </div>
                                        </div>
                                        <div class="space-y-1">
                                            <div class="text-[11px] text-slate-400 font-bold px-2">月</div>
                                            <div class="border border-slate-100 rounded-2xl bg-slate-50/20 flex items-center px-3 py-[10px]">
                                                <input v-model="newItemMoon" list="num-list" class="w-full bg-transparent border-none text-[16px] font-bold text-slate-900 focus:ring-0 outline-none text-center" placeholder="0">
                                                <span class="text-slate-400 font-bold ml-0.5 shrink-0 text-[11px]">粒</span>
                                            </div>
                                        </div>
                                        <div class="space-y-1">
                                            <div class="text-[11px] text-slate-400 font-bold px-2">光</div>
                                            <div class="border border-slate-100 rounded-2xl bg-slate-50/20 flex items-center px-3 py-[10px]">
                                                <input v-model="newItemLight" list="num-list" class="w-full bg-transparent border-none text-[16px] font-bold text-slate-900 focus:ring-0 outline-none text-center" placeholder="0">
                                                <span class="text-slate-400 font-bold ml-0.5 shrink-0 text-[11px]">粒</span>
                                            </div>
                                        </div>
                                        <div v-if="!newItemMainRemarks.trim()" class="space-y-1">
                                            <div class="text-[11px] text-slate-400 font-bold px-2">天份</div>
                                            <div class="border border-slate-100 rounded-2xl bg-slate-50/20 flex items-center px-2 py-[10px]">
                                                <input v-model="newItemMainDays" list="num-list" class="w-full bg-transparent border-none text-[16px] font-bold text-slate-900 focus:ring-0 outline-none text-center" placeholder="0">
                                                <span class="text-slate-400 font-bold ml-0.5 shrink-0 text-[11px]">天</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div v-else-if="magicItemCategory === '金丹'" class="grid grid-cols-3 gap-2">
                                        <div class="space-y-1">
                                            <div class="text-[11px] text-slate-400 font-bold px-2">吃</div>
                                            <div class="border border-slate-100 rounded-2xl bg-slate-50/20 flex items-center px-3 py-[10px]">
                                                <input v-model="newItemMainTimes" list="num-list" class="w-full bg-transparent border-none text-[17px] font-black text-slate-900 focus:ring-0 outline-none text-center placeholder:text-[17px]" placeholder="0">
                                                <span class="text-slate-400 font-black ml-1 shrink-0 text-[13px]">粒</span>
                                            </div>
                                        </div>
                                        <div class="space-y-1">
                                            <div class="text-[11px] text-slate-400 font-bold px-2">洗</div>
                                            <div class="border border-slate-100 rounded-2xl bg-slate-50/20 flex items-center px-3 py-[10px]">
                                                <input v-model="newItemMainHours" list="num-list" class="w-full bg-transparent border-none text-[17px] font-black text-slate-900 focus:ring-0 outline-none text-center placeholder:text-[17px]" placeholder="0">
                                                <span class="text-slate-400 font-black ml-1 shrink-0 text-[13px]">粒</span>
                                            </div>
                                        </div>
                                        <div v-if="!newItemMainRemarks.trim()" class="space-y-1">
                                            <div class="text-[11px] text-slate-400 font-bold px-2">天份</div>
                                            <div class="border border-slate-100 rounded-2xl bg-slate-50/20 flex items-center px-3 py-[10px]">
                                                <input v-model="newItemMainDays" list="num-list" class="w-full bg-transparent border-none text-[17px] font-black text-slate-900 focus:ring-0 outline-none text-center placeholder:text-[17px]" placeholder="0">
                                                <span class="text-slate-400 font-black ml-1 shrink-0 text-[13px]">天</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- TALISMANS / SHUWUN / TAI-LING -->
                                    <div v-else-if="magicItemCategory === '符令' || magicItemCategory === '太令'" class="space-y-2.5">
                                        <div class="grid grid-cols-2 gap-2">
                                            <div class="space-y-1">
                                                <div class="text-[11px] text-slate-400 font-bold px-2">尺寸</div>
                                                <div class="border border-slate-100 rounded-2xl bg-slate-50/20 flex items-center px-4 py-[10px]">
                                                    <input v-model="newItemSubSize" class="w-full bg-transparent border-none text-[17px] font-black text-slate-900 focus:ring-0 outline-none text-center placeholder:text-[17px]" placeholder="尺寸">
                                                </div>
                                            </div>
                                            <div v-if="!newItemMainRemarks.trim()" class="space-y-1">
                                                <div class="text-[11px] text-slate-400 font-bold px-2">天數</div>
                                                <div class="border border-slate-100 rounded-2xl bg-slate-50/20 flex items-center px-3 py-[10px]">
                                                    <input v-model="newItemSubDetails" list="num-list" class="w-full bg-transparent border-none text-[17px] font-black text-slate-900 focus:ring-0 outline-none text-center placeholder:text-[17px]" placeholder="天份">
                                                    <span class="text-slate-400 font-black ml-1 shrink-0 text-[13px]">天份</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="border border-slate-100 rounded-2xl bg-slate-50/20 px-3 py-[10px] flex items-center relative">
                                            <input v-model="newItemPractitioner" @input="activePractitionerDropdownId = 'mainFulingPract'" @focus="activePractitionerDropdownId = 'mainFulingPract'" class="w-full bg-transparent border-none text-[17px] font-black text-slate-900 focus:ring-0 outline-none text-left placeholder:text-[17px]" placeholder="開立人 (法號)...">
                                            <button @click.stop="activePractitionerDropdownId = (activePractitionerDropdownId === 'mainFulingPract' ? null : 'mainFulingPract')" class="p-1 text-slate-900 opacity-60 hover:text-indigo-500 hover:opacity-100">
                                                <svg class="w-5 h-5" :class="activePractitionerDropdownId === 'mainFulingPract' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                            </button>
                                            <div v-if="activePractitionerDropdownId === 'mainFulingPract'" class="absolute left-0 top-full mt-2 w-full bg-white rounded-3xl shadow-[0_20px_50px_rgba(0,0,0,0.2)] border border-slate-100 z-[600] overflow-hidden p-1.5 animate-fade-in max-h-[300px] overflow-y-auto custom-scrollbar">
                                                <div v-for="dn in detailModalFilteredDharmaNames" :key="'mainfp'+dn.id" 
                                                     @click.stop="newItemPractitioner = dn.name; activePractitionerDropdownId = null" 
                                                     class="px-5 h-[38px] flex items-center rounded-2xl hover:bg-indigo-50 font-black text-[17px] text-slate-900 active:bg-indigo-100 transition-all cursor-pointer">
                                                    {{ dn.name }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- INCENSE COIL (Strict search) -->
                                    <div v-else-if="magicItemCategory === '香環'" class="grid grid-cols-2 gap-2">
                                        <div class="border border-slate-100 rounded-2xl bg-slate-50/20 flex items-center px-4 py-[10px]">
                                            <input v-model="newItemSubDetails" list="num-list" class="w-full bg-transparent border-none text-[17px] font-black text-slate-900 focus:ring-0 outline-none text-center placeholder:text-[17px]" placeholder="個數">
                                            <span class="text-slate-400 font-black ml-1 shrink-0 text-[13px]">個</span>
                                        </div>
                                        <div class="border border-slate-100 rounded-2xl bg-slate-50/20 flex items-center px-4 py-[10px]">
                                            <input v-model="newItemSubSheets" list="num-list" class="w-full bg-transparent border-none text-[17px] font-black text-slate-900 focus:ring-0 outline-none text-center placeholder:text-[17px]" placeholder="盒數">
                                            <span class="text-slate-400 font-black ml-1 shrink-0 text-[13px]">盒</span>
                                        </div>
                                    </div>

                                    <!-- FU-LU INCENSE -->
                                    <div v-else-if="magicItemCategory === '福祿香'" class="grid grid-cols-2 gap-2">
                                        <div class="border border-slate-100 rounded-2xl bg-slate-50/20 flex items-center px-4 py-[10px]">
                                            <input v-model="newItemSubDetails" list="num-list" class="w-full bg-transparent border-none text-[17px] font-black text-slate-900 focus:ring-0 outline-none text-center placeholder:text-[17px]" placeholder="0">
                                            <span class="text-slate-400 font-black ml-1 shrink-0 text-[13px]">根</span>
                                        </div>
                                        <div class="border border-slate-100 rounded-2xl bg-slate-50/20 flex items-center px-4 py-[10px]">
                                            <input v-model="newItemSubSheets" list="num-list" class="w-full bg-transparent border-none text-[17px] font-black text-slate-900 focus:ring-0 outline-none text-center placeholder:text-[17px]" placeholder="0">
                                            <span class="text-slate-400 font-black ml-1 shrink-0 text-[13px]">包</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- CLEARING & INSTRUMENT SPECIAL CASE (Visible even in default category) -->
                                <div v-if="!showAddDetails && (isSpecialInstrument(newItemName) || newItemName.includes('由'))" class="mt-4 space-y-4 px-1 mb-4">
                                    <!-- 1. Practitioner -->
                                    <div class="space-y-1.5">
                                        <div class="text-[13px] text-slate-400 font-bold px-1 text-left select-none">執法</div>
                                        <div class="relative group">
                                            <div class="border border-blue-100/50 rounded-xl bg-blue-50/40 px-4 py-[10px] flex items-center transition-all focus-within:border-blue-300 relative">
                                                <input v-model="newItemPractitioner" @input="activePractitionerDropdownId = 'pract2'" @focus="activePractitionerDropdownId = 'pract2'" class="w-full bg-transparent border-none text-[17px] font-black text-slate-900 focus:ring-0 outline-none text-left placeholder-sky-400 placeholder:text-[17px] placeholder:font-black" placeholder="輸入法號...">
                                                <button @click.stop="activePractitionerDropdownId = (activePractitionerDropdownId === 'pract2' ? null : 'pract2')" class="p-1 text-slate-900 opacity-60 hover:text-indigo-500 hover:opacity-100">
                                                    <svg class="w-5 h-5" :class="activePractitionerDropdownId === 'pract2' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                                </button>
                                                <div v-if="activePractitionerDropdownId === 'pract2'" class="absolute left-0 top-full mt-2 w-full bg-white rounded-3xl shadow-[0_20px_50px_rgba(0,0,0,0.2)] border border-slate-100 z-[600] overflow-hidden p-1.5 animate-fade-in max-h-[300px] overflow-y-auto custom-scrollbar">
                                                    <div v-for="dn in detailModalFilteredDharmaNames" :key="'pract2'+dn.id" 
                                                         @click.stop="newItemPractitioner = dn.name; activePractitionerDropdownId = null" 
                                                         class="px-5 h-[38px] flex items-center rounded-2xl hover:bg-indigo-50 font-black text-[17px] text-slate-900 active:bg-indigo-100 transition-all cursor-pointer">
                                                        {{ dn.name }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- 2. Instrument Name - Only show if the name is generic -->
                                    <div v-if="(newItemName === '令牌' || newItemName === '法器' || newItemName?.includes('由')) && newItemName !== '皇令牌' && !['極令令牌', '元令令牌', '道令令牌', '靈令令牌', '玉皇令令牌', '龍令令牌', '王令令牌', '太令令牌'].some(k => newItemName === k)" class="space-y-1.5">
                                        <div class="text-[13px] text-slate-400 font-bold px-1 text-left select-none">法器名稱</div>
                                        <div class="border border-blue-100/50 rounded-xl bg-blue-50/40 px-4 py-[10px] flex items-center transition-all focus-within:border-blue-300">
                                            <input v-model="newItemSubInstrumentName" list="instrument-list" class="w-full bg-transparent border-none text-[17px] font-black text-slate-900 focus:ring-0 outline-none text-left placeholder-sky-400 placeholder:text-[17px] placeholder:font-black" placeholder="使用的法器...">
                                        </div>
                                    </div>
                                    <!-- 3. Body Part -->
                                    <div class="space-y-1.5">
                                        <div class="text-[13px] text-slate-400 font-bold px-1 text-left select-none">清煞部位</div>
                                        <div class="border border-blue-100/50 rounded-xl bg-blue-50/40 px-4 py-[10px] flex items-center transition-all focus-within:border-blue-300">
                                            <input v-model="newItemSubBodyPart" list="body-part-list" class="w-full bg-transparent border-none text-[17px] font-black text-slate-900 focus:ring-0 outline-none text-left placeholder-sky-400 placeholder:text-[17px] placeholder:font-black" placeholder="輸入部位...">
                                        </div>
                                    </div>
                                </div>


                                <div class="mt-4 border-t border-slate-50 pt-3">
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="flex items-center space-x-1">
                                            <button @click="showAddDetails = !showAddDetails" class="text-[12px] font-bold text-slate-400 px-1 flex items-center group">
                                                <svg :class="{'rotate-180': showAddDetails}" class="w-4 h-4 mr-1 text-slate-300 transition-transform group-hover:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="2.5" /></svg>
                                                <span class="opacity-50">內容物/細項</span>
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <!-- Staged Contents List (Plain display as requested) -->
                                    <div v-if="stagedContents.length > 0" class="px-2 mb-3 space-y-1">
                                        <div v-for="(sc, sci) in stagedContents" :key="sci" class="text-[15px] text-slate-700 font-bold flex items-center justify-between group">
                                            <span>+ {{ sc.name }}{{ sc.details ? ' : ' + sc.details : '' }}{{ sc.remarks?.trim() ? ' (' + sc.remarks.trim() + ')' : '' }}</span>
                                            <button @click="stagedContents.splice(sci, 1)" class="text-slate-300 hover:text-red-400 opacity-0 group-hover:opacity-100 transition-all">×</button>
                                        </div>
                                    </div>

                                    <div v-if="showAddDetails" class="space-y-3 animate-fade-in mt-1">
                                        <div class="grid grid-cols-12 gap-2.5 items-end">
                                            <div :class="(isSpecialInstrument(newItemSubName) || newItemSubName?.includes('由')) ? 'col-span-12' : 'col-span-9'" class="space-y-1 relative">
                                                <div class="text-[12px] text-slate-400 font-bold px-1 mb-0.5 text-left">內容物名稱</div>
                                                <div class="border border-blue-100/50 rounded-xl bg-blue-50/40 px-4 py-[10px] flex items-center transition-all focus-within:border-blue-300 relative">
                                                    <input v-model="newItemSubName" @input="activeSubTreasureDropdownId = 'sub'"
                                                           class="w-full bg-transparent border-none outline-none shadow-none text-[17px] font-black text-slate-900 focus:ring-0 text-left placeholder-sky-400 placeholder:text-[17px] placeholder:font-black" 
                                                           placeholder="輸入法寶名稱">
                                                    <div class="flex items-center space-x-2 pr-1">
                                                        <button @click.stop="activeSubTreasureDropdownId = (activeSubTreasureDropdownId === 'sub' ? null : 'sub')" class="p-1 text-slate-300 hover:text-indigo-500">
                                                            <svg class="w-5 h-5" :class="activeSubTreasureDropdownId === 'sub' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                                        </button>
                                                        <button v-if="newItemSubName" @click="newItemSubName = ''" class="text-slate-300 hover:text-red-400 active:scale-95 transition-all">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                                        </button>
                                                        <button @click="stageContent" class="text-red-500 text-[32px] font-light leading-none active:scale-90 transition-all"> + </button>
                                                    </div>
                                                </div>
                                                <!-- Custom Sub-Treasure Dropdown -->
                                                <div v-if="activeSubTreasureDropdownId === 'sub'" class="absolute left-0 top-full mt-2 w-full bg-white rounded-3xl shadow-[0_20px_50px_rgba(0,0,0,0.2)] border border-slate-100 z-[600] overflow-hidden p-1.5 animate-fade-in max-h-[300px] overflow-y-auto custom-scrollbar">
                                                    <div v-for="name in uniqueTreasureNames.filter(n => !newItemSubName || n.toLowerCase().includes(newItemSubName.toLowerCase()))" :key="'sub'+name" 
                                                         @click.stop="newItemSubName = name; activeSubTreasureDropdownId = null" 
                                                         class="px-5 h-[38px] flex items-center rounded-2xl hover:bg-indigo-50 font-black text-[17px] text-slate-900 active:bg-indigo-100 transition-all cursor-pointer">
                                                        {{ name }}
                                                    </div>
                                                </div>
                                                <div v-if="newItemSubName === '令牌' || newItemSubName === '令牌 '" class="mt-3 flex flex-wrap gap-2 px-1 animate-fade-in">
                                                    <button v-for="t in ['太令令牌', '極令令牌', '道令令牌', '元令令牌', '靈令令牌', '玉皇令令牌', '皇令牌', '龍令令牌', '王令令牌']" 
                                                            :key="t"
                                                            @click="newItemSubName = t"
                                                            class="px-4 py-[10px] bg-blue-50 text-blue-600 rounded-xl text-[17px] font-black border border-blue-100 active:scale-95 transition-all">
                                                        {{ t }}
                                                    </button>
                                                </div>
                                            </div>

                                            <div v-if="magicItemSubCategory === 'default' && !(isSpecialInstrument(newItemSubName) || newItemSubName?.includes('由'))" class="col-span-3 space-y-1.5">
                                                <div class="text-[13px] text-slate-400 font-bold px-1 text-center select-none">天份</div>
                                                <div class="border border-slate-400 rounded-xl bg-blue-50/40 overflow-hidden flex items-center px-1 transition-all py-[10px]">
                                                    <input v-model="newItemDetailsExtraDays" list="num-list"
                                                           class="w-full bg-transparent border-none text-[17px] font-black text-slate-900 focus:ring-0 outline-none text-center placeholder:text-[17px]" 
                                                           placeholder="0">
                                                    <span class="text-slate-400 font-black mr-0.5 shrink-0 text-[12px]">天</span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Specialized Sub-Inputs -->
                                        <div v-if="magicItemSubCategory !== 'default'" class="space-y-2.5">
                                            <div v-if="magicItemSubCategory === '三光金丹'" class="grid grid-cols-4 gap-2">
                                            <div class="space-y-1">
                                                <div class="text-[11px] text-slate-400 font-bold px-2">日</div>
                                                <div class="border border-slate-100 rounded-2xl bg-slate-50/20 flex items-center px-3 py-[10px]">
                                                    <input v-model="newItemSubSun" list="num-list" class="w-full bg-transparent border-none text-[16px] font-bold text-slate-900 focus:ring-0 outline-none text-center" placeholder="0">
                                                    <span class="text-slate-400 font-bold ml-0.5 shrink-0 text-[11px]">粒</span>
                                                </div>
                                            </div>
                                            <div class="space-y-1">
                                                <div class="text-[11px] text-slate-400 font-bold px-2">月</div>
                                                <div class="border border-slate-100 rounded-2xl bg-slate-50/20 flex items-center px-3 py-[10px]">
                                                    <input v-model="newItemSubMoon" list="num-list" class="w-full bg-transparent border-none text-[16px] font-bold text-slate-900 focus:ring-0 outline-none text-center" placeholder="0">
                                                    <span class="text-slate-400 font-bold ml-0.5 shrink-0 text-[11px]">粒</span>
                                                </div>
                                            </div>
                                            <div class="space-y-1">
                                                <div class="text-[11px] text-slate-400 font-bold px-2">光</div>
                                                <div class="border border-slate-100 rounded-2xl bg-slate-50/20 flex items-center px-3 py-[10px]">
                                                    <input v-model="newItemSubLight" list="num-list" class="w-full bg-transparent border-none text-[16px] font-bold text-slate-900 focus:ring-0 outline-none text-center" placeholder="0">
                                                    <span class="text-slate-400 font-bold ml-0.5 shrink-0 text-[11px]">粒</span>
                                                </div>
                                            </div>
                                            <div class="space-y-1">
                                                <div class="text-[11px] text-slate-400 font-bold px-2">天份</div>
                                                <div class="border border-slate-100 rounded-2xl bg-slate-50/20 flex items-center px-3 py-[10px]">
                                                    <input v-model="newItemDetailsExtraDays" list="num-list" class="w-full bg-transparent border-none text-[16px] font-bold text-slate-900 focus:ring-0 outline-none text-center" placeholder="0">
                                                    <span class="text-slate-400 font-bold ml-0.5 shrink-0 text-[11px]">天</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div v-else-if="magicItemSubCategory === '金丹'" class="grid grid-cols-3 gap-2">
                                                <div class="space-y-1">
                                                    <div class="text-[11px] text-slate-400 font-bold px-2">吃</div>
                                                    <div class="border border-slate-100 rounded-2xl bg-slate-50/20 flex items-center px-3 py-[10px]">
                                                        <input v-model="newItemSubTimes" list="num-list" class="w-full bg-transparent border-none text-[16px] font-bold text-slate-900 focus:ring-0 outline-none text-center" placeholder="0">
                                                        <span class="text-slate-400 font-bold ml-1 shrink-0 text-[13px]">粒</span>
                                                    </div>
                                                </div>
                                                <div class="space-y-1">
                                                    <div class="text-[11px] text-slate-400 font-bold px-2">洗</div>
                                                    <div class="border border-slate-100 rounded-2xl bg-slate-50/20 flex items-center px-3 py-[10px]">
                                                        <input v-model="newItemSubHours" list="num-list" class="w-full bg-transparent border-none text-[16px] font-bold text-slate-900 focus:ring-0 outline-none text-center" placeholder="0">
                                                        <span class="text-slate-400 font-bold ml-1 shrink-0 text-[13px]">粒</span>
                                                    </div>
                                                </div>
                                                <div class="space-y-1">
                                                    <div class="text-[11px] text-slate-400 font-bold px-2">天份</div>
                                                    <div class="border border-slate-100 rounded-2xl bg-slate-50/20 flex items-center px-3 py-[10px]">
                                                        <input v-model="newItemDetailsExtraDays" list="num-list" class="w-full bg-transparent border-none text-[16px] font-bold text-slate-900 focus:ring-0 outline-none text-center" placeholder="0">
                                                        <span class="text-slate-400 font-bold ml-1 shrink-0 text-[13px]">天</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div v-else-if="magicItemSubCategory === '符令' || magicItemSubCategory === '太令'" class="space-y-2.5">
                                                <div class="grid grid-cols-2 gap-2">
                                                    <div class="space-y-1">
                                                        <div class="text-[11px] text-slate-400 font-bold px-2">尺寸</div>
                                                        <div class="border border-slate-100 rounded-2xl bg-slate-50/20 flex items-center px-4 py-[10px]">
                                                            <input v-model="newItemSubSize" class="w-full bg-transparent border-none text-[17px] font-black text-slate-900 focus:ring-0 outline-none text-center placeholder:text-[17px]" placeholder="尺寸">
                                                        </div>
                                                    </div>
                                                    <div class="space-y-1">
                                                        <div class="text-[11px] text-slate-400 font-bold px-2">天數</div>
                                                        <div class="border border-slate-100 rounded-2xl bg-slate-50/20 flex items-center px-3 py-[10px]">
                                                            <input v-model="newItemDetailsExtraDays" list="num-list" class="w-full bg-transparent border-none text-[17px] font-black text-slate-900 focus:ring-0 outline-none text-center placeholder:text-[17px]" placeholder="天份">
                                                            <span class="text-slate-400 font-black ml-1 shrink-0 text-[13px]">天份</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                    <div class="border border-slate-100 rounded-2xl bg-slate-50/20 px-3 py-[10px] flex items-center relative">
                                                        <input v-model="newItemSubPractitioner" @input="activeSubPractitionerDropdownId = 'subFulingPract'" @focus="activeSubPractitionerDropdownId = 'subFulingPract'" class="w-full bg-transparent border-none text-[17px] font-black text-slate-900 focus:ring-0 outline-none text-left placeholder:text-[17px]" placeholder="開立人 (法號)...">
                                                        <button @click.stop="activeSubPractitionerDropdownId = (activeSubPractitionerDropdownId === 'subFulingPract' ? null : 'subFulingPract')" class="p-1 text-slate-900 opacity-60 hover:text-indigo-500 hover:opacity-100">
                                                            <svg class="w-5 h-5" :class="activeSubPractitionerDropdownId === 'subFulingPract' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                                        </button>
                                                        <div v-if="activeSubPractitionerDropdownId === 'subFulingPract'" class="absolute left-0 top-full mt-2 w-full bg-white rounded-3xl shadow-[0_20px_50px_rgba(0,0,0,0.2)] border border-slate-100 z-[600] overflow-hidden p-1.5 animate-fade-in max-h-[300px] overflow-y-auto custom-scrollbar">
                                                            <div v-for="dn in dharmaNames.filter(d => !newItemSubPractitioner || d.name.includes(newItemSubPractitioner))" :key="'spsub'+dn.id" 
                                                                 @click.stop="newItemSubPractitioner = dn.name; activeSubPractitionerDropdownId = null" 
                                                                 class="px-5 h-[38px] flex items-center rounded-2xl hover:bg-indigo-50 font-black text-[17px] text-slate-900 active:bg-indigo-100 transition-all cursor-pointer">
                                                                {{ dn.name }}
                                                            </div>
                                                        </div>
                                                    </div>
                                            </div>
                                            <div v-else-if="magicItemSubCategory === '香環'" class="grid grid-cols-2 gap-2">
                                                <div class="border border-slate-100 rounded-2xl bg-slate-50/20 flex items-center px-4 py-[10px]">
                                                    <input v-model="newItemDetailsExtraDays" list="num-list" class="w-full bg-transparent border-none text-[17px] font-black text-slate-900 focus:ring-0 outline-none text-center placeholder:text-[17px]" placeholder="個數">
                                                    <span class="text-slate-400 font-black ml-1 shrink-0 text-[13px]">個</span>
                                                </div>
                                                <div class="border border-slate-100 rounded-2xl bg-slate-50/20 flex items-center px-4 py-[10px]">
                                                    <input v-model="newItemSubSheets" list="num-list" class="w-full bg-transparent border-none text-[17px] font-black text-slate-900 focus:ring-0 outline-none text-center placeholder:text-[17px]" placeholder="盒數">
                                                    <span class="text-slate-400 font-black ml-1 shrink-0 text-[13px]">盒</span>
                                                </div>
                                            </div>
                                            <div v-else-if="magicItemSubCategory === '福祿香'" class="grid grid-cols-2 gap-2">
                                                <div class="border border-slate-100 rounded-2xl bg-slate-50/20 flex items-center px-4 py-[10px]">
                                                    <input v-model="newItemDetailsExtraDays" list="num-list" class="w-full bg-transparent border-none text-[17px] font-black text-slate-900 focus:ring-0 outline-none text-center placeholder:text-[17px]" placeholder="0">
                                                    <span class="text-slate-400 font-black ml-1 shrink-0 text-[13px]">根</span>
                                                </div>
                                                <div class="border border-slate-100 rounded-2xl bg-slate-50/20 flex items-center px-4 py-[10px]">
                                                    <input v-model="newItemSubSheets" list="num-list" class="w-full bg-transparent border-none text-[17px] font-black text-slate-900 focus:ring-0 outline-none text-center placeholder:text-[17px]" placeholder="0">
                                                    <span class="text-slate-400 font-black ml-1 shrink-0 text-[13px]">包</span>
                                                </div>
                                            </div>
                                        </div>

                                            
                                            <!-- SUB-ITEM INSTRUMENT SPECIAL CASE (Always accessible if detected) -->
                                            <div v-if="isSpecialInstrument(newItemSubName) || newItemSubName?.includes('由')" class="mt-4 space-y-4">
                                                <!-- 1. Practitioner -->
                                                <div class="space-y-1.5">
                                                    <div class="text-[13px] text-slate-400 font-bold px-1 text-left select-none">執法</div>
                                                    <div class="relative group">
                                                        <div class="border border-blue-100/50 rounded-xl bg-blue-50/40 px-4 h-[52px] flex items-center transition-all focus-within:border-blue-300 relative">
                                                            <input v-model="newItemSubPractitioner" @input="activeSubPractitionerDropdownId = 'subPract2'" @focus="activeSubPractitionerDropdownId = 'subPract2'" class="w-full bg-transparent border-none text-[17px] font-black text-slate-900 focus:ring-0 outline-none text-left placeholder-sky-400 placeholder:text-[17px]" placeholder="輸入法號...">
                                                            <button @click.stop="activeSubPractitionerDropdownId = (activeSubPractitionerDropdownId === 'subPract2' ? null : 'subPract2')" class="p-1 text-slate-900 opacity-60 hover:text-indigo-500 hover:opacity-100">
                                                                <svg class="w-5 h-5" :class="activeSubPractitionerDropdownId === 'subPract2' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                                            </button>
                                                            <div v-if="activeSubPractitionerDropdownId === 'subPract2'" class="absolute left-0 top-full mt-2 w-full bg-white rounded-3xl shadow-[0_20px_50px_rgba(0,0,0,0.2)] border border-slate-100 z-[600] overflow-hidden p-1.5 animate-fade-in max-h-[300px] overflow-y-auto custom-scrollbar">
                                                                <div v-for="dn in dharmaNames.filter(d => !newItemSubPractitioner || d.name.includes(newItemSubPractitioner))" :key="'sp2'+dn.id" 
                                                                     @click.stop="newItemSubPractitioner = dn.name; activeSubPractitionerDropdownId = null" 
                                                                     class="px-5 h-[38px] flex items-center rounded-2xl hover:bg-indigo-50 font-black text-[17px] text-slate-900 active:bg-indigo-100 transition-all cursor-pointer">
                                                                    {{ dn.name }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- 2. Instrument Name - Only show if the name is generic -->
                                                <div v-if="(newItemSubName === '令牌' || newItemSubName === '法器' || newItemSubName?.includes('由')) && newItemSubName !== '皇令牌' && !['極令令牌', '元令令牌', '道令令牌', '靈令令牌', '玉皇令令牌', '龍令令牌', '王令令牌', '太令令牌'].some(k => newItemSubName === k)" class="space-y-1.5">
                                                    <div class="text-[13px] text-slate-400 font-bold px-1 text-left select-none">法器名稱</div>
                                                    <div class="border border-blue-100/50 rounded-xl bg-blue-50/40 px-4 h-[52px] flex items-center transition-all focus-within:border-blue-300">
                                                        <input v-model="newItemSubInstrumentName" 
                                                               list="instrument-list" 
                                                               class="w-full bg-transparent border-none text-[17px] font-black text-slate-900 focus:ring-0 outline-none text-left placeholder-sky-400 placeholder:text-[17px]" 
                                                               placeholder="使用的法器...">
                                                    </div>
                                                </div>
                                                <!-- 3. Body Part -->
                                                <div class="space-y-1.5">
                                                    <div class="text-[13px] text-slate-400 font-bold px-1 text-left select-none">清煞部位</div>
                                                    <div class="border border-blue-100/50 rounded-xl bg-blue-50/40 px-4 h-[52px] flex items-center transition-all focus-within:border-blue-300">
                                                        <input v-model="newItemSubBodyPart" list="body-part-list" class="w-full bg-transparent border-none text-[17px] font-black text-slate-900 focus:ring-0 outline-none text-left placeholder-sky-400 placeholder:text-[17px]" placeholder="輸入部位...">
                                                    </div>
                                                </div>
                                            </div>



                                        <div class="mt-2 text-left">
                                            <button @click="showSubRemarks = !showSubRemarks" class="text-[11px] font-bold text-slate-400 flex items-center px-1">
                                                {{ showSubRemarks ? '隱藏細項備註' : '+ 加入細項備註' }}
                                            </button>
                                            <div v-if="showSubRemarks" class="mt-2 flex items-center animate-fade-in overflow-hidden">
                                                <div class="flex-1 border border-blue-100/50 rounded-2xl bg-blue-50/30 px-3 h-[64px] flex items-center overflow-hidden">
                                                    <input v-model="newItemRemarks"
                                                           class="w-full bg-transparent border-none text-[17px] font-black text-slate-900 focus:ring-0 text-left py-3 placeholder-sky-400 placeholder:text-[17px] placeholder:font-black" 
                                                           placeholder="加入內容物備註...">
                                                </div>
                                            </div>
                                        </div>
                                        






                                    </div>
                                </div>

                                <!-- Secondary Quick Add Bar (Mirrors the top header for better accessibility when form is long) -->
                                <div v-if="newItemName" class="mt-8 mb-4 px-1 py-4 flex items-center justify-between border-t-2 border-slate-50">
                                    <div class="flex items-center text-[22px] font-black text-slate-800">
                                        <span class="w-8 h-8 rounded-full bg-orange-900 text-slate-100 flex items-center justify-center text-[18px] mr-2.5 shrink-0">1</span>
                                        <span>錄入法寶 <span class="text-indigo-600">({{ stripMasterPrefix(getRecipientName({dharma_name_ids: form.dharma_name_ids, target_remarks: form.target_remarks})) }})</span></span>
                                    </div>
                                    <button @click="addNewItemQuickly" 
                                            :class="isAddingFlash ? 'text-emerald-500 scale-125' : 'text-red-500'"
                                            class="text-[36px] font-light leading-none active:scale-95 transition-all duration-300"> + </button>
                                </div>

                        <!-- History List (always visible, outside collapsible) -->
                        <div v-if="form.items.length > 0" class="mt-4 px-1 border-t border-slate-50 pt-4 mb-2">
                            <div class="text-[12px] font-bold text-slate-400 mb-2 flex items-center justify-between">
                                <span>已加入記錄 ({{ form.items.length }})</span>
                                <span class="text-[10px] opacity-50">最下方為最新</span>
                            </div>
                            <div class="space-y-3 pr-1">
                                <div v-for="(subItems, gName, gIdx) in groupItems(form.items)" :key="gIdx" class="bg-white border border-slate-100 p-3.5 rounded-2xl shadow-sm space-y-2 text-left">
                                    <div class="text-[16px] font-black text-slate-900 leading-tight">
                                        {{ gIdx + 1 }}. {{ stripMasterPrefix(gName) }}{{ getMainDetails(subItems) ? ' : ' + getMainDetails(subItems) : '' }}
                                    </div>
                                    <div class="space-y-2.5">
                                        <div v-for="item in subItems" :key="item.uid" class="flex items-start justify-between pl-3 border-l-2 border-indigo-50">
                                            <div class="flex-1">
                                                <div class="text-[14px] text-slate-600 font-bold flex items-start leading-tight">
                                                    <span class="text-indigo-400 mr-1.5 shrink-0">+ </span>
                                                    <span v-if="item.name && !shouldHideContentName(gName, item.name)" class="text-indigo-400 mr-1.5 shrink-0">{{ item.name }}</span>
                                                    <span>{{ item.details }} <span v-if="getCleanRemark(item.remarks || item.sub_name, item.details)" class="text-slate-400">({{ getCleanRemark(item.remarks || item.sub_name, item.details) }})</span></span>
                                                </div>
                                            </div>
                                            <button @click="removeMagicItem(item.uid)" class="ml-2 text-slate-200 hover:text-red-400 transition-all text-[18px] leading-none">×</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Footer Remarks (independent, always visible, multi-entry) -->
                        <div class="px-1 mt-4 mb-2">
                            <div class="text-[14px] font-black text-slate-400 tracking-[0.1em] uppercase px-1 mb-2">結尾備註</div>
                            <!-- Added entries list -->
                            <div v-if="footerRemarksList.length > 0" class="mb-2 space-y-1">
                                <div v-for="(r, ri) in footerRemarksList" :key="ri" class="flex items-center bg-indigo-50 rounded-2xl px-3 py-2">
                                    <span class="flex-1 text-[16px] font-bold text-slate-800">{{ r }}</span>
                                    <button @click="footerRemarksList.splice(ri, 1); syncFooterRemarks()" class="text-slate-300 hover:text-red-400 ml-2 text-[18px] leading-none transition-all">×</button>
                                </div>
                            </div>
                            <!-- Input + Add button -->
                            <div class="flex items-center border border-slate-400 rounded-2xl bg-white overflow-hidden shadow-sm">
                                <input v-model="newFooterRemark" list="remark-list" placeholder="例如：完畢..." @keyup.enter="addFooterRemark" class="flex-1 bg-transparent border-none text-[17px] font-black text-slate-900 focus:ring-0 outline-none px-4 py-3 placeholder-sky-400">
                                <button @click="addFooterRemark" class="px-4 py-3 text-indigo-500 font-black text-[22px] leading-none hover:text-indigo-700 transition-all">+</button>
                            </div>
                        </div>

                        <!-- Close Button -->
                        <div class="px-2 pb-10 flex justify-center">
                            <button @click.prevent="handleItemsDetailClose" 
                                    class="text-slate-400 font-bold text-[16px] active:scale-[0.98] transition-all">
                                離開視窗
                            </button>
                        </div>
                
                    </div>
                </div>

                        <div class="pt-8 border-t border-slate-100 flex flex-col space-y-5">
                            <!-- High-Fidelity Card Preview - Hidden per user request -->
                            <div v-if="false && (form.content.trim() || form.items.length > 0)" class="bg-white rounded-[28px] p-5 border border-slate-200 shadow-xl shadow-slate-100/50 space-y-4 animate-fade-in relative overflow-hidden">
                                <div class="absolute top-0 right-0 px-3 py-1 bg-indigo-500 text-white text-[10px] font-black uppercase tracking-widest rounded-bl-xl shadow-sm">預覽</div>
                                
                                <div class="flex flex-col border-l-4 border-indigo-500 pl-3">
                                    <span class="text-[12px] font-bold text-slate-400 uppercase tracking-tighter leading-none">{{ (form.date || '').replace(/-/g, '/') }}</span>
                                    <span class="text-[16px] font-black text-slate-900 leading-tight mt-1.5 pr-8">
                                        {{ (masterNameInput || form.master_name || '仙師') }}開示給：{{ getRecipientName({ dharma_name_ids: form.dharma_name_ids, target_remarks: form.target_remarks }) }}
                                    </span>
                                </div>

                                <!-- Recipient Detail Box (if group) -->
                                <div v-if="getFullRecipientList({ dharma_name_ids: form.dharma_name_ids })" class="text-[14px] font-bold text-indigo-600 bg-indigo-50/50 rounded-2xl px-4 py-2.5 border border-indigo-100/50 space-y-1">
                                    <div v-if="getFullRecipientList({ dharma_name_ids: form.dharma_name_ids }).groupName" class="flex items-center text-indigo-700">
                                        <span class="mr-2 text-[15px]">🏘️</span> 
                                        <span>群組：{{ getFullRecipientList({ dharma_name_ids: form.dharma_name_ids }).groupName }}</span>
                                    </div>
                                    <div v-if="getFullRecipientList({ dharma_name_ids: form.dharma_name_ids }).names.length > 0" class="flex items-start">
                                        <span class="mr-2 text-[15px] shrink-0">👥</span>
                                        <span class="opacity-80">人員：{{ getFullRecipientList({ dharma_name_ids: form.dharma_name_ids }).names.join(', ') }}</span>
                                    </div>
                                </div>

                                <div v-if="form.content?.trim()" class="text-[17px] text-black font-black leading-tight whitespace-pre-wrap px-1">
                                    {{ form.content.trim() }}
                                </div>

                                <div v-if="form.items.length > 0" class="space-y-3 pt-2 border-t border-slate-50">
                                    <div class="text-[12px] font-black text-slate-300 uppercase tracking-widest pl-1 mb-1">賜降：</div>
                                    <div v-for="(group, gName, gIdx) in groupedPendingItems" :key="gName" class="text-[16px] text-slate-700 font-black flex flex-col px-1">
                                        <div class="flex items-center">
                                            <span class="text-indigo-400 mr-2 shrink-0">💎</span>
                                            {{ gIdx + 1 }}. {{ stripMasterPrefix(gName) }}{{ getMainDetails(group) ? ' : ' + getMainDetails(group) : '' }}
                                        </div>
                                        <div v-if="group.some(m => m.name || m.sub_name)" class="pl-7 space-y-1 mt-1">
                                            <div v-for="m in group.filter(sm => sm.name || sm.sub_name)" :key="m.uid" class="text-[14px] text-slate-400 font-bold flex items-center">
                                                <span class="mr-1.5 opacity-50">+</span> {{ m.name }}{{ m.details ? ' : ' + m.details : '' }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="fixed bottom-[7vh] left-0 right-0 px-6 py-2 pb-1 bg-white/90 backdrop-blur-md border-t border-slate-100 z-[520] shadow-[0_-10px_30px_rgba(0,0,0,0.05)]">
                                <div class="grid grid-cols-2 gap-3">
                                    <button @click="handleItemsDetailClose(true)" class="w-full bg-blue-50/50 text-blue-600 border border-blue-100 rounded-[24px] py-[10px] active:scale-95 text-[17px] font-black leading-tight flex flex-col items-center justify-center">
                                        <span>完成並新增</span>
                                        <span class="text-[11px] opacity-60">下一位人員</span>
                                    </button>
                                    <button @click.prevent="handleItemsDetailClose('save')" :disabled="saving" class="w-full bg-[#FFB266] text-white rounded-[24px] py-[10px] active:scale-95 disabled:opacity-50 text-[19px] font-black shadow-xl shadow-slate-900/20">
                                        確認存檔
                                    </button>
                                </div>
                            </div>
                            <button v-if="form.items.length === 0" @click="handleItemsDetailClose(false)" class="w-full text-slate-400 py-3 text-[14px] font-bold">
                                (無降寶，直接返回列表)
                            </button>
                        </div>
                        <div class="flex justify-center">
                            <p class="text-[14px] text-slate-400 mt-4 font-medium px-6 text-center leading-relaxed">※ 保存後將同時儲存對象、仙師及所有開示與降寶內容。</p>
                        </div>


                    <!-- Bottom Navbar in Detail Mode for consistency -->
                    <mobile-navbar 
                        :can-back="true"
                        :show-action="false"
                        :action-active="false"
                        :search-active="false"
                        :can-more="false"
                        @back="itemsDetailMode = false"
                        @home="$emit('goHome')"
                        @action="() => {}"
                        @search="() => {}"
                        @more="() => {}"
                    />
                </div>

                <!-- Dharma Picker Modal -->
                <div v-if="showDharmaPicker" class="fixed inset-0 z-[400] bg-[#FFB266]/40 flex items-end justify-center sm:items-center animate-fade-in">
                    <div class="bg-white w-full h-[85vh] sm:h-[70vh] sm:max-w-xl rounded-t-[32px] sm:rounded-[32px] flex flex-col shadow-2xl overflow-hidden animate-slide-up">
                        <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between shrink-0">
                            <div>
                                <h3 class="text-[20px] font-bold text-slate-900">選擇對象</h3>
                                <p class="text-[14px] text-slate-400">可搜尋「法號」或「群組名稱」</p>
                            </div>
                            <button @click.prevent="showDharmaPicker = false" class="bg-slate-100 text-slate-500 w-8 h-8 rounded-full flex items-center justify-center active:scale-90 transition-transform">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2" /></svg>
                            </button>
                        </div>
                        
                        <div class="px-6 py-4 shrink-0 bg-slate-50/50">
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-300">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="2" /></svg>
                                </span>
                                <input v-model="pickerSearch" type="text" placeholder="輸入法號或群組名稱進行搜尋..." 
                                       class="w-full bg-white border border-slate-200 rounded-2xl py-3.5 pl-11 pr-4 text-[17px] text-slate-900 focus:ring-4 focus:ring-indigo-100 transition-all outline-none shadow-sm font-black placeholder:font-black">
                            </div>
                        </div>

                        <div class="flex-1 overflow-y-auto px-6 py-2 custom-scrollbar">
                            <div class="mb-4 mt-2">
                                <div class="text-[14px] text-slate-400 font-bold px-1 mb-3 tracking-widest uppercase flex items-center">
                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" stroke-width="2"/></svg>
                                    {{ pickerSearch ? '法號搜尋結果' : '所有法號' }}
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <button v-for="dn in filteredPickerResults" :key="dn.id" 
                                            @click.prevent="toggleDharmaName(dn.id)"
                                            :class="form.dharma_name_ids.includes(dn.id) ? 'bg-indigo-50 border-indigo-200 text-indigo-700 shadow-sm' : 'bg-slate-50 border-slate-50 text-slate-600'"
                                            class="flex items-center px-4 py-3.5 rounded-2xl border transition-all text-[17px] font-black active:scale-[0.98]">
                                        <div class="w-5 h-5 rounded-md border flex items-center justify-center mr-3 shrink-0"
                                             :class="form.dharma_name_ids.includes(dn.id) ? 'bg-indigo-600 border-indigo-600' : 'bg-white border-slate-200 shadow-sm'">
                                            <svg v-if="form.dharma_name_ids.includes(dn.id)" class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" /></svg>
                                        </div>
                                        <span class="truncate text-[17px]">{{ dn.name }}</span>
                                    </button>
                                </div>
                            </div>

                            <div v-if="filteredGroups.length > 0" class="mb-6">
                                <div class="text-[14px] text-indigo-500 font-bold px-1 mb-3 tracking-widest uppercase flex items-center">
                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" stroke-width="2"/></svg>
                                    群組搜尋結果
                                </div>
                                <div class="space-y-2">
                                    <div v-for="group in filteredGroups" :key="group.id" 
                                         class="border border-slate-100 rounded-2xl overflow-hidden bg-white shadow-sm">
                                        <div @click.prevent="toggleGroupAccordion(group.id)" 
                                             class="flex items-center justify-between px-4 py-4 cursor-pointer active:bg-slate-50">
                                            <div class="flex items-center space-x-3">
                                                <div @click.stop.prevent="toggleGroupSelection(group)" 
                                                     class="w-6 h-6 rounded-lg border flex items-center justify-center transition-all shadow-sm"
                                                     :class="isGroupFullySelected(group) ? 'bg-indigo-600 border-indigo-600' : 'bg-white border-slate-200'">
                                                    <svg v-if="isGroupFullySelected(group)" class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" /></svg>
                                                </div>
                                                <span class="text-[17px] font-black text-slate-800" :class="isGroupFullySelected(group) ? 'text-indigo-600' : 'text-slate-800'">{{ group.name }}</span>
                                                <span class="text-[14px] bg-slate-50 text-slate-400 px-2.5 py-1 rounded-full font-bold">{{ group.dharma_names.length }}人</span>
                                            </div>
                                            <svg :class="expandedGroupPicker === group.id ? 'rotate-180' : ''" class="w-4 h-4 text-slate-300 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" /></svg>
                                        </div>
                                        <div v-if="expandedGroupPicker === group.id || pickerSearch" class="bg-indigo-50/20 px-4 py-3 border-t border-indigo-50/50 grid grid-cols-2 gap-2 animate-fade-in">
                                            <button v-for="member in group.dharma_names" :key="member.id" 
                                                    @click.prevent="toggleDharmaName(member.id)"
                                                    class="flex items-center p-2 rounded-xl text-[17px] transition-all bg-white shadow-sm"
                                                    :class="form.dharma_name_ids.includes(member.id) ? 'text-slate-800 font-black border border-indigo-200' : 'text-slate-900 border border-transparent font-black'">
                                                <div class="w-2.5 h-2.5 rounded-full mr-2 shrink-0 shadow-sm" :class="form.dharma_name_ids.includes(member.id) ? 'bg-indigo-500' : 'bg-slate-200'"></div>
                                                <span class="truncate">{{ member.name }}</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="p-6 border-t border-slate-100 flex items-center space-x-3 shrink-0 bg-white">
                            <button @click.prevent="form.dharma_name_ids = []" class="flex-1 py-4 bg-slate-200 text-slate-700 rounded-2xl font-bold active:scale-95 transition-all text-[17px]">全部清除</button>
                            <button @click.prevent="showDharmaPicker = false" class="flex-1 py-4 bg-[#4A3728] text-white rounded-2xl font-bold active:scale-95 transition-all text-[17px] shadow-lg shadow-slate-200/50">確認選擇</button>
                        </div>
                    </div>
                </div>

                <!-- Group Members Detail Modal -->
                <div v-if="showGroupMembersModal && activeModalGroup" class="fixed inset-0 z-[600] flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm animate-fade-in">
                    <div class="bg-white w-full max-w-lg rounded-[32px] shadow-2xl flex flex-col overflow-hidden animate-slide-up max-h-[80vh]">
                        <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between shrink-0 bg-indigo-50/30">
                            <div>
                                <h3 class="text-[20px] font-bold text-slate-900 tracking-tight">群組成員：{{ activeModalGroup?.name }}</h3>
                                <p class="text-[14px] text-slate-400 font-bold">共 {{ activeModalGroup?.dharma_names?.length || 0 }} 位人員</p>
                            </div>
                            <button @click.prevent="showGroupMembersModal = false" class="bg-white text-slate-400 w-8 h-8 rounded-full flex items-center justify-center shadow-sm active:scale-90 transition-transform">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" /></svg>
                            </button>
                        </div>
                        <div class="flex-1 overflow-y-auto p-4 custom-scrollbar">
                            <div v-if="activeModalGroupGrouped.length > 0" class="grid grid-cols-2 gap-x-3 gap-y-6">
                                <div v-for="pg in activeModalGroupGrouped" :key="pg.palaceName" class="space-y-2">
                                    <div class="text-[14px] font-black text-indigo-600 uppercase tracking-tighter flex items-center px-1 border-b border-indigo-50 pb-1">
                                        <span class="w-2 h-2 bg-indigo-500 rounded-full mr-1.5"></span>
                                        {{ pg.palaceName }}
                                    </div>
                                    <div class="grid grid-cols-1 gap-2">
                                        <div v-for="member in pg.members" :key="member.id" 
                                             class="flex items-center px-3 py-2.5 bg-slate-50/50 rounded-xl border border-slate-100 shadow-sm">
                                            <button @click.stop="toggleDharmaName(member.id)" class="mr-2 text-slate-300 hover:text-red-500 transition-colors shrink-0">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                            </button>
                                            <span class="text-[16px] font-black text-slate-800 truncate">{{ stripMasterPrefix(member.name) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div v-else-if="activeModalGroup" class="grid grid-cols-2 gap-3">
                                <div v-for="member in activeModalGroup.dharma_names" :key="member.id" 
                                     class="flex items-center px-4 py-3 bg-slate-50 rounded-2xl border border-slate-100">
                                    <button @click.stop="toggleDharmaName(member.id)" class="mr-2 text-slate-300 hover:text-red-500 transition-colors shrink-0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    </button>
                                    <span class="text-[17px] font-black text-slate-800 truncate">{{ stripMasterPrefix(member.name) }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="p-6 border-t border-slate-100 bg-white">
                            <button @click.prevent="showGroupMembersModal = false" class="w-full py-[10px] bg-amber-100 text-amber-900 border border-amber-200 rounded-2xl font-bold text-[18px] active:scale-95 transition-all shadow-sm">
                                關閉預覽
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Palace Picker Modal -->
                <div v-if="showPalacePicker" class="fixed inset-0 z-[700] bg-indigo-900/60 backdrop-blur-sm flex items-center justify-center p-6 animate-fade-in" @click.self="showPalacePicker = false">
                    <div class="bg-white w-full max-w-lg rounded-[40px] shadow-2xl overflow-hidden flex flex-col animate-slide-up border border-white/20">
                        <div class="px-8 py-7 border-b border-slate-100 flex items-center justify-between bg-gradient-to-r from-indigo-50/30 to-white">
                            <div>
                                <h3 class="text-[24px] font-black text-slate-900 tracking-tight">各宮選擇</h3>
                                <p class="text-[14px] text-slate-400 font-bold mt-0.5">請選擇要指定的宮名</p>
                            </div>
                            <button @click="showPalacePicker = false" class="w-10 h-10 bg-slate-50 text-slate-400 rounded-full flex items-center justify-center hover:bg-slate-100 transition-all active:scale-90">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" /></svg>
                            </button>
                        </div>
                        <div class="p-8 overflow-y-auto max-h-[60vh] custom-scrollbar">
                            <div class="grid grid-cols-2 gap-4">
                                <button v-for="p in palaceOrder" 
                                        :key="p"
                                        @click="selectPalaceGroup(p)"
                                        class="px-2 py-5 bg-slate-50 border border-slate-100 rounded-[24px] text-[17px] font-black text-slate-800 hover:bg-indigo-600 hover:text-white hover:border-indigo-600 transition-all active:scale-95 flex items-center justify-center shadow-sm hover:shadow-indigo-200 hover:shadow-lg whitespace-nowrap">
                                    <svg class="w-4 h-4 mr-2 text-slate-300 group-hover:text-white/60 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    {{ p }}
                                </button>
                            </div>
                        </div>
                        <div class="p-8 bg-slate-50/50 border-t border-slate-100">
                            <button @click="selectPalaceGroup('各宮')" 
                                    class="w-full py-5 bg-indigo-600 text-white rounded-[24px] text-[20px] font-black shadow-xl shadow-indigo-100 active:scale-[0.98] transition-all flex items-center justify-center group">
                                <span class="mr-2">✨</span>
                                選擇全部 (各宮)
                                <svg class="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M13 7l5 5m0 0l-5 5m5-5H6" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Main List View -->
                <div v-show="!addMode" 
                     class="pb-32 flex-1 overflow-y-auto bg-white" 
                     @click="focusedId = null; focusedDate = null; activeDropdownId = null" 
                     @scroll="handleScroll">
                    <div v-if="loading && visibleItems.length === 0" class="text-center py-12 text-slate-400 text-[20px] font-bold tracking-widest uppercase">載入紀錄中...</div>
                    <div v-else class="space-y-0 mt-0 min-h-full">
                        <!-- Date-Based Accordion: Grouping by Daily sessions -->
                        <template v-for="dateGroup in recordsByDate" :key="dateGroup.date">
                            <!-- Date Header: Collapsed by Default -->
                            <div v-show="focusedId === null || isDateOfFocused(dateGroup.date)"
                                 @click.stop="toggleDateExpand(dateGroup.date)" 
                                 class="px-5 py-4 bg-slate-50 border-b border-slate-300 flex items-center justify-between cursor-pointer active:bg-slate-100 sticky top-0 z-[10] shadow-sm">
                                 <div class="flex items-center">
                                     <span class="app-title font-outfit uppercase tracking-wider">{{ formatDate(dateGroup.date) }}</span>
                                     <span class="ml-3 px-2.5 py-0.5 bg-white border border-slate-200 rounded-full text-[12px] font-black font-outfit" style="color: #0f172a !important;">
                                         {{ dateGroup.items.length }} 則
                                     </span>
                                 </div>
                                 <svg :class="focusedDate === dateGroup.date ? 'rotate-180' : 'rotate-[-90deg]'" class="w-4 h-4 text-slate-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" /></svg>
                            </div>

                            <!-- List of Teachings for this Date -->
                            <div v-if="focusedDate === dateGroup.date" class="bg-white animate-fade-in">
                                <template v-for="(item, index) in dateGroup.items" :key="item.id">
                            <!-- EACH RECORD IS A SEPARATE SESSION FOLDER per user request -->
                             <div :id="'teaching-row-' + item.id"
                                  v-show="focusedId === null || focusedId === item.id"
                                  @click.stop="reorderMode ? null : toggleExpand(item.id)"
                                  class="py-[15px] px-[12px] flex flex-col cursor-pointer active:bg-slate-200 transition-colors bg-white border-b border-slate-300 shadow-sm"
                                  :class="[isSessionFocused(item) ? 'bg-slate-50 ring-2 ring-indigo-50/10' : '']">
                                
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center min-w-0 flex-1">
                                        <!-- Sequence Number / Reorder Input -->


                                        <svg v-if="!reorderMode" :class="focusedId == item.id ? '' : 'rotate-[-90deg]'" class="w-4 h-4 text-slate-400 mr-2 transition-transform shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" /></svg>
                                        <div class="flex flex-col min-w-0">
                                            <div v-if="false" class="text-[18px] mb-0.5 font-black font-outfit tracking-tighter" style="color: #0d0d0d !important; font-weight: 900 !important;">
                                                {{ (item.date || '').replace(/-/g, '/') }}
                                            </div>
                                            <div class="app-body font-bold leading-tight">
                                                {{ item.master?.name || item.master_name || '仙師' }}<span v-if="item.content?.trim()">開示</span>給：{{ getRecipientName(item) }}
                                            </div>
                                            <!-- Request 2: Content/Item Summary in List Header -->
                                            <div v-if="!isSessionFocused(item)" class="mt-0.5 text-[17px] font-normal truncate font-outfit" style="color: #0f172a !important;">
                                                <span v-if="item.content">{{ item.content.split('\n')[0] }}</span>
                                                <span v-else-if="item.items?.length > 0">{{ item.items.map(i => i.treasure_name || i.name).join(', ') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-2 shrink-0 ml-2 translate-y-[-10px]">
                                        <div class="relative">
                                            <button @click.stop="activeDropdownId = activeDropdownId === item.id ? null : item.id" class="w-8 h-8 flex items-center justify-center text-slate-300 hover:text-slate-500">
                                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM18 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                                            </button>
                                            <div v-if="activeDropdownId === item.id" class="absolute right-0 top-full mt-2 w-48 bg-white rounded-3xl shadow-[0_20px_50px_rgba(0,0,0,0.15)] border border-slate-50 z-50 overflow-hidden p-1.5 focus:outline-none">
                                                <div class="flex flex-col space-y-1">
                                                    <button @click.stop="toggleExpand(item.id); activeDropdownId = null" class="w-full px-4 py-2 text-left flex items-center hover:bg-slate-50 rounded-2xl transition-all">
                                                        <span class="text-[17px] font-black text-slate-900">{{ isSessionFocused(item) ? '收起清單' : '展開清單' }}</span>
                                                    </button>
                                                    <button @click.stop="handleMenuEdit(item)" class="w-full px-4 py-2 text-left flex items-center hover:bg-slate-50 rounded-2xl transition-all">
                                                        <span class="text-[17px] font-black text-slate-900">修改</span>
                                                    </button>
                                                    <button @click.stop="copyAsTextFile(item); activeDropdownId = null" class="w-full px-4 py-2 text-left flex items-center hover:bg-slate-50 rounded-2xl transition-all">
                                                        <span class="text-[17px] font-black text-slate-900">複製貼 LINE</span>
                                                    </button>
                                                    <button @click.stop="downloadTeaching(item); activeDropdownId = null" class="w-full px-4 py-2 text-left flex items-center hover:bg-slate-50 rounded-2xl transition-all">
                                                        <span class="text-[17px] font-black text-slate-900">下載檔案</span>
                                                    </button>
                                                    <button @click.stop="deleteItem(item.id); activeDropdownId = null" class="w-full px-4 py-2 text-left flex items-center hover:bg-rose-50 rounded-2xl transition-all text-rose-500">
                                                        <span class="text-[17px] font-black">刪除</span>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Expanded Content (Linked Expansion) -->
                                <div v-if="isSessionFocused(item)" @click.stop class="px-5 pb-5 animate-fade-in">
                                    <!-- View Mode (Read-Only) -->
                                    <div v-if="inlineEditingId !== item.id" class="space-y-4">
                                        <!-- Personnel Detail Box: Toggleable for high-density -->
                                        <div v-if="getFullRecipientList(item)" class="mt-1 space-y-2">
                                            <button @click.stop="toggleRecipientDetails(item.id)" 
                                                    class="flex items-center space-x-2 text-indigo-600 hover:text-indigo-800 transition-colors group">
                                                <div class="w-6 h-6 bg-indigo-50 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                                                    <svg class="w-3.5 h-3.5" :class="showRecipientDetails[item.id] ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                                </div>
                                                <span class="text-[13px] font-black uppercase tracking-wider">點選查看對象詳情</span>
                                            </button>
                                            <div v-if="showRecipientDetails[item.id]" class="text-indigo-600 bg-indigo-50/50 rounded-2xl px-4 py-3 border border-indigo-100/50 space-y-1 animate-fade-in text-left">
                                                <div v-if="getFullRecipientList(item).groupName" class="app-body text-indigo-700 leading-tight">
                                                    {{ getFullRecipientList(item).groupName }}
                                                </div>
                                                <div v-if="getFullRecipientList(item).names.length > 0" class="app-title !text-indigo-600/70 leading-snug">
                                                    {{ getFullRecipientList(item).names.join(', ') }}
                                                </div>
                                                <div v-if="item.target_remarks" class="app-title !text-indigo-400 pt-1">
                                                    說明：{{ item.target_remarks }}
                                                </div>
                                            </div>
                                        </div>

                                        <div v-if="item.content?.trim()" class="app-body whitespace-pre-wrap">
                                            {{ item.content.trim() }}
                                        </div>

                                        <div v-if="item.items?.length > 0 && !item.content?.includes('賜降：')" class="mt-2">
                                            <div class="app-body font-bold mb-1">賜降：</div>
                                            <div class="space-y-0.5">
                                                <template v-for="(group, gName, gIdx) in groupItems(item.items)" :key="gName">
                                                        <div class="text-[17px] font-bold text-black">
                                                            {{ stripMasterPrefix(gName) }}
                                                            <template v-if="group.length === 1 && !group[0].name">
                                                                <span v-if="group[0].details || group[0].remarks || group[0].sub_name" class="ml-1">: {{ group[0].details }} <span v-if="group[0].remarks || group[0].sub_name" class="opacity-60">({{ group[0].remarks || group[0].sub_name }})</span></span>
                                                            </template>
                                                        </div>
                                                        <!-- Specialized Indented Layout: Show if multi-item or has name -->
                                                        <div class="space-y-0.5" v-if="group.length > 1 || (group[0] && group[0].name)">
                                                            <div v-if="group.length > 1 && getMainDetails(group)" 
                                                                 class="pl-6 text-[17px] text-black font-bold opacity-80">
                                                                {{ getMainDetails(group) }}
                                                            </div>
                                                            <div v-for="(m, midx) in group" :key="midx">
                                                                <div v-if="m.name || (group.length > 1 && getCleanRemark(m.remarks || m.sub_name, m.details))" class="pl-6 text-[17px] text-black font-bold opacity-80">
                                                                    <template v-if="m.name">
                                                                        <span v-if="isSpecialTreasure(m.name)">{{ stripMasterPrefix(m.name) }}</span>
                                                                        <span v-else>{{ stripMasterPrefix(m.name) }}{{ (m.details) ? ':' + m.details : '' }} <span v-if="getCleanRemark(m.remarks || m.sub_name, m.details)" class="text-[14px] text-slate-400">({{ getCleanRemark(m.remarks || m.sub_name, m.details) }})</span></span>
                                                                        <div v-if="m.details && isSpecialTreasure(m.name) && !getCleanRemark(m.remarks || m.sub_name, m.details)" class="pl-4 opacity-80">{{ m.details }}</div>
                                                                    </template>
                                                                </div>
                                                            </div>
                                                        </div>
                                                </template>
                                            </div>
                                        </div>

                                        <div v-if="item.items_footer_remarks?.trim()" class="app-body pt-1">
                                            {{ item.items_footer_remarks.trim() }}
                                        </div>

                                        <div class="pt-1 text-left" v-if="!hasFinishedSuffix(item)">
                                            <span class="tracking-widest font-bold" style="color: #0d0d0d !important; font-size: 17px;">完畢</span>
                                        </div>

                                    </div>

                                    <!-- Edit Mode (Interactive Modification Interface) -->
                                    <div v-if="inlineEditingId === item.id" class="pt-4 border-t-2 border-indigo-100 mt-4 space-y-4 animate-fade-in text-left">
                                        <!-- Header Boxes (Master & Date & Recipient) -->
                                        <div class="space-y-3">
                                            <div class="grid grid-cols-2 gap-3">
                                                <div v-if="false" class="space-y-1">
                                                    <label class="app-title block ml-1">開示日期</label>
                                                    <div class="relative flex items-center h-[48px]">
                                                        <input v-model="inlineEditData.date" type="text" placeholder="年/月/日 或 註記文字" 
                                                            class="w-full h-full rounded-xl border-2 border-slate-200 focus:border-indigo-500 focus:ring-0 pl-3 pr-10 bg-white app-body text-[16px] font-bold outline-none">
                                                        <button @click="activeDate = 'inlineEdit'" class="absolute right-2 text-slate-300 hover:text-indigo-600 transition-colors p-1">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="space-y-1">
                                                    <label class="app-title block ml-1">主講仙師</label>
                                                    <input v-model="inlineEditData.master_name" 
                                                           class="w-full rounded-xl border-2 border-slate-200 focus:border-indigo-500 focus:ring-0 px-3 py-2.5 bg-white"
                                                           placeholder="仙師名稱...">
                                                </div>
                                            </div>
                                            <div class="space-y-1">
                                                <label class="app-title block ml-1">對象 (開示給)</label>
                                                <input v-model="inlineEditData.target_name" 
                                                       class="w-full rounded-xl border-2 border-slate-200 focus:border-indigo-500 focus:ring-0 px-4 py-3 bg-white"
                                                       placeholder="輸入對象...">
                                            </div>
                                        </div>

                                        <!-- Content Box (Opening Content) -->
                                        <div class="space-y-1">
                                            <label class="app-title block ml-1">【開示內容】</label>
                                            <textarea v-model="inlineEditData.content" 
                                                      @paste="e => handleSmartPaste(e, inlineEditData, null)"
                                                      class="w-full rounded-xl border-2 border-slate-200 focus:border-indigo-500 focus:ring-0 p-4 bg-white"
                                                      rows="5"
                                                      placeholder="請輸入開示內容..."></textarea>
                                        </div>

                                        <!-- Items Boxes (Treasure Content) -->
                                        <div class="space-y-3">
                                            <label class="app-title block ml-1">【降寶內容】(可直接修改)</label>
                                            <div v-for="(m, midx) in inlineEditData.items" :key="midx" class="p-4 bg-slate-50 rounded-2xl border-2 border-slate-200 space-y-3 relative shadow-sm">
                                                <button @click.stop="inlineEditData.items.splice(midx, 1)" class="absolute top-2 right-2 p-1 text-rose-400 active:scale-90 transition-all">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" /></svg>
                                                </button>
                                                <div class="space-y-1 pr-8">
                                                    <label class="app-title block ml-1 uppercase">法寶名稱</label>
                                                    <input v-model="m.name" 
                                                           class="w-full rounded-xl border border-slate-300 px-3 py-2 bg-white"
                                                           placeholder="輸入法寶名稱">
                                                </div>
                                                <div class="grid grid-cols-2 gap-3">
                                                    <div class="space-y-1">
                                                        <label class="app-title block ml-1 uppercase">數據/天份</label>
                                                        <input v-model="m.details" 
                                                               class="w-full rounded-xl border border-slate-300 px-3 py-2 bg-white"
                                                               placeholder="例如：9天份">
                                                    </div>
                                                    <div class="space-y-1">
                                                        <label class="app-title block ml-1 uppercase">備註</label>
                                                        <input v-model="m.remarks" 
                                                               class="w-full rounded-xl border border-slate-300 px-3 py-2 bg-white"
                                                               placeholder="額外說明">
                                                    </div>
                                                </div>
                                            </div>
                                            <button @click.stop="inlineEditData.items.push({ name: '', details: '', remarks: '' })" 
                                                    class="w-full py-3 border-2 border-dashed border-indigo-200 rounded-2xl text-indigo-500 font-black text-[15px] hover:bg-indigo-50 active:scale-[0.99] transition-all bg-indigo-50/20">
                                                + 新增降寶項目
                                            </button>
                                        </div>

                                        <!-- Footer Remarks Box -->
                                        <div class="space-y-1">
                                            <label class="app-title block ml-1">【結尾備註】</label>
                                            <input v-model="inlineEditData.items_footer_remarks" 
                                                   class="w-full rounded-xl border-2 border-slate-200 focus:border-indigo-500 focus:ring-0 px-4 py-3 bg-white"
                                                   placeholder="例如：完畢...">
                                        </div>

                                        <!-- Final Action Buttons -->
                                        <div class="flex space-x-3 pt-6 pb-10">
                                            <button @click.stop="cancelInlineEdit" class="flex-1 h-[56px] rounded-2xl bg-slate-100 text-slate-600 font-black text-[16px] active:scale-95 transition-all">取消</button>
                                            <button @click.stop="saveInlineEdit" :disabled="saving" class="flex-1 h-[56px] rounded-2xl bg-[#FFB266] text-white font-black text-[19px] shadow-xl active:scale-95 transition-all">
                                                {{ saving ? '正在存檔...' : '確認修改' }}
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Read-Only View: Actions are tucked in the top right menu -->
                                </div>
                            </div>
                                </template>
                            </div>
                        </template>
                    </div>
                </div>



                <search-component v-if="showSearch" v-model="searchQuery" :show="showSearch" @close="showSearch = false" />
                <div v-if="distributionModal.show" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-[#FFB266]/60 backdrop-blur-sm animate-fade-in">
                    <div class="bg-white rounded-[32px] w-full max-w-sm overflow-hidden shadow-2xl animate-slide-up border border-white/20">
                        <div class="p-8 text-center space-y-6">
                            <div class="space-y-2">
                                <div class="text-[22px] font-black text-black tracking-tight leading-tight">偵測到不同的仙師開示</div>
                                <p class="text-[15px] text-slate-500 font-bold">內容包含：{{ distributionModal.detectedNames.join('、') }}</p>
                            </div>
                            
                            <div class="flex flex-col space-y-3 pt-2">
                                <!-- Primary Action: Distribute -->
                                <button @click="executeDistributionSave('distribute')" 
                                        class="w-full py-4 bg-[#FFB266] text-white rounded-2xl font-black text-[17px] active:scale-[0.98] transition-all shadow-lg">
                                    {{ distributionModal.detectedNames.length === 1 ? '存入「' + distributionModal.detectedNames[0] + '」資料夾' : '依內容自動分流儲存' }}
                                </button>
                                
                                <!-- Secondary Action: Keep in Current -->
                                <button @click="executeDistributionSave('keep')" 
                                        class="w-full py-4 bg-[#F1F5F9] text-black rounded-2xl font-black text-[17px] active:scale-[0.98] transition-all border border-slate-200">
                                    維持存入「{{ currentFolder?.name || '目前' }}」資料夾
                                </button>
                                
                                <button @click="distributionModal.show = false" 
                                        class="w-full py-3 text-slate-400 font-bold text-[15px] hover:text-slate-600 transition-colors">
                                    取消錄入
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Full Page Save Confirmation Overlay -->
                <div v-if="saveConfirmModal.show" class="fixed inset-0 z-[600] bg-white animate-fade-in flex flex-col font-sans text-left">
                    <!-- High-Density Header -->
                    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50 shrink-0">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-orange-100 text-orange-500 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="3" /></svg>
                            </div>
                            <div>
                                <h3 class="text-[19px] font-black text-slate-900 leading-tight">確認錄入資料</h3>
                                <p class="text-slate-400 font-bold text-[11px] uppercase tracking-widest">請核對以下內容是否正確</p>
                            </div>
                        </div>
                        <button @click="saveConfirmModal.show = false" class="p-2 text-slate-300 hover:text-slate-500">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" /></svg>
                        </button>
                    </div>
                    
                    <!-- High-Density Summary Content: Full Preview of All Records -->
                    <div class="flex-1 overflow-y-auto px-4 py-4 pb-32 custom-scrollbar bg-slate-50/30 space-y-4">
                        <div v-for="(item, bIdx) in saveConfirmModal.records" :key="bIdx" 
                             class="bg-white rounded-[28px] p-5 border border-slate-100 shadow-sm space-y-4 animate-fade-in text-left">
                            <div class="flex items-center justify-between mb-1">
                                <span v-if="saveConfirmModal.records.length > 1" class="w-8 h-8 bg-indigo-600 text-white rounded-full flex items-center justify-center font-black text-[14px] shadow-lg shadow-indigo-100">{{ bIdx + 1 }}</span>
                                <span class="text-[12px] font-black text-slate-300 uppercase tracking-[0.2em]">{{ (item.date || form.date || '').replace(/-/g, '/') }}</span>
                            </div>

                            <div class="flex flex-col border-l-4 border-indigo-500 pl-3">
                                <span class="text-[17px] font-black text-slate-900 leading-tight">
                                    {{ item.master_name }}<span v-if="item.content?.trim()">開示</span>給：{{ getRecipientName(item) }}
                                </span>
                            </div>

                            <!-- Personnel Detail Box: Toggleable for high-density -->
                            <div v-if="getFullRecipientList(item)" class="space-y-2">
                                <button @click="toggleRecipientDetails(item.id || bIdx)" 
                                        class="flex items-center space-x-2 text-indigo-600 hover:text-indigo-800 transition-colors group">
                                    <div class="w-6 h-6 bg-indigo-50 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                                        <svg class="w-3.5 h-3.5" :class="showRecipientDetails[item.id || bIdx] ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    </div>
                                    <span class="text-[13px] font-black uppercase tracking-wider">點選查看對象詳情</span>
                                </button>

                                <div v-if="showRecipientDetails[item.id || bIdx]" 
                                     class="text-indigo-600 bg-indigo-50/50 rounded-2xl px-4 py-2.5 border border-indigo-100/50 space-y-1 animate-fade-in text-left">
                                    <div v-if="getFullRecipientList(item).groupName" class="flex items-center text-indigo-700">
                                        <span class="mr-2 text-[15px]">🏘️</span> 
                                        <span>群組：{{ getFullRecipientList(item).groupName }}</span>
                                    </div>
                                    <div v-if="getFullRecipientList(item).names.length > 0" class="flex items-start">
                                        <span class="mr-2 text-[15px] shrink-0">👥</span>
                                        <span class="opacity-80">人員：{{ getFullRecipientList(item).names.join(', ') }}</span>
                                    </div>
                                    <div v-if="item.target_remarks" class="text-[12px] font-medium text-indigo-400 pt-1">
                                        說明：{{ item.target_remarks }}
                                    </div>
                                </div>
                            </div>

                            <div v-if="item.content?.trim()" class="text-[16px] text-black font-black leading-tight whitespace-pre-wrap px-1">
                                {{ item.content.trim() }}
                            </div>

                            <div v-if="item.items?.length > 0 && !item.content?.includes('賜降：')" class="pt-3 border-t border-slate-50 space-y-3">
                                <div class="text-[12px] font-black text-slate-300 uppercase tracking-widest pl-1">賜降：</div>
                                <div v-for="(group, gName, gIdx) in groupItems(item.items)" :key="gIdx" class="text-[15px] text-slate-800 font-bold flex flex-col px-1">
                                    <div class="flex items-center">
                                        {{ stripMasterPrefix(gName) }}{{ (group.length === 1 && !group[0].name) ? (getMergedContent(group[0].details, group[0].remarks || group[0].sub_name) ? ' : ' + getMergedContent(group[0].details, group[0].remarks || group[0].sub_name) : '') : (getMainDetails(group) ? ' : ' + getMainDetails(group) : '') }}
                                    </div>
                                    <div v-if="group.length > 1 || (group[0] && group[0].name)" class="pl-7 space-y-0.5 mt-1">
                                        <div v-for="m in group" :key="m.uid">
                                            <div v-if="(m.name && !shouldHideContentName(gName, m.name)) || (group.length > 1 && getCleanRemark(m.remarks || m.sub_name, m.details))" class="text-[13px] text-slate-400 font-bold">
                                                + {{ m.name ? m.name : (getMainDetails(group) ? '' : '項目') }}
                                                {{ (m.details && (m.name || !getMainDetails(group))) ? (m.name ? ' : ' : '') + m.details : '' }}
                                                <span v-if="getCleanRemark(m.remarks || m.sub_name, m.details)" class="opacity-60 ml-1">({{ getCleanRemark(m.remarks || m.sub_name, m.details) }})</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="fixed bottom-[7vh] left-0 right-0 px-6 py-2 pb-1 border-t border-slate-100 bg-white/95 backdrop-blur-md shadow-[0_-10px_30px_rgba(0,0,0,0.05)] shrink-0 flex items-center space-x-3 z-[610]">
                        <button @click="saveConfirmModal.show = false" class="px-10 py-[10px] bg-slate-100 text-slate-500 rounded-2xl font-black text-[17px] active:scale-[0.98] transition-all whitespace-nowrap">
                            修改
                        </button>
                        <button @click="performActualSave" class="flex-1 py-[10px] bg-[#FFB266] text-white rounded-2xl font-black text-[19px] shadow-lg active:scale-[0.98] transition-all flex items-center justify-center">
                            {{ saving ? '錄入中...' : '確認存檔' }}
                        </button>
                    </div>
                </div>

            </template>
            
            <!-- Global Components (Visible on all levels) -->
            <add-action-menu :show="showAddMenu" @close="showAddMenu = false" :actions="addActions" />
            <mobile-navbar 
                :can-back="currentFolder !== null || currentCategory !== null || addMode"
                :show-action="(currentFolder !== null || (currentCategory === 'masters' && !currentFolder)) && !addMode"
                :action-active="showAddMenu"
                :search-active="showSearch"
                :can-more="!!currentFolder && !addMode"
                @back="handleBack"
                @home="$emit('goHome')"
                @action="showAddMenu = !showAddMenu"
                @search="showSearch = !showSearch"
                @more="itemPagination.last_page > 0 ? exportListExcel() : null"
            />
            
            <compact-date-picker 
                v-if="activeDate"
                v-model="datePickerValue"
                title="選擇日期"
                @close="activeDate = null"
            />
            <!-- Floating Login Info Removed per user request -->
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, defineEmits, watch, nextTick } from 'vue';
import axios from 'axios';
import SearchComponent from './SearchComponent.vue';
import MobileNavbar from './MobileNavbar.vue';
import AddActionMenu from './AddActionMenu.vue';
import CompactDatePicker from './CompactDatePicker.vue';

const getTodayStr = () => {
    const d = new Date();
    return `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`;
};

// Reactive State
const props = defineProps(['user']);
const formatDate = (dateStr) => {
    if (!dateStr || dateStr === '-' || dateStr === '未設定') return dateStr || '-';
    const s = String(dateStr).split('T')[0].trim();
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

const emit = defineEmits(['goHome']);

const resetToRoot = () => {
    currentCategory.value = null;
    currentFolder.value = null;
    addMode.value = false;
    searchQuery.value = '';
    focusedId.value = null;
    expandedId.value = null;
    reorderMode.value = false;
    openMenuId.value = null;
};
const activeEntryTab = ref('single');
const batchImportContent = ref('');
const isAddingFlash = ref(false);
const currentFolder = ref(null);
const currentCategory = ref(null);

watch(() => props.user, (newVal) => {
    if (newVal?.permissions && !newVal.permissions.can_see_daily_teachings && currentCategory.value === null && !currentFolder.value) {
        currentCategory.value = 'masters';
    }
}, { immediate: true });

const addMode = ref(false);
const activeDate = ref(null);

const datePickerValue = computed({
    get: () => activeDate.value === 'inlineEdit' ? inlineEditData.value.date : form.value.date,
    set: (val) => {
        if (activeDate.value === 'inlineEdit') {
            inlineEditData.value.date = val;
        } else {
            form.value.date = val;
        }
    }
});

const formatValue = (val, unit) => {
    if (val === '1' || val === 1) return '一次性';
    return val ? `${val}${unit}` : '';
};
const getRoleOrder = (member) => {
    const groupNames = (member.groups || []).map(g => g.name);
    if (groupNames.includes('宮主')) return 1;
    if (groupNames.includes('副宮主')) return 2;
    return 3;
};

const stripMasterPrefix = (name) => {
    if (!name) return '';
    const parts = name.split(':');
    const res = parts.length > 1 ? parts[1] : parts[0];
    return res.trim();
};

const formatGroupName = (name) => {
    const groupToPalace = {
        '第一組': '玄通宮', '第二組': '玄應宮', '第三組': '玄心宮', '第四組': '玄妙宮',
        '第五組': '玄昇宮', '第六組': '玄願宮', '第七組': '玄法宮', '第八組': '玄閻宮',
        '第九組': '玄窕宮', '第十組': '玄瑤宮', '第十一組': '玄義宮'
    };
    return groupToPalace[name] || name;
};

const getMainDetails = (items) => {
    if (!items || items.length === 0) return '';
    const main = items.find(i => !i.name);
    if (main) return main.details || '';
    
    // Fallback: If no header record (name: ''), but it's a special treasure with exactly one item
    const tName = items[0]?.treasure_name || '';
    if (items.length === 1 && (tName.includes('七素') || tName.includes('七表'))) {
        return items[0].details || '';
    }
    return '';
};

const getRecordHeaderPreview = (item, index, allRecords) => {
    let masterName = item.master?.name || item.master_name;
    if (!masterName) masterName = '父皇仙師';
    
    const recipient = getRecipientName(item);
    
    // Deduplication logic: 
    if (index > 0) {
        const prevItem = allRecords[index - 1];
        const prevMasterName = prevItem.master?.name || prevItem.master_name || '父皇仙師';
        const prevRecipient = getRecipientName(prevItem);
        
        // If everything matches (Date, Master, Recipient) -> HIDE HEADER COMPLETELY
        if (masterName === prevMasterName && item.date === prevItem.date && recipient === prevRecipient) {
            return '';
        }
        
        // If same master/date but DIFFERENT recipient -> only show "開示給..."
        if (masterName === prevMasterName && item.date === prevItem.date) {
            return `開示給：${recipient}`;
        }
    }
    
    return `${masterName}開示給：${recipient}`;
};

const showAddMenu = ref(false);
const teachings = ref([]); 
const visibleItems = ref([]);
const dharmaNames = ref([]);
const groups = ref([]);
const treasures = ref([]);
const saveConfirmModal = ref({
    show: false,
    records: []
});
const masters = ref([]);
const loading = ref(false);
const saving = ref(false);
const editingId = ref(null);
const focusedId = ref(null);
const focusedDate = ref(null);
const activeDropdownId = ref(null);
const allExpanded = ref(false);
const sortDesc = ref(true);
const reorderMode = ref(false);
const inlineEditingId = ref(null);
const inlineEditData = ref({
    id: null,
    date: '',
    master_name: '',
    target_name: '',
    content: '',
    items: [],
    items_footer_remarks: ''
});

const handleMenuEdit = (item) => {
    startInlineEdit(item);
    activeDropdownId.value = null;
};

const startInlineEdit = (item) => {
    // Explicitly set focus to ensure expansion without toggling
    focusedId.value = item.id;
    inlineEditingId.value = item.id;
    inlineEditData.value = { 
        id: item.id,
        date: item.date || '',
        master_name: item.master?.name || item.master_name || '父皇仙師',
        target_name: getRecipientName(item),
        content: item.content || '',
        // Robust normalization to ensure pre-filling works regardless of property names (name vs treasure_name)
        items: item.items ? item.items.map(i => ({
            name: i.name || i.treasure_name || '',
            details: i.details || '',
            remarks: i.remarks || i.sub_name || ''
        })) : [],
        items_footer_remarks: item.items_footer_remarks || ''
    };
};

const cancelInlineEdit = () => {
    inlineEditingId.value = null;
    inlineEditData.value = { id: null, date: '', master_name: '', target_name: '', content: '', items: [], items_footer_remarks: '' };
};

const saveInlineEdit = async () => {
    if (!inlineEditData.value.id) return;
    saving.value = true;
    try {
        const res = await axios.patch(`/teachings/${inlineEditData.value.id}`, {
            date: inlineEditData.value.date,
            master_name: inlineEditData.value.master_name,
            target_name: inlineEditData.value.target_name,
            content: inlineEditData.value.content,
            items: inlineEditData.value.items,
            items_footer_remarks: inlineEditData.value.items_footer_remarks
        });
        
        // Update local state
        const idx = teachings.value.findIndex(t => t.id === inlineEditData.value.id);
        if (idx !== -1) {
            teachings.value[idx] = { ...teachings.value[idx], ...res.data };
        }
        
        cancelInlineEdit();
    } catch (err) {
        console.error('Failed to save inline edit:', err);
        alert('存檔失敗，請稍後再試');
    } finally {
        saving.value = false;
    }
};

const getCleanRemark = (remark, details) => {
    if (!remark) return '';
    if (!details) return remark;
    const dayMatch = details.match(/(\d+)\s*天份/);
    if (dayMatch) {
        const d = dayMatch[1];
        const regex = new RegExp(d + '\\s*天份', 'g');
        return remark.replace(regex, '').replace(/^[,\s:]+/, '').replace(/[,\s:]+$/, '').trim();
    }
    return remark;
};

const hasFinishedSuffix = (item) => {
    if (!item) return false;
    const content = (item.content || '').trim();
    const footer = (item.items_footer_remarks || '').trim();
    return content.includes('完畢') || footer.includes('完畢');
};

const getMergedContent = (details, remark) => {
    if (remark?.trim()) return remark.trim();
    return details || '';
};

const activePractitionerDropdownId = ref(null);
const treasureInput = ref(null);
const activeSubPractitionerDropdownId = ref(null);

const handleReorder = async (item, newOrder, currentList) => {
    const targetOrder = parseInt(newOrder);
    if (isNaN(targetOrder)) return;

    const list = [...currentList];
    const oldIndex = list.findIndex(r => r.id === item.id);
    if (oldIndex === -1) return;

    const [movedItem] = list.splice(oldIndex, 1);
    list.splice(Math.max(0, targetOrder - 1), 0, movedItem);

    // Prepare API orders. Note: Each "item" in currentList might represent multiple merged records (ids)
    const orders = [];
    list.forEach((block, idx) => {
        const newSortOrder = sortDesc.value ? (list.length - idx) : (idx + 1);
        // Update all records that belong to this block
        const idsToUpdate = block.ids || [block.id];
        idsToUpdate.forEach(id => {
            orders.push({ id, sort_order: newSortOrder });
            
            // Update local state for immediate feedback
            const orig = visibleItems.value.find(v => v.id === id);
            if (orig) orig.sort_order = newSortOrder;
        });
    });

    try {
        await axios.post('/teachings/reorder', { orders });
    } catch (err) {
        console.error('Reorder failed:', err);
    }
};

const showPreviewRecipients = ref(new Set());
const togglePreviewRecipients = (idx) => {
    if (showPreviewRecipients.value.has(idx)) showPreviewRecipients.value.delete(idx);
    else showPreviewRecipients.value.add(idx);
};

const toggleSort = () => { sortDesc.value = !sortDesc.value; };
const groupedRecords = computed(() => {
    const search = (searchQuery.value || '').toLowerCase().trim();
    let list = visibleItems.value;
    if (search) {
        list = list.filter(i => {
            const m = (i.master?.name || i.master_name || '').toLowerCase();
            const c = (i.content || '').toLowerCase();
            const r = getRecipientName(i).toLowerCase();
            const tr = (i.items || []).some(t => (t.treasure_name || '').toLowerCase().includes(search));
            return m.includes(search) || c.includes(search) || r.includes(search) || tr;
        });
    }
    
    // Sort first
    list.sort((a, b) => {
        const dA = new Date(a.date).getTime();
        const dB = new Date(b.date).getTime();
        
        // Group by date first
        if (sortDesc.value) {
            if (dB !== dA) return dB - dA;
        } else {
            if (dA !== dB) return dA - dB;
        }
        
        // Within same date, follow sortDesc (New to Old -> latest opening first)
        const sA = a.sort_order || 999999;
        const sB = b.sort_order || 999999;
        if (sA !== sB) return sortDesc.value ? sB - sA : sA - sB;
        
        return sortDesc.value ? b.id - a.id : a.id - b.id;
    });

    // Disabled Merging Logic: Every database record is now treated as an independent entry 
    // unless they are explicitly saved as a single record.
    const grouped = list.map(item => ({
        ...item,
        namesKey: (item.dharma_names || []).map(dn => dn.name).sort().join(','),
        ids: [item.id]
    }));
    
    return grouped;
});

const recordsByDate = computed(() => {
    const map = {};
    groupedRecords.value.forEach(item => {
        if (!map[item.date]) map[item.date] = [];
        map[item.date].push(item);
    });
    return Object.keys(map).sort((a,b) => {
        const dA = new Date(a).getTime();
        const dB = new Date(b).getTime();
        return sortDesc.value ? dB - dA : dA - dB;
    }).map(date => ({ date, items: map[date] }));
});

const toggleDateExpand = (date) => {
    if (focusedDate.value === date) focusedDate.value = null;
    else {
        focusedDate.value = date;
        focusedId.value = null; 
        activeDropdownId.value = null;
        showRecipientDetails.value = {};
    }
};

const activeMasterDropdownId = ref(null);
const allMastersList = computed(() => {
    // Requirement: Follow original sequence while excluding '父皇仙師'
    return (masters.value || [])
        .map(m => m.name)
        .filter(n => n && n !== '父皇仙師');
});

const pickMaster = (name, index = null) => {
    if (index !== null) {
        batchRecords.value[index].master_name = name;
    } else {
        masterNameInput.value = name;
        resolveMasterId();
    }
    activeMasterDropdownId.value = null;
};

const showRecipientDetails = ref({});
const toggleRecipientDetails = (id) => {
    showRecipientDetails.value[id] = !showRecipientDetails.value[id];
};
const itemsDetailMode = ref(false);
const expandedDetails = ref({});
const newItemName = ref('');
const newItemDays = ref('');
const newItemSubName = ref('');
const newItemSubDetails = ref('');
const newItemRemarks = ref('');
const stagedContents = ref([]);
const showMainRemarks = ref(false);
const showSubRemarks = ref(false);
const showAddDetails = ref(false);
const newItemSubTimes = ref('');
const newItemSubHours = ref('');
const newItemMainTimes = ref('');
const newItemMainHours = ref('');
const newItemMainDays = ref('');
const newItemSubSize = ref('');
const newItemSubSheets = ref('');
const newItemSubPractitioner = ref('');
const newItemPractitioner = ref('');
const newItemSubBodyPart = ref('');
const newItemSubInstrumentName = ref('');
const newItemMainRemarks = ref('');
const newItemDetailsExtraDays = ref('');
const newItemSun = ref('');
const newItemMoon = ref('');
const newItemLight = ref('');
const newItemSubSun = ref('');
const newItemSubMoon = ref('');
const newItemSubLight = ref('');
const activeTreasureDropdownId = ref(null);
const activeSubTreasureDropdownId = ref(null);


const isSpecialInstrument = (name) => {
    if (!name) return false;
    const n = name.toLowerCase();
    const list = [
        '由', '金印', '令牌', '令旗', '天筆', '法筆', '玉筆', '寶鏡', '現惡鏡', '寶劍', 
        '八卦', '寶扇', '油燈', '淨塵', '法器', '筆', '鑑', '印', '勾', '旗'
    ];
    if (n.includes('陣') || n.includes('提升玄能') || n.includes('解脫')) return false;
    // Removed circular instrumentTreasures.value.some check
    return list.some(k => n.includes(k)) && !n.includes('專司靈療') && !n.includes('法陣');
};

const isSpecialTreasure = (name) => {
    if (!name) return false;
    return isSpecialInstrument(name) || 
           name.includes('丹') || name.includes('符') || name.includes('令') || 
           name.includes('疏') || name.includes('香') || name.includes('由');
};

const isSaving = ref(false);
const activeBatchIndex = ref(null);

const openBatchItemDetails = (index) => {
    activeBatchIndex.value = index;
    // Copy the block's current items to the form items for editing
    const br = batchRecords.value[index];
    form.value.items = JSON.parse(JSON.stringify(br.items || []));
    // SYNC basic fields to form so preview is accurate
    form.value.content = br.content || '';
    form.value.dharma_name_ids = [...(br.dharma_name_ids || [])];
    form.value.target_remarks = br.target_remarks || '';
    form.value.master_name = br.master_name;
    
    itemsDetailMode.value = true;
};

const addBatchBlock = (startEditing = false) => {
    // Strict Inheritance: Always use the first master in the set
    const firstMaster = batchRecords.value.length > 0 ? (batchRecords.value[0].master_name || masterNameInput.value) : (masterNameInput.value || '父皇仙師');
    
    const newIndex = batchRecords.value.length;
    batchRecords.value.push({ 
        dharma_name_ids: [], content: '', dharmaSearchQuery: '', 
        target_remarks: '', relatives: '', items: [], 
        master_name: firstMaster 
    });

    if (startEditing) {
        openBatchItemDetails(newIndex);
    }
};

const handleItemsDetailClose = (mode = false) => {
    if (activeBatchIndex.value !== null) {
        // Sync everything from form to the specific batch block
        const br = batchRecords.value[activeBatchIndex.value];
        br.items = JSON.parse(JSON.stringify(form.value.items));
        br.content = form.value.content;
        br.dharma_name_ids = [...form.value.dharma_name_ids];
        br.target_remarks = form.value.target_remarks;
        
        // Strict Inheritance
        const firstM = batchRecords.value[0].master_name || masterNameInput.value;
        br.master_name = firstM;

        form.value.items = []; 
        activeBatchIndex.value = null;
    } else if (mode === true) {
        // Requirement: If adding next but not in a block yet, stash the current form first
        stashAndContinue();
        addBatchBlock(false);
        return;
    }
    
    itemsDetailMode.value = false;

    if (mode === true) {
        addBatchBlock(false); 
    } else if (mode === 'save') {
        saveItem();
    }
};

const scrollToTreasureTop = () => {
    const el = document.querySelector('.items-detail-container');
    if (el) el.scrollTo({ top: 0, behavior: 'smooth' });
    // Focus the name input
    const input = document.querySelector('.treasure-name-input');
    if (input) input.focus();
};

const stashAndContinue = () => {
    // Resolve Master Name robustly: Always prioritize the first master in the batch set for consistency
    const firstMaster = batchRecords.value.length > 0 ? batchRecords.value[0].master_name : (masterNameInput.value || '父皇仙師');

    const newRecord = {
        dharma_name_ids: [...form.value.dharma_name_ids],
        content: form.value.content,
        items: [...form.value.items],
        dharmaSearchQuery: dharmaSearchQuery.value || '',
        master_name: firstMaster, 
        target_remarks: form.value.target_remarks || ''
    };
    
    // Remove first dummy block if it exists and is empty
    if (batchRecords.value.length === 1 && !batchRecords.value[0].content && batchRecords.value[0].items.length === 0 && !batchRecords.value[0].dharma_name_ids.length) {
        batchRecords.value = [newRecord];
    } else {
        batchRecords.value.push(newRecord);
    }
    
    // Clear for next
    form.value.content = '';
    form.value.items = [];
    form.value.dharma_name_ids = [];
    form.value.target_remarks = '';
    stagedContents.value = [];
    dharmaSearchQuery.value = '';
    
    // Requirement: Keep user in 'single' entry mode for the next persona per request
    activeEntryTab.value = 'single';
    itemsDetailMode.value = false;
};

const formatMasterName = (name) => {
    if (!name) return '';
    return name.replace('每日開示', '').replace('開示記錄', '').replace('專區', '').trim();
};

const uniqueTreasureNames = computed(() => {
    const names = (treasures.value || []).filter(t => {
        const name = t.name || '';
        if (name.includes('陣') || name.includes('提升玄能')) return false;
        return true;
    }).map(t => {
        const n = t.name ? t.name.trim() : '';
        return stripMasterPrefix(n);
    });
    
    // Add specific tokens and instruments to the suggestions
    const extraSuggestions = [
        '太令令牌', '極令令牌', '道令令牌', '元令令牌', '靈令令牌', '玉皇令令牌', '皇令牌', '龍令令牌', '王令令牌',
        '極令', '元令', '道令', '靈令', '玉皇令', '太令', '龍令', '王令', 
        '金印', '令牌', '令旗', '天筆', '法筆', '玉筆', '八卦', '寶扇', '油燈', '法器', '清煞'
    ];
    
    const combined = [...new Set([...names, ...extraSuggestions])];
    return combined.filter(n => n && n !== '清煞法寶').sort((a, b) => a.length - b.length || a.localeCompare(b, 'zh-TW', { collation: 'stroke' }));
});


const canCRUD = (item) => {
    if (!props.user) return false;
    return item.user_id === props.user.id || props.user.is_admin;
};

const currentEntryPreview = computed(() => {
    if (!newItemName.value) return '';
    const effectiveCategory = newItemSubName.value ? magicItemSubCategory.value : magicItemCategory.value;
    return buildTreasureDetails(
        effectiveCategory,
        newItemSubName.value ? newItemSubTimes.value : newItemMainTimes.value,
        newItemSubName.value ? newItemSubHours.value : newItemMainHours.value,
        newItemSubName.value ? newItemDetailsExtraDays.value : (newItemMainDays.value || newItemDays.value || newItemSubDetails.value),
        newItemSubSize.value,
        newItemSubSheets.value,
        newItemSubName.value ? newItemSubPractitioner.value : newItemPractitioner.value,
        newItemSubName.value || newItemName.value,
        newItemSubBodyPart.value,
        newItemSubInstrumentName.value,
        newItemSubSun.value,
        newItemSubMoon.value,
        newItemSubLight.value,
        newItemName.value
    );
});

const magicItemCategory = computed(() => {
    if (!newItemName.value) return 'default';
    if (newItemName.value.includes('三光金丹')) return '三光金丹';
    if (newItemName.value.includes('丹')) return '金丹';
    if (newItemName.value.includes('太令')) return '太令';
    if (newItemName.value.includes('香環')) return '香環';
    if (newItemName.value.includes('福祿香')) return '福祿香';
    if (newItemName.value.includes('由') || newItemName.value.includes('執法') || newItemName.value.includes('清煞') || newItemName.value.includes('專司靈療') || newItemName.value.includes('香灰')) return 'default';
    if ((newItemName.value.includes('符') || newItemName.value.includes('令') || newItemName.value.includes('疏文')) && !newItemName.value.includes('令旗') && !newItemName.value.includes('令牌')) return '符令';
    return 'default';
});

const magicItemSubCategory = computed(() => {
    if (!newItemSubName.value) return 'default';
    if (newItemSubName.value.includes('三光金丹')) return '三光金丹';
    if (newItemSubName.value.includes('丹')) return '金丹';
    if (newItemSubName.value.includes('太令')) return '太令';
    if (newItemSubName.value.includes('香環')) return '香環';
    if (newItemSubName.value.includes('福祿香')) return '福祿香';
    if ((newItemSubName.value.includes('符') || newItemSubName.value.includes('令') || newItemSubName.value.includes('疏文')) && !newItemSubName.value.includes('令旗') && !newItemSubName.value.includes('令牌')) return '符令';
    return 'default';
});

const units = ref(['天份', '次性', '顆', '張', '個', '盒']);

const showDharmaPicker = ref(false);
const showPalacePicker = ref(false);
const pickerSearch = ref('');
const expandedGroupPicker = ref(null);
const collapsedDates = ref(new Set());
const initializedDates = ref(new Set());

const uniqueDharmaNames = computed(() => {
    const names = (dharmaNames.value || []).map(d => (d.name || '').trim());
    return [...new Set(names.filter(n => n))];
});
const armyOrder = ['虎賁軍', '虎甲軍', '黑曜軍'];
const palaceOrder = ['玄通宮', '玄應宮', '玄心宮', '玄妙宮', '玄昇宮', '玄願宮', '玄法宮', '玄閻宮', '玄窕宮', '玄瑤宮', '玄義宮'];

const palaceDharmaMapping = computed(() => {
    const mapping = new Map();
    const groupToPalace = {
        '第一組': '玄通宮', '第二組': '玄應宮', '第三組': '玄心宮', '第四組': '玄妙宮',
        '第五組': '玄昇宮', '第六組': '玄願宮', '第七組': '玄法宮', '第八組': '玄閻宮',
        '第九組': '玄窕宮', '第十組': '玄瑤宮', '第十一組': '玄義宮'
    };
    
    (groups.value || []).forEach(g => {
        let pIndex = -1;
        const mappedName = groupToPalace[g.name] || g.name;
        pIndex = palaceOrder.indexOf(mappedName);
        
        if (pIndex === -1) {
            palaceOrder.forEach((p, idx) => {
                if (g.name.includes(p)) pIndex = idx;
            });
        }
        
        if (pIndex !== -1 && g.dharma_names) {
            g.dharma_names.forEach(dn => {
                // Map both ID and name to the palace index
                if (!mapping.has(String(dn.id)) || mapping.get(String(dn.id)) > pIndex) {
                    mapping.set(String(dn.id), pIndex);
                    mapping.set(dn.name, pIndex);
                }
            });
        }
    });
    return mapping;
});

const sortedDharmaNames = computed(() => {
    const list = (dharmaNames.value || []).slice();
    return list.sort((a, b) => {
        if (a.order !== undefined && b.order !== undefined) {
            if (a.order !== b.order) return a.order - b.order;
        }
        
        const idxA = palaceDharmaMapping.value.get(String(a.id));
        const idxB = palaceDharmaMapping.value.get(String(b.id));
        
        if (idxA !== undefined && idxB !== undefined) {
            if (idxA !== idxB) return idxA - idxB;
        } else if (idxA !== undefined) {
            return -1;
        } else if (idxB !== undefined) {
            return 1;
        }
        
        return (a.name || '').localeCompare(b.name || '', 'zh-TW', { collation: 'stroke' });
    });
});

const uniqueUnits = computed(() => {
    const names = units.value.map(u => u.trim());
    return [...new Set(names.filter(n => n))].sort((a, b) => a.toLowerCase().localeCompare(b.toLowerCase()));
});

visibleItems.value.forEach(item => {
    if (!initializedDates.value.has(item.date)) {
        collapsedDates.value.add(item.date);
        initializedDates.value.add(item.date);
    }
});

const activeModalGroupGrouped = computed(() => {
    if (!activeModalGroup.value) return [];
    
    // Use the dharma_names already populated in triggerGroupSelection
    const membersList = activeModalGroup.value.dharma_names || [];
    if (membersList.length === 0) return [];
    
    const name = activeModalGroup.value?.name || '';

    // Filter to only include those actually in the current form selection
    const currentMembers = membersList.filter(m => {
        return form.value.dharma_name_ids.some(id => String(id) === String(m.id));
    });

    // Sort by Database Order
    currentMembers.sort((a, b) => (a.order || 999) - (b.order || 999) || (a.name || '').localeCompare(b.name || '', 'zh-TW', { collation: 'stroke' }));

    // SPECIAL CASE: '全體殿生' should be a single flat list
    if (name === '全體殿生') {
        return [{ palaceName: '全體成員', members: currentMembers }];
    }

    // Special groups that should be hidden from this modal view
    if (name === '世代交替' || name.includes('直屬弟子')) return [];

    // '各宮' or groups containing '宮' (except specific ones) should show Palace subdivision
    if (name === '各宮' || (name.includes('宮') && !name.includes('宮主'))) {
        const grouped = [];
        const palaces = [
            '玄通宮', '玄應宮', '玄心宮', '玄妙宮', '玄昇宮', 
            '玄願宮', '玄法宮', '玄閻宮', '玄窕宮', '玄瑤宮', '玄義宮'
        ];
        
        palaces.forEach(pName => {
            const members = currentMembers.filter(m => {
                const pIdx = palaceDharmaMapping.value.get(String(m.id));
                return pIdx !== undefined && palaces[pIdx] === pName;
            });
            if (members.length > 0) {
                grouped.push({ palaceName: pName, members });
            }
        });

        // Add remaining members who don't belong to a palace
        const handledIds = new Set();
        grouped.forEach(g => g.members.forEach(m => handledIds.add(String(m.id))));
        const others = currentMembers.filter(m => !handledIds.has(String(m.id)));
        if (others.length > 0) {
            grouped.push({ palaceName: '其他', members: others });
        }
        
        return grouped;
    }

    // Default: Return a single group with all members
    return [{ palaceName: '群組成員', members: currentMembers }];
});

const showSearch = ref(false);
const searchQuery = ref('');
const itemPagination = ref({ current_page: 1, last_page: 1 });
const batchRecords = ref([
    { dharma_name_ids: [], content: '', dharmaSearchQuery: '', target_remarks: '', relatives: '', items: [], master_name: '' }
]);

const removeBatchBlock = (index) => {
    if (batchRecords.value.length > 1) batchRecords.value.splice(index, 1);
};

const handleBlockDharmaInput = (index, val) => {
    const block = batchRecords.value[index];
    if (!val) { block.dharma_name_ids = []; return; }
    const matchedDN = dharmaNames.value.find(dn => dn.name === val);
    if (matchedDN) { block.dharma_name_ids = [matchedDN.id]; return; }
    const matchedGroup = groups.value.find(g => g.name === val);
    if (matchedGroup) {
        block.dharma_name_ids = (matchedGroup.dharma_names || []).map(dn => dn.id);
        block.dharmaSearchQuery = matchedGroup.name;
    }
};


const form = ref({
    supplement: '', target_remarks: '', content: '',
    date: '', master_id: null, items: [], 
    remarks: '', items_footer_remarks: '', user_id: 1, dharma_name_ids: []
});

// Multi-entry footer remarks
const footerRemarksList = ref([]);
const newFooterRemark = ref('');

const syncFooterRemarks = () => {
    form.value.items_footer_remarks = footerRemarksList.value.join('\n');
};

const addFooterRemark = () => {
    const val = newFooterRemark.value.trim();
    if (!val) return;
    footerRemarksList.value.push(val);
    newFooterRemark.value = '';
    syncFooterRemarks();
};

// When loading an existing record for edit, parse existing remarks back to list
watch(() => form.value.items_footer_remarks, (val) => {
    if (typeof val === 'string' && val.trim()) {
        const lines = val.split('\n').map(l => l.trim()).filter(l => l);
        // Only sync if the list doesn't already match (avoid infinite loop)
        if (lines.join('\n') !== footerRemarksList.value.join('\n')) {
            footerRemarksList.value = lines;
        }
    } else if (!val) {
        footerRemarksList.value = [];
    }
}, { immediate: true });

const dharmaSearchQuery = ref('');
const showGroupMembersModal = ref(false);

const currentMatchedGroup = computed(() => {
    if (form.value.dharma_name_ids.length <= 1) return null;
    const currentIds = form.value.dharma_name_ids.map(id => String(id));
    const matched = (groups.value || []).find(g => {
        if (g.dharma_names.length !== currentIds.length) return false;
        const gIds = g.dharma_names.map(m => String(m.id));
        return currentIds.every(id => gIds.includes(id));
    });
    
    
    if (matched && matched.dharma_names) {
        // Sort dharma_names by palace mapping to ensure correct order for "Palace Master" etc.
        const sorted = [...matched.dharma_names].sort((a, b) => {
            if (a.order !== undefined && b.order !== undefined) {
                if (a.order !== b.order) return a.order - b.order;
            }
            
            const idxA = palaceDharmaMapping.value.get(String(a.id));
            const idxB = palaceDharmaMapping.value.get(String(b.id));
            
            if (idxA !== undefined && idxB !== undefined) {
                if (idxA !== idxB) return idxA - idxB;
            } else if (idxA !== undefined) {
                return -1;
            } else if (idxB !== undefined) {
                return 1;
            }
            
            return (a.name || '').localeCompare(b.name || '', 'zh-TW', { collation: 'stroke' });
        });
        return { ...matched, dharma_names: sorted };
    }
    
    return matched;
});

const activeModalGroup = ref(null);

const mainSearchFilteredDharmaNames = computed(() => {
    return (dharmaNames.value || [])
        .filter(d => !dharmaSearchQuery.value || (d.name && d.name.includes(dharmaSearchQuery.value)));
});

const palacePrioritizedGroups = computed(() => {
    const armyOrder = ['虎賁軍', '虎甲軍', '黑曜軍'];
const palaceOrder = [
        '玄通宮', '玄應宮', '玄心宮', '玄妙宮', '玄昇宮', 
        '玄願宮', '玄法宮', '玄閻宮', '玄窕宮', '玄瑤宮', '玄義宮'
    ];
    const groupToPalace = {
        '第一組': '玄通宮', '第二組': '玄應宮', '第三組': '玄心宮', '第四組': '玄妙宮',
        '第五組': '玄昇宮', '第六組': '玄願宮', '第七組': '玄法宮', '第八組': '玄閻宮',
        '第九組': '玄窕宮', '第十組': '玄瑤宮', '第十一組': '玄義宮'
    };
    
    const list = (groups.value || []).filter(g => g.name !== '信眾' && g.name !== '各宮');
    
    return list.sort((a, b) => {
        let idxA = -1;
        let idxB = -1;

        const nameA = groupToPalace[a.name] || a.name;
        const nameB = groupToPalace[b.name] || b.name;

        idxA = palaceOrder.indexOf(nameA);
        idxB = palaceOrder.indexOf(nameB);

        // Fallback to "includes" for positions like Palace Master
        if (idxA === -1) {
            palaceOrder.forEach((p, i) => { if (a.name.includes(p)) idxA = i; });
        }
        if (idxB === -1) {
            palaceOrder.forEach((p, i) => { if (b.name.includes(p)) idxB = i; });
        }

        // Specific handling for generic Palace Master groups if they don't have palace names
        if (idxA === -1) {
            if (a.name === '宮主') idxA = 100;
            if (a.name === '宮主副宮主') idxA = 101;
        }
        if (idxB === -1) {
            if (b.name === '宮主') idxB = 100;
            if (b.name === '宮主副宮主') idxB = 101;
        }
        
        if (idxA !== -1 && idxB !== -1) {
            if (idxA !== idxB) return idxA - idxB;
            return a.name.localeCompare(b.name, 'zh-TW', { collation: 'stroke' });
        }
        if (idxA !== -1) return -1;
        if (idxB !== -1) return 1;
        
        return (a.name || '').localeCompare(b.name || '', 'zh-TW', { collation: 'stroke' });
    });
});

const mainSearchFilteredGroups = computed(() => {
    const q = (dharmaSearchQuery.value || '').toLowerCase().trim();
    if (!q) return palacePrioritizedGroups.value;
    
    const groupToPalace = {
        '第一組': '玄通宮', '第二組': '玄應宮', '第三組': '玄心宮', '第四組': '玄妙宮',
        '第五組': '玄昇宮', '第六組': '玄願宮', '第七組': '玄法宮', '第八組': '玄閻宮',
        '第九組': '玄窕宮', '第十組': '玄瑤宮', '第十一組': '玄義宮'
    };

    // Alias "各宮" to show all 11 palaces in the specified order
    if (q === '各宮') {
        return palacePrioritizedGroups.value.filter(g => {
            const mapped = groupToPalace[g.name] || g.name;
            return palaceOrder.includes(mapped);
        });
    }
    
    return palacePrioritizedGroups.value.filter(g => {
        const mapped = groupToPalace[g.name] || g.name;
        return (mapped || '').toLowerCase().includes(q) || (g.name || '').toLowerCase().includes(q);
    });
});

const detailModalFilteredDharmaNames = computed(() => {
    return (dharmaNames.value || [])
        .filter(d => !newItemPractitioner.value || (d.name && d.name.includes(newItemPractitioner.value)));
});

const folders_list = [
    { id: 1, name: '老祖仙師' }, { id: 2, name: '元始仙師' }, { id: 3, name: '道祖仙師' },
    { id: 4, name: '靈寶仙師' }, { id: 5, name: '父皇' }, { id: 6, name: '太宰仙師' },
    { id: 7, name: '太子' }, { id: 8, name: '閻王仙師' }, { id: 0, name: '父皇仙師每日開示' }
];

const filteredFolders = computed(() => {
    return folders_list.filter(f => {
        if (f.id === 0) return false;
        return true;
    });
});

const masterNameInput = ref('');

const filteredPickerResults = computed(() => {
    if (!pickerSearch.value) return (dharmaNames.value || []).slice(0, 100); 
    const q = pickerSearch.value.toLowerCase();
    
    // Find groups that match the query
    const matchedDNsFromGroups = new Set();
    (groups.value || []).forEach(g => {
        if ((g.name || '').toLowerCase().includes(q)) {
            (g.dharma_names || []).forEach(m => matchedDNsFromGroups.add(m.id));
        }
    });

    return (dharmaNames.value || []).filter(dn => {
        const nameMatch = (dn.name || '').toLowerCase().includes(q);
        const groupMatch = matchedDNsFromGroups.has(dn.id);
        return nameMatch || groupMatch;
    });
});

const filteredGroups = computed(() => {
    if (!pickerSearch.value) return palacePrioritizedGroups.value;
    const q = pickerSearch.value.toLowerCase();
    return palacePrioritizedGroups.value.filter(g => (g.name || '').toLowerCase().includes(q));
});

const instrumentTreasures = computed(() => {
    return treasures.value.filter(t => (isSpecialInstrument(t.name) || (t.category?.name === '法器')) && !t.name.includes('陣') && !t.name.includes('提升玄能'));
});

const groupedPendingItems = computed(() => groupItems(form.value.items));

const addActions = computed(() => {
    const isDaily = currentFolder.value?.id === 0 || currentFolder.value?.id === '0';
    const actions = [];
    
    if (isDaily) {
        actions.push({ 
            label: '逐筆新增', 
            description: '錄入單筆開示及賜降紀錄',
            icon: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>',
            colorClass: 'bg-indigo-50 text-indigo-600',
            handler: () => { showAdd(); activeEntryTab.value = 'single'; showAddMenu.value = false; } 
        });
    }

    if (!isDaily) {
        actions.push({ 
            label: '多筆新增 (Excel 貼上)', 
            description: '批次貼上文字清單匯入',
            icon: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>',
            colorClass: 'bg-blue-50 text-blue-600',
            handler: () => { showAdd(); activeEntryTab.value = 'batch'; showAddMenu.value = false; } 
        });
    }

    actions.push({ 
        label: '複製貼 LINE', 
        description: '將紀錄轉為文字複製貼上',
        icon: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>',
        colorClass: 'bg-purple-50 text-purple-600',
        handler: copyAllToLine 
    });

    actions.push({ 
        label: '匯出文字檔', 
        description: '下載完整開示記錄文字檔',
        icon: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>',
        colorClass: 'bg-emerald-50 text-emerald-600',
        handler: exportListTxt 
    });

    return actions;
});

// Methods
const toggleGroupAccordion = (id) => { expandedGroupPicker.value = expandedGroupPicker.value === id ? null : id; };

const stripContentHeaders = (text) => {
    if (!text) return "";
    let cleaned = text.trim();
    // 1. Strip the "開示給" line and everything before it
    const recMatch = cleaned.match(/開示給(.*?)[：:]/);
    if (recMatch) {
        const matchIndex = cleaned.indexOf(recMatch[0]);
        const lineEndIndex = cleaned.indexOf('\n', matchIndex);
        if (lineEndIndex !== -1) {
            cleaned = cleaned.substring(lineEndIndex).trim();
        } else {
            cleaned = ""; // Only header was present
        }
    }
    // 2. Strip any date lines at the top
    cleaned = cleaned.replace(/^(\d{2,4}[\/\s.-]\d{1,2}[\/\s.-]\d{1,2})\s*$/m, '').trim();
    return cleaned;
};

const handleSmartPaste = (e, targetRefOrObj, blockIndex = null) => {
    const text = e.clipboardData.getData('text') || '';
    if (!text.includes('仙師') && !text.includes('賜降')) return; // Not our format, allow browser default paste

    e.preventDefault();
    let teachingContent = text;
    
    // Standardize target access
    const isMainForm = blockIndex === null;
    const target = isMainForm ? form.value : targetRefOrObj;
    if (!target) return;

    // 0. Date Detection
    const dateMatch = text.match(/(\d{4}[\/\s.-]\d{1,2}[\/\s.-]\d{1,2})/);
    if (dateMatch) {
        let dStr = dateMatch[1].trim();
        const dObj = new Date(dStr.replace(/\s+/g, '/').replace(/\./g, '/').replace(/-/g, '/'));
        if (!isNaN(dObj)) {
            const yyyy = dObj.getFullYear();
            const mm = String(dObj.getMonth() + 1).padStart(2, '0');
            const dd = String(dObj.getDate()).padStart(2, '0');
            target.date = `${yyyy}-${mm}-${dd}`;
            
            // Sync with global date if in main form
            if (isMainForm) {
                form.value.date = `${yyyy}-${mm}-${dd}`;
            }
        }
    }

    // 1. Master Detection
    const masterNames = ['老祖', '元始', '道祖', '靈寶', '父皇', '太宰', '太子', '閻王'];
    const masterMap = { '老祖': 1, '元始': 2, '道祖': 3, '靈寶': 4, '父皇': 5, '太宰': 6, '太子': 7, '閻王': 8 };
    
    for (const mName of masterNames) {
        if (text.includes(mName + '仙師')) {
            target.master_name = mName + '仙師';
            if (isMainForm) {
                target.master_id = masterMap[mName];
                masterNameInput.value = mName + '仙師';
            }
            break;
        }
    }

    // 2. Recipient Detection
    const recipientMatch = text.match(/開示給(.*?)[：:]/);
    if (recipientMatch) {
        let nameField = recipientMatch[1].trim();
        const foundDN = dharmaNames.value.find(dn => dn.name === nameField);
        if (foundDN) {
            target.dharma_name_ids = [foundDN.id];
            if (isMainForm) dharmaSearchQuery.value = foundDN.name;
            else target.dharmaSearchQuery = foundDN.name;
        } else {
            const foundGroup = (groups.value || []).find(g => g.name === nameField);
            if (foundGroup) {
                target.dharma_name_ids = (foundGroup.dharma_names || []).map(dn => dn.id);
                if (isMainForm) dharmaSearchQuery.value = foundGroup.name;
                else target.dharmaSearchQuery = foundGroup.name;
            }
        }
    }

    // 3. Treasure Detection
    if (text.includes('賜降：')) {
        const parts = text.split('賜降：');
        const treasurePart = parts[1].split('完畢')[0];

        const lines = treasurePart.split('\n').map(l => l.trim()).filter(l => l && !l.includes('完畢'));
        const newItems = [];
        lines.forEach(line => {
            const match = line.match(/^(\d+\.|[+*])\s*(.*?)([:：]\s*(.*))?$/);
            if (match) {
                const tName = match[2].trim();
                const tDetails = match[4] ? match[4].trim() : '';
                newItems.push({
                    uid: Date.now() + Math.random(),
                    treasure_name: tName,
                    details: tDetails,
                    name: '',
                    sub_name: ''
                });
            } else if (line.length > 2) {
                 newItems.push({
                    uid: Date.now() + Math.random(),
                    treasure_name: line.replace(/^(\d+\.|[+*])\s*/, '').trim(),
                    details: '',
                    name: '',
                    sub_name: ''
                 });
            }
        });
        
        if (newItems.length > 0) {
            target.items = [...(target.items || []), ...newItems];
        }
    }

    // 4. Content Cleanup: Strip redundant headers to match Image 3 requirement
    target.content = stripContentHeaders(text);

    // Trigger UI updates
    setTimeout(() => {
        const textareas = document.querySelectorAll('textarea');
        textareas.forEach(t => {
            t.style.height = 'auto';
            t.style.height = t.scrollHeight + 'px';
        });
    }, 50);
};

const triggerGroupSelection = (group) => {
    let members = [];
    
    // SPECIAL CASE: '全體殿生' should include ALL dharma names (approx 46 people)
    if (group.name === '全體殿生') {
        members = [...dharmaNames.value];
    } else {
        // Standard BACKFILL logic for other groups
        members = group.dharma_names || [];
        if (members.length === 0) {
            members = dharmaNames.value.filter(dn => {
                const gIds = (dn.groups || []).map(g => String(g.id));
                return gIds.includes(String(group.id));
            });
        }
    }

    const isSpecialSortGroup = group.name.includes('宮主') || group.name.includes('副宮主');

    if (isSpecialSortGroup) {
        // Sort by Role (Master > Vice > Member) then by Palace/Name Order
        members.sort((a, b) => {
            const roleA = getRoleOrder(a);
            const roleB = getRoleOrder(b);
            if (roleA !== roleB) return roleA - roleB;
            return (a.order || 0) - (b.order || 0) || (a.name || '').localeCompare(b.name || '', 'zh-TW', { collation: 'stroke' });
        });
    } else {
        // All other groups (including All Students and Lineage groups) sort by Database Order primarily
        members.sort((a, b) => {
            if (a.order !== undefined && b.order !== undefined) {
                if (a.order !== b.order) return a.order - b.order;
            }
            return (a.name || '').localeCompare(b.name || '', 'zh-TW', { collation: 'stroke' });
        });
    }

    // Force reactive update of the modal group with members
    activeModalGroup.value = { ...group, dharma_names: members };

    const memberIds = members.map(dn => dn.id);
    form.value.dharma_name_ids = memberIds;
    dharmaSearchQuery.value = formatGroupName(group.name);
    activePractitionerDropdownId.value = null;
    showGroupMembersModal.value = true;
};

const handleDharmaSearchInput = (e) => {
    const val = e.target.value;
    if (val === '各宮') {
        showPalacePicker.value = true;
        return;
    }
    if (!val) { form.value.dharma_name_ids = []; activeModalGroup.value = null; return; }
    const matchedDN = dharmaNames.value.find(dn => dn.name === val);
    if (matchedDN) { form.value.dharma_name_ids = [matchedDN.id]; return; }
    const matchedGroup = groups.value.find(g => g.name === val || formatGroupName(g.name) === val);
    if (matchedGroup) {
        triggerGroupSelection(matchedGroup);
        return;
    }
    // Auto-trigger for keywords if not exact match
    if (val === '宮主' || val === '副宮主' || val === '宮主副宮主' || val === '元帥' || val === '元帥副元帥') {
        const g = groups.value.find(g => g.name === val);
        if (g) triggerGroupSelection(g);
    }
};

const selectPalaceGroup = (pName) => {
    dharmaSearchQuery.value = pName;
    showPalacePicker.value = false;
    activePractitionerDropdownId.value = null;
    
    if (pName === '各宮') {
        const g = (groups.value || []).find(g => g.name === '各宮');
        if (g) {
            form.value.dharma_name_ids = (g.dharma_names || []).map(dn => dn.id);
        }
    } else {
        const g = (groups.value || []).find(g => g.name === pName || formatGroupName(g.name) === pName);
        if (g) {
            form.value.dharma_name_ids = (g.dharma_names || []).map(dn => dn.id);
        }
    }
};

const resolveMasterId = () => {
    const name = masterNameInput.value;
    form.value.master_name = name;
    const m = masters.value.find(x => x.name === name);
    if (m) { form.value.master_id = m.id; return; }
    const hardcoded = { '老祖仙師': 1, '元始仙師': 2, '道祖仙師': 3, '靈寶仙師': 4, '父皇仙師': 5, '太宰仙師': 6, '太子': 7, '閻王仙師': 8 };
    if (hardcoded[name]) { form.value.master_id = hardcoded[name]; return; }
    const f = folders.value.find(x => x.name === name);
    if (f) { form.value.master_id = f.id; return; }
    form.value.master_id = null;
};

const toggleDharmaName = (id) => {
    const idx = form.value.dharma_name_ids.indexOf(id);
    if (idx > -1) form.value.dharma_name_ids.splice(idx, 1);
    else form.value.dharma_name_ids.push(id);
};

const toggleGroupSelection = (group) => {

        // Sort by Role (Master > Vice > Member) then by Palace/Name Order
    group.dharma_names.sort((a, b) => {
        const roleA = getRoleOrder(a);
        const roleB = getRoleOrder(b);
        if (roleA !== roleB) return roleA - roleB;
        return (a.order || 0) - (b.order || 0) || (a.name || '').localeCompare(b.name || '', 'zh-TW', { collation: 'stroke' });
    });
    const memberIds = (group.dharma_names || []).map(dn => dn.id);
    const allSelected = memberIds.every(id => form.value.dharma_name_ids.includes(id));
    if (allSelected) { form.value.dharma_name_ids = form.value.dharma_name_ids.filter(id => !memberIds.includes(id)); }
    else { memberIds.forEach(id => { if (!form.value.dharma_name_ids.includes(id)) form.value.dharma_name_ids.push(id); }); }
};

const isGroupFullySelected = (group) => {
    if (!group.dharma_names || group.dharma_names.length === 0) return false;
    return group.dharma_names.every(dn => form.value.dharma_name_ids.includes(dn.id));
};

const removeMagicItemByGroup = (gName) => {
    form.value.items = form.value.items.filter(item => item.treasure_name !== gName);
};

const fetchItems = async (page = 1) => {
    loading.value = true;
    try {
        const params = { 
            master_id: currentFolder.value?.id, 
            page: page 
        };
        const isDaily = currentFolder.value?.id == 0 || currentFolder.value?.id === '0';
        if (isDaily) {
            params.is_daily = 1;
            params.per_page = 100;
        }
        // In Master folders, we don't pass is_daily so it fetches all records for that master

        const res = await axios.get('/other-teachings', { params });
        if (page === 1) { 
            visibleItems.value = res.data.data;
        }
        else {
            const existing = new Set(visibleItems.value.map(i => i.id));
            res.data.data.forEach(i => { if (!existing.has(i.id)) visibleItems.value.push(i); });
        }
        itemPagination.value = { current_page: res.data.current_page, last_page: res.data.last_page };
    } catch (e) { console.error(e); } finally { loading.value = false; }
};

const handleScroll = (e) => {
    const { scrollTop, scrollHeight, clientHeight } = e.target;
    if (scrollTop + clientHeight >= scrollHeight - 100) {
        if (!loading.value && itemPagination.value.current_page < itemPagination.value.last_page) {
            fetchItems(itemPagination.value.current_page + 1);
        }
    }
};

const syncRecords = async () => {
    try {
        const [dnRes, groupRes, masterRes, treasureRes] = await Promise.allSettled([
            axios.get('/api/dharma-names-list'), 
            axios.get('/api/groups-list'),
            axios.get('/api/masters-list'), 
            axios.get('/api/treasures-list', { params: { type: ['teaching', 'content'] } })
        ]);
        if (dnRes.status === 'fulfilled') dharmaNames.value = dnRes.value.data;
        if (groupRes.status === 'fulfilled') groups.value = groupRes.value.data || [];
        if (masterRes.status === 'fulfilled') masters.value = masterRes.value.data || [];
        if (treasureRes.status === 'fulfilled') treasures.value = (treasureRes.value.data || []).filter(t => t.name !== '清煞法寶');
    } catch (e) { console.error(e); }
};

const getDharmaNameText = (id) => {
    const dn = dharmaNames.value.find(x => x.id === id);
    return dn ? stripMasterPrefix(dn.name) : '';
};


const editItem = (item) => { 
    editingId.value = item.id; 
    form.value = { 
        ...item, 
        dharma_name_ids: item.dharma_names?.map(dn => dn.id) || [] 
    }; 
    masterNameInput.value = item.master?.name || item.master_name || ''; 
    stagedContents.value = []; // Clear previous staging when editing
    addMode.value = true; 
};

const deleteItem = async (id) => { if (!confirm('確定刪除？')) return; try { await axios.delete(`/other-teachings/${id}`); fetchItems(1); } catch (e) { alert('刪除失敗'); } };

const duplicateItem = (item) => { 
    form.value = { ...item, id: null, date: new Date().toLocaleDateString('en-CA'), dharma_name_ids: item.dharma_names.map(dn => dn.id) }; 
    editingId.value = null; addMode.value = true; 
};

const removeMagicItem = (uid) => { 
    form.value.items = form.value.items.filter(item => item.uid !== uid); 
};

const buildTreasureDetails = (cat, t, h, d, sz, sh, pr, itemName = '', bodyPart = '', instrument = '', sun = '', moon = '', light = '', mainTreasureName = '') => {
    let parts = [];
    if (cat === '三光金丹') {
        if (sun) parts.push(`日-${sun} 金丹吃`);
        if (moon) parts.push(`月-${moon} 金丹吃`);
        if (light) parts.push(`光-${light} 金丹吃`);
        if (d) parts.push(`${d} 天份`);
    } else if (cat === '金丹' || (itemName && (itemName.includes('丹') || itemName.includes('金丹'))) || (mainTreasureName && (mainTreasureName.includes('七素') || mainTreasureName.includes('七表'))) || (itemName && (itemName.includes('七素') || itemName.includes('七表')))) {
        const pillLabel = '金丹';
        if (t) parts.push(`${t} ${pillLabel}吃`.replace('  ', ' ').trim());
        if (h) parts.push(`${h} ${pillLabel}洗`.replace('  ', ' ').trim());
        if (d) parts.push(`${d} 天份`);
        return parts.join(' ');
    } else if (cat === '符令' || cat === '太令') {
        if (sz) parts.push(sz);
        if (d) parts.push(`${d} 天份`);
        if (pr) {
            if (isSpecialInstrument(itemName)) {
                parts.push(`執法-${pr}`);
            } else {
                parts.push(`由-${pr} 開立`);
            }
        }
        // Even for Fu-Ling, show instrument/bodyPart if filled (e.g. Special Fu-Ling)
        if (instrument) parts.push(`法器-${instrument}`);
        if (bodyPart) parts.push(`清煞部位-${bodyPart}`);
    } else if (cat === '香環') {
        if (d) parts.push(`${d} 個`);
        if (sh) parts.push(`${sh} 盒`);
    } else if (cat === '福祿香') {
        if (d) parts.push(`${d} 根`);
        if (sh) parts.push(`${sh} 包`);
    } else {
        // Broaden instrument check
        const isInst = isSpecialInstrument(itemName) || itemName?.includes('執法') || !!instrument || !!bodyPart;
        
        if (isInst) {
            if (pr) parts.push(`執法-${pr}`);
            if (instrument) parts.push(`法器-${instrument}`);
            if (bodyPart) parts.push(`清煞部位-${bodyPart}`);
            if (d) parts.push(`${d} 天份`);
        } else {
            if (d) parts.push(`${d} 天份`);
            if (pr) parts.push(`執法-${pr}`);
        }
    }
    return parts.join(' ').trim();
};

function stageContent() {
    if (!newItemSubName.value) return;
    
    const details = buildTreasureDetails(
        magicItemSubCategory.value,
        newItemSubTimes.value,
        newItemSubHours.value,
        newItemDetailsExtraDays.value,
        newItemSubSize.value,
        newItemSubSheets.value,
        newItemSubPractitioner.value,
        newItemSubName.value,
        newItemSubBodyPart.value,
        newItemSubInstrumentName.value,
        newItemSubSun.value,
        newItemSubMoon.value,
        newItemSubLight.value,
        newItemName.value
    );

    stagedContents.value.push({
        uid: Date.now() + Math.random(),
        name: newItemSubName.value,
        details: details,
        remarks: newItemRemarks.value
    });

    // Reset sub-fields
    newItemSubName.value = '';
    newItemDetailsExtraDays.value = '';
    newItemRemarks.value = '';
    newItemSubSize.value = '';
    newItemSubTimes.value = '';
    newItemSubHours.value = '';
    newItemSubSheets.value = '';
    newItemSubPractitioner.value = '';
    newItemPractitioner.value = '';
    newItemSubDetails.value = '';
    newItemSubBodyPart.value = '';
    newItemSubInstrumentName.value = '';
    newItemSubSun.value = '';
    newItemSubMoon.value = '';
    newItemSubLight.value = '';
    newItemSun.value = '';
    newItemMoon.value = '';
    newItemLight.value = '';
    showAddDetails.value = false;
    showSubRemarks.value = false;
    showMainRemarks.value = false;
}

function addNewItemQuickly() {
    if (!newItemName.value || isSaving.value) return;
    
    isSaving.value = true;
    setTimeout(() => { isSaving.value = false; }, 400); // Prevent rapid-fire

    // Correctly capture Days, Size, and Sheets from specialized input sources
    const dVal = newItemMainDays.value || newItemDays.value || newItemSubDetails.value || '';
    const szVal = newItemSubSize.value || '';
    const shVal = newItemSubSheets.value || '';

    // Use buildTreasureDetails for main item header to ensure SevenElement rules apply
    let finalDetails = buildTreasureDetails(
        magicItemCategory.value,
        newItemMainTimes.value,
        newItemMainHours.value,
        dVal,
        szVal,
        shVal,
        newItemPractitioner.value,
        newItemName.value, // itemName
        newItemSubBodyPart.value, // bodyPart
        newItemSubInstrumentName.value, // instrument
        newItemSun.value,
        newItemMoon.value,
        newItemLight.value,
        newItemName.value
    );

    const mainItem = {
        treasure_name: newItemName.value,
        details: finalDetails,
        main_remarks: newItemMainRemarks.value || '',
    };

    // If we have staged contents, commit them as sub-items
    if (stagedContents.value.length > 0) {
        // First, if the main item has details, ensure they are saved as the "header" details (item with empty name)
        if (mainItem.details) {
            form.value.items.push({
                uid: Date.now() + Math.random(),
                treasure_name: mainItem.treasure_name,
                details: mainItem.details,
                name: '',
                sub_name: mainItem.main_remarks
            });
        }
        stagedContents.value.forEach((sc) => {
            const combinedRemarks = [sc.remarks, mainItem.main_remarks].filter(r => r).join(' ');
            form.value.items.push({
                uid: Date.now() + Math.random(),
                treasure_name: mainItem.treasure_name,
                details: sc.details,
                name: sc.name,
                sub_name: combinedRemarks
            });
        });
    } else {
        const combinedRemarks = [newItemRemarks.value, mainItem.main_remarks].filter(r => r).join(' ');
        const effectiveCategory = newItemSubName.value ? magicItemSubCategory.value : magicItemCategory.value;

        // Use appropriate source variables based on whether we are adding a detail or the main item
        let directDetails = buildTreasureDetails(
            effectiveCategory,
            newItemSubName.value ? newItemSubTimes.value : newItemMainTimes.value,
            newItemSubName.value ? newItemSubHours.value : newItemMainHours.value,
            newItemSubName.value ? newItemDetailsExtraDays.value : (newItemMainRemarks.value.trim() ? '' : (newItemMainDays.value || newItemDays.value || newItemSubDetails.value)),
            newItemSubSize.value,
            newItemSubSheets.value,
            newItemSubName.value ? newItemSubPractitioner.value : newItemPractitioner.value,
            newItemSubName.value || newItemName.value,
            newItemSubBodyPart.value,
            newItemSubInstrumentName.value,
            newItemSubName.value ? newItemSubSun.value : newItemSun.value,
            newItemSubName.value ? newItemSubMoon.value : newItemMoon.value,
            newItemSubName.value ? newItemSubLight.value : newItemLight.value,
            newItemName.value
        );

        // Logic fix: Ensure header details are always present if mainItem has details
        if (mainItem.details) {
            // Push header record if it doesn't exist for this treasure in current session
            const exists = form.value.items.some(i => i.treasure_name === mainItem.treasure_name && !i.name);
            if (!exists) {
                form.value.items.push({
                    uid: Date.now() + Math.random(),
                    treasure_name: mainItem.treasure_name,
                    details: mainItem.details,
                    name: '',
                    sub_name: mainItem.main_remarks
                });
            } else if (!newItemSubName.value) {
                // If only main stats updated and no sub-name, update existing header details
                const idx = form.value.items.findIndex(i => i.treasure_name === mainItem.treasure_name && !i.name);
                if (idx !== -1) form.value.items[idx].details = mainItem.details;
            }
        }

        // Push sub-item if provided
        if (newItemSubName.value) {
            form.value.items.push({
                uid: Date.now() + Math.random(),
                treasure_name: mainItem.treasure_name,
                details: directDetails,
                name: newItemSubName.value,
                sub_name: combinedRemarks
            });
        } else if (!mainItem.details) {
            // Fallback for simple items
            form.value.items.push({
                uid: Date.now() + Math.random(),
                treasure_name: mainItem.treasure_name,
                details: '',
                name: '',
                sub_name: combinedRemarks
            });
        }
    }

    // Reset everything
    newItemName.value = '';
    newItemDays.value = '';
    newItemMainTimes.value = '';
    newItemMainHours.value = '';
    newItemMainDays.value = '';
    newItemMainRemarks.value = '';
    newItemDetailsExtraDays.value = '';
    newItemSubName.value = '';
    newItemSubDetails.value = '';
    newItemSubTimes.value = '';
    newItemSubHours.value = '';
    newItemSubSize.value = '';
    newItemSubSheets.value = '';
    newItemSubPractitioner.value = '';
    newItemPractitioner.value = '';
    newItemSubDetails.value = '';
    newItemSubBodyPart.value = '';
    newItemSubInstrumentName.value = '';
    newItemRemarks.value = '';
    showMainRemarks.value = false;
    showSubRemarks.value = false;
    stagedContents.value = [];
    showAddDetails.value = false;
    // Flash success
    isAddingFlash.value = true;
    setTimeout(() => { isAddingFlash.value = false; }, 600);

    // Refocus the treasure input
    nextTick(() => {
        if (treasureInput.value) {
            treasureInput.value.focus();
        }
    });
}

const addNewCategory = () => {
    form.value.items.push({ uid: Date.now() + Math.random(), treasure_name: '', name: '', sub_name: '', details: '' });
};

const addSubItemToGroup = (gName) => {
    // If gName is empty, we just add to the first empty or generic group
    form.value.items.push({ uid: Date.now() + Math.random(), treasure_name: gName, name: '', sub_name: '', details: '' });
    // Auto-expand to show the new sub-item
    expandedDetails.value[gName] = true;
};

const removeGroup = (gName) => {
    form.value.items = form.value.items.filter(item => item.treasure_name !== gName);
};

const updateGroupName = (oldName, newName) => {
    form.value.items.forEach(item => {
        if (item.treasure_name === oldName) item.treasure_name = newName;
    });
};

const groupItems = (items) => {
    const groups = {};
    if (!items) return groups;
    items.forEach((item, index) => { 
        const key = item.treasure_name || '未命名法寶'; 
        if (!groups[key]) groups[key] = []; 
        groups[key].push({ ...item }); 
    });
    return groups;
};

const shouldHideContentName = (treasureName, contentName) => {
    const isSeven = (treasureName || '').includes('七素') || (treasureName || '').includes('七表');
    return isSeven && contentName === '金丹';
};

const generateSummary = (item) => { 
    if (!item?.items || item.items.length === 0) return ''; 
    const first = item.items[0];
    const name = formatTreasureName(first.treasure_name || first.name); 
    const details = first.details ? ` (${first.details})` : '';
    return name + details + (item.items.length > 1 ? ` 等${item.items.length}項` : ''); 
};

const getFirstItemNameOnly = (item) => {
    if (!item?.items || item.items.length === 0) return '';
    const first = item.items[0];
    const name = formatTreasureName(first.treasure_name || first.name);
    const details = first.details ? ` (${first.details})` : '';
    const more = item.items.length > 1 ? ` ...等${item.items.length}項` : '';
    return name + details + more;
};

const formatTreasureName = (name) => name ? name.split(':').pop() : '';

const getMasterName = (id) => {
    if (!id || id == 0) return '父皇仙師';
    const m = masters.value.find(x => x.id == id);
    return m ? m.name : '未指定';
};

const getItemCount = (id) => markings.value?.[id] || 0; // Simplified
const markings = ref({});

const getRecipientName = (item) => {
    const listInfo = getFullRecipientList(item);
    // Priority 1: Pasted name from batch import or manual target remarks
    const pastedName = item.dharmaSearchQuery || item.target_remarks;
    
    // If no formal list found, return the pasted name or default to "All members"
    if (!listInfo) return (pastedName || '全體成員');
    
    // Priority 2: If the pasted name exactly matches the detected group name, just use it
    if (listInfo.groupName && pastedName && pastedName.includes(listInfo.groupName)) return pastedName;
    
    const remarks = (item.target_remarks && item.target_remarks !== listInfo.groupName) ? ` (${item.target_remarks})` : '';
    
    if (listInfo.groupName) {
        return `${listInfo.groupName}${remarks}`;
    }
    return listInfo.names.join(', ') + remarks;
};

const getFullRecipientList = (item) => {
    const names = (item.dharma_names || []).map(dn => dn.name);
    const pendingIds = item.dharma_name_ids || [];
    
    let resolvedNames = [...names];
    if (resolvedNames.length === 0 && pendingIds.length > 0) {
        resolvedNames = pendingIds.map(id => dharmaNames.value.find(dn => dn.id === id)?.name).filter(n => n);
    }
    
    if (resolvedNames.length === 0) return null;

    // Group detection logic with robust ID normalization
    const normalizedItemIds = (item.dharma_names || []).map(d => Number(d.id)).sort((a, b) => a - b).join(',');
    const normalizedPendingIds = (pendingIds || []).map(id => Number(id)).sort((a, b) => a - b).join(',');
    const searchIds = normalizedItemIds || normalizedPendingIds;
    
    let groupName = null;
    
    if (searchIds && (groups.value || []).length > 0) {
        const matched = groups.value.find(g => {
            const gIds = (g.dharma_names || []).map(dn => Number(dn.id)).sort((a, b) => a - b).join(',');
            return gIds === searchIds;
        });
        if (matched) groupName = matched.name;
    }

    if (groupName === '各宮') {
        groupName = '玄通宮、玄應宮、玄心宮、玄妙宮、玄昇宮、玄願宮、玄法宮、玄閻宮、玄窕宮、玄瑤宮、玄義宮';
    } else {
        const groupToPalace = {
            '第一組': '玄通宮', '第二組': '玄應宮', '第三組': '玄心宮', '第四組': '玄妙宮',
            '第五組': '玄昇宮', '第六組': '玄願宮', '第七組': '玄法宮', '第八組': '玄閻宮',
            '第九組': '玄窕宮', '第十組': '玄瑤宮', '第十一組': '玄義宮'
        };
        if (groupToPalace[groupName]) {
            groupName = groupToPalace[groupName];
        }
    }

    // Sort names by official database order
    const nameToObj = new Map();
    dharmaNames.value.forEach(dn => nameToObj.set(dn.name, dn));

    resolvedNames.sort((a, b) => {
        const objA = nameToObj.get(a);
        const objB = nameToObj.get(b);
        
        if (objA && objB) return objA.order - objB.order;
        if (objA) return -1;
        if (objB) return 1;
        return a.localeCompare(b, 'zh-TW', { collation: 'stroke' });
    });

    return { groupName, names: resolvedNames };
};

const getRecordHeader = (item, index, allRecords) => {
    let masterName = item.master?.name || item.master_name;
    if (!masterName) {
        masterName = '父皇仙師';
    }
    const remarks = item.target_remarks ? ` (${item.target_remarks})` : '';
    const recipient = getRecipientName(item);

    // User requested to always include master name even if deduplicated
    return `${masterName}開示給：${recipient}${remarks}`;
};

const isSameSessionAsNext = (item, index, allRecords) => {
    if (!allRecords || index >= allRecords.length - 1) return false;
    const next = allRecords[index + 1];
    
    const masterName = item.master?.name || item.master_name || '父皇仙師';
    const nextMaster = next.master?.name || next.master_name || '父皇仙師';
    
    return masterName === nextMaster && item.date === next.date;
};

const isSessionFocused = (item) => {
    if (!focusedId.value) return false;
    // Check main ID first
    if (focusedId.value === item.id) return true;
    
    // Check if they are from the same session
    const focusedItem = visibleItems.value.find(i => i.id === focusedId.value);
    if (!focusedItem) return false;
    
    const m1 = item.master?.name || item.master_name || '父皇仙師';
    const m2 = focusedItem.master?.name || focusedItem.master_name || '父皇仙師';
    
    return item.date === focusedItem.date && m1 === m2;
};

const toggleExpand = (id) => { 
    if (focusedId.value == id) {
        focusedId.value = null;
    } else {
        focusedId.value = id;
    }
    inlineEditingId.value = null;
    activeDropdownId.value = null;
    if (showRecipientDetails.value) showRecipientDetails.value = {};
};

const isDateOfFocused = (date) => {
    if (!focusedId.value) return true;
    const matchedItem = visibleItems.value.find(i => i.id == focusedId.value);
    return matchedItem ? matchedItem.date === date : false;
};

const handleBack = () => { 
    if (addMode.value) { 
        addMode.value = false; 
        editingId.value = null; 
    } else if (currentFolder.value) { 
        const isDaily = currentFolder.value.id === 0 || currentFolder.value.id === '0';
        currentFolder.value = null; 
        if (isDaily) currentCategory.value = null;
        
        // If restricted, skip category selection and go home when backing out of a folder
        if (props.user?.permissions && !props.user.permissions.can_see_daily_teachings) {
            currentCategory.value = null;
            emit('goHome');
        }
    } else if (currentCategory.value) { 
        currentCategory.value = null; 
    } else { 
        emit('goHome'); 
    } 
};

const copyAsTextFile = (item) => {
    try {
        const text = formatTeachingForFile(item);
        navigator.clipboard.writeText(text);
        alert('內容已複製到剪貼簿');
    } catch (err) {
        console.error('Copy failed:', err);
    }
};

const formatTeachingForFile = (item, index = null, allRecords = []) => {
    const recipient = getRecipientName(item);
    const grouped = groupItems(item.items);
    let treasureLines = [];
    const groupKeys = Object.keys(grouped);
    
    groupKeys.forEach((gName, gIdx) => {
        const group = grouped[gName];
        let line = groupKeys.length > 1 ? `${gIdx + 1}. ${gName}` : gName;
        
        if (group.length === 1 && !group[0].name && !group[0].sub_name) {
            if (group[0].details) line += `: ${group[0].details}`;
            treasureLines.push(line);
        } else if (group.length === 1) {
            line += `: ${group[0].name || ''} ${group[0].sub_name || ''} ${group[0].details || ''}`.replace(/\s+/g, ' ').trim();
            treasureLines.push(line);
        } else {
            treasureLines.push(line);
            group.forEach(m => {
                if (m.name || m.sub_name || m.details) {
                    treasureLines.push(`   ${m.name || ''}${m.details ? ':' + m.details : ''}${m.sub_name ? ' (' + m.sub_name + ')' : ''}`.trimEnd());
                }
            });
        }
    });

    const treasureText = treasureLines.length > 0 ? '\n賜降：\n' + treasureLines.join('\n') : '';
    const safeContent = (item.content && item.content !== 'null') ? '\n' + item.content : '';
    
    let footer = form.value?.items_footer_remarks ? '\n\n' + form.value.items_footer_remarks : '';
    let headerLabel = (item.content && item.content !== 'null') ? '開示給' : '給';
    return `${(item.date || '').replace(/-/g, '/')}\n${item.master?.name || (item.master_name || '仙師')}${headerLabel}${recipient}：${safeContent}${treasureText}${footer}`;
};

const formatTeachingForExport = (item, index = null, allRecords = []) => {
    const recipient = getRecipientName(item);
    const grouped = groupItems(item.items);
    let treasureLines = [];
    const groupKeys = Object.keys(grouped);
    
    groupKeys.forEach((gName, gIdx) => {
        const group = grouped[gName];
        // "沒有項次就不要加項次" (If only one group, no numbering like 1. )
        let line = groupKeys.length > 1 ? `${gIdx + 1}. ${gName}` : gName;
        
        if (group.length === 1 && !group[0].name && !group[0].sub_name) {
            if (group[0].details) line += `: ${group[0].details}`;
            treasureLines.push(line);
        } else if (group.length === 1) {
            line += `: ${group[0].name || ''} ${group[0].sub_name || ''} ${group[0].details || ''}`.replace(/\s+/g, ' ').trim();
            treasureLines.push(line);
        } else {
            treasureLines.push(line);
            group.forEach(m => {
                if (m.name || m.sub_name || m.details) {
                    // Indentation: 3 spaces (as requested "3PX")
                    treasureLines.push(`   ${m.name || ''}${m.details ? ':' + m.details : ''}${m.sub_name ? ' (' + m.sub_name + ')' : ''}`.trimEnd());
                }
            });
        }
    });

    const treasureText = treasureLines.length > 0 ? '\n賜降：\n' + treasureLines.join('\n') : '';
    const safeContent = (item.content && item.content !== 'null') ? '\n' + item.content : '';
    
    let footer = item.items_footer_remarks ? '\n\n' + item.items_footer_remarks : '';
    if (index !== null && allRecords.length > 0) {
        if (isSameSessionAsNext(item, index, allRecords)) {
            footer = '';
        }
    }

    let headerLabel = (item.content && item.content !== 'null') ? '開示給' : '給';
    return `${(item.date || '').replace(/-/g, '/')}\n${item.master?.name || (item.master_name || '仙師')}${headerLabel}${recipient}：${safeContent}${treasureText}${footer}`;
};

const downloadTeaching = (item) => {
    const text = formatTeachingForExport(item);
    const blob = new Blob([text], { type: 'text/plain' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `開示記錄_${item.date}.txt`;
    a.click();
    window.URL.revokeObjectURL(url);
};

const copyToLine = (item, index = null, allRecords = []) => {
    const text = formatTeachingForExport(item, index, allRecords);
    
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(text).then(() => {
            alert('內容已複製');
        }).catch(err => {
            console.error('Copy failed', err);
        });
    } else {
        const textArea = document.createElement("textarea");
        textArea.value = text;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        alert('內容已複製 (相容模式)');
    }
};

const processBatchText = () => {
    if (!batchImportContent.value.trim()) return;
    
    // Split by common delimiters (e.g., "完畢", horizontal lines, or multiple newlines)
    const rawBlocks = batchImportContent.value.split(/完畢[！!]?|--+|==+/);
    const newRecords = [];

    const nameAliasMap = {
        '金容': '靈果', '金涓': '靈慧', '金梅': '靈妙', '金蘭': '靈智', '金平': '靈平',
        '金瑞': '龍戰', '金耀': '龍勝', '金旭': '靈心', '金熙': '靈情', '金吉': '靈奇',
        '金祥': '靈傾', '金恩': '靈昡', '金鈺': '元續', '金穎': '赤峰',
        '金律': '閻㻇', '金欣': '閻闇', '閰琉': '閻尊', '金剛': '閰帝', '金頓': '閻爵',
        '金虹': '赤覺', '金湘': '紫元', '金雍': '道妙', '金無': '閻澤', '金真': '閻願',
        '金翎': '鳳尊', '金妙': '鳳媓'
    };
    const translateName = (n) => nameAliasMap[n] || n;
    
    const masterNames = ['老祖', '元始', '道祖', '靈寶', '父皇', '太宰', '太子', '閻王'];
    const masterOptions = ['老祖仙師', '元始仙師', '道祖仙師', '靈寶仙師', '父皇仙師', '太宰仙師', '太子', '閻王仙師'];
    
    rawBlocks.forEach(block => {
        let text = block.trim();
        if (!text || text.length < 5) return;
        
        const record = { 
            dharma_name_ids: [], content: '', dharmaSearchQuery: '', 
            target_remarks: '', items: [], master_name: '', date: null
        };
        
        // 0. Date Detection (Look for date patterns anywhere in the text)
        const dateMatch = text.match(/(\d{2,4}[\/\s.-]\d{1,2}[\/\s.-]\d{1,2})/) || text.match(/(\d{1,2}[\/\s.-]\d{1,2})/);
        if (dateMatch) {
            let dStr = dateMatch[0].trim();
            // Basic normalization: if it's M/D, prepend current year
            if (dStr.split(/[\/\s.-]/).length === 2) {
                dStr = new Date().getFullYear() + '/' + dStr;
            }
            try {
                const dObj = new Date(dStr.replace(/\s+/g, '/').replace(/\./g, '/').replace(/-/g, '/'));
                if (!isNaN(dObj.getTime())) {
                    let yyyy = dObj.getFullYear();
                    // Handle Minguo year (Taiwan calendar): e.g., 113 -> 2024
                    if (yyyy < 1000) {
                        yyyy += 1911;
                    }
                    const mm = String(dObj.getMonth() + 1).padStart(2, '0');
                    const dd = String(dObj.getDate()).padStart(2, '0');
                    record.date = `${yyyy}-${mm}-${dd}`;
                }
            } catch(e) {}
        }
        
        // 1. Master Detection
        let foundMaster = false;
        let matchedKeywordInText = '';
        masterNames.forEach((m, idx) => {
            const keyword = (m === '太子' ? '太子' : m + '仙師');
            if (text.includes(keyword)) {
                record.master_name = masterOptions[idx];
                matchedKeywordInText = keyword;
                foundMaster = true;
            }
        });
        // We do NOT fallback to currentFolder name here to avoid locking records to the current view during shunting
        
        // 2. Recipient Detection (Highly Robust Regex)
        // Match "開示給" followed by any characters until a colon, newline, or specific terminator
        const recMatch = text.match(/開示給\s*([^：:\n\r]*)/);
        let contentToParse = text;

        if (recMatch && recMatch[1].trim()) {
            const nameField = translateName(recMatch[1].trim());
            record.dharmaSearchQuery = nameField;
            record.target_remarks = nameField; // Essential for persistence
            
            const foundDN = dharmaNames.value.find(dn => dn.name === nameField);
            if (foundDN) record.dharma_name_ids = [foundDN.id];
            else {
                const foundGroup = (groups.value || []).find(g => g.name === nameField);
                if (foundGroup) {
                    record.dharma_name_ids = (foundGroup.dharma_names || []).map(dn => dn.id);
                }
            }
            // Clean content to remove the recipient line if it's a distinct line
            const fullMatch = recMatch[0];
            contentToParse = text.replace(fullMatch, '').trim();
            // Handle optional trailing colon if it was outside the capture group
            if (contentToParse.startsWith('：') || contentToParse.startsWith(':')) {
                contentToParse = contentToParse.substring(1).trim();
            }
        }

        // Clean up any remaining instances of the master name (headers/repetitions) 
        // to avoid duplication with the UI's fixed master header.
        if (matchedKeywordInText) {
            // Master name is extracted but NOT stripped to preserve "exactly same" content
        }
        
        // 3. Line-by-line Parsing (Heuristic for content vs items)
        const lines = contentToParse.split('\n');
        let contentLines = [];
        let parsingTreasures = false;

        lines.forEach(line => {
            const l = line.trim();
            if (!l) return;
            
            if (l.includes('賜降：')) {
                parsingTreasures = true;
                return;
            }
            
            // Heuristic: If it has a prefix (1. or +) AND (parsingTreasures OR contains treasure keywords)
            // OR it contains keywords like "法寶", "天份", "金丹", "符", "疏文"
            const hasTreasureKeywords = l.includes('法寶') || l.includes('天份') || 
                                       l.includes('金丹') || l.includes('符') || 
                                       l.includes('疏文');
            
            // Only auto-detect as item if we are in treasures section OR it's a short line with keywords
            const isItem = (parsingTreasures && l.match(/^(\d+\.|[+*])/)) || 
                           (hasTreasureKeywords && l.length < 60);
            
            if (isItem) {
                const tMatch = l.match(/^(\d+\.|[+*])?\s*(.*?)([:：]\s*(.*))?$/);
                if (tMatch) {
                    record.items.push({
                        uid: Date.now() + Math.random(),
                        treasure_name: tMatch[2].trim(),
                        details: tMatch[4] || '',
                        name: '', sub_name: ''
                    });
                }
            } else {
                contentLines.push(l);
            }
        });
        // Final record content is the cleaned text block (minus headers) plus "完畢" 
        // to match Image 3 visual standard.
        let cleanedRecordContent = stripContentHeaders(text);
        if (block.includes('完畢') && !cleanedRecordContent.includes('完畢')) {
            cleanedRecordContent += "\n\n完畢";
        }
        record.content = cleanedRecordContent;
        
        newRecords.push(record);
    });
    
    if (newRecords.length > 0) {
        batchRecords.value = newRecords;
    }
};

const handleBatchPaste = (e) => {
    const text = e.clipboardData.getData('text');
    if (!text) return;
    // Allow default paste, then trigger smart parse after a tick
    setTimeout(processBatchText, 200);
};

const moveBatchRecord = (index, direction) => {
    const targetIndex = index + direction;
    if (targetIndex < 0 || targetIndex >= batchRecords.value.length) return;
    
    // Swap elements in the reactive array
    const record = batchRecords.value[index];
    batchRecords.value.splice(index, 1);
    batchRecords.value.splice(targetIndex, 0, record);
};

const handleBatchFileImport = async (e) => {
    const file = e.target.files[0];
    if (!file) return;

    const reader = new FileReader();
    const ext = file.name.split('.').pop().toLowerCase();

    if (ext === 'txt' || ext === 'csv') {
        reader.onload = (res) => {
            batchImportContent.value = res.target.result;
            processBatchText();
        };
        reader.readAsText(file);
    } else if (ext === 'xlsx' || ext === 'xls') {
        reader.onload = (res) => {
            const data = new Uint8Array(res.target.result);
            const workbook = window.XLSX.read(data, { type: 'array' });
            const firstSheet = workbook.Sheets[workbook.SheetNames[0]];
            const rows = window.XLSX.utils.sheet_to_json(firstSheet, { header: 1 });
            batchImportContent.value = rows.map(r => r.join(' ')).join('\n');
            processBatchText();
        };
        reader.readAsArrayBuffer(file);
    } else if (ext === 'docx') {
        loading.value = true;
        try {
            if (!window.mammoth) {
                await new Promise((resolve, reject) => {
                    const script = document.createElement('script');
                    script.src = "https://cdnjs.cloudflare.com/ajax/libs/mammoth/1.4.21/mammoth.browser.min.js";
                    script.onload = resolve; script.onerror = reject; document.head.appendChild(script);
                });
            }
            reader.onload = async (res) => {
                const arrayBuffer = res.target.result;
                const result = await window.mammoth.extractRawText({ arrayBuffer: arrayBuffer });
                batchImportContent.value = result.value || '';
                processBatchText();
                loading.value = false;
            };
            reader.readAsArrayBuffer(file);
        } catch (err) {
            console.error(err);
            loading.value = false;
        }
    }
};

const toggleDateCollapse = (date) => {
    if (focusedDate.value === date) {
        // Closing the active focus -> restore all
        focusedDate.value = null;
        focusedId.value = null;
        // Make sure it returns to a collapsed state in the main list
        const newSet = new Set(collapsedDates.value);
        newSet.add(date);
        collapsedDates.value = newSet;
    } else if (focusedDate.value !== null) {
        // Trying to switch focus -> show prompt
        const oldDate = focusedDate.value.replace(/-/g, '/');
        const newDate = date.replace(/-/g, '/');
        if (confirm(`目前正在切閱「${oldDate}」的紀錄，是否關閉並改為查看「${newDate}」的開示內容？`)) {
            focusedDate.value = date;
            focusedId.value = null;
            const newSet = new Set(collapsedDates.value);
            newSet.delete(date); // Ensure it's expanded in focus mode
            collapsedDates.value = newSet;
        }
    } else {
        // Fresh focus: Hide others (focusedDate handles this) and ensure this one is expanded
        focusedDate.value = date;
        focusedId.value = null;
        allExpanded.value = false; // RESET: Ensure we start with summaries only
        const newSet = new Set(collapsedDates.value);
        newSet.delete(date); 
        collapsedDates.value = newSet;
    }
};

const handleDateInit = (date) => {
    // Moved initialization logic out of template to avoid re-renders
    return '';
};

watch(visibleItems, (newItems) => {
    const newCollapsed = new Set(collapsedDates.value);
    const newInit = new Set(initializedDates.value);
    let changed = false;
    newItems.forEach(item => {
        if (!newInit.has(item.date)) {
            newCollapsed.add(item.date);
            newInit.add(item.date);
            changed = true;
        }
    });
    if (changed) {
        collapsedDates.value = newCollapsed;
        initializedDates.value = newInit;
    }
}, { immediate: true });

const copyAllToLine = async () => {
    loading.value = true;
    try {
        const isDaily = currentFolder.value?.id == 0 || currentFolder.value?.id === '0';
        const params = { 
            master_id: currentFolder.value?.id, 
            is_daily: isDaily ? 1 : 0,
            per_page: 500 // Batch fetch enough records
        };
        const res = await axios.get('/other-teachings', { params });
        const allItems = res.data.data || res.data;
        const text = allItems.map((item, idx) => {
            return formatTeachingForExport(item, idx, allItems);
        }).join('\n\n\n');
        
        await navigator.clipboard.writeText(text);
        alert('已複製完整清單至剪貼簿');
    } catch (e) { alert('複製失敗'); } finally { loading.value = false; }
};

const clearTodayDaily = async () => {
    if (!confirm(`確定清空今日 (${form.value.date}) 的每日開示暫存區？\n此動作不會影響已分流至各仙師專區的原始紀錄。`)) return;
    loading.value = true;
    try {
        const res = await axios.get('/other-teachings', { params: { master_id: 5, per_page: 200, is_daily: 1 } });
        const items = (res.data.data || res.data).filter(i => i.date === form.value.date && i.is_daily);
        
        for (const item of items) {
            await axios.delete(`/other-teachings/${item.id}`);
        }
        
        alert('今日每日開示暫存區已清空 (仙師專區紀錄均已受保護)');
        fetchItems(1);
    } catch (e) {
        alert('清空失敗');
    } finally {
        loading.value = false;
    }
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

const exportListTxt = async () => {
    loading.value = true;
    try {
        const isDaily = currentFolder.value?.id == 0 || currentFolder.value?.id === '0';
        const params = { 
            master_id: currentFolder.value?.id, 
            is_daily: isDaily ? 1 : 0,
            per_page: 500
        };
        const res = await axios.get('/other-teachings', { params });
        const allItems = res.data.data || res.data;
        const text = allItems.map(item => {
            const dnText = item.dharma_names?.map(d => d.name).join(', ') || '全員';
            const grouped = groupItems(item.items);
            let treasLines = [];
            Object.keys(grouped).forEach((gName, gIdx) => {
                const group = grouped[gName];
                let line = `${gIdx + 1}. ${gName}`;
                if (group.length === 1 && !group[0].name && !group[0].sub_name) {
                    if (group[0].details) line += `: ${group[0].details}`;
                    treasLines.push(line);
                } else if (group.length === 1) {
                    line += `: ${group[0].name || ''} ${group[0].sub_name || ''} ${group[0].details || ''}`.replace(/\s+/g, ' ').trim();
                    treasLines.push(line);
                } else {
                    treasLines.push(line);
                    group.forEach(m => {
                        if (m.name || m.sub_name || m.details) {
                            treasLines.push(`      ${m.name || ''}${m.details ? ':' + m.details : ''}${m.sub_name ? ' (' + m.sub_name + ')' : ''}`.trimEnd());
                        }
                    });
                }
            });
            const treasuresStr = treasLines.join('\n');
            const safeContent = (item.content && item.content !== 'null') ? '\n' + item.content : '';
            return `【日期：${item.date?.replace(/-/g, '/')}】\n${item.master?.name || '仙師'}開示給${dnText}：${safeContent}${treasuresStr ? '\n賜降：\n'+treasuresStr : ''}\n\n完畢！`;
        }).join('\n\n' + '='.repeat(40) + '\n\n');
        
        const fileName = `開示記錄_${currentFolder.value?.name}_${getTodayStr()}.txt`;
        triggerSimpleDownload(text, fileName);
    } catch (e) { 
        console.error(e);
        alert('匯出失敗'); 
    } finally { 
        loading.value = false; 
    }
};

const saveItem = async () => {
    if (saving.value) return;

    // Sync active detail mode items back to the record block before saving
    if (itemsDetailMode.value && activeBatchIndex.value !== null) {
        batchRecords.value[activeBatchIndex.value].items = JSON.parse(JSON.stringify(form.value.items));
        // Also sync basic fields from form back to batchRecord if in batch mode
        if (activeEntryTab.value === 'batch') {
            const br = batchRecords.value[activeBatchIndex.value];
            br.content = form.value.content;
            br.date = form.value.date;
            br.dharma_name_ids = [...form.value.dharma_name_ids];
            br.target_remarks = form.value.target_remarks;
        }
    }

    // Resolve Master Name robustly:
    // 1. User Input
    // 2. Previously stashed record's master name (for session consistency)
    // 3. Selected Master ID mapping
    // 4. Folder name fallback
    let currentMasterName = masterNameInput.value;
    if (!currentMasterName && batchRecords.value.length > 0) {
        currentMasterName = batchRecords.value[batchRecords.value.length - 1].master_name;
    }
    if (!currentMasterName) {
        currentMasterName = masters.value.find(m => m.id === form.value.master_id)?.name;
    }
    if (!currentMasterName) {
        currentMasterName = (currentFolder.value?.name ? formatMasterName(currentFolder.value.name) : '父皇仙師');
    }
    // Validation
    // Validation: Allow all saves as per user request to "not show" the alert
    // The previous block checked for empty content/items and alerted.

    if (activeEntryTab.value === 'batch' || batchRecords.value.length > 1 || (batchRecords.value.length === 1 && (batchRecords.value[0].content || batchRecords.value[0].items.length > 0))) {
        // Requirement: If user hasn't clicked "Smart Parse" yet but has content in the textarea, do it now
        if (activeEntryTab.value === 'batch' && batchImportContent.value.trim()) {
            const hasSubstantialRecords = batchRecords.value.some(r => r.content?.trim() || r.items?.length > 0 || r.dharma_name_ids?.length > 0);
            if (!hasSubstantialRecords) {
                processBatchText();
            }
        }

        // If current form has content/items/persona not yet stashed, add it as a final block now
        if (form.value.content.trim() || form.value.items.length > 0 || form.value.dharma_name_ids.length > 0) {
            const currentBlock = {
                dharma_name_ids: [...form.value.dharma_name_ids],
                content: form.value.content,
                items: [...form.value.items],
                dharmaSearchQuery: dharmaSearchQuery.value || '',
                master_name: currentMasterName,
                target_remarks: form.value.target_remarks
            };
            
            // Fix: Be more inclusive with what defines an "existing record" to prevent overwrites
            const first = batchRecords.value[0];
            const isFirstActuallyEmpty = !first.content && first.items.length === 0 && first.dharma_name_ids.length === 0;

            if (batchRecords.value.length === 1 && isFirstActuallyEmpty) {
                batchRecords.value = [currentBlock];
            } else {
                batchRecords.value.push(currentBlock);
            }
            form.value.items = [];
        }

        const blocksToProcess = batchRecords.value.filter(r => r.content.trim() || r.items.length > 0);
        if (blocksToProcess.length > 0) {
            saveConfirmModal.value.records = blocksToProcess;
            saveConfirmModal.value.show = true;
            itemsDetailMode.value = false;
            return;
        }
    } else {
        // Individual save confirmation
        saveConfirmModal.value.records = [{
            dharma_name_ids: [...form.value.dharma_name_ids],
            content: form.value.content,
            items: [...form.value.items],
            master_name: currentMasterName,
            target_remarks: form.value.target_remarks,
            date: form.value.date
        }];
        saveConfirmModal.value.show = true;
        showPreviewRecipients.value.clear(); // Reset for fresh preview
        itemsDetailMode.value = false;
        return;
    }
};

const performActualSave = async () => {
    if (saving.value) return;
    
    // Always distribution 'keep' mode as per user request to skip intermediate modals
    if (activeEntryTab.value === 'batch' || batchRecords.value.length > 1) {
        await executeDistributionSave('distribute');
        saveConfirmModal.value.show = false;
        return;
    }

    // Single item save logic
    saving.value = true;
    try {
        const payload = {
            ...form.value,
            master_id: form.value.master_id || currentFolder.value?.id,
            is_daily: (currentFolder.value?.id == 0 || currentFolder.value?.id === '0') ? 1 : 0
        };

        if (editingId.value) {
            await axios.put(`/other-teachings/${editingId.value}`, payload);
        } else {
            const res = await axios.post('/other-teachings', payload);
            if (res.data?.id) {
                focusedId.value = res.data.id;
                // Ensure the newly added item is scrolled into view
                setTimeout(() => {
                    const element = document.getElementById(`teaching-row-${res.data.id}`);
                    if (element) {
                        element.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                }, 600);
            }
        }
        
        saveConfirmModal.value.show = false;
        addMode.value = false;
        // Navigation: Reliable return to Daily Teachings category and folder
        currentCategory.value = 'daily';
        currentFolder.value = { id: 0, name: '父皇仙師每日開示' }; 
        fetchItems(1);
        alert('儲存成功');
    } catch (e) {
        console.error(e);
        alert('儲存失敗：' + (e.response?.data?.message || '伺服器錯誤'));
    } finally {
        saving.value = false;
    }
};

const distributionModal = ref({
    show: false,
    detectedNames: [],
    isDailyFolder: false,
    finalMasterId: null
});

const executeDistributionSave = async (mode) => {
    saving.value = true;
    const masterMap = { '老祖': 1, '元始': 2, '道祖': 3, '靈寶': 4, '父皇': 5, '太宰': 6, '太子': 7, '閻王': 8 };
    const mid = form.value.master_id || currentFolder.value?.id;
    const currentMasterId = (mid == 0 || mid === '0') ? null : mid;

    // Requirement: If user hasn't clicked "Smart Parse" yet, do it automatically
    if (activeEntryTab.value === 'batch' && batchImportContent.value.trim()) {
        const hasSubstantialRecords = batchRecords.value.some(r => r.content?.trim() || r.items?.length > 0 || r.dharma_name_ids?.length > 0);
        if (!hasSubstantialRecords) {
            processBatchText();
        }
    }

    try {
        // Iterate through each block record
        for (const record of batchRecords.value) {
            let content = record.content?.trim() || '';
            let items = [...(record.items || [])];
            
            // Requirement: If user typed into the card manually, we should try to extract items from content
            if (content && items.length === 0) {
                const lines = content.split('\n');
                let remainingContent = [];
                let parsingTreasures = false;
                
                lines.forEach(line => {
                    const l = line.trim();
                    if (!l) return;
                    if (l.includes('賜降：')) { parsingTreasures = true; return; }
                    
                    const isItem = parsingTreasures || l.match(/^(\d+\.|[+*])/) || 
                                   l.includes('法寶') || l.includes('天份') || 
                                   l.includes('金丹') || l.includes('符') || 
                                   l.includes('疏文');
                                   
                    if (isItem) {
                        const tMatch = l.match(/^(\d+\.|[+*])?\s*(.*?)([:：]\s*(.*))?$/);
                        if (tMatch) {
                            items.push({
                                uid: Date.now() + Math.random(),
                                treasure_name: tMatch[2].trim(),
                                details: tMatch[4] || '',
                                name: '', sub_name: ''
                            });
                        }
                    } else {
                        remainingContent.push(l);
                    }
                });
                content = remainingContent.join('\n');
            }

            // Allow save if there is content OR items OR names selected (as per user request)
            if (!content && items.length === 0 && record.dharma_name_ids.length === 0) continue;

            let blockMasterId = currentMasterId;
            
            // Auto-detect Master/Folder if in distribute mode
            if (mode === 'distribute') {
                // Priority 1: Check specifically detected master_name from parsing
                let detectedId = null;
                if (record.master_name) {
                    const mClean = record.master_name.replace('仙師', '').trim();
                    if (masterMap[mClean]) detectedId = masterMap[mClean];
                }
                
                // Priority 2: Scan content if priority 1 failed or to confirm
                if (!detectedId) {
                    for (const [key, id] of Object.entries(masterMap)) {
                        if (content.includes(key)) {
                            detectedId = id;
                            break;
                        }
                    }
                }

                if (detectedId) {
                    blockMasterId = detectedId;
                } else {
                    // Fallback to current folder if no master was mentioned at all
                    blockMasterId = currentMasterId || 5; 
                }
            }

            const isDailyFolder = (currentFolder.value?.id == 0 || currentFolder.value?.id === '0');
            
            let finalDharmaIds = [...(record.dharma_name_ids || [])];
            let finalTargetRemarks = record.target_remarks || form.value.target_remarks || '';

            // If no IDs but have a search query (manual type), try to resolve it
            if (finalDharmaIds.length === 0 && record.dharmaSearchQuery) {
                const nameField = record.dharmaSearchQuery.trim();
                const foundDN = dharmaNames.value.find(dn => dn.name === nameField);
                if (foundDN) {
                    finalDharmaIds = [foundDN.id];
                } else {
                    const foundGroup = (groups.value || []).find(g => g.name === nameField);
                    if (foundGroup) {
                        finalDharmaIds = (foundGroup.dharma_names || []).map(dn => dn.id);
                    } else {
                        // If still not found, put it in target_remarks so user can see who it's for
                        finalTargetRemarks = (finalTargetRemarks ? finalTargetRemarks + ' ' : '') + nameField;
                    }
                }
            }

            const payload = {
                date: record.date || form.value.date || getTodayStr(),
                master_id: blockMasterId,
                content: content,
                dharma_name_ids: finalDharmaIds.length > 0 ? finalDharmaIds : (form.value.dharma_name_ids || []),
                target_remarks: finalTargetRemarks,
                items: items,
                user_id: 1,
                is_daily: (activeEntryTab.value === 'batch' || isDailyFolder) ? 1 : 0
            };
            
            const res = await axios.post('/other-teachings', payload);
            if (res.data?.id) {
                // Optimistic UI: Add to the beginning of the list to ensure visibility
                visibleItems.value.unshift(res.data);
                if (!focusedId.value) {
                    focusedId.value = res.data.id;
                    focusedDate.value = res.data.date;
                }
            }
        }
        
        if (focusedId.value) {
            setTimeout(() => {
                const element = document.getElementById(`teaching-row-${focusedId.value}`);
                if (element) {
                    element.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }, 600);
        }
        
        distributionModal.value.show = false;
        addMode.value = false;
        currentCategory.value = 'daily';
        currentFolder.value = { id: 0, name: '父皇仙師每日開示' }; 
        batchRecords.value = [{ dharma_name_ids: [], content: '', dharmaSearchQuery: '', target_remarks: '', items: [] }];
        fetchItems(1);
        alert('錄入成功');
    } catch (e) {
        alert('存檔過程中發生錯誤');
    } finally {
        saving.value = false;
    }
};

const showAdd = () => {
    editingId.value = null;
    form.value = {
        supplement: '', target_remarks: '', content: '',
        date: '', master_id: null, items: [], 
        remarks: '', items_footer_remarks: '', user_id: 1, dharma_name_ids: []
    };

    dharmaSearchQuery.value = '';
    masterNameInput.value = '';
    batchImportContent.value = ''; // Reset the collective paste area

    if (currentFolder.value) {
        const isDaily = currentFolder.value.id === 0 || currentFolder.value.id === '0';
        const mName = isDaily ? '父皇仙師' : currentFolder.value.name;
        const mId = isDaily ? 5 : currentFolder.value.id;
        
        form.value.master_id = mId;
        form.value.master_name = mName;
        masterNameInput.value = mName;
        
        batchRecords.value = [{ 
            dharma_name_ids: [], content: '', dharmaSearchQuery: '', 
            target_remarks: '', relatives: '', items: [], master_name: mName 
        }];
        
        if (isDaily) activeEntryTab.value = 'single';
        else activeEntryTab.value = 'batch';
    }
    addMode.value = true;
};

const showMultiMasterAdd = () => {
    editingId.value = null;
    form.value = {
        supplement: '', target_remarks: '', content: '',
        date: '', master_id: 5, items: [], 
        remarks: '', items_footer_remarks: '', user_id: 1, dharma_name_ids: []
    };

    dharmaSearchQuery.value = '';
    masterNameInput.value = '多位仙師'; 
    batchImportContent.value = ''; 
    batchRecords.value = [{ 
        dharma_name_ids: [], content: '', dharmaSearchQuery: '', 
        target_remarks: '', relatives: '', items: [], master_name: '' 
    }];
    
    activeEntryTab.value = 'batch';
    addMode.value = true;
};

watch(() => form.value.dharma_name_ids, (newIds) => {
    if (newIds.length === 1) {
        const name = getDharmaNameText(newIds[0]);
        if (dharmaSearchQuery.value !== name) dharmaSearchQuery.value = name;
    } else if (newIds.length === 0) {
        dharmaSearchQuery.value = '';
    } else {
        const isGroupMatch = (groups.value || []).some(g => g.name === dharmaSearchQuery.value);
        if (!isGroupMatch) {
            dharmaSearchQuery.value = '';
        }
    }
}, { deep: true });

watch(newItemName, (val) => {
    if (val) {
        showAddDetails.value = false;
        showMainRemarks.value = false;

        // Removed auto-add logic per user feedback that it triggers too quickly 
        // and prevents adding sub-items/using specialized modes.
        // User now explicitly clicks '+' or 'Add to Record' to submit.
    } else {
        // Requirement: If Main Name is cleared, clear everything associated with this entry
        newItemSubName.value = '';
        stagedContents.value = [];
        newItemSubInstrumentName.value = '';
        newItemSubPractitioner.value = '';
        newItemPractitioner.value = '';
        newItemSubBodyPart.value = '';
        newItemSubSize.value = '';
        newItemSubSheets.value = '';
        newItemSubDetails.value = '';
        newItemSubTimes.value = '';
        newItemSubHours.value = '';
        newItemMainTimes.value = '';
        newItemMainHours.value = '';
        newItemMainDays.value = '';
        newItemDetailsExtraDays.value = '';
        newItemMainRemarks.value = '';
        newItemRemarks.value = '';
        showMainRemarks.value = false;
        showSubRemarks.value = false;
        showAddDetails.value = false;
    }
});

watch(newItemSubName, (val) => {
    if (val) {
        showSubRemarks.value = false;
    }
});

watch(currentFolder, (val) => { if (val) { visibleItems.value = []; fetchItems(1); syncRecords(); } });

onMounted(syncRecords);
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar { width: 5px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
@keyframes slide-up { from { transform: translateY(100%); } to { transform: translateY(0); } }
.animate-slide-up { animation: slide-up 0.3s cubic-bezier(0.16, 1, 0.3, 1); }
@keyframes fade-in { from { opacity: 0; } to { opacity: 1; } }
.animate-fade-in { animation: fade-in 0.2s ease-out; }
.custom-date-input::-webkit-calendar-picker-indicator { margin-left: auto; cursor: pointer; }
</style>
 
