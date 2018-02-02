/* global Clipboard */

document.addEventListener('DOMContentLoaded', () => {
  const clipboard = new Clipboard('.ebanx-button--copy');
  const iframe = document.querySelector('.ebanx-cash-payment iframe');

  clipboard.on('success', e => {
    const text = e.trigger.innerText;

    e.trigger.innerText = 'Copiado!';

    setTimeout(
      () => {
        e.trigger.innerText = text;
      },
      2000
    );
  });

  const resizeIframe = iframe => {
    iframe.style.height = `${iframe.contentWindow.document.body.parentElement.scrollHeight} px`;
  };

  if (iframe) {
    window.addEventListener('load', () => {
      resizeIframe(iframe);
    });

    iframe.contentWindow.addEventListener('resize', () => {
      resizeIframe(iframe);
    });
  }
});
