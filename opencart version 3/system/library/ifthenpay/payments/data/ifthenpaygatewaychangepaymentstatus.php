<?php

declare(strict_types=1);

namespace Ifthenpay\Payments\Data;

use Ifthenpay\Base\CheckPaymentStatusBase;
use Ifthenpay\Payments\Gateway;

class IfthenpaygatewayChangePaymentStatus extends CheckPaymentStatusBase
{
    protected $paymentMethod = Gateway::IFTHENPAYGATEWAY;

    protected function setGatewayDataBuilder(): void
    {
        $this->gatewayDataBuilder->setIfthenpaygatewayKey($this->ifthenpayController->config->get('payment_ifthenpaygateway_ifthenpaygatewayKey'));
    }

    protected function getPendingOrders(): void
    {
        $this->ifthenpayController->load->model('extension/payment/ifthenpaygateway');
        $this->pendingOrders = $this->ifthenpayController->model_extension_payment_ifthenpaygateway->getAllPendingOrders();
    }

    public function changePaymentStatus(): void
    {
        $this->setGatewayDataBuilder();
        $this->getPendingOrders();
        if (!empty($this->pendingOrders)) {
            foreach ($this->pendingOrders as $pendingOrder) {
                $ifthenpaygatewayPayment = $this->ifthenpayController->model_extension_payment_ifthenpaygateway->getPaymentByOrderId($pendingOrder['order_id'])->row;
                if (!empty($ifthenpaygatewayPayment)) {
                    $this->gatewayDataBuilder->setIdPedido($ifthenpaygatewayPayment['id_transacao']);
                    if ($this->paymentStatus->setData($this->gatewayDataBuilder)->getPaymentStatus()) {
                        $this->savePaymentStatus($ifthenpaygatewayPayment);
                    }
                }
            }
        }
    }
}
