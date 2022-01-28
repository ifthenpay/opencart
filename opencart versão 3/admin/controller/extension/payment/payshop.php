<?php

use Ifthenpay\Payments\Gateway;

class ControllerExtensionPaymentPayshop extends IfthenpayController {
  protected $paymentMethod = Gateway::PAYSHOP;
}