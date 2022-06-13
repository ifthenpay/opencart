<?php

use Ifthenpay\Payments\Gateway;

class ControllerExtensionPaymentPayshop extends IfthenpayControllerCatalog
{
	protected $paymentMethod = Gateway::PAYSHOP;
}