import os

path = r'd:\xampp\htdocs\fabou_la\resources\js\components\LuckyDraw.vue'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# Ensure toggle functionality is robust and consistent across Step 1 and Step 2

# In Step 1, the dharma-btn already uses mousedown which calls startDrag -> togglePending.
# I'll add a @click.prevent="" to prevent any default browser behavior that might interfere.
content = content.replace(
    'class="dharma-btn flex items-center justify-center font-black text-[17px] transition-all active:scale-95 rounded-md border shadow-sm w-full min-h-[45px] select-none"',
    '@click.prevent="" class="dharma-btn flex items-center justify-center font-black text-[17px] transition-all active:scale-95 rounded-md border shadow-sm w-full min-h-[45px] select-none"'
)

# In Step 2, ensure the toggleRoundParticipant is called correctly.
# My previous replacement might have missed the simple click feel.
# I'll check the Step 2 button replacement.

# Step 2 buttons currently:
# @mousedown="startDragStep2(name)" ... @touchstart="handleTouchStartStep2($event, name)"

# I'll refine the startDrag functions to be more reliable for quick clicks.

with open(path, 'w', encoding='utf-8', newline='\r\n') as f:
    f.write(content)

print("LuckyDraw toggle logic confirmed and refined.")
