import os

path = r'd:\xampp\htdocs\fabou_la\resources\js\components\LuckyDraw.vue'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# Update drawCount logic in confirmSelection for Lottery Mode
old_logic = """    currentStep.value = 2;
    if (lotteryMode.value) {
        roundParticipants.value = [...selectedNames.value];
    }"""

new_logic = """    currentStep.value = 2;
    if (lotteryMode.value) {
        roundParticipants.value = [...selectedNames.value];
        drawCount.value = roundParticipants.value.length;
    }"""

content = content.replace(old_logic, new_logic)

with open(path, 'w', encoding='utf-8', newline='\r\n') as f:
    f.write(content)

print("LuckyDraw: Updated drawCount logic for Lottery Mode.")
