<?php
/*
 * 2007-2012 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author PrestaShop SA <contact@prestashop.com>
 *  @copyright  2007-2012 PrestaShop SA
 *  @version  Release: $Revision: 13573 $
 *  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

/**
 * @since 1.5.0
 */

class PayPalSubmitModuleFrontController extends ModuleFrontController
{
	public $display_column_left = false;

	public function getPayPalOrder($id_order)
	{
		$sql = 'SELECT *
				FROM `' . _DB_PREFIX_ . 'paypal_order`
				WHERE `id_order` = ' . (int)$id_order;

		return Db::getInstance()->getRow($sql);
	}

	public function initContent()
	{
		parent::initContent();

		$this->paypal = new PayPal();
		$this->context = Context::getContext();

		$this->id_module = (int)Tools::getValue('id_module');
		$this->id_order = (int)Tools::getValue('id_order');
		$this->order = new Order($this->id_order);

		$this->context->smarty->assign(
			array('order'                   => $this->order,
		          'id_order'                => $this->id_order,
		          'currency'                => $this->context->currency,
		          'is_guest'                => $this->context->customer->is_guest,
		          'paypal_order'            => $this->getPayPalOrder($this->id_order),
		          'HOOK_ORDER_CONFIRMATION' => $this->displayOrderConfirmation(),
		          'HOOK_PAYMENT_RETURN'     => $this->displayPaymentReturn())
		);

		if ($this->context->customer->is_guest)
		{
			$this->context->smarty->assign(array('id_order'           => $this->id_order,
			                                     'id_order_formatted' => sprintf('#%06d', $this->id_order)));
			/* If guest we clear the cookie for security reason */
			$this->context->customer->mylogout();
		}



		$this->setTemplate('order-confirmation.tpl');
	}

	/**
	 * Execute the hook displayPaymentReturn
	 */
	public function displayPaymentReturn()
	{
		if (Validate::isUnsignedId($this->id_order) && Validate::isUnsignedId($this->id_module))
		{
			$params = array();
			$order = new Order($this->id_order);
			$currency = new Currency($order->id_currency);

			if (Validate::isLoadedObject($order))
			{
				$params['total_to_pay'] = $order->getOrdersTotalPaid();
				$params['currency'] = $currency->sign;
				$params['objOrder'] = $order;
				$params['currencyObj'] = $currency;

				return Hook::exec('displayPaymentReturn', $params, $this->id_module);
			}
		}
		return false;
	}

	/**
	 * Execute the hook displayOrderConfirmation
	 */
	public function displayOrderConfirmation()
	{
		if (Validate::isUnsignedId($this->id_order))
		{
			$params = array();
			$order = new Order($this->id_order);
			$currency = new Currency($order->id_currency);

			if (Validate::isLoadedObject($order))
			{
				$params['total_to_pay'] = $order->getOrdersTotalPaid();
				$params['currency'] = $currency->sign;
				$params['objOrder'] = $order;
				$params['currencyObj'] = $currency;

				return Hook::exec('displayOrderConfirmation', $params);
			}
		}
		return false;
	}
}