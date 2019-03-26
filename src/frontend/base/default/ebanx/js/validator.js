/* global Validation */

Validation.add('brand-required', ' ', (v) => {
  return !Validation.get('IsEmpty').test(v);
});

Validation.add('validate-ar-document-length', 'Document digit number is invalid', (v) => {
  const documentSelector = document.querySelector('#ebanx-document-type-ebanx_cc_ar');
  if (documentSelector.value === 'ARG_DNI') {
    return (v.length === 7 || v.length === 8);
  } else {
    return v.length === 13;
  }
});
