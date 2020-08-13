<?php

class Ebanx_Gateway_Block_Form_Debitcard_Uy extends Ebanx_Gateway_Block_Form_Debitcard
{
    public $country = 'uy';

    public $localCurrency = 'UYU';

    /**
     * @return array
     */
    public function getText()
    {
        return array(
            'method-desc' => 'Pagar con Tarjeta de Débito.',
            'local-amount' => 'Total a pagar en Peso Uruguayo: ',
            'card-number' => 'Número de la tarjeta',
            'duedate' => 'Fecha de expiración',
            'duedate-month' => 'Mes',
            'duedate-year' => 'Año',
            'cvv' => 'Código de verificación',
            'name' => 'Titular de la tarjeta',
        );
    }
}
