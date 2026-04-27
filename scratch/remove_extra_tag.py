import os

path = r'd:\xampp\htdocs\fabou_la\resources\js\components\LuckyDraw.vue'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# Remove the extra </div> tag before GLOBAL BOTTOM NAVIGATION
content = content.replace(
    '                </div>\n            </div>\n        </div>\n\n        <!-- GLOBAL BOTTOM NAVIGATION',
    '                </div>\n            </div>\n\n        <!-- GLOBAL BOTTOM NAVIGATION'
)

with open(path, 'w', encoding='utf-8', newline='\r\n') as f:
    f.write(content)

print("LuckyDraw: Extra tag removed.")
