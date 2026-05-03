<template>
    <div v-if="mode" class="fixed inset-0 z-[3500] flex items-end md:items-center justify-center px-0">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm" @click="$emit('cancel')"></div>
        
        <!-- Form Container -->
        <div class="relative w-full h-full md:h-auto md:max-h-[95vh] md:max-w-xl bg-white md:rounded-[32px] shadow-[0_-10px_40px_rgba(0,0,0,0.1)] overflow-hidden animate-slide-up flex flex-col pb-[7vh]">
            
            <!-- Header -->
            <div class="px-[10px] py-[12px] flex items-center bg-white border-b border-slate-50 relative">
                <div class="flex-1 flex flex-col justify-center min-w-0">
                    <div class="text-[22px] font-black leading-none font-outfit uppercase tracking-wider text-red-600">法寶登記專區</div>
                    <div class="text-[1.4rem] font-bold mt-2 font-outfit text-slate-900 leading-tight break-words" style="font-size: 1.4rem !important;">
                        {{ (form.category === 'major' ? '重大皇恩登記簿' : '其他皇恩登記簿') }} - {{ selectedMasterName || '請選擇仙師' }}
                    </div>
                </div>
                <button @click="$emit('cancel')" class="text-slate-300 hover:text-slate-600 transition-colors p-2 absolute right-4 top-1/2 -translate-y-1/2">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
            </div>

            <!-- Tab Selection -->
            <div class="px-6 pt-4 flex space-x-1">
                <button @click="localMode = 'single'" :class="[localMode === 'single' ? 'bg-blue-600 text-white' : 'text-slate-400 hover:bg-slate-50']" :style="localMode === 'single' ? 'color: white !important;' : ''" class="flex-1 py-[10px] rounded-xl text-[16px] font-bold transition-all whitespace-nowrap">逐筆登錄</button>
                <button @click="localMode = 'batch'" :class="[localMode === 'batch' ? 'bg-blue-600 text-white' : 'text-slate-400 hover:bg-slate-50']" :style="localMode === 'batch' ? 'color: white !important;' : ''" class="flex-1 py-[10px] rounded-xl text-[16px] font-bold transition-all whitespace-nowrap">多筆載錄</button>
            </div>

            <!-- Scrollable Content -->
            <div class="flex-1 min-h-0 overflow-y-auto px-[10px] py-[20px] space-y-[20px] custom-scrollbar bg-white">
                
                <!-- Date & Master Selection (Grid Layout as per Screenshot) -->
                <div class="grid gap-4" :class="localMode === 'single' ? 'grid-cols-2' : 'grid-cols-1'">
                    <div v-if="localMode === 'single'" class="space-y-1.5">
                        <label class="text-[17px] font-bold text-slate-800 block ml-1">得知日期</label>
                        <div class="relative flex items-center">
                            <input v-model="form.record_date" type="text" placeholder="年/月/日 或 註記文字" 
                                class="w-full py-[10px] rounded-2xl border border-slate-400 bg-white pl-3 pr-8 focus:ring-2 focus:ring-indigo-100 outline-none text-[15px] font-bold text-slate-900 shadow-sm">
                            <button @click="activePicker = { idx: 'main', field: 'record_date', title: '選擇日期' }" class="absolute right-3 text-slate-400 hover:text-indigo-600 transition-all p-1">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </button>
                        </div>
                    </div>
                    <div class="space-y-1.5 relative">
                        <label class="text-[17px] font-bold text-slate-800 block ml-1">載錄目標仙師</label>
                        <!-- Custom Dropdown Button -->
                        <div class="relative">
                            <button @click="showMasterDropdown = true" 
                                class="w-full py-[12px] rounded-2xl border border-slate-400 bg-white px-4 text-[17px] font-bold text-slate-900 focus:ring-2 focus:ring-indigo-100 outline-none flex items-center justify-between active:bg-slate-50 transition-all shadow-sm">
                                <span :class="form.master_id ? 'text-slate-900' : 'text-slate-400'">{{ selectedMasterName || '請選擇仙師' }}</span>
                                <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M19 9l-7 7-7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>

                            <!-- Bottom Sheet Picker for Masters -->
                            <teleport to="body">
                                <div v-if="showMasterDropdown" class="fixed inset-0 z-[5000] flex items-end justify-center">
                                    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-[2px] transition-opacity" @click="showMasterDropdown = false"></div>
                                    <div class="relative w-full max-w-xl bg-white rounded-t-[32px] shadow-2xl overflow-hidden animate-slide-up flex flex-col max-h-[80vh]">
                                        <div class="px-6 py-5 border-b border-slate-50 flex items-center justify-between bg-white sticky top-0 z-10">
                                            <span class="text-[20px] font-black text-slate-900">選擇載錄目標仙師</span>
                                            <button @click="showMasterDropdown = false" class="p-2 text-slate-300 hover:text-slate-600 transition-colors">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                            </button>
                                        </div>
                                        <div class="flex-1 overflow-y-auto custom-scrollbar p-3 space-y-2 pb-10">
                                            <button v-for="m in masters" :key="m.id"
                                                @click="form.master_id = m.id; showMasterDropdown = false"
                                                class="w-full p-5 text-left rounded-2xl transition-all flex items-center justify-between border-2"
                                                :class="form.master_id === m.id ? 'bg-blue-50 border-blue-200 text-blue-700' : 'bg-slate-50/50 border-transparent text-slate-700 hover:bg-slate-100'">
                                                <span class="text-[18px] font-bold">{{ m.name === '父皇仙師' ? '父皇' : m.name }}</span>
                                                <div v-if="form.master_id === m.id" class="w-6 h-6 bg-blue-600 rounded-full flex items-center justify-center text-white">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                                </div>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </teleport>
                        </div>
                    </div>
                </div>

                <!-- Treasure Names (Single Mode) -->
                <div v-if="localMode === 'single'" class="space-y-2">
                    <div class="flex items-center justify-between ml-1">
                        <label class="text-[15px] font-bold text-red-600 uppercase tracking-wider">法寶名稱 (支援多筆貼入)</label>
                    </div>
                    <textarea v-model="treasureNamesText" rows="3" 
                        class="w-full py-[10px] rounded-2xl border border-slate-400 bg-white px-4 text-[17px] font-bold text-slate-900 focus:ring-2 focus:ring-indigo-100 outline-none"
                        placeholder="輸入法寶名稱，可用空格或換行分隔多筆..."></textarea>
                </div>

                <!-- Single Mode Fields -->
                <div v-if="localMode === 'single'" class="space-y-5 animate-fade-in">
                    <!-- Purpose & Method Grid -->
                    <div class="grid grid-cols-1 gap-4">
                        <div class="space-y-1.5">
                            <label class="text-[15px] font-bold text-slate-500 block ml-1 uppercase tracking-wider">法寶用意</label>
                            <textarea v-model="form.purpose" rows="2" placeholder="預設用意" class="w-full py-[10px] rounded-2xl border border-slate-400 bg-white px-4 text-[17px] font-bold text-red-600 focus:ring-2 focus:ring-indigo-100 outline-none custom-scrollbar"></textarea>
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[15px] font-bold text-slate-500 block ml-1 uppercase tracking-wider">求寶方式</label>
                            <textarea v-model="form.acquisition_method" rows="2" placeholder="預設方式" class="w-full py-[10px] rounded-2xl border border-slate-400 bg-white px-4 text-[17px] font-bold text-red-600 focus:ring-2 focus:ring-indigo-100 outline-none custom-scrollbar"></textarea>
                        </div>
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-[15px] font-bold text-slate-500 block ml-1 uppercase tracking-wider">備註 (整體)</label>
                        <input v-model="form.remarks" type="text" placeholder="輸入備註 (選填)" class="w-full py-[10px] rounded-2xl border border-slate-400 bg-white px-4 text-[17px] font-bold text-slate-900 focus:ring-2 focus:ring-indigo-100 outline-none">
                    </div>
                </div>

                <!-- Personnel List (Single Mode) -->
                <div v-if="localMode === 'single'" class="space-y-6">
                        <div class="flex items-center justify-between ml-1">
                            <label class="text-[15px] font-bold text-red-600 uppercase tracking-tight">承接皇恩的師兄姐</label>
                            <button @click="addPersonnelRow" class="px-3 py-1 bg-indigo-600 text-white rounded-lg text-[12px] font-bold active:scale-95 transition-all shadow-sm" style="color: white !important;">
                                ＋ 新增人員
                            </button>
                        </div>

                        <div v-for="(p, idx) in personnel" :key="idx" class="p-4 bg-slate-50/30 rounded-2xl border border-slate-400 space-y-3 relative group animate-fade-in">
                            <div class="absolute top-2 right-2 flex items-center space-x-1">
                                <!-- Reorder Buttons -->
                                <button v-if="personnel.length > 1" @click="movePersonnel(idx, -1)" :disabled="idx === 0" class="p-1 text-slate-300 hover:text-indigo-500 disabled:opacity-10 transition-all active:scale-90">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 15l7-7 7 7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </button>
                                <button v-if="personnel.length > 1" @click="movePersonnel(idx, 1)" :disabled="idx === personnel.length - 1" class="p-1 text-slate-300 hover:text-indigo-500 disabled:opacity-10 transition-all active:scale-90">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </button>
                                <button @click="removePersonnelRow(idx)" class="ml-1 text-slate-300 hover:text-red-500 transition-colors p-1">✕</button>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-x-2 gap-y-3">
                                <div class="space-y-1">
                                    <label class="text-[11px] text-red-400 ml-1 font-bold">法號/群組</label>
                                    <input v-model="p.custom_name" type="text" placeholder="法號/群組" list="dharma-names"
                                        @keydown.enter.prevent="handlePersonnelEnter(idx)"
                                        @input="e => handlePersonnelNameInput(idx, e)"
                                        class="personnel-name-input w-full py-[10px] rounded-xl border border-slate-400 bg-white px-3 text-[18px] font-bold text-slate-900 focus:ring-2 focus:ring-indigo-100 outline-none font-outfit">
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[11px] text-red-400 ml-1 font-bold">備註對象</label>
                                    <div class="relative flex items-center border border-slate-400 rounded-xl bg-white overflow-visible min-h-[46px]">
                                        <input v-model="p.relationship" type="text" placeholder="備註對象 (例如：母親)" 
                                            @focus="activeRelationshipDropdownIdx = idx"
                                            class="w-full bg-transparent border-none px-3 text-[16px] font-normal text-slate-900 focus:ring-0 outline-none">
                                        <button @click.stop="activeRelationshipDropdownIdx = (activeRelationshipDropdownIdx === idx ? null : idx)" class="p-2 mr-1 text-indigo-500 hover:text-indigo-700 transition-all active:scale-90">
                                            <svg class="w-6 h-6" :class="activeRelationshipDropdownIdx === idx ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        </button>
                                        <div v-if="activeRelationshipDropdownIdx === idx" class="absolute left-0 top-full mt-1 w-full bg-white rounded-2xl shadow-[0_15px_40px_rgba(0,0,0,0.15)] border border-slate-100 z-[610] overflow-hidden p-1.5 animate-fade-in max-h-[250px] overflow-y-auto custom-scrollbar">
                                            <div v-for="opt in relationshipOptions" :key="opt"
                                                 @click.stop="p.relationship = opt; activeRelationshipDropdownIdx = null"
                                                 class="px-4 h-[36px] flex items-center rounded-xl hover:bg-indigo-50 font-bold text-[15px] text-slate-900 active:bg-indigo-100 transition-all cursor-pointer">
                                                {{ opt }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[11px] text-red-400 ml-1 font-bold">日期</label>
                                    <div class="relative w-full py-[10px] rounded-xl border border-slate-400 bg-white flex items-center overflow-hidden group/date">
                                        <input 
                                            :value="p.obtained_date ? p.obtained_date.replace(/-/g, '/') : ''"
                                            @input="e => handlePersonnelDateInput(idx, e)"
                                            placeholder="年/月/日"
                                            class="personnel-date-input w-full h-full bg-transparent px-2 text-[14px] font-bold font-outfit text-slate-900 outline-none uppercase"
                                        >
                                        <button @click="activePicker = { idx, field: 'obtained_date', title: (p.custom_name || '師兄姐') + '取得日期' }" 
                                            class="absolute right-0 top-0 h-full px-2 text-slate-300 hover:text-indigo-500 transition-colors bg-white/80 backdrop-blur-sm">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        </button>
                                    </div>
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[11px] text-slate-400 ml-1 font-bold">個人備註</label>
                                    <div @click="$emit('openRemarksEdit', { idx, remarks: p.remarks })"
                                        class="w-full h-[46px] rounded-xl border border-slate-400 bg-white px-3 py-[10px] text-[15px] font-normal text-slate-900 cursor-pointer hover:border-indigo-300 transition-colors flex items-center justify-between group/rem">
                                        <span :class="p.remarks ? 'text-slate-900' : 'text-slate-300'" class="truncate flex-1 text-[13px]">
                                            {{ p.remarks || '備註...' }}
                                        </span>
                                        <svg class="w-4 h-4 text-slate-200 group-hover/rem:text-indigo-400 transition-colors shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div v-if="personnel.length === 0" class="text-center py-6 bg-slate-50/30 rounded-2xl border border-dashed border-slate-400 text-slate-300 text-[13px]">
                            尚無人員紀錄
                        </div>

                        <!-- Secondary Add Button at the Bottom -->
                        <button @click="addPersonRow" 
                            class="w-full py-4 mt-2 rounded-2xl border-2 border-dashed border-indigo-100 bg-indigo-50/30 flex items-center justify-center space-x-2 text-indigo-500 hover:bg-indigo-50 hover:border-indigo-200 active:scale-[0.98] transition-all group">
                            <div class="w-8 h-8 rounded-full bg-white shadow-sm flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </div>
                            <span class="text-[16px] font-black">新增人員</span>
                        </button>

                        <!-- Palace Logic Notice & Table -->
                        <div v-if="isPalaceMode" class="mt-6 space-y-4">
                            <div v-if="!hasPalaceName" class="p-6 bg-red-50 border-2 border-red-100 rounded-[24px] text-center space-y-2 animate-fade-in">
                                <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center mx-auto mb-2 shadow-sm">
                                    <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </div>
                                <div class="text-[17px] font-black text-red-600">若是各宮載錄</div>
                                <div class="text-[15px] font-bold text-red-400 leading-relaxed">請至少於上方輸入一個宮名<br>（如：玄通宮）<br>系統將自動開啟各宮列表</div>
                            </div>

                            <div v-if="hasPalaceName" class="overflow-x-auto rounded-[24px] border border-slate-100 shadow-sm bg-white animate-fade-in">
                                <table class="w-full border-collapse bg-white text-[16px]">
                                    <thead>
                                        <tr class="bg-indigo-50/50 text-slate-700 font-outfit">
                                            <th class="border-b border-slate-100 px-3 py-3 text-left font-black w-[80px]">宮名</th>
                                            <th class="border-b border-slate-100 px-2 py-3 text-center font-black">取得日期</th>
                                            <th class="border-b border-slate-100 px-3 py-3 text-center font-black w-[60px]">備註</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="pName in palaceSortOrder" :key="pName" class="hover:bg-slate-50 transition-colors">
                                            <td class="border-b border-slate-50 px-3 py-3 font-black text-black whitespace-nowrap bg-slate-50/20 font-outfit">{{ pName }}</td>
                                            <td class="border-b border-slate-50 p-1 text-black">
                                                <div class="flex items-center px-1 py-1 justify-center relative">
                                                    <input v-model="palaceData[pName].obtained_date" 
                                                        type="text"
                                                        placeholder="日期"
                                                        class="w-full bg-white border border-slate-300 rounded-lg px-2 py-1.5 text-[14px] font-black font-outfit text-center focus:ring-2 focus:ring-indigo-100 outline-none"
                                                        style="color: rgb(220, 20, 40) !important;">
                                                    <button @click.stop="activePicker = { idx: pName, field: 'obtained_date', title: pName + '取得日期' }"
                                                        class="absolute right-2 text-slate-300 hover:text-indigo-600 p-0.5">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                                    </button>
                                                </div>
                                            </td>
                                            <td class="border-b border-slate-50 p-1 text-black">
                                                <div @click.stop="$emit('openRemarksEdit', { idx: pName, remarks: palaceData[pName].remarks })" 
                                                    class="w-full py-2 px-3 flex items-center justify-center cursor-pointer active:scale-90 transition-all">
                                                    <span v-if="palaceData[pName].remarks" class="text-[18px] text-amber-500 animate-pulse">●</span>
                                                    <span v-else class="text-slate-200">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" /></svg>
                                                    </span>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                <!-- Batch Mode Fields (Matching Mockup) -->
                <div v-if="localMode === 'batch'" class="space-y-4 animate-fade-in">
                    <div class="flex items-center justify-between ml-1">
                        <label class="text-[17px] font-bold text-slate-800">貼入法寶名稱明細</label>
                        <div class="flex items-center space-x-2">
                            <button v-if="batchInput" @click="batchInput = ''" class="px-3 py-1 bg-red-50 text-red-500 rounded-lg text-[13px] font-bold active:scale-95 transition-all flex items-center space-x-1 border border-red-100">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                <span>清空</span>
                            </button>
                        </div>
                    </div>
                    <textarea v-model="batchInput" rows="10" 
                        class="w-full rounded-2xl border border-slate-300 text-[16px] font-normal text-slate-900 bg-white focus:ring-2 focus:ring-indigo-100 p-4" 
                        placeholder="支援直接貼上或輸入 Excel 或 LINE 內容... 第一行若是日期將自動作為登記日期！"></textarea>
                    
                    <!-- Batch Preview Section -->
                    <div v-if="parsedItemsCount > 0" class="border border-indigo-100 rounded-[24px] overflow-hidden bg-white animate-fade-in">
                        <div class="bg-indigo-50/50 px-4 py-3 border-b border-indigo-100 flex justify-between items-center">
                            <div class="flex flex-col">
                                <span class="text-[15px] font-bold text-indigo-600">偵測到 {{ parsedItemsCount }} 筆資料</span>
                                <span class="text-[11px] text-indigo-400 font-medium">包含日期與法寶自動識別</span>
                            </div>
                        </div>

                        <!-- Preview Chips (Quick Overview) -->
                        <div class="px-4 py-2 bg-white flex flex-wrap gap-1.5 border-b border-indigo-50 max-h-[100px] overflow-y-auto no-scrollbar">
                            <span v-for="(item, idx) in previewItems" :key="idx" class="px-2 py-0.5 bg-slate-50 border border-slate-200 rounded-lg text-[12px] font-bold text-slate-600">
                                {{ item }}
                            </span>
                        </div>

                        <!-- Detailed Table Preview -->
                        <div class="max-h-48 overflow-y-auto custom-scrollbar no-scrollbar">
                            <table class="w-full text-[13px] text-left">
                                <thead class="bg-slate-50 text-slate-400 sticky top-0 uppercase tracking-tight border-b border-slate-100">
                                    <tr>
                                        <th class="p-2 border-b border-slate-100 font-bold">預計存入仙師</th>
                                        <th class="p-2 border-b border-slate-100 font-bold">法寶名稱</th>
                                        <th v-if="batchParsedRows.some(r => r.date)" class="p-2 border-b border-slate-100 font-bold">記載日期</th>
                                        <th class="p-2 border-b border-slate-100 font-bold">內容摘要</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item, idx) in batchParsedRows" :key="idx" class="border-b border-slate-50 last:border-0 hover:bg-slate-50 transition-colors">
                                        <td class="py-3 px-2 text-indigo-500 font-black text-[10px] whitespace-nowrap">
                                            {{ getMasterName(item.master_id) }}
                                        </td>
                                        <td class="py-3 px-2 text-slate-900 font-black truncate max-w-[100px]">{{ item.name }}</td>
                                        <td v-if="batchParsedRows.some(r => r.date)" class="py-3 px-2 text-slate-500 font-medium whitespace-nowrap">{{ item.date ? item.date.replace(/-/g, '/') : '-' }}</td>
                                        <td class="py-3 px-2 text-slate-500 font-medium truncate max-w-[150px]">{{ item.remarks || item.purpose || '-' }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>


                </div>
                <!-- Dharma Names & Palaces Auto-complete List -->
                <datalist id="dharma-names">
                    <option v-for="dn in dharmaNames" :key="dn.id" :value="dn.name" />
                    <option value="玄通宮" />
                    <option value="玄應宮" />
                    <option value="玄心宮" />
                    <option value="玄妙宮" />
                    <option value="玄昇宮" />
                    <option value="玄願宮" />
                    <option value="玄法宮" />
                    <option value="玄閻宮" />
                    <option value="玄窕宮" />
                    <option value="玄瑤宮" />
                    <option value="玄義宮" />
                </datalist>
            </div>

            <!-- Footer Action -->
            <div class="absolute bottom-[7vh] left-0 right-0 p-4 bg-white/95 backdrop-blur-md border-t border-slate-50 z-[10]">
                <button v-if="localMode === 'single'" @click="handleSubmit" :disabled="isSaving" class="w-full bg-blue-600 text-white rounded-2xl font-bold text-[20px] py-[10px]" style="color: white !important;">
                    {{ isSaving ? '儲存中...' : '儲存' }}
                </button>
                <button v-else @click="handleSubmit" :disabled="isSaving || parsedItemsCount === 0" 
                    class="w-full bg-blue-600 text-white rounded-2xl font-bold text-[20px] py-[10px] transition-all disabled:opacity-50" style="color: white !important;">
                    {{ isSaving ? '儲存中...' : `儲存這 ${parsedItemsCount} 筆資料` }}
                </button>
            </div>

            <!-- Global Mobile Navbar -->
            <mobile-navbar 
                :can-back="false"
                @home="$emit('cancel')"
                :show-action="false"
                :can-search="false"
                is-absolute
            />
        </div>

        <compact-date-picker 
            v-if="activePicker && activePicker.idx === 'main'"
            v-model="form[activePicker.field]"
            :title="activePicker.title"
            @close="activePicker = null"
        />
        <compact-date-picker 
            v-if="activePicker && typeof activePicker.idx === 'number'"
            v-model="personnel[activePicker.idx][activePicker.field]"
            :title="activePicker.title"
            @close="activePicker = null"
        />
        <compact-date-picker 
            v-if="activePicker && typeof activePicker.idx === 'string' && activePicker.idx !== 'main'"
            v-model="palaceData[activePicker.idx][activePicker.field]"
            :title="activePicker.title"
            @close="activePicker = null"
        />
    </div>
</template>

<script setup>
import { ref, watch, computed, onMounted } from 'vue';
import axios from 'axios';
import MobileNavbar from './MobileNavbar.vue';

const props = defineProps({
    mode: String,
    initialData: Object,
    masters: Array,
    isSaving: Boolean
});

const emit = defineEmits(['saveSingle', 'saveBatch', 'cancel', 'openRemarksEdit']);

import CompactDatePicker from './CompactDatePicker.vue';

const localMode = ref(props.mode || 'single');
const form = ref({ ...props.initialData });
const treasureNamesText = ref('');
const batchInput = ref('');
const showMasterDropdown = ref(false);
const personnel = ref([]);
const dharmaNames = ref([]);
const activePicker = ref(null); // { idx: number | 'main', field: string, title: string }
const activeRelationshipDropdownIdx = ref(null);
const relationshipOptions = ['母親', '父親', '公公', '婆婆', '爺爺', '奶奶', '外公', '外婆'];
const fileInput = ref(null);

const treasureNames = computed(() => {
    // 優先依換行切分，其次依兩個以上的空格或 Tab 切分，保留單個標點符號（如括號、逗號）作為名稱的一部分
    return treasureNamesText.value.split(/[\n\r]+|[\s\t]{2,}/).filter(n => n.trim());
});

const palaceSortOrder = [
    '玄通宮', '玄應宮', '玄心宮', '玄妙宮', '玄昇宮',
    '玄願宮', '玄法宮', '玄閻宮', '玄窕宮', '玄瑤宮', '玄義宮'
];

const palaceData = ref(palaceSortOrder.reduce((acc, name) => {
    acc[name] = { obtained_date: '', remarks: '' };
    return acc;
}, {}));

const isPalaceMode = computed(() => {
    return form.value.master_id === 8; // 閻王仙師
});

const hasPalaceName = computed(() => {
    const palaceRegex = /^玄(通|應|心|妙|昇|升|願|法|閻|窕|瑤|義)宮$/;
    return personnel.value.some(p => palaceRegex.test(p.custom_name));
});

const cleanedTreasureNames = computed(() => treasureNames.value);

const batchParsedRows = computed(() => {
    if (!batchInput.value.trim()) return [];
    
    const lines = batchInput.value.split('\n');
    const results = [];
    let currentMasterId = form.value.master_id;
    let currentDateInText = null; // Changed from currentDate = form.value.record_date to null
    let currentContextYear = new Date().getFullYear();

    const parseDateText = (str, ctxYear = null) => {
        if (!str) return null;
        // Handle explicitly ROC prefixes like "民國113年" or "113年"
        const rocYearMatch = str.match(/^(?:民國)?\s*(\d{2,3})\s*年?$/);
        if (rocYearMatch) {
            const y = parseInt(rocYearMatch[1]) + 1911;
            return `${y}-01-01`;
        }

        // Priority 1: 4-digit CE
        const ceMatch = str.match(/\b(\d{4})[/\-\s](\d{1,2})[/\-\s](\d{1,2})\b/);
        if (ceMatch) return `${ceMatch[1]}-${ceMatch[2].padStart(2,'0')}-${ceMatch[3].padStart(2,'0')}`;
        // Priority 2: 2-3 digit ROC
        const rocMatch = str.match(/\b(\d{2,3})[/\-\s](\d{1,2})[/\-\s](\d{1,2})\b/);
        if (rocMatch) {
            const y = parseInt(rocMatch[1]) + 1911;
            return `${y}-${rocMatch[2].padStart(2,'0')}-${rocMatch[3].padStart(2,'0')}`;
        }
        // Priority 3: Month/Day only (Uses contextYear or Current Year)
        const mdMatch = str.match(/\b(\d{1,2})[/\-\s](\d{1,2})\b/);
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

    lines.forEach(line => {
        let normLine = line.normalize('NFKC').trim();
        if (!normLine) return;

        // Inline Year Prefix
        const inlineYearMatch = normLine.match(/^(?:民國)?\s*(\d{2,4})\s*年?\s+/);
        if (inlineYearMatch) {
            let y = parseInt(inlineYearMatch[1]);
            if (y < 1000) y += 1911;
            currentContextYear = y;
            normLine = normLine.replace(inlineYearMatch[0], '').trim();
        }

        // Detect Standalone Year Line
        const standaloneYearMatch = normLine.match(/^(?:民國)?\s*(\d{2,4})\s*年?$/);
        if (standaloneYearMatch) {
            let y = parseInt(standaloneYearMatch[1]);
            if (y < 1000) y += 1911;
            currentContextYear = y;
            return;
        }

        // 1. Detect Standalone Date Header
        const dateHeader = parseDateText(normLine, currentContextYear);
        const isPureDateStr = normLine.replace(/[\d/.\-年月日時分秒\s]/g, '').length === 0;
        if (dateHeader && isPureDateStr) {
            currentDateInText = dateHeader;
            return;
        }

        // 2. Detect Master Header
        const masterMatch = props.masters?.find(m => normLine.includes(m.name) && normLine.length < 15);
        if (masterMatch) {
            currentMasterId = masterMatch.id;
            return;
        }

        // 3. Detect Attribute Keywords
        const attrKeywords = ['用意', '狀態', '備註', '求寶方式', '求寶', '由來', '得知日期', '登記日期', '求得日期', '日期'];
        const firstWord = normLine.split(/[\s：:]/)[0];
        
        if (attrKeywords.includes(firstWord) && results.length > 0) {
            const prev = results[results.length - 1];
            const val = normLine.replace(new RegExp(`^${firstWord}[\\s：:]*`), '').trim();
            if (firstWord === '用意') prev.purpose = val;
            else if (firstWord === '狀態') {
                prev.status = val;
                if (val.includes('已登記')) prev.obtained_date = prev.date || currentDate;
            }
            else if (['得知日期', '登記日期', '求得日期', '日期'].includes(firstWord)) {
                const d = parseDateText(val, currentContextYear) || val;
                if (firstWord === '得知日期') {
                    prev.date = d;
                    currentDateInText = d;
                } else {
                    prev.obtained_date = d;
                    prev.status = '已登記';
                    currentDateInText = d;
                }
            }
            else if (firstWord === '求寶方式' || firstWord === '求寶') prev.acquisition_method = val;
            else prev.remarks = (prev.remarks ? prev.remarks + ' ' : '') + val;
            return;
        }

        // 4. Parse Standard Record
        // Extract date from start of line if present
        const lineStartDateMatch = normLine.match(/^(\d{1,4}[/.-]\d{1,2}[/.-]\d{1,2}|\d{1,2}[/.-]\d{1,2})\s+/);
        if (lineStartDateMatch) {
            const parsed = parseDateText(lineStartDateMatch[1], currentContextYear);
            if (parsed) currentDateInText = parsed;
            normLine = normLine.replace(lineStartDateMatch[0], '').trim();
        }

        const kwMatch = normLine.match(/^\s*((允求|賜降|得知|賜予|賜|法寶名稱|法寶內容|求得)\s*)?(.*?)[：:](.*)/);
        if (kwMatch && kwMatch[3] && kwMatch[3].trim() && !attrKeywords.includes(kwMatch[3].trim())) {
            let rawName = kwMatch[3].trim();
            rawName = rawName.replace(/^(允求|賜降|得知|賜予|賜|求得)\s*/, '');
            const name = rawName;
            const val = kwMatch[4].trim();
            
            const item = { 
                name, 
                remarks: val, 
                master_id: currentMasterId, 
                date: currentDateInText || '', 
                purpose: '',
                acquisition_method: '',
                obtained_date: currentDateInText || form.value.record_date || '',
                status: (currentDateInText || form.value.record_date) ? '已登記' : '未求得'
            };
            
            if (val.includes('已登記')) {
                item.status = '已登記';
                item.obtained_date = currentDate;
                item.remarks = val.replace('已登記', '').replace(/[，、, \s]+$/, '').trim();
            }
            results.push(item);
        } else if (normLine.match(/^(允求|賜降|得知|賜予|賜|求得)\s+/)) {
            const parts = normLine.split(/\s+/);
            const item = { 
                name: parts[1], 
                remarks: parts.slice(2).join(' '), 
                master_id: currentMasterId, 
                date: currentDateInText || '', 
                purpose: '',
                acquisition_method: '',
                obtained_date: currentDateInText || form.value.record_date || '',
                status: (currentDateInText || form.value.record_date) ? '已登記' : '未求得'
            };
            results.push(item);
        } else if (normLine.length < 50) {
            const item = { 
                name: normLine, 
                remarks: '', 
                master_id: currentMasterId, 
                date: currentDateInText || '', 
                purpose: '',
                acquisition_method: '',
                obtained_date: currentDateInText || form.value.record_date || '',
                status: (currentDateInText || form.value.record_date) ? '已登記' : '未求得'
            };
            results.push(item);
        }
    });
    return results;
});

const parsedItemsCount = computed(() => batchParsedRows.value.length);

const previewItems = computed(() => {
    return batchParsedRows.value.slice(0, 20).map(r => r.name);
});

// Methods for row management removed as we use textarea now

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
        relationship: '',
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

const movePersonnel = (idx, direction) => {
    const targetIdx = idx + direction;
    if (targetIdx < 0 || targetIdx >= personnel.value.length) return;
    const item = personnel.value[idx];
    personnel.value.splice(idx, 1);
    personnel.value.splice(targetIdx, 0, item);
};

const selectedMasterName = computed(() => {
    const m = props.masters?.find(m => String(m.id) === String(form.value.master_id));
    if (m) {
        return m.name === '父皇仙師' ? '父皇' : m.name;
    }
    return '';
});

watch(() => props.initialData, (newVal) => {
    form.value = { ...newVal };
    treasureNamesText.value = newVal.name || '';
    if (newVal.dharma_name_registries) {
        personnel.value = newVal.dharma_name_registries.map(r => ({
            dharma_name_id: r.dharma_name_id,
            custom_name: r.dharma_name?.name || r.custom_name || '',
            relationship: Array.isArray(r.related_personnel) ? r.related_personnel.join('、') : (r.related_personnel || ''),
            obtained_date: r.obtained_date || '',
            remarks: Array.isArray(r.remarks) ? r.remarks.join('\n') : (r.remarks || '')
        }));
    }
}, { deep: true, immediate: true });

watch(() => props.mode, (newVal) => {
    if (newVal) localMode.value = newVal;
});

// Intelligent Date Auto-conversion and Auto-newline for Batch Mode
watch(batchInput, (newVal, oldVal) => {
    if (newVal.length <= oldVal.length) return;
    
    // Pattern: 2-3 digit ROC year followed by date separators
    // Removed $ anchor to handle text following date
    const rocRegex = /(\b\d{2,3})([/.-])(\d{1,2})\2(\d{1,2})(?!\d)/;
    const ceRegex = /(\b\d{4})([/.-])(\d{1,2})\2(\d{1,2})(?!\d)/;
    
    let match = newVal.match(rocRegex);
    if (match) {
        const fullMatch = match[0];
        const y = parseInt(match[1]) + 1911;
        const s = match[2];
        const m = match[3].padStart(2, '0');
        const d = match[4].padStart(2, '0');
        // Only replace if it hasn't been converted yet
        if (fullMatch.startsWith(match[1])) {
            batchInput.value = newVal.replace(fullMatch, `${y}${s}${m}${s}${d}\n`);
        }
    } else {
        const ceMatch = newVal.match(ceRegex);
        if (ceMatch) {
            const fullMatch = ceMatch[0];
            const y = ceMatch[1];
            const s = ceMatch[2];
            const m = ceMatch[3].padStart(2, '0');
            const d = ceMatch[4].padStart(2, '0');
            // If it's CE, just ensure formatting and add newline
            if (ceMatch[3].length === 1 || ceMatch[4].length === 1 || !newVal.includes(fullMatch + '\n')) {
                batchInput.value = newVal.replace(fullMatch, `${y}${s}${m}${s}${d}\n`);
            }
        }
    }
});

import { nextTick } from 'vue';
const handlePersonnelEnter = (idx) => {
    if (idx === personnel.value.length - 1) {
        addPersonnelRow();
    }
    nextTick(() => {
        const inputs = document.querySelectorAll('.personnel-name-input');
        if (inputs[idx + 1]) inputs[idx + 1].focus();
    });
};

watch(personnel, (newVal) => {
    newVal.forEach((p, idx) => {
        if (p.relationship) {
            let rel = p.relationship.trim();
            // Keep the '之' if present, and don't force convert to 母親/父親
            if (!rel.startsWith('之') && !rel.startsWith('的')) {
                // Optional: add '之' if it's a known relative term? 
                // The user said "之字不顯示的規則去掉", implying they want it to show up.
            }
        }
    });
}, { deep: true });

// handleTreasureNameInput removed
const getMasterName = (id) => {
    const m = props.masters?.find(m => String(m.id) === String(id));
    return m ? m.name : '預設';
};

const handlePersonnelNameInput = (idx, event) => {
    let val = event.target.value;
    
    // Rule: "Name之Relative" split
    const relSplitMatch = val.match(/^(.*?)[之的](.+)$/);
    if (relSplitMatch) {
        const namePart = relSplitMatch[1].trim();
        let relPart = relSplitMatch[2].trim();
        
        // Keep the '之' or '的' as part of the relationship
        const connector = val.includes('之') ? '之' : '的';
        
        personnel.value[idx].custom_name = namePart;
        personnel.value[idx].relationship = connector + relPart;
        return;
    }

    const splitters = /[，、, \s]+/;
    
    // Check for multi-name input (e.g., "A B C" or "A,B,C")
    if (splitters.test(val)) {
        const names = val.split(splitters).filter(n => n.trim());
        if (names.length > 1) {
            // Keep the first name in current row, move others to new rows
            personnel.value[idx].custom_name = names[0];
            
            // Insert new rows after current index
            const newRows = names.slice(1).map(n => ({
                custom_name: n,
                relationship: personnel.value[idx].relationship,
                obtained_date: personnel.value[idx].obtained_date,
                remarks: ''
            }));
            
            personnel.value.splice(idx + 1, 0, ...newRows);
            
            // Move focus to the end of the newly added rows
            nextTick(() => {
                const inputs = document.querySelectorAll('.personnel-name-input');
                const lastIdx = idx + names.length - 1;
                if (inputs[lastIdx]) inputs[lastIdx].focus();
            });
        }
    }
};

const handlePersonnelDateInput = (idx, event) => {
    let val = event.target.value.trim();
    if (!val) return;
    
    const rocRegex = /^(\d{2,3})([/.-])(\d{1,2})\2(\d{1,2})$/;
    const ceRegex = /^(\d{4})([/.-])(\d{1,2})\2(\d{1,2})$/;
    
    let isComplete = false;
    let match = val.match(rocRegex);
    if (match) {
        const y = parseInt(match[1]) + 1911;
        const m = match[3].padStart(2, '0');
        const d = match[4].padStart(2, '0');
        personnel.value[idx].obtained_date = `${y}-${m}-${d}`;
        isComplete = true;
    } else {
        match = val.match(ceRegex);
        if (match) {
            const y = match[1];
            const m = match[3].padStart(2, '0');
            const d = match[4].padStart(2, '0');
            personnel.value[idx].obtained_date = `${y}-${m}-${d}`;
            isComplete = true;
        }
    }
    
    if (isComplete) {
        // Move focus to next line (next person's name field)
        if (idx === personnel.value.length - 1) {
            addPersonnelRow();
        }
        nextTick(() => {
            const nextRowNameInput = document.querySelectorAll('.personnel-name-input')[idx + 1];
            if (nextRowNameInput) nextRowNameInput.focus();
        });
    }
};


const handleFileImport = (event) => {
    const file = event.target.files[0];
    if (!file) return;

    if (typeof XLSX === 'undefined') {
        const script = document.createElement('script');
        script.src = "https://cdn.sheetjs.com/xlsx-latest/package/dist/xlsx.full.min.js";
        script.onload = () => processExcelFile(file);
        document.head.appendChild(script);
    } else {
        processExcelFile(file);
    }
};

const processExcelFile = (file) => {
    const reader = new FileReader();
    reader.onload = (e) => {
        const data = new Uint8Array(e.target.result);
        const workbook = XLSX.read(data, { type: 'array' });
        const firstSheetName = workbook.SheetNames[0];
        const worksheet = workbook.Sheets[firstSheetName];
        const jsonData = XLSX.utils.sheet_to_json(worksheet, { header: 1 });
        
        let newText = "";
        jsonData.forEach(row => {
            if (row.length === 0) return;
            const line = row.filter(cell => cell !== null && cell !== undefined).join(' ');
            if (line.trim()) newText += line + "\n";
        });

        if (newText) batchInput.value += (batchInput.value ? "\n" : "") + newText;
    };
    reader.readAsArrayBuffer(file);
};

const validateSingle = () => {
    if (!form.value.master_id) return '請選擇仙師';
    
    // Auto-fill main record_date if empty but first personnel has a date
    if (!form.value.record_date && personnel.value.length > 0 && personnel.value[0].obtained_date) {
        form.value.record_date = personnel.value[0].obtained_date;
    }
    // Default to today if still empty
    if (!form.value.record_date) {
        form.value.record_date = new Date().toISOString().split('T')[0];
    }

    if (!form.value.name && !treasureNamesText.value.trim()) return '請輸入法寶名稱';
    if (!form.value.record_date) return '請輸入頂部的「得知日期」';
    
    return null;
};

const handleSubmit = async () => {
    if (localMode.value === 'single') {
        const error = validateSingle();
        if (error) {
            alert(error);
            return;
        }
        
        // Clean up personnel: remove empty rows
        let cleanedPersonnel = personnel.value.filter(p => p.custom_name && p.custom_name.trim() !== '');

        // If Palace Mode, merge palaceData into personnel
        if (isPalaceMode.value && hasPalaceName.value) {
            palaceSortOrder.forEach(pName => {
                const pInfo = palaceData.value[pName];
                if (pInfo.obtained_date || pInfo.remarks) {
                    // Check if already in personnel (to avoid duplicates)
                    const existingIdx = cleanedPersonnel.findIndex(p => p.custom_name.replace('升', '昇') === pName.replace('升', '昇'));
                    if (existingIdx !== -1) {
                        if (pInfo.obtained_date) cleanedPersonnel[existingIdx].obtained_date = pInfo.obtained_date;
                        if (pInfo.remarks) cleanedPersonnel[existingIdx].remarks = pInfo.remarks;
                    } else {
                        cleanedPersonnel.push({
                            custom_name: pName,
                            relationship: '',
                            obtained_date: pInfo.obtained_date,
                            remarks: pInfo.remarks
                        });
                    }
                }
            });
        }

        // If multiple names entered in textarea, handle them
        if (cleanedTreasureNames.value.length > 0) {
            cleanedTreasureNames.value.forEach(tn => {
                let finalName = tn.trim();
                let finalMethod = form.value.acquisition_method;
                
                const methodMatch = finalName.match(/(.*)\s*(求寶|求寶方式)[：:](.*)/);
                if (methodMatch) {
                    finalName = methodMatch[1].trim();
                    finalMethod = methodMatch[3].trim();
                }

                const payload = { 
                    ...form.value, 
                    name: finalName,
                    acquisition_method: finalMethod,
                    dharma_name_registries: cleanedPersonnel.map(p => ({
                        ...p,
                        remarks: p.remarks, // Backend normalizeRemarks handles string -> array
                        related_personnel: p.relationship ? p.relationship.split(/[、, ]+/).filter(x => x) : []
                    }))
                };
                emit('saveSingle', payload);
            });
        } else {
            // Fallback for single name in form.name
            const payload = {
                ...form.value,
                dharma_name_registries: cleanedPersonnel.map(p => ({
                    ...p,
                    remarks: p.remarks,
                    related_personnel: p.relationship ? p.relationship.split(/[、, ]+/).filter(x => x) : []
                }))
            };
            emit('saveSingle', payload);
        }
    } else {
        // Batch Save Logic
        if (!form.value.master_id) {
            alert('請選擇仙師');
            return;
        }
        if (batchParsedRows.value.length === 0) {
            alert('請貼上或輸入有效資料內容');
            return;
        }
        emit('saveBatch', { 
            input: batchInput.value, 
            masterId: form.value.master_id,
            rows: batchParsedRows.value.map(row => ({
                name: row.name,
                master_id: row.master_id || form.value.master_id,
                record_date: row.date || form.value.record_date || '',
                remarks: row.remarks || form.value.remarks || ''
            }))
        });
    }
};

const updatePersonnelRemarks = (idx, content) => {
    if (personnel.value[idx]) {
        personnel.value[idx].remarks = content;
    }
};

defineExpose({ updatePersonnelRemarks });
</script>

<style scoped>
.animate-slide-up { animation: slideUp 0.4s cubic-bezier(0.16, 1, 0.3, 1); }
.animate-fade-in { animation: fadeIn 0.3s ease-out; }
.animate-pop-in { animation: popIn 0.25s cubic-bezier(0.2, 1, 0.3, 1); }
@keyframes slideUp { from { transform: translateY(100%); } to { transform: translateY(0); } }
@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
@keyframes popIn { from { opacity: 0; transform: scale(0.95) translateY(-10px); } to { opacity: 1; transform: scale(1) translateY(0); } }

.dropdown-enter-active, .dropdown-leave-active { transition: all 0.2s ease; }
.dropdown-enter-from, .dropdown-leave-to { opacity: 0; transform: translateY(-10px) scale(0.95); }

.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
</style>
