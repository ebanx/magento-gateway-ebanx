import R from 'ramda';
import Joi from 'joi';
import defaults from '../../../defaults';

export const CHECKOUT_SCHEMA = {
  pe: {
    compliance: () => ({
      city: Joi.string().required(),
      phone: Joi.string().required(),
      email: Joi.string().required(),
      state: Joi.string().required(),
      country: Joi.string().required(),
      zipcode: Joi.string().required(),
      address: Joi.string().required(),
      password: Joi.string().optional(),
      document: Joi.string().required(),
      lastName: Joi.string().required(),
      countryId: Joi.string().required(),
      firstName: Joi.string().required(),
      paymentMethod: Joi.any().allow(
        R.pluck('id')(
          R.values(
            defaults.pay.api.DEFAULT_VALUES.paymentMethods.pe
          )
        )
      ).optional(),
    }),
    pagoEfectivo() {
      return Joi.object().keys(
        Object.assign(
          {},
          this.compliance(),
          {
            schema: 'PeruPagoEfectivo',
          }
        )
      ).without('schema', R.keys(this.compliance()));
    },
    safetyPay() {
      return Joi.object().keys(
        Object.assign(
          {},
          this.compliance(),
          {
            schema: 'PeruSafetyPay',
            paymentType: Joi.any().allow(
              defaults.pay.api.DEFAULT_VALUES.paymentMethods.pe.safetyPay.types
            ).required(),
          }
        )
      ).without('schema', [...R.keys(this.compliance()), ...['paymentType']]);
    },
  },
  co: {
    compliance: () => ({
      city: Joi.string().required(),
      phone: Joi.string().required(),
      email: Joi.string().required(),
      state: Joi.string().required(),
      country: Joi.string().required(),
      zipcode: Joi.string().required(),
      address: Joi.string().required(),
      document: Joi.string().required(),
      password: Joi.string().optional(),
      lastName: Joi.string().required(),
      countryId: Joi.string().required(),
      firstName: Joi.string().required(),
      paymentMethod: Joi.any().allow(
        R.pluck('id')(
          R.values(
            defaults.pay.api.DEFAULT_VALUES.paymentMethods.ec
          )
        )
      ).optional(),
    }),
    creditcard() {
      return Joi.object().keys(
        Object.assign(
          {},
          this.compliance(),
          {
            schema: 'ColombiaCreditCard',
            card: Joi.object().keys({
              save: Joi.boolean().optional(),
              number: Joi.string().required(),
              name: Joi.string().required(),
              cvv: Joi.string().required(),
              expiryMonth: Joi.string().min(2).max(2).required(),
              expiryYear: Joi.string().min(4).max(4).required(),
            }).required(),
          }
        )
      ).without('schema', [...R.keys(this.compliance()), ...['card']]);
    },
    baloto() {
      return Joi.object().keys(
        Object.assign(
          {},
          this.compliance(),
          {
            schema: 'ColombiaBaloto',
          }
        )
      ).without('schema', R.keys(this.compliance()));
    },
    pse() {
      return Joi.object().keys(
        Object.assign(
          {},
          this.compliance(),
          {
            schema: 'ColombiaPse',
            paymentTypeId: Joi.string().required(),
            paymentType: Joi.any().allow(
              defaults.pay.api.DEFAULT_VALUES.paymentMethods.co.pse.types
            ).required(),
          }
        )
      ).without('schema', [...R.keys(this.compliance()), ...['paymentType', 'paymentTypeId']]);
    },
  },
  mx: {
    compliance: () => ({
      city: Joi.string().required(),
      phone: Joi.string().required(),
      email: Joi.string().required(),
      state: Joi.string().required(),
      country: Joi.string().required(),
      zipcode: Joi.string().required(),
      address: Joi.string().required(),
      password: Joi.string().optional(),
      lastName: Joi.string().required(),
      countryId: Joi.string().required(),
      firstName: Joi.string().required(),
      paymentMethod: Joi.any().allow(
        R.pluck('id')(
          R.values(
            defaults.pay.api.DEFAULT_VALUES.paymentMethods.mx
          )
        )
      ).optional(),
    }),
    spei() {
      return Joi.object().keys(
        Object.assign(
          {},
          this.compliance(),
          {
            schema: 'MexicoSpei',
          }
        )
      ).without('schema', R.keys(this.compliance()));
    },
    oxxo() {
      return Joi.object().keys(
        Object.assign(
          {},
          this.compliance(),
          {
            schema: 'MexicoOxxo',
          }
        )
      ).without('schema', R.keys(this.compliance()));
    },
    creditcard() {
      return Joi.object().keys(
        Object.assign(
          {},
          this.compliance(),
          {
            schema: 'MexicoCreditCard',
            card: Joi.object().keys({
              save: Joi.boolean().optional(),
              name: Joi.string().required(),
              number: Joi.string().required(),
              cvv: Joi.string().required(),
              expiryMonth: Joi.string().min(2).max(2).required(),
              expiryYear: Joi.string().min(4).max(4).required(),
            }).required(),
          }
        )
      ).without('schema', [...R.keys(this.compliance()), ...['card']]);
    },
    debitcard() {
      return Joi.object().keys(
        Object.assign(
          {},
          this.compliance(),
          {
            schema: 'MexicoDebitCard',
            card: Joi.object().keys({
              save: Joi.boolean().optional(),
              name: Joi.string().required(),
              number: Joi.string().required(),
              cvv: Joi.string().required(),
              expiryMonth: Joi.string().min(2).max(2).required(),
              expiryYear: Joi.string().min(4).max(4).required(),
            }).required(),
          }
        )
      ).without('schema', [...R.keys(this.compliance()), ...['card']]);
    },
  },
  ar: {
    compliance: () => ({
      city: Joi.string().required(),
      phone: Joi.string().required(),
      email: Joi.string().required(),
      state: Joi.string().required(),
      country: Joi.string().required(),
      stateId: Joi.string().required(),
      zipcode: Joi.string().required(),
      address: Joi.string().required(),
      password: Joi.string().optional(),
      document: Joi.string().required(),
      documentType: Joi.string().required(),
      documentTypeId: Joi.string().required(),
      lastName: Joi.string().required(),
      countryId: Joi.string().required(),
      firstName: Joi.string().required(),
    }),
    efectivo() {
      return Joi.object().keys(
        Object.assign(
          {},
          this.compliance(),
          {
            schema: 'ArgentinaEfectivo',
            paymentMethod: Joi.any().allow(
              defaults.pay.api.DEFAULT_VALUES.paymentMethods.ar.efectivo.types
            ).required(),
          }
        )
      ).without('schema', [...R.keys(this.compliance()), ...['paymentMethod']]);
    },
    creditcard() {
      return Joi.object().keys(
        Object.assign(
          {},
          this.compliance(),
          {
            paymentMethod: Joi.any().allow('creditcard').required(),
            schema: 'ArgentinaCreditCard',
            card: Joi.object().keys({
              name: Joi.string().required(),
              number: Joi.string().required(),
              cvv: Joi.string().required(),
              expiryMonth: Joi.string().min(2).max(2).required(),
              expiryYear: Joi.string().min(4).max(4).required(),
            }).required(),
          }
        )
      ).without('schema', [...R.keys(this.compliance()), ...['card', 'paymentMethod']]);
    },
  },
  ec: {
    compliance: () => ({
      city: Joi.string().required(),
      phone: Joi.string().required(),
      email: Joi.string().required(),
      state: Joi.string().required(),
      country: Joi.string().required(),
      zipcode: Joi.string().required(),
      address: Joi.string().required(),
      password: Joi.string().optional(),
      lastName: Joi.string().required(),
      countryId: Joi.string().required(),
      firstName: Joi.string().required(),
      paymentMethod: Joi.any().allow(
        R.pluck('id')(
          R.values(
            defaults.pay.api.DEFAULT_VALUES.paymentMethods.ec
          )
        )
      ).optional(),
    }),
    safetyPay() {
      return Joi.object().keys(
        Object.assign(
          {},
          this.compliance(),
          {
            schema: 'EcuadorSafetyPay',
            paymentType: Joi.any().allow(
              defaults.pay.api.DEFAULT_VALUES.paymentMethods.ec.safetyPay.types
            ).required(),
          }
        )
      ).without('schema', [...R.keys(this.compliance()), ...['paymentType']]);
    },
  },
  br: {
    compliance: () => ({
      city: Joi.string().required(),
      phone: Joi.string().required(),
      email: Joi.string().required(),
      state: Joi.string().required(),
      country: Joi.string().required(),
      zipcode: Joi.string().required(),
      address: Joi.string().required(),
      password: Joi.string().optional(),
      document: Joi.string().required(),
      lastName: Joi.string().required(),
      countryId: Joi.string().required(),
      firstName: Joi.string().required(),
      paymentMethod: Joi.any().allow(
        R.pluck('id')(
          R.values(
            defaults.pay.api.DEFAULT_VALUES.paymentMethods.br
          )
        )
      ).required(),
    }),
    boleto() {
      return Joi.object().keys(
        Object.assign(
          {},
          this.compliance(),
          { schema: 'BrazilBoleto' }
        )
      ).without('schema', R.keys(this.compliance()));
    },
    tef() {
      return Joi.object().keys(
        Object.assign(
          {},
          this.compliance(),
          {
            schema: 'BrazilTef',
            paymentType: Joi.any().allow(
              R.pluck('id')(
                R.values(
                  defaults.pay.api.DEFAULT_VALUES.paymentMethods.br.tef.types
                )
              )
            ).required(),
          }
        )
      ).without('schema', [...R.keys(this.compliance()), ...['paymentType']]);
    },
    creditcard() {
      return Joi.object().keys(
        Object.assign(
          {},
          this.compliance(),
          {
            schema: 'BrazilCreditCard',
            card: Joi.object().keys({
              save: Joi.boolean().optional(),
              number: Joi.string().required(),
              cvv: Joi.string().required(),
              expiryMonth: Joi.string().min(2).max(2).required(),
              expiryYear: Joi.string().min(4).max(4).required(),
            }).required(),
          }
        )
      ).without('schema', [...R.keys(this.compliance()), ...['card']]);
    },
  },
  cl: {
    compliance: () => ({
      city: Joi.string().required(),
      phone: Joi.string().required(),
      email: Joi.string().required(),
      state: Joi.string().required(),
      country: Joi.string().required(),
      zipcode: Joi.string().required(),
      address: Joi.string().required(),
      password: Joi.string().optional(),
      lastName: Joi.string().required(),
      document: Joi.string().required(),
      countryId: Joi.string().required(),
      firstName: Joi.string().required(),
      paymentMethod: Joi.any().allow(
        R.pluck('id')(
          R.values(
            defaults.pay.api.DEFAULT_VALUES.paymentMethods.cl
          )
        )
      ).optional(),
    }),
    sencillito() {
      return Joi.object().keys(
        Object.assign(
          {},
          this.compliance(),
          {
            schema: 'ChileSencillito',
          }
        )
      ).without('schema', R.keys(this.compliance()));
    },
    servipag() {
      return Joi.object().keys(
        Object.assign(
          {},
          this.compliance(),
          {
            schema: 'ChileServiPag',
          }
        )
      ).without('schema', R.keys(this.compliance()));
    },
    webpay() {
      return Joi.object().keys(
        Object.assign(
          {},
          this.compliance(),
          {
            schema: 'ChileWebpay',
          }
        )
      ).without('schema', [...R.keys(this.compliance()), ...['document']]);
    },
    multicaja() {
      return Joi.object().keys(
        Object.assign(
          {},
          this.compliance(),
          {
            schema: 'ChileMulticaja',
          }
        )
      ).without('schema', R.keys(this.compliance()));
    },
  },
};
