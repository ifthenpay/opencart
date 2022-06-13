<?php

declare(strict_types=1);

namespace Ifthenpay\Payments\Data;

use Ifthenpay\Contracts\Payments\PaymentReturnInterface;
use Ifthenpay\Base\Payments\CCardBase;
use Ifthenpay\Traits\Payments\ConvertCurrency;
use Ifthenpay\Payments\Gateway;

class CCardPaymentReturn extends CCardBase implements PaymentReturnInterface
{
    use ConvertCurrency;

    public function setTwigVariables(): void
    {
        parent::setTwigVariables();
        $this->twigDefaultData->setIdPedido($this->paymentGatewayResultData->idPedido);
    }

    public function getPaymentReturn()
    {
        $this->setGatewayBuilderData();
        $this->paymentGatewayResultData = $this->ifthenpayGateway->execute(
            $this->paymentDefaultData->paymentMethod,
            $this->gatewayBuilder,
            strval($this->paymentDefaultData->order['order_id']),
            strval($this->convertToCurrency($this->paymentDefaultData->order, $this->ifthenpayController))
        )->getData();
        $this->saveToDatabase();
        $this->setTwigVariables();
        $this->setRedirectUrl(true, $this->paymentGatewayResultData->paymentUrl);
        return $this;
    }
}
