
const l = "道祖仙師開示給在場全體：";
const recMatch = l.match(/(?:開示給|給|開示給對象)\s*[:：]?\s*([^：:\n\r]*)/);
console.log("Match:", recMatch);
if (recMatch) {
    console.log("Group 1:", recMatch[1].trim());
}
