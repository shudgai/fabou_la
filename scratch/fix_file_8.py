import os

path = r'd:\xampp\htdocs\fabou_la\resources\js\components\TeachingManager.vue'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# 1. Update isPositionGroup to include Marshall
content = content.replace("name.includes('副宮主') || name.includes('組')", "name.includes('副宮主') || name.includes('組') || name.includes('元帥')")

# 2. Add Army Order to script
content = content.replace("const palaceOrder = [", "const armyOrder = ['虎寅軍', '虎甲軍', '黑曜軍'];\nconst palaceOrder = [")

# 3. Update activeModalGroupGrouped to handle Army grouping
target_grouped_logic = """    const grouped = [];
    palaceOrder.forEach(pName => {"""

replacement_grouped_logic = """    const grouped = [];
    const currentOrder = name.includes('元帥') ? armyOrder : palaceOrder;
    currentOrder.forEach(pName => {"""

content = content.replace(target_grouped_logic, replacement_grouped_logic)

# 4. Update the filter in activeModalGroupGrouped to check for Army names if needed
target_filter = """            const pIdx = palaceDharmaMapping.value.get(String(m.id));
            return pIdx !== undefined && palaceOrder[pIdx] === pName;"""

replacement_filter = """            if (name.includes('元帥')) {
                // For Marshall group, find which army they belong to
                const groupNames = (m.groups || []).map(g => g.name);
                return groupNames.includes(pName);
            }
            const pIdx = palaceDharmaMapping.value.get(String(m.id));
            return pIdx !== undefined && palaceOrder[pIdx] === pName;"""

content = content.replace(target_filter, replacement_filter)

# 5. Fix the handleDharmaSearchInput to include Marshall keyword
content = content.replace("val === '宮主副宮主'", "val === '宮主副宮主' || val === '元帥' || val === '元帥副元帥'")

with open(path, 'w', encoding='utf-8', newline='\r\n') as f:
    f.write(content)

print("File updated with Army grouping and Marshall keyword trigger.")
