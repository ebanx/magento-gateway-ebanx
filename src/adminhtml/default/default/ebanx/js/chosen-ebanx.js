/* global $$ */
/* global Chosen */

document.observe('dom:loaded', () => {
  const ebanxSelectFields = $$('#payment_ebanx_settings select[multiple], #payment_ebanx_settings select[id$="_status"]');
  if (ebanxSelectFields.length) {
    ebanxSelectFields.each((el) => {
      new Chosen(el, {
        placeholder_text: ' ',
        no_results_text: 'Press Enter to add',
        width: '270px',
      });
    });
  }
});
