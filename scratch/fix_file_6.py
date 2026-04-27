import os

path = r'd:\xampp\htdocs\fabou_la\resources\js\components\TeachingManager.vue'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

target = """    palaceOrder.forEach(pName => {
        const members = activeModalGroup.value.dharma_names.filter(m => {
            const pIdx = palaceDharmaMapping.value.get(String(m.id));
            return pIdx !== undefined && palaceOrder[pIdx] === pName;
        });"""

replacement = """    palaceOrder.forEach(pName => {
        const members = activeModalGroup.value.dharma_names.filter(m => {
            if (!form.value.dharma_name_ids.map(id => String(id)).includes(String(m.id))) return false;
            const pIdx = palaceDharmaMapping.value.get(String(m.id));
            return pIdx !== undefined && palaceOrder[pIdx] === pName;
        });"""

content = content.replace(target, replacement)

with open(path, 'w', encoding='utf-8', newline='\r\n') as f:
    f.write(content)
