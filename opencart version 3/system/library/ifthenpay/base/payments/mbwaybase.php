<?php

declare(strict_types=1);

namespace Ifthenpay\Base\Payments;


use Ifthenpay\Base\PaymentBase;
use Ifthenpay\Payments\Gateway;

class MbwayBase extends PaymentBase
{
    protected $paymentMethod = Gateway::MBWAY;
    protected $paymentMethodAlias = 'MB WAY';

    public function setTwigVariables(): void
    {
        parent::setTwigVariables();
        $this->twigDefaultData->setPaymentReturnMbwayConfirmPayment($this->ifthenpayController->language->get('paymentReturnMbwayConfirmPayment'));
        $this->twigDefaultData->setIfthenpayPaymentPanelPhone($this->ifthenpayController->language->get('ifthenpayPaymentPanelPhone'));
        $this->twigDefaultData->setIfthenpayPaymentPanelOrder($this->ifthenpayController->language->get('ifthenpayPaymentPanelOrder'));
        $this->twigDefaultData->setIfthenpayPaymentPanelMbwayNotificationNotReceive($this->ifthenpayController->language->get('ifthenpayPaymentPanelMbwayNotificationNotReceive'));
        $this->twigDefaultData->setIfthenpayPaymentPanelMbwayResendNotification($this->ifthenpayController->language->get('ifthenpayPaymentPanelMbwayResendNotification'));
        $this->twigDefaultData->setSpinner($this->ifthenpayController->load->view('extension/payment/spinner'));
        $this->twigDefaultData->setPaymentReturnMbwayConfirmPaymentMinutes($this->ifthenpayController->language->get('paymentReturnMbwayConfirmPaymentMinutes'));
        $this->twigDefaultData->setPaymentReturnMbwayConfirmPaymentNotificationExpired($this->ifthenpayController->language->get('paymentReturnMbwayConfirmPaymentNotificationExpired'));
        $this->twigDefaultData->setPaymentReturnMbwayConfirmPaymentNotificationTime($this->ifthenpayController->language->get('paymentReturnMbwayConfirmPaymentNotificationTime'));
        $this->twigDefaultData->setPaymentReturnMbwayConfirmPaymentNotificationResend($this->ifthenpayController->language->get('paymentReturnMbwayConfirmPaymentNotificationResend'));
        $this->twigDefaultData->setPaymentReturnMbwayPaymentPaid($this->ifthenpayController->language->get('paymentReturnMbwayPaymentPaid'));
        $this->twigDefaultData->setPaymentReturnMbwayPaymentRefused($this->ifthenpayController->language->get('paymentReturnMbwayPaymentRefused'));
        $this->twigDefaultData->setPaymentReturnMbwayPaymentError($this->ifthenpayController->language->get('paymentReturnMbwayPaymentError'));
        $this->twigDefaultData->setPaymentReturnMbwayOrderConfirmed($this->ifthenpayController->language->get('paymentReturnMbwayOrderConfirmed'));
    }
    protected function setGatewayBuilderData(): void
    {
        $this->gatewayBuilder->setMbwayKey($this->configData['payment_mbway_mbwayKey']);
        if (isset($this->ifthenpayController->request->post['mbwayInputPhone']) && $this->ifthenpayController->request->post['mbwayInputPhone']) {
            $this->gatewayBuilder->setTelemovel($this->ifthenpayController->request->post['mbwayInputPhone']);
        } else if (isset($this->ifthenpayController->request->get['mbwayTelemovel']) && $this->ifthenpayController->request->get['mbwayTelemovel']) {
            $this->gatewayBuilder->setTelemovel($this->ifthenpayController->request->get['mbwayTelemovel']);
        } else {
            $this->gatewayBuilder->setTelemovel($this->ifthenpayController->request->cookie['mbwayInputPhone']);
        }
    }

    protected function saveToDatabase(): void
    {
        $this->ifthenpayController->load->model('extension/payment/mbway');

        $mbwayPayment = $this->ifthenpayController
            ->model_extension_payment_mbway->getPaymentByOrderId($this->paymentDefaultData->order['order_id'])->row;
        if (empty($mbwayPayment)) {
            $this->ifthenpayController->model_extension_payment_mbway
                ->savePayment($this->paymentDefaultData, $this->paymentGatewayResultData);
        } else {
            $this->ifthenpayController->model_extension_payment_mbway
                ->updatePendingMbway($mbwayPayment['id_ifthenpay_mbway'], $this->paymentDefaultData, $this->paymentGatewayResultData);
        }
    }

    public function getFromDatabaseById(): void
    {
        $this->ifthenpayController->load->model('extension/payment/mbway');
		
		$this->paymentDataFromDb = $this->ifthenpayController->model_extension_payment_mbway
            ->getPaymentByOrderId($this->paymentDefaultData->order['order_id'])
            ->row; 
    }

    /*protected function setEmailVariables(): void
    {
        //void
    }*/
}
