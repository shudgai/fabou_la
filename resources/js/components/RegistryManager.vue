<template>
    <div class="bg-white h-full flex flex-col relative text-slate-900 registry-manager-module">
        <!-- Transition Logo Overlay -->
        <div v-if="isInitialLoading" class="fixed inset-0 z-[5000] bg-white flex flex-col items-center justify-center pointer-events-none transition-opacity duration-300">
            <div class="relative flex flex-col items-center">
                <logo-imperial-notebook :height="120" spinning />
                <div class="mt-4 text-[17px] font-black text-slate-400 tracking-widest animate-pulse">載錄載入中...</div>
            </div>
        </div>
        <!-- Global Dual Header System -->
        <!-- Header 1: Module Level (Shown ONLY when not in a folder/add mode) -->
        <div v-if="!currentFolder && !addMode" 
            class="border-b border-transparent flex flex-col bg-white sticky top-0 z-[110] w-full transition-all duration-300">
            <!-- Row 1: Global Title -->
            <div class="px-4 py-2 bg-white flex items-center gap-2 border-b border-transparent">
                <logo-imperial-notebook :height="30" />
                <h1 class="font-outfit !font-black !text-[#dc2626] tracking-widest pt-[2px]" style="font-size: 26px !important; font-weight: 900 !important;">特殊法寶登記專區</h1>
            </div>
        </div>

        <!-- Header 2: Action/Folder Level (Mobile & Desktop Unified Layout) -->
        <div v-if="currentFolder && !addMode && !focusedId" 
            class="border-b border-white flex flex-col bg-white sticky top-0 z-[110] w-full transition-all duration-300 md:hidden">
            <!-- Row 1: Global Title -->
            <div class="px-4 py-2 bg-white flex items-center gap-2 border-b border-transparent">
                <logo-imperial-notebook :height="30" />
                <h1 class="font-outfit !font-black !text-[#dc2626] tracking-widest pt-[2px]" style="font-size: 26px !important; font-weight: 900 !important;">特殊法寶登記專區</h1>
            </div>
            <!-- Row 2: Category Name + Master Name (Consolidated 2-row Header) -->
            <div class="px-4 bg-white border-b border-transparent flex items-center justify-between py-[5px]">
                <div class="flex items-baseline gap-x-2 flex-1 flex-wrap pt-[10px]">
                    <span class="font-outfit font-normal text-slate-900" style="font-size: 23px !important; line-height: 1.1; transform: translateY(1.5px);">
                        {{ currentFolder.name === '父皇仙師' ? '父皇' : currentFolder.name }}
                    </span>
                </div>
                <div class="flex flex-col items-end justify-center shrink-0 ml-2 gap-0.5">
                    <button v-if="!reorderMode" @click="toggleSort" class="px-1 py-0.5 active:scale-95 transition-all font-black leading-none" style="color: #4f46e5 !important; font-size: 13px !important;">
                        {{ sortDesc ? '新→舊' : '舊→新' }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Main Scrollable Area -->
        <div ref="scrollContainer" :class="loading ? 'opacity-50 pointer-events-none' : ''" class="flex-1 overflow-y-auto custom-scrollbar !touch-auto bg-white transition-opacity duration-200" style="padding-bottom: 150px;">
            <!-- Category and Master Selection -->
            <div v-if="!currentFolder && !addMode" class="min-h-screen bg-white flex flex-col items-center">


                <!-- Root Categories (Scaled up to match TeachingManager) -->
                <div v-if="!currentCategory" class="flex-1 flex flex-col items-center pt-8 pb-20 w-full space-y-2.5 bg-white">

                    <button @click="currentCategory = 'major'" class="flex flex-col items-center justify-center bg-white active:scale-95 transition-all group relative rounded-none w-full max-w-[465px]">
                        <div class="relative w-full max-w-[465px] aspect-[245/158] bg-white">
                            <picture><source srcset="/image/registry_book_yellow_v2.webp" type="image/webp"><img src="/image/registry_book_yellow_v2.png" 
                                 fetchpriority="high"
                                 loading="eager"
                                 class="w-full h-full object-contain transition-transform group-hover:scale-105 mix-blend-multiply transform-gpu" 
                                 style="will-change: transform;"
                                 alt="書籍圖示"></picture>
                            <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none pt-2 pb-2 px-2 font-biaokai-locked" style="transform: translateY(12px);">
                                <div class="font-black tracking-tight leading-tight text-center font-biaokai-locked text-[34px] text-[#8B0000]">特殊法寶<br>登記專區</div>
                                <div class="mt-8 flex items-center font-biaokai-locked">
                                    <span class="text-white font-normal tracking-tight font-biaokai-locked text-[16px]">共 {{ categoryCounts.major || 0 }} 筆</span>
                                </div>
                            </div>
                        </div>
                    </button>
                </div>

                <!-- Masters Grid -->
                <div v-else class="grid grid-cols-2 justify-items-center w-full max-w-3xl mx-auto gap-y-6 gap-x-12 pt-12 pb-20 bg-white">
                    <button v-for="(folder, idx) in folders" :key="folder.id" 
                             @click="currentFolder = folder"
                              class="flex flex-col items-center justify-center transition-all active:scale-95 rounded-none group p-0 w-[175px] h-[138px] flex-shrink-0 relative overflow-visible bg-white"
                              :style="{ zIndex: folders.length - idx }">

                        <div class="relative w-[175px] h-[138px] flex items-center justify-center overflow-visible">
                            <picture><source srcset="/image/registry_book_yellow_v2.webp" type="image/webp"><img src="/image/registry_book_yellow_v2.png" 
                                 fetchpriority="high"
                                 loading="eager"
                                 class="w-[320px] h-[206px] max-none object-contain transition-transform group-hover:scale-105 mix-blend-multiply transform-gpu" 
                                 style="will-change: transform;"
                                 alt="書籍圖示"></picture>

                             <div class="absolute inset-0 flex flex-col items-center pointer-events-none pt-[52px] font-biaokai" style="font-family: 'DFKai-SB', '標楷體', 'BiauKai', 'Kaiti TC', serif !important; transform: translateY(2px);">
                                 <div class="font-black tracking-tighter leading-tight text-center font-biaokai -mt-6" style="font-size: 14.5px !important; color: rgb(139, 0, 0) !important; font-family: 'DFKai-SB', '標楷體', 'BiauKai', 'Kaiti TC', serif !important;">特殊法寶<br>登記專區</div>
                                
                                <div class="flex-1 flex flex-col items-center justify-center w-full mt-1">
                                    <div class="font-black tracking-tight leading-tight text-center whitespace-nowrap !font-black font-biaokai"
                                         :style="'font-size: 18px !important; font-weight: 900 !important; color: ' + (folder.name === '閻王仙師' ? '#000000' : '#dc2626') + ' !important; font-family: \'DFKai-SB\', \'標楷體\', \'BiauKai\', \'Kaiti TC\', serif !important; -webkit-text-stroke: 0.5px ' + (folder.name === '閻王仙師' ? '#000000' : '#dc2626') + ' !important;'">
                                         {{ folder.name === '父皇仙師' ? '父皇' : folder.name }}
                                    </div>
                                    <div class="mt-[15px] flex items-center font-biaokai-locked" style="font-family: 'DFKai-SB', '標楷體', 'BiauKai', 'Kaiti TC', serif !important;">
                                        <span class="font-black text-white font-biaokai-locked" style="font-size: 11.5px !important; font-family: 'DFKai-SB', '標楷體', 'BiauKai', 'Kaiti TC', serif !important;">{{ folderCounts[folder.id] || 0 }} 筆</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </button>
                </div>

                <div class="mt-12 flex justify-center pb-32">
                    <button @click="$emit('goHome')" class="text-slate-300 hover:text-slate-500 transition-colors flex items-center space-x-2 active:scale-95">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" /></svg>
                        <span class="text-xs font-medium tracking-widest">返回專區列表</span>
                    </button>
                </div>
            </div>

            <!-- Folder Contents -->
            <div v-else-if="currentFolder && !addMode" 
                :class="[
                    'px-0 bg-white transition-all duration-300 w-full md:max-w-xl md:mx-auto',
                    focusedId ? 'fixed inset-0 z-[100] pt-[90px] overflow-y-auto custom-scrollbar' : ''
                ]"
                style="">
                <!-- Desktop Centered Header Section within Col-7 (Updated per user request for 图2 Layout) -->
                <div v-if="!focusedId" class="hidden md:flex flex-col items-center border-b border-slate-100 bg-white sticky top-0 z-[50]">
                    <!-- Top Row: Main Title + All Action Buttons -->
                    <div class="flex items-center justify-between bg-white border-b border-slate-100 w-full py-2 px-4">
                        <div class="flex items-center gap-2">
                        <logo-imperial-notebook :height="36" class="md:hidden" />
                        <h1 class="tracking-widest font-outfit !font-black !text-[#dc2626]" style="font-size: 30px !important;">特殊法寶登記區</h1>
                    </div>
                        <div class="flex items-center space-x-3">
                            <!-- Sort Button -->
                            <button v-if="!reorderMode" @click="toggleSort" class="px-4 py-1.5 bg-indigo-600 border border-indigo-500 rounded-xl active:scale-95 transition-all font-black" style="color: white !important; font-size: 16px !important;">
                                {{ sortDesc ? '新→舊' : '舊→新' }}
                            </button>
                            <!-- Reorder Button -->
                            <button v-if="currentFolder" @click="reorderMode = !reorderMode" 
                                    :class="reorderMode ? 'bg-emerald-600 text-white border-2 border-emerald-500 shadow-lg' : 'bg-slate-50 text-slate-400 border border-transparent'"
                                    class="px-3 py-1.5 rounded-xl font-black transition-all active:scale-95 whitespace-nowrap"
                                    style="font-size: 16px !important;">
                                {{ reorderMode ? '確認排序' : '修改排序' }}
                            </button>
                            <!-- Back/Close Button -->
                            <button @click="resetToRoot" class="w-8 h-8 flex items-center justify-center rounded-full bg-slate-100 text-slate-400 hover:text-red-500 transition-all active:scale-90">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </button>
                        </div>
                    </div>
                    <!-- Bottom Row: Dynamic Sub-title + Folder Name (same line, adjacent) -->
                    <div class="px-4 py-2 bg-white flex items-baseline gap-x-2 w-full">
                        <span class="font-outfit font-normal text-slate-900" style="font-size: 23px !important; transform: translateY(1.5px);">
                            {{ currentFolder.name === '父皇仙師' ? '父皇' : currentFolder.name }}
                        </span>
                    </div>
                </div>

                <div :class="[focusedId ? 'p-0 pb-[120px] md:p-[10px]' : 'px-2 py-[10px]']" class="mt-0 relative">

                    <div v-if="showSearch && !focusedId" class="mb-4 sticky top-0 z-40 bg-white pb-2 px-1 animate-fade-in">
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-indigo-400 group-focus-within:text-indigo-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </div>
                                <input v-model.lazy="searchQuery" @keyup.enter="$event.target.blur()" type="text" placeholder="搜尋項目、用意、法號..." 
                                    class="block w-full pl-11 pr-12 h-[52px] bg-white border-2 border-transparent focus:border-indigo-100 focus:bg-white rounded-2xl text-[17px] font-black font-outfit text-slate-800 placeholder-slate-300 transition-all outline-none">
                                <button v-if="searchQuery" @click="searchQuery = ''" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-300 hover:text-red-500 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </button>
                            </div>
                        </div>

                        <!-- Pagination (Moved to headers for both mobile and desktop) -->

                        <!-- Empty State -->
                        <div v-if="filteredTreasures.length === 0 && !loading" class="flex flex-col items-center justify-center py-24 px-6 text-center">
                            <h3 class="text-[17px] font-black text-slate-300 font-outfit tracking-widest">尚無資料</h3>
                        </div>

                        <div v-for="(item, idx) in filteredTreasures" :key="item.id" 
                         @click="editItemId === item.id ? null : toggleExpand(item.id)"
                             :class="[
                                 'bg-white pl-3 pr-4 py-[10px] border-b border-slate-300 relative transition-all cursor-pointer active:bg-white flex items-start outline-none focus:outline-none',
                                 focusedId === item.id ? 'min-h-[calc(100dvh-100px)] md:min-h-[60dvh] border-transparent !mb-0 md:!mb-10 !rounded-none md:!rounded-[48px] -mx-4 md:mx-0 z-[60] md:border md:border-slate-100 md:mt-4' : '',
                                 openMenuId === item.id ? 'z-[50]' : 'z-0'
                             ]">

                            <!-- Sequence Number / Reorder Input -->
                            <div class="mr-2.5 shrink-0 flex items-center justify-center pt-1 md:pt-2">
                                <div v-if="!reorderMode" class="w-8 h-8 md:w-12 md:h-12 bg-slate-50 md:bg-slate-100/60 rounded-xl flex items-center justify-center text-[14px] md:text-[18px] font-black text-slate-400 md:text-slate-500 transition-all">
                                    {{ idx + 1 }}
                                </div>
                                <input v-else 
                                       type="number" 
                                       :value="idx + 1"
                                       @click.stop
                                       @change="e => handleReorder(item, e.target.value)"
                                       class="w-10 h-9 bg-blue-50 border-2 border-blue-200 rounded-xl text-center text-[15px] font-black text-blue-600 focus:ring-2 focus:ring-blue-400 outline-none">
                            </div>


                            <!-- Card Header (Toggle Expansion) - Standardized to Imperial Grace Style -->
                            <div :class="[!expandedIds.has(item.id) && editItemId !== item.id ? 'flex' : 'hidden md:flex']" class="mt-0 flex-col flex-1 min-w-0 pr-4 py-1">
                                <!-- Row 1: Name + (Expansion Indicator) -->
                                <div class="flex items-center justify-between">
                                    <div class="app-body font-bold text-slate-900 leading-tight truncate">{{ item.name }}</div>
                                    <div v-if="!expandedIds.has(item.id)" class="ml-2 text-slate-300 transition-colors md:hidden">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path d="M19 9l-7 7-7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </div>
                                </div>
                            </div>

                                    <!-- EDIT MODE: Inline editable fields -->
                                    <div v-if="editItemId === item.id" @click.stop class="border-t border-slate-50 md:mt-2 md:pt-4 md:border-t-slate-100 relative -ml-[40px] -mr-4">
                                        <!-- Branding Header for Edit Mode -->
                                        <div class="fixed top-0 left-0 right-0 z-[110] bg-white border-b border-slate-100 flex flex-col pt-2 animate-fade-in md:hidden">
                                            <!-- Row 1: Global Title -->
                                            <div class="px-4 py-2 bg-white flex items-center gap-2 border-b border-transparent">
                                                <logo-imperial-notebook :height="30" />
                                                <h1 class="font-outfit !font-black !text-[#dc2626] tracking-widest pt-[2px]" style="font-size: 26px !important; font-weight: 900 !important;">特殊法寶登記區</h1>
                                            </div>
                                            <!-- Row 2: Category Name (if selected) -->
                                            <div class="px-4 bg-white border-b border-transparent flex items-center justify-between py-[5px]">
                                                <div class="flex items-baseline gap-x-2 flex-1 flex-wrap">
                                                    <span class="font-outfit font-normal" style="font-size: 20px !important; transform: translateY(1.5px);"
                                                          :style="{ color: (item.master_id && getMasterName(item.master_id) === '閻王仙師') ? '#0f172a' : '#dc2626' }">
                                                        特殊法寶登記簿 — {{ item.master_id ? getMasterName(item.master_id) : '未設定' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="w-full space-y-4 px-0 mb-4 animate-fade-in pt-[90px] md:pt-[30px]">
                                            <!-- Collapsible Header for Edit Mode -->
                                            <div class="flex flex-col group cursor-pointer px-4 pt-4 pb-2" @click.stop="toggleDetails(item.id)">
                                                <div class="flex items-center justify-between">
                                                    <span class="font-bold text-[14px] text-slate-500">法寶名稱</span>
                                                    <!-- Toggle Icon -->
                                                    <div class="transition-transform duration-300" :class="{ 'rotate-180': expandedDetailIds.has(item.id) }">
                                                        <svg class="w-5 h-5 text-slate-300 group-hover:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path d="M19 9l-7 7-7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
                                                        </svg>
                                                    </div>
                                                </div>
                                                <div class="pt-1">
                                                    <input v-model="editData.name" type="text" placeholder="輸入名稱"
                                                        class="w-full text-[17px] font-black border-0 border-b-2 border-slate-100 focus:border-blue-500 bg-transparent py-1 outline-none"
                                                        @click.stop>
                                                </div>
                                            </div>

                                            <!-- Collapsible Details Section for Edit Mode -->
                                            <div v-if="expandedDetailIds.has(item.id)" class="space-y-4 animate-slide-down border-t border-slate-50 pt-4 pb-2 px-0">
                                                <div class="space-y-1 relative group px-4">
                                                    <label class="app-title tracking-wider block text-slate-500 font-bold">得知日期</label>
                                                    <input v-model="editData.record_date" type="text" placeholder="年/月/日"
                                                        class="w-full text-center text-[11.9px] font-normal border-0 border-b-2 border-slate-100 focus:border-blue-500 bg-transparent py-2 outline-none">
                                                    <button @click="activePicker = { field: 'record_date' }" class="absolute right-4 bottom-2 text-slate-300 hover:text-indigo-500">
                                                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2v12a2 2 0 002 2z" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                                    </button>
                                                </div>
                                                <div class="space-y-1 px-4">
                                                    <label class="app-title tracking-wider block text-slate-500 font-bold">用意 (選填)</label>
                                                    <textarea v-model="editData.purpose" rows="2" placeholder="輸入用意..."
                                                        class="w-full text-[17px] font-black border-0 border-b-2 border-slate-100 focus:border-blue-500 bg-transparent py-2 outline-none resize-none"></textarea>
                                                </div>
                                                <div class="space-y-1 px-4">
                                                    <label class="app-title tracking-wider block text-slate-500 font-bold">功效 (選填)</label>
                                                    <textarea v-model="editData.effect" rows="2" placeholder="輸入功效..."
                                                        class="w-full text-[17px] font-black border-0 border-b-2 border-slate-100 focus:border-blue-500 bg-transparent py-2 outline-none resize-none"></textarea>
                                                </div>
                                                <div class="space-y-1 px-4">
                                                    <label class="app-title tracking-wider block text-slate-500 font-bold">作法/求寶方式</label>
                                                    <textarea v-model="editData.acquisition_method" rows="2" placeholder="輸入作法..."
                                                        class="w-full text-[17px] font-black border-0 border-b-2 border-slate-100 focus:border-blue-500 bg-transparent py-2 outline-none resize-none"></textarea>
                                                </div>
                                                <div class="space-y-1 px-4">
                                                    <label class="app-title tracking-wider block text-slate-500 font-bold">法寶內容 (選填)</label>
                                                    <textarea v-model="editData.content" rows="2" placeholder="輸入法寶內容..."
                                                        class="w-full text-[17px] font-black border-0 border-b-2 border-slate-100 focus:border-blue-500 bg-transparent py-2 outline-none resize-none"></textarea>
                                                </div>
                                                <div class="space-y-1 px-4">
                                                    <label class="app-title tracking-wider block text-slate-500 font-bold">備註 (選填)</label>
                                                    <input v-model="editData.remarks" type="text" placeholder="輸入備註..."
                                                        class="w-full text-[17px] font-black border-0 border-b-2 border-slate-100 focus:border-blue-500 bg-transparent py-2 outline-none">
                                                </div>
                                            </div>

                                            <!-- 承接師兄姐 — Dharma Name Table Layout -->
                                            <div class="space-y-3 pt-[10px] border-t border-slate-50 mt-[10px] md:mt-6">
                                                <div class="flex items-center justify-between px-4">
                                                    <label class="app-title tracking-wider block text-slate-500 font-bold">承接師兄姐</label>
                                                    <button @click.stop="showDharmaSelector = !showDharmaSelector" 
                                                        class="flex items-center gap-1 px-3 py-1 bg-indigo-50 text-indigo-600 rounded-full text-[13px] font-black active:scale-95 transition-all">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                                        新增
                                                    </button>
                                                </div>

                                                <!-- Selector Grid (Toggleable) -->
                                                <div v-if="showDharmaSelector" class="animate-fade-in space-y-3 bg-white p-3 rounded-none border-y border-slate-100">
                                                    <div class="relative px-4">
                                                        <input v-model="dharmaEditSearch" type="text" placeholder="搜尋法號..."
                                                            class="w-full text-center text-[15px] font-black border-0 border-b-2 border-white focus:border-indigo-500 bg-transparent py-2 outline-none">
                                                    </div>
                                                    <div class="grid grid-cols-4 md:grid-cols-5 gap-1.5 max-h-[160px] overflow-y-auto custom-scrollbar py-1">
                                                        <template v-for="dn in filteredEditDharmaNames" :key="dn.id">
                                                            <button @click.stop="toggleDharmaSelection(dn)"
                                                                :style="{
                                                                    backgroundColor: isDharmaSelected(dn) ? '#bfdbfe' : '#ffffff',
                                                                    borderColor: isDharmaSelected(dn) ? '#93c5fd' : '#d1d5db',
                                                                    borderWidth: isDharmaSelected(dn) ? '2px' : '1px',
                                                                }"
                                                                class="flex items-center justify-center font-black text-[14px] transition-all active:scale-95 rounded-md border shadow-sm w-full min-h-[36px]">
                                                                <span class="truncate leading-none">{{ dn.name }}</span>
                                                            </button>
                                                        </template>
                                                    </div>
                                                </div>

                                                <!-- Table Layout (Matching View Mode) -->
                                                <div class="border border-slate-200 rounded-xl overflow-hidden bg-white mx-auto w-[90%] mb-6 px-4">
                                                    <table class="w-full border-collapse bg-white table-fixed">
                                                        <thead>
                                                            <tr class="bg-slate-50 text-slate-500 font-outfit border-b border-slate-200">
                                                                <th class="w-[32%] px-[10px] py-3 text-left font-black text-[15px] font-outfit border-r border-slate-100 pl-[10px]">法號</th>
                                                                <th class="w-[36%] px-[10px] py-3 text-center font-black text-[15px] font-outfit border-r border-slate-100">日期</th>
                                                                <th class="w-[26%] px-[10px] py-3 text-center font-black text-[15px] font-outfit">備註</th>
                                                                <th class="w-[6%]"></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr v-for="(dnr, dnrIdx) in editData.dharma_name_registries" :key="dnrIdx" class="hover:bg-slate-50 transition-colors border-b border-slate-50 last:border-0">
                                                                <td class="px-1 py-1.5 border-r border-slate-50">
                                                                    <input v-model="dnr.custom_name"
                                                                        list="dharma-names-list"
                                                                        @blur="syncDharmaNameId(dnr)"
                                                                        class="w-full text-[16px] font-black text-slate-900 border-0 bg-transparent outline-none cursor-text"
                                                                        placeholder="輸入法號...">
                                                                </td>
                                                                <td class="p-0 border-r border-slate-50 relative group">
                                                                    <input :value="dnr.obtained_date ? dnr.obtained_date.replace(/-/g, '/') : ''"
                                                                        @input="e => { const v = e.target.value.trim(); dnr.obtained_date = v ? v.replace(/\//g, '-') : ''; }"
                                                                        placeholder="--"
                                                                        class="w-full text-center text-[14px] font-normal !text-rose-600 bg-transparent py-2 outline-none pl-[16px]"
                                                                        style="font-family: 'PMingLiU', serif; font-size: 14px !important;">
                                                                    <button @click="activePicker = { field: 'obtained_date', index: dnrIdx }" class="absolute left-0.5 top-1/2 -translate-y-1/2 text-slate-200 hover:text-red-400 group-hover:text-red-300 transition-colors">
                                                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2v12a2 2 0 002 2z" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                                                    </button>
                                                                </td>
                                                                 <td class="p-0">
                                                                     <div v-if="activeRemarksEditIdx !== dnrIdx" 
                                                                          @click="activeRemarksEditIdx = dnrIdx"
                                                                          class="w-full min-h-[32px] flex items-center justify-center cursor-pointer py-2">
                                                                         <span class="text-[13px] font-bold text-slate-400 leading-tight" v-html="renderRemarksHtml(dnr.remarks, true)"></span>
                                                                     </div>
                                                                     <input v-else
                                                                         v-model="dnr.remarks"
                                                                         v-focus
                                                                         @blur="activeRemarksEditIdx = null"
                                                                         @keyup.enter="activeRemarksEditIdx = null"
                                                                         placeholder="--"
                                                                         class="w-full text-center text-[13px] font-bold text-slate-400 bg-transparent py-2 outline-none pl-[5px]">
                                                                 </td>
                                                                <td class="px-1 text-center">
                                                                    <button @click.stop="removeDharmaSelection(dnrIdx)" class="text-slate-300 hover:text-red-500 p-1">
                                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                                                    </button>
                                                                </td>
                                                            </tr>
                                                            <tr v-if="!editData.dharma_name_registries?.length">
                                                                <td colspan="4" class="py-8 text-center text-slate-300 text-[14px] font-black">尚未選擇承接人員</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>

                                            <!-- Save/Cancel Buttons -->
                                            <div class="flex gap-2 justify-center pt-4 pb-8 px-[15px]">
                                                <button @click="cancelEdit" class="w-[85px] py-3 bg-slate-100 text-slate-400 rounded-2xl font-black text-[17px] active:scale-95 transition-all">取消</button>
                                                <button @click="saveEdit" :disabled="isSaving" class="flex-1 max-w-[200px] py-3 bg-emerald-600 text-white rounded-2xl font-black text-[17px] active:scale-95 transition-all disabled:bg-slate-300" style="color: white !important;">{{ isSaving ? '儲存中...' : '確認修改' }}</button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- VIEW MODE: Read-only expanded details -->
                                    <div v-if="expandedIds.has(item.id) && editItemId !== item.id" @click.stop class="md:mt-2 md:pt-4 relative -ml-[40px] -mr-4">
                                        <!-- Header Actions (Inside Expanded) -->
                                        <div class="absolute right-2 top-12 z-[50] flex flex-col gap-3 items-center">
                                            <button @click.stop="toggleExpand(item.id)" 
                                                    class="p-2 active:scale-90 transition-all text-slate-400 hover:text-slate-600">
                                                <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path d="M6 18L18 6M6 6l12 12" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                            </button>
                                            <div class="relative">
                                                <button @click.stop="openMenuId = openMenuId === item.id ? null : item.id" 
                                                        class="p-2 active:scale-90 transition-all !text-[#dc1428]">
                                                    <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 24 24">
                                                        <circle cx="5" cy="12" r="2" />
                                                        <circle cx="12" cy="12" r="2" />
                                                        <circle cx="19" cy="12" r="2" />
                                                    </svg>
                                                </button>
                                                <div v-if="openMenuId === item.id" 
                                                     class="absolute right-0 mt-0 w-24 bg-white opacity-100 border border-slate-200 shadow-xl rounded-2xl z-[110] py-1 ring-1 ring-black ring-opacity-5 animate-fade-in overflow-hidden">
                                                    <button @click.stop="toggleExpand(item.id); openMenuId = null" 
                                                            class="w-full text-left px-3 py-2.5 text-[16px] font-black text-slate-900 hover:bg-slate-50 flex items-center transition-colors border-b border-slate-50 whitespace-nowrap">
                                                        收起
                                                    </button>
                                                    <button @click.stop="openEdit(item); openMenuId = null" 
                                                            class="w-full text-left px-3 py-2.5 text-[16px] font-black text-slate-900 hover:bg-slate-50 flex items-center transition-colors border-b border-slate-50 whitespace-nowrap">
                                                        修改
                                                    </button>
                                                    <button @click.stop="copyAsTextFile(item); openMenuId = null" 
                                                            class="w-full text-left px-3 py-2.5 text-[16px] font-black text-slate-900 hover:bg-slate-50 flex items-center transition-colors border-b border-slate-50 whitespace-nowrap">
                                                        複製
                                                    </button>
                                                    <button @click.stop="downloadItemData(item); openMenuId = null" 
                                                            class="w-full text-left px-3 py-2.5 text-[16px] font-black text-slate-900 hover:bg-slate-50 flex items-center transition-colors border-b border-slate-50 whitespace-nowrap">
                                                        下載
                                                    </button>
                                                    <button @click.stop="deleteConfirmId = item.id; openMenuId = null" 
                                                            class="w-full text-left px-3 py-2.5 text-[16px] font-black text-red-600 hover:bg-red-50 flex items-center transition-colors whitespace-nowrap">
                                                        刪除
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <div v-if="focusedId" class="fixed top-0 left-0 right-0 z-[110] bg-white border-b border-slate-100 flex flex-col pt-2 animate-fade-in md:hidden">
                                            <!-- Row 1: Global Title -->
                                            <div class="px-4 py-2 bg-white flex items-center gap-2 border-b border-transparent">
                                                <logo-imperial-notebook :height="30" />
                                                <h1 class="font-outfit !font-black !text-[#dc2626] tracking-widest pt-[2px]" style="font-size: 26px !important; font-weight: 900 !important;">特殊法寶登記區</h1>
                                            </div>
                                            <!-- Row 2: Category Name (if selected) -->
                                            <div class="px-4 bg-white border-b border-transparent flex items-center justify-between py-[5px]">
                                                <div class="flex items-baseline gap-x-2 flex-1 flex-wrap">
                                                    <span class="font-outfit font-normal" style="font-size: 20px !important; transform: translateY(1.5px);"
                                                          :style="{ color: (item.master_id && getMasterName(item.master_id) === '閻王仙師') ? '#0f172a' : '#dc2626' }">
                                                        特殊法寶登記簿 — {{ item.master_id ? getMasterName(item.master_id) : '未設定' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="w-full px-4 mb-4 animate-fade-in pt-4 mt-10 space-y-2 bg-white">
                                            <!-- Treasure Name Row (Header) -->
                                            <div class="flex flex-col group cursor-pointer px-4 pt-4 pb-2" @click.stop="toggleDetails(item.id)">
                                                <div class="flex items-center justify-between">
                                                    <span class="font-bold text-[14px] text-slate-500">法寶名稱</span>
                                                    <!-- Toggle Icon (Moved right) -->
                                                    <div class="transition-transform duration-300" :class="{ 'rotate-180': expandedDetailIds.has(item.id) }">
                                                        <svg class="w-5 h-5 text-slate-300 group-hover:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path d="M19 9l-7 7-7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
                                                        </svg>
                                                    </div>
                                                </div>
                                                <div class="pt-1">
                                                    <span class="font-black text-[16px] text-slate-900 leading-tight">{{ item.name || '-' }}</span>
                                                </div>
                                            </div>

                                            <!-- Collapsible Details Section -->
                                            <div v-if="expandedDetailIds.has(item.id)" class="space-y-4 animate-slide-down border-t border-slate-50 pt-4 pb-2 px-4">
                                                <!-- Date Grid -->
                                                <div class="flex flex-col">
                                                    <span class="font-bold text-[14px] text-slate-400">得知日期</span>
                                                    <div class="pt-1">
                                                        <span class="font-normal text-[15.5px] font-outfit text-slate-900">
                                                            {{ formatDisplayDate(item.record_date) || formatDisplayDate(getEarliestDate(item)) || '-' }}
                                                        </span>
                                                    </div>
                                                </div>

                                                <!-- Efficacy Grid -->
                                                <div class="flex flex-col">
                                                    <span class="font-bold text-[14px] text-slate-400">功效</span>
                                                    <div class="pt-1">
                                                        <span class="font-bold text-[16px] text-[#059669] leading-relaxed">{{ item.effect || '-' }}</span>
                                                    </div>
                                                </div>

                                                <!-- Purpose Grid -->
                                                <div class="flex flex-col">
                                                    <span class="font-bold text-[14px] text-slate-400">用意</span>
                                                    <div class="pt-1">
                                                        <span class="font-normal text-[16px] text-slate-800 leading-relaxed">{{ item.purpose || '-' }}</span>
                                                    </div>
                                                </div>

                                                <!-- Method Grid -->
                                                <div class="flex flex-col">
                                                    <span class="font-bold text-[14px] text-slate-400">作法/求寶方式</span>
                                                    <div class="pt-1">
                                                        <span class="font-normal text-[16px] text-slate-800 leading-relaxed">{{ item.acquisition_method || '-' }}</span>
                                                    </div>
                                                </div>

                                                <!-- Content Grid -->
                                                <div class="flex flex-col">
                                                    <span class="font-bold text-[14px] text-slate-400">法寶內容</span>
                                                    <div class="pt-1">
                                                        <span class="font-normal text-[16px] text-slate-800 leading-relaxed">{{ item.content || '-' }}</span>
                                                    </div>
                                                </div>

                                                <!-- Remarks Grid -->
                                                <div class="flex flex-col">
                                                    <span class="font-bold text-[14px] text-slate-400">備註</span>
                                                    <div class="pt-1">
                                                        <span class="font-normal text-[16px] text-slate-800 leading-relaxed">{{ item.remarks || '-' }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="space-y-3 pt-[10px] border-t border-slate-50 mt-[10px] md:mt-6">
                                            <template v-if="currentCategory === 'major'">
                                                <div class="border border-slate-200 rounded-xl overflow-hidden bg-white mx-auto w-[90%] mb-20 px-4">
                                                    <table class="w-full border-collapse bg-white table-fixed">
                                                        <thead>
                                                            <tr class="bg-slate-50 text-slate-500 border-b border-slate-200">
                                                                <th class="w-[38%] px-2 py-3 text-left font-black text-[15px] font-outfit border-r border-slate-100">法號</th>
                                                                <th class="w-[28%] px-2 py-3 text-center font-black text-[15px] font-outfit border-r border-slate-100">日期</th>
                                                                <th class="w-[34%] px-2 py-3 text-center font-black text-[15px] font-outfit">備註</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr v-for="dnr in getFilteredSortedRegistries(item)" :key="dnr.id || dnr.dharma_name_id" class="border-b border-slate-50 last:border-0 hover:bg-slate-50 transition-colors">
                                                                <td class="px-[10px] py-2.5 font-black text-slate-900 border-r border-slate-50 text-[16px] font-outfit truncate">
                                                                    {{ getDharmaNameText(dnr) }}
                                                                </td>
                                                                 <td class="px-[10px] py-2.5 text-left border-r border-slate-50">
                                                                        <span class="text-[14px] font-normal font-outfit text-rose-600" style="font-family: 'PMingLiU', serif;">
                                                                            {{ formatDisplayDate(dnr.obtained_date) || '--' }}
                                                                        </span>
                                                                    </td>
                                                                    <td class="px-[10px] py-2.5 text-center">
                                                                        <div @click.stop="triggerRemarksEdit(item, dnr)" class="w-full min-h-[24px] flex items-center justify-center cursor-pointer">
                                                                            <span class="text-[13px] font-bold text-slate-400 leading-tight" v-html="renderRemarksHtml(dnr.remarks)"></span>
                                                                        </div>
                                                                    </td>
                                                                 </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </template>
                                                <template v-else>
                                                    <div class="border border-slate-200 rounded-xl overflow-hidden bg-white mx-auto w-[90%] mb-20 px-4">
                                                        <table class="w-full border-collapse bg-white table-fixed">
                                                            <thead>
                                                                <tr class="bg-slate-50 text-slate-500 border-b border-slate-200">
                                                                    <th class="w-[38%] px-2 py-3 text-left font-black text-[15px] font-outfit border-r border-slate-100">法號</th>
                                                                    <th class="w-[28%] px-2 py-3 text-center font-black text-[15px] font-outfit border-r border-slate-100">日期</th>
                                                                    <th class="w-[34%] px-2 py-3 text-center font-black text-[15px] font-outfit">備註</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr v-for="dnr in getFilteredSortedRegistries(item)" :key="dnr.id" class="border-b border-slate-50 last:border-0 hover:bg-slate-50 transition-colors">
                                                                    <td class="px-[10px] py-2.5 font-black text-slate-900 border-r border-slate-50 text-[16px] font-outfit truncate">
                                                                        {{ getDharmaNameText(dnr) }}
                                                                    </td>
                                                                     <td class="px-[10px] py-2.5 text-left border-r border-slate-50">
                                                                         <span class="text-[14px] font-normal font-outfit text-rose-600" style="font-family: 'PMingLiU', serif;">
                                                                             {{ formatDisplayDate(dnr.obtained_date) || '--' }}
                                                                         </span>
                                                                     </td>
                                                                     <td class="px-[10px] py-2.5 text-center">
                                                                         <div @click.stop="triggerRemarksEdit(item, dnr)" class="w-full min-h-[24px] flex items-center justify-center cursor-pointer">
                                                                             <span class="text-[13px] font-bold text-slate-400 leading-tight">
                                                                                 <span v-if="dnr.related_personnel && dnr.related_personnel.length">{{ translateRelList(dnr.related_personnel) }}：</span>
                                                                                 <span v-html="renderRemarksHtml(dnr.remarks)"></span>
                                                                             </span>
                                                                         </div>
                                                                     </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>
                                </div>
                            </div>
                        </div>
                </div>

        <registry-add-form 
            v-if="addMode"
            ref="addFormRef"
            :mode="addMode" 
            :initialData="form"
            :masters="masters"
            :category="currentCategory"
            :isSaving="isSaving"
            @openRemarksEdit="handleAddFormRemarksEdit"
            @saveSingle="saveSingle"
            @saveBatch="triggerBatchSave"
            @cancel="addMode = null"
        />

        <!-- Persistent Toasts/Picker -->
        <teleport to="body">
            <div v-if="persistentToast" class="fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 z-[9999] pointer-events-auto">
                <div class="bg-white rounded-3xl shadow-[0_20px_60px_rgba(0,0,0,0.3)] flex flex-col border border-slate-100 overflow-hidden" style="padding: 28px; min-width: 320px; max-width: calc(100vw - 32px);">
                    <div class="flex items-start justify-between mb-8">
                        <span class="text-[17px] font-black text-slate-900 leading-relaxed tracking-widest">
                            {{ persistentToast.msg }}
                        </span>
                        <button @click="persistentToast = null" class="ml-6 text-slate-400 hover:text-slate-600 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="3" stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                    <div v-if="['confirm', 'deleteConfirm', 'mismatchConfirm'].includes(persistentToast.type)" class="flex space-x-4">
                        <button v-for="a in persistentToast.actions" :key="a.label" @click="a.handler" 
                            class="flex-1 h-[52px] rounded-2xl text-[16px] font-black tracking-tighter active:scale-95 transition-all shadow-lg !text-white border border-transparent"
                            :class="persistentToast.type === 'deleteConfirm' ? 'bg-red-600 shadow-red-100' : 'bg-blue-600 shadow-blue-100'"
                            style="color: white !important;">
                            {{ a.label }}
                        </button>
                        <button v-if="persistentToast.type !== 'mismatchConfirm'" @click="persistentToast = null" class="flex-1 bg-slate-100 text-slate-500 h-[52px] rounded-2xl border border-slate-200 text-[18px] font-black tracking-widest active:scale-95 transition-all">取消</button>
                    </div>
                    <div v-else class="flex justify-end mt-2">
                        <button @click="persistentToast = null" class="bg-indigo-600 !text-white px-10 py-3.5 rounded-2xl text-[18px] font-black tracking-widest active:scale-95 transition-all shadow-lg shadow-indigo-100" style="color: white !important;">知道了</button>
                    </div>
                </div>
            </div>
        </teleport>

        <!-- Delete Confirmation Overlay -->
        <teleport to="body">
            <div v-if="deleteConfirmId" class="fixed inset-0 z-[250] flex items-center justify-center p-4 bg-slate-900/80 animate-fade-in">
                <div class="bg-white w-full max-w-sm rounded-[32px] p-6 animate-pop-in">
                    <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" /></svg>
                    </div>
                    <h3 class="text-[22px] font-black text-center mb-2 font-outfit text-slate-800">確定要刪除嗎？</h3>
                    <p class="text-slate-500 text-center mb-6 font-black font-outfit leading-relaxed">此操作將永久刪除此筆載錄，且無法復原。請確認身分後再執行。</p>
                    <div class="flex flex-col space-y-3">
                        <button @click="executeDelete" class="w-full py-4 bg-red-600 !text-white rounded-2xl font-black text-[18px] active:scale-95 transition-all font-outfit">
                            是的，確認刪除
                        </button>
                        <button @click="deleteConfirmId = null" class="w-full py-4 bg-slate-100 text-slate-600 rounded-2xl font-black text-[18px] active:scale-95 transition-all font-outfit">
                            返回
                        </button>
                    </div>
                </div>
            </div>
        </teleport>



        <!-- Remarks Viewer -->
        <remarks-viewer 
            v-model="showRemarksModal"
            :remarks="activeRemarks"
            :editable="currentAddFormIdx !== null || currentDnrId !== null"
            :forceEdit="currentAddFormIdx !== null || currentDnrId !== null"
            :dnrId="currentDnrId"
            @save="handleRemarksViewerSave"
        />
        <lucky-draw :show="showLuckyDraw" @close="showLuckyDraw = false" />

        <!-- Bottom Pagination -->
        <div v-if="currentFolder && !focusedId && paginationMeta && paginationMeta.last_page > 1" 
             class="fixed z-[100] flex justify-center bg-white border-t border-slate-200 py-0.5" 
             style="bottom: calc(7dvh + env(safe-area-inset-bottom)); left: 0; right: 0;">
            <pagination-buttons :meta="paginationMeta" @page-change="handlePageChange" class="!mb-0 !mt-0" />
        </div>

        <mobile-navbar 
            is-absolute
            :can-back="true"
            :can-home="true"
            :show-action="true"
            :action-disabled="false"
            :action-active="showAddMenu"
            :search-active="showSearch"
            @back="handleBack"
            @home="$emit('goHome')"
            @action="showAddMenu = !showAddMenu"
            @search="showSearch = !showSearch"
        />

        <!-- Add Action Menu (uses z-[2000], above navbar) -->
        <add-action-menu
            v-if="!focusedId"
            :show="showAddMenu"
            @close="showAddMenu = false"
            :actions="addActions"
        />

        <!-- Date Picker for Registry Rows -->
        <compact-date-picker 
            v-if="activePicker"
            :show="true"
            :title="activePicker.field === 'record_date' ? '得知日期' : '取得日期'"
            @close="activePicker = null"
            @confirm="handlePickerConfirm"
        />

        <datalist id="dharma-names-list">
            <option v-for="dn in availableDharmaNamesForDatalist" :key="dn.id" :value="dn.name"></option>
        </datalist>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, defineEmits, watch, nextTick } from 'vue';
import { debounce } from '../utils/debounce';
import axios from 'axios';

const getTodayStr = () => {
    const d = new Date();
    return `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`;
};
import MobileNavbar from './MobileNavbar.vue';
import AddActionMenu from './AddActionMenu.vue';
import RegistryAddForm from './RegistryAddForm.vue';
import CompactDatePicker from './CompactDatePicker.vue';
import LuckyDraw from './LuckyDraw.vue';
import RemarksViewer from './RemarksViewer.vue';
import PaginationButtons from './PaginationButtons.vue';
import EditableInputChips from './EditableInputChips.vue';
import { writeClipboard, lockBodyScroll, unlockBodyScroll } from '../utils/iosCompat';

const isDesktop = ref(window.innerWidth >= 768);
const handleResize = () => { isDesktop.value = window.innerWidth >= 768; };

const activeRemarksEditIdx = ref(null);
const vFocus = {
    mounted: (el) => el.focus()
};

onMounted(() => {
    window.addEventListener('resize', handleResize);
    loadData();
});
onUnmounted(() => {
    window.removeEventListener('resize', handleResize);
});

const resetToRoot = () => {
    currentCategory.value = null;
    currentFolder.value = null;
    addMode.value = null;
    searchQuery.value = '';
    focusedId.value = null;
    openMenuId.value = null;
    expandedIds.value = new Set();
    reorderMode.value = false;
    dharmaPickerItemId.value = null;
    if (scrollContainer.value) {
        scrollContainer.value.scrollTop = 0;
    }
};

const emit = defineEmits(['goHome']);

const getEarliestDate = (item) => {
    if (!item.dharma_name_registries || item.dharma_name_registries.length === 0) {
        return item.record_date || '-';
    }
    const dates = item.dharma_name_registries
        .map(dn => dn.obtained_date)
        .filter(d => d)
        .sort();
    return dates.length > 0 ? dates[0] : (item.record_date || '-');
};

const translateRel = (rel) => {
    if (!rel) return '';
    let result = rel.trim();
    if (result === '之父' || result === '父') return '父親';
    if (result === '之母' || result === '母') return '母親';
    if (result === '之嬤' || result === '嬤') return '奶奶';
    if (result === '之夫' || result === '夫') return '先生';
    return result.replace(/^[之的]/, '');
};

const translateRelList = (relList) => {
    if (!relList) return '-';
    if (Array.isArray(relList)) {
        return relList.map(translateRel).join('、') || '-';
    }
    return translateRel(relList) || '-';
};

const formatDisplayDate = (dateStr) => {
    if (!dateStr || dateStr === '-' || dateStr === '未設定') return dateStr;
    // Strip time part if present (e.g., T16:00:00.000000Z)
    const s = String(dateStr).split('T')[0].trim();
    const parts = s.split(/[-\/]/);
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

const copyAsTextFile = (item) => {
    try {
        const text = formatRegistryForFile(item);
        navigator.clipboard.writeText(text);
        persistentToast.value = { msg: '✓ 內容已複製到剪貼簿', type: 'success' };
        setTimeout(() => { if (persistentToast.value?.type === 'success') persistentToast.value = null; }, 1500);
    } catch (err) {
        persistentToast.value = { msg: '✖ 複製失敗', type: 'error' };
    }
};

const formatRegistryForFile = (item) => {
    let registries = [...(item.dharma_name_registries || [])];

    registries.sort((a, b) => {
        const getSortIndex = (dnr) => {
            const name = getDharmaNameText(dnr);
            return dharmaNames.value.findIndex(dn => dn.id === dnr.dharma_name_id || dn.name === name);
        };

        const indexA = getSortIndex(a);
        const indexB = getSortIndex(b);

        if (indexA !== -1 && indexB !== -1) return indexA - indexB;
        if (indexA !== -1) return -1;
        if (indexB !== -1) return 1;
        return (getDharmaNameText(a)).localeCompare(getDharmaNameText(b), 'zh-Hant');
    });

    let res = `法寶內容：${item.name}\n用意：${item.purpose || ''}\n功效：${item.effect || ''}\n作法：${item.acquisition_method || ''}\n日期：${formatDisplayDate(getEarliestDate(item))}\n------------------\n`;
    registries.forEach(r => {
        res += `${getDharmaNameText(r)} | ${formatDisplayDate(r.obtained_date) || '-'} | ${r.remarks || ''}\n`;
    });
    return res;
};

// States
const currentCategory = ref(null); 
const currentFolder = ref(null);
const folderCounts = ref({});
const categoryCounts = ref({});
const addMode = ref(null);
const showLuckyDraw = ref(false);
const currentAddFormIdx = ref(null);
const addFormRef = ref(null);
const allTreasures = ref([]);
const masters = ref([]);
const dharmaNames = ref([]);
const loading = ref(false);
const isInitialLoading = ref(true);
const persistentToast = ref(null);
const isSaving = ref(false);
const dharmaPickerItemId = ref(null);
const sortDesc = ref(false);
const openMenuId = ref(null);
const expandedIds = ref(new Set());
const expandedDetailIds = ref(new Set());
const focusedId = ref(null);
const scrollContainer = ref(null);
const showAddMenu = ref(false);
const addActions = computed(() => {
    const actions = [
        {
            label: '逐筆新增',
            description: '輸入單筆登記紀錄',
            icon: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>',
            handler: () => openAdd('single')
        },
        {
            label: '多筆貼上新增',
            description: '貼上文字批次匯入',
            icon: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>',
            colorClass: 'bg-blue-50 text-blue-600',
            handler: () => openAdd('batch')
        }
    ];

    if (currentFolder.value) {
        actions.push({
            label: '複製全部至 LINE',
            description: '複製此資料夾所有記錄',
            icon: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>',
            colorClass: 'bg-emerald-50 text-emerald-600',
            handler: () => copyAllToLine()
        });
        actions.push({
            label: reorderMode.value ? '結束手動排序' : '手動排序',
            description: '調整記錄顯示順序',
            icon: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 6h16M4 12h16M4 18h7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>',
            colorClass: 'bg-amber-50 text-amber-600',
            handler: () => { reorderMode.value = !reorderMode.value; }
        });
    }

    return actions;
});
const showSearch = ref(false);
const searchQuery = ref('');

watch(searchQuery, debounce((newVal) => {
    if (newVal?.trim() && focusedId.value) {
        focusedId.value = null;
        expandedIds.value.clear();
    }
    currentPage.value = 1;
    loadData(1);
    if (scrollContainer.value) {
        scrollContainer.value.scrollTop = 0;
    }
}, 300));
const showExportMenu = ref(false);
const deleteConfirmId = ref(null);

const showRemarksModal = ref(false);
const activeRemarks = ref('');
const currentDnrId = ref(null);
const reorderMode = ref(false);
const paginationMeta = ref(null);
const currentPage = ref(1);
const editItemId = ref(null);
const editData = ref(null);
const dharmaEditSearch = ref('');
const showDharmaSelector = ref(false);
const activePicker = ref(null);

const handlePageChange = (page) => {
    currentPage.value = page;
    loadData(page);
};






const form = ref({
    master_id: null,
    category: 'major',
    name: '',
    purpose: '',
    effect: '',
    acquisition_method: '',
    remarks: '',
    record_date: '',
    obtained_date: '',
    dharma_name_registries: []
});

const folders = computed(() => {
    if (!Array.isArray(masters.value) || masters.value.length === 0) {
        return []; // Wait for API to load
    }
    return masters.value.map(m => ({
        ...m,
        name: m.name === '父皇仙師' ? '父皇' : m.name
    }));
});

const folderNameOptions = computed(() => folders.value.map(f => f.name));

const handleFolderNameChange = (newName) => {
    if (!newName) {
        currentFolder.value = null;
        return;
    }
    const found = folders.value.find(f => f.name === newName);
    if (found && found.id !== currentFolder.value?.id) {
        currentFolder.value = found;
    }
};

const loadData = async (page = 1) => {
    loading.value = true;
    // Clear existing treasures to prevent showing stale data from other folders
    if (page === 1) allTreasures.value = [];
    
    try {
        const ts = new Date().getTime();
        const params = {
            page,
            t: ts
        };
        if (searchQuery.value) params.search = searchQuery.value;
        if (currentFolder.value) params.master_id = currentFolder.value.id;
        if (currentCategory.value) params.category = currentCategory.value;
        params.sortDesc = sortDesc.value ? 1 : 0;

        // If no folder is selected, we only need the metadata (counts and lists)
        // We can skip the main records list to avoid "showing everything" briefly
        if (!currentFolder.value && !searchQuery.value) {
            params.per_page = 1; // Minimum fetch just for counts
        }

        const [res, mres, dres] = await Promise.all([
            axios.get('/registries', { params }),
            axios.get('/api/masters-list'),
            axios.get('/api/dharma-names-list?all=1')
        ]);
        allTreasures.value = res.data.data || [];
        paginationMeta.value = {
            current_page: res.data.current_page,
            last_page: res.data.last_page,
            total: res.data.total
        };
        if (res.data.folderCounts && page === 1) {
            folderCounts.value = res.data.folderCounts;
            categoryCounts.value = res.data.categoryCounts || {};
        }
        masters.value = mres.data;
        dharmaNames.value = dres.data;
    } catch (e) {
        console.error('Load data failed:', e);
        allTreasures.value = [];
    } finally { 
        loading.value = false; 
        isInitialLoading.value = false;
    }
};

watch([sortDesc, currentFolder, currentCategory], () => {
    currentPage.value = 1;
    loadData(1);
    if (scrollContainer.value) {
        scrollContainer.value.scrollTop = 0;
    }
});

const filteredTreasures = computed(() => {
    // If an item is focused, only show that item
    if (focusedId.value) {
        return allTreasures.value.filter(t => t.id === focusedId.value);
    }
    // With server-side pagination, allTreasures.value already contains filtered data
    return allTreasures.value;
});

const dynamicHeaderTitle = computed(() => {
    if (!currentFolder.value) return '特殊法寶登記區';

    const catName = currentCategory.value === 'major' ? '特殊法寶登記簿' : '其他皇恩登記簿';
    return `${catName}-${currentFolder.value.name}`;
});

const renderRemarksHtml = (remarks, isEditMode = false) => {
    if (!remarks || (Array.isArray(remarks) && remarks.length === 0)) return '--';
    let text = '';
    
    // Normalize input
    let raw = Array.isArray(remarks) ? remarks.join('；') : String(remarks);
    
    // Handle JSON array strings e.g. ["2024/06/23 ..."]
    const trimmed = raw.trim();
    if (trimmed.startsWith('[') && trimmed.endsWith(']')) {
        try {
            const parsed = JSON.parse(trimmed);
            if (Array.isArray(parsed)) {
                raw = parsed.join('；');
            }
        } catch (e) {}
    }
    
    // Strip wrapping quotes and outer symbols
    text = raw.replace(/^["'](.*)["']$/, '$1')
              .replace(/^[（(［\[「](.*?)[」\]］)）]$/, '$1');
    
    // Broad regex for dates: 2024/01/01, 113.10.06, 2024-05-12, etc.
    // Handles / . - and full-width ／
    const dateRegex = /(\d{2,4}[\/\.\-／]\s?\d{1,2}\s?[\/\.\-／]\s?\d{1,2})/g;

    // Replace and format date content to match YYYY/MM/DD and standard view mode style
    let replaced = text.replace(dateRegex, (match) => {
        const parts = match.split(/[\/\.\-／]/);
        let formatted = match;
        if (parts.length === 3) {
            let y = parts[0].trim();
            let m = parts[1].trim().padStart(2, '0');
            let d = parts[2].trim().padStart(2, '0');
            if (!isNaN(parseInt(y)) && !isNaN(parseInt(m)) && !isNaN(parseInt(d))) {
                formatted = `${y}/${m}/${d}`;
            }
        }
        const fontSize = isEditMode ? '14px' : '14px';
        return `<span style="color: #ef4444 !important; font-weight: normal !important; font-size: ${fontSize} !important; font-family: 'PMingLiU', serif !important;">${formatted}</span><br>`;
    });
    if (replaced.endsWith('<br>')) {
        replaced = replaced.slice(0, -4);
    }
    return replaced;
};

const getDharmaNameText = (dnr) => {
    const rawName = (dnr.custom_name || '').trim();
    if (rawName === '金巧' || rawName.startsWith('道霞')) {
        return rawName;
    }
    
    // Priority 1: Use pre-resolved ID from DB
    if (dnr.dharma_name_id) {
        const dn = dharmaNames.value.find(d => d.id === dnr.dharma_name_id);
        if (dn) {
            if (dn.id === 7 && rawName) {
                return rawName;
            }
            return dn.name;
        }
    }
    
    if (!rawName) return '--';
    
    // Resolve "金巧" or the new name/alias of ID 7 to the current official name of ID 7
    const goldQiaoOfficial = dharmaNames.value.find(d => d.id === 7);
    if (goldQiaoOfficial && (rawName === '金巧' || rawName === goldQiaoOfficial.name || (goldQiaoOfficial.alias && goldQiaoOfficial.alias.includes(rawName)))) {
        return rawName || goldQiaoOfficial.name;
    }
    
    if (rawName.startsWith('道霞')) {
        return rawName;
    }
    
    if (rawName === '金巧') return rawName;
    
    // Priority 2: Resolve alias/old name to official name
    const match = dharmaNames.value.find(d => 
        d.name === rawName || (d.alias && d.alias.includes(rawName))
    );
    
    return match ? match.name : rawName;
};

const getPeopleSummary = (item) => {
    if (!item.dharma_name_registries) return '';
    return item.dharma_name_registries.map(r => getDharmaNameText(r)).join('、');
};

const getSortedRegistries = (item) => {
    let rawRegistries = [...(item.dharma_name_registries || [])];
    let expanded = [];

    // Virtually split entries that contain multiple names
    rawRegistries.forEach(r => {
        const nameText = r.custom_name || '';
        const recognizedRels = ['母', '父', '夫', '嬤', '婆', '公', '先生', '太太', '母親', '父親', '奶奶', '爺爺', '外公', '外婆'];
        const isRel = recognizedRels.some(rel => nameText.endsWith(rel));
        
        if (!isRel && (nameText.includes(',') || nameText.includes('，') || nameText.includes('、'))) {
            const names = nameText.split(/[，、, \t]+/).map(n => n.trim()).filter(n => n);
            names.forEach(name => {
                expanded.push({ ...r, custom_name: name });
            });
        } else {
            expanded.push(r);
        }
    });

    // Deduplicate: Each person (by ID or name) should only appear once per treasure block
    const uniqueMap = new Map();
    expanded.forEach(r => {
        // Create a unique key: priority to dharma_name_id, then fallback to custom_name
        const key = r.dharma_name_id ? `id_${r.dharma_name_id}` : `name_${(r.custom_name || '').trim()}`;
        if (!uniqueMap.has(key)) {
            uniqueMap.set(key, r);
        }
    });
    
    let registries = Array.from(uniqueMap.values());

    // Sort based on master list order
    return sortRegistries(registries);
};

const getFilteredSortedRegistries = (item) => {
    let list = getSortedRegistries(item);
    const q = searchQuery.value?.toLowerCase().trim();
    if (!q) return list;
    return list.filter(dnr => {
        const dnText = getDharmaNameText(dnr).toLowerCase();
        const rawRemarks = dnr.remarks;
        const remarksStr = Array.isArray(rawRemarks) ? rawRemarks.join(' ') : (rawRemarks || '');
        return dnText.includes(q) || remarksStr.toLowerCase().includes(q);
    });
};



const personCentricList = computed(() => {
    if (currentCategory.value !== 'other' || !currentFolder.value) return [];
    const personMap = new Map();
    allTreasures.value
        .filter(t => t.master_id === currentFolder.value?.id && t.category === 'other')
        .forEach(t => {
            (t.dharma_name_registries || []).forEach(dnr => {
                const dn = dharmaNames.value.find(d => d.id === dnr.dharma_name_id);
                const personName = dn ? dn.name : dnr.custom_name;
                if (!personName) return;
                if (!personMap.has(personName)) personMap.set(personName, { name: personName, records: [] });
                personMap.get(personName).records.push({
                    id: t.id + '-' + (dnr.id || personName),
                    source_id: t.id,
                    treasure_name: t.name,
                    obtained_date: dnr.obtained_date,
                    remarks: dnr.remarks,
                    purpose: t.purpose
                });
            });
        });
    const sortedPersons = Array.from(personMap.values()).sort((a,b) => {
        const nameA = a.name;
        const nameB = b.name;
        const indexA = dharmaNames.value.findIndex(d => d.name === nameA);
        const indexB = dharmaNames.value.findIndex(d => d.name === nameB);
        if (indexA !== -1 && indexB !== -1) return indexA - indexB;
        if (indexA !== -1) return -1;
        if (indexB !== -1) return 1;
        return a.name.localeCompare(b.name, 'zh-Hant');
    });
    sortedPersons.forEach(p => p.records.sort((a,b) => (b.obtained_date||'').localeCompare(a.obtained_date||'')));
    return sortedPersons;
});

const displayTitle = computed(() => {
    if (!focusedId.value) return '';
    return allTreasures.value.find(t => t.id === focusedId.value)?.name || '';
});

const expandedPersonNames = ref(new Set());
const togglePerson = (name) => {
    const next = new Set(expandedPersonNames.value);
    if (next.has(name)) next.delete(name);
    else next.add(name);
    expandedPersonNames.value = next;
};

const openAdd = (mode = 'single') => {
    form.value = {
        master_id: currentFolder.value?.id || null,
        category: currentCategory.value || 'major',
        name: '',
        purpose: '',
        effect: '',
        acquisition_method: '',
        remarks: '',
        record_date: mode === 'single' ? getTodayStr() : '',
        obtained_date: '',
        status: '已求得',
        dharma_name_registries: []
    };
    addMode.value = mode;
};

const openEdit = (item) => {
    expandedIds.value = new Set([item.id]);
    focusedId.value = item.id; // Also focus it
    editItemId.value = item.id;
    const cloned = JSON.parse(JSON.stringify(item));
    
    // Split multi-name rows into individual rows for editing (Visual Parity with View Mode)
    if (cloned.dharma_name_registries) {
        const splitList = [];
        cloned.dharma_name_registries.forEach(r => {
            // Ensure custom_name is populated for the input field if missing but ID exists, OR if ID matches 7 (or custom_name is '金巧' / starts with '道霞')
            if (r.dharma_name_id === 7) {
                const dn = dharmaNames.value.find(d => d.id === 7);
                if (dn) {
                    if (!r.custom_name) {
                        r.custom_name = dn.name;
                    }
                }
            } else if (r.dharma_name_id) {
                const dn = dharmaNames.value.find(d => d.id === r.dharma_name_id);
                if (dn) r.custom_name = dn.name;
            }
            
            const goldQiaoOfficial = dharmaNames.value.find(d => d.id === 7);
            if (goldQiaoOfficial && (r.custom_name === '金巧' || (r.custom_name && r.custom_name.startsWith('道霞')))) {
                if (!r.custom_name.startsWith('道霞')) {
                    r.custom_name = goldQiaoOfficial.name;
                }
                r.dharma_name_id = 7;
            }
            const nameText = r.custom_name || '';
            const recognizedRels = ['母', '父', '夫', '嬤', '婆', '公', '先生', '太太', '母親', '父親', '奶奶', '爺爺', '外公', '外婆'];
            const isRel = recognizedRels.some(rel => nameText.endsWith(rel));
            
            if (!isRel && (nameText.includes(',') || nameText.includes('，') || nameText.includes('、'))) {
                const names = nameText.split(/[，、, \t]+/).map(n => n.trim()).filter(n => n);
                names.forEach(name => {
                    splitList.push({ ...r, custom_name: name, id: null });
                });
            } else {
                splitList.push(r);
            }
        });
        cloned.dharma_name_registries = splitList;

        // Strict De-duplicate to prevent rows appearing twice (as seen in user report)
        const uniqueList = [];
        const seenKeys = new Set();
        cloned.dharma_name_registries.forEach(r => {
            const name = (r.custom_name || '').trim();
            const key = r.dharma_name_id ? `id:${r.dharma_name_id}` : (name ? `name:${name}` : null);
            if (key && !seenKeys.has(key)) {
                seenKeys.add(key);
                uniqueList.push(r);
            } else if (!key) {
                uniqueList.push(r); // Keep empty rows if any
            }
        });
        cloned.dharma_name_registries = uniqueList;
        sortRegistries(cloned.dharma_name_registries);
    }
    
    editData.value = cloned;
    showDharmaSelector.value = false;
};

const cancelEdit = () => {
    editItemId.value = null;
    editData.value = null;
};

const saveEdit = async () => {
    if (!editData.value || isSaving.value) return;
    isSaving.value = true;
    try {
        const data = editData.value;
        const rawRegistries = (data.dharma_name_registries || []).map(r => ({
            id: r.id,
            dharma_name_id: r.dharma_name_id,
            custom_name: r.custom_name,
            obtained_date: r.obtained_date,
            remarks: typeof r.remarks === 'string' ? r.remarks : (Array.isArray(r.remarks) ? r.remarks.join('\n') : ''),
            related_personnel: Array.isArray(r.related_personnel) ? r.related_personnel : (r.related_personnel ? r.related_personnel.split(/[、, ]+/).filter(x => x) : [])
        }));

        // De-duplicate based on dharma_name_id or custom_name
        const registries = [];
        const seen = new Set();
        rawRegistries.forEach(r => {
            const key = r.dharma_name_id ? `id:${r.dharma_name_id}` : `name:${(r.custom_name || '').trim()}`;
            if (key && !seen.has(key)) {
                seen.add(key);
                registries.push(r);
            }
        });

        const payload = {
            id: data.id,
            master_id: data.master_id,
            category: data.category,
            name: data.name,
            purpose: data.purpose,
            effect: data.effect,
            acquisition_method: data.acquisition_method,
            remarks: data.remarks,
            record_date: data.record_date,
            obtained_date: data.obtained_date,
            status: data.status,
            dharma_name_registries: registries,
            _method: 'PATCH'
        };
        await axios.post(`/registries/${data.id}`, payload);
        editItemId.value = null;
        editData.value = null;
        expandedIds.value.clear();
        focusedId.value = null;
        loadData();
        persistentToast.value = { msg: '✓ 修改成功', type: 'success' };
        setTimeout(() => { if (persistentToast.value?.type === 'success') persistentToast.value = null; }, 2000);
    } catch (e) {
        console.error(e);
        const errorMsg = e.response?.data?.error || e.response?.data?.message || '儲存失敗';
        persistentToast.value = { msg: '✖ ' + errorMsg, type: 'error' };
    } finally {
        isSaving.value = false;
    }
};

const goldQiaoExpanded = ref(false);
const handlePickerConfirm = (date) => {
    if (!activePicker.value) return;
    const { field, index } = activePicker.value;
    const dateStr = date.replace(/\//g, '-');
    if (field === 'record_date') {
        editData.value.record_date = dateStr;
    } else if (field === 'obtained_date' && index !== null) {
        editData.value.dharma_name_registries[index].obtained_date = dateStr;
    }
    activePicker.value = null;
};

const filteredEditDharmaNames = computed(() => {
    const q = dharmaEditSearch.value?.toLowerCase().trim();
    const selectedIds = new Set((editData.value?.dharma_name_registries || []).map(r => r.dharma_name_id).filter(id => id));
    const selectedNames = new Set((editData.value?.dharma_name_registries || []).map(r => (r.custom_name || '').trim()).filter(n => n));
    
    let list = dharmaNames.value.filter(dn => !selectedIds.has(dn.id) && !selectedNames.has(dn.name));

    if (!q) return list;
    return list.filter(dn => dn.name.toLowerCase().includes(q) || (dn.alias && dn.alias.toLowerCase().includes(q)));
});

const availableDharmaNamesForDatalist = computed(() => {
    if (!editData.value?.dharma_name_registries) return dharmaNames.value;
    const selectedNames = new Set(editData.value.dharma_name_registries.map(r => (r.custom_name || '').trim()).filter(n => n));
    return dharmaNames.value.filter(dn => !selectedNames.has(dn.name));
});

const isDharmaSelected = (dn) => {
    if (!editData.value?.dharma_name_registries) return false;
    return editData.value.dharma_name_registries.some(r => r.dharma_name_id === dn.id);
};

const sortRegistries = (arr) => {
    if (!arr) return [];
    const getSortIndex = (dnr) => {
        // ID: 7 represents 金巧 (or whatever it is modified to)
        if (dnr.dharma_name_id === 7) {
            const fengIdx = dharmaNames.value.findIndex(dn => dn.id === 6 || dn.name === '鳳尊');
            if (fengIdx !== -1) return fengIdx + 0.5;
        }
        const name = getDharmaNameText(dnr);
        const goldQiaoOfficial = dharmaNames.value.find(d => d.id === 7);
        if (name === '金巧' || name.startsWith('道霞') || (goldQiaoOfficial && (name === goldQiaoOfficial.name || name.startsWith(goldQiaoOfficial.name)))) {
            const fengIdx = dharmaNames.value.findIndex(dn => dn.id === 6 || dn.name === '鳳尊');
            if (fengIdx !== -1) return fengIdx + 0.5;
        }
        
        const searchName = name;
        return dharmaNames.value.findIndex(dn => dn.id === dnr.dharma_name_id || dn.name === searchName);
    };
    return arr.sort((a, b) => {
        const indexA = getSortIndex(a);
        const indexB = getSortIndex(b);
        if (indexA !== -1 && indexB !== -1) return indexA - indexB;
        if (indexA !== -1) return -1;
        if (indexB !== -1) return 1;
        return (getDharmaNameText(a)).localeCompare(getDharmaNameText(b), 'zh-Hant');
    });
};

const getNamesList = (item) => {
    if (!item.dharma_name_registries) return '-';
    // Use the same sort logic as the list view
    const sorted = sortRegistries([...item.dharma_name_registries]);
    return sorted.map(r => getDharmaNameText(r)).join(', ');
};


const syncDharmaNameId = (dnr) => {
    if (!dnr.custom_name) {
        dnr.dharma_name_id = null;
        return;
    }
    const name = dnr.custom_name.trim();
    
    // Prevent duplicate entries within the same record
    const isDuplicate = (editData.value.dharma_name_registries || []).some(r => 
        r !== dnr && (
            (r.custom_name && r.custom_name.trim() === name) || 
            (r.dharma_name_id && dharmaNames.value.find(d => d.id === r.dharma_name_id)?.name === name)
        )
    );

    if (isDuplicate) {
        alert(`「${name}」已存在於清單中，請勿重複輸入。`);
        dnr.custom_name = '';
        dnr.dharma_name_id = null;
        return;
    }

    const dn = dharmaNames.value.find(d => d.name === name || d.alias === name);
    if (dn) {
        dnr.dharma_name_id = dn.id;
        dnr.custom_name = dn.name; // Use standard name
    } else {
        dnr.dharma_name_id = null;
    }
};

const toggleDharmaSelection = (dn) => {
    const registries = editData.value.dharma_name_registries || [];
    const idx = registries.findIndex(r => r.dharma_name_id === dn.id);

    if (idx >= 0) {
        registries.splice(idx, 1);
    } else {
        registries.push({
            dharma_name_id: dn.id,
            custom_name: dn.name,
            obtained_date: editData.value.record_date || '',
            related_personnel: [],
            remarks: ''
        });
        sortRegistries(editData.value.dharma_name_registries);
    }
};

const removeDharmaSelection = (idx) => {
    editData.value.dharma_name_registries.splice(idx, 1);
};

const deleteDharmaNameRegistry = async (dnr, item) => {
    if (!dnr.id) return;
    if (!window.confirm(`確定刪除「${getDharmaNameText(dnr)}」的法號紀錄？`)) return;
    try {
        await axios.delete(`/dharma-name-registries/${dnr.id}`);
        if (item.dharma_name_registries) {
            const idx = item.dharma_name_registries.findIndex(r => r.id === dnr.id);
            if (idx !== -1) item.dharma_name_registries.splice(idx, 1);
        }
        persistentToast.value = { msg: '✓ 已刪除', type: 'success' };
        setTimeout(() => { if (persistentToast.value?.type === 'success') persistentToast.value = null; }, 2000);
    } catch (e) {
        const errorMsg = e.response?.data?.error || e.response?.data?.message || '刪除失敗';
        persistentToast.value = { msg: '✖ ' + errorMsg, type: 'error' };
    }
};

const getMasterName = (id) => {
    const m = masters.value.find(m => String(m.id) === String(id));
    if (m) {
        return m.name === '父皇仙師' ? '父皇' : m.name;
    }
    return '未設定';
};

const toggleExpand = (id) => {
    const nextExpanded = new Set(expandedIds.value);
    if (nextExpanded.has(id)) {
        nextExpanded.delete(id);
        expandedDetailIds.value.delete(id); // Clear detail state on close
        focusedId.value = null;
    } else {
        nextExpanded.clear();
        nextExpanded.add(id);
        focusedId.value = id;
    }
    expandedIds.value = nextExpanded;
};

const toggleDetails = (id) => {
    if (expandedDetailIds.value.has(id)) {
        expandedDetailIds.value.delete(id);
    } else {
        expandedDetailIds.value.add(id);
    }
};

const handleBack = () => {
    if (focusedId.value) {
        focusedId.value = null;
        expandedIds.value.clear();
        return;
    }
    if (addMode.value) addMode.value = null;
    else if (currentFolder.value) currentFolder.value = null;
    else if (currentCategory.value) currentCategory.value = null;
    else emit('goHome');
};

const saveSingle = async (data, shuntAction = null) => {
    if (isSaving.value) return;
    if (!data.name?.trim()) {
        persistentToast.value = { msg: '✖ 請輸入求寶內容', type: 'error' };
        return;
    }
    // 如果沒有選擇仙師，預設存入當前資料夾
    if (!data.master_id) {
        if (currentFolder.value) {
            data.master_id = currentFolder.value.id;
        } else {
            persistentToast.value = { msg: '✖ 請選擇仙師', type: 'error' };
            return;
        }
    }

    // Master Mismatch Routing (Shunting)
    if (currentFolder.value && String(data.master_id) !== String(currentFolder.value.id) && !shuntAction) {
        persistentToast.value = {
            msg: `偵測到所選仙師為「${getMasterName(data.master_id)}」，與當前資料夾「${currentFolder.value.name}」不同。請問要存入何處？`,
            type: 'mismatchConfirm',
            actions: [
                { label: `存入 ${currentFolder.value.name}`, handler: () => saveSingle(data, 'force') },
                { label: `存入 ${getMasterName(data.master_id)}`, handler: () => saveSingle(data, 'shunt') }
            ]
        };
        return;
    }

    // Apply the chosen action
    if (shuntAction === 'force') {
        data.master_id = currentFolder.value.id;
    }

    // 前處理：將「親友」欄位轉換為備註內容後再送出
    const processedDnrs = (data.dharma_name_registries || []).map(p => {
        const { relationship, ...rest } = p;
        let remarks = rest.remarks || '';
        if (relationship && relationship.trim()) {
            let datePrefix = '';
            if (rest.obtained_date) {
                const dParts = rest.obtained_date.split('-');
                if (dParts.length === 3) {
                    datePrefix = `${dParts[0]}/${dParts[1].padStart(2,'0')}/${dParts[2].padStart(2,'0')}`;
                }
            }
            // 格式：日期  法號關係詞（例如：2026-05-02  靈昡父親）
            const namePart = rest.custom_name ? rest.custom_name.trim() : '';
            const relEntry = datePrefix
                ? `${datePrefix}  ${namePart}${relationship.trim()}`
                : `${namePart}${relationship.trim()}`;
            if (!remarks.includes(relEntry)) {
                remarks = remarks ? remarks + '\n' + relEntry : relEntry;
            }
            // Clear the obtained_date because the relationship person obtained it, not the dharma name holder
            rest.obtained_date = null;
        }
        return {
            ...rest,
            remarks,
            related_personnel: relationship
                ? relationship.split(/[、, ]+/).filter(x => x)
                : (rest.related_personnel || [])
        };
    });

    isSaving.value = true;
    try {
        const payload = { ...data, dharma_name_registries: processedDnrs };
        if (data.id) await axios.post(`/registries/${data.id}`, { ...payload, _method: 'PATCH' });
        else await axios.post('/registries', payload);
        addMode.value = null;
        expandedIds.value.clear();
        focusedId.value = null;
        loadData();
        persistentToast.value = { msg: '✓ 儲存成功', type: 'success' };
        setTimeout(() => { if (persistentToast.value?.type === 'success') persistentToast.value = null; }, 2000);
    } catch (e) {
        console.error(e);
        const errorMsg = e.response?.data?.error || e.response?.data?.message || '儲存失敗，請檢查資料';
        persistentToast.value = { msg: '✖ ' + errorMsg, type: 'error' };
    } finally {
        isSaving.value = false;
    }
};

const triggerBatchSave = async (batchData) => {
    if (isSaving.value) return;
    isSaving.value = true;
    try {
        // Use pre-parsed rows from the form if available (avoids incompatible re-parsing)
        if (batchData.rows && batchData.rows.length > 0) {
            const records = batchData.rows.map(row => ({
                name: row.name,
                master_id: row.master_id || batchData.masterId,
                category: currentCategory.value || 'major',
                record_date: row.record_date || '',
                obtained_date: row.record_date || '',
                acquisition_method: row.acquisition_method || '',
                purpose: row.purpose || '',
                effect: row.effect || '',
                content: row.content || '',
                remarks: row.remarks || '',
                dharma_name_registries: [{
                    custom_name: row.recipient_name,
                    remarks: row.person_remarks || '',
                    obtained_date: row.record_date || ''
                }].filter(d => d.custom_name)
            }));
            if (records.length === 0) {
                persistentToast.value = { msg: '✖ 無法解析出任何法寶資料', type: 'error' };
                isSaving.value = false;
                return;
            }
            const res = await axios.post('/registries/batch', records);
            const savedCount = res.data?.count ?? records.length;
            addMode.value = null;
            expandedIds.value.clear();
            focusedId.value = null;
            loadData();
            persistentToast.value = { msg: `✓ 批量新增成功 (${savedCount} 筆)`, type: 'success' };
            setTimeout(() => { if (persistentToast.value?.type === 'success') persistentToast.value = null; }, 2000);
            isSaving.value = false;
            return;
        }

        const text = batchData.input || '';
        const blocks = text.split(/\n\s*\n|\r\n\s*\r\n/).filter(b => b.trim());
        const masterNames = masters.value.map(m => m.name);
        const masterMap = {};
        masters.value.forEach(m => masterMap[m.name] = m.id);

        const nameAliasMap = {
            '金容': '靈果', '金涓': '靈慧', '金梅': '靈妙', '金蘭': '靈智', '金平': '靈平',
            '金瑞': '龍戰', '金耀': '龍勝', '金旭': '靈心', '金熹': '靈情', '金吉': '靈奇',
            '金祥': '靈傾', '金恩': '靈昡', '金鈺': '元續', '金穎': '赤峰',
            '金律': '閻㻇', '金欣': '閻闇', '閰琉': '閻尊', '金剛': '閰帝', '金頓': '閻爵',
            '金虹': '赤覺', '金湘': '紫元', '金雍': '道妙', '金無': '閻澤', '金真': '閻願',
            '金翎': '鳳尊', '金妙': '鳳媓'
        };
        const translateName = (raw) => nameAliasMap[raw] || raw;

        let rawRecords = [];
        let blockMasterId = batchData.masterId || (currentFolder.value?.id);
        if (!blockMasterId) {
            isSaving.value = false;
            persistentToast.value = { msg: '✖ 請先選擇仙師再進行批量新增', type: 'error' };
            return;
        }
        let blockDate = batchData.date || '';
        let currentContextYear = new Date().getFullYear();

        const parseDateStr = (str, ctxYear = null) => {
            if (!str) return null;
            // Handle explicitly ROC prefixes like "民國113年" or "113年" or "113"
            const rocYearMatch = str.match(/^(?:民國)?\s*(\d{2,3})\s*年?$/);
            if (rocYearMatch) {
                const y = parseInt(rocYearMatch[1]) + 1911;
                return `${y}-01-01`;
            }

            // Priority 1: 4-digit CE
            const ceMatch = str.match(/\b(\d{4})[\/\-\s](\d{1,2})[\/\-\s](\d{1,2})\b/);
            if (ceMatch) return `${ceMatch[1]}-${ceMatch[2].padStart(2,'0')}-${ceMatch[3].padStart(2,'0')}`;
            // Priority 2: 2-3 digit ROC
            const rocMatch = str.match(/\b(\d{2,3})[\/\-\s](\d{1,2})[\/\-\s](\d{1,2})\b/);
            if (rocMatch) {
                const y = parseInt(rocMatch[1]) + 1911;
                return `${y}-${rocMatch[2].padStart(2,'0')}-${rocMatch[3].padStart(2,'0')}`;
            }
            // Priority 3: Month/Day only (Uses contextYear or Current Year)
            const mdMatch = str.match(/\b(\d{1,2})[\/\-\s](\d{1,2})\b/);
            if (mdMatch) {
                const y = ctxYear || new Date().getFullYear();
                return `${y}-${mdMatch[1].padStart(2,'0')}-${mdMatch[2].padStart(2,'0')}`;
            }
            // Fallback for year only
            const standaloneYMatch = str.match(/^\s*(\d{2,4})\s*[年]?\s*$/);
            if (standaloneYMatch) {
                let y = parseInt(standaloneYMatch[1]);
                if (y < 1000) y += 1911;
                return `${y}-01-01`;
            }
            return null;
        };

        blocks.forEach(block => {
            const lines = block.split('\n').map(l => l.trim()).filter(l => l);
            if (lines.length === 0) return;

            let pendingTreasureName = '';
            let pendingAttrs = { purpose: '', remarks: '', status: '', record_date: null, obtained_date: null, acquisition_method: '' };
            let activeRecord = null;

            for (let i = 0; i < lines.length; i++) {
                let line = lines[i].normalize('NFKC').trim();

                // Inline Year Prefix (e.g. 民國113年 4/21 允求...)
                const inlineYearMatch = line.match(/^(?:民國)?\s*(\d{2,4})\s*年?\s+/);
                if (inlineYearMatch) {
                    let y = parseInt(inlineYearMatch[1]);
                    if (y < 1000) y += 1911;
                    currentContextYear = y;
                    line = line.replace(inlineYearMatch[0], '').trim();
                }

                // 1. Metadata Detection
                masterNames.forEach(mName => { 
                    if (line.includes(mName) && line.length < 15) {
                        blockMasterId = masterMap[mName];
                        activeRecord = null; // Clear context on new master
                    }
                });

                // Detect Standalone Year Line
                const standaloneYearMatch = line.match(/^(?:民國)?\s*(\d{2,4})\s*年?$/);
                if (standaloneYearMatch) {
                    let y = parseInt(standaloneYearMatch[1]);
                    if (y < 1000) y += 1911;
                    currentContextYear = y;
                    continue; // Skip the rest, it's just setting the context year
                }

                const lineDateParsed = parseDateStr(line, currentContextYear);
                const isPureDateStr = line.replace(/[\d\/.\-年月日時分秒\s]/g, '').length === 0;
                if (lineDateParsed && !line.includes('|') && !line.includes('│') && !line.includes('\t') && isPureDateStr) {
                    blockDate = lineDateParsed;
                    activeRecord = null; // Clear context on new date header
                    if (rawRecords.length === 0 || !activeRecord) pendingAttrs.record_date = lineDateParsed;
                    continue;
                }

                if (line.includes('完畢') || masterNames.some(m => line === m || line === m + '仙師')) {
                    activeRecord = null;
                    continue;
                }

                let treasureName = '';
                let recipients = [];
                let lineDate = blockDate;
                let lineRemarks = '';
                let lineObtainedDate = null;

                // 2. Attribute Detection
                const attrKeywords = ['用意', '狀態', '備註', '求寶方式', '求寶', '由來', '得知日期', '登記日期', '求得日期', '日期'];
                const firstWord = line.split(/[\s：:]/)[0];
                if (attrKeywords.includes(firstWord)) {
                    const val = line.replace(new RegExp(`^${firstWord}[\\s：:]*`), '').trim();
                    const target = activeRecord || pendingAttrs;

                    if (firstWord === '用意') target.purpose = (target.purpose ? target.purpose + '；' : '') + val;
                    else if (firstWord === '狀態') {
                        if (val.includes('已登記')) {
                            if (target === pendingAttrs) { target.status = '已登記'; target.obtained_date = target.record_date || blockDate; }
                            else { target.obtained_date = target.record_date || blockDate; }
                        }
                    }
                    else if (['得知日期', '登記日期', '求得日期', '日期'].includes(firstWord)) {
                        const d = parseDateStr(val, currentContextYear) || val;
                        target.record_date = d;
                        if (firstWord === '登記日期' || firstWord === '求得日期') target.obtained_date = d;
                    }
                    else if (firstWord === '求寶方式' || firstWord === '求寶') target.acquisition_method = val;
                    else if (firstWord === '備註') {
                        // Check if this remark is specific to a person (e.g. "赤覺: ...")
                        const nameMatch = val.match(/^([^：:]+)[：:](.*)/);
                        if (nameMatch && activeRecord && activeRecord.dharma_name_registries) {
                            const pName = nameMatch[1].trim();
                            const pContent = nameMatch[2].trim();
                            const person = activeRecord.dharma_name_registries.find(r => r.custom_name === pName);
                            if (person) {
                                person.remarks = (person.remarks ? person.remarks + '\n' : '') + pContent;
                            } else {
                                target.remarks = (target.remarks ? target.remarks + '\n' : '') + val;
                            }
                        } else {
                            target.remarks = (target.remarks ? target.remarks + '\n' : '') + val;
                        }
                    }
                    continue;
                }

                // 3. Main Parsing logic
                // Extract date from start of line if present
                const lineStartDateMatch = line.match(/^(\d{1,4}[\/.-]\d{1,2}[\/.-]\d{1,2}|\d{1,2}[\/.-]\d{1,2})\s+/);
                if (lineStartDateMatch) {
                    const parsed = parseDateStr(lineStartDateMatch[1], currentContextYear);
                    if (parsed) lineDate = parsed;
                    else lineDate = lineStartDateMatch[1].replace(/\//g, '-');

                    if (!blockDate || blockDate === getTodayStr()) {
                        blockDate = lineDate;
                    }
                    line = line.replace(lineStartDateMatch[0], '').trim();
                }

                const kwMatch = line.match(/^\s*((允求|賜降|得知|賜予|賜|法寶名稱|法寶內容|求得)\s*)?(.*?)[：:](.*)/);
                if (kwMatch && kwMatch[3] && kwMatch[3].trim() && !attrKeywords.includes(kwMatch[3].trim())) {
                    let rawName = kwMatch[3].trim();
                    // Strip common prefixes from name if they were accidentally caught
                    rawName = rawName.replace(/^(允求|賜降|得知|賜予|賜|求得)\s*/, '');
                    treasureName = rawName;

                    const content = kwMatch[4].trim();
                    if (content) {
                        // Robust splitting including multiple delimiters
                        recipients = content.split(/[，、,\s\t]+/).map(n => n.trim()).filter(n => n);
                        if (content.includes('已登記')) lineObtainedDate = lineDate;
                    } else {
                        pendingTreasureName = treasureName;
                        continue;
                    }
                } 
                else if (line.match(/^(允求|賜降|得知|賜予|賜|求得)\s+/)) {
                    // Handle lines like "允求 森羅戒 金了、閻爵"
                    const parts = line.split(/\s+/);
                    treasureName = parts[1];
                    recipients = parts.slice(2).join(' ').split(/[，、,\s\t]+/).map(n => n.trim()).filter(n => n);
                    if (line.includes('已登記')) lineObtainedDate = lineDate;
                }
                else if (line.includes('求得')) {
                    const parts = line.split('求得').map(p => p.trim());
                    if (parts.length >= 2) {
                        recipients = [parts[0]];
                        treasureName = parts[1];
                        lineObtainedDate = lineDate;
                    }
                }
                else if (line.includes('|') || line.includes('│') || line.includes('\t')) {
                    const parts = line.split(/[|│\t]/).map(p => p.trim());
                    treasureName = parts[0];
                    if (parts[1]) recipients = [parts[1]];
                    if (parts[2]) {
                        const parsed = parseDateStr(parts[2], currentContextYear);
                        if (parsed) { lineDate = parsed; lineObtainedDate = parsed; }
                        else { lineDate = parts[2].replace(/\//g,'-'); lineObtainedDate = lineDate; }
                    }
                    if (parts[3]) lineRemarks = parts[3];
                }
                else if (pendingTreasureName) {
                    treasureName = pendingTreasureName;
                    recipients = line.split(/[，、, \s]+/).filter(n => n.trim());
                    pendingTreasureName = '';
                }
                else if (line.includes(' ') && !line.includes('：') && !line.includes(':')) {
                    const spaceParts = line.split(/\s+/);
                    const hasSeparators = line.includes('，') || line.includes('、') || line.includes(',') || line.includes('.');
                    if (spaceParts.length >= 2 && (spaceParts[1].length <= 20 || hasSeparators)) {
                        treasureName = spaceParts[0];
                        recipients = spaceParts.slice(1).join(' ').split(/[，、,\s\t]+/).map(n => n.trim()).filter(n => n);
                    } else {
                        treasureName = line.trim();
                    }
                }
                else if (line.length > 0 && line.length < 50) {
                    treasureName = line;
                }

                if (treasureName) {
                    treasureName = treasureName.replace(/[：:。！？]$/, '').trim();
                    const dnr = recipients.map(rawName => {
                        let cleanRaw = rawName.trim();
                        // 處理括號規則：若有括號，優先提取內容
                        let parenMatch = cleanRaw.match(/^(.*?)\((.*?)\)$/);
                        let nameForProcessing = cleanRaw;
                        let extraRemarkFromParen = '';

                        if (parenMatch) {
                            if (parenMatch[1].trim()) {
                                nameForProcessing = parenMatch[1].trim();
                                extraRemarkFromParen = parenMatch[2].trim();
                            } else {
                                nameForProcessing = parenMatch[2].trim();
                            }
                        }

                        let translated = translateName(nameForProcessing).trim();
                        if (!translated) return null;

                        let relMatch = translated.match(/^(.*?)([之的].+)$/);
                        
                        // Strict relationship detection for names starting with a Dharma Name
                        if (!relMatch) {
                            const dnsList = dharmaNames.value || [];
                            const sortedDns = [...dnsList].sort((a, b) => (b.name?.length || 0) - (a.name?.length || 0));
                            const recognizedRels = ['母', '父', '夫', '嬤', '婆', '公', '先生', '太太', '母親', '父親', '奶奶', '爺爺', '外公', '外婆'];
                            
                            const matchedDn = sortedDns.find(dn => {
                                if (dn.name && translated.startsWith(dn.name) && translated.length > dn.name.length) {
                                    const suffix = translated.substring(dn.name.length);
                                    const cleanSuffix = suffix.replace(/^[之的]/, '').trim();
                                    return recognizedRels.includes(cleanSuffix);
                                }
                                return false;
                            });

                            if (matchedDn) {
                                const relPart = translated.substring(matchedDn.name.length);
                                relMatch = [translated, matchedDn.name, relPart];
                            }
                        }

                        if (relMatch) {
                            const rel = relMatch[2].replace(/^[之的]/, '').trim();
                            const recognized = ['母', '父', '夫', '嬤', '婆', '公', '先生', '太太', '母親', '父親', '奶奶', '爺爺', '外公', '外婆'];
                            if (!recognized.includes(rel)) {
                                relMatch = null; 
                            }
                        }

                        const finalDate = lineDate || blockDate || getTodayStr();
                        const dParts = finalDate.split('-');
                        const dStr = `${dParts[0]}/${dParts[1].padStart(2,'0')}/${dParts[2].padStart(2,'0')}`;

                        if (relMatch) {
                            const translateRel = (rel) => {
                                if (!rel) return rel;
                                const r = rel.trim();
                                if (r === '之父' || r === '父') return '父親';
                                if (r === '之母' || r === '母') return '母親';
                                if (r === '之嬤' || r === '嬤') return '奶奶';
                                if (r === '之夫' || r === '夫') return '先生';
                                return r.replace(/^[之的]/, '');
                            };

                            const nameOnly = relMatch[1].trim();
                            const relPart = translateRel(relMatch[2]);
                            // 格式：日期  法號關係詞
                            let relEntry = `${dStr}  ${nameOnly}${relPart}`;
                            if (extraRemarkFromParen) relEntry += ` (${extraRemarkFromParen})`;
                            return { custom_name: nameOnly, remarks: relEntry, obtained_date: null };
                        }

                        let finalRemarks = lineRemarks;
                        if (extraRemarkFromParen) {
                            finalRemarks = (finalRemarks ? finalRemarks + '\n' : '') + extraRemarkFromParen;
                        }
                        return { custom_name: translated, remarks: finalRemarks, obtained_date: lineObtainedDate || lineDate || blockDate };
                    }).filter(n => n !== null);

                    const recipientsList = dnr.filter(n => n !== null);
                    recipientsList.forEach(onePersonDnr => {
                        const newRec = {
                            name: treasureName, 
                            master_id: blockMasterId,
                            category: batchData.category || currentCategory.value || 'major',
                            record_date: lineDate || blockDate, 
                            obtained_date: lineObtainedDate,
                            acquisition_method: batchData.defaults?.acquisition_method || '',
                            purpose: batchData.defaults?.purpose || '',
                            remarks: batchData.defaults?.remarks || '',
                            dharma_name_registries: [onePersonDnr]
                        };

                        // Apply pending attributes
                        if (pendingAttrs.purpose) newRec.purpose = pendingAttrs.purpose;
                        if (pendingAttrs.remarks) newRec.remarks = pendingAttrs.remarks;
                        if (pendingAttrs.record_date) newRec.record_date = pendingAttrs.record_date;
                        if (pendingAttrs.obtained_date) newRec.obtained_date = pendingAttrs.obtained_date;
                        if (pendingAttrs.acquisition_method) newRec.acquisition_method = pendingAttrs.acquisition_method;

                        rawRecords.push(newRec);
                        activeRecord = newRec; // Set context to the last one for subsequent attributes
                    });

                    // Reset pending
                    pendingAttrs = { purpose: '', remarks: '', status: '', record_date: null, obtained_date: null, acquisition_method: '' };
                }
            }
        });

        // --- MERGE STAGE (Simplified: No grouping by treasure name to keep people separate) ---
        const records = rawRecords; 
        if (records.length === 0) {
            persistentToast.value = { msg: '✖ 無法解析出任何法寶資料', type: 'error' };
            isSaving.value = false;
            return;
        }

        const res = await axios.post('/registries/batch', records);
        const savedCount = res.data?.count ?? records.length;
        addMode.value = null;
        expandedIds.value.clear();
        focusedId.value = null;
        loadData();
        persistentToast.value = { msg: `✓ 批量新增成功 (${savedCount} 筆)`, type: 'success' };
        setTimeout(() => { if (persistentToast.value?.type === 'success') persistentToast.value = null; }, 2000);
    } catch (e) {
        console.error(e);
        const errorMsg = e.response?.data?.error || '批量儲存失敗';
        persistentToast.value = { msg: '✖ ' + errorMsg, type: 'error' };
    } finally {
        isSaving.value = false;
    }
};



const triggerRemarksEdit = (item, personIdentifier) => {
    const dnr = typeof personIdentifier === 'object' 
        ? personIdentifier 
        : (item.dharma_name_registries || []).find(r => r.dharma_name_id === personIdentifier);

    if (dnr) {
        openRemarks(dnr);
    }
    showRemarksModal.value = true;
};







const openRemarks = (dnrOrContent) => {
    if (typeof dnrOrContent === 'string') {
        activeRemarks.value = dnrOrContent;
        currentDnrId.value = null;
    } else {
        const r = dnrOrContent.remarks;
        activeRemarks.value = Array.isArray(r) ? r.join('\n') : (r || '');
        currentDnrId.value = dnrOrContent.id;
    }
    showRemarksModal.value = true;
};

const handleAddFormRemarksEdit = ({ idx, remarks }) => {
    currentAddFormIdx.value = idx;
    currentDnrId.value = null;
    activeRemarks.value = remarks || '';
    showRemarksModal.value = true;
};

const openCombinedRemarks = (item) => {
    const combined = item.dharma_name_registries
        .filter(r => r.remarks && (Array.isArray(r.remarks) ? r.remarks.length > 0 : r.remarks))
        .map(r => {
            const name = r.dharma_name ? r.dharma_name.name : r.custom_name;
            const text = Array.isArray(r.remarks) ? r.remarks.join('\n  ') : r.remarks;
            return `【${name}】：${text}`;
        })
        .join('\n\n');
    openRemarks(combined);
};

const deleteItem = async (id) => {
    try {
        await axios.delete(`/registries/${id}`);
        persistentToast.value = { msg: '✓ 已成功刪除資料', type: 'success' };
        setTimeout(() => { persistentToast.value = null; }, 2000);

        // Reset states
        deleteConfirmId.value = null;
        focusedId.value = null;
        expandedIds.value = new Set();
        openMenuId.value = null;

        await loadData();
    } catch (e) {
        console.error(e);
        const errorMsg = e.response?.data?.error || '刪除失敗';
        persistentToast.value = { msg: '✖ ' + errorMsg, type: 'error' };
        setTimeout(() => { persistentToast.value = null; }, 2000);
    }
};

const executeDelete = () => {
    if (deleteConfirmId.value) {
        deleteItem(deleteConfirmId.value);
    }
};

const toggleMenu = (id) => openMenuId.value = openMenuId.value === id ? null : id;
const toggleSort = () => sortDesc.value = !sortDesc.value;

const handleReorder = async (item, newOrder) => {
    const targetOrder = parseInt(newOrder);
    if (isNaN(targetOrder)) return;

    const list = [...filteredTreasures.value];
    const oldIndex = list.findIndex(r => r.id === item.id);
    if (oldIndex === -1) return;

    // Remove from old position
    const [movedItem] = list.splice(oldIndex, 1);
    // Insert at new position (1-based index)
    list.splice(Math.max(0, targetOrder - 1), 0, movedItem);

    // Update sort_order for all in the FULL list
    const orders = list.map((r, idx) => ({
        id: r.id,
        sort_order: idx + 1
    }));

    // Update local state immediately
    list.forEach((r, idx) => {
        const orig = allTreasures.value.find(v => v.id === r.id);
        if (orig) orig.sort_order = idx + 1;
    });

    try {
        await axios.post('/registries/reorder', { orders });
        // Refresh to ensure server-side sorting is consistent
        await loadData();
    } catch (err) {
        console.error('Reorder failed:', err);
    }
};

const moveRegistryItem = async (item, direction) => {
    const list = filteredTreasures.value;
    const index = list.findIndex(t => t.id === item.id);
    const targetIdx = index + direction;
    if (targetIdx < 0 || targetIdx >= list.length) return;
    await handleReorder(item, targetIdx + 1);
};

const formatDate = (d) => {
    if (!d) return '-';
    // Use string splitting instead of new Date() to avoid timezone-related day shifts
    const s = String(d).trim();
    const parts = s.split(/[-/]/);
    if (parts.length === 3) {
        let y = parts[0];
        let m = parts[1].padStart(2, '0');
        let d_val = parts[2].padStart(2, '0');
        if (!isNaN(parseInt(y)) && !isNaN(parseInt(m)) && !isNaN(parseInt(d_val))) {
            return `${y}/${m}/${d_val}`;
        }
    }
    return s.replace(/-/g, '/');
};

const copyToLine = (item) => {
    // Collect records from either the saved state or the active edit state
    const currentRegistries = item.dharma_name_registries || [];
    const dnrText = currentRegistries
        .filter(r => r.obtained_date)
        .map(r => {
            const dn = dharmaNames.value.find(d => d.id === r.dharma_name_id);
            const name = dn?.name || r.custom_name || '未知人員';
            return `● ${name} | ${formatToROC(r.obtained_date)}${r.remarks ? ' | ' + r.remarks : ''}`;
        }).join('\n');

    const finalRecordsText = dnrText || '尚無紀錄';
    const text = `【${item.name}】\n用意：${item.purpose || '-'}\n登記狀況：\n${finalRecordsText}`;
    writeClipboard(text).then(success => {
        if (success) {
            persistentToast.value = { msg: '✓ 已複製貼 LINE 格式', type: 'success' };
            setTimeout(() => { if (persistentToast.value?.type === 'success') persistentToast.value = null; }, 1500);
        }
    });
    openMenuId.value = null;
};

const downloadItemData = (item) => {
    const headers = ['法號', '登記日期', '備註'];
    const rows = dharmaNames.value.map(dn => {
        const r = item.dharma_name_registries?.find(reg => reg.dharma_name_id === dn.id);
        return [dn.name, formatToROC(r?.obtained_date) || '', r?.remarks || ''].join(',');
    });

    // Add UTF-8 BOM to prevent garbled text (亂碼) in Excel
    const csvContent = "\ufeff" + [headers.join(','), ...rows].join('\n');
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement("a");
    const url = URL.createObjectURL(blob);
    link.setAttribute("href", url);
    link.setAttribute("download", `${item.name}_登記表.txt`);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    openMenuId.value = null;
};

const copyAllToLine = () => {
    const items = filteredTreasures.value;
    if (!items.length) {
        persistentToast.value = { msg: '✖ 尚無資料可複製', type: 'error' };
        return;
    }

    const text = items.map(item => {
        const dnrText = item.dharma_name_registries?.filter(r => r.obtained_date).map(r => {
            const dn = dharmaNames.value.find(d => d.id === r.dharma_name_id);
            const name = dn?.name || r.custom_name || '未知人員';
            return `● ${name} | ${formatToROC(r.obtained_date)}${r.remarks ? ' | ' + r.remarks : ''}`;
        }).join('\n') || '尚無紀錄';
        return `【${item.name}】\n用意：${item.purpose || '-'}\n內容：${item.content || '-'}\n登記狀況：\n${dnrText}`;
    }).join('\n\n------------------\n\n');

    navigator.clipboard.writeText(text);
    persistentToast.value = { msg: '✓ 已複製全部資料至 LINE', type: 'success' };
    setTimeout(() => persistentToast.value = null, 2000);
};

const downloadAllData = () => {
    const items = filteredTreasures.value;
    if (!items.length) {
        persistentToast.value = { msg: '✖ 尚無資料可下載', type: 'error' };
        return;
    }

    const contents = items.map(item => {
        const dnrText = item.dharma_name_registries?.filter(r => r.obtained_date).map(r => {
            const dn = dharmaNames.value.find(d => d.id === r.dharma_name_id);
            const name = dn?.name || r.custom_name || '未知人員';
            return `● ${name} | ${formatToROC(r.obtained_date)}${r.remarks ? ' | ' + r.remarks : ''}`;
        }).join('\r\n') || '尚無紀錄';

        return `【${item.name}】\r\n用意：${item.purpose || '-'}\r\n內容：${item.content || '-'}\r\n登記狀況：\r\n${dnrText}`;
    }).join('\r\n\r\n--------------------------------\r\n\r\n');

    const finalContent = `【特殊法寶登記區 - ${currentFolder.value.name} 完整清單】\r\n\r\n${contents}`;
    const blob = new Blob(["\ufeff" + finalContent], { type: 'text/plain;charset=utf-8;' });
    const link = document.createElement("a");
    const url = URL.createObjectURL(blob);
    link.setAttribute("href", url);
    link.setAttribute("download", `特殊法寶登記區_${currentFolder.value.name}_完整清單.txt`);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
};


const handleRemarksViewerSave = async ({ dnrId, content }) => {
    if (currentAddFormIdx.value !== null) {
        if (addFormRef.value) {
            addFormRef.value.updatePersonnelRemarks(currentAddFormIdx.value, content);
        }
        showRemarksModal.value = false;
        currentAddFormIdx.value = null;
        return;
    }



    if (!dnrId) return;
    isSaving.value = true;
    try {
        await axios.patch(`/registries/personnel/${dnrId}/remarks`, {
            remarks: content
        });
        persistentToast.value = { msg: '✓ 備註已儲存', type: 'success' };
        setTimeout(() => { if (persistentToast.value?.type === 'success') persistentToast.value = null; }, 1500);
        await loadData();
    } catch (e) {
        persistentToast.value = { msg: '✖ 儲存失敗，請重試', type: 'error' };
    } finally {
        isSaving.value = false;
        showRemarksModal.value = false; // 確保關閉
        currentDnrId.value = null;     // 重置 ID
    }
};

const saveRemarksInline = async () => {
    if (!currentDnrId.value) return;
    isSaving.value = true;
    try {
        await axios.patch(`/registries/personnel/${currentDnrId.value}/remarks`, {
            remarks: activeRemarks.value
        });
        // 先關閉 Modal，再更新資料，避免 watch 更新干擾顯示中的內容
        showRemarksModal.value = false;
        currentDnrId.value = null;
        persistentToast.value = { msg: '✓ 備註已儲存', type: 'success' };
        setTimeout(() => { if (persistentToast.value?.type === 'success') persistentToast.value = null; }, 1500);
        await loadData();
    } catch (e) {
        persistentToast.value = { msg: '✖ 儲存失敗，請重試', type: 'error' };
    } finally {
        isSaving.value = false;
    }
};

const getFolderSum = (id) => {
    return folderCounts.value[id] || 0;
};
const getCategorySum = (category) => {
    return categoryCounts.value[category] || 0;
};
// C6: Body scroll lock for iOS compatibility
const isAnyModalOpen = computed(() => {
    return !!addMode.value || 
           !!persistentToast.value || 
           !!deleteConfirmId.value || 
           !!showRemarksModal.value || 
           !!showLuckyDraw.value || 
           !!showAddMenu.value;
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
.animate-fade-in { animation: fadeIn 0.1s ease-out; }
@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }

.animate-spin-slow {
  animation: spin-slow 1.5s linear infinite;
}
@keyframes spin-slow {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}

.custom-scrollbar { -webkit-overflow-scrolling: touch; overscroll-behavior-y: contain; }
.custom-scrollbar::-webkit-scrollbar { width: 5px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
* { -webkit-tap-highlight-color: transparent; }

/* Locked Font Utility for Administrative Consistency */
.font-biaokai-locked {
    font-family: 'DFKai-SB', '標楷體', 'BiauKai', 'Kaiti TC', serif !important;
}
</style>