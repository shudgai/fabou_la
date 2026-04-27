import os

path = r'd:\xampp\htdocs\fabou_la\resources\js\components\TeachingManager.vue'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# 1. Fix the broken v-else-if syntax errors
# Pattern: v-else-if="!activeModalGroup && !currentMatchedGroup"-if="..."
import re
content = re.sub(r'v-else-if="!activeModalGroup && !currentMatchedGroup"-if="([^"]+)"', r'v-else-if="\1"', content)

# 2. Remove the simple injections
# Pattern: v-else-if="!activeModalGroup && !currentMatchedGroup"
# If it's a simple v-else-if with just that condition, it might be a replacement for v-else.
# But looking at the code, many were originally v-else-if="some_other_condition".
# Let's try to remove it from within quotes first.
content = content.replace('!activeModalGroup && !currentMatchedGroup && ', '')
content = content.replace(' && !activeModalGroup && !currentMatchedGroup', '')

# Handle the case where it was the only condition in v-if/v-else-if
# Be careful here.
# Let's just target the specific one at line 205 which is a template tag.
content = content.replace('<template v-else-if="!activeModalGroup && !currentMatchedGroup">', '<template v-else>')

with open(path, 'w', encoding='utf-8', newline='\r\n') as f:
    f.write(content)

print("Template syntax errors cleaned up.")
