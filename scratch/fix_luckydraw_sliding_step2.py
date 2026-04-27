import os

path = r'd:\xampp\htdocs\fabou_la\resources\js\components\LuckyDraw.vue'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# 1. Update Step 2 selected button style (Dark Green background, White text)
content = content.replace(
    ":class=\"isRoundParticipant(name) ? 'bg-emerald-50 border-emerald-500 text-emerald-700 shadow-sm' : 'bg-white border-slate-100 text-slate-300'\"",
    ":class=\"isRoundParticipant(name) ? 'bg-emerald-600 border-emerald-600 text-white shadow-sm' : 'bg-white border-slate-100 text-slate-300'\""
)

# 2. Add sliding selection support to Step 2 grid
# I need to find the Step 2 grid container (line 171 approx)
# Add @mouseleave="stopDrag" and @touchmove.prevent="handleTouchMoveStep2"
grid_step2_old = '<div class="grid grid-cols-4 gap-2">'
grid_step2_new = '<div class="grid grid-cols-4 gap-2" @mouseleave="stopDrag" @touchmove.prevent="handleTouchMoveStep2">'
content = content.replace(grid_step2_old, grid_step2_new)

# 3. Add drag events to Step 2 buttons
button_step2_old = '<button v-for="name in selectedNames" :key="\'round\'+name"'
button_step2_new = """<button v-for="name in selectedNames" :key="'round'+name"
                                @mousedown="startDragStep2(name)"
                                @mouseenter="onDragEnterStep2(name)"
                                @mouseup="stopDrag"
                                @touchstart="handleTouchStartStep2($event, name)"
                                @touchend="stopDrag"
                                :data-round-name="name" """
content = content.replace(button_step2_old, button_step2_new)

# 4. Implement Step 2 drag logic in script
script_logic = """
const startDragStep2 = (name) => {
    isDragging.value = true;
    const isSelected = isRoundParticipant(name);
    dragSelectionType.value = isSelected ? 'remove' : 'add';
    toggleRoundParticipant(name);
    window.addEventListener('mouseup', stopDrag);
};

const onDragEnterStep2 = (name) => {
    if (!isDragging.value) return;
    const isSelected = isRoundParticipant(name);
    if (dragSelectionType.value === 'add' && !isSelected) {
        roundParticipants.value.push(name);
    } else if (dragSelectionType.value === 'remove' && isSelected) {
        const idx = roundParticipants.value.indexOf(name);
        roundParticipants.value.splice(idx, 1);
    }
};

const handleTouchStartStep2 = (e, name) => {
    lastTouchedName.value = name;
    startDragStep2(name);
};

const handleTouchMoveStep2 = (e) => {
    if (!isDragging.value) return;
    const touch = e.touches[0];
    const el = document.elementFromPoint(touch.clientX, touch.clientY);
    if (!el) return;
    const btn = el.closest('button[data-round-name]');
    if (btn) {
        const name = btn.getAttribute('data-round-name');
        if (name && name !== lastTouchedName.value) {
            lastTouchedName.value = name;
            onDragEnterStep2(name);
        }
    }
};"""

if 'const startDragStep2' not in content:
    content = content.replace('const startDrag = (name) => {', script_logic + '\n\nconst startDrag = (name) => {')

with open(path, 'w', encoding='utf-8', newline='\r\n') as f:
    f.write(content)

print("LuckyDraw Step 2 sliding selection and white text updated.")
