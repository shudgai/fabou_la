<template>
    <div class="bg-slate-50 h-[100dvh] flex flex-col relative overflow-hidden text-slate-900">
        <!-- Header (Shared) -->
        <div class="border-b border-slate-300 flex items-center bg-white sticky top-0 z-[110]" style="padding: 8px 15px; min-height: 52px;">
            <button @click="handleBack" class="text-slate-400 p-2 -ml-2 mr-0.5 active:scale-90 transition-transform shrink-0">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" /></svg>
            </button>
            <div class="flex-1 flex flex-col justify-center min-w-0 py-1">
                <div class="text-[22px] font-black leading-tight font-outfit tracking-widest break-words" style="color: rgb(168, 85, 247);">
                    {{ addMode ? (form.id ? '編輯紀錄' : '新增紀錄') : '開文專區' }}
                </div>
            </div>
            
            <!-- Tab Switcher (Only in List View) -->
            <div v-if="!addMode" class="absolute right-4 top-1/2 -translate-y-1/2 bg-slate-100 p-1 rounded-xl flex shadow-inner">
                <button @click="currentTab = 'weekly'" 
                    :class="currentTab === 'weekly' ? 'bg-white shadow-sm text-purple-600' : 'text-slate-400'"
                    class="px-3 py-1.5 text-[13px] font-black rounded-lg transition-all whitespace-nowrap">
                    每週開文
                </button>
                <button @click="currentTab = 'self'" 
                    :class="currentTab === 'self' ? 'bg-white shadow-sm text-purple-600' : 'text-slate-400'"
                    class="px-3 py-1.5 text-[13px] font-black rounded-lg transition-all whitespace-nowrap">
                    自行開文
                </button>
            </div>
            
            <!-- Form View Sub-Tabs -->
            <div v-else class="absolute right-4 top-1/2 -translate-y-1/2 bg-slate-100 p-1 rounded-xl flex shadow-inner">
                <button @click="formTab = 'original'" 
                    :class="formTab === 'original' ? 'bg-white shadow-sm text-slate-900' : 'text-slate-400'"
                    class="px-3 py-1.5 text-[13px] font-black rounded-lg transition-all whitespace-nowrap">
                    原始內容
                </button>
                <button @click="handleModifiedTabClick" 
                    :class="[
                        formTab === 'modified' ? 'bg-white shadow-sm text-purple-600' : 'text-slate-400',
                        isModifiedLocked ? 'opacity-50 grayscale' : ''
                    ]"
                    class="px-3 py-1.5 text-[13px] font-black rounded-lg transition-all whitespace-nowrap flex items-center">
                    <svg v-if="isModifiedLocked" class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" /></svg>
                    殿中貼文
                </button>
            </div>
        </div>


        <div v-if="persistentToast" class="fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 z-[9999] pointer-events-auto">
            <div class="bg-white rounded-lg shadow-[0_4px_20px_rgba(0,0,0,0.2)] flex flex-col border border-slate-200" style="padding: 10px 15px; min-width: 140px;">
                <div class="flex items-start justify-between space-x-3">
                    <span :class="persistentToast.type === 'error' ? 'text-red-600' : 'text-emerald-600'" class="text-[15px] font-black leading-normal break-words uppercase tracking-wide">
                        {{ persistentToast.msg }}
                    </span>
                    <button v-if="persistentToast.type === 'confirm' || persistentToast.type === 'deleteConfirm'" @click="persistentToast = null" class="text-slate-400 w-5 h-5 flex items-center justify-center font-normal text-sm">✕</button>
                </div>
                <div v-if="persistentToast.type === 'deleteConfirm'" class="flex space-x-2 mt-3 pb-1">
                    <button @click="persistentToast = null" class="flex-1 bg-slate-50 text-slate-600 px-2 py-1.5 rounded border text-[13px] font-normal">取消</button>
                    <button @click="executeDelete" class="flex-1 bg-red-50 text-red-600 px-2 py-1.5 rounded border border-red-100 text-[13px] font-normal">確定刪除</button>
                </div>
            </div>
        </div>



        <!-- LIST VIEW -->
        <div v-if="!addMode" class="flex-1 overflow-y-auto custom-scrollbar p-3 pb-32 relative z-[1]">
            <!-- Weekly Post View -->
            <div v-if="currentTab === 'weekly'" class="space-y-3 max-w-2xl mx-auto">
                <button @click="openAddMode('weekly')" class="w-full bg-white border border-purple-100 rounded-2xl py-2.5 px-4 flex items-center justify-center space-x-3 text-purple-600 hover:bg-purple-50 transition-all shadow-sm active:scale-[0.98]">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span class="font-black text-[15px] tracking-widest">新增每週開文紀錄</span>
                </button>

                <!-- Weekly List -->
                <div 
                    v-for="post in weeklyPosts" 
                    :key="post.id"
                    :id="`post-${post.id}`"
                    @click="toggleExpand(post.id)"
                    :class="[
                        expandedIds[post.id] 
                            ? 'fixed inset-0 z-[150] bg-white overflow-y-auto p-4 md:p-8 animate-fade-in' 
                            : 'bg-white border border-slate-200 rounded-2xl py-2.5 px-4 shadow-sm hover:shadow-md cursor-pointer transition-all active:scale-[0.99] relative'
                    ]"
                >
                    <div :class="{'max-w-3xl mx-auto h-full flex flex-col': expandedIds[post.id]}">
                        <div class="flex items-center justify-between mb-1">
                            <div class="flex flex-col min-w-0">
                                <span class="text-[14px] font-black text-slate-400 tracking-widest mb-1">{{ post.date || '無日期' }}</span>
                                <h3 v-if="!expandedIds[post.id]" class="text-[18px] font-normal text-slate-900 tracking-[0.2em] whitespace-pre-wrap">
                                    {{ post.title || '無抬頭' }}
                                </h3>
                            </div>
                            <div class="flex items-center space-x-2 self-start">
                                <span v-if="post.status === '合格'" style="color: #15803d !important;" class="font-normal text-[18px] shrink-0 tracking-widest">合格</span>
                                <span v-else-if="post.status === '不合格'" style="color: #b91c1c !important;" class="font-normal text-[18px] shrink-0 tracking-widest">不合格</span>
                                <span v-else class="text-slate-900 font-normal text-[18px] shrink-0 tracking-widest">待定</span>
                                
                                <!-- Only show menu if NOT in full page mode, or show a close button in full page mode -->
                                <template v-if="!expandedIds[post.id]">
                                    <button @click.stop="openMenuId = (openMenuId === post.id ? null : post.id)" class="p-1 hover:bg-slate-100 rounded-lg transition-colors">
                                        <svg class="w-6 h-6 text-slate-400" fill="currentColor" viewBox="0 0 24 24">
                                            <circle cx="5" cy="12" r="2" />
                                            <circle cx="12" cy="12" r="2" />
                                            <circle cx="19" cy="12" r="2" />
                                        </svg>
                                    </button>
                                    
                                    <!-- Actions Menu -->
                                    <div v-if="openMenuId === post.id" class="absolute right-4 top-12 w-48 bg-white rounded-2xl shadow-2xl border border-slate-100 z-[100] py-1 animate-fade-in overflow-hidden">
                                        <button @click.stop="openPostMode(post, 'weekly')" class="w-full px-4 py-3 text-left text-[17px] font-bold text-slate-700 hover:bg-slate-50 flex items-center border-b border-slate-50">
                                            <svg class="w-5 h-5 mr-3 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            殿中修改之文
                                        </button>
                                        <button v-if="!hasAnyExpanded" @click.stop="expandAll" class="w-full px-4 py-3 text-left text-[17px] font-bold text-slate-700 hover:bg-slate-50 flex items-center border-b border-slate-50"><svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>展開清單</button>
                                        <button v-else @click.stop="collapseAll" class="w-full px-4 py-3 text-left text-[17px] font-bold text-slate-700 hover:bg-slate-50 flex items-center border-b border-slate-50"><svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 12h16"/></svg>收起清單</button>
                                        <button @click.stop="editItem(post, 'weekly')" class="w-full px-4 py-3 text-left text-[17px] font-bold text-slate-700 hover:bg-slate-50 flex items-center border-b border-slate-50"><svg class="w-5 h-5 mr-3 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>編輯紀錄</button>
                                        <button @click.stop="confirmDelete(post.id, 'weekly')" class="w-full px-4 py-3 text-left text-[17px] font-bold text-red-600 hover:bg-red-50 flex items-center"><svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>刪除紀錄</button>
                                    </div>
                                </template>
                                <button v-else class="p-2 hover:bg-slate-100 rounded-full transition-colors">
                                    <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                        </div>
                        
                        <div v-if="expandedIds[post.id]" class="flex-1 bg-slate-50 px-6 pb-6 pt-3 rounded-2xl border border-slate-100 space-y-4 animate-fade-in overflow-y-auto" @click.stop>
                            <!-- Date & Status Header -->
                            <div class="flex justify-between items-start border-b border-slate-200 pb-2">
                                <div class="flex flex-col">
                                    <span class="text-[14px] font-bold text-slate-300 uppercase tracking-widest mb-1">日期</span>
                                    <span class="text-[16px] font-black text-slate-700 tracking-widest">{{ post.date || '無日期' }}</span>
                                </div>
                                <div class="flex flex-col items-end">
                                    <span class="text-[14px] font-bold text-slate-300 uppercase tracking-widest mb-1">狀態</span>
                                    <span :class="{
                                        'text-green-500': post.status === '合格',
                                        'text-red-500': post.status === '不合格'
                                    }" class="text-[20px] font-black tracking-widest">{{ post.status || '待定' }}</span>
                                </div>
                            </div>
                            
                            <!-- Title inside full page -->
                            <div class="border-b border-slate-100 pb-1">
                                <span class="text-[14px] font-bold text-slate-300 uppercase tracking-widest block mb-2">抬頭</span>
                                <h3 class="text-[18px] font-normal text-slate-900 tracking-[0.2em] leading-[1.6]">
                                    {{ post.title || '無抬頭' }}
                                </h3>
                            </div>

                            <div>
                                <span class="app-title block mb-3 uppercase tracking-widest">開文內容</span>
                                <p class="app-body whitespace-pre-wrap leading-[1.6] tracking-widest">{{ post.original_content || '-' }}</p>
                            </div>
                            <div v-if="post.modified_content" class="h-[1px] bg-slate-200"></div>
                            <div v-if="post.modified_content">
                                <span class="text-[14px] font-bold text-purple-400 uppercase tracking-widest block mb-3">殿中修改之文</span>
                                <div v-if="post.modified_content.startsWith('data:image')" class="mt-4">
                                    <img :src="post.modified_content" 
                                        @click.stop="viewerImage = post.modified_content"
                                        class="max-w-full md:max-w-xl h-auto rounded-xl border border-purple-100 shadow-lg cursor-zoom-in hover:brightness-95 transition-all" alt="殿中修改之文">
                                </div>
                                <p v-else class="text-[18px] font-black text-purple-900 whitespace-pre-wrap leading-[1.6] tracking-widest">{{ post.modified_content || '-' }}</p>
                            </div>
                            <div v-else-if="post.status === '合格'" class="pt-4">
                                <button @click.stop="openPostMode(post, 'weekly')" class="w-full py-4 bg-purple-50 text-purple-600 rounded-2xl border border-purple-100 font-black text-[17px] tracking-widest hover:bg-purple-100 transition-all flex items-center justify-center">
                                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    上傳殿中貼文圖片
                                </button>
                            </div>
                            
                            <div class="pt-8 flex justify-center">
                                <button @click.stop="editItem(post, 'weekly')" class="px-8 py-3 bg-white border border-slate-200 rounded-xl font-bold text-slate-600 hover:bg-slate-50 transition-all">編輯此紀錄</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Self Post View -->
            <div v-if="currentTab === 'self'" class="space-y-3 max-w-2xl mx-auto">
                <button @click="openAddMode('self')" class="w-full bg-white border border-purple-100 rounded-2xl py-2.5 px-4 flex items-center justify-center space-x-3 text-purple-600 hover:bg-purple-50 transition-all shadow-sm active:scale-[0.98]">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span class="font-black text-[15px] tracking-widest">新增自行開文紀錄</span>
                </button>
                <div 
                    v-for="post in selfPosts" 
                    :key="post.id"
                    :id="`post-${post.id}`"
                    @click="toggleExpand(post.id)"
                    :class="[
                        expandedIds[post.id] 
                            ? 'fixed inset-0 z-[150] bg-white overflow-y-auto p-4 md:p-8 animate-fade-in' 
                            : 'bg-white border border-slate-200 rounded-2xl py-2.5 px-4 shadow-sm hover:shadow-md cursor-pointer transition-all active:scale-[0.99] relative'
                    ]"
                >
                    <div :class="{'max-w-3xl mx-auto h-full flex flex-col': expandedIds[post.id]}">
                        <div class="flex items-center justify-between mb-1">
                            <div class="flex flex-col">
                                <span class="text-[14px] font-black text-slate-400 tracking-widest mb-1">{{ post.date || '無日期' }}</span>
                                <div v-if="!expandedIds[post.id]" class="text-[15px] font-bold text-slate-600 tracking-widest line-clamp-2">
                                    {{ (post.original_content || '').substring(0, 50).replace(/\n/g, ' ') }}...
                                </div>
                            </div>
                             <div class="flex items-center space-x-2 self-start">
                                <span :style="{ color: post.message_type === '玄訊' ? '#15803d' : '#0f172a' }" 
                                    class="text-[18px] font-normal shrink-0 tracking-widest">
                                    {{ post.message_type }}
                                </span>
                                <span class="text-[13px] font-black text-purple-600 shrink-0">{{ post.master?.name }}</span>
                                
                                <template v-if="!expandedIds[post.id]">
                                    <button @click.stop="openMenuId = (openMenuId === post.id ? null : post.id)" class="p-1 hover:bg-slate-100 rounded-lg transition-colors">
                                        <svg class="w-6 h-6 text-slate-400" fill="currentColor" viewBox="0 0 24 24">
                                            <circle cx="5" cy="12" r="2" />
                                            <circle cx="12" cy="12" r="2" />
                                            <circle cx="19" cy="12" r="2" />
                                        </svg>
                                    </button>
                                    
                                    <!-- Actions Menu -->
                                    <div v-if="openMenuId === post.id" class="absolute right-4 top-12 w-48 bg-white rounded-2xl shadow-2xl border border-slate-100 z-[100] py-1 animate-fade-in overflow-hidden">
                                        <button @click.stop="editItem(post, 'self')" class="w-full px-4 py-3 text-left text-[17px] font-bold text-slate-700 hover:bg-slate-50 flex items-center border-b border-slate-50"><svg class="w-5 h-5 mr-3 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>編輯紀錄</button>
                                        <button @click.stop="confirmDelete(post.id, 'self')" class="w-full px-4 py-3 text-left text-[17px] font-bold text-red-600 hover:bg-red-50 flex items-center"><svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>刪除紀錄</button>
                                    </div>
                                </template>
                                <button v-else class="p-2 hover:bg-slate-100 rounded-full transition-colors">
                                    <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                        </div>
                        
                        <div v-if="expandedIds[post.id]" class="flex-1 bg-slate-50 px-6 pb-6 pt-3 rounded-2xl border border-slate-100 space-y-4 mt-2 animate-fade-in overflow-y-auto" @click.stop>
                            <!-- Date & Type Header -->
                            <div class="flex justify-between items-start border-b border-slate-200 pb-2">
                                <div class="flex flex-col">
                                    <span class="text-[14px] font-bold text-slate-300 uppercase tracking-widest mb-1">日期</span>
                                    <span class="text-[16px] font-black text-slate-700 tracking-widest">{{ post.date || '無日期' }}</span>
                                </div>
                                <div class="flex flex-col items-end">
                                    <span class="text-[14px] font-bold text-slate-300 uppercase tracking-widest mb-1">類型</span>
                                    <span class="text-[20px] font-black text-purple-600 tracking-widest">{{ post.message_type }}</span>
                                </div>
                            </div>
                            
                            <div>
                                <span class="app-title block mb-3 uppercase tracking-widest">開文內容</span>
                                <p class="app-body whitespace-pre-wrap leading-[1.6] tracking-widest">{{ post.original_content || '-' }}</p>
                            </div>
                            <div v-if="post.modified_content" class="h-[1px] bg-slate-200"></div>
                            <div v-if="post.modified_content">
                                <span class="app-title text-purple-600 block mb-3 uppercase tracking-widest">殿中修改之文</span>
                                <div v-if="post.modified_content.startsWith('data:image')" class="mt-4">
                                    <img :src="post.modified_content" 
                                        @click.stop="viewerImage = post.modified_content"
                                        class="max-w-full md:max-w-xl h-auto rounded-xl border border-purple-100 shadow-lg cursor-zoom-in hover:brightness-95 transition-all" alt="殿中修改之文">
                                </div>
                                <p v-else class="app-body font-black text-purple-900 whitespace-pre-wrap leading-[1.6] tracking-widest">{{ post.modified_content || '-' }}</p>
                            </div>
                            
                            <div class="pt-8 flex justify-center">
                                <button @click.stop="editItem(post, 'self')" class="px-8 py-3 bg-white border border-slate-200 rounded-xl font-bold text-slate-600 hover:bg-slate-50 transition-all">編輯此紀錄</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- FORM VIEW (Full Page) -->
        <div v-else class="flex-1 overflow-y-auto custom-scrollbar bg-white animate-fade-in flex flex-col">
            <!-- Form Tabs -->
            <div class="flex border-b border-slate-100 bg-slate-50/50 sticky top-0 z-10">
                <button @click="formTab = 'original'" 
                    :class="formTab === 'original' ? 'text-purple-600 border-b-2 border-purple-600 bg-white' : 'text-slate-400 hover:bg-slate-100'"
                    class="flex-1 py-1.5 text-[18px] font-black tracking-widest transition-all">
                    原始內容
                </button>
                <button @click="handleModifiedTabClick" 
                    :class="[
                        formTab === 'modified' ? 'text-purple-600 border-b-2 border-purple-600 bg-white' : 'text-slate-400',
                        isModifiedLocked ? 'opacity-50 grayscale' : 'hover:bg-slate-100'
                    ]"
                    class="flex-1 py-1.5 text-[18px] font-black tracking-widest transition-all flex items-center justify-center">
                    殿中修改之文
                    <svg v-if="isModifiedLocked" class="w-3.5 h-3.5 ml-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/></svg>
                </button>
            </div>

            <div class="flex-1 p-4 md:p-6">
                <div class="max-w-2xl mx-auto space-y-6">
                    <!-- Tab 1: Original Content & Meta -->
                    <template v-if="formTab === 'original'">
                        <div class="space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div class="space-y-1">
                                    <div @click="activeDate = 'date'" class="w-full h-12 rounded-2xl bg-slate-50 border border-slate-200 px-4 flex items-center justify-between cursor-pointer active:bg-slate-100 transition-colors mt-5">
                                        <span :class="form.date ? 'text-slate-900 font-bold' : 'text-slate-400'" class="text-[17px] tracking-widest">
                                            {{ form.date || '選擇日期' }}
                                        </span>
                                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    </div>
                                </div>

                                <div v-if="addMode === 'self'" class="space-y-1">
                                    <label class="text-[14px] font-black text-slate-300 uppercase tracking-widest ml-1">類型</label>
                                    <select v-model="form.message_type" class="w-full h-12 rounded-2xl bg-slate-50 border border-slate-200 px-4 text-[17px] font-bold text-slate-900 outline-none tracking-widest">
                                        <option value="玄訊">玄訊</option>
                                        <option value="非玄訊">非玄訊</option>
                                    </select>
                                </div>

                                <div v-if="addMode === 'weekly'" class="space-y-1">
                                    <label class="text-[14px] font-black text-slate-300 uppercase tracking-widest ml-1">審核狀態</label>
                                    <div class="flex items-center space-x-4 h-12">
                                        <button @click="form.status = null" :class="!form.status ? 'text-slate-900' : 'text-slate-400'" class="font-black text-[18px] transition-all active:scale-95 tracking-widest">待定</button>
                                        <button @click="form.status = '合格'" :class="form.status === '合格' ? 'text-emerald-500' : 'text-slate-400'" class="font-black text-[18px] transition-all active:scale-95 tracking-widest">合格</button>
                                        <button @click="form.status = '不合格'" :class="form.status === '不合格' ? 'text-red-500' : 'text-slate-400'" class="font-black text-[18px] transition-all active:scale-95 tracking-widest">不合格</button>
                                    </div>
                                </div>
                            </div>

                            <div v-if="addMode === 'weekly'" class="space-y-1">
                                <label class="text-[14px] font-black text-slate-300 uppercase tracking-widest ml-1">抬頭</label>
                                <input v-model="form.title" type="text" placeholder="輸入抬頭..." class="w-full h-12 rounded-2xl bg-slate-50 border border-slate-200 px-4 text-[17px] font-bold text-slate-900 outline-none focus:border-purple-400 focus:bg-white transition-all tracking-widest">
                            </div>

                            <div v-if="addMode === 'self'" class="space-y-1">
                                <label class="text-[14px] font-black text-slate-300 uppercase tracking-widest ml-1">仙師</label>
                                <input v-model="form.master_name" list="master-list" placeholder="輸入或選擇仙師..." class="w-full h-12 rounded-2xl bg-slate-50 border border-slate-200 px-4 text-[17px] font-bold text-slate-900 outline-none focus:border-purple-400 focus:bg-white transition-all tracking-widest">
                                <datalist id="master-list">
                                    <option v-for="m in masters" :key="m.id" :value="m.name"></option>
                                </datalist>
                            </div>

                            <div class="space-y-1">
                                <label class="text-[14px] font-black text-slate-300 uppercase tracking-widest ml-1">開文內容</label>
                                
                                <div v-if="addMode === 'weekly'" class="bg-slate-50 rounded-2xl border border-slate-100 overflow-hidden shadow-inner">
                                    <div v-for="(char, i) in titleChars" :key="i" class="flex items-center bg-white border-b border-slate-50 last:border-0 group">
                                        <div class="w-10 h-9 flex items-center justify-center bg-slate-50/50 border-r border-slate-100 text-[17px] font-black text-slate-400 group-hover:text-purple-600 transition-colors">{{ char }}</div>
                                        <input v-model="weeklyLines[i]" type="text" placeholder="接著輸入..." class="flex-1 h-9 px-3 bg-transparent text-[16px] font-bold text-slate-800 outline-none placeholder:text-slate-200">
                                    </div>
                                </div>
                                
                                <div v-if="addMode === 'self'" class="space-y-1">
                                    <textarea v-model="form.original_content" rows="6" placeholder="請輸入開文內容..." class="w-full rounded-2xl bg-slate-50 border border-slate-200 p-4 text-[17px] font-bold text-slate-900 outline-none focus:border-purple-400 focus:bg-white transition-all tracking-widest leading-[1.2]"></textarea>
                                </div>
                            </div>
                            
                        </div>
                    </template>

                    <!-- Tab 2: Modified Content -->
                    <template v-else>
                        <div v-if="isModifiedLocked" class="bg-amber-50 border border-amber-200 rounded-3xl p-8 flex flex-col items-center justify-center text-center space-y-4 py-20">
                            <div class="w-16 h-16 bg-amber-100 text-amber-600 rounded-full flex items-center justify-center shadow-inner">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </div>
                            <div>
                                <h3 class="text-[18px] font-black text-amber-900 mb-1 tracking-widest">功能鎖定中</h3>
                                <p class="text-[15px] font-bold text-amber-700 tracking-wider">有合格才有機會鋪文。<br>請先在列表將此紀錄核定為「合格」。</p>
                            </div>
                            <button @click="formTab = 'original'" class="px-6 py-2 bg-white border border-amber-200 text-amber-700 rounded-xl font-black text-[14px] shadow-sm tracking-widest">返回原始內容</button>
                        </div>

                        <template v-else>
                            <div class="space-y-6">


                                <!-- Image Upload Area (Bottom) -->
                                <div class="space-y-2">
                                    <label class="text-[14px] font-black text-purple-600 uppercase tracking-widest ml-1 flex items-center">
                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h14a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        上傳/貼入殿中修改後之圖片
                                    </label>
                                    
                                    <div @click="triggerImageUpload" @paste="onImagePaste"
                                        class="w-full min-h-[300px] rounded-2xl bg-purple-50/30 border-2 border-dashed border-purple-200 flex flex-col items-center justify-center p-6 cursor-pointer hover:bg-purple-50 hover:border-purple-400 transition-all group overflow-hidden">
                                        
                                        <template v-if="form.modified_content && form.modified_content.startsWith('data:image')">
                                            <div class="relative group">
                                                <img :src="form.modified_content" 
                                                    @click.stop="viewerImage = form.modified_content"
                                                    class="max-w-[320px] h-auto rounded-xl shadow-lg border border-purple-100 cursor-zoom-in" alt="預覽">
                                                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center rounded-xl pointer-events-none">
                                                    <span class="text-white font-bold text-[14px]">點擊放大查看</span>
                                                </div>
                                            </div>
                                            <button @click.stop="form.modified_content = ''" class="mt-4 text-red-500 font-bold text-[13px] hover:underline">移除圖片</button>
                                        </template>
                                        <template v-else>
                                            <div class="text-center space-y-3">
                                                <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto group-hover:scale-110 transition-transform">
                                                    <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                                </div>
                                                <div class="space-y-1">
                                                    <p class="text-[17px] font-black text-purple-900 tracking-widest">點擊上傳 或 直接貼上圖片</p>
                                                    <p class="text-[13px] font-bold text-slate-400">支援 Ctrl+V 貼上截圖</p>
                                                </div>
                                            </div>
                                        </template>
                                        
                                        <input id="imageUpload" type="file" accept="image/*" class="hidden" @change="handleImageUpload">
                                    </div>
                                </div>

                                <div v-if="addMode === 'self'" class="space-y-1">
                                    <label class="text-[13px] font-black text-slate-400 uppercase tracking-widest ml-1">由那位師兄姐修改</label>
                                    <input v-model="form.modified_by" type="text" placeholder="例如：玄義宮師兄" class="w-full h-12 rounded-2xl bg-slate-50 border border-slate-200 px-4 text-[17px] font-bold text-slate-900 outline-none focus:border-purple-400 focus:bg-white transition-all tracking-widest">
                                </div>
                            </div>
                        </template>
                    </template>
                </div>
            </div>

            <!-- Form Footer -->
            <div class="p-4 bg-white border-t border-slate-100 shadow-[0_-4px_20px_rgba(0,0,0,0.03)]">
                <div class="max-w-2xl mx-auto w-full">
                    <button @click="saveForm" :disabled="isSaving" 
                        class="w-full bg-purple-600 !text-white h-16 rounded-2xl font-black text-[20px] shadow-xl shadow-purple-200 hover:bg-purple-700 active:scale-[0.98] transition-all disabled:bg-slate-300 flex items-center justify-center overflow-hidden tracking-[0.2em]">
                        <template v-if="isSaving">
                            <svg class="animate-spin h-7 w-7 !text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        </template>
                        <template v-else>
                            儲存紀錄
                        </template>
                    </button>
                </div>
            </div>
            
            <!-- Shared Date Picker -->
            <compact-date-picker 
                v-if="activeDate"
                v-model="form[activeDate]"
                title="選擇日期"
                @close="activeDate = null"
            />
        </div>


        <!-- Image Zoom Overlay -->
        <div v-if="viewerImage" class="fixed inset-0 z-[300] bg-black/90 flex items-center justify-center p-4 md:p-12 animate-fade-in" @click="viewerImage = null">
            <img :src="viewerImage" class="max-w-full max-h-full object-contain rounded-lg shadow-2xl animate-scale-in" alt="Zoomed view">
            <button @click="viewerImage = null" class="absolute top-6 right-6 p-3 bg-white/10 hover:bg-white/20 rounded-full backdrop-blur-md transition-all">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    </div>
