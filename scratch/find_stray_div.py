
import re

file_path = r'd:\xampp\htdocs\fabou_la\resources\js\components\TeachingManager.vue'
with open(file_path, 'r', encoding='utf-8') as f:
    lines = f.readlines()

stack = []
for i, line in enumerate(lines):
    # Find all div opens
    for m in re.finditer(r'<div[^>]*[^/]>', line):
        stack.append(('open', i+1))
    # Find all div closes
    for m in re.finditer(r'</div>', line):
        if stack:
            stack.pop()
        else:
            print(f"EXTRA CLOSE at line {i+1}")

for s in stack:
    print(f"STILL OPEN: {s[0]} from line {s[1]}")
