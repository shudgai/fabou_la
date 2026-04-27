import os

path = r'd:\xampp\htdocs\fabou_la\resources\js\components\TeachingManager.vue'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# 1. Update activeModalGroupGrouped to exempt '世代交替'
content = content.replace(
    "if (!isPositionGroup && palacesFound.size <= 1) return [];",
    "if ((!isPositionGroup && palacesFound.size <= 1) || name === '世代交替') return [];"
)

# 2. Update triggerGroupSelection to sort members for '世代交替'
sort_logic = """    if (group.name === '世代交替') {
        group.dharma_names.sort((a, b) => (a.order || 0) - (b.order || 0) || (a.name || '').localeCompare(b.name || '', 'zh-TW', { collation: 'stroke' }));
    }"""

if 'if (group.name === \'世代交替\') {' not in content:
    content = content.replace(
        "const memberIds = (group.dharma_names || []).map(dn => dn.id);",
        sort_logic + "\n    const memberIds = (group.dharma_names || []).map(dn => dn.id);"
    )

with open(path, 'w', encoding='utf-8', newline='\r\n') as f:
    f.write(content)

print("Modal display for 'Generation Replacement' updated.")