</template>

<script setup>
// Final stable version
import { ref, onMounted, onUnmounted, watch, computed, nextTick } from 'vue';
import axios from 'axios';
import CompactDatePicker from './CompactDatePicker.vue';

const emit = defineEmits(['goHome']);

const currentTab = ref('weekly'); // 'weekly' or 'self'
const weeklyPosts = ref([]);
const selfPosts = ref([]);
const masters = ref([]);
const loading = ref(false);

// Form States
const addMode = ref(null); // 'weekly' or 'self' or null
const formTab = ref('original'); // 'original' or 'modified'
const form = ref({});
const activeDate = ref(null);
const isSaving = ref(false);
const viewerImage = ref(null);
const activeStatusDropdownId = ref(null);

const expandedIds = ref({});
const hasAnyExpanded = computed(() => Object.values(expandedIds.value).some(v => v));

const toggleExpand = (id) => {
    if (expandedIds.value[id]) {
        expandedIds.value[id] = false;
    } else {
        // Clear others for better focus
        expandedIds.value = { [id]: true };
        
        // Close menus
        openMenuId.value = null;
        
        nextTick(() => {
            const el = document.getElementById(`post-${id}`);
            if (el) {
                el.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    }
};

const expandAll = () => {
    const list = currentTab.value === 'weekly' ? weeklyPosts.value : selfPosts.value;
    const newExpanded = {};
    list.forEach(p => newExpanded[p.id] = true);
    expandedIds.value = newExpanded;
    openMenuId.value = null;
};

const collapseAll = () => {
    expandedIds.value = {};
    openMenuId.value = null;
};

// Weekly Acrostic Handling
const weeklyLines = ref(Array(14).fill(''));
const titleChars = computed(() => {
    const cleanTitle = (form.value.title || '').replace(/\s+/g, '');
    const chars = cleanTitle.split('');
    const result = [];
    for (let i = 0; i < 14; i++) {
        result.push(chars[i] || '');
    }
    return result;
});

const currentOriginalPreview = computed(() => {
    if (addMode.value === 'weekly') {
        return titleChars.value.map((char, i) => (char || '') + (weeklyLines.value[i] || '')).join('\n');
    }
    return form.value.original_content;
});

// Permission: Only qualified posts can have modified content (Weekly only)
const isModifiedLocked = computed(() => {
    if (addMode.value === 'weekly') {
        return form.value.status !== '合格';
    }
    return false;
});

const persistentToast = ref(null);
const deleteConfirmId = ref(null);
const deleteConfirmType = ref(null);

const openMenuId = ref(null);
const openMenuType = ref(null);

const loadData = async () => {
    loading.value = true;
    try {
        const [res, mres] = await Promise.all([
            axios.get('/kaiwen'),
            axios.get('/api/masters-list')
        ]);
        weeklyPosts.value = res.data.weeklyPosts || [];
        selfPosts.value = res.data.selfPosts || [];
        masters.value = mres.data || [];
    } catch (e) {
        console.error('Failed to load kaiwen', e);
    } finally {
        loading.value = false;
    }
};

const handleBack = () => {
    if (addMode.value) {
        addMode.value = null;
    } else {
        emit('goHome');
    }
};

const handleModifiedTabClick = () => {
    if (isModifiedLocked.value) {
        persistentToast.value = { msg: '仙師允方可鋪文', type: 'error' };
        setTimeout(() => { persistentToast.value = null; }, 2000);
        return;
    }
    formTab.value = 'modified';
};

const getMasterName = (id) => {
    if (!id) return '-';
    const m = masters.value.find(x => String(x.id) === String(id));
    return m ? m.name : '-';
};

const openAddMode = (type) => {
    addMode.value = type;
    formTab.value = 'original';
    weeklyLines.value = Array(14).fill('');
    form.value = {
        date: new Date().toISOString().split('T')[0],
        status: null,
        message_type: type === 'self' ? '玄訊' : undefined,
        original_content: '',
        modified_content: ''
    };
};

const editItem = (post, type) => {
    addMode.value = type;
    formTab.value = 'original';
    form.value = { ...post };
    
    if (type === 'weekly' && post.original_content) {
        const lines = post.original_content.split('\n');
        const cleanTitle = (form.value.title || '').replace(/\s+/g, '');
        weeklyLines.value = Array(14).fill('').map((_, i) => {
            const fullLine = lines[i] || '';
            const char = cleanTitle[i] || '';
            if (char && fullLine.startsWith(char)) {
                return fullLine.substring(char.length);
            }
            return fullLine;
        });
    }
    
    openMenuId.value = null;
};

// Image Handling Logic
const triggerImageUpload = () => {
    const input = document.getElementById('imageUpload');
    if (input) input.click();
};

const handleImageUpload = (e) => {
    const file = e.target.files[0];
    if (file) {
        processImage(file);
    }
};

const onImagePaste = (e) => {
    const items = (e.clipboardData || e.originalEvent.clipboardData).items;
    for (let index in items) {
        const item = items[index];
        if (item.kind === 'file') {
            const blob = item.getAsFile();
            processImage(blob);
        }
    }
};

const processImage = (file) => {
    const reader = new FileReader();
    reader.onload = (event) => {
        const img = new Image();
        img.onload = () => {
            const canvas = document.createElement('canvas');
            const MAX_WIDTH = 800; // Optimal width for readability vs storage
            let width = img.width;
            let height = img.height;

            if (width > MAX_WIDTH) {
                height *= MAX_WIDTH / width;
                width = MAX_WIDTH;
            }

            canvas.width = width;
            canvas.height = height;
            const ctx = canvas.getContext('2d');
            
            // Fill white background (useful for transparent PNGs converted to JPEG)
            ctx.fillStyle = '#FFFFFF';
            ctx.fillRect(0, 0, width, height);
            
            ctx.drawImage(img, 0, 0, width, height);
            
            // Convert to JPEG with 0.7 quality for high compression
            form.value.modified_content = canvas.toDataURL('image/jpeg', 0.7);
        };
        img.src = event.target.result;
    };
    reader.readAsDataURL(file);
};

const handleGlobalPaste = (e) => {
    if (addMode.value && formTab.value === 'modified') {
        onImagePaste(e);
    }
};

onMounted(() => {
    loadData();
    window.addEventListener('paste', handleGlobalPaste);
    window.addEventListener('click', (e) => { 
        if (!e.target.closest('.relative')) {
            openMenuId.value = null;
        }
    });
});

onUnmounted(() => {
    window.removeEventListener('paste', handleGlobalPaste);
});

const openPostMode = (post, type) => {
    addMode.value = type;
    formTab.value = 'modified';
    form.value = { ...post };
    openMenuId.value = null;
    
    // Auto populate weekly lines if editing from weekly
    if (type === 'weekly' && post.original_content) {
        const lines = post.original_content.split('\n');
        const cleanTitle = (form.value.title || '').replace(/\s+/g, '');
        weeklyLines.value = Array(14).fill('').map((_, i) => {
            const fullLine = lines[i] || '';
            const char = cleanTitle[i] || '';
            if (char && fullLine.startsWith(char)) {
                return fullLine.substring(char.length);
            }
            return fullLine;
        });
    }
};

const toggleMenu = (id, type) => {
    if (openMenuId.value === id && openMenuType.value === type) {
        openMenuId.value = null;
        openMenuType.value = null;
    } else {
        openMenuId.value = id;
        openMenuType.value = type;
    }
};

const formatTitleForSave = (title) => {
    if (!title) return '';
    const clean = title.replace(/\s+/g, '');
    let result = '';
    for (let i = 0; i < clean.length; i++) {
        result += clean[i];
        if ((i + 1) % 7 === 0 && i !== clean.length - 1) {
            result += '\n';
        }
    }
    return result;
};

const setStatusAndClose = (post, status) => {
    setWeeklyStatus(post, status);
    activeStatusDropdownId.value = null;
};

const cycleWeeklyStatus = (post) => {
    let nextStatus;
    if (!post.status) nextStatus = '合格';
    else if (post.status === '合格') nextStatus = '不合格';
    else nextStatus = null;
    setWeeklyStatus(post, nextStatus);
};

const setWeeklyStatus = async (post, status) => {
    try {
        await axios.patch(`/kaiwen/weekly/${post.id}`, { status });
        post.status = status;
        persistentToast.value = { msg: '狀態已更新', type: 'success' };
        setTimeout(() => { persistentToast.value = null; }, 1000);
    } catch (e) {
        console.error('Update status failed', e);
    }
};

const updateItemStatus = async (post, type) => {
    try {
        const url = type === 'weekly' ? `/kaiwen/weekly/${post.id}` : `/kaiwen/self/${post.id}`;
        await axios.patch(url, { status: post.status });
        persistentToast.value = { msg: '狀態已更新', type: 'success' };
        setTimeout(() => { persistentToast.value = null; }, 1000);
    } catch (e) {
        console.error('Update status failed', e);
    }
};

const copyFullText = (post) => {
    const textToCopy = post.modified_content || post.original_content;
    if (!textToCopy) {
        persistentToast.value = { msg: '無內容可複製', type: 'error' };
        setTimeout(() => { persistentToast.value = null; }, 2000);
        return;
    }
    
    navigator.clipboard.writeText(textToCopy).then(() => {
        persistentToast.value = { msg: '✓ 已全文複製', type: 'success' };
        setTimeout(() => { persistentToast.value = null; }, 2000);
    }).catch(err => {
        console.error('Copy failed', err);
        persistentToast.value = { msg: '複製失敗', type: 'error' };
        setTimeout(() => { persistentToast.value = null; }, 2000);
    });
};

const saveForm = async () => {
    isSaving.value = true;
    
    // Format title to 7 characters per line
    if (form.value.title) {
        form.value.title = formatTitleForSave(form.value.title);
    }

    if (addMode.value === 'weekly') {
        form.value.original_content = currentOriginalPreview.value;
    }
    
    try {
        const url = addMode.value === 'weekly' 
            ? (form.value.id ? `/kaiwen/weekly/${form.value.id}` : `/kaiwen/weekly`)
            : (form.value.id ? `/kaiwen/self/${form.value.id}` : `/kaiwen/self`);
        const method = form.value.id ? 'PUT' : 'POST';
        await axios({ method, url, data: form.value });
        persistentToast.value = { msg: '✓ 儲存成功', type: 'success' };
        setTimeout(() => { persistentToast.value = null; }, 2000);
        addMode.value = null;
        loadData();
    } catch (e) {
        console.error(e);
        persistentToast.value = { msg: '儲存失敗', type: 'error' };
        setTimeout(() => { persistentToast.value = null; }, 3000);
    } finally {
        isSaving.value = false;
    }
};

const confirmDelete = (id, type) => {
    deleteConfirmId.value = id;
    deleteConfirmType.value = type;
    persistentToast.value = { msg: '確定要刪除這筆開文紀錄嗎？', type: 'deleteConfirm' };
    openMenuId.value = null;
};

const executeDelete = async () => {
    if (!deleteConfirmId.value || !deleteConfirmType.value) return;
    try {
        await axios.delete(`/kaiwen/${deleteConfirmType.value}/${deleteConfirmId.value}`);
        persistentToast.value = { msg: '✓ 刪除成功', type: 'success' };
        setTimeout(() => { persistentToast.value = null; }, 2000);
        loadData();
    } catch (e) {
        persistentToast.value = { msg: '刪除失敗', type: 'error' };
        setTimeout(() => { persistentToast.value = null; }, 3000);
    } finally {
        deleteConfirmId.value = null;
        deleteConfirmType.value = null;
    }
};


</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar { width: 6px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
.animate-fade-in { animation: fadeIn 0.3s ease-out; }
@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
</style>
