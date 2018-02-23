/* global it, describe, before, context, cy */

import R from 'ramda';
import Faker from 'faker';
import defaults from '../../../defaults';
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

describe('Shopping', () => {
  before(() => {
    magento = new Magento(cy);

    magento.setupPlugin();
  });

  context('Mexico', () => {
    context('Oxxo', () => {
      it('can buy `blue horizons bracelets` using oxxo to personal', () => {
        magento.buyBlueHorizonsBraceletsWithOxxoToPersonal(mock(
          {
            paymentMethod: defaults.pay.api.DEFAULT_VALUES.paymentMethods.mx.oxxo.id,
          }
        ));
      });
    });

    context('Debit Card', () => {
      it('can buy `blue horizons bracelets`, using debit card', () => {
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
          .buyBlueHorizonsBraceletsWithDebitCardToPersonal(mock(mockData));
      });
    });

    context('Credit Card', () => {
      it('can buy `blue horizons bracelets`, using credit card', () => {
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
          .buyBlueHorizonsBraceletsWithCreditCardToPersonal(mock(mockData));
      });
    });

    context('Spei', () => {
      it('can buy `blue horizons bracelets`, using Spei to personal', () => {
        const mockData = {
          paymentMethod: defaults.pay.api.DEFAULT_VALUES.paymentMethods.mx.spei.id,
        };

        magento
          .buyBlueHorizonsBraceletsWithSpeiToPersonal(mock(mockData));
      });
    });
  });
});
