<template>
    <div class="bg-slate-50 h-[100vh] flex flex-col relative overflow-hidden text-slate-900">
        <AddActionMenu 
            :show="showAddMenu" 
            :actions="addActions" 
            @close="showAddMenu = false"
        />
        <!-- Header (Shared) -->
        <div v-if="!hasAnyExpanded" class="border-b border-slate-300 flex items-center bg-white sticky top-0 z-[110]" style="padding: 8px 10px; min-height: 52px;">
            <div class="flex-1 flex flex-col justify-center min-w-0 py-1 pl-2 cursor-pointer" @click="resetToRoot">
                <div class="app-title text-[25px] font-bold leading-tight font-outfit tracking-widest break-words" style="color: rgb(168, 85, 247);">
                    {{ addMode ? (form.id ? '編輯紀錄' : '新增紀錄') : '開文專區' }}
                </div>
            </div>
            
            <!-- Search Icon (Only in main list view) -->
            <div v-if="!addMode && !hasAnyExpanded" class="flex items-center space-x-2 mr-2">
                <button @click="showSearch = !showSearch" class="p-2 text-slate-400 active:scale-90 transition-all">
                    <svg v-if="!showSearch" class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <svg v-else class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            
            <!-- Tab Switcher (Only in main list view, hide when expanded or in add mode) -->
            <div v-if="!addMode && !hasAnyExpanded && !showSearch" class="absolute right-4 top-1/2 -translate-y-1/2 bg-slate-100 p-1 rounded-xl flex shadow-inner animate-fade-in">
                <button @click="currentTab = 'weekly'" 
                    :class="currentTab === 'weekly' ? 'bg-white shadow-sm text-purple-600' : 'text-slate-400'"
                    class="px-3 py-1.5 app-body text-[17px] font-bold rounded-lg transition-all whitespace-nowrap">
                    每週開文
                </button>
                <button @click="currentTab = 'self'" 
                    :class="currentTab === 'self' ? 'bg-white shadow-sm text-purple-600' : 'text-slate-400'"
                    class="px-3 py-1.5 app-body text-[17px] font-bold rounded-lg transition-all whitespace-nowrap">
                    自行開文
                </button>
            </div>

            <!-- Search Input (Only when showSearch is active) -->
            <div v-if="showSearch && !addMode && !hasAnyExpanded" class="absolute right-12 top-1/2 -translate-y-1/2 flex items-center animate-fade-in" style="width: calc(100% - 150px);">
                <input v-model="searchQuery" type="text" placeholder="搜尋標題或內容..." 
                    class="w-full h-[36px] bg-slate-50 border border-slate-200 rounded-xl px-4 text-[16px] outline-none focus:border-purple-300 transition-all shadow-inner">
            </div>
            
            <!-- Header placeholder for alignment -->
            <div class="w-[40px]"></div>
        </div>


        <div v-if="persistentToast" class="fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 z-[9999] pointer-events-auto">
            <div class="bg-white rounded-3xl shadow-[0_20px_60px_rgba(0,0,0,0.3)] flex flex-col border border-slate-100 overflow-hidden" style="padding: 28px; min-width: 360px;">
                <div class="flex items-center justify-between mb-8">
                    <span class="text-[17px] font-black text-slate-900 leading-tight whitespace-nowrap tracking-widest">
                        {{ persistentToast.msg }}
                    </span>
                    <button @click="persistentToast = null" class="ml-6 text-slate-400 hover:text-slate-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="3" stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div v-if="persistentToast.type === 'deleteConfirm'" class="flex space-x-4">
                    <button @click="persistentToast = null" class="flex-1 bg-slate-50 text-slate-600 h-[48px] rounded-2xl border border-slate-100 text-[17px] font-black tracking-widest active:scale-95 transition-all">取消</button>
                    <button @click="executeDelete" class="flex-1 bg-red-50 text-red-600 h-[48px] rounded-2xl border border-red-100 text-[17px] font-black tracking-widest active:scale-95 transition-all">確定刪除</button>
                </div>
                <div v-else class="flex justify-end mt-2">
                    <button @click="persistentToast = null" class="bg-purple-50 text-purple-600 px-8 py-2.5 rounded-2xl text-[17px] font-black tracking-widest active:scale-95 transition-all">確定</button>
                </div>
            </div>
        </div>



        <!-- LIST VIEW -->
        <div v-if="!addMode" class="flex-1 overflow-y-auto custom-scrollbar px-[10px] py-3 pb-32 relative z-[1]">
            <!-- Weekly Post View -->
            <div v-if="currentTab === 'weekly'" class="space-y-3 max-w-2xl mx-auto">
                <!-- Removed top add button -->

                <!-- Weekly List -->
                <div 
                    v-for="post in filteredWeeklyPosts" 
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
                                <span class="text-[15px] tracking-tighter mb-0.5" style="color: #0d0d0d !important; font-family: 'Noto Sans TC', sans-serif !important; font-weight: 900 !important;">{{ post.date || '無日期' }}</span>
                                <span v-if="!expandedIds[post.id]" class="app-body text-[17px] tracking-widest" style="font-family: 'Montserrat', sans-serif !important; font-weight: 400 !important; color: #1e293b !important;">{{ (post.title || '').replace(/\s+/g, '').substring(0, 7) }}</span>
                                <span v-if="!expandedIds[post.id]" class="app-body text-[17px] tracking-widest" style="font-family: 'Montserrat', sans-serif !important; font-weight: 400 !important; color: #475569 !important;">{{ (post.title || '').replace(/\s+/g, '').substring(7, 14) }}</span>
                            </div>
                            <div class="flex items-center space-x-2 self-start">
                                <span v-if="post.status === '合格'" style="color: #16a34a !important;" class="text-[17px] font-black tracking-widest">合格</span>
                                <span v-else-if="post.status === '不合格'" style="color: #dc2626 !important;" class="text-[17px] font-black tracking-widest">不合格</span>
                                <span v-else class="text-[17px] font-black tracking-widest text-slate-400">待定</span>
                                
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
                                        <button @click.stop="openPostMode(post, 'weekly')" class="w-full px-4 py-3 text-left text-[17px] font-black text-slate-900 hover:bg-slate-50 flex items-center border-b border-slate-50">
                                            殿中修改之文
                                        </button>
                                        <button v-if="!hasAnyExpanded" @click.stop="expandAll" class="w-full px-4 py-3 text-left text-[17px] font-black text-slate-900 hover:bg-slate-50 flex items-center border-b border-slate-50">展開清單</button>
                                        <button v-else @click.stop="collapseAll" class="w-full px-4 py-3 text-left text-[17px] font-black text-slate-900 hover:bg-slate-50 flex items-center border-b border-slate-50">收起清單</button>
                                        <button @click.stop="editItem(post, 'weekly')" class="w-full px-4 py-3 text-left text-[17px] font-black text-slate-900 hover:bg-slate-50 flex items-center border-b border-slate-50">編輯紀錄</button>
                                        <button @click.stop="copyAsTextFile(post); openMenuId = null" class="w-full px-4 py-3 text-left text-[17px] font-black text-slate-900 hover:bg-slate-50 flex items-center border-b border-slate-50">複製 LINE</button>
                                        <button @click.stop="downloadPost(post); openMenuId = null" class="w-full px-4 py-3 text-left text-[17px] font-black text-slate-900 hover:bg-slate-50 flex items-center border-b border-slate-50">下載檔案</button>
                                        <button @click.stop="confirmDelete(post.id, 'weekly')" class="w-full px-4 py-3 text-left text-[17px] font-bold text-red-600 hover:bg-red-50 flex items-center">刪除紀錄</button>
                                    </div>
                                </template>
                                <button v-else @click.stop="toggleExpand(post.id)" class="p-2 hover:bg-slate-100 rounded-full transition-colors">
                                    <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                        </div>
                        
                        <div v-if="expandedIds[post.id]" class="flex-1 bg-slate-50 px-6 pb-6 pt-3 rounded-2xl border border-slate-100 space-y-4 animate-fade-in overflow-y-auto" @click.stop>
                            <!-- Title inside full page -->
                            <div class="border-b border-slate-100 pb-1">
                                <span class="text-[14px] font-bold text-slate-300 uppercase tracking-widest block mb-2">抬頭</span>
                                <h3 class="text-[18px] font-normal text-slate-900 tracking-[0.2em] leading-[1.6]">
                                    {{ post.title || '無抬頭' }}
                                </h3>
                            </div>

                            <div class="relative">
                                <div class="flex items-center justify-between mb-3">
                                    <span class="app-title block uppercase tracking-widest">開文內容</span>
                                    <div class="relative">
                                        <button @click.stop="openMenuId = (openMenuId === 'exp-' + post.id ? null : 'exp-' + post.id)" class="p-1 hover:bg-slate-100 rounded-lg transition-colors">
                                            <svg class="w-6 h-6 text-slate-400" fill="currentColor" viewBox="0 0 24 24">
                                                <circle cx="5" cy="12" r="2" />
                                                <circle cx="12" cy="12" r="2" />
                                                <circle cx="19" cy="12" r="2" />
                                            </svg>
                                        </button>
                                        
                                        <!-- Actions Menu in Expanded View -->
                                        <div v-if="openMenuId === 'exp-' + post.id" class="absolute right-0 top-full mt-1 w-48 bg-white rounded-2xl shadow-2xl border border-slate-100 z-[200] py-1 animate-fade-in overflow-hidden">
                                            <button @click.stop="toggleExpand(post.id); openMenuId = null" class="w-full px-4 py-3 text-left text-[17px] font-black text-slate-900 hover:bg-slate-50 flex items-center border-b border-slate-50">
                                                收起清單
                                            </button>
                                            <button @click.stop="editItem(post, 'weekly')" class="w-full px-4 py-3 text-left text-[17px] font-black text-slate-900 hover:bg-slate-50 flex items-center border-b border-slate-50">
                                                編輯紀錄
                                            </button>
                                            <button @click.stop="copyAsTextFile(post); openMenuId = null" class="w-full px-4 py-3 text-left text-[17px] font-black text-slate-900 hover:bg-slate-50 flex items-center border-b border-slate-50">
                                                複製 LINE
                                            </button>
                                            <button @click.stop="downloadPost(post); openMenuId = null" class="w-full px-4 py-3 text-left text-[17px] font-black text-slate-900 hover:bg-slate-50 flex items-center border-b border-slate-50">
                                                下載檔案
                                            </button>
                                            <button @click.stop="openPostMode(post, 'weekly')" class="w-full px-4 py-3 text-left text-[17px] font-black text-slate-900 hover:bg-slate-50 flex items-center border-b border-slate-50">
                                                殿中修改之文
                                            </button>
                                            <button @click.stop="confirmDelete(post.id, 'weekly')" class="w-full px-4 py-3 text-left text-[17px] font-black text-red-600 hover:bg-red-50 flex items-center">
                                                刪除紀錄
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <p class="app-body whitespace-pre-wrap leading-[1.6] tracking-widest">{{ post.original_content || '-' }}</p>
                            </div>
                            <div v-if="post.modified_content" class="h-[1px] bg-slate-200"></div>
                            <div v-if="post.modified_content">
                                <span class="text-[14px] font-bold text-red-500 uppercase tracking-widest block mb-3">殿中修改之文</span>
                                <div v-if="post.modified_content.startsWith('data:image')" class="mt-4">
                                    <img :src="post.modified_content" 
                                        @click.stop="viewerImage = post.modified_content"
                                        class="max-w-full md:max-w-xl h-auto rounded-xl border border-purple-100 shadow-lg cursor-zoom-in hover:brightness-95 transition-all" alt="殿中修改之文">
                                </div>
                                <p v-else class="text-[18px] font-bold text-red-600 whitespace-pre-wrap leading-[1.6] tracking-widest">{{ post.modified_content || '-' }}</p>
                            </div>
                            <div v-else-if="post.status === '合格'" class="pt-4">
                                <button @click.stop="openPostMode(post, 'weekly')" class="w-full py-4 bg-purple-50 text-purple-600 rounded-2xl border border-purple-100 font-black text-[17px] tracking-widest hover:bg-purple-100 transition-all flex items-center justify-center">
                                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    上傳殿中貼文圖片
                                </button>
                            </div>
                            
                            <div class="pt-8 pb-12"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Self Post View -->
            <div v-if="currentTab === 'self'" class="space-y-3 max-w-2xl mx-auto">
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
                            <div class="flex flex-col min-w-0">
                                <span class="text-[15px] tracking-tighter mb-0.5" style="color: #0d0d0d !important; font-family: 'Noto Sans TC', sans-serif !important; font-weight: 900 !important;">{{ post.date || '無日期' }}</span>
                                <span v-if="!expandedIds[post.id]" class="app-body text-[17px] tracking-widest" style="font-family: 'Montserrat', sans-serif !important; font-weight: 400 !important; color: #1e293b !important;">{{ getSelfMaster(post.original_content) }}</span>
                                <span v-if="!expandedIds[post.id]" class="app-body text-[17px] tracking-widest" style="font-family: 'Montserrat', sans-serif !important; font-weight: 400 !important; color: #475569 !important;">{{ getSelfPreview(post.original_content) }}</span>
                            </div>
                             <div class="flex items-center space-x-2 self-start">
                                <span :style="`color: ${post.message_type === '玄訊' ? '#16a34a' : post.message_type === '非玄訊' ? '#92400e' : '#94a3b8'} !important`"
                                    class="app-body !font-bold shrink-0 tracking-widest">
                                    {{ post.message_type || '待定' }}
                                </span>
                                
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
                                        <button v-if="post.message_type === '玄訊'" @click.stop="openPostMode(post, 'self')" class="w-full px-4 py-3 text-left text-[17px] font-black text-slate-900 hover:bg-slate-50 flex items-center border-b border-slate-50">
                                            <svg class="w-5 h-5 mr-3 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            殿中修改之文
                                        </button>
                                        <button v-if="!hasAnyExpanded" @click.stop="expandAll" class="w-full px-4 py-3 text-left text-[17px] font-black text-slate-900 hover:bg-slate-50 flex items-center border-b border-slate-50"><svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>展開清單</button>
                                        <button v-else @click.stop="collapseAll" class="w-full px-4 py-3 text-left text-[17px] font-black text-slate-900 hover:bg-slate-50 flex items-center border-b border-slate-50"><svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 12h16"/></svg>收起清單</button>
                                        <button @click.stop="editItem(post, 'self')" class="w-full px-4 py-3 text-left text-[17px] font-black text-slate-900 hover:bg-slate-50 flex items-center border-b border-slate-50">編輯紀錄</button>
                                        <button @click.stop="copyAsTextFile(post); openMenuId = null" class="w-full px-4 py-3 text-left text-[17px] font-black text-slate-900 hover:bg-slate-50 flex items-center border-b border-slate-50">複製 LINE</button>
                                        <button @click.stop="downloadPost(post); openMenuId = null" class="w-full px-4 py-3 text-left text-[17px] font-black text-slate-900 hover:bg-slate-50 flex items-center border-b border-slate-50">下載檔案</button>
                                        <button @click.stop="confirmDelete(post.id, 'self')" class="w-full px-4 py-3 text-left text-[17px] font-black text-red-600 hover:bg-red-50 flex items-center">刪除紀錄</button>
                                    </div>
                                </template>
                                <button v-else @click.stop="toggleExpand(post.id)" class="p-2 hover:bg-slate-100 rounded-full transition-colors">
                                    <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                        </div>
                        
                        <div v-if="expandedIds[post.id]" class="flex-1 bg-slate-50 px-6 pb-6 pt-3 rounded-2xl border border-slate-100 space-y-4 mt-2 animate-fade-in overflow-y-auto" @click.stop>

                            
                            <div class="relative">
                                <div class="flex items-center justify-between mb-3">
                                    <span class="app-title block uppercase tracking-widest">開文內容</span>
                                    <div class="relative">
                                        <button @click.stop="openMenuId = (openMenuId === 'exp-' + post.id ? null : 'exp-' + post.id)" class="p-1 hover:bg-slate-100 rounded-lg transition-colors">
                                            <svg class="w-6 h-6 text-slate-400" fill="currentColor" viewBox="0 0 24 24">
                                                <circle cx="5" cy="12" r="2" />
                                                <circle cx="12" cy="12" r="2" />
                                                <circle cx="19" cy="12" r="2" />
                                            </svg>
                                        </button>
                                        
                                        <!-- Actions Menu in Expanded View -->
                                        <div v-if="openMenuId === 'exp-' + post.id" class="absolute right-0 top-full mt-1 w-48 bg-white rounded-2xl shadow-2xl border border-slate-100 z-[200] py-1 animate-fade-in overflow-hidden">
                                            <button @click.stop="toggleExpand(post.id); openMenuId = null" class="w-full px-4 py-3 text-left text-[17px] font-black text-slate-900 hover:bg-slate-50 flex items-center border-b border-slate-50">
                                                收起清單
                                            </button>
                                            <button @click.stop="editItem(post, 'self')" class="w-full px-4 py-3 text-left text-[17px] font-black text-slate-900 hover:bg-slate-50 flex items-center border-b border-slate-50">
                                                編輯紀錄
                                            </button>
                                            <button v-if="post.message_type === '玄訊'" @click.stop="openPostMode(post, 'self')" class="w-full px-4 py-3 text-left text-[17px] font-black text-slate-900 hover:bg-slate-50 flex items-center border-b border-slate-50">
                                                殿中修改之文
                                            </button>
                                            <button @click.stop="confirmDelete(post.id, 'self')" class="w-full px-4 py-3 text-left text-[17px] font-black text-red-600 hover:bg-red-50 flex items-center">
                                                刪除紀錄
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <p class="app-body whitespace-pre-wrap leading-[1.6] tracking-widest">{{ post.original_content || '-' }}</p>
                            </div>
                            <div v-if="post.modified_content" class="h-[1px] bg-slate-200"></div>
                            <div v-if="post.modified_content && (currentTab === 'weekly' || post.message_type === '玄訊')">
                                <span class="app-title text-red-600 block mb-3 uppercase tracking-widest">殿中修改之文</span>
                                <div v-if="post.modified_content.startsWith('data:image')" class="mt-4">
                                    <img :src="post.modified_content" 
                                        @click.stop="viewerImage = post.modified_content"
                                        class="max-w-full md:max-w-xl h-auto rounded-xl border border-purple-100 shadow-lg cursor-zoom-in hover:brightness-95 transition-all" alt="殿中修改之文">
                                </div>
                                <p v-else class="app-body font-bold text-red-600 whitespace-pre-wrap leading-[1.6] tracking-widest">{{ post.modified_content || '-' }}</p>
                            </div>
                            
                            <div class="pt-8 pb-12"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- FORM VIEW (Full Page) -->
        <div v-else class="flex-1 overflow-y-auto custom-scrollbar bg-white animate-fade-in flex flex-col">

            <div class="flex-1 p-4 md:p-6">
                <div class="max-w-2xl mx-auto space-y-6">

                    <!-- Section 1: Original Content & Meta -->
                    <div class="space-y-4">
                        <div class="space-y-1">
                            <div @click="activeDate = 'date'" class="w-full h-[36px] rounded-lg bg-white border border-slate-200 px-3 flex items-center justify-between cursor-pointer active:bg-slate-50 transition-all mt-5 shadow-sm overflow-hidden">
                                <span :class="form.date ? 'text-slate-900' : 'text-slate-400'" class="leading-tight" style="font-family: 'Noto Sans TC', sans-serif !important; font-weight: 900 !important;">
                                    {{ form.date || '選擇日期' }}
                                </span>
                                <svg class="w-4 h-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </div>
                        </div>

                        <div v-if="addMode === 'self'" class="space-y-1">
                            <label class="ml-1" style="font-family: 'Noto Sans TC', sans-serif !important; font-weight: 900 !important; color: #1e293b !important;">類型</label>
                            <select v-model="form.message_type" class="w-full h-[36px] rounded-lg bg-white border border-slate-200 px-2 outline-none shadow-sm" style="font-family: 'Montserrat', sans-serif !important; font-weight: 400 !important; color: #0f172a !important;">
                                <option value="">待定</option>
                                <option value="玄訊">玄訊</option>
                                <option value="非玄訊">非玄訊</option>
                            </select>
                        </div>

                        <div v-if="addMode === 'weekly'" class="space-y-1">
                            <label class="ml-1" style="font-family: 'Noto Sans TC', sans-serif !important; font-weight: 900 !important; color: #1e293b !important;">審核狀態</label>
                            <div class="flex items-center space-x-5 h-[36px] px-2">
                                <button @click="form.status = null" 
                                    :class="!form.status ? 'text-slate-900 opacity-100' : 'text-slate-400 opacity-40'" 
                                    class="transition-all active:scale-95 tracking-widest text-[17px] font-black">待定</button>
                                <button @click="form.status = '合格'" 
                                    :class="form.status === '合格' ? 'text-[#16a34a] opacity-100' : 'text-slate-400 opacity-40'" 
                                    class="transition-all active:scale-95 tracking-widest text-[17px] font-black">合格</button>
                                <button @click="form.status = '不合格'" 
                                    :class="form.status === '不合格' ? 'text-[#dc2626] opacity-100' : 'text-slate-400 opacity-40'" 
                                    class="transition-all active:scale-95 tracking-widest text-[17px] font-black">不合格</button>
                            </div>
                        </div>

                        <div v-if="addMode === 'weekly'" class="space-y-1">
                            <label class="ml-1" style="font-family: 'Noto Sans TC', sans-serif !important; font-weight: 900 !important; color: #1e293b !important;">抬頭</label>
                            <input v-model="form.title" type="text" placeholder="輸入抬頭..." class="w-full h-[36px] rounded-lg bg-white border border-slate-200 px-3 outline-none shadow-sm focus:border-purple-300 transition-all" style="font-family: 'Montserrat', sans-serif !important; font-weight: 400 !important; color: #0f172a !important;">
                        </div>

                        <div v-if="addMode === 'self'" class="space-y-1">
                            <label class="ml-1" style="font-family: 'Noto Sans TC', sans-serif !important; font-weight: 900 !important; color: #1e293b !important;">仙師</label>
                            <input v-model="form.master_name" list="master-list" placeholder="輸入或選擇仙師..." class="w-full h-[36px] rounded-lg bg-white border border-slate-200 px-3 outline-none shadow-sm focus:border-purple-300 transition-all" style="font-family: 'Montserrat', sans-serif !important; font-weight: 400 !important; color: #0f172a !important;">
                            <datalist id="master-list">
                                <option v-for="m in masters" :key="m.id" :value="m.name"></option>
                            </datalist>
                        </div>

                        <div class="space-y-1">
                            <label class="ml-1" style="font-family: 'Noto Sans TC', sans-serif !important; font-weight: 900 !important; color: #1e293b !important;">開文內容</label>
                            <div v-if="addMode === 'weekly' && !isManualWeekly" class="bg-white rounded-xl border border-slate-100 overflow-hidden shadow-sm">
                                <div v-for="(char, i) in titleChars" :key="i" class="flex items-center bg-white border-b border-slate-50 last:border-0 group">
                                    <div class="w-10 h-9 flex items-center justify-center bg-slate-50/50 border-r border-slate-100 transition-colors" style="font-family: 'Montserrat', sans-serif !important; font-weight: 400 !important; color: #64748b !important;">{{ char }}</div>
                                    <input v-model="weeklyLines[i]" type="text" placeholder="接著輸入..." class="flex-1 h-9 px-3 bg-transparent outline-none placeholder:text-slate-200" style="font-family: 'Montserrat', sans-serif !important; font-weight: 400 !important; color: #0f172a !important;">
                                </div>
                            </div>
                            <div v-else>
                                <textarea v-model="form.original_content" rows="6" placeholder="請輸入開文內容..." class="w-full rounded-xl bg-white border border-slate-200 p-3 outline-none focus:border-purple-300 transition-all leading-[1.4] shadow-sm" style="font-family: 'Montserrat', sans-serif !important; font-weight: 400 !important; color: #0f172a !important;"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Modified Content (Weekly Only) -->
                    <div v-if="addMode === 'weekly'" class="pt-8 mt-8 border-t border-slate-100">
                        <h3 class="app-title text-[20px] mb-4 text-purple-600">殿中貼文內容</h3>
                        <div v-if="isModifiedLocked" class="bg-amber-50 border border-amber-200 rounded-3xl p-8 flex flex-col items-center justify-center text-center space-y-4 py-20">
                            <div class="w-16 h-16 bg-amber-100 text-amber-600 rounded-full flex items-center justify-center shadow-inner">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </div>
                            <div>
                                <h3 class="text-[18px] font-black text-amber-900 mb-1 tracking-widest">功能鎖定中</h3>
                                <p class="text-[15px] font-bold text-amber-700 tracking-wider">有合格才有機會鋪文。<br>請先在列表將此紀錄核定為「合格」。</p>
                            </div>
                        </div>
                        <template v-else>
                            <div class="space-y-2">
                                <label class="app-title ml-1 flex items-center">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    殿中貼文內容 (圖片或文字)
                                </label>
                                <div class="w-full min-h-[300px] rounded-2xl bg-purple-50/30 border-2 border-dashed border-purple-200 relative group overflow-hidden transition-all hover:border-purple-400">
                                    <template v-if="form.modified_content && form.modified_content.startsWith('data:image')">
                                        <div class="p-6 flex flex-col items-center justify-center min-h-[300px] w-full">
                                            <div class="relative group/img">
                                                <img :src="form.modified_content" @click.stop="viewerImage = form.modified_content" class="max-w-full max-h-[400px] rounded-xl shadow-lg border border-purple-100 cursor-zoom-in" alt="預覽">
                                                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover/img:opacity-100 transition-opacity flex items-center justify-center rounded-xl pointer-events-none">
                                                    <span class="text-white font-bold text-[14px]">點擊放大查看</span>
                                                </div>
                                            </div>
                                            <button @click.stop="form.modified_content = ''" class="mt-4 text-red-500 font-bold text-[13px] hover:underline flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                                清除內容並重新貼上
                                            </button>
                                        </div>
                                    </template>
                                    <template v-else>
                                        <textarea v-model="form.modified_content" @paste="onImagePaste" placeholder="在此直接貼上 (Ctrl+V) 文字或截圖..." class="w-full h-full min-h-[300px] bg-transparent p-6 app-body font-bold text-purple-900 outline-none resize-none cursor-text placeholder:text-purple-200 leading-relaxed"></textarea>
                                    </template>
                                    <input id="imageUpload" type="file" accept="image/*" class="hidden" @change="handleImageUpload">
                                </div>
                                <div v-if="!isModifiedLocked && !(form.modified_content && form.modified_content.startsWith('data:image'))" class="flex justify-center mt-4">
                                    <button @click="triggerImageUpload" class="flex items-center space-x-2 px-6 py-3 bg-white border border-purple-200 rounded-2xl text-purple-600 font-black shadow-sm hover:bg-purple-50 transition-all active:scale-95">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h14a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        <span class="app-body">上傳圖片檔案</span>
                                    </button>
                                </div>
                            </div>
                            <datalist id="masterList">
                                <option v-for="m in masters" :key="m.id" :value="m.name"></option>
                            </datalist>
                        </template>
                    </div>

                </div>
            </div>

            <!-- Form Footer (Fixed at bottom) -->
            <div class="p-4 bg-white border-t border-slate-200 shadow-[0_-10px_30px_rgba(0,0,0,0.05)] sticky bottom-0 z-[110]">
                <div class="max-w-2xl mx-auto w-full flex space-x-4">
                    <button @click="addMode = null" class="flex-1 h-[44px] rounded-xl font-black text-[16px] text-slate-500 bg-slate-50 border border-slate-200 hover:bg-slate-100 transition-all active:scale-[0.98] tracking-widest">取消返回</button>
                    <button @click="saveForm" :disabled="isSaving"
                        style="color: #ffffff !important;"
                        class="flex-[2] bg-purple-600 h-[44px] rounded-xl font-black text-[18px] shadow-lg shadow-purple-100 hover:bg-purple-700 active:scale-[0.98] transition-all disabled:bg-slate-300 flex items-center justify-center overflow-hidden tracking-[0.2em]">
                        <template v-if="isSaving">
                            <svg class="animate-spin h-7 w-7 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
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


        <!-- Bottom Navigation Bar (Mobile First) - Height set to 7% of viewport -->
        <div v-if="!addMode" class="fixed bottom-0 inset-x-0 h-[7vh] bg-white/80 backdrop-blur-xl border-t border-slate-200 px-6 z-[100] flex items-center justify-between shadow-[0_-10px_30px_rgba(0,0,0,0.05)] animate-slide-up">
            <button @click="$emit('goHome')" class="h-full px-4 rounded-xl flex items-center justify-center transition-all active:scale-90 text-slate-400">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>

            <button @click="currentTab = 'weekly'" :class="currentTab === 'weekly' ? 'text-purple-600 bg-purple-50' : 'text-slate-400'" class="h-full px-4 rounded-xl flex items-center justify-center transition-all active:scale-90">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>

            <!-- Center Add Button -->
            <button @click.stop="showAddMenu = true" 
                class="h-full px-4 rounded-xl flex items-center justify-center transition-all active:scale-95 text-purple-600 bg-purple-50/50">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 6v6m0 0v6m0-6h6m-6 0H6" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>

            <button @click="currentTab = 'self'" :class="currentTab === 'self' ? 'text-purple-600 bg-purple-50' : 'text-slate-400'" class="h-full px-4 rounded-xl flex items-center justify-center transition-all active:scale-90">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>

            <!-- Font Gear Menu -->
            <div class="flex justify-center relative">
                <button @click="showFontMenu = !showFontMenu" 
                    class="h-full px-4 rounded-xl flex items-center justify-center transition-all text-purple-600 bg-purple-50/50 active:scale-95">
                    <svg class="w-6 h-6 transition-transform duration-500" :class="{'rotate-90': showFontMenu}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
                <div v-if="showFontMenu" class="absolute bottom-full right-0 mb-4 w-16 bg-white rounded-3xl shadow-[0_10px_40px_rgba(0,0,0,0.2)] border border-slate-100 p-4 z-[110] animate-slide-up flex flex-col items-center space-y-4">
                    <div class="h-44 flex flex-col items-center justify-between py-2 relative">
                        <span class="text-[10px] font-black text-slate-300 uppercase tracking-tighter">Large</span>
                        <div class="relative w-1.5 h-32 bg-slate-100 rounded-full overflow-hidden">
                            <input 
                                type="range" 
                                min="0" 
                                max="2" 
                                step="1" 
                                :value="fontSizeValue"
                                @input="handleFontSizeSlider"
                                class="absolute w-32 h-1.5 top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 -rotate-90 appearance-none bg-transparent cursor-pointer [&::-webkit-slider-thumb]:appearance-none [&::-webkit-slider-thumb]:w-4 [&::-webkit-slider-thumb]:h-4 [&::-webkit-slider-thumb]:rounded-full [&::-webkit-slider-thumb]:bg-purple-600"
                            >
                        </div>
                        <span class="text-[10px] font-black text-slate-300 uppercase tracking-tighter">Small</span>
                    </div>
                </div>
            </div>
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
import AddActionMenu from './AddActionMenu.vue';

