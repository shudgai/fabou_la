import os

path = r'd:\xampp\htdocs\fabou_la\resources\js\components\TeachingManager.vue'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# Remove the redundant/specific sort block
redundant_block = """        if (group.name === '世代交替' || group.name.includes('直屬弟子') || ['同享皇恩', '享享皇天恩', '殿中之人', '龍鳳閻合脈'].includes(group.name)) {
        group.dharma_names.sort((a, b) => (a.order || 0) - (b.order || 0) || (a.name || '').localeCompare(b.name || '', 'zh-TW', { collation: 'stroke' }));
    }"""

content = content.replace(redundant_block, "")

with open(path, 'w', encoding='utf-8', newline='\r\n') as f:
    f.write(content)

print("Redundant sort block removed.")
