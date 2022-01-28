<?php

use Ifthenpay\Payments\Gateway;

class ModelExtensionPaymentMultibanco extends IfthenpayModel {
	protected $paymentMethod = Gateway::MULTIBANCO;
}