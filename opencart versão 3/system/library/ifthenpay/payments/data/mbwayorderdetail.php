<?php

declare(strict_types=1);

namespace Ifthenpay\Payments\Data;

use Ifthenpay\Base\Payments\MbwayBase;
use Ifthenpay\Contracts\Order\OrderDetailInterface;

class MbwayOrderDetail extends MbwayBase implements OrderDetailInterface
{
    public function setTwigVariables(): void
    {
        parent::setTwigVariables();
        $this->twigDefaultData->setTelemovel(!empty($this->paymentDataFromDb) ? $this->paymentDataFromDb['telemovel'] : '');
        $this->twigDefaultData->setIdPedido(!empty($this->paymentDataFromDb) ? $this->paymentDataFromDb['id_transacao'] : '');
        $this->twigDefaultData->setIfthenpayPaymentPanelPhone($this->ifthenpayController->language->get('ifthenpayPaymentPanelPhone'));
        $this->twigDefaultData->setIfthenpayPaymentPanelOrder($this->ifthenpayController->language->get('ifthenpayPaymentPanelOrder'));
        $this->twigDefaultData->setIfthenpayPaymentPanelMbwayNotificationNotReceive($this->ifthenpayController->language->get('ifthenpayPaymentPanelMbwayNotificationNotReceive'));
        $this->twigDefaultData->setIfthenpayPaymentPanelMbwayResendNotification($this->ifthenpayController->language->get('ifthenpayPaymentPanelMbwayResendNotification'));
        $this->twigDefaultData->setSpinner($this->ifthenpayController->load->view('extension/payment/spinner'));

        if (!empty($this->paymentDataFromDb) && $this->paymentDataFromDb['status'] !== 'paid' && 
        strtolower($this->paymentDefaultData->order['order_status']) !== 'canceled') {
                $this->twigDefaultData->setResendMbwayNotificationControllerUrl(
                    $this->ifthenpayController->url->link('extension/payment/mbway/resendMbwayNotification', 
                        [
                            'orderId' => $this->paymentDefaultData->order['order_id'],
                            'mbwayTelemovel' => $this->paymentDataFromDb['telemovel'],
                            'orderTotalPay' => $this->paymentDefaultData->order['total'],
                        ]
                    )  
                );
            
            if (isset($this->ifthenpayController->session->data['ifthenpayPaymentReturn']['mbwayResendNotificationSent']) && 
                !$this->ifthenpayController->session->data['ifthenpayPaymentReturn']['mbwayResendNotificationSent'] && 
                    $this->ifthenpayController->session->data['ifthenpayPaymentReturn']['mbwayCountdownShow']) {
                        $this->ifthenpayController->session->data['ifthenpayPaymentReturn']['mbwayCountdownShow'] = false;
                        $this->twigDefaultData->setMbwayCountdownShow(false);
            } else if ($this->ifthenpayController->session->data['ifthenpayPaymentReturn']['mbwayCountdownShow']){
                $this->ifthenpayController->session->data['ifthenpayPaymentReturn']['mbwayCountdownShow'] = true;
                $this->twigDefaultData->setMbwayCountdownShow(true);
                $this->ifthenpayController->session->data['ifthenpayPaymentReturn']['mbwayResendNotificationSent'] = false;
                $this->twigDefaultData->setPaymentReturnMbwayConfirmPayment($this->ifthenpayController->language->get('paymentReturnMbwayConfirmPayment'));
                $this->twigDefaultData->setPaymentReturnMbwayConfirmPayment5Minutes($this->ifthenpayController->language->get('paymentReturnMbwayConfirmPayment5Minutes'));
                $this->twigDefaultData->setPaymentReturnMbwayConfirmPaymentNotificationExpired($this->ifthenpayController->language->get('paymentReturnMbwayConfirmPaymentNotificationExpired'));
                $this->twigDefaultData->setPaymentReturnMbwayConfirmPaymentNotificationTime($this->ifthenpayController->language->get('paymentReturnMbwayConfirmPaymentNotificationTime'));
                $this->twigDefaultData->setPaymentReturnMbwayConfirmPaymentNotificationResend($this->ifthenpayController->language->get('paymentReturnMbwayConfirmPaymentNotificationResend'));
                $this->twigDefaultData->setPaymentReturnMbwayPaymentPaid($this->ifthenpayController->language->get('paymentReturnMbwayPaymentPaid'));
                $this->twigDefaultData->setPaymentReturnMbwayOrderConfirmed($this->ifthenpayController->language->get('paymentReturnMbwayOrderConfirmed'));
                
            } else {
                $this->twigDefaultData->setMbwayCountdownShow(false);
                $this->ifthenpayController->session->data['ifthenpayPaymentReturn']['mbwayResendNotificationSent'] = false;
            }
        } else {
            $this->smartyDefaultData->setMbwayCountdownShow(false);
            $this->smartyDefaultData->setResendMbwayNotificationControllerUrl('');
        }
    }

    public function getOrderDetail(): OrderDetailInterface
    {
        $this->getFromDatabaseById();
        $this->setTwigVariables();
        return $this;
    }
}
