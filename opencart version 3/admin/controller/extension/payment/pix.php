<?php

use Ifthenpay\Payments\Gateway;

class ControllerExtensionPaymentPix extends IfthenpayController
{
	protected $paymentMethod = Gateway::PIX;
}
