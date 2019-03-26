"use strict";Validation.add("brand-required"," ",function(e){return!Validation.get("IsEmpty").test(e)}),Validation.add("validate-ar-document-length","Document digit number is invalid",function(e){var t=document.querySelector("#ebanx-document-type-ebanx_cc_ar");return console.log(t.value),"ARG_DNI"===t.value?7===e.length||8===e.length:13===e.length});
//# sourceMappingURL=validator.js.map
