document.addEventListener('DOMContentLoaded', function () {
  var button = document.getElementById('product-oneclick-ebanx-button');
  var tooltip = document.getElementById('ebanx-one-click-tooltip');
  var close = document.getElementById('ebanx-one-click-close-button');
  var elements = document.getElementsByName('payment[selected_card]');

  if (elements.length) {
    elements[0].checked = true;
  }

  if (button) {
    button.addEventListener('click', function (e) {
      e.preventDefault();
      tooltip.classList.toggle('is-active');
    });
  }

  if (close) {
    close.addEventListener('click', function (e) {
      e.preventDefault();
      tooltip.classList.remove('is-active');
    });
  }
});
