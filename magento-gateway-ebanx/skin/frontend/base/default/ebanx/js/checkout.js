function handleEbanxForm(formId) {
    const CARD_NUMBER_ID = 'ebanx_cc_br_cc_number';
    const CARD_EXPIRATION_MONTH_ID = 'ebanx_cc_br_expiration';
    const CARD_EXPIRATION_YEAR_ID = 'ebanx_cc_br_expiration_yr';
    const CARD_CVV_ID = 'ebanx_cc_br_cc_cid';
    const EBANX_TOKEN = 'ebanx_token';
    const EBANX_BRAND = 'ebanx_brand';
    const EBANX_MASKED_CARD_NUMBER = 'ebanx_masked_card_number';
    const EBANX_DEVICE_FINGERPRINT = 'ebanx_device_fingerprint';
    const EBANX_BILLING_INSTALMENTS = 'ebanx_billing_instalments';
    const EBANX_BILLING_CVV = 'ebanx_billing_cvv';

    var ebanxForm = document.getElementById(formId);

    if(ebanxForm) {
        document.getElementById(CARD_NUMBER_ID).addEventListener('focusout', handleToken, false);
        document.getElementById(CARD_EXPIRATION_MONTH_ID).addEventListener('focusout', handleToken, false);
        document.getElementById(CARD_EXPIRATION_YEAR_ID).addEventListener('focusout', handleToken, false);
        document.getElementById(CARD_CVV_ID).addEventListener('focusout', handleToken, false);
    }

    function handleToken() {
        if(!isFormEmpty()){
            removeHiddenInputs();
            generateToken();
        }
    }

    function isFormEmpty() {
        var cardNumber= document.getElementById(CARD_NUMBER_ID);
        var cardExpirationMonth= document.getElementById(CARD_EXPIRATION_MONTH_ID);
        var cardExpirationYear= document.getElementById(CARD_EXPIRATION_YEAR_ID);
        var cardCvv= document.getElementById(CARD_CVV_ID);

        if(cardNumber && cardExpirationMonth && cardExpirationYear && cardCvv){
            return (cardNumber.value.length === 0 || cardExpirationMonth.value === 0 || cardExpirationYear.value === 0 || cardCvv.value.length === 0);
        }


        return true;
    }

    function removeHiddenInputs() {
        var ebanxToken = document.getElementById(EBANX_TOKEN);
        var ebanxBrand = document.getElementById(EBANX_BRAND);
        var ebanxMaskedCardNumber = document.getElementById(EBANX_MASKED_CARD_NUMBER);
        var ebanxDeviceFingerprint = document.getElementById(EBANX_DEVICE_FINGERPRINT);
        var ebanxBillingInstalments = document.getElementById(EBANX_BILLING_INSTALMENTS);
        var ebanxBillingCvv = document.getElementById(EBANX_BILLING_CVV);

        ebanxToken.parentNode.removeChild(ebanxToken);
        ebanxBrand.parentNode.removeChild(ebanxBrand);
        ebanxMaskedCardNumber.parentNode.removeChild(ebanxMaskedCardNumber);
        ebanxDeviceFingerprint.parentNode.removeChild(ebanxDeviceFingerprint);
        ebanxBillingInstalments.parentNode.removeChild(ebanxBillingInstalments);
        ebanxBillingCvv.parentNode.removeChild(ebanxBillingCvv);
    }

    function generateToken() {
      // TODO
    }
}
