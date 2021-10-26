const dropdownToggle = (context, close = false) => {
    const $parent = context.closest('.menu-city');

    if (!close && context.getAttribute('aria-expanded') === 'false') {
        context.setAttribute('aria-expanded', 'true');
        context.classList.add('show');
        $parent.querySelector('.dropdown-menu').classList.add('show');
    }
    else if (context.getAttribute('aria-expanded') === 'true') {
        context.setAttribute('aria-expanded', 'false');
        context.classList.remove('show');
        $parent.querySelector('.dropdown-menu').classList.remove('show');
    }
}

document.querySelector('header .menu-city .dropdown-toggle').addEventListener('click', event => {
    event.preventDefault();
    dropdownToggle(event.target);
});

document.querySelector('header .menu-city .city-choose .city-another').addEventListener('click', event => {
    event.preventDefault();
    event.target.closest('.city-choose').style.display = '';
    dropdownToggle(document.querySelector('header .menu-city .dropdown-toggle'));
});

window.addEventListener('click', event => {
    if (!event.target.closest('.dropdown-toggle') && !event.target.closest('.city-another'))
        dropdownToggle(document.querySelector('header .menu-city .dropdown-toggle'), true);
});

// Фиксированная шапка при прокрутке
window.addEventListener('scroll', () => {
    const nav = document.querySelector('.fixed-box');
    const empty_box = nav.closest('.container').querySelector('.empty-box');
    if (window.scrollY > nav.offsetHeight) {
        nav.classList.add('fixed');
        empty_box.style.display = 'block';
    }
    else {
        nav.classList.remove('fixed');
        empty_box.removeAttribute('style');
    }
});
