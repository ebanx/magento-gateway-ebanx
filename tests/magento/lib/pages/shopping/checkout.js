/* global Cypress */

import R from 'ramda';
import { pay } from '../../../../defaults';
import { CHECKOUT_SCHEMA } from '../../schemas/checkout';
import {
  validateSchema,
  waitUrlHas,
  sanitizeMethod,
} from '../../../../utils';

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
const confirmSimulator = Symbol('confirmSimulator');
const fillInputWithJquery = Symbol('fillInputWithJquery');
const fillLoggedInCompliance = Symbol('fillLoggedInCompliance');

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
      debitCardCvv: (country) => `#ebanx_dc_${country}_dc_cid`,
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
    const element = `#s_method_${method || 'flatrate'}_${method || 'flatrate'}`;
    this.cy.get(element, {timeout: 30000}).then((elm) => {
      if (!elm.is(':checked')) {
        this[clickElement](element);
      }
    });

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
    this[chooseShipping](data.shippingMethod);
  }

  [fillLoggedInCompliance] (data) {
    this[selectCountry](data);
    this[fillAddress](data);
    this[fillPostcode](data);
    this[fillCity](data);
    this[fillState](data);
    this[fillPhone](data);

    this[clickElement]('#billing-buttons-container > button');
    this[chooseShipping](data.shippingMethod);
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

  [fillDebitCardName] (country, data) {
    this[fillInput](data, 'name', `#ebanx_dc_${country}_dc_name`);
  }

  [fillDebitCardNumber] (country, data) {
    this[fillInput](data, 'number', `#ebanx_dc_${country}_dc_number`);
  }

  [fillDebitCardExpiryMonth] (country, data) {
    this[selectField](data, 'expiryMonth', 'expiryMonth', `#ebanx_dc_${country}_expiration`);
  }

  [fillDebitCardExpiryYear] (country, data) {
    this[selectField](data, 'expiryYear', 'expiryYear', `#ebanx_dc_${country}_expiration_yr`);
  }

  [fillDebitCardCvv] (country, data) {
    this[fillInput](data, 'cvv', this.inputs.debitCardCvv(country));
  }

  [confirmSimulator] (next) {
    this.cy
      .get('#mestre > div > div > div > a:nth-child(1)', { timeout: 30000 })
      .should('be.visible')
      .click();

    waitUrlHas(`${Cypress.env('DEMO_URL')}/checkout/onepage/success`);

    next();
  }

  placeWithTef(data, next) {
    validateSchema(CHECKOUT_SCHEMA.br.tef(), data, () => {
      this[fillBilling](data);
      this[clickElement]('#p_method_ebanx_tef');
      this[fillInputWithJquery](data, 'document', '#ebanx-document-ebanx_tef');
      this[clickElement](`#ebanx_tef_${data.paymentType}`);

      this[placeOrder]();

      waitUrlHas(`${pay.api.newUrl}/directtefredirect`);

      this[confirmSimulator](next);
    });
  }

  placeWithSencillito(data, next) {
    validateSchema(CHECKOUT_SCHEMA.cl.sencillito(), data, () => {
      this[fillBilling](data);
      this[clickElement]('#p_method_ebanx_sencillito');

      this[fillInputWithJquery](data, 'document', '#ebanx-document-ebanx_sencillito');

      this[placeOrder]();

      waitUrlHas(`${pay.api.url}/simulator/confirm`);

      this.cy
        .get('.via.sencillito img.logoPT', { timeout: 15000 })
        .should('be.visible');

      this[confirmSimulator](next);
    });
  }

  placeWithServipag(data, next) {
    validateSchema(CHECKOUT_SCHEMA.cl.servipag(), data, () => {
      this[fillBilling](data);
      this[clickElement]('#p_method_ebanx_servipag');

      this[fillInputWithJquery](data, 'document', '#ebanx-document-ebanx_servipag');

      this[placeOrder]();

      waitUrlHas(`${pay.api.url}/simulator/confirm`);

      this.cy
        .get('.via.servipag img.logoPT', { timeout: 15000 })
        .should('be.visible');

      this[confirmSimulator](next);
    });
  }

  placeWithMulticaja(data, next) {
    validateSchema(CHECKOUT_SCHEMA.cl.multicaja(), data, () => {
      this[fillBilling](data);
      this[clickElement]('#p_method_ebanx_multicaja');

      this[fillInputWithJquery](data, 'document', '#ebanx-document-ebanx_multicaja');

      this[placeOrder]();

      waitUrlHas(`${pay.api.url}/simulator/confirm`);

      this.cy
        .get('.via.multicaja img.logoPT', { timeout: 15000 })
        .should('be.visible');

      this[confirmSimulator](next);
    });
  }

  placeWithWebpay(data, next) {
    validateSchema(CHECKOUT_SCHEMA.cl.webpay(), data, () => {
      this[fillBilling](data);
      this[clickElement]('#p_method_ebanx_webpay');

      this[fillInputWithJquery](data, 'document', '#ebanx-document-ebanx_webpay');

      this[placeOrder]();

      waitUrlHas(`${pay.api.url}/simulator/confirm`);

      this.cy
        .get('.via.webpay img.logoPT', { timeout: 15000 })
        .should('be.visible');

      this[confirmSimulator](next);
    });
  }

  placeWithPse(data, next) {
    validateSchema(CHECKOUT_SCHEMA.co.pse(), data, () => {
      this[fillBilling](data);
      this[clickElement]('#p_method_ebanx_pse');

      this[selectField](data, 'paymentType', 'paymentTypeId', '#ebanx_pse_bank');

      this[selectField](data, 'documentType', 'documentTypeId', `#ebanx-document-type-ebanx_pse`);
      this[fillInputWithJquery](data, 'document', `#ebanx-document-ebanx_pse`);

      this[placeOrder]();

      waitUrlHas(`${pay.api.url}/simulator/confirm`);

      this.cy
        .get('.via.eft', { timeout: 15000 })
        .should('be.visible');

      this[confirmSimulator](next);
    });
  }

  placeWithSafetyPay(data, next) {
    const elm = {
      ec: '#p_method_ebanx_safetypay_ec',
      pe: '#p_method_ebanx_safetypay',
    };

    const lowerCountry = data.countryId.toLowerCase();

    validateSchema(CHECKOUT_SCHEMA[lowerCountry].safetyPay(), data, () => {
      this[fillBilling](data);
      this[clickElement](elm[lowerCountry]);
      this[fillInput](data, 'document', '#ebanx-document-ebanx_safetypay');
      this[clickElement](`#ebanx_safetypay_type_${data.paymentType.toLowerCase()}`);
      this[placeOrder]();

      waitUrlHas(`${pay.api.url}/simulator/confirm`);

      this.cy
        .get(`.safetypay-${data.paymentType.toLowerCase()}`, { timeout: 15000 })
        .should('be.visible');

      this[confirmSimulator](next);
    });
  }

  placeWithBaloto(data, next) {
    validateSchema(CHECKOUT_SCHEMA.co.baloto(), data, () => {
      this[fillBilling](data);
      this[clickElement]('#p_method_ebanx_baloto');

      this[selectField](data, 'documentType', 'documentTypeId', `#ebanx-document-type-ebanx_baloto`);
      this[fillInputWithJquery](data, 'document', '#ebanx-document-ebanx_baloto');

      this[placeOrder]();

      next();
    });
  }

  placeWithPagoEfectivo(data, next) {
    validateSchema(CHECKOUT_SCHEMA.pe.pagoEfectivo(), data, () => {
      this[fillBilling](data);
      this[clickElement]('#p_method_ebanx_pagoefectivo');
      this[fillInput](data, 'document', '#ebanx-document-ebanx_pagoefectivo');

      this[placeOrder]();

      next();
    });
  }

  placeWithEfectivo(data, next) {
    validateSchema(CHECKOUT_SCHEMA.ar.efectivo(), data, () => {
      this[fillBilling](data);
      this[clickElement](`#p_method_ebanx_${sanitizeMethod(data.paymentMethod)}`);
      this[selectField](data, 'documentType', 'documentTypeId', `#ebanx-document-type-ebanx_${sanitizeMethod(data.paymentMethod)}`);
      this[fillInputWithJquery](data, 'document', `#ebanx-document-ebanx_${sanitizeMethod(data.paymentMethod)}`);

      this[placeOrder]();

      next();
    });
  }

  placeWithCreditCard(data, next) {
    const lowerCountry = data.countryId.toLowerCase();

    validateSchema(CHECKOUT_SCHEMA[lowerCountry].creditcard(), data, () => {
      this[fillBilling](data);
      this[clickElement](`#p_method_ebanx_cc_${lowerCountry}`);

      this[selectField](data, 'documentType', 'documentTypeId', `#ebanx-document-type-ebanx_cc_${lowerCountry}`);
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

  placeWithDebitCard(data, next) {
    const lowerCountry = data.countryId.toLowerCase();

    validateSchema(CHECKOUT_SCHEMA[lowerCountry].debitcard(), data, () => {
      this[fillBilling](data);
      this[clickElement](`#p_method_ebanx_dc_${lowerCountry}`);

      this[selectField](data, 'documentType', 'documentTypeId', `#ebanx-document-type-ebanx_dc_${lowerCountry}`);
      this[fillInputWithJquery](data, 'document', `#ebanx-document-ebanx_dc_${lowerCountry}`);

      this[fillDebitCardName](lowerCountry, data.card);
      this[fillDebitCardNumber](lowerCountry, data.card);
      this[fillDebitCardExpiryMonth](lowerCountry, data.card);
      this[fillDebitCardExpiryYear](lowerCountry, data.card);
      this[fillDebitCardCvv](lowerCountry, data.card);

      this.cy.get(this.inputs.debitCardCvv(lowerCountry)).focus().blur();
      this.cy.wait(10000);

      this[placeOrder]();

      next();
    });
  }

  placeWithSpei(data, next) {
    validateSchema(CHECKOUT_SCHEMA.mx.spei(), data, () => {
      this[fillBilling](data);
      this[clickElement]('#p_method_ebanx_spei');
      this[placeOrder]();

      next();
    });
  }

  placeWithBoleto(data, next) {
    validateSchema(CHECKOUT_SCHEMA.br.boleto(), data, () => {
      this[fillBilling](data);
      this[clickElement]('#p_method_ebanx_boleto');
      this[fillInputWithJquery](data, 'document', '#ebanx-document-ebanx_boleto');
      this[placeOrder]();

      next();
    });
  }

  placeWithBoletoLoggedIn(data, next) {
    validateSchema(CHECKOUT_SCHEMA.br.boleto(), data, () => {
      this[fillLoggedInCompliance](data);
      this[clickElement]('#p_method_ebanx_boleto');
      this[fillInputWithJquery](data, 'document', '#ebanx-document-ebanx_boleto');
      this[placeOrder]();

      next();
    });
  }

  placeWithOxxo(data, next) {
    validateSchema(CHECKOUT_SCHEMA.mx.oxxo(), data, () => {
      this[fillBilling](data);
      this[clickElement]('#p_method_ebanx_oxxo');
      this[placeOrder]();

      next();
    });
  }
}
