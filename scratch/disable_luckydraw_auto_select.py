import os

path = r'd:\xampp\htdocs\fabou_la\resources\js\components\LuckyDraw.vue'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# Remove the "Select all by default" logic in loadUsers
content = content.replace(
    "// Select all by default as per user request\n        pendingNames.value = users.value.map(u => u.name);",
    "// Start with empty selection as per user request\n        pendingNames.value = [];"
)

with open(path, 'w', encoding='utf-8', newline='\r\n') as f:
    f.write(content)

print("LuckyDraw: Auto-selection of all participants disabled.")
