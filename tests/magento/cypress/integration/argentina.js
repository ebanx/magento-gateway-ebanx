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
    firstName: 'MESSI',
    lastName: 'LIONEL ANDRES',
    document: '23-33016244-9',
    documentType: 'CUIL',
    documentTypeId: 'ARG_CUIL',
    address: Faker.address.streetName(),
    city: Faker.address.city(),
    state: 'Catamarca',
    stateId: 'K',
    zipcode: Faker.address.zipCode(),
    phone: Faker.phone.phoneNumberFormat(2),
    email: Faker.internet.email(),
    country: 'Argentina',
    countryId: 'AR',
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

  context('Argentina', () => {
    context('Efectivo', () => {
      it('can buy `blue horizons bracelets` using Rapipago to personal', () => {
        magento.buyBlueHorizonsBraceletsWithEfectivoToPersonal(mock(
          {
            paymentMethod: defaults.pay.api.DEFAULT_VALUES.paymentMethods.ar.efectivo.types.rapipago,
          }
        ));
      });

      it('can buy `blue horizons bracelets` using Pagofacil to personal', () => {
        magento.buyBlueHorizonsBraceletsWithEfectivoToPersonal(mock(
          {
            paymentMethod: defaults.pay.api.DEFAULT_VALUES.paymentMethods.ar.efectivo.types.pagofacil,
          }
        ));
      });

      it('can buy `blue horizons bracelets` using OtrosCupones to personal', () => {
        magento.buyBlueHorizonsBraceletsWithEfectivoToPersonal(mock(
          {
            paymentMethod: defaults.pay.api.DEFAULT_VALUES.paymentMethods.ar.efectivo.types.otrosCupones,
          }
        ));
      });
    });

    context('Credit Card', () => {
      it('can buy `blue horizons bracelets` using credit card', () => {
        const checkoutData = mock({
          paymentMethod: defaults.pay.api.DEFAULT_VALUES.paymentMethods.ar.creditcard.id,
          card: {
            name: Faker.name.findName(),
            number: defaults._globals.cardsWhitelist.mastercard,
            expiryYear: '2028',
            expiryMonth: '12',
            cvv: '123',
          },
        });

        magento.buyBlueHorizonsBraceletsWithCreditCardToPersonal(checkoutData, (resp) => {
          api.queryPayment(resp.hash, Cypress.env('DEMO_INTEGRATION_KEY'), (payment) => {
            const checkoutPayment = Api.paymentData({
              amount_ext: (Cypress.env('DEMO_SHIPPING_RATE') + Cypress.env('BLUE_HORIZONS_BRACELETS_PRICE')).toFixed(2),
              payment_type_code: 'mastercard',
              instalments: '1',
              status: 'CO',
            });

            wrapOrderAssertations(payment, checkoutPayment, Api.customerData(checkoutData));
          });
        });
      });
    });
  });
});
