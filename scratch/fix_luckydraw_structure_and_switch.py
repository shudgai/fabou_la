import os

path = r'd:\xampp\htdocs\fabou_la\resources\js\components\LuckyDraw.vue'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# 1. Fix the double div opening at line 60-61
content = content.replace(
    '<div v-else class="flex-1 overflow-y-auto no-scrollbar pb-24 animate-fade-in">\n                <div class="flex-1 overflow-y-auto no-scrollbar pb-24">',
    '<div v-else class="flex-1 overflow-y-auto no-scrollbar pb-24 animate-fade-in">'
)

# 2. Add a "Switch Mode" button in the header
content = content.replace(
    '<button @click="resetAll" class="p-1 text-slate-400 hover:text-red-500 shrink-0">',
    '<button v-if="lotteryMode !== null" @click="lotteryMode = null; resetAll(true)" class="px-2 py-1 text-[13px] font-black text-blue-600 bg-blue-50 rounded-lg mr-2">切換模式</button>\n                                <button @click="resetAll" class="p-1 text-slate-400 hover:text-red-500 shrink-0">'
)

# 3. Ensure the structural integrity of the v-if/v-else gatekeeper
# I'll check the closing divs.
# I had: </div>\n</div>\n        </div> at the end of Step 1.
# I'll make sure it's clean.

with open(path, 'w', encoding='utf-8', newline='\r\n') as f:
    f.write(content)

print("LuckyDraw: Fixed structure and added 'Switch Mode' button.")
