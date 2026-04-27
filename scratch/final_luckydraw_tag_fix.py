import os

path = r'd:\xampp\htdocs\fabou_la\resources\js\components\LuckyDraw.vue'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# Remove the extra </div> at line 283
content = content.replace('        </div>\n    </div>\n    </div>\n\n        <!-- FULLSCREEN', '        </div>\n    </div>\n\n        <!-- FULLSCREEN')

with open(path, 'w', encoding='utf-8', newline='\r\n') as f:
    f.write(content)

print("LuckyDraw: Final structural tag fix for Step 2.")
