/* global Cypress */

import R from 'ramda';
import { pay } from '../../../../defaults';
import { CHECKOUT_SCHEMA } from '../../schemas/checkout';
import { validateSchema, waitUrlHas } from '../../../../utils';

const fillCity = Symbol('fillCity');
const fillInput = Symbol('fillInput');
const fillState = Symbol('fillState');
const fillPhone = Symbol('fillPhone');
const fillEmail = Symbol('fillEmail');
const placeOrder = Symbol('placeOrder');
const selectField = Symbol('selectField');
const fillBilling = Symbol('fillBilling');
const fillAddress = Symbol('fillAddress');
const clickElement = Symbol('clickElement');
const fillPostcode = Symbol('fillPostcode');
const fillLastName = Symbol('fillLastName');
const selectCountry = Symbol('selectCountry');
const fillFirstName = Symbol('fillFirstName');
const fillCompliance = Symbol('fillCompliance');
const chooseShipping = Symbol('chooseShipping');
const fillInputWithJquery = Symbol('fillInputWithJquery');

const fillCreditCardCvv = Symbol('fillCreditCardCvv');
const fillCreditCardName = Symbol('fillCreditCardName');
const fillCreditCardNumber = Symbol('fillCreditCardNumber');
const fillCreditCardExpiryYear = Symbol('fillCreditCardExpiryYear');
const fillCreditCardExpiryMonth = Symbol('fillCreditCardExpiryMonth');

export default class Checkout {
  constructor(cy) {
    this.cy = cy;
    this.inputs = {
      creditCardCvv: (country) => `#ebanx_cc_${country}_cc_cid`,
      creditCardNumber: (country) => `#ebanx_cc_${country}_cc_number`,
    };
  }

  [fillInputWithJquery] (data, property, input) {
    R.ifElse(
      R.propSatisfies((x) => (x !== undefined), property), (data) => {
        this.cy
          .get(input, { timeout: 30000 })
          .should('be.visible')
          .then(($input) => {
            $input.val(data[property]).trigger('input');
          })
          .get(input)
          .should('have.value', data[property]);
      },
      R.always(null)
    )(data);
  }

  [fillInput] (data, property, input) {
    R.ifElse(
      R.propSatisfies((x) => (x !== undefined), property), (data) => {
        this.cy
          .get(input, { timeout: 30000 })
          .should('be.visible')
          .type(data[property])
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

  [selectField] (data, property, propertyId, input) {
    R.ifElse(
      R.propSatisfies((x) => (x !== undefined), property), (data) => {
        this.cy
          .get(input, { timeout: 30000 })
          .should('be.visible')
          .select(data[property])
          .get(input)
          .should('have.value', data[propertyId]);
      },
      R.always(null)
    )(data);
  }

  [selectCountry] (data) {
    this[selectField](data, 'country', 'countryId', '#billing\\3a country_id');
  }

  [clickElement] (element) {
    this.cy
      .get(element, { timeout: 30000 })
      .should('be.visible')
      .click();
  }

  [placeOrder] () {
    this[clickElement]('#payment-buttons-container > button');
    this[clickElement]('#review-buttons-container > button');
  }

  [chooseShipping] (method) {
    this[clickElement](`#s_method_${method || 'flatrate'}_${method || 'flatrate'}`);
    this[clickElement]('#shipping-method-buttons-container > button');
  }

  [fillCompliance] (data) {
    this[selectCountry](data);
    this[fillFirstName](data);
    this[fillLastName](data);
    this[fillEmail](data);
    this[fillAddress](data);
    this[fillPostcode](data);
    this[fillCity](data);
    this[fillState](data);
    this[fillPhone](data);

    this[clickElement]('#billing-buttons-container > button');
  }

  [fillBilling] (data) {
    R.ifElse(
      R.propSatisfies((x) => (x !== undefined), 'password'), () => {
        this[clickElement]('#login\\3a register');
        this[clickElement]('#onepage-guest-register-button');

        this[fillInput](data, 'password', '#billing\\3a customer_password');
        this[fillInput](data, 'password', '#billing\\3a confirm_password');

        this[fillCompliance](data);
      },
      () => {
        this[clickElement]('#login\\3a guest');
        this[clickElement]('#onepage-guest-register-button');

        this[fillCompliance](data);
      }
    )(data);
  }

  [fillCreditCardNumber] (country, data) {
    this[fillInput](data, 'number', this.inputs.creditCardNumber(country));
  }

  [fillCreditCardExpiryMonth] (country, data) {
    this[selectField](data, 'expiryMonth', 'expiryMonth', `#ebanx_cc_${country}_expiration`);
  }

  [fillCreditCardExpiryYear] (country, data) {
    this[selectField](data, 'expiryYear', 'expiryYear', `#ebanx_cc_${country}_expiration_yr`);
  }

  [fillCreditCardCvv] (country, data) {
    this[fillInput](data, 'cvv', this.inputs.creditCardCvv(country));
  }

  [fillCreditCardName] (country, data) {
    this[fillInput](data, 'name', `#ebanx_cc_${country}_cc_name`);
  }

  placeWithTef(data, next) {
    validateSchema(CHECKOUT_SCHEMA.br.tef(), data, () => {
      this[fillBilling](data);
      this[chooseShipping](data.shippingMethod);
      this[clickElement]('#p_method_ebanx_tef');
      this[fillInputWithJquery](data, 'document', '#ebanx-document-ebanx_tef');
      this[clickElement](`#ebanx_tef_${data.paymentType}`);

      this[placeOrder]();

      waitUrlHas(`${pay.api.url}/directtefredirect`);

      this.cy
        .get('#mestre > div > div > div > a:nth-child(1)', { timeout: 30000 })
        .should('be.visible')
        .click();

      waitUrlHas(`${Cypress.env('DEMO_URL')}/checkout/onepage/success`);

      next();
    });
  }

  placeWithCreditCard(data, next) {
    const lowerCountry = data.countryId.toLowerCase();

    validateSchema(CHECKOUT_SCHEMA[lowerCountry].creditcard(), data, () => {
      this[fillBilling](data);
      this[chooseShipping](data.shippingMethod);
      this[clickElement](`#p_method_ebanx_cc_${lowerCountry}`);

      this[fillInputWithJquery](data, 'document', `#ebanx-document-ebanx_cc_${lowerCountry}`);

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

      this.cy.get(this.inputs.creditCardCvv(lowerCountry)).focus().blur();
      this.cy.wait(10000);

      this[placeOrder]();

      next();
    });
  }

  placeWithBoleto(data, next) {
    validateSchema(CHECKOUT_SCHEMA.br.boleto(), data, () => {
      this[fillBilling](data);
      this[chooseShipping](data.shippingMethod);
      this[clickElement]('#p_method_ebanx_boleto');
      this[fillInputWithJquery](data, 'document', '#ebanx-document-ebanx_boleto');
      this[placeOrder]();

      next();
    });
  }
}
