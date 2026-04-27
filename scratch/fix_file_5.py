import os

path = r'd:\xampp\htdocs\fabou_la\resources\js\components\TeachingManager.vue'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

content = content.replace('共 {{ selectedGroup.dharma_names.length }} 位人員', '共 {{ activeModalGroup?.dharma_names?.length || 0 }} 位人員')

with open(path, 'w', encoding='utf-8', newline='\r\n') as f:
    f.write(content)
