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
    var errorDiv = document.querySelector('#ebanx-error-message');
    var wow = document.querySelector('#advice-required-entry-ebanx_' + paymentType + '_' + countryCode + '_brand');

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

  // TODO: use mutation observer to change empty brand validation message into a generic "check your card data" message

  var MutationObserver = MutationObserver;
  if (typeof MutationObserver === 'undefined') {
    MutationObserver = function(){
      var def = function(callback) {
        this.listeners = [];
        this.callback = callback;
        this.initialized = false;
        this.clock = -1;
      };

      function tryInitialize() {
        if (this.initialized) return;
        this.initialized = true;

        var self = this;
        this.clock = setInterval(function(){
          for (var i in self.listeners) {
            var mutation = self.listeners[i];
            var newChildren = getTargetChildren(mutation.target);
            var newAttributes = getTargetAttributes(mutation.target);

            if (mutation.options.indexOf('childList') >= 0
              && !areChildrenEqual(mutation.children, newChildren)) {
              mutation.type = 'childList';
              this.callback(mutation);
            }

            if (mutation.options.indexOf('attributes') >= 0
              && !areAttributesEqual(mutation.attributes, newAttributes)) {
              mutation.type = 'attributes';
              this.callback(mutation);
            }

            mutation.children = newChildren;
            mutation.attributes = newAttributes;
          }
        }, 500);
      };

      def.prototype.disconnect = function() {
        this.initialized = false;
        if (!this.initialized) return;
        clearInterval(this.clock);
      };

      def.prototype.observe = function(target, options) {
        var defaults = { attributes: false, childList: false };
        for (var attr in defaults) {
          if (typeof options[attr] === 'undefined')
            options[attr] = defaults[attr];
        }

        var mutation = {
          options: [],
          target: target,
          attributes: getTargetAttributes(target),
          children: getTargetChildren(target)
        };

        if (options.childList)
          mutation.options.push('childList');

        if (options.attributes)
          mutation.options.push('attributes');

        this.listeners.push(mutation);

        tryInitialize.call(this);
      };

      function areChildrenEqual(listA, listB) {
        return listA.length === listB.length;
      }

      function areAttributesEqual(listA, listB) {
        if (listA.length !== listB.length) return false;

        for (var i in listA) {
          if (listA[i] !== listB[i]) return false;
        }

        return true;
      }

      function getTargetChildren(target) {
        return [].slice.apply(target.childNodes);
      }

      function getTargetAttributes(target) {
        return [].slice.apply(target.attributes);
      }

      return def;
    }
  }
}
