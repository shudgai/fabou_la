const batchInput = `113\n4/21 允求閻王仙師金印：玄升宮\n\n5/5 允求 森羅戒：金了、閻爵\n求寶：寶戒請出來求寶`;

const lines = batchInput.split('\n');
const results = [];
let currentMasterId = 1;
let currentDate = '2026-04-30';
let currentContextYear = new Date().getFullYear();

const parseDateText = (str, ctxYear = null) => {
    if (!str) return null;
    const rocYearMatch = str.match(/^(?:民國)?\s*(\d{2,3})\s*年?$/);
    if (rocYearMatch) {
        const y = parseInt(rocYearMatch[1]) + 1911;
        return `${y}-01-01`;
    }
    const ceMatch = str.match(/\b(\d{4})[/\-\s](\d{1,2})[/\-\s](\d{1,2})\b/);
    if (ceMatch) return `${ceMatch[1]}-${ceMatch[2].padStart(2,'0')}-${ceMatch[3].padStart(2,'0')}`;
    const rocMatch = str.match(/\b(\d{2,3})[/\-\s](\d{1,2})[/\-\s](\d{1,2})\b/);
    if (rocMatch) {
        const y = parseInt(rocMatch[1]) + 1911;
        return `${y}-${rocMatch[2].padStart(2,'0')}-${rocMatch[3].padStart(2,'0')}`;
    }
    const mdMatch = str.match(/\b(\d{1,2})[/\-\s](\d{1,2})\b/);
    if (mdMatch) {
        const y = ctxYear || new Date().getFullYear();
        return `${y}-${mdMatch[1].padStart(2,'0')}-${mdMatch[2].padStart(2,'0')}`;
    }
    const standaloneYMatch = str.match(/^\s*(\d{2,4})\s*[年]?\s*$/);
    if (standaloneYMatch) {
        let y = parseInt(standaloneYMatch[1]);
        if (y < 1000) y += 1911;
        return `${y}-01-01`;
    }
    return null;
};

lines.forEach(line => {
    let normLine = line.normalize('NFKC').trim();
    if (!normLine) return;

    const inlineYearMatch = normLine.match(/^(?:民國)?\s*(\d{2,4})\s*年?\s+/);
    if (inlineYearMatch) {
        let y = parseInt(inlineYearMatch[1]);
        if (y < 1000) y += 1911;
        currentContextYear = y;
        normLine = normLine.replace(inlineYearMatch[0], '').trim();
    }

    const standaloneYearMatch = normLine.match(/^(?:民國)?\s*(\d{2,4})\s*年?$/);
    if (standaloneYearMatch) {
        let y = parseInt(standaloneYearMatch[1]);
        if (y < 1000) y += 1911;
        currentContextYear = y;
        return;
    }

    const dateHeader = parseDateText(normLine, currentContextYear);
    const isPureDateStr = normLine.replace(/[\d/.\-年月日時分秒\s]/g, '').length === 0;
    if (dateHeader && isPureDateStr) {
        currentDate = dateHeader;
        return;
    }

    const attrKeywords = ['用意', '狀態', '備註', '求寶方式', '求寶', '由來', '得知日期', '登記日期', '求得日期', '日期'];
    const firstWord = normLine.split(/[\s：:]/)[0];
    if (attrKeywords.includes(firstWord) && results.length > 0) {
        const prev = results[results.length - 1];
        const val = normLine.replace(new RegExp(`^${firstWord}[\\s：:]*`), '').trim();
        if (firstWord === '求寶方式' || firstWord === '求寶') prev.acquisition_method = val;
        return;
    }

    const lineStartDateMatch = normLine.match(/^(\d{1,4}[/.-]\d{1,2}[/.-]\d{1,2}|\d{1,2}[/.-]\d{1,2})\s+/);
    if (lineStartDateMatch) {
        const parsed = parseDateText(lineStartDateMatch[1], currentContextYear);
        if (parsed) currentDate = parsed;
        normLine = normLine.replace(lineStartDateMatch[0], '').trim();
    }

    const kwMatch = normLine.match(/^\s*((允求|賜降|得知|賜予|賜|法寶名稱|法寶內容)\s*)?(.*?)[：:](.*)/);
    if (kwMatch) {
        const name = kwMatch[3].trim();
        const val = kwMatch[4].trim();
        if (attrKeywords.includes(name)) return;
        results.push({ name, remarks: val, date: currentDate, acquisition_method: '' });
    } else if (normLine.length < 50) {
        results.push({ name: normLine, remarks: '', date: currentDate, acquisition_method: '' });
    }
});
console.log(JSON.stringify(results, null, 2));
