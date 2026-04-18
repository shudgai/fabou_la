
import sys

file_path = r'd:\xampp\htdocs\fabou_la\resources\js\components\TeachingManager.vue'
with open(file_path, 'r', encoding='utf-8') as f:
    lines = f.readlines()

# We want to remove lines from line 244 (index 243) to line 441 (index 440)
# But we need to be careful with line numbers after previous edits.
# Let's search for unique markers.

start_marker = '<div class="space-y-2">'
end_marker = '<p class="text-[13px] text-slate-400 mt-3 font-bold">※ 此按鈕將法寶大項與內容物一併存入</p>'

start_idx = -1
end_idx = -1

# Based on previous view_file, the redundant Step 1 starts at line 244.
# Let's look for the first occurrence of Step 1 label *after* line 240.

for i in range(240, len(lines)):
    if 'tempItem.treasure_name' in lines[i] and 'input' in lines[i]:
        # Found the redundant Step 1 input
        # The div starts a few lines up at line 244
        start_idx = 243 # line 244
        break

if start_idx != -1:
    for i in range(start_idx, len(lines)):
        if '此按鈕將法寶大項與內容物一併存入' in lines[i]:
            # Found the end of redundant block
            end_idx = i + 1 # include the closing div
            break

if start_idx != -1 and end_idx != -1:
    print(f"Removing lines from {start_idx+1} to {end_idx+1}")
    del lines[start_idx:end_idx+1] # +1 because there's usually a trailing div

with open(file_path, 'w', encoding='utf-8') as f:
    f.writelines(lines)
