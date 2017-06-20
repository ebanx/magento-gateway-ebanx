function handleEbanxForm(formId) {
  var gid = function (element) {
    return document.getElementById(element);
  };
  var responseData = null;

  var cardName = gid('ebanx_cc_br_cc_name');
  var cardNumber = gid('ebanx_cc_br_cc_number');
  var cardExpirationMonth = gid('ebanx_cc_br_expiration');
  var cardExpirationYear = gid('ebanx_cc_br_expiration_yr');
  var cardCvv = gid('ebanx_cc_br_cc_cid');
  var ebanxToken = gid('ebanx_token');
  var ebanxBrand = gid('ebanx_brand');
  var ebanxMaskedCardNumber = gid('ebanx_masked_card_number');
  var ebanxDeviceFingerprint = gid('ebanx_device_fingerprint');
  var ebanxBillingInstalments = gid('ebanx_billing_instalments');
  var ebanxBillingCvv = gid('ebanx_billing_cvv');
  var ebanxMode = gid('ebanx_mode');
  var ebanxIntegrationKey = gid('ebanx_integration_key');
  var ebanxCountry = gid('ebanx_country');

  var ebanxForm = gid(formId);

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
      EBANX.card.createToken({
        card_number: parseInt(cardNumber.value.replace(/ /g,'')),
        card_name: cardName.value,
        card_due_date: (parseInt( cardExpirationMonth.value ) || 0) + '/' + (parseInt( cardExpirationYear.value ) || 0),
        card_cvv: cardCvv.value
      }, saveToken);
    }
  };

  var saveToken = function (response) {
    if (response.data.hasOwnProperty('status')) {
      responseData = response.data;
      ebanxToken.value = responseData.token;
      ebanxBrand.value = responseData.payment_type_code;
      ebanxMaskedCardNumber.value = responseData.masked_card_number;
      ebanxDeviceFingerprint.value = responseData.deviceId;
      return;
    }
    var errorMessage = response.error.err.message || response.error.err.status_message;
    alert(errorMessage);
  };

  var clearResponseData = function () {
    responseData = null;
    ebanxToken.value = '';
    ebanxBrand.value = '';
    ebanxMaskedCardNumber.value = '';
    ebanxDeviceFingerprint.value = '';
  };

  if (ebanxForm) {
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
}
