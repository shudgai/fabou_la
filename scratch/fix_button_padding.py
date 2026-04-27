import os

path = r'd:\xampp\htdocs\fabou_la\resources\js\components\TeachingManager.vue'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# 1. Replace py-[20px] with py-[10px]
content = content.replace('py-[20px]', 'py-[10px]')

# 2. Replace py-4 with py-[10px] for these specific large buttons
# Specifically the one at line 1287
content = content.replace('class="w-full py-4 bg-amber-100', 'class="w-full py-[10px] bg-amber-100')

with open(path, 'w', encoding='utf-8', newline='\r\n') as f:
    f.write(content)

print("Button padding updated: py-[20px]/py-4 -> py-[10px]")
