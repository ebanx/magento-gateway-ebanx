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
                $interestRate = $this->getInterestRate($i);
                $result = $amount / $i;

                if ($interestRate > 0) {
                    $label = Mage::helper('ebanx')->__('%sx - %s com juros', $i, $quote->getStore()->formatPrice(round($result, 2), false));
                } else {
                    $label = Mage::helper('ebanx')->__('%sx - %s sem juros', $i, $quote->getStore()->formatPrice(round($result, 2), false));
                }
            }
            $options[$i] = $label;
        }
        return $options;
    }

    public function getInterestRate($instalments)
    {
        if ($instalments < 2) {
            return 0;
        }

        $interestMap = unserialize(Mage::helper('ebanx')->getInterestRate());
        Mage::log(print_r($interestMap, true), null, 'ebanxInterestRates.log', true);
        usort($interestMap, array($this, '_sortInterestRateByInstalments'));
        $interestMap = array_reverse($interestMap, true);
        $interestRate = 0;
        foreach ($interestMap as $item) {
            if ($instalments <= $item['instalments']) {
                $interestRate = $item['interest'];
            }
        }
        return (float)$interestRate/100;
    }

    protected function _sortInterestRateByInstalments($a, $b)
    {
        if ($a['instalments'] == $b['instalments']) {
            return 0;
        }
        return ($a['instalments'] < $b['instalments']) ? -1 : 1;
    }
}
