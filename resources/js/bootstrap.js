import 'bootstrap';

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

// import Echo from 'laravel-echo';

// import Pusher from 'pusher-js';
// window.Pusher = Pusher;

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: import.meta.env.VITE_PUSHER_APP_KEY,
//     cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER ?? 'mt1',
//     wsHost: import.meta.env.VITE_PUSHER_HOST ?? `ws-${import.meta.env.VITE_PUSHER_APP_CLUSTER}.pusher.com`,
//     wsPort: import.meta.env.VITE_PUSHER_PORT ?? 80,
//     wssPort: import.meta.env.VITE_PUSHER_PORT ?? 443,
//     forceTLS: (import.meta.env.VITE_PUSHER_SCHEME ?? 'https') === 'https',
//     enabledTransports: ['ws', 'wss'],
// });

/**
 * 全域日期轉換工具 (ROC -> Gregorian)
 * 支援格式：112/05/20, 112-05-20, 112.05.20, 112年05月20日 等
 */
window.convertROCDate = function(str) {
    if (typeof str !== 'string') return str;
    // 匹配 50~149 年，避免誤判一般數字
    const rocDateRegex = /(?<!\d)([5-9]\d|1[0-4]\d)[\.\/\-年](0?[1-9]|1[0-2])[\.\/\-月](0?[1-9]|[12]\d|3[01])(?:日)?(?!\d)/g;
    return str.replace(rocDateRegex, (m, y, mo, d) => {
        return (parseInt(y, 10) + 1911) + '-' + mo.padStart(2, '0') + '-' + d.padStart(2, '0');
    });
};

// 1. DOM 層級攔截：當使用者在任何輸入框或文字區域輸入、貼上時，即時轉換 (UI 即時反饋)
document.addEventListener('input', (e) => {
    if (e.target && (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA')) {
        // 排除密碼或不需要轉換的特殊欄位
        if (e.target.type === 'password' || e.target.type === 'email') return;
        
        const orig = e.target.value;
        const converted = window.convertROCDate(orig);
        
        if (orig !== converted) {
            const start = e.target.selectionStart;
            const end = e.target.selectionEnd;
            e.target.value = converted;
            // 觸發 input 事件以讓 Vue/Alpine 等框架的 v-model 同步更新
            e.target.dispatchEvent(new Event('input', { bubbles: true }));
            // 嘗試恢復游標位置 (轉換後長度可能會變，但對日期來說大致可接受)
            try { e.target.setSelectionRange(start, end); } catch (err) {}
        }
    }
});

// 2. Axios 層級攔截：確保任何送往後端的資料 (包含匯入的隱藏資料) 都被強制轉換為西元年 (資料庫防線)
window.axios.interceptors.request.use(config => {
    if (config.data) {
        const replaceROC = (obj) => {
            for (let key in obj) {
                if (typeof obj[key] === 'string') {
                    obj[key] = window.convertROCDate(obj[key]);
                } else if (typeof obj[key] === 'object' && obj[key] !== null && !(obj[key] instanceof File)) {
                    replaceROC(obj[key]);
                }
            }
        };
        // 若為 FormData 則不深層遍歷，但專案大多使用 JSON payload
        if (!(config.data instanceof FormData)) {
            replaceROC(config.data);
        }
    }
    return config;
});

