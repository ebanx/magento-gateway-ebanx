"use strict";var handler=function(e,a,t){var n=t.value.replace(/\D/g,""),r=t.value.length>a?1:0;VMasker(t).unMask(),VMasker(t).maskPattern(e[r]),t.value=VMasker.toPattern(n,e[r])},inputHandler=function(e,a){if("br"===a.toLowerCase()&&e){var t=["999.999.999-999","99.999.999/9999-99"];VMasker(e).maskPattern(t[0]),e.addEventListener("input",function(e){handler(t,14,e.target)},!1)}},initTaxVatLabel=function(e){var a=document.querySelector('label[for="taxvat"]');a&&(a.innerHTML=e)},hideTaxVat=function(){var e=document.querySelector('label[for="taxvat"]');e&&(e.style.display="none");var a=document.getElementById("taxvat");a&&(a.style.display="none")};
//# sourceMappingURL=input-handler.js.map
