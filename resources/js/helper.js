const token = document.head.querySelector('meta[name="csrf-token"]');
window.axios = require('axios');
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.interceptors.response.use(config => config, error => {
    if (error.response.status) {
        if (error.response.data?.message)
            return Promise.reject(error.response.data.message);
        return Promise.reject(error.response.statusText);
    }
    return Promise.reject(error.response.message);
});
// if (token)
//     window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
// else
//     console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');

window.alertMessage = (message, type = 'danger', target = null) => {
    if (typeof message === 'object') message = Object.values(message).pop();
    const alert = document.createElement('div');
    alert.classList.add('alert', 'alert-' + type);
    alert.setAttribute('role', 'alert');
    alert.innerText = message;
    if (target) target.append(alert);
    else document.getElementById('flash').append(alert);

    const closeFlash = event => {
        event.target.remove();
        alert.removeEventListener('click', closeFlash);
    };
    alert.addEventListener('click', closeFlash);
}

window.showPrice = function () {
    const product = this.closest('[data-product]');
    const id = product.getAttribute('data-product');
    axios.get('/catalog/get-price', { params: { id } })
        .then(({data}) => {
            const mask = product.querySelector('.price .mask');
            const real = product.querySelector('.price .real');
            real.innerHTML = `<span style="font-size: 1.1rem">от ${data} &#8381;</span>`;
            mask.style.display = 'none';
            real.style.display = 'block';
        })
        .catch(error => {
            console.log(error);
            alertMessage(error);
        })
}
