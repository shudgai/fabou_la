import os

path = r'd:\xampp\htdocs\fabou_la\resources\js\components\LuckyDraw.vue'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# Move the closing div of v-else to after the confirm button
content = content.replace('</div>\n\n            <!-- Confirm button -->', '<!-- Confirm button -->')
content = content.replace('</button>\n            </div>\n        </div>', '</button>\n            </div>\n</div>\n        </div>')

with open(path, 'w', encoding='utf-8', newline='\r\n') as f:
    f.write(content)

print("LuckyDraw: Confirm button also hidden until mode selection.")
