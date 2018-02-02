/* global Validation */

Validation.add('brand-required', ' ', (v) => {
  return !Validation.get('IsEmpty').test(v);
});
