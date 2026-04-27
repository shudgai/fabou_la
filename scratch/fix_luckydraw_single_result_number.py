import os

path = r'd:\xampp\htdocs\fabou_la\resources\js\components\LuckyDraw.vue'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# Add numbering to single result case
content = content.replace(
    '{{ results[0] }}</h3>',
    '1. {{ results[0] }}</h3>'
)

with open(path, 'w', encoding='utf-8', newline='\r\n') as f:
    f.write(content)

print("LuckyDraw: Numbering added to single result case.")
