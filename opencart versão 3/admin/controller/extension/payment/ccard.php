<?php

use Ifthenpay\Payments\Gateway;

class ControllerExtensionPaymentCcard extends IfthenpayController {
  protected $paymentMethod = Gateway::CCARD;
}