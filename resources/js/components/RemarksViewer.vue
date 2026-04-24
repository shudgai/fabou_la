<template>
    <Teleport to="body">
        <div v-if="isOpen"
             class="fixed inset-0 z-[400] flex items-end md:items-center justify-center p-0 md:p-4"
             @click.self="close">
            <!-- Backdrop -->
            <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity duration-300" 
                 :class="isOpen ? 'opacity-100' : 'opacity-0'"
                 @click.stop="close"></div>
            
            <!-- Modal Container -->
            <div class="relative bg-white w-full max-w-lg md:rounded-[32px] rounded-t-[32px] p-6 shadow-2xl flex flex-col max-h-[85vh] animate-slide-up-fancy transition-all"
                 @click.stop>
                
                <div class="w-12 h-1.5 bg-slate-100 rounded-full mx-auto mb-4 md:hidden"></div>

                <!-- Header -->
                <div class="flex items-center justify-between mb-4">
                    <div class="flex flex-col">
                        <h3 class="text-[19px] font-black font-outfit text-slate-800">詳細備註內容</h3>
                        <p class="text-[12px] font-bold" :class="isActuallyEditing ? 'text-orange-500' : 'text-slate-400'">
                            {{ isActuallyEditing ? '正在編輯人員備註...' : '檢視備註內容' }}
                        </p>
                    </div>
                    <div class="flex items-center space-x-2">
                        <button v-if="editable && !isActuallyEditing" 
                                @click="isActuallyEditing = true"
                                class="flex items-center space-x-1 px-3 py-1.5 bg-indigo-50 text-indigo-600 rounded-full text-[13px] font-black active:scale-95 transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            <span>修改</span>
                        </button>
                        <button @click.stop="close"
                                class="text-slate-300 hover:text-slate-600 p-2 -mr-2 active:scale-90 transition-all">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M6 18L18 6M6 6l12 12" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Content Area -->
                <div class="flex-1 overflow-y-auto min-h-[160px] md:min-h-[200px] p-4 rounded-2xl bg-slate-50/50 border border-slate-100">
                    <textarea
                        v-if="isActuallyEditing"
                        v-model="localContent"
                        autofocus
                        class="w-full h-full min-h-[150px] bg-transparent text-[17px] font-black font-outfit text-slate-800 leading-relaxed outline-none resize-none placeholder-slate-300"
                        placeholder="請輸入詳細備註..."></textarea>
                    <div v-else
                         class="text-[17px] font-black font-outfit text-slate-800 leading-relaxed whitespace-pre-wrap">
                        {{ localContent || '（無備註內容）' }}
                    </div>
                </div>

                <!-- Footer Actions -->
                <div class="mt-6 flex gap-3 pb-2 md:pb-0" style="padding-bottom: env(safe-area-inset-bottom);">
                    <button v-if="isActuallyEditing"
                            @click.stop="save"
                            class="flex-[2] py-4 bg-orange-500 text-white rounded-2xl font-black text-[18px] active:scale-95 transition-all font-outfit shadow-lg shadow-orange-100 border-b-4 border-orange-600">
                        確認並儲存
                    </button>
                    <button @click.stop="close"
                            class="flex-1 py-4 bg-slate-100 text-slate-600 rounded-2xl font-black text-[18px] active:scale-95 transition-all font-outfit border-b-4 border-slate-200">
                        {{ isActuallyEditing ? '取消編輯' : '關閉' }}
                    </button>
                </div>
            </div>
        </div>
    </Teleport>
</template>

<script setup>
import { ref, watch, computed } from 'vue';

const props = defineProps({
    modelValue: { type: Boolean, default: false },
    remarks:    { type: [String, Array], default: '' },
    editable:   { type: Boolean, default: false }, // 是否允許編輯
    forceEdit:  { type: Boolean, default: false }, // 是否強制一開啟就進入編輯模式
    dnrId:      { type: [Number, String], default: null }
});

const emit = defineEmits(['update:modelValue', 'save']);

const isOpen            = ref(false);
const localContent      = ref('');
const isActuallyEditing = ref(false);

// Sync local state when modal opens or remarks change
watch(() => props.modelValue, (val) => {
    if (val) {
        const r = props.remarks;
        localContent.value = Array.isArray(r) ? r.join('\n') : (r || '');
        isActuallyEditing.value = props.forceEdit;
        isOpen.value = true;
    } else {
        isOpen.value = false;
        isActuallyEditing.value = false;
    }
});

// 關鍵修復：當背景資料（props.remarks）更新時，同步更新 localContent (若非正在編輯中)
watch(() => props.remarks, (newVal) => {
    if (!isActuallyEditing.value) {
        localContent.value = Array.isArray(newVal) ? newVal.join('\n') : (newVal || '');
    }
}, { deep: true });

const close = () => {
    isOpen.value = false;
    emit('update:modelValue', false);
};

const save = () => {
    emit('save', { dnrId: props.dnrId, content: localContent.value });
    // 注意：不要在這裡立即 close，讓父組件處理完並透過 prop 更新後再由 watch 關閉或更新
};
</script>

<style scoped>
.animate-slide-up-fancy {
    animation: slideUpFancy 0.35s cubic-bezier(0.34, 1.56, 0.64, 1) both;
}
@keyframes slideUpFancy {
    from { opacity: 0; transform: translateY(100%) scale(0.95); }
    to   { opacity: 1; transform: translateY(0) scale(1); }
}
</style>
