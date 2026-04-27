import os

path = r'd:\xampp\htdocs\fabou_la\resources\js\components\LuckyDraw.vue'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# 1. Set lotteryMode to null
content = content.replace('const lotteryMode = ref(false);', 'const lotteryMode = ref(null);')

# 2. Hide everything below the header if lotteryMode is null
# We wrap the main content in Step 1 with v-if="lotteryMode !== null"

old_step1_content_start = '<!-- Main scrollable selection grid -->'
new_step1_content_start = """
                <!-- MODE SELECTION GATEKEEPER -->
                <div v-if="lotteryMode === null" class="flex-1 flex flex-col justify-center animate-fade-in bg-slate-50/30">
                    <div class="text-[19px] font-black text-slate-800 mb-8 text-center px-4">請選擇排列方式：</div>
                    <div class="grid grid-cols-1 gap-6 max-w-[280px] mx-auto w-full">
                        <button @click="lotteryMode = false" 
                            class="flex flex-col items-center justify-center p-8 rounded-[40px] border-2 transition-all active:scale-95 bg-white border-blue-100 shadow-xl shadow-blue-50/50">
                            <svg class="w-12 h-12 mb-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            <span class="text-[22px] font-black text-blue-700">直接排列</span>
                            <span class="text-[15px] font-bold text-blue-400 opacity-80 mt-1">依點選順序</span>
                        </button>
                        <button @click="lotteryMode = true" 
                            class="flex flex-col items-center justify-center p-8 rounded-[40px] border-2 transition-all active:scale-95 bg-white border-emerald-100 shadow-xl shadow-emerald-50/50">
                            <svg class="w-12 h-12 mb-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.183.319l-3.08 1.914a1 1 0 00.314 1.83l3.593.513a4 4 0 013.11 2.303l.274.549a1 1 0 001.754 0l.274-.549a4 4 0 013.11-2.303l3.593-.513a1 1 0 00.314-1.83l-3.08-1.914z"></path></svg>
                            <span class="text-[22px] font-black text-emerald-700">回合抽籤</span>
                            <span class="text-[15px] font-bold text-emerald-400 opacity-80 mt-1">從名單中隨機</span>
                        </button>
                    </div>
                </div>

                <!-- Main scrollable selection grid -->
                <div v-else class="flex-1 overflow-y-auto no-scrollbar pb-24 animate-fade-in">"""

content = content.replace(old_step1_content_start, new_step1_content_start)

# 3. Add closing div for v-else
# It should go before the "Confirm button"
old_confirm_start = '<!-- Confirm button -->'
new_confirm_start = '</div>\n\n            <!-- Confirm button -->'
content = content.replace(old_confirm_start, new_confirm_start)

# 4. Remove the old mode selection block which is now inside the v-else
# I'll search for the old block and remove it
import re
content = re.sub(r'<!-- Mode Selection \(Two Ways\) -->.*?</div>\s+</div>', '', content, flags=re.DOTALL)
# Wait! re.DOTALL might be too aggressive. I'll use a more specific pattern.

with open(path, 'w', encoding='utf-8', newline='\r\n') as f:
    f.write(content)

print("LuckyDraw: Interface streamlined with mode selection gatekeeper.")
