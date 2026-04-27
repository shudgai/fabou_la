import os

path = r'd:\xampp\htdocs\fabou_la\resources\js\components\LuckyDraw.vue'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# 1. Use v-if instead of v-show for all steps to ensure clean transitions and resolve blank results
content = content.replace(
    '<div v-show="currentStep === 1"',
    '<div v-if="currentStep === 1"'
)
content = content.replace(
    '<div v-show="currentStep === 2"',
    '<div v-if="currentStep === 2"'
)
content = content.replace(
    '<div v-show="currentStep === 3"',
    '<div v-if="currentStep === 3"'
)

# 2. Add a clear debug indicator to Step 3 header to verify it's active
content = content.replace(
    '<h2 class="text-[19px] font-black text-slate-900 flex-1 text-center">抽籤結果 ({{ lotteryMode ? \'回合抽籤\' : \'直接排列\' }})</h2>',
    '<h2 class="text-[19px] font-black text-slate-900 flex-1 text-center">抽籤結果 ({{ results.length }}人)</h2>'
)

# 3. Increase Z-index of Step 3 just in case
content = content.replace(
    'id v-if="currentStep === 3" class="flex flex-col w-full h-full bg-white overflow-hidden relative"',
    'id v-if="currentStep === 3" class="flex flex-col w-full h-full bg-white overflow-hidden relative z-[1000]"'
)
# Note: I'll use a more direct replace for the z-index if the above fails
if 'z-[1000]' not in content:
    content = content.replace(
        'v-if="currentStep === 3" class="flex flex-col w-full h-full bg-white overflow-hidden relative"',
        'v-if="currentStep === 3" class="flex flex-col w-full h-full bg-white overflow-hidden relative z-[1000]"'
    )

with open(path, 'w', encoding='utf-8', newline='\r\n') as f:
    f.write(content)

print("LuckyDraw: Transitioned to v-if for all steps and added Step 3 z-index.")
