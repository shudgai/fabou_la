import os

path = r'd:\xampp\htdocs\fabou_la\resources\js\components\TeachingManager.vue'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# 1. Update activeModalGroupGrouped exemption
content = content.replace(
    "|| ['同享皇恩', '享享皇天恩', '殿中之人', '龍鳳閻合脈'].includes(name)",
    "|| ['同享皇恩', '享享皇天恩', '殿中之人', '龍鳳閻合脈', '全體殿生'].includes(name)"
)

# 2. Update triggerGroupSelection sort logic
content = content.replace(
    "|| ['同享皇恩', '享享皇天恩', '殿中之人', '龍鳳閻合脈'].includes(group.name)",
    "|| ['同享皇恩', '享享皇天恩', '殿中之人', '龍鳳閻合脈', '全體殿生'].includes(group.name)"
)

with open(path, 'w', encoding='utf-8', newline='\r\n') as f:
    f.write(content)

print("Modal display for '全體殿生' updated.")
