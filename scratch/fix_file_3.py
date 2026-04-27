import os

path = r'd:\xampp\htdocs\fabou_la\resources\js\components\TeachingManager.vue'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# 1. Fix the remaining script issues
content = content.replace('const name = selectedGroup.value.name', 'const name = activeModalGroup.value.name')
content = content.replace('selectedGroup.value = group;', 'activeModalGroup.value = group;')

# 2. Fix the template issues
# Replace selectedGroup with currentMatchedGroup in the tags display
content = content.replace('<template v-if="selectedGroup">', '<template v-if="currentMatchedGroup">')
content = content.replace('群組：{{ formatGroupName(selectedGroup?.name) }}', '群組：{{ formatGroupName(currentMatchedGroup?.name) }}')
content = content.replace('({{ selectedGroup.dharma_names.length }}人)', '({{ currentMatchedGroup.dharma_names.length }}人)')
content = content.replace('@click="showGroupMembersModal = true"', '@click="activeModalGroup = currentMatchedGroup; showGroupMembersModal = true"')

# 3. Update the modal template to use activeModalGroup
content = content.replace('v-if="showGroupMembersModal && selectedGroup"', 'v-if="showGroupMembersModal && activeModalGroup"')
content = content.replace('群組成員：{{ selectedGroup?.name }}', '群組成員：{{ activeModalGroup?.name }}')
content = content.replace('v-if="selectedGroupGrouped.length > 0"', 'v-if="activeModalGroupGrouped.length > 0"')
content = content.replace('v-for="pg in selectedGroupGrouped"', 'v-for="pg in activeModalGroupGrouped"')
content = content.replace('v-else-if="selectedGroup"', 'v-else-if="activeModalGroup"')
content = content.replace('v-for="member in selectedGroup.dharma_names"', 'v-for="member in activeModalGroup.dharma_names"')

# 4. Final safety checks
content = content.replace('activeModalGroup.value.name', "activeModalGroup.value?.name || ''")

with open(path, 'w', encoding='utf-8', newline='\r\n') as f:
    f.write(content)

print("File updated successfully via python script.")
