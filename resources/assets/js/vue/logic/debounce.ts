export default function createDebounce() {
    let timeout: any = null;
    return function (func: Function, delay: number) {
        clearTimeout(timeout);
        timeout = setTimeout(() => {
            func();
        }, delay || 200);
    };
}
