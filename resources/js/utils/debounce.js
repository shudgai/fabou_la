export function debounce(fn, delay = 300) {
    let timer = null;
    const debounced = function (...args) {
        clearTimeout(timer);
        timer = setTimeout(() => fn.apply(this, args), delay);
    };
    debounced.cancel = () => {
        clearTimeout(timer);
        timer = null;
    };
    return debounced;
}
