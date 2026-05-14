const fs = require('fs');
const content = fs.readFileSync('D:/xampp/htdocs/fabou_la/resources/js/components/RegistryManager.vue', 'utf8');

let openBrackets = 0;
let inString = false;
let stringChar = null;

for (let i = 0; i < content.length; i++) {
    const ch = content[i];
    const prev = content[i-1] || '';
    
    // Toggle string state
    if ((ch === "'" || ch === '"' || ch === '`') && prev !== '\\') {
        if (!inString) {
            inString = true;
            stringChar = ch;
        } else if (ch === stringChar) {
            inString = false;
            stringChar = null;
        }
    }
    
    if (!inString) {
        if (ch === '{') openBrackets++;
        if (ch === '}') openBrackets--;
    }
}

console.log('Final bracket balance:', openBrackets);

// Find where balance is nonzero
openBrackets = 0;
inString = false;
stringChar = null;
const lines = content.split('\n');
for (let lineIdx = 0; lineIdx < lines.length; lineIdx++) {
    const line = lines[lineIdx];
    let prevBalance = openBrackets;
    for (let j = 0; j < line.length; j++) {
        const ch = line[j];
        const prev = line[j-1] || '';
        
        if ((ch === "'" || ch === '"' || ch === '`') && prev !== '\\') {
            if (!inString) { inString = true; stringChar = ch; }
            else if (ch === stringChar) { inString = false; stringChar = null; }
        }
        
        if (!inString) {
            if (ch === '{') openBrackets++;
            if (ch === '}') openBrackets--;
        }
    }
    if (openBrackets !== prevBalance) {
        console.log(`Line ${lineIdx+1}: bal=${openBrackets} | ${line.trim().substring(0,80)}`);
    }
}
