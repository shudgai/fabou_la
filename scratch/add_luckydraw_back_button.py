import os

path = r'd:\xampp\htdocs\fabou_la\resources\js\components\LuckyDraw.vue'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# Add "返回挑選" button in Step 1 when list is filtered
new_confirm_buttons = """                <div class="space-y-2">
                    <button
                        v-if="selectionFiltered"
                        @click="selectionFiltered = false"
                        class="w-full py-[10px] rounded-2xl font-black text-[17px] transition-all active:scale-[0.98] border-2 border-slate-200 bg-white text-slate-600 shadow-sm"
                    >
                        返回挑選人員
                    </button>
                    <button
                        v-if="!selectionFiltered && pendingNames.length > 0"
                        @click="performQuickDraw"
                        class="w-full py-[10px] rounded-2xl font-black text-[17px] transition-all active:scale-[0.98] shadow-lg border-2 border-indigo-500 bg-white text-indigo-600"
                    >
                        直接全部隨機排序
                    </button>
                    <button
                        @click="confirmSelection"
                        :disabled="pendingNames.length === 0"
                        class="w-full py-[10px] rounded-2xl font-black text-[17px] transition-all active:scale-[0.98] shadow-none"
                        :style="{
                            background: pendingNames.length === 0 ? '#93c5fd' : (selectionFiltered ? '#16a34a' : '#1d4ed8'),
                            color: '#ffffff'
                        }"
                    >
                        <span v-if="!selectionFiltered" style="color: #ffffff !important;">確定 (已選 {{ pendingNames.length }} 人)</span>
                        <span v-else style="color: #ffffff !important;">確定 → 進入抽籤設定</span>
                    </button>
                </div>"""

content = content.replace(
    """                <div class="space-y-2">
                    <button
                        v-if="!selectionFiltered && pendingNames.length > 0"
                        @click="performQuickDraw"
                        class="w-full py-[10px] rounded-2xl font-black text-[17px] transition-all active:scale-[0.98] shadow-lg border-2 border-indigo-500 bg-white text-indigo-600"
                    >
                        直接全部隨機排序
                    </button>
                    <button
                        @click="confirmSelection"
                        :disabled="pendingNames.length === 0"
                        class="w-full py-[10px] rounded-2xl font-black text-[17px] transition-all active:scale-[0.98] shadow-none"
                        :style="{
                            background: pendingNames.length === 0 ? '#93c5fd' : (selectionFiltered ? '#16a34a' : '#1d4ed8'),
                            color: '#ffffff'
                        }"
                    >
                        <span v-if="!selectionFiltered" style="color: #ffffff !important;">確定 (已選 {{ pendingNames.length }} 人)</span>
                        <span v-else style="color: #ffffff !important;">確定 → 進入抽籤設定</span>
                    </button>
                </div>""",
    new_confirm_buttons
)

with open(path, 'w', encoding='utf-8', newline='\r\n') as f:
    f.write(content)

print("LuckyDraw: 'Back to Selection' button added to Step 1 confirmation area.")
