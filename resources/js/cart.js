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

const cartModal = function () {
    const card = this.closest('[data-product]');
    const id = card.getAttribute('data-product');
    const productModal = document.querySelector('.product-modal');
    productModal.querySelector('button.btn-number').setAttribute('data-product', id);
    productModal.querySelector('input.input-number').setAttribute('data-product', id);

    const name = card.querySelector('.card-title').innerText;
    const price = card.querySelector('.card-text .price').innerHTML;
    const img = card.querySelector('.card-img-top').getAttribute('src');
    productModal.querySelector('img').setAttribute('src', img);
    productModal.querySelector('span.name').innerText = name;
    productModal.querySelector('span.price').innerHTML = price;
    productModal.querySelector('.price_mask').addEventListener('click', () => showPrice(this));
}

document.querySelectorAll("[data-action='remove']").forEach(item => {
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

document.querySelectorAll("[data-type='product']").forEach(item => {
    item.addEventListener('newCart', async (event) => {
        const modal = event.target;
        const product = event.detail.product;
        const title = product.querySelector('.card-title');
        const btn = product.querySelector("[data-toggle='modal']");

        try {
            await cartAdd(product.getAttribute('data-product'));
        }
        catch (e) {
            alertMessage(e);
        }

        modal.setAttribute('data-product', product.getAttribute('data-product'));
        modal.querySelector('.name').innerHTML = title.innerHTML;
        modal.querySelector('.price').innerHTML = product.querySelector('.price').innerHTML;
        modal.querySelector('.input-group input').setAttribute('max', product.querySelector('[data-max]').getAttribute('data-max'));
        modal.querySelector('img').setAttribute('alt', title.innerText);
        modal.querySelector('img').setAttribute('src', product.querySelector('.card-img-top').getAttribute('src'));

        btn.removeAttribute('data-toggle');
        btn.removeAttribute('data-target');
        btn.removeAttribute('data-max');
        btn.innerHTML = 'Добавлено';
    })
});
