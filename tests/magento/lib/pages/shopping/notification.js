/* global Cypress, expect */
export default class Home {
  constructor(cy) {
    this.cy = cy;
  }

  notifyCancelledCardPayment(hash, merchantPaymentCode) {
    this.cy
      .request(`${Cypress.env('DEMO_URL')}/ebanx/payment/notify/operation/update/notification_type/forced/hash_codes/${hash}/merchant_payment_code/${merchantPaymentCode}`)
      .then((obj) => {
        expect(obj.body.message).to.be.equal('Declined Credit Card payment.');
      });
  }
}
