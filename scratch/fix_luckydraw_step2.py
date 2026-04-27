import os

path = r'd:\xampp\htdocs\fabou_la\resources\js\components\LuckyDraw.vue'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# 1. Update font size for buttons in Step 2 (Participant Selection)
content = content.replace(
    'class="h-12 flex items-center justify-center rounded-xl border-2 font-black transition-all active:scale-95"',
    'class="h-12 flex items-center justify-center rounded-xl border-2 font-black text-[17px] transition-all active:scale-95"'
)

# 2. Make "Draw Count" box visible in both modes
# Remove v-if="!lotteryMode"
content = content.replace(
    '<div v-if="!lotteryMode" class="flex items-center justify-between px-1">',
    '<div class="flex items-center justify-between px-1">'
)

# 3. Update Draw Count input logic to use roundParticipants in Lottery Mode
# The input blur logic: @blur="drawCount = Math.max(1, Math.min(selectedNames.length, drawCount || 1))"
# The buttons: Math.min(selectedNames.length, drawCount + 1)

content = content.replace(
    "@blur=\"drawCount = Math.max(1, Math.min(selectedNames.length, drawCount || 1))\"",
    "@blur=\"drawCount = Math.max(1, Math.min(lotteryMode ? roundParticipants.length : selectedNames.length, drawCount || 1))\""
)

content = content.replace(
    "Math.min(selectedNames.length, drawCount + 1)",
    "Math.min(lotteryMode ? roundParticipants.length : selectedNames.length, drawCount + 1)"
)

# 4. Update selected tags font size in Step 2
content = content.replace(
    'class="bg-indigo-50 text-indigo-700 px-3 py-1 rounded-lg font-black text-[15px] border border-indigo-100 flex items-center"',
    'class="bg-indigo-50 text-indigo-700 px-3 py-1.5 rounded-lg font-black text-[17px] border border-indigo-100 flex items-center"'
)

with open(path, 'w', encoding='utf-8', newline='\r\n') as f:
    f.write(content)

print("LuckyDraw interface updated: Font size 17px and Draw Count visible in all modes.")
