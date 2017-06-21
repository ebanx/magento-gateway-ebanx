<?php
class Ebanx_Gateway_Model_Observer
{
    public function addTotal(Varien_Event_Observer $observer)
    {
        /** @var $block Mage_Core_Block_Abstract */
        $block = $observer->getEvent()->getBlock();
        if ($block instanceof Mage_Checkout_Block_Onepage_Review_Info) {
            /** @var $transport Varien_Object */
            $transport = $observer->getEvent()->getTransport();
            $reviewHtml = $transport->getHtml();
            $totalHtml = $block->getLayout()
                ->createBlock('ebanx/checkout_cart_total')
                ->toHtml();

            $html = str_replace('</tfoot>', $totalHtml . '</tfoot>', $reviewHtml);
            $transport->setHtml($html);
        }
    }
}
