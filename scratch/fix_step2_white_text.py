import os

path = r'd:\xampp\htdocs\fabou_la\resources\js\components\LuckyDraw.vue'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# Force white text for Step 2 selected buttons
content = content.replace(
    ":class=\"isRoundParticipant(name) ? 'bg-emerald-600 border-emerald-600 text-white shadow-sm' : 'bg-white border-slate-100 text-slate-300'\"",
    ":class=\"isRoundParticipant(name) ? 'bg-emerald-600 border-emerald-600 text-white shadow-sm' : 'bg-white border-slate-100 text-slate-300'\" :style=\"{ color: isRoundParticipant(name) ? '#ffffff !important' : '' }\""
)

with open(path, 'w', encoding='utf-8', newline='\r\n') as f:
    f.write(content)

print("Step 2 buttons white text forced.")
