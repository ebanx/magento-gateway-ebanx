'use strict';

var defaultLabel;
var taxVatLabel;
var taxVatInput;

var qs = function (el) {
  return document.querySelector(el);
};

var getLabelByCountry = function (country, defaultLabel) {
  switch (country.toLowerCase()) {
    case 'br':
      return EBANXData.brazilAllowedDocumentFields.join(' / ').toUpperCase();
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
    if(taxVatInput) {
      setTimeout(function(){taxVatInput.placeholder = newLabel;}, 10)
    }
};

var init = function () {
  var countrySelect = qs('#billing\\:country_id');
  taxVatLabel = qs('label[for="billing\\:taxvat"]');
  taxVatInput = document.getElementById('billing:taxvat');

  if (taxVatLabel && countrySelect) {
    defaultLabel = taxVatLabel.innerHTML;

    countrySelect.addEventListener('change', changeTaxVatLabel);
    countrySelect.dispatchEvent(new Event('change'));
  }
};

document.addEventListener('DOMContentLoaded', init);
