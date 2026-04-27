import os

path = r'd:\xampp\htdocs\fabou_la\resources\js\components\LuckyDraw.vue'
with open(path, 'r', encoding='utf-8') as f:
    lines = f.readlines()

new_lines = []
skip_old_modes = False
for line in lines:
    if '<!-- MODES HIDDEN ONCE SELECTED -->' in line:
        new_lines.append('                    <!-- Mode Selection (Two Ways) -->\n')
        new_lines.append('                    <div v-if="lotteryMode === null" class="flex-1 flex flex-col justify-center animate-fade-in">\n')
        new_lines.append('                        <div class="text-[19px] font-black text-slate-800 mb-6 text-center">請選擇排列方式：</div>\n')
        new_lines.append('                        <div class="grid grid-cols-1 gap-6 max-w-xs mx-auto w-full px-6">\n')
        new_lines.append('                            <button @click="lotteryMode = false" class="flex flex-col items-center justify-center p-8 rounded-[32px] border-2 transition-all active:scale-95 bg-white border-blue-100 shadow-xl shadow-blue-50/50">\n')
        new_lines.append('                                <svg class="w-12 h-12 mb-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>\n')
        new_lines.append('                                <span class="text-[22px] font-black text-blue-700">直接排列</span>\n')
        new_lines.append('                                <span class="text-[15px] font-bold text-blue-400 opacity-80">依點選順序</span>\n')
        new_lines.append('                            </button>\n')
        new_lines.append('                            <button @click="lotteryMode = true" class="flex flex-col items-center justify-center p-8 rounded-[32px] border-2 transition-all active:scale-95 bg-white border-emerald-100 shadow-xl shadow-emerald-50/50">\n')
        new_lines.append('                                <svg class="w-12 h-12 mb-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.183.319l-3.08 1.914a1 1 0 00.314 1.83l3.593.513a4 4 0 013.11 2.303l.274.549a1 1 0 001.754 0l.274-.549a4 4 0 013.11-2.303l3.593-.513a1 1 0 00.314-1.83l-3.08-1.914z"></path></svg>\n')
        new_lines.append('                                <span class="text-[22px] font-black text-emerald-700">回合抽籤</span>\n')
        new_lines.append('                                <span class="text-[15px] font-bold text-emerald-400 opacity-80">從名單中隨機</span>\n')
        new_lines.append('                            </button>\n')
        new_lines.append('                        </div>\n')
        new_lines.append('                    </div>\n')
        skip_old_modes = True
        continue
    
    if skip_old_modes:
        if '</div>' in line and '</div>' in lines[lines.index(line)-1]: # End of mode block?
            # This is hard to detect reliably with lines.
            # I'll use markers instead.
            pass

with open(path, 'w', encoding='utf-8') as f:
    f.writelines(new_lines)

# Re-doing the surgery with a safer approach
