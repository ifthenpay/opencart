<?php

declare(strict_types=1);

namespace Ifthenpay\Strategy\Payments;

use Ifthenpay\Strategy\Payments\IfthenpayStrategy;
use Ifthenpay\Contracts\Payments\PaymentReturnInterface;


class IfthenpayPaymentReturn extends IfthenpayStrategy
{

    protected function setDefaultTwigData(): void
    {
        parent::setDefaultTwigData();
        $this->twigDefaultData->setPaymentReturnErrorTitle($this->ifthenpayController->language->get('paymentReturnErrorTitle'));
        $this->twigDefaultData->setPaymentReturnErrorText($this->ifthenpayController->language->get('paymentReturnErrorText')); 
        $this->twigDefaultData->setPaymentReturnTitle($this->ifthenpayController->language->get('paymentReturnTitle'));
        $this->twigDefaultData->setIfthenpayPaymentPanelTotalToPay($this->ifthenpayController->language->get('ifthenpayPaymentPanelTotalToPay'));
        $this->twigDefaultData->setOrderView(true);
        $base_url = HTTP_SERVER ?? '';

        $this->twigDefaultData->setBaseUrl($base_url);
    }

    public function execute(): PaymentReturnInterface
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
            ->getPaymentReturn();
    }
}
