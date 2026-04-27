import os

path = r'd:\xampp\htdocs\fabou_la\resources\js\components\LuckyDraw.vue'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# Update performRoundDraw to respect drawCount
content = content.replace(
    "results.value = shuffled;",
    "results.value = shuffled.slice(0, Math.min(drawCount.value, pool.length));"
)

with open(path, 'w', encoding='utf-8', newline='\r\n') as f:
    f.write(content)

print("performRoundDraw updated to respect drawCount.")
