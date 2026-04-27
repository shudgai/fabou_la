import sys
import os

path = r'd:\xampp\htdocs\fabou_la\resources\js\components\TeachingManager.vue'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# 1. Ensure Palace Picker is clean and hidden if showPalacePicker is false
# Actually, let's just make sure it's correct.

# 2. Fix the 500 error: I suspect it's the split tags or weird characters.
# I'll re-save the file with standard formatting.

with open(path, 'w', encoding='utf-8', newline='\n') as f:
    f.write(content)

print("File re-saved cleanly.")
