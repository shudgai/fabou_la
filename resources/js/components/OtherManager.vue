<template>
    <div class="h-full bg-white flex flex-col md:flex-row">
        <!-- Sidebar: Folder List -->
        <div class="w-full md:w-64 border-r border-slate-100 flex flex-col pt-4">
            <div class="px-4 mb-6 flex items-center justify-between">
                <h2 class="text-xl font-bold text-slate-800">其他專區</h2>
            </div>

            <div class="flex-grow overflow-y-auto space-y-1 px-2">
                <button v-for="folder in folders" :key="folder.id" 
                    @click="activeFolderId = folder.id"
                    :class="['w-full text-left px-4 py-3 rounded-xl transition-all flex items-center group', 
                            activeFolderId === folder.id ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-50']">
                    <div class="w-2 h-2 rounded-full mr-3" :style="{ backgroundColor: folder.color || '#6366f1' }"></div>
                    <span class="font-medium truncate flex-grow">{{ folder.name }}</span>
                </button>
            </div>
        </div>

        <!-- Main Content: Record List -->
        <div class="flex-grow flex flex-col bg-slate-50/50 overflow-y-auto">
            <div v-if="activeFolder" class="h-full">
                <!-- Special View: 開文核定表 -->
                <kaiwen-approval v-if="activeFolder.name.includes('開文核定')" />
                <!-- Special View: 隨機分組 -->
                <random-group v-else-if="activeFolder.name.includes('隨機分組')" />
                
                <!-- Default View: Standard Records -->
                <div v-else class="max-w-4xl mx-auto w-full p-6">
                    <div class="flex items-center justify-between mb-8">
                        <h1 class="text-2xl font-black text-slate-900">{{ activeFolder.name }}</h1>
                        <button @click="showAddRecord = true" class="bg-indigo-600 text-white px-4 py-2 rounded-xl font-bold shadow-lg shadow-indigo-100 hover:bg-indigo-700 transition-all flex items-center text-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            新增紀錄
                        </button>
                    </div>

                    <div class="space-y-4">
                        <div v-for="record in activeFolder.other_records" :key="record.id" class="bg-white p-6 rounded-[24px] shadow-sm border border-slate-100 hover:shadow-md transition-shadow relative group">
                            <button @click="deleteRecord(record.id)" class="absolute top-4 right-4 opacity-0 group-hover:opacity-100 text-slate-300 hover:text-red-500 transition-opacity">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </button>
                            
                            <div class="flex items-center text-slate-400 text-xs font-bold uppercase tracking-widest mb-2">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                {{ formatDate(record.record_date) || formatDate(record.created_at) }}
                            </div>

                            <h3 v-if="record.title" class="text-lg font-bold text-slate-800 mb-2">{{ record.title }}</h3>
                            <p class="text-slate-600 whitespace-pre-wrap leading-relaxed">{{ record.content }}</p>
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
                <div class="flex space-x-3">
                    <button @click="showAddFolder = false" class="flex-grow py-4 rounded-2xl font-bold text-slate-400 hover:bg-slate-50 transition-colors">取消</button>
                    <button @click="saveFolder" class="flex-grow py-4 rounded-2xl font-bold bg-indigo-600 text-white shadow-lg shadow-indigo-100 hover:bg-indigo-700 transition-all">建立</button>
                </div>
            </div>
        </div>

        <!-- Add Record Modal -->
        <div v-if="showAddRecord" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-[40px] p-8 w-full max-w-xl shadow-2xl">
                <h3 class="text-xl font-bold mb-6">新增記事</h3>
                <input v-model="newRecord.title" placeholder="標題 (選填)" class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl mb-4 focus:ring-2 focus:ring-indigo-500/20 transition-all font-medium">
                <textarea v-model="newRecord.content" rows="6" placeholder="內容..." class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl mb-6 focus:ring-2 focus:ring-indigo-500/20 transition-all font-medium resize-none"></textarea>
                <div class="flex space-x-3">
                    <button @click="showAddRecord = false" class="flex-grow py-4 rounded-2xl font-bold text-slate-400 hover:bg-slate-50 transition-colors">取消</button>
                    <button @click="saveRecord" class="flex-grow py-4 rounded-2xl font-bold bg-indigo-600 text-white shadow-lg shadow-indigo-100 hover:bg-indigo-700 transition-all">儲存記事</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';
import KaiwenApproval from './KaiwenApproval.vue';
import RandomGroup from './RandomGroup.vue';

const folders = ref([]);
const activeFolderId = ref(null);
const showAddFolder = ref(false);
const showAddRecord = ref(false);

const newFolderName = ref('');
const newFolderColor = ref('#6366f1');
const newRecord = ref({ title: '', content: '', record_date: new Date().toISOString().split('T')[0] });

const activeFolder = computed(() => folders.value.find(f => f.id === activeFolderId.value));

const loadData = async () => {
    const res = await axios.get('/other-folders');
    folders.value = res.data;
    
    // Auto-initialize if empty or missing required folders
    const hasKaiwen = folders.value.some(f => f.name.includes('開文核定'));
    const hasRandom = folders.value.some(f => f.name.includes('隨機分組'));

    if (!hasKaiwen || !hasRandom) {
        if (!hasKaiwen) await axios.post('/other-folders', { name: '開文核定表', color: '#6366f1' });
        if (!hasRandom) await axios.post('/other-folders', { name: '隨機分組', color: '#10b981' });
        return loadData();
    }

    if (folders.value.length > 0 && !activeFolderId.value) {
        const kaiwen = folders.value.find(f => f.name.includes('開文核定'));
        activeFolderId.value = kaiwen ? kaiwen.id : folders.value[0].id;
    }
};

const saveFolder = async () => {
    if (!newFolderName.value) return;
    await axios.post('/other-folders', { name: newFolderName.value, color: newFolderColor.value });
    newFolderName.value = '';
    showAddFolder.value = false;
    await loadData();
};

const deleteFolder = async (id) => {
    if (!confirm('確定要刪除整個資料夾嗎？內容也會被刪除。')) return;
    await axios.delete(`/other-folders/${id}`);
    if (activeFolderId.value === id) activeFolderId.value = null;
    await loadData();
};

const saveRecord = async () => {
    if (!newRecord.value.content) return;
    await axios.post(`/other-folders/${activeFolderId.value}/records`, newRecord.value);
    newRecord.value = { title: '', content: '', record_date: new Date().toISOString().split('T')[0] };
    showAddRecord.value = false;
    await loadData();
};

const deleteRecord = async (id) => {
    if (!confirm('確定要刪除此記事嗎？')) return;
    await axios.delete(`/other-records/${id}`);
    await loadData();
};

const formatDate = (dateString) => {
    if (!dateString) return '';
    return new Date(dateString).toLocaleDateString('zh-TW', { year: 'numeric', month: '2-digit', day: '2-digit' }).replace(/\//g, '.');
};

onMounted(loadData);
</script>
