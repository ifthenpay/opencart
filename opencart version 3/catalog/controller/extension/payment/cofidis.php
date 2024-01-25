<?php

use Ifthenpay\Payments\Gateway;

class ControllerExtensionPaymentCofidis extends IfthenpayControllerCatalog
{
	protected $paymentMethod = Gateway::COFIDIS;
}
