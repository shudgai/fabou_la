import os

path = r'd:\xampp\htdocs\fabou_la\resources\js\components\LuckyDraw.vue'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# Redesign header for "Horizontal Row with Vertical Stacked Text" - but make it more premium and compact
# The user seems to want them side-by-side (Horizontal) and stacked text (Vertical)
# But maybe they want the text to be NORMALLY oriented? 
# "三個按鈕要橫向的, 然後直式排列"
# Let's try: A single horizontal row, but the buttons are tall capsules with stacked characters.
# I will refine the spacing to make it look less "stretched".

new_header_buttons = """                            <div class="flex items-start space-x-1.5 pt-1">
                                <button ontouchstart="" @click="invertSelection(); activeAction = 'invert'" 
                                    :class="[
                                        'w-10 py-2.5 rounded-2xl shadow-sm border transition-all duration-150 font-black flex flex-col items-center justify-center leading-none space-y-0.5',
                                        activeAction === 'invert' ? 'bg-blue-600 border-blue-600 text-white' : 'bg-white border-slate-200 text-black active:bg-slate-100'
                                    ]" :style="{ color: activeAction === 'invert' ? '#ffffff !important' : '' }">
                                    <span class="text-[15px]">反</span><span class="text-[15px]">選</span>
                                </button>
                                <button v-if="lotteryMode === false && !selectionFiltered" 
                                    @click="selectionMode = (selectionMode === 'pool' ? 'fixed' : 'pool')"
                                    class="w-10 py-2.5 font-black rounded-2xl transition-all shadow-sm border flex flex-col items-center justify-center leading-none space-y-0.5"
                                    :class="selectionMode === 'fixed' ? 'bg-indigo-600 border-indigo-600 text-white' : 'bg-indigo-50 border-indigo-100 text-indigo-600'">
                                    <span class="text-[13px]">優</span><span class="text-[13px]">先</span><span class="text-[13px]">固</span><span class="text-[13px]">定</span>
                                </button>
                                <button v-if="lotteryMode !== null" @click="lotteryMode = null; resetAll(true)" 
                                    class="w-10 py-2.5 font-black text-blue-700 bg-blue-50 border border-blue-100 rounded-2xl shadow-sm flex flex-col items-center justify-center leading-none space-y-0.5">
                                    <span class="text-[13px]">切</span><span class="text-[13px]">換</span><span class="text-[13px]">模</span><span class="text-[13px]">式</span>
                                </button>
                                <button @click="resetAll" class="w-10 h-10 text-slate-400 hover:text-rose-500 transition-colors bg-slate-50 rounded-2xl border border-slate-100 flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </button>
                            </div>"""

content = content.replace(
    """                            <div class="flex items-center space-x-1">
                                <button ontouchstart="" @click="invertSelection(); activeAction = 'invert'" 
                                    :class="[
                                        'px-1.5 py-2 text-[14px] rounded-xl shadow-sm border transition-all duration-150 font-black flex flex-col items-center justify-center leading-tight',
                                        activeAction === 'invert' ? 'bg-blue-600 border-blue-600 text-white' : 'bg-white border-slate-300 text-black active:bg-slate-400'
                                    ]" :style="{ color: activeAction === 'invert' ? '#ffffff !important' : '' }">
                                    <span>反</span><span>選</span>
                                </button>
                                <button v-if="lotteryMode === false && !selectionFiltered" 
                                    @click="selectionMode = (selectionMode === 'pool' ? 'fixed' : 'pool')"
                                    class="px-1.5 py-2 text-[14px] font-black rounded-xl transition-all shadow-sm border flex flex-col items-center justify-center leading-tight"
                                    :class="selectionMode === 'fixed' ? 'bg-indigo-600 border-indigo-600 text-white' : 'bg-indigo-50 border-indigo-100 text-indigo-600'">
                                    <span>切</span><span>換</span><span>固</span><span>定</span>
                                </button>
                                <button v-if="lotteryMode !== null" @click="lotteryMode = null; resetAll(true)" 
                                    class="px-1.5 py-2 text-[14px] font-black text-blue-700 bg-blue-50 border border-blue-100 rounded-xl shadow-sm flex flex-col items-center justify-center leading-tight">
                                    <span>切</span><span>換</span><span>模</span><span>式</span>
                                </button>
                                <button @click="resetAll" class="p-1.5 text-slate-400 hover:text-rose-500 transition-colors bg-slate-50 rounded-xl border border-slate-100 flex items-center justify-center">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </button>
                            </div>""",
    new_header_buttons
)

with open(path, 'w', encoding='utf-8', newline='\r\n') as f:
    f.write(content)

print("LuckyDraw: Header buttons refined to Horizontal Row with compact Vertical Text Stack.")
