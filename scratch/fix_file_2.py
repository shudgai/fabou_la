import os

path = r'd:\xampp\htdocs\fabou_la\resources\js\components\TeachingManager.vue'
with open(path, 'r', encoding='utf-8') as f:
    lines = f.readlines()

# Fix 1: Ensure selectedGroupGrouped uses global palaceOrder
# Fix 2: Remove group-hover:text-white/60 if not group
# Fix 3: Optional chaining for safety

new_lines = []
for line in lines:
    # Fix palaceOrder local definition
    if "const palaceOrder = ['玄通宮'" in line and "const palaceOrder =" not in lines[lines.index(line)-1]:
        # Keep only the global one
        if "const palaceDharmaMapping" in "".join(lines[lines.index(line)-10:lines.index(line)]):
            new_lines.append(line)
        else:
            continue
    else:
        # Add optional chaining to selectedGroup.name
        line = line.replace('selectedGroup.name', 'selectedGroup?.name')
        # Add group class to palace picker buttons
        if 'p in palaceOrder' in line and 'class="' in line and 'group' not in line:
            line = line.replace('class="', 'class="group ')
        new_lines.append(line)

with open(path, 'w', encoding='utf-8', newline='\r\n') as f:
    f.writelines(new_lines)

print("File updated via python.")
