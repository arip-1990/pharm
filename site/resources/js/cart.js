class Cart {
    constructor() {
        this.cart = document.querySelector('.cart .quantity');
        this.elements = [].concat(...document.querySelectorAll("[itemtype='https://schema.org/Product'][data-product] .btn"));
    }

    listen() {
        this.elements.forEach(element => {
            element.addEventListener('newCart', () => this.newCartListener(element));
        });

        document.querySelectorAll('.cart .cart-remove').forEach(item => {
            item.addEventListener('click', async (event) => {
                const product = event.target.closest('[data-product]');
                try {
                    await this.remove(product.getAttribute('data-product'));
                    product.remove();
                    this.changeCartQuantity();
                }
                catch (e) {
                    alertMessage(e);
                }
            });
        });
        document.querySelectorAll('[data-product] .input-group .btn').forEach(element => {
            element.addEventListener('click', (event) => {
                event.preventDefault();
                const elem = event.target;
                const input = elem.closest('.input-group').querySelector('input');
                const currentVal = parseInt(input.value);

                if (!isNaN(currentVal)) {
                    if(elem.getAttribute('data-type') === '-') {
                        if(currentVal > parseInt(input.getAttribute('min'))) {
                            input.value = String(currentVal - 1);
                        }
                    }
                    else if(elem.getAttribute('data-type') === '+') {
                        if(currentVal < parseInt(input.getAttribute('max'))) {
                            input.value = String(currentVal + 1);
                        }
                    }
                }
                else {
                    input.value = String(0);
                }

                input.dispatchEvent(new Event('change'));
            })
        });
        document.querySelectorAll('[data-product] .input-group .input-number').forEach(element => {
            element.addEventListener('change', async (event) => {
                const minValue = parseInt(event.target.getAttribute('min'));
                const maxValue = parseInt(event.target.getAttribute('max'));
                const currentValue = parseInt(event.target.value);
                const productId = event.target.closest('[data-product]').getAttribute('data-product');
                this.toggleDisabled(event.target);

                if (currentValue < minValue) {
                    event.target.value = minValue;
                    try {
                        await this.quantity(productId, minValue);
                    }
                    catch (e) {
                        alertMessage('Ошибка!');
                    }
                }
                else if (currentValue > maxValue) {
                    event.target.value = maxValue;
                    try {
                        await this.quantity(productId, maxValue);
                    }
                    catch (e) {
                        alertMessage('Ошибка!');
                    }
                }
                else {
                    try {
                        await this.quantity(productId, currentValue);
                    }
                    catch (e) {
                        alertMessage('Ошибка!');
                    }
                }
            })
        });
        document.querySelectorAll('[data-product] .input-group .input-number').forEach(element => {
            element.addEventListener('keydown', (event) => {
                // Allow: backspace, delete, tab, escape, enter and .       // Allow: Ctrl+A                            // Allow: home, end, left, right
                if ([46, 8, 9, 27, 13, 190].includes(event.keyCode) || (event.keyCode === 65 && event.ctrlKey === true) || (event.keyCode >= 35 && event.keyCode <= 39))
                    return;

                // Ensure that it is a number and stop the keypress
                if ((event.shiftKey || (event.keyCode < 48 || event.keyCode > 57)) && (event.keyCode < 96 || event.keyCode > 105))
                    event.preventDefault();
            })
        });
    }

    toggleDisabled(element) {
        const product = element.closest('[data-product]');
        if (product) {
            const input = product.querySelector('.input-group input');
            product.querySelectorAll('.input-group .btn[data-type]').forEach(elem => {
                if (elem.getAttribute('data-type') === '-') {
                    if (parseInt(input.value) <= parseInt(input.getAttribute('min')))
                        elem.setAttribute('disabled', '');
                    else elem.removeAttribute('disabled');
                }
                else if (elem.getAttribute('data-type') === '+') {
                    if (parseInt(input.value) >= parseInt(input.getAttribute('max')))
                        elem.setAttribute('disabled', '');
                    else elem.removeAttribute('disabled');
                }
            });
        }
    }

    async newCartListener(element) {
        const modal = document.querySelector('.modal[data-type=product]');
        const product = element.closest('[data-product]');
        const name = product.querySelector('[itemprop=name]');

        try {
            await this.add(product.getAttribute('data-product'));
        }
        catch (e) {
            alertMessage(e);
        }

        modal.setAttribute('data-product', product.getAttribute('data-product'));
        modal.querySelector('.title').innerHTML = name.innerHTML;
        modal.querySelector('.price').innerHTML = product.querySelector('[itemprop=price]').innerHTML;
        modal.querySelector('.input-group input').setAttribute('max', product.querySelector('[data-max]').getAttribute('data-max'));
        modal.querySelector('img').setAttribute('alt', name.innerText);
        modal.querySelector('img').setAttribute('src', product.querySelector('[itemprop=image]').getAttribute('src'));
        modal.querySelectorAll('.input-group .btn[data-type]').forEach(elem => elem.removeAttribute('disabled'));

        element.removeAttribute('data-toggle');
        element.removeAttribute('data-target');
        element.removeAttribute('data-max');
        element.innerHTML = 'Добавлено';
    }

    changeCartQuantity() {
        let totalQnt = 0;
        let totalPrice = 0;

        document.querySelectorAll('.cart > .product').forEach(product => {
            const qnt = Number(product.querySelector('input').value);
            const price = parseFloat(product.querySelector('.product_price span').innerText.match(/\d+/)[0]);

            if (!isNaN(qnt)) {
                totalQnt += qnt;
                if (!isNaN(price))
                    totalPrice += (price * qnt);
            }
        });

        const total = document.getElementById('total-price');
        if (total) total.innerText = totalPrice.toString();
    }

    async add(id, quantity = 1) {
        try {
            const { data } = await axios.post(`/cart/${id}`, { total: quantity });
            document.querySelector('.cart .quantity').innerText = data.total;
            this.changeCartQuantity();
        }
        catch (e) {
            return new Error(e);
        }
    }

    async remove(id) {
        try {
            const { data } = await axios.delete(`/cart/${id}`);
            document.querySelector('.cart .quantity').innerText = data.total;
        }
        catch (e) {
            return new Error('Ошибка! Не удалось удалить товар из корзины');
        }
    }

    async quantity(id, quantity) {
        try {
            const { data } = await axios.put(`/cart/${id}`, { quantity });
            document.querySelector('.cart .quantity').innerText = data.total;
            this.changeCartQuantity();
        }
        catch (e) {
            return new Error(e);
        }
    }
}

new Cart().listen();
