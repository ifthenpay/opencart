<?php

declare(strict_types=1);

namespace Ifthenpay\Payments\Data;

use Ifthenpay\Base\Payments\MbwayBase;
use Ifthenpay\Contracts\Payments\PaymentReturnInterface;

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
        $this->twigDefaultData->setPaymentReturnMbwayConfirmPayment($this->ifthenpayController->language->get('paymentReturnMbwayConfirmPayment'));
        $this->twigDefaultData->setPaymentReturnMbwayConfirmPayment5Minutes($this->ifthenpayController->language->get('paymentReturnMbwayConfirmPayment5Minutes'));
        $this->twigDefaultData->setPaymentReturnMbwayConfirmPaymentNotificationExpired($this->ifthenpayController->language->get('paymentReturnMbwayConfirmPaymentNotificationExpired'));
        $this->twigDefaultData->setPaymentReturnMbwayConfirmPaymentNotificationTime($this->ifthenpayController->language->get('paymentReturnMbwayConfirmPaymentNotificationTime'));
        $this->twigDefaultData->setPaymentReturnMbwayConfirmPaymentNotificationResend($this->ifthenpayController->language->get('paymentReturnMbwayConfirmPaymentNotificationResend'));
        $this->twigDefaultData->setPaymentReturnMbwayPaymentPaid($this->ifthenpayController->language->get('paymentReturnMbwayPaymentPaid'));
        $this->twigDefaultData->setPaymentReturnMbwayOrderConfirmed($this->ifthenpayController->language->get('paymentReturnMbwayOrderConfirmed'));
        $this->twigDefaultData->setIfthenpayPaymentPanelPhone($this->ifthenpayController->language->get('ifthenpayPaymentPanelPhone'));
        $this->twigDefaultData->setIfthenpayPaymentPanelOrder($this->ifthenpayController->language->get('ifthenpayPaymentPanelOrder'));
        $this->twigDefaultData->setIfthenpayPaymentPanelMbwayNotificationNotReceive($this->ifthenpayController->language->get('ifthenpayPaymentPanelMbwayNotificationNotReceive'));
        $this->twigDefaultData->setIfthenpayPaymentPanelMbwayResendNotification($this->ifthenpayController->language->get('ifthenpayPaymentPanelMbwayResendNotification'));
        $this->twigDefaultData->setSpinner($this->ifthenpayController->load->view('extension/payment/spinner'));
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