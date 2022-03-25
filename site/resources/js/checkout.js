document.querySelectorAll('.radio-button input[name=delivery]').forEach((item, i, items) => {
  item.addEventListener('click', () => {
    items.forEach(elem => elem.parentNode.classList.remove('active'));
    item.parentNode.classList.add('active');
  });
});

document.querySelectorAll('.radio-button input[name=payment]').forEach((item, i, items) => {
  item.addEventListener('click', () => {
    items.forEach(elem => elem.parentNode.classList.remove('active'));
    item.parentNode.classList.add('active');
  });
});
