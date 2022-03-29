const zoom = event => {
  const zoomer = event.currentTarget;
  let offsetX = 0, offsetY = 0;
  event.offsetX ? offsetX = event.offsetX : offsetX = event.touches[0].pageX;
  event.offsetY ? offsetY = event.offsetY : offsetX = event.touches[0].pageX;
  zoomer.style.backgroundPosition = (offsetX / zoomer.offsetWidth * 100) + '% ' + offsetY / zoomer.offsetHeight * 100 + '%';
}

document.querySelectorAll('figure.zoom').forEach(element => {
  element.addEventListener('mousemove', zoom);
});

document.querySelectorAll('figure.zoom').forEach(element => {
  element.addEventListener('mouseleave', () => element.style = 'background-image: ' + element.style.backgroundImage);
});
