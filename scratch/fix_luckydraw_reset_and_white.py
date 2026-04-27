import os

path = r'd:\xampp\htdocs\fabou_la\resources\js\components\LuckyDraw.vue'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# 1. Force white color with !important in getPendingStyle
content = content.replace(
    "color: '#ffffff',",
    "color: '#ffffff !important',"
)

# 2. Add watch on lotteryMode to clear selection when switching modes
mode_watch = """
watch(lotteryMode, () => {
    resetAll(true);
});
"""

if 'watch(lotteryMode' not in content:
    content = content.replace('const lotteryMode = ref(false);', 'const lotteryMode = ref(false);\n' + mode_watch)

with open(path, 'w', encoding='utf-8', newline='\r\n') as f:
    f.write(content)

print("LuckyDraw updated: Forced white text and auto-reset on mode switch.")
