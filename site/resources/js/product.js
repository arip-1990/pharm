document.querySelectorAll('[data-product] .price .mask').forEach(element => {
    element.addEventListener('click', showPrice);
});
