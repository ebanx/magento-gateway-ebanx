var ebanxRemoveOSCRequireDocument = function (country) {
  var doc = document.getElementById('billing:taxvat');
  var advice = document.getElementById('advice-validate-taxvat-billing:taxvat');

  if (!doc) {
    return;
  }
  if (!doc.classList.contains('validate-taxvat')){
    doc.classList.add('validate-taxvat');
  }
  if (!(country === 'BR' || country === 'CO' || country === 'CL')) {
    doc.classList.remove('validate-taxvat');
    doc.classList.remove('validation-failed');
    if (advice){
      advice.remove();
    }
  }

};
