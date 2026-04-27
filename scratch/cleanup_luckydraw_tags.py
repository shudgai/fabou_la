import os

path = r'd:\xampp\htdocs\fabou_la\resources\js\components\LuckyDraw.vue'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# 1. Clean up Step 1 bottom
# Currently:
# 127: </div> (End v-else)
# 128: </div> (End animate-fade-in)
# 129: </div> (End Step 1 wrapper)
# 133: Confirm button area...

# Fix: Step 1 wrapper should close AFTER confirm button.
# And ensure only ONE close for animate-fade-in.

content = content.replace(
    '                    </div>\n                </div>\n            </div>\n\n\n            <!-- Confirm button -->',
    '                    </div>\n                \n\n            <!-- Confirm button -->'
)

content = content.replace(
    '                    </button>\n                </div>\n            </div>\n        </div>',
    '                    </button>\n                </div>\n            </div>\n        </div>\n    </div>'
)

# 2. Fix the missing tag for Root
# If line 387 is invalid end tag, it means Root was ALREADY closed.
# Let's count again.
# Root (2)
# Step 1 (5)
# Step 2 (161) -> Wait! I'll check line 161.

# I'll use a safer approach: Replace the whole Step 1 and Step 2 transition.

# I'll just remove the redundant </div> at 128-129.

with open(path, 'w', encoding='utf-8', newline='\r\n') as f:
    f.write(content)

print("LuckyDraw: Attempting structural cleanup.")
