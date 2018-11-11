/* global Cypress */

const visit = Symbol('visit');

export default class Login {
  constructor(cy) {
    this.cy = cy;
  }

  [visit]() {
    this.cy
      .visit(`${Cypress.env('DEMO_URL')}/admin`)
      .get('#username', { timeout: 30000 })
      .should('be.visible');
  }

  login() {
    this[visit]();

    this.cy
      .get('#username', { timeout: 30000 })
      .should('be.visible')
      .type(Cypress.env('ADMIN_USERNAME'))
      .should('have.value', Cypress.env('ADMIN_USERNAME'))
      .get('#login')
      .should('be.visible')
      .type(Cypress.env('ADMIN_PASSWORD'))
      .should('have.value', Cypress.env('ADMIN_PASSWORD'))
      .get('input[type="submit"]')
      .should('be.visible')
      .click()
      .get('#nav > li:nth-child(9) > ul > li.last.level1 > a', { timeout: 10000 })
      .should('be.visible');

    return this;
  }
}
