import os

path = r'd:\xampp\htdocs\fabou_la\resources\js\components\LuckyDraw.vue'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# 1. Force white color for the "46 人" badge
content = content.replace(
    '<span class="px-2 py-0.5 bg-blue-600 text-white text-[11px] font-black rounded-full">{{ pendingNames.length }} 人</span>',
    '<span class="px-2 py-0.5 bg-blue-600 text-white text-[11px] font-black rounded-full" style="color: white !important;">{{ pendingNames.length }} 人</span>'
)

# 2. Update dharma buttons selected style (Blue background, White text)
content = content.replace(
    "backgroundColor: '#bfdbfe', // Light Blue for Selected\n            color: '#000000',",
    "backgroundColor: '#1d4ed8', // Dark Blue for Selected\n            color: '#ffffff',"
)
content = content.replace(
    "border: '2px solid #93c5fd'",
    "border: '2px solid #1e40af'"
)

with open(path, 'w', encoding='utf-8', newline='\r\n') as f:
    f.write(content)

print("LuckyDraw buttons text color updated to white.")
