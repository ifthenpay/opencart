<?php

use Ifthenpay\Payments\Gateway;

class ModelExtensionPaymentPix extends IfthenpayModel
{
	protected $paymentMethod = Gateway::PIX;
}
