export default class MyOrders {
  constructor(cy) {
    this.cy = cy;
  }

  stillOnView() {
    this.cy
      .get('.sales-order-view')
      .should('be.visible');

    return this;
  }
}
