import os

path = r'd:\xampp\htdocs\fabou_la\resources\js\components\TeachingManager.vue'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# 1. Remove the 'return null' for '各宮' in currentMatchedGroup
content = content.replace("if (matched && matched.name === '各宮') return null;", "")

# 2. Add sorting to the individual tags mode just in case
target_v_for = 'v-for="id in form.dharma_name_ids"'
replacement_v_for = 'v-for="id in [...form.dharma_name_ids].sort((a,b) => (palaceDharmaMapping.get(String(a)) || 99) - (palaceDharmaMapping.get(String(b)) || 99))"'

# Wait, I should use a computed property for the sorted IDs to avoid inline logic complexity
# Actually, I'll just update the currentMatchedGroup logic to be more inclusive

with open(path, 'w', encoding='utf-8', newline='\r\n') as f:
    f.write(content)

print("Removed special case null return for '各宮' group detection.")
