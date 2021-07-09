<?php

declare(strict_types=1);

namespace Ifthenpay\Base\Payments;


use Ifthenpay\Base\PaymentBase;

class MbwayBase extends PaymentBase
{
    protected $paymentMethod = 'mbway';
    protected $paymentMethodAlias = 'MB WAY';

    protected function setGatewayBuilderData(): void
    {
        $this->gatewayBuilder->setMbwayKey($this->configData['payment_mbway_mbwayKey']);
        $this->gatewayBuilder->setTelemovel($this->ifthenpayController->request->post['mbwayInputPhone']);
    }

    protected function saveToDatabase(): void
    {
        $this->ifthenpayController->load->model('extension/payment/mbway');
		
		$this->ifthenpayController->model_extension_payment_mbway->savePayment($this->paymentDefaultData, $this->paymentGatewayResultData);
    }

    public function getFromDatabaseById(): void
    {
        $this->ifthenpayController->load->model('extension/payment/mbway');
		
		$this->paymentDataFromDb = $this->ifthenpayController->model_extension_payment_mbway
            ->getPaymentByOrderId($this->paymentDefaultData->order['order_id'])
            ->row; 
    }

    /*protected function setEmailVariables(): void
    {
        //void
    }*/
}
