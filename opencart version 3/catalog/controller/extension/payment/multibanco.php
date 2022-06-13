<?php

use Ifthenpay\Payments\Gateway;

class ControllerExtensionPaymentMultibanco extends IfthenpayControllerCatalog
{
	protected $paymentMethod = Gateway::MULTIBANCO;

}