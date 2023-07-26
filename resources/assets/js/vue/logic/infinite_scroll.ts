export function hasInfiniteScroll(
    callback: Function,
    element: Element = getDocumentElement()
) {
    // Assume they are at the bottom of the page if they are within this number of pixels
    const BOTTOM_PADDING_PIXELS = 550;
    const BOTTOM_PADDING_PIXELS_CUSTOM_ELEMENT = 250;

    const FINAL_PADDING =
        element === getDocumentElement()
            ? BOTTOM_PADDING_PIXELS
            : BOTTOM_PADDING_PIXELS_CUSTOM_ELEMENT;

    // TODO: this needs to be able to be reset
    // play page can generate 2 pages with the same pixel lengths that have independent scrolls
    // ex. scroll one page on most popular, swap to recently played, page wont load as it has been 'calledForHeight'
    let calledForHeight: number;

    // document.documentElement doesnt have scroll events, have to listen to the Window in that case
    let scrollingElement: Window | Element | null = element;
    if (element === document.scrollingElement) {
        scrollingElement = window;
    }

    let paused = false;

    scrollingElement.addEventListener("scroll", checkForScroll);
    scrollingElement.addEventListener("resize", checkForScroll);
    checkForScroll();

    async function checkForScroll() {
        if (paused) return;
        if (calledForHeight == element.scrollHeight) return;

        let bottomOfWindow =
            Math.abs(
                element.scrollHeight - element.clientHeight - element.scrollTop
            ) < FINAL_PADDING;
        if (bottomOfWindow) {
            calledForHeight = element.scrollHeight;
            await callback();
            // TODO: need to keep checking for height here incase one load doesnt induce a scroll
        }
    }

    function clearScroll(): void {
        calledForHeight = 0;
    }

    function setPaused(state: boolean): void {
        paused = state;
        checkForScroll();
    }

    return { clearScroll, setPaused };
}

function getDocumentElement(): Element {
    let scrollElement = document.scrollingElement;
    if (scrollElement !== null) {
        return scrollElement;
    }

    return document.documentElement;
}
