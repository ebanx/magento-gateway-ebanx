/* global VMasker */
/* global EBANXData */
/* global amsCheckoutHandler */
/* global ebanxRemoveOSCRequireDocument */

let defaultLabel;
let taxVatLabel;
let taxVatInput;

const qs = function(el) {
  return document.querySelector(el);
};

function inputHandler(masks, max, event) {
  const c = event.target;
  const v = c.value.replace(/\D/g, '');
  const m = c.value.length > max ? 1 : 0;
  VMasker(c).unMask();
  VMasker(c).maskPattern(masks[m]);
  c.value = VMasker.toPattern(v, masks[m]);
}

const getLabelByCountry = (country, defaultLabel) => {
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

const OSCRequire = country => {
  if (typeof ebanxRemoveOSCRequireDocument !== 'undefined') {
    ebanxRemoveOSCRequireDocument(country);
  }
};

const changeTaxVatLabel = () => {
  const country = this.value;
  const newLabel = getLabelByCountry(country, defaultLabel);

  OSCRequire(country);

  setTimeout(
    function() {
      taxVatInput.placeholder = '';
    },
    10
  );

  taxVatLabel.innerHTML = newLabel;
  if (country === 'BR' || country === 'CO' || country === 'CL') {
    setTimeout(
      () => {
        taxVatInput.placeholder = newLabel;
      },
      10
    );
  }

  VMasker(taxVatInput).unMask();

  if (country === 'BR') {
    const taxVatMask = newLabel.indexOf('CNPJ') !== -1
      ? [ '999.999.999-99', '99.999.999/9999-99' ]
      : [ '999.999.999-99', '999.999.999-99' ];
    VMasker(taxVatInput).maskPattern(taxVatMask[0]);
    taxVatInput.addEventListener(
      'input',
      inputHandler.bind(undefined, taxVatMask, 14),
      false
    );
  }
};

const init = () => {
  if (!EBANXData.maskTaxVat) {
    return;
  }

  const countrySelect = qs('#billing\\:country_id');
  taxVatLabel = qs('label[for="billing\\:taxvat"]');
  taxVatInput = document.getElementById('billing:taxvat');

  if (!taxVatLabel && typeof amsCheckoutHandler === 'function') {
    taxVatLabel = amsCheckoutHandler();
  }

  if (taxVatLabel && countrySelect && taxVatInput) {
    defaultLabel = taxVatLabel.innerHTML;

    countrySelect.addEventListener('change', changeTaxVatLabel);
    countrySelect.dispatchEvent(new Event('change'));
  }
};

document.addEventListener('DOMContentLoaded', init);
