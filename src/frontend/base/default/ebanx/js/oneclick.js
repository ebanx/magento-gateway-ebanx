/* global productAddToCartForm */

document.addEventListener('DOMContentLoaded', () => {
  const button = document.getElementById('product-oneclick-ebanx-button');
  const tooltip = document.getElementById('ebanx-one-click-tooltip');
  const close = document.getElementById('ebanx-one-click-close-button');
  const elements = document.getElementsByName('payment[selected_card]');
  const payButton = document.getElementById('ebanx-oneclick-pay-button');
  const form = document.getElementById('product_addtocart_form');

  if (elements.length) {
    elements[0].checked = true;
  }

  if (button) {
    button.addEventListener('click', e => {
      e.preventDefault();
      tooltip.classList.toggle('is-active');
    });
  }

  if (close) {
    close.addEventListener('click', e => {
      e.preventDefault();
      tooltip.classList.remove('is-active');
    });
  }

  if (payButton && form) {
    payButton.addEventListener('click', () => {
      form.action = '/ebanx/oneclick/pay';
      productAddToCartForm.submit(payButton);
    });
  }
});
