import os

path = r'd:\xampp\htdocs\fabou_la\resources\js\components\TeachingManager.vue'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# 1. Define the sections
groups_section = """                                                <div v-if="mainSearchFilteredGroups.length > 0" class="px-5 py-2 text-[12px] font-bold text-indigo-500 uppercase tracking-widest bg-slate-50/50 mb-1 rounded-t-2xl">群組</div>
                                                <div v-for="g in mainSearchFilteredGroups" :key="'g'+g.id"
                                                     @click.stop="g.name === '各宮' ? (showPalacePicker = true) : triggerGroupSelection(g)"
                                                     class="px-5 h-[38px] flex items-center rounded-2xl hover:bg-indigo-50 font-black text-[17px] text-indigo-600 active:bg-indigo-100 transition-all cursor-pointer">
                                                    {{ formatGroupName(g.name) }}
                                                </div>"""

dharma_section = """                                                <div v-if="mainSearchFilteredDharmaNames.length > 0" class="px-5 py-2 text-[12px] font-bold text-slate-400 uppercase tracking-widest bg-slate-50/50 mt-2 mb-1">法號</div>
                                                <div v-for="dn in mainSearchFilteredDharmaNames" :key="'dn'+dn.id"
                                                     @click.stop="dharmaSearchQuery = dn.name; activePractitionerDropdownId = null; handleDharmaSearchInput({target: {value: dn.name}})"
                                                     class="px-5 h-[38px] flex items-center rounded-2xl hover:bg-indigo-50 font-black text-[17px] text-slate-900 active:bg-indigo-100 transition-all cursor-pointer">
                                                    {{ dn.name }}
                                                </div>"""

# 2. Swap them
# Be careful with indentation and exact matches
content = content.replace(groups_section + "\n" + dharma_section, dharma_section + "\n" + groups_section)

# Handle potential whitespace differences if the above fails
if groups_section in content and dharma_section in content and dharma_section not in content.split(groups_section)[0]:
    # It's in the old order, let's try a safer replacement
    parts = content.split(groups_section)
    if dharma_section in parts[1]:
        sub_parts = parts[1].split(dharma_section)
        content = parts[0] + dharma_section + sub_parts[0] + groups_section + sub_parts[1]

with open(path, 'w', encoding='utf-8', newline='\r\n') as f:
    f.write(content)

print("Search dropdown order swapped: Dharma Names first.")
