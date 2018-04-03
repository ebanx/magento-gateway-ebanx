/* global EBANXData */
/* global amsCheckoutHandler */
/* global ebanxRemoveOSCRequireDocument */
/* global inputHandler */

let defaultLabel = null;
let taxVatLabel= document.querySelector('label[for="billing\\:taxvat"]');
const taxVatInput = document.getElementById('billing:taxvat');
const countrySelect = document.querySelector('#billing\\:country_id');

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

  inputHandler(taxVatInput, country);
};

const init = () => {
  const div = document.createElement('li');
  div.className = 'fields';
  div.innerHTML = `<div class="field">
    <label for="billing:ebanx_document_type" class="required">
        Document Type
    </label>
    <div class="input-box">
        <input type="text" name="billing[ebanx_document_type]" id="billing:ebanx_document_type" title="Document Type" class="input-text required-entry" />
    </div>
</div>`;
  document.getElementById('billing:taxvat').parentNode.parentNode.parentNode.insertBefore(div, document.getElementById('billing:taxvat').previousElementSibling);
  if (!EBANXData.maskTaxVat) {
    return;
  }

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
