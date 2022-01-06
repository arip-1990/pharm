const NODE_TEXT = 3;

const getElementFromSelector = element => {
    const selector = element.getAttribute('data-target');
    return selector ? document.querySelector(selector) : null;
}

const children = (element, selector) => [].concat(...element.children).filter(child => child.matches(selector));

const parents = (element, selector) => {
    const parents = [];
    let ancestor = element.parentNode;

    while (ancestor && ancestor.nodeType === Node.ELEMENT_NODE && ancestor.nodeType !== NODE_TEXT) {
        if (ancestor.matches(selector))
            parents.push(ancestor);

        ancestor = ancestor.parentNode;
    }

    return parents;
};

const prev = (element, selector) => {
    let previous = element.previousElementSibling;

    while (previous) {
        if (previous.matches(selector))
            return [previous];

        previous = previous.previousElementSibling;
    }

    return [];
};

const next = (element, selector) => {
    let next = element.nextElementSibling;

    while (next) {
        if (next.matches(selector))
            return [next];

        next = next.nextElementSibling;
    }

    return [];
}

const isElement = obj => (obj[0] || obj).nodeType;

const emulateTransitionEnd = (element, duration) => {
    let called = false
    const durationPadding = 5
    const emulatedDuration = duration + durationPadding

    const listener = () => {
        called = true;
        element.removeEventListener('transitionend', listener);
    }

    element.addEventListener('transitionend', listener);
    setTimeout(() => {
        if (!called)
            element.dispatchEvent(new Event('transitionend'));
    }, emulatedDuration);
}

const getTransitionDurationFromElement = element => {
    if (!element) return 0;

    let { transitionDuration, transitionDelay } = window.getComputedStyle(element);

    const floatTransitionDuration = Number.parseFloat(transitionDuration);
    const floatTransitionDelay = Number.parseFloat(transitionDelay);

    if (!floatTransitionDuration && !floatTransitionDelay)
        return 0;

    transitionDuration = transitionDuration.split(',')[0]
    transitionDelay = transitionDelay.split(',')[0]

    return (Number.parseFloat(transitionDuration) + Number.parseFloat(transitionDelay)) * 1000;
}

export {
    getElementFromSelector,
    emulateTransitionEnd,
    getTransitionDurationFromElement,
    children,
    parents,
    prev,
    next,
    isElement
}
