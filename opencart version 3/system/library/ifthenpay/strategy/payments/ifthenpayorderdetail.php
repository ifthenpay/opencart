<?php

declare(strict_types=1);

namespace Ifthenpay\Strategy\Payments;

use Ifthenpay\Strategy\Payments\IfthenpayStrategy;
use Ifthenpay\Contracts\Order\OrderDetailInterface;

class IfthenpayOrderDetail extends IfthenpayStrategy
{

    protected function setDefaultTwigData(): void
    {
        parent::setDefaultTwigData();
        $this->twigDefaultData->setOrderView(false);
        $base_url = HTTP_SERVER ?? '';

        $this->twigDefaultData->setBaseUrl($base_url);
    }

    public function execute(): OrderDetailInterface
    {
        $this->setDefaultData();
        $this->setDefaultTwigData();

        return $this->factory
            ->setType(strtolower($this->getPaymentMethodName($this->order['payment_code'])))
            ->setPaymentDefaultData($this->paymentDefaultData)
            ->setConfigData($this->configData)
            ->setTwigDefaultData($this->twigDefaultData)
            ->setIfthenpayController($this->ifthenpayController)
            ->build()
            ->getOrderDetail();            
    }
}
