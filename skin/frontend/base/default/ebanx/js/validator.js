Validation.add('brand-required', ' ', function(v) {
  return !Validation.get('IsEmpty').test(v);
});
