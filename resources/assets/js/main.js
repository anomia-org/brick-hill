function debouncer(func, timeout) {
    var timeoutID, timeout = timeout || 200;
    return function () {
        var scope = this, args = arguments;
        clearTimeout(timeoutID);
        timeoutID = setTimeout(function () {
            func.apply(scope, Array.prototype.slice.call(args));
        }, timeout);
    };
}

$(document).ready(() => {
    // might be better to use document.on listeners to allow 3rd party buttons added
    // also other reasons
    $('.tabs .tab').on('click', (e) => {
        let tab = $(e.target);
        let oldTab = $('.tabs .tab.active');
        let tabNum = tab.data('tab');
        let currentTab = $('.tab-holder .tab-body.active');
        let nextTab = $(`.tab-holder .tab-body[data-tab="${tabNum}"]`);
                    
        oldTab.removeClass('active');
        tab.addClass('active');

        currentTab.removeClass('active');
        nextTab.addClass('active');
    })

    $('.tab-buttons .tab-button').on('click', (e) => {
        let current = $('.tab-buttons .tab-button.blue');
        let clicked = $(e.target);
        
        current.removeClass('blue').addClass('transparent');
        clicked.addClass('blue').removeClass('transparent');

        $('.button-tab.active').removeClass('active');
        $('.button-tab[data-tab="'+clicked.data('tab')+'"]').addClass('active');
    })

    // this is still used in legacy, wrap in a try catch to prevent errors with spaces
    try {
        if(window.location.hash && $(`.tabs .tab${window.location.hash}`).length > 0) {
            $(`.tabs .tab${window.location.hash}`).click();
        }
    } catch {}
})

window.debouncer = debouncer