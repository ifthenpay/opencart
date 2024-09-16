<?php

use Ifthenpay\Payments\Gateway;
class ModelExtensionPaymentIfthenpaygateway extends IfthenpayModel {
	protected $paymentMethod = Gateway::IFTHENPAYGATEWAY;
}
