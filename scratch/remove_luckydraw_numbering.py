import os

path = r'd:\xampp\htdocs\fabou_la\resources\js\components\LuckyDraw.vue'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# 1. Remove index from Pending Tags (Step 1)
content = content.replace(
    '<span class="mr-1 opacity-40 text-[12px] font-bold">{{ pidx + 1 }}.</span>',
    ''
)

# 2. Remove index from Results List (Step 3)
content = content.replace(
    '<span class="text-[13px] font-black text-slate-400 mb-1">#{{ idx + 1 }}</span>',
    ''
)

# 3. Update Copy Results logic (Remove numbering)
content = content.replace(
    "const text = results.value.map((n, i) => `${i + 1}. ${n}`).join('\\n');",
    "const text = results.value.join('\\n');"
)

with open(path, 'w', encoding='utf-8', newline='\r\n') as f:
    f.write(content)

print("LuckyDraw numbering removed from tags and results.")
