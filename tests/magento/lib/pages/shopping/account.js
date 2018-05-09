/* global Cypress */
import R from "ramda";

const visit = Symbol('visit');
const register = Symbol('register');
const fillInput = Symbol('fillInput');
const fillAccountInfo = Symbol('fillAccountInfo');

export default class Account {
  constructor(cy) {
    this.cy = cy;
  }

  [visit] () {
    this.cy
      .visit(`${Cypress.env('DEMO_URL')}/customer/account/create/`)
      .get('#firstname', { timeout: 10000 })
      .should('be.visible');
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

  [register]() {
    this.cy
      .get('button.button:nth-child(2)')
      .should('be.visible')
      .click();

    this.cy
      .get('.success-msg', { timeout: 15000 })
      .should('be.visible');
  }

  [fillAccountInfo](data) {
    this[fillInput](data, 'firstName', '#firstname');
    this[fillInput](data, 'lastName', '#lastname');
    this[fillInput](data, 'email', '#email_address');
    this[fillInput](data, 'password', '#password');
    this[fillInput](data, 'password', '#confirmation');
  }

  create(data) {
    this[visit]();
    this[fillAccountInfo](data);
    this[register]();

    return this;
  }
}
