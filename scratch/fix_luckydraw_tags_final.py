import os

path = r'd:\xampp\htdocs\fabou_la\resources\js\components\LuckyDraw.vue'
with open(path, 'r', encoding='utf-8') as f:
    lines = f.readlines()

# Line numbers are 1-indexed. In the view_file output:
# 127: </div>
# 128: </div>
# 129: </div>
# 131: </div>  <-- Extra
# 157: </div>  <-- Extra

# I'll use a safer string-based approach to avoid line number confusion.
content = "".join(lines)

# Remove the specific extra blocks
content = content.replace('            </div>\n\n            </div>\n\n            <!-- Confirm button -->', '            </div>\n\n            <!-- Confirm button -->')
content = content.replace('                </div>\n            </div>\n        </div>\n\n        <!-- STEP 2: DRAW CONFIGURATION', '                </div>\n            </div>\n\n        <!-- STEP 2: DRAW CONFIGURATION')

with open(path, 'w', encoding='utf-8', newline='\r\n') as f:
    f.write(content)

print("LuckyDraw: Redundant closing </div> tags removed.")
