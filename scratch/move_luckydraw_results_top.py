import os

path = r'd:\xampp\htdocs\fabou_la\resources\js\components\LuckyDraw.vue'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# 1. Remove excessive padding and spacing in Step 3
content = content.replace(
    'class="max-w-lg mx-auto space-y-8 pb-32 pt-10"',
    'class="max-w-lg mx-auto space-y-4 pb-32 pt-2"'
)

# 2. Adjust single result spacing to be higher
content = content.replace(
    'class="flex flex-col items-center justify-center space-y-6 animate-scale-in"',
    'class="flex flex-col items-center justify-center space-y-4 animate-scale-in pt-4"'
)

# 3. Adjust crown size slightly if it feels too big/pushy
content = content.replace(
    'text-[64px]',
    'text-[56px]'
)

with open(path, 'w', encoding='utf-8', newline='\r\n') as f:
    f.write(content)

print("LuckyDraw: Step 3 results moved to the top.")
