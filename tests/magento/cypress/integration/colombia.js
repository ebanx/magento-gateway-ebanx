/* global it, describe, before, context, cy */

import R from 'ramda';
import Faker from 'faker';
import defaults from '../../../defaults';
import Magento from '../../lib/operator';

Faker.locale = 'es';

const mock = (data) => (R.merge(
  data,
  {
    firstName: Faker.name.firstName(),
    lastName: Faker.name.lastName(),
    address: Faker.address.streetName(),
    city: Faker.address.city(),
    state: Faker.address.state(),
    zipcode: Faker.address.zipCode(),
    document: '213498478',
    phone: Faker.phone.phoneNumberFormat(2),
    email: Faker.internet.email(),
    country: 'Colombia',
    countryId: 'CO',
    documentType: 'Cédula de Ciudadania',
    documentTypeId: 'COL_CC',
  }
));

let magento;

describe('Shopping', () => {
  before(() => {
    magento = new Magento(cy);

    magento.setupPlugin();
  });

  context('Colombia', () => {
    context('Pse', () => {
      it('can buy `blue horizons bracelets` using Pse to personal', () => {
        magento.buyBlueHorizonsBraceletsWithPseToPersonal(mock(
          {
            paymentMethod: defaults.pay.api.DEFAULT_VALUES.paymentMethods.co.pse.id,
            paymentType: defaults.pay.api.DEFAULT_VALUES.paymentMethods.co.pse.types.agrario.name,
            paymentTypeId: defaults.pay.api.DEFAULT_VALUES.paymentMethods.co.pse.types.agrario.id,
          }
        ));
      });
    });

    context('Baloto', () => {
      it('can buy `blue horizons bracelets` using Baloto to personal', () => {
        magento.buyBlueHorizonsBraceletsWithBalotoToPersonal(mock(
          {
            paymentMethod: defaults.pay.api.DEFAULT_VALUES.paymentMethods.co.baloto.id,
          }
        ));
      });
    });

    context('Credit Card', () => {
      it('can buy `blue horizons bracelets`, using credit card', () => {
        const mockData = {
          paymentMethod: defaults.pay.api.DEFAULT_VALUES.paymentMethods.co.creditcard.id,
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
  });
});
