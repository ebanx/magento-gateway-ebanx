/* global confirm */

document.observe('dom:loaded', () => {
  const iofLocalAmount = document.querySelector('#payment_ebanx_settings_iof_local_amount');
  if (!iofLocalAmount) {
    return;
  }
  let confirmed = null;

  const renderIofAlert = (el) => {
    confirmed = confirm(`You need to validate this change with EBANX, only deselecting or selecting the box will not set this to your customer. Contact your EBANX Account Manager or Business Development Expert.`); // eslint-disable-line
    if (!confirmed) {
      el.target.value = 1 - el.target.value;
    }
  };
  iofLocalAmount.addEventListener('change', renderIofAlert);
});
