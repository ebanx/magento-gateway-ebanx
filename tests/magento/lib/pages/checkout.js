/* global document */

import R from 'ramda';
import { pay } from '../../../defaults';
import { CHECKOUT_SCHEMA } from '../schemas/checkout';
import { waitUrlHas, validateSchema, sanitizeMethod } from '../../../utils';

const fillCity = Symbol('fillCity');
const fillInput = Symbol('fillInput');
const fillState = Symbol('fillState');
const fillPhone = Symbol('fillPhone');
const fillEmail = Symbol('fillEmail');
const placeOrder = Symbol('placeOrder');
const waitOverlay = Symbol('waitOverlay');
const fillBilling = Symbol('fillBilling');
const fillAddress = Symbol('fillAddress');
const clickElement = Symbol('clickElement');
const fillDocument = Symbol('fillDocument');
const fillPostcode = Symbol('fillPostcode');
const fillLastName = Symbol('fillLastName');
const fillPassword = Symbol('fillPassword');
const selectCountry = Symbol('selectCountry');
const fillFirstName = Symbol('fillFirstName');
const choosePaymentMethod = Symbol('choosePaymentMethod');

const fillCreditCardCvv = Symbol('fillCreditCardCvv');
const fillCreditCardName = Symbol('fillCreditCardName');
const fillCreditCardNumber = Symbol('fillCreditCardNumber');
const fillCreditCardExpiryYear = Symbol('fillCreditCardExpiryYear');
const fillCreditCardExpiryMonth = Symbol('fillCreditCardExpiryMonth');

const fillDebitCardCvv = Symbol('fillDebitCardCvv');
const fillDebitCardName = Symbol('fillDebitCardName');
const fillDebitCardNumber = Symbol('fillDebitCardNumber');
const fillDebitCardExpiryYear = Symbol('fillDebitCardExpiryYear');
const fillDebitCardExpiryMonth = Symbol('fillDebitCardExpiryMonth');

export default class Checkout {
  constructor(cy) {
    this.cy = cy;
    this.inputs = {
      creditCardNumber: (country) => `#ebanx_cc_${country}_cc_number`,
    };
  }

  [fillInput] (data, property, input, next) {
    R.ifElse(
      R.propSatisfies((x) => (x !== undefined), property), (data) => {
        this.cy
          .get(input, { timeout: 10000 })
          .should('be.visible')
          .then(($elm) => {
            $elm.val(data[property]);

            R.ifElse(
              R.propSatisfies((x) => (x instanceof Function), 'next'), (data) => {
                data.next($elm);
              },
              R.always(null)
            )({ next });
          })
          .get(input)
          .should('have.value', data[property]);
      },
      R.always(null)
    )(data);
  }

  [fillFirstName] (data) {
    this[fillInput](data, 'firstName', '#billing\\3a firstname');
  }

  [fillLastName] (data) {
    this[fillInput](data, 'lastName', '#billing\\3a lastname');
  }

  [fillPassword] (data) {
    const pW = {
      property: 'password',
      elm: '#billing\\3a customer_password',
    };

    R.ifElse(
      R.propSatisfies((x) => (x !== undefined), pW.property), (data) => {
        this[clickElement]('#billing\\3a register_account');
        this[fillInput](data, pW.property, pW.elm);
        this[fillInput](data, pW.property, '#billing\\3a confirm_password');
      },
      R.always(null)
    )(data);
  }

  [fillAddress] (data) {
    this[fillInput](data, 'address', '#billing\\3a street1');
  }

  [fillCity] (data) {
    this[fillInput](data, 'city', '#billing\\3a city');
  }

  [fillState] (data) {
    this[fillInput](data, 'state', '#billing\\3a region');
  }

  [fillPostcode] (data) {
    this[fillInput](data, 'zipcode', '#billing\\3a postcode');
  }

  [fillPhone] (data) {
    this[fillInput](data, 'phone', '#billing\\3a telephone');
  }

  [fillEmail] (data) {
    this[fillInput](data, 'email', '#billing\\3a email');
  }

  [fillDocument] (data, elm) {
    const cap = elm || (data.paymentMethod ? sanitizeMethod(data.paymentMethod) : '') || (data.paymentType ? sanitizeMethod(data.paymentType) : '');

    this[fillInput](data, 'document', `#ebanx-document-ebanx_${cap}`);
  }

  [fillCreditCardNumber] (country, data) {
    this[fillInput](data, 'number', this.inputs.creditCardNumber(country));
  }

  [fillCreditCardExpiryMonth] (country, data) {
    this[fillInput](data, 'expiryMonth', `#ebanx_cc_${country}_expiration`);
  }

  [fillCreditCardExpiryYear] (country, data) {
    this[fillInput](data, 'expiryYear', `#ebanx_cc_${country}_expiration_yr`);
  }

  [fillCreditCardCvv] (country, data) {
    const elm = `#ebanx_cc_${country}_cc_cid`;

    this[waitOverlay]();
    this[fillInput](data, 'cvv', elm);
    this[waitOverlay]();

    this.cy
      .get(elm)
      .first()
      .focus()
      .blur();
  }

