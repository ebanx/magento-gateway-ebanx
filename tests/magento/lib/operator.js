import Home from './pages/home';
import Cart from './pages/cart';
import Checkout from './pages/checkout';
import ThankYou from './pages/thankYou';
import MyOrders from './pages/myOrders';
import WonderWomansPurse from './pages/wonderWomansPurse';

import { tryNext } from '../../utils';

const buyWonderWomansPurse = Symbol('buyWonderWomansPurse');

export default class Magento {
  constructor(cy) {
    this.cy = cy;
    this.pages = {
      home: new Home(cy),
      cart: new Cart(cy),
      checkout: new Checkout(cy),
      thankYou: new ThankYou(cy),
      myOrders: new MyOrders(cy),
      wonderWomansPurse: new WonderWomansPurse(cy),
    };
  }

  [buyWonderWomansPurse]() {
    this.pages.wonderWomansPurse
      .buy();

    this.pages.cart
      .proceedToCheckoutWithOpened();
  }

  logout() {
    this.pages.home
      .logout();

    return this;
  }

  buyWonderWomansPurseByOneClick(cvv) {
    this.pages.wonderWomansPurse
      .buyByOneClick(cvv);

    this.pages.myOrders
      .stillOnView();

    return this;
  }

  buyWonderWomansPurseWithSpeiToPersonal(data) {
    this[buyWonderWomansPurse]();

    this.pages.checkout.placeWithSpei(data, () => {
      this.pages.thankYou
        .stillOnSpei();
    });
  }

  buyWonderWomansPurseWithTefToPersonal(data, next) {
    this[buyWonderWomansPurse]();

    this.pages.checkout
      .placeWithTef(data, (resp) => {
        tryNext(next, resp);
      });

    // pages.thankYou
    //   .stillOnTef();

    return this;
  }

  buyWonderWomansPurseWithSafetyPayToPersonal(data) {
    this[buyWonderWomansPurse]();

    this.pages.checkout.placeWithSafetyPay(data);

    // this.pages.thankYou
    //   .stillOnSafetyPay();

    return this;
  }

  buyWonderWomansPurseWithBalotoToPersonal(data) {
    this[buyWonderWomansPurse]();

    this.pages.checkout.placeWithBaloto(data, () => {
      this.pages.thankYou
        .stillOnBaloto();
    });
  }

  buyWonderWomansPurseWithEfectivoToPersonal(data) {
    this[buyWonderWomansPurse]();

    this.pages.checkout
      .placeWithEfectivo(data, () => {
        this.pages.thankYou
          .stillOnEfectivo(data.paymentMethod);
      });


    return this;
  }

  buyWonderWomansPurseWithPseToPersonal(data) {
    this[buyWonderWomansPurse]();

    this.pages.checkout.placeWithPse(data);

    // this.pages.thankYou
    //   .stillOnPse();

    return this;
  }

  buyWonderWomansPurseWithOxxoToPersonal(data) {
    this[buyWonderWomansPurse]();

    this.pages.checkout.placeWithOxxo(data, () => {
      this.pages.thankYou
        .stillOnOxxo();
    });

    return this;
  }

  buyWonderWomansPurseWithCreditCardToPersonal(data, next) {
    this[buyWonderWomansPurse]();

    this.pages.checkout
      .placeWithCreditCard(data, () => {
        this.pages.thankYou
          .stillOnCreditCard((resp) => {
            tryNext(next, resp);
          });
      });
  }

  buyWonderWomansPurseWithPagoEfectivoToPersonal(data) {
    this[buyWonderWomansPurse]();

    this.pages.checkout.placeWithPagoEfectivo(data, () => {
      this.pages.thankYou
        .stillOnPagoEfectivo();
    });
  }

  buyWonderWomansPurseWithDebitCardToPersonal(data, next) {
    this[buyWonderWomansPurse]();

    this.pages.checkout
      .placeWithDebitCard(data, () => {
        this.pages.thankYou
          .stillOnDebitCard();

        tryNext(next);
      });
  }

  buyWonderWomansPurseWithBoletoToPersonal(data, next) {
    this[buyWonderWomansPurse]();

    this.pages.checkout.placeWithBoleto(data, () => {
      this.pages.thankYou
        .stillOnBoleto((resp) => {
          tryNext(next, resp);
        });
    });
  }

  buyWonderWomansPurseWithServiPagToPersonal(data) {
    this[buyWonderWomansPurse]();

    this.pages.checkout
      .placeWithServiPag(data);

    // pages.thankYou
    //   .stillOnServiPag();

    return this;
  }

  buyWonderWomansPurseWithMulticajaToPersonal(data) {
    this[buyWonderWomansPurse]();

    this.pages.checkout
      .placeWithMulticaja(data);

    // pages.thankYou
    //   .stillOnMulticaja();

    return this;
  }

  buyWonderWomansPurseWithWebpayToPersonal(data) {
    this[buyWonderWomansPurse]();

    this.pages.checkout
      .placeWithWebpay(data);

    // pages.thankYou
    //   .stillOnWebpay();

    return this;
  }

  buyWonderWomansPurseWithSencillitoToPersonal(data) {
    this[buyWonderWomansPurse]();

    this.pages.checkout
      .placeWithSencillito(data);

    // pages.thankYou
    //   .stillOnSencillito();

    return this;
  }
}
