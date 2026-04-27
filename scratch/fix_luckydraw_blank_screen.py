import os

path = r'd:\xampp\htdocs\fabou_la\resources\js\components\LuckyDraw.vue'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# 1. Fix confirmSelection to default to full count in Direct Mode
# This prevents results from being limited to 1 person by mistake
new_confirm_selection = """const confirmSelection = () => {
    if (pendingNames.value.length === 0) return;
    if (!selectionFiltered.value) {
        selectionFiltered.value = true;
        return;
    }
    
    selectedNames.value = [...pendingNames.value];
    
    // In Direct Mode, default to all selected names
    // In Lottery Mode, default to 1 person
    if (lotteryMode.value === false) {
        drawCount.value = selectedNames.value.length;
    } else {
        drawCount.value = 1;
    }
    
    currentStep.value = 2;
    if (lotteryMode.value) {
        roundParticipants.value = [];
        fixedParticipants.value = [];
    }
};"""

content = content.replace(
    """const confirmSelection = () => {
    if (pendingNames.value.length === 0) return;
    if (!selectionFiltered.value) {
        selectionFiltered.value = true;
        return;
    }
    
    selectedNames.value = [...pendingNames.value];
    
    // In Direct Mode, we just use the selected names as is
    // In Lottery Mode, we still go to Step 2 but it acts as a "Round Pool"
    drawCount.value = Math.min(1, selectedNames.value.length);
    currentStep.value = 2;
    if (lotteryMode.value) {
        roundParticipants.value = [];
    fixedParticipants.value = [];
        drawCount.value = 1;
    }
};""",
    new_confirm_selection
)

# 2. Add robustness to performDraw to prevent blank screens
# Adding try-catch and ensuring isDrawing always turns off
new_perform_draw = """const performDraw = () => {
    if (selectedNames.value.length === 0) return;
    
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
            
            const fixed = [...fixedParticipants.value];
            // Filter fixed list to ensure they actually exist in the pool
            const validFixed = fixed.filter(n => pool.includes(n));
            const remaining = pool.filter(n => !validFixed.includes(n));
            const shuffledRemaining = [...remaining].sort(() => Math.random() - 0.5);
            
            const fullList = [...validFixed, ...shuffledRemaining];
            
            // Ensure results has at least something if fullList is not empty
            const finalCount = Math.max(1, Math.min(drawCount.value || fullList.length, fullList.length));
            results.value = fullList.slice(0, finalCount);
            
            if (results.value.length === 0 && fullList.length > 0) {
                results.value = [fullList[0]];
            }
            
            hasResult.value = true;
            currentStep.value = 3;
        } catch (err) {
            console.error("Draw Error:", err);
            // Fallback to avoid blank screen
            results.value = pool.slice(0, 1);
            currentStep.value = 3;
        } finally {
            isDrawing.value = false;
        }
    }, 3000);
};"""

content = content.replace(
    """const performDraw = () => {
    if (selectedNames.value.length === 0) return;
    
    isDrawing.value = true;
    hasResult.value = false;
    buildFlyingSticks([...selectedNames.value]);

    const pool = [...selectedNames.value];
    let idx = 0;
    lotteryInterval = setInterval(() => {
        idx = (idx + 1) % pool.length;
        lotteryDisplayNames.value = [pool[idx]];
    }, 80);

    setTimeout(() => {
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
    }, 3000);
};""",
    new_perform_draw
)

with open(path, 'w', encoding='utf-8', newline='\r\n') as f:
    f.write(content)

print("LuckyDraw: performDraw robustness improved and Direct Mode count bug fixed.")
