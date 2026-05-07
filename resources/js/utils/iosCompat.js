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
// C3: Clipboard write with iOS fallback
// navigator.clipboard.writeText fails on iOS unless called synchronously from user gesture.
// Also, it only works in Secure Contexts (HTTPS/localhost).
export async function writeClipboard(text) {
    // 1. Try modern Clipboard API (if available and in secure context)
    if (navigator.clipboard && navigator.clipboard.writeText) {
        try {
            await navigator.clipboard.writeText(text);
            return true;
        } catch (e) {
            console.warn('Modern Clipboard API failed, falling back...', e);
        }
    }

    // 2. iOS Safari / HTTP Fallback: execCommand('copy')
    // CRITICAL: This must be called as soon as possible after a user gesture.
    // If there is a long async delay (e.g., API call) before this, iOS will block it.
    try {
        const textarea = document.createElement('textarea');
        textarea.value = text;
        
        // Ensure textarea is not visible but part of the DOM
        textarea.style.position = 'fixed';
        textarea.style.left = '-9999px';
        textarea.style.top = '0';
        textarea.style.opacity = '0';
        
        // Prevent keyboard from popping up on mobile
        textarea.setAttribute('readonly', '');
        document.body.appendChild(textarea);
        
        // Selection for iOS
        textarea.focus();
        textarea.setSelectionRange(0, 999999);
        
        const success = document.execCommand('copy');
        document.body.removeChild(textarea);
        
        if (success) return true;
    } catch (e) {
        console.error('All clipboard methods failed:', e);
    }

    return false;
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

// C6: Body scroll lock for iOS (Prevents rubber-band background scrolling when modals are open)
let lockCount = 0;
let scrollPos = 0;

export function lockBodyScroll() {
    if (lockCount === 0) {
        scrollPos = window.pageYOffset;
        document.body.style.overflow = 'hidden';
        document.body.style.position = 'fixed';
        document.body.style.top = `-${scrollPos}px`;
        document.body.style.width = '100%';
        document.body.style.height = '100dvh'; // Use dynamic viewport height
    }
    lockCount++;
}

export function unlockBodyScroll() {
    lockCount = Math.max(0, lockCount - 1);
    if (lockCount === 0) {
        document.body.style.removeProperty('overflow');
        document.body.style.removeProperty('position');
        document.body.style.removeProperty('top');
        document.body.style.removeProperty('width');
        document.body.style.removeProperty('height');
        window.scrollTo(0, scrollPos);
    }
}
