<template>
    <!-- Symmetrical Rounded Label Logo (Balanced Margins, No Shadows) -->
    <span class="inline-flex items-center justify-center relative transition-transform active:scale-95 group overflow-hidden" 
          :style="`height:${height}px; padding: 0 ${padX}px; min-width: ${height * 2.5}px;` ">
        
        <!-- Background (Rounded Rectangle) -->
        <div class="absolute inset-0 bg-[#FFD700] rounded-2xl border border-amber-500/20"></div>
        
        <div class="relative flex items-center justify-center z-10 w-full">
            <div class="flex items-center space-x-2">
                <!-- Taiji SVG (Shadow Removed) -->
                <svg :width="taijiSize" :height="taijiSize" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg" class="shrink-0">
                    <circle cx="50" cy="50" r="50" fill="white"/>
                    <path d="M50 0C22.3858 0 0 22.3858 0 50C0 77.6142 22.3858 100 50 100C50 75 50 75 50 50C50 25 50 25 50 0Z" fill="white"/>
                    <path d="M50 0C77.6142 0 100 22.3858 100 50C100 77.6142 77.6142 100 50 100V50V0Z" fill="black"/>
                    <path d="M50 100C36.1929 100 25 88.8071 25 75C25 61.1929 36.1929 50 50 50V100Z" fill="black"/>
                    <path d="M50 50C63.8071 50 75 38.8071 75 25C75 11.1929 63.8071 0 50 0V50Z" fill="white"/>
                    <circle cx="50" cy="75" r="8" fill="white"/>
                    <circle cx="50" cy="25" r="8" fill="black"/>
                </svg>
                <!-- Red Text -->
                <span class="font-outfit !text-[20px] md:!text-[24px] !font-black !text-[#dc2626] tracking-tighter leading-none">皇恩筆記本</span>
            </div>
        </div>
    </span>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    height: { type: Number, default: 48 }
});

const taijiSize = computed(() => Math.round(props.height * 0.6));
const padX = computed(() => Math.round((props.height - taijiSize.value) * 1.5));
</script>
