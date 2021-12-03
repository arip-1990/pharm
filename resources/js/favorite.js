class Favorite {
    constructor() {
        this.favorite = document.querySelector('.fav .quantity');

        document.querySelectorAll('.favorite-toggle').forEach((element) => {
            element.addEventListener('click', () => this.listener(element));
        });
        document.querySelectorAll('.favorite .favorite-remove').forEach((element) => {
            element.addEventListener('click', () => this.removeFavorite(element, true));
        });
    }

    listener(element) {
        if (element.getAttribute('data-action') === 'add') {
            this.addFavorite(element).then(() => {
                element.setAttribute('src', '/images/heart.png');
                element.setAttribute('data-action', 'remove');
            });
        }
        else {
            this.removeFavorite(element).then(() => {
                element.setAttribute('src', '/images/fav.png');
                element.setAttribute('data-action', 'add');
            });
        }
    }

    async addFavorite(element) {
        try {
            const product = element.closest('[data-product]');
            const { data } = await axios.post('/favorite/' + product.getAttribute('data-product'));
            this.favorite.innerText = data.total;
        }
        catch (e) {
            console.error(e);
        }
    }

    async removeFavorite(element, removeElement = false) {
        try {
            const product = element.closest('[data-product]');
            const { data } = await axios.delete('/favorite/' + product.getAttribute('data-product'));
            this.favorite.innerText = data.total;
            if (removeElement) product.remove();
        }
        catch (e) {
            console.error(e);
        }
    }
}

new Favorite();
