<?php

declare(strict_types=1);

namespace Ifthenpay\Payments\Data;

use Ifthenpay\Contracts\Payments\PaymentReturnInterface;
use Ifthenpay\Base\Payments\CCardBase;

class CCardPaymentReturn extends CCardBase implements PaymentReturnInterface
{

    public function setTwigVariables(): void
    {
        parent::setTwigVariables();
        $this->twigDefaultData->setIdPedido($this->paymentGatewayResultData->idPedido);
    }

    public function getPaymentReturn()
    {
        $this->setGatewayBuilderData();
        if ($this->paymentDefaultData->order['currency_code'] !== 'EUR') {
            $orderTotal =  $this->ifthenpayController->currency->format($this->paymentDefaultData->order['total'], 'EUR', '', false);  
        } else {
            $orderTotal = $this->ifthenpayController->currency->format($this->paymentDefaultData->order['total'], 
                $this->paymentDefaultData->order['currency_code'], 
                $this->paymentDefaultData->order['currency_value'], 
                false
            );
        }
        $this->paymentGatewayResultData = $this->ifthenpayGateway->execute(
            $this->paymentDefaultData->paymentMethod,
            $this->gatewayBuilder,
            strval($this->paymentDefaultData->order['order_id']),
            strval($orderTotal)
        )->getData();
        $this->saveToDatabase();
        $this->setTwigVariables();
        $this->setRedirectUrl(true, $this->paymentGatewayResultData->paymentUrl);
        return $this;
    }
}
