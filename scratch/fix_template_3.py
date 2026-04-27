import os

path = r'd:\xampp\htdocs\fabou_la\resources\js\components\TeachingManager.vue'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# Replace the remaining buggy v-else-if with v-else
content = content.replace('v-else-if="!activeModalGroup && !currentMatchedGroup"', 'v-else')

with open(path, 'w', encoding='utf-8', newline='\r\n') as f:
    f.write(content)

print("Remaining template bugs cleaned up.")
