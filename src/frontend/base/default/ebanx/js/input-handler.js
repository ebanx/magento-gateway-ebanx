/* global VMasker */

const maskValues = {
  br: {
    masks: ['999.999.999-999', '99.999.999/9999-99'],
    changeOnLenght: 14,
  },
  ar: {
    masks: ['SSSSSSSS', 'SS-SSSSSSSS-S'],
    changeOnLenght: 8,
  },
  co: {
    masks: ['999999999999999999'],
    changeOnLenght: 0,
  },
  cl: {
    masks: ['99.999.999-S'],
    changeOnLenght: 0,
  },
};

const handler = (masks, max, element) => {
  let toggleMask = 0;
  if(max !== 0){
    toggleMask = element.value.length > max ? 1 : 0;
  }
  VMasker(element).unMask();
  VMasker(element).maskPattern(masks[toggleMask]);
  element.value = VMasker.toPattern(element.value, masks[toggleMask]);
};

const inputHandler = (inputDoc, country) => { // eslint-disable-line no-unused-vars
  if (country === 'AR') {
    inputDoc.classList.add('validate-ar-document-length');
  }
  if (maskValues[country.toLowerCase()] && inputDoc) {
    const docMaskValues = maskValues[country.toLowerCase()];
    VMasker(inputDoc).maskPattern(docMaskValues.masks[0]);
    inputDoc.addEventListener('input', (e) => { handler(docMaskValues.masks, docMaskValues.changeOnLenght, e.target); }, false);
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

const selectOption = (elements, option) => { // eslint-disable-line no-unused-vars
  if (!elements || elements === null) {
    return;
  }

  elements.forEach((elem) => {
    if(elem.value === option) {
      elem.setAttribute('selected', 'selected');
    }
  });
};
