'use strict';

var defaultLabel;
var taxVatLabel;

var qs = function (el) {
  return document.querySelector(el);
};

var getLabelByCountry = function (country, defaultLabel) {
  switch (country.toLowerCase()) {
    case 'br':
      return 'CPF / CNPJ';
    case 'co':
      return 'DNI';
    case 'cl':
      return 'RUT';
    default:
      return defaultLabel;
  }
};

var changeTaxVatLabel = function () {
  var country = this.value;
  var newLabel = getLabelByCountry(country, defaultLabel);

  taxVatLabel.innerHTML = newLabel;
};

var init = function () {
  taxVatLabel = qs('label[for="billing\\:taxvat"]');

  if (taxVatLabel) {
    var countrySelect = qs('#billing\\:country_id');
    defaultLabel = taxVatLabel.innerHTML;

    countrySelect.addEventListener('change', changeTaxVatLabel);
    countrySelect.dispatchEvent(new Event('change'));
  }
};

document.addEventListener('DOMContentLoaded', init);
