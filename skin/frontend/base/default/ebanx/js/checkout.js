function handleEbanxForm(countryCode, paymentType) {
  var getById = function (element) {
    return document.getElementById(element);
  };
  var responseData = null;

  var cardName = getById('ebanx_' + paymentType + '_' + countryCode + '_' + paymentType + '_name');
  var cardNumber = getById('ebanx_' + paymentType + '_' + countryCode + '_' + paymentType + '_number');
  var cardExpirationMonth = getById('ebanx_' + paymentType + '_' + countryCode + '_expiration');
  var cardExpirationYear = getById('ebanx_' + paymentType + '_' + countryCode + '_expiration_yr');
  var cardCvv = getById('ebanx_' + paymentType + '_' + countryCode + '_' + paymentType + '_cid');
  var ebanxToken = getById('ebanx_' + paymentType + '_' + countryCode + '_token');
  var ebanxBrand = getById('ebanx_' + paymentType + '_' + countryCode + '_brand');
  var ebanxMaskedCardNumber = getById('ebanx_' + paymentType + '_' + countryCode + '_masked_card_number');
  var ebanxDeviceFingerprint = getById('ebanx_' + paymentType + '_' + countryCode + '_device_fingerprint');
  var ebanxMode = getById('ebanx_' + paymentType + '_' + countryCode + '_mode');
  var ebanxIntegrationKey = getById('ebanx_' + paymentType + '_' + countryCode + '_integration_key');
  var ebanxCountry = getById('ebanx_' + paymentType + '_' + countryCode + '_country');
  var errorDiv = getById('#ebanx-error-message');

  var hasEbanxForm = typeof getById('payment_form_ebanx_' + paymentType + '_' + countryCode) !== 'undefined';

  var mode = ebanxMode.value === 'sandbox' ? 'test' : 'production';
  EBANX.config.setMode(mode);
  EBANX.config.setPublishableKey(ebanxIntegrationKey.value);
  EBANX.config.setCountry(ebanxCountry.value);

  var handleToken = function () {
    if (!isFormEmpty()) {
      generateToken();
    }
  };

  var isFormEmpty = function () {
    return !cardNumber.value.length ||
      !cardName.value.length ||
      !cardExpirationMonth.value.length ||
      !cardExpirationYear.value.length ||
      !cardCvv.value.length;
  };

  var generateToken = function () {
    if (!responseData) {
      disableBtnPlaceOrder(true);

      EBANX.card.createToken({
        card_number: parseInt(cardNumber.value.replace(/ /g, '')),
        card_name: cardName.value,
        card_due_date: (parseInt(cardExpirationMonth.value) || 0) + '/' + (parseInt(cardExpirationYear.value) || 0),
        card_cvv: cardCvv.value
      }, saveToken);
    }
  };

  var saveToken = function (response) {
    if (!response.data.hasOwnProperty('status')) {
      var error = response.error.err;
      var errorMessage = error.message;

      if (!error.message) {
        EBANX.errors.InvalidValueFieldError( error.status_code );
        errorMessage = EBANX.errors.message || 'Some error happened. Please, verify the data of your credit card and try again.';
      }

      errorDiv.innerHTML = errorMessage;
      disableBtnPlaceOrder(false);

      return;
    }

    errorDiv.innerHTML = '';

    responseData = response.data;
    ebanxToken.value = responseData.token;
    ebanxBrand.value = responseData.payment_type_code;
    ebanxMaskedCardNumber.value = responseData.masked_card_number;
    ebanxDeviceFingerprint.value = responseData.deviceId;

    disableBtnPlaceOrder(false);
  };

  var clearResponseData = function () {
    responseData = null;
    ebanxToken.value = '';
    ebanxBrand.value = '';
    ebanxMaskedCardNumber.value = '';
    ebanxDeviceFingerprint.value = '';
    errorDiv.innerHTML = '';
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

  var disableBtnPlaceOrder = function(shouldDisable) {
    var placeOrderButton = document.querySelector('#review-buttons-container > button');
    if (typeof placeOrderButton !== 'undefined' && placeOrderButton) {
      placeOrderButton.disabled = shouldDisable;
    }
  };

}
