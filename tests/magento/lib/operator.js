import Home from './pages/shopping/home';
import Cart from './pages/shopping/cart';
import Account from './pages/shopping/account';
import Checkout from './pages/shopping/checkout';
import ThankYou from './pages/shopping/thankYou';
import Notification from './pages/shopping/notification';
import BlueHorizonsBracelets from './pages/shopping/blueHorizonsBracelets';

import AdminHome from './pages/admin/home';
import AdminLogin from './pages/admin/login';
import AdminConfigurations from './pages/admin/configurations';
import AdminPaymentMethods from './pages/admin/paymentMethods';

import { tryNext } from '../../utils';

const buyBlueHorizonsBracelets = Symbol('buyBlueHorizonsBracelets');

export default class Magento {
  constructor(cy) {
    this.cy = cy;
    this.pages = {
      home: new Home(cy),
      cart: new Cart(cy),
      account: new Account(cy),
      checkout: new Checkout(cy),
      thankYou: new ThankYou(cy),
      notification: new Notification(cy),
      blueHorizonsBracelets: new BlueHorizonsBracelets(cy),
      admin: {
        home: new AdminHome(cy),
        login: new AdminLogin(cy),
        configurations: new AdminConfigurations(cy),
        paymentMethods: new AdminPaymentMethods(cy),
      },
    };
  }

  [buyBlueHorizonsBracelets]() {
    this.pages.blueHorizonsBracelets
      .buy();

    this.pages.cart
      .proceedToCheckoutWithOpened();
  }

  logout() {
    this.pages.home
      .logout();

    return this;
  }

  setupPlugin(setupOptions = {}) {
    this.pages.admin.login
      .login();

    this.pages.admin.home
      .goToConfigurations();

    this.pages.admin.configurations
      .goToPaymentMethods();

    this.pages.admin.paymentMethods
      .setupEbanxPlugin(setupOptions);
  }

  createAccount(data) {
    this.pages.account.create(data);
  }

  buyBlueHorizonsBraceletsByOneClick(cvv) {
    this.pages.blueHorizonsBracelets
      .buyByOneClick(cvv);

    return this;
  }

  buyBlueHorizonsBraceletsWithTefToPersonal(data, next) {
    this[buyBlueHorizonsBracelets]();

    this.pages.checkout
      .placeWithTef(data, () => {
        this.pages.thankYou
          .stillOnTef((resp) => {
            tryNext(next, resp);
          });
      });
  }

  buyBlueHorizonsBraceletsWithEfectivoToPersonal(data, next) {
    this[buyBlueHorizonsBracelets]();

    this.pages.checkout
      .placeWithEfectivo(data, () => {
        this.pages.thankYou
          .stillOnEfectivo(data.paymentMethod, (resp) => {
            tryNext(next, resp);
          });
      });
  }

  buyBlueHorizonsBraceletsWithDebitCardToPersonal(data, next) {
    this[buyBlueHorizonsBracelets]();

    this.pages.checkout
      .placeWithDebitCard(data, () => {
        this.pages.thankYou
          .stillOnDebitCard((resp) => {
            tryNext(next, resp);
          });
      });
  }

  buyBlueHorizonsBraceletsWithCreditCardToPersonal(data, next) {
    this[buyBlueHorizonsBracelets]();

    this.pages.checkout
      .placeWithCreditCard(data, () => {
        this.pages.thankYou
          .stillOnCreditCard((resp) => {
            tryNext(next, resp);
          });
      });
  }

  failToBuyBlueHorizonsBraceletsWithCreditCard(data, next) {
    this[buyBlueHorizonsBracelets]();

    this.pages.checkout
      .placeWithCreditCard(data, () => {
        this.pages.thankYou
          .failedOnCreditCard(next);
      });
  }

  notifyCancelledCardPayment(hash, merchantPaymentCode) {
    this.pages.notification.notifyCancelledCardPayment(hash, merchantPaymentCode);
  }

