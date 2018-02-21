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
    state: Faker.address.state(),
    zipcode: Faker.address.zipCode(),
    phone: Faker.phone.phoneNumberFormat(2),
    email: Faker.internet.email(),
    country: 'Chile',
    countryId: 'CL',
  }
));

let magento;

describe('Magento', () => {
  before(() => {
    assertUrlStatus(Cypress.env('DEMO_URL'));

    magento = new Magento(cy);
  });

  context('Chile', () => {
    context('Sencillito', () => {
      it('can buy `wonder womans purse` using Sencillito to personal', () => {
        magento.buyWonderWomansPurseWithSencillitoToPersonal(mock(
          {
            paymentMethod: defaults.pay.api.DEFAULT_VALUES.paymentMethods.cl.sencillito.id,
          }
        ));
      });
    });

    context('ServiPag', () => {
      it('can buy `wonder womans purse` using ServiPag to personal', () => {
        magento.buyWonderWomansPurseWithServiPagToPersonal(mock(
          {
            paymentMethod: defaults.pay.api.DEFAULT_VALUES.paymentMethods.cl.servipag.id,
          }
        ));
      });
    });

    context('Webpay', () => {
      it('can buy `wonder womans purse` using Webpay to personal', () => {
        magento.buyWonderWomansPurseWithWebpayToPersonal(mock(
          {
            document: Faker.random.uuid(),
            paymentMethod: defaults.pay.api.DEFAULT_VALUES.paymentMethods.cl.webpay.id,
          }
        ));
      });
    });

    context('Multicaja', () => {
      it('can buy `wonder womans purse` using Multicaja to personal', () => {
        magento.buyWonderWomansPurseWithMulticajaToPersonal(mock(
          {
            paymentMethod: defaults.pay.api.DEFAULT_VALUES.paymentMethods.cl.multicaja.id,
          }
        ));
      });
    });
  });
});
