import os

path = r'd:\xampp\htdocs\fabou_la\resources\js\components\LuckyDraw.vue'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# 1. Add fixedParticipants ref and update headers for Direct Mode Step 2
content = content.replace(
    "const roundParticipants = ref([]);",
    "const roundParticipants = ref([]);\nconst fixedParticipants = ref([]);"
)

# 2. Update Step 2 UI for Direct Mode (Add Fixed selection)
direct_mode_config_old = """                    <!-- DIRECT MODE: Original Draw Count -->
                    <div v-else class="space-y-6">
                        <div class="space-y-1.5 px-1 pt-1">
                            <div class="flex items-center justify-between mb-2">
                                <label class="text-[15px] font-black text-slate-400 uppercase tracking-widest">📋 當前待抽名單</label>
                                <span class="text-[17px] font-black text-indigo-600">{{ selectedNames.length }} 人</span>
                            </div>
                            <div class="grid grid-cols-5 gap-y-3 pt-1">
                                <span v-for="name in selectedNames" :key="name" class="text-[17px] font-black text-slate-700 text-center">{{ name }}</span>
                            </div>
                        </div>
                    </div>"""

direct_mode_config_new = """                    <!-- DIRECT MODE: Fixed Front Positions -->
                    <div v-else class="space-y-6">
                        <div class="space-y-4">
                            <div class="flex items-center justify-between mb-2">
                                <label class="text-[15px] font-black text-slate-400 uppercase tracking-widest">1. 選擇優先固定人員 (排在最前面)</label>
                                <span class="text-[17px] font-black text-indigo-600">{{ fixedParticipants.length }} 人</span>
                            </div>
                            <div class="grid grid-cols-4 gap-2" @mouseleave="stopDrag" @touchmove.prevent="handleTouchMoveFixed">
                                <button v-for="name in selectedNames" :key="'fixed'+name"
                                    @mousedown="startDragFixed(name)"
                                    @mouseenter="onDragEnterFixed(name)"
                                    @mouseup="stopDrag"
                                    @touchstart="handleTouchStartFixed($event, name)"
                                    @touchend="stopDrag"
                                    :data-fixed-name="name"
                                    class="h-12 flex items-center justify-center rounded-xl border-2 font-black text-[17px] transition-all active:scale-95"
                                    :class="fixedParticipants.includes(name) ? 'bg-indigo-600 border-indigo-600 text-white shadow-sm' : 'bg-white border-slate-200 text-black shadow-sm'"
                                    :style="{ color: fixedParticipants.includes(name) ? '#ffffff !important' : '#000000 !important' }">
                                    {{ name }}
                                </button>
                            </div>
                            
                            <div v-if="fixedParticipants.length > 0" class="pt-2 border-t border-slate-100">
                                <label class="text-[15px] font-black text-slate-400 uppercase tracking-widest block mb-3">2. 優先排列順序</label>
                                <div class="flex flex-wrap gap-2">
                                    <div v-for="(name, fidx) in fixedParticipants" :key="'ftag'+name" class="bg-indigo-50 text-indigo-700 px-3 py-1.5 rounded-lg font-black text-[17px] border border-indigo-100 flex items-center">
                                        <span class="mr-1 text-[13px] opacity-40">{{ fidx + 1 }}.</span>
                                        {{ name }}
                                        <button @click="toggleFixedParticipant(name)" class="ml-1 text-indigo-300">×</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>"""

content = content.replace(direct_mode_config_old, direct_mode_config_new)

# 3. Add drag functions for Fixed selection
fixed_drag_logic = """
const toggleFixedParticipant = (name) => {
    const idx = fixedParticipants.value.indexOf(name);
    if (idx > -1) fixedParticipants.value.splice(idx, 1);
    else fixedParticipants.value.push(name);
};

const startDragFixed = (name) => {
    isDragging.value = true;
    const isSelected = fixedParticipants.value.includes(name);
    dragSelectionType.value = isSelected ? 'remove' : 'add';
    toggleFixedParticipant(name);
    window.addEventListener('mouseup', stopDrag);
};

const onDragEnterFixed = (name) => {
    if (!isDragging.value) return;
    const isSelected = fixedParticipants.value.includes(name);
    if (dragSelectionType.value === 'add' && !isSelected) {
        fixedParticipants.value.push(name);
    } else if (dragSelectionType.value === 'remove' && isSelected) {
        const idx = fixedParticipants.value.indexOf(name);
        fixedParticipants.value.splice(idx, 1);
    }
};

const handleTouchStartFixed = (e, name) => {
    lastTouchedName.value = name;
    startDragFixed(name);
};

const handleTouchMoveFixed = (e) => {
    if (!isDragging.value) return;
    const touch = e.touches[0];
    const el = document.elementFromPoint(touch.clientX, touch.clientY);
    if (!el) return;
    const btn = el.closest('button[data-fixed-name]');
    if (btn) {
        const name = btn.getAttribute('data-fixed-name');
        if (name && name !== lastTouchedName.value) {
            lastTouchedName.value = name;
            onDragEnterFixed(name);
        }
    }
};"""

content = content.replace('const startDragStep2 = (name) => {', fixed_drag_logic + '\n\nconst startDragStep2 = (name) => {')

# 4. Update performDraw logic (Direct Mode)
content = content.replace(
    """    setTimeout(() => {
        clearInterval(lotteryInterval);
        const shuffled = [...pool].sort(() => Math.random() - 0.5);
        results.value = shuffled.slice(0, Math.min(drawCount.value, pool.length));
        isDrawing.value = false;
        hasResult.value = true;
        currentStep.value = 3;
    }, 3000);""",
    """    setTimeout(() => {
        clearInterval(lotteryInterval);
        
        const fixed = [...fixedParticipants.value];
        const remaining = pool.filter(n => !fixed.includes(n));
        const shuffledRemaining = remaining.sort(() => Math.random() - 0.5);
        
        // Combine: Fixed at front + randomized rest
        const fullList = [...fixed, ...shuffledRemaining];
        
        // Respect drawCount (if user only wants a subset of the final combined list)
        // Usually in Direct Mode with fixed, they probably want the whole list
        results.value = fullList.slice(0, Math.min(drawCount.value, fullList.length));
        
        isDrawing.value = false;
        hasResult.value = true;
        currentStep.value = 3;
    }, 3000);"""
)

# 5. Restore index numbering in Step 3 results
content = content.replace(
    '<span class="text-[19px] font-black text-black">{{ name }}</span>',
    '<span class="text-[13px] font-black text-slate-400 mb-1">#{{ idx + 1 }}</span>\n                                <span class="text-[19px] font-black text-black">{{ name }}</span>'
)

# 6. Restore index numbering in Copy Results logic
content = content.replace(
    "const text = results.value.join('\\n');",
    "const text = results.value.map((n, i) => `${i + 1}. ${n}`).join('\\n');"
)

# 7. Add fixedParticipants to resetAll
content = content.replace(
    "roundParticipants.value = [];",
    "roundParticipants.value = [];\n    fixedParticipants.value = [];"
)

with open(path, 'w', encoding='utf-8', newline='\r\n') as f:
    f.write(content)

print("LuckyDraw: Fixed positions feature added to Direct Mode with numbering restored.")
