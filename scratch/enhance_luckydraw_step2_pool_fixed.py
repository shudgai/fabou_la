import os
import re

path = r'd:\xampp\htdocs\fabou_la\resources\js\components\LuckyDraw.vue'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# Enhance Step 2 (Direct Mode) to explicitly show the Random Shuffle Pool
new_direct_mode_step2 = """                    <!-- DIRECT MODE: Fixed Front Positions -->
                    <div v-else class="space-y-8">
                        <!-- Section 1: Fixed People -->
                        <div class="space-y-4">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex flex-col">
                                    <label class="text-[15px] font-black text-slate-400 uppercase tracking-widest">1. 選擇優先固定人員</label>
                                    <span class="text-[12px] text-slate-400 font-bold">點選變為紫色，將固定排在最前面</span>
                                </div>
                                <span class="text-[17px] font-black text-indigo-600">{{ fixedParticipants.length }} 人</span>
                            </div>
                            <div class="grid grid-cols-4 gap-2">
                                <button v-for="name in selectedNames" :key="'fixed'+name"
                                    @click="toggleFixedParticipant(name)"
                                    class="h-12 flex items-center justify-center rounded-xl border-2 font-black text-[17px] transition-all active:scale-95"
                                    :class="fixedParticipants.includes(name) ? 'bg-indigo-600 border-indigo-600 text-white shadow-sm' : 'bg-white border-slate-200 text-black shadow-sm'" 
                                    :style="{ color: fixedParticipants.includes(name) ? '#ffffff !important' : '#000000 !important' }">
                                    {{ name }}
                                </button>
                            </div>
                        </div>

                        <!-- Section 2: Summary of Lists -->
                        <div v-if="fixedParticipants.length > 0 || (selectedNames.length - fixedParticipants.length) > 0" class="pt-6 border-t border-slate-100 space-y-6">
                            <!-- Fixed List -->
                            <div v-if="fixedParticipants.length > 0">
                                <label class="text-[15px] font-black text-indigo-500 uppercase tracking-widest block mb-3">● 優先排列順序 (第 1 ~ {{ fixedParticipants.length }} 名)</label>
                                <div class="flex flex-wrap gap-2">
                                    <div v-for="(name, fidx) in fixedParticipants" :key="'ftag'+name" class="bg-indigo-50 text-indigo-700 px-3 py-1.5 rounded-lg font-black text-[17px] border border-indigo-100 flex items-center">
                                        <span class="mr-1 text-[13px] opacity-40">{{ fidx + 1 }}.</span>
                                        {{ name }}
                                        <button @click="toggleFixedParticipant(name)" class="ml-1 text-indigo-300">×</button>
                                    </div>
                                </div>
                            </div>

                            <!-- Random Pool List -->
                            <div v-if="(selectedNames.length - fixedParticipants.length) > 0">
                                <label class="text-[15px] font-black text-blue-500 uppercase tracking-widest block mb-3">● 隨機抽籤名單 (接在固定人員之後)</label>
                                <div class="flex flex-wrap gap-2">
                                    <div v-for="name in selectedNames.filter(n => !fixedParticipants.includes(n))" :key="'rtag'+name" class="bg-blue-50 text-blue-700 px-3 py-1.5 rounded-lg font-black text-[17px] border border-blue-100 flex items-center">
                                        {{ name }}
                                    </div>
                                </div>
                                <div class="mt-2 text-[12px] text-blue-400 font-bold italic">以上 {{ selectedNames.length - fixedParticipants.length }} 人將進行隨機排序</div>
                            </div>
                        </div>
                    </div>"""

# Safely replace the old Step 2 content
pattern = re.compile(r'<!-- DIRECT MODE: Fixed Front Positions -->.*?</div>\s+</div>\s+</div>', re.DOTALL)
match = pattern.search(content)
if match:
    content = content[:match.start()] + new_direct_mode_step2 + "\n                </div>\n            </div>" + content[match.end():]

with open(path, 'w', encoding='utf-8', newline='\r\n') as f:
    f.write(content)

print("LuckyDraw: Step 2 (Direct Mode) enhanced with explicit random pool display.")
