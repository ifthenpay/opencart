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
        $this->gatewayBuilder->setMbwayKey($this->configData['payment_ifthenpay_mbway_mbwayKey']);
        $this->gatewayBuilder->setTelemovel($this->ifthenpayController->request->post['mbwayInputPhone']);
    }

    /*protected function setEmailVariables(): void
    {
        //void
    }*/
}
