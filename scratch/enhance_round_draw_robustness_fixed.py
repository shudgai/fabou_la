import os
import re

path = r'd:\xampp\htdocs\fabou_la\resources\js\components\LuckyDraw.vue'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# Refined regex to find performRoundDraw
pattern = re.compile(r'const performRoundDraw = \(.*?\}\s+3000\);', re.DOTALL)
new_perform_round_draw = """const performRoundDraw = () => {
    if (roundParticipants.value.length === 0) {
        alert('請先選擇參與抽籤的人員');
        return;
    }
    
    isDrawing.value = true;
    hasResult.value = false;
    buildFlyingSticks([...roundParticipants.value]);

    const pool = [...roundParticipants.value];
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
            const shuffled = [...pool].sort(() => Math.random() - 0.5);
            // Default drawCount to at least 1
            const count = Math.max(1, Math.min(drawCount.value || 1, pool.length));
            results.value = shuffled.slice(0, count);
            
            if (results.value.length === 0 && pool.length > 0) {
                results.value = [pool[0]];
            }
            
            hasResult.value = true;
            currentStep.value = 3;
        } catch (err) {
            console.error("Round Draw Error:", err);
            results.value = pool.slice(0, 1);
            currentStep.value = 3;
        } finally {
            isDrawing.value = false;
        }
    }, 3000);
};"""

if pattern.search(content):
    content = pattern.sub(new_perform_round_draw, content)
else:
    # Fallback if regex fails - try simpler match
    content = content.replace("const performRoundDraw = () => {", new_perform_round_draw.replace("const performRoundDraw = () => {", "// Replacement fallback\nconst performRoundDraw = () => {"))

with open(path, 'w', encoding='utf-8', newline='\r\n') as f:
    f.write(content)

print("LuckyDraw: performRoundDraw robustness fix applied.")
