import os

path = r'd:\xampp\htdocs\fabou_la\resources\js\components\LuckyDraw.vue'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# Fix the duplicate :style attribute in Step 2 fixed button
duplicate_style = ':style="{ color: fixedParticipants.includes(name) ? \'#ffffff !important\' : \'#000000 !important\' }" :style="{ color: fixedParticipants.includes(name) ? \'#ffffff !important\' : \'#000000 !important\' }"'
single_style = ':style="{ color: fixedParticipants.includes(name) ? \'#ffffff !important\' : \'#000000 !important\' }"'

if duplicate_style in content:
    content = content.replace(duplicate_style, single_style)
else:
    # Fallback: search for specific line 217-218 pattern
    import re
    content = re.sub(
        r'(:style=".*?").*?\1',
        r'\1',
        content,
        flags=re.DOTALL
    )

with open(path, 'w', encoding='utf-8', newline='\r\n') as f:
    f.write(content)

print("LuckyDraw: Duplicate :style attribute removed.")
