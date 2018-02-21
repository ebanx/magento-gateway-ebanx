/* global Cypress, it, describe, before, context, cy */

import R from 'ramda';
import Faker from 'faker';
import defaults from '../../../defaults';
import { assertUrlStatus } from '../../../utils';
import Magento from '../../lib/operator';

Faker.locale = 'es_MX';

const mock = (data) => (R.merge(
  data,
  {
    firstName: Faker.name.firstName(),
    lastName: Faker.name.lastName(),
    address: Faker.address.streetName(),
    city: Faker.address.city(),
    state: Faker.address.state(),
    zipcode: Faker.address.zipCode(),
    phone: Faker.phone.phoneNumberFormat(2),
    email: Faker.internet.email(),
    country: 'Mexico',
    countryId: 'MX',
  }
));

let magento;

describe('Magento', () => {
  before(() => {
    assertUrlStatus(Cypress.env('DEMO_URL'));

    magento = new Magento(cy);
  });

  context('Mexico', () => {
    context('Oxxo', () => {
      it('can buy `wonder womans purse` using oxxo to personal', () => {
        magento.buyWonderWomansPurseWithOxxoToPersonal(mock(
          {
            paymentMethod: defaults.pay.api.DEFAULT_VALUES.paymentMethods.mx.oxxo.id,
          }
        ));
      });
    });

    context('Debit Card', () => {
      it('can buy `wonder womans purse`, using debit card', () => {
        const mockData = {
          paymentMethod: defaults.pay.api.DEFAULT_VALUES.paymentMethods.mx.debitcard.id,
          card: {
            name: Faker.name.findName(),
            number: defaults._globals.cardsWhitelist.mastercard,
            expiryYear: '2028',
            expiryMonth: '12',
            cvv: '123',
          },
        };

        magento
          .buyWonderWomansPurseWithDebitCardToPersonal(mock(mockData));
      });
    });

    context('Credit Card', () => {
      it('can buy `wonder womans purse`, using credit card and create account without one-click', () => {
        const mockData = {
          paymentMethod: defaults.pay.api.DEFAULT_VALUES.paymentMethods.mx.creditcard.id,
          card: {
            name: Faker.name.findName(),
            number: defaults._globals.cardsWhitelist.mastercard,
            expiryYear: '2028',
            expiryMonth: '12',
            cvv: '123',
          },
        };

        magento
          .buyWonderWomansPurseWithCreditCardToPersonal(mock(mockData));
      });
    });

    context('Spei', () => {
      it('can buy `wonder womans purse`, using Spei to personal', () => {
        const mockData = {
          paymentMethod: defaults.pay.api.DEFAULT_VALUES.paymentMethods.mx.spei.id,
        };

        magento
          .buyWonderWomansPurseWithSpeiToPersonal(mock(mockData));
      });
    });
  });
});
