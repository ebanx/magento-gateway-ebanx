export default {
  _globals: {
    cardsWhitelist: {
      mastercard: '5555 5555 5555 4444',
      visa: '4111 1111 1111 1111',
    },
    cardsBlacklist: {
      visa: '4000 0000 0000 0002',
    },
  },
  pay: {
    url: 'https://sandbox.ebanx.com',
    newUrl: 'https://sandbox.ebanxpay.com',
    api: {
      url: 'https://sandbox.ebanx.com/ws',
      newUrl: 'https://sandbox.ebanxpay.com/ws',
      DEFAULT_VALUES: {
        paymentMethods: {
          ar: {
            efectivo: {
              id : 'efectivo',
              types: {
                rapipago: 'Rapipago',
                pagofacil: 'Pagofacil',
                otrosCupones: 'Otros Cupones',
              },
            },
            creditcard: {
              id: 'creditcard',
            },
          },
          cl: {
            sencillito: {
              id: 'sencillito',
            },
            servipag: {
              id: 'servipag',
            },
            webpay: {
              id: 'webpay',
            },
            multicaja: {
              id: 'multicaja',
            },
          },
          co: {
            baloto: {
              id: 'baloto',
            },
            creditcard: {
              id: 'creditcard',
            },
            pse: {
              id: 'eft',
              types: {
                agrario: {
                  name: 'Banco Agrario',
                  id: 'banco_agrario',
                },
              },
            },
          },
          pe: {
            pagoEfectivo: {
              id: 'pagoefectivo',
            },
            safetyPay: {
              id: 'safetypay',
              types: {
                cash: 'Cash',
                online: 'Online',
              },
            },
          },
          ec: {
            safetyPay: {
              id: 'safetypay',
              types: {
                cash: 'Cash',
                online: 'Online',
              },
            },
          },
          mx: {
            oxxo: {
              id: 'oxxo',
            },
            creditcard: {
              id: 'creditcard',
            },
            debitcard: {
              id: 'debitcard',
            },
            spei: {
              id: 'spei',
            },
          },
          br: {
            boleto: {
              id: 'boleto',
            },
            creditcard: {
              id: 'creditcard',
            },
            tef: {
              id: 'tef',
              types: {
                itau: {
                  id: 'itau',
                  label: 'Itaú',
                },
              },
            },
            ebanxbalance: {
              id: 'ebanxbalance',
            },
          },
          uy: {
            creditcard: {
              id: 'creditcard',
            },
          },
        },
      },
    },
  },
};
