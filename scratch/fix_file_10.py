import os

path = r'd:\xampp\htdocs\fabou_la\resources\js\components\TeachingManager.vue'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# 1. Update the template to use activeModalGroup as a prioritized display
target_tag_logic = """                                            <template v-if="currentMatchedGroup">
                                                <div @click="activeModalGroup = currentMatchedGroup; showGroupMembersModal = true" 
                                                     class="bg-indigo-50 border-2 border-indigo-100 text-indigo-700 px-4 py-[10px] rounded-2xl app-body flex items-center shadow-sm cursor-pointer hover:bg-indigo-100 transition-all">
                                                    <span class="mr-2 w-2 h-2 bg-indigo-600 rounded-full animate-pulse"></span>
                                                    <span class="font-black text-[17px]">群組：{{ formatGroupName(currentMatchedGroup?.name) }} ({{ currentMatchedGroup.dharma_names.length }}人)</span>
                                                    <span class="ml-2 text-[12px] opacity-60 underline font-bold tracking-tight">查看明細</span>
                                                    <button @click.stop="form.dharma_name_ids = []; dharmaSearchQuery = ''; activeModalGroup = null" class="ml-4 text-indigo-300 hover:text-red-500 transition-colors">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                                    </button>
                                                </div>
                                            </template>"""

# Wait, I need to check the current code exactly
# I'll use a more flexible replace

content = content.replace("v-if=\"currentMatchedGroup\"", "v-if=\"activeModalGroup || currentMatchedGroup\"")
content = content.replace("activeModalGroup = currentMatchedGroup; showGroupMembersModal = true", "showGroupMembersModal = true")
content = content.replace("{{ formatGroupName(currentMatchedGroup?.name) }}", "{{ formatGroupName(activeModalGroup?.name || currentMatchedGroup?.name) }}")
content = content.replace("{{ currentMatchedGroup.dharma_names.length }}人", "{{ form.dharma_name_ids.length }}人")
content = content.replace("v-else", "v-else-if=\"!activeModalGroup && !currentMatchedGroup\"")

# Also ensure activeModalGroup is cleared when selection becomes empty
content = content.replace("if (!val) { form.value.dharma_name_ids = []; return; }", "if (!val) { form.value.dharma_name_ids = []; activeModalGroup.value = null; return; }")

with open(path, 'w', encoding='utf-8', newline='\r\n') as f:
    f.write(content)

print("Updated tags logic to prioritize activeModalGroup display and hide individuals.")
