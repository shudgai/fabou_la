import os

path = r'd:\xampp\htdocs\fabou_la\resources\js\components\LuckyDraw.vue'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# 1. Fix the Header Layout (Step 1)
# Move the three buttons into a vertical stack
old_header_buttons = """                            <div class="flex items-center space-x-2">
                                <div class="flex items-center space-x-1 flex-1 max-w-[80px]">
                                    <button ontouchstart="" @click="invertSelection(); activeAction = 'invert'" 
                                        :class="[
                                            'flex-1 py-[6px] px-3 text-[17px] rounded-lg shadow-sm border transition-colors duration-150',
                                            activeAction === 'invert' ? 'bg-blue-600 border-blue-600 text-white' : 'bg-white border-slate-300 text-black active:bg-slate-400'
                                        ]" :style="{ color: activeAction === 'invert' ? '#ffffff !important' : '' }">反選</button>
                                </div>
                                                                <button v-if="lotteryMode === false && !selectionFiltered" 
                                    @click="selectionMode = (selectionMode === 'pool' ? 'fixed' : 'pool')"
                                    class="px-3 py-1 text-[13px] font-black rounded-lg mr-2 transition-all"
                                    :class="selectionMode === 'fixed' ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-600'">
                                    {{ selectionMode === 'fixed' ? '正在選取：優先固定' : '切換選取：優先固定' }}
                                </button>
                                <button v-if="lotteryMode !== null" @click="lotteryMode = null; resetAll(true)" class="px-2 py-1 text-[13px] font-black text-blue-600 bg-blue-50 rounded-lg mr-2">切換模式</button>
                                <button @click="resetAll" class="p-1 text-slate-400 hover:text-red-500 shrink-0">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </button>
                            </div>"""

new_header_buttons = """                            <div class="flex flex-col items-end space-y-2">
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
                            </div>"""

content = content.replace(old_header_buttons, new_header_buttons)

# 2. Fix the Step 2 tag mismatch (missing </div> for animate-slide-in)
content = content.replace(
    '                    </button>\n                </div>\n            </div>\n        </div>',
    '                    </button>\n                </div>\n            </div>\n        </div>\n    </div>'
)

# 3. Double check Step 1 closing tags
# Line 155 currently closes line 5. Correct.
# Line 383 closes line 2. Correct.

with open(path, 'w', encoding='utf-8', newline='\r\n') as f:
    f.write(content)

print("LuckyDraw: Header buttons rearranged to vertical and Step 2 tags fixed.")
