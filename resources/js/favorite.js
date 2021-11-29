const addFavorite = async function () {
    this.setAttribute('src', '/images/heart.png');
    this.setAttribute('data-action', 'remove');

    const fav = document.querySelector('.fav .quantity');
    try {
        const { data } = await axios.post('/favorite/' + this.closest('[data-product]').getAttribute('data-product'));
        fav.innerText = data.total;
        this.removeEventListener('click', addFavorite);
        this.addEventListener('click', removeFavorite);
    }
    catch (e) {
        console.error(e);
    }
}
const removeFavorite = async function () {
    console.log(this);

    const fav = document.querySelector('.fav .quantity');
    const product = this.closest('[data-product]');
    try {
        const { data } = await axios.delete('/favorite/' + product.getAttribute('data-product'));
        fav.innerText = data.total;
        this.removeEventListener('click', removeFavorite);
        if (this.classList.contains('favorite-remove'))
            product.remove();
        else {
            this.setAttribute('src', '/images/fav.png');
            this.setAttribute('data-action', 'add');
            this.addEventListener('click', addFavorite);
        }
    }
    catch (e) {
        console.error(e);
    }
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
