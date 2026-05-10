<template>
    <div class="fixed inset-0 z-[2000] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-md animate-fade-in" @click="$emit('close')"></div>
        <div class="relative bg-white rounded-[40px] shadow-2xl w-full max-w-md overflow-hidden animate-slide-up border border-slate-100 flex flex-col max-h-[90dvh]">
            <!-- Header -->
            <div class="p-8 border-b border-slate-50 flex items-center justify-between bg-white shrink-0">
                <div>
                    <h2 class="text-2xl font-black text-slate-900 tracking-tight">個人帳號設定</h2>
                    <p class="text-slate-400 text-[13px] font-bold mt-0.5">管理您的基本資料與密碼</p>
                </div>
                <button @click="$emit('close')" class="p-2 text-slate-300 hover:text-slate-500 active:scale-90 transition-all">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
            </div>

            <!-- Content Area -->
            <div class="flex-1 overflow-y-auto p-8 space-y-8 custom-scrollbar">
                <!-- Info Section -->
                <div class="space-y-4">
                    <div class="flex items-center space-x-4 mb-2">
                        <div class="w-1.5 h-6 bg-indigo-500 rounded-full"></div>
                        <h3 class="text-lg font-black text-slate-900">基本資料 (鎖定)</h3>
                    </div>

                    <div class="grid grid-cols-1 gap-4">
                        <div class="p-5 bg-slate-50/50 rounded-3xl border border-slate-50 flex items-center space-x-4">
                            <div class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center shadow-sm shrink-0">
                                <svg class="w-6 h-6 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </div>
                            <div class="min-w-0">
                                <p class="text-[11px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1.5">綁定法號</p>
                                <p class="text-lg font-black text-slate-900 truncate">{{ user?.dharma_name?.name || user?.name || '未設定' }}</p>
                            </div>
                        </div>

                        <div class="p-5 bg-slate-50/50 rounded-3xl border border-slate-50 flex items-center space-x-4">
                            <div class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center shadow-sm shrink-0">
                                <svg class="w-6 h-6 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </div>
                            <div class="min-w-0">
                                <p class="text-[11px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1.5">登入信箱</p>
                                <p class="text-base font-black text-slate-900 truncate">{{ user?.email }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Password Change Section -->
                <div class="space-y-4">
                    <div class="flex items-center space-x-4 mb-2">
                        <div class="w-1.5 h-6 bg-rose-500 rounded-full"></div>
                        <h3 class="text-lg font-black text-slate-900">更改登入密碼</h3>
                    </div>

                    <div class="space-y-4 p-6 bg-rose-50/30 rounded-[32px] border border-rose-50/50">
                        <div class="space-y-1.5">
                            <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1">目前密碼</label>
                            <input v-model="passForm.current_password" type="password" placeholder="請輸入舊密碼"
                                class="w-full px-5 py-4 bg-white border border-slate-100 rounded-2xl text-[15px] focus:ring-2 focus:ring-rose-200 outline-none transition-all placeholder-slate-300">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1">新密碼</label>
                            <input v-model="passForm.new_password" type="password" placeholder="請輸入新密碼 (至少 8 碼)"
                                class="w-full px-5 py-4 bg-white border border-slate-100 rounded-2xl text-[15px] focus:ring-2 focus:ring-indigo-200 outline-none transition-all placeholder-slate-300">
                        </div>

                        <button @click="updatePassword" :disabled="loading" 
                            class="w-full bg-slate-900 text-white font-black py-4 rounded-2xl shadow-xl shadow-slate-100 hover:bg-slate-800 active:scale-95 transition-all flex justify-center items-center">
                            <span v-if="loading" class="w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
                            <span v-else>確認更改密碼</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Footer Decoration -->
            <div class="p-6 bg-slate-50/50 flex justify-center items-center shrink-0">
                <div class="flex items-center space-x-2">
                    <div class="w-1 h-1 bg-slate-300 rounded-full"></div>
                    <span class="text-[10px] font-black text-slate-300 uppercase tracking-[0.3em]">Secure Account Management</span>
                    <div class="w-1 h-1 bg-slate-300 rounded-full"></div>
                </div>
            </div>
        </div>

        <!-- Success Toast -->
        <div v-if="toast" class="fixed bottom-12 left-1/2 -translate-x-1/2 bg-slate-900 text-white px-8 py-4 rounded-full shadow-2xl z-[3000] flex items-center space-x-3 animate-fade-in whitespace-nowrap">
            <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
            <span class="text-[15px] font-black tracking-tight">{{ toast }}</span>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive } from 'vue';
import axios from 'axios';

const props = defineProps({
    user: Object
});
const emit = defineEmits(['close']);

const loading = ref(false);
const toast = ref(null);
const passForm = reactive({
    current_password: '',
    new_password: ''
});

const showToast = (msg) => {
    toast.value = msg;
    setTimeout(() => { toast.value = null; }, 3000);
};

const updatePassword = async () => {
    if (!passForm.current_password || !passForm.new_password) {
        alert('請填寫所有欄位');
        return;
    }
    if (passForm.new_password.length < 8) {
        alert('新密碼長度至少需要 8 個字元');
        return;
    }

    loading.value = true;
    try {
        await axios.post('/api/user/password', passForm);
        showToast('密碼已成功更新');
        passForm.current_password = '';
        passForm.new_password = '';
        // Close after success? User choice. Let's wait a bit.
        setTimeout(() => emit('close'), 2000);
    } catch (e) {
        alert(e.response?.data?.message || '密碼更新失敗，請確認目前密碼是否正確');
    } finally {
        loading.value = false;
    }
};
</script>

<style scoped>
@keyframes slide-up {
    from { transform: translateY(50px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}
.animate-slide-up { animation: slide-up 0.4s cubic-bezier(0.16, 1, 0.3, 1); }
@keyframes fade-in { from { opacity: 0; } to { opacity: 1; } }
.animate-fade-in { animation: fade-in 0.3s ease-out; }
.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
</style>
