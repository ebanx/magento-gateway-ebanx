document.addEventListener('DOMContentLoaded', function () {
  var clipboard = new Clipboard('.ebanx-button--copy');

  // iFrame Resizer
  var iframe = document.querySelector('.ebanx-cash-payment iframe');

  if (iframe) {
    var resizeIframe = function resizeIframe(iframe) {
      iframe.style.height = iframe.contentWindow.document.body.parentElement.scrollHeight + 'px';
    }

    window.addEventListener('load', function () {
      resizeIframe(iframe);
    });

    iframe.contentWindow.addEventListener('resize', function () {
      resizeIframe(iframe);
    });
  }
});
