class Favorite {
    constructor() {
        this.favorite = document.querySelector('.fav .quantity');
        this.elements = [].concat(...document.querySelectorAll('.favorite-toggle'));

        if (this.elements.length) {
            this.elements.forEach((element) => element.addEventListener('click', () => this.listener(element)));
        }
        else {
            this.elements = [].concat(...document.querySelectorAll('.favorite .favorite-remove'));
            this.elements.forEach((element, index) => element.addEventListener('click', () => {
                this.removeFavorite(element, true).then(() => this.elements.splice(index, 1));
            }));
        }
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
        console.log(element);
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
