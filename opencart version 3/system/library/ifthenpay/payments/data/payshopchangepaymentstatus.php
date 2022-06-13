<?php

declare(strict_types=1);

namespace Ifthenpay\Payments\Data;

use Ifthenpay\Base\CheckPaymentStatusBase;
use Ifthenpay\Payments\Gateway;

class PayshopChangePaymentStatus extends CheckPaymentStatusBase
{
    protected $paymentMethod = Gateway::PAYSHOP;
    
    protected function setGatewayDataBuilder(): void
    {
        $this->gatewayDataBuilder->setBackofficeKey($this->ifthenpayController->config->get('payment_payshop_backofficeKey'));
        $this->gatewayDataBuilder->setPayshopKey($this->ifthenpayController->config->get('payment_payshop_payshopKey'));
    }

    protected function getPendingOrders(): void
    {
        $this->ifthenpayController->load->model('extension/payment/payshop');
        $this->pendingOrders = $this->ifthenpayController->model_extension_payment_payshop->getAllPendingOrders();
    }
    
    public function changePaymentStatus(): void
    {
        $this->setGatewayDataBuilder();
        $this->getPendingOrders();
        if (!empty($this->pendingOrders)) {
            foreach ($this->pendingOrders as $pendingOrder) {
                $payshopPayment = $this->ifthenpayController->model_extension_payment_payshop->getPaymentByOrderId($pendingOrder['order_id'])->row;
                if (!empty($payshopPayment)) {
                    $this->gatewayDataBuilder->setReferencia($payshopPayment['referencia']);
                    if ($this->paymentStatus->setData($this->gatewayDataBuilder)->getPaymentStatus()) {
                        $this->savePaymentStatus($payshopPayment); 
                    }
                }
            }
        }
    }
}
