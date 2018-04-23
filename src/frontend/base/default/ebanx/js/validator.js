/* global Validation */

Validation.add('brand-required', ' ', (v) => {
  return !Validation.get('IsEmpty').test(v);
});

Validation.add('validate-ar-document-length', 'Document must have 11 digits', (v) => {
  return v.length === 13;
});
