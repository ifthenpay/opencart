<?php

use Ifthenpay\Payments\Gateway;

class ModelExtensionPaymentPayshop extends IfthenpayModel {
	protected $paymentMethod = Gateway::PAYSHOP;
}