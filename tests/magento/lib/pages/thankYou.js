/* global expect */
import { sanitizeMethod, tryNext } from '../../../utils';

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

  stillOnCreditCard(next) {
    this[stillOn]();

    this[extractHash]((hash) => {
      tryNext(next, { hash });
    });
  }

  stillOnPagoEfectivo() {
    this[stillOn]();

    this.cy
      .get('#ebanx-pagoefectivo-frame')
      .then(($pagoIframe) => {
        expect($pagoIframe.contents().find('.cip-code').length).to.equal(1);
      });

    return this;
  }

  stillOnSpei() {
    this[stillOn]();

    this.cy
      .get('#ebanx-spei-frame')
      .then(($speiIframe) => {
        expect($speiIframe.contents().find('table.spei-table.non-responsive .amount').length).to.equal(2);
      });

    return this;
  }

  stillOnDebitCard() {
    this[stillOn]();

    return this;
  }

  stillOnEfectivo(method) {
    this[stillOn]();

    const elm = {
      otroscupones: 'cupon',
    };

    this.cy
      .get(`#ebanx-${elm[sanitizeMethod(method)] || sanitizeMethod(method)}-frame`)
      .then(($efectivoIframe) => {
        expect($efectivoIframe.contents().find('.barcode.img-responsive').length).to.equal(1);
      });

    return this;
  }

  stillOnOxxo() {
    this[stillOn]();

    this.cy
      .get('#ebanx-oxxo-frame')
      .then(($oxxoIframe) => {
        expect($oxxoIframe.contents().find('div.oxxo-barcode > div.oxxo-barcode-img').length).to.equal(1);
      });

    return this;
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

  stillOnBaloto() {
    this[stillOn]();

    this.cy
      .get('#ebanx-baloto-frame')
      .then(($balotoIframe) => {
        expect($balotoIframe.contents().find('.baloto-details__item .affiliation_code').length).to.equal(1);
      });

    return this;
  }
}
