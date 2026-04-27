import os

path = r'd:\xampp\htdocs\fabou_la\resources\js\components\LuckyDraw.vue'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# Fix the missing </div> for the header bar (Line 8)
# Currently it looks like:
# 48:      </div> (Closes Third Row)
# 49:   </div> (Closes Line 9)
# Missing </div> to close Line 8

content = content.replace(
    '                        </div>\n                </div>\n\n                \n                <!-- MODE SELECTION GATEKEEPER -->',
    '                        </div>\n                    </div>\n                </div>\n\n                \n                <!-- MODE SELECTION GATEKEEPER -->'
)

with open(path, 'w', encoding='utf-8', newline='\r\n') as f:
    f.write(content)

print("LuckyDraw: Header tag balance restored.")
