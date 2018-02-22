/* global Cypress */

const visit = Symbol('visit');

export default class Home {
  constructor(cy) {
    this.cy = cy;
  }

  [visit]() {
    this.cy
      .visit(Cypress.env('DEMO_URL'))
      .get('.mybag-link', { timeout: 15000 })
      .should('be.visible');
  }

  logout() {
    this[visit]();

    this.cy
      .get('body')
      .then(($body) => {
        const logoutLink = $body.find('a:contains(Log Out)');

        if (logoutLink.length) {
          logoutLink.click();
        }
      });
  }
}
