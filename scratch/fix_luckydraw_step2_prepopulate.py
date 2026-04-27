import os

path = r'd:\xampp\htdocs\fabou_la\resources\js\components\LuckyDraw.vue'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# 1. Pre-populate roundParticipants when moving to Step 2 in Lottery Mode
content = content.replace(
    "currentStep.value = 2;",
    "currentStep.value = 2;\n    if (lotteryMode.value) {\n        roundParticipants.value = [...selectedNames.value];\n    }"
)

# 2. Add roundParticipants to resetAll
content = content.replace(
    "results.value = [];",
    "results.value = [];\n    roundParticipants.value = [];"
)

with open(path, 'w', encoding='utf-8', newline='\r\n') as f:
    f.write(content)

print("LuckyDraw: Pre-populating round participants and updated resetAll.")
