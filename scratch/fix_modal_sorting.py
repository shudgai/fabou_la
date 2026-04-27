import os

path = r'd:\xampp\htdocs\fabou_la\resources\js\components\TeachingManager.vue'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# 1. Define getRoleOrder
role_order_func = """const getRoleOrder = (member) => {
    const groupNames = (member.groups || []).map(g => g.name);
    if (groupNames.includes('宮主')) return 1;
    if (groupNames.includes('副宮主')) return 2;
    return 3;
};"""

if 'const getRoleOrder' not in content:
    content = content.replace('const stripMasterPrefix = (name) => {', role_order_func + '\n\nconst stripMasterPrefix = (name) => {')

# 2. Update triggerGroupSelection sorting
# For palace groups, we want to sort by Role Order then by Name Order
sort_logic = """    // Sort by Role (Master > Vice > Member) then by Palace/Name Order
    group.dharma_names.sort((a, b) => {
        const roleA = getRoleOrder(a);
        const roleB = getRoleOrder(b);
        if (roleA !== roleB) return roleA - roleB;
        return (a.order || 0) - (b.order || 0) || (a.name || '').localeCompare(b.name || '', 'zh-TW', { collation: 'stroke' });
    });"""

# Replace the previous sort_logic from Turn 69
old_logic = "if (group.name === '世代交替' || group.name.includes('直屬弟子') || ['同享皇恩', '享享皇天恩', '殿中之人', '龍鳳閻合脈'].includes(group.name)) {"
if old_logic in content:
    # Make it apply to ALL groups that show in modal
    content = content.replace(
        "const memberIds = (group.dharma_names || []).map(dn => dn.id);",
        sort_logic + "\n    const memberIds = (group.dharma_names || []).map(dn => dn.id);"
    )

with open(path, 'w', encoding='utf-8', newline='\r\n') as f:
    f.write(content)

print("Modal sorting logic updated: Master > Vice > Member.")
