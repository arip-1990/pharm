const backdrop = document.getElementById('backdrop');

const closeAll = () => {
  const transitionHandler = function () {
    this.removeAttribute('style');
    this.removeEventListener('transitionend', transitionHandler);
  }

  document.querySelectorAll('#modal > .modal').forEach(element => {
    if (element.classList.contains('show')) {
      element.classList.remove('show');
      element.removeAttribute('aria-modal');
      element.setAttribute('aria-hidden', 'true');
      element.addEventListener('transitionend', transitionHandler);
    }
  });
}
const show = event => {
  event.preventDefault();
  const target = event.target.getAttribute('data-target');
  const modal = document.querySelector(`#modal [data-type='${target}']`);
  closeAll();

  document.body.classList.add('modal-open');
  if (document.body.offsetHeight > window.innerHeight) {
    document.body.style.paddingRight = '17px';
    const fixedBox = document.querySelector('.fixed-box.fixed');
    if (fixedBox) fixedBox.style.paddingRight = '17px';
  }
  backdrop.removeAttribute('style');

  modal.style.display = 'block';
  modal.removeAttribute('aria-hidden');
  modal.setAttribute('aria-modal', 'true');

  setTimeout(() => {
    if (target === 'product') {
      event.target.dispatchEvent(new CustomEvent('newCart'));
      event.target.removeEventListener('click', show);
    }
    backdrop.classList.add('show');
    modal.classList.add('show');
  }, 100);
}
const close = event => {
  event.preventDefault();
  const transitionHandler = function () {
    this.style.display = 'none';
    document.body.removeAttribute('style');
    document.querySelector('.fixed-box').removeAttribute('style');
    document.body.classList.remove('modal-open');
    this.removeEventListener('transitionend', transitionHandler);
  }

  closeAll();
  backdrop.classList.remove('show');
  backdrop.addEventListener('transitionend', transitionHandler);
}

document.querySelectorAll('[data-toggle=modal]').forEach(element => {
  element.addEventListener('click', show);
});
document.querySelectorAll('[data-dismiss=modal]').forEach(element => {
  element.addEventListener('click', close);
});

document.querySelectorAll('#modal > .modal').forEach(element => {
  element.addEventListener('click', event => {
    if (event.target.classList.contains('modal') && event.target.classList.contains('show'))
      close(event);
  })
});
