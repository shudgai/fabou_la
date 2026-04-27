import os

path = r'd:\xampp\htdocs\fabou_la\resources\js\components\LuckyDraw.vue'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# 1. Update Step 2 Grid: Fixed -> Red with Number, Random -> Emerald without Number
new_direct_grid = """                            <div class="grid grid-cols-4 gap-2">
                                <button v-for="name in selectedNames" :key="'fixed'+name"
                                    @click="toggleFixedParticipant(name)"
                                    class="h-12 flex flex-col items-center justify-center rounded-xl border-2 font-black text-[15px] transition-all active:scale-95 leading-tight shadow-sm"
                                    :class="fixedParticipants.includes(name) ? 'bg-rose-600 border-rose-600 text-white' : 'bg-emerald-500 border-emerald-500 text-white'" 
                                    :style="{ color: '#ffffff !important' }">
                                    <span>{{ name }}</span>
                                    <span v-if="fixedParticipants.includes(name)" class="text-[10px] opacity-90 font-bold uppercase tracking-tighter">
                                        第 {{ fixedParticipants.indexOf(name) + 1 }} 位
                                    </span>
                                </button>
                            </div>"""

import re
# Find the grid in Step 2 Direct Mode
pattern = re.compile(r'<div class="grid grid-cols-4 gap-2">.*?<\/button>\s+<\/div>', re.DOTALL)
content = pattern.sub(new_direct_grid, content)

# 2. Update the summary list colors to match
content = content.replace(
    'label class="text-[15px] font-black text-indigo-500',
    'label class="text-[15px] font-black text-rose-500'
)
content = content.replace(
    'class="bg-indigo-50 text-indigo-700 px-3 py-1.5 rounded-lg font-black text-[17px] border border-indigo-100 flex items-center"',
    'class="bg-rose-50 text-rose-700 px-3 py-1.5 rounded-lg font-black text-[17px] border border-rose-100 flex items-center"'
)
content = content.replace(
    'class="ml-1 text-indigo-300"',
    'class="ml-1 text-rose-300"'
)

# 3. Ensure Random Pool buttons are also solid emerald to distinguish from fixed red
content = content.replace(
    "bg-emerald-50 border-emerald-200 text-emerald-700",
    "bg-emerald-500 border-emerald-500 text-white"
)

with open(path, 'w', encoding='utf-8', newline='\r\n') as f:
    f.write(content)

print("LuckyDraw: Step 2 colors updated. Fixed -> Red (with #), Random -> Emerald (no #).")
