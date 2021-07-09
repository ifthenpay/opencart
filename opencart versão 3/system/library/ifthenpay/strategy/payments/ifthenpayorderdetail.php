<?php

declare(strict_types=1);

namespace Ifthenpay\Strategy\Payments;

use Ifthenpay\Strategy\Payments\IfthenpayStrategy;
use Ifthenpay\Contracts\Order\OrderDetailInterface;

class IfthenpayOrderDetail extends IfthenpayStrategy
{

    private function setDefaultTwigData(): void
    {
        $this->ifthenpayController->load->language('extension/payment/' . $this->order['payment_code']);
        $this->twigDefaultData->setOrderId($this->order['order_id']);
        $this->twigDefaultData->setTotalToPay($this->paymentValueFormated);
        $this->twigDefaultData->setPaymentMethod($this->getPaymentMethodName($this->order['payment_code']));
        $this->twigDefaultData->setOrderView(false);
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
