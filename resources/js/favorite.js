const addFavorite = function () {
    this.setAttribute('src', '/images/heart.png');
    this.setAttribute('data-action', 'remove');

    const fav = document.querySelector('.fav .quantity');
    let count = Number(fav.innerText);
    if (!count) count = 0;
    count++;

    axios.post('/favorite/' + this.closest('[data-product]').getAttribute('data-product'))
        .then(() => {
            fav.innerText = count;
            this.removeEventListener('click', addFavorite);
            this.addEventListener('click', removeFavorite);
        })
        .catch(error => console.error(error));
}
const removeFavorite = function () {
    this.setAttribute('src', '/images/fav.png');
    this.setAttribute('data-action', 'add');

    const fav = document.querySelector('.fav .quantity');
    let count = Number(fav.innerText);
    if(count > 0) count--;
    else count = 0;

    const product = this.closest('[data-product]');
    axios.delete('/favorite/' + product.getAttribute('data-product'))
        .then(() => {
            if (this.classList.contains('favorite-remove'))
                product.remove();
            fav.innerText = count;
            this.removeEventListener('click', removeFavorite);
            this.addEventListener('click', addFavorite);
        })
        .catch(error => console.error(error));
}

document.querySelectorAll(".favorite-toggle[data-action='add']").forEach(item => {
    item.addEventListener('click', addFavorite);
});
document.querySelectorAll(".favorite-toggle[data-action='remove']").forEach(item => {
    item.addEventListener('click', removeFavorite);
});
document.querySelectorAll(".favorite-remove[data-action='remove']").forEach(item => {
    item.addEventListener('click', removeFavorite);
});
