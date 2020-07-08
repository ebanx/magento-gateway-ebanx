"use strict";Validation.add("brand-required"," ",function(t){return!Validation.get("IsEmpty").test(t)}),Validation.add("validate-ar-document-length","Document digit number is invalid",function(t){return"ARG_DNI"===document.querySelector("#ebanx-document-type-ebanx_cc_ar").value?7===t.length||8===t.length:13===t.length});
//# sourceMappingURL=validator.js.map
