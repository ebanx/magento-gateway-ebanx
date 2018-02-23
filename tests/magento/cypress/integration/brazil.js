/* global Cypress, it, describe, before, context, cy */

import R from 'ramda';
import Faker from 'faker';
import defaults from '../../../defaults';
import {
  wrapOrderAssertations,
} from '../../../utils';
import Magento from '../../lib/operator';
import Api from '../../../pay/lib/operator';

Faker.locale = 'pt_BR';

const mock = (data) => (R.merge(
  data,
  {
    firstName: Faker.name.firstName(),
    lastName: Faker.name.lastName(),
    address: Faker.address.streetName(),
    city: Faker.address.city(),
    state: Faker.address.stateAbbr(),
    country: 'Brazil',
    countryId: 'BR',
    zipcode: '80010010',
    phone: Faker.phone.phoneNumberFormat(2),
    email: Faker.internet.email(),
    document: '278.517.215-98',
  }
));

const brPayCustomerData = (checkoutData) => Api.customerData(
  checkoutData,
  {
    document: R.pipe(R.pick(['document']), R.values, R.join(''), R.replace(/\D/g, ''))(checkoutData),
  }
);

let api;
let magento;

describe('Shopping', () => {
  before(() => {
    api = new Api(cy);
    magento = new Magento(cy);

    magento.setupPlugin();
  });

  context('Brazil', () => {
    context('Boleto', () => {
      it('can buy `blue horizons bracelets` using boleto to personal', () => {
        const checkoutData = mock(
          {
            paymentMethod: defaults.pay.api.DEFAULT_VALUES.paymentMethods.br.boleto.id,
          }
        );

        magento.buyBlueHorizonsBraceletsWithBoletoToPersonal(checkoutData, (resp) => {
          api.queryPayment(resp.hash, Cypress.env('DEMO_INTEGRATION_KEY'), (payment) => {
            const checkoutPayment = Api.paymentData({
              payment_type_code: checkoutData.paymentMethod,
              boleto_url: `${defaults.pay.url}/print/?hash=${resp.hash}`,
              instalments: '1',
              status: 'PE',
              amount_ext: (Cypress.env('DEMO_SHIPPING_RATE') + Cypress.env('BLUE_HORIZONS_BRACELETS_PRICE')).toFixed(2),
            });

            wrapOrderAssertations(payment, checkoutPayment, brPayCustomerData(checkoutData));
          });
        });
      });
    });

    context('Credit Card', () => {
      it('can buy `blue horizons bracelets`, create account and can one-click', () => {
        const checkoutData = mock({
          paymentMethod: defaults.pay.api.DEFAULT_VALUES.paymentMethods.br.creditcard.id,
          card: {
            number: defaults._globals.cardsWhitelist.mastercard,
            expiryYear: '2028',
            expiryMonth: '12',
            cvv: '123',
            save: true,
          },
          password: Faker.internet.password(),
        });

        magento.buyBlueHorizonsBraceletsWithCreditCardToPersonal(checkoutData, (resp) => {
          api.queryPayment(resp.hash, Cypress.env('DEMO_INTEGRATION_KEY'), (payment) => {
            const checkoutPayment = Api.paymentData({
              amount_ext: (Cypress.env('DEMO_SHIPPING_RATE') + Cypress.env('BLUE_HORIZONS_BRACELETS_PRICE')).toFixed(2),
              payment_type_code: 'mastercard',
              instalments: '1',
              status: 'CO',
            });

            wrapOrderAssertations(payment, checkoutPayment, brPayCustomerData(checkoutData));

            magento.buyBlueHorizonsBraceletsByOneClick(checkoutData.card.cvv);
          });
        });
      });
    });

    context('Tef', () => {
      it('can buy `blue horizons bracelets` using tef (Itaú) to personal', () => {
        const checkoutData = mock(
          {
            paymentMethod: defaults.pay.api.DEFAULT_VALUES.paymentMethods.br.tef.id,
            paymentType: defaults.pay.api.DEFAULT_VALUES.paymentMethods.br.tef.types.itau.id,
          }
        );

        magento.buyBlueHorizonsBraceletsWithTefToPersonal(checkoutData, (resp) => {
          api.queryPayment(resp.hash, Cypress.env('DEMO_INTEGRATION_KEY'), (payment) => {
            const checkoutPayment = Api.paymentData({
              amount_ext: (Cypress.env('DEMO_SHIPPING_RATE') + Cypress.env('BLUE_HORIZONS_BRACELETS_PRICE')).toFixed(2),
              payment_type_code: 'itau',
              instalments: '1',
              status: 'CO',
            });

            wrapOrderAssertations(payment, checkoutPayment, brPayCustomerData(checkoutData));
          });
        });
      });
    });
  });
});
