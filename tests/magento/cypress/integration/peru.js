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
    document: '123456789',
    email: Faker.internet.email(),
    country: 'Peru',
    countryId: 'PE',
  }
));

let magento;

describe('Magento', () => {
  before(() => {
    magento = new Magento(cy);

    magento.setupPlugin();
  });

  context('Peru', () => {
    context('PagoEfectivo', () => {
      it('can buy `blue horizons bracelets` using PagoEfectivo to personal', () => {
        magento.buyBlueHorizonsBraceletsWithPagoEfectivoToPersonal(mock(
          {
            paymentMethod: defaults.pay.api.DEFAULT_VALUES.paymentMethods.pe.pagoEfectivo.id,
          }
        ));
      });
    });

    context('SafetyPay', () => {
      it('can buy `blue horizons bracelets` using SafetyPay(CASH) to personal', () => {
        magento.buyBlueHorizonsBraceletsWithSafetyPayToPersonal(mock(
          {
            paymentMethod: defaults.pay.api.DEFAULT_VALUES.paymentMethods.pe.safetyPay.id,
            paymentType: defaults.pay.api.DEFAULT_VALUES.paymentMethods.pe.safetyPay.types.cash,
          }
        ));
      });

      it('can buy `blue horizons bracelets` using SafetyPay(ONLINE) to personal', () => {
        magento.buyBlueHorizonsBraceletsWithSafetyPayToPersonal(mock(
          {
            paymentMethod: defaults.pay.api.DEFAULT_VALUES.paymentMethods.pe.safetyPay.id,
            paymentType: defaults.pay.api.DEFAULT_VALUES.paymentMethods.pe.safetyPay.types.online,
          }
        ));
      });
    });
  });
});
