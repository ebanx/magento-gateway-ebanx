/* global Cypress, it, describe, before, context, cy */

import R from 'ramda';
import Faker from 'faker';
import defaults from '../../../defaults';
import { assertUrlStatus } from '../../../utils';
import Magento from '../../lib/operator';

Faker.locale = 'es';

const mock = (data) => (R.merge(
  data,
  {
    firstName: Faker.name.firstName(),
    lastName: Faker.name.lastName(),
    address: Faker.address.streetName(),
    city: Faker.address.city(),
    state: 'Catamarca',
    stateId: 'K',
    zipcode: Faker.address.zipCode(),
    document: Faker.random.uuid(),
    phone: Faker.phone.phoneNumberFormat(2),
    email: Faker.internet.email(),
    country: 'Argentina',
    countryId: 'AR',
  }
));

let magento;

describe('Magento', () => {
  before(() => {
    assertUrlStatus(Cypress.env('DEMO_URL'));

    magento = new Magento(cy);
  });

  context('Argentina', () => {
    context('Efectivo', () => {
      it('can buy `wonder womans purse` using Rapipago to personal', () => {
        magento.buyWonderWomansPurseWithEfectivoToPersonal(mock(
          {
            paymentMethod: defaults.pay.api.DEFAULT_VALUES.paymentMethods.ar.efectivo.types.rapipago,
          }
        ));
      });

      it('can buy `wonder womans purse` using Pagofacil to personal', () => {
        magento.buyWonderWomansPurseWithEfectivoToPersonal(mock(
          {
            paymentMethod: defaults.pay.api.DEFAULT_VALUES.paymentMethods.ar.efectivo.types.pagofacil,
          }
        ));
      });

      it('can buy `wonder womans purse` using OtrosCupones to personal', () => {
        magento.buyWonderWomansPurseWithEfectivoToPersonal(mock(
          {
            paymentMethod: defaults.pay.api.DEFAULT_VALUES.paymentMethods.ar.efectivo.types.otrosCupones,
          }
        ));
      });
    });

    context('Credit Card', () => {
      it('can buy `wonder womans purse` using credit card', () => {
        const mockData = {
          paymentMethod: defaults.pay.api.DEFAULT_VALUES.paymentMethods.ar.creditcard.id,
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
  });
});
