<?php

use Ifthenpay\Payments\Gateway;

class ModelExtensionPaymentMbway extends IfthenpayModel {
	protected $paymentMethod = Gateway::MBWAY;
}