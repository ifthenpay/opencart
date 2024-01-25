<?php

use Ifthenpay\Payments\Gateway;

class ModelExtensionPaymentCofidis extends IfthenpayModel
{
	protected $paymentMethod = Gateway::COFIDIS;
}
