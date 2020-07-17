/* global it, describe, before, context, cy, Cypress */

import R from 'ramda';
import Faker from 'faker';
import defaults from '../../../defaults';
import {
  wrapOrderAssertations,
} from '../../../utils';
import Magento from '../../lib/operator';
import Api from '../../../pay/lib/operator';

Faker.locale = 'es';

const mock = (data) => (R.merge(
  data,
  {
    firstName: Faker.name.firstName(),
    lastName: Faker.name.lastName(),
    document: '2.124.958-2',
    address: Faker.address.streetName(),
    city: Faker.address.city(),
    state: Faker.address.state(),
    zipcode: Faker.address.zipCode(),
    phone: Faker.phone.phoneNumberFormat(2),
    email: Faker.internet.email(),
    country: 'Uruguay',
    countryId: 'UY',
  }
));

let api;
let magento;

describe('Shopping', () => {
  before(() => {
    api = new Api(cy);
    magento = new Magento(cy);

    magento.setupPlugin();
  });

  context('Uruguay', () => {
    context('Credit Card', () => {
      it('can buy `blue horizons bracelets` using credit card', () => {
        const checkoutData = mock({
          paymentMethod: defaults.pay.api.DEFAULT_VALUES.paymentMethods.uy.creditcard.id,
          card: {
            name: Faker.name.findName(),
            number: defaults._globals.cardsWhitelist.visa,
            expiryYear: '2028',
            expiryMonth: '12',
            cvv: '123',
          },
        });

        magento.buyBlueHorizonsBraceletsWithCreditCardToPersonal(checkoutData, (resp) => {
          api.queryPayment(resp.hash, Cypress.env('DEMO_INTEGRATION_KEY'), (payment) => {
            const checkoutPayment = Api.paymentData({
              amount_ext: (Cypress.env('DEMO_SHIPPING_RATE') + Cypress.env('BLUE_HORIZONS_BRACELETS_PRICE')).toFixed(2),
              payment_type_code: 'visa',
              instalments: '1',
              status: 'CO',
            });

            wrapOrderAssertations(payment, checkoutPayment, Api.customerData(checkoutData));
          });
        });
      });
    });

    context('Debit Card', () => {
      it('can buy `blue horizons bracelets`, using debit card', () => {
        const mockData = {
          paymentMethod: defaults.pay.api.DEFAULT_VALUES.paymentMethods.uy.debitcard.id,
          card: {
            name: Faker.name.findName(),
            number: defaults._globals.cardsWhitelist.visa,
            expiryYear: '2028',
            expiryMonth: '12',
            cvv: '123',
          },
        };

        magento
          .buyBlueHorizonsBraceletsWithDebitCardToPersonal(mock(mockData));
      });
    });
  });
});
