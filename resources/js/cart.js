class Cart {
    constructor() {
        this.cart = document.querySelector('.cart .quantity');

        document.querySelectorAll('[data-type=product]').forEach(element => {
            element.addEventListener('newCart', (e) => this.newCartListener(e, element));
        });
    }

    async newCartListener(event, element) {
        const modal = event.target;
        const name = element.querySelector('[itemprop=name]');
        const btn = element.querySelector('[data-toggle=modal]');

        try {
            await this.add(element.getAttribute('data-product'));
        }
        catch (e) {
            alertMessage(e);
        }

        modal.setAttribute('data-product', element.getAttribute('data-product'));
        modal.querySelector('.title').innerHTML = name.innerHTML;
        modal.querySelector('.price').innerHTML = element.querySelector('[itemprop=price]').innerHTML;
        modal.querySelector('.input-group input').setAttribute('max', element.querySelector('[data-max]').getAttribute('data-max'));
        modal.querySelector('img').setAttribute('alt', name.innerText);
        modal.querySelector('img').setAttribute('src', element.querySelector('[itemprop=image]').getAttribute('src'));

        btn.removeAttribute('data-toggle');
        btn.removeAttribute('data-target');
        btn.removeAttribute('data-max');
        btn.innerHTML = 'Добавлено';
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
            changeCartQuantity();
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
            changeCartQuantity();
        }
        catch (e) {
            return new Error(e);
        }
    }
}


const changeCartQuantity = () => {
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

const cartAdd = async (id, quantity = 1) => {
    try {
        const { data } = await axios.post(`/cart/${id}`, { total: quantity });
        document.querySelector('.cart .quantity').innerText = data.total;
        changeCartQuantity();
    }
    catch (e) {
        return new Error(e);
    }
}
const cartRemove = async (id) => {
    try {
        const { data } = await axios.delete(`/cart/${id}`);
        document.querySelector('.cart .quantity').innerText = data.total;
    }
    catch (e) {
        return new Error('Ошибка! Не удалось удалить товар из корзины');
    }
}
const cartQuantity = async (id, quantity) => {
    try {
        const { data } = await axios.put(`/cart/${id}`, { quantity });
        document.querySelector('.cart .quantity').innerText = data.total;
        changeCartQuantity();
    }
    catch (e) {
        return new Error(e);
    }
}

document.querySelectorAll('.cart .cart-remove').forEach(item => {
    item.addEventListener('click', async function () {
        const product = this.closest('[data-product]');
        try {
            await cartRemove(product.getAttribute('data-product'));
            product.remove();
            changeCartQuantity();
        }
        catch (e) {
            alertMessage(e);
        }
    });
});

document.querySelectorAll('[data-product] .input-group .btn').forEach(element => {
    element.addEventListener('click', function (event) {
        event.preventDefault();
        const input = this.closest('.input-group').querySelector('input');
        const currentVal = parseInt(input.value);

        if (!isNaN(currentVal)) {
            if(this.getAttribute('data-type') === '-') {
                if(currentVal > parseInt(input.getAttribute('min'))) {
                    input.value = String(currentVal - 1);
                }
                if(parseInt(input.value) <= parseInt(input.getAttribute('min'))) {
                    this.setAttribute('disabled', true);
                }
            }
            else if(this.getAttribute('data-type') === '+') {
                if(currentVal < parseInt(input.getAttribute('max'))) {
                    input.value = String(currentVal + 1);
                }
                if(parseInt(input.value) >= parseInt(input.getAttribute('max'))) {
                    this.setAttribute('disabled', true);
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
    element.addEventListener('change', async function () {
        const minValue = parseInt(this.getAttribute('min'));
        const maxValue = parseInt(this.getAttribute('max'));
        const currentValue = parseInt(this.value);
        const productId = this.closest('[data-product]').getAttribute('data-product');

        if (currentValue > minValue)
            this.closest('.input-group').querySelector(".btn[data-type='-']").removeAttribute('disabled');
        if (currentValue < maxValue)
            this.closest('.input-group').querySelector(".btn[data-type='+']").removeAttribute('disabled');

        if (currentValue < minValue) {
            this.value = minValue;
            try {
                await cartQuantity(productId, minValue);
            }
            catch (e) {
                alertMessage('Ошибка!');
            }
        }
        else if (currentValue > maxValue) {
            this.value = maxValue;
            try {
                await cartQuantity(productId, maxValue);
            }
            catch (e) {
                alertMessage('Ошибка!');
            }
        }
        else {
            try {
                await cartQuantity(productId, currentValue);
            }
            catch (e) {
                alertMessage('Ошибка!');
            }
        }
    })
});
document.querySelectorAll('[data-product] .input-group .input-number').forEach(element => {
    element.addEventListener('keydown', event => {
        // Allow: backspace, delete, tab, escape, enter and .       // Allow: Ctrl+A                            // Allow: home, end, left, right
        if ([46, 8, 9, 27, 13, 190].includes(event.keyCode) || (event.keyCode === 65 && event.ctrlKey === true) || (event.keyCode >= 35 && event.keyCode <= 39))
            return;

        // Ensure that it is a number and stop the keypress
        if ((event.shiftKey || (event.keyCode < 48 || event.keyCode > 57)) && (event.keyCode < 96 || event.keyCode > 105))
            event.preventDefault();
    })
});

new Cart();
