import os

path = r'd:\xampp\htdocs\fabou_la\resources\js\components\LuckyDraw.vue'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# Remove the redundant closing div tags between Step 2 main body and Action Area
# The correct sequence should end at line 267.
content = content.replace(
    '                    </div>\n                </div>\n            </div>\n                        </div>\n                    </div>\n                </div>\n            </div>',
    '                    </div>\n                </div>\n            </div>'
)

with open(path, 'w', encoding='utf-8', newline='\r\n') as f:
    f.write(content)

print("LuckyDraw: Removed redundant closing tags in Step 2 transition.")
