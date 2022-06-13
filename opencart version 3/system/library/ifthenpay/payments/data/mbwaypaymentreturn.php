<?php

declare(strict_types=1);

namespace Ifthenpay\Payments\Data;

use Ifthenpay\Base\Payments\MbwayBase;
use Ifthenpay\Contracts\Payments\PaymentReturnInterface;
use Ifthenpay\Payments\Gateway;

class MbwayPaymentReturn extends MbwayBase implements PaymentReturnInterface
{
    public function setTwigVariables(): void
    {
        parent::setTwigVariables();
        $this->twigDefaultData->setTelemovel($this->paymentGatewayResultData->telemovel);
        $this->twigDefaultData->setOrderId((string) $this->paymentDefaultData->order['order_id']);
        $this->twigDefaultData->setIdPedido($this->paymentGatewayResultData->idPedido);
        $this->twigDefaultData->setResendMbwayNotificationControllerUrl(
            $this->ifthenpayController->url->link('extension/payment/mbway/resendMbwayNotification', 
                [
                    'orderId' => $this->paymentDefaultData->order['order_id'],
                    'mbwayTelemovel' => $this->paymentGatewayResultData->telemovel,
                    'orderTotalPay' => $this->paymentDefaultData->order['total'],
                ]
            )            
        );
        $this->twigDefaultData->setMbwayCountdownShow(true);
        $this->twigDefaultData->setIfthenpayPaymentPanelIdPedido($this->ifthenpayController->language->get('ifthenpayPaymentPanelIdPedido'));
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