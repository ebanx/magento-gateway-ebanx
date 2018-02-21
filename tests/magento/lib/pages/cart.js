const proceedTocheckout = Symbol('proceedTocheckout');

export default class Cart {
  constructor(cy) {
    this.cy = cy;
  }

  [proceedTocheckout] () {
    return this.cy
      .get('.page-title.title-buttons .button.btn-proceed-checkout.btn-checkout', { timeout: 10000 })
      .should('be.visible');
  }

  proceedToCheckoutWithOpened() {
    this[proceedTocheckout]().click();

    return this;
  }
}
