import os

path = r'd:\xampp\htdocs\fabou_la\resources\js\components\LuckyDraw.vue'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# 1. Update togglePending to ensure deselection always works
# Revised logic: If it's already selected in ANY way, clicking it makes it unselected (white)
new_toggle_pending = """const togglePending = (name) => {
    const isPool = pendingNames.value.includes(name);
    const isFixed = fixedParticipants.value.includes(name);
    
    if (isPool || isFixed) {
        // Already selected (either blue or indigo), so UNSELECT completely (white)
        const pIdx = pendingNames.value.indexOf(name);
        if (pIdx > -1) pendingNames.value.splice(pIdx, 1);
        
        const fIdx = fixedParticipants.value.indexOf(name);
        if (fIdx > -1) fixedParticipants.value.splice(fIdx, 1);
    } else {
        // Not selected, add according to current mode
        if (selectionMode.value === 'fixed') {
            fixedParticipants.value.push(name);
            pendingNames.value.push(name);
        } else {
            pendingNames.value.push(name);
        }
    }
};"""

content = content.replace(
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
};""",
    new_toggle_pending
)

# 2. Increase bottom padding for the scrolling list to clear the navbars
content = content.replace(
    '<div v-else class="flex-1 overflow-y-auto no-scrollbar pb-24 animate-fade-in">',
    '<div v-else class="flex-1 overflow-y-auto no-scrollbar pb-64 animate-fade-in">'
)

with open(path, 'w', encoding='utf-8', newline='\r\n') as f:
    f.write(content)

print("LuckyDraw: togglePending logic improved and bottom padding increased.")
