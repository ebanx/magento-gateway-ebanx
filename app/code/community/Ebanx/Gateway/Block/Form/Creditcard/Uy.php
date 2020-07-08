<?php

class Ebanx_Gateway_Block_Form_Creditcard_Uy extends Ebanx_Gateway_Block_Form_Creditcard
{
    public $country = 'uy';

    public $localCurrency = 'UYU';

    /**
     * @return string
     */
    protected function getTemplatePath()
    {
        return 'ebanx/form/creditcard_uy.phtml';
    }

    /**
     * @param bool $hasInterests Has interests
     *
     * @return string
     */
    protected function getInterestMessage($hasInterests)
    {
        return $hasInterests ? 'con intereses' : '';
    }

    /**
     * @return array
     */
    public function getText()
    {
        return array(
            'method-desc' => 'Pagar con Tarjeta de Crédito.',
            'newcard' => 'Nueva tarjeta',
            'local-amount' => 'Total a pagar en Peso uruguayo: ',
            'card-number' => 'Número de la tarjeta',
            'duedate' => 'Fecha de expiración',
            'cvv' => 'Código de verificación',
            'save' => 'Salvar este cartão para compras futuras',
            'instalments' => 'Número de parcelas',
            'name' => 'Titular de la tarjeta',
        );
    }
}
