/* global EBANXData */
/* global amsCheckoutHandler */
/* global ebanxRemoveOSCRequireDocument */
/* global inputHandler */

let defaultLabel = null;
let taxVatLabel = null;
let taxVatInput = null;
let countrySelect = null;

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
  const country = countrySelect.value;
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

const addDocumentTypeField = () => {
  if (countrySelect.value !== 'AR' || document.getElementById('billing:ebanx_document_type')) {
    return;
  }

  const div = document.createElement('li');
  div.className = 'fields';
  div.innerHTML = `<div class="field">
      <label for="billing:ebanx_document_type" class="required">
          Document Type
      </label>
      <div class="input-box">
          <select name="billing[ebanx_document_type]" id="billing:ebanx_document_type" title="Document Type" class="validate-select required-entry">
            <option value="ARG_CUIT">CUIT</option>
            <option value="ARG_CUIL">CUIL</option>
            <option value="ARG_CDI">CDI</option>
          </select>
      </div>
    </div>`;
  const ul = taxVatInput.closest('ul');
  ul.insertBefore(div, taxVatInput.closest('li'));
};

const init = () => {
  taxVatLabel= document.querySelector('label[for="billing\\:taxvat"]');
  taxVatInput = document.getElementById('billing:taxvat');
  countrySelect = document.querySelector('#billing\\:country_id');

  if (!EBANXData.maskTaxVat && !taxVatLabel && !taxVatInput && taxVatInput === null) {
    return;
  }

  addDocumentTypeField();

  if (!taxVatLabel && typeof amsCheckoutHandler === 'function') {
    taxVatLabel = amsCheckoutHandler();
  }

  if (countrySelect) {
    defaultLabel = taxVatLabel.innerHTML;

    countrySelect.addEventListener('change', changeTaxVatLabel);
    countrySelect.addEventListener('change', addDocumentTypeField);
    countrySelect.dispatchEvent(new Event('change'));
  }
};

document.addEventListener('DOMContentLoaded', init);
