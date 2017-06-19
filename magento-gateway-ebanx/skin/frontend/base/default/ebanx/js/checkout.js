function handleEbanxForm(formId) {
  // var CARD_NAME_ID = 'ebanx_cc_br_cc_name';
  var CARD_NUMBER_ID = 'ebanx_cc_br_cc_number';
  var CARD_EXPIRATION_MONTH_ID = 'ebanx_cc_br_expiration';
  var CARD_EXPIRATION_YEAR_ID = 'ebanx_cc_br_expiration_yr';
  var CARD_CVV_ID = 'ebanx_cc_br_cc_cid';
  var EBANX_TOKEN = 'ebanx_token';
  var EBANX_BRAND = 'ebanx_brand';
  var EBANX_MASKED_CARD_NUMBER = 'ebanx_masked_card_number';
  var EBANX_DEVICE_FINGERPRINT = 'ebanx_device_fingerprint';
  var EBANX_BILLING_INSTALMENTS = 'ebanx_billing_instalments';
  var EBANX_BILLING_CVV = 'ebanx_billing_cvv';
  var EBANX_MODE = 'ebanx_mode';
  var EBANX_INTEGRATION_KEY = 'ebanx_integration_key';
  var EBANX_COUNTRY = 'ebanx_country';

  var responseData;
  var iterations = 0;

  // var cardName = document.getElementById(CARD_NAME_ID);
  var cardNumber = document.getElementById(CARD_NUMBER_ID);
  var cardExpirationMonth = document.getElementById(CARD_EXPIRATION_MONTH_ID);
  var cardExpirationYear = document.getElementById(CARD_EXPIRATION_YEAR_ID);
  var cardCvv = document.getElementById(CARD_CVV_ID);
  var ebanxToken = document.getElementById(EBANX_TOKEN);
  var ebanxBrand = document.getElementById(EBANX_BRAND);
  var ebanxMaskedCardNumber = document.getElementById(EBANX_MASKED_CARD_NUMBER);
  var ebanxDeviceFingerprint = document.getElementById(EBANX_DEVICE_FINGERPRINT);
  var ebanxBillingInstalments = document.getElementById(EBANX_BILLING_INSTALMENTS);
  var ebanxBillingCvv = document.getElementById(EBANX_BILLING_CVV);
  var ebanxMode = document.getElementById(EBANX_MODE);
  var ebanxIntegrationKey = document.getElementById(EBANX_INTEGRATION_KEY);
  var ebanxCountry = document.getElementById(EBANX_COUNTRY);

  var ebanxForm = document.getElementById(formId);

  var mode = ebanxMode.value === 'sandbox' ? 'test' : 'production';
  EBANX.config.setMode(mode);
  EBANX.config.setPublishableKey(ebanxIntegrationKey.value);
  EBANX.config.setCountry(ebanxCountry.value);

  if (ebanxForm) {
    // document.getElementById(CARD_NAME_ID).addEventListener('focusout', handleToken, false);
    document.getElementById(CARD_NUMBER_ID).addEventListener('focusout', handleToken, false);
    document.getElementById(CARD_EXPIRATION_MONTH_ID).addEventListener('focusout', handleToken, false);
    document.getElementById(CARD_EXPIRATION_YEAR_ID).addEventListener('focusout', handleToken, false);
    document.getElementById(CARD_CVV_ID).addEventListener('focusout', handleToken, false);
  }

  function handleToken() {
    if (!isFormEmpty()) {
      // removeHiddenInputs();
      generateToken();
    }
  }

  function isFormEmpty() {
    if (cardNumber &&
      // cardName &&
      cardExpirationMonth &&
      cardExpirationYear &&
      cardCvv) {
      return (cardNumber.value.length ||
      // cardName.value.length ||
      cardExpirationMonth.value ||
      cardExpirationYear.value ||
      cardCvv.value.length);
    }

    return true;
  }

  function generateToken() {
    if (!responseData) {
      EBANX.card.createToken({
        card_number: parseInt(cardNumber.value.replace(/ /g,'')),
        // card_name: cardName.value,
        card_due_date: (parseInt( cardExpirationMonth.value ) || 0) + '/' + (parseInt( cardExpirationYear.value ) || 0),
        card_cvv: cardCvv.value
      }, saveToken);
    }
  }

  function saveToken(response) {
    if (response.status !== 'SUCCESS' && iterations++ < 3){
      return generateToken();
    }
    responseData = response.data;
    ebanxToken.value = responseData.token;
    ebanxBrand.value = responseData.payment_type_code;
    ebanxMaskedCardNumber.value = responseData.masked_card_number;
    ebanxDeviceFingerprint.value = responseData.deviceId;
  }
}
