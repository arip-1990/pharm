import IMask from "imask";

function replacer() {
  if (this.value.length === 1 && !this.value.startsWith('+')) {
    this.value = '7' + this.value.replace(/^[78]/, '');
  }
}

document.getElementById('login').addEventListener('input', replacer);
document.getElementById('phone').addEventListener('input', replacer);

const loginMask = IMask(document.getElementById('login'), {mask: '+0 (000) 000-00-00'});
const phoneMask = IMask(document.getElementById('phone'), {mask: '+0 (000) 000-00-00'});

const mask = event => {
  event.preventDefault();
  const target = event.target;
  switch (target.getAttribute('name')) {
    case 'login':
      target.login.value = loginMask.unmaskedValue;
      target.submit();
      break;
    case 'edit':
      target.phone.value = phoneMask.unmaskedValue;
      target.submit();
      break;
    case 'register':
      const data = {
        email: target.email.value,
        name: target.name.value,
        phone: phoneMask.unmaskedValue,
        password: target.password.value,
      }
      axios.post(target.action, data)
        .then(data => location.reload())
        .catch(error => console.log(error));
      break;
    default:
      target.submit();
  }
}

for (const item of document.forms) {
  item.addEventListener('submit', mask);
}
