"use strict";var maskValues={br:{masks:["999.999.999-999","99.999.999/9999-99"],changeOnLenght:14},ar:{masks:["SS-SSSSSSSS-S"],changeOnLenght:0},co:{masks:["999999999999999999"],changeOnLenght:0},cl:{masks:["99.999.999-S"],changeOnLenght:0}},handler=function(e,a,t){var n=0;0!==a&&(n=t.value.length>a?1:0),VMasker(t).unMask(),VMasker(t).maskPattern(e[n]),t.value=VMasker.toPattern(t.value,e[n])},inputHandler=function(e,a){if("AR"===a&&e.classList.add("validate-ar-document-length"),maskValues[a.toLowerCase()]&&e){var t=maskValues[a.toLowerCase()];VMasker(e).maskPattern(t.masks[0]),e.addEventListener("input",function(e){handler(t.masks,t.changeOnLenght,e.target)},!1)}},initTaxVatLabel=function(e){var a=document.querySelector('label[for="taxvat"]');a&&(a.innerHTML=e)},hideTaxVat=function(){var e=document.querySelector('label[for="taxvat"]');e&&(e.style.display="none");var a=document.getElementById("taxvat");a&&(a.style.display="none")},selectOption=function(e,a){e&&null!==e&&e.forEach(function(e){e.value===a&&e.setAttribute("selected","selected")})};
//# sourceMappingURL=input-handler.js.map
