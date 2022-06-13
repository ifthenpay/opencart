<?php

use Ifthenpay\Payments\Gateway;

class ControllerExtensionPaymentCcard extends IfthenpayControllerCatalog
{
	protected $paymentMethod = Gateway::CCARD;
}