<?php

declare(strict_types=1);

namespace Ifthenpay\Payments\Data;

use Ifthenpay\Base\CheckPaymentStatusBase;
use Ifthenpay\Payments\Gateway;

class MbwayChangePaymentStatus extends CheckPaymentStatusBase
{
    protected $paymentMethod = Gateway::MBWAY;

    protected function setGatewayDataBuilder(): void
    {
        $this->gatewayDataBuilder->setMbwayKey($this->ifthenpayController->config->get('payment_mbway_mbwayKey'));
    }

    protected function getPendingOrders(): void
    {
        $this->ifthenpayController->load->model('extension/payment/mbway');
        $this->pendingOrders = $this->ifthenpayController->model_extension_payment_mbway->getAllPendingOrders();
    }
    
    public function changePaymentStatus(): void
    {
        $this->setGatewayDataBuilder();
        $this->getPendingOrders();
        if (!empty($this->pendingOrders)) {
            foreach ($this->pendingOrders as $pendingOrder) {
                $mbwayPayment = $this->ifthenpayController->model_extension_payment_mbway->getPaymentByOrderId($pendingOrder['order_id'])->row;
                if (!empty($mbwayPayment)) {
                    $this->gatewayDataBuilder->setIdPedido($mbwayPayment['id_transacao']);
                    if ($this->paymentStatus->setData($this->gatewayDataBuilder)->getPaymentStatus()) {
                        $this->savePaymentStatus($mbwayPayment);
                    }
                }
            }
        }
    }
}
