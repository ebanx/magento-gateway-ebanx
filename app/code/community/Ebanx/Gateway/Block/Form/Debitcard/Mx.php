<?php

class Ebanx_Gateway_Block_Form_Debitcard_Mx extends Ebanx_Gateway_Block_Form_Debitcard
{
    public $country = 'mx';

    public $localCurrency = 'MXN';

    /**
     * @return array
     */
    public function getText()
    {
        return array(
            'method-desc' => 'Pagar con Tarjeta de Débito.',
            'local-amount' => 'Total a pagar en Peso Mexicano: ',
            'card-number' => 'Número de la tarjeta',
            'duedate' => 'Fecha de expiración',
            'cvv' => 'Código de verificación',
            'name' => 'Titular de la tarjeta',
        );
    }
}
