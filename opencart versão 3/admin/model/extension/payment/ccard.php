<?php

use Ifthenpay\Payments\Gateway;

class ModelExtensionPaymentCcard extends IfthenpayModel {
	protected $paymentMethod = Gateway::CCARD;
}