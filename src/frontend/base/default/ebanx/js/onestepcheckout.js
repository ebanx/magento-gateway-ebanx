var ebanxRemoveOSCRequireDocument = country => { // eslint-disable-line no-unused-vars
  const doc = document.getElementById('billing:taxvat');
  const advice = document.getElementById(
    'advice-validate-taxvat-billing:taxvat'
  );

  if (!doc.classList.contains('validate-taxvat')) {
    doc.classList.add('validate-taxvat');
  }
  if (!(country === 'BR' || country === 'CO' || country === 'CL')) {
    doc.classList.remove('validate-taxvat');
    doc.classList.remove('validation-failed');
    if (advice) {
      advice.remove();
    }
  }
};
