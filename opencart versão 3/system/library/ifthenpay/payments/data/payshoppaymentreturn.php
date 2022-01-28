<?php

declare(strict_types=1);

namespace Ifthenpay\Payments\Data;

use Ifthenpay\Base\Payments\PayshopBase;
use Ifthenpay\Contracts\Payments\PaymentReturnInterface;
use Ifthenpay\Payments\Gateway;


class PayshopPaymentReturn extends PayshopBase implements PaymentReturnInterface
{

    public function setTwigVariables(): void
    {
        parent::setTwigVariables();
        $this->twigDefaultData->setReferencia($this->paymentGatewayResultData->referencia);
        $this->twigDefaultData->setValidade($this->paymentGatewayResultData->validade !== '' ? 
            (new \DateTime($this->paymentGatewayResultData->validade))->format('d-m-Y') : '');
        $this->twigDefaultData->setIfthenpayPaymentPanelReferencia($this->ifthenpayController->language->get('ifthenpayPaymentPanelReferencia'));
        $this->twigDefaultData->setIfthenpayPaymentPanelValidade($this->ifthenpayController->language->get('ifthenpayPaymentPanelValidade'));
        $this->twigDefaultData->setIdPedido($this->paymentGatewayResultData->idPedido);
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