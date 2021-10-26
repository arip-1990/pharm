const changeCartQuantity = (quantity = null) => {
    let totalQnt = 0;
    let totalPrice = 0;
    const cartQnt = document.querySelector('.cart .quantity');
    if (quantity)
        totalQnt = Number(cartQnt.innerText) + quantity;

    const products = document.querySelectorAll('.cart > .product');
    if (products.length) {
        totalQnt = 0;
        for (let product of products) {
            const qnt = Number(product.querySelector('input').value);
            const price = parseFloat(product.querySelector('.product_price span').innerText.match(/\d+/)[0]);

            if (!isNaN(qnt)) {
                totalQnt += qnt;
                if (!isNaN(price))
                    totalPrice += (price * qnt);
            }
        }
    }

    const total = document.getElementById('total-price');
    if (total)
        total.innerText = totalPrice.toString();
    cartQnt.innerText = totalQnt.toString();
}

const cartAdd = function () {
    const product = this.closest('[data-product]');
    const id = product.getAttribute('data-product');
    const total = parseInt(product.querySelector('.input-group .input-number').value);

    axios.post('/cart/' + id, { total })
        .then(data => {
            changeCartQuantity(total);
            product.querySelector("[data-toggle='modal']").innerHTML = 'Добавлено';
        })
        .catch(error => alertMessage('Ошибка!', error));
}
const cartRemove = function () {
    const product = this.closest('[data-product]');
    axios.delete('/cart/' + product.getAttribute('data-product'))
        .then(() => {
            product.remove();
            changeCartQuantity();
        })
        .catch(error => {
            alertMessage('Ошибка! Не удалось удалить товар из корзины')
        });
}
const cartQuantity = (id, quantity, minus = false) => {
    axios.put('/cart/' + id, {quantity})
        .then(response => minus ? changeCartQuantity(-1) : changeCartQuantity(1))
        .catch(error => alertMessage('Ошибка! Не удалось изменить товар из корзины'));
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

document.querySelectorAll("[data-action='cart-add']").forEach(item => {
   item.addEventListener('click', cartAdd);
});
document.querySelectorAll("[data-action='cart-remove']").forEach(item => {
    item.addEventListener('click', cartRemove);
});

// document.querySelectorAll('[data-product] .input-group .btn').forEach(element => {
//     element.addEventListener('click', function (event) {
//         event.preventDefault();
//
//         const input = this.closest('.input-group').querySelector('input');
//         const type = this.getAttribute('data-type');
//         const productId = this.closest('[data-product]').getAttribute('data-product');
//         const currentVal = parseInt(input.value);
//
//         if (!isNaN(currentVal)) {
//             if(type === 'minus') {
//                 if(currentVal > parseInt(input.getAttribute('min'))) {
//                     input.value = String(currentVal - 1);
//                     cartQuantity(productId, currentVal - 1, true);
//                 }
//                 if(parseInt(input.value) <= parseInt(input.getAttribute('min'))) {
//                     this.setAttribute('disabled', true);
//                 }
//             }
//             else if(type === 'plus') {
//                 if(currentVal < parseInt(input.getAttribute('max'))) {
//                     input.value = String(currentVal + 1);
//                     cartQuantity(productId, currentVal + 1);
//                 }
//                 if(parseInt(input.value) >= parseInt(input.getAttribute('max'))) {
//                     this.setAttribute('disabled', true);
//                 }
//             }
//         }
//         else {
//             input.value = String(0);
//         }
//
//         input.dispatchEvent(new Event('change'));
//     })
// });
// document.querySelectorAll('[data-product] .input-group .input-number').forEach(element => {
//     element.addEventListener('change', function () {
//         const minValue = parseInt(this.getAttribute('min'));
//         const maxValue = parseInt(this.getAttribute('max'));
//         const currentValue = parseInt(this.value);
//         const productId = this.closest('[data-product]').getAttribute('data-product');
//
//         if (currentValue > minValue)
//             this.closest('.input-group').querySelector('.btn[data-type=minus]').removeAttribute('disabled');
//         if (currentValue < maxValue)
//             this.closest('.input-group').querySelector('.btn[data-type=plus]').removeAttribute('disabled');
//
//         if (currentValue < minValue) {
//             this.value = minValue;
//             cartQuantity(productId, minValue);
//             alertMessage('Количества товара ниже минимума для заказа!');
//         }
//         else if (currentValue > maxValue) {
//             this.value = maxValue;
//             cartQuantity(productId, maxValue, true);
//             alertMessage('Количество превышает допустимое!');
//         }
//     })
// });
// document.querySelectorAll('[data-product] .input-group .input-number').forEach(element => {
//     element.addEventListener('keydown', event => {
//         // Allow: backspace, delete, tab, escape, enter and .       // Allow: Ctrl+A                            // Allow: home, end, left, right
//         if ([46, 8, 9, 27, 13, 190].includes(event.keyCode) || (event.keyCode === 65 && event.ctrlKey === true) || (event.keyCode >= 35 && event.keyCode <= 39))
//             return;
//
//         // Ensure that it is a number and stop the keypress
//         if ((event.shiftKey || (event.keyCode < 48 || event.keyCode > 57)) && (event.keyCode < 96 || event.keyCode > 105))
//             event.preventDefault();
//     })
// });

document.querySelector("[data-type='product']").addEventListener('newCart', (event) => {
    const modal = event.target;
    const product = event.detail.product;
    const title = product.querySelector('.card-title');

    modal.setAttribute('data-product', product.getAttribute('data-product'));
    modal.querySelector('.name').innerHTML = title.innerHTML;
    modal.querySelector('.price').innerHTML = product.querySelector('.price').innerHTML;
    modal.querySelector('.input-group input').setAttribute('max', product.querySelector('[data-max]').getAttribute('data-max'));
    modal.querySelector('img').setAttribute('alt', title.innerText);
    modal.querySelector('img').setAttribute('src', product.querySelector('.card-img-top').getAttribute('src'));
});
