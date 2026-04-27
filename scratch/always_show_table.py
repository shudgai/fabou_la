import os

path = r'd:\xampp\htdocs\fabou_la\resources\js\components\LuckyDraw.vue'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# 1. Update displayUsers to ALWAYS show the full table, even in confirmation view
content = content.replace(
    """const displayUsers = computed(() => {
    if (!users.value || !Array.isArray(users.value)) return [];
    const pending = pendingNames.value || [];
    if (selectionFiltered.value) {
        return users.value.filter(u => u && u.name && pending.includes(u.name.trim()));
    }
    return users.value.filter(u => u && u.name);
});""",
    """const displayUsers = computed(() => {
    if (!users.value || !Array.isArray(users.value)) return [];
    // Always return all users so the table is always visible
    return users.value.filter(u => u && u.name);
});"""
)

# 2. Update the label and UI in Step 1 to be consistent
content = content.replace(
    """<span class="font-black text-[15px] shrink-0" :style="{ color: selectionFiltered ? '#1d4ed8' : '#94a3b8' }">
                                {{ selectionFiltered ? '已確認名單' : '點選待定法號' }}
                            </span>""",
    """<span class="font-black text-[15px] shrink-0 text-slate-400">
                                全名單表 (可繼續增減)
                            </span>"""
)

# 3. Ensure Manual Add Input is always visible
content = content.replace(
    """<div v-if="!selectionFiltered" class="flex items-center bg-slate-100 rounded-lg px-2 py-1 flex-1 max-w-[150px]">""",
    """<div class="flex items-center bg-slate-100 rounded-lg px-2 py-1 flex-1 max-w-[150px]">"""
)

with open(path, 'w', encoding='utf-8', newline='\r\n') as f:
    f.write(content)

print("LuckyDraw: Step 1 updated to always show the full dharma name table.")
