import os

# Update LuckyDraw.vue
path_ld = r'd:\xampp\htdocs\fabou_la\resources\js\components\LuckyDraw.vue'
with open(path_ld, 'r', encoding='utf-8') as f:
    content = f.read()

# Change bottom-[7vh] and remove large padding-bottom to stick to navbar
content = content.replace(
    '<div class="fixed bottom-[7vh] left-0 right-0 px-4 pb-4 pt-3 bg-white/95 backdrop-blur-sm border-t border-slate-100 z-[200]">',
    '<div class="fixed bottom-[7vh] left-0 right-0 px-4 pb-1 pt-3 bg-white/95 backdrop-blur-sm border-t border-slate-100 z-[200]">'
)
content = content.replace(
    '<div class="fixed bottom-[7vh] left-0 right-0 px-4 pb-4 pt-3 bg-white/95 backdrop-blur-md border-t border-slate-100 z-[200]">',
    '<div class="fixed bottom-[7vh] left-0 right-0 px-4 pb-1 pt-3 bg-white/95 backdrop-blur-md border-t border-slate-100 z-[200]">'
)

with open(path_ld, 'w', encoding='utf-8', newline='\r\n') as f:
    f.write(content)

# Update TeachingManager.vue
path_tm = r'd:\xampp\htdocs\fabou_la\resources\js\components\TeachingManager.vue'
with open(path_tm, 'r', encoding='utf-8') as f:
    content = f.read()

# Update bottom bar positions and paddings in TeachingManager
content = content.replace(
    'fixed bottom-[7vh] left-0 right-0 p-[3px] pb-[3px] backdrop-blur-md z-[300] flex items-center space-x-4 px-6 shadow-[0_-10px_30px_rgba(0,0,0,0.05)]',
    'fixed bottom-[7vh] left-0 right-0 p-[3px] pb-[1px] backdrop-blur-md z-[300] flex items-center space-x-4 px-6 shadow-[0_-10px_30px_rgba(0,0,0,0.05)]'
)
content = content.replace(
    'fixed bottom-[7vh] left-0 right-0 px-6 py-5 bg-white/90 backdrop-blur-md border-t border-slate-100 z-[520] shadow-[0_-10px_30px_rgba(0,0,0,0.05)]',
    'fixed bottom-[7vh] left-0 right-0 px-6 py-2 pb-1 bg-white/90 backdrop-blur-md border-t border-slate-100 z-[520] shadow-[0_-10px_30px_rgba(0,0,0,0.05)]'
)
content = content.replace(
    'fixed bottom-[7vh] left-0 right-0 px-6 py-5 border-t border-slate-100 bg-white/95 backdrop-blur-md shadow-[0_-10px_30px_rgba(0,0,0,0.05)] shrink-0 flex items-center space-x-3 z-[610]',
    'fixed bottom-[7vh] left-0 right-0 px-6 py-2 pb-1 border-t border-slate-100 bg-white/95 backdrop-blur-md shadow-[0_-10px_30px_rgba(0,0,0,0.05)] shrink-0 flex items-center space-x-3 z-[610]'
)

with open(path_tm, 'w', encoding='utf-8', newline='\r\n') as f:
    f.write(content)

print("Buttons adjusted to stick closer to the navigation bar.")
