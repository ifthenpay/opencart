<?php

declare(strict_types=1);

namespace Ifthenpay\Payments\Data;

use Ifthenpay\Base\CheckPaymentStatusBase;

class MbwayChangePaymentStatus extends CheckPaymentStatusBase
{
    protected function setGatewayDataBuilder(): void
    {
        $this->gatewayDataBuilder->setMbwayKey($this->ifthenpayController->config->get('payment_mbway_mbwayKey'));
    }

    protected function getPendingOrders(): void
    {
        $this->ifthenpayController->load->model('extension/payment/mbway');
        $this->pendingOrders = $this->ifthenpayController->model_extension_payment_mbway->getAllMbwayPendingOrders();
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
                        $this->ifthenpayController->model_extension_payment_mbway->updatePaymentStatus(
                            $mbwayPayment['id_ifthenpay_mbway'], 
                            'paid'
                        );
                        $catalogChangeOrderStatusEndpoint = $this->ifthenpayController->config->get('config_secure') ? rtrim(HTTP_CATALOG, '/') : rtrim(HTTPS_CATALOG, '/') . '/index.php?route=extension/payment/mbway/changeOrderStatusFromWebservice';
                        $this->webservice->getRequest(
                            $catalogChangeOrderStatusEndpoint,
                            [
                            'order_id' => $pendingOrder['order_id'],
                            ],
                            false
                        );
                    }
                }
                
            }
        }
    }
}