'use strict';

var defaultLabel;
var taxVatLabel;
var taxVatInput;

var qs = function (el) {
  return document.querySelector(el);
};

function inputHandler(masks, max, event) {
  var c = event.target;
  var v = c.value.replace(/\D/g, '');
  var m = c.value.length > max ? 1 : 0;
  VMasker(c).unMask();
  VMasker(c).maskPattern(masks[m]);
  c.value = VMasker.toPattern(v, masks[m]);
}

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

  OSCRequire(country);

  setTimeout(function(){taxVatInput.placeholder = '';}, 10);

  taxVatLabel.innerHTML = newLabel;
  if(taxVatInput && (country === 'BR' || country === 'CO' || country === 'CL')) {
      setTimeout(function(){taxVatInput.placeholder = newLabel;}, 10)
    }

  if (country === 'BR' && taxVatInput) {
    var taxVatMask = newLabel.indexOf('CNPJ') !== -1 ? ['999.999.999-99', '99.999.999/9999-99'] : ['999.999.999-99', '999.999.999-99'];
    VMasker(taxVatInput).maskPattern(taxVatMask[0]);
    taxVatInput.addEventListener('input', inputHandler.bind(undefined, taxVatMask, 14), false);
  }
};

var init = function () {
  var countrySelect = qs('#billing\\:country_id');
  taxVatLabel = qs('label[for="billing\\:taxvat"]');
  taxVatInput = document.getElementById('billing:taxvat');

  if (!taxVatLabel && typeof amsCheckoutHandler === 'function') {
    taxVatLabel = amsCheckoutHandler();
  }

  if (taxVatLabel && countrySelect) {
    defaultLabel = taxVatLabel.innerHTML;

    countrySelect.addEventListener('change', changeTaxVatLabel);
    countrySelect.dispatchEvent(new Event('change'));
  }
};

var OSCRequire = function (country) {
  if (typeof ebanxRemoveOSCRequireDocument !== 'undefined') {
    ebanxRemoveOSCRequireDocument(country);
  }
};

document.addEventListener('DOMContentLoaded', init);
