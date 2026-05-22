<template>
    <div class="inline-flex items-center justify-center relative transition-transform active:scale-95 group overflow-hidden" 
          :class="{ 'logo-spin': spinning }"
          :style="`width: ${badgeSize}px; height: ${badgeSize}px;` ">

        <svg class="w-full h-full drop-shadow-sm" viewBox="0 0 240 240" fill="none" xmlns="http://www.w3.org/2000/svg">
            <!-- Golden decorative rings -->
            <circle cx="120" cy="120" r="114" fill="white" stroke="#eab308" stroke-width="4"/>
            <circle cx="120" cy="120" r="108" fill="white" stroke="#eab308" stroke-width="4"/>
            <circle cx="120" cy="120" r="58" fill="white" stroke="#eab308" stroke-width="4"/>

            <!-- Central Taiji -->
            <g transform="translate(70, 70)">
                <circle cx="50" cy="50" r="49" fill="white" stroke="black" stroke-width="2"/>
                <mask id="taiji-mask-vue">
                    <rect x="50" y="-10" width="60" height="120" fill="white"></rect>
                    <circle cx="50" cy="25.5" r="24.5" fill="black"></circle>
                    <circle cx="50" cy="74.5" r="24.5" fill="white"></circle>
                </mask>
                <circle cx="50" cy="50" r="49" fill="black" mask="url(#taiji-mask-vue)"></circle>
                <circle cx="50" cy="74.5" r="8" fill="white"/>
                <circle cx="50" cy="25.5" r="8" fill="black"/>
            </g>

            <!-- Circular Text wrapping around -->
            <defs>
                <path id="badgeArcMobileNav" d="M 71.9, 168.1 A 68,68 0 1,1 168.1, 168.1" />
            </defs>
            <text font-family="'BiauKai', 'DFKai-SB', 'PMingLiU', 'Noto Serif TC', serif" font-weight="900" font-size="38" fill="#dc2626">
                <textPath href="#badgeArcMobileNav" startOffset="50%" text-anchor="middle" letter-spacing="10">
                    皇恩筆記本
                </textPath>
            </text>

            <!-- Bottom decorative elements: 9 dots arranged up and down (zigzag) and spread out -->
            <!-- Inner dots (5) -->
            <circle cx="120" cy="188" r="3.5" fill="#dc2626" />
            <circle cx="120" cy="188" r="3.5" fill="#dc2626" transform="rotate(30, 120, 120)" />
            <circle cx="120" cy="188" r="3.5" fill="#dc2626" transform="rotate(-30, 120, 120)" />
            <circle cx="120" cy="188" r="3.5" fill="#dc2626" transform="rotate(60, 120, 120)" />
            <circle cx="120" cy="188" r="3.5" fill="#dc2626" transform="rotate(-60, 120, 120)" />

            <!-- Outer dots (4) -->
            <circle cx="120" cy="208" r="3.5" fill="#dc2626" transform="rotate(15, 120, 120)" />
            <circle cx="120" cy="208" r="3.5" fill="#dc2626" transform="rotate(-15, 120, 120)" />
            <circle cx="120" cy="208" r="3.5" fill="#dc2626" transform="rotate(45, 120, 120)" />
            <circle cx="120" cy="208" r="3.5" fill="#dc2626" transform="rotate(-45, 120, 120)" />
        </svg>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    height: { type: Number, default: 48 },
    small: { type: Boolean, default: false },
    spinning: { type: Boolean, default: false }
});

const badgeSize = computed(() => {
    const base = Math.max(53, Math.round(props.height * 1.05));
    const scaled = props.small || props.spinning ? Math.round(base * 0.6) : base;
    return scaled;
});
</script>

<style>
.logo-spin {
    animation: logo-spin-keyframes 2.5s linear infinite;
}

@keyframes logo-spin-keyframes {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
</style>
