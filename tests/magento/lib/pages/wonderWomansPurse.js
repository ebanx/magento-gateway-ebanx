/* global Cypress, expect */

const url = Symbol('url');
const payNow = Symbol('payNow');
const fillCvv = Symbol('fillCvv');
const onClick = Symbol('onClick');
const addToCart = Symbol('addToCart');
const fillInput = Symbol('fillInput');
const clickElement = Symbol('clickElement');
const proceedTocheckout = Symbol('proceedTocheckout');

export default class WonderWomansPurse {
  constructor(cy) {
    this.cy = cy;
  }

  [addToCart] () {
    this[clickElement]('.add-to-box .add-to-cart .button.btn-cart');
  }

  [proceedTocheckout] () {
    this.cy
      .get('.page-title.title-buttons .button.btn-proceed-checkout.btn-checkout', { timeout: 10000 })
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
    return `${Cypress.env('DEMO_URL')}/index.php/wonder-woman-s-purse.html`;
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
