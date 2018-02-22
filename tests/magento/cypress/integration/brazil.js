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

describe('Magento', () => {
  before(() => {
    api = new Api(cy);
    magento = new Magento(cy);

    magento.setupPlugin();
  });

  context('Brazil', () => {
    context('Boleto', () => {
      it('can buy `wonder womans purse` using boleto to personal', () => {
        const checkoutData = mock(
          {
            paymentMethod: defaults.pay.api.DEFAULT_VALUES.paymentMethods.br.boleto.id,
          }
        );

        magento.buyBlueHorizonsBraceletsWithBoletoToPersonal(checkoutData);
      });
    });
  });
});
