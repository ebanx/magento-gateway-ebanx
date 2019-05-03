/* global ebanxUpdateFireInterest */

const initInstalments = (code, lowerCountry) => { // eslint-disable-line no-unused-vars
  const selectInstalment = document.querySelector(`#${code}_instalments`);

  if (typeof ebanxUpdateFireInterest !== 'undefined' && selectInstalment) {
    selectInstalment.addEventListener('change', () => {
      ebanxUpdateFireInterest();
    });
  }

  const updateInstalment = () => {
    const text = document.querySelector(`#cc-${lowerCountry}-local-amount`);
    const instalmentOption = selectInstalment.options[selectInstalment.selectedIndex];
    const localAmount = instalmentOption && instalmentOption.getAttribute ? instalmentOption.getAttribute('data-local-amount') : false;

    if (text && text.innerHTML && localAmount) {
      text.innerHTML = `<strong> ${localAmount} </strong>`;
    }
  };

  updateInstalment();
  if (selectInstalment) {
    document.addEventListener('DOMContentLoaded', updateInstalment);
    selectInstalment.addEventListener('change', updateInstalment);
  }
};
