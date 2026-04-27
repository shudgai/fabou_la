import os

path = r'd:\xampp\htdocs\fabou_la\resources\js\components\LuckyDraw.vue'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# 1. Add selectionMode ref
content = content.replace(
    'const lotteryMode = ref(null);',
    'const lotteryMode = ref(null);\nconst selectionMode = ref("pool"); // "pool" or "fixed"'
)

# 2. Add Toggle in Header for Direct Mode
header_toggle = """                                <button v-if="lotteryMode === false && !selectionFiltered" 
                                    @click="selectionMode = (selectionMode === 'pool' ? 'fixed' : 'pool')"
                                    class="px-3 py-1 text-[13px] font-black rounded-lg mr-2 transition-all"
                                    :class="selectionMode === 'fixed' ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-600'">
                                    {{ selectionMode === 'fixed' ? '正在選取：優先固定' : '切換選取：優先固定' }}
                                </button>"""

content = content.replace(
    '<button v-if="lotteryMode !== null" @click="lotteryMode = null; resetAll(true)" class="px-2 py-1 text-[13px] font-black text-blue-600 bg-blue-50 rounded-lg mr-2">切換模式</button>',
    header_toggle + '\n                                <button v-if="lotteryMode !== null" @click="lotteryMode = null; resetAll(true)" class="px-2 py-1 text-[13px] font-black text-blue-600 bg-blue-50 rounded-lg mr-2">切換模式</button>'
)

# 3. Update togglePending logic to handle selectionMode
content = content.replace(
    """const togglePending = (name) => {
    const idx = pendingNames.value.indexOf(name);
    if (idx === -1) {
        pendingNames.value.push(name);
    } else {
        pendingNames.value.splice(idx, 1);
    }
};""",
    """const togglePending = (name) => {
    if (selectionMode.value === 'fixed') {
        const idx = fixedParticipants.value.indexOf(name);
        if (idx > -1) {
            fixedParticipants.value.splice(idx, 1);
        } else {
            fixedParticipants.value.push(name);
            // Also ensure it is in the pool
            if (!pendingNames.value.includes(name)) pendingNames.value.push(name);
        }
    } else {
        const idx = pendingNames.value.indexOf(name);
        if (idx === -1) {
            pendingNames.value.push(name);
        } else {
            pendingNames.value.splice(idx, 1);
            // Also remove from fixed if removed from pool
            const fidx = fixedParticipants.value.indexOf(name);
            if (fidx > -1) fixedParticipants.value.splice(fidx, 1);
        }
    }
};"""
)

# 4. Update getPendingStyle to show Fixed color
content = content.replace(
    "const isSelected = pendingNames.value.includes(t);",
    "const isSelected = pendingNames.value.includes(t);\n    const isFixed = fixedParticipants.value.includes(t);"
)

content = content.replace(
    "if (isSelected) {",
    "if (isFixed) {\n        return {\n            backgroundColor: '#4f46e5',\n            color: '#ffffff !important',\n            border: '2px solid #4338ca'\n        };\n    } else if (isSelected) {"
)

# 5. Show fixed priority order in Step 1
content = content.replace(
    '已選定人員 (基礎名單)',
    '{{ selectionMode === "fixed" ? "優先固定人員 (照點選順序)" : "已選定人員 (基礎名單)" }}'
)

# 6. Update Confirm button logic to bypass Step 2 if fixed are already selected?
# No, Step 2 is still useful for drawCount.

with open(path, 'w', encoding='utf-8', newline='\r\n') as f:
    f.write(content)

print("LuckyDraw: Hybrid selection mode added to Step 1.")
