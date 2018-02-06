/* global EBANX */
/* global Validation */

const hasClass = (element, cls) => {
  return (` ${element.className} `).indexOf(` ${cls} `) > -1;
};

const resetValidations = (form, selector) => {
  Array.from(form.querySelectorAll(selector)).forEach((inputRequired) => {
    inputRequired.classList.remove('required-entry', 'validation-failed', 'brand-required');
    if (inputRequired.nextElementSibling && hasClass(inputRequired.nextElementSibling, 'validation-advice')) {
      inputRequired.nextElementSibling.style.display = 'none';
    }
  });
};

const addRequiredClassToInputs = (inputNodeList, validationClass, form, selector) => {
  resetValidations(form, selector);
  Array.from(inputNodeList).forEach((inputToValidate) => {
    inputToValidate.classList.add(validationClass);
  });
};

const validationFormListener = (form, creditCardOptions) => {
  const inputSelector = '.required-entry-input';
  const selectSelector = '.required-entry-select';
  Array.from(creditCardOptions).forEach((cardOption) => {
    cardOption.querySelector('input[type=radio]').addEventListener('change', (event) => {
      console.log(event.target);
      addRequiredClassToInputs(event.target.parentElement.querySelectorAll(inputSelector), 'required-entry', form, inputSelector);
      addRequiredClassToInputs(event.target.parentElement.querySelectorAll(selectSelector), 'validate-select', form, selectSelector);
    });
  });
};

const initCreditCardOption = (creditCardOption, form) => {
  const element = creditCardOption.querySelector('input[type=radio]');
  const inputSelector = '.required-entry-input';
  element.checked = true;
  addRequiredClassToInputs(element.parentElement.querySelectorAll(inputSelector), 'required-entry', form, inputSelector);
};

var handleEbanxForm = (countryCode, paymentType, formId) => { // eslint-disable-line no-unused-vars
  const form = document.querySelector(`#${formId}`);
  const creditCardOptions = form.querySelectorAll('.ebanx-credit-card-option');

  validationFormListener(form, creditCardOptions);
  initCreditCardOption(creditCardOptions[0], form);

  const getById = function (element) {
    return document.getElementById(element);
  };
  let responseData = null;

  const cardName = getById('ebanx_' + paymentType + '_' + countryCode + '_' + paymentType + '_name');
  const cardNumber = getById('ebanx_' + paymentType + '_' + countryCode + '_' + paymentType + '_number');
  const cardExpirationMonth = getById('ebanx_' + paymentType + '_' + countryCode + '_expiration');
  const cardExpirationYear = getById('ebanx_' + paymentType + '_' + countryCode + '_expiration_yr');
  const cardCvv = getById('ebanx_' + paymentType + '_' + countryCode + '_' + paymentType + '_cid');
  const ebanxToken = getById('ebanx_' + paymentType + '_' + countryCode + '_token');
  const ebanxBrand = getById('ebanx_' + paymentType + '_' + countryCode + '_brand');
  const ebanxMaskedCardNumber = getById('ebanx_' + paymentType + '_' + countryCode + '_masked_card_number');
  const ebanxDeviceFingerprint = getById('ebanx_' + paymentType + '_' + countryCode + '_device_fingerprint');
  const ebanxMode = getById('ebanx_' + paymentType + '_' + countryCode + '_mode');
  const ebanxIntegrationKey = getById('ebanx_' + paymentType + '_' + countryCode + '_integration_key');
  const ebanxCountry = getById('ebanx_' + paymentType + '_' + countryCode + '_country');
  const errorDiv = getById('ebanx-error-message');

  const hasEbanxForm = typeof getById('payment_form_ebanx_' + paymentType + '_' + countryCode) !== 'undefined';

  const mode = ebanxMode.value === 'sandbox' ? 'test' : 'production';

  EBANX.config.setMode(mode);
  EBANX.config.setPublishableKey(ebanxIntegrationKey.value);
  EBANX.config.setCountry(ebanxCountry.value);

  const isFormEmpty = () => {
    return !cardNumber.value.length ||
      !cardName.value.length ||
      !cardExpirationMonth.value.length ||
      !cardExpirationYear.value.length ||
      !cardCvv.value.length;
  };

  const disableBtnPlaceOrder = (shouldDisable) => {
    const placeOrderButton = document.querySelector('#review-buttons-container > button');
    if (typeof placeOrderButton !== 'undefined' && placeOrderButton) {
      placeOrderButton.disabled = shouldDisable;
    }
  };

  const saveToken = (response) => {
    if (!response.data.hasOwnProperty('status')) {
      const error = response.error.err;
      let errorMessage = error.message;

      if (!error.message) {
        EBANX.errors.InvalidValueFieldError(error.status_code);
        errorMessage = EBANX.errors.message || 'Some error happened. Please, verify the data of your credit card and try again.';
      }

      errorDiv.innerHTML = errorMessage;
      disableBtnPlaceOrder(false);

      setTimeout(() => {
        Validation.showAdvice({
          advices: false,
        }, errorDiv, 'ebanx-error-message');
      }, 1000);

      return false;
    }

    responseData = response.data;
    ebanxToken.value = responseData.token;
    ebanxBrand.value = responseData.payment_type_code;
    ebanxMaskedCardNumber.value = responseData.masked_card_number;
    ebanxDeviceFingerprint.value = responseData.deviceId;

    disableBtnPlaceOrder(false);
  };

  const generateToken = () => {
    if (!responseData) {
      disableBtnPlaceOrder(true);

      EBANX.card.createToken({
        card_number: parseInt(cardNumber.value.replace(/ /g, '')),
        card_name: cardName.value,
        card_due_date: (parseInt(cardExpirationMonth.value) || 0) + '/' + (parseInt(cardExpirationYear.value) || 0),
        card_cvv: cardCvv.value,
      }, saveToken);
    }
  };

  const handleToken = () => {
    if (!isFormEmpty()) {
      generateToken();
    }
  };

  const clearResponseData = () => {
    responseData = null;
    ebanxToken.value = '';
    ebanxBrand.value = '';
    ebanxMaskedCardNumber.value = '';
    ebanxDeviceFingerprint.value = '';
    Validation.hideAdvice({
      advices: false,
    }, errorDiv);
  };

  if (hasEbanxForm) {
    cardName.addEventListener('blur', handleToken, false);
    cardNumber.addEventListener('blur', handleToken, false);
    cardExpirationMonth.addEventListener('blur', handleToken, false);
    cardExpirationYear.addEventListener('blur', handleToken, false);
    cardCvv.addEventListener('blur', handleToken, false);

    cardName.addEventListener('change', clearResponseData, false);
    cardNumber.addEventListener('change', clearResponseData, false);
    cardExpirationMonth.addEventListener('change', clearResponseData, false);
    cardExpirationYear.addEventListener('change', clearResponseData, false);
    cardCvv.addEventListener('change', clearResponseData, false);
  }
};
