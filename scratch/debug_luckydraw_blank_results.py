import os

path = r'd:\xampp\htdocs\fabou_la\resources\js\components\LuckyDraw.vue'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# Add debug logs and force Step 3 transition
new_perform_draw = """const performDraw = () => {
    if (selectedNames.value.length === 0) {
        alert('請先選擇名單');
        return;
    }
    
    console.log("Starting Draw with:", selectedNames.value.length, "people");
    isDrawing.value = true;
    hasResult.value = false;
    buildFlyingSticks([...selectedNames.value]);

    const pool = [...selectedNames.value];
    let idx = 0;
    lotteryInterval = setInterval(() => {
        if (pool.length > 0) {
            idx = (idx + 1) % pool.length;
            lotteryDisplayNames.value = [pool[idx]];
        }
    }, 80);

    setTimeout(() => {
        try {
            clearInterval(lotteryInterval);
            console.log("Draw Animation Finished");
            
            const fixed = [...fixedParticipants.value];
            const validFixed = fixed.filter(n => pool.includes(n));
            const remaining = pool.filter(n => !validFixed.includes(n));
            const shuffledRemaining = [...remaining].sort(() => Math.random() - 0.5);
            
            const fullList = [...validFixed, ...shuffledRemaining];
            console.log("Full Sorted List:", fullList);
            
            const count = Math.max(1, Math.min(drawCount.value || fullList.length, fullList.length));
            results.value = [...fullList.slice(0, count)];
            
            if (results.value.length === 0 && fullList.length > 0) {
                results.value = [fullList[0]];
            }
            
            console.log("Final Results:", results.value);
            hasResult.value = true;
            currentStep.value = 3;
        } catch (err) {
            console.error("Draw Error:", err);
            results.value = pool.slice(0, 1);
            currentStep.value = 3;
        } finally {
            isDrawing.value = false;
            console.log("isDrawing set to false, Current Step:", currentStep.value);
        }
    }, 3000);
};"""

import re
pattern = re.compile(r'const performDraw = \(.*?\}\s+3000\);', re.DOTALL)
content = pattern.sub(new_perform_draw, content)

with open(path, 'w', encoding='utf-8', newline='\r\n') as f:
    f.write(content)

print("LuckyDraw: performDraw updated with debug logs and force state.")
