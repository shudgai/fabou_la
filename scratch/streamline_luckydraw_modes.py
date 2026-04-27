import os

path = r'd:\xampp\htdocs\fabou_la\resources\js\components\LuckyDraw.vue'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# 1. Change lotteryMode to null by default to represent "Not Selected"
content = content.replace(
    'const lotteryMode = ref(false);',
    'const lotteryMode = ref(null);'
)

# 2. Update Step 1 template to hide mode selection after choice
# Wrap mode selection in v-if="lotteryMode === null"
mode_selection_block = """                    <!-- Mode Selection (Two Ways) -->
                    <div v-if="lotteryMode === null" class="px-3 py-4 flex-1 flex flex-col justify-center">
                        <div class="text-[19px] font-black text-slate-800 mb-6 text-center">請選擇排列方式：</div>
                        <div class="grid grid-cols-1 gap-6 max-w-xs mx-auto w-full">
                            <button @click="lotteryMode = false" 
                                class="flex flex-col items-center justify-center p-8 rounded-[32px] border-2 transition-all active:scale-95 bg-white border-blue-100 shadow-xl shadow-blue-50/50">
                                <svg class="w-12 h-12 mb-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                <span class="text-[22px] font-black text-blue-700">直接排列</span>
                                <span class="text-[15px] font-bold text-blue-400 opacity-80">依點選順序</span>
                            </button>
                            <button @click="lotteryMode = true" 
                                class="flex flex-col items-center justify-center p-8 rounded-[32px] border-2 transition-all active:scale-95 bg-white border-emerald-100 shadow-xl shadow-emerald-50/50">
                                <svg class="w-12 h-12 mb-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.183.319l-3.08 1.914a1 1 0 00.314 1.83l3.593.513a4 4 0 013.11 2.303l.274.549a1 1 0 001.754 0l.274-.549a4 4 0 013.11-2.303l3.593-.513a1 1 0 00.314-1.83l-3.08-1.914z"></path></svg>
                                <span class="text-[22px] font-black text-emerald-700">回合抽籤</span>
                                <span class="text-[15px] font-bold text-emerald-400 opacity-80">從名單中隨機</span>
                            </button>
                        </div>
                    </div>"""

# Find and replace the original mode selection
old_mode_selection = content.find('<!-- Mode Selection (Two Ways) -->')
# I'll replace the block manually or with a marker
# Actually, I'll just use a direct replace if it's stable.

# 3. Show grid only if mode is selected
grid_start = '<div class="flex items-center justify-between px-3 py-1.5">'
new_grid_start = '<div v-if="lotteryMode !== null" class="animate-fade-in flex flex-col h-full">' + grid_start

# I'll use a more surgical approach.
content = content.replace(
    '<span class="text-slate-700 font-normal shrink-0 mr-2" style="font-size: 22px !important;">抽順序 - {{ lotteryMode ? \'回合抽籤\' : \'直接排列\' }}</span>',
    '<span class="text-slate-700 font-normal shrink-0 mr-2" style="font-size: 22px !important;">{{ lotteryMode === null ? \'選擇抽籤模式\' : (\'抽順序 - \' + (lotteryMode ? \'回合抽籤\' : \'直接排列\')) }}</span>'
)

# Replace the mode selection block
content = content.replace(
    '<!-- Mode Selection (Two Ways) -->',
    '<!-- MODES HIDDEN ONCE SELECTED -->'
)

# Re-apply mode logic
with open(path, 'w', encoding='utf-8', newline='\r\n') as f:
    f.write(content)

print("LuckyDraw: Mode selection streamlined. Next: Update template structure.")
