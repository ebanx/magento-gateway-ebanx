"use strict";var initInstalments=function(e,a){var r=document.querySelector("#"+e+"_instalments");"undefined"!=typeof ebanxUpdateFireInterest&&r&&r.addEventListener("change",function(){ebanxUpdateFireInterest()});var t=function(){var e=document.querySelector("#cc-"+a+"-local-amount"),t=r.options[r.selectedIndex],n=!(!t||!t.getAttribute)&&t.getAttribute("data-local-amount");e&&e.innerHTML&&n&&(e.innerHTML="<strong> "+n+" </strong>")};t(),r&&(document.addEventListener("DOMContentLoaded",t),r.addEventListener("change",t))};
//# sourceMappingURL=instalments.js.map
