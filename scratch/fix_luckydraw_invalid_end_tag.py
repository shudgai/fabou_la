import os

path = r'd:\xampp\htdocs\fabou_la\resources\js\components\LuckyDraw.vue'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# Fix the structural error (extra </div> at the end of Step 1)
# I added </button>\n            </div>\n</div>\n        </div> in fix_luckydraw_confirm_visibility.py
# The extra </div> before </div>\n        </div> is causing the issue.

content = content.replace('</button>\n            </div>\n</div>\n        </div>', '</button>\n            </div>\n        </div>')

with open(path, 'w', encoding='utf-8', newline='\r\n') as f:
    f.write(content)

print("LuckyDraw: Extra </div> removed.")
