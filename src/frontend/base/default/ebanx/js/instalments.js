/* global ebanxUpdateFireInterest */

const initInstalments = (code, lowerCountry) => { // eslint-disable-line no-unused-vars
  if (typeof ebanxUpdateFireInterest !== 'undefined') {
    document.querySelector(`#${code}_instalments`)
      .addEventListener('change', () => {
        ebanxUpdateFireInterest();
      });
  }

  var selectInstalment = document.querySelector(`#${code}_instalments`);

  const updateInstalment = () => {
    const text = document.querySelector(`#cc-${lowerCountry}-local-amount`);
    const instalmentOption = selectInstalment.options[selectInstalment.selectedIndex];
    const localAmount = instalmentOption && instalmentOption.getAttribute ? instalmentOption.getAttribute('data-local-amount') : false;

    if (text && text.innerHTML && localAmount) {
      text.innerHTML = `<strong> ${localAmount} </strong>`;
    }
  };

  if (selectInstalment) {
    document.addEventListener('DOMContentLoaded', updateInstalment);
    selectInstalment.addEventListener('change', updateInstalment);
  }
};
