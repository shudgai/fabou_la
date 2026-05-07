// iOS Safari compatibility utilities

// C6: localStorage safety wrapper - Private Browsing throws SecurityError
export const safeLocalStorage = {
    getItem(key) {
        try {
            return localStorage.getItem(key);
        } catch (e) {
            console.warn('localStorage access denied (Private Browsing?):', e);
            return null;
        }
    },
    setItem(key, value) {
        try {
            localStorage.setItem(key, value);
        } catch (e) {
            console.warn('localStorage write denied (Private Browsing?):', e);
        }
    },
    removeItem(key) {
        try {
            localStorage.removeItem(key);
        } catch (e) {
            console.warn('localStorage remove denied (Private Browsing?):', e);
        }
    }
};

// C3: Clipboard write with iOS fallback
// navigator.clipboard.writeText fails on iOS unless called synchronously from user gesture
export async function writeClipboard(text) {
    if (navigator.clipboard && navigator.clipboard.writeText) {
        try {
            await navigator.clipboard.writeText(text);
            return true;
        } catch (e) {
            // Fall back to execCommand
        }
    }
    // iOS Safari fallback: create temporary textarea and use execCommand('copy')
    try {
        const textarea = document.createElement('textarea');
        textarea.value = text;
        textarea.style.position = 'fixed';
        textarea.style.left = '-9999px';
        textarea.style.top = '-9999px';
        document.body.appendChild(textarea);
        textarea.focus();
        textarea.select();
        const success = document.execCommand('copy');
        document.body.removeChild(textarea);
        return success;
    } catch (e) {
        console.error('Clipboard copy failed:', e);
        return false;
    }
}

// C4: iOS Safari download fallback - <a download> is ignored on iOS
// Opens blob in new tab or triggers server-side download
export function downloadBlob(blob, filename) {
    // iOS detection
    const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent) ||
        (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1);

    if (isIOS) {
        // iOS Safari: open blob URL in new tab (user can long-press to save)
        const url = URL.createObjectURL(blob);
        window.open(url, '_blank');
        // Don't revoke immediately - let the new tab load
        setTimeout(() => URL.revokeObjectURL(url), 60000);
    } else {
        // Standard download for other browsers
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = filename;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
    }
}
