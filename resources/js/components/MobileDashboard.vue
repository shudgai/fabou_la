<template>
    <div class="min-h-screen bg-white">
        <!-- 標題區域: 統一為 12px -->
        <div style="padding: 12px 15px;">
            <h1 style="font-size: 26px; font-weight: 400; color: #1a202c; margin: 0;">皇恩筆記本</h1>
        </div>

        <!-- 條列式專區 -->
        <div class="flex flex-col">
            <button v-for="item in menuItems" :key="item.id" 
                @click="navigate(item.id)"
                class="flex items-center justify-between w-full border-b border-gray-50 active:bg-gray-50 transition-colors"
                style="padding: 16px 15px;">
                <span style="font-size: 18px; font-weight: 400; color: #4a5568; white-space: nowrap;">{{ item.label }}</span>
                <svg class="h-4 w-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </button>
        </div>
    </div>
</template>

<script setup>
const menuItems = [
    { id: 'grace', label: '重大皇恩專區' },
    { id: 'teaching', label: '父皇仙師開示專區' },
    { id: 'grudge', label: '怨靈專區' },
    { id: 'military', label: '軍隊專區' },
    { id: 'treasure', label: '法寶登記專區' },
    { id: 'trash', label: '回收桶' },
];

const emit = defineEmits(['navigate']);
const navigate = (id) => {
    emit('navigate', id);
};
</script>
