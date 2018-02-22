const proceedTocheckout = Symbol('proceedTocheckout');

export default class Cart {
  constructor(cy) {
    this.cy = cy;
  }

  [proceedTocheckout] () {
    return this.cy
      .get('div.cart.display-single-price > div.page-title.title-buttons > ul > li > button', { timeout: 10000 })
      .should('be.visible')
      .click();
  }

  proceedToCheckoutWithOpened() {
    this[proceedTocheckout]();

    return this;
  }
}
