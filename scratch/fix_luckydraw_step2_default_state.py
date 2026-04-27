import os

path = r'd:\xampp\htdocs\fabou_la\resources\js\components\LuckyDraw.vue'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# 1. Revert pre-population of roundParticipants in confirmSelection
# Old logic from Turn 84:
# if (lotteryMode.value) { roundParticipants.value = [...selectedNames.value]; drawCount.value = roundParticipants.value.length; }

revert_logic = """    if (lotteryMode.value) {
        roundParticipants.value = [];
        drawCount.value = 1;
    }"""

content = content.replace(
    """    if (lotteryMode.value) {
        roundParticipants.value = [...selectedNames.value];
        drawCount.value = roundParticipants.value.length;
    }""",
    revert_logic
)

# 2. Update Step 2 button style for "Unselected" state
# Currently: bg-white border-slate-100 text-slate-300
# User wants "白底黑字" (White background, Black text)
content = content.replace(
    "isRoundParticipant(name) ? 'bg-emerald-600 border-emerald-600 text-white shadow-sm' : 'bg-white border-slate-100 text-slate-300'",
    "isRoundParticipant(name) ? 'bg-emerald-600 border-emerald-600 text-white shadow-sm' : 'bg-white border-slate-200 text-black shadow-sm'"
)

# 3. Update the inline style to only apply white text when selected
content = content.replace(
    ":style=\"{ color: isRoundParticipant(name) ? '#ffffff !important' : '' }\"",
    ":style=\"{ color: isRoundParticipant(name) ? '#ffffff !important' : '#000000 !important' }\""
)

with open(path, 'w', encoding='utf-8', newline='\r\n') as f:
    f.write(content)

print("LuckyDraw: Step 2 default state changed to white background/black text.")
