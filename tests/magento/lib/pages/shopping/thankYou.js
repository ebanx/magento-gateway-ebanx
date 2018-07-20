/* global expect, window */

import { sanitizeMethod, tryNext } from '../../../../utils';

const stillOn = Symbol('stillOn');
const extractHash = Symbol('extractHash');
const stillOnAndExtractHash = Symbol('stillOnAndExtractHash');
const stillOnAndExtractHashFromUrl = Symbol('stillOnAndExtractHashFromUrl');

export default class ThankYou {
  constructor(cy) {
    this.cy = cy;
  }

  [stillOn] () {
    this.cy
      .get('body.checkout-onepage-success', { timeout: 30000 })
      .should('be.visible');
  }

  [stillOnAndExtractHashFromUrl](next) {
    this[stillOn]();

    this.cy
      .url()
      .then(($url) => tryNext(next, {hash: $url.split('hash=')[1] }));
  }

  [stillOnAndExtractHash](next) {
    this[stillOn]();

    this[extractHash]((hash) => {
      tryNext(next, { hash });
    });
  }

  [extractHash](next) {
    this.cy
      .get('.ebanx-details > input[type="hidden"]')
      .then(($elm) => {
        next($elm.data('doraemon-hash'));
      });
  }

  stillOnEfectivo(method, next) {
    this[stillOn]();

    const elm = {
      otroscupones: 'cupon',
    };

    this.cy
      .get(`#ebanx-${elm[sanitizeMethod(method)] || sanitizeMethod(method)}-frame`)
      .then(($efectivoIframe) => {
        expect($efectivoIframe.contents().find('.barcode.img-responsive').length).to.equal(1);
      });

    this[extractHash]((hash) => {
      tryNext(next, { hash });
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

  stillOnBaloto(next) {
    this[stillOn]();

    this.cy
      .get('#ebanx-baloto-frame')
      .then(($balotoIframe) => {
        expect($balotoIframe.contents().find('.baloto-details__item .affiliation_code').length).to.equal(1);
      });

    this[extractHash]((hash) => {
      tryNext(next, { hash });
    });
  }

  stillOnSpei(next) {
    this[stillOn]();

    this.cy
      .get('#ebanx-spei-frame')
      .then(($speiIframe) => {
        expect($speiIframe.contents().find('table.spei-table.non-responsive .amount').length).to.equal(2);
      });

    this[extractHash]((hash) => {
      tryNext(next, { hash });
    });
  }

  stillOnOxxo(next) {
    this[stillOn]();

    this.cy
      .get('#ebanx-oxxo-frame')
      .then(($oxxoIframe) => {
        expect($oxxoIframe.contents().find('div.oxxo-barcode > div.oxxo-barcode-img').length).to.equal(1);
      });

    this[extractHash]((hash) => {
      tryNext(next, { hash });
    });
  }

  stillOnPagoEfectivo(next) {
    this[stillOn]();

    this.cy
      .get('#ebanx-pagoefectivo-frame')
      .then(($pagoEfectivoIframe) => {
        expect($pagoEfectivoIframe.contents().find('.cip-code').length).to.equal(1);
      });

    this[extractHash]((hash) => {
      tryNext(next, { hash });
    });
  }

  stillOnSafetyPay(next) {
    this[stillOnAndExtractHashFromUrl](next);
  }

  stillOnCreditCard(next) {
    this[stillOnAndExtractHash](next);
  }

  failedOnCreditCard() {
    const stub = this.cy.stub();

    this.cy.on('window:alert', stub);

    this.cy
      .get('#review-please-wait', { timeout: 10000 })
      .should('be.visible')
      .get('#review-please-wait', { timeout: 10000 })
      .should('not.be.visible')
      .then(() => (
        expect(stub.getCall(0)).to.be.calledWith('Houve um problema com seu cartão de crédito, entre em contato com o emissor do cartão.')
      ));
  }

  stillOnDebitCard(next) {
    this[stillOnAndExtractHash](next);
  }

  stillOnPse(next) {
    this[stillOnAndExtractHashFromUrl](next);
  }

  stillOnWebpay(next) {
    this[stillOnAndExtractHashFromUrl](next);
  }

  stillOnMulticaja(next) {
    this[stillOnAndExtractHashFromUrl](next);
  }

  stillOnSencillito(next) {
    this[stillOnAndExtractHashFromUrl](next);
  }

  stillOnServipag(next) {
    this[stillOnAndExtractHashFromUrl](next);
  }

  stillOnTef(next) {
    this[stillOnAndExtractHashFromUrl](next);
  }
}
