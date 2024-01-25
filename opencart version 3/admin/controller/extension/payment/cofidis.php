<?php

use Ifthenpay\Payments\Gateway;

class ControllerExtensionPaymentCofidis extends IfthenpayController
{
	protected $paymentMethod = Gateway::COFIDIS;
}
