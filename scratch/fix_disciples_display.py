import os

path = r'd:\xampp\htdocs\fabou_la\resources\js\components\TeachingManager.vue'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# 1. Update activeModalGroupGrouped exemption
content = content.replace(
    "|| name === '世代交替') return [];",
    "|| name === '世代交替' || name.includes('直屬弟子')) return [];"
)

# 2. Update triggerGroupSelection to sort members for Disciples
sort_logic = """    if (group.name === '世代交替' || group.name.includes('直屬弟子')) {
        group.dharma_names.sort((a, b) => (a.order || 0) - (b.order || 0) || (a.name || '').localeCompare(b.name || '', 'zh-TW', { collation: 'stroke' }));
    }"""

# Since I already added a version of this in the previous turn, I'll replace it
content = content.replace(
    "if (group.name === '世代交替') {",
    "if (group.name === '世代交替' || group.name.includes('直屬弟子')) {"
)

with open(path, 'w', encoding='utf-8', newline='\r\n') as f:
    f.write(content)

print("Modal display for 'Direct Disciples' groups updated.")
