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
    country: 'Ecuador',
    countryId: 'EC',
  }
));

let magento;

describe('Shopping', () => {
  before(() => {
    magento = new Magento(cy);

    magento.setupPlugin();
  });

  context('Ecuador', () => {
    context('SafetyPay', () => {
      it('can buy `blue horizons bracelets` using SafetyPay(CASH) to personal', () => {
        magento.buyBlueHorizonsBraceletsWithSafetyPayToPersonal(mock(
          {
            paymentType: defaults.pay.api.DEFAULT_VALUES.paymentMethods.ec.safetyPay.types.cash,
          }
        ));
      });

      it('can buy `blue horizons bracelets` using SafetyPay(ONLINE) to personal', () => {
        magento.buyBlueHorizonsBraceletsWithSafetyPayToPersonal(mock(
          {
            paymentType: defaults.pay.api.DEFAULT_VALUES.paymentMethods.ec.safetyPay.types.online,
          }
        ));
      });
    });
  });
});
