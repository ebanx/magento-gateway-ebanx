/* global EBANX */
/* global Validation */

const invalidCardErrorMessage = 'Some error happened. Please, verify the data of your card and try again.';

// Observer for elements
function ElementsObserver(elements) {
  const completionCallbacks = [];
  const changeCallbacks = [];

  function addCompletionCallback(callback) {
    completionCallbacks.push(callback);
    return this;
  }

  function addChangeCallback(callback) {
    changeCallbacks.push(callback);
    return this;
  }

  function areAllElementsFilled() {
    return elements.every(element => element.value && element.value.trim().length);
  }

  function notifyElementChange(element) {
    changeCallbacks.forEach(callback => {
      if (callback) callback(element);
    });
  }

  function notifyCompletion() {
    completionCallbacks.forEach(callback => {
      if (callback) callback();
    });
  }

  function onChangeElement(element) {
    notifyElementChange(element);
    if (areAllElementsFilled()) {
      notifyCompletion();
    }
  }

  // Init fields
  elements.forEach(element => {
    element.addEventListener('change', (event) => {
      onChangeElement(event.target);
    }, false);
  });

  return {
    addCompletionCallback,
    addChangeCallback,
  };
}

const waitFor = (elementFinder, callback) => {
  const waiter = setInterval(() => {
    const element = elementFinder();
    if (typeof element === 'undefined' || element === null) {
      return false;
    }
    clearInterval(waiter);
    callback(element);
  }, 500);
};

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
    if (hasClass(inputToValidate, 'hidden-input-brand')) {
      inputToValidate.classList.add('brand-required');
      inputToValidate.classList.remove('required-entry');
    }
  });
};

const validationFormListener = (form, creditCardOptions) => {
  const inputSelector = '.required-entry-input';
  const selectSelector = '.required-entry-select';
  Array.from(creditCardOptions).forEach((cardOption) => {
    cardOption.querySelector('input[type=radio]').addEventListener('change', (event) => {
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

const initCreditCardWithoutSavedCards = (form) => {
  form.querySelectorAll('.required-entry-input').forEach((inputToValidate) => {
    inputToValidate.classList.add('required-entry');
  });
  form.querySelectorAll('.required-entry-select').forEach((inputToValidate) => {
    inputToValidate.classList.add('validate-select');
  });
};

const initCreditCardForm = (creditCardOptions, form) => {
  if (creditCardOptions.length !== 0) {
    validationFormListener(form, creditCardOptions);
    initCreditCardOption(creditCardOptions[0], form);
  } else {
    initCreditCardWithoutSavedCards(form);
  }
};

var handleEbanxForm = (countryCode, paymentType, formListId) => { // eslint-disable-line no-unused-vars
  const initCreditCardOptions = (formList) => {
    const creditCardOptions = formList.querySelectorAll('.ebanx-credit-card-option');
    initCreditCardForm(creditCardOptions, formList);
  };

  waitFor(() => {
    return document.querySelector(`#${formListId}`);
  }, initCreditCardOptions);

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

  let blurTargetElement = null;

  EBANX.config.setMode(mode);
  EBANX.config.setPublishableKey(ebanxIntegrationKey.value);
  EBANX.config.setCountry(ebanxCountry.value);

  const disableBtnPlaceOrder = (shouldDisable) => {
    const placeOrderButton = document.querySelector('#review-buttons-container > button');
    if (typeof placeOrderButton !== 'undefined' && placeOrderButton) {
      placeOrderButton.disabled = shouldDisable;
    }
  };

  const forceClickInPlaceOrder = (elem) => {
    if (!elem) {
      return false;
    }
    var event = document.createEvent('Event');
    event.initEvent('click', true, true);
    elem.dispatchEvent(event);
  };

  const setCardErrorMessage = (message) => {
    errorDiv.innerHTML = message;
    disableBtnPlaceOrder(false);

    setTimeout(() => {
      Validation.showAdvice({
        advices: false,
      }, errorDiv, 'ebanx-error-message');
    }, 500);

    return false;
  };

  const saveToken = (response) => {
    if (!response.data.token){
      return setCardErrorMessage(invalidCardErrorMessage);
    }

    if (!response.data.hasOwnProperty('status')) {
      const error = response.error.err;
      let errorMessage = error.message;

      if (!error.message) {
        EBANX.errors.InvalidValueFieldError(error.status_code);
        errorMessage = EBANX.errors.message || invalidCardErrorMessage;
      }

      return setCardErrorMessage(errorMessage);
    }

    responseData = response.data;
    ebanxToken.value = responseData.token;
    ebanxBrand.value = responseData.payment_type_code;
    ebanxMaskedCardNumber.value = responseData.masked_card_number;
    ebanxDeviceFingerprint.value = responseData.deviceId;

    disableBtnPlaceOrder(false);
    forceClickInPlaceOrder(blurTargetElement);
  };

  const generateToken = (blurTarget) => {
    if (blurTarget && (blurTarget.type === 'button' || blurTarget.type === 'span')) {
      blurTargetElement = blurTarget;
    }

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
    ElementsObserver([
      cardName,
      cardNumber,
      cardExpirationMonth,
      cardExpirationYear,
      cardCvv,
    ])
      .addChangeCallback(clearResponseData)
      .addCompletionCallback(generateToken);
  }

  cardNumber.addEventListener('input', function (elm) {
    setInterval(function() {
      cardCvv.setAttribute('maxlength', 3);
      if ((' ' + elm.target.className + ' ').indexOf(' amex ') > -1) {
        cardCvv.setAttribute('maxlength', 4);
      }

      if ((' ' + elm.target.className + ' ').indexOf(' unknown ') > -1) {
        cardCvv.value = '';
      }
    }, 200);
  });
};