  [fillCreditCardName] (country, data) {
    this[fillInput](data, 'name', `#ebanx_cc_${country}_cc_name`);
  }

  [fillDebitCardName] (country, data) {
    this[fillInput](data, 'name', `#ebanx_dc_${country}_dc_name`);
  }

  [fillDebitCardNumber] (country, data) {
    this[fillInput](data, 'number', `#ebanx_dc_${country}_dc_number`);
  }

  [fillDebitCardExpiryMonth] (country, data) {
    this[fillInput](data, 'expiryMonth', `#ebanx_dc_${country}_expiration`);
  }

  [fillDebitCardExpiryYear] (country, data) {
    this[fillInput](data, 'expiryYear', `#ebanx_dc_${country}_expiration_yr`);
  }

  [fillDebitCardCvv] (country, data) {
    const elm = `#ebanx_dc_${country}_dc_cid`;

    this[waitOverlay]();
    this[fillInput](data, 'cvv', elm);
    this[waitOverlay]();

    this.cy
      .get(elm)
      .first()
      .focus()
      .blur();
  }

  [waitOverlay] () {
    this.cy
      .get('#checkout-payment-method-load.updating')
      .should('be.not.visible', { timeout: 25000 });

    this.cy
      .get('.firecheckout-loader.shown')
      .should('be.not.visible', { timeout: 25000 });

    this.cy
      .get('.input-box.loading')
      .should('be.not.visible', { timeout: 25000 });
  }

  [selectCountry] (data, next) {
    this[fillInput](data, 'countryId', '#billing\\3a country_id', ($country) => {
      const evt = document.createEvent('HTMLEvents');
      evt.initEvent('change', false, true);
      $country.get()[0].dispatchEvent(evt);

      this[waitOverlay]();

      this.cy.wait(1500);

      R.ifElse(
        R.propSatisfies((x) => (x instanceof Function), 'next'), (data) => {
          data.next();
        },
        R.always(null)
      )({ next });
    });
  }

  [choosePaymentMethod] (data) {
    const resolveMethod = (method, country) => {
      const elmMethods = {
        ar: {
          creditcard: 'cc_ar',
        },
        br: {
          creditcard: 'cc_br',
        },
        co: {
          eft: 'pse',
          creditcard: 'cc_co',
        },
        mx: {
          debitcard: 'dc_mx',
          creditcard: 'cc_mx',
        },
      };

      return elmMethods[country] && elmMethods[country][method] ? elmMethods[country][method] : sanitizeMethod(method);
    };

    const pM = {
      elm: (method, countryId) => `#p_method_ebanx_${resolveMethod(method, countryId)}`,
      property: 'paymentMethod',
    };

    R.ifElse(
      R.propSatisfies((x) => (x !== undefined), pM.property), (data) => {
        this[waitOverlay]();

        this.cy
          .get(pM.elm(data[pM.property], data.countryId.toLowerCase()))
          .should('be.visible')
          .click();
      },
      R.always(null)
    )(data);
  }

  [clickElement] (element) {
    this.cy
      .get(element, { timeout: 10000 })
      .should('be.visible')
      .click();
  }

  [placeOrder] () {
    this[clickElement]('#checkout-review-submit .button.btn-checkout');
  }

  [fillBilling] (data) {
    this[selectCountry](data, () => {
      this[fillFirstName](data);
      this[fillLastName](data);
      this[fillEmail](data);
      this[fillAddress](data);
      this[fillPostcode](data);
      this[fillCity](data);
      this[fillState](data);
      this[fillPhone](data);
      this[fillPassword](data);
      this[choosePaymentMethod](data);
    });
  }

  placeWithSencillito(data) {
    validateSchema(CHECKOUT_SCHEMA.cl.sencillito(), data, () => {
      this[fillBilling](data);
      this[placeOrder]();

      waitUrlHas(`${pay.api.url}/simulator/confirm`);

      this.cy
        .get('.via.sencillito img.logoPT', { timeout: 15000 })
        .should('be.visible');
    });
  }

  placeWithDebitCard(data, next) {
    const lowerCountry = data.countryId.toLowerCase();

    validateSchema(CHECKOUT_SCHEMA[lowerCountry].debitcard(), data, () => {
      this[fillBilling](data);
      this[fillDebitCardName](lowerCountry, data.card);
      this[fillDebitCardNumber](lowerCountry, data.card);
      this[fillDebitCardExpiryMonth](lowerCountry, data.card);
      this[fillDebitCardExpiryYear](lowerCountry, data.card);
      this[fillDebitCardCvv](lowerCountry, data.card);

      this[placeOrder]();

      next();
    });
  }

  placeWithSpei(data, next) {
    validateSchema(CHECKOUT_SCHEMA.mx.spei(), data, () => {
      this[fillBilling](data);
      this[placeOrder]();

      next();
    });
  }

