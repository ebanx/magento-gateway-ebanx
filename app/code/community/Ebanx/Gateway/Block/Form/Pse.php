<?php

class Ebanx_Gateway_Block_Form_Pse extends Ebanx_Gateway_Block_Form_Abstract
{
	public function getBanksList()
	{
		return array(
			'banco_agrario' => 'Banco Agrario',
			'banco_av_villas' => 'Banco AV Villas',
			'banco_bbva_colombia_s.a.' => 'Banco BBVA Colombia',
			'banco_caja_social' => 'Banco Caja Social',
			'banco_colpatria' => 'Banco Colpatria',
			'banco_cooperativo_coopcentral' => 'Banco Cooperativo Coopcentral',
			'banco_corpbanca_s.a' => 'Banco CorpBanca Colombia',
			'banco_davivienda' => 'Banco Davivienda',
			'banco_de_bogota' => 'Banco de Bogotá',
			'banco_de_occidente' => 'Banco de Occidente',
			'banco_falabella_' => 'Banco Falabella',
			'banco_gnb_sudameris' => 'Banco GNB Sudameris',
			'banco_pichincha_s.a.' => 'Banco Pichincha',
			'banco_popular' => 'Banco Popular',
			'banco_procredit' => 'Banco ProCredit',
			'bancolombia' => 'Bancolombia',
			'bancoomeva_s.a.' => 'Bancoomeva',
			'citibank_' => 'Citibank',
			'helm_bank_s.a.' => 'Helm Bank'
		);
	}

	protected function _construct()
	{
		parent::_construct();
		$this->setTemplate('ebanx/form/pse.phtml');
	}
}
