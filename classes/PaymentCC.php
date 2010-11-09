<?php

class PaymentCCCore extends ObjectModel
{
	public $id_order;
	public $id_currency;
	public $amount;
	public $transaction_id;
	public $card_number;
	public $card_brand;
	public $card_expiration;
	public $card_holder;
	public $date_add;

	protected	$fieldsRequired = array('id_currency', 'amount');
	protected	$fieldsSize = array('transaction_id' => 254, 'card_number' => 254, 'card_brand' => 254, 'card_expiration' => 254, 'card_holder' => 254);
	protected	$fieldsValidate = array(
		'id_order' => 'isUnsignedId', 'id_currency' => 'isUnsignedId', 'amount' => 'isPrice',
		'transaction_id' => 'isAnything', 'card_number' => 'isAnything', 'card_brand' => 'isAnything', 'card_expiration' => 'isAnything', 'card_holder' => 'isAnything');

	protected 	$table = 'payment_cc';
	protected 	$identifier = 'id_payment_cc';

	public function getFields()
	{
		parent::validateFields();
		$fields['id_order'] = intval($this->id_order);
		$fields['id_currency'] = intval($this->id_currency);
		$fields['amount'] = floatval($this->amount);
		$fields['transaction_id'] = pSQL($this->transaction_id);
		$fields['card_number'] = pSQL($this->card_number);
		$fields['card_brand'] = pSQL($this->card_brand);
		$fields['card_expiration'] = pSQL($this->card_expiration);
		$fields['card_holder'] = pSQL($this->card_holder);
		$fields['date_add'] = pSQL($this->date_add);
		return $fields;
	}
	
	public function add($autodate = true, $nullValues = false)
	{
		if (parent::add($autodate, $nullValues))
		{
			Module::hookExec('paymentCCAdded', array('paymentCC' => $this));
			return true;
		}
		return false;
	}
}

?>