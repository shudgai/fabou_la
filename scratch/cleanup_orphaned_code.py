import os

path = r'd:\xampp\htdocs\fabou_la\resources\js\components\LuckyDraw.vue'
with open(path, 'r', encoding='utf-8') as f:
    lines = f.readlines()

# Identify the range to remove
# 844: if (roundParticipants.value.length === 0) return;
# We want to remove from 844 until we see the next "const" or "function" definition

start_remove = 843 # 0-indexed is 843
end_remove = 843
for i in range(start_remove, len(lines)):
    if 'const performQuickDraw' in lines[i]:
        end_remove = i
        break

# If we didn't find performQuickDraw, we might need a different anchor
if end_remove == 843:
    for i in range(start_remove, len(lines)):
        if 'const performDraw' in lines[i]:
            end_remove = i
            break

if end_remove > start_remove:
    new_lines = lines[:start_remove] + lines[end_remove:]
    with open(path, 'w', encoding='utf-8', newline='\r\n') as f:
        f.writelines(new_lines)
    print(f"LuckyDraw: Removed orphaned code from line {start_remove+1} to {end_remove}.")
else:
    print("LuckyDraw: Could not identify orphaned code range.")
