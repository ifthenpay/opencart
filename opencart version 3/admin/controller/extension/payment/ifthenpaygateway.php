<?php

use Ifthenpay\Payments\Gateway;
use Ifthenpay\Utility\Mix;

class ControllerExtensionPaymentIfthenpaygateway extends IfthenpayController
{
	protected $paymentMethod = Gateway::IFTHENPAYGATEWAY;
}
