import IMask from "imask";

function replacer () {
  if (this.value.length === 1 && !this.value.startsWith('+')) {
    this.value = '7' + this.value.replace(/^[78]/, '');
  }
}
document.getElementById('login').addEventListener('input', replacer);
document.getElementById('phone').addEventListener('input', replacer);

const loginMask = IMask(document.getElementById('login'), {mask: '+0 (000) 000-00-00'});
const phoneMask = IMask(document.getElementById('phone'), {mask: '+0 (000) 000-00-00'});

const mask = function (event) {
    event.preventDefault();
    switch (this.name) {
        case 'login':
            this.login.value = loginMask.unmaskedValue;
            this.submit();
            break;
        case 'edit':
            this.phone.value = phoneMask.unmaskedValue;
            this.submit();
            break;
        case 'register':
            const data = {
                email: this.email.value,
                name: this.name.value,
                phone: phoneMask.unmaskedValue,
                password: this.password.value,
                password_confirmation: this.password_confirmation.value
            }
            axios.post(this.action, data)
                .then(data => location.reload())
                .catch(error => console.log(error));
            break;
        default:
            this.submit();
    }
}

for (const item of document.forms)
    item.addEventListener('submit', mask);
