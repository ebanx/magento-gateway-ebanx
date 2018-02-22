/* global Cypress, expect */

const url = Symbol('url');
const payNow = Symbol('payNow');
const fillCvv = Symbol('fillCvv');
const onClick = Symbol('onClick');
const addToCart = Symbol('addToCart');
const fillInput = Symbol('fillInput');
const clickElement = Symbol('clickElement');
const proceedTocheckout = Symbol('proceedTocheckout');

export default class BlueHorizonsBracelets {
  constructor(cy) {
    this.cy = cy;
  }

  [addToCart] () {
    this[clickElement]('#product_addtocart_form > div.add-to-cart-wrapper > div > div > div.add-to-cart-buttons > button');
  }

  [proceedTocheckout] () {
    this.cy
      .get('div.cart.display-single-price > div.page-title.title-buttons > ul > li > button', { timeout: 10000 })
      .should('be.visible');
  }

  [payNow] () {
    this[clickElement]('#ebanx-oneclick-pay-button');
  }

  [onClick] () {
    this[clickElement]('#product-oneclick-ebanx-button');
  }

  [fillInput] (input, value) {
    this.cy
      .get(input, { timeout: 10000 })
      .should('be.visible')
      .then(($country) => {
        $country.val(value);
      })
      .get(input)
      .should('have.value', value);
  }

  [fillCvv] (cvv) {
    this[fillInput]('.input-text.cvv.validate-cc-cvn.ebanx-format-cvc-number', cvv);
  }

  [clickElement] (element) {
    this.cy
      .get(element, { timeout: 10000 })
      .should('be.visible')
      .click();
  }

  [url]() {
    return `${Cypress.env('DEMO_URL')}/accessories/jewelry/blue-horizons-bracelets.html`;
  }

  visit() {
    this.cy
      .visit(this[url]())
      .url()
      .then(($url) => {
        expect($url).to.equal(this[url]());
      });

    return this;
  }

  buy() {
    this.visit();

    this[addToCart]();
    this[proceedTocheckout]();

    return this;
  }

  buyByOneClick(cvv) {
    this.visit();

    this[onClick]();
    this[fillCvv](cvv);
    this[payNow]();

    return this;
  }
}
