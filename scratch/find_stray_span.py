
import re

file_path = r'd:\xampp\htdocs\fabou_la\resources\js\components\TeachingManager.vue'
with open(file_path, 'r', encoding='utf-8') as f:
    lines = f.readlines()

stack = []
for i, line in enumerate(lines):
    for m in re.finditer(r'<span[^>]*[^/]>', line):
        stack.append(('open', i+1))
    for m in re.finditer(r'</span>', line):
        if stack:
            stack.pop()
        else:
            print(f"EXTRA CLOSE at line {i+1}")

for s in stack:
    print(f"STILL OPEN: {s[0]} from line {s[1]}")
