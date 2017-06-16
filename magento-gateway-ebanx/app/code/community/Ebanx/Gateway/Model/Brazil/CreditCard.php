<?php
class Ebanx_Gateway_Model_Brazil_CreditCard extends Ebanx_Gateway_Model_Payment_CreditCard
{
    const MIN_INSTALMENT_VALUE = 20;

	protected $gateway;

	protected $_code = 'ebanx_cc_br';

	protected $_formBlockType = 'ebanx/form_creditcardbr';
	protected $_infoBlockType = 'ebanx/info_creditcardbr';

	public function __construct()
	{
		parent::__construct();

		$this->gateway = $this->ebanx->creditCard();
	}

	public function getInstalmentOptions()
    {
        $quote = $this->getInfoInstance()->getQuote();
        $amount = $quote->getGrandTotal();

        $maxInstalments = (int)Mage::helper('ebanx')->getMaxInstalments();
        $minInstalmentValue = (float)Mage::helper('ebanx')->getMinInstalmentValue();

        if ($minInstalmentValue < self::MIN_INSTALMENT_VALUE) {
            $minInstalmentValue = self::MIN_INSTALMENT_VALUE;
        }

        $instalments = floor($amount / $minInstalmentValue);
        if ($instalments > $maxInstalments) {
            $instalments = $maxInstalments;
        } elseif ($instalments < 1) {
            $instalments = 1;
        }

        $options = array();
        for ($i=1; $i <= $instalments; $i++) {
            if ($i == 1) {
                $label = Mage::helper('ebanx')->__('Pague a vista - %s', $quote->getStore()->formatPrice($amount, false));
            } else {
                // TODO: Interest Rates
                $result = $amount / $i;

                $label = Mage::helper('ebanx')->__('%sx - %s sem juros', $i, $quote->getStore()->formatPrice(round($result, 2), false));
            }
            $options[$i] = $label;
        }
        return $options;
    }
}
