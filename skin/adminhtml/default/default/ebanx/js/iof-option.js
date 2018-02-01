document.observe("dom:loaded", function () {
  var iofLocalAmount = document.querySelector('#payment_ebanx_settings_iof_local_amount');
  var confirmed;

  var renderIofAlert = function (el) {
    confirmed = confirm('You need to validate this change with EBANX, only deselecting or selecting the box will ' +
      'not set this to your customer. Contact your EBANX Account Manager or Business Development Expert.');
    if (!confirmed) {
      el.target.value = 1 - el.target.value;
    }
  };
  iofLocalAmount.addEventListener('change', renderIofAlert);
});


