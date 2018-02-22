export default class Configurations {
  constructor(cy) {
    this.cy = cy;
  }

  goToPaymentMethods() {
    this.cy
      .get('#system_config_tabs > li:nth-child(4) > dl > dd:nth-child(10) > a', { timeout: 10000 })
      .should('be.visible')
      .click()
      .get('#payment_ebanx_settings-head', { timeout: 10000 })
      .should('be.visible');

    return this;
  }
}
