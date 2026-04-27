import os

path = r'd:\xampp\htdocs\fabou_la\resources\js\components\TeachingManager.vue'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# 1. Update activeModalGroupGrouped exemption
# Add the new groups to the list
new_groups = ['同享皇恩', '享享皇天恩', '殿中之人']
exemption_condition = "|| name === '世代交替' || name.includes('直屬弟子')"
new_exemption = exemption_condition + " || ['" + "', '".join(new_groups) + "'].includes(name)"

content = content.replace(exemption_condition, new_exemption)

# 2. Update triggerGroupSelection to sort members for these groups
sort_condition = "if (group.name === '世代交替' || group.name.includes('直屬弟子')) {"
new_sort_condition = "if (group.name === '世代交替' || group.name.includes('直屬弟子') || ['" + "', '".join(new_groups) + "'].includes(group.name)) {"

content = content.replace(sort_condition, new_sort_condition)

with open(path, 'w', encoding='utf-8', newline='\r\n') as f:
    f.write(content)

print("Modal display for specific groups (Imperial Grace, Palace People) updated.")
