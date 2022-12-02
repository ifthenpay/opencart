<?php

declare(strict_types=1);

namespace Ifthenpay\Payments\Data;

use Ifthenpay\Base\Payments\MultibancoBase;
use Ifthenpay\Contracts\Payments\PaymentReturnInterface;
use Ifthenpay\Payments\Gateway;


class MultibancoPaymentReturn extends MultibancoBase implements PaymentReturnInterface
{
    public function setTwigVariables(): void
    {
        parent::setTwigVariables();
        $this->twigDefaultData->setEntidade($this->paymentGatewayResultData->entidade);
        $this->twigDefaultData->setReferencia($this->paymentGatewayResultData->referencia);
        $this->twigDefaultData->setIfthenpayPaymentPanelEntidade($this->ifthenpayController->language->get('ifthenpayPaymentPanelEntidade'));
        $this->twigDefaultData->setIfthenpayPaymentPanelReferencia($this->ifthenpayController->language->get('ifthenpayPaymentPanelReferencia'));
        $this->twigDefaultData->setIfthenpayPaymentPanelProcessed($this->ifthenpayController->language->get('ifthenpayPaymentPanelProcessed'));
        
        if (isset($this->paymentGatewayResultData->validade) && $this->paymentGatewayResultData->validade) {
            $this->twigDefaultData->setValidade($this->paymentGatewayResultData->validade);
            $this->twigDefaultData->setIfthenpayPaymentPanelValidade($this->ifthenpayController->language->get('ifthenpayPaymentPanelValidade'));
        }
    }

    public function getPaymentReturn(): PaymentReturnInterface
    {
        $this->setGatewayBuilderData();
        $this->paymentGatewayResultData = $this->ifthenpayGateway->execute(
            $this->paymentDefaultData->paymentMethod,
            $this->gatewayBuilder,
            strval($this->paymentDefaultData->order['order_id']),
            strval(
                $this->ifthenpayController->currency->format($this->paymentDefaultData->order['total'], 
                    $this->paymentDefaultData->order['currency_code'], 
                    $this->paymentDefaultData->order['currency_value'], 
                    false
                )
            )
        )->getData();
        $this->saveToDatabase();
        $this->setTwigVariables();
        $this->setRedirectUrl();
        return $this;
    }
}