import os

path = r'd:\xampp\htdocs\fabou_la\resources\js\components\LuckyDraw.vue'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# Put all header buttons in a single horizontal row, using vertical text stack to save space
new_header_buttons = """                            <div class="flex items-center space-x-1">
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
                            </div>"""

# Find the old button container and replace it
import re
pattern = r'<div class="flex flex-col items-end space-y-2">.*?</div>\s+</div>\s+</div>'
# Wait, my regex might be too broad. I'll use a string replacement for the container.

content = content.replace(
    """                            <div class="flex flex-col items-end space-y-2">
                                <div class="flex items-center space-x-2">
                                    <button v-if="lotteryMode === false && !selectionFiltered" 
                                        @click="selectionMode = (selectionMode === 'pool' ? 'fixed' : 'pool')"
                                        class="px-3 py-1.5 text-[15px] font-black rounded-xl transition-all shadow-sm border"
                                        :class="selectionMode === 'fixed' ? 'bg-indigo-600 border-indigo-600 text-white' : 'bg-indigo-50 border-indigo-100 text-indigo-600'">
                                        {{ selectionMode === 'fixed' ? '選取中：優先固定' : '切換：選取固定人' }}
                                    </button>
                                    <button v-if="lotteryMode !== null" @click="lotteryMode = null; resetAll(true)" class="px-3 py-1.5 text-[15px] font-black text-blue-700 bg-blue-50 border border-blue-100 rounded-xl shadow-sm">切換模式</button>
                                    <button @click="resetAll" class="p-2 text-slate-400 hover:text-rose-500 transition-colors bg-slate-50 rounded-xl border border-slate-100">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    </button>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <button ontouchstart="" @click="invertSelection(); activeAction = 'invert'" 
                                        :class="[
                                            'py-[6px] px-4 text-[17px] rounded-xl shadow-sm border transition-colors duration-150 font-black',
                                            activeAction === 'invert' ? 'bg-blue-600 border-blue-600 text-white' : 'bg-white border-slate-300 text-black active:bg-slate-400'
                                        ]" :style="{ color: activeAction === 'invert' ? '#ffffff !important' : '' }">反選</button>
                                </div>
                            </div>""",
    new_header_buttons
)

with open(path, 'w', encoding='utf-8', newline='\r\n') as f:
    f.write(content)

print("LuckyDraw: All header buttons unified into a single horizontal row with vertical text stack.")
