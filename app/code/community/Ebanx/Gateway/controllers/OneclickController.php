<?php

class Ebanx_Gateway_OneclickController extends Mage_Core_Controller_Front_Action
{
	/**
	 * @var Mage_Catalog_Model_Product
	 */
	private $product;

	/**
	 * @var array
	 */
	private $request;

	/**
	 * @var float
	 */
	private $subTotal;

	/**
	 * @var Mage_Sales_Model_Order
	 */
	private $order;

	/**
	 * @var int
	 */
	private $storeId;

	/**
	 * @var Mage_Eav_Model_Config
	 */
	private $reservedOrderId;

	/**
	 * @var Mage_Sales_Model_Order_Address
	 */
	private $shippingAddress;

	/**
	 * @var Mage_Sales_Model_Order_Payment
	 */
	private $orderPayment;

	/**
	 * @var Mage_Core_Model_Resource_Transaction
	 */
	private $transaction;

	/**
	 * @var Mage_Customer_Model_Customer
	 */
	private $customer;

	protected function _construct()
	{
		parent::_construct();

		$this->request = Mage::app()->getRequest()->getPost();
	}

	public function payAction()
	{
		if (!Mage::getSingleton('customer/session')->isLoggedIn()
			|| !isset($this->request['product'])) {
			return $this->_redirect('/');
		}
		$this->customer = Mage::getSingleton('customer/session')->getCustomer();
		$this->product  = Mage::getModel('catalog/product')->load($this->request['product']);

		if (!$this->isCardFromCustomer()) {
			return $this->_redirect('/');
		}

		$this->createOrder(array($this->request), $this->getPaymentMethod());

		return $this->_redirect('sales/order/view', array('order_id' => $this->order->getId()));
	}

	private function isCardFromCustomer()
	{
		$selectedCard = $this->request['payment']['selected_card'];
		$cardToken    = $this->request['payment']['ebanx_token'][$selectedCard];
		$customerId   = $this->customer->getId();

		return Mage::getModel('ebanx/usercard')->doesCardBelongsToCustomer($cardToken, $customerId);
	}

	private function getPaymentMethod()
	{
		$selectedCard = $this->request['payment']['selected_card'];
		$cardToken    = $this->request['payment']['ebanx_token'][$selectedCard];

		return Mage::getModel('ebanx/usercard')->getPaymentMethodByToken($cardToken);
	}

	/**
	 * @param array  $products
	 * @param string $paymentMethod
	 */
	private function createOrder($products, $paymentMethod)
	{
		$this->transaction = Mage::getModel('core/resource_transaction');
		$this->storeId     = $this->customer->getStoreId();

		$this->reservedOrderId = Mage::getSingleton('eav/config');
		$this->reservedOrderId = $this->reservedOrderId->getEntityType('order')
													   ->fetchNewIncrementId($this->storeId);

		$currencyCode = Mage::app()->getStore()->getCurrentCurrencyCode();
		$this->order  = Mage::getModel('sales/order');
		$this->order  = $this->order->setIncrementId($this->reservedOrderId)
									->setStoreId($this->storeId)
									->setQuoteId(0)
									->setGlobalCurrencyCode($currencyCode)
									->setBaseCurrencyCode($currencyCode)
									->setStoreCurrencyCode($currencyCode)
									->setOrderCurrencyCode($currencyCode);


		$this->order->setCustomerEmail($this->customer->getEmail())
					->setCustomerFirstname($this->customer->getFirstname())
					->setCustomerLastname($this->customer->getLastname())
					->setCustomerGroupId($this->customer->getGroupId())
					->setCustomerIsGuest(0)
					->setCustomer($this->customer);


		$billing        = $this->customer->getDefaultBillingAddress();
		$billingAddress = Mage::getModel('sales/order_address')
							  ->setStoreId($this->storeId)
							  ->setAddressType(Mage_Sales_Model_Quote_Address::TYPE_BILLING)
							  ->setCustomerId($this->customer->getId())
							  ->setCustomerAddressId($this->customer->getDefaultBilling())
							  ->setCustomerAddress_id($billing->getEntityId())
							  ->setPrefix($billing->getPrefix())
							  ->setFirstname($billing->getFirstname())
							  ->setMiddlename($billing->getMiddlename())
							  ->setLastname($billing->getLastname())
							  ->setSuffix($billing->getSuffix())
							  ->setCompany($billing->getCompany())
							  ->setStreet($billing->getStreet())
							  ->setCity($billing->getCity())
							  ->setCountry_id($billing->getCountryId())
							  ->setRegion($billing->getRegion())
							  ->setRegion_id($billing->getRegionId())
							  ->setPostcode($billing->getPostcode())
							  ->setTelephone($billing->getTelephone())
							  ->setFax($billing->getFax())
							  ->setVatId($this->customer->getEbanxCustomerDocument());
		$this->order->setBillingAddress($billingAddress);

		$shipping              = $this->customer->getDefaultShippingAddress();
		$this->shippingAddress = Mage::getModel('sales/order_address');
		$this->shippingAddress = $this->shippingAddress->setStoreId($this->storeId)
													   ->setAddressType(Mage_Sales_Model_Quote_Address::TYPE_SHIPPING)
													   ->setCustomerId($this->customer->getId())
													   ->setCustomerAddressId($this->customer->getDefaultShipping())
													   ->setCustomer_address_id($shipping->getEntityId())
													   ->setPrefix($shipping->getPrefix())
													   ->setFirstname($shipping->getFirstname())
													   ->setMiddlename($shipping->getMiddlename())
													   ->setLastname($shipping->getLastname())
													   ->setSuffix($shipping->getSuffix())
													   ->setCompany($shipping->getCompany())
													   ->setStreet($shipping->getStreet())
													   ->setCity($shipping->getCity())
													   ->setCountry_id($shipping->getCountryId())
													   ->setRegion($shipping->getRegion())
													   ->setRegion_id($shipping->getRegionId())
													   ->setPostcode($shipping->getPostcode())
													   ->setTelephone($shipping->getTelephone())
													   ->setFax($shipping->getFax())
													   ->setVatId($billing->getVatId());
		Mage::app()->getRequest()->setPost('ebanx-document',
			array($paymentMethod => $this->customer->getEbanxCustomerDocument()));
		$this->customer = $this->customer->setCountryId('br');

		$this->order->setShippingAddress($this->shippingAddress)
					->setCustomerTaxvat($this->customer->getEbanxCustomerDocument());

		$this->orderPayment = Mage::getModel('sales/order_payment');
		$this->orderPayment = $this->orderPayment->setStoreId($this->storeId)
												 ->setCustomerPaymentId(0)
												 ->setMethod($paymentMethod)
												 ->setPoNumber(' – ');

		$this->order->setPayment($this->orderPayment);

		$this->subTotal = 0;

		foreach ($products as $productRequest) {
			$this->addProduct($productRequest);
		}

		$this->order->setSubtotal($this->subTotal)
					->setBaseSubtotal($this->subTotal)
					->setGrandTotal($this->subTotal)
					->setBaseGrandTotal($this->subTotal);

		$this->transaction->addObject($this->order);
		$this->transaction->addCommitCallback(array($this->order, 'place'));
		$this->transaction->addCommitCallback(array($this->order, 'save'));
		$this->transaction->save();
	}

