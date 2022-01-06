// Bootstrap
require('bootstrap/js/src/collapse');

document.querySelectorAll('.store-item a').forEach(item => {
    item.addEventListener('click', event => {
        event.stopPropagation();
        const url = event.target.getAttribute('href');
        if (url) location.href = url;
    })
});
