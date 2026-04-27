import os

path = r'd:\xampp\htdocs\fabou_la\resources\js\components\LuckyDraw.vue'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# 1. Update Step 1 header to show mode name
# This might be tricky because mode name is decided in the middle of the page.
# But I can show it if they click the mode buttons.

content = content.replace(
    '<span class="text-slate-700 font-normal shrink-0 mr-2" style="font-size: 22px !important;">抽順序</span>',
    '<span class="text-slate-700 font-normal shrink-0 mr-2" style="font-size: 22px !important;">抽順序 - {{ lotteryMode ? \'回合抽籤\' : \'直接排列\' }}</span>'
)

# 2. Update Step 2 header to be more descriptive
content = content.replace(
    '<span class="text-slate-700 font-normal shrink-0 mr-2" style="font-size: 22px !important;">{{ lotteryMode ? \'本輪人員挑選\' : \'抽籤設定\' }}</span>',
    '<span class="text-slate-700 font-normal shrink-0 mr-2" style="font-size: 22px !important;">{{ lotteryMode ? \'回合抽籤 - 本輪挑選\' : \'直接排列 - 抽籤設定\' }}</span>'
)

# 3. Update Step 3 header
content = content.replace(
    '<h2 class="text-[19px] font-black text-slate-900 flex-1 text-center">抽籤結果</h2>',
    '<h2 class="text-[19px] font-black text-slate-900 flex-1 text-center">抽籤結果 ({{ lotteryMode ? \'回合抽籤\' : \'直接排列\' }})</h2>'
)

with open(path, 'w', encoding='utf-8', newline='\r\n') as f:
    f.write(content)

print("LuckyDraw: Unit names added to headers.")