const getTodayStr = () => {
    const d = new Date();
    return `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`;
};

const emit = defineEmits(['goHome']);

const resetToRoot = () => {
    addMode.value = null;
    searchQuery.value = '';
    showSearch.value = false;
    expandedIds.value = {};
    openMenuId.value = null;
};

const currentTab = ref('weekly'); // 'weekly' or 'self'
const weeklyPosts = ref([]);
const selfPosts = ref([]);
const masters = ref([]);
const loading = ref(false);
const searchQuery = ref('');
const showSearch = ref(false);

const showFontMenu = ref(false);
const showAddMenu = ref(false);
const isManualWeekly = ref(false);

const addActions = computed(() => {
    if (currentTab.value === 'weekly') {
        return [
            { 
                label: '新增每週開文', 
                description: '14字抬頭藏頭詩格式',
                icon: '<path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />',
                bgColor: 'bg-purple-100',
                textColor: 'text-purple-600',
                handler: () => { openAddMode('weekly'); isManualWeekly.value = false; }
            },
            { 
                label: '新增其他文章', 
                description: '自由內容、直接貼上或輸入',
                icon: '<path stroke-linecap="round" stroke-linejoin="round" d="M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 01-2.25 2.25H5.625A2.25 2.25 0 013.375 18V4.625C3.375 4.004 3.879 3.5 4.5 3.5h9.75a.75.75 0 01.75.75v4.5a.75.75 0 01-.75.75h-4.5a.75.75 0 01-.75-.75V3.5" />',
                bgColor: 'bg-amber-100',
                textColor: 'text-amber-600',
                handler: () => { openAddMode('weekly'); isManualWeekly.value = true; }
            }
        ];
    }
    return [
        { 
            label: '新增自行載錄', 
            description: '手動輸入開文內容',
            icon: '<path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />',
            bgColor: 'bg-indigo-100',
            textColor: 'text-indigo-600',
            handler: () => { openAddMode('self'); isManualWeekly.value = false; }
        },
        { 
            label: '新增其他文章', 
            description: '自由內容、直接貼上或輸入',
            icon: '<path stroke-linecap="round" stroke-linejoin="round" d="M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 01-2.25 2.25H5.625A2.25 2.25 0 013.375 18V4.625C3.375 4.004 3.879 3.5 4.5 3.5h9.75a.75.75 0 01.75.75v4.5a.75.75 0 01-.75.75h-4.5a.75.75 0 01-.75-.75V3.5" />',
            bgColor: 'bg-amber-100',
            textColor: 'text-amber-600',
            handler: () => { openAddMode('self'); isManualWeekly.value = true; }
        }
    ];
});

