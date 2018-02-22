/* global Cypress */

export default class PaymentMethods {
  constructor(cy) {
    this.cy = cy;
  }

  setupEbanxPlugin() {
    this.cy
      .get('#payment_ebanx_settings-head', { timeout: 10000 })
      .should('be.visible')
      .click()
      .get('#payment_ebanx_settings_integration_key_sandbox', { timeout: 10000 })
      .should('be.visible')
      .type(Cypress.env('DEMO_INTEGRATION_KEY'))
      .get('#payment_ebanx_settings_integration_key_public_sandbox')
      .should('be.visible')
      .type(Cypress.env('DEMO_PUBLIC_INTEGRATION_KEY'))
      .get('#content > div > div.content-header > table > tbody > tr > td.form-buttons .scalable.save')
      .should('be.visible')
      .click()
      .get('#messages > ul > li', { timeout: 10000 })
      .should('be.visible');

    return this;
  }
}
