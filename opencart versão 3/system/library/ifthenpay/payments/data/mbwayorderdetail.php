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
