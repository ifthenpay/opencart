<?php

use Ifthenpay\Payments\Gateway;
use Ifthenpay\Utility\Mix;

class ControllerExtensionPaymentMbway extends IfthenpayController {
  protected $paymentMethod = Gateway::MBWAY;
  
  public function insertMbwayInputAdminOrderCreate(&$route, &$data, &$output)
  {
    $mix = $this->ifthenpayContainer->getIoc()->make(Mix::class);
    $variablesForJavascript = [
      'catalogUrl' => $this->config->get('config_secure') ? HTTP_CATALOG : HTTPS_CATALOG,
      'paymentMethodLanguage' => [
        'required' => $this->language->get('error_payment_mbway_input_required'),
        'invalid' => $this->language->get('error_payment_mbway_input_invalid'),
        'mbwayPhoneNumber' => $this->language->get('mbwayPhoneNumber'),
      ],
      'mbwaySvgUrl' => 'image/payment/ifthenpay/mbway.svg'
    ];
    $output .= '<link href="' . $variablesForJavascript['catalogUrl'] . 'catalog/view/theme/default/stylesheet/ifthenpay/' . $mix->create('paymentOptions.css') . 
    '" rel="stylesheet" type="text/css">';
    $output .= '<script type="text/javascript"> var phpVariables =' .  json_encode($variablesForJavascript) . ';</script>';
    $output .= '<script src="./view/javascript/ifthenpay/' . $mix->create('adminOrderCreatePage.js') . 
    '" type="text/javascript"></script>';
  }
}