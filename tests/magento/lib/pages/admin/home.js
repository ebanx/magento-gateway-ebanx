export default class Home {
  constructor(cy) {
    this.cy = cy;
  }

  goToConfigurations() {
    this.cy    
      .get('#nav > li:nth-child(9) > ul > li.last.level1 > a', { timeout: 10000 })
      .click({ force: true })
      .get('#system_config_tabs > li:nth-child(4) > dl > dd:nth-child(10) > a', { timeout: 10000 })
      .should('be.visible');

    return this;
  }
}
