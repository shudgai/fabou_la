import os

path = r'd:\xampp\htdocs\fabou_la\resources\js\components\TeachingManager.vue'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# 1. Add activeModalGroup.value = group; to triggerGroupSelection
if 'activeModalGroup.value = group;' not in content:
    content = content.replace('const triggerGroupSelection = (group) => {', 'const triggerGroupSelection = (group) => {\n    activeModalGroup.value = group;')

# 2. Add 副宮主 trigger to handleDharmaSearchInput
trigger_code = """    if (val === '宮主' || val === '副宮主' || val === '宮主副宮主') {
        const g = groups.value.find(g => g.name === val);
        if (g) triggerGroupSelection(g);
        return;
    }"""

if "val === '宮主'" not in content:
    content = content.replace('    if (matchedGroup) {\n        triggerGroupSelection(matchedGroup);\n        return;\n    }', '    if (matchedGroup) {\n        triggerGroupSelection(matchedGroup);\n        return;\n    }\n' + trigger_code)

with open(path, 'w', encoding='utf-8', newline='\r\n') as f:
    f.write(content)

print("File updated with triggers and modal fix.")
