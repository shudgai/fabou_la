import os

path = r'd:\xampp\htdocs\fabou_la\resources\js\components\LuckyDraw.vue'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# Fix Step 1 tag balance
# Line 5 (Step 1) and Line 6 (Animate) need to be closed before Step 2.
# Currently only one </div> at 161.

content = content.replace(
    '                </div>\n            </div>\n        </div>\n\n        <!-- STEP 2: DRAW CONFIGURATION',
    '                </div>\n            </div>\n        </div>\n    </div>\n\n        <!-- STEP 2: DRAW CONFIGURATION'
)

with open(path, 'w', encoding='utf-8', newline='\r\n') as f:
    f.write(content)

print("LuckyDraw: Step 1 tag balance fixed.")
