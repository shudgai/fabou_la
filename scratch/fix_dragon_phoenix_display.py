import os

path = r'd:\xampp\htdocs\fabou_la\resources\js\components\TeachingManager.vue'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# Add '龍鳳閻合脈' to the existing list
target_groups = ['同享皇恩', '享享皇天恩', '殿中之人', '龍鳳閻合脈']
old_list = "['同享皇恩', '享享皇天恩', '殿中之人']"
new_list = "['" + "', '".join(target_groups) + "']"

content = content.replace(old_list, new_list)

with open(path, 'w', encoding='utf-8', newline='\r\n') as f:
    f.write(content)

print("Modal display for '龍鳳閻合脈' updated.")