const filteredWeeklyPosts = computed(() => {
    if (!searchQuery.value) return weeklyPosts.value;
    const q = searchQuery.value.toLowerCase();
    return weeklyPosts.value.filter(p => 
        (p.title && p.title.toLowerCase().includes(q)) || 
        (p.original_content && p.original_content.toLowerCase().includes(q)) ||
        (p.modified_content && p.modified_content.toLowerCase().includes(q))
    );
});

const filteredSelfPosts = computed(() => {
    if (!searchQuery.value) return selfPosts.value;
    const q = searchQuery.value.toLowerCase();
    return selfPosts.value.filter(p => 
        (p.original_content && p.original_content.toLowerCase().includes(q)) ||
        (p.modified_content && p.modified_content.toLowerCase().includes(q))
    );
});

// Form States
const addMode = ref(null); // 'weekly' or 'self' or null
const formTab = ref('original'); // 'original' or 'modified'
const form = ref({});
const activeDate = ref(null);
const isSaving = ref(false);
const viewerImage = ref(null);
const activeStatusDropdownId = ref(null);

const currentFontSize = ref(localStorage.getItem('fabou_font_size') || 'font-medium');

const fontSizeValue = computed(() => {
    if (currentFontSize.value === 'font-small') return 0;
    if (currentFontSize.value === 'font-large') return 2;
    return 1;
});

