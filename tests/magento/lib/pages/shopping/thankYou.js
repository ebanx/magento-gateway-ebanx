/* global expect */

import { tryNext } from '../../../../utils';

const stillOn = Symbol('stillOn');
const extractHash = Symbol('extractHash');

export default class ThankYou {
  constructor(cy) {
    this.cy = cy;
  }

  [stillOn] () {
    this.cy
      .get('body.checkout-onepage-success', { timeout: 15000 })
      .should('be.visible');
  }

  [extractHash](next) {
    this.cy
      .get('.ebanx-details > input[type="hidden"]')
      .then(($elm) => {
        next($elm.data('doraemon-hash'));
      });
  }

  stillOnBoleto(next) {
    this[stillOn]();

    this.cy
      .get('#ebanx-boleto-frame')
      .should('be.visible')
      .then(($boletoIframe) => {
        expect($boletoIframe.contents().find('table.table-boleto').length).to.equal(4);
      });

    this[extractHash]((hash) => {
      tryNext(next, { hash });
    });
  }

  stillOnCreditCard(next) {
    this[stillOn]();

    this[extractHash]((hash) => {
      tryNext(next, { hash });
    });
  }
}
