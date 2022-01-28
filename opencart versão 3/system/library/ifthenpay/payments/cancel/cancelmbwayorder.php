<?php

declare(strict_types=1);

namespace Ifthenpay\Payments\Cancel;

use Ifthenpay\Payments\Gateway;

class CancelMbwayOrder extends CancelOrder
{
    protected $paymentMethod = Gateway::MBWAY;
  
    public function cancelOrder(): void
    {
        if ($this->ifthenpayController->config->get('payment_mbway_activate_cancelMbwayOrder')) {
            $this->setPendingOrders();
            if (!empty($this->pendingOrders)) {
                foreach ($this->pendingOrders as $order) {
                    $mbwayPayment = $this->ifthenpayController->model_extension_payment_mbway->getPaymentByOrderId($order['order_id'])->row;
                    if (!empty($mbwayPayment)) {
                        $this->gatewayDataBuilder->setMbwayKey($this->ifthenpayController->config->get('payment_mbway_mbwayKey'));
                        $this->gatewayDataBuilder->setIdPedido($mbwayPayment['id_transacao']);
                        if (!$this->paymentStatus->setData($this->gatewayDataBuilder)->getPaymentStatus()) {
                            $this->checkTimeChangeStatus($order);
                        }
                        $this->logCancelOrder($order['order_id']);
                    }
                }
            }
        }
    }
}


