import os

path = r'd:\xampp\htdocs\fabou_la\resources\js\components\LuckyDraw.vue'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# Define missing Step 2 helper functions
new_functions = """
const toggleFixedParticipant = (name) => {
    const idx = fixedParticipants.value.indexOf(name);
    if (idx > -1) {
        fixedParticipants.value.splice(idx, 1);
    } else {
        fixedParticipants.value.push(name);
    }
};

const toggleRoundParticipant = (name) => {
    const idx = roundParticipants.value.indexOf(name);
    if (idx > -1) {
        roundParticipants.value.splice(idx, 1);
    } else {
        roundParticipants.value.push(name);
    }
};

const isRoundParticipant = (name) => roundParticipants.value.includes(name);
"""

# Insert before Animation Logic
content = content.replace('// Animation Logic', new_functions + '\n// Animation Logic')

# Refine Step 2 (Direct Mode) Grid to be clearer about "Fixed" vs "Random"
new_direct_step2_grid = """                            <div class="grid grid-cols-4 gap-2">
                                <button v-for="name in selectedNames" :key="'fixed'+name"
                                    @click="toggleFixedParticipant(name)"
                                    class="h-12 flex flex-col items-center justify-center rounded-xl border-2 font-black text-[15px] transition-all active:scale-95 leading-tight"
                                    :class="fixedParticipants.includes(name) ? 'bg-indigo-600 border-indigo-600 text-white shadow-sm' : 'bg-blue-50 border-blue-200 text-blue-700 shadow-sm'" 
                                    :style="{ color: fixedParticipants.includes(name) ? '#ffffff !important' : '#1d4ed8 !important' }">
                                    <span>{{ name }}</span>
                                    <span class="text-[10px] opacity-60 font-bold uppercase tracking-tighter">
                                        {{ fixedParticipants.includes(name) ? '第 ' + (fixedParticipants.indexOf(name) + 1) + ' 位' : '隨機抽籤' }}
                                    </span>
                                </button>
                            </div>"""

import re
content = content.replace(
    re.search(r'<div class="grid grid-cols-4 gap-2">.*?<\/button>\s+<\/div>', content, re.DOTALL).group(0),
    new_direct_step2_grid
)

with open(path, 'w', encoding='utf-8', newline='\r\n') as f:
    f.write(content)

print("LuckyDraw: Step 2 helper functions added and Direct Mode grid refined.")
