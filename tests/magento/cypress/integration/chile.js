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
    phone: Faker.phone.phoneNumberFormat(2),
    email: Faker.internet.email(),
    country: 'Chile',
    countryId: 'CL',
  }
));

let magento;

describe('Shopping', () => {
  before(() => {
    magento = new Magento(cy);

    magento.setupPlugin();
  });

  context('Chile', () => {
    context('Sencillito', () => {
      it('can buy `blue horizons bracelets` using Sencillito to personal', () => {
        magento.buyBlueHorizonsBraceletsWithSencillitoToPersonal(mock(
          {
            paymentMethod: defaults.pay.api.DEFAULT_VALUES.paymentMethods.cl.sencillito.id,
          }
        ));
      });
    });

    context('Servipag', () => {
      it('can buy `blue horizons bracelets` using Servipag to personal', () => {
        magento.buyBlueHorizonsBraceletsWithServipagToPersonal(mock(
          {
            paymentMethod: defaults.pay.api.DEFAULT_VALUES.paymentMethods.cl.servipag.id,
          }
        ));
      });
    });

    context('Webpay', () => {
      it('can buy `blue horizons bracelets` using Webpay to personal', () => {
        magento.buyBlueHorizonsBraceletsWithWebpayToPersonal(mock(
          {
            document: '248760164',
            paymentMethod: defaults.pay.api.DEFAULT_VALUES.paymentMethods.cl.webpay.id,
          }
        ));
      });
    });

    context('Multicaja', () => {
      it('can buy `blue horizons bracelets` using Multicaja to personal', () => {
        magento.buyBlueHorizonsBraceletsWithMulticajaToPersonal(mock(
          {
            paymentMethod: defaults.pay.api.DEFAULT_VALUES.paymentMethods.cl.multicaja.id,
          }
        ));
      });
    });
  });
});
