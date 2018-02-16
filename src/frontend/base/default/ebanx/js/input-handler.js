/* global VMasker */

const handler = (masks, max, element) => {
  const value = element.value.replace(/\D/g, '');
  const toggleMask = element.value.length > max ? 1 : 0;
  VMasker(element).unMask();
  VMasker(element).maskPattern(masks[toggleMask]);
  element.value = VMasker.toPattern(value, masks[toggleMask]);
};

const inputHandler = (inputDoc, country) => { // eslint-disable-line no-unused-vars
  if (country.toLowerCase() === 'br') {
    const docMask = ['999.999.999-999', '99.999.999/9999-99'];
    VMasker(inputDoc).maskPattern(docMask[0]);
    inputDoc.addEventListener('input', (e) => { handler(docMask, 14, e.target); }, false);
  }
};

const initTaxVatLabel = (label) => { // eslint-disable-line no-unused-vars
  const taxvatLabel = document.querySelector('label[for="taxvat"]');
  if (taxvatLabel) {
    taxvatLabel.innerHTML = label;
  }
};

const hideTaxVat = () => { // eslint-disable-line no-unused-vars
  const taxvatLabel = document.querySelector('label[for="taxvat"]');
  if (taxvatLabel) {
    taxvatLabel.style.display = 'none';
  }

  const taxvat = document.getElementById('taxvat');
  if (taxvat) {
    taxvat.style.display = 'none';
  }
};
