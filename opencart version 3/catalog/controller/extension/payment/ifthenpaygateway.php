<?php

use Ifthenpay\Payments\Gateway;

class ControllerExtensionPaymentIfthenpaygateway extends IfthenpayControllerCatalog
{
	protected $paymentMethod = Gateway::IFTHENPAYGATEWAY;
}
