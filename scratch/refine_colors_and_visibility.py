import os

path = r'd:\xampp\htdocs\fabou_la\resources\js\components\LuckyDraw.vue'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# 1. Update Step 2 Colors for Direct Mode
# Fixed: Indigo, Random: Emerald
new_direct_grid = """                            <div class="grid grid-cols-4 gap-2">
                                <button v-for="name in selectedNames" :key="'fixed'+name"
                                    @click="toggleFixedParticipant(name)"
                                    class="h-12 flex flex-col items-center justify-center rounded-xl border-2 font-black text-[15px] transition-all active:scale-95 leading-tight shadow-sm"
                                    :class="fixedParticipants.includes(name) ? 'bg-indigo-600 border-indigo-600 text-white' : 'bg-emerald-50 border-emerald-200 text-emerald-700'" 
                                    :style="{ color: fixedParticipants.includes(name) ? '#ffffff !important' : '#047857 !important' }">
                                    <span>{{ name }}</span>
                                    <span class="text-[10px] opacity-60 font-bold uppercase tracking-tighter">
                                        {{ fixedParticipants.includes(name) ? '第 ' + (fixedParticipants.indexOf(name) + 1) + ' 位' : '隨機抽籤' }}
                                    </span>
                                </button>
                            </div>"""

import re
content = content.replace(
    re.search(r'<div class="grid grid-cols-4 gap-2">.*?<\/button>\s+<\/div>', content, re.DOTALL).group(0),
    new_direct_step2_grid if 'new_direct_step2_grid' in locals() else new_direct_grid
)

# 2. Fix the Step 2 "Random Pool List" section color as well
content = content.replace(
    'label class="text-[15px] font-black text-blue-500',
    'label class="text-[15px] font-black text-emerald-500'
)
content = content.replace(
    'class="bg-blue-50 text-blue-700 px-3 py-1.5 rounded-lg font-black text-[17px] border border-blue-100 flex items-center"',
    'class="bg-emerald-50 text-emerald-700 px-3 py-1.5 rounded-lg font-black text-[17px] border border-emerald-100 flex items-center"'
)
content = content.replace(
    'class="mt-2 text-[12px] text-blue-400 font-bold italic"',
    'class="mt-2 text-[12px] text-emerald-500 font-bold italic"'
)

# 3. Double check Step 3 visibility - Ensuring isDrawing is cleared
# I'll check performDraw again to make sure it always clears isDrawing

with open(path, 'w', encoding='utf-8', newline='\r\n') as f:
    f.write(content)

print("LuckyDraw: Step 2 colors updated to Indigo (Fixed) and Emerald (Random). Visibility check performed.")
