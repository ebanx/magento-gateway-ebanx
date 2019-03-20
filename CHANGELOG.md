# CHANGELOG

## 2.13.1
* Fix     - Update the lib js version [#354](https://github.com/ebanx/magento-gateway-ebanx/pull/354)
* Quality - Refactor checkout.js credit card tokenization process [#353](https://github.com/ebanx/magento-gateway-ebanx/pull/353)

## 2.13.0
* Feature - Remove cash payment from Argentina [#350](https://github.com/ebanx/magento-gateway-ebanx/pull/350)
* Fix     - Add card token presence verification on checkout [#349](https://github.com/ebanx/magento-gateway-ebanx/pull/349)
* Fix     - Inject Ebanx javascript and stylesheet in fancycheckout pages [#348](https://github.com/ebanx/magento-gateway-ebanx/pull/346)

## 2.12.3
* Fix - Plugincheck and UserAgent wrong version and JSON format [#346](https://github.com/ebanx/magento-gateway-ebanx/pull/346)

## 2.12.2
* Fix - Change IOF calculation on checkout review [#344](https://github.com/ebanx/magento-gateway-ebanx/pull/344)

## 2.12.1
* Fix - Remove Echo usage from plugincheck page [#342](https://github.com/ebanx/magento-gateway-ebanx/pull/342) 

## 2.12.0
* Feature - Add health check page [#337](https://github.com/ebanx/magento-gateway-ebanx/pull/337)
* Feature - Add UserAgent version tracking [#336](https://github.com/ebanx/magento-gateway-ebanx/pull/336) 

## 2.11.0
* Feature - Add Colombia Document Type [#328](https://github.com/ebanx/magento-gateway-ebanx/pull/328)

## 2.10.0
* Feature - Add interest fee to order details [320](https://github.com/ebanx/magento-gateway-ebanx/pull/320)
* Feature - Change all API urls [321](https://github.com/ebanx/magento-gateway-ebanx/pull/321)
* Feature - Removed Demo Merchant integration keys from env file [322](https://github.com/ebanx/magento-gateway-ebanx/pull/322)

## 2.9.1
* Fix - Make chilean document mandatory [#317](https://github.com/ebanx/magento-gateway-ebanx/pull/317)

## 2.9.0
* Feature - Modify notification flow to account for declined Credit Card operations [#315](https://github.com/ebanx/magento-gateway-ebanx/pull/315)
* Feature - Document is required again in Colombia [#316](https://github.com/ebanx/magento-gateway-ebanx/pull/316)
* Fix - Update log data correctly [#313](https://github.com/ebanx/magento-gateway-ebanx/pull/313)

## 2.8.1
* Fix - Refunded payments will no longer change their status on notification arrival [#311](https://github.com/ebanx/magento-gateway-ebanx/pull/311)

## 2.8.0
* Feature - Force Argentinian document to be 11 digits long [#301](https://github.com/ebanx/magento-gateway-ebanx/pull/301)
* Feature - Check if platform configurations are compliant to extension needs [#302](https://github.com/ebanx/magento-gateway-ebanx/pull/302)
* Feature - Change return for notifications when payment is not found [#303](https://github.com/ebanx/magento-gateway-ebanx/pull/303)
* Feature - Show EBANX Account gateway only when store currency is USD [#304](https://github.com/ebanx/magento-gateway-ebanx/pull/304)
* Feature - Try to get customer's email from Customer model [#305](https://github.com/ebanx/magento-gateway-ebanx/pull/305)
* Fix - Don't roll payment status back for ending statuses [#310](https://github.com/ebanx/magento-gateway-ebanx/pull/310)

## 2.7.0
* Feature - Changed text for minimum local amount config [#275](https://github.com/ebanx/magento-gateway-ebanx/pull/275)
* Feature - Save debug logs for EBANX's later access [#281](https://github.com/ebanx/magento-gateway-ebanx/pull/281)
* Feature - Changed text for displaying payment method logos config [#282](https://github.com/ebanx/magento-gateway-ebanx/pull/282)
* Feature - Added document type as a requirement for argentinian payments [#287](https://github.com/ebanx/magento-gateway-ebanx/pull/287)
* Feature - Send payment risk profile id to EBANX on payments [#296](https://github.com/ebanx/magento-gateway-ebanx/pull/296)
* Fix - Added "total spent" message to debit card's checkout [#277](https://github.com/ebanx/magento-gateway-ebanx/pull/277)
* Fix - Define country variable before trying to use it [#298](https://github.com/ebanx/magento-gateway-ebanx/pull/298)

## 2.6.0
* Feature - Remove documents for Chile (except webpay) [#252](https://github.com/ebanx/magento-gateway-ebanx/pull/252)
* Feature - Remove document for Baloto and PSE [#253](https://github.com/ebanx/magento-gateway-ebanx/pull/253)
* Feature - Add document for Peru [#254](https://github.com/ebanx/magento-gateway-ebanx/pull/254)
* Feature - Add document on Argentina [#250](https://github.com/ebanx/magento-gateway-ebanx/pull/250)
* Feature - Add sandbox warning on gateways [#268](https://github.com/ebanx/magento-gateway-ebanx/pull/268) and [#270](https://github.com/ebanx/magento-gateway-ebanx/pull/270)
* Feature - Document masks in all Latam countries [#269](https://github.com/ebanx/magento-gateway-ebanx/pull/269)
* Feature - Updated error messages [#273](https://github.com/ebanx/magento-gateway-ebanx/pull/273)
* Fix - Saved credit card validation not working [#246](https://github.com/ebanx/magento-gateway-ebanx/pull/246)
* Fix - Local amount element not found on credit card gateway [#247](https://github.com/ebanx/magento-gateway-ebanx/pull/247)
* Fix - Benjamin not sending Latam customers documents [#249](https://github.com/ebanx/magento-gateway-ebanx/pull/249)
* Fix - Check if there's a document before trying to return it [#257](https://github.com/ebanx/magento-gateway-ebanx/pull/257)
* Fix - Two clicks needed to buy with credit card [#266](https://github.com/ebanx/magento-gateway-ebanx/pull/266)

## 2.5.3
* Fix - Make cash payments due date reflect admin configuration [#242](https://github.com/ebanx/magento-gateway-ebanx/pull/242)

## 2.5.2
* Fix - Option to disable IOF on local amount on checkout [#240](https://github.com/ebanx/magento-gateway-ebanx/pull/240)
* Fix - Get customer data from billing address from checkouts that don't save it on customer model [#241](https://github.com/ebanx/magento-gateway-ebanx/pull/241)

## 2.5.0
* Feature - One-click payment comes disabled by default [#233](https://github.com/ebanx/magento-gateway-ebanx/pull/233)
* Feature - Hide interest rates configuration table when not using it [#234](https://github.com/ebanx/magento-gateway-ebanx/pull/234)
* Feature - Button to sync an order's payment status on demand in admin panel [#236](https://github.com/ebanx/magento-gateway-ebanx/pull/236)
* Fix - Credit card tokenization error messages were skippable during checkout [#235](https://github.com/ebanx/magento-gateway-ebanx/pull/235)

## 2.4.1
* Fix - One-click Payment templates and blocks review [#230](https://github.com/ebanx/magento-gateway-ebanx/pull/230)
* Fix - Front-end templates and blocks review [#228](https://github.com/ebanx/magento-gateway-ebanx/pull/228)
* Fix - Invoice generation is on by default for new installations [#226](https://github.com/ebanx/magento-gateway-ebanx/pull/226)

## 2.4.0
* Feature - Index controller deprecation and permanent redirect [#224](https://github.com/ebanx/magento-gateway-ebanx/pull/224)
* Feature - Legacy support for old ebanx-magento extensions [#223](https://github.com/ebanx/magento-gateway-ebanx/pull/223)
* Fix - Change magento order state along with status in notification [#222](https://github.com/ebanx/magento-gateway-ebanx/pull/222)
* Fix - Icons container template [#221](https://github.com/ebanx/magento-gateway-ebanx/pull/221)
* Start changelog :)
