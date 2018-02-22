import Home from './pages/shopping/home';
import Cart from './pages/shopping/cart';
import Checkout from './pages/shopping/checkout';
import ThankYou from './pages/shopping/thankYou';
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
      checkout: new Checkout(cy),
      thankYou: new ThankYou(cy),
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

  setupPlugin() {
    this.pages.admin.login
      .login();

    this.pages.admin.home
      .goToConfigurations();

    this.pages.admin.configurations
      .goToPaymentMethods();

    this.pages.admin.paymentMethods
      .setupEbanxPlugin();
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

  buyBlueHorizonsBraceletsWithBoletoToPersonal(data, next) {
    this[buyBlueHorizonsBracelets]();

    this.pages.checkout.placeWithBoleto(data, () => {
      this.pages.thankYou
        .stillOnBoleto((resp) => {
          tryNext(next, resp);
        });
    });
  }
}
