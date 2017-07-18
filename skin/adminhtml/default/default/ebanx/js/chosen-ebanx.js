document.observe("dom:loaded", function() {
  var ebanxSelectFields = $$('#payment_ebanx_settings select[multiple], #payment_ebanx_settings select[id$="_status"]');
  if (ebanxSelectFields.length) {
    ebanxSelectFields.each(function (el) {
      new Chosen(el, {
        placeholder_text: ' ',
        no_results_text: 'Press Enter to add',
        width: '270px'
      });
    });
  }
});