  buyBlueHorizonsBraceletsWithOxxoToPersonal(data, next) {
    this[buyBlueHorizonsBracelets]();

    this.pages.checkout.placeWithOxxo(data, () => {
      this.pages.thankYou
        .stillOnOxxo((resp) => {
          tryNext(next, resp);
        });
    });
  }

  buyBlueHorizonsBraceletsWithBoletoToPersonal(data, next) {
    this[buyBlueHorizonsBracelets]();

    this.pages.checkout.placeWithBoleto(data, () => {
      this.pages.thankYou
        .stillOnBoleto((resp) => {
          tryNext(next, resp);
        });
    });
  }

  buyBlueHorizonsBraceletsWithBoletoLoggedIn(data, next) {
    this[buyBlueHorizonsBracelets]();

    this.pages.checkout.placeWithBoletoLoggedIn(data, () => {
      this.pages.thankYou
        .stillOnBoleto((resp) => {
          tryNext(next, resp);
        });
    });
  }

  buyBlueHorizonsBraceletsWithSpeiToPersonal(data, next) {
    this[buyBlueHorizonsBracelets]();

    this.pages.checkout.placeWithSpei(data, () => {
      this.pages.thankYou
        .stillOnSpei((resp) => {
          tryNext(next, resp);
        });
    });
  }

  buyBlueHorizonsBraceletsWithSencillitoToPersonal(data, next) {
    this[buyBlueHorizonsBracelets]();

    this.pages.checkout.placeWithSencillito(data, () => {
      this.pages.thankYou
        .stillOnSencillito((resp) => {
          tryNext(next, resp);
        });
    });
  }

  buyBlueHorizonsBraceletsWithServipagToPersonal(data, next) {
    this[buyBlueHorizonsBracelets]();

    this.pages.checkout.placeWithServipag(data, () => {
      this.pages.thankYou
        .stillOnServipag((resp) => {
          tryNext(next, resp);
        });
    });
  }

  buyBlueHorizonsBraceletsWithWebpayToPersonal(data, next) {
    this[buyBlueHorizonsBracelets]();

    this.pages.checkout.placeWithWebpay(data, () => {
      this.pages.thankYou
        .stillOnWebpay((resp) => {
          tryNext(next, resp);
        });
    });
  }

  buyBlueHorizonsBraceletsWithPagoEfectivoToPersonal(data, next) {
    this[buyBlueHorizonsBracelets]();

    this.pages.checkout.placeWithPagoEfectivo(data, () => {
      this.pages.thankYou
        .stillOnPagoEfectivo((resp) => {
          tryNext(next, resp);
        });
    });
  }

  buyBlueHorizonsBraceletsWithSafetyPayToPersonal(data, next) {
    this[buyBlueHorizonsBracelets]();

    this.pages.checkout.placeWithSafetyPay(data, () => {
      this.pages.thankYou
        .stillOnSafetyPay((resp) => {
          tryNext(next, resp);
        });
    });
  }

  buyBlueHorizonsBraceletsWithMulticajaToPersonal(data, next) {
    this[buyBlueHorizonsBracelets]();

    this.pages.checkout.placeWithMulticaja(data, () => {
      this.pages.thankYou
        .stillOnMulticaja((resp) => {
          tryNext(next, resp);
        });
    });
  }

  buyBlueHorizonsBraceletsWithPseToPersonal(data, next) {
    this[buyBlueHorizonsBracelets]();

    this.pages.checkout.placeWithPse(data, () => {
      this.pages.thankYou
        .stillOnPse((resp) => {
          tryNext(next, resp);
        });
    });
  }

  buyBlueHorizonsBraceletsWithBalotoToPersonal(data, next) {
    this[buyBlueHorizonsBracelets]();

    this.pages.checkout.placeWithBaloto(data, () => {
      this.pages.thankYou
        .stillOnBaloto((resp) => {
          tryNext(next, resp);
        });
    });
  }
}
