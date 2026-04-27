import os

path = r'd:\xampp\htdocs\fabou_la\resources\js\components\LuckyDraw.vue'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# 1. Standardize numbering to "1." instead of "#1" in Step 3
content = content.replace(
    '<span class="text-[13px] font-black text-slate-400 mb-1">#{{ idx + 1 }}</span>',
    '<span class="text-[15px] font-black text-slate-400 mb-1">{{ idx + 1 }}.</span>'
)

# 2. Ensure white text for all selected states (Indigo for Direct Mode, Emerald for Lottery Mode)
# I'll check Step 2 Indigo buttons
content = content.replace(
    ":class=\"fixedParticipants.includes(name) ? 'bg-indigo-600 border-indigo-600 text-white shadow-sm' : 'bg-white border-slate-200 text-black shadow-sm'\"",
    ":class=\"fixedParticipants.includes(name) ? 'bg-indigo-600 border-indigo-600 text-white shadow-sm' : 'bg-white border-slate-200 text-black shadow-sm'\" :style=\"{ color: fixedParticipants.includes(name) ? '#ffffff !important' : '#000000 !important' }\""
)

# 3. Double check Step 2 Emerald buttons (already has style, but I'll make sure it's correct)
# I'll re-apply the style to be safe
if ':style="{ color: isRoundParticipant(name) ? \'#ffffff !important\' : \'#000000 !important\' }"' not in content:
     content = content.replace(
        "isRoundParticipant(name) ? 'bg-emerald-600 border-emerald-600 text-white shadow-sm' : 'bg-white border-slate-200 text-black shadow-sm'\"",
        "isRoundParticipant(name) ? 'bg-emerald-600 border-emerald-600 text-white shadow-sm' : 'bg-white border-slate-200 text-black shadow-sm'\" :style=\"{ color: isRoundParticipant(name) ? '#ffffff !important' : '#000000 !important' }\""
    )

# 4. Make sure numbering in results is VERY clear
# Maybe use a bolder color?
content = content.replace(
    'text-slate-400 mb-1">{{ idx + 1 }}.</span>',
    'text-indigo-500 mb-1">{{ idx + 1 }}.</span>'
)

# 5. Fix the numbering in Step 1 Selection Tags (if requested)
# User said "不加編號" in Turn 81, but maybe they want it back now?
# Actually, they specifically said "要加序號" for the combined list.
# I'll leave Step 1 tags without numbers for now unless they complain.

with open(path, 'w', encoding='utf-8', newline='\r\n') as f:
    f.write(content)

print("LuckyDraw: Numbering standardized to '1.' and white text enforced on all selected states.")