	private function addProduct($requestData)
	{
		$request = new Varien_Object();
		$request->setData($requestData);

		$product = Mage::getModel('catalog/product')->load($request['product']);

		$cartCandidates = $product->getTypeInstance(true)
								  ->prepareForCartAdvanced($request, $product);

		if (is_string($cartCandidates)) {
			throw new Exception($cartCandidates);
		}

		if (!is_array($cartCandidates)) {
			$cartCandidates = array($cartCandidates);
		}

		$parentItem = null;
		$errors     = array();
		$items      = array();
		foreach ($cartCandidates as $candidate) {
			$item = $this->productToOrderItem($candidate, $candidate->getCartQty());

			$items[] = $item;

			/**
			 * As parent item we should always use the item of first added product
			 */
			if (!$parentItem) {
				$parentItem = $item;
			}
			if ($parentItem && $candidate->getParentProductId()) {
				$item->setParentItem($parentItem);
			}
			/**
			 * We specify qty after we know about parent (for stock)
			 */
			$item->setQty($item->getQty() + $candidate->getCartQty());

			// collect errors instead of throwing first one
			if ($item->getHasError()) {
				$message = $item->getMessage();
				if (!in_array($message, $errors)) { // filter duplicate messages
					$errors[] = $message;
				}
			}
		}
		if (!empty($errors)) {
			Mage::throwException(implode("\n", $errors));
		}

		foreach ($items as $item) {
			$this->order->addItem($item);
		}

		return $items;
	}

	private function productToOrderItem(Mage_Catalog_Model_Product $product, $qty = 1)
	{
		$rowTotal = $product->getFinalPrice() * $qty;

		$options = $product->getCustomOptions();

		$optionsByCode = array();

		foreach ($options as $option) {
			$quoteOption = Mage::getModel('sales/quote_item_option')->setData($option->getData())
							   ->setProduct($option->getProduct());

			$optionsByCode[$quoteOption->getCode()] = $quoteOption;
		}

		$product->setCustomOptions($optionsByCode);

		$options = $product->getTypeInstance(true)->getOrderOptions($product);

		$orderItem = Mage::getModel('sales/order_item')
						 ->setStoreId($this->storeId)
						 ->setQuoteItemId(0)
						 ->setQuoteParentItemId(null)
						 ->setProductId($product->getId())
						 ->setProductType($product->getTypeId())
						 ->setQtyBackordered(null)
						 ->setTotalQtyOrdered($product['rqty'])
						 ->setQtyOrdered($product['qty'])
						 ->setName($product->getName())
						 ->setSku($product->getSku())
						 ->setPrice($product->getFinalPrice())
						 ->setBasePrice($product->getFinalPrice())
						 ->setOriginalPrice($product->getFinalPrice())
						 ->setRowTotal($rowTotal)
						 ->setBaseRowTotal($rowTotal)
						 ->setWeeeTaxApplied(serialize(array()))
						 ->setBaseWeeeTaxDisposition(0)
						 ->setWeeeTaxDisposition(0)
						 ->setBaseWeeeTaxRowDisposition(0)
						 ->setWeeeTaxRowDisposition(0)
						 ->setBaseWeeeTaxAppliedAmount(0)
						 ->setBaseWeeeTaxAppliedRowAmount(0)
						 ->setWeeeTaxAppliedAmount(0)
						 ->setWeeeTaxAppliedRowAmount(0)
						 ->setProductOptions($options);

		$this->subTotal += $rowTotal;

		return $orderItem;
	}
}
