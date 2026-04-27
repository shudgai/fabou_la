import os

path = r'd:\xampp\htdocs\fabou_la\resources\js\components\LuckyDraw.vue'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# Fix the structural order: Move Step 1 closing tags to AFTER the confirm button
content = content.replace(
    '            </div>\n\n            <!-- Confirm button -->',
    '\n\n            <!-- Confirm button -->'
)

content = content.replace(
    '                </div>\n            </div>\n\n        <!-- STEP 2: DRAW CONFIGURATION',
    '                </div>\n            </div>\n        </div>\n    </div>\n\n        <!-- STEP 2: DRAW CONFIGURATION'
)

with open(path, 'w', encoding='utf-8', newline='\r\n') as f:
    f.write(content)

print("LuckyDraw: Step 1 wrapper correctly closed after confirm button.")
