<template>
    <div class="bg-white h-full flex flex-col text-slate-900">
        <!-- Transition Logo Overlay -->
        <div v-if="loading" class="fixed inset-0 z-[9999] bg-white flex flex-col items-center justify-center pointer-events-none transition-opacity duration-300">
            <div class="relative flex flex-col items-center">
                <logo-imperial-notebook :height="120" spinning />
                <div class="mt-4 text-[17px] font-black text-slate-400 tracking-widest animate-pulse">載錄載入中...</div>
            </div>
        </div>
        <div class="bg-white h-full relative w-full shadow-sm flex flex-col font-sans">

            <!-- Global Main Title (Hidden when inside folder to avoid duplication) -->
            <div v-if="!currentFolder && !addMode" class="px-[15px] py-[10px] flex items-center bg-white border-b border-slate-100 relative min-h-[52px] cursor-pointer w-full z-[120]" @click="resetToRoot">
                <div class="flex-1 flex flex-col justify-start min-w-0 py-1 pl-1">
                    <div class="flex items-center gap-2">
                        <logo-imperial-notebook :height="36" class="md:hidden" />
                        <h1 class="text-red-600 leading-tight font-outfit tracking-tighter font-black whitespace-nowrap" style="color: #dc2626 !important; font-size: 24px !important; padding-top: 5px; font-weight: 900 !important;">父皇仙師開示專區</h1>
                    </div>
                </div>
            </div>
            <div v-if="(currentCategory !== null || currentFolder !== null || addMode) && !(currentCategory === 'masters' && !currentFolder && !addMode)" class="flex flex-col border-b border-slate-300 bg-white sticky top-0 z-[110] w-full">
                <!-- Header Row (only when close/back button is needed) -->
                <div v-if="focusedId || addMode" class="flex items-center justify-end px-3 py-2">
                    <button v-if="focusedId" @click.stop="focusedId = null" class="w-8 h-8 flex items-center justify-center bg-red-50 text-white rounded-2xl active:scale-90 transition-all border border-red-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" /></svg>
                    </button>
                    <button v-if="addMode" @click="addMode = false" class="text-slate-400 p-2 active:scale-90 transition-transform">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" /></svg>
                    </button>
                </div>
                <!-- Sub-header Row -->
                <div v-if="!addMode" class="px-[15px] pt-1 pb-[6px] bg-white flex items-end justify-between w-full">
                    <span :class="currentFolder && currentFolder.name === '閻王仙師' ? 'text-slate-900' : '!text-[#dc2626]'" class="font-outfit whitespace-nowrap !font-medium" style="font-size: 26px !important;">
                        {{ currentFolder ? (currentFolder.id === 0 ? '每日開示' : (currentFolder.name === '父皇' ? '父皇仙師' : currentFolder.name)) : '每日開示' }}
                    </span>
                    <div v-if="currentFolder !== null" class="flex items-center space-x-2 shrink-0 ml-4">
                        <button v-if="!reorderMode" @click="toggleSort" class="active:scale-95 transition-all font-black text-indigo-600" style="font-size: 16px !important;">
                            {{ sortDesc ? '新→舊' : '舊→新' }}
                        </button>
                        <button v-if="!focusedId" @click="reorderMode = !reorderMode" 
                                :class="reorderMode ? 'bg-emerald-600 text-white border-2 border-emerald-500 shadow-lg' : 'bg-slate-50 text-[#64748b] border border-transparent'"
                                class="px-3 py-1.5 rounded-xl font-black transition-all active:scale-95 whitespace-nowrap shadow-sm"
                                style="font-size: 16px !important;">
                            {{ reorderMode ? '確認排序' : '修改排序' }}
                        </button>
                    </div>
                </div>
            </div>

            <template v-if="currentCategory === null && currentFolder === null && !addMode">
                <div class="flex-1 overflow-y-auto custom-scrollbar bg-white w-full">
                    <div class="flex flex-col items-center pt-8 pb-20 w-full space-y-2.5 bg-white">
                        <!-- Category 1: Daily Teaching (Large Folder Style) -->
                        <button v-if="user?.permissions?.can_see_daily_teachings"
                            @click="currentCategory = 'daily'; currentFolder = folders_list.find(f => f.id === 0)"
                            class="flex flex-col items-center justify-center bg-white active:scale-95 transition-all group relative rounded-none w-full max-w-[465px]">
                            <div class="relative w-full max-w-[465px] aspect-[245/158] bg-white">
                                <picture><source srcset="/image/imperial_grace_book_v5.webp" type="image/webp"><img src="/image/imperial_grace_book_v5.png" fetchpriority="high" loading="eager" class="w-full h-full object-contain transition-transform group-hover:scale-105 mix-blend-multiply" alt="書籍圖示"></picture>
                                <!-- Label Inside -->
                                <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none font-biaokai-locked pt-2" style="transform: translateY(-10px);">
                                    <span class="font-black text-white drop-shadow-[0_2px_4px_rgba(0,0,0,0.5)] tracking-tighter leading-tight text-center font-biaokai-locked" style="font-weight: 900 !important; font-size: 38px !important; color: white !important;">
                                         父皇仙師<br>每日開示
                                     </span>
                                     <div class="mt-[22px] flex items-center font-biaokai-locked">
                                         <span class="font-black tracking-tight drop-shadow-sm whitespace-nowrap font-biaokai-locked text-white" style="font-size: 19px !important; color: white !important;">{{ folderCounts.daily || 0 }} 筆</span>
                                     </div>
                                </div>
                            </div>
                        </button>

                        <!-- Category 2: Master Teachings (Large Gold Folder Style) -->
                        <button v-if="user?.permissions?.can_see_teaching_folders"
                            @click="currentCategory = 'masters'"
                            class="flex flex-col items-center justify-center bg-white active:scale-95 transition-all group relative rounded-none w-full max-w-[465px]">
                            <div class="relative w-full max-w-[465px] aspect-[245/158] bg-white">
                                <picture><source srcset="/image/imperial_grace_book_v5.webp" type="image/webp"><img src="/image/imperial_grace_book_v5.png" fetchpriority="high" loading="eager" class="w-full h-full object-contain transition-transform group-hover:scale-105 mix-blend-multiply" alt="書籍圖示"></picture>
                                <!-- Label Inside -->
                                <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none font-biaokai-locked pt-2" style="transform: translateY(-10px);">
                                    <span class="font-black text-white drop-shadow-[0_2px_4px_rgba(0,0,0,0.5)] tracking-tighter leading-tight text-center font-biaokai-locked" style="font-weight: 900 !important; font-size: 38px !important; color: white !important;">
                                        父皇仙師<br>開示載錄
                                    </span>
                                    <div class="mt-[22px] flex items-center font-biaokai-locked">
                                        <span class="font-black tracking-tight drop-shadow-sm whitespace-nowrap font-biaokai-locked text-white" style="font-size: 19px !important; color: white !important;">{{ mastersTotalCount }} 筆</span>
                                    </div>
                                </div>
                            </div>
                        </button>
                    </div>
                </div>
            </template>

            <template v-else-if="currentCategory === 'masters' && !currentFolder && !addMode">
                <div class="flex-1 overflow-auto custom-scrollbar w-full">
                    <div class="flex items-center relative min-h-[52px] border-b border-slate-50 sticky top-0 bg-white z-30">
                        <button @click="currentCategory = null" class="p-4 text-slate-400 active:scale-90 transition-transform z-10">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" /></svg>
                        </button>
                        <div class="absolute inset-x-0 flex justify-center items-center pointer-events-none">
                            <span class="font-black" :class="currentCategory === 2 && currentFolder?.name === '閻王仙師' ? 'text-slate-900' : 'text-red-600'" style="font-size: 26px !important; display: inline-block; transform: translateY(2px);">父皇仙師開示載錄</span>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-x-0 gap-y-0.5 p-2 place-items-center">
                        <button v-for="(folder, idx) in filteredFolders" :key="folder.id" 
                            @click="currentFolder = folder"
                            class="flex flex-col items-center justify-center active:scale-95 transition-all group relative rounded-none w-[175px] h-[138px] flex-shrink-0 p-0 overflow-visible">
                            <div class="relative w-[175px] h-[138px] flex items-center justify-center overflow-visible">
                                <picture><source srcset="/image/imperial_grace_book_v5.webp" type="image/webp"><img src="/image/imperial_grace_book_v5.png" fetchpriority="high" loading="eager" class="w-[220px] h-[142px] max-w-none object-contain transition-transform group-hover:scale-105 mix-blend-multiply transform-gpu" style="will-change: transform;" alt="書籍圖示"></picture>
                                 <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none pt-12 font-biaokai-locked" style="transform: translateY(-20px);">
                                     <div class="font-black tracking-tighter leading-none text-center font-biaokai-locked text-white drop-shadow-[0_1px_2px_rgba(0,0,0,0.4)]"
                                          style="font-size: 11px !important;">父皇仙師開示載錄</div>

                                     <div class="font-black tracking-tight leading-tight text-center whitespace-nowrap !font-black mt-[2px] font-biaokai-locked text-white drop-shadow-[0_2px_4px_rgba(0,0,0,0.5)]"
                                          style="font-weight: 900 !important; font-size: 26px !important;">
                                          {{ folder.name === '父皇仙師' ? '父皇' : folder.name }}
                                     </div>
                                     <div class="mt-[2px] flex items-center font-biaokai-locked">
                                         <span class="font-black whitespace-nowrap drop-shadow-sm font-biaokai-locked text-white"
                                               style="font-size: 10.4px !important; line-height: 2; color: white !important;">{{ folderCounts[folder.id] || 0 }} 筆</span>
                                     </div>
                                 </div>
                            </div>
                        </button>
                    </div>
                </div>
            </template>

            <!-- Level 2: List & Add View -->
            <template v-else>
                <!-- New Step-by-Step Add Form -->
                <teaching-add-form 
                    v-if="addMode"
                    :mode="activeEntryTab === 'batch' ? 'batch' : 'single'"
                    :initial-data="form"
                    :masters="masters"
                    :dharma-names="dharmaNames"
                    :groups="groups"
                    :unique-treasure-names="uniqueTreasureNames"
                    :is-saving="saving"
                    @save="saveItem"
                    @close="addMode = false"
                />

                <!-- Main List View -->
                 <div :class="[!addMode ? 'block' : 'hidden md:block']" 
                      class="pb-32 flex-1 overflow-y-auto bg-white w-full md:max-w-xl md:mx-auto" 
                      @click="focusedId = null; focusedDate = null; activeDropdownId = null">
                     <div v-if="loading && visibleItems.length === 0" class="text-center py-12 text-slate-400 text-[20px] font-bold tracking-widest uppercase">載入紀錄中...</div>
                     <div v-else class="space-y-0 mt-0 min-h-full">
                          <!-- Search -->
                          <div v-if="showSearch" class="px-1 pb-3 animate-fade-in">
                              <div class="relative group">
                                  <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                      <svg class="h-5 w-5 text-indigo-400 group-focus-within:text-indigo-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                  </div>
                                  <input v-model="searchQuery" type="text" placeholder="搜尋內容、法號或法寶..."
                                      class="block w-full pl-11 pr-12 h-[52px] bg-slate-50 border-2 border-transparent focus:border-indigo-100 focus:bg-white rounded-2xl text-[17px] font-black font-outfit text-slate-800 placeholder-slate-300 transition-all outline-none shadow-sm">
                                  <button v-if="searchQuery" @click="searchQuery = ''" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-300 hover:text-red-500 transition-colors">
                                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                  </button>
                              </div>
                          </div>
                        <!-- Date-Based Accordion: Grouping by Daily sessions -->
                        <template v-for="dateGroup in recordsByDate" :key="dateGroup.date">
                            <!-- Date Header: Collapsed by Default -->
                            <div v-show="focusedId === null || isDateOfFocused(dateGroup.date)"
                                 @click.stop="toggleDateExpand(dateGroup.date)" 
                                 class="px-5 py-[20px] bg-white border-b border-slate-300 flex items-center justify-between cursor-pointer active:bg-slate-100 sticky top-0 z-[10] shadow-sm">
                                 <div class="flex items-center">
                                     <span class="app-title font-outfit uppercase tracking-wider">{{ formatDate(dateGroup.date) }}</span>
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
                                  class="py-[25px] px-[12px] flex flex-col cursor-pointer active:bg-slate-200 transition-colors bg-white border-b border-slate-300 shadow-sm"
                                  :class="[isSessionFocused(item) ? 'bg-slate-50 ring-2 ring-indigo-50/10' : '']">

                                <div class="flex items-center justify-between">
                                    <div class="flex items-center min-w-0 flex-1">
                                        <!-- Sequence Number / Reorder Input -->
                                        <div class="mr-3 shrink-0 flex items-center justify-center">
                                            <div v-if="!reorderMode" class="w-8 h-8 flex items-center justify-center text-[17px] font-black text-indigo-600">
                                                {{ String(index + 1).padStart(2, '0') }}
                                            </div>
                                            <input v-else 
                                                   type="number" 
                                                   :value="index + 1"
                                                   @click.stop
                                                   @change="e => handleReorder(item, e.target.value)"
                                                   class="w-10 h-9 bg-blue-50 border-2 border-blue-200 rounded-2xl text-center text-[15px] font-black text-blue-600 focus:ring-2 focus:ring-blue-400 outline-none">
                                        </div>

                                        <svg v-if="!reorderMode" :class="focusedId == item.id ? '' : 'rotate-[-90deg]'" class="w-4 h-4 text-slate-400 mr-2 transition-transform shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" /></svg>
                                        <div class="flex flex-col min-w-0">
                                            <div class="text-[14px] mb-1 font-bold text-slate-400 font-outfit tracking-tighter">
                                                {{ (item.date || '').replace(/-/g, '/') }}
                                            </div>
                                            <!-- Literal Record Content (Truncated to 3 lines) -->
                                            <template v-if="isContentLiteral(item)">
                                                <div class="text-[17px] font-black text-slate-900 leading-relaxed whitespace-pre-wrap mt-1 text-left">
                                                    {{ getFirstThreeLines(item.content) }}
                                                </div>
                                            </template>
                                            <template v-else>
                                                <!-- Title Row (Black Bold) -->
                                                <div class="text-[17px] font-black leading-none mb-1 text-slate-900">
                                                    <span :class="(item.master?.name || item.master_name) === '閻王仙師' ? 'text-slate-900' : 'text-red-600'">{{ formatMasterName(item.master?.name || item.master_name) }}</span>開示給:{{ getRecipientName(item) }}
                                                </div>
                                                <!-- Content Summary Row (3 Lines max) -->
                                                <div class="text-[17px] font-semibold text-slate-500 leading-relaxed whitespace-pre-wrap text-left">
                                                    <span v-if="item.content">{{ getFirstThreeLines(item.content) }}</span>
                                                    <span v-else-if="item.items?.length > 0">{{ item.items.map(i => i.treasure_name || i.name).join(', ') }}</span>
                                                </div>
                                            </template>
                                        </div>
                                    </div>

                                </div>

                            </div>

                                <!-- Full-page Expanded Overlay -->
                                <teleport to="body">
                                    <div v-if="isSessionFocused(item)" class="fixed inset-0 z-[500] animate-fade-in">
                                        <!-- Backdrop (click to close) -->
                                        <div class="absolute inset-0 bg-slate-900/50" @click="toggleExpand(item.id)"></div>
                                        <!-- Content Panel -->
                                        <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                                        <div class="w-full h-full bg-white flex flex-col md:max-w-xl md:max-h-[90dvh] md:rounded-[32px] md:shadow-2xl overflow-hidden animate-slide-up pointer-events-auto transform-gpu" style="will-change: transform;">
                                            <!-- Global Main Title (Added) -->
                                            <div class="px-[15px] py-[10px] flex items-center bg-white border-b border-slate-300 relative min-h-[52px] shrink-0">
                                                <div class="flex-1">
                                                    <div class="flex items-center gap-2">
                        <logo-imperial-notebook :height="36" class="md:hidden" />
                        <h1 class="font-black text-red-600 tracking-tight text-center whitespace-nowrap" style="font-size: 30px !important; color: #dc2626 !important; font-weight: 900 !important;">父皇仙師開示專區</h1>
                    </div>
                                                </div>
                                            </div>

                                            <!-- Sub-folder Title (Added for consistency) -->
                                            <div class="pt-[5px] pb-2 flex items-center justify-center border-b border-slate-50 bg-white min-h-[44px] relative">
                                                <span class="text-[24px] font-medium text-red-600 tracking-tight" style="font-size: 24px !important; color: #dc2626 !important; font-weight: 500 !important;">每日開示載錄</span>
                                                <button @click.stop="toggleExpand(item.id)" class="w-9 h-9 flex items-center justify-center bg-slate-100 text-slate-500 rounded-full active:scale-90 transition-all absolute right-3">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" /></svg>
                                                </button>
                                            </div>

                                            <!-- Header (Sub-title Style) -->
                                            <div class="px-[15px] flex items-center justify-between shrink-0 bg-white" :class="isContentLiteral(item) ? 'pt-4 pb-0' : 'py-4 border-b border-slate-100'">
                                                <div class="flex flex-col">
                                                    <div class="text-[15px] font-bold text-slate-400 mb-1">{{ (item.date || '').replace(/-/g, '/') }}</div>
                                                    <div v-if="!isContentLiteral(item)" class="text-[17px] font-black text-slate-900 leading-tight">
                                                        {{ formatMasterName(item.master?.name || item.master_name) }}開示給:{{ getRecipientName(item) }}
                                                    </div>
                                                </div>
                                                <div class="flex items-center space-x-1">
                                                    <div class="relative">
                                                        <button @click.stop="activeDropdownId = activeDropdownId === item.id ? null : item.id" class="w-10 h-10 flex items-center justify-center text-[#dc1428] active:scale-95 transition-transform">
                                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM18 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                                                        </button>
                                                        <div v-if="activeDropdownId === item.id" class="absolute right-0 top-full mt-2 w-48 bg-white rounded-3xl shadow-[0_20px_50px_rgba(0,0,0,0.15)] border border-slate-50 z-[600] p-1.5 focus:outline-none">
                                                            <div class="flex flex-col space-y-1">
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

                                            <!-- Scrollable Content -->
                                            <div class="flex-1 overflow-y-auto px-[15px] pb-32 custom-scrollbar" :class="isContentLiteral(item) ? 'pt-0' : 'pt-2'">
                                                <!-- View Mode -->
                                                <div v-if="inlineEditingId !== item.id" class="space-y-4">
                                                    <div v-if="getFullRecipientList(item)" class="space-y-2 mb-4">
                                                        <button @click.stop="toggleRecipientDetails(item.id)" 
                                                                class="flex items-center space-x-2 text-indigo-600 hover:text-indigo-800 transition-colors group py-1">
                                                            <div class="w-8 h-8 bg-indigo-50 rounded-full flex items-center justify-center">
                                                                <svg class="w-4 h-4 text-indigo-500" :class="showRecipientDetails[item.id] ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                                            </div>
                                                            <span class="text-[17px] font-black text-slate-900">點選查看對象詳情</span>
                                                        </button>
                                                        <div v-if="showRecipientDetails[item.id]" class="text-indigo-600 bg-indigo-50/50 rounded-2xl px-4 py-3 border border-indigo-100/50 space-y-1 animate-fade-in text-left">
                                                            <div v-if="getFullRecipientList(item).groupName" class="app-body text-indigo-700 leading-tight">{{ getFullRecipientList(item).groupName }}</div>
                                                            <div v-if="getFullRecipientList(item).names.length > 0" class="app-title !text-indigo-600/70 leading-snug">{{ getFullRecipientList(item).names.join(', ') }}</div>
                                                            <div v-if="getFullRecipientList(item)?.names.join(', ') !== item.target_remarks && getFullRecipientList(item)?.groupName && item.target_remarks" class="app-title !text-indigo-400 pt-1 whitespace-pre-wrap">說明：{{ item.target_remarks }}</div>
                                                        </div>
                                                    </div>

                                                    <div v-if="item.content?.trim()" class="text-[17px] font-semibold text-slate-800 leading-relaxed whitespace-pre-wrap">{{ item.content.trim() }}</div>

                                                    <div v-if="item.items?.length > 0 && !isContentLiteral(item)" class="mt-2">
                                                        <label class="text-[15px] font-bold text-slate-400 uppercase tracking-widest block mb-1">賜降：</label>
                                                        <div class="space-y-1">
                                                            <template v-for="(group, gName, gIdx) in groupItems(item.items)" :key="gName">
                                                                <div class="text-[17px] font-semibold text-slate-800 leading-relaxed">
                                                                    <template v-if="item.items.length > 1"><span class="text-slate-400 mr-1.5 font-outfit">{{ gIdx + 1 }}.</span></template>{{ stripMasterPrefix(gName) }}
                                                                    <template v-if="group.length === 1 && !group[0].name">
                                                                        <span v-if="group[0].details || group[0].remarks || group[0].sub_name" class="ml-1">: {{ group[0].details }} <span v-if="group[0].remarks || group[0].sub_name" class="text-[17px] font-normal text-slate-400">({{ group[0].remarks || group[0].sub_name }})</span></span>
                                                                    </template>
                                                                </div>
                                                                <div class="space-y-1" v-if="group.length > 1 || (group[0] && group[0].name)">
                                                                    <div v-if="group.length > 1 && getMainDetails(group)" class="pl-6 text-[17px] font-normal text-slate-800 leading-relaxed">{{ getMainDetails(group) }}</div>
                                                                    <div v-for="(m, midx) in group" :key="midx">
                                                                        <div v-if="m.name || (group.length > 1 && getCleanRemark(m.remarks || m.sub_name, m.details))" class="pl-6 text-[17px] font-normal text-slate-800 leading-relaxed">
                                                                            <template v-if="m.name">
                                                                                <template v-if="item.items.length > 1 || group.length > 1"><span class="text-slate-400 mr-1.5 font-outfit">{{ gIdx + 1 }}.{{ midx + 1 }}</span></template>
                                                                                <span v-if="isSpecialTreasure(m.name)">{{ stripMasterPrefix(m.name) }}</span>
                                                                                <span v-else>{{ stripMasterPrefix(m.name) }}{{ (m.details) ? ':' + m.details : '' }} <span v-if="getCleanRemark(m.remarks || m.sub_name, m.details)" class="text-[17px] font-normal text-slate-400">({{ getCleanRemark(m.remarks || m.sub_name, m.details) }})</span></span>
                                                                                <div v-if="m.details && isSpecialTreasure(m.name) && !getCleanRemark(m.remarks || m.sub_name, m.details)" class="pl-4 text-[17px] font-normal text-slate-500">{{ m.details }}</div>
                                                                            </template>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </template>
                                                        </div>
                                                    </div>

                                                    <div v-if="item.items_footer_remarks && !isContentLiteral(item)" class="text-[17px] font-normal text-slate-600 leading-relaxed pt-1 whitespace-pre-wrap">{{ item.items_footer_remarks }}</div>
                                                    <div class="pt-1 text-left" v-if="!hasFinishedSuffix(item) && !isContentLiteral(item)"><span class="text-[17px] font-normal text-slate-700">完畢</span></div>
                                                </div>

                                                <!-- Edit Mode -->
                                                <div v-if="inlineEditingId === item.id" class="pt-4 space-y-4 animate-fade-in text-left">
                                                    <div class="bg-slate-50 rounded-3xl border-2 border-dashed border-indigo-100 p-5 shadow-inner">
                                                        <div class="flex items-center justify-between mb-3 px-1">
                                                            <span class="text-[14px] font-black text-indigo-400 uppercase tracking-widest flex items-center">
                                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                                                Word 模式編輯中
                                                            </span>
                                                            <span class="text-[12px] text-slate-400 font-bold">系統將自動解析格式</span>
                                                        </div>
                                                        <textarea v-model="inlineWordText" 
                                                                  class="w-full min-h-[400px] bg-transparent border-none text-[17px] font-black text-slate-800 focus:ring-0 outline-none leading-relaxed resize-none overflow-hidden"
                                                                  @input="e => { e.target.style.height = 'auto'; e.target.style.height = e.target.scrollHeight + 'px' }"
                                                                  placeholder="輸入內容..."></textarea>
                                                    </div>
                                                    <div class="flex space-x-3 pt-4 pb-6">
                                                        <button @click.stop="cancelInlineEdit" class="flex-1 h-[56px] rounded-2xl bg-slate-100 text-slate-600 font-black text-[16px] active:scale-95 transition-all">取消</button>
                                                        <button @click.stop="saveInlineEdit" :disabled="saving" class="flex-1 h-[56px] rounded-2xl bg-[#FFB266] text-white font-black text-[19px] shadow-xl active:scale-95 transition-all">{{ saving ? '正在存檔...' : '確認修改' }}</button>
                                                    </div>
                                                </div>
                                            </div>

                                        </div><!-- /white panel -->
                                        </div><!-- /pointer-events-none wrapper -->
                                    </div><!-- /overlay root -->
                                </teleport>
                                 </template>
                             </div>
                         </template>

                     </div>
                </div>

                <div v-if="distributionModal.show" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-[#FFB266]/60 backdrop-blur-sm animate-fade-in">
                    <div class="bg-white rounded-[32px] w-full max-w-sm overflow-hidden shadow-2xl animate-slide-up border border-white/20">
                        <div class="p-8 text-center space-y-6">
                            <div class="space-y-2">
                                <div class="text-[22px] font-black text-black tracking-tight leading-tight">偵測到不同的仙師開示</div>
                                <p class="text-[15px] text-slate-500 font-bold">內容包含：{{ distributionModal.detectedNames.join('、') }}</p>
                            </div>

                            <div class="flex flex-col space-y-4 pt-2">
                                <!-- Primary Action: Distribute -->
                                <button @click="executeDistributionSave('distribute')" 
                                        class="w-full py-4 bg-amber-500 text-white rounded-2xl font-black text-[17px] active:scale-[0.98] transition-all shadow-lg" style="color: white !important;">
                                    {{ distributionModal.detectedNames.length === 1 ? '存入「' + distributionModal.detectedNames[0] + '」資料夾' : '依內容自動分流儲存' }}
                                </button>

                                <!-- Secondary Action: Keep in Current -->
                                <button @click="executeDistributionSave('keep')" 
                                        class="w-full py-4 bg-indigo-600 text-white rounded-2xl font-black text-[17px] active:scale-[0.98] transition-all shadow-lg" style="color: white !important;">
                                    維持存入「{{ currentFolder?.name || '目前' }}」資料夾
                                </button>

                                <button @click="distributionModal.show = false" 
                                        class="w-full py-3 text-slate-400 font-bold text-[15px] active:scale-95 transition-all">
                                    取消錄入
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Master Mismatch Warning Modal -->
                <div v-if="masterMismatchModal.show" class="fixed inset-0 z-[3100] flex items-center justify-center p-4" style="background:rgba(15,23,42,0.55);backdrop-filter:blur(4px)">
                    <div class="bg-white rounded-[28px] shadow-2xl w-full max-w-sm mx-auto p-6 animate-fade-in text-left">
                        <div class="flex items-center space-x-3 mb-4">
                            <div class="w-10 h-10 rounded-2xl bg-amber-100 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </div>
                            <div>
                                <h3 class="text-[17px] font-black text-slate-900">仙師不符提示</h3>
                                <p class="text-[12px] text-slate-400 font-bold">貼上資料偵測到不同仙師</p>
                            </div>
                        </div>
                        <div class="bg-slate-50 rounded-2xl p-4 mb-5 space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-[13px] text-slate-400 font-bold">目前資料夾</span>
                                <span class="text-[15px] font-black text-indigo-600">{{ masterMismatchModal.currentMasterName }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-[13px] text-slate-400 font-bold">貼上資料偵測到</span>
                                <span class="text-[15px] font-black text-orange-500">{{ masterMismatchModal.detectedMasterName }}</span>
                            </div>
                        </div>
                        <p class="text-[13px] text-slate-500 font-bold mb-4 text-center">請選擇要使用哪位仙師儲存這筆資料：</p>
                        <div class="flex flex-col space-y-3">
                            <button @click="chooseMasterAndProceed(false)" class="w-full py-4 bg-indigo-600 text-white rounded-2xl font-black text-[16px] active:scale-[0.98] transition-all shadow-md" style="color: white !important;">
                                使用目前資料夾：{{ masterMismatchModal.currentMasterName }}
                            </button>
                            <button @click="chooseMasterAndProceed(true)" class="w-full py-4 bg-amber-500 text-white rounded-2xl font-black text-[16px] active:scale-[0.98] transition-all shadow-md" style="color: white !important;">
                                使用貼上資料：{{ masterMismatchModal.detectedMasterName }}
                            </button>
                            <button @click="masterMismatchModal.show = false" class="w-full py-3 bg-slate-100 text-slate-500 rounded-2xl font-black text-[15px] active:scale-[0.98] transition-all">
                                取消
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Full Page Save Confirmation Overlay -->
                <div v-if="saveConfirmModal.show" class="fixed inset-0 z-[3000] bg-white animate-fade-in flex flex-col font-sans text-left">
                    <!-- High-Density Header -->
                    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50 shrink-0">
                        <div class="flex items-center space-x-3">
                        <div class="flex flex-col">
                            <div class="flex items-center gap-2">
                        <logo-imperial-notebook :height="36" class="md:hidden" />
                        <h1 class="text-red-600 leading-tight font-outfit tracking-widest break-words font-black" style="color: #dc2626 !important; font-size: 30px !important; padding-top: 5px; font-weight: 900 !important;">確認錄入資料</h1>
                    </div>
                            <span class="text-[24px] font-medium text-red-600 font-outfit whitespace-nowrap" style="color: #dc2626 !important; font-size: 24px !important; font-weight: 500 !important;">請核對以下內容是否正確</span>
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
                                <span v-if="item.date || form.date" class="text-[12px] font-black text-slate-300 uppercase tracking-[0.2em]">{{ (item.date || form.date || '').replace(/-/g, '/') }}</span>
                            </div>

                            <div class="flex flex-col border-l-4 border-indigo-500 pl-3">
                                <span class="text-[17px] font-normal text-slate-900 leading-tight">
                                    {{ formatMasterName(item.master_name) }}開示給:{{ getRecipientName(item) }}
                                </span>
                            </div>

                            <!-- Personnel Detail Box: Toggleable for high-density -->
                            <div v-if="getFullRecipientList(item)" class="space-y-2">
                                <button @click="toggleRecipientDetails(item.id || bIdx)" 
                                        class="flex items-center space-x-2 text-indigo-600 hover:text-indigo-800 transition-colors group">
                                    <div class="w-6 h-6 bg-indigo-50 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform">
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
                                    <div v-if="item.target_remarks" class="text-[12px] font-medium text-indigo-400 pt-1 whitespace-pre-wrap">
                                        說明：{{ item.target_remarks }}
                                    </div>
                                </div>
                            </div>

                            <div v-if="stripContentHeaders(item.content)?.trim()" class="text-[17px] text-slate-900 font-semibold leading-relaxed whitespace-pre-wrap px-1">
                                {{ stripContentHeaders(item.content).trim() }}
                            </div>

                            <div v-if="item.items?.length > 0" class="pt-3 space-y-2">
                                <div v-if="!item.content?.includes('賜降：') || stripContentHeaders(item.content) !== item.content" class="text-[17px] font-black text-slate-900 pl-1">賜降：</div>
                                <div v-for="(group, gName, gIdx) in groupItems(item.items)" :key="gIdx" class="text-[17px] text-slate-900 font-normal flex flex-col px-1">
                                    <div class="flex items-center">
                                        <span class="text-slate-400 mr-2 font-outfit">{{ gIdx + 1 }}.</span> {{ stripMasterPrefix(gName) }}{{ (group.length === 1 && !group[0].name) ? (getMergedContent(group[0].details, group[0].remarks || group[0].sub_name) ? ' : ' + getMergedContent(group[0].details, group[0].remarks || group[0].sub_name) : '') : (getMainDetails(group) ? ' : ' + getMainDetails(group) : '') }}
                                    </div>
                                    <div v-if="group.length > 1 || (group[0] && group[0].name)" class="pl-6 space-y-0.5 mt-1">
                                        <div v-for="(m, mIdx) in group" :key="m.uid">
                                            <div v-if="(m.name && !shouldHideContentName(gName, m.name)) || (group.length > 1 && getCleanRemark(m.remarks || m.sub_name, m.details))" class="text-[17px] text-slate-900 font-normal">
                                                <span class="text-slate-400 mr-1.5 font-outfit">{{ gIdx + 1 }}.{{ mIdx + 1 }}</span> {{ m.name ? m.name : (getMainDetails(group) ? '' : '項目') }}
                                                {{ (m.details && (m.name || !getMainDetails(group))) ? (m.name ? ' : ' : '') + m.details : '' }}
                                                <span v-if="getCleanRemark(m.remarks || m.sub_name, m.details)" class="opacity-60 ml-1">({{ getCleanRemark(m.remarks || m.sub_name, m.details) }})</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div v-if="item.items_footer_remarks" class="text-[17px] text-slate-900 font-normal leading-tight pt-2 border-t border-slate-50 whitespace-pre-wrap">
                                {{ item.items_footer_remarks }}
                            </div>
                        </div>
                    </div>

                    <div class="fixed bottom-[37px] left-0 right-0 md:fixed md:bottom-[37px] md:left-0 md:right-0 px-6 pt-[4px] pb-[1px] border-t border-slate-100 bg-white/95 backdrop-blur-md shadow-[0_-10px_30px_rgba(0,0,0,0.05)] shrink-0 flex items-center space-x-4 z-[1210]">
                        <button @click="saveConfirmModal.show = false" class="px-8 py-[12px] bg-slate-100 text-slate-500 rounded-2xl font-black text-[17px] active:scale-[0.98] transition-all whitespace-nowrap">
                            修改
                        </button>
                        <button @click="performActualSave" class="flex-1 py-[12px] bg-amber-500 text-white rounded-2xl font-black text-[19px] shadow-lg shadow-amber-200/40 active:scale-[0.98] transition-all flex items-center justify-center" style="color: white !important;">
                            {{ saving ? '錄入中...' : '確認存檔' }}
                        </button>
                    </div>
                </div>

            </template>

            <!-- Global Components (Visible on all levels) -->
            <add-action-menu :show="showAddMenu" @close="showAddMenu = false" :actions="addActions" />

            <!-- Floating Pagination above MobileNavbar -->
            <div v-if="currentFolder && !addMode && !itemsDetailMode && itemPagination && itemPagination.last_page > 1" 
                 class="fixed z-[100] flex justify-center bg-white border-t border-slate-200 py-0.5" 
                 style="bottom: calc(7dvh + env(safe-area-inset-bottom)); left: 0; right: 0;">
                <pagination-buttons 
                    :meta="itemPagination" 
                    @page-change="fetchItems" 
                    class="!mb-0 !mt-0"
                />
            </div>
            <mobile-navbar 
                :can-back="currentFolder !== null || currentCategory !== null || addMode"
                :show-action="(currentFolder !== null || currentCategory !== null || (currentFolder === null && currentCategory === null)) && !addMode"
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
                            <button v-if="persistentToast.type === 'deleteConfirm' || persistentToast.type === 'clearConfirm' || persistentToast.type === 'switchConfirm'" 
                                    @click="executeToastAction" 
                                    class="w-full py-4 bg-rose-500 text-white rounded-2xl font-black text-[18px] active:scale-95 transition-all shadow-lg shadow-rose-200/50" 
                                    style="color: white !important;">
                                {{ persistentToast.type === 'deleteConfirm' ? '確認刪除' : (persistentToast.type === 'switchConfirm' ? '確認切換' : '確認執行') }}
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
            <!-- Floating Login Info Removed per user request -->
        </div>
    </div>
</template>

<script setup>
import TeachingAddForm from './TeachingAddForm.vue';
import { ref, computed, onMounted, onUnmounted, defineEmits, watch, nextTick } from 'vue';
import { debounce } from '../utils/debounce';
import axios from 'axios';
import SearchComponent from './SearchComponent.vue';
import MobileNavbar from './MobileNavbar.vue';
import AddActionMenu from './AddActionMenu.vue';
import CompactDatePicker from './CompactDatePicker.vue';
import PaginationButtons from './PaginationButtons.vue';
import { writeClipboard, downloadBlob, lockBodyScroll, unlockBodyScroll } from '../utils/iosCompat';

const getTodayStr = () => {
    const d = new Date();
    return `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`;
};

// Reactive State
const props = defineProps(['user']);
const formatMasterName = (name) => {
    if (!name) return '仙師';
    let clean = name.replace('每日開示', '').replace('開示記錄', '').replace('專區', '').trim();
    if (['老祖', '元始', '道祖', '靈寶', '太宰', '閻王'].includes(clean)) return clean + '仙師';
    return clean;
};

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

const isContentLiteral = (item) => {
    if (!item.content || item.content.trim().length < 5) return false;
    const c = item.content.trim();
    // Detect if content contains full record structure or is multi-line
    const lines = c.split('\n');
    if (lines.length > 1) return true; // Most batch records are multi-line
    
    const masters = ['老祖', '元始', '道祖', '靈寶', '父皇', '太宰', '太子', '閻王'];
    const hasMaster = masters.some(m => c.includes(m));
    const hasMarkers = c.includes('開示') || c.includes('給') || c.includes('：') || c.includes(':');
    return hasMaster && hasMarkers;
};

const getFirstThreeLines = (text) => {
    if (!text) return '';
    const lines = text.trim().split('\n');
    if (lines.length <= 3) return text.trim();
    return lines.slice(0, 3).join('\n') + '...';
};

const emit = defineEmits(['goHome']);

const resetToRoot = () => {
    currentCategory.value = null;
    currentFolder.value = null;
    addMode.value = false;
    searchQuery.value = '';
    focusedId.value = null;
    reorderMode.value = false;
    activeDropdownId.value = null;
};
const activeEntryTab = ref('single');
const batchImportContent = ref('');
const isAddingFlash = ref(false);
const currentFolder = ref(null);
const currentCategory = ref(null);
const folderCounts = ref({});

// Removed auto-redirect to masters for general users per request to show category folder first.

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
    return name;
};

const palaceToGroupName = {
    '玄通宮': '第一組', '玄應宮': '第二組', '玄心宮': '第三組', '玄妙宮': '第四組',
    '玄昇宮': '第五組', '玄願宮': '第六組', '玄法宮': '第七組', '玄閻宮': '第八組',
    '玄窕宮': '第九組', '玄瑤宮': '第十組', '玄義宮': '第十一組'
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
    if (!masterName) masterName = '父皇';

    const recipient = getRecipientName(item);

    // Deduplication logic: 
    if (index > 0) {
        const prevItem = allRecords[index - 1];
        const prevMasterName = prevItem.master?.name || prevItem.master_name || '父皇';
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
const masterMismatchModal = ref({
    show: false,
    detectedMasterName: '',
    detectedMasterId: null,
    currentMasterName: '',
    currentMasterId: null
});
// null = default (use folder master if in specific folder, else auto-detect)
// 'auto' = user explicitly chose auto-detect from text
// number = user explicitly chose specific master ID
const pendingMasterId = ref(null);
const masters = ref([]);
const loading = ref(false);
const saving = ref(false);
const editingId = ref(null);
const focusedId = ref(null);
const focusedDate = ref(null);
const activeDropdownId = ref(null);
const allExpanded = ref(false);
const sortDesc = ref(true);
const persistentToast = ref(null);
const deleteConfirmId = ref(null);
const toastActionContext = ref(null);
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

const inlineWordText = ref('');

const startInlineEdit = (item) => {
    // Explicitly set focus to ensure expansion without toggling
    focusedId.value = item.id;
    inlineEditingId.value = item.id;

    // Word Mode: Prepare full text version
    inlineWordText.value = formatTeachingForFile(item).trim();

    inlineEditData.value = { 
        id: item.id,
        date: item.date || '',
        master_name: item.master?.name || item.master_name || '父皇',
        target_name: getRecipientName(item),
        content: item.content || '',
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
    if (!inlineWordText.value) return;
    saving.value = true;
    try {
        // 1. Parse the unified text back into structured data
        const parsed = parseTeachingWordText(inlineWordText.value);

        // 2. Send to backend
        const res = await axios.patch(`/teachings/${inlineEditData.value.id}`, {
            date: parsed.date || inlineEditData.value.date,
            master_name: parsed.master_name || inlineEditData.value.master_name,
            target_name: parsed.target_name || inlineEditData.value.target_name,
            content: parsed.content || '',
            items: parsed.items || [],
            items_footer_remarks: parsed.items_footer_remarks || ''
        });

        // Update local state
        const idx = teachings.value.findIndex(t => t.id === inlineEditData.value.id);
        if (idx !== -1) {
            teachings.value[idx] = { ...teachings.value[idx], ...res.data };
        }

        cancelInlineEdit();
    } catch (err) {
        console.error('Failed to save inline edit:', err);
        persistentToast.value = { msg: '✖ 存檔失敗，請稍後再試', type: 'error' };
    } finally {
        saving.value = false;
    }
};

const parseTeachingWordText = (text) => {
    const lines = text.split('\n').map(l => l.trim());
    const result = {
        date: '',
        master_name: '',
        target_name: '',
        content: '',
        items: [],
        items_footer_remarks: ''
    };

    let section = 'header';
    let contentLines = [];

    lines.forEach((line, index) => {
        if (!line && section !== 'content') return;

        if (index === 0) {
            // First line is usually date: 2026/05/01
            const dateMatch = line.match(/(\d{4}[\/\s.-]\d{1,2}[\/\s.-]\d{1,2})/);
            if (dateMatch) {
                result.date = dateMatch[1].replace(/\//g, '-');
            }
            return;
        }

        if (line.includes('開示給') || line.includes('給')) {
            const separator = line.includes('開示給') ? '開示給' : '給';
            const parts = line.split(separator);
            result.master_name = parts[0].trim();
            result.target_name = parts[1].replace(/[：:]/, '').trim();
            section = 'content';
            return;
        }

        if (line.includes('賜降：') || line.startsWith('賜降')) {
            section = 'treasures';
            return;
        }

        if (section === 'content') {
            const val = line.trim();
            const footerKeywords = ['完畢', '完畢！', '*允同享皇恩'];
            if (footerKeywords.some(k => val === k || val.startsWith(k))) {
                result.items_footer_remarks = (result.items_footer_remarks ? result.items_footer_remarks + '\n' : '') + val;
            } else {
                contentLines.push(line);
            }
        } else if (section === 'treasures') {
            // Parse treasure line: "1. 法寶: 9天份" or "   項目 (備註)"
            const match = line.match(/^(\d+\.|[+*])\s*(.*?)([:：]\s*(.*))?$/);
            if (match) {
                const tName = match[2].trim();
                const tDetails = match[4] ? match[4].trim() : '';
                result.items.push({
                    uid: Date.now() + Math.random(),
                    treasure_name: tName,
                    details: tDetails,
                    name: '',
                    sub_name: ''
                });
            } else if (line.startsWith('   ') || line.startsWith('\t')) {
                // Sub-item logic
                const subMatch = line.trim().match(/^(.*?)\s*(\((.*?)\))?$/);
                if (subMatch && result.items.length > 0) {
                    const lastItem = result.items[result.items.length - 1];
                    const namePart = subMatch[1].trim();
                    const remarkPart = subMatch[3] ? subMatch[3].trim() : '';

                    if (!lastItem.name) {
                        lastItem.name = namePart;
                        lastItem.sub_name = remarkPart;
                    } else {
                        result.items.push({
                            uid: Date.now() + Math.random(),
                            treasure_name: lastItem.treasure_name,
                            name: namePart,
                            sub_name: remarkPart,
                            details: ''
                        });
                    }
                }
            } else {
                // Non-indented line after treasures section started - Treat as footer remark
                const val = line.trim();
                if (val && !val.includes('賜降')) {
                    result.items_footer_remarks = (result.items_footer_remarks ? result.items_footer_remarks + '\n' : '') + val;
                }
            }
        }
    });

    result.content = contentLines.join('\n').trim();
    return result;
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
const activeTargetRemarksDropdown = ref(null);
const activeBatchTargetRemarksIdx = ref(null);
const relationshipOptions = ['長輩', '母親', '父親', '公公', '婆婆', '爺爺', '奶奶', '外公', '外婆'];
const treasureInput = ref(null);
const activeSubPractitionerDropdownId = ref(null);

const showPreviewRecipients = ref(new Set());
const togglePreviewRecipients = (idx) => {
    if (showPreviewRecipients.value.has(idx)) showPreviewRecipients.value.delete(idx);
    else showPreviewRecipients.value.add(idx);
};

const toggleSort = () => { sortDesc.value = !sortDesc.value; };
const groupedRecords = computed(() => {
    let list = [...visibleItems.value];
    // Server-side filtering is now used, so we don't need client-side filter here.
    // However, we keep the sorting logic.

    // Sort first
    list.sort((a, b) => {
        const dA = a.date ? new Date(a.date).getTime() : NaN;
        const dB = b.date ? new Date(b.date).getTime() : NaN;

        // Push "Historical Accumulation" (no date) to the end regardless of sort direction
        if (isNaN(dA) && isNaN(dB)) return sortDesc.value ? b.id - a.id : a.id - b.id;
        if (isNaN(dA)) return 1;
        if (isNaN(dB)) return -1;

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
    let list = groupedRecords.value;

    // Solo Mode: Focus on a specific record (hide other dates and other records)
    if (focusedId.value) {
        const item = list.find(i => i.id === focusedId.value);
        if (item) {
            const d = item.date || '歷史累積';
            return [{ date: d, items: [item] }];
        }
    }

    // Isolation Mode: Focus on a specific date (hide other dates)
    if (focusedDate.value) {
        const items = list.filter(item => (item.date || '歷史累積') === focusedDate.value);
        if (items.length > 0) {
            return [{ date: focusedDate.value, items }];
        }
    }

    // Default: Group and return all records
    const map = {};
    list.forEach(item => {
        const d = item.date || '歷史累積';
        if (!map[d]) map[d] = [];
        map[d].push(item);
    });
    return Object.keys(map).sort((a,b) => {
        // Always put "歷史累積" (Historical) at the very bottom
        if (a === '歷史累積') return 1;
        if (b === '歷史累積') return -1;

        const dA = new Date(a).getTime();
        const dB = new Date(b).getTime();

        if (isNaN(dA)) return 1;
        if (isNaN(dB)) return -1;

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

const hasSubDetailData = computed(() => {
    return newItemSubName.value.trim() !== '' || 
           (newItemDetailsExtraDays.value !== '' && newItemDetailsExtraDays.value !== 0) ||
           (newItemSubSun.value !== '' && newItemSubSun.value !== 0) ||
           (newItemSubMoon.value !== '' && newItemSubMoon.value !== 0) ||
           (newItemSubLight.value !== '' && newItemSubLight.value !== 0) ||
           (newItemSubTimes.value !== '' && newItemSubTimes.value !== 0) ||
           (newItemSubHours.value !== '' && newItemSubHours.value !== 0) ||
           newItemSubSize.value.trim() !== '' ||
           newItemSubSheets.value.trim() !== '' ||
           newItemSubPractitioner.value.trim() !== '' ||
           newItemSubBodyPart.value.trim() !== '' ||
           newItemSubInstrumentName.value.trim() !== '' ||
           stagedContents.value.length > 0;
});
const activeTreasureDropdownId = ref(null);
const activeDaysDropdownId = ref(null);
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
    // br.items_footer_remarks might be undefined/empty if just parsed; 
    // we should only overwrite the form's remarks if the record HAS specific remarks.
    if (br.items_footer_remarks) {
        form.value.items_footer_remarks = br.items_footer_remarks;
    }
    form.value.master_name = br.master_name;

    itemsDetailMode.value = true;
};

const addBatchBlock = (startEditing = false) => {
    // Strict Inheritance: Always use the first master in the set
    const firstMaster = batchRecords.value.length > 0 ? (batchRecords.value[0].master_name || masterNameInput.value) : (masterNameInput.value || '父皇');

    const newIndex = batchRecords.value.length;
    batchRecords.value.push({ 
        dharma_name_ids: [], content: '', dharmaSearchQuery: '', 
        target_remarks: '', relatives: '', items: [], 
        items_footer_remarks: form.value.items_footer_remarks || '',
        master_name: firstMaster 
    });

    if (startEditing) {
        openBatchItemDetails(newIndex);
    }
};

const handleItemsDetailClose = (mode = false) => {
    if (newFooterRemark.value.trim()) addFooterRemark();
    if (activeBatchIndex.value !== null) {
        // Sync everything from form to the specific batch block
        const br = batchRecords.value[activeBatchIndex.value];
        br.items = JSON.parse(JSON.stringify(form.value.items));
        br.content = form.value.content;
        br.dharma_name_ids = [...form.value.dharma_name_ids];
        br.target_remarks = form.value.target_remarks;
        br.items_footer_remarks = form.value.items_footer_remarks;

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
    const firstMaster = batchRecords.value.length > 0 ? batchRecords.value[0].master_name : (masterNameInput.value || '父皇');

    const newRecord = {
        dharma_name_ids: [...form.value.dharma_name_ids],
        content: form.value.content,
        items: [...form.value.items],
        dharmaSearchQuery: dharmaSearchQuery.value || '',
        master_name: firstMaster, 
        target_remarks: form.value.target_remarks || '',
        items_footer_remarks: form.value.items_footer_remarks || ''
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
        '金印', '令牌', '令旗', '天筆', '法筆', '玉筆', '八卦', '寶扇', '油燈', '清煞'
    ];

    const combined = [...new Set([...names, ...extraSuggestions])];
    return combined.filter(n => n && n !== '清煞法寶' && n !== '法器').sort((a, b) => a.length - b.length || a.localeCompare(b, 'zh-TW', { collation: 'stroke' }));
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
        const getSortIndex = (dn) => {
            const name = dn.name;
            const searchName = name;
            // Use the original dharmaNames order (which follows database)
            return dharmaNames.value.findIndex(d => d.id === dn.id || d.name === searchName);
        };

        const indexA = getSortIndex(a);
        const indexB = getSortIndex(b);

        if (indexA !== -1 && indexB !== -1) return indexA - indexB;
        if (indexA !== -1) return -1;
        if (indexB !== -1) return 1;
        return (a.name || '').localeCompare(b.name || '', 'zh-Hant');
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
    { dharma_name_ids: [], content: '', dharmaSearchQuery: '', target_remarks: '', relatives: '', items: [], items_footer_remarks: '', master_name: '' }
]);

const rawLines = computed(() => {
    return batchImportContent.value.split('\n').filter(l => l.trim() !== '');
});

const removeBatchBlock = (index) => {
    if (batchRecords.value.length > 1) batchRecords.value.splice(index, 1);
};

const handleBlockDharmaInput = (index, val) => {
    const block = batchRecords.value[index];
    if (!val) { block.dharma_name_ids = []; return; }

    // Rule: "Name之Relative" split
    const relSplitMatch = val.match(/^(.*?)[之協助的](.+)$/);
    if (relSplitMatch) {
        const namePart = relSplitMatch[1].trim();
        let relPart = relSplitMatch[2].trim();

        // Terminology Translation
        if (relPart === '母') relPart = '母親';
        if (relPart === '父') relPart = '父親';

        block.dharmaSearchQuery = namePart;
        block.target_remarks = relPart;

        // Find dharma name ID for the name part
        const matchedDN = dharmaNames.value.find(dn => dn.name === namePart);
        if (matchedDN) { block.dharma_name_ids = [matchedDN.id]; }
        return;
    }

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
    remarks: '', items_footer_remarks: '', user_id: props.user?.id || 1, dharma_name_ids: []
});

// Multi-entry footer remarks
const footerRemarksList = ref([]);
const newFooterRemark = ref('');

const syncFooterRemarks = () => {
    form.value.items_footer_remarks = footerRemarksList.value.join('\n');
};

const addFooterRemark = () => {
    const val = newFooterRemark.value.trim();
    // Allow adding even if empty (to support "加行" / adding a blank line)
    footerRemarksList.value.push(val);
    newFooterRemark.value = '';
    syncFooterRemarks();
};

// When loading an existing record for edit, parse existing remarks back to list
watch(() => form.value.items_footer_remarks, (val) => {
    if (typeof val === 'string') {
        const lines = val.split('\n').map(l => l.trim());
        // Only sync if the list doesn't already match (avoid infinite loop)
        if (lines.join('\n') !== footerRemarksList.value.join('\n')) {
            footerRemarksList.value = lines;
        }
    } else if (val === null || val === undefined) {
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
        // Absolute priority for global groups
        if (a.name === '在場全體' || a.name === '全體殿生') {
            if (b.name === '在場全體' || b.name === '全體殿生') return a.name.localeCompare(b.name, 'zh-TW', { collation: 'stroke' });
            return -1;
        }
        if (b.name === '在場全體' || b.name === '全體殿生') return 1;

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



const mastersTotalCount = computed(() => {
    return Object.entries(folderCounts.value)
        .filter(([key]) => key !== 'daily' && key !== '0')
        .reduce((acc, [_, val]) => acc + Number(val), 0);
});

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
    const isDaily = currentFolder.value?.id === 0 || currentFolder.value?.id === '0' || currentCategory.value === 'daily';
    const actions = [];

    if (!isDaily && currentFolder.value === null) {
        actions.push({ 
            label: '多筆新增', 
            description: '快速進入多筆資料智慧錄入介面',
            icon: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>',
            colorClass: 'bg-orange-50 text-orange-600',
            handler: () => { 
                currentFolder.value = folders_list.find(f => f.id === 0); 
                currentCategory.value = 'daily';
                showAdd(); 
                activeEntryTab.value = 'batch'; 
                showAddMenu.value = false; 
            } 
        });
    }

    if (isDaily) {
        actions.push({ 
            label: '逐筆新增', 
            description: '錄入單筆開示及賜降紀錄',
            icon: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>',
            colorClass: 'bg-indigo-50 text-indigo-600',
            handler: () => { showAdd(); activeEntryTab.value = 'single'; showAddMenu.value = false; } 
        });
    }

    if (!isDaily && currentFolder.value !== null) {
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
    let lines = text.trim().split('\n');
    let startIndex = 0;

    for (let i = 0; i < lines.length; i++) {
        const line = lines[i].trim();
        if (!line) {
            startIndex = i + 1;
            continue;
        }

        // Date check: 2026/05/07 or 2026-05-07
        if (/^\d{2,4}[\/\s.-]\d{1,2}[\/\s.-]\d{1,2}\s*$/.test(line)) {
            startIndex = i + 1;
            continue;
        }

        // Header check: Master + 開示給 + Recipient
        if (line.includes('開示給')) {
            startIndex = i + 1;
            continue;
        }

        // Standard "賜降" header check
        if (line === '賜降：' || line === '賜降') {
            startIndex = i + 1;
            continue;
        }

        break;
    }

    return lines.slice(startIndex).join('\n').trim();
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
    } else if (group.name === '在場全體') {
        // Standalone label - no individual members
        members = [];
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
    const matchedGroup = groups.value.find(g => g.name === val || g.name === palaceToGroupName[val]);
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
        const g = (groups.value || []).find(g => g.name === pName || g.name === palaceToGroupName[pName]);
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
    const hardcoded = { '老祖仙師': 1, '元始仙師': 2, '道祖仙師': 3, '靈寶仙師': 4, '父皇仙師': 5, '父皇': 5, '太宰仙師': 6, '太子': 7, '閻王仙師': 8 };
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
    if (group.name === '在場全體') {
        const isActive = dharmaSearchQuery.value === '在場全體';
        if (isActive) {
            dharmaSearchQuery.value = '';
            form.value.dharma_name_ids = [];
        } else {
            dharmaSearchQuery.value = '在場全體';
            form.value.dharma_name_ids = [];
            activeModalGroup.value = group;
        }
        return;
    }

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
    if (group.name === '在場全體') return dharmaSearchQuery.value === '在場全體';
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
            page: page,
            search: searchQuery.value,
            sortDesc: sortDesc.value ? 1 : 0
        };
        const isDaily = currentFolder.value?.id == 0 || currentFolder.value?.id === '0';
        if (isDaily) {
            params.is_daily = 1;
            params.per_page = 10;
        } else {
            params.per_page = 10;
        }

        const res = await axios.get('/teachings', { params });
        const parseItem = (i) => ({
            ...i,
            items: typeof i.items === 'string' ? JSON.parse(i.items) : (i.items || []),
            dharma_name_ids: typeof i.dharma_name_ids === 'string' ? JSON.parse(i.dharma_name_ids) : (i.dharma_name_ids || [])
        });

        const recordsObj = res.data.records || res.data;
        const itemsArray = recordsObj.data || recordsObj;

        if (res.data.folderCounts && page === 1) {
            folderCounts.value = res.data.folderCounts;
        }

        visibleItems.value = itemsArray.map(parseItem);
        itemPagination.value = { 
            current_page: recordsObj.current_page || 1, 
            last_page: recordsObj.last_page || 1,
            total: recordsObj.total || itemsArray.length
        };

        // Scroll to top when page changes
        const container = document.querySelector('.overflow-y-auto.flex-1');
        if (container) container.scrollTo({ top: 0, behavior: 'smooth' });

    } catch (e) { console.error(e); } finally { loading.value = false; }
};

watch(searchQuery, debounce(() => {
    if (currentFolder.value || currentCategory.value) {
        fetchItems(1);
    }
}, 300));

const syncRecords = async () => {
    try {
        const [dnRes, groupRes, masterRes, treasureRes, teachRes] = await Promise.allSettled([
            axios.get('/api/dharma-names-list'), 
            axios.get('/api/groups-list'),
            axios.get('/api/masters-list'), 
            axios.get('/api/treasures-list', { params: { type: ['teaching', 'content'] } }),
            axios.get('/teachings', { params: { per_page: 2000 } })
        ]);
        if (dnRes.status === 'fulfilled') dharmaNames.value = dnRes.value.data;
        if (groupRes.status === 'fulfilled') groups.value = groupRes.value.data || [];
        if (masterRes.status === 'fulfilled') masters.value = masterRes.value.data || [];
        if (treasureRes.status === 'fulfilled') treasures.value = (treasureRes.value.data || []).filter(t => t.name !== '清煞法寶');
        if (teachRes.status === 'fulfilled') {
            const data = teachRes.value.data;
            const recordsObj = data.records || data;
            teachings.value = Array.isArray(recordsObj) ? recordsObj : (recordsObj.data || []);
            if (data.folderCounts) {
                folderCounts.value = data.folderCounts;
            }
        }
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

const deleteItem = (id) => { 
    deleteConfirmId.value = id;
    persistentToast.value = { msg: '確定要刪除這筆教導紀錄嗎？', type: 'deleteConfirm' };
    activeDropdownId.value = null;
};

const executeToastAction = async () => {
    if (!persistentToast.value) return;
    const type = persistentToast.value.type;

    if (type === 'deleteConfirm') {
        try { 
            await axios.delete(`/teachings/${deleteConfirmId.value}`); 
            persistentToast.value = { msg: '✓ 已成功刪除紀錄', type: 'success' };
            setTimeout(() => { if (persistentToast.value?.type === 'success') persistentToast.value = null; }, 1500);
            focusedId.value = null;
            focusedDate.value = null;
            fetchItems(1); 
        } catch (e) { 
            persistentToast.value = { msg: '✖ 刪除失敗', type: 'error' };
        }
    } else if (type === 'clearConfirm') {
        await executeClearTodayDaily();
    } else if (type === 'switchConfirm') {
        const date = toastActionContext.value;
        focusedDate.value = date;
        focusedId.value = null;
        const newSet = new Set(collapsedDates.value);
        newSet.delete(date);
        collapsedDates.value = newSet;
        persistentToast.value = null;
    }
    deleteConfirmId.value = null;
    toastActionContext.value = null;
};

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
            const combinedRemarks = [sc.remarks, mainItem.main_remarks].filter(r => r).join('\n');
            form.value.items.push({
                uid: Date.now() + Math.random(),
                treasure_name: mainItem.treasure_name,
                details: sc.details,
                name: sc.name,
                sub_name: combinedRemarks
            });
        });
    } else {
        const combinedRemarks = [newItemRemarks.value, mainItem.main_remarks].filter(r => r).join('\n');
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
    newItemRemarks.value = '';
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

    let itemsArr = items;
    if (typeof items === 'string') {
        try {
            itemsArr = JSON.parse(items);
        } catch (e) {
            console.error('Failed to parse items JSON:', e);
            return groups;
        }
    }

    if (!Array.isArray(itemsArr)) return groups;

    itemsArr.forEach((item, index) => { 
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
    if (!id || id == 0) return '父皇';
    const m = masters.value.find(x => x.id == id);
    if (m) {
        return m.name === '父皇仙師' ? '父皇' : m.name;
    }
    return '未指定';
};

const getItemCount = (id) => markings.value?.[id] || 0; // Simplified
const markings = ref({});

const getRecipientName = (item) => {
    const pastedName = (item.dharmaSearchQuery || item.target_remarks || '').trim();
    const listInfo = getFullRecipientList(item, pastedName);

    if (!listInfo) return (pastedName || '對象');

    if (listInfo.groupName && pastedName && (pastedName.includes(listInfo.groupName) || listInfo.groupName.includes(pastedName))) {
        // Deduplicate if pastedName is just repeated groupName (e.g., "在場全體 在場全體")
        if (pastedName.trim() === `${listInfo.groupName} ${listInfo.groupName}`) return listInfo.groupName;
        return pastedName;
    }

    // Logic to prevent redundant parentheses (e.g., 鳳媓 (鳳媓))
    let remarks = '';
    const rawRemarks = (item.target_remarks || '').trim();
    const hasRemarks = rawRemarks !== '';
    if (hasRemarks) {
        const isRedundant = (listInfo.groupName?.trim() === rawRemarks) || 
                          (listInfo.names.length === 1 && listInfo.names[0]?.trim() === rawRemarks) ||
                          (listInfo.names.length > 0 && listInfo.names.join(', ').trim() === rawRemarks);
        if (!isRedundant) {
            remarks = ` (${rawRemarks})`;
        }
    }

    let res = "";
    if (listInfo.groupName) {
        if (pastedName && listInfo.names.includes(pastedName)) {
            res = `${pastedName}${remarks}`;
        } else {
            res = `${listInfo.groupName}${remarks}`;
        }
    } else {
        res = listInfo.names.join(', ') + remarks;
    }

    // Final deduplication for cases like "在場全體 在場全體"
    const parts = res.trim().split(/\s+/);
    if (parts.length === 2 && parts[0] === parts[1]) return parts[0];

    return res;
};

const getFullRecipientList = (item, hintName = null) => {
    const names = (item.dharma_names || []).map(dn => dn.name);
    const pendingIds = item.dharma_name_ids || [];

    let resolvedNames = [...names];
    if (resolvedNames.length === 0 && pendingIds.length > 0) {
        resolvedNames = pendingIds.map(id => dharmaNames.value.find(dn => dn.id === id)?.name).filter(n => n);
    }

    // If still empty, use target_remarks or dharmaSearchQuery to allow "View Details" button for custom recipients
    if (resolvedNames.length === 0 && (item.target_remarks || item.dharmaSearchQuery)) {
        const fallback = (item.dharmaSearchQuery || item.target_remarks || '').trim();
        if (fallback) resolvedNames = [fallback];
    }

    if (resolvedNames.length === 0) return null;

    // Group detection logic with robust ID normalization
    const normalizedItemIds = (item.dharma_names || []).map(d => Number(d.id)).sort((a, b) => a - b).join(',');
    const normalizedPendingIds = (pendingIds || []).map(id => Number(id)).sort((a, b) => a - b).join(',');
    const searchIds = normalizedItemIds || normalizedPendingIds;

    let groupName = null;

    if (searchIds && (groups.value || []).length > 0) {
        const matched = (groups.value || []).find(g => {
            const gDn = g.dharma_names || g.dharmaNames || [];
            const gIds = gDn.map(dn => Number(dn.id)).sort((a, b) => a - b).join(',');
            return gIds === searchIds;
        });
        
        if (matched) {
            const members = matched.dharma_names || matched.dharmaNames || [];
            if (members.length > 1) {
                groupName = matched.name;
            } else if (members.length === 1 && hintName) {
                const h = hintName.trim();
                if (h === matched.name || h === palaceToGroupName[matched.name] || matched.name === palaceToGroupName[h]) {
                    groupName = matched.name;
                }
            }
        }
        else if ((pendingIds.length > 0 && pendingIds.length === dharmaNames.value.length) || (resolvedNames.length > 0 && resolvedNames.length === dharmaNames.value.length)) groupName = '全體殿生';
    }

    if (groupName === '各宮') {
        groupName = '各宮 (玄通宮、玄應宮、玄心宮、玄妙宮、玄昇宮、玄願宮、玄法宮、玄閻宮、玄窕宮、玄瑤宮、玄義宮)';
    }

    // Sort names by official database order
    const nameToObj = new Map();
    dharmaNames.value.forEach(dn => nameToObj.set(dn.name, dn));

    // Deduplicate names
    resolvedNames = [...new Set(resolvedNames)];

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
    if (!masterName || masterName === '父皇仙師' || masterName === '父皇') {
        masterName = '父皇';
    }
    const recipient = getRecipientName(item);

    // User requested to always include master name even if deduplicated
    return `${masterName}開示給：${recipient}`;
};

const isSameSessionAsNext = (item, index, allRecords) => {
    if (!allRecords || index >= allRecords.length - 1) return false;
    const next = allRecords[index + 1];

    const masterName = (item.master?.name === '父皇仙師' || item.master?.name === '父皇' ? '父皇' : (item.master?.name || item.master_name || '父皇'));
    const nextMaster = (next.master?.name === '父皇仙師' || next.master?.name === '父皇' ? '父皇' : (next.master?.name || next.master_name || '父皇'));

    return masterName === nextMaster && item.date === next.date;
};

const isSessionFocused = (item) => {
    if (!focusedId.value) return false;
    // Check main ID first
    if (focusedId.value === item.id) return true;

    // Check if they are from the same session
    const focusedItem = visibleItems.value.find(i => i.id === focusedId.value);
    if (!focusedItem) return false;

    const m1 = (item.master?.name === '父皇仙師' || item.master?.name === '父皇' ? '父皇' : (item.master?.name || item.master_name || '父皇'));
    const m2 = (focusedItem.master?.name === '父皇仙師' || focusedItem.master?.name === '父皇' ? '父皇' : (focusedItem.master?.name || focusedItem.master_name || '父皇'));

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
    if (!focusedId.value) return false;
    const matchedItem = visibleItems.value.find(i => i.id == focusedId.value);
    // If focusedId is set but not found in current visible items, 
    // don't filter out all dates (which causes a blank screen)
    if (!matchedItem) return true; 
    return matchedItem.date === date;
};

const handleReorder = async (item, newOrderStr) => {
    const newOrder = parseInt(newOrderStr);
    const dateGroup = recordsByDate.value.find(g => g.date === item.date);
    if (!dateGroup || isNaN(newOrder)) return;

    // Create a copy of the current group's items
    const list = [...dateGroup.items];
    const oldIndex = list.findIndex(i => i.id === item.id);
    if (oldIndex === -1) return;

    // Bounds check
    const targetIndex = Math.max(0, Math.min(newOrder - 1, list.length - 1));

    // Move the item in memory
    const [movedItem] = list.splice(oldIndex, 1);
    list.splice(targetIndex, 0, movedItem);

    // Calculate new sort_orders for everyone in this group
    // In our system, usually sortDesc=true means higher sort_order is on top.
    // However, the display index (1, 2, 3...) is relative to the current view.
    // We update everyone in this date group to have sequential sort_orders.
    const orders = [];
    list.forEach((entry, idx) => {
        // If sortDesc is true, the first item (idx=0) should have the highest order
        const calculatedOrder = sortDesc.value ? (list.length - idx) : (idx + 1);
        orders.push({
            id: entry.id,
            sort_order: calculatedOrder
        });
    });

    try {
        await axios.post('/teachings/reorder', { orders });
        fetchItems(itemPagination.value.current_page);
    } catch (e) {
        const msg = e.response?.data?.message || e.message || '連線錯誤';
        persistentToast.value = { msg: '✖ 排序更新失敗: ' + msg, type: 'error' };
    }
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
        persistentToast.value = { msg: '✓ 內容已複製到剪貼簿', type: 'success' };
        setTimeout(() => { if (persistentToast.value?.type === 'success') persistentToast.value = null; }, 1500);
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

    let footer = item.items_footer_remarks ? '\n\n' + item.items_footer_remarks : '';
    if (!hasFinishedSuffix(item)) {
        footer += (footer ? '\n\n' : '\n\n') + '完畢';
    }

    let headerLabel = (item.content && item.content !== 'null') ? '開示給' : '給';
    return `${(item.date || '').replace(/-/g, '/')}\n${item.master?.name || (item.master_name || '仙師')}${headerLabel}${recipient}：${safeContent}${treasureText}${footer}\n`;
};

const formatTeachingForExport = (item, index = null, allRecords = []) => {
    // REQUIREMENT: Literal records return full content exactly as displayed (no synthetic headers)
    if (isContentLiteral(item)) {
        return item.content.trim();
    }

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
    const safeContent = (item.content && item.content !== 'null') ? '\n' + item.content.trim() : '';

    let footer = item.items_footer_remarks ? '\n\n' + item.items_footer_remarks : '';
    if (index !== null && allRecords.length > 0) {
        if (isSameSessionAsNext(item, index, allRecords)) {
            // Only hide if the footer is generic "完畢" or empty, or if it's identical to the next one
            const nextItem = allRecords[index + 1];
            if (!footer.trim() || footer.trim() === '完畢' || footer.trim() === '完畢！' || (nextItem && nextItem.items_footer_remarks === item.items_footer_remarks)) {
                footer = '';
            }
        }
    }

    if (footer && !hasFinishedSuffix(item) && footer.length > 0) {
        // If we have other footer remarks but no "finished" suffix, add it
        if (!footer.includes('完畢')) footer += '\n\n完畢';
    } else if (!footer && !hasFinishedSuffix(item)) {
        // If no footer at all and no suffix in content, add it
        footer = '\n\n完畢';
    }

    let headerLabel = (item.content && item.content !== 'null') ? '開示給' : '給';
    // Match list view header style: Master + Label + Recipient
    const mName = formatMasterName(item.master?.name || item.master_name);
    return `${(item.date || '').replace(/-/g, '/')}\n${mName}${headerLabel}${recipient}：${safeContent}${treasureText}${footer}\n`;
};

const downloadTeaching = (item) => {
    const text = formatTeachingForExport(item);
    const blob = new Blob([text], { type: 'text/plain' });
    downloadBlob(blob, `開示記錄_${item.date}.txt`);
};

const copyToLine = (item, index = null, allRecords = []) => {
    const text = formatTeachingForExport(item, index, allRecords);

    writeClipboard(text).then((success) => {
        if (success) {
            persistentToast.value = { msg: '✓ 內容已複製', type: 'success' };
            setTimeout(() => { if (persistentToast.value?.type === 'success') persistentToast.value = null; }, 1500);
        }
    });
};

const processBatchText = () => {
    if (!batchImportContent.value.trim()) return;

    const rawBlocks = batchImportContent.value.match(/[\s\S]*?完畢[！!]?/g) || [batchImportContent.value];
    const newRecords = [];

    const masterNames = ['老祖', '元始', '道祖', '靈寶', '父皇', '太宰', '太子', '閻王'];
    const masterOptions = ['老祖仙師', '元始仙師', '道祖仙師', '靈寶仙師', '父皇', '太宰仙師', '太子', '閻王仙師'];

    let currentActiveDate = null;

    rawBlocks.forEach(block => {
        let text = block.trim();
        if (!text || text.length < 5) return;

        const record = { 
            dharma_name_ids: [...(form.value.dharma_name_ids || [])],
            dharmaSearchQuery: dharmaSearchQuery.value || '',
            target_remarks: form.value.target_remarks || '',
            content: text, // Set content to FULL text
            items: [], 
            master_name: '', 
            date: null,
            items_footer_remarks: ''
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
                    currentActiveDate = `${yyyy}-${mm}-${dd}`;
                }
            } catch(e) {}
        }

        // Apply the sticky date (either found in this block or remembered from previous blocks)
        record.date = currentActiveDate;

        // 1. Master Detection
        let foundMaster = false;
        let matchedKeywordInText = '';
        masterNames.forEach((m, idx) => {
            const keyword = (m === '太子' || m === '父皇') ? m : m + '仙師';
            if (text.includes(keyword)) {
                record.master_name = masterOptions[idx];
                matchedKeywordInText = keyword;
                foundMaster = true;
            }
        });
        // We do NOT fallback to currentFolder name here to avoid locking records to the current view during shunting
        // 2. Recipient Detection (Highly Robust Regex)
        const recMatch = text.match(/開示給\s*([^：:\n\r]*)/);
        let contentToParse = text;

        if (recMatch && recMatch[1].trim()) {
            const nameField = recMatch[1].trim();
            record.dharmaSearchQuery = nameField;
            record.target_remarks = nameField;

            const foundDN = dharmaNames.value.find(dn => dn.name === nameField);
            if (foundDN) record.dharma_name_ids = [foundDN.id];
            else if (nameField === '全體殿生') {
                record.dharma_name_ids = (dharmaNames.value || []).map(dn => dn.id);
            } else {
                const foundGroup = (groups.value || []).find(g => g.name === nameField);
                if (foundGroup) {
                    record.dharma_name_ids = (foundGroup.dharma_names || []).map(dn => dn.id);
                }
            }
        }

        // 3. Line-by-line Parsing (Heuristic for content vs items)
        const lines = contentToParse.split('\n');
        let contentLines = [];
        let parsingTreasures = false;
        let remarks = [];

        lines.forEach(line => {
            const l = line.trim();
            if (!l) return;

            // Detect special footer remarks
            if (l.includes('允同享皇恩') || l.includes('完畢')) {
                remarks.push(l);
                return;
            }

            if (l.includes('賜降：')) {
                parsingTreasures = true;
                return;
            }

            // Only detect items when explicitly under "賜降：" section
            const isItem = parsingTreasures && l.match(/^(\d+\.|[+])/);

            if (isItem) {
                const tMatch = l.match(/^(\d+\.|[+])?\s*(.*?)([:：]\s*(.*))?$/);
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

        // Sync detected remarks to items_footer_remarks
        if (remarks.length > 0) {
            record.items_footer_remarks = remarks.join('\n');
        }

        // Requirement: Save the FULL original text in the content field for literal records
        record.content = text;

        newRecords.push(record);
    });

    if (newRecords.length > 0) {
        batchRecords.value = newRecords;
    }
};

const handleBatchPaste = (e) => {
    e.preventDefault();
    const text = e.clipboardData.getData('text');
    if (!text) return;
    batchImportContent.value = text;
    nextTick(() => processBatchText());
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
        if (focusedDate.value !== null) {
            // Trying to switch focus -> show prompt
            const oldDate = focusedDate.value.replace(/-/g, '/');
            const newDate = date.replace(/-/g, '/');
            toastActionContext.value = date;
            persistentToast.value = { 
                msg: `目前正在切閱「${oldDate}」的紀錄，是否關閉並改為查看「${newDate}」的開示內容？`, 
                type: 'switchConfirm' 
            };
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

watch(sortDesc, () => {
    fetchItems(1);
});

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
        const res = await axios.get('/teachings', { params });
        const recordsObj = res.data.records || res.data;
        const allItems = recordsObj.data || recordsObj;
        const text = allItems.map((item, idx) => {
            return formatTeachingForExport(item, idx, allItems);
        }).join('\n\n\n');

        await navigator.clipboard.writeText(text);
        persistentToast.value = { msg: '✓ 已複製完整清單至剪貼簿', type: 'success' };
        setTimeout(() => { if (persistentToast.value?.type === 'success') persistentToast.value = null; }, 1500);
    } catch (e) { 
        persistentToast.value = { msg: '✖ 複製失敗', type: 'error' };
    } finally { loading.value = false; }
};

const clearTodayDaily = () => {
    persistentToast.value = { 
        msg: `確定清空今日 (${form.value.date}) 的每日開示暫存區？\n此動作不會影響已分流至各仙師專區的原始紀錄。`, 
        type: 'clearConfirm' 
    };
};

const executeClearTodayDaily = async () => {
    loading.value = true;
    try {
        const res = await axios.get('/teachings', { params: { master_id: 5, per_page: 200, is_daily: 1 } });
        const recordsObj = res.data.records || res.data;
        const itemsArray = recordsObj.data || recordsObj;
        const items = itemsArray.filter(i => i.date === form.value.date && i.is_daily);

        for (const item of items) {
            await axios.delete(`/teachings/${item.id}`);
        }

        persistentToast.value = { msg: '✓ 今日每日開示暫存區已清空\n(仙師專區紀錄均已受保護)', type: 'success' };
        setTimeout(() => { if (persistentToast.value?.type === 'success') persistentToast.value = null; }, 3000);
        fetchItems(1);
    } catch (e) {
        persistentToast.value = { msg: '✖ 清空失敗', type: 'error' };
    } finally {
        loading.value = false;
    }
};

const triggerSimpleDownload = (text, filename) => {
    const blob = new Blob(['\uFEFF' + text], { type: 'text/plain;charset=utf-8' });
    downloadBlob(blob, filename);
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
        const res = await axios.get('/teachings', { params });
        const recordsObj = res.data.records || res.data;
        const allItems = recordsObj.data || recordsObj;
        const text = allItems.map((item, idx) => {
            return formatTeachingForExport(item, idx, allItems);
        }).join('\n' + '='.repeat(40) + '\n');

        const fileName = `開示記錄_${currentFolder.value?.name}_${getTodayStr()}.txt`;
        triggerSimpleDownload(text, fileName);
    } catch (e) { 
        console.error(e);
        persistentToast.value = { msg: '✖ 匯出失敗', type: 'error' };
    } finally { 
        loading.value = false; 
    }
};

const saveItem = async (data = null) => {
    if (data) {
        if (data.mode === 'batch') {
            activeEntryTab.value = 'batch';
            batchRecords.value = data.records || [];
            batchImportContent.value = data.importContent || '';
            await executeDistributionSave('distribute');
            return;
        }

        // Sync incoming data from TeachingAddForm
        form.value.date = data.date;
        form.value.master_id = data.master_id;
        form.value.dharma_name_ids = data.dharma_name_ids;
        form.value.target_remarks = data.target_remarks || '';
        form.value.content = data.content || '';
        form.value.items = data.items || [];
        form.value.items_footer_remarks = data.items_footer_remarks || '';
        dharmaSearchQuery.value = data.dharmaSearchQuery || '';
        
        // Sync masterNameInput for consistency
        const m = masters.value.find(v => v.id === data.master_id);
        if (m) masterNameInput.value = m.name;
    }

    if (newFooterRemark.value.trim()) addFooterRemark();
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
            br.items_footer_remarks = form.value.items_footer_remarks;
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
        currentMasterName = (currentFolder.value?.name ? formatMasterName(currentFolder.value.name) : '父皇');
    }
    // Validation: Prevent saving if no data is present
    const hasCurrentData = form.value.content?.trim() || form.value.items?.length > 0 || form.value.dharma_name_ids?.length > 0;
    const hasBatchData = batchRecords.value?.some(r => r.content?.trim() || r.items?.length > 0 || r.dharma_name_ids?.length > 0);
    const hasImportData = activeEntryTab.value === 'batch' && batchImportContent.value?.trim();

    if (!hasCurrentData && !hasBatchData && !hasImportData) {
        persistentToast.value = { msg: '✖ 請先輸入開示內容或賜降項目後再儲存。', type: 'error' };
        return;
    }

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
                target_remarks: form.value.target_remarks,
                items_footer_remarks: form.value.items_footer_remarks
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

        const blocksToProcess = batchRecords.value.filter(r => r.content?.trim() || r.items?.length > 0 || r.dharma_name_ids?.length > 0);
        if (blocksToProcess.length > 0) {
            // ── Master Mismatch Detection ────────────────────────────────────
            const mIdMap = { '老祖': 1, '元始': 2, '道祖': 3, '靈寶': 4, '父皇': 5, '太宰': 6, '太子': 7, '閻王': 8 };
            const isInSpecificFolder = currentFolder.value &&
                currentFolder.value.id !== 0 && currentFolder.value.id !== '0' && currentFolder.value.id !== null;

            if (isInSpecificFolder) {
                const folderMasterId = Number(currentFolder.value.id);
                let mismatchName = null;
                let mismatchId = null;
                for (const record of blocksToProcess) {
                    if (record.master_name) {
                        const mClean = record.master_name.replace(/仙師$/,'').trim();
                        const did = mIdMap[mClean];
                        if (did && did !== folderMasterId) {
                            mismatchName = record.master_name;
                            mismatchId = did;
                            break;
                        }
                    }
                }
                if (mismatchId) {
                    // Store blocks for use after user choice
                    saveConfirmModal.value.records = blocksToProcess.map(r => ({
                        ...r, items_footer_remarks: r.items_footer_remarks || form.value.items_footer_remarks || ''
                    }));
                    addMode.value = false;
                    masterMismatchModal.value = {
                        show: true,
                        detectedMasterName: mismatchName,
                        detectedMasterId: mismatchId,
                        currentMasterName: currentFolder.value.name,
                        currentMasterId: folderMasterId
                    };
                    pendingMasterId.value = null; // reset
                    itemsDetailMode.value = false;
                    return; // Wait for user choice
                }
            }
            // ── No mismatch: reset pendingMasterId and proceed ───────────────
            pendingMasterId.value = null;
            saveConfirmModal.value.records = blocksToProcess.map(r => ({
                ...r,
                items_footer_remarks: r.items_footer_remarks || form.value.items_footer_remarks || ''
            }));
            addMode.value = false;
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
            items_footer_remarks: form.value.items_footer_remarks,
            dharmaSearchQuery: dharmaSearchQuery.value || '',
            master_name: currentMasterName,
            target_remarks: form.value.target_remarks,
            date: form.value.date
        }];
        addMode.value = false;
        saveConfirmModal.value.show = true;
        showPreviewRecipients.value.clear();
        itemsDetailMode.value = false;
        return;
    }
};

// Called after user makes a choice in the master mismatch modal
const chooseMasterAndProceed = (useDetected) => {
    masterMismatchModal.value.show = false;
    if (useDetected) {
        // User wants to use the master detected from the pasted text
        pendingMasterId.value = 'auto';
    } else {
        // User wants to use the current folder's master
        pendingMasterId.value = masterMismatchModal.value.currentMasterId;
    }
    // Show save confirmation with the already-prepared records
    saveConfirmModal.value.show = true;
    itemsDetailMode.value = false;
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
        const { master_name: _mn, ...formClean } = form.value;
        const payload = {
            ...formClean,
            master_id: form.value.master_id || currentFolder.value?.id,
            is_daily: (currentFolder.value?.id == 0 || currentFolder.value?.id === '0') ? 1 : 0 // Correctly flag daily teachings vs master records
        };

        if (editingId.value) {
            await axios.put(`/teachings/${editingId.value}`, payload);
        } else {
            const res = await axios.post('/teachings', payload);
            if (res.data?.id) {
                const mObj = masters.value.find(v => v.id == payload.master_id);
                const newRecord = {
                    ...res.data,
                    master: mObj || null,
                    items: typeof res.data.items === 'string' ? JSON.parse(res.data.items) : (res.data.items || []),
                    dharma_name_ids: typeof res.data.dharma_name_ids === 'string' ? JSON.parse(res.data.dharma_name_ids) : (res.data.dharma_name_ids || [])
                };
                // Optimistic UI: Add to top of list immediately
                visibleItems.value.unshift(newRecord);
                focusedId.value = res.data.id;
                focusedDate.value = res.data.date;

                // Ensure the newly added item is scrolled into view
                nextTick(() => {
                    const element = document.getElementById(`teaching-row-${res.data.id}`);
                    if (element) {
                        element.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                });
            }
        }

        saveConfirmModal.value.show = false;
        addMode.value = false;

        // Refresh in background to ensure total consistency (sorting, etc)
        fetchItems(1);
    } catch (e) {
        console.error(e);
        persistentToast.value = { msg: '✖ 儲存失敗：' + (e.response?.data?.message || '伺服器錯誤'), type: 'error' };
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
    if (newFooterRemark.value.trim()) addFooterRemark();
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
                    }
                    // We NO LONGER strip item lines from content to ensure the list view shows the FULL pasted text
                    remainingContent.push(l);
                });
                content = remainingContent.join('\n');
            }

            // Allow save if there is content OR items OR names selected (as per user request)
            if (!content && items.length === 0 && record.dharma_name_ids.length === 0) continue;

            let blockMasterId = currentMasterId;

            // Master ID Resolution:
            // 1. pendingMasterId = number → user explicitly chose this master (folder's master)
            // 2. pendingMasterId = 'auto' → user chose text auto-detection
            // 3. pendingMasterId = null, isInSpecificFolder → use folder's master silently
            // 4. pendingMasterId = null, not in specific folder → auto-detect from text
            const isInSpecificMasterFolder = currentFolder.value && 
                currentFolder.value.id !== 0 && 
                currentFolder.value.id !== '0' && 
                currentFolder.value.id !== null;

            if (typeof pendingMasterId.value === 'number') {
                // User explicitly chose the folder's master via mismatch dialog
                blockMasterId = pendingMasterId.value;
            } else if (pendingMasterId.value === 'auto' || (mode === 'distribute' && !isInSpecificMasterFolder)) {
                // Auto-detect from pasted text
                let detectedId = null;
                if (record.master_name) {
                    const mClean = record.master_name.replace('仙師', '').trim();
                    if (masterMap[mClean]) detectedId = masterMap[mClean];
                }
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
                    blockMasterId = currentMasterId || 5;
                }
            }
            // else: isInSpecificMasterFolder && pendingMasterId===null → blockMasterId = currentMasterId (folder's master)

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
                        finalTargetRemarks = foundGroup.name;
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
                items_footer_remarks: record.items_footer_remarks || form.value.items_footer_remarks || '',
                items: items,
                user_id: props.user?.id || 1,
                is_daily: (currentFolder.value?.id == 0 || currentFolder.value?.id === '0') ? 1 : 0 // Correctly flag daily teachings vs master records
            };

            const res = await axios.post('/teachings', payload);
            if (res.data?.id) {
                const mObj = masters.value.find(v => v.id == blockMasterId);
                const newRecord = {
                    ...res.data,
                    master: mObj || null,
                    items: typeof res.data.items === 'string' ? JSON.parse(res.data.items) : (res.data.items || []),
                    dharma_name_ids: typeof res.data.dharma_name_ids === 'string' ? JSON.parse(res.data.dharma_name_ids) : (res.data.dharma_name_ids || [])
                };
                // Optimistic UI: Add to the beginning of the list to ensure visibility
                visibleItems.value.unshift(newRecord);
                if (!focusedId.value) {
                    focusedId.value = res.data.id;
                    focusedDate.value = res.data.date;
                }
            }
        }

        if (focusedId.value) {
            nextTick(() => {
                const element = document.getElementById(`teaching-row-${focusedId.value}`);
                if (element) {
                    element.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            });
        }

        distributionModal.value.show = false;
        addMode.value = false;

        // Stay in current folder to allow user to see the newly added data
        batchRecords.value = [{ dharma_name_ids: [], content: '', dharmaSearchQuery: '', target_remarks: '', items: [] }];
        batchImportContent.value = '';
        fetchItems(1);
    } catch (e) {
        addMode.value = false; // Also close form on error
        persistentToast.value = { msg: '✖ 存檔過程中發生錯誤', type: 'error' };
    } finally {
        saving.value = false;
    }
};

const showAdd = () => {
    editingId.value = null;
    form.value = {
        supplement: '', target_remarks: '', content: '',
        date: '', master_id: null, items: [], 
        remarks: '', items_footer_remarks: '', user_id: props.user?.id || 1, dharma_name_ids: []
    };

    dharmaSearchQuery.value = '';
    masterNameInput.value = '';
    batchImportContent.value = ''; // Reset the collective paste area

    const dailyMaster = masters.value.find(m => m.name === '父皇' || m.name === '父皇仙師');
    const dailyMasterId = dailyMaster?.id || null;

    if (currentFolder.value) {
        const isDaily = currentFolder.value.id === 0 || currentFolder.value.id === '0';
        const mName = isDaily ? '父皇' : currentFolder.value.name;
        const mId = isDaily ? dailyMasterId : currentFolder.value.id;

        form.value.master_id = mId;
        form.value.master_name = mName;
        masterNameInput.value = mName;

        batchRecords.value = [{ 
            dharma_name_ids: [], content: '', dharmaSearchQuery: '', 
            target_remarks: '', relatives: '', items: [], master_name: mName 
        }];

        if (isDaily) activeEntryTab.value = 'single';
        else activeEntryTab.value = 'batch';
    } else if (currentCategory.value === 'daily') {
        form.value.master_id = dailyMasterId;
        form.value.master_name = '父皇';
        masterNameInput.value = '父皇';
        batchRecords.value = [{
            dharma_name_ids: [], content: '', dharmaSearchQuery: '',
            target_remarks: '', relatives: '', items: [], master_name: '父皇'
        }];
        activeEntryTab.value = 'single';
    }
    addMode.value = true;
};

const showMultiMasterAdd = () => {
    editingId.value = null;
    form.value = {
        supplement: '', target_remarks: '', content: '',
        date: '', master_id: 5, items: [], 
        remarks: '', items_footer_remarks: '', user_id: props.user?.id || 1, dharma_name_ids: []
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

watch(batchRecords, (newVal) => {
    newVal.forEach(block => {
        if (block.target_remarks) {
            let rel = block.target_remarks.trim();
            if (rel === '之母' || rel === '母') block.target_remarks = '母親';
            else if (rel === '之父' || rel === '父') block.target_remarks = '父親';
            else if (rel === '之嬤' || rel === '嬤') block.target_remarks = '奶奶';
            else if (rel === '之夫' || rel === '夫') block.target_remarks = '先生';
            else if (rel.startsWith('之') || rel.startsWith('的')) {
                block.target_remarks = rel.substring(1);
            }
        }
    });
}, { deep: true });

watch(() => form.value.dharma_name_ids, (newIds) => {
    if (newIds.length === 1) {
        const name = getDharmaNameText(newIds[0]);
        if (dharmaSearchQuery.value !== name) dharmaSearchQuery.value = name;
    } else if (newIds.length === 0) {
        // Special case: "在場全體" is a virtual group with no IDs, don't clear it
        if (dharmaSearchQuery.value !== '在場全體') {
            dharmaSearchQuery.value = '';
        }
    } else {
        const isGroupMatch = (groups.value || []).some(g => g.name === dharmaSearchQuery.value || g.name === palaceToGroupName[dharmaSearchQuery.value]);
        if (!isGroupMatch && dharmaSearchQuery.value !== '在場全體') {
            dharmaSearchQuery.value = '';
        }
    }
}, { deep: true });

watch(newItemName, (val) => {
    if (val) {
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
    }
});

watch(newItemSubName, (val) => {
    if (val) {
        showSubRemarks.value = false;
    }
});

watch(currentFolder, (val) => { 
    if (val) { 
        // Only reset focus if we are NOT in the middle of saving/adding a record
        // This allows newly added records to remain focused after redirecting to Daily list
        if (!saving.value) {
            focusedId.value = null; 
            focusedDate.value = null; 
            visibleItems.value = []; 
        }
        fetchItems(1); 
        syncRecords(); 
    } 
});

const getFolderSum = (id) => {
    if (id === 0 || id === '0') {
        return folderCounts.value['daily'] || 0;
    }
    return folderCounts.value[id] || 0;
};

const getMastersTotalCount = () => {
    return Object.entries(folderCounts.value).reduce((total, [key, count]) => {
        if (key !== 'daily') return total + count;
        return total;
    }, 0);
};

onMounted(() => {
    syncRecords();
    document.addEventListener('click', () => {
        activeDropdownId.value = null;
        activeTreasureDropdownId.value = null;
        activeDaysDropdownId.value = null;
        activeSubTreasureDropdownId.value = null;
        activeTargetRemarksDropdown.value = null;
        activePractitionerDropdownId.value = null;
        activeSubPractitionerDropdownId.value = null;
    });
});
const isAnyModalOpen = computed(() => {
    return !!addMode.value || 
           !!focusedId.value || 
           !!persistentToast.value || 
           !!showAddMenu.value || 
           !!activeDropdownId.value || 
           !!itemsDetailMode.value ||
           saveConfirmModal.value?.show;
});

watch(isAnyModalOpen, (newVal) => {
    if (newVal) lockBodyScroll();
    else unlockBodyScroll();
});

onUnmounted(() => {
    if (isAnyModalOpen.value) unlockBodyScroll();
});
</script>

<style scoped>
.custom-scrollbar { -webkit-overflow-scrolling: touch; overscroll-behavior-y: contain; }
.custom-scrollbar::-webkit-scrollbar { width: 5px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
.font-biaokai-locked { font-family: 'DFKai-SB', '標楷體', serif !important; }
@keyframes slide-up { from { transform: translateY(10%); } to { transform: translateY(0); } }
.animate-slide-up { animation: slide-up 0.15s ease-out; }
@keyframes fade-in { from { opacity: 0; } to { opacity: 1; } }
.animate-fade-in { animation: fade-in 0.1s ease-out; }
.custom-date-input::-webkit-calendar-picker-indicator { margin-left: auto; cursor: pointer; }
* { -webkit-tap-highlight-color: transparent; }
</style>