const handleFontSizeSlider = (e) => {
    const val = parseInt(e.target.value);
    const options = ['font-small', 'font-medium', 'font-large'];
    const next = options[val];
    
    document.body.classList.remove('font-small', 'font-medium', 'font-large');
    document.body.classList.add(next);
    localStorage.setItem('fabou_font_size', next);
    currentFontSize.value = next;
};

const formatListTitle = (title) => {
    if (!title) return '無抬頭';
    if (currentFontSize.value !== 'font-large') return title;
    
    // For large font, split every 7 characters
    const clean = title.replace(/\s+/g, '');
    let res = '';
    for (let i = 0; i < clean.length; i += 7) {
        res += clean.substring(i, i + 7) + '\n';
    }
    return res.trim();
};

// 自行開文：從 original_content 解析仙師名（找到「仙師」二字為止）
const getSelfMaster = (content) => {
    if (!content) return '';
    const idx = content.indexOf('仙師');
    if (idx === -1) return '';
    return content.substring(0, idx + 2).trim();
};

// 自行開文：仙師名之後的內容前7字
const getSelfPreview = (content) => {
    if (!content) return '';
    const idx = content.indexOf('仙師');
    if (idx === -1) return content.substring(0, 7);
    const after = content.substring(idx + 2).trim();
    return after.substring(0, 7);
};

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
    if (addMode.value === 'weekly' && !isManualWeekly.value) {
        return titleChars.value.map((char, i) => (char || '') + (weeklyLines.value[i] || '')).join('\n');
    }
    return form.value.original_content;
});