  placeWithCreditCard(data, next) {
    const lowerCountry = data.countryId.toLowerCase();

    validateSchema(CHECKOUT_SCHEMA[lowerCountry].creditcard(), data, () => {
      this[fillBilling](data);
      this[fillDocument](data, `cc_${lowerCountry}`);
      this[fillCreditCardName](lowerCountry, data.card);
      this[fillCreditCardNumber](lowerCountry, data.card);
      this[fillCreditCardExpiryMonth](lowerCountry, data.card);
      this[fillCreditCardExpiryYear](lowerCountry, data.card);
      this[fillCreditCardCvv](lowerCountry, data.card);

      R.ifElse(
        R.propSatisfies((x) => (x !== undefined), 'save'), () => {
          this[clickElement]('#ebanx_save_credit_card');
        },
        R.always(null)
      )(data.card);

      this.cy.get(this.inputs.creditCardNumber(lowerCountry)).focus().blur();
      this.cy.wait(15000);

      this[placeOrder]();

      next();
    });
  }

  placeWithEfectivo(data, next) {
    validateSchema(CHECKOUT_SCHEMA.ar.efectivo(), data, () => {
      this[fillBilling](data);
      this[fillDocument](data);
      this[placeOrder]();

      next();
    });
  }

  placeWithPagoEfectivo(data, next) {
    validateSchema(CHECKOUT_SCHEMA.pe.pagoEfectivo(), data, () => {
      this[fillBilling](data);
      this[fillDocument](data);
      this[placeOrder]();

      next();
    });
  }

  placeWithSafetyPay(data) {
    validateSchema(CHECKOUT_SCHEMA[data.countryId.toLowerCase()].safetyPay(), data, () => {
      this[fillBilling](data);
      this[fillDocument](data);
      this[clickElement](`#ebanx_safetypay_type_${data.paymentType.toLowerCase()}`);
      this[placeOrder]();

      // TODO: Move to another place (something like: `pay/pages/simulator`)

      waitUrlHas(`${pay.api.url}/simulator/confirm`);

      this.cy
        .get(`.safetypay-${data.paymentType.toLowerCase()}`, { timeout: 15000 })
        .should('be.visible');
    });
  }

  placeWithOxxo(data, next) {
    validateSchema(CHECKOUT_SCHEMA.mx.oxxo(), data, () => {
      this[fillBilling](data);
      this[placeOrder]();

      next();
    });
  }

  placeWithTef(data, next) {
    validateSchema(CHECKOUT_SCHEMA.br.tef(), data, () => {
      this[fillBilling](data);
      this[fillDocument](data);
      this[clickElement](`#ebanx_tef_${data.paymentType}`);
      this[placeOrder]();

      waitUrlHas(`${pay.api.url}/directtefredirect`);

      this.cy
        .url()
        .then(($url) => next({hash: $url.split('hash=')[1] }));
    });
  }

  placeWithBoleto(data, next) {
    validateSchema(CHECKOUT_SCHEMA.br.boleto(), data, () => {
      this[fillBilling](data);
      this[fillDocument](data);
      this[placeOrder]();

      next();
    });
  }

  placeWithPse(data) {
    validateSchema(CHECKOUT_SCHEMA.co.pse(), data, () => {
      this[fillBilling](data);
      this[fillInput](data, 'paymentType', '#ebanx_pse_bank');
      this[placeOrder]();

      // TODO: Move to another place (something like: `pay/pages/simulator`)

      waitUrlHas(`${pay.api.url}/simulator/confirm`);

      this.cy
        .get('.via.eft', { timeout: 15000 })
        .should('be.visible');
    });
  }

  placeWithServiPag(data) {
    validateSchema(CHECKOUT_SCHEMA.cl.servipag(), data, () => {
      this[fillBilling](data);
      this[fillDocument](data);
      this[placeOrder]();

      waitUrlHas(`${pay.api.url}/simulator/confirm`);

      this.cy
        .get('.via.servipag img.logoPT', { timeout: 15000 })
        .should('be.visible');
    });
  }

  placeWithWebpay(data) {
    validateSchema(CHECKOUT_SCHEMA.cl.webpay(), data, () => {
      this[fillBilling](data);
      this[fillDocument](data);
      this[placeOrder]();

      waitUrlHas(`${pay.api.url}/simulator/confirm`);

      this.cy
        .get('.via.webpay img.logoPT', { timeout: 15000 })
        .should('be.visible');
    });
  }

  placeWithMulticaja(data) {
    validateSchema(CHECKOUT_SCHEMA.cl.multicaja(), data, () => {
      this[fillBilling](data);
      this[fillDocument](data);
      this[placeOrder]();

      waitUrlHas(`${pay.api.url}/simulator/confirm`);

      this.cy
        .get('.via.multicaja img.logoPT', { timeout: 15000 })
        .should('be.visible');
    });
  }

  placeWithBaloto(data, next) {
    validateSchema(CHECKOUT_SCHEMA.co.baloto(), data, () => {
      this[fillBilling](data);
      this[placeOrder]();

      next();
    });
  }
}
