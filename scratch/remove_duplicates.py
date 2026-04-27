import os

path = r'd:\xampp\htdocs\fabou_la\resources\js\components\LuckyDraw.vue'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# Remove the duplicate declarations at around line 785
# Note: They are already correctly defined at around line 454-470.

content = content.replace(
    """const toggleFixedParticipant = (name) => {
    const idx = fixedParticipants.value.indexOf(name);
    if (idx > -1) {
        fixedParticipants.value.splice(idx, 1);
    } else {
        fixedParticipants.value.push(name);
    }
};

const toggleRoundParticipant = (name) => {
    const idx = roundParticipants.value.indexOf(name);
    if (idx > -1) {
        roundParticipants.value.splice(idx, 1);
    } else {
        roundParticipants.value.push(name);
    }
};

const isRoundParticipant = (name) => roundParticipants.value.includes(name);""",
    ""
)

with open(path, 'w', encoding='utf-8', newline='\r\n') as f:
    f.write(content)

print("LuckyDraw: Duplicate declarations removed.")