// Permission: Only qualified posts can have modified content (Weekly only)
const isModifiedLocked = computed(() => {
    if (addMode.value === 'weekly') {
        return form.value.status !== '合格';
    }
    if (addMode.value === 'self') {
        return form.value.message_type !== '玄訊';
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
    if (hasAnyExpanded.value) {
        expandedIds.value = {};
    } else if (addMode.value) {
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
        date: getTodayStr(),
        status: null,
        message_type: type === 'self' ? '' : undefined,
        original_content: '',
        modified_content: ''
    };
};

const editItem = (post, type) => {
    addMode.value = type;
    formTab.value = 'original';
    form.value = { ...post };
    
    if (type === 'weekly' && post.original_content) {
        const lines = (post.original_content || '').split('\n');
        const cleanTitle = (form.value.title || '').replace(/\s+/g, '');
        
        // Detect if it's manual or acrostic
        // If it's 14 lines and matches the title characters, it's likely acrostic
        let isAcrostic = lines.length >= 14;
        if (isAcrostic && cleanTitle) {
            for (let i = 0; i < 14; i++) {
                if (cleanTitle[i] && lines[i] && !lines[i].startsWith(cleanTitle[i])) {
                    isAcrostic = false;
                    break;
                }
            }
        }
        
        isManualWeekly.value = !isAcrostic;

        if (isAcrostic) {
            weeklyLines.value = Array(14).fill('').map((_, i) => {
                const fullLine = lines[i] || '';
                const char = cleanTitle[i] || '';
                if (char && fullLine.startsWith(char)) {
                    return fullLine.substring(char.length);
                }
                return fullLine;
            });
        }
    } else {
        isManualWeekly.value = false;
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
    const data = e.clipboardData || (e.originalEvent && e.originalEvent.clipboardData);
    if (!data) return;

    const items = data.items;
    let foundImage = false;

    for (let i = 0; i < items.length; i++) {
        if (items[i].type.indexOf('image') !== -1) {
            const blob = items[i].getAsFile();
            if (blob) {
                processImage(blob);
                foundImage = true;
                break;
            }
        }
    }

    if (!foundImage) {
        const text = data.getData('text');
        if (text) {
            form.value.modified_content = text;
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
    // If user is already focusing on an input or textarea, let the browser handle it
    if (['INPUT', 'TEXTAREA'].includes(document.activeElement.tagName)) {
        return;
    }

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

const copyAsTextFile = (post) => {
    try {
        const text = formatPostForFile(post);
        navigator.clipboard.writeText(text);
        persistentToast.value = { msg: '內容已複製到剪貼簿', type: 'success' };
        setTimeout(() => { persistentToast.value = null; }, 2000);
    } catch (err) {
        console.error('Copy failed:', err);
    }
};

const downloadPost = (post) => {
    const text = formatPostForFile(post);
    const blob = new Blob([text], { type: 'text/plain' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `開文紀錄_${post.date}_${(post.title || '').substring(0, 10)}.txt`;
    a.click();
    window.URL.revokeObjectURL(url);
};

const formatPostForFile = (post) => {
    let res = `日期：${post.date}\n`;
    res += `\n開文內容：\n${post.original_content}\n`;
    if (post.modified_content && !post.modified_content.startsWith('data:image')) {
        res += `\n殿中修改之文：\n${post.modified_content}\n`;
    }
    return res;
};

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
.animate-slide-up { animation: slideUp 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
@keyframes slideUp { from { transform: translateY(100%); } to { transform: translateY(0); } }
</style>
