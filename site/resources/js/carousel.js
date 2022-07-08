const backdrop = document.getElementById('backdrop');

let images = [];
let mainImg = 0;
let prevImg = 0;
let nextImg = 0;

const loadGallery = () => {
  const mainView = document.querySelector('.carousel .carousel-slide_view__main');
  const leftView = document.querySelector('.carousel .carousel-slide_view__left');
  const rightView = document.querySelector('.carousel .carousel-slide_view__right');

  if (images.length > 1) {
    if (mainView) mainView.style.backgroundImage = "url(" + images[mainImg] + ")";
    if (leftView) leftView.style.backgroundImage = "url(" + images[prevImg] + ")";
    if (rightView) rightView.style.backgroundImage = "url(" + images[nextImg] + ")";
  }
  else {
    if (mainView) mainView.style.backgroundImage = "url(" + images[mainImg] + ")";
    if (leftView) leftView.style.display = 'none';
    if (rightView) rightView.style.display = 'none';
  }
};
const nextView = () => {
  prevImg = mainImg;
  mainImg = nextImg;

  if (nextImg >= (images.length -1)) nextImg = 0;
  else nextImg++;

  loadGallery();
};
const prevView = () => {
  nextImg = mainImg
  mainImg = prevImg;

  if (prevImg === 0) prevImg = images.length - 1;
  else prevImg--;

  loadGallery();
};

const show = event => {
  event.preventDefault();
  const target = event.target.getAttribute('data-target');
  const carousel = document.querySelector(target);

  document.body.classList.add('modal-open');
  if (document.body.offsetHeight > window.innerHeight) {
    document.body.style.paddingRight = '17px';
    const fixedBox = document.querySelector('.fixed-box.fixed');
    if (fixedBox) fixedBox.style.paddingRight = '17px';
  }

  backdrop.removeAttribute('style');
  carousel.removeAttribute('style');
  carousel.removeAttribute('aria-hidden');

  setTimeout(() => {
    backdrop.classList.add('show');
    carousel.classList.add('show');
  }, 100);
}
const close = event => {
  const element = event.target;
  if (element.classList.contains('carousel') && element.classList.contains('show')) {
    event.preventDefault();

    const transitionHandler = function () {
      this.style.display = 'none';
      document.body.removeAttribute('style');
      document.querySelector('.fixed-box').removeAttribute('style');
      document.body.classList.remove('modal-open');
      this.removeEventListener('transitionend', transitionHandler);
    }

    element.classList.remove('show');
    element.setAttribute('aria-hidden', 'true');
    element.addEventListener('transitionend', transitionHandler);
    backdrop.classList.remove('show');
    backdrop.addEventListener('transitionend', transitionHandler);
  }
}

document.querySelectorAll('[data-toggle=carousel]').forEach(element => {
  images.push(element.getAttribute('src'));
  prevImg = images.length - 1; nextImg = images.length > 1 ? 1 : 0;

  element.addEventListener('click', show);
});

document.querySelectorAll('.carousel').forEach(element => {
  element.addEventListener('click', close);
});

document.querySelectorAll('.carousel .carousel-slide_view__right').forEach(item => {
  item.addEventListener("click", nextView);
});
document.querySelectorAll('.carousel .carousel-slide_view__left').forEach(item => {
  item.addEventListener("click", prevView);
});

document.addEventListener('keyup', event => {
  if (event.keyCode === 37) prevView();
  else if(event.keyCode === 39) nextView();
});

loadGallery();
