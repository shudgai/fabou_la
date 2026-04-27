import os

path = r'd:\xampp\htdocs\fabou_la\resources\js\components\TeachingManager.vue'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

content = content.replace("'虎寅軍'", "'虎賁軍'")

with open(path, 'w', encoding='utf-8', newline='\r\n') as f:
    f.write(content)

print("TeachingManager updated: 虎寅軍 -> 虎賁軍")
