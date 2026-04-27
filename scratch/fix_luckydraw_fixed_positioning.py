import os

path = r'd:\xampp\htdocs\fabou_la\resources\js\components\LuckyDraw.vue'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# 1. Force Step 3 to be fixed at the top to resolve the "bottom alignment" issue
content = content.replace(
    'v-if="currentStep === 3" class="flex flex-col w-full h-full bg-white overflow-hidden relative z-[1000]"',
    'v-if="currentStep === 3" class="fixed inset-0 flex flex-col bg-white overflow-hidden z-[1000]"'
)

# 2. Also ensure Step 1 and 2 are consistent
content = content.replace(
    'v-if="currentStep === 1" class="flex flex-col w-full h-full bg-white overflow-hidden relative"',
    'v-if="currentStep === 1" class="fixed inset-0 flex flex-col bg-white overflow-hidden z-[150]"'
)
content = content.replace(
    'v-if="currentStep === 2" class="flex flex-col w-full h-full bg-slate-50/10 overflow-hidden relative"',
    'v-if="currentStep === 2" class="fixed inset-0 flex flex-col bg-white overflow-hidden z-[150]"'
)

# 3. Adjust the Root wrapper to NOT use flex-col if we are using fixed steps
# This prevents any weird stacking issues.
content = content.replace(
    'class="fixed inset-0 z-[150] flex flex-col bg-white overflow-hidden animate-fade-in font-sans"',
    'class="fixed inset-0 z-[100] bg-white overflow-hidden animate-fade-in font-sans"'
)

with open(path, 'w', encoding='utf-8', newline='\r\n') as f:
    f.write(content)

print("LuckyDraw: All steps converted to fixed inset-0 for absolute positioning.")
